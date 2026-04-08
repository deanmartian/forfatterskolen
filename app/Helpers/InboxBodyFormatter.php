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

        // Steg 0: fjern markdown-formatering vi IKKE ønsker (fet, kursiv, overskrifter)
        // — vi tillater kun [tekst](url)-lenker. Denne stripping skjer FØR vi
        // håndterer lenker slik at vi ikke skader [tekst](url)-syntax.
        $body = self::stripUnwantedMarkdown($body);

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

    /**
     * Fjerner markdown-formatering som ikke skal vises i e-poster:
     * - **fet** → fet (uten asterisker)
     * - *kursiv* → kursiv (uten asterisker)  — men IKKE rør URL-er
     * - # Overskrifter → Overskrifter
     * - __understrek__ → understrek
     */
    private static function stripUnwantedMarkdown(string $body): string
    {
        // **fet** → fet
        $body = preg_replace('/\*\*(.+?)\*\*/s', '$1', $body);

        // __understrek__ → understrek
        $body = preg_replace('/__(.+?)__/s', '$1', $body);

        // # Overskrift på egen linje (1-6 hashtags etterfulgt av space)
        $body = preg_replace('/^#{1,6}\s+(.+)$/m', '$1', $body);

        // *kursiv* → kursiv (men IKKE asterisker midt i ord, og IKKE inni URL-er)
        // Regelen: en asterisk fulgt av tekst som ikke inneholder asterisk eller mellomrom
        // før neste asterisk
        $body = preg_replace('/(?<![\w*])\*([^\s*][^*]*?)\*(?![\w*])/s', '$1', $body);

        return $body;
    }
}
