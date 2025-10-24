<?php

namespace App\Support;

class FileIntegrity
{
    public static function resolveFilePath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (is_file($path)) {
            return $path;
        }

        $trimmed = ltrim($path, '/');

        $publicPath = public_path($trimmed);
        if (is_file($publicPath)) {
            return $publicPath;
        }

        $basePath = base_path($trimmed);
        if (is_file($basePath)) {
            return $basePath;
        }

        return null;
    }

    public static function isFileReadable(?string $absolutePath): bool
    {
        if (! $absolutePath) {
            return false;
        }

        $absolutePath = self::resolveFilePath($absolutePath);

        if (! $absolutePath || ! is_file($absolutePath)) {
            return false;
        }

        if (filesize($absolutePath) <= 0) {
            return false;
        }

        $handle = @fopen($absolutePath, 'r');

        if ($handle === false) {
            return false;
        }

        fclose($handle);

        return true;
    }

    public static function removeUploadedFile(?string $absolutePath): void
    {
        $absolutePath = $absolutePath ? self::resolveFilePath($absolutePath) : null;

        if ($absolutePath && is_file($absolutePath)) {
            @unlink($absolutePath);
        }
    }
}
