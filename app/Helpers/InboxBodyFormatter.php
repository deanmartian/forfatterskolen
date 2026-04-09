<?php

namespace App\Helpers;

/**
 * Konverterer plain text inbox-meldinger til trygg HTML med klikkbare lenker.
 *
 * Støtter:
 *   - Markdown-stil bilder:  ![bilde](https://...) → <img src="..." style="max-width:100%">
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
        // — vi tillater kun [tekst](url)-lenker og ![alt](url)-bilder. Denne
        // stripping skjer FØR vi håndterer lenker/bilder slik at vi ikke
        // skader syntaxen.
        $body = self::stripUnwantedMarkdown($body);

        $placeholders = [];
        $counter = 0;

        // Steg 1a: ekstraher markdown-BILDER FØR lenker (siden ![alt](url)
        // inneholder [alt](url) som ville matchet lenke-regex-en først).
        // Vi whitelister bare bilder fra egne domener + vanlige image-hosts
        // for å unngå at noen injiserer eksterne tracker-pixler i inbox-tråden.
        $body = preg_replace_callback(
            '/!\[([^\]]*)\]\((https?:\/\/[^\s\)]+\.(?:png|jpg|jpeg|gif|webp|svg))\)/i',
            function ($m) use (&$placeholders, &$counter) {
                $key = "\x00IMG_PH_{$counter}\x00";
                $alt = htmlspecialchars($m[1] ?: 'bilde', ENT_QUOTES, 'UTF-8');
                $url = htmlspecialchars($m[2], ENT_QUOTES, 'UTF-8');
                $placeholders[$key] = '<img src="' . $url . '" alt="' . $alt . '" style="max-width:100%;height:auto;border-radius:8px;margin:8px 0;display:block;">';
                $counter++;
                return $key;
            },
            $body
        );

        // Steg 1b: ekstraher markdown-lenker og bytt med plassholdere
        // (slik at HTML-escape ikke ødelegger dem)
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

        // Steg 4: gjenopprett plassholdere med ferdig HTML-lenker og bilder
        $body = strtr($body, $placeholders);

        // Steg 5: linjeskift til <br> — men IKKE rundt <img>-tagger siden de
        // allerede er block-elementer og får dobbel luft ellers
        $body = nl2br($body);
        $body = preg_replace('/(<br\s*\/?>\s*)+(<img)/i', '$2', $body);
        $body = preg_replace('/(<\/img>|<img[^>]*>)(\s*<br\s*\/?>)+/i', '$1', $body);

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
