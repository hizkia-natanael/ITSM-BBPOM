<?php

namespace App\Filament\Resources\Tickets\Schemas\Specifications;

use App\Services\Export\TicketQueryBuilder;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Illuminate\Support\HtmlString;

class ZoomSpecification
{
    public static function make(): Group
    {
        return Group::make([
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
        ->columnSpanFull();
    }
}
