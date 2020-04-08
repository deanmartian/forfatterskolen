@extends('frontend.layout')

@section('title')
    <title>Gro Dahle Page</title>
@stop

@section('content')

    <div class="henrik-page">
        <div class="container">
            <div class="row header">
                <div class="col-md-12 text-center">
                    <h2 class="theme-text">
                        En perfekt gave til den skriveglade!
                    </h2>
                    <h1>
                        Sett i gang! <br>
                        Virtuell workshop med Gro Dahle
                    </h1>
                </div>
            </div>

            <div class="row first-container">
                <div class="col-md-6">
                    <div class="owner-image-container" data-bg="https://www.forfatterskolen.no/images-new/gro-dahle-author-image.jpg">
                    </div>
                </div>
                <div class="col-md-6 d-flex owner-details-container">
                    <div class="align-self-center owner-details">
                        <p>
                            Gro Dahle har utgitt diktsamlinger, barnebøker og fortellinger, og har ledet en rekke kurs i
                            kreativ skriving.
                        </p>

                        <ul>
                            <li>
                                Hva gjør en god fortelling god?
                            </li>
                            <li>
                                Har du lyst til å skrive, men usikker på hvordan komme i gang og hva?
                            </li>
                            <li>
                                Har du vanskelig for å begynne?
                            </li>
                        </ul>

                        <p>
                            Da kan det hjelpe med noen igangsettere og et par grunnteknikker som jeg sjøl bruker, og som
                            de fleste av mine forfatterkollegaer også tyr til. Nemlig friskrift og projeksjoner.
                        </p>
                    </div> <!-- end owner-details-->
                </div> <!-- end owner-details-container -->
            </div> <!-- end first-container -->
        </div> <!-- end container -->

        <div class="question-container">
            <div class="container text-center">
                <h1 class="font-barlow-medium theme-text">
                    Stimulerer det intuitive språksenteret
                </h1>

                <p>
                    Friskriften handler om å ikke tenke, å holde armen sig hånda i gang med å skrive, men ikke tenke,
                    bare dytte armen inn i skrivingen, dytte hånda inn i språket og skrive uten å tenke og styre, uten
                    retning og mål, uten å være flink og gjøre riktig, bare skrive i vei, styrte av sted uten vurdering
                    og refleksjon og prestasjonsangst og tanke, tillate seg å skrive dårlig, fyke av sted med tusjen
                    eller kulepennen og selv henge etter med setningene foran deg, løpe av sted med ordene. Selvfølgelig
                    tenker vi! Men vi har flere språksystemet - og det å trene opp språksystemet rundt følelsessenteret
                    til å skrive i vei - og holde igjen på sjefen i kontrollsenteret i frontallappen, den logiske
                    pannelappene som gjør det så tungt å komme i gang, for denne sjefen tiltaler deg ikke å skrive hva
                    som helst, så kan vi heller la det andre og mer intuitive språksenteret løpe oss i vei. Jeg starter
                    alltid med friskriften. Den er full av energi og skriver meg til steder jeg ikke hadde noen anelse
                    om fantes.
                </p>
            </div> <!-- end container -->
        </div> <!-- end question-container -->

        <div class="third-container">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 d-flex left-container">
                        <div class="align-self-center">
                            <h1 class="font-barlow-medium theme-text mb-0">
                                To kursdager
                            </h1>
                            <p class="mt-5">
                                Det vil bli to virtuelle kurskvelder (alt skjer online), der Gro gir igangsettende og
                                kreativt forløsende oppgaver – og går gjennom tekster (et tilfeldig utvalg tekster fra
                                kursdeltakerne). Oppgavene kan du gjøre hjemme i din egen stue. Du får også mulighet til
                                å stille Gro spørsmål i pausene og etter endt kursdag. Vi skal ta for oss de viktige
                                temaene friskrift og projeksjon: 1) Igangsetting og friskrift: Friskrift er en metode
                                som er nyttig for faglitteratur så vel som skjønnlitteratur. Friskriften handler om å
                                slippe løs og holde prestasjonsangsten unna. Prestasjonsangsten ligger i kontrollen og
                                i den logiske styringen Og når vi er redde for å ikke skrive bra nok, gjøre godt nok,
                                gjøre riktig og presist nok, Så strammer den logiske og styrende frontal lappen til så
                                det blir nesten umulig å gjøre noe som helst. Det er da det kan være fint å ikke tenke,
                                skrive uten å styre. Selvfølgelig tenker vi, men hvis vi prøver å holde det logiske
                                språksenteret unna, kan vi fri oss fra styringen, Og vi har et språksenter rundt
                                følelsessenteret også, i det limbiske systemet, rundt følelseskjernen Amygdala! Dette
                                språksenteret trer i kraft, når vi klarer å holde språksenteret i pannen unna og
                                avledet. Og da får vi en mer løs og fri styring over språket, for da skriver vi fra et
                                annet sted. Projeksjoner betyr å kaste ut tanker mot et tilfeldig mønster eller
                                tilfeldige utgangspunkt. Projeksjoner er fine å kombinere med forskriften. De fleste
                                forfatterkollegene til Gro bruker en eller flere projeksjons-teknikker i starten av nye
                                skriveprosjekter.
                            </p>
                        </div> <!-- end left-container-->
                    </div> <!-- end left column -->
                    <div class="col-md-6 presenter-container">
                        <div id="presenter-carousel" class="carousel slide"
                             data-ride="carousel" data-interval="10000">

                            <!-- The slideshow -->
                            <div class="container carousel-inner no-padding">
                                <div class="carousel-item active">
                                    <img data-src="https://www.forfatterskolen.no/images-new/langeland/gro-dahle.png"
                                         alt="">
                                </div>

                                {{--<div class="carousel-item">
                                    <img data-src="https://www.forfatterskolen.no/images-new/langeland/henrik-med-text.jpg" alt="">
                                </div>--}}
                            </div> <!-- end carouse-inner -->

                            <!-- Left and right controls -->
                            {{--<a class="carousel-control-prev" href="#presenter-carousel" data-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </a>
                            <a class="carousel-control-next" href="#presenter-carousel" data-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </a>--}}
                        </div> <!-- end presenter-carousel -->

                        <div class="col-md-12">
                            <div class="story-details">
                                <h1 class="font-barlow-medium theme-text mb-0">
                                    Den magiske "famlefasen"
                                </h1>
                                <p class="font-montserrat-light mt-4">
                                    Og så er det projeksjonene, tilfeldige utgangspunkt. Jeg har ti projeksjonsoppgaver
                                    som jeg tror det kan være morsomt å prøve! Siden dette er workshop, skal vi også
                                    bygge litt råmateriale og gå gjennom tekster der og da, lese opp og få kommentarer.
                                    Dette er famlefase, så det er ja-fase, men alle ja-faser har potensial til å gå
                                    videre med, og det dukker opp biter og bilder og tekster og snutter som er verdt å
                                    hente fram og prøve ut og jobbe videre med. Igangsetting, famlefase og lek med
                                    råmateriale er min beste opplevelse av skriving. Det er den følelsen av å være i det
                                    kreative, å kjenne at det skjer ting, oppleve språket våkne og se fine tekster bli
                                    født! Det er magi rett og slett!
                                </p>
                            </div> <!-- end story-details -->
                        </div> <!-- end col-md-12 -->
                    </div> <!-- end presenter-container-->
                </div> <!-- end row -->

                <div class="row contemporary-writer">
                    <div class="container text-center">
                        <h1 class="font-barlow-medium theme-text mb-0">
                            En av våre ledende samtidsforfattere
                        </h1>

                        <p class="font-montserrat-light font-16 mt-5">
                            Gro Dahle har skrevet bøker i mange sjangre, som er oversatt til flere språk, og vunnet en
                            rekke litterære priser (Brageprisen, Kulturdepartementets pris for barne- og
                            ungdomslitteratur, Aschehoug-prisen, Triztan Vindtorns poesipris m.fl). Hun har holdt mange
                            populære skrivekurs og utgitt bøker om å skrive. Nå holder hun virtuell workshop for deg
                            som vil åpne opp dine kreative rom, og komme skikkelig i gang med skrivingen.
                        </p>
                    </div>
                </div> <!-- end contemporary-writer-->
            </div> <!-- end container -->
        </div> <!-- end third-container -->

        <div class="fourth-container" data-bg="https://www.forfatterskolen.no/images-new/langeland/testimonial-bg.png">
            <div class="container">
                <div class="col-md-8 head">
                    <div class="row">
                        <h1 class="font-barlow-medium">
                            Et legendarisk skrivekurs
                        </h1>

                        <p class="mt-4 font-montserrat-regular">
                            Kurset er åpent for alle, og opp gjennom årene har svært mange deltakere har hatt stort utbytte av å
                            delta på Langelands fortellekunst-seminar. Her er noen tilbakemeldinger:
                        </p>
                    </div>
                </div> <!-- end head-->

                <div class="col-md-12 px-0 mt-5">
                    <div id="testimonials-carousel" class="carousel slide"
                         data-ride="carousel" data-interval="10000">

                        <!-- Indicators -->
                        <ul class="carousel-indicators">
                            <li data-target="#testimonials-carousel" data-slide-to="0" class="active"></li>
                            <li data-target="#testimonials-carousel" data-slide-to="1"></li>
                            <li data-target="#testimonials-carousel" data-slide-to="2"></li>
                            <li data-target="#testimonials-carousel" data-slide-to="3"></li>
                        </ul>

                        <!-- The slideshow -->
                        <div class="container carousel-inner row">
                            <div class="carousel-item active">
                                <div class="col-sm-6 col-xs-12 h-100">
                                    <div class="card card-global rounded-0">
                                        <div class="testimonial-container my-auto">
                                            <p class="font-montserrat-regular">
                                                «Kurset er håndfast og konkret, avmystifiserer litterær skriving og legger
                                                vekt på håndverket, med mange gode tips til struktur og arbeidsprosess.»
                                            </p>

                                            <p class="font-montserrat-medium theme-text mb-0 mt-4">
                                                — Maja Lunde, forfatter
                                            </p>
                                        </div> <!-- end testimonial-container-->
                                    </div>
                                </div> <!-- end column -->
                                <div class="col-sm-6 col-xs-12 h-100">
                                    <div class="card card-global rounded-0">
                                        <div class="testimonial-container my-auto">
                                            <p class="font-montserrat-regular">
                                                «En super kursleder, teoretisk sterk, flink til å trekke ut nyttige
                                                prinsipper både fra litteraturen og ’kokebøkene’ om litteratur. Inspirerende
                                                å se hvordan han jobber selv. Han gir gode, presise råd og er engasjert.»
                                            </p>

                                            <p class="font-montserrat-medium theme-text mb-0 mt-4">
                                                — Anders Danielsen Lie, forfatter og skuespiller
                                            </p>
                                        </div> <!-- end testimonial-container-->
                                    </div>
                                </div> <!-- end column -->
                            </div>  <!-- end carousel-item -->

                            <div class="carousel-item">
                                <div class="col-sm-6 col-xs-12 h-100">
                                    <div class="card card-global rounded-0">
                                        <div class="testimonial-container my-auto">
                                            <p class="font-montserrat-regular">
                                                «Det finnes mange der ute som kan fiksjonsskrivingens håndverk, og som
                                                kan akademisk litterær analyse, men ikke mange som kan begge deler. Det
                                                kan Henrik. Kombinasjonen av solid akademisk bakgrunn og hands-on
                                                håndverkskunnskap er nok det som gjør kurset hans så bra.»
                                            </p>

                                            <p class="font-montserrat-medium theme-text mb-0 mt-4">
                                                — Espen Ytreberg, professor i medievitenskap
                                            </p>
                                        </div> <!-- end testimonial-container-->
                                    </div>
                                </div> <!-- end column -->
                                <div class="col-sm-6 col-xs-12 h-100">
                                    <div class="card card-global rounded-0">
                                        <div class="testimonial-container my-auto">
                                            <p class="font-montserrat-regular">
                                                «Verktøyene og metodene Langeland sverger til er nyttige for alle, og
                                                uansett hvordan man velger å forholde seg til tekst, bør man ha
                                                grunnleggende kjennskap til tradisjonell dramaturgi.»
                                            </p>

                                            <p class="font-montserrat-medium theme-text mb-0 mt-4">
                                                — Nicolai Houm, forfatter og redaktør
                                            </p>
                                        </div> <!-- end testimonial-container-->
                                    </div>
                                </div> <!-- end column -->
                            </div> <!-- end carousel-item -->

                            <div class="carousel-item">
                                <div class="col-sm-6 col-xs-12 h-100">
                                    <div class="card card-global rounded-0">
                                        <div class="testimonial-container my-auto">
                                            <p class="font-montserrat-regular">
                                                «Det er fint å få en gjennomgang av det teoretiske ved skrivefaget
                                                konsentrert over en helg.»
                                            </p>

                                            <p class="font-montserrat-medium theme-text mb-0 mt-4">
                                                — Axel Hellstenius, manus- og barnebokforfatter
                                            </p>
                                        </div> <!-- end testimonial-container-->
                                    </div>
                                </div> <!-- end column -->
                                <div class="col-sm-6 col-xs-12 h-100">
                                    <div class="card card-global rounded-0">
                                        <div class="testimonial-container my-auto">
                                            <p class="font-montserrat-regular">
                                                «Dette var et supert kurs. Akkurat det jeg trengte for å sette ting på
                                                plass i eget hode, og i egen tekst.»
                                            </p>

                                            <p class="font-montserrat-medium theme-text mb-0 mt-4">
                                                — Gro T. Fykse, forfatter
                                            </p>
                                        </div> <!-- end testimonial-container-->
                                    </div>
                                </div> <!-- end column -->
                            </div> <!-- end carousel-item -->

                            <div class="carousel-item">
                                <div class="col-sm-6 col-xs-12 h-100">
                                    <div class="card card-global rounded-0">
                                        <div class="testimonial-container my-auto">
                                            <p class="font-montserrat-regular">
                                                «Kurset var helt avgjørende for meg. Jeg trodde at jeg hadde skrevet
                                                ferdig halve debutboken min, men etter kurset dro jeg hjem og skrev om
                                                hele romanen.»
                                            </p>

                                            <p class="font-montserrat-medium theme-text mb-0 mt-4">
                                                — Kristine Tofte, forfatter
                                            </p>
                                        </div> <!-- end testimonial-container-->
                                    </div>
                                </div> <!-- end column -->
                                <div class="col-sm-6 col-xs-12 h-100">
                                    <div class="card card-global rounded-0">
                                        <div class="testimonial-container my-auto">
                                            <p class="font-montserrat-regular">
                                                «Anbefalast! Henrik Langeland er ein framifrå kurshaldar og
                                                skriveinspirator.»
                                            </p>

                                            <p class="font-montserrat-medium theme-text mb-0 mt-4">
                                                — Per Roger Sandvik, biblioteksjef Nesodden
                                            </p>
                                        </div> <!-- end testimonial-container-->
                                    </div>
                                </div> <!-- end column -->
                            </div> <!-- end carousel-item -->
                        </div> <!-- end carouse-inner -->
                    </div> <!-- end presenter-carousel -->
                </div> <!-- end col-md-12 -->
            </div> <!-- end container -->
        </div> <!-- end fourth-container -->

        <div class="fifth-container" data-bg="https://www.forfatterskolen.no/images-new/langeland/book-subtle-bg.png">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 d-flex left-container">
                        <div class="align-self-center">
                            <h1 class="font-barlow-medium mb-0 theme-text">
                                Praktiske opplysninger
                            </h1>

                            <p class="mt-4 mb-0">
                                Workshopen arrangeres over to påfølgende dager, fem timer per dag inkludert pauser.
                            </p>

                            <p class="mt-4 mb-0 border-bottom">
                                <span class="font-montserrat-medium">Hvor:</span> Alt foregår online. Det eneste du
                                trenger er en PC, pad eller mobil og enbrukbar internett-linje.
                            </p>

                            <p class="mt-4 mb-0 border-bottom">
                                <span class="font-montserrat-medium">Pris:</span> kr 1490,- (bindende påmelding)
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 right-container">
                        <h1 class="font-barlow-medium mb-0 theme-text">
                            Kursdatoer 2019
                        </h1>

                        <p class="mt-4 mb-5 border-bottom" style="padding-bottom: 30px">
                            Oppgavene fra Gro er ment for din egen del. Det blir likevel anledning til å sende inn
                            tekster, som Gro vil gjennomgå under workshopen. Hun plukker da ut et tilfeldig antall
                            tekster som gjennomgåes i plenum.
                        </p>
                    </div>
                </div>
            </div> <!-- end container -->
        </div> <!-- end fifth-container -->

        <div class="last-container">
            <div class="container text-center">
                <h1 class="font-barlow-regular">
                    <a href="{{ route('front.workshop.checkout', 12) }}" style="color: #fff; font-size: inherit">
                        Meld deg på workshopen her
                    </a>
                </h1>
            </div>
        </div>

    </div> <!-- end henrik-page -->

@stop
