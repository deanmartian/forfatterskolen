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
        // Don't modify Styles.xml with DOMDocument — it breaks InDesign's strict XML.
        // Stories reference styles by name; InDesign creates missing styles on open.
        // This is intentional — styles are created automatically when the IDML is opened.
    }

    private function generateStories(ParsedManuscript $manuscript, Publication $publication): array
    {
        $storiesDir = "{$this->workDir}/Stories";
        if (!is_dir($storiesDir)) {
            mkdir($storiesDir, 0755, true);
        }

        // Put ALL content in one story, overwriting the first existing story (u121)
        // This way it's linked to the existing text frame and InDesign flows the text
        $mainStoryId = 'u121';
        $paras = [];

        // Front matter
        $paras[] = $this->makePara('Halvtittel', $publication->title);
        $paras[] = $this->makePara('Forfatter', strtoupper($publication->author_name));
        $paras[] = $this->makePara('Tittel', $publication->title);
        $paras[] = $this->makePara('Forfatter', strtoupper($publication->author_name));
        if ($publication->subtitle) {
            $paras[] = $this->makePara('Halvtittel', $publication->subtitle);
        }
        $paras[] = $this->makePara('Forlag', strtoupper($publication->publisher ?? 'Indiemoon'));

        // Colophon
        $paras[] = $this->makePara('Kolofon', "\xC2\xA9 " . date('Y') . ' ' . $publication->author_name);
        $paras[] = $this->makePara('Kolofon', 'Utgitt av ' . ($publication->publisher ?? 'Indiemoon'));
        if ($publication->isbn) {
            $paras[] = $this->makePara('Kolofon', 'ISBN ' . $publication->isbn);
        }
        $paras[] = $this->makePara('Kolofon', 'Sats og layout: Indiemoon');
        $paras[] = $this->makePara('Kolofon', 'Trykk: ScandinavianBook');

        // Chapters
        foreach ($manuscript->chapters as $i => $chapter) {
            $paras[] = $this->makePara('Kapittelnummer', (string) $chapter['number']);
            $paras[] = $this->makePara('Kapitteloverskrift', $chapter['title']);
            $paras = array_merge($paras, $this->htmlToParagraphs($chapter['html']));
        }

        // Overwrite the existing story file
        $this->writeStory($mainStoryId, $paras);

        // No new story files to add to designmap
        return [];
    }

    private function writeStory(string $id, array $paragraphs): void
    {
        $selfId = str_starts_with($id, 'u') ? $id : "u{$id}";
        $ns = 'xmlns:idPkg="http://ns.adobe.com/AdobeInDesign/idml/1.0/packaging"';
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
        $xml .= "<idPkg:Story {$ns} DOMVersion=\"8.0\">\n";
        $xml .= "\t<Story Self=\"{$selfId}\" AppliedTOCStyle=\"n\" TrackChanges=\"false\" StoryTitle=\"\">\n";
        $xml .= "\t\t<StoryPreference OpticalMarginAlignment=\"false\"/>\n";
        $xml .= "\t\t<InCopyExportOption IncludeGraphicProxies=\"true\" IncludeAllResources=\"false\"/>\n";

        foreach ($paragraphs as $para) {
            $xml .= $para . "\n";
        }

        $xml .= "\t</Story>\n";
        $xml .= "</idPkg:Story>\n";

        $filename = str_starts_with($id, 'u') ? "Story_{$id}" : "Story_u{$id}";
        file_put_contents("{$this->workDir}/Stories/{$filename}.xml", $xml);
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
        $content = file_get_contents($dmPath);

        // Insert story references before closing </Document> tag
        $storyRefs = '';
        foreach ($storyFiles as $storyFile) {
            $storyRefs .= "\t<idPkg:Story src=\"{$storyFile}\" xmlns:idPkg=\"http://ns.adobe.com/AdobeInDesign/idml/1.0/packaging\"/>\n";
        }

        $content = str_replace('</Document>', $storyRefs . '</Document>', $content);
        file_put_contents($dmPath, $content);
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
