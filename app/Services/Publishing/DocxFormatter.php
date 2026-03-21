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

        $phpWord = new PhpWord();
        $phpWord->getSettings()->setThemeFontLang(new \PhpOffice\PhpWord\Style\Language('nb-NO'));

        // Default styles
        $phpWord->setDefaultFontName('Georgia');
        $phpWord->setDefaultFontSize(11);
        $phpWord->setDefaultParagraphStyle([
            'spaceAfter' => 120,
            'lineHeight' => 1.5,
        ]);

        // Heading styles
        $phpWord->addTitleStyle(1, ['size' => 22, 'bold' => true], ['spaceAfter' => 240, 'spaceBefore' => 480, 'alignment' => 'center']);
        $phpWord->addTitleStyle(2, ['size' => 16, 'bold' => true], ['spaceAfter' => 200, 'spaceBefore' => 360]);
        $phpWord->addTitleStyle(3, ['size' => 13, 'bold' => true], ['spaceAfter' => 160, 'spaceBefore' => 240]);

        // Title page
        $section = $phpWord->addSection(['marginTop' => 4000]);
        $section->addText($publication->title, ['size' => 28, 'bold' => true], ['alignment' => 'center', 'spaceBefore' => 6000]);
        if ($publication->subtitle) {
            $section->addText($publication->subtitle, ['size' => 16, 'color' => '444444'], ['alignment' => 'center', 'spaceBefore' => 300]);
        }
        $section->addText($publication->author_name, ['size' => 16, 'color' => '666666'], ['alignment' => 'center', 'spaceBefore' => 800]);
        $section->addText('Indiemoon Publishing', ['size' => 12, 'color' => '999999'], ['alignment' => 'center', 'spaceBefore' => 2000]);

        // Chapters
        foreach ($manuscript->chapters as $chapter) {
            $section = $phpWord->addSection();

            if ($chapter['title']) {
                $section->addTitle($chapter['title'], 1);
            }

            // Convert HTML to PhpWord elements
            $chapterHtml = '<body>' . $chapter['html'] . '</body>';
            try {
                Html::addHtml($section, $chapterHtml, true);
            } catch (\Throwable $e) {
                // Fallback: add as plain text
                $text = strip_tags($chapter['html']);
                $paragraphs = explode("\n", $text);
                foreach ($paragraphs as $para) {
                    $para = trim($para);
                    if ($para !== '') {
                        $section->addText($para);
                    }
                }
            }
        }

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($outputPath);
    }
}
