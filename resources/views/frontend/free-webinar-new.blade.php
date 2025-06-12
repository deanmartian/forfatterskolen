@extends('frontend.layout')

@section('title')
    <title>Free Webinar &rsaquo; {{ $freeWebinar->title }}</title>
@stop

@section('styles')
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Oswald" />
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
?>
<header>
    <div class="shell">
        <div id="headline">
            {{--@if($freeWebinar->webinar_presenters->count())
                <h1 class="role-element leadstyle-text">{{ $presenterList }}</h1>
            @else

            @endif--}}
                <div style="height: 30px"></div>
            <div class="line"></div>
            <p class="role-element leadstyle-text">
                {{ $freeWebinar->title }}
            </p>

            {{--<a href="#subscribe-container" id="button2-top" class="role-element leadstyle-link">Claim My Spot Now! »</a>--}}
        </div>

        <div class="clear"></div>
    </div>
</header>

<div class="clear"></div>

<section id="intro">
    <div class="shell">
        <div id="box-left">
            <div class="cont">
                <div id="info">
                    <div id="calendar">
                        <p id="month">{{ ucwords(\App\Http\FrontendHelpers::convertMonthLanguage($date->format('n'))) }}</p>
                        <p id="date">{{ $date->format('d') }}</p>
                    </div>

                    <div id="dates">
                        <p id="day">{{ ucwords(\App\Http\FrontendHelpers::convertDayLanguage($date->format('N'))) }}</p>
                        <p id="month-date">
                            <span class="leadstyle-fontsized" style="font-size:36px;">
                                {{ $date->format('d')
                                .' '.ucwords(\App\Http\FrontendHelpers::convertMonthLanguage($date->format('n'))) }}
                            </span>
                        </p>
                        <p id="time">
                            klokken {{ \Carbon\Carbon::parse($freeWebinar->start_date)->format('H:i') }}
                        </p>
                    </div>
                </div> <!-- end of #info -->

                <div class="clear"></div>

                @if($freeWebinar->webinar_presenters->count())
                    <div id="host-id">
                        @foreach($freeWebinar->webinar_presenters as $presenter)
                            <div class="host">
                                <img src="{{ $presenter->image ? $presenter->image : asset('images/user.png') }}">
                                {{--<p class="host-intro role-element leadstyle-text">Presenter</p>--}}
                                <p class="host-name role-element leadstyle-text" style="margin-top: 10px">
                                    {{ $presenter->first_name }} {{ $presenter->last_name }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div> <!-- end of .cont -->
        </div> <!-- end of #box-left -->
        <div id="box-right">
            <div class="countdown-box role-element leadstyle-container">
                <div id="countdown-container" class="role-element leadstyle-container">
                    <p class="launch-text role-element leadstyle-text">
                        Webinaret starter om
                    </p>
                    <div id="timer">
                        <ul id="countdown" class="role-element leadstyle-countdown">
                            <li>
                                <span class="countdown" id="days">0</span>
                                <div class="count-holder">
                                    <p class="timeRefDays role-element leadstyle-text">dager</p>
                                </div>
                            </li>
                            <li>
                                <span class="countdown" id="hours">0</span>
                                <div class="count-holder">
                                    <p class="timeRefHours role-element leadstyle-text">timer</p>
                                </div>
                            </li>
                            <li>
                                <span class="countdown" id="minutes">0</span>

                                <div class="count-holder">
                                    <p class="timeRefMinutes role-element leadstyle-text">minutter</p>
                                </div>
                            </li>
                            <li>
                                <span class="countdown" id="seconds">0</span>

                                <div class="count-holder">
                                    <p class="timeRefSeconds role-element leadstyle-text">Sekunder</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="clear"></div>
                </div>
            </div> <!-- end of .countdown-box role-element leadstyle-container -->

            <div class="cont">
                <p class="list-title role-element leadstyle-text">
                    {{--We're Going To Show You...--}}
                </p>

                <p class="description">
                    {{ $freeWebinar->description }}
                </p>
            </div>
        </div> <!-- end of #box-right -->

        <div class="clear"></div>
    </div> <!-- end of .shell -->
</section> <!-- end of #intro -->

<div class="clear"></div>

<section id="warning" class="role-element leadstyle-container">
    <div class="shell">
        <div id="message">
        </div>
        <p id="info1" class="role-element leadstyle-text">
            <b>Reserver min plass her</b>
        </p>
    </div> <!-- end of .shell -->
</section> <!-- end of #warning -->

<section id="bottom-arrow-box" class="role-element leadstyle-container">
    <div class="shell">
        <div class="btm-box-img"></div>
    </div> <!-- end of .shell -->
</section> <!-- end of .#bottom-arrow-box -->

<div class="container" id="subscribe-container">
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1 text-center">
            <div class="subscribe-success">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form action="{{ route('front.free-webinar.submit', $freeWebinar->id) }}" method="POST"
                              class="form-inline">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <input type="text" name="name" class="form-control" placeholder="Navn" value="{{ old('name') }}"
                                       required>
                            </div>

                            <div class="form-group">
                                <input type="email" name="email" class="form-control" placeholder="Epost" value="{{ old('email') }}"
                                       required>
                            </div>

                            <button type="submit" class="btn btn-primary">Meld meg på</button>
                        </form>

                        <div class="col-sm-12 margin-top">
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

            daysCont.text(days);
            hoursCont.text(hours);
            minutesCont.text(minutes);
            secondsCont.text(seconds);

            // if the date is in the past then put 0
            if (distance < 0) {
                daysCont.text(0);
                hoursCont.text(0);
                minutesCont.text(0);
                secondsCont.text(0);
            }
        }
    </script>
@stop