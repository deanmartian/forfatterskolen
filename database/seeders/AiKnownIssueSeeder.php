<?php

namespace Database\Seeders;

use App\Models\AiKnownIssue;
use Illuminate\Database\Seeder;

/**
 * Seeder for AI-kunnskap (kjente feil og workarounds som inbox-AI bruker).
 *
 * Kjøres slik:
 *   php artisan db:seed --class=AiKnownIssueSeeder
 *
 * Det er trygt å kjøre flere ganger — den oppretter ikke duplikater.
 * Sven Inge kan redigere/slette/legge til via admin/ai-knowledge etterpå.
 */
class AiKnownIssueSeeder extends Seeder
{
    public function run(): void
    {
        $issues = [
            [
                'title' => 'Vipps-betalingsbug ble fikset 08.04.2026 — eleven kan trygt prøve på nytt',
                'description' => 'Tidligere ble noen Vipps-bestillinger droppet pga en operator-presedens-bug i fallback-håndteringen. Dette ble fikset 08.04.2026. Hvis en elev rapporterer at de fikk en feil eller ble sendt tilbake til utgangspunktet ved Vipps-betaling før 08.04.2026, så er det dette som skjedde — be dem prøve på nytt nå.',
                'workaround' => 'Be eleven prøve Vipps-betalingen på nytt — bugen er fikset. Hvis det FORTSATT ikke fungerer, kan de prøve hard refresh (Cmd+Shift+R), bytte nettleser, eller velge Svea (faktura/avbetaling) som alternativ betalingsmetode i checkout.',
                'severity' => 'info',
                'category' => 'betaling',
            ],
            [
                'title' => 'Magisk innloggingslenke utløper etter 24 timer',
                'description' => 'Engangslenkene som sendes på e-post for passordfri innlogging er gyldige i 24 timer. Hvis eleven prøver å bruke en eldre lenke får de feilmelding.',
                'workaround' => 'Be eleven om å gå til /login og velge "Send meg en innloggingslenke" på nytt. En ny lenke kommer på e-post i løpet av sekunder.',
                'severity' => 'info',
                'category' => 'innlogging',
            ],
            [
                'title' => 'Store .docx-filer over 20 MB kan feile under opplasting',
                'description' => 'Manus med mange bilder eller kompleks formatering kan overstige opplastingsgrensen og feile midt i opplastingen, særlig på trege internettforbindelser.',
                'workaround' => 'Foreslå at eleven (1) komprimerer bilder i Word ved å gå til Fil → Komprimer bilder, eller (2) splitter manuset i flere kapitler/dokumenter, eller (3) lagrer som PDF i stedet for .docx.',
                'severity' => 'medium',
                'category' => 'manus',
            ],
            [
                'title' => 'Push-varsler virker ikke alltid på iPhone/iPad',
                'description' => 'iOS Safari støtter web push-varsler bare hvis nettsiden er lagt til på hjemskjermen som en PWA. Brukere som bruker forfatterskolen.no i vanlig Safari får ikke push.',
                'workaround' => 'Be iPhone-eleven om å åpne forfatterskolen.no i Safari, trykke på Del-ikonet (firkant med pil opp) og velge "Legg til på Hjem-skjermen". Etter dette må de åpne appen fra hjemskjermen og godkjenne varsler.',
                'severity' => 'low',
                'category' => 'varsler',
            ],
            [
                'title' => 'Webinar-opptak kan ta opptil 2 timer å bli tilgjengelig',
                'description' => 'BigMarker prosesserer webinar-opptak etter at sendingen er ferdig, og det kan ta 30 minutter til 2 timer før opptaket dukker opp i kursportalen.',
                'workaround' => 'Hvis en elev spør hvor opptaket er rett etter et webinar: forklar at det prosesseres og normalt er klart innen 2 timer. Hvis det har gått mer enn 4 timer, sjekk i admin under Webinarer.',
                'severity' => 'info',
                'category' => 'webinar',
            ],
            [
                'title' => 'E-post fra oss havner av og til i søppelpost',
                'description' => 'Spesielt på Hotmail/Outlook og Gmail kan vår e-post (post@forfatterskolen.no, ingen-svar@forfatterskolen.no) bli filtrert til søppelpost-mappen.',
                'workaround' => 'Be eleven sjekke søppelpost/spam-mappen. For permanent fiks: marker e-posten som "Ikke spam" og legg post@forfatterskolen.no til i kontaktlista. Tilby å sende viktig informasjon på nytt.',
                'severity' => 'medium',
                'category' => 'e-post',
            ],
            [
                'title' => 'Vipps eFaktura kan ta 1-2 dager å dukke opp i Vipps-appen',
                'description' => 'Når vi sender en eFaktura via Vipps, må den prosesseres av Vipps sitt system før den vises i elevens Vipps-app. Dette tar typisk 1-2 hverdager.',
                'workaround' => 'Forklar at fakturaen er sendt og kommer i Vipps-appen om kort tid. Hvis det haster, kan eleven betale via vanlig faktura med KID i mellomtiden — den ligger i kontoportalen under Mine kjøp.',
                'severity' => 'low',
                'category' => 'betaling',
            ],
            [
                'title' => 'Innlevering vises ikke alltid umiddelbart etter opplasting',
                'description' => 'Etter at en elev laster opp en oppgave/innlevering, kan det av og til ta noen sekunder før status oppdateres til "Levert" — særlig på treg forbindelse.',
                'workaround' => 'Be eleven laste siden på nytt (Cmd+R eller F5) etter et halvt minutt. Hvis det fortsatt står "Ikke levert", be dem prøve å laste opp på nytt — fila er nesten alltid lagret, det er bare visningen som henger.',
                'severity' => 'low',
                'category' => 'oppgaver',
            ],
            [
                'title' => 'Coaching-timer kan ikke dobbeltbookes — konflikt med eksisterende bookinger',
                'description' => 'Hvis en elev prøver å booke en coaching-time som allerede er tatt, eller som overlapper med en eksisterende booking, får de en feilmelding.',
                'workaround' => 'Be eleven gå til /account/coaching-timer og velge en annen tilgjengelig tid. Hvis ingen ledige tider passer, kan administrasjonen åpne flere slots for redaktøren.',
                'severity' => 'info',
                'category' => 'coaching',
            ],
            [
                'title' => 'Nettleserutvidelser (ad-blockere) kan blokkere Vipps og betalingsvinduer',
                'description' => 'Aggressive ad-blockere som uBlock Origin, AdGuard eller Brave Shields kan blokkere Vipps-popupen, Stripe-feltene eller hele betalingsflyten.',
                'workaround' => 'Be eleven slå av ad-blocker midlertidig for forfatterskolen.no, eller prøve i et privat/inkognito-vindu der utvidelser ofte er deaktivert. Et fungerende eksempel: i Chrome kan eleven klikke på utvidelsen og velge "Pause for denne siden".',
                'severity' => 'medium',
                'category' => 'betaling',
            ],
            [
                'title' => 'Vi har ikke en egen app — siden ER appen (PWA), URL avhenger av rolle',
                'description' => 'Brukere spør ofte hva appen heter eller hvor de laster den ned. Vi har INGEN separat app i App Store eller Google Play — sidene våre er Progressive Web Apps (PWA) som legges til hjemskjermen. VIKTIG: URL-en er forskjellig basert på rolle: elever bruker forfatterskolen.no, redaktører bruker editor.forfatterskolen.no, admin bruker admin.forfatterskolen.no. Sjekk ALLTID rollen før du gir instruksjon!',
                'workaround' => 'Sjekk rollen i elevdata først. (1) Hvis ELEV: be dem legge forfatterskolen.no til på hjemskjermen. (2) Hvis REDAKTØR: be dem legge editor.forfatterskolen.no til på hjemskjermen. (3) Hvis ADMIN: admin.forfatterskolen.no. iPhone (Safari): trykk Del-ikonet → "Legg til på Hjem-skjermen". Android (Chrome): tre prikker → "Legg til på startsiden". Etter at ikonet er på hjemskjermen, åpner de portalen fra det ikonet og kan da aktivere push-varsler.',
                'severity' => 'medium',
                'category' => 'varsler',
            ],
        ];

        // Slett gamle/utdaterte oppføringer som har fått nye titler
        AiKnownIssue::where('title', 'Vi har ikke en egen app — forfatterskolen.no ER appen (PWA)')->delete();
        AiKnownIssue::where('title', 'Vipps-betaling kan henge på Mac med Safari')->delete();

        foreach ($issues as $data) {
            AiKnownIssue::updateOrCreate(
                ['title' => $data['title']],
                array_merge($data, [
                    'status' => 'active',
                    'discovered_at' => now()->toDateString(),
                ])
            );
        }

        $this->command->info('AI-kunnskap: ' . count($issues) . ' kjente feil/workarounds er sådd inn.');
    }
}
