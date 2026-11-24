<?php

namespace Tests\Feature;

use App\Models\BackupSetting;
use App\Models\DatabaseBackup;
use App\Models\User;
use App\Services\DatabaseBackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tests\TestCase;

class DatabaseBackupTest extends TestCase
{
    use RefreshDatabase;

    private string $backupDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->backupDir = storage_path('app/temp/phpunit-backups');
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

    public function test_non_admin_cannot_access_backup_endpoints(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $ketuaTim = User::factory()->create(['role' => 'ketua_tim']);

        $this->actingAs($user)->get('/admin/backup')->assertForbidden();
        $this->actingAs($user)->get(route('admin.backup.index'))->assertForbidden();
        $this->actingAs($user)->post(route('admin.backup.run'))->assertForbidden();
        $this->actingAs($user)->put(route('admin.backup.settings.update'), [])->assertForbidden();
        $this->actingAs($user)->get(route('admin.backup.google.connect'))->assertForbidden();

        $this->actingAs($ketuaTim)->get('/admin/backup')->assertForbidden();
        $this->actingAs($ketuaTim)->get(route('admin.backup.index'))->assertForbidden();
        $this->actingAs($ketuaTim)->post(route('admin.backup.run'))->assertForbidden();
    }

    public function test_admin_can_view_backup_page_and_navigation_link(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        BackupSetting::create([
            'auto_backup_enabled' => true,
            'schedule_time' => '02:00',
            'retention_days' => 14,
            'filename_prefix' => 'backup-elite',
            'gdrive_client_id' => 'client-id-123',
            'gdrive_client_secret' => 'super-secret-value',
            'gdrive_refresh_token' => 'refresh-token-value',
            'gdrive_folder_name' => 'Backup-ELITE',
        ]);

        $dashboardResponse = $this->actingAs($admin)->get('/admin');
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('Backup Database');

        $response = $this->actingAs($admin)->get('/admin/backup');
        $response->assertOk();
        $response->assertSee('client-id-123');
        $response->assertSee('Backup-ELITE');
        $response->assertSee('02:00');
        $response->assertDontSee('super-secret-value');
        $response->assertDontSee('refresh-token-value');
    }

    public function test_admin_can_update_backup_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->put(route('admin.backup.settings.update'), [
            'auto_backup_enabled' => true,
            'schedule_time' => '03:30',
            'retention_days' => 14,
            'filename_prefix' => 'backup-elite',
            'include_documents' => false,
            'gdrive_client_id' => 'cid-test',
            'gdrive_client_secret' => 'secret-test',
            'gdrive_folder_name' => 'Arsip-ELITE',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $setting = BackupSetting::first();
        $this->assertNotNull($setting);
        $this->assertTrue($setting->auto_backup_enabled);
        $this->assertFalse($setting->include_documents);
        $this->assertSame('03:30', $setting->schedule_time);
        $this->assertSame(14, $setting->retention_days);
        $this->assertSame('cid-test', $setting->gdrive_client_id);
        $this->assertSame('secret-test', $setting->gdrive_client_secret);
        $this->assertSame('Arsip-ELITE', $setting->gdrive_folder_name);
    }

    public function test_zip_archive_includes_documents_and_excludes_backups_folder(): void
    {
        $tempRoot = storage_path('app/temp/backup-doc-test');
        File::deleteDirectory($tempRoot);
        File::ensureDirectoryExists($tempRoot.'/private/ticket_attachments');
        File::ensureDirectoryExists($tempRoot.'/private/backups');
        File::ensureDirectoryExists($tempRoot.'/public/lampiran');

        $sqlPath = $tempRoot.'/dump.sql';
        $zipPath = $tempRoot.'/archive.zip';
        file_put_contents($sqlPath, 'SELECT 1;');
        file_put_contents($tempRoot.'/private/ticket_attachments/aduan.docx', 'doc-bytes');
        file_put_contents($tempRoot.'/private/backups/old.zip', 'skip-me');
        file_put_contents($tempRoot.'/private/.gitignore', "*\n");
        file_put_contents($tempRoot.'/public/lampiran/bukti.jpg', 'img-bytes');

        try {
            $service = app(DatabaseBackupService::class);
            $size = $service->createZipArchive($sqlPath, $zipPath, true, [
                'private' => $tempRoot.'/private',
                'public' => $tempRoot.'/public',
            ]);

            $this->assertGreaterThan(0, $size);

            $zip = new \ZipArchive;
            $this->assertTrue($zip->open($zipPath) === true);
            $this->assertNotFalse($zip->locateName('dump.sql'));
            $this->assertNotFalse($zip->locateName('dokumen/private/ticket_attachments/aduan.docx'));
            $this->assertNotFalse($zip->locateName('dokumen/public/lampiran/bukti.jpg'));
            $this->assertFalse($zip->locateName('dokumen/private/backups/old.zip'));
            $this->assertFalse($zip->locateName('dokumen/private/.gitignore'));
            $zip->close();
        } finally {
            File::deleteDirectory($tempRoot);
        }
    }

    public function test_manual_backup_creates_zip_and_uploads_to_google_drive(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        BackupSetting::create([
            'auto_backup_enabled' => true,
            'schedule_time' => '02:00',
            'retention_days' => 14,
            'filename_prefix' => 'backup-elite',
            'include_documents' => false,
            'gdrive_client_id' => 'client-id',
            'gdrive_client_secret' => 'client-secret',
            'gdrive_refresh_token' => 'refresh-token',
            'gdrive_folder_id' => 'folder-123',
            'gdrive_folder_name' => 'Backup-ELITE',
        ]);

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'fake-access-token',
                'expires_in' => 3600,
            ], 200),
            'https://www.googleapis.com/upload/drive/v3/files*' => Http::response([], 200, [
                'Location' => 'https://upload.example.test/session-1',
            ]),
            'https://upload.example.test/session-1' => Http::response([
                'id' => 'drive-file-999',
                'webViewLink' => 'https://drive.google.com/file/d/drive-file-999/view',
            ], 200),
        ]);

        $response = $this->actingAs($admin)->post(route('admin.backup.run'));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $backup = DatabaseBackup::first();
        $this->assertNotNull($backup);
        $this->assertSame('manual', $backup->trigger_type);
        $this->assertSame('success', $backup->status);
        $this->assertSame('uploaded', $backup->gdrive_status);
        $this->assertSame('drive-file-999', $backup->gdrive_file_id);
        $this->assertStringStartsWith('backup-elite-'.now('Asia/Jakarta')->format('Y-m-d').'_', $backup->filename);
        $this->assertStringEndsWith('.zip', $backup->filename);
        $this->assertFileExists((string) $backup->local_path);
        $this->assertGreaterThan(0, $backup->size_bytes);

        $this->actingAs($admin)
            ->get(route('admin.backup.download', $backup))
            ->assertOk();
    }

    public function test_scheduled_command_names_zip_by_date_and_deletes_only_expired_local_files(): void
    {
        BackupSetting::create([
            'auto_backup_enabled' => true,
            'schedule_time' => '02:00',
            'retention_days' => 14,
            'filename_prefix' => 'backup-elite',
            'include_documents' => false,
            'gdrive_client_id' => 'client-id',
            'gdrive_client_secret' => 'client-secret',
            'gdrive_refresh_token' => 'refresh-token',
            'gdrive_folder_id' => 'folder-123',
        ]);

        File::ensureDirectoryExists($this->backupDir);
        $expiredPath = $this->backupDir.DIRECTORY_SEPARATOR.'backup-elite-2026-01-01.zip';
        file_put_contents($expiredPath, 'old-zip-bytes');
        touch($expiredPath, now()->subDays(16)->getTimestamp());

        $expiredRecord = DatabaseBackup::create([
            'filename' => 'backup-elite-2026-01-01.zip',
            'local_path' => $expiredPath,
            'size_bytes' => 13,
            'trigger_type' => 'scheduled',
            'status' => 'success',
            'gdrive_status' => 'uploaded',
            'gdrive_file_id' => 'old-drive-id',
        ]);

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response(['access_token' => 'fake-access'], 200),
            'https://www.googleapis.com/upload/drive/v3/files*' => Http::response([], 200, [
                'Location' => 'https://upload.example.test/session-2',
            ]),
            'https://upload.example.test/session-2' => Http::response([
                'id' => 'drive-scheduled-id',
                'webViewLink' => 'https://drive.google.com/file/d/drive-scheduled-id/view',
            ], 200),
        ]);

        $this->artisan('elite:backup-database --trigger=scheduled')->assertSuccessful();

        $expectedZip = $this->backupDir.DIRECTORY_SEPARATOR.'backup-elite-'.now('Asia/Jakarta')->format('Y-m-d').'.zip';
        $this->assertFileExists($expectedZip);
        $this->assertFileDoesNotExist($expiredPath);

        $expiredRecord->refresh();
        $this->assertNotNull($expiredRecord->local_deleted_at);

        Http::assertNotSent(fn ($request) => $request->method() === 'DELETE');
    }

    public function test_google_oauth_callback_stores_refresh_token_and_email(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        BackupSetting::create([
            'gdrive_client_id' => 'cid-oauth',
            'gdrive_client_secret' => 'secret-oauth',
        ]);

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'access-123',
                'refresh_token' => 'refresh-from-google',
            ], 200),
            'https://www.googleapis.com/drive/v3/about*' => Http::response([
                'user' => ['emailAddress' => 'helpdesk.bbpom@gmail.com'],
            ], 200),
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['gdrive_oauth_state' => 'valid-state'])
            ->get(route('admin.backup.google.callback', [
                'state' => 'valid-state',
                'code' => 'auth-code-xyz',
            ]));

        $response->assertRedirect('/admin/backup');
        $response->assertSessionHas('success');

        $setting = BackupSetting::first();
        $this->assertSame('refresh-from-google', $setting->gdrive_refresh_token);
        $this->assertSame('helpdesk.bbpom@gmail.com', $setting->gdrive_account_email);
    }

    public function test_admin_can_delete_backup_record_and_local_file(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        File::ensureDirectoryExists($this->backupDir);
        $zipPath = $this->backupDir.DIRECTORY_SEPARATOR.'backup-elite-2026-11-19.zip';
        file_put_contents($zipPath, 'zip-content');

        $backup = DatabaseBackup::create([
            'filename' => 'backup-elite-2026-11-19.zip',
            'local_path' => $zipPath,
            'size_bytes' => 11,
            'trigger_type' => 'manual',
            'status' => 'success',
            'gdrive_status' => 'uploaded',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.backup.destroy', $backup));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertFileDoesNotExist($zipPath);
        $this->assertDatabaseMissing('database_backups', ['id' => $backup->id]);
    }

    public function test_non_admin_cannot_download_env_backup(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->postJson(route('admin.backup.env-download'), [
                'admin_password' => 'password',
                'zip_password' => 'Rahasia123',
            ])
            ->assertForbidden();
    }

    public function test_admin_env_download_rejects_wrong_password_and_encrypts_zip_on_valid_password(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => bcrypt('AdminPass123!'),
        ]);

        $this->actingAs($admin)
            ->postJson(route('admin.backup.env-download'), [
                'admin_password' => 'SalahPassword',
                'zip_password' => 'ZipSecret123!',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('admin_password');

        $response = $this->actingAs($admin)
            ->post(route('admin.backup.env-download'), [
                'admin_password' => 'AdminPass123!',
                'zip_password' => 'ZipSecret123!',
            ]);

        $response->assertOk();
        $response->assertHeader('content-type', 'application/zip');

        /** @var BinaryFileResponse $baseResponse */
        $baseResponse = $response->baseResponse;
        $downloadedZip = $baseResponse->getFile()->getPathname();

        try {
            $zip = new \ZipArchive;
            $this->assertTrue($zip->open($downloadedZip) === true);
            $this->assertFalse(@$zip->getFromName('.env'));
            $zip->setPassword('ZipSecret123!');
            $envContent = $zip->getFromName('.env');
            $this->assertIsString($envContent);
            $this->assertStringContainsString('APP_KEY=', $envContent);
            $zip->close();
        } finally {
            @unlink($downloadedZip);
        }
    }

    public function test_restore_command_restores_sql_and_documents_from_zip(): void
    {
        $tempRoot = storage_path('app/temp/restore-test');
        File::deleteDirectory($tempRoot);
        File::ensureDirectoryExists($tempRoot.'/src/private/ticket_attachments');
        File::ensureDirectoryExists($tempRoot.'/src/public/lampiran');
        File::ensureDirectoryExists($tempRoot.'/dest/private');
        File::ensureDirectoryExists($tempRoot.'/dest/public');

        $sqlPath = $tempRoot.'/restore_sample.sql';
        $zipPath = $this->backupDir.'/backup-elite-restore-test.zip';
        file_put_contents($sqlPath, "DROP TABLE IF EXISTS restore_probe;\nCREATE TABLE restore_probe (id INTEGER PRIMARY KEY, note TEXT);\nINSERT INTO restore_probe (id, note) VALUES (1, 'pulih');");
        file_put_contents($tempRoot.'/src/private/ticket_attachments/lampiran_pulih.docx', 'restored-docx');
        file_put_contents($tempRoot.'/src/public/lampiran/aduan.pdf', 'restored-pdf');

        try {
            $service = app(DatabaseBackupService::class);
            $service->createZipArchive($sqlPath, $zipPath, true, [
                'private' => $tempRoot.'/src/private',
                'public' => $tempRoot.'/src/public',
            ]);

            $result = $service->restoreFromZip($zipPath, false, [
                'private' => $tempRoot.'/dest/private',
                'public' => $tempRoot.'/dest/public',
            ]);

            $this->assertSame('restore_sample.sql', $result['sql_file']);
            $this->assertSame(2, $result['restored_documents']);
            $this->assertFileExists($tempRoot.'/dest/private/ticket_attachments/lampiran_pulih.docx');
            $this->assertSame('restored-pdf', file_get_contents($tempRoot.'/dest/public/lampiran/aduan.pdf'));
            $this->assertDatabaseHas('restore_probe', ['id' => 1, 'note' => 'pulih']);

            $this->artisan('elite:restore-database backup-elite-restore-test.zip --no-documents --force')
                ->assertSuccessful();
        } finally {
            File::deleteDirectory($tempRoot);
        }
    }
}
