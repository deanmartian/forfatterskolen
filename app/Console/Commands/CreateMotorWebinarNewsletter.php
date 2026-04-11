<?php

namespace App\Console\Commands;

use App\Models\Newsletter;
use Illuminate\Console\Command;

class CreateMotorWebinarNewsletter extends Command
{
    protected $signature = 'newsletter:create-motor-webinar';
    protected $description = 'Opprett nyhetsbrev-invitasjon for Motor-webinaret 15. april';

    public function handle(): int
    {
        $html = '<div style="max-width:600px;margin:0 auto;font-family:Georgia,serif;font-size:16px;line-height:1.7;color:#333;">'
            . '<p>Hei,</p>'
            . '<p>Jeg har snakket med mange som drømmer om å skrive en roman. Og det som går igjen er det samme, gang på gang:</p>'
            . '<p><em>&laquo;Jeg begynner. Jeg skriver noen scener. Og så stopper det opp.&raquo;</em></p>'
            . '<p>Det er ikke fordi de mangler talent. Det er fordi historien mangler en <strong>motor</strong>.</p>'
            . '<p><strong>Onsdag 15. april kl 20:00</strong> holder jeg et gratis webinar som handler om akkurat dette:</p>'
            . '<p><strong>Finn fortellingens motor</strong> &mdash; slik finner du ut hva boken din egentlig handler om, og hvordan du lager en konflikt som driver historien fremover.</p>'
            . '<p>På webinaret får du:</p>'
            . '<ul>'
            . '<li>&#9997;&#65039; En enkel forklaring på hva romanens motor er</li>'
            . '<li>&#128214; Hjelp til å finne ut hva boken din egentlig handler om</li>'
            . '<li>&#128161; Hvordan du lager en konflikt som driver historien fremover</li>'
            . '<li>&#9989; Konkrete spørsmål du kan stille manuset ditt med det samme</li>'
            . '<li>&#127873; En miniøvelse du tar med deg hjem og kan bruke med det samme</li>'
            . '</ul>'
            . '<p><strong>Detaljer:</strong><br>'
            . '&#128197; Onsdag 15. april kl 20:00<br>'
            . '&#9201;&#65039; Ca 60 minutter<br>'
            . '&#127909; Live på nett (opptak sendes til alle påmeldte)<br>'
            . '&#127386; Helt gratis</p>'
            . '<p style="text-align:center;margin:30px 0;">'
            . '<a href="https://www.forfatterskolen.no/gratis-webinar/95" style="background:#862736;color:#fff;padding:14px 32px;border-radius:6px;text-decoration:none;font-weight:bold;font-size:17px;">Reserver plassen din her &rarr;</a>'
            . '</p>'
            . '<p>Vi ses!</p>'
            . '<p>Ha en fin dag!<br>Mvh Kristine S. Henningsen<br>Rektor, Forfatterskolen</p>'
            . '<p><strong>PS:</strong> Webinar-deltakere får en spesialrabatt på <strong>Romankurs i gruppe</strong> (oppstart 20. april) &mdash; mer info på selve webinaret.</p>'
            . '</div>';

        $newsletter = Newsletter::create([
            'subject' => 'Hvorfor stopper romanen din opp?',
            'preview_text' => 'Gratis webinar onsdag 15. april kl 20:00 — Finn fortellingens motor',
            'body_html' => $html,
            'from_address' => 'post@nyhetsbrev.forfatterskolen.no',
            'from_name' => 'Forfatterskolen',
            'segment' => 'no_active_course',
            'status' => 'scheduled',
            'scheduled_at' => '2026-04-12 19:00:00',
        ]);

        $this->info("Nyhetsbrev opprettet! ID: {$newsletter->id}");
        $this->info("Emne: {$newsletter->subject}");
        $this->info("Segment: no_active_course (alle uten aktive elever)");
        $this->info("Planlagt: søndag 12. april kl 19:00");

        return 0;
    }
}
