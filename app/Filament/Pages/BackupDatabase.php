<?php

namespace App\Filament\Pages;

use App\Models\BackupSetting;
use App\Models\DatabaseBackup;
use App\Services\GoogleDriveService;
use BackedEnum;
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

    protected function getViewData(): array
    {
        return static::getPageData();
    }

    public static function getPageData(): array
    {
        // Tandai antrean backup yang macet lebih dari 15 menit sebagai gagal agar tidak menggantung.
        DatabaseBackup::whereIn('status', ['pending', 'running'])
            ->where('created_at', '<', now()->subMinutes(15))
            ->update([
                'status' => 'failed',
                'gdrive_status' => 'failed',
                'error_message' => 'Proses backup terhenti / timeout (antrean worker tidak aktif atau gagal mengeksekusi).',
                'completed_at' => now(),
            ]);

        $driveService = app(GoogleDriveService::class);
        $cfg = BackupSetting::effectiveConfig();

        $backups = DatabaseBackup::with('createdByUser:id,name')
            ->latest('id')
            ->take(30)
            ->get();

        $activeLocalBackups = DatabaseBackup::whereNull('local_deleted_at')
            ->where('status', 'success')
            ->get(['local_path', 'size_bytes'])
            ->filter(fn (DatabaseBackup $b) => ! empty($b->local_path) && is_file($b->local_path));

        $lastSuccess = DatabaseBackup::where('status', 'success')
            ->latest('completed_at')
            ->first(['completed_at', 'gdrive_status']);

        $hasRunningJob = DatabaseBackup::whereIn('status', ['pending', 'running'])
            ->where('created_at', '>=', now()->subMinutes(15))
            ->exists();

        return [
            'settings' => [
                'auto_backup_enabled' => $cfg['auto_backup_enabled'],
                'schedule_time' => $cfg['schedule_time'],
                'retention_days' => $cfg['retention_days'],
                'filename_prefix' => $cfg['filename_prefix'],
                'include_documents' => $cfg['include_documents'],
                'gdrive_client_id' => $cfg['gdrive_client_id'],
                'has_client_secret' => $cfg['gdrive_client_secret'] !== '',
                'has_refresh_token' => $cfg['gdrive_refresh_token'] !== '',
                'gdrive_connected' => $driveService->isConfigured(),
                'has_oauth_client' => $driveService->hasOAuthClient(),
                'gdrive_folder_id' => $cfg['gdrive_folder_id'],
                'gdrive_folder_name' => $cfg['gdrive_folder_name'],
                'gdrive_account_email' => $cfg['gdrive_account_email'],
                'oauth_redirect_uri' => route('admin.backup.google.callback'),
                'can_download_env' => true,
            ],
            'backups' => $backups,
            'summary' => [
                'total_backups' => DatabaseBackup::count(),
                'local_count' => $activeLocalBackups->count(),
                'local_size_bytes' => (int) $activeLocalBackups->sum('size_bytes'),
                'uploaded_count' => DatabaseBackup::where('gdrive_status', 'uploaded')->count(),
                'last_success_at' => $lastSuccess?->completed_at?->copy()->timezone('Asia/Jakarta')->format('d/m/Y H:i'),
                'has_running_job' => $hasRunningJob,
            ],
        ];
    }
}
