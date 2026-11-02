<?php

namespace App\Services\Tickets;

use App\Models\Ticket;
use Carbon\Carbon;

class TicketTrendChartService
{
    public function getStatusCounts(string $period, ?string $startDate, ?string $endDate): array
    {
        $query = Ticket::query();

        if ($period === 'bulan') {
            $query->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth());
        } elseif ($period === 'tahun') {
            $query->where('created_at', '>=', Carbon::now()->subYears(4)->startOfYear());
        } elseif ($period === 'custom' && $startDate && $endDate) {
            [$start, $end] = $this->resolveCustomRange($startDate, $endDate);
            $query->whereBetween('created_at', [$start, $end]);
        }
        return [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'diproses' => (clone $query)->where('status', 'diproses')->count(),
            'selesai' => (clone $query)->where('status', 'selesai')->count(),
            'ditolak' => (clone $query)->where('status', 'ditolak')->count(),
        ];
    }

    public function getChartData(string $period, ?string $startDate, ?string $endDate, ?string $filter): array
    {
        $buckets = $this->buildBuckets($period, $startDate, $endDate);
        $labels = [];
        $series = ['pending' => [], 'diproses' => [], 'selesai' => [], 'ditolak' => []];
        foreach ($buckets as [$label, $start, $end]) {
            $labels[] = $label;
            foreach (array_keys($series) as $status) {
                $series[$status][] = Ticket::where('status', $status)->whereBetween('created_at', [$start, $end])->count();
            }
        }
        $meta = [
            'pending' => ['Pending (Menunggu)', '#d97706', 'rgba(217, 119, 6, 0.15)'],
            'diproses' => ['Diproses (Teknisi)', '#2563eb', 'rgba(37, 99, 235, 0.15)'],
            'selesai' => ['Selesai (Tuntas)', '#059669', 'rgba(5, 150, 105, 0.15)'],
            'ditolak' => ['Ditolak', '#e11d48', 'rgba(225, 29, 72, 0.15)'],
        ];
        $datasets = [];
        foreach ($meta as $key => [$label, $border, $bg]) {
            $datasets[] = [
                'label' => $label,
                'data' => $series[$key],
                'borderColor' => $border,
                'backgroundColor' => $bg,
                'fill' => true,
                'tension' => 0.35,
                'hidden' => ($filter && $filter !== 'all') ? ($filter !== $key) : false,
            ];
        }
        return ['datasets' => $datasets, 'labels' => $labels];
    }

    public function getChartOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'responsive' => true,
            'animation' => ['duration' => 500, 'easing' => 'easeOutQuart'],
            'transitions' => [
                'active' => ['animation' => ['duration' => 300]],
                'resize' => ['animation' => ['duration' => 450, 'easing' => 'easeOutQuart']],
            ],
            'interaction' => ['mode' => 'index', 'intersect' => false],
            'layout' => ['padding' => ['top' => 6, 'bottom' => 4, 'left' => 4, 'right' => 4]],
            'plugins' => [
                'legend' => ['display' => false],
                'tooltip' => ['padding' => 8, 'cornerRadius' => 8],
            ],
            'scales' => [
                'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0, 'font' => ['size' => 11]], 'grid' => ['color' => 'rgba(226, 232, 240, 0.6)']],
                'x' => ['ticks' => ['font' => ['size' => 11]], 'grid' => ['display' => false]],
            ],
        ];
    }

    private function buildBuckets(string $period, ?string $startDate, ?string $endDate): array
    {
        $buckets = [];
        if ($period === 'tahun') {
            for ($i = 4; $i >= 0; $i--) {
                $d = Carbon::now()->subYears($i);
                $buckets[] = [(string) $d->year, (clone $d)->startOfYear(), (clone $d)->endOfYear()];
            }
        } elseif ($period === 'custom' && $startDate && $endDate) {
            [$start, $end] = $this->resolveCustomRange($startDate, $endDate);
            if ($start->diffInDays($end) <= 31) {
                for ($c = clone $start; $c->lte($end); $c->addDay()) {
                    $buckets[] = [$c->translatedFormat('d M'), (clone $c)->startOfDay(), (clone $c)->endOfDay()];
                }
            } else {
                for ($c = (clone $start)->startOfMonth(), $endM = (clone $end)->endOfMonth(); $c->lte($endM); $c->addMonth()) {
                    $buckets[] = [$c->translatedFormat('M Y'), (clone $c)->startOfMonth(), (clone $c)->endOfMonth()];
                }
            }
        } else {
            for ($i = 5; $i >= 0; $i--) {
                $m = Carbon::now()->subMonths($i);
                $buckets[] = [$m->translatedFormat('M Y'), (clone $m)->startOfMonth(), (clone $m)->endOfMonth()];
            }
        }
        return $buckets;
    }

    private function resolveCustomRange(string $startDate, string $endDate): array
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();
        return $start->gt($end) ? [$end, $start] : [$start, $end];
    }
}
