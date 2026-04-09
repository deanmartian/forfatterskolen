<?php

namespace App\Console\Commands;

use App\Models\EmailSequence;
use App\Models\EmailSequenceStep;
use Illuminate\Console\Command;

/**
 * Seeder for email-sekvensen til "Finn fortellingens motor"-webinaret.
 *
 * Oppretter en dedikert EmailSequence med trigger_event
 * 'motor_webinar_registration' og 6 steg som matcher Sonnets plan:
 *
 *   Steg 1: Bekreftelse (umiddelbart ved påmelding)
 *   Steg 2: Reminder 1 dag før webinar (14. april 17:00)
 *   Steg 3: Reminder 1 time før webinar (15. april 19:00)
 *   Steg 4: Opptak-mail (15. april 22:00)
 *   Steg 5: Oppfølging med MOTOR5000 (17. april 10:00)
 *   Steg 6: 2 dager igjen til deadline (18. april 10:00)
 *   Steg 7: Siste sjanse (20. april 10:00)
 *
 * Idempotent — kan kjøres flere ganger, oppdaterer eksisterende
 * steg i stedet for å lage duplikater.
 *
 * NB: For å faktisk TRIGGE denne sekvensen når noen melder seg på
 * Motor-webinaret, må HomeController::freeWebinarRegister og
 * FetchFacebookLeads oppdateres til å sjekke webinar-ID og fyre
 * 'motor_webinar_registration' i stedet for 'webinar_registration'
 * når det gjelder Motor-webinaret. Se dokumentasjon i
 * docs/ad-kampanjer/motor-webinar-email-sekvens.md.
 *
 * Bruk:
 *   php artisan seed:motor-emails
 */
class SeedMotorWebinarEmails extends Command
{
    protected $signature = 'seed:motor-emails';

    protected $description = 'Seeder Motor-webinar email-sekvens (6 mailer med MOTOR5000-rabatt)';

    public function handle(): int
    {
        $sequence = EmailSequence::firstOrCreate(
            ['trigger_event' => 'motor_webinar_registration'],
            [
                'name' => 'Motor Webinar — Finn fortellingens motor',
                'description' => 'Komplett email-sekvens for Motor-webinaret 15.04.2026 med MOTOR5000-rabatt til 20. april',
                'from_type' => 'transactional',
                'is_active' => true,
            ]
        );

        $this->info("Sequence: #{$sequence->id} ({$sequence->trigger_event})");

        $steps = [
            [
                'step_number' => 1,
                'subject' => '✅ Du er påmeldt: Finn fortellingens motor',
                'body_html' => $this->buildStep1Html(),
                'delay_hours' => 0,
                'send_time' => null,
                'from_type' => 'transactional',
                'only_without_active_course' => false,
            ],
            [
                'step_number' => 2,
                'subject' => '📅 I morgen kl 20:00 — webinaret ditt er klart',
                'body_html' => $this->buildStep2Html(),
                'delay_hours' => 0, // Beregnes dynamisk av EmailAutomationService
                'send_time' => '17:00',
                'from_type' => 'transactional',
                'only_without_active_course' => false,
            ],
            [
                'step_number' => 3,
                'subject' => '⏰ Én time til vi starter',
                'body_html' => $this->buildStep3Html(),
                'delay_hours' => 0, // Beregnes dynamisk: 1 time før webinar-start
                'send_time' => null,
                'from_type' => 'transactional',
                'only_without_active_course' => false,
            ],
            [
                'step_number' => 4,
                'subject' => '🎬 Se opptaket: Finn fortellingens motor',
                'body_html' => $this->buildStep4Html(),
                'delay_hours' => 0, // Beregnes dynamisk: 2 timer etter webinar-start
                'send_time' => null,
                'from_type' => 'transactional',
                'only_without_active_course' => false,
            ],
            [
                'step_number' => 5,
                'subject' => 'Slik får du 5 000 kr avslag på Romankurs (utløper søndag)',
                'body_html' => $this->buildStep5Html(),
                'delay_hours' => 48, // 2 dager etter registrering
                'send_time' => '10:00',
                'from_type' => 'newsletter',
                'only_without_active_course' => true,
            ],
            [
                'step_number' => 6,
                'subject' => '2 dager igjen — har du bestemt deg? 📖',
                'body_html' => $this->buildStep6Html(),
                'delay_hours' => 72, // 3 dager etter registrering
                'send_time' => '10:00',
                'from_type' => 'newsletter',
                'only_without_active_course' => true,
            ],
            [
                'step_number' => 7,
                'subject' => 'I dag — kursstart og siste frist for rabatten ⏰',
                'body_html' => $this->buildStep7Html(),
                'delay_hours' => 120, // 5 dager etter registrering (ca 20. april)
                'send_time' => '08:00',
                'from_type' => 'newsletter',
                'only_without_active_course' => true,
            ],
        ];

        foreach ($steps as $stepData) {
            $step = EmailSequenceStep::updateOrCreate(
                ['sequence_id' => $sequence->id, 'step_number' => $stepData['step_number']],
                $stepData
            );
            $this->line("  Steg {$step->step_number}: {$step->subject}");
        }

        $this->info('');
        $this->info('✓ ' . $sequence->steps()->count() . ' steg seedet for Motor-webinaret');
        $this->info('');
        $this->info('VIKTIG: For at denne sekvensen skal fyre automatisk ved webinar-');
        $this->info('påmelding, må HomeController::freeWebinarRegister og');
        $this->info('FetchFacebookLeads sjekke webinar-ID og fyre trigger');
        $this->info('"motor_webinar_registration" i stedet for "webinar_registration"');
        $this->info('når det gjelder Motor-webinaret. Dette gjøres automatisk av');
        $this->info('launch:motor-webinar-kommandoen.');

        return 0;
    }

    private function buildStep1Html(): string
    {
        return <<<'HTML'
<h1>Hei [fornavn]! 📖</h1>

<p>Du er nå påmeldt gratiswebinaret <strong>Finn fortellingens motor</strong>.</p>

<p>📅 <strong>Dato:</strong> Onsdag 15. april<br>
🕐 <strong>Tid:</strong> Kl 20:00 (ca 60 minutter)<br>
🎥 <strong>Hvor:</strong> Live på nett</p>

<p>Du trenger ikke forberede noe — bare ha klar en kopp kaffe (eller te) og kanskje et notatblokk. Jeg skal dele en miniøvelse du kan bruke på manuset ditt med det samme.</p>

<p><a href="[join_lenke]" style="display:inline-block;background:#862736;color:#fff;padding:14px 28px;text-decoration:none;border-radius:6px;font-weight:600;">Din inngangslenke →</a></p>

<p>Jeg sender deg en påminnelse dagen før og en siste påminnelse én time før vi starter.</p>

<p>Gleder meg til å se deg!<br>
— Kristine</p>

<p style="color:#888;font-size:13px;"><strong>PS:</strong> Hvis du ikke kan være med live — ikke bekymre deg. Opptaket sendes til alle påmeldte samme kveld. 🎥</p>
HTML;
    }

    private function buildStep2Html(): string
    {
        return <<<'HTML'
<h1>Hei [fornavn]!</h1>

<p>Bare en kort påminnelse: <strong>i morgen kveld kl 20:00</strong> ses vi på webinaret.</p>

<p>✍️ <strong>Finn fortellingens motor</strong><br>
🗓️ Onsdag 15. april kl 20:00</p>

<p>Du trenger ikke forberede noe — bare ha klar en kopp kaffe og kanskje et notatblokk.</p>

<p><a href="[join_lenke]" style="display:inline-block;background:#862736;color:#fff;padding:14px 28px;text-decoration:none;border-radius:6px;font-weight:600;">Din plass i webinaret →</a></p>

<p>Jeg sender deg en siste påminnelse én time før vi starter.</p>

<p>Vi ses i morgen!<br>
— Kristine</p>

<p style="color:#888;font-size:13px;"><strong>PS:</strong> Opptaket sendes til alle påmeldte samme kveld. 🎥</p>
HTML;
    }

    private function buildStep3Html(): string
    {
        return <<<'HTML'
<h1>Hei [fornavn]!</h1>

<p>Webinaret starter om <strong>én time</strong>.</p>

<p>🎙️ <strong>Finn fortellingens motor</strong><br>
🗓️ Onsdag 15. april kl 20:00</p>

<p><a href="[join_lenke]" style="display:inline-block;background:#862736;color:#fff;padding:14px 28px;text-decoration:none;border-radius:6px;font-weight:600;">Bli med her →</a></p>

<p>Anbefalt: Logg inn 5 minutter før, så du er klar når vi trykker "live".</p>

<p>Vi ses om én time!<br>
— Kristine</p>
HTML;
    }

    private function buildStep4Html(): string
    {
        return <<<'HTML'
<h1>Hei [fornavn]!</h1>

<p>Takk for at du var med på webinaret i kveld — eller at du ville delta!</p>

<p>Her kan du se opptaket når det passer deg:</p>

<p><a href="[reprise_url]" style="display:inline-block;background:#862736;color:#fff;padding:14px 28px;text-decoration:none;border-radius:6px;font-weight:600;">🎬 Se opptaket →</a></p>

<p>Husk øvelsen vi gjorde — den er første skritt mot å finne romanens motor.</p>

<p>I morgen sender jeg deg mer info om hvordan du kan gå videre med manuset ditt. Hold øye med innboksen. 📖</p>

<p>Ha en fin kveld!<br>
— Kristine</p>
HTML;
    }

    private function buildStep5Html(): string
    {
        return <<<'HTML'
<h1>Hei [fornavn]!</h1>

<p>Takk for at du var med på webinaret — eller at du har sett opptaket! 📖</p>

<p>Som jeg nevnte, så får webinar-deltakere en <strong>spesialrabatt</strong> på <strong>Romankurs i gruppe</strong> som starter <strong>søndag 20. april</strong>.</p>

<h3>Webinar-rabatten:</h3>

<table style="border-collapse:collapse;width:100%;max-width:500px;">
  <tr style="background:#f9edef;">
    <th style="padding:10px;border:1px solid #ddd;text-align:left;">Pakke</th>
    <th style="padding:10px;border:1px solid #ddd;text-align:right;">Ordinær</th>
    <th style="padding:10px;border:1px solid #ddd;text-align:right;">Webinar-pris</th>
    <th style="padding:10px;border:1px solid #ddd;text-align:right;">Du sparer</th>
  </tr>
  <tr>
    <td style="padding:10px;border:1px solid #ddd;"><strong>Basic</strong></td>
    <td style="padding:10px;border:1px solid #ddd;text-align:right;">10 900 kr</td>
    <td style="padding:10px;border:1px solid #ddd;text-align:right;">9 900 kr</td>
    <td style="padding:10px;border:1px solid #ddd;text-align:right;">1 000 kr</td>
  </tr>
  <tr style="background:#fafafa;">
    <td style="padding:10px;border:1px solid #ddd;"><strong>Standard</strong></td>
    <td style="padding:10px;border:1px solid #ddd;text-align:right;">13 900 kr</td>
    <td style="padding:10px;border:1px solid #ddd;text-align:right;">8 900 kr</td>
    <td style="padding:10px;border:1px solid #ddd;text-align:right;"><strong>5 000 kr</strong></td>
  </tr>
  <tr>
    <td style="padding:10px;border:1px solid #ddd;"><strong>Pro</strong></td>
    <td style="padding:10px;border:1px solid #ddd;text-align:right;">18 900 kr</td>
    <td style="padding:10px;border:1px solid #ddd;text-align:right;">13 900 kr</td>
    <td style="padding:10px;border:1px solid #ddd;text-align:right;"><strong>5 000 kr</strong></td>
  </tr>
</table>

<p><strong>Bruk kode <code style="background:#f9edef;padding:4px 8px;border-radius:4px;color:#862736;">MOTOR5000</code> i kassen.</strong></p>

<p>Rabatten gjelder <strong>kun til og med søndag 20. april kl 23:59</strong> (samme dag som kursstart).</p>

<p>På Romankurs i gruppe får du:</p>
<ul>
<li>✅ 10 kursmoduler med live webinarer</li>
<li>✅ Profesjonell tilbakemelding på manuset ditt</li>
<li>✅ Mentormøter med anerkjente forfattere</li>
<li>✅ Et fellesskap med andre som skriver</li>
<li>✅ 14 dagers angrefrist — ingen risiko</li>
</ul>

<p><a href="[kurs_tilbud_url]" style="display:inline-block;background:#862736;color:#fff;padding:14px 28px;text-decoration:none;border-radius:6px;font-weight:600;">Se pakker og meld deg på →</a></p>

<p>Ha en fin dag!<br>
— Kristine</p>

<p style="color:#888;font-size:13px;"><strong>PS:</strong> Hvis du har spørsmål før du bestemmer deg, svar på denne mailen — jeg leser alle svar selv.</p>
HTML;
    }

    private function buildStep6Html(): string
    {
        return <<<'HTML'
<h1>Hei [fornavn]!</h1>

<p>To dager igjen til <strong>søndag 20. april</strong> — både kursstart og siste frist for webinar-rabatten MOTOR5000.</p>

<p>Jeg vet det er en stor beslutning å melde seg på et kurs som går over flere måneder. Så her er det jeg pleier å spørre folk om når de står og vakler:</p>

<p><strong>1. Har du tid til å skrive 2-3 timer i uka?</strong><br>
Det er alt som trengs. Du trenger ikke slutte i jobben eller flytte hjemmefra.</p>

<p><strong>2. Er du villig til å dele teksten din med andre?</strong><br>
Det er der magien skjer. Å få (og gi) tilbakemelding er det som flytter manus framover.</p>

<p><strong>3. Har du tålmodighet til å gå gjennom flere runder?</strong><br>
En roman blir ikke ferdig på første utkast. Det visste du allerede.</p>

<p>Hvis du svarer ja på alle tre — dette er kurset for deg.</p>

<p><a href="[kurs_tilbud_url]" style="display:inline-block;background:#862736;color:#fff;padding:14px 28px;text-decoration:none;border-radius:6px;font-weight:600;">Se pakker og meld deg på →</a></p>

<p>Bruk <strong>MOTOR5000</strong> i kassen for rabatten.</p>

<p>Ha en fin dag!<br>
— Kristine</p>

<p style="color:#888;font-size:13px;"><strong>PS:</strong> 14 dagers angrefrist betyr at du kan ombestemme deg etter kursstart. Ingen risiko.</p>
HTML;
    }

    private function buildStep7Html(): string
    {
        return <<<'HTML'
<h1>Hei [fornavn]!</h1>

<p>Kursstart i dag. Rabatten utløper ved midnatt.</p>

<p><strong>Romankurs i gruppe</strong> starter <strong>i dag kl 20:00</strong>. Dette er den siste muligheten til å være med første runde, og siste dag for <strong>MOTOR5000</strong>-rabatten.</p>

<ul>
<li>10 kursmoduler</li>
<li>Profesjonell tilbakemelding på manuset ditt</li>
<li>Mentormøter med Maja Lunde, Tom Egeland og flere</li>
<li>Opptil 5 000 kr i rabatt med koden <strong>MOTOR5000</strong></li>
<li>14 dagers angrefrist</li>
</ul>

<p><a href="[kurs_tilbud_url]" style="display:inline-block;background:#862736;color:#fff;padding:14px 28px;text-decoration:none;border-radius:6px;font-weight:600;">Meld deg på her før midnatt →</a></p>

<p>Bruk <strong>MOTOR5000</strong> i kassen.</p>

<p>Vi heier på deg.</p>

<p>— Kristine</p>

<p style="color:#888;font-size:13px;"><strong>PS:</strong> Hvis du har tenkt på dette lenge — det er i kveld det skjer. Romanen din venter. 📖</p>
HTML;
    }
}
