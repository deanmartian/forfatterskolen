@extends('frontend.layout-self-publishing')

@section('title')
    <title>Checkout &rsaquo; Forfatterskolen</title>
@stop

@section('content')

    <div class="container" style="margin-top: 50px">
        <publishing-service-checkout :active-service="{{ json_encode($service) }}" 
        :user="{{ json_encode(Auth::user()) }}"></publishing-service-checkout>
    </div>

@stop

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/app.js?v='.time()) }}"></script>
@stop