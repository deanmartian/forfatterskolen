# AI-kunnskap for inbox-utkast

Denne fila leses inn i prompten hver gang AI-en lager et utkast til kundesvar. Hold den oppdatert med:

- Nylige bugfikser eleven kan bli berørt av
- Generell viktig info som ikke står i kurslisten/databasen
- Workarounds for kjente problemer
- Vanlige spørsmål med autoritative svar

**Endringer her krever ingen kodeoppdatering — bare lagre fila og pushe den.** For ad-hoc kjente feil bruker du heller admin-grensesnittet (`/admin/ai-knowledge`) som er rasker enn å pushe kode.

---

## Generelt

- Forfatterskolen, EasyWrite og Indiemoon Publishing er samme firma. Sven Inge Henningsen er eier/daglig leder, Kristine S. Henningsen er rektor.
- Vi er en liten organisasjon — varm og personlig tone, ikke korporativt.
- Bruk gjerne smilefjes som :-) eller :) der det passer.
- Eleven skal alltid føle at vi er på lag med dem.
- Vi er sjelden uenige med eleven — finn løsninger.

## Innlogging

- Eleven kan alltid be om "magisk lenke" på `/login` — dette er enklere enn passord.
- Hvis passord-tilbakestilling ikke fungerer, send `Innloggingslenke` fra elevdataene.
- Google- og Facebook-innlogging finnes også.
- Vipps-innlogging finnes på `/login`.

## Betaling

- Vi støtter Vipps, Svea (faktura/avbetaling), PayPal, og kort.
- Avbetaling 3 eller 6 måneder via Svea.
- Kvitteringer/fakturaer ligger i elevens kontoside under "Mine kjøp".
- Hvis Vipps henger eller feiler, kan eleven prøve på nytt eller velge en annen betalingsmetode.

## Kurs og deltakelse

- Alle kurs er nettbaserte og kan følges hjemmefra.
- Eleven har tilgang i ett år fra oppstart.
- Det er mulig å forlenge tilgangen til symbolsk pris (kontakt support).
- Innleveringer kan utsettes ved behov — eleven kan be om utsettelse via portalen.
- Webinaropptak er alltid tilgjengelig.

## Manustjenester

- Vi vurderer manus opp til ca 70 000 ord. Større manus splittes opp.
- Tilbakemelding tar normalt 4–6 uker, men kan ta lengre tid ved høy pågang.
- Alle manustjenester er konfidensielle — vi deler aldri elevens manus med andre.

## Selvpublisering (Indiemoon)

- Vi tilbyr full pakke: omslag, layout, trykk, e-bok, ISBN.
- Bøkene selges via vår egen nettbutikk og kan distribueres til Adlibris/Bokkilden.
- Royalty utbetales kvartalsvis.

---

## Nylig fikset

> Hold denne lista oppdatert med ting som er fikset siste 30 dager.
> Hvis en elev skriver om noe som står her, kan AI si "vi har akkurat fikset dette, prøv igjen".

- **08.04.2026** — Passord-tilbakestillingssiden var nesten usynlig (hvit på hvit) etter Bootstrap 5-migreringen. Nå redesignet med tydelig vinrødt kort. Hvis eleven fortsatt ser problemer, be dem om hard refresh (Cmd+Shift+R på Mac, Ctrl+F5 på Windows).
- **07.04.2026** — Vipps fallback-bug der bestillingen kunne droppes på grunn av operator-presedens. Fikset.
- **07.04.2026** — Pay-later-kurs ble feilaktig deaktivert i Svea callback. Fikset.

## Tekniske workarounds (gi disse instruksjonene når relevant)

### Hard refresh / tøm cache
Hvis eleven ser en side som virker rar, gammel, eller "grå ut", be dem først om hard refresh:
- **Mac:** `Cmd + Shift + R`
- **Windows:** `Ctrl + F5` eller `Ctrl + Shift + R`

### Slette cookies for forfatterskolen.no
Hvis hard refresh ikke hjelper, og særlig hvis innlogging henger, må eleven slette cookies for vår side:

**Chrome / Edge:**
1. Åpne forfatterskolen.no
2. Klikk på hengelås-ikonet til venstre for adressefeltet
3. Velg "Informasjonskapsler og nettstedsdata" → "Administrer informasjonskapsler"
4. Velg forfatterskolen.no og klikk "Fjern"
5. Last siden på nytt

**Safari (Mac):**
1. Safari → Innstillinger → Personvern → Behandle nettstedsdata
2. Søk etter "forfatterskolen"
3. Velg og klikk "Fjern"
4. Last siden på nytt

**Firefox:**
1. Klikk på hengelås-ikonet i adressefeltet
2. "Slett cookies og nettstedsdata"
3. Last siden på nytt

### Privat / inkognito-vindu
Et raskt alternativ er å åpne forfatterskolen.no i et privat / inkognito-vindu:
- **Chrome/Edge:** `Cmd/Ctrl + Shift + N`
- **Safari:** `Cmd + Shift + N`
- **Firefox:** `Cmd/Ctrl + Shift + P`

Hvis siden funker i inkognito men ikke vanlig vindu, så er det helt sikkert et cache/cookie-problem.

### Bytte nettleser
Hvis eleven bruker en gammel Safari eller Internet Explorer, anbefal Chrome eller Firefox.

### Slå av nettleserutvidelser (extensions)
Noen ad-blockere og personvern-utvidelser blokkerer Vipps, Stripe og lignende. Be eleven slå av extensions midlertidig hvis betaling henger.

---

## Vanlige spørsmål med standardsvar

**"Hvordan får jeg tilgang til kurset etter kjøp?"**
Tilgang aktiveres automatisk etter betaling. Eleven får e-post med innloggingslenke. Hvis ikke: send `Innloggingslenke` fra elevdataene.

**"Hvor lenge har jeg tilgang?"**
Ett år fra oppstart. Forlengelse mulig.

**"Hvordan leverer jeg oppgaven?"**
Via portalen under "Mine oppgaver". Last opp som .docx eller skriv direkte i editoren.

**"Hvem er redaktøren min?"**
Sjekk `Aktive kurs` og `Coaching-timer` i elevdataene. Hvis ikke tildelt: si at vi tildeler innen 1–2 dager.

**"Når får jeg tilbakemelding?"**
Normalt 4–6 uker. Sjekk `Oppgavestatus` for konkret frist.
