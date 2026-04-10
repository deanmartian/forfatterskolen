@extends('frontend.layout')

@section('page_title', 'Vilkår og betingelser — Forfatterskolen')

@section('styles')
<style>
    .terms-page { background: #fff; }
    .terms-section {
        max-width: 800px;
        margin: 0 auto;
        padding: 48px 24px;
    }
    .terms-header {
        text-align: center;
        margin-bottom: 40px;
    }
    .terms-header h1 {
        font-family: Georgia, serif;
        font-size: 32px;
        color: #1a1a1a;
        margin-bottom: 8px;
    }
    .terms-meta {
        background: #f9edef;
        padding: 16px 24px;
        border-radius: 8px;
        font-size: 14px;
        color: #555;
        margin-bottom: 32px;
        text-align: center;
    }
    .terms-nav {
        background: #f9edef;
        padding: 24px;
        border-radius: 8px;
        margin-bottom: 40px;
    }
    .terms-nav h3 {
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 12px;
        color: #333;
    }
    .terms-nav a {
        display: block;
        color: #862736;
        padding: 6px 0;
        text-decoration: none;
        font-size: 15px;
    }
    .terms-nav a:hover { text-decoration: underline; }
    .terms-section h2 {
        color: #862736;
        font-family: Georgia, serif;
        font-size: 24px;
        border-bottom: 2px solid #862736;
        padding-bottom: 12px;
        margin-top: 48px;
        margin-bottom: 24px;
    }
    .terms-section h3 {
        color: #1a1a1a;
        font-size: 18px;
        margin-top: 28px;
        margin-bottom: 12px;
    }
    .terms-section p {
        color: #444;
        line-height: 1.8;
        margin-bottom: 16px;
        font-size: 15px;
    }
    .terms-section ul {
        color: #444;
        line-height: 1.8;
        margin-bottom: 16px;
        padding-left: 24px;
    }
    .terms-section li { margin-bottom: 6px; font-size: 15px; }
    .terms-contact {
        background: #f9edef;
        padding: 24px;
        border-radius: 8px;
        margin-top: 32px;
    }
    .terms-contact p { margin-bottom: 4px; }
</style>
@stop

@section('content')
<div class="terms-page">
    <div class="terms-section">
        <div class="terms-header">
            <h1>Vilkår, betingelser og personvern</h1>
        </div>

        <div class="terms-meta">
            Forfatterskolen AS · Lihagen 21, 3029 Drammen · post@forfatterskolen.no · 411 23 555<br>
            Org.nr: 913 573 633 · Sist oppdatert: 19.3.2026
        </div>

        <div class="terms-nav">
            <h3>Innhold</h3>
            <a href="#personvern">1. Personvernerklæring</a>
            <a href="#kjopsvilkar">2. Generelle kjøpsvilkår</a>
            <a href="#kurs">3. Vilkår for kurs</a>
            <a href="#manus">4. Vilkår for manusutvikling</a>
            <a href="#workshop">5. Vilkår for skriveverksteder</a>
            <a href="#coaching">6. Vilkår for coachingtime</a>
            <a href="#kontakt">7. Kontakt og klager</a>
        </div>

        {{-- 1. Personvernerklæring --}}
        <h2 id="personvern">1. Personvernerklæring</h2>

        <p>Forfatterskolen AS er behandlingsansvarlig for personopplysninger vi samler inn når du bruker våre tjenester. Vi behandler personopplysninger i samsvar med personopplysningsloven og EUs personvernforordning (GDPR).</p>

        <div class="terms-contact">
            <p><strong>Kontaktinformasjon til behandlingsansvarlig:</strong></p>
            <p>Forfatterskolen AS</p>
            <p>Lihagen 21, 3029 Drammen</p>
            <p>post@forfatterskolen.no</p>
            <p>Tlf: 411 23 555</p>
            <p>Org.nr: 913 573 633</p>
        </div>

        <h3>1.1 Hvilke opplysninger samler vi inn</h3>
        <p>Vi samler inn personopplysninger i følgende situasjoner:</p>
        <ul>
            <li>Navn, e-postadresse og betalingsinformasjon ved kjøp av kurs eller tjenester</li>
            <li>Navn og e-postadresse ved påmelding til nyhetsbrev eller gratis webinar</li>
            <li>Teknisk informasjon som IP-adresse og nettleserdata ved bruk av nettsiden</li>
            <li>Informasjon du selv oppgir i kursportalen, manus og oppgaver</li>
        </ul>

        <h3>1.2 Formål og grunnlag for behandling</h3>
        <p>Vi behandler personopplysninger for å:</p>
        <ul>
            <li>Levere kurs, manusutvikling og andre tjenester du har kjøpt</li>
            <li>Sende bekreftelser, fakturaer og annen nødvendig kommunikasjon</li>
            <li>Sende nyhetsbrev og markedsføring (kun med ditt samtykke)</li>
            <li>Forbedre nettsiden og tjenestene våre</li>
            <li>Oppfylle lovpålagte forpliktelser</li>
        </ul>

        <h3>1.3 Dine rettigheter</h3>
        <p>Du har rett til å:</p>
        <ul>
            <li>Få innsyn i hvilke opplysninger vi har om deg</li>
            <li>Kreve retting av feilaktige opplysninger</li>
            <li>Kreve sletting av opplysninger ("rett til å bli glemt")</li>
            <li>Trekke tilbake samtykke til enhver tid</li>
            <li>Klage til Datatilsynet (datatilsynet.no)</li>
        </ul>
        <p>Ta kontakt på post@forfatterskolen.no for å utøve disse rettighetene.</p>

        <h3>1.4 Deling med tredjeparter</h3>
        <p>Vi deler ikke dine personopplysninger med tredjeparter uten ditt samtykke, med unntak av:</p>
        <ul>
            <li>Betalingstjenesteleverandører (Fiken, Vipps, PayPal) for å gjennomføre transaksjoner</li>
            <li>Offentlige myndigheter når dette er lovpålagt</li>
            <li>Leverandører av tekniske tjenester som er nødvendige for å levere våre tjenester</li>
        </ul>

        <h3>1.5 Informasjonskapsler (cookies)</h3>
        <p>Vi bruker informasjonskapsler for å forbedre brukeropplevelsen, analysere trafikk via Google Analytics, og vise relevante annonser via Google Ads og Meta (Facebook/Instagram). Du kan administrere dine cookie-innstillinger i nettleseren din.</p>

        {{-- 2. Generelle kjøpsvilkår --}}
        <h2 id="kjopsvilkar">2. Generelle kjøpsvilkår</h2>

        <h3>2.1 Angrefrist</h3>
        <p>Forbrukere har 14 dagers angrefrist fra kjøpsdato, med mindre annet er spesifisert. På kurs som annonseres med angrefrist fra kursets startdato gjelder denne fristen fra oppstartdatoen.</p>
        <p>For å benytte angrefristen må du gi oss skriftlig beskjed innen fristen utløper. Tilbakebetaling skjer innen 7 virkedager til original betalingsmetode.</p>
        <p>Angrefristloven gjelder kun for forbrukere, ikke for næringsdrivende som kjøper kurs i næringsøyemed.</p>

        <h3>2.2 Betaling og kortbetaling</h3>
        <p>Vi aksepterer Visa og MasterCard via våre samarbeidspartnere. Alle transaksjoner er kryptert via SSL og er sikre. Vi aksepterer også betaling via Vipps og faktura.</p>

        <h3>2.3 Delbetaling og avdragsordning</h3>
        <p>Kjøp med avdragsordning er bindende og skal betales i sin helhet innen avtalt tid. Ved mislighold kan Forfatterskolen kreve restbeløpet umiddelbart. Forfatterskolen tilbyr rentefrie delbetalingsordninger på utvalgte kurs.</p>

        <h3>2.4 Sykdomsavbrudd</h3>
        <p>Ved dokumentert sykdom som hindrer deltakelse på kurs, kan kursets oppstart eller tilgangsperiode utsettes. Ta kontakt med oss på post@forfatterskolen.no med legeerklæring.</p>

        <h3>2.5 Salg til mindreårige</h3>
        <p>Kjøpere under 18 år må ha tillatelse fra foresatte.</p>

        <h3>2.6 Opphavsrett og bruksrett</h3>
        <p>Alt innhold i kursportalen er beskyttet av opphavsrett og tilhører Forfatterskolen AS. Du kjøper en personlig, ikke-overførbar bruksrett til innholdet i kursperioden. Kopiering, deling eller videredistribusjon av kursinnhold er ikke tillatt.</p>

        <h3>2.7 Konfidensialitet</h3>
        <p>Vi respekterer ditt privatliv og forventer at du respekterer privatlivet til andre kursdeltakere. Erfaringer og uttalelser fra andre deltakere i kursportalen skal holdes konfidensielle.</p>

        <h3>2.8 Ansvarsbegrensning</h3>
        <p>Forfatterskolen AS er ikke ansvarlig for indirekte tap eller følgeskader som følge av bruk av våre tjenester. Vårt ansvar er begrenset til det beløpet du har betalt for den aktuelle tjenesten.</p>

        {{-- 3. Vilkår for kurs --}}
        <h2 id="kurs">3. Vilkår for kjøp av kurs</h2>

        <h3>3.1 Tilgang og varighet</h3>
        <p>Du får tilgang til kursinnholdet i den perioden som er angitt ved kjøp. Tilgang gis fra kjøpsdato med mindre annet er avtalt. Etter utløp av tilgangsperioden vil du ikke lenger ha tilgang til kursportalen.</p>

        <h3>3.2 Kurset gjennomføres digitalt</h3>
        <p>Forfatterskolens kurs gjennomføres som nettbaserte kurs. Du trenger tilgang til internett og en oppdatert nettleser for å delta. Webinarer og mentormøter gjennomføres via videokonferanse.</p>

        <h3>3.3 Avbestilling</h3>
        <p>Avbestilling etter angrefristens utløp gir ikke rett til tilbakebetaling, med mindre annet er spesifisert ved kjøp eller avtalt med Forfatterskolen.</p>

        <h3>3.4 Endringer i kursinnhold</h3>
        <p>Forfatterskolen forbeholder seg retten til å gjøre endringer i kursinnhold, mentorer og gjennomføring. Vi vil varsle om vesentlige endringer i god tid.</p>

        {{-- 4. Vilkår for manusutvikling --}}
        <h2 id="manus">4. Vilkår for manusutvikling</h2>

        <h3>4.1 Bestilling og behandlingstid</h3>
        <p>Etter bestilling av manusutvikling vil du motta en bekreftelse med forventet leveringstid. Leveringstiden avhenger av manustype og redaktørens kapasitet, og er normalt 2–8 uker.</p>

        <h3>4.2 Levering av tilbakemelding</h3>
        <p>Tilbakemeldingen leveres som et kommentert Word-dokument. Ser du ikke kommentarene i dokumentet, ta kontakt med oss så hjelper vi deg.</p>

        <h3>4.3 Konfidensialitet</h3>
        <p>Alle manus som sendes inn til Forfatterskolen behandles konfidensielt. Vi deler ikke ditt manus med tredjeparter uten ditt samtykke.</p>

        <h3>4.4 Opphavsrett til manus</h3>
        <p>Du beholder full opphavsrett til ditt eget manus. Forfatterskolen har kun rett til å lese og kommentere manuset i forbindelse med den bestilte tjenesten.</p>

        {{-- 5. Vilkår for skriveverksteder --}}
        <h2 id="workshop">5. Vilkår for skriveverksteder og workshops</h2>

        <h3>5.1 Påmelding og betaling</h3>
        <p>Påmelding til skriveverksteder og workshops er bindende fra betalingstidspunktet. Angrefrist gjelder i henhold til punkt 2.1.</p>

        <h3>5.2 Avlysning</h3>
        <p>Dersom et skriveverksted avlyses av Forfatterskolen, vil du motta full refusjon. Vi forbeholder oss retten til å avlyse arrangementer ved for lav påmelding eller force majeure.</p>

        <h3>5.3 Utelukkelse</h3>
        <p>Forfatterskolen forbeholder seg retten til å utelukke deltakere som opptrer på en måte som er skadelig for andre deltakere eller arrangementet.</p>

        {{-- 6. Vilkår for coachingtime --}}
        <h2 id="coaching">6. Vilkår for coachingtime og veiledning</h2>

        <h3>6.1 Booking og avbestilling</h3>
        <p>Booking av coachingtime bekreftes skriftlig. Avbestilling må skje senest 24 timer før avtalt tid. Ved avbestilling med kortere varsel eller ved uteblivelse, beholdes betalingen.</p>

        <h3>6.2 Gjennomføring</h3>
        <p>Coachingtimer gjennomføres via videokonferanse. Du vil motta en lenke til møtet i god tid før avtalt tid. Dersom du ikke møter til avtalt tid, anses timen som gjennomført.</p>

        {{-- 7. Kontakt og klager --}}
        <h2 id="kontakt">7. Kontakt og klager</h2>

        <p>Har du spørsmål til vilkårene, eller ønsker du å klage på en tjeneste? Ta kontakt med oss:</p>

        <div class="terms-contact">
            <p><strong>Forfatterskolen AS</strong></p>
            <p>Lihagen 21, 3029 Drammen</p>
            <p>post@forfatterskolen.no</p>
            <p>Tlf: 411 23 555</p>
        </div>

        <p>Vi bestreber oss på å svare på alle henvendelser innen 2 virkedager.</p>
        <p>Dersom vi ikke kommer til enighet, kan du klage til <a href="https://www.forbrukertilsynet.no" style="color:#862736;">Forbrukertilsynet</a> eller <a href="https://www.forbrukerradet.no" style="color:#862736;">Forbrukerrådet</a>.</p>

    </div>
</div>
@stop
