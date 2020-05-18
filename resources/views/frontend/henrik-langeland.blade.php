@extends('frontend.layout')

@section('title')
    <title>Henrik Langeland Page</title>
@stop

@section('content')

    <div class="henrik-page"data-bg="https://www.forfatterskolen.no/images-new/langeland/bg-image.png">
        <div class="container">
            <div class="row header">
                <div class="col-md-12 text-center">
                    <img data-src="https://www.forfatterskolen.no/images-new/langeland/header-image.png" alt="">
                    <h1>
                        Henrik H. Langeland
                    </h1>
                </div>
            </div>

            <div class="row first-container">
                <div class="col-md-6">
                    <div class="owner-image-container"data-bg="https://www.forfatterskolen.no/images-new/langeland/henrik.jpg">
                    </div>
                </div>
                <div class="col-md-6 d-flex owner-details-container">
                    <div class="align-self-center owner-details">
                        <p>
                            Henrik H. Langeland (f. 1972) fikk sitt gjennombrudd som forfatter med romanen Wonderboy i
                            2003. Han har skrevet flere romaner og sakprosabøker.
                        </p>

                        <ul>
                            <li>
                                Hva gjør en god fortelling god?
                            </li>
                            <li>
                                Hvorfor er det slik at vi synes noen historier er kjedelige, mens vi lar oss begeistre
                                av andre?
                            </li>
                            <li>
                                Og hvilke grep er det forfattere bruker for å skape fortellinger vi blir oppslukt av?
                            </li>
                        </ul>

                        <p>
                            Å skrive en fortelling er noe alle synes er vanskelig. Så er det da heller ingen enkel kunst.
                        </p>
                    </div> <!-- end owner-details-->
                </div> <!-- end owner-details-container -->
            </div> <!-- end first-container -->
        </div> <!-- end container -->

        <div class="question-container">
            <div class="container text-center">
                <h1 class="font-barlow-medium theme-text">
                    Hvordan skape bedre fortellinger?
                </h1>

                <p>
                    Henrik Langeland skrivekurs retter seg mot dem som vil lære seg de konkrete mekanismene den godt
                    skrevne fortellingen er basert på. Siden 2008 har over tusen mennesker deltatt på seminarene hans -
                    ikke bare skjønnlitterære forfattere, men alle med ambisjoner om å skape bedre fortellinger. Kurset
                    er nyttig for enhver som har en historie de vil formidle på best mulig måte, enten man er i
                    startfasen, ønsker å komme videre i prosessen eller allerede har publisert tekster.
                </p>
            </div> <!-- end container -->
        </div> <!-- end question-container -->

        <div class="third-container">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 d-flex left-container">
                        <div class="align-self-center">
                            <h1 class="font-barlow-medium theme-text mb-0">
                                Tre virtuelle kursdager
                            </h1>
                            <p class="mt-5">
                                Dette kurset er et opptak av livekurset som Henrik holdt i Cappelen Damms lokaler i 2019.
                                Gjennom tre intensive økter går Langeland gjennom de grunnleggende bestanddelene i
                                fortellekunsten. Temaer som forholdet mellom språk og handling, interessevekkere,
                                tidsperspektiver, spenningsstrukturer, faktaformidling, verdivendinger,
                                kapitteloppbygging, karakterutvikling og synsvinkelbruk blir presentert slik at
                                deltakerne forstår hvordan disse elementene sammen virker i å skape en levende og
                                interessant fortelling - en fortelling man kan bli fengslet av.

                            </p>

                            <p class="mt-5">
                                I kurset tar også Langeland opp de rent praktiske delene ved skriveprosessen, slik som
                                stoffinnsamling, arbeidsrutiner og forlagsrelasjoner.
                            </p>

                            <p class="mt-5">
                                Det inneholder dessuten en egen del om å skrive manus for film og tv-serier.
                            </p>

                            <p class="mt-5">
                                Kursdeltakerne skal ikke levere inn egne tekster, men Henrik vil gi en oppgave på
                                tampen av hver kurskveld. Oppgaven er ment for din egen del.
                            </p>
                        </div> <!-- end left-container-->
                    </div> <!-- end left column -->
                    <div class="col-md-6 presenter-container">
                        <div id="presenter-carousel" class="carousel slide"
                             data-ride="carousel" data-interval="10000">

                            <!-- The slideshow -->
                            <div class="container carousel-inner no-padding">
                                <div class="carousel-item active">
                                    <img data-src="https://www.forfatterskolen.no/images-new/langeland/maja-text.jpg" alt="">
                                </div>

                                <div class="carousel-item">
                                    <img data-src="https://www.forfatterskolen.no/images-new/langeland/henrik-med-text.jpg" alt="">
                                </div>
                            </div> <!-- end carouse-inner -->

                            <!-- Left and right controls -->
                            <a class="carousel-control-prev" href="#presenter-carousel" data-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </a>
                            <a class="carousel-control-next" href="#presenter-carousel" data-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </a>
                        </div> <!-- end presenter-carousel -->

                        <div class="col-md-12">
                            <div class="story-details">
                                <h1 class="font-barlow-medium theme-text mb-0">
                                    Ut og stjæle hester og Fortellekunst
                                </h1>
                                <p class="font-montserrat-light mt-4">
                                    Mange ev eksemplene vil bli hentet fra Per Pettersons Ut og stjæle hester, så det er
                                    en fordel a ha lest den romanen i forkant. Seminaret er basert på Langelands
                                    Fortellekunst. En håndbok i litterære teknikker, men inneholder også et utvidet
                                    materiale. I tillegg har Langeland invitert en special guest, Erlend Loe, for å
                                    samtale om det praktiske arbeidet i å skape gode fortellinger.

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
                            Kursleder Henrik H. Langeland er en av våre ledende samtidsforfattere og har utgitt en rekke bøker,
                            blant annet romanene Wonderboy, Francis Meyers lidenskap, Fyrsten og Verdensmestrene. Han har
                            doktorgraden i litteraturvitenskap på en avhandling om Marcel Proust, vært redaktør for tidsskriftet
                            Vinduet og har hatt en rekke engasjementer i norsk nærings- og samfunnsliv.
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
                                <span class="font-montserrat-medium">Hvor:</span> Alt foregår online. Du trenger kun en
                                PC, pad eller mobil + en brukbar internettlinje.
                            </p>

                            <p class="mt-4 mb-0 border-bottom">
                                <span class="font-montserrat-medium">Hvordan:</span> Kurset vil skje over tre påfølgende
                                dager, der du hver kursdag vil få tilgang til én modul. Du kan se opptaket når som helst
                                i løpet av dagen. Fra klokken 20-21 er det Q & A (live) med Henrik, der du kan stille
                                spørsmål som er knyttet til kurset (eller skriverelatert, på en eller annen måte).
                                Opptaket er tilgjengelig i to uker etter at kurset er avsluttet.
                            </p>

                            <p class="mt-4 mb-0 border-bottom">
                                <span class="font-montserrat-medium">Pris:</span> Kroner 1490,- og kroner 990,- for
                                elever ved Forfatterskolen (med aktivt webinarabonnement)
                            </p>

                            <p class="mt-4 mb-0">
                                <span class="font-montserrat-medium">Alle spørsmål kan rettes til:</span>
                                <a href="mailto:post@forfatterskolen.no">post@forfatterskolen.no</a>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 right-container">
                        <h1 class="font-barlow-medium mb-0 theme-text">
                            Kursdatoer 2020
                        </h1>

                        <p class="mt-4 mb-5 border-bottom" style="padding-bottom: 30px">
                            Kurset går over tre påfølgende dager, 24. 25 og 26 mai. Det blir sluppet én kursmodul
                            (videoopptak) hver dag – og fra 20-21 kan du snakket med Henrik direkte (via webinar).

                        </p>

                        <h1 class="font-barlow-medium mb-0 theme-text">
                            Påmelding
                        </h1>

                        <p class="mt-4 mb-0">
                            Send bindende påmelding via lenken under (dersom du får problemer med bestillingen, ta
                            kontakt med <a href="mailto: support@forfatterskolen.no"> support@forfatterskolen.no</a>)
                        </p>
                    </div>
                </div>
            </div> <!-- end container -->
        </div> <!-- end fifth-container -->

        <div class="last-container">
            <div class="container text-center">
                <h1 class="font-barlow-regular">
                    <a href="{{ route('front.workshop.checkout', 13) }}" style="color: #fff; font-size: inherit">
                        Meld deg på skrivekurset her
                    </a>
                </h1>
            </div>
        </div>

    </div> <!-- end henrik-page -->

@stop
