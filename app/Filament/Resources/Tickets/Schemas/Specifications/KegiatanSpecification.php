<?php

namespace App\Filament\Resources\Tickets\Schemas\Specifications;

use App\Services\Export\TicketQueryBuilder;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Illuminate\Support\HtmlString;

class KegiatanSpecification
{
    public static function make(): Group
    {
        return Group::make([
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
        ->columnSpanFull();
    }
}
