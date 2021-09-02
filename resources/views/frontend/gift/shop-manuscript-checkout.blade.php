@extends('frontend.layout')

@section('title')
    <title>Checkout &rsaquo; Forfatterskolen</title>
@stop

@section('content')

    <div class="checkout-page" data-bg="https://www.forfatterskolen.no/images-new/checkout-bg.png" id="app-container">
        <div class="container">
            <gift-shop-manuscript-checkout :user="{{ json_encode($user) }}" :shop-manuscript="{{ json_encode($shopManuscript) }}"
                                           :gift-card="{{ json_encode($giftCard) }}" :gift-cards="{{ json_encode($giftCards) }}">

            </gift-shop-manuscript-checkout>
        </div>
    </div>

@stop

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/app.js?v='.time()) }}"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
@stop