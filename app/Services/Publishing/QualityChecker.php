<?php

namespace App\Services\Publishing;

class QualityChecker
{
    public function validatePdf(string $path): bool
    {
        return file_exists($path) && filesize($path) > 1000;
    }

    public function validateEpub(string $path): bool
    {
        if (!file_exists($path) || filesize($path) < 500) {
            return false;
        }

        $zip = new \ZipArchive;
        if ($zip->open($path) !== true) {
            return false;
        }

        $hasContent = $zip->getFromName('OEBPS/content.opf') !== false;
        $hasMimetype = $zip->getFromName('mimetype') !== false;
        $zip->close();

        return $hasContent && $hasMimetype;
    }

    public function validateDocx(string $path): bool
    {
        if (!file_exists($path) || filesize($path) < 500) {
            return false;
        }

        $zip = new \ZipArchive;
        if ($zip->open($path) !== true) {
            return false;
        }

        $hasDocument = $zip->getFromName('word/document.xml') !== false;
        $zip->close();

        return $hasDocument;
    }
}
