@extends('frontend.layouts.course-portal')

@section('title')
<title>Påbyggingstreff &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
<style>
    .pabygg-wrapper {
        --wine: #862736;
        --wine-hover: #9c2e40;
        --wine-dark: #5c1a25;
        --wine-light: rgba(134, 39, 54, 0.08);
        --wine-light-solid: #f4e8ea;
        --cream: #faf8f5;
        padding: 30px 0 60px;
    }

    .pabygg-hero {
        background: linear-gradient(135deg, var(--wine) 0%, var(--wine-dark) 100%);
        color: #fff;
        border-radius: 12px;
        padding: 40px;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }

    .pabygg-hero::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: rgba(255,255,255,0.05);
    }

    .pabygg-hero h1 {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 8px;
    }

    .pabygg-hero .subtitle {
        font-size: 16px;
        opacity: 0.9;
    }

    .pabygg-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        padding: 30px;
        margin-bottom: 24px;
    }

    .pabygg-card h2 {
        color: var(--wine);
        font-size: 20px;
        font-weight: 600;
        margin: 0 0 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .pabygg-card h2 i {
        font-size: 22px;
    }

    .pabygg-card ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .pabygg-card ul li {
        padding: 8px 0;
        border-bottom: 1px solid #f0eded;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 15px;
        line-height: 1.5;
    }

    .pabygg-card ul li:last-child {
        border-bottom: none;
    }

    .pabygg-card ul li i {
        color: var(--wine);
        margin-top: 3px;
        flex-shrink: 0;
    }

    .pabygg-highlight {
        background: var(--wine-light);
        border-left: 4px solid var(--wine);
        padding: 16px 20px;
        border-radius: 0 8px 8px 0;
        margin-top: 16px;
        font-size: 14px;
    }

    .pabygg-highlight i {
        color: var(--wine);
        margin-right: 6px;
    }

    /* Radio buttons */
    .pabygg-radio-group {
        display: flex;
        gap: 16px;
        margin: 20px 0;
    }

    .pabygg-radio-option {
        flex: 1;
        position: relative;
    }

    .pabygg-radio-option input[type="radio"] {
        position: absolute;
        opacity: 0;
    }

    .pabygg-radio-option label {
        display: block;
        border: 2px solid #e0d8da;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: #fff;
    }

    .pabygg-radio-option label:hover {
        border-color: var(--wine);
        background: var(--wine-light);
    }

    .pabygg-radio-option input[type="radio"]:checked + label {
        border-color: var(--wine);
        background: var(--wine-light);
        box-shadow: 0 0 0 1px var(--wine);
    }

    .pabygg-radio-option label .day-name {
        display: block;
        font-size: 18px;
        font-weight: 600;
        color: var(--wine);
        margin-bottom: 4px;
    }

    .pabygg-radio-option label .day-date {
        display: block;
        font-size: 14px;
        color: #666;
    }

    .pabygg-radio-option label .day-spots {
        display: block;
        font-size: 12px;
        margin-top: 6px;
        color: #2e7d32;
        font-weight: 500;
    }

    .pabygg-radio-option label .day-spots.full {
        color: #c62828;
    }

    .pabygg-radio-option input[type="radio"]:disabled + label {
        opacity: 0.5;
        cursor: not-allowed;
        border-color: #ddd;
    }

    .pabygg-radio-option input[type="radio"]:disabled + label:hover {
        border-color: #ddd;
        background: #fff;
    }

    .btn-wine {
        background: var(--wine);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 12px 32px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-wine:hover {
        background: var(--wine-hover);
        color: #fff;
    }

    .pabygg-success {
        background: #e8f5e9;
        border: 1px solid #c8e6c9;
        border-radius: 10px;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .pabygg-success i {
        color: #2e7d32;
        font-size: 24px;
    }

    .pabygg-success .success-text strong {
        display: block;
        color: #1b5e20;
        font-size: 16px;
    }

    .pabygg-success .success-text span {
        color: #4a7c4e;
        font-size: 14px;
    }

    .flash-success {
        background: #e8f5e9;
        border: 1px solid #c8e6c9;
        border-radius: 8px;
        padding: 14px 20px;
        margin-bottom: 20px;
        color: #2e7d32;
        font-weight: 500;
    }

    @media (max-width: 576px) {
        .pabygg-hero { padding: 24px; }
        .pabygg-hero h1 { font-size: 22px; }
        .pabygg-radio-group { flex-direction: column; }
        .pabygg-card { padding: 20px; }
    }
</style>
@stop

@section('content')
<div class="pabygg-wrapper">
    <div class="container">

        {{-- Hero --}}
        <div class="pabygg-hero">
            <h1>Påbyggingstreff — 8. og 9. mai 2026</h1>
            <div class="subtitle">Samling for påbyggingskurset &middot; Kurs 120</div>
        </div>

        {{-- Flash --}}
        @if(session('success'))
            <div class="flash-success">
                <i class="fa fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-7">

                {{-- Praktisk info --}}
                <div class="pabygg-card">
                    <h2><i class="fa fa-info-circle"></i> Praktisk informasjon</h2>
                    <ul>
                        <li>
                            <i class="fa fa-users"></i>
                            <span>Deltakerne deles i to grupper, ca. 9 per dag</span>
                        </li>
                        <li>
                            <i class="fa fa-clock-o"></i>
                            <span>Ca. 30 minutter per tekst</span>
                        </li>
                        <li>
                            <i class="fa fa-cutlery"></i>
                            <span>Lunsj ca. kl 13 (alle ordner selv)</span>
                        </li>
                        <li>
                            <i class="fa fa-heart"></i>
                            <span>Trygge og konstruktive tilbakemeldinger</span>
                        </li>
                        <li>
                            <i class="fa fa-play-circle"></i>
                            <span>Kort intro ved oppstart</span>
                        </li>
                        <li>
                            <i class="fa fa-coffee"></i>
                            <span>Pauser underveis, kaffe/te/frukt</span>
                        </li>
                    </ul>

                    <div class="pabygg-highlight">
                        <i class="fa fa-glass"></i>
                        <strong>NB: Antologi-lansering</strong>
                        <span>Bar Bardot begge kveldene — dørene åpner kl 18:00!</span>
                    </div>
                </div>

                {{-- Tekst-påminnelse --}}
                <div class="pabygg-card">
                    <h2><i class="fa fa-upload"></i> Husk teksten din!</h2>
                    <p style="margin: 0; font-size: 15px; color: #555;">
                        Husk å laste opp teksten din under oppgaven
                        <strong>&laquo;10 sider til samling&raquo;</strong> i kurset ditt,
                        slik at gruppen kan forberede seg.
                    </p>
                </div>

            </div>

            <div class="col-md-5">

                {{-- Påmelding --}}
                <div class="pabygg-card">
                    <h2><i class="fa fa-calendar-check-o"></i> Påmelding</h2>

                    @if($courseTaken->pabygg_treff_day)
                        <div class="pabygg-success">
                            <i class="fa fa-check-circle"></i>
                            <div class="success-text">
                                <strong>Du er påmeldt!</strong>
                                <span>
                                    @if($courseTaken->pabygg_treff_day === 'friday')
                                        Fredag 8. mai
                                    @elseif($courseTaken->pabygg_treff_day === 'saturday')
                                        Lørdag 9. mai
                                    @elseif($courseTaken->pabygg_treff_day === 'digital')
                                        Digitalt møte
                                    @endif
                                </span>
                            </div>
                        </div>
                        <p style="font-size: 14px; color: #888; margin-bottom: 16px;">
                            Du kan endre dag ved å velge på nytt nedenfor.
                        </p>
                    @endif

                    <form method="POST" action="{{ route('learner.pabygg-treff.store') }}">
                        @csrf

                        <div class="pabygg-radio-group">
                            <div class="pabygg-radio-option">
                                <input type="radio" name="pabygg_treff_day" value="friday"
                                       id="day-friday"
                                       {{ old('pabygg_treff_day', $courseTaken->pabygg_treff_day) === 'friday' ? 'checked' : '' }}
                                       {{ $fridayCount >= $maxPerDay && $courseTaken->pabygg_treff_day !== 'friday' ? 'disabled' : '' }}>
                                <label for="day-friday">
                                    <span class="day-name">Fredag</span>
                                    <span class="day-date">8. mai 2026</span>
                                    <span class="day-spots {{ $fridayCount >= $maxPerDay ? 'full' : '' }}">
                                        {{ $fridayCount >= $maxPerDay ? 'Fullt' : ($maxPerDay - $fridayCount) . ' plasser igjen' }}
                                    </span>
                                </label>
                            </div>
                            <div class="pabygg-radio-option">
                                <input type="radio" name="pabygg_treff_day" value="saturday"
                                       id="day-saturday"
                                       {{ old('pabygg_treff_day', $courseTaken->pabygg_treff_day) === 'saturday' ? 'checked' : '' }}
                                       {{ $saturdayCount >= $maxPerDay && $courseTaken->pabygg_treff_day !== 'saturday' ? 'disabled' : '' }}>
                                <label for="day-saturday">
                                    <span class="day-name">Lørdag</span>
                                    <span class="day-date">9. mai 2026</span>
                                    <span class="day-spots {{ $saturdayCount >= $maxPerDay ? 'full' : '' }}">
                                        {{ $saturdayCount >= $maxPerDay ? 'Fullt' : ($maxPerDay - $saturdayCount) . ' plasser igjen' }}
                                    </span>
                                </label>
                            </div>
                            <div class="pabygg-radio-option">
                                <input type="radio" name="pabygg_treff_day" value="digital"
                                       id="day-digital"
                                       {{ old('pabygg_treff_day', $courseTaken->pabygg_treff_day) === 'digital' ? 'checked' : '' }}>
                                <label for="day-digital">
                                    <span class="day-name"><i class="fa fa-video-camera"></i> Digitalt</span>
                                    <span class="day-date">Kan ikke komme fysisk</span>
                                    <span class="day-spots">Digitalt møte avtales</span>
                                </label>
                            </div>
                        </div>

                        @error('pabygg_treff_day')
                            <p style="color: #c62828; font-size: 14px; margin-bottom: 10px;">{{ $message }}</p>
                        @enderror

                        <button type="submit" class="btn-wine">
                            {{ $courseTaken->pabygg_treff_day ? 'Endre dag' : 'Meld meg på' }}
                        </button>
                    </form>
                </div>

            </div>
        </div>

    </div>
</div>
@stop
