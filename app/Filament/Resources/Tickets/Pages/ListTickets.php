<?php

namespace App\Filament\Resources\Tickets\Pages;

use App\Filament\Resources\Tickets\Schemas\TicketExportModalSchema;
use App\Filament\Resources\Tickets\TicketResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Livewire\Attributes\On;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    #[On('refresh-tickets')]
    public function refreshTickets(): void
    {
        // Livewire will automatically re-render the tickets table
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Ekspor Rekapitulasi Aduan')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->modalHeading('Ekspor Rekapitulasi Aduan')
                ->modalDescription('Pilih format berkas, kategori layanan TIK, dan periode waktu yang ingin diekspor.')
                ->modalSubmitActionLabel('Unduh Berkas')
                ->modalIcon('heroicon-o-document-arrow-down')
                ->modalWidth('4xl')
                ->form(TicketExportModalSchema::make())
                ->action(fn (array $data) => redirect()->route('admin.tickets.export', $data)),
        ];
    }
}
