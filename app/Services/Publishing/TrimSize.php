<?php

namespace App\Services\Publishing;

enum TrimSize: string
{
    case FORMAT_140x220 = '140x220';
    case FORMAT_A5      = '148x210';
    case FORMAT_155x230 = '155x230';
    case FORMAT_170x240 = '170x240';
    case FORMAT_A4      = '210x297';

    public function dimensions(): array
    {
        return match($this) {
            self::FORMAT_140x220 => ['width' => 140, 'height' => 220, 'marginTop' => 18, 'marginBottom' => 22, 'marginInside' => 20, 'marginOutside' => 15, 'bleed' => 3],
            self::FORMAT_A5      => ['width' => 148, 'height' => 210, 'marginTop' => 18, 'marginBottom' => 22, 'marginInside' => 20, 'marginOutside' => 15, 'bleed' => 3],
            self::FORMAT_155x230 => ['width' => 155, 'height' => 230, 'marginTop' => 20, 'marginBottom' => 24, 'marginInside' => 22, 'marginOutside' => 17, 'bleed' => 3],
            self::FORMAT_170x240 => ['width' => 170, 'height' => 240, 'marginTop' => 22, 'marginBottom' => 26, 'marginInside' => 24, 'marginOutside' => 18, 'bleed' => 3],
            self::FORMAT_A4      => ['width' => 210, 'height' => 297, 'marginTop' => 25, 'marginBottom' => 30, 'marginInside' => 25, 'marginOutside' => 20, 'bleed' => 3],
        };
    }

    public function label(): string
    {
        return match($this) {
            self::FORMAT_140x220 => '140 × 220 mm — Vanligste romanformat',
            self::FORMAT_A5      => '148 × 210 mm — A5 (sakprosa)',
            self::FORMAT_155x230 => '155 × 230 mm — Stort romanformat',
            self::FORMAT_170x240 => '170 × 240 mm — Sakprosa, illustrert',
            self::FORMAT_A4      => '210 × 297 mm — A4 (barnebøker)',
        };
    }

    public function spineWidth(int $pageCount, PaperType $paperType): float
    {
        $sheetThickness = $paperType->sheetThicknessMm();
        $spineBase = ($pageCount / 2) * $sheetThickness;
        return round($spineBase + 0.8, 1);
    }
}
