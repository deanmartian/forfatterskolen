@extends('frontend.layout')

@section('page_title', 'Takk for påmeldingen &rsaquo; Forfatterskolen')
@section('robots', '<meta name="robots" content="noindex, follow">')
@section('meta_desc', 'Takk for påmeldingen til webinaret. Sjekk e-posten din for bekreftelse.')

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

        <h1>Takk for påmeldingen!</h1>
        <p>
            Du er nå påmeldt webinaret. Du vil motta en e-post fra oss med lenken du skal bruke til webinaret i løpet av kort tid.
        </p>

        <a href="/" class="ty-btn">
            <i class="fa fa-home"></i> Til forsiden
        </a>

        <p style="font-size:0.85rem;color:#8a8580;margin-top:32px;">
            Hilsen Sven Inge<br>Forfatterskolen
        </p>
    </div>
</div>
@stop
