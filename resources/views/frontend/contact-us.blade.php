@extends('frontend.layout')

@section('title')
    <title>Forfatterskolen &rsaquo; Kontakt Oss</title>
@stop

@section('styles')
    <style>
        .fa-arrows-h:before
    </style>
@stop

@section('content')
    <div class="contact-page">
        <div class="header text-center">
            <h1>
                Kontakt Oss
            </h1>
        </div>

        <div class="container">
            <div class="row main-container">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2 text-center">
                            <div class="editor-circle">
                                <img src="{{ asset('images/kristine.png') }}" alt="" class="rounded-circle">
                            </div>
                            <i>Foto: Vibeke Montero</i>
                        </div>
                        <div class="col-md-10 first-description">
                            <h2>
                                Forfatterskolen er en nettbasert skriveskole, med kurs innenfor flere sjangre. Se våre
                                kurs her: Forfatterskolens varierte og skreddersydde tilbud.
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 description-container">
                    <p>
                        Grunnlegger og rektor for skolen er Kristine Storli Henningsen, som er en suksessfull
                        skjønnlitterær-, seriebok-, sakprosa-, spennings- og selvhjelpsbokforfatter på henholdsvis
                        Gyldendal Forlag, Cappelen Damm, Schibsted, Juritzen og Flux. Til sammen har hun utgitt 26 bøker,
                        og debutromanen, I skyggen av store trær, er solgt til flere land.
                    </p>
                    <p>
                        Kristine har også jobbet som journalist i 15 år, og som redaktør i fem av dem. Hun var redaktør
                        for småbarnsmagasinet PlussTid i fem år. Skolens rektor står bak den populære bloggen
                        <a href="http://www.antisupermamma.no" class="text-theme">Antisupermamma</a>, som har over 20 000
                        følgere hver uke og har vært i en rekke medier de siste årene. Hun er også fast familieblogger
                        for VG.
                    </p>
                    <p>
                        Ved siden av skrivingen er Kristine utdannet gestaltpsykoterapeut ved NGI.
                    </p>
                    <p>
                        <span>Kristine</span> er ikke alene om å administrere skolen, som stadig vokser i omfang og har
                        flere hundre elever. Med seg på laget har hun flere dyktige manuskonsulenter og to faste ansatte.
                        Forfatterskolen har fulgt flere elever tett frem mot utgivelse, og ønsker å hjelpe frem mange
                        flere. De utgir også en elev i året, som ekslusivt blir plukket blant elevene og får
                        hedersbetegnelsen Årets Drømmeforfatter. Utgivelsen fullfinansieres av Forfatterskolen, og boken
                        vil bli utgitt av forlaget Forfatterdrøm, som er under paraplyen Forfatterskolen. Les mer om
                        forlaget her: <a href="http://www.forfatterdrom.no" class="text-theme">Forfatterdrøm</a>
                    </p>
                    <p>
                        Interessert i kurs eller noe annet skriverelatert? Send oss en mail:
                        <a href="mailto:post@forfatterskolen.no" class="text-theme">post@forfatterskolen.no</a>.
                    </p>
                </div> <!-- end description-container -->

            </div> <!-- end main-container -->

            <div class="row secondary-container">
                <div class="col-md-6">
                    <h1>Vår Eminente Ståb</h1>
                    <div class="row stab-row">
                        <ul>
                            <li>
                                <div class="row">
                                    <div class="col-sm-2 stab-image">
                                        <img src="{{ asset('images/hanne.png')  }}" class="rounded-circle">
                                    </div>
                                    <div class="col-sm-10">
                                        <h2>
                                            Hanne Einang
                                        </h2>
                                        <p>
                                            Hanne er vår grundige, dyktige og løsningsorienterte sekretær, assistent og mye
                                            annet. Hanne brenner for at elevene skal ha det bra på skolen, og er inne på det
                                            lukkede skriveforumet langt mer enn hun får betalt for.
                                        </p>
                                        <i class="fa fa-envelope"></i> <a href="mailto:Hanne@forfatterskolen.no">Hanne@forfatterskolen.no</a>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="row">
                                    <div class="col-sm-2 stab-image">
                                        <img src="{{ asset('images/elin.png')  }}" class="rounded-circle">
                                    </div>
                                    <div class="col-sm-10">
                                        <h2>
                                            Elin S Rotevatn
                                        </h2>
                                        <p>
                                            Elin S Rotevatn er vår faste redaktør. Hun har en Cand.mag-grad i allmenn
                                            litteraturvitenskap, og har i tillegg studert skriveteori gjennom en årrekke.
                                            Elin har jobbet som konsulent, blant annet for Riksantikvaren, og elsker å
                                            gå inn i andres tekst og finne forbedringspotensial - både på det
                                            strukturelle og det språklige plan.
                                        </p>
                                        <i class="fa fa-envelope"></i> <a href="mailto:Elin@forfatterskolen.no">Elin@forfatterskolen.no</a>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="row">
                                    <div class="col-sm-2 stab-image">
                                        <img src="{{ asset('images/sven.png')  }}" class="rounded-circle">
                                    </div>
                                    <div class="col-sm-10">
                                        <h2>
                                            Sven Inge Henningsen
                                        </h2>
                                        <p>
                                            Hva skulle vi gjort uten supportavdelingen? Sven Inge tar seg av alt det
                                            tekniske på skolen, ordner med betalinger og delbetalinger. tar seg av
                                            logistikken og altfor mye annet.
                                        </p>
                                        <i class="fa fa-envelope"></i> <a href="mailto:support@forfatterskolen.no">support@forfatterskolen.no</a>
                                        <br>
                                        <i class="img-icon arrows-h"></i> <a href="mailto:support@forfatterskolen.no">Fjernsupport</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <h1>Kontakt Oss I Dag</h1>
                    <div class="row contact-row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-sm-2 contact-image">
                                    <img src="{{ asset('images/kristine2.png')  }}" class="rounded-circle">
                                </div>
                                <div class="col-sm-10">
                                    <h2 class="author">
                                        KRISTINE STORLI HENNINGSEN
                                    </h2>
                                    <p class="author">
                                        Rektor
                                    </p>
                                </div>
                            </div>

                            <div class="row contact-info-container">
                                <div class="col-md-10 pl-0">
                                    <p>
                                        <i class="img-icon marker"></i>
                                        <span>Postboks 9233, 3028 Drammen</span>
                                    </p>
                                    <p>
                                        <i class="fa fa-envelope"></i>
                                        <span>post@forfatterskolen.no</span>
                                    </p>
                                    <p>
                                        <i class="img-icon telephone"></i>
                                        <span>+47 411 23 555</span>
                                    </p>
                                    <p>
                                        <i class="img-icon twitter"></i>
                                        <span>
                                            <i class="img-icon pinterest"></i>
                                        </span>
                                        <span>
                                            <i class="img-icon instagram"></i>
                                        </span>
                                        <span>
                                            <i class="img-icon facebook"></i>
                                        </span>
                                    </p>
                                </div>
                            </div> <!-- end contact-info-container -->

                            <div class="row contact-form-container">
                                <form method="POST" action="" onsubmit="disableSubmit(this)">
                                    {{ csrf_field() }}
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="fullname" placeholder="Navn" required
                                               value="{{ old('fullname') }}">
                                    </div>
                                    <div class="form-group">
                                        <input type="email" class="form-control" name="email" placeholder="E-postadresse" required
                                               value="{{ old('email') }}">
                                    </div>
                                    <div class="form-group">
                                        <textarea class="form-control" rows="1" name="message" placeholder="Skriv inn meldingen din" required>{{ old('message') }}</textarea>
                                    </div>
                                    <div class="form-group mb-0 custom-checkbox">
                                        <input type="checkbox" name="terms" required="" id="terms">
                                        <label class="accept-terms" for="terms">Jeg aksepterer <a href="http://forfatterskolen.local/opt-in-terms" target="_blank">vilkårene</a></label>
                                    </div>
                                    <p class="note">
                                        PS! Vi respekterer personvernretten og deler ikke e-posten din med noen.
                                    </p>

                                    {!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::display() !!}

                                    <div class="mt-4">
                                        <button type="submit" class="btn site-btn-global">Send</button>
                                    </div>
                                </form>

                                @if ( $errors->any() )
                                    <?php
                                        $alert_type = session('alert_type');
                                        if(!Session::has('alert_type')) {
                                            $alert_type = 'danger';
                                        }
                                    ?>
                                    <div class="alert alert-{{ $alert_type }} mt-4" style="width: 100%">
                                        <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                                        <ul>
                                            @foreach($errors->all() as $error)
                                                <li>{{$error}}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div> <!-- end contact-form-container -->
                        </div> <!-- end col-md-12 -->
                    </div> <!-- end contact-row -->

                    <div class="row save-data-container">
                        <h1>
                            Dine data, dine valg
                        </h1>
                        <p>
                            Forfatterskolen er den som behandler dine data. Dine data er trygge hos oss. Vi bruker dem
                            til å tilpasse tjenestene og tilbudene for deg.
                        </p>
                    </div> <!-- end save-data-container -->
                </div> <!-- end col-md-6 -->
            </div> <!-- end secondary-container -->
        </div>
    </div>
@stop

@section('scripts')
    {!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::renderJS() !!}
    <script>
        /* increase textarea height */
        let textarea = document.querySelector('textarea');

        textarea.addEventListener('keydown', autosize);

        function autosize(){
            let el = this;
            setTimeout(function(){
                el.style.cssText = 'height:auto; padding:0';
                // for box-sizing other than "content-box" use:
                // el.style.cssText = '-moz-box-sizing:content-box';
                let scrollHeight = el.scrollHeight + 15;
                el.style.cssText = 'height:' + scrollHeight + 'px';
            },0);
        }
    </script>
@stop