<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard Admin';

    protected static ?string $navigationLabel = 'Dashboard Admin';

    public function getColumns(): int|array
    {
        return 12;
    }
}
