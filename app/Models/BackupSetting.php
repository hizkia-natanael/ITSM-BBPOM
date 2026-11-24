<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class BackupSetting extends Model
{
    protected $guarded = ['id'];

    protected $hidden = [
        'gdrive_client_secret',
        'gdrive_refresh_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'auto_backup_enabled' => 'boolean',
            'retention_days' => 'integer',
            'include_documents' => 'boolean',
            'gdrive_client_secret' => 'encrypted',
            'gdrive_refresh_token' => 'encrypted',
        ];
    }

    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function current(): ?self
    {
        try {
            if (! Schema::hasTable('backup_settings')) {
                return null;
            }

            return self::query()->first();
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Gabungkan pengaturan database dengan fallback dari .env/config.
     *
     * @return array{
     *     auto_backup_enabled: bool,
     *     schedule_time: string,
     *     retention_days: int,
     *     filename_prefix: string,
     *     include_documents: bool,
     *     gdrive_client_id: string,
     *     gdrive_client_secret: string,
     *     gdrive_refresh_token: string,
     *     gdrive_folder_id: string,
     *     gdrive_folder_name: string,
     *     gdrive_account_email: string,
     * }
     */
    public static function effectiveConfig(): array
    {
        $record = self::current();

        $pick = static function (?string $dbValue, ?string $configValue, string $default = ''): string {
            if (is_string($dbValue) && trim($dbValue) !== '') {
                return trim($dbValue);
            }

            if (is_string($configValue) && trim($configValue) !== '') {
                return trim($configValue);
            }

            return $default;
        };

        return [
            'auto_backup_enabled' => $record?->auto_backup_enabled ?? (bool) config('services.backup.enabled', true),
            'schedule_time' => $pick($record?->schedule_time, (string) config('services.backup.schedule_time', '02:00'), '02:00'),
            'retention_days' => max(1, (int) ($record?->retention_days ?? config('services.backup.retention_days', 14))),
            'filename_prefix' => $pick($record?->filename_prefix, (string) config('services.backup.filename_prefix', 'backup-elite'), 'backup-elite'),
            'include_documents' => $record?->include_documents ?? (bool) config('services.backup.include_documents', true),
            'gdrive_client_id' => $pick($record?->gdrive_client_id, config('services.google_drive.client_id')),
            'gdrive_client_secret' => $pick($record?->gdrive_client_secret, config('services.google_drive.client_secret')),
            'gdrive_refresh_token' => $pick($record?->gdrive_refresh_token, config('services.google_drive.refresh_token')),
            'gdrive_folder_id' => $pick($record?->gdrive_folder_id, config('services.google_drive.folder_id')),
            'gdrive_folder_name' => $pick($record?->gdrive_folder_name, (string) config('services.google_drive.folder_name', 'Backup-ELITE'), 'Backup-ELITE'),
            'gdrive_account_email' => (string) ($record?->gdrive_account_email ?? ''),
        ];
    }

    public static function scheduledTimeSafe(): string
    {
        $time = self::effectiveConfig()['schedule_time'];

        if (preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $time) === 1) {
            return $time;
        }

        return '02:00';
    }

    public static function isAutoBackupEnabledSafe(): bool
    {
        return self::effectiveConfig()['auto_backup_enabled'];
    }
}
