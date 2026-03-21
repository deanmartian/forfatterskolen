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

            // Step 3: Generate PDF
            $publication->update(['status' => 'generating']);
            $pdfPath = $generator->generatePdf($bookHtml, $publication);
            $publication->update([
                'output_pdf' => "publications/{$publication->id}/book-print.pdf",
                'status' => 'preview',
            ]);

            Log::info("Publication {$publication->id} generated successfully", [
                'pages' => $publication->page_count,
                'words' => $publication->word_count,
                'chapters' => $publication->chapter_count,
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
