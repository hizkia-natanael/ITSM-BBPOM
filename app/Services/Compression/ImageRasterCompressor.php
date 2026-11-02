<?php

namespace App\Services\Compression;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageRasterCompressor
{
    /**
     * Compress raster images using GD with balanced visually-lossless quality.
     */
    public function compress(UploadedFile $file, string $ext, string $subDirectory, string $disk): ?array
    {
        $realPath = $file->getRealPath();
        $originalSize = (int) $file->getSize();

        $imageInfo = @getimagesize($realPath);
        if (!$imageInfo) {
            return null;
        }

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

        [$targetWidth, $targetHeight] = $this->resolveDimensions($origWidth, $origHeight, 1920);
        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

        if ($ext === 'png' || $ext === 'webp') {
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
            $transparent = imagecolorallocatealpha($targetImage, 0, 0, 0, 127);
            imagefilledrectangle($targetImage, 0, 0, $targetWidth, $targetHeight, $transparent);
        }

        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $origWidth, $origHeight);

        $tmpPath = tempnam(sys_get_temp_dir(), 'bbpom_comp_');
        match ($ext) {
            'jpg', 'jpeg' => imagejpeg($targetImage, $tmpPath, 82),
            'png' => imagepng($targetImage, $tmpPath, 8),
            'webp' => imagewebp($targetImage, $tmpPath, 82),
            default => null,
        };

        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        clearstatcache(true, $tmpPath);
        $compressedSize = filesize($tmpPath);

        if ($compressedSize > 0 && $compressedSize < $originalSize) {
            $finalPath = "{$subDirectory}/" . Str::random(40) . '.' . $ext;
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

        return [
            'path' => $file->store($subDirectory, $disk),
            'size' => $originalSize,
            'is_compressed' => false,
            'saved_bytes' => 0,
        ];
    }

    private function resolveDimensions(int $width, int $height, int $max): array
    {
        if ($width <= $max && $height <= $max) {
            return [$width, $height];
        }

        return $width >= $height
            ? [$max, (int) round(($height / $width) * $max)]
            : [(int) round(($width / $height) * $max), $max];
    }
}
