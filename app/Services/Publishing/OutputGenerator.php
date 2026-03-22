<?php

namespace App\Services\Publishing;

use App\Models\Publication;

class OutputGenerator
{
    public function __construct(
        private PdfRenderer $pdfRenderer,
        private EpubBuilder $epubBuilder,
        private DocxFormatter $docxFormatter,
    ) {}

    public function generatePdf(string $bookHtml, Publication $publication): string
    {
        $trimSize = TrimSize::tryFrom($publication->trim_size) ?? TrimSize::FORMAT_140x220;
        $outputDir = storage_path("app/publications/{$publication->id}");
        $pdfPath = "{$outputDir}/book-print.pdf";

        $this->pdfRenderer->render($bookHtml, $pdfPath, $trimSize);

        // Count pages from generated PDF instead of rendering again
        $pageCount = 0;
        if (file_exists($pdfPath) && filesize($pdfPath) > 1000) {
            $content = file_get_contents($pdfPath);
            $pageCount = preg_match_all('/\/Type\s*\/Page[^s]/i', $content);
        }
        $publication->update(['page_count' => max($pageCount, 1)]);

        return $pdfPath;
    }

    public function generateEpub(ParsedManuscript $manuscript, Publication $publication): string
    {
        $outputDir = storage_path("app/publications/{$publication->id}");
        $epubPath = "{$outputDir}/book.epub";

        $this->epubBuilder->build($manuscript, $publication, $epubPath);

        return $epubPath;
    }

    public function generateDocx(ParsedManuscript $manuscript, Publication $publication): string
    {
        $outputDir = storage_path("app/publications/{$publication->id}");
        $docxPath = "{$outputDir}/book-formatted.docx";

        $this->docxFormatter->format($manuscript, $publication, $docxPath);

        return $docxPath;
    }
}
