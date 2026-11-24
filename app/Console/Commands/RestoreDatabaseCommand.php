<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RestoreDatabaseCommand extends Command
{
    protected $signature = 'elite:restore-database
        {file? : Nama file ZIP di folder backups atau path lengkap file ZIP}
        {--no-documents : Jangan restore folder dokumen, hanya restore database}
        {--force : Jalankan restore tanpa konfirmasi interaktif}';

    protected $description = 'Restore database (dan dokumen opsional) dari arsip ZIP backup ELITE.';

    public function handle(DatabaseBackupService $backupService): int
    {
        $inputFile = $this->argument('file');

        if (empty($inputFile)) {
            $available = $backupService->listLocalBackupFiles();
            if ($available === []) {
                $this->error('Tidak ada file backup ZIP di folder '.$backupService->backupDirectory());

                return self::FAILURE;
            }

            if ($this->option('no-interaction')) {
                $this->error('Sebutkan argumen nama/path file ZIP saat menjalankan dengan --no-interaction.');

                return self::FAILURE;
            }

            $inputFile = $this->choice('Pilih file backup ZIP yang ingin di-restore', $available, 0);
        }

        try {
            $zipPath = $backupService->resolveZipPath((string) $inputFile);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $skipDocuments = (bool) $this->option('no-documents');
        $dbName = (string) config('database.connections.'.config('database.default').'.database');

        $this->warn("PERINGATAN: Restore akan menimpa database '{$dbName}'".($skipDocuments ? '' : ' beserta file dokumen di storage/app').' menggunakan:');
        $this->line(" • {$zipPath}");

        if (! $this->option('force') && ! $this->confirm('Yakin ingin melanjutkan proses restore?')) {
            $this->warn('Restore dibatalkan.');

            return self::SUCCESS;
        }

        $manageMaintenance = ! app()->runningUnitTests() && ! app()->isDownForMaintenance();
        if ($manageMaintenance) {
            Artisan::call('down');
        }

        try {
            $this->info('Sedang memulihkan database'.($skipDocuments ? '...' : ' dan dokumen...'));
            $result = $backupService->restoreFromZip($zipPath, $skipDocuments);

            if (! app()->runningUnitTests()) {
                Artisan::call('optimize:clear');
            }

            $this->info("Restore selesai dari '{$result['sql_file']}'.");
            if (! $skipDocuments) {
                $this->line("Dokumen dipulihkan: {$result['restored_documents']} file.");
            }

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Restore gagal: '.$e->getMessage());

            return self::FAILURE;
        } finally {
            if ($manageMaintenance) {
                Artisan::call('up');
            }
        }
    }
}
