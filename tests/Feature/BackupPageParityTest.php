<?php

namespace Tests\Feature;

use App\Models\BackupSetting;
use App\Models\DatabaseBackup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BackupPageParityTest extends TestCase
{
    use RefreshDatabase;

    private string $backupDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->backupDir = storage_path('app/temp/phpunit-backup-parity');
        config()->set('services.backup.local_dir', $this->backupDir);
        File::ensureDirectoryExists($this->backupDir);
        File::cleanDirectory($this->backupDir);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->backupDir)) {
            File::deleteDirectory($this->backupDir);
        }

        parent::tearDown();
    }

    public function test_backup_page_renders_oauth_controls_folder_id_and_modals(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        BackupSetting::create([
            'auto_backup_enabled' => true,
            'schedule_time' => '02:00',
            'retention_days' => 14,
            'filename_prefix' => 'backup-elite',
            'gdrive_client_id' => 'cid-parity',
            'gdrive_client_secret' => 'secret-parity',
            'gdrive_refresh_token' => 'refresh-parity',
            'gdrive_folder_id' => 'folder-xyz-99',
            'gdrive_folder_name' => 'Backup-ELITE',
            'gdrive_account_email' => 'backup@bbpom.go.id',
        ]);

        DatabaseBackup::create([
            'filename' => 'backup-elite-2026-11-24_090000.zip',
            'trigger_type' => 'manual',
            'status' => 'failed',
            'gdrive_status' => 'failed',
            'error_message' => 'Simulated verbose failure message for modal inspection',
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get('/admin/backup');

        $response->assertOk();
        $response->assertSee('Authorized Redirect URI:');
        $response->assertSee(route('admin.backup.google.callback'));
        $response->assertSee('Hubungkan Ulang GDrive');
        $response->assertSee('Putus Koneksi');
        $response->assertSee('GDrive Terhubung (backup@bbpom.go.id)');
        $response->assertSee('folder-xyz-99');
        $response->assertSee('Muat Ulang');
        $response->assertSee('Detail Kegagalan Backup');
        $response->assertSee('Hapus Riwayat Backup');
        $response->assertSee('Unduh Backup .env Terenkripsi');
    }

    public function test_stale_pending_backup_older_than_15_minutes_is_marked_failed_on_page_load(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $stale = DatabaseBackup::create([
            'filename' => 'backup-elite-stale.zip',
            'trigger_type' => 'manual',
            'status' => 'running',
            'gdrive_status' => 'pending',
            'created_by' => $admin->id,
        ]);
        DatabaseBackup::whereKey($stale->id)->update([
            'created_at' => now()->subMinutes(20),
        ]);

        $this->actingAs($admin)->get('/admin/backup')->assertOk();

        $stale->refresh();
        $this->assertSame('failed', $stale->status);
        $this->assertSame('failed', $stale->gdrive_status);
        $this->assertNotNull($stale->completed_at);
        $this->assertStringContainsString('timeout', (string) $stale->error_message);
    }

    public function test_pdo_dumper_fallback_writes_valid_sql_dump(): void
    {
        User::factory()->create(['email' => 'dumper@pom.go.id']);

        $sqlPath = $this->backupDir.DIRECTORY_SEPARATOR.'pdo_fallback.sql';
        $dumper = app(\App\Services\Backup\MysqlPdoDumper::class);
        $dumper->dump(['database' => config('database.connections.sqlite.database')], $sqlPath);

        $this->assertFileExists($sqlPath);
        $content = (string) file_get_contents($sqlPath);
        $this->assertStringContainsString('DROP TABLE IF EXISTS `users`', $content);
        $this->assertStringContainsString('dumper@pom.go.id', $content);
    }
}
