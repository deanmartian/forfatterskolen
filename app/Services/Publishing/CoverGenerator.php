<?php

namespace App\Services\Publishing;

use App\Models\Publication;
use Dompdf\Dompdf;

class CoverGenerator
{
    public function __construct(
        private CoverDimensionCalculator $calculator,
    ) {}

    public function generate(Publication $publication, array $options = []): string
    {
        $trimSize = TrimSize::tryFrom($publication->trim_size) ?? TrimSize::FORMAT_140x220;
        $dims = $trimSize->dimensions();
        $binding = BindingType::tryFrom($publication->binding_type) ?? BindingType::PAPERBACK;
        $spineWidth = $publication->spine_width_mm ?? 10;

        $coverDims = $this->calculator->calculate(
            bookWidth: $dims['width'],
            bookHeight: $dims['height'],
            spineWidth: $spineWidth,
            binding: $binding,
        );

        $template = $options['template'] ?? 'classic';
        $backgroundColor = $options['backgroundColor'] ?? '#1a1a2e';
        $textColor = $options['textColor'] ?? '#ffffff';
        $blurb = $options['blurb'] ?? '';
        $coverImagePath = $options['coverImage'] ?? null;
        $showGuidelines = $options['preview'] ?? false;

        $html = $this->buildCoverHtml($publication, $coverDims, $template, $backgroundColor, $textColor, $blurb, $coverImagePath, $showGuidelines);

        $outputDir = storage_path("app/publications/{$publication->id}/covers");
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $pdfPath = "{$outputDir}/cover-print.pdf";
        $this->renderPdf($html, $pdfPath, $coverDims);

        // Also generate preview with guidelines
        $previewHtml = $this->buildCoverHtml($publication, $coverDims, $template, $backgroundColor, $textColor, $blurb, $coverImagePath, true);
        $previewPath = "{$outputDir}/cover-preview.pdf";
        $this->renderPdf($previewHtml, $previewPath, $coverDims);

        // Upload to Dropbox
        $dropboxDir = "Forfatterskolen_app/publications/{$publication->user_id}/{$publication->id}/covers";
        \Illuminate\Support\Facades\Storage::disk('dropbox')->put("{$dropboxDir}/cover-print.pdf", file_get_contents($pdfPath));
        \Illuminate\Support\Facades\Storage::disk('dropbox')->put("{$dropboxDir}/cover-preview.pdf", file_get_contents($previewPath));

        $publication->update([
            'cover_front' => "{$dropboxDir}/cover-print.pdf",
        ]);

        return $pdfPath;
    }

    public function generateTemplatePdf(Publication $publication): string
    {
        $trimSize = TrimSize::tryFrom($publication->trim_size) ?? TrimSize::FORMAT_140x220;
        $dims = $trimSize->dimensions();
        $binding = BindingType::tryFrom($publication->binding_type) ?? BindingType::PAPERBACK;
        $spineWidth = $publication->spine_width_mm ?? 10;

        $coverDims = $this->calculator->calculate(
            bookWidth: $dims['width'],
            bookHeight: $dims['height'],
            spineWidth: $spineWidth,
            binding: $binding,
        );

        $html = $this->buildTemplateHtml($coverDims);

        $outputDir = storage_path("app/publications/{$publication->id}/covers");
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $templatePath = "{$outputDir}/cover-template.pdf";
        $this->renderPdf($html, $templatePath, $coverDims);

        return $templatePath;
    }

    private function renderPdf(string $html, string $outputPath, CoverDimensions $dims): void
    {
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper([0, 0, $dims->totalWidthPt(), $dims->totalHeightPt()]);
        $dompdf->getOptions()->set('defaultFont', 'DejaVu Sans');
        $dompdf->getOptions()->set('isHtml5ParserEnabled', true);
        $dompdf->getOptions()->set('isRemoteEnabled', true);
        $dompdf->render();

        file_put_contents($outputPath, $dompdf->output());
    }

    private function buildCoverHtml(Publication $pub, CoverDimensions $dims, string $template, string $bgColor, string $textColor, string $blurb, ?string $coverImage, bool $showGuidelines): string
    {
        $title = htmlspecialchars($pub->title);
        $subtitle = $pub->subtitle ? htmlspecialchars($pub->subtitle) : '';
        $author = htmlspecialchars($pub->author_name);
        $isbn = $pub->isbn ? htmlspecialchars($pub->isbn) : '';
        $blurbHtml = htmlspecialchars($blurb);

        $edge = $dims->bleed ?: (($dims->ombukWidth ?? 0) + ($dims->formingWidth ?? 0));
        $contentHeight = $dims->totalHeight - 2 * $edge;

        $spineFontSize = $dims->spineWidth > 15 ? '9pt' : ($dims->spineWidth > 10 ? '7pt' : '6pt');

        $templateCss = $this->getTemplateCss($template, $textColor);
        $coverImageCss = $coverImage ? "background-image: url('{$coverImage}'); background-size: cover; background-position: center;" : '';

        $subtitleHtml = $subtitle ? "<h2 class='cover-subtitle'>{$subtitle}</h2>" : '';

        $guidelinesHtml = '';
        if ($showGuidelines) {
            $safe = $dims->frontSafeZone();
            $tolerance = $dims->spineToleranceMm();
            $guidelinesHtml = <<<HTML
<div style="position:absolute;left:{$dims->spineX}mm;top:0;width:{$dims->spineWidth}mm;height:100%;border-left:0.3mm dashed rgba(255,0,0,0.6);border-right:0.3mm dashed rgba(255,0,0,0.6);z-index:100;"></div>
<div style="position:absolute;left:{$edge}mm;top:{$edge}mm;right:{$edge}mm;bottom:{$edge}mm;border:0.3mm dashed rgba(0,150,255,0.6);z-index:100;"></div>
<div style="position:absolute;left:{$safe['x']}mm;top:{$safe['y']}mm;width:{$safe['width']}mm;height:{$safe['height']}mm;border:0.3mm dashed rgba(0,200,0,0.4);z-index:100;"></div>
<div style="position:absolute;left:{$dims->spineX}mm;top:1mm;font-size:5pt;color:rgba(255,0,0,0.7);font-family:sans-serif;z-index:101;">RYGG ({$dims->spineWidth}mm &plusmn;{$tolerance}mm)</div>
<div style="position:absolute;left:{$dims->frontX}mm;top:1mm;font-size:5pt;color:rgba(255,0,0,0.7);font-family:sans-serif;z-index:101;padding-left:3mm;">FORSIDE</div>
<div style="position:absolute;left:{$dims->backX}mm;top:1mm;font-size:5pt;color:rgba(255,0,0,0.7);font-family:sans-serif;z-index:101;padding-left:3mm;">BAKSIDE</div>
<div style="position:absolute;left:1mm;bottom:1mm;font-size:4pt;color:rgba(0,0,0,0.5);font-family:sans-serif;z-index:101;">Total: {$dims->totalWidth}&times;{$dims->totalHeight}mm | Bl&aring; = skj&aelig;rekant | R&oslash;d = rygg | Gr&oslash;nn = sikker sone</div>
HTML;
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { size: {$dims->totalWidth}mm {$dims->totalHeight}mm; margin: 0; }
* { margin: 0; padding: 0; box-sizing: border-box; }
body { width: {$dims->totalWidth}mm; height: {$dims->totalHeight}mm; position: relative; overflow: hidden; font-family: 'DejaVu Sans', sans-serif; }

.cover-bg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: {$bgColor}; }

.cover-front {
    position: absolute; left: {$dims->frontX}mm; top: {$edge}mm;
    width: {$dims->frontWidth}mm; height: {$contentHeight}mm;
    display: flex; flex-direction: column; justify-content: center; align-items: center;
    padding: 15mm; color: {$textColor}; {$coverImageCss}
}

.cover-spine {
    position: absolute; left: {$dims->spineX}mm; top: {$edge}mm;
    width: {$dims->spineWidth}mm; height: {$contentHeight}mm;
    display: flex; align-items: center; justify-content: center; color: {$textColor};
    overflow: hidden;
}

.cover-back {
    position: absolute; left: {$dims->backX}mm; top: {$edge}mm;
    width: {$dims->backWidth}mm; height: {$contentHeight}mm;
    padding: 15mm; color: {$textColor};
}

.spine-text {
    writing-mode: vertical-rl; transform: rotate(180deg);
    font-size: {$spineFontSize}; white-space: nowrap; letter-spacing: 0.05em;
}

{$templateCss}
</style>
</head>
<body>
<div class="cover-bg"></div>

<div class="cover-back">
    <p style="font-size:10pt;line-height:1.6;margin-top:20mm;">{$blurbHtml}</p>
</div>

<div class="cover-spine">
    <div class="spine-text">
        <span>{$title}</span>
        <span style="margin-left:3mm;font-size:0.85em;">{$author}</span>
    </div>
</div>

<div class="cover-front">
    <h1 class="cover-title">{$title}</h1>
    {$subtitleHtml}
    <p class="cover-author">{$author}</p>
</div>

{$guidelinesHtml}
</body>
</html>
HTML;
    }

    private function buildTemplateHtml(CoverDimensions $dims): string
    {
        $edge = $dims->bleed ?: (($dims->ombukWidth ?? 0) + ($dims->formingWidth ?? 0));
        $safe = $dims->frontSafeZone();
        $tolerance = $dims->spineToleranceMm();

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { size: {$dims->totalWidth}mm {$dims->totalHeight}mm; margin: 0; }
* { margin: 0; padding: 0; box-sizing: border-box; }
body { width: {$dims->totalWidth}mm; height: {$dims->totalHeight}mm; position: relative; font-family: sans-serif; background: #fff; }
</style>
</head>
<body>
<!-- Bleed/edge -->
<div style="position:absolute;left:{$edge}mm;top:{$edge}mm;right:{$edge}mm;bottom:{$edge}mm;border:0.5mm solid #0096ff;"></div>
<!-- Spine -->
<div style="position:absolute;left:{$dims->spineX}mm;top:0;width:{$dims->spineWidth}mm;height:100%;border-left:0.5mm solid #ff0000;border-right:0.5mm solid #ff0000;background:rgba(255,0,0,0.05);"></div>
<!-- Safe zone forside -->
<div style="position:absolute;left:{$safe['x']}mm;top:{$safe['y']}mm;width:{$safe['width']}mm;height:{$safe['height']}mm;border:0.3mm dashed #00cc00;"></div>
<!-- Labels -->
<div style="position:absolute;left:{$dims->spineX}mm;top:3mm;width:{$dims->spineWidth}mm;text-align:center;font-size:6pt;color:#ff0000;">RYGG<br>{$dims->spineWidth}mm<br>&plusmn;{$tolerance}mm</div>
<div style="position:absolute;left:{$dims->frontX}mm;top:3mm;font-size:8pt;color:#333;padding-left:5mm;">FORSIDE<br><span style="font-size:6pt;color:#888;">{$dims->frontWidth} &times; {$dims->totalHeight}mm</span></div>
<div style="position:absolute;left:{$dims->backX}mm;top:3mm;font-size:8pt;color:#333;padding-left:5mm;">BAKSIDE<br><span style="font-size:6pt;color:#888;">{$dims->backWidth} &times; {$dims->totalHeight}mm</span></div>
<div style="position:absolute;left:2mm;bottom:2mm;font-size:5pt;color:#666;">Total: {$dims->totalWidth} &times; {$dims->totalHeight}mm | Bl&aring; = skj&aelig;rekant ({$dims->bleed}mm) | R&oslash;d = rygg | Gr&oslash;nn = sikker sone (5mm)</div>
</body>
</html>
HTML;
    }

    private function getTemplateCss(string $template, string $textColor): string
    {
        return match ($template) {
            'modern' => <<<CSS
.cover-title { font-size: 28pt; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 300; text-align: center; margin-bottom: 5mm; }
.cover-subtitle { font-size: 13pt; font-weight: 300; text-align: center; margin-bottom: 10mm; opacity: 0.8; }
.cover-author { font-size: 11pt; margin-top: auto; letter-spacing: 0.15em; text-transform: uppercase; }
CSS,
            'bold' => <<<CSS
.cover-title { font-size: 36pt; font-weight: 900; text-align: center; margin-bottom: 5mm; line-height: 1.1; }
.cover-subtitle { font-size: 14pt; text-align: center; margin-bottom: 10mm; }
.cover-author { font-size: 14pt; margin-top: auto; font-weight: 700; }
CSS,
            'image-full' => <<<CSS
.cover-front { justify-content: flex-end; padding-bottom: 25mm; }
.cover-title { font-size: 26pt; text-align: center; margin-bottom: 3mm; text-shadow: 0 2px 8px rgba(0,0,0,0.5); }
.cover-subtitle { font-size: 12pt; text-align: center; margin-bottom: 8mm; text-shadow: 0 1px 4px rgba(0,0,0,0.5); }
.cover-author { font-size: 11pt; text-shadow: 0 1px 4px rgba(0,0,0,0.5); }
CSS,
            default => <<<CSS
.cover-title { font-family: 'DejaVu Serif', Georgia, serif; font-size: 24pt; text-align: center; margin-bottom: 5mm; }
.cover-subtitle { font-family: 'DejaVu Serif', Georgia, serif; font-size: 14pt; font-weight: normal; text-align: center; margin-bottom: 10mm; }
.cover-author { font-size: 12pt; margin-top: auto; }
CSS,
        };
    }
}
