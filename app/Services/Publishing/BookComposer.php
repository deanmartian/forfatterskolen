<?php

namespace App\Services\Publishing;

use App\Models\Publication;
use Illuminate\Support\Facades\View;

class BookComposer
{
    public function compose(ParsedManuscript $manuscript, Publication $publication): string
    {
        $theme = $publication->theme ?? 'classic';
        $format = $publication->trim_size ?? '140x220';

        // Prepare chapter data for Blade templates
        $chapters = [];
        foreach ($manuscript->chapters as $chapter) {
            $chapters[] = [
                'number' => $chapter['number'],
                'title' => $chapter['title'],
                'html' => $chapter['html'],
                'page' => '', // Page numbers filled by PDF renderer
            ];
        }

        // Check if Blade templates exist
        $themeView = "publishing.themes.{$theme}";
        if (View::exists($themeView)) {
            // Use the professional Blade template system
            return view($themeView, [
                'format' => $format,
                'book' => $publication,
                'chapters' => $chapters,
                'overrides' => [],
            ])->render();
        }

        // Fallback: inline HTML generation (for themes without Blade templates)
        return $this->composeFallback($manuscript, $publication);
    }

    /**
     * Fallback composer for when Blade templates are not available.
     */
    private function composeFallback(ParsedManuscript $manuscript, Publication $publication): string
    {
        $trimSize = TrimSize::tryFrom($publication->trim_size) ?? TrimSize::FORMAT_140x220;
        $dims = $trimSize->dimensions();
        $theme = $publication->theme ?? 'classic';
        $themeCss = $this->getThemeCss($theme);

        $chaptersHtml = '';
        foreach ($manuscript->chapters as $chapter) {
            $title = htmlspecialchars($chapter['title']);
            $chaptersHtml .= '<div class="chapter">';
            if ($title) {
                $chaptersHtml .= "<h1>{$title}</h1>\n";
            }
            $chaptersHtml .= $chapter['html'];
            $chaptersHtml .= "</div>\n";
        }

        $frontMatter = $this->buildFrontMatter($publication);

        return <<<HTML
<!DOCTYPE html>
<html lang="{$publication->language}">
<head>
<meta charset="UTF-8">
<title>{$publication->title}</title>
<style>
@page {
    size: {$dims['width']}mm {$dims['height']}mm;
    margin-top: {$dims['marginTop']}mm;
    margin-bottom: {$dims['marginBottom']}mm;
    margin-left: {$dims['marginInside']}mm;
    margin-right: {$dims['marginOutside']}mm;
}
body {
    font-family: 'DejaVu Serif', 'Georgia', serif;
    font-size: 11pt;
    line-height: 1.5;
    color: #1a1a1a;
    text-align: justify;
    orphans: 3;
    widows: 3;
}
h1, h2, h3, h4 { page-break-after: avoid; text-align: left; }
h1 { font-size: 20pt; margin: 30mm 0 10mm; }
h2 { font-size: 16pt; margin: 15mm 0 8mm; }
p { margin: 0 0 0.4em; }
p + p { text-indent: 1.5em; margin-top: 0; }
.chapter { page-break-before: always; }
.chapter:first-of-type { page-break-before: auto; }
.chapter p:first-of-type { text-indent: 0; }
.front-matter { text-align: center; page-break-after: always; }
.title-page h1 { font-size: 28pt; margin-top: 40mm; }
.title-page .author { font-size: 16pt; margin-top: 15mm; color: #444; }
.title-page .publisher { font-size: 12pt; margin-top: 30mm; color: #666; }
.copyright-page { font-size: 9pt; color: #666; margin-top: 60mm; line-height: 1.8; text-align: left; }
.dedication { font-style: italic; margin-top: 50mm; font-size: 12pt; }
{$themeCss}
</style>
</head>
<body>
{$frontMatter}
{$chaptersHtml}
</body>
</html>
HTML;
    }

    private function buildFrontMatter(Publication $publication): string
    {
        $title = htmlspecialchars($publication->title);
        $subtitle = $publication->subtitle ? '<h2>' . htmlspecialchars($publication->subtitle) . '</h2>' : '';
        $author = htmlspecialchars($publication->author_name);
        $year = date('Y');
        $isbn = $publication->isbn ? '<br>ISBN: ' . htmlspecialchars($publication->isbn) : '';
        $colophon = $publication->colophon_extra ? '<br>' . htmlspecialchars($publication->colophon_extra) : '';
        $dedicationHtml = '';
        if ($publication->dedication) {
            $dedication = htmlspecialchars($publication->dedication);
            $dedicationHtml = "<div class=\"front-matter dedication\"><p>{$dedication}</p></div>";
        }

        return <<<HTML
<div class="front-matter title-page">
    <h1>{$title}</h1>{$subtitle}
    <p class="author">{$author}</p>
    <p class="publisher">Indiemoon Publishing</p>
</div>
<div class="front-matter copyright-page">
    <p>&copy; {$year} {$author}{$isbn}{$colophon}</p>
    <p>Satt med Indiemoon Publishing Pipeline</p>
</div>
{$dedicationHtml}
HTML;
    }

    private function getThemeCss(string $theme): string
    {
        return match ($theme) {
            'modern' => 'body { font-family: "DejaVu Sans", "Helvetica", sans-serif; font-size: 10.5pt; } h1 { font-weight: 300; letter-spacing: 0.05em; text-transform: uppercase; }',
            'crime' => 'h1 { text-transform: uppercase; letter-spacing: 0.1em; border-bottom: 2pt solid #000; padding-bottom: 8pt; }',
            'children' => 'body { font-size: 13pt; line-height: 1.7; } h1 { color: #862736; font-size: 24pt; }',
            'nonfiction' => 'body { font-size: 10.5pt; } h1 { font-size: 18pt; } h2 { font-size: 14pt; border-bottom: 1px solid #ccc; padding-bottom: 4pt; }',
            default => '',
        };
    }
}
