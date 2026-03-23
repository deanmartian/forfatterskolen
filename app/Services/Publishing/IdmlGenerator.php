<?php

namespace App\Services\Publishing;

use App\Models\Publication;
use ZipArchive;

/**
 * Generates IDML (InDesign Markup Language) files from parsed manuscripts.
 * IDML is a ZIP containing XML files that InDesign can open natively.
 */
class IdmlGenerator
{
    public function generate(ParsedManuscript $manuscript, Publication $publication, string $outputPath): void
    {
        $trimSize = TrimSize::tryFrom($publication->trim_size) ?? TrimSize::FORMAT_140x220;
        $dims = $trimSize->dimensions();

        $tempDir = sys_get_temp_dir() . '/idml_' . uniqid();
        mkdir($tempDir, 0755, true);

        $this->createMimetype($tempDir);
        $this->createMetaInf($tempDir);
        $this->createDesignMap($tempDir, $manuscript);
        $this->createPreferences($tempDir, $dims);
        $this->createStyles($tempDir, $publication);
        $this->createStories($tempDir, $manuscript, $publication);
        $this->createSpreads($tempDir, $dims, count($manuscript->chapters));

        $this->packageIdml($tempDir, $outputPath);
        $this->removeDir($tempDir);
    }

    private function createMimetype(string $dir): void
    {
        file_put_contents("$dir/mimetype", 'application/vnd.adobe.indesign-idml-package+xml');
    }

    private function createMetaInf(string $dir): void
    {
        mkdir("$dir/META-INF", 0755, true);
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= '<container><rootfiles><rootfile full-path="designmap.xml"/></rootfiles></container>';
        file_put_contents("$dir/META-INF/container.xml", $xml);
    }

    private function createDesignMap(string $dir, ParsedManuscript $manuscript): void
    {
        $ns = 'xmlns:idPkg="http://ns.adobe.com/AdobeInDesign/idml/1.0/packaging"';
        $totalStories = count($manuscript->chapters) + 2;
        $storyRefs = '';
        for ($i = 1; $i <= $totalStories; $i++) {
            $storyRefs .= "    <idPkg:Story src=\"Stories/Story_u{$i}.xml\" {$ns}/>\n";
        }

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
        $xml .= "<Document DOMVersion=\"19.0\" Self=\"d\">\n";
        $xml .= "    <idPkg:Preferences src=\"Resources/Preferences.xml\" {$ns}/>\n";
        $xml .= "    <idPkg:Styles src=\"Resources/Styles.xml\" {$ns}/>\n";
        $xml .= $storyRefs;
        $xml .= "    <idPkg:Spread src=\"Spreads/Spread_main.xml\" {$ns}/>\n";
        $xml .= "</Document>";

        file_put_contents("$dir/designmap.xml", $xml);
    }

    private function createPreferences(string $dir, array $dims): void
    {
        mkdir("$dir/Resources", 0755, true);

        $pageW = round($dims['width'] * 2.834645669, 2);
        $pageH = round($dims['height'] * 2.834645669, 2);
        $mTop = round($dims['marginTop'] * 2.834645669, 2);
        $mBottom = round($dims['marginBottom'] * 2.834645669, 2);
        $mInside = round($dims['marginInside'] * 2.834645669, 2);
        $mOutside = round($dims['marginOutside'] * 2.834645669, 2);

        $ns = 'xmlns:idPkg="http://ns.adobe.com/AdobeInDesign/idml/1.0/packaging"';
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
        $xml .= "<idPkg:Preferences {$ns} DOMVersion=\"19.0\">\n";
        $xml .= "    <DocumentPreference PageWidth=\"{$pageW}\" PageHeight=\"{$pageH}\" FacingPages=\"true\"/>\n";
        $xml .= "    <MarginPreference Top=\"{$mTop}\" Bottom=\"{$mBottom}\" Left=\"{$mInside}\" Right=\"{$mOutside}\" ColumnCount=\"1\"/>\n";
        $xml .= "</idPkg:Preferences>";

        file_put_contents("$dir/Resources/Preferences.xml", $xml);
    }

    private function createStyles(string $dir, Publication $publication): void
    {
        $fonts = $this->getThemeFonts($publication->theme ?? 'classic');
        $bf = $fonts['body'];
        $hf = $fonts['heading'];

        $ns = 'xmlns:idPkg="http://ns.adobe.com/AdobeInDesign/idml/1.0/packaging"';
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
        $xml .= "<idPkg:Styles {$ns} DOMVersion=\"19.0\">\n";
        $xml .= "  <RootParagraphStyleGroup Self=\"di0\">\n";

        $styles = [
            ['name' => '[Basic Paragraph]', 'font' => $bf, 'style' => 'Regular', 'size' => 11, 'leading' => 14.5, 'just' => 'LeftJustified', 'indent' => 0],
            ['name' => 'Brodtekst', 'font' => $bf, 'style' => 'Regular', 'size' => 11, 'leading' => 14.5, 'just' => 'LeftJustified', 'indent' => 12.76],
            ['name' => 'Brodtekst uten innrykk', 'font' => $bf, 'style' => 'Regular', 'size' => 11, 'leading' => 14.5, 'just' => 'LeftJustified', 'indent' => 0],
            ['name' => 'Kapitteloverskrift', 'font' => $hf, 'style' => 'Bold', 'size' => 20, 'leading' => 24, 'just' => 'CenterAlign', 'indent' => 0, 'caps' => true],
            ['name' => 'Kapittelnummer', 'font' => $hf, 'style' => 'Light', 'size' => 48, 'leading' => 48, 'just' => 'CenterAlign', 'indent' => 0],
            ['name' => 'Halvtittel', 'font' => $hf, 'style' => 'Bold', 'size' => 16, 'leading' => 20, 'just' => 'CenterAlign', 'indent' => 0],
            ['name' => 'Tittel', 'font' => $hf, 'style' => 'Bold', 'size' => 28, 'leading' => 33.6, 'just' => 'CenterAlign', 'indent' => 0, 'caps' => true],
            ['name' => 'Forfatter', 'font' => $hf, 'style' => 'Light', 'size' => 10, 'leading' => 12, 'just' => 'CenterAlign', 'indent' => 0, 'caps' => true, 'tracking' => 150],
            ['name' => 'Forlag', 'font' => $hf, 'style' => 'Light', 'size' => 8, 'leading' => 10, 'just' => 'CenterAlign', 'indent' => 0, 'caps' => true, 'tracking' => 200],
            ['name' => 'Kolofon', 'font' => $bf, 'style' => 'Regular', 'size' => 7, 'leading' => 11, 'just' => 'LeftJustified', 'indent' => 0],
            ['name' => 'Sceneskift', 'font' => $bf, 'style' => 'Regular', 'size' => 11, 'leading' => 14.5, 'just' => 'CenterAlign', 'indent' => 0],
            ['name' => 'Sitat', 'font' => $bf, 'style' => 'Italic', 'size' => 10.5, 'leading' => 14, 'just' => 'LeftJustified', 'indent' => 0, 'leftIndent' => 14.17],
            ['name' => 'Kapittelmeta', 'font' => $hf, 'style' => 'SemiBold', 'size' => 10, 'leading' => 13, 'just' => 'CenterAlign', 'indent' => 0],
        ];

        foreach ($styles as $s) {
            $attrs = "Self=\"ParagraphStyle/{$s['name']}\" Name=\"{$s['name']}\" AppliedFont=\"{$s['font']}\" FontStyle=\"{$s['style']}\" PointSize=\"{$s['size']}\" Leading=\"{$s['leading']}\" Justification=\"{$s['just']}\" FirstLineIndent=\"{$s['indent']}\"";
            if (!empty($s['caps'])) $attrs .= ' Capitalization="AllCaps"';
            if (!empty($s['tracking'])) $attrs .= " Tracking=\"{$s['tracking']}\"";
            if (!empty($s['leftIndent'])) $attrs .= " LeftIndent=\"{$s['leftIndent']}\"";
            $xml .= "    <ParagraphStyle {$attrs}/>\n";
        }

        $xml .= "  </RootParagraphStyleGroup>\n";
        $xml .= "  <RootCharacterStyleGroup Self=\"di1\">\n";
        $xml .= "    <CharacterStyle Self=\"CharacterStyle/\$ID/[No character style]\" Name=\"[None]\"/>\n";
        $xml .= "    <CharacterStyle Self=\"CharacterStyle/Kursiv\" Name=\"Kursiv\" FontStyle=\"Italic\"/>\n";
        $xml .= "    <CharacterStyle Self=\"CharacterStyle/Fet\" Name=\"Fet\" FontStyle=\"Bold\"/>\n";
        $xml .= "    <CharacterStyle Self=\"CharacterStyle/Fet kursiv\" Name=\"Fet kursiv\" FontStyle=\"Bold Italic\"/>\n";
        $xml .= "  </RootCharacterStyleGroup>\n";
        $xml .= "</idPkg:Styles>";

        file_put_contents("$dir/Resources/Styles.xml", $xml);
    }

    private function createStories(string $dir, ParsedManuscript $manuscript, Publication $publication): void
    {
        mkdir("$dir/Stories", 0755, true);

        // Story 1: Front matter
        $front = [
            $this->para('Halvtittel', $publication->title),
            $this->para('Forfatter', strtoupper($publication->author_name)),
            $this->para('Tittel', $publication->title),
            $this->para('Forfatter', strtoupper($publication->author_name)),
            $this->para('Forlag', strtoupper($publication->publisher ?? 'Indiemoon')),
        ];
        file_put_contents("$dir/Stories/Story_u1.xml", $this->buildStoryXml(1, $front));

        // Story 2: Colophon
        $colophon = [
            $this->para('Kolofon', "\xC2\xA9 " . date('Y') . ' ' . $publication->author_name),
            $this->para('Kolofon', 'Utgitt av ' . ($publication->publisher ?? 'Indiemoon')),
        ];
        if ($publication->isbn) {
            $colophon[] = $this->para('Kolofon', 'ISBN ' . $publication->isbn);
        }
        $colophon[] = $this->para('Kolofon', 'Sats og layout: Indiemoon');
        $colophon[] = $this->para('Kolofon', 'Trykk: ScandinavianBook');
        file_put_contents("$dir/Stories/Story_u2.xml", $this->buildStoryXml(2, $colophon));

        // Stories 3+: Chapters
        foreach ($manuscript->chapters as $i => $chapter) {
            $storyId = $i + 3;
            $paras = [];
            $paras[] = $this->para('Kapittelnummer', (string) $chapter['number']);
            $paras[] = $this->para('Kapitteloverskrift', $chapter['title']);
            $paras = array_merge($paras, $this->htmlToParagraphs($chapter['html']));
            file_put_contents("$dir/Stories/Story_u{$storyId}.xml", $this->buildStoryXml($storyId, $paras));
        }
    }

    private function createSpreads(string $dir, array $dims, int $chapterCount): void
    {
        mkdir("$dir/Spreads", 0755, true);
        $pageW = round($dims['width'] * 2.834645669, 2);
        $pageH = round($dims['height'] * 2.834645669, 2);

        $ns = 'xmlns:idPkg="http://ns.adobe.com/AdobeInDesign/idml/1.0/packaging"';
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
        $xml .= "<idPkg:Spread {$ns} DOMVersion=\"19.0\">\n";
        $xml .= "    <Spread Self=\"uc1\" PageCount=\"1\">\n";
        $xml .= "        <Page Self=\"page1\" GeometricBounds=\"0 0 {$pageH} {$pageW}\"/>\n";
        $xml .= "    </Spread>\n";
        $xml .= "</idPkg:Spread>";

        file_put_contents("$dir/Spreads/Spread_main.xml", $xml);
    }

    private function buildStoryXml(int $id, array $paragraphs): string
    {
        $ns = 'xmlns:idPkg="http://ns.adobe.com/AdobeInDesign/idml/1.0/packaging"';
        $content = implode("\n", $paragraphs);

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
        $xml .= "<idPkg:Story {$ns} DOMVersion=\"19.0\">\n";
        $xml .= "    <Story Self=\"u{$id}\" AppliedTOCStyle=\"n\" TrackChanges=\"false\">\n";
        $xml .= $content . "\n";
        $xml .= "    </Story>\n";
        $xml .= "</idPkg:Story>";

        return $xml;
    }

    private function para(string $style, string $text): string
    {
        $escaped = htmlspecialchars($text, ENT_XML1, 'UTF-8');
        $noStyle = 'CharacterStyle/$ID/[No character style]';
        return "        <ParagraphStyleRange AppliedParagraphStyle=\"ParagraphStyle/{$style}\">\n" .
               "            <CharacterStyleRange AppliedCharacterStyle=\"{$noStyle}\">\n" .
               "                <Content>{$escaped}</Content>\n" .
               "            </CharacterStyleRange>\n" .
               "        </ParagraphStyleRange>";
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

            if (str_contains($content, '* * *') || str_contains($content, "\xE2\x81\x82") || str_contains($content, "\xE2\x96\xA0")) {
                $paras[] = $this->para('Sceneskift', '* * *');
                $isFirstPara = true;
                continue;
            }

            $fullTag = $matches[0][$i];
            if (str_contains($fullTag, 'chapter-meta')) {
                $paras[] = $this->paraWithRuns('Kapittelmeta', $content);
                continue;
            }

            if ($tag === 'blockquote') {
                $paras[] = $this->paraWithRuns('Sitat', $content);
                $isFirstPara = true;
                continue;
            }

            $style = $isFirstPara ? 'Brodtekst uten innrykk' : 'Brodtekst';
            $paras[] = $this->paraWithRuns($style, $content);
            $isFirstPara = false;
        }

        return $paras;
    }

    private function paraWithRuns(string $style, string $html): string
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
            if (empty($text)) continue;

            $charStyle = $noStyle;
            if ($isBold && $isItalic) $charStyle = 'CharacterStyle/Fet kursiv';
            elseif ($isBold) $charStyle = 'CharacterStyle/Fet';
            elseif ($isItalic) $charStyle = 'CharacterStyle/Kursiv';

            $escaped = htmlspecialchars($text, ENT_XML1, 'UTF-8');
            $runs[] = "            <CharacterStyleRange AppliedCharacterStyle=\"{$charStyle}\">\n" .
                      "                <Content>{$escaped}</Content>\n" .
                      "            </CharacterStyleRange>";
        }

        return "        <ParagraphStyleRange AppliedParagraphStyle=\"ParagraphStyle/{$style}\">\n" .
               implode("\n", $runs) . "\n" .
               "        </ParagraphStyleRange>";
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

    private function packageIdml(string $dir, string $outputPath): void
    {
        $zip = new ZipArchive();
        $zip->open($outputPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFile("$dir/mimetype", 'mimetype');
        $zip->setCompressionName('mimetype', ZipArchive::CM_STORE);
        $this->addDirToZip($zip, $dir, $dir);
        $zip->close();
    }

    private function addDirToZip(ZipArchive $zip, string $dir, string $baseDir): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            $relativePath = str_replace('\\', '/', substr($file->getRealPath(), strlen($baseDir) + 1));
            if ($relativePath === 'mimetype') continue;
            $zip->addFile($file->getRealPath(), $relativePath);
        }
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
