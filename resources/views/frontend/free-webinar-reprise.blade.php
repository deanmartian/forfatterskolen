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
            <p style="font-size: 15px; color: #444; line-height: 1.7;">
                Gro Dahle er en av Norges mest elskede forfattere. I dette webinaret deler hun sine beste tips for hvordan du skaper karakterer som lever og puster — karakterer leseren ikke glemmer.
            </p>
            <p style="font-size: 15px; color: #444; line-height: 1.7; margin-top: 16px;"><strong>Du vil lære:</strong></p>
            <ul style="font-size: 15px; color: #444; line-height: 1.9; padding-left: 20px;">
                <li>Hvordan bygge en karakter som bærer en hel historie</li>
                <li>Intuitive og analytiske teknikker du kan bruke med en gang</li>
                <li>Hvordan gi karakteren stemme, vilje og liv</li>
            </ul>

            <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #eee;">
                <h3>Nå har vi et konkret spørsmål til deg:</h3>
                <p style="font-size: 15px; color: #444; line-height: 1.7;">
                    Har du en romanidé du ikke helt får tak på — eller et manus du vil løfte til et høyere nivå?
                </p>
                <p style="font-size: 15px; color: #444; line-height: 1.7; margin-top: 12px;">
                    20. april sparker vi i gang et nytt <strong>10 ukers intensivt romankurs</strong>, der du får lære og komme tett på noen av landets mest erfarne forfattere:
                    <strong>Trude Marstein, Gro Dahle, Bjarte Breiteig og Rolf Enger.</strong>
                </p>
                <p style="font-size: 15px; color: #444; line-height: 1.7; margin-top: 12px;">
                    Dette er ikke et kurs du bare «tar».<br>
                    Det er et kurs du <strong>skriver deg gjennom.</strong>
                </p>
                <p style="font-size: 15px; color: #444; line-height: 1.7; margin-top: 16px;"><strong>Du får:</strong></p>
                <ul style="font-size: 15px; color: #444; line-height: 1.9; padding-left: 20px;">
                    <li>En tydelig struktur om hvordan du går fra idé til ferdig førsteutkast</li>
                    <li>Ukentlige webinarer med undervisning og mulighet for spørsmål (til forfatterne — og oss)</li>
                    <li>Profesjonell tilbakemelding på teksten din</li>
                    <li>Et skrivemiljø som hjelper deg videre (og varer lenge etter at kurset er ferdig)</li>
                </ul>
                <p style="font-size: 15px; color: #444; line-height: 1.7; margin-top: 16px;">
                    I løpet av 10 uker bygger du, eller bearbeider, romanen din — steg for steg, med veiledning fra folk som virkelig kan faget.
                </p>
            </div>
        </div>

        {{-- CTA: Romankurs --}}
        <div class="reprise-cta" style="text-align: left;">
            <h3 style="text-align: center;">Har du en romanidé du ikke helt får tak på?</h3>
            <p style="text-align: center; margin-bottom: 24px;">20. april sparker vi i gang et nytt <strong>10 ukers intensivt romankurs</strong>, der du får lære og komme tett på noen av landets mest erfarne forfattere:</p>
            <p style="text-align: center; font-size: 17px; font-weight: 600; color: #1a1a1a; margin-bottom: 24px;">
                Trude Marstein, Gro Dahle, Bjarte Breiteig og Rolf Enger.
            </p>

            <p style="font-size: 15px; color: #444; line-height: 1.7; margin-bottom: 8px;">Dette er ikke et kurs du bare «tar».<br>Det er et kurs du <strong>skriver deg gjennom</strong>.</p>

            <p style="font-size: 15px; color: #444; line-height: 1.7; margin-bottom: 4px;"><strong>Du får:</strong></p>
            <ul style="font-size: 15px; color: #444; line-height: 1.9; margin-bottom: 24px; padding-left: 20px;">
                <li>En tydelig struktur om hvordan du går fra idé til ferdig førsteutkast.</li>
                <li>Ukentlige webinarer med undervisning og mulighet for spørsmål</li>
                <li>Profesjonell tilbakemelding på teksten din.</li>
                <li>Et skrivemiljø som hjelper deg videre (og varer lenge etter at kurset er ferdig)</li>
            </ul>

            <div style="text-align: center;">
                <a href="/course/121" class="reprise-cta-btn" style="font-size: 17px; padding: 16px 40px;">
                    Les mer og bestill →
                </a>
                <p style="font-size: 13px; color: #888; margin-top: 12px;">Earlybird-pris til 1. april — spar kr 5 500</p>
            </div>
        </div>
    </div>
</div>
@stop
