<?php

namespace App\Jobs;

use App\Models\Publication;
use App\Services\Publishing\BookComposer;
use App\Services\Publishing\ManuscriptParser;
use App\Services\Publishing\OutputGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessPublicationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 900;

    public function __construct(
        private int $publicationId,
    ) {}

    public function handle(ManuscriptParser $parser, BookComposer $composer, OutputGenerator $generator): void
    {
        $publication = Publication::findOrFail($this->publicationId);

        try {
            // Step 1: Parse - download from Dropbox to temp file
            $publication->update(['status' => 'parsing']);
            $dropboxPath = $publication->source_manuscript;
            $tempDir = storage_path("app/publications/{$publication->id}");
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            $docxPath = "{$tempDir}/" . basename($dropboxPath);
            $content = Storage::disk('dropbox')->get($dropboxPath);
            file_put_contents($docxPath, $content);
            $manuscript = $parser->parse($docxPath);

            $publication->update([
                'word_count' => $manuscript->wordCount,
                'chapter_count' => $manuscript->chapterCount(),
            ]);

            // Step 2: Compose
            $publication->update(['status' => 'composing']);
            $bookHtml = $composer->compose($manuscript, $publication);

            // Save intermediate HTML
            $htmlDir = storage_path("app/publications/{$publication->id}");
            if (!is_dir($htmlDir)) {
                mkdir($htmlDir, 0755, true);
            }
            $htmlPath = "{$htmlDir}/book.html";
            file_put_contents($htmlPath, $bookHtml);
            $publication->update(['parsed_html' => "publications/{$publication->id}/book.html"]);

            // Step 3: Generate all formats (locally first, then upload to Dropbox)
            $publication->update(['status' => 'generating']);
            $dropboxOutputDir = "Forfatterskolen_app/publications/{$publication->user_id}/{$publication->id}";

            $generator->generatePdf($bookHtml, $publication);
            $pdfLocal = "{$tempDir}/book-print.pdf";
            Storage::disk('dropbox')->put("{$dropboxOutputDir}/book-print.pdf", file_get_contents($pdfLocal));
            $publication->update(['output_pdf' => "{$dropboxOutputDir}/book-print.pdf"]);

            $generator->generateEpub($manuscript, $publication);
            $epubLocal = "{$tempDir}/book.epub";
            Storage::disk('dropbox')->put("{$dropboxOutputDir}/book.epub", file_get_contents($epubLocal));
            $publication->update(['output_epub' => "{$dropboxOutputDir}/book.epub"]);

            $generator->generateDocx($manuscript, $publication);
            $docxLocal = "{$tempDir}/book-formatted.docx";
            Storage::disk('dropbox')->put("{$dropboxOutputDir}/book-formatted.docx", file_get_contents($docxLocal));
            $publication->update(['output_docx' => "{$dropboxOutputDir}/book-formatted.docx"]);

            // Calculate spine width
            $trimSize = \App\Services\Publishing\TrimSize::tryFrom($publication->trim_size) ?? \App\Services\Publishing\TrimSize::FORMAT_140x220;
            $paperType = \App\Services\Publishing\PaperType::tryFrom($publication->paper_type) ?? \App\Services\Publishing\PaperType::MUNKEN_CREAM_100;
            $spineWidth = $trimSize->spineWidth($publication->page_count ?? 0, $paperType);

            $publication->update([
                'spine_width_mm' => $spineWidth,
                'status' => 'preview',
            ]);

            Log::info("Publication {$publication->id} generated successfully", [
                'pages' => $publication->page_count,
                'words' => $publication->word_count,
                'chapters' => $publication->chapter_count,
                'spine_mm' => $spineWidth,
            ]);

        } catch (\Throwable $e) {
            $publication->update([
                'status' => 'error',
                'error_message' => $e->getMessage(),
            ]);
            Log::error("Publication {$publication->id} failed", ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
