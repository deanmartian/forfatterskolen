@extends('frontend.layout')

@section('title')
    <title>Thank You &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="thank-you-page">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 left-container">
                    <img src="{{ asset('images-new/thumb-icon.png') }}" alt="" class="thumb">
                    <h1>Takk for at du meldte deg på!</h1>
                    <p>
                        Du vil nå få tilsendt en epost!
                    </p>
                </div>

                <div class="col-sm-6 right-container">
                    <img src="{{ asset('images-new/thankyou-hero.jpg') }}" alt="">
                </div>
            </div>
        </div>
    </div>
@stop