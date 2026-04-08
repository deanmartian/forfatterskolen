<?php

namespace App\Helpers;

/**
 * Konverterer plain text inbox-meldinger til trygg HTML med klikkbare lenker.
 *
 * Støtter:
 *   - Markdown-stil lenker:  [innloggingslenke](https://...) → <a href="...">innloggingslenke</a>
 *   - Rå URLer:              https://example.com → <a href="https://example.com">https://example.com</a>
 *   - Linjeskift:            \n → <br>
 *
 * Alt annet HTML-escapes for sikkerhet.
 */
class InboxBodyFormatter
{
    public static function toHtml(?string $body): string
    {
        if ($body === null || $body === '') {
            return '';
        }

        // Steg 1: ekstraher markdown-lenker og bytt med plassholdere
        // (slik at HTML-escape ikke ødelegger dem)
        $placeholders = [];
        $counter = 0;
        $body = preg_replace_callback(
            '/\[([^\]]+)\]\((https?:\/\/[^\s\)]+)\)/',
            function ($m) use (&$placeholders, &$counter) {
                $key = "\x00LINK_PH_{$counter}\x00";
                $text = htmlspecialchars($m[1], ENT_QUOTES, 'UTF-8');
                $url = htmlspecialchars($m[2], ENT_QUOTES, 'UTF-8');
                $placeholders[$key] = '<a href="' . $url . '" target="_blank" rel="noopener">' . $text . '</a>';
                $counter++;
                return $key;
            },
            $body
        );

        // Steg 2: HTML-escape resten av teksten
        $body = htmlspecialchars($body, ENT_QUOTES, 'UTF-8');

        // Steg 3: konverter rå URLer til klikkbare lenker
        // (etter escape, så vi vet det er trygt)
        $body = preg_replace_callback(
            '/(https?:\/\/[^\s<\x00]+)/',
            function ($m) {
                $url = rtrim($m[1], '.,;:!?');
                $trail = substr($m[1], strlen($url));
                return '<a href="' . $url . '" target="_blank" rel="noopener">' . $url . '</a>' . $trail;
            },
            $body
        );

        // Steg 4: gjenopprett plassholdere med ferdig HTML-lenker
        $body = strtr($body, $placeholders);

        // Steg 5: linjeskift til <br>
        $body = nl2br($body);

        return $body;
    }
}
