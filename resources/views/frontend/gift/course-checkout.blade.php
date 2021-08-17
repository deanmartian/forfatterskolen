@extends('frontend.layout')

@section('title')
    <title>Checkout &rsaquo; Forfatterskolen</title>
@stop

@section('content')

    <div class="checkout-page" data-bg="https://www.forfatterskolen.no/images-new/checkout-bg.png" id="app-container">
        <div class="container">
            <gift-course-checkout :course="{{ json_encode($course) }}" :package-id="{{ $package_id }}"
                             :passed-coupon="{{ json_encode($coupon) }}"
                             :packages="{{ json_encode($packages) }}"
                             :user="{{ json_encode($user) }}" :start-index="{{ $startIndex }}"
                                  :gift-card="{{ json_encode($giftCard) }}" :gift-cards="{{ json_encode($giftCards) }}">
            </gift-course-checkout>
        </div>
    </div>

@stop

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/app.js?v='.time()) }}"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
@stop