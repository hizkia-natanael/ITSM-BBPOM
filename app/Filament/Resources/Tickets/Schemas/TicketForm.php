<?php

namespace App\Filament\Resources\Tickets\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Status Penanganan Layanan')
                    ->description('Kelola progres penanganan tiket ITSM oleh tim teknisi IT BBPOM Semarang.')
                    ->schema([
                        Grid::make(2)
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
                                    ->selectablePlaceholder(false)
                                    ->helperText('Perbarui status tiket ini agar pelapor dapat melihat progres langsung di dashboard.'),

                                TextInput::make('handled_by_display')
                                    ->label('Petugas Penanggung Jawab (Teknisi IT)')
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
                            ]),
                    ]),

                Section::make('Rincian Laporan Pegawai (Read-only)')
                    ->description('Data asli yang dikirimkan oleh pemohon layanan.')
                    ->schema([
                        Grid::make(2)
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
                                    ->label('Nama Pegawai (Pelapor)')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('category.name')
                                    ->label('Kategori Kendala')
                                    ->disabled()
                                    ->dehydrated(false),
                            ]),

                        TextInput::make('subject')
                            ->label('Subjek / Judul Kendala')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Deskripsi Kendala Lengkap')
                            ->disabled()
                            ->dehydrated(false)
                            ->rows(4)
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record && !$record->hasSubDetail()),

                        Placeholder::make('attachment')
                            ->label('Berkas Lampiran Pendukung')
                            ->content(function ($record) {
                                if (!$record) {
                                    return new HtmlString('<span class="text-xs text-gray-500 italic">Tidak ada lampiran berkas yang disertakan.</span>');
                                }
                                $attachments = $record->attachments;
                                if ($attachments && $attachments->isNotEmpty()) {
                                    $html = '<div class="space-y-1.5 text-xs">';
                                    foreach ($attachments as $att) {
                                        $url = route('tickets.attachments.item', ['ticket' => $record, 'attachment' => $att]);
                                        $html .= '<div class="flex items-center gap-2"><a href="' . e($url) . '" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 rounded-lg text-xs font-semibold underline transition"><i class="ri-attachment-2"></i> ' . e($att->file_name) . ' (' . e($att->formatted_size) . ')</a></div>';
                                    }
                                    $html .= '</div>';
                                    return new HtmlString($html);
                                }
                                if ($record->attachment) {
                                    $url = route('tickets.attachment', $record);
                                    return new HtmlString('<a href="' . e($url) . '" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg text-xs font-semibold underline transition"><i class="ri-attachment-2"></i> Lihat / Unduh Lampiran Berkas</a>');
                                }
                                return new HtmlString('<span class="text-xs text-gray-500 italic">Tidak ada lampiran berkas yang disertakan.</span>');
                            }),
                    ]),

                Section::make('Rincian Layanan Khusus Sertifikat BSrE / TTE')
                    ->description('Data formulir pengajuan khusus Sertifikat BSrE.')
                    ->visible(fn ($record) => $record && $record->bsreDetail !== null)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('bsre_subcategory')
                                    ->label('Sub Kategori BSrE')
                                    ->formatStateUsing(fn ($record) => $record?->bsreDetail?->sub_category_label ?? '-')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('bsre_tanggal')
                                    ->label('Tanggal Pengajuan')
                                    ->formatStateUsing(fn ($record) => $record?->bsreDetail?->tanggal_pengajuan ? $record->bsreDetail->tanggal_pengajuan->translatedFormat('d F Y') : '-')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('bsre_nama')
                                    ->label('Nama Pemohon')
                                    ->formatStateUsing(fn ($record) => $record?->bsreDetail?->nama ?? '-')
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
                            ->formatStateUsing(fn ($record) => $record?->bsreDetail?->deskripsi ?? '')
                            ->rows(3)
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ]),

                Section::make('Rincian Layanan Zoom Meeting / Webinar')
                    ->description('Data formulir pengajuan peminjaman link Zoom atau kebutuhan rapat virtual.')
                    ->visible(fn ($record) => $record && $record->zoomDetail !== null)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('zoom_subcategory')
                                    ->label('Sub Kategori Zoom')
                                    ->formatStateUsing(fn ($record) => $record?->zoomDetail?->sub_category_label ?? '-')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('zoom_tanggal')
                                    ->label('Tanggal Pengajuan')
                                    ->formatStateUsing(fn ($record) => $record?->zoomDetail?->tanggal_pengajuan ? $record->zoomDetail->tanggal_pengajuan->translatedFormat('d F Y') : '-')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('zoom_nama')
                                    ->label('Nama Pemohon')
                                    ->formatStateUsing(fn ($record) => $record?->zoomDetail?->nama ?? '-')
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
                            ->formatStateUsing(fn ($record) => $record?->zoomDetail?->deskripsi ?? '')
                            ->rows(3)
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ]),

                Section::make('Rincian Layanan Formulir & Fasilitas Kegiatan')
                    ->description('Data presensi daftar hadir, QR Code, kuesioner survei, atau evaluasi pretest/posttest.')
                    ->visible(fn ($record) => $record && $record->kegiatanDetail !== null)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('kegiatan_subcategory')
                                    ->label('Sub Kategori Kegiatan')
                                    ->formatStateUsing(fn ($record) => $record?->kegiatanDetail?->sub_category_label ?? '-')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('kegiatan_tanggal')
                                    ->label('Tanggal Pengajuan')
                                    ->formatStateUsing(fn ($record) => $record?->kegiatanDetail?->tanggal_pengajuan ? $record->kegiatanDetail->tanggal_pengajuan->translatedFormat('d F Y') : '-')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('kegiatan_nama')
                                    ->label('Nama Pemohon')
                                    ->formatStateUsing(fn ($record) => $record?->kegiatanDetail?->nama ?? '-')
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
                                    ->dehydrated(false),

                                TextInput::make('kegiatan_jenis_test')
                                    ->label('Jenis Evaluasi')
                                    ->visible(fn ($record) => !empty($record?->kegiatanDetail?->jenis_test))
                                    ->formatStateUsing(fn ($record) => $record?->kegiatanDetail?->jenis_test_label ?? '-')
                                    ->disabled()
                                    ->dehydrated(false),
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
                            ->formatStateUsing(fn ($record) => $record?->kegiatanDetail?->deskripsi ?? '')
                            ->rows(3)
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ]),

                Section::make('Rincian Layanan Install Ulang Windows')
                    ->description('Data perangkat dinas dan alasan install ulang OS.')
                    ->visible(fn ($record) => $record && $record->windowsDetail !== null)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('windows_subcategory')
                                    ->label('Sub Kategori Windows')
                                    ->formatStateUsing(fn ($record) => $record?->windowsDetail?->sub_category_label ?? '-')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('windows_tanggal')
                                    ->label('Tanggal Pengajuan')
                                    ->formatStateUsing(fn ($record) => $record?->windowsDetail?->tanggal_pengajuan ? $record->windowsDetail->tanggal_pengajuan->translatedFormat('d F Y') : '-')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('windows_nama')
                                    ->label('Nama Pemohon')
                                    ->formatStateUsing(fn ($record) => $record?->windowsDetail?->nama ?? '-')
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
                            ->formatStateUsing(fn ($record) => $record?->windowsDetail?->deskripsi ?? '')
                            ->rows(3)
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ]),

                Section::make('Rincian Layanan Lisensi Microsoft Office')
                    ->description('Data perangkat dinas dan kebutuhan lisensi Microsoft Office.')
                    ->visible(fn ($record) => $record && $record->officeDetail !== null)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('office_subcategory')
                                    ->label('Sub Kategori Lisensi')
                                    ->formatStateUsing(fn ($record) => $record?->officeDetail?->sub_category_label ?? '-')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('office_tanggal')
                                    ->label('Tanggal Pengajuan')
                                    ->formatStateUsing(fn ($record) => $record?->officeDetail?->tanggal_pengajuan ? $record->officeDetail->tanggal_pengajuan->translatedFormat('d F Y') : '-')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('office_nama')
                                    ->label('Nama Pemohon')
                                    ->formatStateUsing(fn ($record) => $record?->officeDetail?->nama ?? '-')
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
                            ->formatStateUsing(fn ($record) => $record?->officeDetail?->deskripsi ?? '')
                            ->rows(3)
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ]),

                Section::make('Rincian Layanan TIK Umum / Lainnya')
                    ->description('Data permohonan dukungan umum TIK BBPOM.')
                    ->visible(fn ($record) => $record && $record->lainnyaDetail !== null)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('lainnya_tanggal')
                                    ->label('Tanggal Pengajuan')
                                    ->formatStateUsing(fn ($record) => $record?->lainnyaDetail?->tanggal_pengajuan ? $record->lainnyaDetail->tanggal_pengajuan->translatedFormat('d F Y') : '-')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('lainnya_nama')
                                    ->label('Nama Pemohon')
                                    ->formatStateUsing(fn ($record) => $record?->lainnyaDetail?->nama ?? '-')
                                    ->disabled()
                                    ->dehydrated(false),
                            ]),

                        Textarea::make('lainnya_deskripsi')
                            ->label('Deskripsi Permintaan / Kendala')
                            ->formatStateUsing(fn ($record) => $record?->lainnyaDetail?->deskripsi ?? '')
                            ->rows(4)
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
