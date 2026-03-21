<?php

namespace App\Services\Publishing;

use App\Models\Publication;
use ZipArchive;

class EpubBuilder
{
    public function build(ParsedManuscript $manuscript, Publication $publication, string $outputPath): void
    {
        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($outputPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Cannot create EPUB: {$outputPath}");
        }

        // mimetype must be first, uncompressed
        $zip->addFromString('mimetype', 'application/epub+zip');
        $zip->setCompressionName('mimetype', ZipArchive::CM_STORE);

        // META-INF/container.xml
        $zip->addFromString('META-INF/container.xml', $this->containerXml());

        // OEBPS/content.opf
        $zip->addFromString('OEBPS/content.opf', $this->contentOpf($manuscript, $publication));

        // OEBPS/toc.xhtml (nav)
        $zip->addFromString('OEBPS/toc.xhtml', $this->tocXhtml($manuscript, $publication));

        // OEBPS/style.css
        $zip->addFromString('OEBPS/style.css', $this->styleCss($publication->theme ?? 'classic'));

        // Title page
        $zip->addFromString('OEBPS/titlepage.xhtml', $this->titlePage($publication));

        // Chapters
        foreach ($manuscript->chapters as $i => $chapter) {
            $zip->addFromString("OEBPS/chapter{$i}.xhtml", $this->chapterXhtml($chapter, $publication->language ?? 'nb'));
        }

        $zip->close();
    }

    private function containerXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<container version="1.0" xmlns="urn:oasis:names:tc:opendocument:xmlns:container">
  <rootfiles>
    <rootfile full-path="OEBPS/content.opf" media-type="application/oebps-package+xml"/>
  </rootfiles>
</container>
XML;
    }

    private function contentOpf(ParsedManuscript $manuscript, Publication $publication): string
    {
        $title = htmlspecialchars($publication->title);
        $author = htmlspecialchars($publication->author_name);
        $lang = $publication->language ?? 'nb';
        $uuid = 'urn:uuid:' . \Illuminate\Support\Str::uuid();
        $date = date('Y-m-d');

        $chapterItems = '';
        $chapterSpine = '';
        foreach ($manuscript->chapters as $i => $chapter) {
            $chapterItems .= "    <item id=\"chapter{$i}\" href=\"chapter{$i}.xhtml\" media-type=\"application/xhtml+xml\"/>\n";
            $chapterSpine .= "    <itemref idref=\"chapter{$i}\"/>\n";
        }

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<package xmlns="http://www.idpf.org/2007/opf" version="3.0" unique-identifier="bookid">
  <metadata xmlns:dc="http://purl.org/dc/elements/1.1/">
    <dc:identifier id="bookid">{$uuid}</dc:identifier>
    <dc:title>{$title}</dc:title>
    <dc:creator>{$author}</dc:creator>
    <dc:language>{$lang}</dc:language>
    <dc:date>{$date}</dc:date>
    <meta property="dcterms:modified">{$date}T00:00:00Z</meta>
  </metadata>
  <manifest>
    <item id="nav" href="toc.xhtml" media-type="application/xhtml+xml" properties="nav"/>
    <item id="style" href="style.css" media-type="text/css"/>
    <item id="titlepage" href="titlepage.xhtml" media-type="application/xhtml+xml"/>
{$chapterItems}  </manifest>
  <spine>
    <itemref idref="titlepage"/>
{$chapterSpine}  </spine>
</package>
XML;
    }

    private function tocXhtml(ParsedManuscript $manuscript, Publication $publication): string
    {
        $title = htmlspecialchars($publication->title);
        $lang = $publication->language ?? 'nb';

        $items = "<li><a href=\"titlepage.xhtml\">Tittelside</a></li>\n";
        foreach ($manuscript->chapters as $i => $chapter) {
            $chTitle = htmlspecialchars($chapter['title'] ?: 'Kapittel ' . ($i + 1));
            $items .= "        <li><a href=\"chapter{$i}.xhtml\">{$chTitle}</a></li>\n";
        }

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:epub="http://www.idpf.org/2007/ops" lang="{$lang}">
<head>
  <meta charset="UTF-8"/>
  <title>{$title} - Innhold</title>
  <link rel="stylesheet" href="style.css"/>
</head>
<body>
  <nav epub:type="toc">
    <h1>Innhold</h1>
    <ol>
      {$items}
    </ol>
  </nav>
</body>
</html>
XML;
    }

    private function titlePage(Publication $publication): string
    {
        $title = htmlspecialchars($publication->title);
        $subtitle = $publication->subtitle ? '<h2>' . htmlspecialchars($publication->subtitle) . '</h2>' : '';
        $author = htmlspecialchars($publication->author_name);
        $lang = $publication->language ?? 'nb';

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$lang}">
<head>
  <meta charset="UTF-8"/>
  <title>{$title}</title>
  <link rel="stylesheet" href="style.css"/>
</head>
<body>
  <div class="titlepage">
    <h1>{$title}</h1>
    {$subtitle}
    <p class="author">{$author}</p>
    <p class="publisher">Indiemoon Publishing</p>
  </div>
</body>
</html>
XML;
    }

    private function chapterXhtml(array $chapter, string $lang): string
    {
        $title = htmlspecialchars($chapter['title'] ?: '');
        $heading = $title ? "<h1>{$title}</h1>" : '';
        $content = $chapter['html'];

        // Ensure XHTML compliance
        $content = preg_replace('/<br\s*>/', '<br/>', $content);
        $content = preg_replace('/<hr\s*>/', '<hr/>', $content);

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$lang}">
<head>
  <meta charset="UTF-8"/>
  <title>{$title}</title>
  <link rel="stylesheet" href="style.css"/>
</head>
<body>
  <section class="chapter">
    {$heading}
    {$content}
  </section>
</body>
</html>
XML;
    }

    private function styleCss(string $theme): string
    {
        $base = <<<'CSS'
body {
    font-family: Georgia, serif;
    font-size: 1em;
    line-height: 1.6;
    color: #1a1a1a;
    margin: 1em;
}
h1 { font-size: 1.8em; margin: 1.5em 0 0.5em; text-align: center; }
h2 { font-size: 1.4em; margin: 1em 0 0.4em; }
h3 { font-size: 1.2em; margin: 0.8em 0 0.3em; }
p { margin: 0.4em 0; text-align: justify; }
p + p { text-indent: 1.5em; }
.titlepage { text-align: center; margin-top: 30%; }
.titlepage h1 { font-size: 2.2em; margin-bottom: 0.3em; }
.titlepage .author { font-size: 1.3em; color: #444; margin-top: 1em; }
.titlepage .publisher { font-size: 1em; color: #888; margin-top: 3em; }
.chapter p:first-of-type { text-indent: 0; }
CSS;

        $themeExtra = match ($theme) {
            'modern' => "body { font-family: Helvetica, Arial, sans-serif; } h1 { text-transform: uppercase; letter-spacing: 0.05em; font-weight: 300; }",
            'crime' => "h1 { text-transform: uppercase; letter-spacing: 0.1em; border-bottom: 2px solid #000; padding-bottom: 0.3em; }",
            'children' => "body { font-size: 1.2em; line-height: 1.8; } h1 { color: #862736; }",
            'nonfiction' => "h2 { border-bottom: 1px solid #ccc; padding-bottom: 0.2em; }",
            default => '',
        };

        return $base . "\n" . $themeExtra;
    }
}
