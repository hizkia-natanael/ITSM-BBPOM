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
        ])
        ->visible(fn ($record) => $record && $record->lainnyaDetail !== null)
        ->columnSpanFull();
    }
}
