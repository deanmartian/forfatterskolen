<?php

namespace App\Support;

class FileIntegrity
{
    public static function isFileReadable(?string $absolutePath): bool
    {
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
        if ($absolutePath && is_file($absolutePath)) {
            @unlink($absolutePath);
        }
    }
}
