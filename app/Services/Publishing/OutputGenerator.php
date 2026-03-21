<?php

namespace App\Services\Publishing;

use App\Models\Publication;

class OutputGenerator
{
    public function __construct(
        private PdfRenderer $pdfRenderer,
    ) {}

    public function generatePdf(string $bookHtml, Publication $publication): string
    {
        $trimSize = TrimSize::tryFrom($publication->trim_size) ?? TrimSize::FORMAT_140x220;
        $outputDir = storage_path("app/publications/{$publication->id}");
        $pdfPath = "{$outputDir}/book-print.pdf";

        $this->pdfRenderer->render($bookHtml, $pdfPath, $trimSize);

        $pageCount = $this->pdfRenderer->getPageCount($bookHtml, $trimSize);
        $publication->update(['page_count' => $pageCount]);

        return $pdfPath;
    }
}
