<?php

namespace App\Filament\Widgets;

use App\Services\Tickets\TicketTrendChartService;
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

    public string $period = 'bulan';

    public ?string $startDate = null;

    public ?string $endDate = null;

    public function mount(): void
    {
        $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
    }

    public function selectFilter(string $status): void
    {
        $this->filter = ($this->filter === $status && $status !== 'all') ? 'all' : $status;
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
            $this->startDate ??= Carbon::now()->subDays(30)->format('Y-m-d');
            $this->endDate ??= Carbon::now()->format('Y-m-d');
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
        return app(TicketTrendChartService::class)->getStatusCounts($this->period, $this->startDate, $this->endDate);
    }

    protected function getData(): array
    {
        return app(TicketTrendChartService::class)->getChartData($this->period, $this->startDate, $this->endDate, $this->filter);
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): ?array
    {
        return app(TicketTrendChartService::class)->getChartOptions();
    }
}
