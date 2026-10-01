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

    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int|array|null
    {
        return [
            'default' => 1,
            'sm' => 2,
            'md' => 3,
            'xl' => 5,
        ];
    }

    protected function getStats(): array
    {
        $totalTickets = Ticket::count();
        $pendingTickets = Ticket::where('status', 'pending')->count();
        $diprosesTickets = Ticket::where('status', 'diproses')->count();
        $selesaiTickets = Ticket::where('status', 'selesai')->count();
        $ditolakTickets = Ticket::where('status', 'ditolak')->count();

        // 6-month micro-trend sparklines
        $totalTrend = [];
        $pendingTrend = [];
        $diprosesTrend = [];
        $selesaiTrend = [];
        $ditolakTrend = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);

            $totalTrend[] = Ticket::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $pendingTrend[] = Ticket::where('status', 'pending')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $diprosesTrend[] = Ticket::where('status', 'diproses')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $selesaiTrend[] = Ticket::where('status', 'selesai')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $ditolakTrend[] = Ticket::where('status', 'ditolak')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }

        return [
            Stat::make('Total Pengaduan', $totalTickets)
                ->description('Seluruh tiket masuk')
                ->descriptionIcon('heroicon-m-ticket')
                ->chart($totalTrend)
                ->color('primary'),

            Stat::make('Tiket Pending', $pendingTickets)
                ->description('Menunggu verifikasi admin')
                ->descriptionIcon('heroicon-m-clock')
                ->chart($pendingTrend)
                ->color('warning'),

            Stat::make('Tiket Diproses', $diprosesTickets)
                ->description('Sedang ditangani teknisi')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->chart($diprosesTrend)
                ->color('info'),

            Stat::make('Tiket Selesai', $selesaiTickets)
                ->description('Kendala berhasil tuntas')
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart($selesaiTrend)
                ->color('success'),

            Stat::make('Tiket Ditolak', $ditolakTickets)
                ->description('Permohonan dibatalkan')
                ->descriptionIcon('heroicon-m-x-circle')
                ->chart($ditolakTrend)
                ->color('danger'),
        ];
    }
}
