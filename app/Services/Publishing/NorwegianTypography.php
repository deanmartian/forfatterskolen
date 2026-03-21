<?php

namespace App\Services\Publishing;

class NorwegianTypography
{
    public static function apply(string $html): string
    {
        // Anførselstegn: "" → «»
        $html = preg_replace('/(?<=>|^|\s)"([^"]+)"/', ' «$1»', $html);
        $html = str_replace(["\u{201C}", "\u{201D}"], ['«', '»'], $html);

        // Indre anførselstegn: '' → ''
        $html = preg_replace('/(?<=\s)\'([^\']+)\'/', "\u{2018}$1\u{2019}", $html);

        // Apostrof: ' → ' (U+2019)
        $html = preg_replace("/(\w)'(\w)/u", "$1\u{2019}$2", $html);

        // Tankestrek: " - " → " – "
        $html = str_replace(' - ', " \u{2013} ", $html);
        $html = str_replace('--', "\u{2013}", $html);

        // Ellipse: ... → …
        $html = str_replace('...', "\u{2026}", $html);

        // Hardt mellomrom etter forkortelser
        $html = preg_replace('/\b(f\.eks\.|bl\.a\.|dvs\.|mfl\.|osv\.|ca\.|kr\.|nr\.|s\.|kl\.)\s/', "$1\u{00A0}", $html);

        // Tall + enhet
        $html = preg_replace('/(\d)\s+(ord|sider?|kr|%|mm|cm|m|km|kg|g|l|ml|stk)\b/', "$1\u{00A0}$2", $html);

        // Tittel + navn
        $html = preg_replace('/\b(dr|hr|fru|frk|prof|st)\.\s+/', "$1.\u{00A0}", $html);

        // Paragraftegn
        $html = preg_replace('/(§|kap\.)\s+(\d)/', "$1\u{00A0}$2", $html);

        // Tusenskilletegn
        $html = preg_replace_callback('/\b(\d{5,})\b/', function ($m) {
            return preg_replace('/(\d)(?=(\d{3})+(?!\d))/', "$1\u{202F}", $m[1]);
        }, $html);

        return $html;
    }
}
