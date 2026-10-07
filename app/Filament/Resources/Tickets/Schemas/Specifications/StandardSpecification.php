<?php

namespace App\Filament\Resources\Tickets\Schemas\Specifications;

use App\Services\Export\TicketQueryBuilder;
use App\Services\Tickets\TicketDynamicPayloadCompiler;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Illuminate\Support\HtmlString;

class StandardSpecification
{
    public static function make(): Group
    {
        return Group::make([
            TextInput::make('standard_subcategory')
                ->label('Klasifikasi Kendala / Sub-Layanan')
                ->visible(function ($record) {
                    if (!$record) return true;
                    if ($record->hasSubDetail()) return false;
                    if (empty($record->description)) return true;
                    $extracted = TicketDynamicPayloadCompiler::extractStructuredPayload($record->description);
                    return ! $extracted['is_structured'];
                })
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

            Placeholder::make('structured_specification_preview')
                ->hiddenLabel()
                ->visible(function ($record) {
                    if (!$record) return false;
                    if ($record->hasSubDetail()) return true;
                    if (empty($record->description)) return false;
                    $extracted = TicketDynamicPayloadCompiler::extractStructuredPayload($record->description);
                    return $extracted['is_structured'];
                })
                ->content(fn ($record) => view('filament.components.ticket-specification-card', ['record' => $record]))
                ->columnSpanFull(),

            Textarea::make('description')
                ->label('Deskripsi Kendala Lengkap')
                ->visible(function ($record) {
                    if (!$record) return true;
                    if ($record->hasSubDetail()) return false;
                    if (empty($record->description)) return true;
                    $extracted = TicketDynamicPayloadCompiler::extractStructuredPayload($record->description);
                    return ! $extracted['is_structured'];
                })
                ->disabled()
                ->dehydrated(false)
                ->formatStateUsing(fn ($state) => TicketQueryBuilder::stripMarkdown($state ?? ''))
                ->rows(5)
                ->columnSpanFull(),
        ])
        ->visible(fn ($record) => $record !== null)
        ->columnSpanFull();
    }
}
