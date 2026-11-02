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
                        TicketFormIdentitySection::make(),

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
                            Action::make('save')
                                ->label('Simpan Perubahan')
                                ->submit('save')
                                ->color('primary')
                                ->visible(fn () => auth()->user()?->isAdmin() ?? false)
                                ->keyBindings(['mod+s']),
                            Action::make('cancel')
                                ->label(fn () => (auth()->user()?->isKetuaTim() ?? false) ? 'Kembali' : 'Batal')
                                ->color('gray')
                                ->url(fn () => TicketResource::getUrl('index')),
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
                        'diterima' => 'Diterima (Disetujui / Terverifikasi)',
                        'diproses' => 'Diproses (Sedang Ditangani Teknisi)',
                        'selesai' => 'Selesai (Kendala Tuntas Diperbaiki)',
                        'ditolak' => 'Ditolak (Dibatalkan / Tidak Sesuai)',
                    ])
                    ->required()->native(false)->position('bottom')->selectablePlaceholder(false)
                    ->disabled(fn () => auth()->user()?->isKetuaTim() ?? false)
                    ->helperText('Tahapan status tiket: Pending -> Diterima -> Diproses -> Selesai / Ditolak.'),

                TextInput::make('handled_by_display')
                    ->label('Penanggung Jawab (Alur Admin)')
                    ->disabled()->dehydrated(false)
                    ->formatStateUsing(function ($record) {
                        if (!$record) return (auth()->user()?->name ?? 'Admin') . ' (Otomatis)';
                        $parts = [];
                        if ($record->acceptedBy) $parts[] = 'Diterima: ' . $record->acceptedBy->name;
                        if ($record->processedBy) $parts[] = 'Diproses: ' . $record->processedBy->name;
                        if ($record->resolvedBy) $parts[] = 'Selesai/Tolak: ' . $record->resolvedBy->name;
                        return !empty($parts) ? implode(' | ', $parts) : ($record->handledBy?->name ?? 'Belum Ditugaskan');
                    })
                    ->helperText(function ($record) {
                        $curr = auth()->user()?->name ?? 'Admin';
                        return (auth()->user()?->isKetuaTim() ?? false)
                            ? 'Mode monitoring: Anda melihat riwayat admin penanggung jawab di setiap status.'
                            : "Akun login Anda ({$curr}) otomatis dicatat saat mengubah status tiket.";
                    }),
            ])
            ->columnSpanFull();
    }
}
