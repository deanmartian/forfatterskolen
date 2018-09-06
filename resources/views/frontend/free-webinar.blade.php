@extends('frontend.layout')

@section('title')
    <title>Free Webinar &rsaquo; {{ $freeWebinar->title }}</title>
@stop

@section('content')

    <div class="container">
        <div class="courses-hero text-center">
            <div class="row">
                <div class="col-sm-12">
                    <h2><span class="highlight">HVO</span>RDAN SKRIVE KRIM?</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="container free-webinar-container">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
                <div class="col-sm-5 left-container">
                    <div class="presents">
                        <div class="col-sm-5 no-left-padding circle">
                            September
                        </div>
                        <div class="col-sm-7"></div>
                    </div>
                </div>

                <div class="col-sm-7">

                </div>
            </div>
        </div>
    </div>
@stop
