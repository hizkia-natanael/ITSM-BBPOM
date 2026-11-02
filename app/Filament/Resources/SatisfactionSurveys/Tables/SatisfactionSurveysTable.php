<?php

namespace App\Filament\Resources\SatisfactionSurveys\Tables;

use App\Filament\Resources\Tickets\TicketResource;
use App\Models\TicketSatisfactionSurvey;
use Filament\Actions\ViewAction;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SatisfactionSurveysTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticket.ticket_number')
                    ->label('Nomor Tiket')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary')
                    ->url(fn (TicketSatisfactionSurvey $record): string => TicketResource::getUrl('edit', ['record' => $record->ticket_id])),

                TextColumn::make('user.name')
                    ->label('Pegawai Pemohon')
                    ->searchable()
                    ->sortable()
                    ->description(fn (TicketSatisfactionSurvey $record): string => $record->user->nip ? 'NIP. ' . $record->user->nip : ($record->user->email ?? '')),

                TextColumn::make('average_rating')
                    ->label('Skor Rata-rata')
                    ->sortable()
                    ->badge()
                    ->color(Color::Amber)
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 1, '.', '') . ' / 5'),

                TextColumn::make('speed_rating')
                    ->label('Kecepatan')
                    ->alignCenter()
                    ->formatStateUsing(fn ($state): string => $state . '/5'),

                TextColumn::make('quality_rating')
                    ->label('Kualitas')
                    ->alignCenter()
                    ->formatStateUsing(fn ($state): string => $state . '/5'),

                TextColumn::make('communication_rating')
                    ->label('Komunikasi')
                    ->alignCenter()
                    ->formatStateUsing(fn ($state): string => $state . '/5'),

                TextColumn::make('system_rating')
                    ->label('Sistem ELITE')
                    ->alignCenter()
                    ->formatStateUsing(fn ($state): string => $state . '/5'),

                TextColumn::make('feedback')
                    ->label('Kritik & Masukan')
                    ->limit(40)
                    ->tooltip(fn (TextColumn $column): ?string => $column->getState())
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('Tanggal Ulasan')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('rating_group')
                    ->label('Tapis Bintang')
                    ->options([
                        '5' => 'Bintang 5 (Sempurna)',
                        '4' => 'Bintang 4 (Baik)',
                        '3' => 'Bintang 3 (Cukup)',
                        'low' => 'Bintang 1-2 (Perlu Perhatian)',
                    ])
                    ->query(function ($query, array $data) {
                        $val = $data['value'] ?? null;
                        if ($val === '5') $query->where('average_rating', '>=', 4.5);
                        elseif ($val === '4') $query->whereBetween('average_rating', [3.5, 4.49]);
                        elseif ($val === '3') $query->whereBetween('average_rating', [2.5, 3.49]);
                        elseif ($val === 'low') $query->where('average_rating', '<', 2.5);
                    }),
            ]);
    }
}
