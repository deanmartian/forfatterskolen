@extends('frontend.layout')

@section('title')
<title>Reprise: {{ $freeWebinar->title }} — Forfatterskolen</title>
@stop

@section('styles')
<style>
    .reprise-page { background: #f0eeeb; }
    .reprise-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 48px 24px;
    }
    .reprise-badge {
        display: inline-block;
        background: #862736;
        color: #fff;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 4px 12px;
        border-radius: 4px;
        margin-bottom: 16px;
    }
    .reprise-title {
        font-family: Georgia, serif;
        font-size: 28px;
        color: #1a1a1a;
        margin-bottom: 8px;
        font-weight: normal;
    }
    .reprise-meta {
        font-size: 14px;
        color: #888;
        margin-bottom: 32px;
    }
    .reprise-video {
        background: #1a1a1a;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 32px;
    }
    .reprise-video iframe,
    .reprise-video video {
        width: 100%;
        aspect-ratio: 16/9;
        display: block;
    }
    .reprise-video .wistia_responsive_padding {
        border-radius: 12px;
        overflow: hidden;
    }
    .reprise-description {
        background: #fff;
        border-radius: 12px;
        padding: 32px;
        margin-bottom: 32px;
    }
    .reprise-description h3 {
        font-family: Georgia, serif;
        font-size: 20px;
        color: #1a1a1a;
        margin-bottom: 16px;
    }
    .reprise-description p {
        font-size: 15px;
        color: #444;
        line-height: 1.7;
    }
    .reprise-cta {
        background: #fff;
        border-radius: 12px;
        padding: 32px;
        text-align: center;
    }
    .reprise-cta h3 {
        font-family: Georgia, serif;
        font-size: 22px;
        color: #1a1a1a;
        margin-bottom: 12px;
    }
    .reprise-cta p {
        font-size: 15px;
        color: #666;
        margin-bottom: 20px;
    }
    .reprise-cta-btn {
        display: inline-block;
        background: #862736;
        color: #fff;
        padding: 14px 32px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 15px;
        font-weight: 500;
        transition: background 0.2s;
    }
    .reprise-cta-btn:hover { background: #6b1e2b; color: #fff; text-decoration: none; }
    .reprise-presenter {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
    }
    .reprise-presenter img {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        object-fit: cover;
    }
    .reprise-presenter-name {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a1a;
    }
    .reprise-presenter-role {
        font-size: 13px;
        color: #888;
    }
    @media (max-width: 768px) {
        .reprise-container { padding: 24px 16px; }
        .reprise-title { font-size: 22px; }
    }
</style>
@stop

@section('content')
<div class="reprise-page">
    <div class="reprise-container">
        <span class="reprise-badge">Reprise</span>
        <h1 class="reprise-title">{{ $freeWebinar->title }}</h1>
        <div class="reprise-meta">
            Holdt {{ \Carbon\Carbon::parse($freeWebinar->start_date)->translatedFormat('j. F Y') }}
            · Forfatterskolen
        </div>

        {{-- Video --}}
        <div class="reprise-video">
            @if($replayEmbed)
                {!! $replayEmbed !!}
            @else
                <div style="padding: 80px 24px; text-align: center; color: #888;">
                    <p style="font-size: 18px;">Opptaket er ikke tilgjengelig ennå.</p>
                    <p style="font-size: 14px;">Sjekk igjen senere — opptaket publiseres kort tid etter webinaret.</p>
                </div>
            @endif
        </div>

        {{-- Presenter + beskrivelse --}}
        <div class="reprise-description">
            @php $presenter = $freeWebinar->webinar_presenters->first(); @endphp
            @if($presenter)
                <div class="reprise-presenter">
                    @if($presenter->image)
                        <img src="{{ asset('storage/' . $presenter->image) }}" alt="{{ $presenter->first_name }}">
                    @endif
                    <div>
                        <div class="reprise-presenter-name">{{ $presenter->first_name }} {{ $presenter->last_name }}</div>
                        <div class="reprise-presenter-role">Foredragsholder</div>
                    </div>
                </div>
            @endif

            <h3>Om webinaret</h3>
            <p>{!! nl2br(e($freeWebinar->description)) !!}</p>
        </div>

        {{-- CTA --}}
        <div class="reprise-cta">
            <h3>Inspirert av webinaret?</h3>
            <p>Ta neste steg i skrivereisen din — vi har kurs for alle nivåer.</p>
            <a href="/course" class="reprise-cta-btn">Se våre kurs →</a>
        </div>
    </div>
</div>
@stop
