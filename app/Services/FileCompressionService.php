<?php

namespace App\Services;

use App\Services\Compression\ImageRasterCompressor;
use App\Services\Compression\ZipBundleCompressor;
use Illuminate\Http\UploadedFile;

class FileCompressionService
{
    protected ImageRasterCompressor $imageCompressor;
    protected ZipBundleCompressor $zipCompressor;

    public function __construct(
        ?ImageRasterCompressor $imageCompressor = null,
        ?ZipBundleCompressor $zipCompressor = null,
    ) {
        $this->imageCompressor = $imageCompressor ?? new ImageRasterCompressor();
        $this->zipCompressor = $zipCompressor ?? new ZipBundleCompressor();
    }

    /**
     * Process multiple uploaded files for a ticket response.
     *
     * @param array<UploadedFile> $files
     */
    public function processMultipleUploads(array $files, int|string $ticketId, string $mode = 'compressed'): array
    {
        if (count($files) === 1) {
            return $this->processUpload($files[0], $ticketId, $mode);
        }

        $disk = config('filesystems.attachment_disk', 'local');
        $subDirectory = "ticket-responses/{$ticketId}";
        $totalOriginalSize = array_sum(array_map(fn ($f) => (int) $f->getSize(), $files));

        $bundled = $this->zipCompressor->bundleMultiple($files, $subDirectory, $disk, $mode, $totalOriginalSize);

        return $bundled ?? $this->processUpload($files[0], $ticketId, $mode);
    }

    /**
     * Process a single uploaded file for a ticket response.
     */
    public function processUpload(UploadedFile $file, int|string $ticketId, string $mode = 'compressed'): array
    {
        $disk = config('filesystems.attachment_disk', 'local');
        $originalName = $file->getClientOriginalName();
        $originalSize = (int) $file->getSize();
        $mime = $file->getClientMimeType() ?: $file->getMimeType();
        $ext = strtolower($file->getClientOriginalExtension());
        $subDirectory = "ticket-responses/{$ticketId}";

        if ($mode === 'original') {
            return $this->buildPayload($file->store($subDirectory, $disk), $originalName, $originalSize, $originalSize, $mime, 'original', false, 0);
        }

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) && extension_loaded('gd')) {
            $img = $this->compressImage($file, $ext, $subDirectory, $disk);
            if ($img !== null) {
                return $this->buildPayload($img['path'], $originalName, $img['size'], $originalSize, $mime, 'compressed', $img['is_compressed'], $img['saved_bytes']);
            }
        }

        $skipExts = ['zip', 'rar', '7z', 'gz', 'tar', 'bz2', 'docx', 'xlsx', 'pptx', 'odt', 'ods', 'odp'];
        if (in_array($ext, $skipExts) || $originalSize <= 1048576 || !extension_loaded('zip')) {
            return $this->buildPayload($file->store($subDirectory, $disk), $originalName, $originalSize, $originalSize, $mime, 'compressed', false, 0);
        }

        $zip = $this->compressGeneralFile($file, $originalName, $subDirectory, $disk);
        if ($zip !== null && $zip['is_compressed']) {
            return $this->buildPayload($zip['path'], $zip['name'], $zip['size'], $originalSize, 'application/zip', 'compressed', true, $zip['saved_bytes']);
        }

        return $this->buildPayload($file->store($subDirectory, $disk), $originalName, $originalSize, $originalSize, $mime, 'compressed', false, 0);
    }

    protected function compressImage(UploadedFile $file, string $ext, string $subDirectory, string $disk): ?array
    {
        return $this->imageCompressor->compress($file, $ext, $subDirectory, $disk);
    }

    protected function compressGeneralFile(UploadedFile $file, string $originalName, string $subDirectory, string $disk): ?array
    {
        return $this->zipCompressor->compressSingleFile($file, $originalName, $subDirectory, $disk);
    }

    private function buildPayload(string $path, string $name, int $size, int $origSize, ?string $mime, string $mode, bool $compressed, int $saved): array
    {
        return [
            'file_path' => $path,
            'file_name' => $name,
            'file_size' => $size,
            'original_size' => $origSize,
            'mime_type' => $mime,
            'compression_mode' => $mode,
            'is_compressed' => $compressed,
            'saved_bytes' => $saved,
        ];
    }
}
