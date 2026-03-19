<?php

namespace App\Services;

use Dompdf\Dompdf;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class DocxToPdfService
{
    /**
     * Convert a .docx file to PDF with Word comments rendered inline.
     *
     * @param  string  $docxPath  Absolute path to the .docx file
     * @param  string  $downloadName  Filename for the PDF download (without extension)
     * @return \Illuminate\Http\Response|null
     */
    public function convertWithComments(string $docxPath, string $downloadName = 'manuscript')
    {
        if (! file_exists($docxPath)) {
            Log::error('DocxToPdfService: file not found', ['path' => $docxPath]);

            return null;
        }

        $extension = strtolower(pathinfo($docxPath, PATHINFO_EXTENSION));
        if ($extension !== 'docx') {
            Log::error('DocxToPdfService: not a .docx file', ['path' => $docxPath]);

            return null;
        }

        try {
            $parsed = $this->parseDocx($docxPath);
            $html = $this->buildHtml($parsed['blocks'], $downloadName);

            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('a4');
            $dompdf->getOptions()->set('defaultFont', 'DejaVu Sans');
            $dompdf->render();

            $safeName = preg_replace('/[^a-zA-Z0-9_\-æøåÆØÅ ]/', '', $downloadName);
            if (empty($safeName)) {
                $safeName = 'manuscript';
            }

            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $safeName . '.pdf"',
            ]);
        } catch (\Throwable $e) {
            Log::error('DocxToPdfService: conversion failed', [
                'path' => $docxPath,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Parse .docx XML to extract paragraphs with inline comment markers.
     */
    protected function parseDocx(string $docxPath): array
    {
        $zip = new ZipArchive;
        if ($zip->open($docxPath) !== true) {
            throw new \RuntimeException("Cannot open docx: {$docxPath}");
        }

        // Parse comments.xml
        $comments = [];
        $commentsXml = $zip->getFromName('word/comments.xml');
        if ($commentsXml !== false) {
            $comments = $this->parseCommentsXml($commentsXml);
        }

        // Parse document.xml
        $documentXml = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($documentXml === false) {
            throw new \RuntimeException("Cannot read word/document.xml from: {$docxPath}");
        }

        $blocks = $this->parseDocumentXml($documentXml, $comments);

        return ['blocks' => $blocks, 'comments' => $comments];
    }

    /**
     * Parse word/comments.xml and return an array keyed by comment ID.
     */
    protected function parseCommentsXml(string $xml): array
    {
        $dom = new \DOMDocument;
        $dom->loadXML($xml);

        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        $comments = [];
        $commentNodes = $xpath->query('//w:comment');

        foreach ($commentNodes as $commentNode) {
            $id = $commentNode->getAttribute('w:id');
            $author = $commentNode->getAttribute('w:author') ?: 'Ukjent';
            $date = $commentNode->getAttribute('w:date') ?: '';

            // Extract all text from <w:t> inside the comment
            $textNodes = $xpath->query('.//w:t', $commentNode);
            $text = '';
            foreach ($textNodes as $tn) {
                $text .= $tn->textContent;
            }

            $comments[$id] = [
                'id' => $id,
                'author' => $author,
                'date' => $date ? date('d.m.Y H:i', strtotime($date)) : '',
                'text' => trim($text),
            ];
        }

        return $comments;
    }

    /**
     * Parse word/document.xml, tracking commentRangeStart/End to associate
     * text runs with comments. Returns an array of paragraph blocks.
     */
    protected function parseDocumentXml(string $xml, array $comments): array
    {
        $dom = new \DOMDocument;
        $dom->loadXML($xml);

        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        $paragraphs = $xpath->query('//w:body/w:p');
        $blocks = [];

        // Track which comment IDs are currently "open" (between rangeStart and rangeEnd)
        $openComments = [];

        foreach ($paragraphs as $para) {
            $segments = [];
            $isBold = false;
            $isItalic = false;

            foreach ($para->childNodes as $child) {
                $localName = $child->localName;

                // Comment range start
                if ($localName === 'commentRangeStart') {
                    $cid = $child->getAttribute('w:id');
                    if (isset($comments[$cid])) {
                        $openComments[$cid] = true;
                    }
                }

                // Comment range end
                if ($localName === 'commentRangeEnd') {
                    $cid = $child->getAttribute('w:id');
                    unset($openComments[$cid]);

                    // Insert the comment inline after the commented range ends
                    if (isset($comments[$cid])) {
                        $c = $comments[$cid];
                        $segments[] = [
                            'type' => 'comment',
                            'author' => $c['author'],
                            'date' => $c['date'],
                            'text' => $c['text'],
                        ];
                    }
                }

                // Run element (w:r) containing text
                if ($localName === 'r') {
                    $textNodes = $xpath->query('w:t', $child);
                    $runText = '';
                    foreach ($textNodes as $tn) {
                        $runText .= $tn->textContent;
                    }

                    if ($runText === '') {
                        continue;
                    }

                    // Check formatting
                    $rPr = $xpath->query('w:rPr', $child)->item(0);
                    $bold = false;
                    $italic = false;
                    if ($rPr) {
                        $bold = $xpath->query('w:b', $rPr)->length > 0;
                        $italic = $xpath->query('w:i', $rPr)->length > 0;
                    }

                    $isCommented = ! empty($openComments);

                    $segments[] = [
                        'type' => 'text',
                        'text' => $runText,
                        'bold' => $bold,
                        'italic' => $italic,
                        'commented' => $isCommented,
                    ];
                }
            }

            $blocks[] = ['segments' => $segments];
        }

        return $blocks;
    }

    /**
     * Build the full HTML document from parsed blocks.
     */
    protected function buildHtml(array $blocks, string $title): string
    {
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8">';
        $html .= '<title>'.htmlspecialchars($title).'</title>';
        $html .= '<style>'."\n".$this->getCss()."\n".'</style>';
        $html .= '</head><body>';
        $html .= '<div class="document">';

        foreach ($blocks as $block) {
            if (empty($block['segments'])) {
                $html .= '<p class="empty-para">&nbsp;</p>';

                continue;
            }

            $html .= '<p>';
            foreach ($block['segments'] as $seg) {
                if ($seg['type'] === 'text') {
                    $text = htmlspecialchars($seg['text']);
                    if ($seg['commented']) {
                        $text = '<span class="commented">'.$text.'</span>';
                    }
                    if ($seg['bold']) {
                        $text = '<strong>'.$text.'</strong>';
                    }
                    if ($seg['italic']) {
                        $text = '<em>'.$text.'</em>';
                    }
                    $html .= $text;
                } elseif ($seg['type'] === 'comment') {
                    $author = htmlspecialchars($seg['author']);
                    $date = htmlspecialchars($seg['date']);
                    $commentText = htmlspecialchars($seg['text']);
                    $html .= '<span class="comment-bubble">';
                    $html .= '<span class="comment-author">'.$author.'</span>';
                    if ($date) {
                        $html .= ' <span class="comment-date">('.$date.')</span>';
                    }
                    $html .= ': '.$commentText;
                    $html .= '</span>';
                }
            }
            $html .= '</p>';
        }

        $html .= '</div>';
        $html .= '</body></html>';

        return $html;
    }

    /**
     * CSS styles for the PDF output.
     */
    protected function getCss(): string
    {
        return <<<'CSS'
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: "DejaVu Sans", sans-serif;
    font-size: 11pt;
    line-height: 1.6;
    color: #1a1a1a;
    padding: 20px 30px;
}
.document {
    max-width: 100%;
}
p {
    margin-bottom: 8px;
    text-align: left;
}
p.empty-para {
    margin-bottom: 4px;
}
.commented {
    background-color: #fff9c4;
    border-bottom: 2px solid #f9a825;
    padding: 0 1px;
}
.comment-bubble {
    display: inline;
    background-color: #fff3e0;
    border: 1px solid #ffb74d;
    border-radius: 3px;
    padding: 2px 6px;
    margin-left: 4px;
    font-size: 9pt;
    color: #e65100;
    line-height: 1.4;
}
.comment-author {
    font-weight: bold;
    color: #bf360c;
}
.comment-date {
    color: #8d6e63;
    font-size: 8pt;
}
CSS;
    }
}
