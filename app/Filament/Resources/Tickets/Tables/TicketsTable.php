<?php

namespace App\Filament\Resources\Tickets\Tables;

use App\Filament\Resources\Tickets\TicketResource;
use App\Models\Ticket;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn (Ticket $record): string => TicketResource::getUrl('edit', ['record' => $record]))
            ->columns([
                TextColumn::make('ticket_number')
                    ->label('No. Tiket')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary')
                    ->url(fn (Ticket $record): string => TicketResource::getUrl('edit', ['record' => $record])),

                TextColumn::make('user.name')
                    ->label('Pelapor')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->user?->email),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('subject')
                    ->label('Subjek Kendala')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->subject),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'diterima' => 'primary',
                        'diproses' => 'info',
                        'selesai' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'diterima' => 'Diterima',
                        'diproses' => 'Diproses',
                        'selesai' => 'Selesai',
                        'ditolak' => 'Ditolak',
                        default => ucfirst($state),
                    })
                    ->sortable(),

                TextColumn::make('handledBy.name')
                    ->label('Petugas IT')
                    ->placeholder('Belum Ditugaskan')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('responses_count')
                    ->counts('responses')
                    ->label('Tanggapan')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->label('Waktu Masuk')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Kendala')
                    ->options([
                        'pending' => 'Pending',
                        'diterima' => 'Diterima',
                        'diproses' => 'Diproses',
                        'selesai' => 'Selesai',
                        'ditolak' => 'Ditolak',
                    ]),

                SelectFilter::make('handled_by_id')
                    ->relationship('handledBy', 'name', fn ($query) => $query->whereIn('role', ['admin', 'ketua_tim']))
                    ->label('Petugas IT (Admin)'),

                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Kategori Layanan'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label(fn () => (auth()->user()?->isKetuaTim() ?? false) ? 'Pantau Rincian' : 'Tindak Lanjut & Balas')
                    ->icon(fn () => (auth()->user()?->isKetuaTim() ?? false) ? 'heroicon-m-eye' : 'heroicon-m-pencil-square'),
                DeleteAction::make()->visible(fn () => auth()->user()?->isAdmin() ?? false),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn () => auth()->user()?->isAdmin() ?? false),
                ]),
            ]);
    }
}
