<?php

namespace App\Helpers;

/**
 * Fjerner sitert tekst fra inngående e-poster slik at AI-tjenesten kan
 * fokusere på det som faktisk er nytt.
 *
 * Når en kunde svarer fra Gmail/Outlook/Apple Mail blir hele forrige
 * e-post lagt ved som "quoted text" — enten med ">"-prefiks eller en
 * "On X, Y wrote:"-introduksjon. Hvis vi sender dette rått til AI-en,
 * blir den forvirret og svarer på det opprinnelige spørsmålet i stedet
 * for det siste.
 */
class EmailQuoteStripper
{
    public static function strip(?string $body): string
    {
        if ($body === null || $body === '') {
            return '';
        }

        // Normaliser linjeskift
        $body = str_replace(["\r\n", "\r"], "\n", $body);
        $lines = explode("\n", $body);

        $cleanLines = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);

            // Stopp ved typiske "quote header"-linjer fra forskjellige klienter
            if (self::isQuoteHeader($trimmed)) {
                break;
            }

            // Hopp over linjer som starter med ">" (sitatprefiks)
            if (str_starts_with($trimmed, '>')) {
                continue;
            }

            // Hopp over Outlook-style separator
            if (preg_match('/^_{5,}$/', $trimmed) || preg_match('/^-{5,}$/', $trimmed)) {
                break;
            }

            $cleanLines[] = $line;
        }

        // Fjern overflødige tomme linjer på slutten
        return trim(implode("\n", $cleanLines));
    }

    private static function isQuoteHeader(string $line): bool
    {
        if ($line === '') {
            return false;
        }

        // Norske Gmail-quotes: "tir. 7. apr. 2026, 14:03 skrev Forfatterskolen :"
        if (preg_match('/^(man|tir|ons|tor|fre|l[øo]r|s[øo]n)\.\s*\d+\.\s*\w+\.\s*\d{4},?\s*\d+[:.]\d+\s*skrev/iu', $line)) {
            return true;
        }

        // Engelske Gmail-quotes: "On Tue, Apr 7, 2026 at 9:55 AM ... wrote:"
        if (preg_match('/^On\s+\w+,?\s+\w+\s+\d+,?\s+\d{4}.+wrote:?/i', $line)) {
            return true;
        }

        // Apple Mail norsk: "Den 07.04.2026 kl. 14:03 skrev ..."
        if (preg_match('/^Den\s+\d+\.\d+\.\d+.+skrev/iu', $line)) {
            return true;
        }

        // Apple Mail engelsk: "On 07.04.2026, at 14:03, ... wrote:"
        if (preg_match('/^On\s+\d+\.\d+\.\d+.+wrote:?/i', $line)) {
            return true;
        }

        // Outlook plain: "-----Original Message-----" / "Fra: ..."
        if (preg_match('/^-+\s*Original Message\s*-+$/i', $line)) {
            return true;
        }
        if (preg_match('/^(From|Fra):\s+.+/i', $line)) {
            return true;
        }

        return false;
    }
}
