<?php

namespace Database\Seeders;

use App\MarketingPlan;
use Illuminate\Database\Seeder;

class MarketingPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Før bokskrivingen begynner',
                'questions' => [
                    [
                        'main_question' => 'Dialog med forfatter (kan være deg selv!)',
                        'sub_question' => ['Hva ønsker du å formidle med boken?', 'Hvem er leseren?', 'Hva skal leseren oppnå med boken?'],
                    ],
                    [
                        'main_question' => 'Er det en personlig historie som kan brukes når boken lanseres?',
                    ],
                    [
                        'main_question' => 'Avklare',
                        'sub_question' => ['Hva det er forventet at forfatter skal gjøre?', 'Hva forfatter forventer at forlag skal gjøre?'],
                    ],
                ],
            ],

            [
                'name' => 'Seks måneder før lansering',
                'questions' => [
                    [
                        'main_question' => 'Begynne å bygge e-postliste',
                    ],

                    [
                        'main_question' => 'Informere eksisterende kunder om at boken kommer',
                    ],

                    [
                        'main_question' => 'Finne aktuelle samarbeidspartnere',
                    ],

                    [
                        'main_question' => 'Informere bokkjeder (innkjøpere) personlig i god tid',
                    ],
                ],
            ],

            [
                'name' => 'Tre maneder før',
                'questions' => [
                    [
                        'main_question' => 'Lage liste over folk som potensielt kan hjelpe til med å promotere Boken',
                    ],
                ],
            ],

            [
                'name' => 'To måneder før',
                'questions' => [
                    [
                        'main_question' => 'Kontakte aktuelle journalister og magasine',
                        'sub_question' => ['Lage pressemappe (med bilder, utdrag, cover)'],
                    ],

                    [
                        'main_question' => 'Legge ut utdrag (tre første kapitler) gratis på nett i bytte mot e-postadresse',
                    ],

                    [
                        'main_question' => 'Anmelder-eksemplarer til bloggere/innflytelsespersoner/journalister',
                    ],

                    [
                        'main_question' => 'Teaservideo/trailer for boken',
                    ],
                ],
            ],

            [
                'name' => 'En måned før',
                'questions' => [
                    [
                        'main_question' => 'Starte forhåndssalg',
                        'sub_question' => ['Gjøre boken tilgjengelig I nettbutikk'],
                    ],

                    [
                        'main_question' => 'Kontakte potensielle forhandlere utover bokhandlere',
                    ],
                ],
            ],

            [
                'name' => 'To uker før:',
                'questions' => [
                    [
                        'main_question' => 'Bokhandlere: Sende info på e-post, tilby leseeksemplar',
                    ],

                    [
                        'main_question' => 'Begynne å sende informasjon til e-postliste',
                    ],

                    [
                        'main_question' => 'Kontakte aktuelle aviser, fagblader med mer',
                        'sub_question' => ['Kontakte God morgen Norge'],
                    ],
                ],
            ],

            [
                'name' => 'En uke før',
                'questions' => [
                    [
                        'main_question' => 'Gjesteinnelegg og intervjuer på andre blogger',
                    ],
                ],
            ],

            [
                'name' => 'Dagen før lansering',
                'questions' => [
                    [
                        'main_question' => 'Sjekke at alle systemer virker',
                    ],

                    [
                        'main_question' => 'Gjøre testbestilling, dobbeltsjekke alt',
                    ],

                    [
                        'main_question' => 'Gjøre klar Facebook- og Google-annonser',
                    ],

                    [
                        'main_question' => 'Sette opp autoresponder-rekke til kjøpere',
                        'sub_question' => [
                            'Takk for bestilling',
                            'Dele ekstramateriale',
                            'Tips en venn?',
                            'Få tilbakemeldinger ',
                        ],
                    ],
                ],
            ],

            [
                'name' => 'LANSERING',
                'questions' => [
                    [
                        'main_question' => 'Publisere nyheten på alle kanaler',
                        'sub_question' => [
                            'Webside',
                            'Facebook',
                            'Instagram',
                        ],
                    ],

                    [
                        'main_question' => 'Sende siste e-post til e-postliste',
                        'sub_question' => [
                            'Informere om antall forhåndssolgte',
                            'Oppfordre til å kjope boken',
                        ],
                    ],
                ],
            ],

            [
                'name' => 'Uken etter lansering',
                'questions' => [
                    [
                        'main_question' => 'Følge opp media',
                    ],

                    [
                        'main_question' => 'Følge opp kunder',
                    ],

                    [
                        'main_question' => 'Sjekke effekten av annonser',
                    ],

                    [
                        'main_question' => 'Lage relevant automatisering',
                        'sub_question' => ['Merslag? Kunder som har kjøpt ander bøker, der denne kan være interessant?'],
                    ],
                ],
            ],

            [
                'name' => 'Måneden etter lansering',
                'questions' => [
                    [
                        'main_question' => 'Evaluere lanseringen',
                    ],

                    [
                        'main_question' => 'Oppdater markedsplan',
                    ],

                    [
                        'main_question' => 'Oppdatere/lage nye annonser',
                    ],
                ],
            ],
        ];

        foreach ($plans as $plan) {
            $marketingPlan = MarketingPlan::create([
                'name' => $plan['name'],
            ]);

            foreach ($plan['questions'] as $question) {
                $marketingPlan->questions()->create([
                    'main_question' => $question['main_question'],
                    'sub_question' => isset($question['sub_question']) ? json_encode($question['sub_question'], JSON_UNESCAPED_UNICODE) : null,
                ]);
            }
        }
    }
}
