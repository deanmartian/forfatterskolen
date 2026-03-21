<?php

namespace App\Services\Publishing;

enum PaperType: string
{
    case MUNKEN_CREAM_80  = 'munken_cream_80';
    case MUNKEN_CREAM_100 = 'munken_cream_100';
    case MUNKEN_WHITE_100 = 'munken_white_100';
    case OFFSET_90        = 'offset_90';
    case MAT_COATED_115   = 'mat_coated_115';
    case SILK_170         = 'silk_170';

    public function label(): string
    {
        return match($this) {
            self::MUNKEN_CREAM_80  => '80g Munken Cream — Romaner, krim',
            self::MUNKEN_CREAM_100 => '100g Munken Cream — Standard roman',
            self::MUNKEN_WHITE_100 => '100g Munken White — Sakprosa, illustrert',
            self::OFFSET_90        => '90g Offsetpapir — Økonomisk',
            self::MAT_COATED_115   => '115g Mat-bestrøget — Fotobøker',
            self::SILK_170         => '170g Silk-papir — Premium illustrert',
        };
    }

    public function sheetThicknessMm(): float
    {
        return match($this) {
            self::MUNKEN_CREAM_80  => 0.120,
            self::MUNKEN_CREAM_100 => 0.150,
            self::MUNKEN_WHITE_100 => 0.150,
            self::OFFSET_90        => 0.110,
            self::MAT_COATED_115   => 0.100,
            self::SILK_170         => 0.150,
        };
    }
}
