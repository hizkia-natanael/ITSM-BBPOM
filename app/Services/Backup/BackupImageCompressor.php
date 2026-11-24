<?php

namespace App\Services\Backup;

use GdImage;

class BackupImageCompressor
{
    private const SUPPORTED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    private const MIN_BYTES_TO_COMPRESS = 20480;

    /**
     * Kompres salinan gambar di memori untuk arsip ZIP backup tanpa mengubah file asli.
     */
    public function compressForArchive(string $sourcePath): ?string
    {
        if (! extension_loaded('gd') || ! config('services.backup.compress_images', true)) {
            return null;
        }

        $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        $originalSize = @filesize($sourcePath);
        if (! in_array($ext, self::SUPPORTED_EXTENSIONS, true) || $originalSize === false || $originalSize < self::MIN_BYTES_TO_COMPRESS) {
            return null;
        }

        $info = @getimagesize($sourcePath);
        if (! $info || $info[0] <= 0 || $info[1] <= 0) {
            return null;
        }

        if (ini_get('memory_limit') !== '-1') {
            @ini_set('memory_limit', '256M');
        }

        $source = $this->createImageResource($sourcePath, $ext);
        if (! $source) {
            return null;
        }

        $maxDim = max(640, (int) config('services.backup.image_max_dimension', 1440));
        $quality = max(40, min(95, (int) config('services.backup.image_quality', 72)));
        $target = $this->resizeImage($source, $info[0], $info[1], $maxDim, $ext);
        imagedestroy($source);
        if (! $target) {
            return null;
        }

        $bytes = $this->encodeImage($target, $ext, $quality);
        imagedestroy($target);

        return ($bytes !== null && strlen($bytes) < $originalSize) ? $bytes : null;
    }

    private function createImageResource(string $path, string $ext): ?GdImage
    {
        $img = match ($ext) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($path),
            'png' => @imagecreatefrompng($path),
            'webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };

        return $img instanceof GdImage ? $img : null;
    }

    private function resizeImage(GdImage $source, int $origW, int $origH, int $maxDim, string $ext): ?GdImage
    {
        [$targetW, $targetH] = ($origW <= $maxDim && $origH <= $maxDim)
            ? [$origW, $origH]
            : ($origW >= $origH
                ? [$maxDim, max(1, (int) round(($origH / $origW) * $maxDim))]
                : [max(1, (int) round(($origW / $origH) * $maxDim)), $maxDim]);

        $target = imagecreatetruecolor($targetW, $targetH);
        if (! $target instanceof GdImage) {
            return null;
        }

        if ($ext === 'png' || $ext === 'webp') {
            imagealphablending($target, false);
            imagesavealpha($target, true);
            $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
            if ($transparent !== false) {
                imagefilledrectangle($target, 0, 0, $targetW, $targetH, $transparent);
            }
        }

        imagecopyresampled($target, $source, 0, 0, 0, 0, $targetW, $targetH, $origW, $origH);

        return $target;
    }

    private function encodeImage(GdImage $target, string $ext, int $quality): ?string
    {
        if ($ext === 'png' && imageistruecolor($target)) {
            @imagetruecolortopalette($target, false, 256);
        }

        ob_start();
        $ok = match ($ext) {
            'jpg', 'jpeg' => @imagejpeg($target, null, $quality),
            'png' => @imagepng($target, null, 9),
            'webp' => function_exists('imagewebp') ? @imagewebp($target, null, $quality) : false,
            default => false,
        };
        $raw = ob_get_clean();

        return ($ok && is_string($raw) && $raw !== '') ? $raw : null;
    }
}
