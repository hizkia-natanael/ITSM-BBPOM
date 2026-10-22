<?php

namespace App\Services\Compression;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class ZipBundleCompressor
{
    /**
     * Bundle multiple uploaded files into a single zip archive.
     *
     * @param array<UploadedFile> $files
     */
    public function bundleMultiple(array $files, string $subDirectory, string $disk, string $mode, int $totalOriginalSize): ?array
    {
        $count = count($files);
        $zipFileName = "arsip_lampiran_{$count}_berkas_" . date('Ymd_His') . '.zip';
        $tmpZipPath = tempnam(sys_get_temp_dir(), 'bbpom_multi_zip_') . '.zip';

        $zip = new ZipArchive();
        if ($zip->open($tmpZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return null;
        }

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

        $finalPath = "{$subDirectory}/" . Str::random(40) . '.zip';
        Storage::disk($disk)->put($finalPath, file_get_contents($tmpZipPath));
        @unlink($tmpZipPath);

        return [
            'file_path' => $finalPath,
            'file_name' => $zipFileName,
            'file_size' => $zipSize,
            'original_size' => $totalOriginalSize,
            'mime_type' => 'application/zip',
            'compression_mode' => $mode,
            'is_compressed' => $zipSize < $totalOriginalSize,
            'saved_bytes' => max(0, $totalOriginalSize - $zipSize),
        ];
    }

    /**
     * Compress a single general file (>1MB) into a zip archive if it saves >= 10% space.
     */
    public function compressSingleFile(UploadedFile $file, string $originalName, string $subDirectory, string $disk): ?array
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

        if ($zipSize > 0 && $zipSize <= ($originalSize * 0.90)) {
            $finalPath = "{$subDirectory}/" . Str::random(40) . '.zip';
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
