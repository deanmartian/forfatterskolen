<?php

namespace App\Console\Commands;

use App\FreeWebinar;
use Illuminate\Console\Command;

/**
 * Printer alle felt-verdier for Google Ads Motor-webinar-kampanjen
 * i et copy-paste-vennlig format. Brukes som midlertidig løsning
 * mens Google Ads Developer Token-søknaden er i "human review"
 * hos Google (kan ta 3-10 arbeidsdager).
 *
 * Når Developer Token er godkjent og full GoogleAdsService er
 * bygget, erstattes denne av direkte API-kall i launch:motor-webinar.
 *
 * Bruk:
 *   php artisan google-ads:print-motor-campaign
 *
 * Du setter så opp kampanjen manuelt i Google Ads → Campaigns →
 * + New campaign, og limer inn feltene etter hvert som du kommer
 * til dem i wizarden.
 */
class PrintGoogleAdsMotorCampaign extends Command
{
    protected $signature = 'google-ads:print-motor-campaign
                            {--webinar-id=95 : FreeWebinar ID}';

    protected $description = 'Print copy-paste-klare felt for Google Ads Motor-webinar-kampanjen';

    public function handle(): int
    {
        $webinar = FreeWebinar::find($this->option('webinar-id'));

        if (!$webinar) {
            $this->error('FreeWebinar #' . $this->option('webinar-id') . ' ikke funnet');
            return 1;
        }

        $landingPage = 'https://www.forfatterskolen.no/gratis-webinar/' . $webinar->id;
        $coursePage = 'https://www.forfatterskolen.no/course/121';

        $this->line('');
        $this->line('═══════════════════════════════════════════════════════════════');
        $this->line('  GOOGLE SEARCH — MOTOR WEBINAR');
        $this->line('═══════════════════════════════════════════════════════════════');
        $this->line('');

        // ============================================================
        // KAMPANJE-INNSTILLINGER
        // ============================================================
        $this->info('1. KAMPANJE-INNSTILLINGER');
        $this->line('');
        $this->line('  Objective:        Leads');
        $this->line('  Type:             Search');
        $this->line('  Campaign name:    Motor Webinar — Søk');
        $this->line('  Conversion goal:  Webinar Lead (opprettet tidligere)');
        $this->line('  Networks:         Kun Search Network (AV Display Network)');
        $this->line('  Locations:        Norge');
        $this->line('  Languages:        Norsk');
        $this->line('  Daily budget:     1 400 kr');
        $this->line('  Bidding:          Maximize conversions');
        $this->line('  Start date:       I dag');
        $this->line('  End date:         20. april 2026 23:59 (kursstart)');
        $this->line('');

        // ============================================================
        // KEYWORDS
        // ============================================================
        $this->info('2. KEYWORDS (copy-paste, én per linje)');
        $this->line('');
        $keywords = [
            // Bred match med +modifier
            '+skrive +roman',
            '+skrivekurs',
            '+bli +forfatter',
            '+hvordan +skrive +bok',
            '+romankurs',
            '+nettkurs +skriving',
            // Phrase match
            '"skrive roman"',
            '"skrivekurs på nett"',
            '"bli forfatter kurs"',
            '"nettbasert skrivekurs"',
            '"finn fortellingens motor"',
            // Exact match
            '[forfatterskolen]',
            '[romankurs]',
            '[skrive bok kurs]',
            '[kristine henningsen]',
        ];
        foreach ($keywords as $kw) {
            $this->line('  ' . $kw);
        }
        $this->line('');

        // ============================================================
        // NEGATIVE KEYWORDS
        // ============================================================
        $this->info('3. NEGATIVE KEYWORDS');
        $this->line('');
        $negatives = [
            'jobb', 'stilling', 'ansatt',
            'gratis bok', 'barnebok', 'barn',
            'noveller',
        ];
        foreach ($negatives as $neg) {
            $this->line('  ' . $neg);
        }
        $this->line('');

        // ============================================================
        // RSA — RESPONSIVE SEARCH AD
        // ============================================================
        $this->info('4. RESPONSIVE SEARCH AD #1 — "Stoppet opp"-vinkel');
        $this->line('');
        $this->line('  Final URL:  ' . $landingPage);
        $this->line('');

        $this->line('  Headlines (max 30 tegn, lim inn én per linje):');
        $headlines = [
            'Gratis Webinar 15. April',
            'Finn Fortellingens Motor',
            'Stoppet Opp I Skrivingen?',
            'Slik Får Du Skrevet Romanen',
            'Gratis · 60 Min · På Nett',
            'Med Rektor Kristine',
            'Konkret Metode · Helt Gratis',
            'Motor: Karakter & Konflikt',
            'Romankurs Starter 20. April',
            'Norges Største Skriveskole',
            'Webinar-Pris T.o.m. 20/4',
            'Meld Deg På Gratis',
        ];
        foreach ($headlines as $h) {
            $len = strlen($h);
            $this->line("    [{$len}/30] {$h}");
        }
        $this->line('');

        $this->line('  Descriptions (max 90 tegn, lim inn én per linje):');
        $descriptions = [
            'Gratis webinar onsdag 15. april kl 20:00. Kristine viser metoden for å finne motoren.',
            'Har du skrevet scener uten rød tråd? Lær den enkle modellen for romanens motor.',
            'Opptak sendes til alle påmeldte. Webinar-pris på Romankurs gjelder til 20. april.',
            'Forfatterskolen er Norges største nettbaserte skriveskole. 200+ utgitte elever.',
        ];
        foreach ($descriptions as $d) {
            $len = strlen($d);
            $this->line("    [{$len}/90] {$d}");
        }
        $this->line('');

        // ============================================================
        // SITELINKS
        // ============================================================
        $this->info('5. SITELINK EXTENSIONS (valgfritt men anbefalt)');
        $this->line('');
        $sitelinks = [
            ['Romankurs', $coursePage, 'Se pakker og priser', 'Oppstart 20. april'],
            ['Om Forfatterskolen', 'https://www.forfatterskolen.no/om-oss', '20 år med skriveundervisning', 'Norges største'],
            ['Suksesshistorier', 'https://www.forfatterskolen.no/historier', '200+ utgitte elever', 'Les hva de sier'],
            ['Kontakt', 'https://www.forfatterskolen.no/kontakt', 'Svarer innen 24 timer', 'post@forfatterskolen.no'],
        ];
        foreach ($sitelinks as $sl) {
            [$text, $url, $desc1, $desc2] = $sl;
            $this->line("  • Text: {$text}");
            $this->line("    URL: {$url}");
            $this->line("    Description 1: {$desc1}");
            $this->line("    Description 2: {$desc2}");
            $this->line('');
        }

        // ============================================================
        // CALLOUT EXTENSIONS
        // ============================================================
        $this->info('6. CALLOUT EXTENSIONS');
        $this->line('');
        $callouts = [
            'Gratis webinar',
            '14 dagers angrefrist',
            'Norges største skriveskole',
            'Opptak tilgjengelig',
            '200+ utgitte elever',
            'Mentormøter inkludert',
        ];
        foreach ($callouts as $c) {
            $this->line('  • ' . $c);
        }
        $this->line('');

        // ============================================================
        // SUMMARY
        // ============================================================
        $this->line('═══════════════════════════════════════════════════════════════');
        $this->info('SLIK SETTER DU OPP (~15 min):');
        $this->line('');
        $this->line('  1. Gå til https://ads.google.com → Campaigns → + New campaign');
        $this->line('  2. Velg "Leads" → "Search" → velg "Webinar Lead"-conversion');
        $this->line('  3. Fyll inn alle feltene over fra toppen og ned');
        $this->line('  4. Launch som PAUSET → review → aktiver');
        $this->line('');
        $this->line('Copy er designet per Sonnets plan og matchet mot Meta-kampanjene.');
        $this->line('');

        return 0;
    }
}
