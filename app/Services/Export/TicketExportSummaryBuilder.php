<?php

namespace App\Services\Export;

use App\Models\User;
use Illuminate\Support\Collection;

class TicketExportSummaryBuilder
{
    public function build(
        Collection $tickets,
        array $filters,
        string $periodLabel,
        string $periodSlug,
        string $handledByLabel,
    ): array {
        $categoryKey = $filters['category'] ?? 'all';
        $periodType = $filters['period_type'] ?? 'all';
        $status = $filters['status'] ?? 'all';
        $handledBy = $filters['handled_by'] ?? 'all';

        $total = $tickets->count();
        $pending = $tickets->where('status', 'pending')->count();
        $diproses = $tickets->where('status', 'diproses')->count();
        $selesai = $tickets->where('status', 'selesai')->count();
        $ditolak = $tickets->where('status', 'ditolak')->count();
        $completionRate = $total > 0 ? round(($selesai / $total) * 100, 1) : 0;

        $services = config('services_gateway.services', []);
        if ($categoryKey === 'all') {
            $categoryName = 'Semua Kategori (12 Layanan TIK)';
            $categorySlug = 'Semua_Layanan';
        } else {
            $categoryName = $services[$categoryKey]['name'] ?? ucwords(str_replace('_', ' ', $categoryKey));
            $categorySlug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $categoryName));
        }

        $statusLabels = [
            'all' => 'Semua Status Aduan',
            'pending' => 'Pending (Menunggu Antrian)',
            'diproses' => 'Diproses (Sedang Ditangani)',
            'selesai' => 'Selesai (Tuntas)',
            'ditolak' => 'Ditolak',
        ];
        $statusLabel = $statusLabels[$status] ?? 'Semua Status';

        $signatoryAdmin = auth()->user();
        if (!$signatoryAdmin && !empty($filters['current_user_id'])) {
            $signatoryAdmin = User::find($filters['current_user_id']);
        }

        return [
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
            'generated_by' => $signatoryAdmin?->name ?? 'Administrator ITSM BBPOM',
            'generated_by_nip' => $signatoryAdmin?->nip ?? null,
            'audit_id' => 'ISO-ITSM-' . now()->format('Ymd-His'),
        ];
    }
}
