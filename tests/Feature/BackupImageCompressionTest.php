<?php

namespace Tests\Feature;

use App\Services\DatabaseBackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use ZipArchive;

class BackupImageCompressionTest extends TestCase
{
    use RefreshDatabase;

    private string $tempRoot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tempRoot = storage_path('app/temp/phpunit-img-compress');
        File::deleteDirectory($this->tempRoot);
        File::ensureDirectoryExists($this->tempRoot.'/private/attachments');
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->tempRoot);
        parent::tearDown();
    }

    public function test_backup_zip_compresses_image_copy_without_modifying_original_file(): void
    {
        $pngPath = $this->tempRoot.'/private/attachments/large-screenshot.png';
        $img = imagecreatetruecolor(2200, 1400);
        for ($y = 0; $y < 1400; $y += 10) {
            $color = imagecolorallocate($img, ($y * 3) % 255, ($y * 5) % 255, ($y * 7) % 255);
            imagefilledrectangle($img, 0, $y, 2199, $y + 9, $color);
        }
        imagepng($img, $pngPath, 0);
        imagedestroy($img);

        $originalSize = filesize($pngPath);
        $originalHash = md5_file($pngPath);
        $sqlPath = $this->tempRoot.'/dump.sql';
        $zipPath = $this->tempRoot.'/backup.zip';
        file_put_contents($sqlPath, 'SELECT 1;');

        config()->set('services.backup.compress_images', true);
        config()->set('services.backup.image_max_dimension', 1440);
        config()->set('services.backup.image_quality', 72);

        app(DatabaseBackupService::class)->createZipArchive($sqlPath, $zipPath, true, [
            'private' => $this->tempRoot.'/private',
        ]);

        // File asli di server tidak berubah
        $this->assertSame($originalSize, filesize($pngPath));
        $this->assertSame($originalHash, md5_file($pngPath));

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($zipPath) === true);
        $stat = $zip->statName('dokumen/private/attachments/large-screenshot.png');
        $archivedBytes = $zip->getFromName('dokumen/private/attachments/large-screenshot.png');
        $zip->close();

        $this->assertIsArray($stat);
        $this->assertIsString($archivedBytes);
        $this->assertLessThan($originalSize, strlen($archivedBytes));
    }

    public function test_backup_zip_keeps_original_bytes_when_image_compression_disabled(): void
    {
        $jpgPath = $this->tempRoot.'/private/attachments/photo.jpg';
        $img = imagecreatetruecolor(800, 600);
        imagefilledrectangle($img, 0, 0, 799, 599, imagecolorallocate($img, 16, 120, 88));
        imagejpeg($img, $jpgPath, 95);
        imagedestroy($img);

        $originalBytes = file_get_contents($jpgPath);
        $sqlPath = $this->tempRoot.'/dump.sql';
        $zipPath = $this->tempRoot.'/backup-raw.zip';
        file_put_contents($sqlPath, 'SELECT 1;');

        config()->set('services.backup.compress_images', false);

        app(DatabaseBackupService::class)->createZipArchive($sqlPath, $zipPath, true, [
            'private' => $this->tempRoot.'/private',
        ]);

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($zipPath) === true);
        $archivedBytes = $zip->getFromName('dokumen/private/attachments/photo.jpg');
        $zip->close();

        $this->assertSame($originalBytes, $archivedBytes);
    }
}
