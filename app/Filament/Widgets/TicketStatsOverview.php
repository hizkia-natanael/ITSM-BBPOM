<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TicketStatsOverview extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '15s';

    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int|array|null
    {
        return [
            'default' => 1,
            'sm' => 2,
            'md' => 3,
            'xl' => 6,
        ];
    }

    protected function getStats(): array
    {
        $trends = $this->buildMonthlyTrends();

        return [
            Stat::make('Total Pengaduan', Ticket::count())
                ->description('Seluruh tiket masuk')
                ->descriptionIcon('heroicon-m-ticket')
                ->chart($trends['total'])
                ->color('primary'),

            Stat::make('Tiket Pending', Ticket::where('status', 'pending')->count())
                ->description('Menunggu verifikasi admin')
                ->descriptionIcon('heroicon-m-clock')
                ->chart($trends['pending'])
                ->color('warning'),

            Stat::make('Tiket Diterima', Ticket::where('status', 'diterima')->count())
                ->description('Disetujui & masuk antrean')
                ->descriptionIcon('heroicon-m-inbox-arrow-down')
                ->chart($trends['diterima'])
                ->color('primary'),

            Stat::make('Tiket Diproses', Ticket::where('status', 'diproses')->count())
                ->description('Sedang ditangani teknisi')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->chart($trends['diproses'])
                ->color('info'),

            Stat::make('Tiket Selesai', Ticket::where('status', 'selesai')->count())
                ->description('Kendala berhasil tuntas')
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart($trends['selesai'])
                ->color('success'),

            Stat::make('Tiket Ditolak', Ticket::where('status', 'ditolak')->count())
                ->description('Permohonan dibatalkan')
                ->descriptionIcon('heroicon-m-x-circle')
                ->chart($trends['ditolak'])
                ->color('danger'),
        ];
    }

    /**
     * @return array<string, list<int>>
     */
    private function buildMonthlyTrends(): array
    {
        $trends = ['total' => [], 'pending' => [], 'diterima' => [], 'diproses' => [], 'selesai' => [], 'ditolak' => []];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $base = Ticket::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month);

            $trends['total'][] = (clone $base)->count();
            foreach (['pending', 'diterima', 'diproses', 'selesai', 'ditolak'] as $status) {
                $trends[$status][] = (clone $base)->where('status', $status)->count();
            }
        }

        return $trends;
    }
}
