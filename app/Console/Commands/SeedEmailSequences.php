<?php

namespace App\Console\Commands;

use App\Models\EmailSequence;
use App\Models\EmailSequenceStep;
use Illuminate\Console\Command;

class SeedEmailSequences extends Command
{
    protected $signature = 'email:seed-sequences';

    protected $description = 'Opprett standard e-postsekvenser (gratis webinar + Facebook lead)';

    public function handle(): int
    {
        $this->createWebinarSequence();
        $this->createFacebookLeadSequence();

        $this->info('E-postsekvenser opprettet!');

        return 0;
    }

    protected function createWebinarSequence(): void
    {
        $sequence = EmailSequence::firstOrCreate(
            ['trigger_event' => 'webinar_registration'],
            [
                'name' => 'Gratis webinar',
                'description' => 'Automatisk sekvens etter webinar-påmelding: bekreftelse, påminnelser og oppfølging',
                'from_type' => 'transactional',
                'is_active' => true,
            ]
        );

        $steps = [
            [
                'step_number' => 1,
                'subject' => '✅ Du er påmeldt: [webinar_tittel]',
                'body_html' => '<h1>Hei [fornavn]!</h1><p>Du er nå påmeldt webinaret <strong>[webinar_tittel]</strong>.</p><p>📅 Dato: [webinar_dato]<br>🕐 Tid: [webinar_tid]</p><p><a href="[join_lenke]" style="display:inline-block;background:#862736;color:#fff;padding:12px 24px;text-decoration:none;border-radius:4px;">Legg til i kalender</a></p><p>Vi gleder oss til å se deg!</p>',
                'delay_hours' => 0,
                'send_time' => null,
                'from_type' => 'transactional',
                'only_without_active_course' => false,
            ],
            [
                'step_number' => 2,
                'subject' => '📅 Påminnelse — [webinar_tittel] er i morgen',
                'body_html' => '<h1>Hei [fornavn]!</h1><p>I morgen er det klart for webinaret <strong>[webinar_tittel]</strong>.</p><p>🕐 Tid: [webinar_tid]</p><p><a href="[join_lenke]" style="display:inline-block;background:#862736;color:#fff;padding:12px 24px;text-decoration:none;border-radius:4px;">Her er din inngangslenke</a></p>',
                'delay_hours' => 0, // Beregnes dynamisk basert på webinar-dato
                'send_time' => '18:00',
                'from_type' => 'transactional',
                'only_without_active_course' => false,
            ],
            [
                'step_number' => 3,
                'subject' => '⏰ En time til — her er din inngangslenke',
                'body_html' => '<h1>Hei [fornavn]!</h1><p>Om en time starter <strong>[webinar_tittel]</strong>.</p><p><a href="[join_lenke]" style="display:inline-block;background:#862736;color:#fff;padding:12px 24px;text-decoration:none;border-radius:4px;">Bli med på webinaret →</a></p>',
                'delay_hours' => 0, // Beregnes dynamisk
                'send_time' => null,
                'from_type' => 'transactional',
                'only_without_active_course' => false,
            ],
            [
                'step_number' => 4,
                'subject' => '🎬 Se opptaket fra [webinar_tittel]',
                'body_html' => '<h1>Hei [fornavn]!</h1><p>Takk for at du deltok (eller ville delta) på <strong>[webinar_tittel]</strong>.</p><p>Her kan du se opptaket:</p><p><a href="[reprise_url]" style="display:inline-block;background:#862736;color:#fff;padding:12px 24px;text-decoration:none;border-radius:4px;">Se opptaket →</a></p>',
                'delay_hours' => 0, // Beregnes dynamisk (2 timer etter start)
                'send_time' => null,
                'from_type' => 'transactional',
                'only_without_active_course' => false,
            ],
            [
                'step_number' => 5,
                'subject' => '💡 Tips fra webinaret',
                'body_html' => '<h1>Hei [fornavn]!</h1><p>Har du tenkt mer på det vi snakket om i webinaret?</p><p>Her er noen tips til deg som vil komme i gang med skrivingen:</p><ul><li>Sett av fast skrivetid hver dag</li><li>Start med en kort tekst</li><li>Les mye innen sjangeren du vil skrive</li></ul><p><a href="[kurs_tilbud_url]" style="display:inline-block;background:#862736;color:#fff;padding:12px 24px;text-decoration:none;border-radius:4px;">Se våre kurs →</a></p>',
                'delay_hours' => 48,
                'send_time' => '10:00',
                'from_type' => 'newsletter',
                'only_without_active_course' => true,
            ],
            [
                'step_number' => 6,
                'subject' => '📚 Har du lyst å gå videre?',
                'body_html' => '<h1>Hei [fornavn]!</h1><p>Mange av deltakerne fra webinaret har allerede startet på Romankurset vårt.</p><p>Kurset gir deg:</p><ul><li>Personlig veiledning fra erfarne forfattere</li><li>Skriveoppgaver med tilbakemelding</li><li>Et støttende fellesskap</li></ul><p><a href="[kurs_tilbud_url]" style="display:inline-block;background:#862736;color:#fff;padding:12px 24px;text-decoration:none;border-radius:4px;">Les mer om Romankurset →</a></p>',
                'delay_hours' => 120,
                'send_time' => '10:00',
                'from_type' => 'newsletter',
                'only_without_active_course' => true,
            ],
            [
                'step_number' => 7,
                'subject' => '⏳ Siste sjanse — tilbud på Romankurset',
                'body_html' => '<h1>Hei [fornavn]!</h1><p>Vi ville bare minne deg på at tilbudet vi nevnte etter webinaret snart utløper.</p><p>Hvis du har lyst å lære mer om skriving og få personlig veiledning, er dette en fin mulighet.</p><p><a href="[kurs_tilbud_url]" style="display:inline-block;background:#862736;color:#fff;padding:12px 24px;text-decoration:none;border-radius:4px;">Se tilbudet →</a></p>',
                'delay_hours' => 192,
                'send_time' => '10:00',
                'from_type' => 'newsletter',
                'only_without_active_course' => true,
            ],
        ];

        foreach ($steps as $stepData) {
            EmailSequenceStep::firstOrCreate(
                ['sequence_id' => $sequence->id, 'step_number' => $stepData['step_number']],
                $stepData
            );
        }

        $this->info("Webinar-sekvens opprettet med {$sequence->steps()->count()} steg.");
    }

    protected function createFacebookLeadSequence(): void
    {
        $sequence = EmailSequence::firstOrCreate(
            ['trigger_event' => 'facebook_lead'],
            [
                'name' => 'Facebook Lead',
                'description' => 'Oppfølging av leads fra Facebook-annonser',
                'from_type' => 'newsletter',
                'is_active' => true,
            ]
        );

        $steps = [
            [
                'step_number' => 1,
                'subject' => 'Takk for din interesse — her er noe til deg',
                'body_html' => '<h1>Hei [fornavn]!</h1><p>Takk for at du viste interesse for Forfatterskolen.</p><p>Vi hjelper folk som drømmer om å skrive med kurs, veiledning og et støttende fellesskap.</p><p><a href="' . url('/') . '" style="display:inline-block;background:#862736;color:#fff;padding:12px 24px;text-decoration:none;border-radius:4px;">Besøk Forfatterskolen →</a></p>',
                'delay_hours' => 0,
                'send_time' => null,
                'from_type' => 'newsletter',
                'only_without_active_course' => false,
            ],
            [
                'step_number' => 2,
                'subject' => 'Tips til deg som vil skrive',
                'body_html' => '<h1>Hei [fornavn]!</h1><p>Her er tre tips som kan hjelpe deg i gang med skrivingen:</p><ol><li><strong>Skriv hver dag</strong> — selv 15 minutter gjør forskjell</li><li><strong>Les mye</strong> — gode forfattere er alltid gode lesere</li><li><strong>Ikke vær redd for å skrive dårlig</strong> — første utkast skal bare skrives</li></ol>',
                'delay_hours' => 24,
                'send_time' => '10:00',
                'from_type' => 'newsletter',
                'only_without_active_course' => false,
            ],
            [
                'step_number' => 3,
                'subject' => 'Gratis webinar — meld deg på her',
                'body_html' => '<h1>Hei [fornavn]!</h1><p>Vi holder jevnlig gratis webinarer om skriving og forfatterliv.</p><p>Meld deg på det neste webinaret og lær av erfarne forfattere — helt gratis!</p><p><a href="' . url('/') . '" style="display:inline-block;background:#862736;color:#fff;padding:12px 24px;text-decoration:none;border-radius:4px;">Se kommende webinarer →</a></p>',
                'delay_hours' => 72,
                'send_time' => '10:00',
                'from_type' => 'newsletter',
                'only_without_active_course' => false,
            ],
            [
                'step_number' => 4,
                'subject' => 'Tilbud på kurs — begrenset plasser',
                'body_html' => '<h1>Hei [fornavn]!</h1><p>Har du lyst å ta skrivingen til neste nivå?</p><p>Romankurset vårt gir deg personlig veiledning, skriveoppgaver med tilbakemelding, og et fellesskap av andre som skriver.</p><p><a href="[kurs_tilbud_url]" style="display:inline-block;background:#862736;color:#fff;padding:12px 24px;text-decoration:none;border-radius:4px;">Les mer om kurset →</a></p>',
                'delay_hours' => 168,
                'send_time' => '10:00',
                'from_type' => 'newsletter',
                'only_without_active_course' => true,
            ],
        ];

        foreach ($steps as $stepData) {
            EmailSequenceStep::firstOrCreate(
                ['sequence_id' => $sequence->id, 'step_number' => $stepData['step_number']],
                $stepData
            );
        }

        $this->info("Facebook Lead-sekvens opprettet med {$sequence->steps()->count()} steg.");
    }
}
