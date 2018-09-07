@extends('frontend.layout')

@section('title')
    <title>Free Webinar &rsaquo; {{ $freeWebinar->title }}</title>
@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/free-webinar.css') }}">
@stop

@section('content')

    <?php
    $presenters = [];
    foreach ($freeWebinar->webinar_presenters as $presenter) {
        $presenters[] = $presenter->first_name.' '.$presenter->last_name;
    }

    $last_element = array_pop($presenters);
    $presenterList = $presenters
        ? implode(', ', $presenters).' and '.$last_element
        : $last_element;

    date_default_timezone_set('US/Eastern');
    $currenttime = date('ga:i:s:u',strtotime($freeWebinar->start_date));
    list($hrs,$mins,$secs,$msecs) = explode(':',$currenttime);
    $eastern =  $hrs." Eastern, ";

    date_default_timezone_set('Pacific/Easter');
    $currenttime = date('ga:i:s:u');
    list($hrs,$mins,$secs,$msecs) = explode(':',$currenttime);
    $pacific =  $hrs." Pacific";


    $date = new DateTime($freeWebinar->start_date);
    $timestamp = $date->getTimestamp();

    $substr3 = "<span class='highlight'>".substr($freeWebinar->title, 0, 3)."</span>";// format the first 3 characters
    $lastTexts = substr($freeWebinar->title, 3, 100);//get the remaining string, 100 is just specified for words
    $webinarTitle = $substr3.$lastTexts;
    ?>

    <div class="container">
        <div class="courses-hero text-center">
            <div class="row">
                <div class="col-sm-12">
                    <h2 class="text-uppercase">{!! $webinarTitle !!}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="container free-webinar-container">
        <div class="row">
            <div class="custom-column">
                <div class="col-sm-5 left-container">
                    <div class="presents">
                        <div class="col-xs-5 no-left-padding circle">
                            <div class="text-container">
                                <span>{{ ucwords(\App\Http\FrontendHelpers::convertMonthLanguage($date->format('n'))) }}</span>
                                <h1>{{ $date->format('d') }}</h1>
                            </div>
                        </div>

                        <div class="col-xs-7 schedule no-right-padding">
                            <span class="day">{{ ucwords(\App\Http\FrontendHelpers::convertDayLanguage($date->format('N'))) }}</span> <br>
                            <span class="day">{{ $date->format('d').' '.ucwords(\App\Http\FrontendHelpers::convertMonthLanguage($date->format('n'))) }}</span> <br>
                            <span class="time">Klokken {{ \Carbon\Carbon::parse($freeWebinar->start_date)->format('H:i') }}</span>
                        </div>
                    </div> <!-- end presents -->

                    <div class="clearfix"></div>

                    <div class="divider"></div>

                    <div class="presenters">
                        @if($freeWebinar->webinar_presenters->count())
                            @foreach($freeWebinar->webinar_presenters->chunk(2) as $presenters)
                                <div class="row">
                                    @foreach($presenters as $presenter)
                                        <div class="col-sm-6 presenter-container">
                                            <img src="{{ $presenter->image ? $presenter->image : asset('images/user.png') }}">
                                            <p class="presenter-name">
                                                {{ $presenter->first_name }} {{ $presenter->last_name }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>


                <div class="col-sm-7 right-container">
                    <div class="jumbotron text-center">
                        <span class="header">
                            Webinaret starter om
                        </span>

                        <div id="timer">
                            <ul id="countdown" class="role-element leadstyle-countdown">
                                <li>
                                    <span class="countdown" id="days">00</span>
                                    <span>:</span>
                                </li>
                                <li>
                                    <span class="countdown" id="hours">00</span>
                                    <span>:</span>
                                </li>
                                <li>
                                    <span class="countdown" id="minutes">00</span>
                                    <span>:</span>
                                </li>
                                <li>
                                    <span class="countdown" id="seconds">00</span>
                                </li>
                            </ul>
                        </div>

                        <p class="description">
                            {{ $freeWebinar->description }}
                        </p>
                    </div> <!-- end jumbotron -->

                    <h2 class="reserve-text">
                        Reserver min plass her
                    </h2>


                    <form action="{{ route('front.free-webinar', $freeWebinar->id) }}" method="POST">
                        {{ csrf_field() }}
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <input type="text" name="first_name" class="form-control" placeholder="Fornavn" value="{{ old('first_name') }}"
                                       required>
                            </div>
                            <div class="col-sm-6">
                                <input type="text" name="last_name" class="form-control" placeholder="Etternavn" value="{{ old('last_name') }}"
                                       required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-7 col-sm-6 email-container">
                                <input type="email" name="email" class="form-control" placeholder="Epost" value="{{ old('email') }}"
                                       required>
                            </div>
                            <div class="col-md-5 col-sm-6">
                                <button type="submit" class="btn btn-submit">Meld meg på</button>
                            </div>
                        </div>
                    </form>

                    <div class="col-sm-12 margin-top no-left-right-padding">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul style="list-style: none">
                                    @foreach($errors->all() as $error)
                                        <li>{{$error}}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $(function(){
            countDownTimer();
            // Update the count down every 1 second
            var x = setInterval(function() {
                countDownTimer();
            }, 1000);

            $(".leadstyle-link").click(function(e){
                e.preventDefault();

                var href = $(this).attr('href');
                $('html, body').animate({
                    scrollTop: $(href).offset().top
                }, 1000);
            });
        });

        function countDownTimer() {
            var countDownDate = new Date("{{ $freeWebinar->start_date }}").getTime();
            //2018-08-17 21:00:00

            // Get todays date and time
            var now = new Date().getTime();

            // Find the distance between now an the count down date
            var distance = countDownDate - now;

            // Time calculations for days, hours, minutes and seconds
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            var daysCont = $("#days"),
                hoursCont = $("#hours"),
                minutesCont = $("#minutes"),
                secondsCont = $("#seconds");

            days = days > 9 ? days : '0'+days;
            hours = hours > 9 ? hours : '0'+hours;
            minutes = minutes > 9 ? minutes : '0'+minutes;
            seconds = seconds > 9 ? seconds : '0'+seconds;

            daysCont.text(days);
            hoursCont.text(hours);
            minutesCont.text(minutes);
            secondsCont.text(seconds);

            // if the date is in the past then put 0
            if (distance < 0) {
                daysCont.text('00');
                hoursCont.text('00');
                minutesCont.text('00');
                secondsCont.text('00');
            }
        }
    </script>
@stop
