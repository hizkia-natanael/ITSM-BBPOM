<?php

namespace App\Filament\Resources\Tickets\Schemas;

use App\Filament\Resources\Tickets\Schemas\Specifications\BsreSpecification;
use App\Filament\Resources\Tickets\Schemas\Specifications\KegiatanSpecification;
use App\Filament\Resources\Tickets\Schemas\Specifications\LainnyaSpecification;
use App\Filament\Resources\Tickets\Schemas\Specifications\OfficeSpecification;
use App\Filament\Resources\Tickets\Schemas\Specifications\StandardSpecification;
use App\Filament\Resources\Tickets\Schemas\Specifications\WindowsSpecification;
use App\Filament\Resources\Tickets\Schemas\Specifications\ZoomSpecification;
use App\Filament\Resources\Tickets\TicketResource;
use App\Services\Export\TicketQueryBuilder;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\HtmlString;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Status Penanganan Layanan')
                    ->description('Kelola progres penanganan tiket ITSM oleh tim teknisi IT BBPOM Semarang.')
                    ->extraAttributes(['class' => 'ticket-master-card'])
                    ->schema([
                        // ==========================================
                        // 1. ZONA PENANGANAN & STATUS (ADMIN ACTION)
                        // ==========================================
                        Grid::make(['default' => 1, 'md' => 2])
                            ->schema([
                                Select::make('status')
                                    ->label('Status Pengaduan')
                                    ->options([
                                        'pending' => 'Pending (Menunggu Antrian IT)',
                                        'diproses' => 'Diproses (Sedang Ditangani Teknisi)',
                                        'selesai' => 'Selesai (Kendala Tuntas Diperbaiki)',
                                        'ditolak' => 'Ditolak (Dibatalkan / Di Luar Wewenang)',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->position('bottom')
                                    ->selectablePlaceholder(false)
                                    ->helperText('Perbarui status tiket ini agar pelapor dapat melihat progres langsung di dashboard.'),

                                TextInput::make('handled_by_display')
                                    ->label('Penanggung Jawab (Teknisi IT)')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(function ($record) {
                                        if ($record && $record->handledBy) {
                                            return $record->handledBy->name;
                                        }
                                        return (auth()->user()?->name ?? 'Admin') . ' (Otomatis akun login)';
                                    })
                                    ->helperText(function ($record) {
                                        $currentUser = auth()->user()?->name ?? 'Admin';
                                        if ($record && $record->handledBy && $record->handledBy->id !== auth()->id()) {
                                            return "Saat ini: {$record->handledBy->name}. Akan otomatis berganti ke {$currentUser} jika Anda memperbarui status.";
                                        }
                                        return "Otomatis terisi akun login Anda ({$currentUser}) saat mengubah status tiket.";
                                    }),
                            ])
                            ->columnSpanFull(),

                        // ==========================================
                        // 2. IDENTITAS POKOK PENGADUAN
                        // ==========================================
                        Grid::make(['default' => 1, 'sm' => 2, 'lg' => 4])
                            ->schema([
                                TextInput::make('ticket_number')
                                    ->label('Nomor Tiket')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('created_at')
                                    ->label('Waktu Pengajuan')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->translatedFormat('d F Y, H:i') . ' WIB' : '-'),

                                TextInput::make('user.name')
                                    ->label('Nama Pemohon (Pelapor)')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(fn ($record) => $record?->user?->name ?? '-'),

                                TextInput::make('category.name')
                                    ->label('Kategori Layanan')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(function ($record) {
                                        if (!$record) return '-';
                                        if ($record->service_key) {
                                            $services = config('services_gateway.services', []);
                                            if (isset($services[$record->service_key]['name'])) {
                                                return $services[$record->service_key]['name'];
                                            }
                                        }
                                        return $record->category?->name ?? 'Layanan TIK';
                                    }),
                            ])
                            ->columnSpanFull(),

                        // ==========================================
                        // 3. SUBJEK ADUAN
                        // ==========================================
                        TextInput::make('subject')
                            ->label('Subjek / Judul Kendala')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state) => TicketQueryBuilder::stripMarkdown($state ?? ''))
                            ->columnSpanFull(),

                        // ==========================================
                        // 4. SPESIFIKASI LAYANAN SPESIFIK (MODULAR SCHEMAS)
                        // ==========================================
                        BsreSpecification::make(),
                        ZoomSpecification::make(),
                        KegiatanSpecification::make(),
                        WindowsSpecification::make(),
                        OfficeSpecification::make(),
                        LainnyaSpecification::make(),
                        StandardSpecification::make(),

                        // ==========================================
                        // 5. BERKAS LAMPIRAN PENDUKUNG TIKET
                        // ==========================================
                        Placeholder::make('attachment')
                            ->label('Berkas Lampiran Pendukung Tiket')
                            ->visible(function ($record) {
                                if (!$record) return false;
                                return ($record->attachments && $record->attachments->isNotEmpty()) || !empty($record->attachment);
                            })
                            ->content(function ($record) {
                                if (!$record) {
                                    return new HtmlString('<span class="text-xs text-gray-500 italic">Tidak ada lampiran berkas yang disertakan.</span>');
                                }
                                $attachments = $record->attachments;
                                if ($attachments && $attachments->isNotEmpty()) {
                                    $html = '<div class="space-y-1.5 text-xs">';
                                    foreach ($attachments as $att) {
                                        $url = route('tickets.attachments.item', ['ticket' => $record, 'attachment' => $att]);
                                        $html .= '<div class="flex items-center gap-2"><a href="' . e($url) . '" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 rounded-lg text-xs font-semibold underline transition"><i class="ri-attachment-2 mr-1"></i> ' . e($att->file_name) . ' (' . e($att->formatted_size) . ')</a></div>';
                                    }
                                    $html .= '</div>';
                                    return new HtmlString($html);
                                }
                                if ($record->attachment) {
                                    $url = route('tickets.attachment', ['ticket' => $record]);
                                    return new HtmlString('<a href="' . e($url) . '" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg text-xs font-semibold underline transition"><i class="ri-attachment-2 mr-1"></i> Lihat / Unduh Lampiran Berkas</a>');
                                }
                                return new HtmlString('<span class="text-xs text-gray-500 italic">Tidak ada lampiran berkas yang disertakan.</span>');
                            })
                            ->columnSpanFull(),

                        // ==========================================
                        // 6. BOTTOM ACTIONS: RATA KIRI DI DALAM KARTU
                        // ==========================================
                        Actions::make([
                            Action::make('save')
                                ->label('Simpan Perubahan')
                                ->submit('save')
                                ->color('primary')
                                ->keyBindings(['mod+s']),
                            Action::make('cancel')
                                ->label('Batal')
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
}
