@extends('frontend.layout')

@section('title')
    <title>Cancelled Order &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="thank-you-page" data-bg="https://www.forfatterskolen.no/images-new/thankyou-bg.png">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 left-container">
                    <h1>
                        {!! trans('site.cancelled-order.title') !!}
                    </h1>
                    <p>
                        {!! trans('site.cancelled-order.description') !!}
                    </p>

                    <a href="{{ route('front.shop-manuscript.checkout', $manuscript_id) }}" class="btn buy-btn">
                        {{ trans('site.front.buy') }}
                    </a>
                </div>

                <div class="col-sm-6 right-container">
                    <img src="{{ asset('images-new/thankyou-hero.jpg') }}" alt="">
                </div>
            </div>
        </div>
    </div>
@stop