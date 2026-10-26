<?php

namespace App\Filament\Resources\Tickets\Schemas;

use App\Filament\Resources\Tickets\Schemas\Specifications\StandardSpecification;
use App\Filament\Resources\Tickets\TicketResource;
use App\Services\Export\TicketQueryBuilder;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Status Penanganan Layanan')
                    ->description('Kelola progres penanganan tiket ITSM oleh tim layanan IT BBPOM Semarang.')
                    ->extraAttributes(['class' => 'ticket-master-card'])
                    ->schema([
                        self::statusAndHandlerGrid(),
                        self::identityGrid(),

                        TextInput::make('subject')
                            ->label('Subjek / Judul Kendala')
                            ->disabled()->dehydrated(false)
                            ->formatStateUsing(fn ($state) => TicketQueryBuilder::stripMarkdown($state ?? ''))
                            ->columnSpanFull(),

                        StandardSpecification::make(),

                        ViewField::make('attachments_list')
                            ->view('filament.resources.tickets.components.attachments-list')
                            ->hiddenLabel()
                            ->visible(fn ($record) => $record && (
                                ($record->attachments && $record->attachments->isNotEmpty())
                                || !empty($record->attachment)
                                || !empty($record->bsreDetail?->file_lampiran)
                                || !empty($record->zoomDetail?->file_lampiran)
                                || !empty($record->kegiatanDetail?->file_lampiran)
                                || !empty($record->windowsDetail?->file_lampiran)
                                || !empty($record->officeDetail?->file_lampiran)
                                || !empty($record->lainnyaDetail?->file_lampiran)
                            ))
                            ->columnSpanFull(),

                        Actions::make([
                            Action::make('save')->label('Simpan Perubahan')->submit('save')->color('primary')->keyBindings(['mod+s']),
                            Action::make('cancel')->label('Batal')->color('gray')->url(fn () => TicketResource::getUrl('index')),
                        ])
                        ->columnSpanFull()
                        ->alignment(Alignment::Start)
                        ->extraAttributes([
                            'class' => 'ticket-form-bottom-actions',
                            'style' => 'width: 100% !important; display: flex !important; justify-content: flex-start !important; align-items: center !important; margin-left: 0 !important; margin-right: auto !important; border-top: 1px solid #e2e8f0 !important; padding-top: 1.25rem !important; margin-top: 0.75rem !important;',
                        ]),
                    ]),
            ]);
    }

    private static function statusAndHandlerGrid(): Grid
    {
        return Grid::make(['default' => 1, 'md' => 2])
            ->schema([
                Select::make('status')
                    ->label('Status Pengaduan')
                    ->options([
                        'pending' => 'Pending (Menunggu Antrian IT)',
                        'diproses' => 'Diproses (Sedang Ditangani Teknisi)',
                        'selesai' => 'Selesai (Kendala Tuntas Diperbaiki)',
                        'ditolak' => 'Ditolak (Dibatalkan / Di Luar Wewenang)',
                    ])
                    ->required()->native(false)->position('bottom')->selectablePlaceholder(false)
                    ->helperText('Perbarui status tiket ini agar pelapor dapat melihat progres langsung di dashboard.'),

                TextInput::make('handled_by_display')
                    ->label('Penanggung Jawab (Teknisi IT)')
                    ->disabled()->dehydrated(false)
                    ->formatStateUsing(fn ($record) => $record?->handledBy?->name ?? ((auth()->user()?->name ?? 'Admin') . ' (Otomatis akun login)'))
                    ->helperText(function ($record) {
                        $curr = auth()->user()?->name ?? 'Admin';
                        return ($record?->handledBy && $record->handledBy->id !== auth()->id())
                            ? "Saat ini: {$record->handledBy->name}. Akan otomatis berganti ke {$curr} jika Anda memperbarui status."
                            : "Otomatis terisi akun login Anda ({$curr}) saat mengubah status tiket.";
                    }),
            ])
            ->columnSpanFull();
    }

    private static function identityGrid(): Grid
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
