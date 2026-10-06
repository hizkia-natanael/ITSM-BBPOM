<?php

namespace App\Filament\Resources\Tickets\Schemas\Specifications;

use App\Services\Export\TicketQueryBuilder;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Illuminate\Support\HtmlString;

class WindowsSpecification
{
    public static function make(): Group
    {
        return Group::make([
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
        ->columnSpanFull();
    }
}
