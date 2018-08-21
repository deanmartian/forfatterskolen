@extends('frontend.layout')

@section('title')
    <title>Terms &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
    <style>
        @import url("https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css");
    p, .font-17  {
        font-size: 17px;
    }

    .terms-title {
        font-size: 45px;
    }

    .panel-title > a:before {
        float: right !important;
        font-family: FontAwesome;
        content:"\f068";
        padding-right: 5px;
    }
    .panel-title > a.collapsed:before {
        float: right !important;
        content:"\f067";
    }
    .panel-title > a:hover,
    .panel-title > a:active,
    .panel-title > a:focus  {
        text-decoration:none;
    }
    </style>
@stop

@section('content')
    <div class="account-container">

        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content">
            <div class="col-sm-12">
                <h2 class="text-center terms-title">Vårt løfte</h2>

                <p>
                    Forfatterskolen Co., Ltd., (UK) (behandlingsansvarlig for EU), og
                    våre filialer ("Forfatterskolen", "vi", "oss", "vår") vet hvor viktig personvern er for våre kunder og
                    brukere, og vi etterstreber å gi klare opplysninger om hvordan vi samler inn, bruker, meddeler,
                    overfører og lagrer dine personopplysninger.
                </p>

                <p>
                    Denne personvernerklæringen gjelder alle Forfatterskolen-enheter, -nettsider, kundeserviceplattformer eller
                    andre nettapplikasjoner som henviser eller kobler til erklæringen (samlet kalt våre "tjenester").
                    Denne personvernerklæringen gjelder uavhengig av om du bruker en datamaskin, en mobiltelefon, et
                    nettbrett, et TV-apparat eller et husholdningsapparat eller en annen smart-enhet for å få tilgang
                    til våre tjenester.
                </p>

                <p>
                    Nedenfor finner du et sammendrag av de viktigste punktene i vår personvernerklæring. For ytterligere
                    informasjon om hvordan vi behandler opplysningene dine, klikk på overskriftene eller fortsett
                    lesningen nedenfor.
                </p>

                <h3 class="text-center">
                    Opplysninger som vi samler inn
                </h3>

                <p>
                    Vi samler inn ulike opplysninger i forbindelse med tjenestene, inkludert:
                </p>
                
                <ul class="font-17">
                    <li>Opplysninger som du oppgir direkte til oss;</li>
                    <li>Opplysninger som vi samler inn om din bruk av våre tjenester;</li>
                    <li>Opplysninger som vi innhenter fra tredjepartskilder.</li>
                </ul>

                <p>
                    Vi kan også be om særskilt samtykke til å samle inn informasjon eller gi deg separate meldinger om
                    hvordan vi samler inn dine personopplysninger på en måte som ikke er forklart i denne
                    personvernerklæringen, i henhold til det som kreves for enkelte tilleggstjenester.
                </p>

                <h3 class="text-center">
                    Bruk og deling av opplysninger
                </h3>

                <p>
                    Vi bruker opplysninger som vi samler inn, blant annet til:
                </p>

                <ul class="font-17">
                    <li>å levere tjenestene du ber om;</li>
                    <li>å forstå hvordan du bruker tjenestene, slik at vi kan forbedre brukeropplevelsen;</li>
                    <li>å lære mer om våre kunder, slik at vi kan kommunisere mest mulig effektivt med deg, og tilby deg
                        de mest relevante tjenestene og brukeropplevelsene;</li>
                    <li>Levere tilpasset innhold og reklame med ditt særskilte samtykke hvor dette er påkrevd.</li>
                </ul>

                <p>
                    Vi kan dele opplysninger om deg med:
                </p>

                <ul class="font-17">
                    <li>
                        tilknyttede selskaper – selskaper som er knyttet til Forfatterskolen Co., Ltd. gjennom
                        felles eierskap eller kontroll.
                    </li>
                    <li>
                        forretningspartnere – selskaper vi stoler på, og som kan levere informasjon om produkter og
                        tjenester du kan tenkes å like, forutsatt at du har gitt ditt særskilte samtykke til dette.
                    </li>
                    <li>
                        tjenesteleverandører – selskaper som leverer tjenester til eller på vegne av Forfatterskolen.
                    </li>
                    <li>
                        rettshåndhevende myndigheter – når vi er rettslig forpliktet til det, eller for å beskytte
                        Forfatterskolen og deres brukere.
                    </li>
                </ul>

                <h3 class="text-center">Tilleggsinformasjon om visse produkter og tjenester</h3>

                <ul class="font-17">
                    <li>
                        Selv om personvernerklæringen gjelder for alle tjenestene våre, vil vi også gi deg egne
                        personverntillegg som gir ytterligere informasjon om retningslinjene våre knyttet til visse
                        tjenester når dette er nødvendig. Disse tilleggene gjelder for din bruk av tjenestene som
                        tilleggene dekker.
                    </li>
                </ul>

                <h3 class="text-center">Kontaktinformasjon</h3>

                <p>
                    address here
                </p>

                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingOne">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                    FORFATTERSKOLEN GLOBALE PERSONVERNERKLÆRING
                                </a>
                            </h4>

                        </div>
                        <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                            <div class="panel-body">
                                Ikrafttredelsesdato: 25. mai 2018

                                Forfatterskolen Co., Ltd., (UK) (behandlingsansvarlig for EU), og våre filialer
                                ("Forfatterskolen", "vi", "oss", "vår") vet hvor viktig personvern er for våre kunder,
                                og vi etterstreber å gi klare opplysninger om hvordan vi samler inn, bruker, meddeler,
                                overfører og lagrer dine personopplysninger. Personvernerklæringen forklarer
                                retningslinjene våre knyttet til opplysninger. Denne personvernerklæringen gjelder alle
                                Forfatterskolen-enheter, -nettsider, kundeserviceplattformer eller andre
                                nettapplikasjoner som henviser eller kobler til erklæringen (samlet kalt våre "tjenester").
                                Denne personvernerklæringen gjelder uavhengig av om du bruker en datamaskin, en
                                mobiltelefon, et nettbrett, et TV-apparat eller et husholdningsapparat eller en annen
                                smart enhet for å få tilgang til våre tjenester. Denne erklæringen omfatter også
                                kundestøtten for slike enheter, nettsteder og nettapplikasjoner.
                                Selv om denne personvernerklæringen gjelder for alle våre tjenester, vil vi også gi deg
                                egne personverntillegg som gir ytterligere informasjon om retningslinjene våre knyttet
                                til visse tjenester når dette er nødvendig. Disse tilleggene gjelder for din bruk av
                                tjenestene som tilleggene dekker.
                                Det er viktig at du leser personvernerklæringen og eventuelle andre tillegg vi har gitt
                                deg nøye, fordi disse dokumentene forklarer hvordan dine personopplysninger behandles
                                hver gang du bruker tjenestene.

                                Du bør dessuten se etter oppdateringer av personvernerklæringen regelmessig. Hvis vi
                                oppdaterer personvernerklæringen, vil vi på forhånd informere deg om endringer som vi
                                anser som viktige ved å legge ut et varsel i de aktuelle tjenestene eller ved å sende
                                deg en e-post der dette er hensiktsmessig. Den nyeste oppdaterte versjonen av
                                personvernerklæringen vil alltid være tilgjengelig her. "Ikrafttredelsesdatoen" øverst
                                viser når personvernerklæringen sist ble oppdatert.
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingTwo">
                            <h4 class="panel-title">
                                <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    OPPLYSNINGER SOM VI SAMLER INN
                                </a>
                            </h4>

                        </div>
                        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                            <div class="panel-body">Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid.</div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingThree">
                            <h4 class="panel-title">
                                <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Title
                                </a>
                            </h4>

                        </div>
                        <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                            <div class="panel-body">Body</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="clearfix"></div>

    </div>

@stop