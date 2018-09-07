@extends('frontend.layout')

@section('title')
    <title>Forfatterskolen</title>
@stop

@section('content')
    <div class="row other-services-container">
        <p class="title">
            <span class="highlight no-padding-bottom">AN</span>DRE <br> TJENESTER
        </p>

        <div class="col-sm-12 no-left-padding service-items">
            <div class="col-md-3 col-sm-4 col-xs-4 no-left-padding column-container">
                <div class="box">
                    <img src="{{ asset('images/book.png') }}" class="pull-left first-image">
                    <div class="pull-left service-container text-uppercase">
                        <label class="service-name">Coaching Timer</label> <br>
                        <a href="{{ route('front.coaching-timer') }}" class="service-link">Les Mer > </a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-4 col-xs-4 no-left-padding column-container">
                <div class="box">
                    <img src="{{ asset('images/book-magnify.png') }}" class="pull-left second-image">
                    <div class="pull-left service-container text-uppercase">
                        <label class="service-name">Korrektur</label> <br>
                        <a href="{{ route('front.correction') }}" class="service-link">Les Mer > </a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-4 col-xs-4 no-left-padding column-container">
                <div class="box">
                    <img src="{{ asset('images/check-list.png') }}" class="pull-left third-image">
                    <div class="pull-left service-container text-uppercase">
                        <label class="service-name">SPRÅKVASK</label> <br>
                        <a href="{{ route('front.copy-editing') }}" class="service-link">Les Mer > </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop