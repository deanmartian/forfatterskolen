<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class FileIntegrityService
{
    public function passes(?string $absolutePath, ?string $extension = null): bool
    {
        if (! $absolutePath || ! is_file($absolutePath) || ! is_readable($absolutePath)) {
            return false;
        }

        if (filesize($absolutePath) === 0) {
            return false;
        }

        $extension = $extension ? strtolower($extension) : strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        try {
            switch ($extension) {
                case 'pdf':
                    $handle = @fopen($absolutePath, 'rb');
                    if (! $handle) {
                        return false;
                    }

                    $header = fread($handle, 4);
                    fclose($handle);

                    return $header !== false && strncmp($header, '%PDF', 4) === 0;
                case 'docx':
                    $zip = new \ZipArchive();
                    $result = $zip->open($absolutePath);
                    if ($result !== true) {
                        return false;
                    }

                    $hasDocument = $zip->locateName('word/document.xml') !== false;
                    $zip->close();

                    return $hasDocument;
                case 'odt':
                    $zip = new \ZipArchive();
                    $result = $zip->open($absolutePath);
                    if ($result !== true) {
                        return false;
                    }

                    $hasContent = $zip->locateName('content.xml') !== false;
                    $zip->close();

                    return $hasContent;
                case 'doc':
                    $handle = @fopen($absolutePath, 'rb');
                    if (! $handle) {
                        return false;
                    }

                    $header = fread($handle, 8);
                    fclose($handle);

                    return $header !== false && strncmp($header, "\xD0\xCF\x11\xE0", 4) === 0;
                default:
                    return true;
            }
        } catch (\Throwable $throwable) {
            Log::warning('Failed validating file integrity.', [
                'file' => $absolutePath,
                'extension' => $extension,
                'error' => $throwable->getMessage(),
            ]);

            return false;
        }
    }
}

