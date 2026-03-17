<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Google Ads Service — forenklet versjon som bruker REST API.
 * For full Google Ads API-integrasjon, installer googleads/google-ads-php.
 *
 * Denne servicen håndterer:
 * - Konverteringssporing (gtag-events)
 * - Kampanje-statistikk (via Google Ads API når tilgjengelig)
 * - Admin-grensesnitt for å lage kampanjer
 */
class GoogleAdsService
{
    private string $adsId;

    public function __construct()
    {
        $this->adsId = config('services.google_ads.id', '');
    }

    /**
     * Hent Google Ads ID (for frontend-tracking)
     */
    public function getAdsId(): string
    {
        return $this->adsId;
    }

    /**
     * Generer gtag konverteringskode for kjøp
     */
    public function getPurchaseConversionScript(float $value, string $currency = 'NOK', string $transactionId = ''): string
    {
        $conversionId = config('services.google_ads.conversion_purchase');
        if (!$conversionId) return '';

        return "gtag('event', 'conversion', {
            'send_to': '{$conversionId}',
            'value': {$value},
            'currency': '{$currency}',
            'transaction_id': '{$transactionId}'
        });";
    }

    /**
     * Generer gtag konverteringskode for checkout
     */
    public function getCheckoutConversionScript(float $value = 0, string $currency = 'NOK'): string
    {
        $conversionId = config('services.google_ads.conversion_checkout');
        if (!$conversionId) return '';

        return "gtag('event', 'conversion', {
            'send_to': '{$conversionId}',
            'value': {$value},
            'currency': '{$currency}'
        });";
    }

    /**
     * Generer gtag konverteringskode for lead (webinar-påmelding)
     */
    public function getLeadConversionScript(): string
    {
        $conversionId = config('services.google_ads.conversion_lead');
        if (!$conversionId) return '';

        return "gtag('event', 'conversion', {
            'send_to': '{$conversionId}'
        });";
    }

    /**
     * Standard søkeord for webinar-kampanjer
     */
    public function getDefaultKeywords(): array
    {
        return [
            'skrivekurs',
            'romankurs',
            'skrivekurs på nett',
            'forfatterkurs',
            'lær å skrive bok',
            'skrivekurs nettbasert',
            'kreativ skriving kurs',
            'forfatterskolen',
            'webinar skriving',
            'gratis skrivekurs',
            'hvordan skrive roman',
            'skriveskole',
        ];
    }

    /**
     * Negative søkeord
     */
    public function getNegativeKeywords(): array
    {
        return [
            'gratis kurs',
            'jobb',
            'ledige stillinger',
            'studium',
            'bachelor',
        ];
    }

    /**
     * Standard nettstedslenker for annonser
     */
    public function getDefaultSitelinks(): array
    {
        return [
            ['text' => 'Gratis tekstvurdering', 'description' => 'Få tilbakemelding fra redaktør', 'url' => '/gratis-tekstvurdering'],
            ['text' => 'Se alle kurs', 'description' => 'Roman, krim, barnebok og mer', 'url' => '/course'],
            ['text' => 'Utgitte elever', 'description' => '200+ forfattere utgitt', 'url' => '/publishing'],
            ['text' => 'Om Forfatterskolen', 'description' => 'Norges største nettbaserte skriveskole', 'url' => '/contact-us'],
        ];
    }

    /**
     * Generer annonsetekster for Responsive Search Ad
     */
    public function generateAdTexts(array $data): array
    {
        $hostName = $data['host_name'] ?? 'Kristine S. Henningsen';
        $dateShort = $data['webinar_date_short'] ?? '';
        $time = $data['webinar_time'] ?? '20:00';
        $headlineShort = $data['ad_headline_short'] ?? 'Skriv bok!';

        return [
            'headlines' => [
                "Gratiswebinar: {$headlineShort}",
                'Gratis skrivekurs på nett',
                'Lær å skrive bok',
                "{$hostName} holder webinar",
                "Webinar {$dateShort}",
                'Meld deg på gratis',
                'Forfatterskolen',
                '200+ utgitte elever',
                'Skrivekurs med forfattere',
                'Nettbasert skrivekurs',
            ],
            'descriptions' => [
                $data['ad_description_1'] ?? "Gratis webinar med {$hostName}. {$dateShort} kl. {$time}. Meld deg på!",
                $data['ad_description_2'] ?? "Forfatterskolen har hjulpet 200+ elever bli utgitt. Gratis webinar — helt uforpliktende.",
                'Lær skrivehåndverket fra profesjonelle. Gratis og nettbasert.',
                "{$hostName} deler sine beste tips. Meld deg på — det koster ingenting!",
            ],
        ];
    }
}
