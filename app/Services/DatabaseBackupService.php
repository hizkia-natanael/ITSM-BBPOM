<?php

namespace App\Services;

use App\Models\BackupSetting;
use App\Models\DatabaseBackup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Process;
use ZipArchive;

class DatabaseBackupService
{
    public function __construct(
        private readonly GoogleDriveService $googleDriveService,
    ) {}

    public function backupDirectory(): string
    {
        return (string) (config('services.backup.local_dir') ?: storage_path('app/private/backups'));
    }

    public function buildFilename(string $triggerType, string $prefix = 'backup-elite'): string
    {
        $cleanPrefix = preg_replace('/[^a-zA-Z0-9_-]/', '-', $prefix) ?: 'backup-elite';
        $wibNow = now('Asia/Jakarta');
        $date = $wibNow->format('Y-m-d');

        if ($triggerType === 'scheduled') {
            return "{$cleanPrefix}-{$date}.zip";
        }

        $time = $wibNow->format('His');

        return "{$cleanPrefix}-{$date}_{$time}.zip";
    }

    public function runBackup(
        string $triggerType = 'scheduled',
        ?int $userId = null,
        ?DatabaseBackup $existingRecord = null,
    ): DatabaseBackup {
        $cfg = BackupSetting::effectiveConfig();
        $backupDir = $this->backupDirectory();
        File::ensureDirectoryExists($backupDir);

        $zipFilename = $existingRecord?->filename ?: $this->buildFilename($triggerType, $cfg['filename_prefix']);
        $zipPath = $backupDir.DIRECTORY_SEPARATOR.$zipFilename;
        $sqlFilename = pathinfo($zipFilename, PATHINFO_FILENAME).'.sql';
        $sqlPath = $backupDir.DIRECTORY_SEPARATOR.$sqlFilename;

        $record = $existingRecord ?? DatabaseBackup::create([
            'filename' => $zipFilename,
            'trigger_type' => $triggerType,
            'status' => 'running',
            'gdrive_status' => 'pending',
            'created_by' => $userId,
            'started_at' => now(),
        ]);

        if ($existingRecord) {
            $record->update([
                'status' => 'running',
                'started_at' => $record->started_at ?? now(),
                'error_message' => null,
            ]);
        }

        try {
            $this->dumpDatabase($sqlPath);
            $sizeBytes = $this->createZipArchive($sqlPath, $zipPath, $cfg['include_documents']);

            $record->update([
                'local_path' => $zipPath,
                'size_bytes' => $sizeBytes,
            ]);

            if ($this->googleDriveService->isConfigured()) {
                $driveFile = $this->googleDriveService->uploadZipFile($zipPath, $zipFilename);

                $record->update([
                    'status' => 'success',
                    'gdrive_status' => 'uploaded',
                    'gdrive_file_id' => $driveFile['id'],
                    'gdrive_web_url' => $driveFile['webViewLink'],
                    'completed_at' => now(),
                ]);
            } else {
                $record->update([
                    'status' => 'success',
                    'gdrive_status' => 'skipped',
                    'error_message' => 'Backup lokal berhasil, namun Google Drive belum dihubungkan.',
                    'completed_at' => now(),
                ]);
            }

            $this->cleanupExpiredLocalBackups($cfg['retention_days']);

            return $record->fresh();
        } catch (\Throwable $e) {
            $record->update([
                'status' => 'failed',
                'gdrive_status' => $record->gdrive_status === 'uploaded' ? 'uploaded' : 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            throw $e;
        } finally {
            if (is_file($sqlPath)) {
                @unlink($sqlPath);
            }
        }
    }

    public function dumpDatabase(string $sqlPath): void
    {
        $connectionName = (string) config('database.default');
        $connection = config("database.connections.{$connectionName}");

        if (! is_array($connection)) {
            throw new RuntimeException("Konfigurasi koneksi database '{$connectionName}' tidak ditemukan.");
        }

        $driver = (string) ($connection['driver'] ?? '');

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $this->dumpMysqlDatabase($connection, $sqlPath);

            return;
        }

        if ($driver === 'sqlite') {
            $this->dumpSqliteDatabase($connection, $sqlPath);

            return;
        }

        throw new RuntimeException("Driver database '{$driver}' belum didukung untuk backup otomatis.");
    }

    /**
     * @return array<string, string>
     */
    public function documentDirectories(): array
    {
        return [
            'private' => storage_path('app/private'),
            'public' => storage_path('app/public'),
        ];
    }

    /**
     * @param  array<string, string>|null  $documentDirs
     */
    public function createZipArchive(
        string $sqlPath,
        string $zipPath,
        bool $includeDocuments = false,
        ?array $documentDirs = null,
    ): int {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('Ekstensi PHP zip (ZipArchive) tidak tersedia.');
        }

        if (! is_file($sqlPath) || filesize($sqlPath) === 0) {
            throw new RuntimeException('File SQL hasil dump kosong atau tidak terbentuk.');
        }

        $zip = new ZipArchive;
        $opened = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($opened !== true) {
            throw new RuntimeException("Gagal membuat file ZIP di {$zipPath} (kode {$opened}).");
        }

        $zip->addFile($sqlPath, basename($sqlPath));

        if ($includeDocuments) {
            $this->appendDocumentsToZip($zip, $documentDirs ?? $this->documentDirectories());
        }

        $zip->close();

        $size = filesize($zipPath);
        if ($size === false || $size <= 0) {
            throw new RuntimeException("File ZIP tidak terbentuk dengan benar: {$zipPath}");
        }

        return $size;
    }

    /**
     * @param  array<string, string>  $documentDirs
     */
    private function appendDocumentsToZip(ZipArchive $zip, array $documentDirs): void
    {
        $excludedDirs = ['backups', 'temp'];

        foreach ($documentDirs as $group => $rootDir) {
            if (! is_dir($rootDir)) {
                continue;
            }

            $normalizedRoot = rtrim(str_replace('\\', '/', realpath($rootDir) ?: $rootDir), '/');
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($rootDir, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($iterator as $file) {
                if (! $file->isFile() || $file->getFilename() === '.gitignore') {
                    continue;
                }

                $fullPath = str_replace('\\', '/', $file->getRealPath() ?: $file->getPathname());
                $relative = ltrim(substr($fullPath, strlen($normalizedRoot)), '/');
                $topSegment = strtok($relative, '/');

                if (in_array($topSegment, $excludedDirs, true)) {
                    continue;
                }

                $zip->addFile($file->getPathname(), "dokumen/{$group}/{$relative}");
            }
        }
    }

    public function cleanupExpiredLocalBackups(int $retentionDays = 14): int
    {
        $backupDir = $this->backupDirectory();
        if (! is_dir($backupDir)) {
            return 0;
        }

        $cutoffTimestamp = now()->subDays(max(1, $retentionDays))->getTimestamp();
        $deletedCount = 0;

        foreach (File::files($backupDir) as $file) {
            if ($file->getExtension() !== 'zip') {
                continue;
            }

            if ($file->getMTime() >= $cutoffTimestamp) {
                continue;
            }

            $fullPath = $file->getPathname();
            $filename = $file->getFilename();

            if (@unlink($fullPath)) {
                $deletedCount++;
                DatabaseBackup::where('filename', $filename)
                    ->whereNull('local_deleted_at')
                    ->update(['local_deleted_at' => now()]);
            }
        }

        return $deletedCount;
    }

    /**
     * @return array<int, string>
     */
    public function listLocalBackupFiles(): array
    {
        $backupDir = $this->backupDirectory();
        if (! is_dir($backupDir)) {
            return [];
        }

        $files = collect(File::files($backupDir))
            ->filter(fn (\SplFileInfo $file) => $file->getExtension() === 'zip')
            ->sortByDesc(fn (\SplFileInfo $file) => $file->getMTime())
            ->map(fn (\SplFileInfo $file) => $file->getFilename())
            ->values()
            ->all();

        return $files;
    }

    public function resolveZipPath(string $input): string
    {
        if (is_file($input)) {
            return $input;
        }

        $candidate = $this->backupDirectory().DIRECTORY_SEPARATOR.basename($input);
        if (is_file($candidate)) {
            return $candidate;
        }

        throw new RuntimeException("File backup ZIP tidak ditemukan: {$input}");
    }

    /**
     * @param  array<string, string>|null  $documentDirs
     * @return array{zip_path: string, sql_file: string, restored_documents: int}
     */
    public function restoreFromZip(
        string $zipInput,
        bool $skipDocuments = false,
        ?array $documentDirs = null,
    ): array {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('Ekstensi PHP zip (ZipArchive) tidak tersedia.');
        }

        $zipPath = $this->resolveZipPath($zipInput);
        $tempDir = storage_path('app/temp/restore_'.Str::random(10));
        File::ensureDirectoryExists($tempDir);

        try {
            $zip = new ZipArchive;
            $opened = $zip->open($zipPath);
            if ($opened !== true) {
                throw new RuntimeException("Gagal membuka arsip ZIP '{$zipPath}' (kode {$opened}).");
            }

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entryName = str_replace('\\', '/', (string) $zip->getNameIndex($i));
                if (
                    str_contains($entryName, '..')
                    || str_starts_with($entryName, '/')
                    || preg_match('/^[a-zA-Z]:/', $entryName) === 1
                ) {
                    $zip->close();
                    throw new RuntimeException("Arsip ZIP berisi path tidak aman: {$entryName}");
                }
            }

            $zip->extractTo($tempDir);
            $zip->close();

            $sqlFiles = glob($tempDir.DIRECTORY_SEPARATOR.'*.sql') ?: [];
            if ($sqlFiles === []) {
                throw new RuntimeException('File .sql tidak ditemukan di root arsip ZIP.');
            }

            $sqlPath = $sqlFiles[0];
            $this->importSqlFile($sqlPath);

            $restoredDocuments = 0;
            if (! $skipDocuments) {
                $restoredDocuments = $this->restoreExtractedDocuments(
                    $tempDir,
                    $documentDirs ?? $this->documentDirectories(),
                );
            }

            return [
                'zip_path' => $zipPath,
                'sql_file' => basename($sqlPath),
                'restored_documents' => $restoredDocuments,
            ];
        } finally {
            File::deleteDirectory($tempDir);
        }
    }

    private function importSqlFile(string $sqlPath): void
    {
        if (! is_file($sqlPath) || filesize($sqlPath) === 0) {
            throw new RuntimeException('File SQL restore kosong atau tidak dapat dibaca.');
        }

        $connectionName = (string) config('database.default');
        $connection = config("database.connections.{$connectionName}");

        if (! is_array($connection)) {
            throw new RuntimeException("Konfigurasi koneksi database '{$connectionName}' tidak ditemukan.");
        }

        $driver = (string) ($connection['driver'] ?? '');

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $this->importMysqlDatabase($connection, $sqlPath);

            return;
        }

        if ($driver === 'sqlite') {
            $this->importSqliteDatabase($connection, $sqlPath);

            return;
        }

        throw new RuntimeException("Driver database '{$driver}' belum didukung untuk restore otomatis.");
    }

    /**
     * @param  array<string, mixed>  $connection
     */
    private function importMysqlDatabase(array $connection, string $sqlPath): void
    {
        $binary = $this->resolveClientBinary(
            (string) config('services.backup.mysql_path', ''),
            [
                'mysql',
                'mariadb',
                '/usr/bin/mysql',
                '/usr/bin/mariadb',
                'C:\\laragon\\bin\\mysql\\*\\bin\\mysql.exe',
                'C:\\xampp\\mysql\\bin\\mysql.exe',
                'C:\\Program Files\\MySQL\\*\\bin\\mysql.exe',
            ],
        );

        if ($binary === null) {
            throw new RuntimeException('Binary mysql/mariadb client tidak ditemukan di container/server.');
        }

        $args = [
            $binary,
            '--host='.(string) ($connection['host'] ?? '127.0.0.1'),
            '--port='.(string) ($connection['port'] ?? '3306'),
            '--user='.(string) ($connection['username'] ?? 'root'),
        ];

        if ($this->isMariaDbBinary($binary)) {
            $args[] = '--skip-ssl';
        }

        $args[] = (string) ($connection['database'] ?? '');

        $stream = fopen($sqlPath, 'rb');
        if ($stream === false) {
            throw new RuntimeException("Gagal membuka file SQL: {$sqlPath}");
        }

        try {
            $process = new Process($args, null, [
                'MYSQL_PWD' => (string) ($connection['password'] ?? ''),
            ]);
            $process->setTimeout(900);
            $process->setInput($stream);
            $process->run();

            if (! $process->isSuccessful()) {
                $err = trim($process->getErrorOutput() ?: $process->getOutput());

                throw new RuntimeException('Restore mysql gagal: '.$err);
            }
        } finally {
            fclose($stream);
        }
    }

    /**
     * @param  array<string, mixed>  $connection
     */
    private function importSqliteDatabase(array $connection, string $sqlPath): void
    {
        $database = (string) ($connection['database'] ?? '');

        if ($database !== '' && $database !== ':memory:' && is_file($database)) {
            $head = (string) file_get_contents($sqlPath, false, null, 0, 16);
            if (str_starts_with($head, 'SQLite format 3')) {
                File::copy($sqlPath, $database);

                return;
            }
        }

        $sql = File::get($sqlPath);
        DB::unprepared($sql);
    }

    /**
     * @param  array<string, string>  $documentDirs
     */
    private function restoreExtractedDocuments(string $extractDir, array $documentDirs): int
    {
        $count = 0;
        $excludedDirs = ['backups', 'temp'];

        foreach ($documentDirs as $group => $targetRoot) {
            $sourceRoot = $extractDir.DIRECTORY_SEPARATOR.'dokumen'.DIRECTORY_SEPARATOR.$group;
            if (! is_dir($sourceRoot)) {
                continue;
            }

            File::ensureDirectoryExists($targetRoot);
            $normalizedSource = rtrim(str_replace('\\', '/', realpath($sourceRoot) ?: $sourceRoot), '/');

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($sourceRoot, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($iterator as $file) {
                if (! $file->isFile() || $file->getFilename() === '.gitignore') {
                    continue;
                }

                $fullPath = str_replace('\\', '/', $file->getRealPath() ?: $file->getPathname());
                $relative = ltrim(substr($fullPath, strlen($normalizedSource)), '/');
                $topSegment = strtok($relative, '/');

                if (in_array($topSegment, $excludedDirs, true)) {
                    continue;
                }

                $destPath = $targetRoot.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
                File::ensureDirectoryExists(dirname($destPath));
                File::copy($file->getPathname(), $destPath);
                $count++;
            }
        }

        return $count;
    }

    /**
     * @param  array<string, mixed>  $connection
     */
    private function dumpMysqlDatabase(array $connection, string $sqlPath): void
    {
        $binary = $this->resolveClientBinary(
            (string) config('services.backup.mysqldump_path', ''),
            [
                'mysqldump',
                'mariadb-dump',
                '/usr/bin/mysqldump',
                '/usr/bin/mariadb-dump',
                'C:\\laragon\\bin\\mysql\\*\\bin\\mysqldump.exe',
                'C:\\xampp\\mysql\\bin\\mysqldump.exe',
                'C:\\Program Files\\MySQL\\*\\bin\\mysqldump.exe',
            ],
        );
        if ($binary === null) {
            throw new RuntimeException('Binary mysqldump/mariadb-dump tidak ditemukan di container/server.');
        }

        $args = [
            $binary,
            '--host='.(string) ($connection['host'] ?? '127.0.0.1'),
            '--port='.(string) ($connection['port'] ?? '3306'),
            '--user='.(string) ($connection['username'] ?? 'root'),
            '--single-transaction',
            '--routines',
            '--triggers',
            '--quick',
            '--no-tablespaces',
        ];

        if ($this->isMariaDbBinary($binary)) {
            $args[] = '--skip-ssl';
        }

        $args[] = '--result-file='.$sqlPath;
        $args[] = (string) ($connection['database'] ?? '');

        $process = new Process($args, null, [
            'MYSQL_PWD' => (string) ($connection['password'] ?? ''),
        ]);
        $process->setTimeout(600);
        $process->run();

        if (! $process->isSuccessful()) {
            $err = trim($process->getErrorOutput() ?: $process->getOutput());

            throw new RuntimeException('mysqldump gagal: '.$err);
        }
    }

    /**
     * @param  array<string, mixed>  $connection
     */
    private function dumpSqliteDatabase(array $connection, string $sqlPath): void
    {
        $database = (string) ($connection['database'] ?? '');

        if ($database !== '' && $database !== ':memory:' && is_file($database)) {
            File::copy($database, $sqlPath);

            return;
        }

        $tables = DB::select("SELECT name, sql FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'");
        $dump = '-- SQLite Backup Generated at '.now()->toIso8601String()."\n";
        foreach ($tables as $table) {
            if (! empty($table->sql)) {
                $dump .= $table->sql.";\n";
            }
        }

        File::put($sqlPath, $dump);
    }

    /**
     * @param  array<int, string>  $candidates
     */
    private function resolveClientBinary(string $configured, array $candidates): ?string
    {
        if ($configured !== '' && is_executable($configured)) {
            return $configured;
        }

        foreach ($candidates as $candidate) {
            if (str_contains($candidate, '*') && function_exists('glob')) {
                $matches = glob($candidate);
                if (! empty($matches)) {
                    foreach ($matches as $match) {
                        if (is_file($match)) {
                            return $match;
                        }
                    }
                }
                continue;
            }

            if (str_contains($candidate, DIRECTORY_SEPARATOR) || str_contains($candidate, '/')) {
                if (is_file($candidate)) {
                    return $candidate;
                }

                continue;
            }

            $whichCmd = PHP_OS_FAMILY === 'Windows' ? ['where', $candidate] : ['which', $candidate];
            $proc = new Process($whichCmd);
            $proc->run();
            if ($proc->isSuccessful()) {
                $resolved = trim(strtok($proc->getOutput(), "\r\n") ?: '');
                if ($resolved !== '') {
                    return $resolved;
                }
            }
        }

        return null;
    }

    private function isMariaDbBinary(string $binary): bool
    {
        try {
            $proc = new Process([$binary, '--version']);
            $proc->setTimeout(5);
            $proc->run();

            return stripos($proc->getOutput(), 'MariaDB') !== false;
        } catch (\Throwable) {
            return false;
        }
    }
}
