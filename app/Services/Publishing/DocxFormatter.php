<?php

namespace App\Services\Publishing;

use App\Models\Publication;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\Style\Font;

class DocxFormatter
{
    public function format(ParsedManuscript $manuscript, Publication $publication, string $outputPath): void
    {
        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $fonts = $this->getThemeFonts($publication->theme ?? 'classic');

        $phpWord = new PhpWord();
        $phpWord->getSettings()->setThemeFontLang(new \PhpOffice\PhpWord\Style\Language('nb-NO'));

        // Default styles
        $phpWord->setDefaultFontName($fonts['body']);
        $phpWord->setDefaultFontSize(11);
        $phpWord->setDefaultParagraphStyle([
            'spaceAfter' => 0,
            'lineHeight' => 1.3,
            'indentation' => ['firstLine' => 360],
        ]);

        // Named paragraph styles (maps to InDesign paragraph styles)
        $phpWord->addParagraphStyle('Brodtekst', [
            'spaceAfter' => 0, 'lineHeight' => 1.3,
            'indentation' => ['firstLine' => 360],
        ]);
        $phpWord->addParagraphStyle('Brodtekst uten innrykk', [
            'spaceAfter' => 0, 'lineHeight' => 1.3,
        ]);
        $phpWord->addParagraphStyle('Kapitteloverskrift', [
            'alignment' => 'center', 'spaceBefore' => 720, 'spaceAfter' => 360,
            'keepNext' => true,
        ]);
        $phpWord->addParagraphStyle('Kapittelnummer', [
            'alignment' => 'center', 'spaceBefore' => 1200, 'spaceAfter' => 120,
            'keepNext' => true,
        ]);
        $phpWord->addParagraphStyle('Sceneskift', [
            'alignment' => 'center', 'spaceBefore' => 240, 'spaceAfter' => 240,
        ]);
        $phpWord->addParagraphStyle('Sitat', [
            'indentation' => ['left' => 400], 'spaceAfter' => 120,
        ]);
        $phpWord->addParagraphStyle('Kapittelmeta', [
            'alignment' => 'center', 'spaceAfter' => 240,
        ]);
        $phpWord->addParagraphStyle('Dialog', [
            'spaceAfter' => 0, 'lineHeight' => 1.3,
        ]);
        $phpWord->addParagraphStyle('Halvtittel', [
            'alignment' => 'center', 'spaceBefore' => 4000,
        ]);
        $phpWord->addParagraphStyle('Tittel', [
            'alignment' => 'center', 'spaceBefore' => 2800,
        ]);
        $phpWord->addParagraphStyle('Forfatter', [
            'alignment' => 'center', 'spaceBefore' => 600,
        ]);
        $phpWord->addParagraphStyle('Forlag', [
            'alignment' => 'center', 'spaceBefore' => 2000,
        ]);
        $phpWord->addParagraphStyle('Kolofon', [
            'spaceAfter' => 60,
        ]);

        // Heading styles for TOC
        $phpWord->addTitleStyle(1, ['size' => 20, 'bold' => true, 'name' => $fonts['heading']], ['alignment' => 'center', 'spaceBefore' => 720, 'spaceAfter' => 360]);

        // === HALVTITTEL ===
        $section = $phpWord->addSection();
        $section->addText($publication->title, ['size' => 16, 'bold' => true, 'name' => $fonts['heading']], 'Halvtittel');

        // === TITTELSIDE ===
        $section = $phpWord->addSection();
        $section->addText(strtoupper($publication->author_name), ['size' => 10, 'name' => $fonts['heading'], 'allCaps' => true, 'spacing' => 150], 'Forfatter');
        $section->addText($publication->title, ['size' => 28, 'bold' => true, 'name' => $fonts['heading']], 'Tittel');
        if ($publication->subtitle) {
            $section->addText($publication->subtitle, ['size' => 14, 'italic' => true, 'name' => $fonts['body']], 'Halvtittel');
        }
        $section->addText(strtoupper($publication->publisher ?? 'Indiemoon'), ['size' => 8, 'name' => $fonts['heading'], 'allCaps' => true, 'spacing' => 200, 'color' => '999999'], 'Forlag');

        // === KOLOFON ===
        $section = $phpWord->addSection();
        $section->addText("\xC2\xA9 " . date('Y') . ' ' . $publication->author_name, ['size' => 7, 'color' => '666666'], 'Kolofon');
        $section->addText('Utgitt av ' . ($publication->publisher ?? 'Indiemoon'), ['size' => 7, 'color' => '666666'], 'Kolofon');
        if ($publication->isbn) {
            $section->addText('ISBN ' . $publication->isbn, ['size' => 7, 'color' => '666666'], 'Kolofon');
        }
        $section->addText('Sats og layout: Indiemoon', ['size' => 7, 'color' => '666666'], 'Kolofon');
        $section->addText('Trykk: ScandinavianBook', ['size' => 7, 'color' => '666666'], 'Kolofon');

        // === KAPITLER ===
        foreach ($manuscript->chapters as $chapter) {
            $section = $phpWord->addSection();

            // Kapittelnummer
            $section->addText((string) $chapter['number'], ['size' => 48, 'name' => $fonts['heading'], 'color' => 'DDDDDD'], 'Kapittelnummer');

            // Kapitteltittel
            if ($chapter['title']) {
                $section->addTitle($chapter['title'], 1);
            }

            // Brødtekst fra HTML
            $this->addChapterContent($section, $chapter['html'], $fonts);
        }

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($outputPath);
    }

    private function addChapterContent($section, string $html, array $fonts): void
    {
        $html = str_replace(['<br>', '<br/>', '<br />'], "\n", $html);
        preg_match_all('/<(p|blockquote|div)[^>]*>(.*?)<\/\1>/s', $html, $matches);

        $isFirstPara = true;

        foreach ($matches[2] as $i => $content) {
            $tag = $matches[1][$i];
            $fullTag = $matches[0][$i];
            $content = trim($content);
            if (empty($content)) continue;

            $plainText = html_entity_decode(strip_tags($content), ENT_QUOTES, 'UTF-8');

            // Scene break
            if (preg_match('/^\s*[\*]{3}|^\s*\x{2042}|^\s*\x{25A0}/u', $plainText)) {
                $section->addText('* * *', ['size' => 11], 'Sceneskift');
                $isFirstPara = true;
                continue;
            }

            // Chapter meta
            if (str_contains($fullTag, 'chapter-meta')) {
                $section->addText($plainText, ['size' => 10, 'bold' => true, 'name' => $fonts['heading']], 'Kapittelmeta');
                continue;
            }

            // Blockquote
            if ($tag === 'blockquote') {
                $section->addText($plainText, ['size' => 10.5, 'italic' => true, 'color' => '555555'], 'Sitat');
                $isFirstPara = true;
                continue;
            }

            // Dialog
            if (preg_match('/^\s*[\x{2013}\x{2014}\x{2012}–—-]/u', $plainText)) {
                $this->addRichText($section, $content, $fonts, 'Dialog');
                $isFirstPara = false;
                continue;
            }

            // Regular paragraph
            $style = $isFirstPara ? 'Brodtekst uten innrykk' : 'Brodtekst';
            $this->addRichText($section, $content, $fonts, $style);
            $isFirstPara = false;
        }
    }

    private function addRichText($section, string $html, array $fonts, string $paraStyle): void
    {
        $html = trim(strip_tags($html, '<em><i><strong><b>'));
        $parts = preg_split('/(<\/?(?:em|i|strong|b)>)/', $html, -1, PREG_SPLIT_DELIM_CAPTURE);

        $textRun = $section->addTextRun($paraStyle);
        $isBold = false;
        $isItalic = false;

        foreach ($parts as $part) {
            if ($part === '<em>' || $part === '<i>') { $isItalic = true; continue; }
            if ($part === '</em>' || $part === '</i>') { $isItalic = false; continue; }
            if ($part === '<strong>' || $part === '<b>') { $isBold = true; continue; }
            if ($part === '</strong>' || $part === '</b>') { $isBold = false; continue; }

            $text = html_entity_decode(strip_tags($part), ENT_QUOTES, 'UTF-8');
            if (empty(trim($text))) continue;

            $fontStyle = ['size' => 11, 'name' => $fonts['body']];
            if ($isBold) $fontStyle['bold'] = true;
            if ($isItalic) $fontStyle['italic'] = true;

            $textRun->addText($text, $fontStyle);
        }
    }

    private function getThemeFonts(string $theme): array
    {
        return match ($theme) {
            'crime' => ['body' => 'Libre Baskerville', 'heading' => 'Oswald'],
            'modern' => ['body' => 'Source Serif 4', 'heading' => 'Inter'],
            'children' => ['body' => 'Literata', 'heading' => 'Nunito'],
            'nonfiction' => ['body' => 'Merriweather', 'heading' => 'Source Sans 3'],
            default => ['body' => 'Crimson Text', 'heading' => 'Cormorant Garamond'],
        };
    }
}
