<?php

namespace App\Filament\Resources\Tickets\Schemas\Specifications;

use App\Services\Export\TicketQueryBuilder;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Illuminate\Support\HtmlString;

class BsreSpecification
{
    public static function make(): Group
    {
        return Group::make([
            Grid::make(['default' => 1, 'md' => 2])
                ->schema([
                    TextInput::make('bsre_subcategory')
                        ->label('Sub Kategori BSrE')
                        ->formatStateUsing(fn ($record) => $record?->bsreDetail?->sub_category_label ?? '-')
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('bsre_nip')
                        ->label('NIP Pemohon')
                        ->visible(fn ($record) => $record?->bsreDetail?->sub_category !== 'lainnya')
                        ->formatStateUsing(fn ($record) => $record?->bsreDetail?->nip ?? '-')
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('bsre_nik')
                        ->label('NIK Pemohon')
                        ->visible(fn ($record) => $record?->bsreDetail?->sub_category !== 'lainnya')
                        ->formatStateUsing(fn ($record) => $record?->bsreDetail?->nik ?? '-')
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('bsre_email')
                        ->label('Email Dinas Pemohon')
                        ->visible(fn ($record) => $record?->bsreDetail?->sub_category !== 'lainnya')
                        ->formatStateUsing(fn ($record) => $record?->bsreDetail?->email ?? '-')
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('bsre_telepon')
                        ->label('Nomor Telepon / WhatsApp')
                        ->visible(fn ($record) => $record?->bsreDetail?->sub_category !== 'lainnya')
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
        ])
        ->visible(fn ($record) => $record && $record->bsreDetail !== null)
        ->columnSpanFull();
    }
}
