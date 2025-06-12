@extends('frontend.layout')

@section('title')
    <title>Free Webinar &rsaquo; {{ $freeWebinar->title }}</title>
@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/free-webinar.css?v='.time()) }}">
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

    $webinarTitle = $freeWebinar->title;
    ?>

    <div class="free-webinar-page free-webinar-page-new">
        <div class="header" data-bg="https://www.forfatterskolen.no/images-new/free-webinar-header-new.png">
        </div>

        <div class="container text-center">
            <h1>{{ $webinarTitle }}</h1>
        </div>

        <div class="container small-container">
            <div class="row">
                <div class="countdown-container w-100 text-center">
                    <h3 class="text-uppercase font-weight-normal">
                        {{ trans('site.front.free-webinar.webinar-start-on') }}
                    </h3>
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
                </div> <!-- end countdown-container-->

                <div class="schedule-container w-100 text-center">
                    <h1>
                        {{ ucfirst(\App\Http\FrontendHelpers::convertDayLanguage($date->format('N'))) }}
                    </h1>

                    <div class="date-container">
                        <h2>
                            <i class="calendar"></i>
                            {{ $date->format('d') }}
                            {{ \App\Http\FrontendHelpers::convertMonthLanguage($date->format('n')) }}
                        </h2>

                        <h2>
                            <i class="clock"></i>
                            {{ str_replace('_time_', \Carbon\Carbon::parse($freeWebinar->start_date)->format('H:i'),
                            trans('site.front.free-webinar.start-time')) }}
                        </h2>
                    </div> <!-- end date-container -->
                </div> <!-- end schedule-container -->

                <div class="presenters w-100 text-center">
                    @if($freeWebinar->webinar_presenters->count())
                        @foreach($freeWebinar->webinar_presenters as $presenter)
                            <div class="presenter-container">
                                <div class="presenter-circle">
                                    <img data-src="https://www.forfatterskolen.no/{{ $presenter->image ? $presenter->image : 'images/user.png' }}"
                                    class="rounded-circle">
                                </div>
                                <p class="presenter-name">
                                    {{ $presenter->first_name }} {{ $presenter->last_name }}
                                </p>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="details">
                    <p class="text-center">
                        {!! nl2br($freeWebinar->description) !!}
                    </p>
                </div>

                <div class="form-container w-100">
                    <form action="{{ route('front.free-webinar.submit', $freeWebinar->id) }}" method="POST" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa at-icon"></i></span>
                            </div>
                            <input type="email" name="email" class="form-control no-border-left"
                                   placeholder="{{ trans('site.front.form.email') }}"
                                   required value="{{ old('email') }}">
                        </div>

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa user-icon"></i></span>
                            </div>
                            <input type="text" placeholder="{{ trans('site.front.form.first-name') }}" name="first_name"
                                   class="form-control no-border-left" required value="{{old('first_name')}}">
                        </div>

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa user-icon"></i></span>
                            </div>
                            <input type="text" placeholder="{{ trans('site.front.form.last-name') }}" name="last_name"
                                   class="form-control no-border-left" required value="{{old('last_name')}}">
                        </div>

                        <button type="submit" class="btn site-btn-global w-100" onclick="gtag_report_conversion()">
                            {{ trans('site.front.free-webinar.sign-me-in') }}
                        </button>
                    </form>

                    <div class="col-sm-12 mt-5">
                        <div class="row">
                            @if($errors->any())
                                <div class="alert alert-danger w-100">
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
            </div> <!-- end row -->
        </div> <!-- end small-container-->
    </div>

@stop

@section('scripts')
    <script>
        $(function(){
            countDownTimer();
            // Update the count down every 1 second
            let x = setInterval(function() {
                countDownTimer();
            }, 1000);

            $(".leadstyle-link").click(function(e){
                e.preventDefault();

                let href = $(this).attr('href');
                $('html, body').animate({
                    scrollTop: $(href).offset().top
                }, 1000);
            });
        });

        function countDownTimer() {
            let countDownDate = new Date("{{ $freeWebinar->start_date }}").getTime();
            //2018-08-17 21:00:00

            // Get todays date and time
            let now = new Date().getTime();

            // Find the distance between now an the count down date
            let distance = countDownDate - now;

            // Time calculations for days, hours, minutes and seconds
            let days = Math.floor(distance / (1000 * 60 * 60 * 24));
            let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            let seconds = Math.floor((distance % (1000 * 60)) / 1000);

            let daysCont = $("#days"),
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
