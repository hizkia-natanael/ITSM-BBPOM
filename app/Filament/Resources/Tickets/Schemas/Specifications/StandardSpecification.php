<?php

namespace App\Filament\Resources\Tickets\Schemas\Specifications;

use App\Services\Export\TicketQueryBuilder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;

class StandardSpecification
{
    public static function make(): Group
    {
        return Group::make([
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
        ->columnSpanFull();
    }
}
