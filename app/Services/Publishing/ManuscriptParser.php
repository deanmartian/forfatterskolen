<?php

namespace App\Services\Publishing;

use ZipArchive;

class ManuscriptParser
{
    public function parse(string $docxPath): ParsedManuscript
    {
        if (!file_exists($docxPath)) {
            throw new \RuntimeException("File not found: {$docxPath}");
        }

        $zip = new ZipArchive;
        if ($zip->open($docxPath) !== true) {
            throw new \RuntimeException("Cannot open docx: {$docxPath}");
        }

        $documentXml = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($documentXml === false) {
            throw new \RuntimeException("Cannot read word/document.xml");
        }

        $paragraphs = $this->parseParagraphs($documentXml);
        $chapters = $this->extractChapters($paragraphs);
        $fullHtml = $this->buildFullHtml($paragraphs);
        $cleanHtml = NorwegianTypography::apply($fullHtml);
        $wordCount = str_word_count(strip_tags($cleanHtml));

        return new ParsedManuscript(
            html: $cleanHtml,
            chapters: $chapters,
            images: [],
            wordCount: $wordCount,
            estimatedPages: (int) ceil($wordCount / 250),
        );
    }

    private function parseParagraphs(string $xml): array
    {
        $dom = new \DOMDocument;
        $dom->loadXML($xml);
        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        $paragraphs = [];

        foreach ($xpath->query('//w:body/w:p') as $para) {
            $style = $this->getParagraphStyle($xpath, $para);
            $runs = $this->extractRuns($xpath, $para);
            $text = implode('', array_column($runs, 'text'));

            $paragraphs[] = [
                'style' => $style,
                'runs' => $runs,
                'text' => $text,
            ];
        }

        return $paragraphs;
    }

    private function getParagraphStyle(\DOMXPath $xpath, \DOMNode $para): ?string
    {
        $pPr = $xpath->query('w:pPr', $para)->item(0);
        if (!$pPr) return null;

        $pStyle = $xpath->query('w:pStyle', $pPr)->item(0);
        if (!$pStyle) return null;

        return $pStyle->getAttribute('w:val');
    }

    private function extractRuns(\DOMXPath $xpath, \DOMNode $para): array
    {
        $runs = [];
        foreach ($xpath->query('w:r', $para) as $run) {
            $textNodes = $xpath->query('w:t', $run);
            $text = '';
            foreach ($textNodes as $tn) {
                $text .= $tn->textContent;
            }
            if ($text === '') continue;

            $rPr = $xpath->query('w:rPr', $run)->item(0);
            $bold = $rPr && $xpath->query('w:b', $rPr)->length > 0;
            $italic = $rPr && $xpath->query('w:i', $rPr)->length > 0;
            $underline = $rPr && $xpath->query('w:u', $rPr)->length > 0;

            $runs[] = [
                'text' => $text,
                'bold' => $bold,
                'italic' => $italic,
                'underline' => $underline,
            ];
        }
        return $runs;
    }

    private function extractChapters(array $paragraphs): array
    {
        $chapters = [];
        $currentChapter = null;
        $chapterNumber = 0;

        foreach ($paragraphs as $para) {
            $isHeading1 = in_array($para['style'], ['Heading1', 'Overskrift1', 'heading1', 'Title']);

            if ($isHeading1 && trim($para['text']) !== '') {
                if ($currentChapter !== null) {
                    $chapters[] = $currentChapter;
                }
                $chapterNumber++;
                $currentChapter = [
                    'number' => $chapterNumber,
                    'title' => trim($para['text']),
                    'paragraphs' => [],
                ];
            } elseif ($currentChapter !== null) {
                $currentChapter['paragraphs'][] = $para;
            }
        }

        if ($currentChapter !== null) {
            $chapters[] = $currentChapter;
        }

        // If no headings found, treat entire document as one chapter
        if (empty($chapters)) {
            $chapters[] = [
                'number' => 1,
                'title' => '',
                'paragraphs' => $paragraphs,
            ];
        }

        // Build HTML for each chapter
        foreach ($chapters as &$chapter) {
            $chapter['html'] = $this->buildChapterHtml($chapter['paragraphs']);
        }

        return $chapters;
    }

    private function buildChapterHtml(array $paragraphs): string
    {
        $html = '';
        foreach ($paragraphs as $para) {
            if (empty($para['runs'])) {
                continue;
            }

            $tag = $this->getHtmlTag($para['style']);
            $content = $this->runsToHtml($para['runs']);
            $html .= "<{$tag}>{$content}</{$tag}>\n";
        }
        return NorwegianTypography::apply($html);
    }

    private function buildFullHtml(array $paragraphs): string
    {
        $html = '';
        foreach ($paragraphs as $para) {
            if (empty($para['runs'])) continue;
            $tag = $this->getHtmlTag($para['style']);
            $content = $this->runsToHtml($para['runs']);
            $html .= "<{$tag}>{$content}</{$tag}>\n";
        }
        return $html;
    }

    private function getHtmlTag(?string $style): string
    {
        return match ($style) {
            'Heading1', 'Overskrift1', 'heading1', 'Title' => 'h1',
            'Heading2', 'Overskrift2', 'heading2', 'Subtitle' => 'h2',
            'Heading3', 'Overskrift3', 'heading3' => 'h3',
            'Heading4', 'Overskrift4', 'heading4' => 'h4',
            default => 'p',
        };
    }

    private function runsToHtml(array $runs): string
    {
        $html = '';
        foreach ($runs as $run) {
            $text = htmlspecialchars($run['text']);
            if ($run['bold']) $text = "<strong>{$text}</strong>";
            if ($run['italic']) $text = "<em>{$text}</em>";
            if ($run['underline']) $text = "<u>{$text}</u>";
            $html .= $text;
        }
        return $html;
    }
}
