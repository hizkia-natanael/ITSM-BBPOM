<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TicketStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalTickets = Ticket::count();
        $pendingTickets = Ticket::where('status', 'pending')->count();
        $diprosesTickets = Ticket::where('status', 'diproses')->count();
        $selesaiTickets = Ticket::where('status', 'selesai')->count();

        return [
            Stat::make('Total Tiket', $totalTickets)
                ->description('Seluruh pengaduan masuk')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('primary'),

            Stat::make('Tiket Pending', $pendingTickets)
                ->description('Menunggu verifikasi admin')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Tiket Diproses', $diprosesTickets)
                ->description('Sedang ditangani teknisi')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),

            Stat::make('Tiket Selesai', $selesaiTickets)
                ->description('Kendala berhasil tuntas')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
