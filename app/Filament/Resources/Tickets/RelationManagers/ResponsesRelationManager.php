<?php

namespace App\Filament\Resources\Tickets\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ResponsesRelationManager extends RelationManager
{
    protected static string $relationship = 'responses';

    protected static ?string $title = 'Riwayat Tanggapan & Diskusi Pengaduan';

    protected static bool $isLazy = false;

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('filament.resources.tickets.relation-managers.responses-stream', [
            'ticket' => $this->getOwnerRecord(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('response_text')
                    ->label('Isi Tanggapan / Balasan Petugas IT')
                    ->placeholder('Tuliskan respon, instruksi, atau perkembangan penanganan kendala...')
                    ->required()
                    ->rows(4),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('response_text')
            ->heading('Riwayat Tanggapan & Diskusi')
            ->description('Percakapan antara pelapor dan tim layanan IT BBPOM Semarang.')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Pengirim')
                    ->badge()
                    ->color(fn ($record) => $record->user?->isAdmin() ? 'success' : 'gray')
                    ->formatStateUsing(fn ($record) => ($record->user?->isAdmin() ? 'Petugas IT: ' : 'Pelapor: ') . ($record->user?->name ?? 'Anonim')),

                TextColumn::make('response_text')
                    ->label('Isi Pesan')
                    ->wrap()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Waktu Dikirim')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Balas / Beri Tanggapan')
                    ->icon('heroicon-m-chat-bubble-left-right')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();
                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
