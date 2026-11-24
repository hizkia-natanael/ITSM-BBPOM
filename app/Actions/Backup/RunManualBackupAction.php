<?php

namespace App\Actions\Backup;

use App\Jobs\RunDatabaseBackupJob;
use App\Models\BackupSetting;
use App\Models\DatabaseBackup;
use App\Models\User;
use App\Services\DatabaseBackupService;

class RunManualBackupAction
{
    public function __construct(
        private readonly DatabaseBackupService $backupService,
    ) {}

    /**
     * @return array{ok: bool, message: string, backup?: DatabaseBackup}
     */
    public function execute(User $user): array
    {
        $alreadyRunning = DatabaseBackup::whereIn('status', ['pending', 'running'])
            ->where('created_at', '>=', now()->subMinutes(15))
            ->exists();

        if ($alreadyRunning) {
            return [
                'ok' => false,
                'message' => 'Proses backup masih berjalan. Tunggu beberapa saat lalu muat ulang.',
            ];
        }

        $cfg = BackupSetting::effectiveConfig();
        $backup = DatabaseBackup::create([
            'filename' => $this->backupService->buildFilename('manual', $cfg['filename_prefix']),
            'trigger_type' => 'manual',
            'status' => 'pending',
            'gdrive_status' => 'pending',
            'created_by' => $user->id,
        ]);

        RunDatabaseBackupJob::dispatch($backup->id);

        return [
            'ok' => true,
            'message' => "Backup manual ({$backup->filename}) telah dijadwalkan ke antrean.",
            'backup' => $backup,
        ];
    }
}
