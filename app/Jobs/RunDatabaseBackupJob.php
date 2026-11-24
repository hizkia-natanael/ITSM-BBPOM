<?php

namespace App\Jobs;

use App\Models\DatabaseBackup;
use App\Services\DatabaseBackupService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RunDatabaseBackupJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = 900;

    public function __construct(public int $databaseBackupId) {}

    public function handle(DatabaseBackupService $backupService): void
    {
        $record = DatabaseBackup::find($this->databaseBackupId);
        if (! $record) {
            return;
        }

        $backupService->runBackup(
            triggerType: $record->trigger_type,
            userId: $record->created_by,
            existingRecord: $record,
        );
    }

    public function failed(\Throwable $exception): void
    {
        $record = DatabaseBackup::find($this->databaseBackupId);
        if ($record && $record->status !== 'success') {
            $record->update([
                'status' => 'failed',
                'gdrive_status' => $record->gdrive_status === 'uploaded' ? 'uploaded' : 'failed',
                'error_message' => $exception->getMessage(),
                'completed_at' => now(),
            ]);
        }
    }
}
