<?php

namespace App\Filament\Resources\Tickets\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;

class TicketFormIdentitySection
{
    public static function make(): Grid
    {
        return Grid::make(['default' => 1, 'sm' => 2, 'lg' => 4])
            ->schema([
                TextInput::make('ticket_number')->label('Nomor Tiket')->disabled()->dehydrated(false),
                TextInput::make('created_at')->label('Waktu Pengajuan')->disabled()->dehydrated(false)
                    ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->translatedFormat('d F Y, H:i') . ' WIB' : '-'),
                TextInput::make('user.name')->label('Nama Pemohon (Pelapor)')->disabled()->dehydrated(false)
                    ->formatStateUsing(fn ($record) => $record?->user?->name ?? '-'),
                TextInput::make('category.name')->label('Kategori Layanan')->disabled()->dehydrated(false)
                    ->formatStateUsing(fn ($record) => $record?->service_name ?? '-'),
            ])
            ->columnSpanFull();
    }
}
