@extends('frontend.layout')

@section('page_title', 'Takk for bestillingen &mdash; Personlig trener &rsaquo; Forfatterskolen')
@section('meta_desc', 'Takk for bestillingen av personlig skrivetrener.')

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

        <h1>Takk for søknaden!</h1>
        <p>Vi har mottatt søknaden din om personlig trener og vil kontakte deg så fort som mulig.</p>

        <a href="/" class="ty-btn">
            <i class="fa fa-home"></i> Til forsiden
        </a>
    </div>
</div>
@stop
