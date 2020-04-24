@extends('frontend.layout')

@section('title')
    <title>Gro Dahle Page</title>
@stop

@section('content')

    <div class="henrik-page gro-dahle-page">
        <div class="container">
            <div class="row header">
                <div class="col-md-12 text-center">
                    <h2 class="theme-text">
                        Virtuell workshop med Gro Dahle
                    </h2>
                    <h1>
                        Sett i gang!
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
                            Da kan det hjelpe med noen igangsettere og et par grunnteknikker som Gro sjøl bruker, og som
                            de fleste av hennes forfatterkollegaer også tyr til. Nemlig friskrift og projeksjoner.
                        </p>
                    </div> <!-- end owner-details-->
                </div> <!-- end owner-details-container -->
            </div> <!-- end first-container -->
        </div> <!-- end container -->

        <div class="question-container">
            <div class="container text-center">
                <h1 class="font-barlow-medium theme-text">
                    To stimulerende kursdager
                </h1>

                <p>
                    Det vil bli to virtuelle kurskvelder (alt skjer online), der Gro gir igangsettende og
                    kreativt forløsende oppgaver – og går gjennom tekster (et tilfeldig utvalg tekster fra
                    kursdeltakerne). Oppgavene kan du gjøre hjemme i din egen stue.
                </p>

                <p>
                    Du får også mulighet til å stille Gro spørsmål i pausene og etter endt kursdag. Vi skal ta for oss
                    de viktige temaene friskrift og projeksjon:
                </p>
                <p>
                    <b>Friskrift:</b> Friskrift er en metode som er nyttig for faglitteratur så vel som skjønnlitteratur.
                    Friskriften handler om å slippe løs og holde prestasjonsangsten unna. Prestasjonsangsten ligger i
                    kontrollen og i den logiske styringen Og når vi er redde for å ikke skrive bra nok, gjøre godt nok,
                    gjøre riktig og presist nok, Så strammer den logiske og styrende frontal lappen til så det blir
                    nesten umulig å gjøre noe som helst. Det er da det kan være fint å ikke tenke, skrive uten å styre.
                    Selvfølgelig tenker vi, men hvis vi prøver å holde det logiske språksenteret unna, kan vi fri oss
                    fra styringen, Og vi har et språksenter rundt følelsessenteret også, i det limbiske systemet, rundt
                    følelseskjernen Amygdala! Dette språksenteret trer i kraft, når vi klarer å holde språksenteret i
                    pannen unna og avledet. Og da får vi en mer løs og fri styring over språket, for da skriver vi fra
                    et annet sted.
                </p>
                <p>
                    <b>Projeksjoner:</b> Projeksjoner betyr å kaste ut tanker mot et tilfeldig mønster eller
                    tilfeldige utgangspunkt. Projeksjoner er fine å kombinere med forskriften. De fleste
                    forfatterkollegene til Gro bruker en eller flere projeksjons-teknikker i starten av nye
                    skriveprosjekter.
                </p>
            </div> <!-- end container -->
        </div> <!-- end question-container -->

        <div class="third-container">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 d-flex left-container">
                        <div class="align-self-center">
                            <h1 class="font-barlow-medium theme-text mb-0">
                                Stimulerer det intuitive språksenteret
                            </h1>
                            <p class="mt-5">
                                Friskriften handler om å ikke tenke, å holde armen sig hånda i gang med å skrive, men
                                ikke tenke, bare dytte armen inn i skrivingen, dytte hånda inn i språket og skrive uten
                                å tenke og styre, uten retning og mål, uten å være flink og gjøre riktig, bare skrive i
                                vei, styrte av sted uten vurdering og refleksjon og prestasjonsangst og tanke, tillate
                                seg å skrive dårlig, fyke av sted med tusjen eller kulepennen og selv henge etter med
                                setningene foran deg, løpe av sted med ordene.
                            </p>

                            <p class="mt-5">
                                Selvfølgelig tenker vi! Men vi har flere språksystemet - og det å trene opp
                                språksystemet rundt følelsessenteret til å skrive i vei - og holde igjen på sjefen i
                                kontrollsenteret i frontallappen, den logiske pannelappene som gjør det så tungt å komme
                                i gang, for denne sjefen tiltaler deg ikke å skrive hva som helst, så kan vi heller la
                                det andre og mer intuitive språksenteret løpe oss i vei.
                            </p>

                            <p class="mt-5">
                                Gro sier: «Jeg starter alltid med friskriften. Den er full av energi og skriver meg til
                                steder jeg ikke hadde noen anelse om fantes».
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
                                    Og så er det projeksjonene, tilfeldige utgangspunkt. Gro har ti projeksjonsoppgaver
                                    som hun tror det kan være morsomt å prøve! Siden dette er workshop, skal vi også
                                    bygge litt råmateriale og gå gjennom tekster der og da, lese opp og få kommentarer.
                                    Dette er famlefase, så det er ja-fase, men alle ja-faser har potensial til å gå
                                    videre med, og det dukker opp biter og bilder og tekster og snutter som er verdt å
                                    hente fram og prøve ut og jobbe videre med. Igangsetting, famlefase og lek med
                                    råmateriale er Gros beste opplevelse av skriving. Hun sier: "Det er den følelsen av
                                    å være i det kreative, å kjenne at det skjer ting, oppleve språket våkne og se fine
                                    tekster bli født. Det er magi rett og slett!"
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
                            Kurset er åpent for alle, og opp gjennom årene har svært mange deltakere har hatt stort
                            utbytte av å delta på Gros skrivekurs. Her er et par tilbakemeldinger:
                        </p>
                    </div>
                </div> <!-- end head-->

                <div class="col-md-12 px-0 mt-5">
                    <div id="testimonials-carousel" class="carousel slide"
                         data-ride="carousel" data-interval="10000">

                        <!-- Indicators -->
                        <ul class="carousel-indicators">
                            <li data-target="#testimonials-carousel" data-slide-to="0" class="active"></li>
                        </ul>

                        <!-- The slideshow -->
                        <div class="container carousel-inner row">
                            <div class="carousel-item active">
                                <div class="col-sm-6 col-xs-12 h-100">
                                    <div class="card card-global rounded-0">
                                        <div class="testimonial-container my-auto">
                                            <p class="font-montserrat-regular">
                                                "Jeg ante ikke at jeg kunne bli så gira av et skrivekurs. Jeg ser alt
                                                klarere nå, og ikke minst vil jeg bare skrive, skrive, skrive"
                                            </p>

                                            <p class="font-montserrat-medium theme-text mb-0 mt-4">
                                                — Marthe
                                            </p>
                                        </div> <!-- end testimonial-container-->
                                    </div>
                                </div> <!-- end column -->
                                <div class="col-sm-6 col-xs-12 h-100">
                                    <div class="card card-global rounded-0">
                                        <div class="testimonial-container my-auto">
                                            <p class="font-montserrat-regular">
                                                "Gro har en tilstedeværelse som kommer gjennom skjermen, og kan
                                                inspirere en gråstein til å skrive kreativt. Gjennomgangen hennes av
                                                tekster er også utrolig konkret og lærerikt".
                                            </p>

                                            <p class="font-montserrat-medium theme-text mb-0 mt-4">
                                                — Kristine
                                            </p>
                                        </div> <!-- end testimonial-container-->
                                    </div>
                                </div> <!-- end column -->
                            </div>  <!-- end carousel-item -->
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
                                trenger er en PC, pad eller mobil og en brukbar internett-linje.
                            </p>

                            <p class="mt-4 mb-0 border-bottom">
                                <span class="font-montserrat-medium">Pris:</span> kr 1490,- (bindende påmelding) <br>
                                <span class="font-montserrat-medium">For elever med aktivt abonnement:</span>
                                Kroner 990,-
                            </p>

                            <p class="mt-4 mb-0 border-bottom">
                                <span class="font-montserrat-medium">Dato:</span> Søndag og mandag 3. og 4. mai <br>
                                Søndag 1900-2100 <br>
                                Mandag 1800-2200
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 right-container">
                        <h1 class="font-barlow-medium mb-0 theme-text">
                            Kursoppgaver
                        </h1>

                        <p class="mt-4 mb-5 border-bottom" style="padding-bottom: 30px">
                            Oppgavene fra Gro er ment for din egen del. Det blir likevel anledning til å sende inn
                            tekster, som Gro vil gjennomgå under workshopen. Hun plukker da ut et tilfeldig antall
                            tekster som gjennomgåes i plenum. Informasjon om tekster og innsending vil bli gitt i egen
                            mail etter påmelding.
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
