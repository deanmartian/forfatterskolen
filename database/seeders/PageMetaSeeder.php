<?php

namespace Database\Seeders;

use App\PageMeta;
use Illuminate\Database\Seeder;

class PageMetaSeeder extends Seeder
{
    /**
     * Seed the application's page meta data.
     */
    public function run(): void
    {
        $pages = [
            [
                'url' => 'https://www.forfatterskolen.no',
                'meta_title' => 'Forfatterskolen – Nettbasert skriveskole i Norge',
                'meta_description' => 'Forfatterskolen tilbyr nettbaserte skrivekurs, veiledning og ressurser som hjelper deg å utvikle historier og manus gjennom profesjonell støtte.',
            ],
            [
                'url' => 'https://www.forfatterskolen.no/blog',
                'meta_title' => 'Skrivetips og inspirasjon – Forfatterskolens blogg',
                'meta_description' => 'Les artikler med skrivetips, forfatterintervjuer og nyheter fra Forfatterskolen som inspirerer deg til å utvikle dine egne tekster og prosjekter.',
            ],
            [
                'url' => 'https://www.forfatterskolen.no/publishing',
                'meta_title' => 'Gi ut bok med støtte fra Forfatterskolens forlagstjenester',
                'meta_description' => 'Utforsk våre profesjonelle forlagstjenester som veileder deg gjennom hele prosessen fra manus til ferdig bok, med design, redaksjonell hjelp og publisering.',
            ],
            [
                'url' => 'https://www.forfatterskolen.no/gift-cards',
                'meta_title' => 'Gavekort på skrivekurs – inspirer en fremtidig forfatter',
                'meta_description' => 'Gi bort et gavekort på Forfatterskolens skrivekurs og motiver noen du kjenner til å starte sin egen skrivereise med veiledning og faglig støtte.',
            ],
            [
                'url' => 'https://www.forfatterskolen.no/coaching-timer',
                'meta_title' => 'Effektiv skrivetid med Forfatterskolens coachingtimer',
                'meta_description' => 'Bestill coachingtimer og få personlig veiledning som hjelper deg å prioritere skrivetid, strukturere prosjekter og nå målene dine som forfatter.',
            ],
            [
                'url' => 'https://www.forfatterskolen.no/children',
                'meta_title' => 'Skriveglede for barn – kreative kurs hos Forfatterskolen',
                'meta_description' => 'Oppdag engasjerende skrivekurs for barn som bygger fantasi, språk og selvtillit gjennom lekne oppgaver og inspirerende undervisning.',
            ],
            [
                'url' => 'https://www.forfatterskolen.no/support',
                'meta_title' => 'Få hjelp og svar – Forfatterskolens kundestøtte',
                'meta_description' => 'Finn svar på vanlige spørsmål og ta kontakt med vårt supportteam for hjelp med kurs, innlogging, betalinger eller tekniske utfordringer.',
            ],
        ];

        foreach ($pages as $page) {
            PageMeta::updateOrCreate(
                ['url' => $page['url']],
                ['meta_title' => $page['meta_title'], 'meta_description' => $page['meta_description'], 'meta_image' => $page['meta_image'] ?? null]
            );
        }
    }
}
