<?php

namespace App\Services\Export;

use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class TicketQueryBuilder
{
    protected array $serviceSlugMap = [
        'sipt' => ['sipt'], 'srikandi' => ['srikandi'],
        'email_corporate' => ['email-corporate', 'email_corporate'],
        'sertifikat_bse' => ['sertifikat-bsre', 'sertifikat_bsre', 'sertifikat_bse'],
        'zoom' => ['zoom'], 'formulir_kegiatan' => ['formulir-kegiatan', 'formulir_kegiatan'],
        'internet' => ['internet', 'jaringan'], 'subsite' => ['subsite'],
        'infografis' => ['infografis'], 'install_windows' => ['install-windows', 'install_windows'],
        'lisensi_office' => ['lisensi-office', 'lisensi_office'], 'lainnya' => ['lainnya'],
    ];

    public static function stripMarkdown(?string $text): string
    {
        if (empty($text)) return '';
        $text = preg_replace(['/\*\*([^*]+)\*\*/', '/(?<!\*)\*([^*]+)\*(?!\*)/', '/^#+\s*/m', '/^\s*[\*•]\s+/m', '/^---+$/m'], ['$1', '$1', '', '- ', ''], $text);
        return trim(preg_replace("/\n{3,}/", "\n\n", str_replace('**', '', $text)));
    }

    public function build(array $filters = []): array
    {
        $query = Ticket::query()->with([
            'user', 'handledBy', 'category', 'responses', 'bsreDetail', 'zoomDetail',
            'kegiatanDetail', 'windowsDetail', 'officeDetail', 'lainnyaDetail',
        ]);

        $categoryKey = $filters['category'] ?? 'all';
        $status = $filters['status'] ?? 'all';

        if (!empty($categoryKey) && $categoryKey !== 'all') {
            $query->where(function (Builder $q) use ($categoryKey) {
                $q->where('service_key', $categoryKey);
                if (isset($this->serviceSlugMap[$categoryKey])) {
                    $q->orWhereHas('category', fn (Builder $cq) => $cq->whereIn('slug', $this->serviceSlugMap[$categoryKey]));
                }
            });
        }

        if (!empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        $handledByLabel = $this->applyHandledByFilter($query, $filters['handled_by'] ?? 'all', $filters);
        [$periodLabel, $periodSlug] = $this->applyDateRangeFilter($query, $filters);

        $tickets = $query->orderBy('created_at', 'desc')->get();
        foreach ($tickets as $ticket) {
            $ticket->clean_description = self::stripMarkdown($ticket->description);
            $ticket->clean_subject = self::stripMarkdown($ticket->subject);
        }

        return [
            'tickets' => $tickets,
            'summary' => (new TicketExportSummaryBuilder())->build($tickets, $filters, $periodLabel, $periodSlug, $handledByLabel),
        ];
    }

    private function applyHandledByFilter(Builder $query, mixed $handledBy, array $filters): string
    {
        if ($handledBy === 'me') {
            $uid = auth()->id() ?? ($filters['current_user_id'] ?? null);
            if ($uid) {
                $query->where('handled_by_id', $uid);
                return 'Ditangani: ' . (auth()->user()?->name ?? 'Saya Sendiri');
            }
            return 'Ditangani: Saya Sendiri';
        }
        if ($handledBy === 'unassigned') {
            $query->whereNull('handled_by_id');
            return 'Belum Ditugaskan';
        }
        if (is_numeric($handledBy)) {
            $query->where('handled_by_id', (int) $handledBy);
            return 'Petugas: ' . (User::find($handledBy)?->name ?? "Admin #{$handledBy}");
        }
        return 'Semua Petugas IT';
    }

    private function applyDateRangeFilter(Builder $query, array $filters): array
    {
        $periodType = $filters['period_type'] ?? 'all';
        if ($periodType === 'yearly') {
            $year = !empty($filters['year']) ? (int) $filters['year'] : (int) now()->format('Y');
            $query->whereYear('created_at', $year);
            return ["Periode Tahun {$year}", "Tahun_{$year}"];
        }
        if ($periodType === 'month_year') {
            $month = !empty($filters['month']) ? (int) $filters['month'] : (int) now()->format('n');
            $year = !empty($filters['year']) ? (int) $filters['year'] : (int) now()->format('Y');
            $query->whereYear('created_at', $year)->whereMonth('created_at', $month);
            $months = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
            $mName = $months[$month] ?? Carbon::createFromDate($year, $month, 1)->translatedFormat('F');
            return ["Periode {$mName} {$year}", "{$year}_{$month}"];
        }
        if ($periodType === 'custom') {
            [$s, $e] = [$filters['start_date'] ?? null, $filters['end_date'] ?? null];
            if ($s && $e) {
                $query->whereDate('created_at', '>=', $s)->whereDate('created_at', '<=', $e);
                return [Carbon::parse($s)->format('d M Y') . ' s/d ' . Carbon::parse($e)->format('d M Y'), Carbon::parse($s)->format('Ymd') . '_sd_' . Carbon::parse($e)->format('Ymd')];
            }
            if ($s || $e) {
                $query->whereDate('created_at', $s ? '>=' : '<=', $s ?: $e);
                $prefix = $s ? 'Mulai' : 'Hingga';
                return ["{$prefix} " . Carbon::parse($s ?: $e)->format('d M Y'), "{$prefix}_" . Carbon::parse($s ?: $e)->format('Ymd')];
            }
        }
        return ['Seluruh Aduan (Semua Waktu)', 'Semua_Waktu'];
    }
}
