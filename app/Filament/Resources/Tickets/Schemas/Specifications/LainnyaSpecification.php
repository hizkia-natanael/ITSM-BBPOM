<?php

namespace App\Filament\Resources\Tickets\Schemas\Specifications;

use App\Services\Export\TicketQueryBuilder;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Group;
use Illuminate\Support\HtmlString;

class LainnyaSpecification
{
    public static function make(): Group
    {
        return Group::make([
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
        ->columnSpanFull();
    }
}
