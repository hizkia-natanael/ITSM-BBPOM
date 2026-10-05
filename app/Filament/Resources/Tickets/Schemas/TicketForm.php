<?php

namespace App\Filament\Resources\Tickets\Schemas;

use App\Filament\Resources\Tickets\TicketResource;
use App\Services\Export\TicketQueryBuilder;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
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
                        // 4. SPESIFIKASI LAYANAN SPESIFIK (NON-DUPLICATE)
                        // ==========================================

                        // --- 4.A Layanan BSrE / TTE ---
                        Group::make([
                            Grid::make(['default' => 1, 'md' => 2])
                                ->schema([
                                    TextInput::make('bsre_subcategory')
                                        ->label('Sub Kategori BSrE')
                                        ->formatStateUsing(fn ($record) => $record?->bsreDetail?->sub_category_label ?? '-')
                                        ->disabled()
                                        ->dehydrated(false),

                                    TextInput::make('bsre_nip')
                                        ->label('NIP Pemohon')
                                        ->formatStateUsing(fn ($record) => $record?->bsreDetail?->nip ?? '-')
                                        ->disabled()
                                        ->dehydrated(false),

                                    TextInput::make('bsre_nik')
                                        ->label('NIK Pemohon')
                                        ->formatStateUsing(fn ($record) => $record?->bsreDetail?->nik ?? '-')
                                        ->disabled()
                                        ->dehydrated(false),

                                    TextInput::make('bsre_email')
                                        ->label('Email Dinas Pemohon')
                                        ->formatStateUsing(fn ($record) => $record?->bsreDetail?->email ?? '-')
                                        ->disabled()
                                        ->dehydrated(false),

                                    TextInput::make('bsre_telepon')
                                        ->label('Nomor Telepon / WhatsApp')
                                        ->formatStateUsing(fn ($record) => $record?->bsreDetail?->nomor_telepon ?? '-')
                                        ->disabled()
                                        ->dehydrated(false),
                                ]),

                            Textarea::make('bsre_deskripsi')
                                ->label('Deskripsi Kendala (Lainnya)')
                                ->visible(fn ($record) => !empty($record?->bsreDetail?->deskripsi))
                                ->formatStateUsing(fn ($record) => TicketQueryBuilder::stripMarkdown($record?->bsreDetail?->deskripsi ?? ''))
                                ->rows(3)
                                ->disabled()
                                ->dehydrated(false)
                                ->columnSpanFull(),

                            Placeholder::make('bsre_lampiran')
                                ->label('Berkas Lampiran BSrE')
                                ->visible(fn ($record) => !empty($record?->bsreDetail?->file_lampiran))
                                ->content(function ($record) {
                                    if (!$record || !$record->bsreDetail?->file_lampiran) {
                                        return new HtmlString('<span class="text-xs text-gray-500 italic">Tidak ada berkas lampiran.</span>');
                                    }
                                    $url = asset('storage/' . $record->bsreDetail->file_lampiran);
                                    return new HtmlString('<a href="' . e($url) . '" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-800 hover:bg-amber-100 rounded-lg text-xs font-semibold underline transition"><i class="ri-attachment-2 mr-1"></i>Lihat / Unduh Lampiran BSrE</a>');
                                })
                                ->columnSpanFull(),
                        ])
                        ->visible(fn ($record) => $record && $record->bsreDetail !== null)
                        ->columnSpanFull(),

                        // --- 4.B Layanan Zoom Meeting / Webinar ---
                        Group::make([
                            Grid::make(['default' => 1, 'md' => 2])
                                ->schema([
                                    TextInput::make('zoom_subcategory')
                                        ->label('Sub Kategori Zoom')
                                        ->formatStateUsing(fn ($record) => $record?->zoomDetail?->sub_category_label ?? '-')
                                        ->disabled()
                                        ->dehydrated(false),

                                    TextInput::make('zoom_nama_acara')
                                        ->label('Nama Acara / Kegiatan')
                                        ->visible(fn ($record) => !empty($record?->zoomDetail?->nama_acara))
                                        ->formatStateUsing(fn ($record) => $record?->zoomDetail?->nama_acara ?? '-')
                                        ->disabled()
                                        ->dehydrated(false),

                                    TextInput::make('zoom_jam')
                                        ->label('Jam / Waktu Pelaksanaan')
                                        ->visible(fn ($record) => !empty($record?->zoomDetail?->jam))
                                        ->formatStateUsing(fn ($record) => $record?->zoomDetail?->jam ?? '-')
                                        ->disabled()
                                        ->dehydrated(false),
                                ]),

                            Textarea::make('zoom_deskripsi')
                                ->label('Deskripsi Kebutuhan / Kendala')
                                ->visible(fn ($record) => !empty($record?->zoomDetail?->deskripsi))
                                ->formatStateUsing(fn ($record) => TicketQueryBuilder::stripMarkdown($record?->zoomDetail?->deskripsi ?? ''))
                                ->rows(3)
                                ->disabled()
                                ->dehydrated(false)
                                ->columnSpanFull(),

                            Placeholder::make('zoom_lampiran')
                                ->label('Berkas Lampiran Zoom')
                                ->visible(fn ($record) => !empty($record?->zoomDetail?->file_lampiran))
                                ->content(function ($record) {
                                    if (!$record || !$record->zoomDetail?->file_lampiran) {
                                        return new HtmlString('<span class="text-xs text-gray-500 italic">Tidak ada berkas lampiran.</span>');
                                    }
                                    $url = asset('storage/' . $record->zoomDetail->file_lampiran);
                                    return new HtmlString('<a href="' . e($url) . '" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-800 hover:bg-blue-100 rounded-lg text-xs font-semibold underline transition"><i class="ri-attachment-2 mr-1"></i>Lihat / Unduh Lampiran Zoom</a>');
                                })
                                ->columnSpanFull(),
                        ])
                        ->visible(fn ($record) => $record && $record->zoomDetail !== null)
                        ->columnSpanFull(),

                        // --- 4.C Layanan Presensi, Kuesioner & Evaluasi Kegiatan ---
                        Group::make([
                            Grid::make(['default' => 1, 'md' => 2])
                                ->schema([
                                    TextInput::make('kegiatan_subcategory')
                                        ->label('Sub Kategori Kegiatan')
                                        ->formatStateUsing(fn ($record) => $record?->kegiatanDetail?->sub_category_label ?? '-')
                                        ->disabled()
                                        ->dehydrated(false),

                                    TextInput::make('kegiatan_nama_acara')
                                        ->label('Nama Acara / Kegiatan')
                                        ->visible(fn ($record) => !empty($record?->kegiatanDetail?->nama_acara))
                                        ->formatStateUsing(fn ($record) => $record?->kegiatanDetail?->nama_acara ?? '-')
                                        ->disabled()
                                        ->dehydrated(false),

                                    TextInput::make('kegiatan_jam')
                                        ->label('Jam / Waktu Pelaksanaan')
                                        ->visible(fn ($record) => !empty($record?->kegiatanDetail?->jam))
                                        ->formatStateUsing(fn ($record) => $record?->kegiatanDetail?->jam ?? '-')
                                        ->disabled()
                                        ->dehydrated(false),

                                    TextInput::make('kegiatan_jenis_test')
                                        ->label('Jenis Evaluasi')
                                        ->visible(fn ($record) => !empty($record?->kegiatanDetail?->jenis_test))
                                        ->formatStateUsing(fn ($record) => $record?->kegiatanDetail?->jenis_test_label ?? '-')
                                        ->disabled()
                                        ->dehydrated(false),

                                    TextInput::make('kegiatan_link_qrcode')
                                        ->label('Tautan Sasaran QR Code')
                                        ->visible(fn ($record) => !empty($record?->kegiatanDetail?->link_qrcode))
                                        ->formatStateUsing(fn ($record) => $record?->kegiatanDetail?->link_qrcode ?? '-')
                                        ->disabled()
                                        ->dehydrated(false)
                                        ->columnSpanFull(),

                                    TextInput::make('kegiatan_judul_survey')
                                        ->label('Judul Survei')
                                        ->visible(fn ($record) => !empty($record?->kegiatanDetail?->judul_survey))
                                        ->formatStateUsing(fn ($record) => $record?->kegiatanDetail?->judul_survey ?? '-')
                                        ->disabled()
                                        ->dehydrated(false)
                                        ->columnSpanFull(),

                                    TextInput::make('kegiatan_judul_acara')
                                        ->label('Judul Acara Evaluasi')
                                        ->visible(fn ($record) => !empty($record?->kegiatanDetail?->judul_acara))
                                        ->formatStateUsing(fn ($record) => $record?->kegiatanDetail?->judul_acara ?? '-')
                                        ->disabled()
                                        ->dehydrated(false)
                                        ->columnSpanFull(),
                                ]),

                            Placeholder::make('kegiatan_pertanyaan')
                                ->label('Daftar Butir Pertanyaan / Soal')
                                ->visible(fn ($record) => !empty($record?->kegiatanDetail?->pertanyaan_data))
                                ->content(function ($record) {
                                    $data = $record?->kegiatanDetail?->pertanyaan_data;
                                    if (empty($data) || !is_array($data)) {
                                        return new HtmlString('<span class="text-xs text-gray-500 italic">Tidak ada butir pertanyaan input langsung (menggunakan dokumen terlampir).</span>');
                                    }
                                    $html = '<div class="space-y-1.5 text-xs">';
                                    foreach ($data as $i => $q) {
                                        $num = $i + 1;
                                        $text = e(is_array($q) ? ($q['text'] ?? $q['pertanyaan'] ?? '') : $q);
                                        $tipe = is_array($q) && (!empty($q['tipe']) || !empty($q['tipe_jawaban']))
                                            ? '<span class="inline-block px-2 py-0.5 ml-2 text-[10px] font-bold rounded bg-emerald-100 text-emerald-800">' . e($q['tipe'] ?? $q['tipe_jawaban']) . '</span>'
                                            : '';
                                        $html .= '<div class="p-2 bg-gray-50 rounded border border-gray-200"><strong>' . $num . '.</strong> ' . $text . $tipe . '</div>';
                                    }
                                    $html .= '</div>';
                                    return new HtmlString($html);
                                })
                                ->columnSpanFull(),

                            Textarea::make('kegiatan_deskripsi')
                                ->label('Deskripsi Kebutuhan')
                                ->visible(fn ($record) => !empty($record?->kegiatanDetail?->deskripsi))
                                ->formatStateUsing(fn ($record) => TicketQueryBuilder::stripMarkdown($record?->kegiatanDetail?->deskripsi ?? ''))
                                ->rows(3)
                                ->disabled()
                                ->dehydrated(false)
                                ->columnSpanFull(),

                            Placeholder::make('kegiatan_lampiran')
                                ->label('Berkas Dokumen Lampiran')
                                ->visible(fn ($record) => !empty($record?->kegiatanDetail?->file_lampiran))
                                ->content(function ($record) {
                                    if (!$record || !$record->kegiatanDetail?->file_lampiran) {
                                        return new HtmlString('<span class="text-xs text-gray-500 italic">Tidak ada berkas lampiran.</span>');
                                    }
                                    $url = asset('storage/' . $record->kegiatanDetail->file_lampiran);
                                    return new HtmlString('<a href="' . e($url) . '" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 rounded-lg text-xs font-semibold underline transition"><i class="ri-attachment-2 mr-1"></i>Lihat / Unduh Dokumen Kegiatan</a>');
                                })
                                ->columnSpanFull(),
                        ])
                        ->visible(fn ($record) => $record && $record->kegiatanDetail !== null)
                        ->columnSpanFull(),

                        // --- 4.D Layanan Install Ulang Windows ---
                        Group::make([
                            Grid::make(['default' => 1, 'md' => 2])
                                ->schema([
                                    TextInput::make('windows_subcategory')
                                        ->label('Sub Kategori Windows')
                                        ->formatStateUsing(fn ($record) => $record?->windowsDetail?->sub_category_label ?? '-')
                                        ->disabled()
                                        ->dehydrated(false),

                                    TextInput::make('windows_bidang')
                                        ->label('Bidang / Substansi')
                                        ->visible(fn ($record) => !empty($record?->windowsDetail?->bidang))
                                        ->formatStateUsing(fn ($record) => $record?->windowsDetail?->bidang ?? '-')
                                        ->disabled()
                                        ->dehydrated(false),

                                    TextInput::make('windows_merk')
                                        ->label('Merk Laptop / Komputer')
                                        ->visible(fn ($record) => !empty($record?->windowsDetail?->merk_perangkat))
                                        ->formatStateUsing(fn ($record) => $record?->windowsDetail?->merk_perangkat ?? '-')
                                        ->disabled()
                                        ->dehydrated(false),

                                    TextInput::make('windows_kode_bmn')
                                        ->label('Kode BMN Perangkat')
                                        ->visible(fn ($record) => !empty($record?->windowsDetail?->kode_bmn))
                                        ->formatStateUsing(fn ($record) => $record?->windowsDetail?->kode_bmn ?? '-')
                                        ->disabled()
                                        ->dehydrated(false),
                                ]),

                            Textarea::make('windows_deskripsi')
                                ->label('Deskripsi Alasan / Kendala')
                                ->visible(fn ($record) => !empty($record?->windowsDetail?->deskripsi))
                                ->formatStateUsing(fn ($record) => TicketQueryBuilder::stripMarkdown($record?->windowsDetail?->deskripsi ?? ''))
                                ->rows(3)
                                ->disabled()
                                ->dehydrated(false)
                                ->columnSpanFull(),

                            Placeholder::make('windows_lampiran')
                                ->label('Berkas Lampiran Perangkat')
                                ->visible(fn ($record) => !empty($record?->windowsDetail?->file_lampiran))
                                ->content(function ($record) {
                                    if (!$record || !$record->windowsDetail?->file_lampiran) {
                                        return new HtmlString('<span class="text-xs text-gray-500 italic">Tidak ada berkas lampiran.</span>');
                                    }
                                    $url = asset('storage/' . $record->windowsDetail->file_lampiran);
                                    return new HtmlString('<a href="' . e($url) . '" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 text-slate-800 hover:bg-slate-200 rounded-lg text-xs font-semibold underline transition"><i class="ri-attachment-2 mr-1"></i>Lihat / Unduh Lampiran Windows</a>');
                                })
                                ->columnSpanFull(),
                        ])
                        ->visible(fn ($record) => $record && $record->windowsDetail !== null)
                        ->columnSpanFull(),

                        // --- 4.E Layanan Lisensi Microsoft Office ---
                        Group::make([
                            Grid::make(['default' => 1, 'md' => 2])
                                ->schema([
                                    TextInput::make('office_subcategory')
                                        ->label('Sub Kategori Lisensi')
                                        ->formatStateUsing(fn ($record) => $record?->officeDetail?->sub_category_label ?? '-')
                                        ->disabled()
                                        ->dehydrated(false),

                                    TextInput::make('office_bidang')
                                        ->label('Bidang / Substansi')
                                        ->visible(fn ($record) => !empty($record?->officeDetail?->bidang))
                                        ->formatStateUsing(fn ($record) => $record?->officeDetail?->bidang ?? '-')
                                        ->disabled()
                                        ->dehydrated(false),

                                    TextInput::make('office_merk')
                                        ->label('Merk Laptop / Komputer')
                                        ->visible(fn ($record) => !empty($record?->officeDetail?->merk_perangkat))
                                        ->formatStateUsing(fn ($record) => $record?->officeDetail?->merk_perangkat ?? '-')
                                        ->disabled()
                                        ->dehydrated(false),

                                    TextInput::make('office_kode_bmn')
                                        ->label('Kode BMN Perangkat')
                                        ->visible(fn ($record) => !empty($record?->officeDetail?->kode_bmn))
                                        ->formatStateUsing(fn ($record) => $record?->officeDetail?->kode_bmn ?? '-')
                                        ->disabled()
                                        ->dehydrated(false),
                                ]),

                            Textarea::make('office_deskripsi')
                                ->label('Deskripsi Kendala Lisensi')
                                ->visible(fn ($record) => !empty($record?->officeDetail?->deskripsi))
                                ->formatStateUsing(fn ($record) => TicketQueryBuilder::stripMarkdown($record?->officeDetail?->deskripsi ?? ''))
                                ->rows(3)
                                ->disabled()
                                ->dehydrated(false)
                                ->columnSpanFull(),

                            Placeholder::make('office_lampiran')
                                ->label('Berkas Lampiran Lisensi')
                                ->visible(fn ($record) => !empty($record?->officeDetail?->file_lampiran))
                                ->content(function ($record) {
                                    if (!$record || !$record->officeDetail?->file_lampiran) {
                                        return new HtmlString('<span class="text-xs text-gray-500 italic">Tidak ada berkas lampiran.</span>');
                                    }
                                    $url = asset('storage/' . $record->officeDetail->file_lampiran);
                                    return new HtmlString('<a href="' . e($url) . '" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-800 hover:bg-amber-100 rounded-lg text-xs font-semibold underline transition"><i class="ri-attachment-2 mr-1"></i>Lihat / Unduh Lampiran Office</a>');
                                })
                                ->columnSpanFull(),
                        ])
                        ->visible(fn ($record) => $record && $record->officeDetail !== null)
                        ->columnSpanFull(),

                        // --- 4.F Layanan TIK Umum / Lainnya ---
                        Group::make([
                            Textarea::make('lainnya_deskripsi')
                                ->label('Deskripsi Permintaan / Kendala')
                                ->formatStateUsing(fn ($record) => TicketQueryBuilder::stripMarkdown($record?->lainnyaDetail?->deskripsi ?? ''))
                                ->rows(4)
                                ->disabled()
                                ->dehydrated(false)
                                ->columnSpanFull(),

                            Placeholder::make('lainnya_lampiran')
                                ->label('Berkas Lampiran')
                                ->visible(fn ($record) => !empty($record?->lainnyaDetail?->file_lampiran))
                                ->content(function ($record) {
                                    if (!$record || !$record->lainnyaDetail?->file_lampiran) {
                                        return new HtmlString('<span class="text-xs text-gray-500 italic">Tidak ada berkas lampiran.</span>');
                                    }
                                    $url = asset('storage/' . $record->lainnyaDetail->file_lampiran);
                                    return new HtmlString('<a href="' . e($url) . '" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 text-slate-800 hover:bg-slate-200 rounded-lg text-xs font-semibold underline transition"><i class="ri-attachment-2 mr-1"></i>Lihat / Unduh Lampiran</a>');
                                })
                                ->columnSpanFull(),
                        ])
                        ->visible(fn ($record) => $record && $record->lainnyaDetail !== null)
                        ->columnSpanFull(),

                        // --- 4.G Layanan Katalog Reguler (Tanpa Form Sub-Detail Khusus) ---
                        Group::make([
                            Grid::make(['default' => 1, 'md' => 2])
                                ->schema([
                                    TextInput::make('standard_subcategory')
                                        ->label('Klasifikasi Kendala / Sub-Layanan')
                                        ->formatStateUsing(function ($record) {
                                            if (!$record) return '-';
                                            $serviceKey = $record->service_key;
                                            $sub = $record->subcategory;
                                            $gateway = config("services_gateway.services.{$serviceKey}");
                                            if ($sub && isset($gateway['subcategories'][$sub])) {
                                                return $gateway['subcategories'][$sub];
                                            }
                                            return $sub ? str_replace('_', ' ', ucwords($sub, '_')) : 'Kendala Umum';
                                        })
                                        ->disabled()
                                        ->dehydrated(false)
                                        ->columnSpanFull(),
                                ]),

                            Textarea::make('description')
                                ->label('Deskripsi Kendala Lengkap')
                                ->disabled()
                                ->dehydrated(false)
                                ->formatStateUsing(fn ($state) => TicketQueryBuilder::stripMarkdown($state ?? ''))
                                ->rows(6)
                                ->columnSpanFull(),
                        ])
                        ->visible(fn ($record) => $record && (
                            $record->bsreDetail === null &&
                            $record->zoomDetail === null &&
                            $record->kegiatanDetail === null &&
                            $record->windowsDetail === null &&
                            $record->officeDetail === null &&
                            $record->lainnyaDetail === null
                        ))
                        ->columnSpanFull(),

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
