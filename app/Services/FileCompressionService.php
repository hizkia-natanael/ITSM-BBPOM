<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class FileCompressionService
{
    /**
     * Process multiple uploaded files for a ticket response.
     * Bundles all files into a single zip archive.
     *
     * @param array<UploadedFile> $files
     * @param int|string $ticketId
     * @param string $mode 'compressed' | 'original'
     * @return array
     */
    public function processMultipleUploads(array $files, int|string $ticketId, string $mode = 'compressed'): array
    {
        $disk = config('filesystems.attachment_disk', 'local');
        $subDirectory = "ticket-responses/{$ticketId}";
        $totalOriginalSize = 0;

        foreach ($files as $f) {
            $totalOriginalSize += (int) $f->getSize();
        }

        if (count($files) === 1) {
            return $this->processUpload($files[0], $ticketId, $mode);
        }

        // Multiple files: Always bundle into a zip archive
        $count = count($files);
        $timestamp = date('Ymd_His');
        $zipFileName = "arsip_lampiran_{$count}_berkas_{$timestamp}.zip";
        $tmpZipPath = tempnam(sys_get_temp_dir(), 'bbpom_multi_zip_') . '.zip';

        $zip = new ZipArchive();
        if ($zip->open($tmpZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            // Fallback: if zip failed to create, process the first file
            return $this->processUpload($files[0], $ticketId, $mode);
        }

        // Add all files into the zip archive with deduplicated entry names
        $usedNames = [];
        foreach ($files as $f) {
            $origName = $f->getClientOriginalName();
            $entryName = $origName;
            $counter = 1;

            while (in_array($entryName, $usedNames, true)) {
                $ext = pathinfo($origName, PATHINFO_EXTENSION);
                $filename = pathinfo($origName, PATHINFO_FILENAME);
                $entryName = $ext ? "{$filename}_{$counter}.{$ext}" : "{$origName}_{$counter}";
                $counter++;
            }

            $usedNames[] = $entryName;
            $zip->addFile($f->getRealPath(), $entryName);
        }

        $zip->close();
        clearstatcache(true, $tmpZipPath);
        $zipSize = file_exists($tmpZipPath) ? filesize($tmpZipPath) : 0;

        $randomName = Str::random(40) . '.zip';
        $finalPath = "{$subDirectory}/{$randomName}";

        Storage::disk($disk)->put($finalPath, file_get_contents($tmpZipPath));
        @unlink($tmpZipPath);

        $savedBytes = max(0, $totalOriginalSize - $zipSize);
        $isCompressed = $zipSize < $totalOriginalSize;

        return [
            'file_path' => $finalPath,
            'file_name' => $zipFileName,
            'file_size' => $zipSize,
            'original_size' => $totalOriginalSize,
            'mime_type' => 'application/zip',
            'compression_mode' => $mode,
            'is_compressed' => $isCompressed,
            'saved_bytes' => $savedBytes,
        ];
    }

    /**
     * Process an uploaded file for a ticket response.
     *
     * @param UploadedFile $file
     * @param int|string $ticketId
     * @param string $mode 'compressed' | 'original'
     * @return array
     */
    public function processUpload(UploadedFile $file, int|string $ticketId, string $mode = 'compressed'): array
    {
        $disk = config('filesystems.attachment_disk', 'local');
        $originalName = $file->getClientOriginalName();
        $originalSize = (int) $file->getSize();
        $mime = $file->getClientMimeType() ?: $file->getMimeType();
        $ext = strtolower($file->getClientOriginalExtension());
        $subDirectory = "ticket-responses/{$ticketId}";

        // If mode is strictly original, store as-is
        if ($mode === 'original') {
            $path = $file->store($subDirectory, $disk);

            return [
                'file_path' => $path,
                'file_name' => $originalName,
                'file_size' => $originalSize,
                'original_size' => $originalSize,
                'mime_type' => $mime,
                'compression_mode' => 'original',
                'is_compressed' => false,
                'saved_bytes' => 0,
            ];
        }

        // Mode is 'compressed'
        // 1. Image compression (Visually lossless, balanced high quality)
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) && extension_loaded('gd')) {
            $imageResult = $this->compressImage($file, $ext, $subDirectory, $disk);
            if ($imageResult !== null) {
                return [
                    'file_path' => $imageResult['path'],
                    'file_name' => $originalName,
                    'file_size' => $imageResult['size'],
                    'original_size' => $originalSize,
                    'mime_type' => $mime,
                    'compression_mode' => 'compressed',
                    'is_compressed' => $imageResult['is_compressed'],
                    'saved_bytes' => $imageResult['saved_bytes'],
                ];
            }
        }

        // 2. Non-image files: check if smart archiving/deflate is warranted
        // Rule: Files > 1 MB (1048576 bytes) are zipped
        $alreadyCompressedArchives = ['zip', 'rar', '7z', 'gz', 'tar', 'bz2'];
        $alreadyCompressedOffice = ['docx', 'xlsx', 'pptx', 'odt', 'ods', 'odp'];
        $oneMegabyte = 1048576; // 1 MB in bytes

        // If already archive or office XML container, or file is <= 1 MB, or zip extension not loaded: keep intact
        if (in_array($ext, $alreadyCompressedArchives) || in_array($ext, $alreadyCompressedOffice) || $originalSize <= $oneMegabyte || !extension_loaded('zip')) {
            $path = $file->store($subDirectory, $disk);

            return [
                'file_path' => $path,
                'file_name' => $originalName,
                'file_size' => $originalSize,
                'original_size' => $originalSize,
                'mime_type' => $mime,
                'compression_mode' => 'compressed',
                'is_compressed' => false,
                'saved_bytes' => 0,
            ];
        }

        // File is > 1 MB and non-archive: compress into ZipArchive
        $zipResult = $this->compressGeneralFile($file, $originalName, $subDirectory, $disk);
        if ($zipResult !== null && $zipResult['is_compressed']) {
            return [
                'file_path' => $zipResult['path'],
                'file_name' => $zipResult['name'],
                'file_size' => $zipResult['size'],
                'original_size' => $originalSize,
                'mime_type' => 'application/zip',
                'compression_mode' => 'compressed',
                'is_compressed' => true,
                'saved_bytes' => $zipResult['saved_bytes'],
            ];
        }

        // Fallback: store as-is if compression didn't save space
        $path = $file->store($subDirectory, $disk);

        return [
            'file_path' => $path,
            'file_name' => $originalName,
            'file_size' => $originalSize,
            'original_size' => $originalSize,
            'mime_type' => $mime,
            'compression_mode' => 'compressed',
            'is_compressed' => false,
            'saved_bytes' => 0,
        ];
    }

    /**
     * Compress raster images using GD with balanced visually-lossless quality.
     */
    protected function compressImage(UploadedFile $file, string $ext, string $subDirectory, string $disk): ?array
    {
        $realPath = $file->getRealPath();
        $originalSize = (int) $file->getSize();

        // Validate image
        $imageInfo = @getimagesize($realPath);
        if (!$imageInfo) {
            return null;
        }

        // Dynamically ensure sufficient memory limit for GD raster transformations
        if (ini_get('memory_limit') !== '-1') {
            @ini_set('memory_limit', '256M');
        }

        [$origWidth, $origHeight] = $imageInfo;
        if ($origWidth <= 0 || $origHeight <= 0) {
            return null;
        }

        $sourceImage = match ($ext) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($realPath),
            'png' => @imagecreatefrompng($realPath),
            'webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($realPath) : null,
            default => null,
        };

        if (!$sourceImage) {
            return null;
        }

        // Check if dimension downscaling is needed (max 1920px for high definition display)
        $maxDimension = 1920;
        $targetWidth = $origWidth;
        $targetHeight = $origHeight;

        if ($origWidth > $maxDimension || $origHeight > $maxDimension) {
            if ($origWidth >= $origHeight) {
                $targetWidth = $maxDimension;
                $targetHeight = (int) round(($origHeight / $origWidth) * $maxDimension);
            } else {
                $targetHeight = $maxDimension;
                $targetWidth = (int) round(($origWidth / $origHeight) * $maxDimension);
            }
        }

        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

        // Preserve transparency for PNG and WebP
        if ($ext === 'png' || $ext === 'webp') {
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
            $transparentColor = imagecolorallocatealpha($targetImage, 0, 0, 0, 127);
            imagefilledrectangle($targetImage, 0, 0, $targetWidth, $targetHeight, $transparentColor);
        }

        imagecopyresampled(
            $targetImage,
            $sourceImage,
            0, 0, 0, 0,
            $targetWidth,
            $targetHeight,
            $origWidth,
            $origHeight
        );

        // Output to temporary file
        $tmpPath = tempnam(sys_get_temp_dir(), 'bbpom_comp_');

        if ($ext === 'jpg' || $ext === 'jpeg') {
            imagejpeg($targetImage, $tmpPath, 82); // High quality balanced JPEG
        } elseif ($ext === 'png') {
            imagepng($targetImage, $tmpPath, 8); // PNG compression level 8
        } elseif ($ext === 'webp') {
            imagewebp($targetImage, $tmpPath, 82);
        }

        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        clearstatcache(true, $tmpPath);
        $compressedSize = filesize($tmpPath);

        // Only use compressed if it actually saved bytes
        if ($compressedSize > 0 && $compressedSize < $originalSize) {
            $randomName = Str::random(40) . '.' . $ext;
            $finalPath = "{$subDirectory}/{$randomName}";

            Storage::disk($disk)->put($finalPath, file_get_contents($tmpPath));
            @unlink($tmpPath);

            return [
                'path' => $finalPath,
                'size' => $compressedSize,
                'is_compressed' => true,
                'saved_bytes' => $originalSize - $compressedSize,
            ];
        }

        @unlink($tmpPath);

        // Otherwise store original
        $finalPath = $file->store($subDirectory, $disk);
        return [
            'path' => $finalPath,
            'size' => $originalSize,
            'is_compressed' => false,
            'saved_bytes' => 0,
        ];
    }

    /**
     * Compress general arbitrary files (documents, text, vectors) into zip archive if it saves space.
     */
    protected function compressGeneralFile(UploadedFile $file, string $originalName, string $subDirectory, string $disk): ?array
    {
        $originalSize = (int) $file->getSize();
        $tmpZipPath = tempnam(sys_get_temp_dir(), 'bbpom_zip_') . '.zip';

        $zip = new ZipArchive();
        if ($zip->open($tmpZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return null;
        }

        $zip->addFile($file->getRealPath(), $originalName);
        $zip->close();

        clearstatcache(true, $tmpZipPath);
        $zipSize = file_exists($tmpZipPath) ? filesize($tmpZipPath) : 0;

        // Check if zip saved at least 10% space
        if ($zipSize > 0 && $zipSize <= ($originalSize * 0.90)) {
            $randomName = Str::random(40) . '.zip';
            $finalPath = "{$subDirectory}/{$randomName}";

            Storage::disk($disk)->put($finalPath, file_get_contents($tmpZipPath));
            @unlink($tmpZipPath);

            return [
                'path' => $finalPath,
                'name' => $originalName . '.zip',
                'size' => $zipSize,
                'is_compressed' => true,
                'saved_bytes' => $originalSize - $zipSize,
            ];
        }

        @unlink($tmpZipPath);
        return null;
    }
}
