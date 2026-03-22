@extends('frontend.layout')

@section('title')
<title>{{ $course->title }} › Forfatterskolen</title>
@stop

@section('metas')
    <meta property="og:title" content="{{ $course->meta_title }}">
    <meta property="og:description" content="{{ $course->meta_description }}">
    <meta name="description" content="{{ $course->meta_description }}">
    <meta property="og:site_name" content="Forfatterskolen">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website" />
    @if ($course->meta_image)
        <meta property="og:image" content="{{ url($course->meta_image) }}">
        <meta property="twitter:image" content="{{ url($course->meta_image) }}">
    @endif
    <meta property="twitter:title" content="{{ $course->meta_title }}">
    <meta property="twitter:description" content="{{ $course->meta_description }}">
    <meta name="twitter:card" content="summary" />
@stop

@section('styles')
<style>
    /* ══════════════════════════════════════════════════════
       CSS VARIABLES
       ══════════════════════════════════════════════════════ */
    .rk-page {
        --wine: #862736;
        --wine-hover: #9c2e40;
        --wine-light: #f4e8ea;
        --cream: #faf8f5;
        --green: #2e7d32;
        --green-bg: #e8f5e9;
        --blue: #1565c0;
        --blue-bg: #e3f2fd;
        --text: #1a1a1a;
        --text-secondary: #5a5550;
        --text-muted: #8a8580;
        --border: rgba(0, 0, 0, 0.08);
        --border-strong: rgba(0, 0, 0, 0.12);
        --font-display: 'Playfair Display', Georgia, serif;
        --font-body: 'Source Sans 3', -apple-system, sans-serif;
        --radius: 10px;
        --radius-lg: 14px;
        --max-width: 1100px;
        font-family: var(--font-body);
        color: var(--text);
        -webkit-font-smoothing: antialiased;
    }

    .rk-page * { box-sizing: border-box; }
    .rk-container { max-width: var(--max-width); margin: 0 auto; padding: 0 2rem; }

    /* ══════════════════════════════════════════════════════
       1. STICKY EARLYBIRD BAR
       ══════════════════════════════════════════════════════ */
    .rk-earlybird-bar {
        position: sticky;
        top: 0;
        z-index: 100;
        background: linear-gradient(135deg, #1c1917, #2a2520);
        padding: 0.65rem 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1.25rem;
        color: #fff;
        font-size: 0.825rem;
    }

    .rk-earlybird-bar__badge {
        font-size: 0.6rem;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #1c1917;
        background: #ffd54f;
        padding: 0.2rem 0.55rem;
        border-radius: 3px;
    }

    .rk-earlybird-bar__text strong { color: #ffd54f; }

    .rk-earlybird-bar__countdown {
        display: flex;
        gap: 0.35rem;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
    }

    .rk-earlybird-bar__unit {
        background: rgba(255,255,255,0.1);
        padding: 0.15rem 0.4rem;
        border-radius: 3px;
        font-size: 0.75rem;
    }

    .rk-earlybird-bar__cta {
        background: var(--wine);
        color: #fff;
        padding: 0.35rem 0.85rem;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.75rem;
        transition: background 0.15s;
    }

    .rk-earlybird-bar__cta:hover { background: var(--wine-hover); color: #fff; }

    /* ══════════════════════════════════════════════════════
       2. HERO
       ══════════════════════════════════════════════════════ */
    .rk-hero { padding: 4rem 0 3rem; }

    .rk-hero__inner {
        display: grid;
        grid-template-columns: 1fr 420px;
        gap: 3rem;
        align-items: start;
    }

    .rk-hero__eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: var(--wine);
        margin-bottom: 0.75rem;
    }

    .rk-hero__eyebrow-dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        background: var(--green);
        animation: rkPulse 2s infinite;
    }

    @keyframes rkPulse { 0%,100%{opacity:1} 50%{opacity:0.4} }

    .rk-hero__title {
        font-family: var(--font-display);
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1.15;
        margin-bottom: 0.5rem;
    }

    .rk-hero__title em { color: var(--wine); font-style: italic; }

    .rk-hero__meta {
        font-size: 0.9rem;
        color: var(--text-muted);
        margin-bottom: 1.25rem;
    }

    .rk-hero__desc {
        font-size: 1rem;
        color: var(--text-secondary);
        line-height: 1.7;
        margin-bottom: 1.5rem;
        max-width: 520px;
    }

    .rk-hero__ctas { display: flex; gap: 0.75rem; margin-bottom: 2rem; }

    .rk-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-family: var(--font-body);
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.15s;
    }

    .rk-btn--primary { background: var(--wine); color: #fff; }
    .rk-btn--primary:hover { background: var(--wine-hover); color: #fff; }
    .rk-btn--outline { background: transparent; color: var(--wine); border: 1.5px solid var(--wine); }
    .rk-btn--outline:hover { background: var(--wine); color: #fff; }

    .rk-hero__trust {
        display: flex;
        gap: 1.5rem;
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    .rk-hero__trust-item {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .rk-hero__trust-item svg { width: 16px; height: 16px; stroke: var(--green); }

    .rk-hero__image {
        border-radius: var(--radius-lg);
        overflow: hidden;
        margin-bottom: 1rem;
        height: 220px;
        background: #e8e4df;
    }

    .rk-hero__image img { width: 100%; height: 100%; object-fit: cover; object-position: center 20%; }

    .rk-hero__quick-price {
        background: var(--cream);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
    }

    .rk-hero__quick-price-label {
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
    }

    .rk-hero__quick-price-row {
        display: flex;
        align-items: baseline;
        gap: 0.5rem;
        margin-bottom: 0.35rem;
    }

    .rk-hero__quick-price-amount {
        font-family: var(--font-display);
        font-size: 2rem;
        font-weight: 700;
        color: var(--wine);
    }

    .rk-hero__quick-price-original {
        font-size: 0.85rem;
        color: var(--text-muted);
        text-decoration: line-through;
    }

    .rk-hero__quick-price-save {
        display: inline-block;
        font-size: 0.65rem;
        font-weight: 600;
        color: var(--green);
        background: var(--green-bg);
        padding: 0.15rem 0.45rem;
        border-radius: 3px;
        margin-bottom: 0.75rem;
    }

    .rk-hero__quick-price-note {
        font-size: 0.72rem;
        color: var(--text-muted);
    }

    /* ══════════════════════════════════════════════════════
       3. FEATURES
       ══════════════════════════════════════════════════════ */
    .rk-features { padding: 3.5rem 0; background: var(--cream); }

    .rk-section-title {
        font-family: var(--font-display);
        font-size: 1.75rem;
        font-weight: 700;
        text-align: center;
        margin-bottom: 0.5rem;
    }

    .rk-section-sub {
        text-align: center;
        font-size: 0.95rem;
        color: var(--text-secondary);
        margin-bottom: 2.5rem;
    }

    .rk-features-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
    }

    .rk-feature-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 1.75rem;
    }

    .rk-feature-card__icon {
        width: 44px; height: 44px;
        border-radius: 10px;
        background: var(--wine-light);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .rk-feature-card__icon svg { width: 22px; height: 22px; stroke: var(--wine); }

    .rk-feature-card__title {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 0.35rem;
    }

    .rk-feature-card__desc {
        font-size: 0.825rem;
        color: var(--text-secondary);
        line-height: 1.6;
    }

    /* ══════════════════════════════════════════════════════
       4. KURSPLAN TIMELINE
       ══════════════════════════════════════════════════════ */
    .rk-kursplan { padding: 4rem 0; }

    .rk-timeline {
        position: relative;
        max-width: 720px;
        margin: 0 auto;
    }

    .rk-timeline::before {
        content: '';
        position: absolute;
        left: 28px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: var(--border);
    }

    .rk-timeline-item {
        display: flex;
        gap: 1.25rem;
        padding-bottom: 1.75rem;
        position: relative;
    }

    .rk-timeline-item:last-child { padding-bottom: 0; }

    .rk-timeline-item__marker {
        width: 56px;
        flex-shrink: 0;
        text-align: center;
    }

    .rk-timeline-item__week {
        width: 56px; height: 56px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid var(--border);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 1;
    }

    .rk-timeline-item__week-num {
        font-size: 1rem;
        font-weight: 700;
        color: var(--wine);
        line-height: 1;
    }

    .rk-timeline-item__week-label {
        font-size: 0.5rem;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--text-muted);
    }

    .rk-timeline-item__content {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.25rem;
        flex: 1;
        transition: box-shadow 0.15s;
    }

    .rk-timeline-item__content:hover {
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    }

    .rk-timeline-item__title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 0.25rem;
    }

    .rk-timeline-item__date {
        font-size: 0.72rem;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
    }

    .rk-timeline-item__desc {
        font-size: 0.825rem;
        color: var(--text-secondary);
        line-height: 1.6;
    }

    .rk-timeline-item__tags {
        display: flex;
        gap: 0.35rem;
        margin-top: 0.6rem;
        flex-wrap: wrap;
    }

    .rk-timeline-tag {
        font-size: 0.6rem;
        font-weight: 600;
        padding: 0.15rem 0.45rem;
        border-radius: 3px;
    }

    .rk-timeline-tag--modul { background: var(--wine-light); color: var(--wine); }
    .rk-timeline-tag--webinar { background: var(--blue-bg); color: var(--blue); }
    .rk-timeline-tag--innlevering { background: var(--green-bg); color: var(--green); }

    .rk-timeline-more {
        text-align: center;
        margin-top: 1.5rem;
        font-size: 0.825rem;
        color: var(--text-muted);
    }

    /* ══════════════════════════════════════════════════════
       5. PACKAGES
       ══════════════════════════════════════════════════════ */
    .rk-packages { padding: 4rem 0; background: var(--cream); }

    .rk-package-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
        align-items: start;
    }

    .rk-package-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        transition: box-shadow 0.15s;
    }

    .rk-package-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.06); }

    .rk-package-card--popular {
        border: 2px solid var(--wine);
        position: relative;
        transform: scale(1.03);
    }

    .rk-package-card__popular-badge {
        position: absolute;
        top: 0; left: 0; right: 0;
        background: var(--wine);
        color: #fff;
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        text-align: center;
        padding: 0.35rem;
    }

    .rk-package-card__header {
        padding: 2rem 1.5rem 1.25rem;
        text-align: center;
    }

    .rk-package-card--popular .rk-package-card__header {
        padding-top: 2.75rem;
    }

    .rk-package-card__name {
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
    }

    .rk-package-card__price {
        font-family: var(--font-display);
        font-size: 2.25rem;
        font-weight: 700;
        color: var(--wine);
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .rk-package-card__original {
        font-size: 0.825rem;
        color: var(--text-muted);
        text-decoration: line-through;
    }

    .rk-package-card__save {
        display: inline-block;
        font-size: 0.65rem;
        font-weight: 600;
        color: var(--green);
        background: var(--green-bg);
        padding: 0.15rem 0.5rem;
        border-radius: 3px;
        margin-top: 0.35rem;
    }

    .rk-package-card__features { padding: 0 1.5rem 1.5rem; }

    .rk-package-feature {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        padding: 0.45rem 0;
        font-size: 0.8rem;
        color: var(--text-secondary);
        border-bottom: 1px solid var(--border);
    }

    .rk-package-feature:last-child { border-bottom: none; }
    .rk-package-feature svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px; }
    .rk-package-feature--included svg { stroke: var(--green); }
    .rk-package-feature--excluded { color: var(--text-muted); opacity: 0.5; }
    .rk-package-feature--excluded svg { stroke: var(--text-muted); }
    .rk-package-feature--highlight { font-weight: 600; color: var(--text); }

    .rk-package-card__cta { padding: 0 1.5rem 1.5rem; }
    .rk-package-card__cta .rk-btn { width: 100%; justify-content: center; }

    .rk-package-card__note {
        font-size: 0.68rem;
        color: var(--text-muted);
        text-align: center;
        margin-top: 0.5rem;
    }

    /* ══════════════════════════════════════════════════════
       6. TESTIMONIALS
       ══════════════════════════════════════════════════════ */
    .rk-testimonials { padding: 4rem 0; }

    .rk-testimonial-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
    }

    .rk-testimonial-card {
        background: var(--cream);
        border-radius: var(--radius-lg);
        padding: 1.75rem;
    }

    .rk-testimonial-card__stars {
        color: #ffa000;
        font-size: 0.9rem;
        margin-bottom: 0.75rem;
    }

    .rk-testimonial-card__text {
        font-size: 0.875rem;
        color: var(--text-secondary);
        line-height: 1.7;
        font-style: italic;
        margin-bottom: 0.85rem;
    }

    .rk-testimonial-card__author {
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--text);
    }

    /* ══════════════════════════════════════════════════════
       7. RISK-FREE
       ══════════════════════════════════════════════════════ */
    .rk-risk-free { padding: 3.5rem 0; background: var(--cream); }

    .rk-risk-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
    }

    .rk-risk-card { text-align: center; padding: 1.5rem; }

    .rk-risk-card__icon {
        width: 48px; height: 48px;
        border-radius: 50%;
        background: var(--green-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.85rem;
    }

    .rk-risk-card__icon svg { width: 24px; height: 24px; stroke: var(--green); }

    .rk-risk-card__title {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .rk-risk-card__desc {
        font-size: 0.825rem;
        color: var(--text-secondary);
        line-height: 1.5;
    }

    /* ══════════════════════════════════════════════════════
       8. FINAL CTA
       ══════════════════════════════════════════════════════ */
    .rk-final-cta { padding: 4rem 0; text-align: center; }

    .rk-final-cta__title {
        font-family: var(--font-display);
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .rk-final-cta__sub {
        font-size: 0.95rem;
        color: var(--text-secondary);
        margin-bottom: 1.5rem;
    }

    /* ══════════════════════════════════════════════════════
       RESPONSIVE
       ══════════════════════════════════════════════════════ */
    @media (max-width: 768px) {
        .rk-hero__inner { grid-template-columns: 1fr; }
        .rk-features-grid { grid-template-columns: 1fr; }
        .rk-package-grid { grid-template-columns: 1fr; }
        .rk-package-card--popular { transform: none; }
        .rk-testimonial-grid { grid-template-columns: 1fr; }
        .rk-risk-grid { grid-template-columns: 1fr; }
        .rk-earlybird-bar { flex-wrap: wrap; font-size: 0.75rem; gap: 0.5rem; }
        .rk-hero__trust { flex-wrap: wrap; gap: 0.75rem; }
        .rk-hero__title { font-size: 2rem; }
    }
</style>
@stop

@section('content')
@php
    $earlybirdDeadline = \Carbon\Carbon::parse(config('courses.romankurs.earlybird_deadline'));
    $isEarlybird = now()->isBefore($earlybirdDeadline);
    $discount = config('courses.romankurs.earlybird_discount');
    $packages = $course->packagesIsShow;
    $startDate = \Carbon\Carbon::parse($course->start_date);
    $lessons = $course->lessons()->where('title', 'like', 'Modul%')->orderBy('order')->get();

    // Cheapest package for hero quick-price
    $cheapest = $packages->sortBy('calculated_price')->first();

    // Module descriptions and tags (not in DB)
    $moduleInfo = [
        1 => ['desc' => 'Sjangre, hva en historie består av, og hvordan du spisser idéen din til en hel roman.', 'tags' => ['modul', 'webinar']],
        2 => ['desc' => 'Struktur, komposisjon og plot. Bygg et solid skjelett for historien din.', 'tags' => ['modul', 'webinar']],
        3 => ['desc' => 'Persongalleri, hovedperson og karakterutvikling. Innlevering av de 2 første sidene.', 'tags' => ['modul', 'webinar', 'innlevering']],
        4 => ['desc' => 'Hvem forteller historien? Valg som påvirker hele leseopplevelsen.', 'tags' => ['modul', 'webinar']],
        5 => ['desc' => 'Hekt leseren på første side og slipp aldri taket. Tilbakemelding på de 2 første sidene.', 'tags' => ['modul', 'webinar', 'innlevering']],
        6 => ['desc' => 'Balansen mellom å vise og fortelle. Et av de viktigste grepene å mestre.', 'tags' => ['modul', 'webinar']],
        7 => ['desc' => 'Bruk språket bevisst. Innlevering av valgfri tekst (1000 ord) + gruppeoppgave.', 'tags' => ['modul', 'webinar', 'innlevering']],
        8 => ['desc' => 'God dialog, indre tanker og formatering av manus.', 'tags' => ['modul', 'webinar']],
        9 => ['desc' => 'Redigering steg for steg. Gruppeoppgave-tilbakemeldinger leveres og mottas.', 'tags' => ['modul', 'webinar', 'innlevering']],
        10 => ['desc' => 'Følgebrev, forlagsinnsending, forfatterøkonomi og selvpublisering. Avslutning og veien videre.', 'tags' => ['modul', 'webinar']],
    ];

    // Tag labels
    $tagLabels = [
        'modul' => 'Modul',
        'webinar' => 'Webinar',
        'innlevering' => 'Innlevering',
    ];

    // Feature comparison per package
    $featureList = [
        ['label' => '10 kursmoduler + videoer', 'basic' => true, 'standard' => true, 'pro' => true],
        ['label' => '10 live webinarer', 'basic' => true, 'standard' => true, 'pro' => true],
        ['label' => 'Tilbakemelding på 2 første sider', 'basic' => true, 'standard' => true, 'pro' => true],
        ['label' => 'Gruppeoppgave (1000 ord)', 'basic' => true, 'standard' => true, 'pro' => true],
        ['label' => 'Mentermøter i ett år', 'basic' => true, 'standard' => true, 'pro' => true],
        ['label' => 'Tilbakemelding på 20 sider (7 500 ord)', 'basic' => false, 'standard' => true, 'pro' => true, 'highlight' => true],
        ['label' => 'Manusutvikling', 'basic' => false, 'standard' => '1 (17 500 ord)', 'pro' => '4 (70 000 ord)', 'highlight' => true],
        ['label' => 'Coaching med redaktør', 'basic' => false, 'standard' => false, 'pro' => '1 time', 'highlight' => true],
    ];
@endphp

<div class="rk-page">

    {{-- ═══════════ 1. STICKY EARLYBIRD BAR ═══════════ --}}
    @if($isEarlybird)
    <div class="rk-earlybird-bar" id="rkStickyBar">
        <span class="rk-earlybird-bar__badge">&#9889; Earlybird</span>
        <span class="rk-earlybird-bar__text">Spar <strong>kr {{ number_format($discount, 0, ',', ' ') }}</strong> — prisen går opp 1. april</span>
        <div class="rk-earlybird-bar__countdown">
            <span class="rk-earlybird-bar__unit" id="rksDays">--d</span>
            <span class="rk-earlybird-bar__unit" id="rksHours">--t</span>
            <span class="rk-earlybird-bar__unit" id="rksMins">--m</span>
        </div>
        <a href="#pakker" class="rk-earlybird-bar__cta">Se priser ↓</a>
    </div>
    @endif

    {{-- ═══════════ 2. HERO ═══════════ --}}
    <section class="rk-hero">
        <div class="rk-container">
            <div class="rk-hero__inner">
                <div>
                    <div class="rk-hero__eyebrow">
                        <span class="rk-hero__eyebrow-dot"></span>
                        Oppstart {{ $startDate->format('d') }}. {{ \App\Http\FrontendHelpers::convertMonthLanguage($startDate->format('n')) }}
                    </div>
                    <h1 class="rk-hero__title">Romankurs <em>i gruppe</em></h1>
                    <p class="rk-hero__meta">10 uker intensivt gruppekurs + 1 år tilgang til alt materiale</p>
                    <p class="rk-hero__desc">
                        Lær å skrive en helstøpt roman med erfarne forfattere og redaktører.
                        Du får kunnskap, profesjonell tilbakemelding og et skrivemiljø som
                        bærer deg hele veien fra idé til ferdig manus.
                    </p>
                    <div class="rk-hero__ctas">
                        <a href="#pakker" class="rk-btn rk-btn--primary">Se pakker og pris ↓</a>
                        <a href="#kursplan" class="rk-btn rk-btn--outline">Se kursplanen</a>
                    </div>
                    <div class="rk-hero__trust">
                        <span class="rk-hero__trust-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                            14 dagers angrefrist
                        </span>
                        <span class="rk-hero__trust-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Avbetaling tilgjengelig
                        </span>
                        <span class="rk-hero__trust-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                            200+ utgitte elever
                        </span>
                    </div>
                </div>

                <div class="rk-hero__sidebar">
                    <div class="rk-hero__image">
                        <img src="{{ url($course->course_image) }}" alt="{{ $course->title }}">
                    </div>
                    @if($cheapest)
                    <div class="rk-hero__quick-price">
                        <div class="rk-hero__quick-price-label">{{ $isEarlybird ? 'Earlybird-pris fra' : 'Pris fra' }}</div>
                        <div class="rk-hero__quick-price-row">
                            <span class="rk-hero__quick-price-amount">kr {{ number_format($cheapest->calculated_price, 0, ',', ' ') }}</span>
                            @if($cheapest->full_payment_is_sale)
                                <span class="rk-hero__quick-price-original">kr {{ number_format($cheapest->full_payment_price, 0, ',', ' ') }}</span>
                            @endif
                        </div>
                        @if($cheapest->full_payment_is_sale)
                            <span class="rk-hero__quick-price-save">Spar kr {{ number_format($cheapest->sale_discount, 0, ',', ' ') }}</span>
                        @endif
                        <div class="rk-hero__quick-price-note">
                            @if($isEarlybird)
                                Prisen går opp 1. april. Bestill nå, betal senere.
                            @else
                                Avbetaling tilgjengelig. Bestill nå, betal senere.
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════ 3. HVA DU FÅR ═══════════ --}}
    <section class="rk-features">
        <div class="rk-container">
            <h2 class="rk-section-title">Hva du får</h2>
            <p class="rk-section-sub">Alt du trenger for å skrive en roman — på ett sted.</p>

            <div class="rk-features-grid">
                <div class="rk-feature-card">
                    <div class="rk-feature-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    </div>
                    <div class="rk-feature-card__title">10 kursmoduler</div>
                    <div class="rk-feature-card__desc">Skriftlig materiale, videoer og øvelser som tar deg fra idé til ferdig førsteutkast. Tilgang i ett helt år.</div>
                </div>
                <div class="rk-feature-card">
                    <div class="rk-feature-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                    </div>
                    <div class="rk-feature-card__title">10 live webinarer</div>
                    <div class="rk-feature-card__desc">Tirsdager kl. 20:00 med kurslærerne. Still spørsmål, få svar. Alt tas opp for reprise.</div>
                </div>
                <div class="rk-feature-card">
                    <div class="rk-feature-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="13" y2="17"/></svg>
                    </div>
                    <div class="rk-feature-card__title">Tilbakemelding fra redaktør</div>
                    <div class="rk-feature-card__desc">Profesjonell vurdering av teksten din. Hva fungerer, hva kan forbedres, og hvordan komme videre.</div>
                </div>
                <div class="rk-feature-card">
                    <div class="rk-feature-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div class="rk-feature-card__title">Mentermøter i ett år</div>
                    <div class="rk-feature-card__desc">Mandager kl. 20:00 med kjente forfattere. 100+ timer i arkivet med blant annet Maja Lunde og Tom Egeland.</div>
                </div>
                <div class="rk-feature-card">
                    <div class="rk-feature-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                    </div>
                    <div class="rk-feature-card__title">Skrivemiljø for livet</div>
                    <div class="rk-feature-card__desc">Lukket kursgruppe + Forfatterskolens skriveforum med hundrevis av skrivende. Livslangt medlemskap.</div>
                </div>
                <div class="rk-feature-card">
                    <div class="rk-feature-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </div>
                    <div class="rk-feature-card__title">Bonus: Tom Egeland-kurs</div>
                    <div class="rk-feature-card__desc">4 timers miniskrivekurs med bestselgerforfatteren Tom Egeland. Inkludert i arkivet for alle elever.</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════ 4. KURSPLAN TIMELINE ═══════════ --}}
    <section class="rk-kursplan" id="kursplan">
        <div class="rk-container">
            <h2 class="rk-section-title">Kursplan</h2>
            <p class="rk-section-sub">10 intensive uker med ny modul og webinar hver uke.</p>

            <div class="rk-timeline">
                @foreach($lessons as $index => $lesson)
                    @php
                        $moduleNum = $index + 1;
                        $info = $moduleInfo[$moduleNum] ?? ['desc' => '', 'tags' => []];
                        $lessonDate = \Carbon\Carbon::parse($lesson->delay);
                        $lessonDateEnd = $lessonDate->copy()->addDays(6);
                    @endphp
                    <div class="rk-timeline-item">
                        <div class="rk-timeline-item__marker">
                            <div class="rk-timeline-item__week">
                                <span class="rk-timeline-item__week-num">{{ $moduleNum }}</span>
                                <span class="rk-timeline-item__week-label">Uke</span>
                            </div>
                        </div>
                        <div class="rk-timeline-item__content">
                            <div class="rk-timeline-item__title">{{ preg_replace('/^Modul\s+\d+\.\s*/', '', $lesson->title) }}</div>
                            <div class="rk-timeline-item__date">{{ $lessonDate->format('d') }}. – {{ $lessonDateEnd->format('d') }}. {{ \App\Http\FrontendHelpers::convertMonthLanguage($lessonDateEnd->format('n')) }}</div>
                            <div class="rk-timeline-item__desc">{!! $info['desc'] !!}</div>
                            <div class="rk-timeline-item__tags">
                                @foreach($info['tags'] as $tag)
                                    <span class="rk-timeline-tag rk-timeline-tag--{{ $tag }}">
                                        @if($tag === 'modul') Modul {{ $moduleNum }}
                                        @elseif($tag === 'webinar') Webinar
                                        @elseif($tag === 'innlevering') Innlevering
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="rk-timeline-more">
                <p>
                    + Innlevering av 20 sider (7 500 ord) i august med tilbakemelding i september.
                    <br>Standard- og pro-pakke inkluderer manusutvikling du kan bruke hele året.
                </p>
            </div>
        </div>
    </section>

    {{-- ═══════════ 5. PACKAGES ═══════════ --}}
    <section class="rk-packages" id="pakker">
        <div class="rk-container">
            <h2 class="rk-section-title">Velg din pakke</h2>
            <p class="rk-section-sub">Alle pakker inkluderer kursmoduler, webinarer, mentermøter og skrivemiljø.</p>

            <div class="rk-package-grid">
                @foreach($packages as $package)
                    @php
                        $isPopular = $package->id == 351;
                        $tierName = str_replace('Romankurs (gruppe) – ', '', $package->variation);
                        $tierKey = strtolower($tierName);
                    @endphp
                    <div class="rk-package-card {{ $isPopular ? 'rk-package-card--popular' : '' }}">
                        @if($isPopular)
                            <div class="rk-package-card__popular-badge">MEST VALGT</div>
                        @endif
                        <div class="rk-package-card__header">
                            <div class="rk-package-card__name">{{ $tierName }}</div>
                            <div class="rk-package-card__price">kr {{ number_format($package->calculated_price, 0, ',', ' ') }}</div>
                            @if($package->full_payment_is_sale)
                                <div class="rk-package-card__original">kr {{ number_format($package->full_payment_price, 0, ',', ' ') }}</div>
                                <span class="rk-package-card__save">Spar {{ number_format($package->sale_discount, 0, ',', ' ') }}</span>
                            @endif
                        </div>
                        <div class="rk-package-card__features">
                            @foreach($featureList as $feature)
                                @php
                                    $val = $feature[$tierKey] ?? false;
                                    $included = $val !== false;
                                    $highlight = ($feature['highlight'] ?? false) && $included;
                                @endphp
                                <div class="rk-package-feature {{ $included ? 'rk-package-feature--included' : 'rk-package-feature--excluded' }} {{ $highlight ? 'rk-package-feature--highlight' : '' }}">
                                    @if($included)
                                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    @else
                                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    @endif
                                    {!! $feature['label'] !!}@if(is_string($val) && $val !== '1') <span style="color: var(--wine); font-weight: 600;">&nbsp;{{ $val }}</span>@endif
                                </div>
                            @endforeach
                        </div>
                        <div class="rk-package-card__cta">
                            <a href="{{ route($checkoutRoute, [$course->id, 'package' => $package->id]) }}"
                               class="rk-btn {{ $isPopular ? 'rk-btn--primary' : 'rk-btn--outline' }}">
                                Velg {{ $tierName }}
                            </a>
                            @if($tierKey === 'standard')
                                <div class="rk-package-card__note">Avbetaling fra kr 350/mnd</div>
                            @elseif($tierKey === 'pro')
                                <div class="rk-package-card__note">Avbetaling fra kr 558/mnd</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════ 6. TESTIMONIALS ═══════════ --}}
    <section class="rk-testimonials">
        <div class="rk-container">
            <h2 class="rk-section-title">Hva elevene sier</h2>
            <p class="rk-section-sub">Noen av tilbakemeldingene fra tidligere kursdeltakere.</p>

            <div class="rk-testimonial-grid">
                <div class="rk-testimonial-card">
                    <div class="rk-testimonial-card__stars">★★★★★</div>
                    <div class="rk-testimonial-card__text">«Det lureste jeg har gjort er å melde meg på romankurs hos Forfatterskolen. Så mye kunnskap, så profesjonelt og så mange flinke folk.»</div>
                    <div class="rk-testimonial-card__author">— Kursdeltaker</div>
                </div>
                <div class="rk-testimonial-card">
                    <div class="rk-testimonial-card__stars">★★★★★</div>
                    <div class="rk-testimonial-card__text">«Spennende, lærerikt og kjempegøy! Jeg er helt nybegynner, men synes opplegget er pedagogisk godt lagt opp.»</div>
                    <div class="rk-testimonial-card__author">— Kursdeltaker</div>
                </div>
                <div class="rk-testimonial-card">
                    <div class="rk-testimonial-card__stars">★★★★★</div>
                    <div class="rk-testimonial-card__text">«Først når man får en slik grundig og profesjonell tilbakemelding på eget manus, skjønner man om man har klart å anvende teorien eller ikke.»</div>
                    <div class="rk-testimonial-card__author">— Kursdeltaker, om manusutviklingen</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════ 7. RISK-FREE ═══════════ --}}
    <section class="rk-risk-free">
        <div class="rk-container">
            <h2 class="rk-section-title">Ingen risiko</h2>
            <p class="rk-section-sub">Vi er så sikre på at du blir fornøyd at vi gjør det enkelt for deg.</p>

            <div class="rk-risk-grid">
                <div class="rk-risk-card">
                    <div class="rk-risk-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <div class="rk-risk-card__title">14 dagers angrefrist</div>
                    <div class="rk-risk-card__desc">Full refusjon innen 14 dager fra kursstart, ingen spørsmål stilt.</div>
                </div>
                <div class="rk-risk-card">
                    <div class="rk-risk-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    </div>
                    <div class="rk-risk-card__title">Avbetaling</div>
                    <div class="rk-risk-card__desc">Opptil 24 måneder å betale ned kurset på. Fleksible løsninger.</div>
                </div>
                <div class="rk-risk-card">
                    <div class="rk-risk-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div class="rk-risk-card__title">Bestill nå, betal senere</div>
                    <div class="rk-risk-card__desc">Sikre deg plassen uten å betale med en gang. Du velger selv når.</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════ 8. FINAL CTA ═══════════ --}}
    <section class="rk-final-cta">
        <div class="rk-container">
            <h2 class="rk-final-cta__title">Klar for å gjøre alvor av skrivedrømmen?</h2>
            <p class="rk-final-cta__sub">Oppstart {{ $startDate->format('d') }}. {{ \App\Http\FrontendHelpers::convertMonthLanguage($startDate->format('n')) }}. @if($isEarlybird) Earlybird-prisen gjelder til 1. april — deretter går prisen opp. @endif</p>
            <a href="#pakker" class="rk-btn rk-btn--primary" style="font-size: 1rem; padding: 0.85rem 2rem;">Se pakker og meld deg på →</a>
        </div>
    </section>

</div>
@stop

@section('scripts')
<script>
    // Earlybird countdown for sticky bar
    (function() {
        var deadlineStr = '{{ config("courses.romankurs.earlybird_deadline") }}';
        if (!deadlineStr) return;
        var deadline = new Date(deadlineStr + 'T23:59:59');
        var daysEl = document.getElementById('rksDays');
        var hoursEl = document.getElementById('rksHours');
        var minsEl = document.getElementById('rksMins');
        var bar = document.getElementById('rkStickyBar');
        if (!daysEl || !hoursEl || !minsEl || !bar) return;

        function update() {
            var now = new Date();
            var diff = deadline - now;
            if (diff <= 0) {
                bar.style.display = 'none';
                return;
            }
            var d = Math.floor(diff / 86400000);
            var h = Math.floor((diff % 86400000) / 3600000);
            var m = Math.floor((diff % 3600000) / 60000);
            daysEl.textContent = d + 'd';
            hoursEl.textContent = (h < 10 ? '0' : '') + h + 't';
            minsEl.textContent = (m < 10 ? '0' : '') + m + 'm';
        }
        update();
        setInterval(update, 60000);
    })();

    // Smooth scroll for anchor links
    document.querySelectorAll('.rk-page a[href^="#"]').forEach(function(a) {
        a.addEventListener('click', function(e) {
            e.preventDefault();
            var target = document.querySelector(this.getAttribute('href'));
            if (target) {
                var offset = document.getElementById('rkStickyBar') ? 50 : 0;
                window.scrollTo({
                    top: target.offsetTop - offset,
                    behavior: 'smooth'
                });
            }
        });
    });
</script>
@stop
