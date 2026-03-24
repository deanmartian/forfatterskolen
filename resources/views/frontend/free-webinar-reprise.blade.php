@extends('frontend.layout')

@section('title')
<title>Reprise: {{ $freeWebinar->title }} — Forfatterskolen</title>
@stop

@section('metas')
    <meta property="og:title" content="Se reprisen: {{ $freeWebinar->title }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($freeWebinar->description), 160) }}">
    <meta name="description" content="Se reprisen av {{ $freeWebinar->title }} gratis. {{ Str::limit(strip_tags($freeWebinar->description), 120) }}">
    <meta property="og:type" content="video.other">
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

        {{-- Om webinaret --}}
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

            <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 20px;">
                <div style="display:flex;align-items:center;gap:12px;background:#f8f4f0;border-radius:8px;padding:14px 18px;border-left:3px solid #862736;">
                    <span style="font-size:20px;">🎭</span>
                    <span style="font-size:15px;color:#333;">Hvordan bygge en karakter som bærer en hel historie</span>
                </div>
                <div style="display:flex;align-items:center;gap:12px;background:#f8f4f0;border-radius:8px;padding:14px 18px;border-left:3px solid #D4A574;">
                    <span style="font-size:20px;">🧠</span>
                    <span style="font-size:15px;color:#333;">Intuitive og analytiske teknikker du kan bruke med en gang</span>
                </div>
                <div style="display:flex;align-items:center;gap:12px;background:#f8f4f0;border-radius:8px;padding:14px 18px;border-left:3px solid #5DCAA5;">
                    <span style="font-size:20px;">✍️</span>
                    <span style="font-size:15px;color:#333;">Hvordan gi karakteren stemme, vilje og liv</span>
                </div>
            </div>
        </div>

        {{-- Romankurs-pitch --}}
        <div style="background:linear-gradient(135deg,#fdf8f0,#fff);border-radius:12px;padding:32px;margin-bottom:32px;border:1px solid #e8ddd0;">
            <h3 style="font-family:Georgia,serif;font-size:22px;color:#862736;margin:0 0 8px;text-align:center;">Har du en romanidé du ikke helt får tak på?</h3>
            <p style="font-size:15px;color:#666;text-align:center;margin:0 0 24px;">Eller et manus du vil løfte til et høyere nivå?</p>

            <div style="background:#fff;border-radius:10px;padding:24px;border-left:4px solid #862736;margin-bottom:24px;">
                <p style="font-size:15px;color:#444;line-height:1.7;margin:0 0 12px;">
                    <strong style="color:#862736;">20. april</strong> sparker vi i gang et nytt <strong>10 ukers intensivt romankurs</strong>, der du får lære og komme tett på noen av landets mest erfarne forfattere:
                </p>
                <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:16px;">
                    <span style="background:#862736;color:#fff;padding:6px 14px;border-radius:20px;font-size:14px;font-weight:600;">Trude Marstein</span>
                    <span style="background:#862736;color:#fff;padding:6px 14px;border-radius:20px;font-size:14px;font-weight:600;">Gro Dahle</span>
                    <span style="background:#862736;color:#fff;padding:6px 14px;border-radius:20px;font-size:14px;font-weight:600;">Bjarte Breiteig</span>
                    <span style="background:#862736;color:#fff;padding:6px 14px;border-radius:20px;font-size:14px;font-weight:600;">Rolf Enger</span>
                </div>
            </div>

            <p style="font-size:16px;color:#444;line-height:1.7;font-style:italic;text-align:center;margin:0 0 24px;">
                Dette er ikke et kurs du bare «tar».<br>
                Det er et kurs du <strong style="font-style:normal;">skriver deg gjennom.</strong>
            </p>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:24px;">
                <div style="background:#fff;border-radius:10px;padding:16px;text-align:center;border:1px solid #eee;">
                    <div style="font-size:24px;margin-bottom:6px;">✍️</div>
                    <div style="font-size:13px;font-weight:600;color:#1a1a1a;">Fra idé til førsteutkast</div>
                    <div style="font-size:12px;color:#888;margin-top:4px;">Steg for steg med veiledning</div>
                </div>
                <div style="background:#fff;border-radius:10px;padding:16px;text-align:center;border:1px solid #eee;">
                    <div style="font-size:24px;margin-bottom:6px;">📹</div>
                    <div style="font-size:13px;font-weight:600;color:#1a1a1a;">Ukentlige webinarer</div>
                    <div style="font-size:12px;color:#888;margin-top:4px;">Med forfatterne — still spørsmål</div>
                </div>
                <div style="background:#fff;border-radius:10px;padding:16px;text-align:center;border:1px solid #eee;">
                    <div style="font-size:24px;margin-bottom:6px;">📝</div>
                    <div style="font-size:13px;font-weight:600;color:#1a1a1a;">Profesjonell tilbakemelding</div>
                    <div style="font-size:12px;color:#888;margin-top:4px;">På teksten din fra redaktør</div>
                </div>
                <div style="background:#fff;border-radius:10px;padding:16px;text-align:center;border:1px solid #eee;">
                    <div style="font-size:24px;margin-bottom:6px;">🤝</div>
                    <div style="font-size:13px;font-weight:600;color:#1a1a1a;">Skrivemiljø som varer</div>
                    <div style="font-size:12px;color:#888;margin-top:4px;">Lenge etter kurset er ferdig</div>
                </div>
            </div>

            <div style="text-align:center;">
                <a href="/course/121" class="reprise-cta-btn" style="font-size:17px;padding:16px 40px;box-shadow:0 4px 15px rgba(134,39,54,0.3);">
                    Les mer og bestill →
                </a>
                <p style="font-size:13px;color:#888;margin-top:12px;">🏷️ Earlybird-pris til 1. april — <strong style="color:#862736;">spar kr 5 500</strong></p>
            </div>
        </div>
    </div>
</div>
@stop
