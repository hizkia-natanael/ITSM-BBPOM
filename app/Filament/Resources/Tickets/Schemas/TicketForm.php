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
                            ->columnSpanFull(),

                        Placeholder::make('attachment')
                            ->label('Berkas Lampiran Pendukung')
                            ->content(function ($record) {
                                if (!$record || !$record->attachment) {
                                    return new HtmlString('<span class="text-xs text-gray-500 italic">Tidak ada lampiran berkas yang disertakan.</span>');
                                }
                                $url = asset('storage/' . $record->attachment);
                                return new HtmlString('<a href="' . e($url) . '" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg text-xs font-semibold underline transition">📎 Lihat / Unduh Lampiran Berkas</a>');
                            }),
                    ]),
            ]);
    }
}
