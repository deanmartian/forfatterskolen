<?php

namespace App\Console\Commands;

use App\Webinar;
use Illuminate\Console\Command;

/**
 * Engangs-kommando for å opprette webinaret "Finn fortellingens motor"
 * (15. april 2026 kl 20:00) koblet til Romankurs i gruppe (/course/121).
 *
 * Idempotent: hvis webinaret allerede finnes (samme tittel + startdato
 * + course_id), så opprettes det IKKE på nytt.
 *
 * Bruk:
 *   php artisan webinar:create-motor
 *
 * BigMarker-ID/link må fylles inn etterpå via admin-UI
 * (/admin/webinar → Rediger) når Kristine har satt opp selve webinaret
 * i BigMarker.
 */
class CreateMotorWebinar extends Command
{
    protected $signature = 'webinar:create-motor';

    protected $description = 'Oppretter webinaret "Finn fortellingens motor" 15.04.2026 kl 20:00 for Romankurs (121)';

    public function handle(): int
    {
        $courseId = 121;
        $title = 'Finn fortellingens motor';
        $startDate = '2026-04-15 20:00:00';
        $host = 'Kristine S. Henningsen';

        // Idempotency: ikke opprett på nytt hvis det allerede finnes
        $existing = Webinar::where('course_id', $courseId)
            ->where('title', $title)
            ->where('start_date', $startDate)
            ->first();

        if ($existing) {
            $this->warn('Webinaret finnes allerede med ID ' . $existing->id);
            $this->info('Tittel:  ' . $existing->title);
            $this->info('Dato:    ' . $existing->start_date);
            $this->info('Kurs:    ' . $existing->course_id);
            $this->info('Host:    ' . $existing->host);
            $this->info('Link:    ' . ($existing->link ?: '(tom)'));
            $this->info('');
            $this->info('Bruk admin-UI (/admin/webinar) for å redigere.');
            return 0;
        }

        $description = <<<'HTML'
<p><strong>Tema:</strong> De fleste som slutter å skrive, gjør det ikke fordi de mangler ideer – de gjør det fordi de ikke vet hva historien egentlig handler om. I dette gratis webinaret får du en enkel modell for å finne romanens motor: hovedperson, konflikt og premiss. Med motor i historien skriver du videre – uten den stopper det opp.</p>

<p><strong>Hva du lærer:</strong></p>
<ul>
<li>En enkel forklaring på hva romanens motor er</li>
<li>Hjelp til å finne ut hva boken din egentlig handler om</li>
<li>Hvordan du lager en konflikt som driver historien fremover</li>
<li>Konkrete spørsmål du kan stille manuset ditt med det samme</li>
<li>En miniøvelse du tar med deg hjem og kan bruke med det samme</li>
</ul>

<p><strong>Hvem dette er for:</strong></p>
<ul>
<li>Deg som vil skrive roman, men opplever at det stopper opp</li>
<li>Deg som har begynt på flere manus uten å bli ferdig</li>
<li>Deg som skriver scener, men mangler rød tråd</li>
<li>Deg som ikke helt vet hva boken din handler om</li>
<li>Deg som drømmer om å gi ut bok – og trenger et dytt i riktig retning</li>
</ul>
HTML;

        $webinar = new Webinar;
        $webinar->course_id = $courseId;
        $webinar->title = $title;
        $webinar->description = $description;
        $webinar->host = $host;
        $webinar->start_date = $startDate;
        $webinar->link = ''; // Fylles inn fra admin-UI når BigMarker-ID er klar
        $webinar->set_as_replay = 0;
        $webinar->status = 1;
        $webinar->save();

        $this->info('✓ Webinar opprettet');
        $this->info('  ID:      ' . $webinar->id);
        $this->info('  Tittel:  ' . $webinar->title);
        $this->info('  Dato:    ' . $webinar->start_date);
        $this->info('  Kurs:    ' . $webinar->course_id);
        $this->info('  Host:    ' . $webinar->host);
        $this->info('  Link:    (tom — legg inn BigMarker-ID via admin-UI)');
        $this->info('');
        $this->info('Neste steg:');
        $this->info('  1. Opprett webinaret i BigMarker (din BigMarker-konto)');
        $this->info('  2. Kopier conference-ID (12-tegns hex, f.eks. e92ff2abfe03)');
        $this->info('  3. Logg inn på admin.forfatterskolen.no/webinar/' . $webinar->id . '/edit');
        $this->info('  4. Lim inn ID-en i Link-feltet og lagre');

        return 0;
    }
}
