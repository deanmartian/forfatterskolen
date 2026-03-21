<?php

namespace App\Services\Publishing;

use Dompdf\Dompdf;

class PdfRenderer
{
    public function render(string $html, string $outputPath, TrimSize $trimSize): void
    {
        $dims = $trimSize->dimensions();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper([0, 0, $dims['width'] * 2.8346, $dims['height'] * 2.8346]); // mm to points
        $dompdf->getOptions()->set('defaultFont', 'DejaVu Serif');
        $dompdf->getOptions()->set('isRemoteEnabled', false);
        $dompdf->getOptions()->set('isHtml5ParserEnabled', true);
        $dompdf->render();

        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($outputPath, $dompdf->output());
    }

    public function getPageCount(string $html, TrimSize $trimSize): int
    {
        $dims = $trimSize->dimensions();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper([0, 0, $dims['width'] * 2.8346, $dims['height'] * 2.8346]);
        $dompdf->getOptions()->set('defaultFont', 'DejaVu Serif');
        $dompdf->render();

        return $dompdf->getCanvas()->get_page_count();
    }
}
