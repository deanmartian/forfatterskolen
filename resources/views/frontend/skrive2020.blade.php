@extends('frontend.layout')

@section('title')
    <title>Skrive 2020</title>
@stop

@section('content')

    <div class="henrik-page skrive2020">
        <div class="container">
            <div class="row header">
                <div class="col-md-12 text-center">
                    <h2 class="theme-text">
                        Kreativ i karantene
                    </h2>
                    <h1>
                        Forfatterskolens nye skrivekonkurranse: <br>
                        Isolasjon
                    </h1>
                </div>
            </div>

            <div class="row first-container">
                <div class="col-md-6">
                    <div class="owner-image-container">
                    </div>
                </div>
                <div class="col-md-6 d-flex owner-details-container">
                    <div class="align-self-center owner-details">
                        <p>
                            - Hold hodet klart og hjertet varmt, sa Oslos byrådsleder i går. Vi er nå inne i en rar og
                            skremmende tid, og i slike tider kan vi heldigvis snu oss til teksten. Å skrive kan fungere
                            som en god virkelighetsflukt. Vi inviterer dermed til skrivekonkurranse – og håper mange vil
                            delta!
                        </p>

                        <ul>
                            <li>
                                1.premie: Gratis manusutvikling av din tekst (105 000 ord), til en verdi
                                av 7 800,
                            </li>
                            <li>
                                2.premie: Gratis manusutvikling av din tekst (52 500 ord), til en verdi av
                                5000,
                            </li>
                            <li>
                                3.premie: Gratis manusutvikling av din tekst (17 500 ord), til en verdi av 2
                                900,
                            </li>
                        </ul>

                        <p>
                            Vi kårer tre vinnere
                        </p>

                        <p>
                            Her kan du lese mer om
                            <a href="https://www.forfatterskolen.no/shop-manuscript" target="_blank">
                                manusutviklingene
                            </a> (premien)
                        </p>
                    </div> <!-- end owner-details-->
                </div> <!-- end owner-details-container -->
            </div> <!-- end first-container -->
        </div> <!-- end container -->

        <div class="question-container">
            <div class="container text-center">
                <h1 class="font-barlow-medium theme-text">
                    Temaet for konkurransen er: ISOLASJON
                </h1>

                <p>
                    Temaet er bredt, her er mange tolkninger, vinklinger og sjangre tilgjengelig for deg. Kun fantasien
                    kan sette grenser. Vi tar imot alle sjangre. Teksten kan være opptil 5 A4-sider. Alle tekster må
                    leveres i Word/PDF, med skriftstørrelse 12
                </p>
            </div> <!-- end container -->
        </div> <!-- end question-container -->

        <div class="third-container">
            <div class="container">
                <div class="row contemporary-writer mt-0">
                    <div class="container text-center">
                        <h1 class="font-barlow-medium theme-text mb-0">
                            Innlevering og frist
                        </h1>

                        <p class="font-montserrat-light font-16 mt-5">
                            FRIST for innlevering: 1.april 2020 Vinnerteksten vil bli publisert på Forfatterskolens
                            nettside (dersom forfatteren samtykker).
                        </p>
                    </div>
                </div> <!-- end contemporary-writer-->
            </div> <!-- end container -->
        </div> <!-- end third-container -->

        <div class="last-container">
            <div class="container text-center">
                <h1 class="font-barlow-regular" style="cursor: pointer" id="redirect-link">
                    Lever inn din tekst
                </h1>
            </div>
        </div>

    </div> <!-- end henrik-page -->

@stop

@section('scripts')
    <script>
        $("#redirect-link").click(function(){
            window.open('/skrive2020', '_blank');
        });
    </script>
@stop
