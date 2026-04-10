@extends('frontend.layout')

@section('page_title', 'Takk for bestillingen &rsaquo; Forfatterskolen')

@section('styles')
@include('frontend.partials.thank-you-styles')
@stop

@section('content')
<div class="ty-wrapper">
    <div class="ty-card">
        <div class="ty-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>

        <h1>Takk for bestillingen!</h1>
        <p>Vi har mottatt din bestilling av publiseringstjeneste. Vi vil ta kontakt med deg så snart som mulig.</p>

        <a href="/" class="ty-btn">
            <i class="fa fa-home"></i> Til forsiden
        </a>
    </div>
</div>
@stop
