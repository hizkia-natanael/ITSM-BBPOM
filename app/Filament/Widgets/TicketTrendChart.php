<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class TicketTrendChart extends ChartWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 7,
        'xl' => 7,
        '2xl' => 8,
    ];

    protected ?string $maxHeight = '230px';

    protected string $view = 'filament.widgets.ticket-trend-chart';

    public ?string $filter = 'all';

    public string $period = 'bulan'; // 'bulan', 'tahun', 'custom'

    public ?string $startDate = null;

    public ?string $endDate = null;

    public function mount(): void
    {
        $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
    }

    public function selectFilter(string $status): void
    {
        if ($this->filter === $status && $status !== 'all') {
            $this->filter = 'all';
        } else {
            $this->filter = $status;
        }
    }

    public function applyHoverFilter(string $status): void
    {
        if ($this->filter !== $status) {
            $this->filter = $status;
        }
    }

    public function setPeriod(string $period): void
    {
        $this->period = $period;

        if ($period === 'custom') {
            if (! $this->startDate) {
                $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
            }
            if (! $this->endDate) {
                $this->endDate = Carbon::now()->format('Y-m-d');
            }
        }
    }

    public function applyHoverPeriod(string $period): void
    {
        if ($this->period !== $period) {
            $this->setPeriod($period);
        }
    }

    public function applyCustomRange(): void
    {
        // Livewire updates the chart automatically
    }

    protected function getFilters(): ?array
    {
        return null;
    }

    public function getHeading(): ?string
    {
        if ($this->period === 'tahun') {
            return 'Tren Pengaduan Layanan TI (Per Tahun)';
        }

        if ($this->period === 'custom' && $this->startDate && $this->endDate) {
            $start = Carbon::parse($this->startDate)->translatedFormat('d M Y');
            $end = Carbon::parse($this->endDate)->translatedFormat('d M Y');
            return "Tren Pengaduan Layanan TI (Rentang: {$start} - {$end})";
        }

        return 'Tren Pengaduan Layanan TI (Per Bulan)';
    }

    public function getDescription(): ?string
    {
        return 'Visualisasi pergerakan pengaduan per status: Pending, Diproses, Selesai, dan Ditolak';
    }

    public function getStatusCounts(): array
    {
        $query = Ticket::query();

        if ($this->period === 'bulan') {
            $from = Carbon::now()->subMonths(5)->startOfMonth();
            $query->where('created_at', '>=', $from);
        } elseif ($this->period === 'tahun') {
            $from = Carbon::now()->subYears(4)->startOfYear();
            $query->where('created_at', '>=', $from);
        } elseif ($this->period === 'custom' && $this->startDate && $this->endDate) {
            $start = Carbon::parse($this->startDate)->startOfDay();
            $end = Carbon::parse($this->endDate)->endOfDay();
            if ($start->gt($end)) {
                [$start, $end] = [$end, $start];
            }
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

    protected function getData(): array
    {
        $labels = [];
        $pendingData = [];
        $diprosesData = [];
        $selesaiData = [];
        $ditolakData = [];

        if ($this->period === 'tahun') {
            // 5 Tahun Terakhir
            for ($i = 4; $i >= 0; $i--) {
                $yearDate = Carbon::now()->subYears($i);
                $labels[] = (string) $yearDate->year;
                $start = (clone $yearDate)->startOfYear();
                $end = (clone $yearDate)->endOfYear();

                $pendingData[] = Ticket::where('status', 'pending')->whereBetween('created_at', [$start, $end])->count();
                $diprosesData[] = Ticket::where('status', 'diproses')->whereBetween('created_at', [$start, $end])->count();
                $selesaiData[] = Ticket::where('status', 'selesai')->whereBetween('created_at', [$start, $end])->count();
                $ditolakData[] = Ticket::where('status', 'ditolak')->whereBetween('created_at', [$start, $end])->count();
            }
        } elseif ($this->period === 'custom' && $this->startDate && $this->endDate) {
            // Rentang Waktu Tertentu
            $start = Carbon::parse($this->startDate)->startOfDay();
            $end = Carbon::parse($this->endDate)->endOfDay();

            if ($start->gt($end)) {
                [$start, $end] = [$end, $start];
            }

            $diffDays = $start->diffInDays($end);

            if ($diffDays <= 31) {
                // Group per hari
                $curr = clone $start;
                while ($curr->lte($end)) {
                    $labels[] = $curr->translatedFormat('d M');
                    $dayStart = (clone $curr)->startOfDay();
                    $dayEnd = (clone $curr)->endOfDay();

                    $pendingData[] = Ticket::where('status', 'pending')->whereBetween('created_at', [$dayStart, $dayEnd])->count();
                    $diprosesData[] = Ticket::where('status', 'diproses')->whereBetween('created_at', [$dayStart, $dayEnd])->count();
                    $selesaiData[] = Ticket::where('status', 'selesai')->whereBetween('created_at', [$dayStart, $dayEnd])->count();
                    $ditolakData[] = Ticket::where('status', 'ditolak')->whereBetween('created_at', [$dayStart, $dayEnd])->count();

                    $curr->addDay();
                }
            } else {
                // Group per bulan
                $curr = (clone $start)->startOfMonth();
                $endMonth = (clone $end)->endOfMonth();

                while ($curr->lte($endMonth)) {
                    $labels[] = $curr->translatedFormat('M Y');
                    $monthStart = (clone $curr)->startOfMonth();
                    $monthEnd = (clone $curr)->endOfMonth();

                    $pendingData[] = Ticket::where('status', 'pending')->whereBetween('created_at', [$monthStart, $monthEnd])->count();
                    $diprosesData[] = Ticket::where('status', 'diproses')->whereBetween('created_at', [$monthStart, $monthEnd])->count();
                    $selesaiData[] = Ticket::where('status', 'selesai')->whereBetween('created_at', [$monthStart, $monthEnd])->count();
                    $ditolakData[] = Ticket::where('status', 'ditolak')->whereBetween('created_at', [$monthStart, $monthEnd])->count();

                    $curr->addMonth();
                }
            }
        } else {
            // Default: Per Bulan (6 Bulan Terakhir)
            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $labels[] = $month->translatedFormat('M Y');
                $start = (clone $month)->startOfMonth();
                $end = (clone $month)->endOfMonth();

                $pendingData[] = Ticket::where('status', 'pending')->whereBetween('created_at', [$start, $end])->count();
                $diprosesData[] = Ticket::where('status', 'diproses')->whereBetween('created_at', [$start, $end])->count();
                $selesaiData[] = Ticket::where('status', 'selesai')->whereBetween('created_at', [$start, $end])->count();
                $ditolakData[] = Ticket::where('status', 'ditolak')->whereBetween('created_at', [$start, $end])->count();
            }
        }

        $allDatasets = [
            'pending' => [
                'label' => 'Pending (Menunggu)',
                'data' => $pendingData,
                'borderColor' => '#d97706', // amber-600
                'backgroundColor' => 'rgba(217, 119, 6, 0.15)',
                'fill' => true,
                'tension' => 0.35,
            ],
            'diproses' => [
                'label' => 'Diproses (Teknisi)',
                'data' => $diprosesData,
                'borderColor' => '#2563eb', // blue-600
                'backgroundColor' => 'rgba(37, 99, 235, 0.15)',
                'fill' => true,
                'tension' => 0.35,
            ],
            'selesai' => [
                'label' => 'Selesai (Tuntas)',
                'data' => $selesaiData,
                'borderColor' => '#059669', // emerald-600
                'backgroundColor' => 'rgba(5, 150, 105, 0.15)',
                'fill' => true,
                'tension' => 0.35,
            ],
            'ditolak' => [
                'label' => 'Ditolak',
                'data' => $ditolakData,
                'borderColor' => '#e11d48', // rose-600
                'backgroundColor' => 'rgba(225, 29, 72, 0.15)',
                'fill' => true,
                'tension' => 0.35,
            ],
        ];

        foreach ($allDatasets as $key => &$ds) {
            if ($this->filter && $this->filter !== 'all') {
                $ds['hidden'] = ($this->filter !== $key);
            } else {
                $ds['hidden'] = false;
            }
        }
        unset($ds);

        return [
            'datasets' => array_values($allDatasets),
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): ?array
    {
        return [
            'maintainAspectRatio' => false,
            'responsive' => true,
            'animation' => [
                'duration' => 500,
                'easing' => 'easeOutQuart',
            ],
            'transitions' => [
                'active' => [
                    'animation' => [
                        'duration' => 300,
                    ],
                ],
                'resize' => [
                    'animation' => [
                        'duration' => 450,
                        'easing' => 'easeOutQuart',
                    ],
                ],
            ],
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
            'layout' => [
                'padding' => [
                    'top' => 6,
                    'bottom' => 4,
                    'left' => 4,
                    'right' => 4,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'padding' => 8,
                    'cornerRadius' => 8,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                        'font' => [
                            'size' => 11,
                        ],
                    ],
                    'grid' => [
                        'color' => 'rgba(226, 232, 240, 0.6)',
                    ],
                ],
                'x' => [
                    'ticks' => [
                        'font' => [
                            'size' => 11,
                        ],
                    ],
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }
}
