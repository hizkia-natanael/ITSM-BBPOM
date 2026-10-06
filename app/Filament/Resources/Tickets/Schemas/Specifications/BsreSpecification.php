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
                        ->formatStateUsing(fn ($record) => $record?->bsreDetail?->nip ?? '-')
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('bsre_nik')
                        ->label('NIK Pemohon')
                        ->formatStateUsing(fn ($record) => $record?->bsreDetail?->nik ?? '-')
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('bsre_email')
                        ->label('Email Dinas Pemohon')
                        ->formatStateUsing(fn ($record) => $record?->bsreDetail?->email ?? '-')
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('bsre_telepon')
                        ->label('Nomor Telepon / WhatsApp')
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

            Placeholder::make('bsre_lampiran')
                ->label('Berkas Lampiran BSrE')
                ->visible(fn ($record) => !empty($record?->bsreDetail?->file_lampiran))
                ->content(function ($record) {
                    if (!$record || !$record->bsreDetail?->file_lampiran) {
                        return new HtmlString('<span class="text-xs text-gray-500 italic">Tidak ada berkas lampiran.</span>');
                    }
                    $url = asset('storage/' . $record->bsreDetail->file_lampiran);
                    return new HtmlString('<a href="' . e($url) . '" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-800 hover:bg-amber-100 rounded-lg text-xs font-semibold underline transition"><i class="ri-attachment-2 mr-1"></i>Lihat / Unduh Lampiran BSrE</a>');
                })
                ->columnSpanFull(),
        ])
        ->visible(fn ($record) => $record && $record->bsreDetail !== null)
        ->columnSpanFull();
    }
}
