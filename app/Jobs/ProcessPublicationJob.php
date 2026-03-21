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

class ProcessPublicationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 300;

    public function __construct(
        private int $publicationId,
    ) {}

    public function handle(ManuscriptParser $parser, BookComposer $composer, OutputGenerator $generator): void
    {
        $publication = Publication::findOrFail($this->publicationId);

        try {
            // Step 1: Parse
            $publication->update(['status' => 'parsing']);
            $docxPath = storage_path('app/' . ltrim($publication->source_manuscript, '/'));
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

            // Step 3: Generate all formats
            $publication->update(['status' => 'generating']);

            $generator->generatePdf($bookHtml, $publication);
            $publication->update(['output_pdf' => "publications/{$publication->id}/book-print.pdf"]);

            $generator->generateEpub($manuscript, $publication);
            $publication->update(['output_epub' => "publications/{$publication->id}/book.epub"]);

            $generator->generateDocx($manuscript, $publication);
            $publication->update(['output_docx' => "publications/{$publication->id}/book-formatted.docx"]);

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
