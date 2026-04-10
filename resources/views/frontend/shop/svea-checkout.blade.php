@extends('frontend.layout')

@section('page_title', 'Betal med Svea &rsaquo; Forfatterskolen')

@section('content')

    <div class="checkout-page" data-bg="https://www.forfatterskolen.no/images-new/checkout-bg.png" id="app-container">
        <div class="container">
            <svea-checkout :course="{{ json_encode($course) }}" :package-id="{{ $package_id }}"
                             :passed-coupon="{{ json_encode($coupon) }}"
                             :packages="{{ json_encode($packages) }}"
                             :user="{{ json_encode($user) }}"></svea-checkout>
        </div>
    </div>

@stop

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/app.js?v='.filemtime(public_path('js/app.js'))) }}"></script>
@stop