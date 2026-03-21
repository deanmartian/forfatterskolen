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

        $pageCount = $this->pdfRenderer->getPageCount($bookHtml, $trimSize);
        $publication->update(['page_count' => $pageCount]);

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
