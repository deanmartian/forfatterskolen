@extends('frontend.layout')

@section('title')
<title>Årskurs Forfatterskolen – Skriv boken din på ett år</title>
@stop

@section('meta')
<meta name="description" content="Bli med på årskurs med forfatter og skrivementor Kristine S. Henningsen, og få alt du trenger for å fullføre manuset ditt på ett år.">
@stop

@section('styles')
<link rel="stylesheet" href="{{ asset('css/arskurs.css') }}">
<script src="https://fast.wistia.com/assets/external/E-v1.js" async></script>
@stop

@section('content')
<div class="arskurs-page">

    {{-- ============================================================
         SECTION 1 – Sticky Page Nav
         ============================================================ --}}
    <nav class="page-nav">
        <div class="container">
            <div class="nav-inner">
                <a href="#oversikt">Oversikt</a>
                <a href="#kursholdere">Kursholdere</a>
                <a href="#deltakere">Deltakere</a>
                @if($course)
                    <a href="{{ route('front.course.checkout', $course->id) }}" class="nav-cta">Påmelding</a>
                @else
                    <a href="#pamelding" class="nav-cta">Påmelding</a>
                @endif
            </div>
        </div>
    </nav>

    {{-- ============================================================
         SECTION 2 – Hero
         ============================================================ --}}
    <section class="hero-section" id="oversikt">
        <div class="hero-bg" style="background-image: url('{{ asset('images/arskurs/hero-kristine.webp') }}')"></div>
        <div class="container">
            <div class="hero-content">
                <div class="hero-label">Årskurs</div>
                <h1>Hvordan skrive boken din på ett år?</h1>
                <p>Din historie har en verdi, og fortjener å bli lest. Men hvordan skal du skrive den, komme i mål, få den ut i verden?</p>
                <p>Bli med på årskurs med forfatter og skrivementor Kristine S. Henningsen, og få alt du trenger for å fullføre manuset ditt på ett år.</p>
                @if($course)
                    <a href="{{ route('front.course.checkout', $course->id) }}" class="btn-cta">Sikre deg en plass nå</a>
                @else
                    <a href="#pamelding" class="btn-cta">Sikre deg en plass nå</a>
                @endif
                <br>
                <a href="#trailer" class="trailer-link">
                    <span class="play-icon"><i class="fa fa-play"></i></span>
                    Se trailer
                </a>
            </div>
        </div>
    </section>

    {{-- ============================================================
         SECTION 3 – 3-Column Features
         ============================================================ --}}
    <section class="section-light features-section">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-label">Læring</div>
                        <h3>Lær deg skrivehåndverket</h3>
                        <img src="{{ asset('images/arskurs/feature-laering.png') }}" alt="Læring">
                        <p>Kurset lærer deg skrivehåndverket på en lettfattelig og inspirerende måte – uansett sjanger.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-label">Vurdering</div>
                        <h3>Få din egen redaktør</h3>
                        <img src="{{ asset('images/arskurs/feature-vurdering.png') }}" alt="Vurdering">
                        <p>Du får din egen redaktør som følger deg gjennom hele kursåret, og gir deg profesjonell tilbakemelding på manus.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-label">Fellesskap</div>
                        <h3>Skriv sammen med andre</h3>
                        <img src="{{ asset('images/arskurs/feature-fellesskap.png') }}" alt="Fellesskap">
                        <p>Vi jobber mot målet i fellesskap, og skriver sammen i økter for å få fart på skrivemotoren.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         SECTION 4 – Trailer Video
         ============================================================ --}}
    <section class="section-dark video-section" id="trailer">
        <div class="container">
            <div class="video-wrapper">
                <div class="wistia_responsive_padding" style="padding:56.25% 0 0 0;position:relative;">
                    <div class="wistia_responsive_wrapper" style="height:100%;left:0;position:absolute;top:0;width:100%;">
                        <div class="wistia_embed wistia_async_g5xz8vikxq seo=true videoFoam=true" style="height:100%;position:relative;width:100%;">&nbsp;</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         SECTION 5 – Price (dynamic from course)
         ============================================================ --}}
    <section class="section-dark price-section" id="pamelding">
        <div class="container">
            <h2>Sikre deg en plass på kurset</h2>
            @if($course)
                <p>{{ $course->title }} tar inn et begrenset antall deltakere.</p>
            @else
                <p>Årskurset tar inn et begrenset antall deltakere.</p>
            @endif

            <div class="price-box">
                <div class="price-box-left">
                    <h3>Påmelding med rabatt</h3>
                    <p class="price-subtitle">Du betaler ingenting før kurset er i gang.</p>
                    @if($package)
                        <div>
                            <span class="price-amount">{{ number_format($package->calculated_price, 0, ',', ' ') }}</span>
                            @if($package->sale_discount > 0)
                                <span class="price-original">{{ number_format($package->full_payment_price, 0, ',', ' ') }}</span>
                            @endif
                        </div>
                    @else
                        <div>
                            <span class="price-amount">39 500</span>
                            <span class="price-original">44 000</span>
                        </div>
                    @endif
                    @if($course)
                        <a href="{{ route('front.course.checkout', $course->id) }}" class="btn-cta">Sikre deg en plass nå</a>
                    @else
                        <a href="#" class="btn-cta">Sikre deg en plass nå</a>
                    @endif
                </div>
                <div class="price-box-right">
                    <ul class="price-features">
                        <li>Skriftlig materiale, videoer og webinarer som lærer deg skrivehåndverket.</li>
                        <li>Tilbakemeldinger på manus fra profesjonell redaktør – gjennom hele kursåret.</li>
                        <li>Ukentlig samskrivingsøkter med Kristine, din personlige skrivetrener.</li>
                        <li>Live webinarer med kjente forfattere og skrivelærere.</li>
                        <li>Tilgang til alle våre live gruppekurs under kursåret (roman, barnebok, novelle, dramatikk og "feelgood").</li>
                        <li>Du er garantert en utgivelse – vi gir ut en antologi med de beste tekstene fra kursåret.</li>
                        <li>Hjelp til utgivelse etter endt kursår.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         SECTION 6 – Course Overview
         ============================================================ --}}
    <section class="section-light course-overview" id="kursinnhold">
        <div class="container">
            <h2>Årskurset<br><span style="font-size:.7em;">– Vår 2026</span></h2>
            <div class="overview-intro">
                <p>Målet med kurset er at du skal fullføre et førsteutkast av manuset ditt, i samarbeid med en profesjonell redaktør. Du får en halvtimes coaching med din redaktør, i starten og slutten av året, og leverer et bestemt antall ord til fastsatte datoer.</p>
                <p>Undervisningen i kurset følger skriveprosessen din, fra idémyldring til redigering av egen tekst. Du får innføring i sentrale temaer innenfor kreativ skriving, og vi jobber kontinuerlig med å utvikle deg som forfatter.</p>
                <p>Du trenger ingen forkunnskaper for å være med. Det spiller ingen rolle hvor du er i prosessen, om du ikke har skrevet et ord eller har kommet et stykke på vei, eller hvilken sjanger du skriver i. Vi tilpasser oss etter ditt nivå og hvor du er i prosessen.</p>
            </div>
        </div>
    </section>

    {{-- ============================================================
         SECTION 7 – Timeline / Steps (kursplan)
         ============================================================ --}}
    <section class="section-light timeline-section">
        <div class="container">
            <div class="kursplan">

                {{-- Vertical bar with rotated text --}}
                <div class="kursplan-bar"><span>Kursåret – steg for steg</span></div>

                {{-- Week 0: Årskurset – våren 2026 + Den røde tråden --}}
                <div class="kp-week">
                    <div class="kp-heading"><h4>Årskurset – våren 2026</h4></div>
                    <div class="kp-card kp-card-gold">
                        <div class="kp-card-body">
                            <h4>Den røde tråden <span class="kp-badge">Ingrediensene i din fortelling</span></h4>
                            <p>Redaktøren hjelper deg med å finne den røde tråden i fortellingen din, og sammen legger dere en plan for hvordan prosjektet skal gjennomføres.</p>
                            <div class="kp-icons-row">
                                <div class="kp-icon-item">
                                    <img src="{{ asset('images/arskurs/steg-pitch.svg') }}" alt="Tema">
                                    <span>Tema</span>
                                </div>
                                <div class="kp-icon-item">
                                    <img src="{{ asset('images/arskurs/steg-protagonist.svg') }}" alt="Karakterer">
                                    <span>Karakterer</span>
                                </div>
                                <div class="kp-icon-item">
                                    <img src="{{ asset('images/arskurs/steg-plotting.svg') }}" alt="Plott">
                                    <span>Plott</span>
                                </div>
                                <div class="kp-icon-item">
                                    <img src="{{ asset('images/arskurs/steg-pov.svg') }}" alt="Synsvinkel">
                                    <span>Synsvinkel</span>
                                </div>
                                <div class="kp-icon-item">
                                    <img src="{{ asset('images/arskurs/steg-place.svg') }}" alt="Miljø">
                                    <span>Miljø</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Week 1: Kurset starter --}}
                <div class="kp-week">
                    <div class="kp-heading"><h4>Kurset starter</h4></div>
                    <p class="kp-desc">Årskurset lærer deg skrivehåndverket, en redaktør veileder deg gjennom manusprosessen, og en lukket skrivegruppe heier deg frem mot mål.</p>
                </div>

                {{-- Steg 1 --}}
                <div class="kp-week">
                    <div class="kp-heading"><h4>Steg 1</h4></div>
                    <div class="kp-card">
                        <div class="kp-card-icon">
                            <img src="{{ asset('images/arskurs/steg-beginnings.svg') }}" alt="Steg 1">
                        </div>
                        <div class="kp-card-content">
                            <h3>Tilgang og fellesskap</h3>
                            <p>Du får tilgang til kursmodulene, som dryppvis skal lære deg om skrivehåndverket. Vi går i gang med faglige webinarer og samskriving, som skal være ukentlige sesjoner gjennom hele året. Du får også tilgang til den lukkede skrivegruppen, som skal være din skrivefamilie under hele kursåret.</p>
                        </div>
                    </div>
                </div>

                {{-- Steg 2 --}}
                <div class="kp-week">
                    <div class="kp-heading"><h4>Steg 2</h4></div>
                    <div class="kp-card">
                        <div class="kp-card-icon">
                            <img src="{{ asset('images/arskurs/steg-secret-sauce.svg') }}" alt="Steg 2">
                        </div>
                        <div class="kp-card-content">
                            <h3>Planlegging og igangsetting</h3>
                            <p>Du skriver en prosjektbeskrivelse, så godt du klarer, som skal hjelpe oss med å finne riktig redaktør til ditt manus. Hvilket mål, og hvilke ønsker, har du for skrivingen?</p>
                        </div>
                    </div>
                </div>

                {{-- Steg 3 --}}
                <div class="kp-week">
                    <div class="kp-heading"><h4>Steg 3</h4></div>
                    <div class="kp-card">
                        <div class="kp-card-icon">
                            <img src="{{ asset('images/arskurs/steg-character.svg') }}" alt="Steg 3">
                        </div>
                        <div class="kp-card-content">
                            <h3>Din egen redaktør</h3>
                            <p>Du får utdelt din egen redaktør, samt svar på prosjektbeskrivelsen. Til sammen skal du levere inn 70 000 ord til redaktøren, i jevnlige bolker gjennom året, som du får grundig tilbakemelding på.</p>
                        </div>
                    </div>
                </div>

                {{-- Steg 4 --}}
                <div class="kp-week">
                    <div class="kp-heading"><h4>Steg 4</h4></div>
                    <div class="kp-card">
                        <div class="kp-card-icon">
                            <img src="{{ asset('images/arskurs/steg-plot-skills.svg') }}" alt="Steg 4">
                        </div>
                        <div class="kp-card-content">
                            <h3>Coachingtime med redaktøren</h3>
                            <p>Du får et halvtimes møte med din tildelte redaktør, via skjerm eller fysisk (det bestemmer dere selv), der dere snakker om skriveprosjektet og veien videre.</p>
                        </div>
                    </div>
                </div>

                {{-- Steg 5 --}}
                <div class="kp-week">
                    <div class="kp-heading"><h4>Steg 5</h4></div>
                    <div class="kp-card">
                        <div class="kp-card-icon">
                            <img src="{{ asset('images/arskurs/steg-description.svg') }}" alt="Steg 5">
                        </div>
                        <div class="kp-card-content">
                            <h3>Sosialt treff med rektor Kristine og skrivegruppen din</h3>
                            <p>Vi synes det er fint, og viktig, å møtes fysisk av og til. Rektor Kristine inviterer alle til sosialt og uhøytidelig treff i Oslo. Alle som vil kan komme!</p>
                        </div>
                    </div>
                </div>

                {{-- Collapsible extra steps --}}
                <div class="kp-extra" id="kursplanExtra">

                    {{-- Steg 6 --}}
                    <div class="kp-week">
                        <div class="kp-heading"><h4>Steg 6</h4></div>
                        <div class="kp-card">
                            <div class="kp-card-icon">
                                <img src="{{ asset('images/arskurs/steg-dialogue.svg') }}" alt="Steg 6">
                            </div>
                            <div class="kp-card-content">
                                <h3>Første innlevering av tekst</h3>
                                <p>Du leverer inn første tekst til redaktøren – 5000 ord. Dette er første del av de 70 000 ordene som skal leveres inn i løpet av året, og som du får tilbakemelding på.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Steg 7 --}}
                    <div class="kp-week">
                        <div class="kp-heading"><h4>Steg 7</h4></div>
                        <div class="kp-card">
                            <div class="kp-card-icon">
                                <img src="{{ asset('images/arskurs/steg-texture.svg') }}" alt="Steg 7">
                            </div>
                            <div class="kp-card-content">
                                <h3>Tilbakemelding fra redaktøren</h3>
                                <p>Du får tilbakemelding fra redaktøren på teksten du leverte inn. Dette er som regel en stor opplevelse for de fleste. Nå begynner teksten å leve, og du ser hva du konkret skal jobbe med videre.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Steg 8 --}}
                    <div class="kp-week">
                        <div class="kp-heading"><h4>Steg 8</h4></div>
                        <div class="kp-card">
                            <div class="kp-card-icon">
                                <img src="{{ asset('images/arskurs/steg-middles.svg') }}" alt="Steg 8">
                            </div>
                            <div class="kp-card-content">
                                <h3>Andre innlevering av tekst</h3>
                                <p>Nå skal du levere inn andre del av teksten din til redaktøren, 10 000 ord.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Steg 9 --}}
                    <div class="kp-week">
                        <div class="kp-heading"><h4>Steg 9</h4></div>
                        <div class="kp-card">
                            <div class="kp-card-icon">
                                <img src="{{ asset('images/arskurs/steg-writing-skills.svg') }}" alt="Steg 9">
                            </div>
                            <div class="kp-card-content">
                                <h3>Tilbakemelding fra redaktøren</h3>
                                <p>Du får tilbakemelding fra redaktøren på andre tekstdel.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Steg 10 --}}
                    <div class="kp-week">
                        <div class="kp-heading"><h4>Steg 10</h4></div>
                        <div class="kp-card">
                            <div class="kp-card-icon">
                                <img src="{{ asset('images/arskurs/steg-writing-techniques.svg') }}" alt="Steg 10">
                            </div>
                            <div class="kp-card-content">
                                <h3>Tredje innlevering av tekst</h3>
                                <p>Du leverer inn tredje innlevering av tekst til redaktøren – 15 000 ord.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Steg 11 --}}
                    <div class="kp-week">
                        <div class="kp-heading"><h4>Steg 11</h4></div>
                        <div class="kp-card">
                            <div class="kp-card-icon">
                                <img src="{{ asset('images/arskurs/steg-chapters.svg') }}" alt="Steg 11">
                            </div>
                            <div class="kp-card-content">
                                <h3>Tilbakemelding fra redaktøren</h3>
                                <p>Du får tilbakemelding på tredje innlevering av tekst fra redaktøren din.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Steg 12 --}}
                    <div class="kp-week">
                        <div class="kp-heading"><h4>Steg 12</h4></div>
                        <div class="kp-card">
                            <div class="kp-card-icon">
                                <img src="{{ asset('images/arskurs/steg-plot-skills-2.svg') }}" alt="Steg 12">
                            </div>
                            <div class="kp-card-content">
                                <h3>Fjerde og siste innlevering av tekst</h3>
                                <p>Du leverer inn fjerde og siste innlevering av tekst til redaktøren – 40 000 ord. Her legger vi som regel inn et sosialt og fysisk treff med rektor og skrivegruppen også!</p>
                            </div>
                        </div>
                    </div>

                    {{-- Steg 13 --}}
                    <div class="kp-week">
                        <div class="kp-heading"><h4>Steg 13</h4></div>
                        <div class="kp-card">
                            <div class="kp-card-icon">
                                <img src="{{ asset('images/arskurs/steg-doubling-up.svg') }}" alt="Steg 13">
                            </div>
                            <div class="kp-card-content">
                                <h3>Fjerde og siste tilbakemelding fra redaktøren</h3>
                                <p>Nå får du fjerde og siste tilbakemelding fra redaktøren din. Denne gangen får du også råd og tips til veien videre for manuset ditt.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Steg 14 --}}
                    <div class="kp-week">
                        <div class="kp-heading"><h4>Steg 14</h4></div>
                        <div class="kp-card">
                            <div class="kp-card-icon">
                                <img src="{{ asset('images/arskurs/steg-next-steps.svg') }}" alt="Steg 14">
                            </div>
                            <div class="kp-card-content">
                                <h3>Vi gir ut antologi – og hjelper deg med veien videre</h3>
                                <p>Vi gir ut en antologi med de beste tekstene fra kursåret, og hjelper deg med å finne veien videre – enten det er forlag eller egenutgivelse.</p>
                            </div>
                        </div>
                    </div>

                </div>{{-- /.kp-extra --}}

                {{-- Toggle button --}}
                <div class="kp-toggle-wrap">
                    <button type="button" class="kp-toggle-btn" id="kursplanToggle">Se hele kursplanen</button>
                </div>

            </div>{{-- /.kursplan --}}
        </div>
    </section>

    {{-- ============================================================
         SECTION 8 – Profesjonell tilbakemelding
         ============================================================ --}}
    <section class="section-light checklist-section">
        <div class="container">
            <div class="section-grid">
                <div class="section-text">
                    <h2 style="text-align:left;">Profesjonell tilbakemelding på tekst</h2>
                    <p>Du får tilgang til kursmateriell som lærer deg skrivehåndverket, og profesjonell tilbakemelding på egen tekst.</p>
                    <ul class="checklist">
                        <li>Skriftlig materiale og videoer som lærer deg håndverket.</li>
                        <li>Flere tilbakemeldinger på egen tekst fra redaktør.</li>
                        <li>Ukentlige fagwebinarer med forfattere, redaktører og skrivelærere.</li>
                        <li>Arkiv med hundrevis av tidligere mentormøter, som du kan se eller lytte til når du måtte ønske.</li>
                        <li>Tilgang til alle våre live gruppekurs under kursåret (roman, barnebok, novelle, dramatikk og "feelgood").</li>
                    </ul>
                </div>
                <div class="section-image">
                    <img src="{{ asset('images/arskurs/tilbakemelding.png') }}" alt="Tilbakemelding">
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         SECTION 9 – Lukket skrivegruppe
         ============================================================ --}}
    <section class="section-light checklist-section">
        <div class="container">
            <div class="section-grid">
                <div class="section-image" style="order:-1;">
                    <img src="{{ asset('images/arskurs/fellesskap-gruppe.png') }}" alt="Skrivegruppe">
                </div>
                <div class="section-text">
                    <h2 style="text-align:left;">Lukket skrivegruppe</h2>
                    <p>Du blir medlem av en lukket skrivegruppe sammen med de andre deltakerne – og oss, selvsagt – der vi samskriver, tipser og støtter og pusher hverandre frem mot målet om ferdig manus.</p>
                    <ul class="checklist">
                        <li>Ukentlige samskrivingsøkter, der vi skriver sammen og får fart på den kreative motoren.</li>
                        <li>Fysiske treff, der vi skravler om livet og litteraturen – og skriving, så klart.</li>
                        <li>Mulighet for å pilotlese hverandre, og oppdage andre skrivevenner.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         SECTION 10 – Skriv boken din på ett år
         ============================================================ --}}
    <section class="section-light checklist-section">
        <div class="container">
            <div class="section-grid">
                <div class="section-text">
                    <h2 style="text-align:left;">Skriv boken din på ett år – sammen med oss!</h2>
                    <ul class="checklist">
                        <li>Du får din egen redaktør som gir deg profesjonell tilbakemelding gjennom hele kursåret.</li>
                        <li>Du får tilgang til kursmoduler som lærer deg skrivehåndverket.</li>
                        <li>Du blir del av en lukket skrivegruppe med samskrivingsøkter og fysiske treff.</li>
                        <li>Du får tilgang til live webinarer med kjente forfattere og skrivelærere.</li>
                        <li>Du er garantert en utgivelse i antologi etter kursåret.</li>
                    </ul>
                    @if($course)
                        <a href="{{ route('front.course.checkout', $course->id) }}" class="btn-cta" style="margin-top:20px;">Meld deg på nå</a>
                    @else
                        <a href="#pamelding" class="btn-cta" style="margin-top:20px;">Meld deg på nå</a>
                    @endif
                </div>
                <div class="section-image">
                    <img src="{{ asset('images/arskurs/kristine-foto.jpg') }}" alt="Kristine S. Henningsen" style="border-radius:8px;">
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         SECTION 11 – Møt kursholderen din
         ============================================================ --}}
    <section class="section-dark kursholder-section" id="kursholdere">
        <div class="container">
            <h2>Møt kursholderen din</h2>
            <div class="kursholder-grid">
                <div class="kursholder-video">
                    <div class="wistia_responsive_padding" style="padding:56.25% 0 0 0;position:relative;">
                        <div class="wistia_responsive_wrapper" style="height:100%;left:0;position:absolute;top:0;width:100%;">
                            <div class="wistia_embed wistia_async_gz13go8vtj seo=true videoFoam=true" style="height:100%;position:relative;width:100%;">&nbsp;</div>
                        </div>
                    </div>
                </div>
                <div class="kursholder-text">
                    <p>Jeg er forfatter og har gitt ut mer enn 30 bøker på ulike forlag. Jeg er også rektor på Forfatterskolen, der jeg blant annet holder dette kurset.</p>
                    <p>Årskurset har hjulpet flere med å gi ut bøker, og det er jeg så stolt av!</p>
                    <p>På kurset fungerer jeg som en personlig skrivetrener – jeg får deg til å skrive på helt til du har et ferdig manusutkast.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         SECTION 12 – Instruktør-grid
         ============================================================ --}}
    <section class="section-light instructor-section">
        <div class="container">
            <h2>Lær fra profesjonelle forfattere og skrivelærere</h2>
            <p style="max-width:800px;margin:0 auto 20px;">Hver uke er det live webinarer med kursholderen og redaktørene, der vi samskriver, bearbeider innsendte tekster og tar for oss ulike skrivetemaer.</p>
            <p style="max-width:800px;margin:0 auto;">Vi har et arkiv med hundrevis av tidligere mentormøter, som du kan se og lytte til når du vil.</p>
            <img src="{{ asset('images/arskurs/team.png') }}" alt="Forfatterskolens team" class="instructor-image">
        </div>
    </section>

    {{-- ============================================================
         SECTION 12B – Deltakere / Testimonials
         ============================================================ --}}
    <section class="section-dark testimonials-section" id="deltakere">
        <div class="container">
            <h2>Hva sier deltakerne?</h2>
            <p class="testimonials-intro">Hør fra tidligere og nåværende årskursdeltakere om deres opplevelse.</p>

            @if(isset($testimonials) && $testimonials->count())
                {{-- Hero testimonials (with large images) --}}
                @foreach($testimonials->filter(fn($t) => !empty($t->author_image) && str_contains($t->author_image, 'arskurs/testimonials/') && !str_contains($t->author_image, 'alberto') && !str_contains($t->author_image, 'lise')) as $hero)
                <div class="testimonial-hero">
                    <div class="testimonial-hero-text">
                        <div class="testimonial-headline">" {{ $hero->description }} "</div>
                        <p>{{ $hero->testimony }}</p>
                        <span class="testimonial-name">{{ $hero->name }}</span>
                    </div>
                    <div class="testimonial-hero-image">
                        <img src="{{ asset($hero->author_image) }}" alt="{{ $hero->name }}">
                    </div>
                </div>
                @endforeach

                {{-- Card testimonials (without images) --}}
                <div class="testimonial-cards">
                    <div class="row">
                        @foreach($testimonials->filter(fn($t) => empty($t->author_image) || !str_contains($t->author_image, 'arskurs/testimonials/')) as $card)
                        <div class="col-md-4 col-sm-6">
                            <div class="testimonial-card">
                                <div class="testimonial-quote-icon">"</div>
                                <div class="testimonial-divider"></div>
                                <p>{{ $card->testimony }}</p>
                                <span class="testimonial-name">{{ $card->name }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Avatar testimonials (Alberto & Lise - with small circular images) --}}
                <div class="testimonial-avatars">
                    <div class="row">
                        @foreach($testimonials->filter(fn($t) => !empty($t->author_image) && (str_contains($t->author_image, 'alberto') || str_contains($t->author_image, 'lise'))) as $avatar)
                        <div class="col-md-6">
                            <div class="testimonial-avatar-card">
                                <div class="testimonial-avatar-header">
                                    <img src="{{ asset($avatar->author_image) }}" alt="{{ $avatar->name }}" class="testimonial-avatar-img">
                                    <span class="testimonial-avatar-name">{{ strtoupper($avatar->name) }}</span>
                                </div>
                                <p>{{ $avatar->testimony }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- ============================================================
         SECTION 13 – FAQ
         ============================================================ --}}
    <section class="section-dark faq-section" id="faq">
        <div class="container">
            <h2>Svar på dine spørsmål</h2>
            <div class="panel-group" id="faqAccordion">

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-bs-toggle="collapse" data-bs-parent="#faqAccordion" href="#faq1" class="collapsed">Hvorfor skal jeg ta dette kurset?</a>
                        </h4>
                    </div>
                    <div id="faq1" class="panel-collapse collapse">
                        <div class="panel-body">Årskurset gir deg alt du trenger for å fullføre og publisere manuset ditt – enten det er en fortelling, sakprosa, dikt eller noe helt annet.</div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-bs-toggle="collapse" data-bs-parent="#faqAccordion" href="#faq2" class="collapsed">Hva lærer jeg på kurset?</a>
                        </h4>
                    </div>
                    <div id="faq2" class="panel-collapse collapse">
                        <div class="panel-body">Når du melder deg på kurset får du tilgang til moduler som lærer deg å skrive i den sjangeren du ønsker. Underveis får du tilbakemeldinger fra din egen redaktør på manuset ditt. Du blir også del av en skrivegruppe som støtter og hjelper deg hele veien inn mot mål – og Kristine, rektor og skrivetrener, som arrangerer samskriving hver uke så du jevnlig får skrevet deg fremover.</div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-bs-toggle="collapse" data-bs-parent="#faqAccordion" href="#faq3" class="collapsed">Hvor lenge varer kurset?</a>
                        </h4>
                    </div>
                    <div id="faq3" class="panel-collapse collapse">
                        <div class="panel-body">Kurset varer omtrent like lenge som et skoleår – fra januar og frem til desember.</div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-bs-toggle="collapse" data-bs-parent="#faqAccordion" href="#faq4" class="collapsed">Hvordan er leksjonene lagt opp?</a>
                        </h4>
                    </div>
                    <div id="faq4" class="panel-collapse collapse">
                        <div class="panel-body">Du får tilgang til kursmoduler i den sjangeren du ønsker å skrive i. De fleste velger romankurset, ettersom dette kan regnes som et grunnleggende kurs i kreativ skriving. Skriver du selvbiografisk, eller sakprosa, får du tilgang til et slikt sjangerspesifikt kurs i tillegg.</div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-bs-toggle="collapse" data-bs-parent="#faqAccordion" href="#faq5" class="collapsed">Hvor lang tid tar de ulike leksjonene?</a>
                        </h4>
                    </div>
                    <div id="faq5" class="panel-collapse collapse">
                        <div class="panel-body">Du kan hoppe inn og ut av kursmodulene under hele året, så det er vanskelig å si nøyaktig. Men de fleste bruker noen timer i uken på å gå gjennom kursmateriell.</div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-bs-toggle="collapse" data-bs-parent="#faqAccordion" href="#faq6" class="collapsed">Hvor mye bør jeg skrive hver dag?</a>
                        </h4>
                    </div>
                    <div id="faq6" class="panel-collapse collapse">
                        <div class="panel-body">Du bør sette av noe tid til skriving hver dag, for å holde motoren oppe. Det kan være alt fra en halvtime til to timer. Under kursåret skal du skrive 70 000 ord, totalt, som du leverer inn i bolker til redaktøren. Den første innleveringen er på 5000 ord.</div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-bs-toggle="collapse" data-bs-parent="#faqAccordion" href="#faq7" class="collapsed">Hvordan er live webinarene lagt opp?</a>
                        </h4>
                    </div>
                    <div id="faq7" class="panel-collapse collapse">
                        <div class="panel-body">Mentormøtene på mandag kveld er for hele skolen. Dette er kjente forfattere som har foredrag og svarer på spørsmål. Tirsdager på dagtid er det samskriving og redigering med rektor. Torsdag kveld er det faglige webinarer med redaktørene våre, der de snakker om ulike temaer og svarer på spørsmål. Du får opptak av alt du ikke kan være med på.</div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-bs-toggle="collapse" data-bs-parent="#faqAccordion" href="#faq8" class="collapsed">Hvordan får jeg tilbakemelding fra redaktøren?</a>
                        </h4>
                    </div>
                    <div id="faq8" class="panel-collapse collapse">
                        <div class="panel-body">Du leverer inn tekster til redaktøren via e-post. Redaktøren leser gjennom og gir en grundig og detaljert tilbakemelding. Du må gjerne spørre redaktøren om alt du vil etterpå. Så arbeider du videre med teksten din til neste tilbakemelding. Dere har to coaching-timer der dere møtes, live og direkte, på starten og mot slutten av kurset.</div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-bs-toggle="collapse" data-bs-parent="#faqAccordion" href="#faq9" class="collapsed">Hva skjer hvis jeg melder meg på nå, og angrer?</a>
                        </h4>
                    </div>
                    <div id="faq9" class="panel-collapse collapse">
                        <div class="panel-body">Da får du pengene tilbake. Så enkelt er det. Det eneste unntaket er hvis du allerede har levert inn tekst til redaktøren og fått tilbakemelding – da har vi allerede brukt ressurser på deg.</div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-bs-toggle="collapse" data-bs-parent="#faqAccordion" href="#faq10" class="collapsed">Hva skjer hvis jeg ikke klarer å skrive ferdig manuset?</a>
                        </h4>
                    </div>
                    <div id="faq10" class="panel-collapse collapse">
                        <div class="panel-body">Da kan du få et års ekstra tilgang til kurset, for en symbolsk pris. Det er også mulig å melde seg på påbyggingsåret, og fortsette samarbeidet med redaktøren der. Summa summarum: Vi hjelper deg helt til du er i mål!</div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-bs-toggle="collapse" data-bs-parent="#faqAccordion" href="#faq11" class="collapsed">Er jeg garantert en utgivelse etter kurset?</a>
                        </h4>
                    </div>
                    <div id="faq11" class="panel-collapse collapse">
                        <div class="panel-body">Vi kan ikke garantere deg å komme gjennom hos de store forlagene, selvsagt. Men vi gjør hva vi kan for å øke sjansene dine, hvis du ønsker det. Flere og flere ønsker også å gi ut selv, og der stiller vi med komplette pakkeløsninger – og den beste kompetansen.</div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-bs-toggle="collapse" data-bs-parent="#faqAccordion" href="#faq12" class="collapsed">Må jeg betale hele kurset på en gang?</a>
                        </h4>
                    </div>
                    <div id="faq12" class="panel-collapse collapse">
                        <div class="panel-body">Nei, i checkout kan du velge ulike betalingsalternativer — blant annet delbetaling via Svea. Vilkårene bestemmes av Svea i selve kjøpsprosessen.</div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-bs-toggle="collapse" data-bs-parent="#faqAccordion" href="#faq13" class="collapsed">Hva hvis jeg vil bytte redaktør?</a>
                        </h4>
                    </div>
                    <div id="faq13" class="panel-collapse collapse">
                        <div class="panel-body">Det er fullt mulig å bytte redaktør, hvis det ikke fungerer så godt med den du har fått tildelt. Bare gi oss beskjed, så fikser vi det med en gang.</div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-bs-toggle="collapse" data-bs-parent="#faqAccordion" href="#faq14" class="collapsed">Kan jeg få hjelp med å gi ut bok etter kurset?</a>
                        </h4>
                    </div>
                    <div id="faq14" class="panel-collapse collapse">
                        <div class="panel-body">Absolutt! Vi kan hjelpe deg med alt fra redaksjonell bearbeidelse til omslag, trykk og distribusjon. Vi har også et eget forlag, og gir ut de beste manusene fra kursåret.</div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ============================================================
         SECTION 14 – Final Video + CTA
         ============================================================ --}}
    <section class="section-dark final-cta-section">
        <div class="container">
            <div class="video-wrapper" style="margin-bottom:50px;">
                <div class="wistia_responsive_padding" style="padding:56.25% 0 0 0;position:relative;">
                    <div class="wistia_responsive_wrapper" style="height:100%;left:0;position:absolute;top:0;width:100%;">
                        <div class="wistia_embed wistia_async_msmewkgwwr seo=true videoFoam=true" style="height:100%;position:relative;width:100%;">&nbsp;</div>
                    </div>
                </div>
            </div>

            <h2>Sikre deg en plass nå!</h2>
            @if($course)
                <p>{{ $course->title }} tar inn et begrenset antall deltakere.</p>
            @else
                <p>Årskurset tar inn et begrenset antall deltakere.</p>
            @endif

            <div class="price-box" style="margin-bottom:0;">
                <div class="price-box-left">
                    <h3>Påmelding med rabatt</h3>
                    <p class="price-subtitle">Du betaler ingenting før kurset er i gang.</p>
                    @if($package)
                        <div>
                            <span class="price-amount">{{ number_format($package->calculated_price, 0, ',', ' ') }}</span>
                            @if($package->sale_discount > 0)
                                <span class="price-original">{{ number_format($package->full_payment_price, 0, ',', ' ') }}</span>
                            @endif
                        </div>
                    @else
                        <div>
                            <span class="price-amount">39 500</span>
                            <span class="price-original">44 000</span>
                        </div>
                    @endif
                    @if($course)
                        <a href="{{ route('front.course.checkout', $course->id) }}" class="btn-cta">Sikre deg en plass nå</a>
                    @else
                        <a href="#" class="btn-cta">Sikre deg en plass nå</a>
                    @endif
                </div>
                <div class="price-box-right">
                    <ul class="price-features">
                        <li>Skriftlig materiale, videoer og webinarer som lærer deg skrivehåndverket.</li>
                        <li>Tilbakemeldinger på manus fra profesjonell redaktør – gjennom hele kursåret.</li>
                        <li>Ukentlig samskrivingsøkter med Kristine, din personlige skrivetrener.</li>
                        <li>Live webinarer med kjente forfattere og skrivelærere.</li>
                        <li>Tilgang til alle våre live gruppekurs under kursåret (roman, barnebok, novelle, dramatikk og "feelgood").</li>
                        <li>Du er garantert en utgivelse – vi gir ut en antologi med de beste tekstene fra kursåret.</li>
                        <li>Hjelp til utgivelse etter endt kursår.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

</div>{{-- /.arskurs-page --}}

{{-- Smooth scroll for anchor links + Kursplan toggle --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll
    document.querySelectorAll('.arskurs-page a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            var target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                var offset = document.querySelector('.page-nav') ? document.querySelector('.page-nav').offsetHeight : 0;
                var top = target.getBoundingClientRect().top + window.pageYOffset - offset;
                window.scrollTo({ top: top, behavior: 'smooth' });
            }
        });
    });

    // Kursplan expand/collapse
    var toggleBtn = document.getElementById('kursplanToggle');
    var extra = document.getElementById('kursplanExtra');
    if (toggleBtn && extra) {
        var isOpen = false;
        toggleBtn.addEventListener('click', function() {
            isOpen = !isOpen;
            extra.style.display = isOpen ? 'block' : 'none';
            toggleBtn.textContent = isOpen ? 'Skjul kursplanen' : 'Se hele kursplanen';
        });
    }
});
</script>
@stop
