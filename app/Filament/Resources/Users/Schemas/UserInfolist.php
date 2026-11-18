<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('email_verified_at')
                    ->dateTime(timezone: 'Asia/Jakarta')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime(timezone: 'Asia/Jakarta')
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime(timezone: 'Asia/Jakarta')
                    ->placeholder('-'),
            ]);
    }
}
