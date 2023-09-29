@extends('frontend.layout')

@section('title')
    <title>Thank You &rsaquo; Forfatterskolen</title>
@stop

@section('content')
{{-- data-bg="https://www.forfatterskolen.no/images-new/thankyou-bg.png" --}}
    <div class="thank-you-page">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 left-container">
                    {{-- <img data-src="https://www.forfatterskolen.no/images-new/thumb-icon.png" alt="" class="thumb"> --}}
                    <h1>Thank You</h1>
                    <p>
                        Thank you for buying our service
                    </p>
                </div>

                <div class="col-sm-6 right-container">
                    <img data-src="https://www.forfatterskolen.no/images-new/thankyou-hero.png" alt="">
                </div>
            </div>
        </div>
    </div>
@stop