<?php

namespace App\Filament\Pages;

use App\Actions\Backup\RunManualBackupAction;
use App\Models\DatabaseBackup;
use App\Services\Backup\BackupPageDataService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class BackupDatabase extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCloudArrowUp;

    protected static ?string $navigationLabel = 'Backup Database';

    protected static string|UnitEnum|null $navigationGroup = 'Pemeliharaan Sistem';

    protected static ?string $title = 'Backup Database & Google Drive';

    protected static ?string $slug = 'backup';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.backup-database';

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<int|string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            url('/admin/backup') => 'Pemeliharaan Sistem',
            'Backup Database',
        ];
    }

    protected function getHeaderActions(): array
    {
        $hasRunning = DatabaseBackup::whereIn('status', ['pending', 'running'])
            ->where('created_at', '>=', now()->subMinutes(15))
            ->exists();

        return [
            Action::make('envBackup')
                ->label('Backup .env')
                ->icon('heroicon-o-key')
                ->color('gray')
                ->extraAttributes([
                    'type' => 'button',
                    'x-on:click' => '$dispatch(\'open-env-modal\')',
                ]),
            Action::make('refreshPage')
                ->label('Muat Ulang')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->extraAttributes(['class' => $hasRunning ? 'bk-running-refresh' : ''])
                ->action(fn () => null),
            Action::make('runBackup')
                ->label($hasRunning ? 'Backup Berjalan...' : 'Backup Sekarang')
                ->icon($hasRunning ? 'heroicon-o-arrow-path' : 'heroicon-o-play')
                ->color('primary')
                ->disabled($hasRunning)
                ->extraAttributes(['class' => $hasRunning ? 'bk-running-primary' : ''])
                ->action('runManualBackup'),
        ];
    }

    public function runManualBackup(RunManualBackupAction $action): void
    {
        $user = auth()->user();
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Akses terbatas untuk Administrator.');
        }

        $result = $action->execute($user);
        $notification = Notification::make()->title($result['ok'] ? 'Backup Dijadwalkan' : 'Backup Masih Berjalan')->body($result['message']);
        $result['ok'] ? $notification->success()->send() : $notification->warning()->send();
    }

    protected function getViewData(): array
    {
        return static::getPageData();
    }

    /**
     * @return array<string, mixed>
     */
    public static function getPageData(): array
    {
        return app(BackupPageDataService::class)->build();
    }
}
