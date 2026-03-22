<?php

namespace App\Services\Publishing;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;

class PdfRenderer
{
    public function render(string $html, string $outputPath, TrimSize $trimSize): void
    {
        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Save HTML to temp file
        $htmlPath = $dir . '/book-temp.html';
        file_put_contents($htmlPath, $html);

        // Use WeasyPrint for full CSS Paged Media support
        $weasyprint = $this->findWeasyPrint();

        $result = Process::timeout(600)->run([
            $weasyprint,
            $htmlPath,
            $outputPath,
            '--presentational-hints',
        ]);

        // Clean up temp HTML
        @unlink($htmlPath);

        // WeasyPrint returns exit code 1 for warnings (not errors)
        // Check if PDF was actually generated and has content
        if (!file_exists($outputPath) || filesize($outputPath) < 1000) {
            Log::warning('WeasyPrint did not produce valid PDF, falling back to DomPDF', [
                'exit_code' => $result->exitCode(),
                'output' => substr($result->output(), 0, 500),
                'error' => substr($result->errorOutput(), 0, 500),
            ]);
            $this->renderWithDomPdf($html, $outputPath, $trimSize);
        }
    }

    public function getPageCount(string $html, TrimSize $trimSize): int
    {
        // Generate a temp PDF and count pages
        $tempPath = storage_path('app/temp-pagecount-' . uniqid() . '.pdf');
        $this->render($html, $tempPath, $trimSize);

        if (!file_exists($tempPath)) {
            return 0;
        }

        // Count pages by reading PDF cross-reference
        $content = file_get_contents($tempPath);
        $count = preg_match_all('/\/Type\s*\/Page[^s]/i', $content);
        @unlink($tempPath);

        return max($count, 1);
    }

    private function findWeasyPrint(): string
    {
        // Check common locations
        $home = $_SERVER['HOME'] ?? $_ENV['HOME'] ?? '/home/forfatter';
        $paths = [
            $home . '/.local/bin/weasyprint',
            '/home/forfatter/.local/bin/weasyprint',
            '/usr/local/bin/weasyprint',
            '/usr/bin/weasyprint',
        ];

        foreach ($paths as $path) {
            if (file_exists($path) && is_executable($path)) {
                return $path;
            }
        }

        // Fallback: use python module
        return 'python3.12 -m weasyprint';
    }

    /**
     * Fallback DomPDF renderer for when WeasyPrint is unavailable
     */
    private function renderWithDomPdf(string $html, string $outputPath, TrimSize $trimSize): void
    {
        $dims = $trimSize->dimensions();
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper([0, 0, $dims['width'] * 2.8346, $dims['height'] * 2.8346]);
        $dompdf->getOptions()->set('defaultFont', 'DejaVu Serif');
        $dompdf->getOptions()->set('isHtml5ParserEnabled', true);
        $dompdf->render();
        file_put_contents($outputPath, $dompdf->output());
    }
}
