<?php

namespace App\Filament\Resources\SatisfactionSurveys;

use App\Filament\Resources\SatisfactionSurveys\Pages\ListSatisfactionSurveys;
use App\Filament\Resources\SatisfactionSurveys\Tables\SatisfactionSurveysTable;
use App\Models\TicketSatisfactionSurvey;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SatisfactionSurveyResource extends Resource
{
    protected static ?string $model = TicketSatisfactionSurvey::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static ?string $navigationLabel = 'Survei Kepuasan';

    protected static ?string $modelLabel = 'Survei Kepuasan';

    protected static ?string $pluralModelLabel = 'Survei Kepuasan';

    protected static ?int $navigationSort = 4;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return SatisfactionSurveysTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSatisfactionSurveys::route('/'),
        ];
    }
}
