@extends('frontend.layout')

@section('title')
<title>Takk for bestillingen &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
<style>
    .ty-wrapper {
        min-height: 70vh;
        display: flex;
        align-items: center;
        padding: 60px 20px;
        background: linear-gradient(135deg, #faf8f5 0%, #fdf5f6 100%);
    }
    .ty-card {
        max-width: 720px;
        margin: 0 auto;
        background: #fff;
        border-radius: 16px;
        padding: 60px 50px;
        box-shadow: 0 10px 40px rgba(134, 39, 54, 0.08);
        text-align: center;
    }
    .ty-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #862736 0%, #5e1a26 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 28px;
        box-shadow: 0 8px 24px rgba(134, 39, 54, 0.2);
    }
    .ty-icon svg { width: 40px; height: 40px; color: #fff; }
    .ty-card h1 {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 2.2rem;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0 0 16px;
        line-height: 1.2;
    }
    .ty-card p {
        font-size: 1.05rem;
        color: #5a5550;
        line-height: 1.7;
        margin-bottom: 32px;
    }
    .ty-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #862736;
        color: #fff !important;
        padding: 14px 32px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 1rem;
        font-weight: 600;
        transition: all 0.15s;
        box-shadow: 0 4px 12px rgba(134, 39, 54, 0.2);
    }
    .ty-btn:hover {
        background: #9c2e40;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(134, 39, 54, 0.3);
        color: #fff !important;
        text-decoration: none;
    }
    .ty-btn-secondary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #862736 !important;
        padding: 14px 28px;
        text-decoration: none;
        font-size: 0.95rem;
        font-weight: 600;
        margin-left: 8px;
    }
    .ty-btn-secondary:hover { text-decoration: underline; color: #862736 !important; }
    .ty-steps {
        margin-top: 40px;
        padding-top: 32px;
        border-top: 1px solid #e8e4de;
        text-align: left;
    }
    .ty-steps-title {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #8a8580;
        margin-bottom: 16px;
        text-align: center;
    }
    .ty-step {
        display: flex;
        gap: 16px;
        align-items: flex-start;
        margin-bottom: 14px;
    }
    .ty-step-num {
        flex-shrink: 0;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #f4e8ea;
        color: #862736;
        font-size: 0.85rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .ty-step-text {
        font-size: 0.95rem;
        color: #5a5550;
        line-height: 1.5;
    }
    .ty-step-text strong { color: #1a1a1a; }

    @media (max-width: 600px) {
        .ty-card { padding: 40px 28px; }
        .ty-card h1 { font-size: 1.6rem; }
    }
</style>
@stop

@section('content')
@php
    $page = Request::input('page');
    switch ($page) {
        case 'paypal':
            $header = 'Takk for betalingen!';
            $message = 'Betalingen din er bekreftet. Du har nå full tilgang til kurset ditt og kan begynne med en gang.';
            $btnLink = route('learner.invoice');
            $btnText = 'Se mine fakturaer';
            $btnIcon = 'list-alt';
            break;
        case 'vipps':
            $header = 'Betalt med Vipps — velkommen!';
            $message = 'Din Vipps-betaling er bekreftet. Du har nå full tilgang til kurset og kan starte med en gang. Kvittering er sendt til e-posten din.';
            $btnLink = route('learner.course');
            $btnText = 'Gå til kursene mine';
            $btnIcon = 'graduation-cap';
            break;
        case 'manuscript':
            $header = 'Takk for bestillingen!';
            $message = 'Manusbestillingen din er mottatt. Vi vil behandle den så raskt som mulig.';
            $btnLink = route('learner.shop-manuscript');
            $btnText = 'Se mine manuskripter';
            $btnIcon = 'file-o';
            break;
        case 'workshop':
            $header = 'Takk for bestillingen!';
            $message = 'Du er nå påmeldt skriveverkstedet. Vi gleder oss til å se deg!';
            $btnLink = route('learner.workshop');
            $btnText = 'Se skriveverksted';
            $btnIcon = 'briefcase';
            break;
        default:
            $header = 'Takk for bestillingen!';
            $message = 'Velkommen til Forfatterskolen! Du har nå tilgang til kurset ditt og kan begynne med en gang.';
            $btnLink = route('learner.course');
            $btnText = 'Gå til kursene mine';
            $btnIcon = 'graduation-cap';
            break;
    }
@endphp

<div class="ty-wrapper">
    <div class="ty-card">
        <div class="ty-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>

        <h1>{{ $header }}</h1>
        <p>{{ $message }}</p>

        <div>
            <a href="{{ $btnLink }}" class="ty-btn">
                <i class="fa fa-{{ $btnIcon }}"></i> {{ $btnText }}
            </a>
            <a href="/" class="ty-btn-secondary">Til forsiden</a>
        </div>

        <div class="ty-steps">
            <div class="ty-steps-title">Slik kommer du i gang</div>
            <div class="ty-step">
                <div class="ty-step-num">1</div>
                <div class="ty-step-text">
                    <strong>Sjekk e-posten din</strong> — du får snart en bekreftelse med nyttig informasjon.
                </div>
            </div>
            <div class="ty-step">
                <div class="ty-step-num">2</div>
                <div class="ty-step-text">
                    <strong>Logg inn i portalen</strong> — der finner du kursmateriellet, webinarer og manus.
                </div>
            </div>
            <div class="ty-step">
                <div class="ty-step-num">3</div>
                <div class="ty-step-text">
                    <strong>Start å skrive</strong> — vi heier på deg hele veien!
                </div>
            </div>
        </div>
    </div>
</div>
@stop
