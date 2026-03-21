<?php

namespace App\Services\Publishing;

class CoverDimensionCalculator
{
    public function calculate(
        float $bookWidth,
        float $bookHeight,
        float $spineWidth,
        BindingType $binding,
        float $flapWidth = 80,
    ): CoverDimensions {
        return match($binding) {
            BindingType::PAPERBACK      => $this->softcover($bookWidth, $bookHeight, $spineWidth),
            BindingType::PAPERBACK_FLAP => $this->flapCover($bookWidth, $bookHeight, $spineWidth, $flapWidth),
            BindingType::HARDCOVER      => $this->hardcover($bookWidth, $bookHeight, $spineWidth),
        };
    }

    public function dustJacket(float $bookWidth, float $bookHeight, float $spineWidth, float $flapWidth = 80): CoverDimensions
    {
        return $this->dustJacketCalc($bookWidth, $bookHeight, $spineWidth, $flapWidth);
    }

    private function softcover(float $w, float $h, float $spine): CoverDimensions
    {
        $bleed = 3.0;
        $totalWidth  = $bleed + $w + $spine + $w + $bleed;
        $totalHeight = $bleed + $h + $bleed;

        return new CoverDimensions(
            totalWidth: $totalWidth, totalHeight: $totalHeight, type: 'softcover',
            zones: [
                ['name' => 'bleed_left',  'x' => 0,                         'width' => $bleed],
                ['name' => 'back',        'x' => $bleed,                    'width' => $w],
                ['name' => 'spine',       'x' => $bleed + $w,               'width' => $spine],
                ['name' => 'front',       'x' => $bleed + $w + $spine,      'width' => $w],
                ['name' => 'bleed_right', 'x' => $bleed + $w + $spine + $w, 'width' => $bleed],
            ],
            bleed: $bleed, spineX: $bleed + $w, spineWidth: $spine,
            frontX: $bleed + $w + $spine, frontWidth: $w,
            backX: $bleed, backWidth: $w, safeZoneInset: 5.0,
        );
    }

    private function flapCover(float $w, float $h, float $spine, float $flap): CoverDimensions
    {
        $bleed = 3.0;
        $forming = 3.0;
        $totalWidth  = $bleed + $flap + $forming + $w + $spine + $w + $forming + $flap + $bleed;
        $totalHeight = $bleed + $h + $bleed;

        return new CoverDimensions(
            totalWidth: $totalWidth, totalHeight: $totalHeight, type: 'flap',
            zones: [
                ['name' => 'bleed_left',  'x' => 0,                                                        'width' => $bleed],
                ['name' => 'flap_back',   'x' => $bleed,                                                   'width' => $flap],
                ['name' => 'form_back',   'x' => $bleed + $flap,                                           'width' => $forming],
                ['name' => 'back',        'x' => $bleed + $flap + $forming,                                'width' => $w],
                ['name' => 'spine',       'x' => $bleed + $flap + $forming + $w,                           'width' => $spine],
                ['name' => 'front',       'x' => $bleed + $flap + $forming + $w + $spine,                  'width' => $w],
                ['name' => 'form_front',  'x' => $bleed + $flap + $forming + $w + $spine + $w,             'width' => $forming],
                ['name' => 'flap_front',  'x' => $bleed + $flap + $forming + $w + $spine + $w + $forming,  'width' => $flap],
                ['name' => 'bleed_right', 'x' => $totalWidth - $bleed,                                     'width' => $bleed],
            ],
            bleed: $bleed, spineX: $bleed + $flap + $forming + $w, spineWidth: $spine,
            frontX: $bleed + $flap + $forming + $w + $spine, frontWidth: $w,
            backX: $bleed + $flap + $forming, backWidth: $w, safeZoneInset: 5.0,
            flapWidth: $flap, formingWidth: $forming,
            foldMarks: [
                $bleed + $flap,
                $bleed + $flap + $forming + $w + $spine + $w + $forming,
            ],
        );
    }

    private function hardcover(float $w, float $h, float $spine): CoverDimensions
    {
        $ombuk = 17.0;
        $forming = 3.0;
        $burnDown = 10.0;
        $totalWidth  = $ombuk + $forming + $w + $spine + $w + $forming + $ombuk;
        $totalHeight = $ombuk + $forming + $h + $forming + $ombuk;

        return new CoverDimensions(
            totalWidth: $totalWidth, totalHeight: $totalHeight, type: 'hardcover',
            zones: [
                ['name' => 'ombuk_left',  'x' => 0,                                          'width' => $ombuk],
                ['name' => 'form_back',   'x' => $ombuk,                                     'width' => $forming],
                ['name' => 'back',        'x' => $ombuk + $forming,                           'width' => $w],
                ['name' => 'spine',       'x' => $ombuk + $forming + $w,                      'width' => $spine],
                ['name' => 'front',       'x' => $ombuk + $forming + $w + $spine,             'width' => $w],
                ['name' => 'form_front',  'x' => $ombuk + $forming + $w + $spine + $w,        'width' => $forming],
                ['name' => 'ombuk_right', 'x' => $totalWidth - $ombuk,                        'width' => $ombuk],
            ],
            bleed: 0, spineX: $ombuk + $forming + $w, spineWidth: $spine,
            frontX: $ombuk + $forming + $w + $spine, frontWidth: $w,
            backX: $ombuk + $forming, backWidth: $w, safeZoneInset: 5.0,
            ombukWidth: $ombuk, formingWidth: $forming, burnDownWidth: $burnDown,
            burnDownBack: $ombuk + $forming + $w - $burnDown,
            burnDownFront: $ombuk + $forming + $w + $spine,
        );
    }

    private function dustJacketCalc(float $w, float $h, float $spine, float $flap): CoverDimensions
    {
        $bleed = 3.0;
        $forming = 2.0;
        $coverWidth  = $w + 5;
        $coverHeight = $h + 2;
        $coverSpine  = $spine + 3;

        $totalWidth  = $bleed + $flap + $forming + $coverWidth + $coverSpine + $coverWidth + $forming + $flap + $bleed;
        $totalHeight = $bleed + $forming + $coverHeight + $forming + $bleed;

        return new CoverDimensions(
            totalWidth: $totalWidth, totalHeight: $totalHeight, type: 'dust_jacket',
            zones: [
                ['name' => 'bleed_left',  'x' => 0,                                                                    'width' => $bleed],
                ['name' => 'flap_back',   'x' => $bleed,                                                               'width' => $flap],
                ['name' => 'form_back',   'x' => $bleed + $flap,                                                       'width' => $forming],
                ['name' => 'back',        'x' => $bleed + $flap + $forming,                                            'width' => $coverWidth],
                ['name' => 'spine',       'x' => $bleed + $flap + $forming + $coverWidth,                              'width' => $coverSpine],
                ['name' => 'front',       'x' => $bleed + $flap + $forming + $coverWidth + $coverSpine,                'width' => $coverWidth],
                ['name' => 'form_front',  'x' => $bleed + $flap + $forming + $coverWidth + $coverSpine + $coverWidth,  'width' => $forming],
                ['name' => 'flap_front',  'x' => $totalWidth - $bleed - $flap,                                         'width' => $flap],
                ['name' => 'bleed_right', 'x' => $totalWidth - $bleed,                                                 'width' => $bleed],
            ],
            bleed: $bleed, spineX: $bleed + $flap + $forming + $coverWidth, spineWidth: $coverSpine,
            frontX: $bleed + $flap + $forming + $coverWidth + $coverSpine, frontWidth: $coverWidth,
            backX: $bleed + $flap + $forming, backWidth: $coverWidth, safeZoneInset: 5.0,
            flapWidth: $flap, flapSafetyMargin: 10.0, formingWidth: $forming,
            availableFlapWidths: [70, 80, 90, 100, 120],
        );
    }
}
