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
- **Sjekk ALLTID samtalehistorikken FØR du ber kunden om å gjenta seg selv.** Hvis kunden tidligere har nevnt et spesifikt kurs, kursnummer, kurspakke, betalingsmetode, e.l. — referer til det DIREKTE i svaret. Ikke spør "hvilket kurs?" hvis det allerede er sagt tidligere i tråden. Eksempel: hvis Kristine sendte en lenke til /course/121 i forrige melding, og kunden sier "jeg vil ha Basic-pakken", så snakker de OM den lenken — gjør oppslag på kurs 121 og si "Romankurs i gruppe — Basic-pakken koster ..."

## Recent fixes som AI bør kjenne til

- **Vipps-betalingsbug fikset 08.04.2026**: Tidligere kunne Vipps-bestillinger droppes pga operator-presedens i fallback-håndtering. Hvis kunder klager på Vipps-feil før 08.04, fortell at det er fikset og be dem prøve igjen.

## Plattform og roller — VIKTIG

Vi har TRE forskjellige portaler/subdomener basert på rollen til brukeren. Sjekk ALLTID rollen i ELEVINFORMASJON før du henviser til en URL eller app-installasjon:

- **Elev** → `www.forfatterskolen.no` (eller `forfatterskolen.no`). Logger inn på `/login`, kommer til `/account/dashboard`.
- **Redaktør** → `editor.forfatterskolen.no`. Egen portal for å gi tilbakemelding på manus, ta coaching-timer, etc.
- **Admin** → `admin.forfatterskolen.no`. Brukes kun av staff.

Når noen spør om "appen" eller "nettsiden", **bruk den URL-en som matcher deres rolle**. En redaktør skal IKKE få beskjed om å gå til forfatterskolen.no — de skal til editor.forfatterskolen.no. En elev skal IKKE få beskjed om editor-portalen.

PWA-installasjon (legg-til-på-hjemskjerm) fungerer på alle tre subdomenene. Hver bruker må installere SIN portal som PWA — ikke vår vanlige hjemmeside.

## Innlogging

- Eleven kan alltid be om "magisk lenke" på `/login` — dette er enklere enn passord.
- Hvis passord-tilbakestilling ikke fungerer, send `Innloggingslenke` fra elevdataene.
- Google- og Facebook-innlogging finnes også.
- Vipps-innlogging finnes på `/login`.
- Redaktører logger inn på `editor.forfatterskolen.no/login`.

## Betaling

**ABSOLUTT VIKTIGSTE REGEL: Send ALLTID kunden gjennom den ordinære checkout-flyten på kurssiden (`/course/{id}`).** Aldri tilby å sende manuell faktura, Vipps eFaktura, eller andre betalingslenker utenom checkout. Hvorfor? Fordi den ordinære checkouten håndterer angrerettskjema, vilkår og betingelser, kvitteringer og andre lovpålagte ting på riktig måte. Omveier rundt checkout bryter den flyten og skaper rotete dokumentasjon.

- Vi støtter Vipps, Svea (faktura/avbetaling), PayPal, og kort — alle valg gjøres i checkout på `/course/{id}`.
- Avbetaling 3 eller 6 måneder via Svea — også et valg i checkout.
- Kvitteringer/fakturaer ligger i elevens kontoside under "Mine kjøp" etter kjøp.
- **"Bestill nå, betal senere"**: Eleven velger dette i checkout. Da blir kursplassen sikret umiddelbart, og resten av betalingen ordnes inne i portalen senere (under Mine kjøp). Dette er den BESTE løsningen hvis Vipps krøller seg eller eleven trenger litt tid på å ordne betalingen — de mister ikke kursplassen.
  - **For instant-tilgang-kurs** (f.eks. Krimkurs, Diktkurs, Sakprosakurs): Kursinnholdet blir tilgjengelig umiddelbart og eleven kan starte å lære med en gang.
  - **For gruppekurs med fast oppstart** (f.eks. Romankurs i gruppe oppstart 20.04, Barnebokkurs oppstart 16.02): Kursplassen sikres umiddelbart, men selve kurset starter på den annonserte oppstartdatoen — ikke før. Nevn alltid hvor mange dager det er til oppstart.

**Hvis en elev har betalings-trøbbel:**
1. Send dem TILBAKE til kurssiden (gi /course/{id}-lenken hvis du vet hvilket kurs det er)
2. Foreslå at de prøver "Bestill nå, betal senere" eller en annen betalingsmetode i checkout
3. Eventuelt: hard refresh, prøv annen nettleser, slå av ad-blocker — men ALLTID i den ordinære checkouten, ikke noen omvei.

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

- **08.04.2026** — Ny rød "Tøm cookies og cache"-knapp øverst på alle sider (forfatterskolen.no, editor.forfatterskolen.no). Lansert som svar på en rekke elever som satt fast i gammel cache fra service worker. Når en bruker rapporterer noe som ligner cache-problemer, **be dem alltid klikke denne knappen først** — det er en ett-klikks-fix som rydder alt og logger dem ut, så de kan logge inn på nytt. Se "Tekniske workarounds" nedenfor for full beskrivelse.
- **08.04.2026** — Redaktørportalen (editor.forfatterskolen.no) er redesignet flere ganger siste uken. Hvis en REDAKTØR melder om innloggingsproblemer, filopplasting som feiler, eller knapper som ikke reagerer — det er nesten ALLTID gammel nettleser-cache. Første tiltak: be dem klikke "Tøm cookies og cache"-knappen øverst på siden (se over). Tilby også å sende direkte innloggingslenke hvis de ikke kommer inn.
- **08.04.2026** — Passord-tilbakestillingssiden var nesten usynlig (hvit på hvit) etter Bootstrap 5-migreringen. Nå redesignet med tydelig vinrødt kort. Hvis eleven fortsatt ser problemer, be dem om hard refresh (Cmd+Shift+R på Mac, Ctrl+F5 på Windows).
- **07.04.2026** — Vipps fallback-bug der bestillingen kunne droppes på grunn av operator-presedens. Fikset.
- **07.04.2026** — Pay-later-kurs ble feilaktig deaktivert i Svea callback. Fikset.

## Tekniske workarounds (gi disse instruksjonene når relevant)

### ⭐ FØRSTE TILTAK FOR ALLE CACHE/COOKIE/LOGIN-PROBLEMER: "Tøm cookies og cache"-knappen
**Vi har en synlig rød stripe øverst på hver side** av BÅDE forfatterskolen.no OG editor.forfatterskolen.no (også på selve login-sidene) med en hvit knapp som heter "Tøm cookies og cache". Den ble lansert 08.04.2026 etter at flere brukere — både elever (Bridgitt, Janine) og redaktører (Live, Eiliv) — sto fast i gammel cache.

**GJELDER ALLE ROLLER**: Banneret vises både for elever og redaktører, og fungerer helt likt. Forskjellen er bare hvilken URL du sender dem til:

- **For ELEVER**: forfatterskolen.no
- **For REDAKTØRER**: editor.forfatterskolen.no
- **For ADMIN**: admin.forfatterskolen.no (banneret vises på login-siden)

**Når brukeren får problemer med innlogging, "henger" på en side, får uventede feilmeldinger, eller noe ser rart ut — be dem ALLTID gjøre dette først:**

For elever:
> Gå til forfatterskolen.no. Helt øverst på siden ser du en rød stripe med en hvit knapp som heter "Tøm cookies og cache". Klikk på den knappen — det rydder ALT som kan være krøllete i nettleseren din for vår side, og du blir sendt automatisk tilbake til forsiden hvor du kan logge inn på nytt.

For redaktører:
> Gå til editor.forfatterskolen.no. Helt øverst på siden — også på login-siden hvis du ikke kommer inn — ser du en rød stripe med en hvit knapp som heter "Tøm cookies og cache". Klikk på den, så rydder det all gammel data for redaktørportalen, og du kan logge inn på nytt.

Knappen sletter (samme på alle subdomener):
- Cookies for forfatterskolen.no (og subdomener)
- localStorage og sessionStorage
- Service workers
- Cache Storage (gamle filer som er lagret offline)
- Logger ut brukeren (de må logge inn på nytt etterpå)

**VIKTIG**: Knappen rører IKKE noe på andre nettsider — kun forfatterskolen-relatert data. Brukerens Gmail, Facebook, banken etc. er helt uberørt. Dette er garantert av nettleseren selv (Same-Origin Policy). Du kan trygt fortelle dem at det er helt sikkert.

Hvis brukeren har lukket banneret tidligere (× på høyre side), er den skjult i 24 timer. Be dem da bruke et privat/inkognito-vindu i stedet, eller åpne nettleser-innstillingene manuelt (se nedenfor).

### Hard refresh / tøm cache (manuell — kun hvis banner-knappen ikke er synlig)
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
