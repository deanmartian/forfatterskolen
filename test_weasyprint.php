<?php
/**
 * Test WeasyPrint PDF generation locally on server.
 * Run: php test_weasyprint.php
 */
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Publication;
use Illuminate\Support\Facades\Process;

$pub = Publication::latest()->first();
if (!$pub) {
    echo "Ingen publikasjon funnet\n";
    exit(1);
}

echo "Publikasjon: {$pub->title} (ID: {$pub->id})\n";
echo "Status: {$pub->status}\n";
echo "Manuscript: {$pub->source_manuscript}\n\n";

// Test 1: Can we read from Dropbox?
echo "--- Test 1: Dropbox ---\n";
try {
    $content = \Illuminate\Support\Facades\Storage::disk('dropbox')->get($pub->source_manuscript);
    echo "OK: Fil hentet fra Dropbox (" . strlen($content) . " bytes)\n";
} catch (\Exception $e) {
    echo "FEIL: " . $e->getMessage() . "\n";
    exit(1);
}

// Save to temp
$tempDir = storage_path("app/publications/{$pub->id}");
@mkdir($tempDir, 0755, true);
$docxPath = "{$tempDir}/" . basename($pub->source_manuscript);
file_put_contents($docxPath, $content);
echo "Lagret til: {$docxPath}\n\n";

// Test 2: Parse manuscript
echo "--- Test 2: Parse ---\n";
$parser = new \App\Services\Publishing\ManuscriptParser();
$manuscript = $parser->parse($docxPath);
echo "OK: {$manuscript->wordCount} ord, {$manuscript->chapterCount()} kapitler\n";
foreach ($manuscript->chapters as $ch) {
    echo "  Kap {$ch['number']}: {$ch['title']} (" . strlen($ch['html']) . " tegn)\n";
}
echo "\n";

// Test 3: Compose book HTML
echo "--- Test 3: Compose ---\n";
$composer = new \App\Services\Publishing\BookComposer();
$bookHtml = $composer->compose($manuscript, $pub);
$htmlPath = "{$tempDir}/book-test.html";
file_put_contents($htmlPath, $bookHtml);
echo "OK: HTML generert (" . strlen($bookHtml) . " tegn)\n";
echo "Lagret: {$htmlPath}\n\n";

// Test 4: WeasyPrint
echo "--- Test 4: WeasyPrint ---\n";
$weasyprint = $_SERVER['HOME'] . '/.local/bin/weasyprint';
if (!file_exists($weasyprint)) {
    $weasyprint = '/home/forfatter/.local/bin/weasyprint';
}
echo "WeasyPrint: {$weasyprint}\n";

$pdfPath = "{$tempDir}/book-test.pdf";
$result = Process::timeout(120)->run("{$weasyprint} {$htmlPath} {$pdfPath} --presentational-hints 2>&1");

echo "Exit code: " . $result->exitCode() . "\n";
echo "Output: " . substr($result->output(), 0, 500) . "\n";
echo "Error: " . substr($result->errorOutput(), 0, 500) . "\n";

if (file_exists($pdfPath)) {
    $size = filesize($pdfPath);
    echo "\nPDF generert: {$pdfPath} ({$size} bytes = " . round($size/1024) . " KB)\n";

    // Count pages
    $pdfContent = file_get_contents($pdfPath);
    $pages = preg_match_all('/\/Type\s*\/Page[^s]/i', $pdfContent);
    echo "Sider: {$pages}\n";
} else {
    echo "\nFEIL: PDF ble ikke generert\n";
}

echo "\nFerdig!\n";
