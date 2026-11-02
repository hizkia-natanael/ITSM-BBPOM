<?php

namespace App\Filament\Resources\SatisfactionSurveys\Pages;

use App\Filament\Resources\SatisfactionSurveys\SatisfactionSurveyResource;
use Filament\Resources\Pages\ListRecords;

class ListSatisfactionSurveys extends ListRecords
{
    protected static string $resource = SatisfactionSurveyResource::class;

    public function getTitle(): string
    {
        return 'Survei Kepuasan & Ulasan Layanan';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
