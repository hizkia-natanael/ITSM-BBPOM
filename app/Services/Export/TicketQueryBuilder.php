<?php

namespace App\Services\Export;

use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TicketQueryBuilder
{
    /**
     * Map of 12 official service keys to fallback category slugs/keywords.
     */
    protected array $serviceSlugMap = [
        'sipt' => ['sipt'],
        'srikandi' => ['srikandi'],
        'email_corporate' => ['email-corporate', 'email_corporate'],
        'sertifikat_bse' => ['sertifikat-bsre', 'sertifikat_bsre', 'sertifikat_bse'],
        'zoom' => ['zoom'],
        'formulir_kegiatan' => ['formulir-kegiatan', 'formulir_kegiatan'],
        'internet' => ['internet', 'jaringan'],
        'subsite' => ['subsite'],
        'infografis' => ['infografis'],
        'install_windows' => ['install-windows', 'install_windows'],
        'lisensi_office' => ['lisensi-office', 'lisensi_office'],
        'lainnya' => ['lainnya'],
    ];

    /**
     * Strip markdown syntax (asterisks, bullet stars, hashtags) for clean international office exports.
     */
    public static function stripMarkdown(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        // 1. Remove bold formatting: **text** -> text
        $text = preg_replace('/\*\*([^*]+)\*\*/', '$1', $text);
        // 2. Remove italic formatting: *text* -> text
        $text = preg_replace('/(?<!\*)\*([^*]+)\*(?!\*)/', '$1', $text);
        // 3. Remove header markers: ### Heading -> Heading
        $text = preg_replace('/^#+\s*/m', '', $text);
        // 4. Standardize bullet points: * Item -> - Item
        $text = preg_replace('/^\s*[\*•]\s+/m', '- ', $text);
        // 5. Remove horizontal rules: ---
        $text = preg_replace('/^---+$/m', '', $text);
        // 6. Remove any leftover stray double/single asterisks
        $text = str_replace('**', '', $text);
        // 7. Normalize multiple empty lines to maximum 2 newlines
        $text = preg_replace("/\n{3,}/", "\n\n", $text);

        return trim($text);
    }

    /**
     * Build the query based on filter parameters and return tickets with summary metrics.
     *
     * @param array $filters
     * @return array{tickets: Collection, summary: array}
     */
    public function build(array $filters = []): array
    {
        $query = Ticket::query()
            ->with([
                'user',
                'handledBy',
                'category',
                'responses',
                'bsreDetail',
                'zoomDetail',
                'kegiatanDetail',
                'windowsDetail',
                'officeDetail',
                'lainnyaDetail',
            ]);

        $categoryKey = $filters['category'] ?? 'all';
        $periodType = $filters['period_type'] ?? 'all';
        $status = $filters['status'] ?? 'all';
        $handledBy = $filters['handled_by'] ?? 'all';

        // 1. Filter Category (12 options or all)
        if (!empty($categoryKey) && $categoryKey !== 'all') {
            $query->where(function (Builder $q) use ($categoryKey) {
                $q->where('service_key', $categoryKey);

                if (isset($this->serviceSlugMap[$categoryKey])) {
                    $slugs = $this->serviceSlugMap[$categoryKey];
                    $q->orWhereHas('category', function (Builder $cq) use ($slugs) {
                        $cq->whereIn('slug', $slugs);
                    });
                }
            });
        }

        // 2. Filter Status
        if (!empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        // 3. Filter Handled By (Admin Penangan)
        $handledByLabel = 'Semua Petugas IT';
        if (!empty($handledBy) && $handledBy !== 'all') {
            if ($handledBy === 'me') {
                $currentUserId = auth()->id() ?? ($filters['current_user_id'] ?? null);
                if ($currentUserId) {
                    $query->where('handled_by_id', $currentUserId);
                    $handledByLabel = 'Ditangani: ' . (auth()->user()?->name ?? 'Saya Sendiri');
                } else {
                    $handledByLabel = 'Ditangani: Saya Sendiri';
                }
            } elseif ($handledBy === 'unassigned') {
                $query->whereNull('handled_by_id');
                $handledByLabel = 'Belum Ditugaskan';
            } elseif (is_numeric($handledBy)) {
                $query->where('handled_by_id', (int) $handledBy);
                $adminUser = User::find($handledBy);
                $handledByLabel = 'Petugas: ' . ($adminUser?->name ?? "Admin #{$handledBy}");
            }
        }

        // 4. Filter Date Range
        $periodLabel = 'Seluruh Aduan (Semua Waktu)';
        $periodSlug = 'Semua_Waktu';

        if ($periodType === 'yearly') {
            $year = !empty($filters['year']) ? (int) $filters['year'] : (int) now()->format('Y');

            $query->whereYear('created_at', $year);

            $periodLabel = "Periode Tahun {$year}";
            $periodSlug = "Tahun_{$year}";
        } elseif ($periodType === 'month_year') {
            $month = !empty($filters['month']) ? (int) $filters['month'] : (int) now()->format('n');
            $year = !empty($filters['year']) ? (int) $filters['year'] : (int) now()->format('Y');

            $query->whereYear('created_at', $year)
                ->whereMonth('created_at', $month);

            $dateObj = Carbon::createFromDate($year, $month, 1);
            $monthNames = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $monthName = $monthNames[$month] ?? $dateObj->translatedFormat('F');
            $periodLabel = "Periode {$monthName} {$year}";
            $periodSlug = "{$year}_{$month}";
        } elseif ($periodType === 'custom') {
            $startDate = $filters['start_date'] ?? null;
            $endDate = $filters['end_date'] ?? null;

            if ($startDate && $endDate) {
                $query->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);
                $periodLabel = Carbon::parse($startDate)->format('d M Y') . ' s/d ' . Carbon::parse($endDate)->format('d M Y');
                $periodSlug = Carbon::parse($startDate)->format('Ymd') . '_sd_' . Carbon::parse($endDate)->format('Ymd');
            } elseif ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
                $periodLabel = 'Mulai ' . Carbon::parse($startDate)->format('d M Y');
                $periodSlug = 'Mulai_' . Carbon::parse($startDate)->format('Ymd');
            } elseif ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
                $periodLabel = 'Hingga ' . Carbon::parse($endDate)->format('d M Y');
                $periodSlug = 'Hingga_' . Carbon::parse($endDate)->format('Ymd');
            }
        }

        // Execute ordered query
        $tickets = $query->orderBy('created_at', 'desc')->get();

        // 5. Clean markdown asterisks on each ticket
        foreach ($tickets as $ticket) {
            $ticket->clean_description = self::stripMarkdown($ticket->description);
            $ticket->clean_subject = self::stripMarkdown($ticket->subject);
        }

        // 6. Compute Summary Statistics
        $total = $tickets->count();
        $pending = $tickets->where('status', 'pending')->count();
        $diproses = $tickets->where('status', 'diproses')->count();
        $selesai = $tickets->where('status', 'selesai')->count();
        $ditolak = $tickets->where('status', 'ditolak')->count();
        $completionRate = $total > 0 ? round(($selesai / $total) * 100, 1) : 0;

        // Resolve Category Name
        $services = config('services_gateway.services', []);
        if ($categoryKey === 'all') {
            $categoryName = 'Semua Kategori (12 Layanan TIK)';
            $categorySlug = 'Semua_Layanan';
        } else {
            $categoryName = $services[$categoryKey]['name'] ?? ucwords(str_replace('_', ' ', $categoryKey));
            $categorySlug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $categoryName));
        }

        // Resolve Status Name
        $statusLabels = [
            'all' => 'Semua Status Aduan',
            'pending' => 'Pending (Menunggu Antrian)',
            'diproses' => 'Diproses (Sedang Ditangani)',
            'selesai' => 'Selesai (Tuntas)',
            'ditolak' => 'Ditolak',
        ];
        $statusLabel = $statusLabels[$status] ?? 'Semua Status';

        $summary = [
            'total' => $total,
            'pending' => $pending,
            'diproses' => $diproses,
            'selesai' => $selesai,
            'ditolak' => $ditolak,
            'completion_rate' => $completionRate,
            'category_key' => $categoryKey,
            'category_name' => $categoryName,
            'category_slug' => $categorySlug,
            'period_type' => $periodType,
            'period_label' => $periodLabel,
            'period_slug' => $periodSlug,
            'status' => $status,
            'status_label' => $statusLabel,
            'handled_by' => $handledBy,
            'handled_by_label' => $handledByLabel,
            'generated_at' => now()->translatedFormat('d F Y, H:i') . ' WIB',
            'generated_by' => auth()->user()?->name ?? 'Administrator ITSM BBPOM',
            'audit_id' => 'ISO-ITSM-' . now()->format('Ymd-His'),
        ];

        return [
            'tickets' => $tickets,
            'summary' => $summary,
        ];
    }
}
