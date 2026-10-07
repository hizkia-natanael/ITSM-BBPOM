<?php

namespace App\Filament\Resources\Tickets\Schemas\Specifications;

use App\Services\Export\TicketQueryBuilder;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Illuminate\Support\HtmlString;

class OfficeSpecification
{
    public static function make(): Group
    {
        return Group::make([
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
        ])
        ->visible(fn ($record) => $record && $record->officeDetail !== null)
        ->columnSpanFull();
    }
}
