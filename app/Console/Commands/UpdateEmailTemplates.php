<?php

namespace App\Console\Commands;

use App\EmailTemplate;
use Illuminate\Console\Command;

class UpdateEmailTemplates extends Command
{
    protected $signature = 'emails:update-templates {--dry-run : Vis endringer uten å lagre}';

    protected $description = 'Oppdater alle e-postmaler med ny tone og innhold';

    public function handle(): int
    {
        $templates = $this->getTemplates();

        $updated = 0;
        $created = 0;
        $skipped = 0;

        foreach ($templates as $name => $data) {
            $existing = EmailTemplate::where('page_name', $name)->first();

            if ($existing) {
                if ($this->option('dry-run')) {
                    $this->info("[DRY-RUN] Ville oppdatert: {$name}");
                    $this->line("  Gammelt emne: {$existing->subject}");
                    $this->line("  Nytt emne:    {$data['subject']}");
                    $updated++;
                    continue;
                }

                $existing->update([
                    'subject' => $data['subject'],
                    'from_email' => $data['from_email'],
                    'email_content' => $data['email_content'],
                ]);
                $this->info("✅ Oppdatert: {$name}");
                $updated++;
            } else {
                if ($this->option('dry-run')) {
                    $this->warn("[DRY-RUN] Ville opprettet: {$name}");
                    $created++;
                    continue;
                }

                EmailTemplate::create([
                    'page_name' => $name,
                    'subject' => $data['subject'],
                    'from_email' => $data['from_email'],
                    'email_content' => $data['email_content'],
                ]);
                $this->warn("🆕 Opprettet: {$name}");
                $created++;
            }
        }

        $this->newLine();
        $this->table(
            ['Oppdatert', 'Opprettet', 'Totalt'],
            [[$updated, $created, $updated + $created]]
        );

        if ($this->option('dry-run')) {
            $this->warn('DRY-RUN modus — ingenting ble lagret. Kjør uten --dry-run for å lagre.');
        }

        return 0;
    }

    private function getTemplates(): array
    {
        return [
            'Shop Manuscript Welcome Email' => [
                'subject' => '✅ Vi har mottatt manuset ditt!',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :firstname,

Tusen takk for at du har sendt inn manuset ditt til Forfatterskolen! Vi er glade for at du har valgt oss til å hjelpe deg videre i skriveprosessen.

Vi tildeler nå en redaktør til prosjektet ditt. Du vil snart høre fra oss med mer informasjon om oppstart og forventet leveringstid.

<strong>Hva skjer videre?</strong>
– En erfaren redaktør leser gjennom manuset ditt
– Du får en grundig og konstruktiv tilbakemelding
– Tilbakemeldingen leveres innen avtalt frist

Har du spørsmål i mellomtiden? Svar gjerne på denne e-posten, så hjelper vi deg!

Skrivevarm hilsen,
Forfatterskolen
post@forfatterskolen.no | 411 23 555',
            ],

            'Shop Manuscript Follow-up Email' => [
                'subject' => 'Hvordan går det med skrivingen?',
                'from_email' => 'kristine@forfatterskolen.no',
                'email_content' => 'Hei :firstname,

Jeg tenkte bare å sjekke innom — hvordan går det med skrivingen etter tilbakemeldingen du fikk fra oss?

Har du kommet i gang med bearbeidingen, eller sitter du fast på noe? Det er helt normalt å trenge litt tid på å la tilbakemeldingen synke inn før man setter i gang.

Husk at vi er her for deg. Du er alltid velkommen til å sende en mail eller ringe hvis du har spørsmål.

:redirect_link Logg inn og se tilbakemeldingen din her :end_redirect_link

Skrivevarm hilsen,
Kristine S. Henningsen
Rektor, Forfatterskolen
kristine@forfatterskolen.no | 411 23 555',
            ],

            'Single Course Welcome Email' => [
                'subject' => '🎉 Velkommen til kurset — vi gleder oss til å ha deg med!',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :firstname,

Velkommen til Forfatterskolen! Vi er så glade for at du har valgt å ta steget og melde deg på kurs.

Du har nå tilgang til kursinnholdet ditt i portalen. Logg inn og kom i gang — det første steget er alltid det viktigste!

:redirect_link Klikk her for å logge inn i kursportalen :end_redirect_link

<strong>Noen tips for å komme godt i gang:</strong>
– Sett av fast tid i uken til skriving og kursarbeid
– Delta aktivt i diskusjonene — fellesskapet er en stor del av opplevelsen
– Send inn oppgavene dine — tilbakemelding fra redaktøren er gull verdt

Vi heier på deg! Lykke til med skrivingen.

Vennlig hilsen,
Forfatterskolen
post@forfatterskolen.no | 411 23 555',
            ],

            'Group Course Welcome Email' => [
                'subject' => '🎉 Velkommen til gruppekurset!',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :firstname,

Velkommen til Forfatterskolen og ditt nye gruppekurs! Du er nå en del av et inspirerende fellesskap av forfattere som alle jobber mot det samme målet — å skrive og fullføre en god bok.

Du har nå tilgang til kursinnholdet i portalen.

:redirect_link Logg inn her og kom i gang :end_redirect_link

<strong>Hva kan du forvente?</strong>
– Ukentlige leksjoner med konkrete skriveøvelser
– Tilbakemelding på tekst fra erfarne redaktører
– Et aktivt og støttende forfatterfellesskap
– Webinarer og mentormøter underveis

Er det noe du lurer på? Svar på denne e-posten, så hjelper vi deg!

Vi gleder oss til å følge skrivereisen din.

Vennlig hilsen,
Forfatterskolen
post@forfatterskolen.no | 411 23 555',
            ],

            'Group Course Multi-invoice Welcome Email' => [
                'subject' => '🎉 Velkommen — her er informasjon om delbetaling',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :firstname,

Velkommen til Forfatterskolen! Vi er glade for at du har valgt å betale kurset i deler — det gjør det enklere å komme i gang.

Du har nå tilgang til kursinnholdet i portalen.

:redirect_link Logg inn her og kom i gang :end_redirect_link

<strong>Om delbetalingen din:</strong>
Du vil motta fakturaer etter avtalt betalingsplan. Sørg for at disse betales innen forfall, slik at du beholder tilgangen til kurset gjennom hele perioden.

Har du spørsmål om betalingen? Send oss en mail på post@forfatterskolen.no.

Vi heier på deg og gleder oss til å følge skrivereisen din!

Vennlig hilsen,
Forfatterskolen
post@forfatterskolen.no | 411 23 555',
            ],

            'Course Taken Follow-up Email' => [
                'subject' => 'Hvordan går det med kurset?',
                'from_email' => 'kristine@forfatterskolen.no',
                'email_content' => 'Hei :firstname,

Jeg tenkte bare å ta kontakt og høre hvordan det går med kurset!

Kommer du deg gjennom leksjonene? Og viktigst av alt — skriver du?

Mange av oss trenger litt oppmuntring innimellom. Skriving er et langt løp, og det er lov å ha dager hvor det går tregt. Det viktigste er at du holder fast ved det.

Har du spørsmål om kurset, eller noe du vil diskutere? Svar gjerne på denne e-posten — jeg leser alle svar personlig.

Vi heier på deg!

Skrivevarm hilsen,
Kristine S. Henningsen
Rektor, Forfatterskolen
kristine@forfatterskolen.no | 411 23 555',
            ],

            'Gift Purchase' => [
                'subject' => '🎁 Du har fått et gavekort fra Forfatterskolen!',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :firstname,

Noen ønsker deg det aller beste — og har gitt deg et gavekort til Forfatterskolen!

Dette er din mulighet til å ta steget og begynne å skrive den boken du alltid har drømt om.

:redirect_link Klikk her for å aktivere gavekortet ditt :end_redirect_link

Har du spørsmål om hvordan du bruker gavekortet? Send oss en mail på post@forfatterskolen.no, så hjelper vi deg!

Vi gleder oss til å ha deg med!

Vennlig hilsen,
Forfatterskolen
post@forfatterskolen.no | 411 23 555',
            ],

            'Coaching Order' => [
                'subject' => '✅ Coachingtime er bestilt!',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :firstname,

Takk for bestillingen! Din coachingtime er nå bekreftet.

<strong>Detaljer om timen din:</strong>
– Type: :coaching_session
– Dato og tid: :booking_details

En redaktør vil ta kontakt med deg i forkant av timen. Er det noe spesielt du ønsker å jobbe med, eller tekst du vil at redaktøren skal lese på forhånd? Send det gjerne til oss i god tid.

Har du spørsmål? Svar på denne e-posten, så hjelper vi deg!

Vennlig hilsen,
Forfatterskolen
post@forfatterskolen.no | 411 23 555',
            ],

            'Copy Editing Order' => [
                'subject' => '✅ Vi har mottatt din bestilling av språkvask!',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :firstname,

Takk for at du har bestilt språkvask hos Forfatterskolen!

En erfaren språkvasker vil nå gå gjennom teksten din og sørge for at den er korrekt, flytende og klar for neste steg — enten det er en forlagsinnlevering, en søknad eller noe helt annet.

Vi tar kontakt med deg så snart arbeidet er i gang og gir deg en estimert leveringstid.

Har du spørsmål? Svar gjerne på denne e-posten.

Vennlig hilsen,
Forfatterskolen
post@forfatterskolen.no | 411 23 555',
            ],

            'Correction Order' => [
                'subject' => '✅ Vi har mottatt din bestilling av korrektur!',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :firstname,

Takk for at du har bestilt korrektur hos Forfatterskolen!

En erfaren korrekturleser vil nå gå gjennom teksten din med et skarpt blikk for stavefeil, tegnsetting og grammatikk — slik at teksten din fremstår så profesjonell som mulig.

Vi tar kontakt med deg så snart arbeidet er i gang.

Har du spørsmål? Svar gjerne på denne e-posten.

Vennlig hilsen,
Forfatterskolen
post@forfatterskolen.no | 411 23 555',
            ],

            'Assignment Submitted' => [
                'subject' => '📝 Ny oppgave innlevert — klar for tilbakemelding',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :editor,

:firstname har levert inn en ny oppgave og venter på tilbakemelding.

<strong>Elev:</strong> :firstname
<strong>Oppgave:</strong> :assignment
<strong>Innlevert:</strong> :date

:redirect_link Klikk her for å se oppgaven i portalen :end_redirect_link

Husk å sette forventet leveringsdato i portalen, slik at eleven vet når de kan forvente tilbakemelding.

Mvh,
Forfatterskolen-portalen',
            ],

            'Manuscript Uploaded' => [
                'subject' => '📄 :learner har lastet opp et nytt manus',
                'from_email' => 'sven.inge@forfatterskolen.no',
                'email_content' => 'Hei,

:learner har lastet opp en endring i manuset ":manuscript_from".

Dette er en automatisk melding siden du er registrert som redaktør for denne eleven.

:redirect_link Logg inn i portalen for å se endringen :end_redirect_link

Mvh,
Sven Inge Henningsen
Forfatterskolen',
            ],

            'New Pending Feedback' => [
                'subject' => '🎉 Tilbakemeldingen din er klar!',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :firstname,

Gode nyheter — tilbakemeldingen din fra redaktøren er nå klar!

Vi håper og tror den vil hjelpe deg videre i skriveprosessen. Husk at det å få tilbakemelding er en av de mest verdifulle tingene du kan gjøre for teksten din.

:redirect_link Klikk her for å se tilbakemeldingen din :end_redirect_link

<strong>Tips:</strong> Les tilbakemeldingen i ro og fred, gjerne to ganger. La den synke inn før du begynner å bearbeide teksten.

Har du spørsmål til tilbakemeldingen? Ta gjerne kontakt med redaktøren din.

Skrivevarm hilsen,
Forfatterskolen
post@forfatterskolen.no | 411 23 555',
            ],

            'Text Number' => [
                'subject' => '📋 Tekstnummer tildelt',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :firstname,

Du har nå fått tildelt et tekstnummer for innleveringen din. Dette nummeret brukes for å holde orden på teksten gjennom vurderingsprosessen.

<strong>Ditt tekstnummer:</strong> :text_number

Ta vare på dette nummeret — du kan bruke det hvis du har spørsmål om statusen på teksten din.

Har du spørsmål? Svar på denne e-posten.

Vennlig hilsen,
Forfatterskolen
post@forfatterskolen.no | 411 23 555',
            ],

            'Assignment Manuscript Removed' => [
                'subject' => 'Manuset ditt er fjernet fra oppgaven',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :firstname,

Vi bekrefter at manuset ditt er fjernet fra oppgaven ":assignment".

Hvis dette var en feil, eller du har spørsmål om hva som skjedde, er du velkommen til å ta kontakt med oss på post@forfatterskolen.no.

Vennlig hilsen,
Forfatterskolen
post@forfatterskolen.no | 411 23 555',
            ],

            'Assignment Manuscript Expected Finish' => [
                'subject' => '📅 Forventet dato for tilbakemelding',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :firstname,

Redaktøren har mottatt oppgaven din og er i gang med tilbakemeldingen.

<strong>Forventet leveringsdato:</strong> :date

Vi gleder oss til å lese teksten din, og gjør vårt beste for å levere innen fristen.

Har du spørsmål? Svar gjerne på denne e-posten.

Skrivevarm hilsen,
Forfatterskolen
post@forfatterskolen.no | 411 23 555',
            ],

            'Auto Renew Reminder' => [
                'subject' => '🔄 Webinarpakken din fornyes om 10 dager',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :firstname,

Vi minner deg om at webinarpakken din fornyes automatisk om 10 dager — akkurat som du har bedt om.

Det kommer mange spennende navn på mentormøtene fremover! Annen hver uke holder rektor Kristine S. Henningsen sine populære redigeringswebinarer, der du kan sende inn tekst på forhånd via portalen og få direkte tilbakemelding.

Har du spørsmål om fornyelsen, eller ønsker du å gjøre endringer? Ta kontakt med oss på post@forfatterskolen.no.

Vi gleder oss til å ha deg med videre!

Skrivevarm hilsen,
Forfatterskolen
post@forfatterskolen.no | 411 23 555',
            ],

            'Invoice Due Reminder' => [
                'subject' => '📋 Påminnelse: faktura forfaller om 14 dager',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :firstname,

Dette er en vennlig påminnelse om at du har en faktura som forfaller om 14 dager.

<strong>Beløp:</strong> :amount kr
<strong>Forfallsdato:</strong> :due_date
<strong>Fakturanummer:</strong> :invoice_number

Husk å betale innen forfall for å beholde tilgangen til kurset ditt.

Har du spørsmål om fakturaen? Ta kontakt på post@forfatterskolen.no.

Vennlig hilsen,
Forfatterskolen
post@forfatterskolen.no | 411 23 555',
            ],

            'Due Invoice Check' => [
                'subject' => '⚠️ Fakturaen din forfaller i morgen',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :firstname,

Dette er en påminnelse om at fakturaen din forfaller <strong>i morgen</strong>.

<strong>Beløp:</strong> :amount kr
<strong>Forfallsdato:</strong> :due_date

Vennligst sørg for å betale i tide for å unngå avbrudd i tilgangen din.

Har du spørsmål? Ta kontakt på post@forfatterskolen.no — vi hjelper deg!

Vennlig hilsen,
Forfatterskolen
post@forfatterskolen.no | 411 23 555',
            ],

            'Do not avail anything' => [
                'subject' => 'Er du klar til å begynne å skrive? ✍️',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :firstname,

Du registrerte deg hos Forfatterskolen i går — men vi ser at du ikke har aktivert noe kurs ennå.

Det er helt forståelig. Det kan være vanskelig å vite hvor man skal begynne.

Visste du at vi har gratiswebinarer der du kan prøve oss uten å forplikte deg til noe? Det er en fin måte å bli kjent med Forfatterskolen på.

:redirect_link Se våre kommende webinarer her :end_redirect_link

Har du spørsmål om hva som passer for deg? Svar på denne e-posten — vi hjelper deg gjerne med å finne rett tilbud.

Vennlig hilsen,
Forfatterskolen
post@forfatterskolen.no | 411 23 555',
            ],

            'Course Expiration Reminder' => [
                'subject' => '⏳ Kurset ditt utløper snart',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :firstname,

En kjapp påminnelse: tilgangen din til kurset utløper om <strong>:days dager</strong> (:expiry_date).

Har du noe igjen du vil rekke før tilgangen avsluttes? Nå er et godt tidspunkt å ta en siste titt!

:redirect_link Logg inn og kom i gang :end_redirect_link

Ønsker du å forlenge tilgangen? Ta kontakt med oss på post@forfatterskolen.no, så finner vi en løsning.

Vennlig hilsen,
Forfatterskolen
post@forfatterskolen.no | 411 23 555',
            ],

            'Shop Manuscript Comment' => [
                'subject' => '💬 Ny kommentar på manus — klar for behandling',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei,

En redaktør har lagt til en kommentar på et manus i manusbutikken.

:redirect_link Logg inn i portalen for å se kommentaren :end_redirect_link

Mvh,
Forfatterskolen-portalen',
            ],

            'Shop Manuscript Feedback' => [
                'subject' => '🎉 Tilbakemeldingen på manuset ditt er klar!',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :firstname,

Tusen takk for at du lot oss lese manuset ditt! Vi er nå ferdige med tilbakemeldingen og gleder oss til å dele den med deg.

<strong>Viktig:</strong> Tilbakemeldingen inneholder kommentarer i MARGEN på teksten din. Ser du dem ikke når du åpner dokumentet? Ta kontakt med oss, så hjelper vi deg!

:redirect_link Klikk her for å se tilbakemeldingen din :end_redirect_link

Ønsker du tilbakemelding fra samme redaktør neste gang? Skriv det i kommentarfeltet når du bestiller — vi etterkommer ønsket ditt så sant redaktøren er ledig.

Vi håper tilbakemeldingen hjelper deg videre i prosessen. Svar gjerne på denne e-posten hvis du har spørsmål!

Skrivevarm hilsen,
Forfatterskolen
post@forfatterskolen.no | 411 23 555',
            ],

            'Coaching Feedback' => [
                'subject' => '🎉 Tilbakemelding fra coachingtimen din er klar!',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :firstname,

Tilbakemeldingen fra coachingsesjonen din er nå klar!

:redirect_link Klikk her for å se tilbakemeldingen :end_redirect_link

Vi håper sesjonen var nyttig og inspirerende. Har du spørsmål til innholdet i tilbakemeldingen? Ta gjerne kontakt med redaktøren din direkte.

Skrivevarm hilsen,
Forfatterskolen
post@forfatterskolen.no | 411 23 555',
            ],

            'Copy Editing Feedback' => [
                'subject' => '✅ Språkvasken er ferdig — her er dokumentet ditt!',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :firstname,

Språkvasken av teksten din er nå ferdig! 🎉

En erfaren språkvasker har gått gjennom teksten din og sørget for at den er korrekt, flytende og klar for neste steg.

:redirect_link Klikk her for å laste ned det språkvaskede dokumentet :end_redirect_link

Har du spørsmål til endringene som er gjort? Svar på denne e-posten, så hjelper vi deg.

Skrivevarm hilsen,
Forfatterskolen
post@forfatterskolen.no | 411 23 555',
            ],

            'Correction Feedback' => [
                'subject' => '✅ Korrekturlesingen er ferdig!',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :firstname,

Korrekturlesingen av teksten din er nå fullført! 🎉

Teksten er gjennomgått for stavefeil, tegnsetting og grammatikk — og er nå klar til å presenteres for verden.

:redirect_link Klikk her for å laste ned det korrekturleste dokumentet :end_redirect_link

Har du spørsmål? Svar på denne e-posten.

Skrivevarm hilsen,
Forfatterskolen
post@forfatterskolen.no | 411 23 555',
            ],

            'Confirm Additional Email' => [
                'subject' => '✉️ Bekreft den nye e-postadressen din',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :firstname,

Du har bedt om å legge til en ny e-postadresse på kontoen din.

:redirect_link Klikk her for å bekrefte e-postadressen :end_redirect_link

Har du ikke gjort denne endringen selv? Ta kontakt med oss umiddelbart på post@forfatterskolen.no.

Vennlig hilsen,
Forfatterskolen
post@forfatterskolen.no | 411 23 555',
            ],

            'Learner Coaching Time Reservation Confirmed' => [
                'subject' => '✅ Coachingtime bekreftet — her er detaljene',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :first_name,

Coachingtimen din er nå bekreftet. Vi gleder oss til å jobbe med deg!

<strong>Type veiledning:</strong> :coaching_session
<strong>Dato og tid:</strong> :booking_details

Har du tekst eller spørsmål du vil at redaktøren skal forberede seg på? Send det gjerne til oss i god tid før timen.

Vi sees!

Vennlig hilsen,
Forfatterskolen
post@forfatterskolen.no | 411 23 555',
            ],

            'Editor New Coaching Time Booking Received' => [
                'subject' => '📅 Ny coachingtime booket for deg',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :editor,

Du har fått en ny coachingtime i kalenderen din.

<strong>Elev:</strong> :learner
<strong>Type:</strong> :coaching_session
<strong>Dato og tid:</strong> :booking_details

:redirect_link Logg inn i portalen for å se detaljene :end_redirect_link

Mvh,
Forfatterskolen',
            ],

            'Graphic Designer Notification' => [
                'subject' => '🎨 Nytt designprosjekt — klar for oppstart',
                'from_email' => 'sven.inge@forfatterskolen.no',
                'email_content' => 'Hei,

Det er et nytt designprosjekt klart for deg i portalen.

:redirect_link Logg inn for å se prosjektdetaljene :end_redirect_link

Ta kontakt hvis du har spørsmål.

Mvh,
Sven Inge Henningsen
Forfatterskolen',
            ],

            'Free Manuscript to Editor' => [
                'subject' => '📄 Nytt gratis manus til vurdering',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei,

Et nytt gratis manus er mottatt og klart for vurdering.

:redirect_link Logg inn i portalen for å se manuset :end_redirect_link

Mvh,
Forfatterskolen-portalen',
            ],

            'Fb Leads Registration' => [
                'subject' => '🎉 Takk for din interesse — her er noe til deg!',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :firstname,

Takk for at du meldte deg på via Facebook! Vi er glade for at du fant oss.

Forfatterskolen er Norges ledende skriveskole, og vi hjelper forfattere i alle stadier — fra første idé til ferdig manus.

Her er hva du kan forvente fra oss:
– Inspirasjon og tips til skrivingen din
– Informasjon om kommende webinarer og kurs
– Gode tilbud på kurs og manusutvikling

:redirect_link Se hva vi tilbyr her :end_redirect_link

Har du spørsmål? Svar på denne e-posten — vi hjelper deg gjerne med å finne det som passer for deg.

Vi heier på deg!

Vennlig hilsen,
Forfatterskolen
post@forfatterskolen.no | 411 23 555',
            ],

            'Manuscript' => [
                'subject' => '📅 Forventet dato for tilbakemelding på manuset ditt',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei,

Redaktøren har nå mottatt manuset ditt, og du kan forvente å få tilbakemelding: <strong>:date</strong>

Vi gleder oss til å lese teksten din, og gjør vårt beste for å levere en grundig og konstruktiv tilbakemelding innen fristen.

Har du spørsmål? Svar gjerne på denne e-posten.

Skrivevarm hilsen,
Forfatterskolen
post@forfatterskolen.no | 411 23 555',
            ],

            'Assignment Manuscript Feedback' => [
                'subject' => '🎉 Tilbakemeldingen på innleveringsoppgaven din er klar!',
                'from_email' => 'post@forfatterskolen.no',
                'email_content' => 'Hei :firstname,

Tilbakemeldingen fra redaktøren på innleveringsoppgaven din er nå klar!

Vi håper og tror du blir fornøyd — og at tilbakemeldingen hjelper deg videre i skriveprosessen.

<strong>Viktig:</strong> Tilbakemeldingen inneholder kommentarer i MARGEN på teksten din. Du finner dem øverst i dokumentet og underveis i manuset. Ser du dem ikke? Ta kontakt, så hjelper vi deg — vi kan også sende en PDF hvis du ikke har Word.

:redirect_link Klikk her for å se tilbakemeldingen din :end_redirect_link

Husk: det er <em>teksten</em> din som får tilbakemelding — ikke deg som person. Redaktøren ønsker deg og prosjektet ditt vel, og målet er alltid å hjelpe teksten nå sitt fulle potensial.

Har du spørsmål til tilbakemeldingen? Ta kontakt med oss.

Skrivevarm hilsen,
Forfatterskolen
post@forfatterskolen.no | 411 23 555',
            ],
        ];
    }
}
