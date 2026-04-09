<?php

namespace App\Console\Commands;

use App\FreeWebinar;
use Illuminate\Console\Command;

/**
 * Engangs-kommando for å opprette gratiswebinaret
 * "Finn fortellingens motor" (15. april 2026 kl 20:00).
 *
 * Dette er et STANDALONE gratiswebinar (FreeWebinar-modellen) —
 * ikke knyttet til et spesifikt kurs via course_id. Formålet er
 * å få webinar-deltakere til å kjøpe Romankurs (/course/121) i
 * etterkant via MOTOR5000-rabattkoden.
 *
 * Feltene følger briefen fra CLAUDE-CODE-webinar-motor-launch.md
 * 1:1 og mapper til free_webinars-tabellen:
 *   TITTEL         → title
 *   TEMA           → description (HTML)
 *   STARTDATO      → start_date
 *   LÆRINGSPUNKTER → learning_points (én per linje)
 *   MÅLGRUPPE      → target_audience (én per linje)
 *   GOTOWEBINAR ID → gtwebinar_id (tom — Sven fyller inn via admin)
 *   REPRISE-URL    → replay_url (tom — fylles inn etter webinaret)
 *
 * Bruk:
 *   php artisan webinar:create-motor
 *
 * Idempotent: hvis en FreeWebinar med samme tittel + startdato
 * allerede finnes, opprettes det ikke på nytt.
 */
class CreateMotorWebinar extends Command
{
    protected $signature = 'webinar:create-motor';

    protected $description = 'Oppretter gratiswebinaret "Finn fortellingens motor" 15.04.2026 kl 20:00 (FreeWebinar)';

    public function handle(): int
    {
        $title = 'Finn fortellingens motor';
        $startDate = '2026-04-15 20:00:00';

        // Idempotency — ikke opprett på nytt hvis det allerede finnes
        $existing = FreeWebinar::where('title', $title)
            ->where('start_date', $startDate)
            ->first();

        if ($existing) {
            $this->warn('Gratiswebinaret finnes allerede med ID ' . $existing->id);
            $this->info('Tittel:       ' . $existing->title);
            $this->info('Dato:         ' . $existing->start_date);
            $this->info('BigMarker ID: ' . ($existing->gtwebinar_id ?: '(tom)'));
            $this->info('Reprise:      ' . ($existing->replay_url ?: '(tom)'));
            $this->info('');
            $this->info('Rediger via admin-UI: /admin/free-course → Webinarer');
            return 0;
        }

        // TEMA fra briefen — vises som hoved-beskrivelse
        $description = <<<'HTML'
<p>De fleste som slutter å skrive, gjør det ikke fordi de mangler ideer – de gjør det fordi de ikke vet hva historien egentlig handler om. I dette gratis webinaret får du en enkel modell for å finne romanens motor: hovedperson, konflikt og premiss. Med motor i historien skriver du videre – uten den stopper det opp.</p>
HTML;

        // LÆRINGSPUNKTER fra briefen (én per linje)
        $learningPoints = <<<'TEXT'
En enkel forklaring på hva romanens motor er
Hjelp til å finne ut hva boken din egentlig handler om
Hvordan du lager en konflikt som driver historien fremover
Konkrete spørsmål du kan stille manuset ditt med det samme
En miniøvelse du tar med deg hjem og kan bruke med det samme
TEXT;

        // MÅLGRUPPE fra briefen (én per linje)
        $targetAudience = <<<'TEXT'
Vil skrive roman, men opplever at det stopper opp
Har begynt på flere manus uten å bli ferdig
Skriver scener, men mangler rød tråd
Vet ikke helt hva boken sin handler om
Drømmer om å gi ut bok – og trenger et dytt i riktig retning
TEXT;

        $webinar = new FreeWebinar;
        $webinar->title = $title;
        $webinar->description = $description;
        $webinar->learning_points = $learningPoints;
        $webinar->target_audience = $targetAudience;
        $webinar->start_date = $startDate;
        $webinar->gtwebinar_id = '';   // Fylles inn fra admin når BigMarker-ID er klar
        $webinar->replay_url = '';     // Fylles inn etter webinaret
        $webinar->save();

        $this->info('');
        $this->info('✓ Gratiswebinar opprettet');
        $this->info('  ID:           ' . $webinar->id);
        $this->info('  Tittel:       ' . $webinar->title);
        $this->info('  Dato:         ' . $webinar->start_date);
        $this->info('  Tema:         ✓ (HTML)');
        $this->info('  Læringspunkter: ✓ (5 punkter)');
        $this->info('  Målgruppe:    ✓ (5 punkter)');
        $this->info('  BigMarker ID: (tom — legg inn via admin)');
        $this->info('  Reprise-URL:  (tom — legg inn etter webinaret)');
        $this->info('');
        $this->info('Neste steg:');
        $this->info('  1. Opprett konferansen i BigMarker');
        $this->info('  2. Kopier 12-tegns hex conference-ID');
        $this->info('  3. Gå til /admin/free-course → Webinarer → Rediger');
        $this->info('  4. Lim inn ID i "GOTOWEBINAR ID"-feltet og lagre');
        $this->info('');
        $this->info('Offentlig URL etter lansering:');
        $this->info('  https://www.forfatterskolen.no/gratis-webinar/' . $webinar->id);
        $this->info('');

        return 0;
    }
}
