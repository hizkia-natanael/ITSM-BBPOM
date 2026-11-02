<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Kategori Layanan')
                ->icon('heroicon-o-plus')
                ->visible(fn () => auth()->user()?->isAdmin() ?? false)
                ->extraAttributes([
                    'class' => 'bbpom-header-create-btn',
                ]),
        ];
    }
}
