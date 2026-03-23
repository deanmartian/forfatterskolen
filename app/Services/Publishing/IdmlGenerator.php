<?php

namespace App\Services\Publishing;

use App\Models\Publication;
use DOMDocument;
use DOMXPath;
use ZipArchive;

/**
 * Template-based IDML generator.
 * Uses a real InDesign IDML template and injects manuscript content.
 */
class IdmlGenerator
{
    private string $workDir;
    private int $nextId = 500;

    public function generate(ParsedManuscript $manuscript, Publication $publication, string $outputPath): void
    {
        $templatePath = $this->getTemplatePath($publication);

        $this->workDir = sys_get_temp_dir() . '/idml_' . uniqid();
        mkdir($this->workDir, 0755, true);

        // 1. Unpack template
        $zip = new ZipArchive();
        $zip->open($templatePath);
        $zip->extractTo($this->workDir);
        $zip->close();

        // 2. Add our paragraph + character styles
        $this->injectStyles($publication);

        // 3. Generate stories for each chapter + front matter
        $storyFiles = $this->generateStories($manuscript, $publication);

        // 4. Update designmap with new stories
        $this->updateDesignMap($storyFiles);

        // 5. Pack back to IDML
        $this->packIdml($outputPath);

        // 6. Cleanup
        $this->removeDir($this->workDir);
    }

    private function getTemplatePath(Publication $publication): string
    {
        $theme = $publication->theme ?? 'crime';
        $trimSize = $publication->trim_size ?? '148x210';

        // Try theme-specific template first, fallback to crime
        $path = resource_path("idml-templates/{$theme}/{$trimSize}.idml");
        if (!file_exists($path)) {
            $path = resource_path("idml-templates/crime/148x210.idml");
        }
        if (!file_exists($path)) {
            throw new \RuntimeException("IDML template not found: {$path}");
        }
        return $path;
    }

    private function injectStyles(Publication $publication): void
    {
        $stylesPath = "{$this->workDir}/Resources/Styles.xml";
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        $doc->load($stylesPath);

        $xpath = new DOMXPath($doc);

        // Find RootParagraphStyleGroup
        $rootPSG = $doc->getElementsByTagName('RootParagraphStyleGroup')->item(0);
        if (!$rootPSG) {
            // Fallback: find parent of first ParagraphStyle
            $firstPS = $doc->getElementsByTagName('ParagraphStyle')->item(0);
            $rootPSG = $firstPS ? $firstPS->parentNode : $doc->documentElement;
        }

        $fonts = $this->getThemeFonts($publication->theme ?? 'classic');

        // Add our paragraph styles
        $styles = [
            ['name' => 'Brodtekst', 'font' => $fonts['body'], 'fstyle' => 'Regular', 'size' => 11, 'leading' => 14.5, 'just' => 'LeftJustified', 'indent' => 12.76, 'hyph' => 'true'],
            ['name' => 'Brodtekst uten innrykk', 'font' => $fonts['body'], 'fstyle' => 'Regular', 'size' => 11, 'leading' => 14.5, 'just' => 'LeftJustified', 'indent' => 0, 'hyph' => 'true'],
            ['name' => 'Kapitteloverskrift', 'font' => $fonts['heading'], 'fstyle' => 'Bold', 'size' => 20, 'leading' => 24, 'just' => 'CenterAlign', 'indent' => 0, 'caps' => 'AllCaps'],
            ['name' => 'Kapittelnummer', 'font' => $fonts['heading'], 'fstyle' => 'Light', 'size' => 48, 'leading' => 48, 'just' => 'CenterAlign', 'indent' => 0],
            ['name' => 'Halvtittel', 'font' => $fonts['heading'], 'fstyle' => 'Bold', 'size' => 16, 'leading' => 20, 'just' => 'CenterAlign', 'indent' => 0],
            ['name' => 'Tittel', 'font' => $fonts['heading'], 'fstyle' => 'Bold', 'size' => 28, 'leading' => 33.6, 'just' => 'CenterAlign', 'indent' => 0, 'caps' => 'AllCaps'],
            ['name' => 'Forfatter', 'font' => $fonts['heading'], 'fstyle' => 'Light', 'size' => 10, 'leading' => 12, 'just' => 'CenterAlign', 'indent' => 0, 'caps' => 'AllCaps', 'tracking' => 150],
            ['name' => 'Forlag', 'font' => $fonts['heading'], 'fstyle' => 'Light', 'size' => 8, 'leading' => 10, 'just' => 'CenterAlign', 'indent' => 0, 'caps' => 'AllCaps', 'tracking' => 200],
            ['name' => 'Kolofon', 'font' => $fonts['body'], 'fstyle' => 'Regular', 'size' => 7, 'leading' => 11, 'just' => 'LeftJustified', 'indent' => 0],
            ['name' => 'Sceneskift', 'font' => $fonts['body'], 'fstyle' => 'Regular', 'size' => 11, 'leading' => 14.5, 'just' => 'CenterAlign', 'indent' => 0],
            ['name' => 'Sitat', 'font' => $fonts['body'], 'fstyle' => 'Italic', 'size' => 10.5, 'leading' => 14, 'just' => 'LeftJustified', 'indent' => 0, 'leftIndent' => 14.17],
            ['name' => 'Kapittelmeta', 'font' => $fonts['heading'], 'fstyle' => 'SemiBold', 'size' => 10, 'leading' => 13, 'just' => 'CenterAlign', 'indent' => 0],
            ['name' => 'Dialog', 'font' => $fonts['body'], 'fstyle' => 'Regular', 'size' => 11, 'leading' => 14.5, 'just' => 'LeftJustified', 'indent' => 0],
        ];

        foreach ($styles as $s) {
            $ps = $doc->createElement('ParagraphStyle');
            $ps->setAttribute('Self', "ParagraphStyle/{$s['name']}");
            $ps->setAttribute('Name', $s['name']);
            $ps->setAttribute('AppliedFont', $s['font']);
            $ps->setAttribute('FontStyle', $s['fstyle']);
            $ps->setAttribute('PointSize', (string) $s['size']);
            $ps->setAttribute('Leading', (string) $s['leading']);
            $ps->setAttribute('Justification', $s['just']);
            $ps->setAttribute('FirstLineIndent', (string) $s['indent']);
            if (!empty($s['hyph'])) $ps->setAttribute('Hyphenation', $s['hyph']);
            if (!empty($s['caps'])) $ps->setAttribute('Capitalization', $s['caps']);
            if (!empty($s['tracking'])) $ps->setAttribute('Tracking', (string) $s['tracking']);
            if (!empty($s['leftIndent'])) $ps->setAttribute('LeftIndent', (string) $s['leftIndent']);
            $rootPSG->appendChild($ps);
        }

        // Add character styles
        $rootCSG = $doc->getElementsByTagName('RootCharacterStyleGroup')->item(0);
        if ($rootCSG) {
            foreach (['Kursiv' => 'Italic', 'Fet' => 'Bold', 'Fet kursiv' => 'Bold Italic'] as $name => $fontStyle) {
                $cs = $doc->createElement('CharacterStyle');
                $cs->setAttribute('Self', "CharacterStyle/{$name}");
                $cs->setAttribute('Name', $name);
                $cs->setAttribute('FontStyle', $fontStyle);
                $rootCSG->appendChild($cs);
            }
        }

        $doc->save($stylesPath);
    }

    private function generateStories(ParsedManuscript $manuscript, Publication $publication): array
    {
        $storiesDir = "{$this->workDir}/Stories";
        if (!is_dir($storiesDir)) {
            mkdir($storiesDir, 0755, true);
        }

        $storyFiles = [];

        // Story: Front matter (halvtittel + tittelside)
        $id = $this->nextId++;
        $paras = [
            $this->makePara('Halvtittel', $publication->title),
            $this->makePara('Forfatter', strtoupper($publication->author_name)),
            $this->makePara('Tittel', $publication->title),
            $this->makePara('Forfatter', strtoupper($publication->author_name)),
            $this->makePara('Forlag', strtoupper($publication->publisher ?? 'Indiemoon')),
        ];
        $this->writeStory($id, $paras);
        $storyFiles[] = "Stories/Story_u{$id}.xml";

        // Story: Colophon
        $id = $this->nextId++;
        $paras = [
            $this->makePara('Kolofon', "\xC2\xA9 " . date('Y') . ' ' . $publication->author_name),
            $this->makePara('Kolofon', 'Utgitt av ' . ($publication->publisher ?? 'Indiemoon')),
        ];
        if ($publication->isbn) {
            $paras[] = $this->makePara('Kolofon', 'ISBN ' . $publication->isbn);
        }
        $paras[] = $this->makePara('Kolofon', 'Sats og layout: Indiemoon');
        $paras[] = $this->makePara('Kolofon', 'Trykk: ScandinavianBook');
        $this->writeStory($id, $paras);
        $storyFiles[] = "Stories/Story_u{$id}.xml";

        // Stories: Chapters
        foreach ($manuscript->chapters as $i => $chapter) {
            $id = $this->nextId++;
            $paras = [];
            $paras[] = $this->makePara('Kapittelnummer', (string) $chapter['number']);
            $paras[] = $this->makePara('Kapitteloverskrift', $chapter['title']);
            $paras = array_merge($paras, $this->htmlToParagraphs($chapter['html']));
            $this->writeStory($id, $paras);
            $storyFiles[] = "Stories/Story_u{$id}.xml";
        }

        return $storyFiles;
    }

    private function writeStory(int $id, array $paragraphs): void
    {
        $ns = 'xmlns:idPkg="http://ns.adobe.com/AdobeInDesign/idml/1.0/packaging"';
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
        $xml .= "<idPkg:Story {$ns} DOMVersion=\"8.0\">\n";
        $xml .= "\t<Story Self=\"u{$id}\" AppliedTOCStyle=\"n\" TrackChanges=\"false\" StoryTitle=\"\">\n";
        $xml .= "\t\t<StoryPreference OpticalMarginAlignment=\"false\"/>\n";
        $xml .= "\t\t<InCopyExportOption IncludeGraphicProxies=\"true\" IncludeAllResources=\"false\"/>\n";

        foreach ($paragraphs as $para) {
            $xml .= $para . "\n";
        }

        $xml .= "\t</Story>\n";
        $xml .= "</idPkg:Story>\n";

        file_put_contents("{$this->workDir}/Stories/Story_u{$id}.xml", $xml);
    }

    private function makePara(string $style, string $text): string
    {
        $escaped = htmlspecialchars($text, ENT_XML1, 'UTF-8');
        $noStyle = 'CharacterStyle/$ID/[No character style]';
        return "\t\t<ParagraphStyleRange AppliedParagraphStyle=\"ParagraphStyle/{$style}\">\n" .
               "\t\t\t<CharacterStyleRange AppliedCharacterStyle=\"{$noStyle}\">\n" .
               "\t\t\t\t<Content>{$escaped}</Content>\n" .
               "\t\t\t</CharacterStyleRange>\n" .
               "\t\t\t<CharacterStyleRange AppliedCharacterStyle=\"{$noStyle}\">\n" .
               "\t\t\t\t<Br/>\n" .
               "\t\t\t</CharacterStyleRange>\n" .
               "\t\t</ParagraphStyleRange>";
    }

    private function makeParaWithRuns(string $style, string $html): string
    {
        $html = trim(strip_tags($html, '<em><i><strong><b>'));
        $parts = preg_split('/(<\/?(?:em|i|strong|b)>)/', $html, -1, PREG_SPLIT_DELIM_CAPTURE);

        $noStyle = 'CharacterStyle/$ID/[No character style]';
        $runs = [];
        $isBold = false;
        $isItalic = false;

        foreach ($parts as $part) {
            if ($part === '<em>' || $part === '<i>') { $isItalic = true; continue; }
            if ($part === '</em>' || $part === '</i>') { $isItalic = false; continue; }
            if ($part === '<strong>' || $part === '<b>') { $isBold = true; continue; }
            if ($part === '</strong>' || $part === '</b>') { $isBold = false; continue; }

            $text = html_entity_decode(strip_tags($part), ENT_QUOTES, 'UTF-8');
            if (empty(trim($text))) continue;

            $charStyle = $noStyle;
            if ($isBold && $isItalic) $charStyle = 'CharacterStyle/Fet kursiv';
            elseif ($isBold) $charStyle = 'CharacterStyle/Fet';
            elseif ($isItalic) $charStyle = 'CharacterStyle/Kursiv';

            $escaped = htmlspecialchars($text, ENT_XML1, 'UTF-8');
            $runs[] = "\t\t\t<CharacterStyleRange AppliedCharacterStyle=\"{$charStyle}\">\n" .
                      "\t\t\t\t<Content>{$escaped}</Content>\n" .
                      "\t\t\t</CharacterStyleRange>";
        }

        // Add line break at end
        $runs[] = "\t\t\t<CharacterStyleRange AppliedCharacterStyle=\"{$noStyle}\">\n" .
                  "\t\t\t\t<Br/>\n" .
                  "\t\t\t</CharacterStyleRange>";

        return "\t\t<ParagraphStyleRange AppliedParagraphStyle=\"ParagraphStyle/{$style}\">\n" .
               implode("\n", $runs) . "\n" .
               "\t\t</ParagraphStyleRange>";
    }

    private function htmlToParagraphs(string $html): array
    {
        $paras = [];
        $isFirstPara = true;

        $html = str_replace(['<br>', '<br/>', '<br />'], "\n", $html);
        preg_match_all('/<(p|blockquote|div)[^>]*>(.*?)<\/\1>/s', $html, $matches);

        foreach ($matches[2] as $i => $content) {
            $tag = $matches[1][$i];
            $content = trim($content);
            if (empty($content)) continue;

            // Scene break
            if (str_contains($content, '* * *') || str_contains($content, "\xE2\x81\x82") || str_contains($content, "\xE2\x96\xA0")) {
                $paras[] = $this->makePara('Sceneskift', '* * *');
                $isFirstPara = true;
                continue;
            }

            // Chapter meta
            $fullTag = $matches[0][$i];
            if (str_contains($fullTag, 'chapter-meta')) {
                $paras[] = $this->makeParaWithRuns('Kapittelmeta', $content);
                continue;
            }

            // Blockquote
            if ($tag === 'blockquote') {
                $paras[] = $this->makeParaWithRuns('Sitat', $content);
                $isFirstPara = true;
                continue;
            }

            // Dialog (starts with dash)
            $plainText = strip_tags($content);
            if (preg_match('/^\s*[\x{2013}\x{2014}–—-]/u', $plainText)) {
                $paras[] = $this->makeParaWithRuns('Dialog', $content);
                $isFirstPara = false;
                continue;
            }

            // Regular paragraph
            $style = $isFirstPara ? 'Brodtekst uten innrykk' : 'Brodtekst';
            $paras[] = $this->makeParaWithRuns($style, $content);
            $isFirstPara = false;
        }

        return $paras;
    }

    private function updateDesignMap(array $storyFiles): void
    {
        $dmPath = "{$this->workDir}/designmap.xml";
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        $doc->load($dmPath);

        $ns = 'http://ns.adobe.com/AdobeInDesign/idml/1.0/packaging';

        foreach ($storyFiles as $storyFile) {
            $storyEl = $doc->createElementNS($ns, 'idPkg:Story');
            $storyEl->setAttribute('src', $storyFile);
            $doc->documentElement->appendChild($storyEl);
        }

        $doc->save($dmPath);
    }

    private function packIdml(string $outputPath): void
    {
        @mkdir(dirname($outputPath), 0755, true);

        $zip = new ZipArchive();
        $zip->open($outputPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // mimetype FIRST, uncompressed
        $mimetypePath = "{$this->workDir}/mimetype";
        $zip->addFile($mimetypePath, 'mimetype');
        $zip->setCompressionName('mimetype', ZipArchive::CM_STORE);

        // All other files
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->workDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            $relativePath = str_replace('\\', '/', substr($file->getRealPath(), strlen($this->workDir) + 1));
            if ($relativePath === 'mimetype') continue;
            $zip->addFile($file->getRealPath(), $relativePath);
        }

        $zip->close();
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

    private function removeDir(string $dir): void
    {
        if (!is_dir($dir)) return;
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            $file->isDir() ? rmdir($file->getRealPath()) : unlink($file->getRealPath());
        }
        rmdir($dir);
    }
}
