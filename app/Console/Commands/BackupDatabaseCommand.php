<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;

class BackupDatabaseCommand extends Command
{
    protected $signature = 'elite:backup-database {--trigger=scheduled : Sumber pemicu backup (scheduled atau manual)}';

    protected $description = 'Backup database ke ZIP, upload ke Google Drive, dan hapus backup lokal > 14 hari.';

    public function handle(DatabaseBackupService $backupService): int
    {
        $trigger = (string) $this->option('trigger');
        if (! in_array($trigger, ['scheduled', 'manual'], true)) {
            $trigger = 'scheduled';
        }

        $this->info("Memulai backup database ELITE ({$trigger})...");

        try {
            $backup = $backupService->runBackup($trigger);

            $sizeKb = round($backup->size_bytes / 1024, 1);
            $this->info("Backup selesai: {$backup->filename} ({$sizeKb} KB)");
            $this->line("Status Google Drive: {$backup->gdrive_status}");

            if ($backup->gdrive_file_id) {
                $this->line("Google Drive File ID: {$backup->gdrive_file_id}");
            }

            if ($backup->error_message) {
                $this->warn($backup->error_message);
            }

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Backup gagal: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
