@extends('frontend.layout')

@section('page_title', 'Gratiswebinar: ' . $freeWebinar->title . ' — Forfatterskolen')

@section('meta_desc', Str::limit(strip_tags($freeWebinar->description), 160))
@section('metas')
    <meta property="og:title" content="Gratiswebinar: {{ $freeWebinar->title }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($freeWebinar->description), 160) }}">
<meta property="og:type" content="event">
    @if($freeWebinar->image)
        <meta property="og:image" content="{{ asset('storage/' . $freeWebinar->image) }}">
        <meta name="twitter:image" content="{{ asset('storage/' . $freeWebinar->image) }}">
    @endif
@stop

@section('styles')
<style>
    :root {
        --wine: #862736;
        --wine-hover: #9c2e40;
        --wine-light-solid: #f4e8ea;
        --cream: #faf8f5;
        --green: #2e7d32;
        --green-bg: #e8f5e9;
        --text-primary: #1a1a1a;
        --text-secondary: #5a5550;
        --text-muted: #8a8580;
        --border: rgba(0, 0, 0, 0.08);
        --border-strong: rgba(0, 0, 0, 0.12);
        --font-display: 'Playfair Display', Georgia, serif;
        --font-body: 'Source Sans 3', -apple-system, sans-serif;
        --radius: 10px;
        --radius-lg: 14px;
        --fw-max-width: 1100px;
    }

    /* ── HERO ──────────────────────────────────────────── */
    .webinar-hero {
        background: linear-gradient(135deg, #1c1917, #2a2520);
        color: #fff;
        padding: 3.5rem 0;
    }

    .webinar-hero__container {
        max-width: var(--fw-max-width);
        margin: 0 auto;
        padding: 0 2rem;
    }

    .webinar-hero__inner {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 3rem;
        align-items: start;
    }

    .webinar-hero__badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.65rem;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
        background: var(--wine);
        padding: 0.3rem 0.75rem;
        border-radius: 20px;
        margin-bottom: 1rem;
        font-family: var(--font-body);
    }

    .webinar-hero__badge-dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        background: #4caf50;
        animation: pulse 2s infinite;
    }

    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }

    .webinar-hero__title {
        font-family: var(--font-display);
        font-size: 2.25rem;
        font-weight: 700;
        line-height: 1.15;
        margin-bottom: 0.75rem;
    }

    .webinar-hero__meta {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        font-size: 0.9rem;
        color: rgba(255,255,255,0.6);
        margin-bottom: 1.5rem;
        font-family: var(--font-body);
    }

    .webinar-hero__meta-item {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .webinar-hero__meta-item svg { width: 16px; height: 16px; stroke: rgba(255,255,255,0.4); }

    .webinar-hero__host {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        margin-bottom: 1.5rem;
    }

    .webinar-hero__host-avatar {
        width: 48px; height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #e8e4df, #d4cec6);
        flex-shrink: 0;
        overflow: hidden;
    }

    .webinar-hero__host-avatar img { width: 100%; height: 100%; object-fit: cover; }

    .webinar-hero__host-name { font-size: 0.9rem; font-weight: 600; font-family: var(--font-body); }
    .webinar-hero__host-role { font-size: 0.75rem; color: rgba(255,255,255,0.5); font-family: var(--font-body); }

    /* Countdown */
    .countdown {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .countdown__unit {
        text-align: center;
        background: rgba(255,255,255,0.08);
        border-radius: 8px;
        padding: 0.65rem 0.85rem;
        min-width: 60px;
    }

    .countdown__number {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1;
        font-variant-numeric: tabular-nums;
        font-family: var(--font-body);
    }

    .countdown__label {
        font-size: 0.55rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: rgba(255,255,255,0.5);
        margin-top: 3px;
        font-family: var(--font-body);
    }

    /* ── REGISTRATION FORM ─────────────────────────────── */
    .reg-card {
        background: #fff;
        border-radius: var(--radius-lg);
        padding: 2rem;
        color: var(--text-primary);
    }

    .reg-card__title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
        font-family: var(--font-body);
    }

    .reg-card__sub {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 1.25rem;
        font-family: var(--font-body);
    }

    .reg-card__spots {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--green);
        background: var(--green-bg);
        padding: 0.25rem 0.65rem;
        border-radius: 20px;
        margin-bottom: 1rem;
        font-family: var(--font-body);
    }

    .fw-form-group {
        margin-bottom: 0.85rem;
    }

    .fw-form-group label {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        margin-bottom: 0.3rem;
        font-family: var(--font-body);
    }

    .fw-form-group input {
        width: 100%;
        padding: 0.65rem 0.85rem;
        border: 1px solid var(--border-strong);
        border-radius: 6px;
        font-family: var(--font-body);
        font-size: 0.875rem;
        outline: none;
    }

    .fw-form-group input:focus { border-color: var(--wine); }

    /* Consent checkboxes */
    .consent-group {
        margin: 1rem 0;
    }

    .consent-item {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .consent-item input[type="checkbox"] {
        width: 16px;
        height: 16px;
        margin-top: 2px;
        accent-color: var(--wine);
        flex-shrink: 0;
    }

    .consent-item label {
        font-size: 0.72rem;
        color: var(--text-secondary);
        line-height: 1.5;
        font-family: var(--font-body);
    }

    .consent-item label a {
        color: var(--wine);
        text-decoration: none;
    }

    .reg-btn {
        width: 100%;
        padding: 0.85rem;
        background: var(--wine);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-family: var(--font-body);
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.15s;
    }

    .reg-btn:hover { background: var(--wine-hover); }
    .reg-btn:disabled { opacity: 0.5; cursor: not-allowed; }

    .reg-note {
        font-size: 0.68rem;
        color: var(--text-muted);
        text-align: center;
        margin-top: 0.65rem;
        font-family: var(--font-body);
    }

    /* ── REPRISE STATE ───────────────── */
    .reprise-card {
        background: #fff;
        border-radius: var(--radius-lg);
        padding: 2rem;
        color: var(--text-primary);
        text-align: center;
    }

    .reprise-card__badge {
        display: inline-block;
        font-size: 0.65rem;
        font-weight: 600;
        color: var(--wine);
        background: var(--wine-light-solid);
        padding: 0.25rem 0.65rem;
        border-radius: 20px;
        margin-bottom: 0.75rem;
        font-family: var(--font-body);
    }

    .reprise-card__title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 0.35rem;
        font-family: var(--font-body);
    }

    .reprise-card__sub {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 1.25rem;
        font-family: var(--font-body);
    }

    /* ── SUCCESS STATE ───────────────── */
    .success-card {
        background: #fff;
        border-radius: var(--radius-lg);
        padding: 2rem;
        color: var(--text-primary);
        text-align: center;
    }

    .success-card__icon {
        width: 48px; height: 48px;
        border-radius: 50%;
        background: var(--green-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
    }

    .success-card__icon svg { width: 24px; height: 24px; }

    /* ── CONTENT SECTION ───────────────────────────────── */
    .webinar-content {
        padding: 3.5rem 0;
    }

    .webinar-content__container {
        max-width: var(--fw-max-width);
        margin: 0 auto;
        padding: 0 2rem;
    }

    .webinar-content__inner {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 3rem;
        align-items: start;
    }

    .content-heading {
        font-family: var(--font-display);
        font-size: 1.35rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .content-text {
        font-size: 0.95rem;
        color: var(--text-secondary);
        line-height: 1.8;
        margin-bottom: 1.5rem;
        font-family: var(--font-body);
    }

    /* Feature list */
    .feature-list {
        margin-bottom: 1.5rem;
    }

    .feature-list__title {
        font-size: 0.95rem;
        font-weight: 700;
        margin-top: 0;
        margin-bottom: 0.65rem;
        font-family: var(--font-body);
    }

    .feature-list__item {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        padding: 0.4rem 0;
        font-size: 0.9rem;
        color: var(--text-secondary);
        line-height: 1.5;
        font-family: var(--font-body);
    }

    .feature-list__item svg { width: 18px; height: 18px; stroke: var(--green); flex-shrink: 0; margin-top: 2px; }

    /* Audience list */
    .audience-list {
        background: var(--cream);
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .audience-list__title {
        font-size: 0.9rem;
        font-weight: 700;
        margin-top: 0;
        margin-bottom: 0.5rem;
        font-family: var(--font-body);
    }

    .audience-list__item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0;
        font-size: 0.85rem;
        color: var(--text-secondary);
        font-family: var(--font-body);
    }

    .audience-list__item::before {
        content: '\2192';
        color: var(--wine);
        font-weight: 600;
    }

    /* ── SIDEBAR ────────────────────────── */
    .sidebar-cta {
        background: var(--cream);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        margin-bottom: 1.25rem;
    }

    .sidebar-cta__title {
        font-size: 0.9rem;
        font-weight: 700;
        margin-bottom: 0.35rem;
        font-family: var(--font-body);
    }

    .sidebar-cta__desc {
        font-size: 0.8rem;
        color: var(--text-muted);
        line-height: 1.5;
        margin-bottom: 1rem;
        font-family: var(--font-body);
    }

    .sidebar-cta__btn {
        display: block;
        text-align: center;
        padding: 0.65rem;
        background: var(--wine);
        color: #fff;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.825rem;
        font-family: var(--font-body);
    }

    .sidebar-cta__btn:hover { background: var(--wine-hover); color: #fff; text-decoration: none; }

    .sidebar-cta__note {
        font-size: 0.68rem;
        color: var(--text-muted);
        text-align: center;
        margin-top: 0.5rem;
        font-family: var(--font-body);
    }

    /* Earlybird mini */
    .earlybird-mini {
        background: linear-gradient(135deg, #1c1917, #2a2520);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        color: #fff;
        text-align: center;
    }

    .earlybird-mini__badge {
        display: inline-block;
        font-size: 0.55rem;
        font-weight: 700;
        color: #1c1917;
        background: #ffd54f;
        padding: 0.15rem 0.5rem;
        border-radius: 3px;
        margin-bottom: 0.5rem;
        font-family: var(--font-body);
    }

    .earlybird-mini__title {
        font-size: 0.9rem;
        font-weight: 700;
        margin-bottom: 0.15rem;
        font-family: var(--font-body);
    }

    .earlybird-mini__price {
        font-family: var(--font-display);
        font-size: 1.5rem;
        font-weight: 700;
        color: #ffd54f;
    }

    .earlybird-mini__original {
        font-size: 0.75rem;
        color: rgba(255,255,255,0.4);
        text-decoration: line-through;
        font-family: var(--font-body);
    }

    .earlybird-mini__btn {
        display: block;
        margin-top: 0.85rem;
        padding: 0.6rem;
        background: var(--wine);
        color: #fff;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.8rem;
        font-family: var(--font-body);
    }

    .earlybird-mini__btn:hover { background: var(--wine-hover); color: #fff; text-decoration: none; }

    /* Error alert */
    .fw-alert {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        margin-bottom: 1rem;
        font-size: 0.8rem;
        color: #991b1b;
        font-family: var(--font-body);
    }

    @media (max-width: 768px) {
        .webinar-hero__inner { grid-template-columns: 1fr; }
        .webinar-content__inner { grid-template-columns: 1fr; }
        .webinar-hero__meta { flex-wrap: wrap; gap: 0.75rem; }
    }
</style>
@stop

@section('content')

<?php
    $startDate = \Carbon\Carbon::parse($freeWebinar->start_date);
    $isFuture = $startDate->isFuture();
    $presenter = $freeWebinar->webinar_presenters->first();
?>

{{-- ═══════════ HERO ═══════════ --}}
<section class="webinar-hero">
    <div class="webinar-hero__container">
        <div class="webinar-hero__inner">
            <div>
                <div class="webinar-hero__badge">
                    @if($isFuture)
                        <span class="webinar-hero__badge-dot"></span> Gratiswebinar
                    @else
                        Reprise tilgjengelig
                    @endif
                </div>

                <h1 class="webinar-hero__title">{{ $freeWebinar->title }}</h1>

                <div class="webinar-hero__meta">
                    <span class="webinar-hero__meta-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        {{ ucfirst($startDate->translatedFormat('l j. F Y')) }}
                    </span>
                    <span class="webinar-hero__meta-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Kl. {{ $startDate->format('H:i') }}
                    </span>
                    <span class="webinar-hero__meta-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                        Live webinar (gratis)
                    </span>
                </div>

                @if($presenter)
                <div class="webinar-hero__host">
                    <div class="webinar-hero__host-avatar">
                        @if($presenter->image)
                            <img src="{{ $presenter->image }}" alt="{{ $presenter->first_name }}">
                        @endif
                    </div>
                    <div>
                        <div class="webinar-hero__host-name">{{ $presenter->first_name }} {{ $presenter->last_name }}</div>
                        <div class="webinar-hero__host-role">Rektor & grunnlegger, Forfatterskolen</div>
                    </div>
                </div>
                @endif

                @if($isFuture)
                <div class="countdown" id="countdown">
                    <div class="countdown__unit"><div class="countdown__number" id="cDays">0</div><div class="countdown__label">Dager</div></div>
                    <div class="countdown__unit"><div class="countdown__number" id="cHours">0</div><div class="countdown__label">Timer</div></div>
                    <div class="countdown__unit"><div class="countdown__number" id="cMins">0</div><div class="countdown__label">Min</div></div>
                    <div class="countdown__unit"><div class="countdown__number" id="cSecs">0</div><div class="countdown__label">Sek</div></div>
                </div>
                @endif
            </div>

            {{-- ── REGISTRATION / REPRISE / SUCCESS ──── --}}
            @if($isFuture)
            <div class="reg-card" id="regCard">
                <div class="reg-card__title">Meld deg p&aring; gratis</div>
                <div class="reg-card__sub">F&aring; lenken til webinaret rett i innboksen.</div>

                @if($errors->any())
                <div class="fw-alert">
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
                @endif

                <form action="{{ route('front.free-webinar.submit', $freeWebinar->id) }}" method="POST" id="regForm">
                    @csrf
                    <div class="fw-form-group">
                        <label>Fornavn</label>
                        <input type="text" name="first_name" placeholder="Ola" required value="{{ old('first_name') }}">
                    </div>
                    <div class="fw-form-group">
                        <label>Etternavn</label>
                        <input type="text" name="last_name" placeholder="Nordmann" required value="{{ old('last_name') }}">
                    </div>
                    <div class="fw-form-group">
                        <label>E-post</label>
                        <input type="email" name="email" placeholder="ola@eksempel.no" required value="{{ old('email') }}">
                    </div>

                    <div class="consent-group">
                        <div class="consent-item">
                            <input type="checkbox" id="consent_terms" name="consent_terms" required>
                            <label for="consent_terms">
                                Jeg godtar <a href="/terms/all" target="_blank">vilk&aring;r og betingelser</a> og <a href="/privacy" target="_blank">personvernreglene</a>. <span style="color: var(--wine);">*</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="reg-btn" id="regBtn">
                        Meld meg p&aring; gratiswebinaret &rarr;
                    </button>

                    <div class="reg-note">
                        Vi deler aldri e-postadressen din. Du kan melde deg av n&aring;r som helst.
                    </div>
                </form>
            </div>

            {{-- Success state (shown via JS after submit or via server redirect) --}}
            <div class="success-card" id="successState" style="display: none;">
                <div class="success-card__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#2e7d32" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div class="reg-card__title">Du er p&aring;meldt!</div>
                <div class="reg-card__sub" style="margin-bottom: 0;">
                    Vi sender deg lenken til webinaret og en p&aring;minnelse p&aring; e-post. Sjekk innboksen din!
                </div>
            </div>

            @else
            {{-- ── REPRISE CARD ──── --}}
            <div class="reprise-card">
                <span class="reprise-card__badge">Reprisen er klar</span>
                <div class="reprise-card__title">Du gikk glipp av webinaret?</div>
                <div class="reprise-card__sub">Ingen fare &mdash; se hele opptaket gratis. Oppgi e-post for &aring; f&aring; tilgang.</div>

                @if($errors->any())
                <div class="fw-alert">
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
                @endif

                @if($freeWebinar->replay_url)
                    <a href="{{ route('front.free-webinar-reprise', $freeWebinar->id) }}" class="reg-btn" style="display:block;text-align:center;text-decoration:none;margin-top:16px;">Se reprisen gratis &rarr;</a>
                    <p style="font-size:13px;color:#888;text-align:center;margin-top:12px;">Ingen registrering nødvendig</p>
                @else
                    <form action="{{ route('front.free-webinar.submit', $freeWebinar->id) }}" method="POST">
                        @csrf
                        <div class="fw-form-group">
                            <label>Fornavn</label>
                            <input type="text" name="first_name" placeholder="Ola" required value="{{ old('first_name') }}">
                        </div>
                        <div class="fw-form-group">
                            <label>Etternavn</label>
                            <input type="text" name="last_name" placeholder="Nordmann" required value="{{ old('last_name') }}">
                        </div>
                        <div class="fw-form-group">
                            <label>E-post</label>
                            <input type="email" name="email" placeholder="ola@eksempel.no" required value="{{ old('email') }}">
                        </div>
                        <div class="consent-group">
                            <div class="consent-item">
                                <input type="checkbox" name="consent_terms" required>
                                <label>Jeg godtar <a href="/terms/all" target="_blank">vilk&aring;rene</a> og <a href="/privacy" target="_blank">personvernreglene</a>. *</label>
                            </div>
                            <div class="consent-item">
                                <input type="checkbox" name="consent_marketing">
                                <label>Jeg &oslash;nsker gratis skrivetips og info om kurs. (Valgfritt)</label>
                            </div>
                        </div>
                        <button type="submit" class="reg-btn">Se reprisen gratis &rarr;</button>
                    </form>
                @endif
            </div>
            @endif
        </div>
    </div>
</section>

{{-- ═══════════ CONTENT ═══════════ --}}
<section class="webinar-content">
    <div class="webinar-content__container">
        <div class="webinar-content__inner">
            <div>
                {{-- Description er HTML fra admin (inneholder <p>-tags).
                     Render direkte som HTML i stedet for å escape + nl2br
                     så <p>-tags vises som paragrafer, ikke som råtekst. --}}
                <div class="content-text">
                    {!! $freeWebinar->description !!}
                </div>

                @if($freeWebinar->learning_points)
                <div class="feature-list">
                    <h2 class="feature-list__title">P&aring; webinaret l&aelig;rer du:</h2>
                    @foreach(explode("\n", trim($freeWebinar->learning_points)) as $point)
                        @if(trim($point))
                        <div class="feature-list__item">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                            {{ trim($point) }}
                        </div>
                        @endif
                    @endforeach
                </div>
                @endif

                @if($freeWebinar->target_audience)
                <div class="audience-list">
                    <h3 class="audience-list__title">Webinaret passer for deg som:</h3>
                    @foreach(explode("\n", trim($freeWebinar->target_audience)) as $item)
                        @if(trim($item))
                        <div class="audience-list__item">{{ trim($item) }}</div>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div>
                <div class="sidebar-cta">
                    <div class="sidebar-cta__title">Kan ikke vente?</div>
                    <div class="sidebar-cta__desc">Send inn en smakebit av teksten din og f&aring; gratis tilbakemelding fra en profesjonell redakt&oslash;r.</div>
                    <a href="{{ route('front.free-manuscript.index') }}" class="sidebar-cta__btn">Gratis tekstvurdering &rarr;</a>
                    <div class="sidebar-cta__note">Opptil 500 ord. Svar innen 3 virkedager.</div>
                </div>

                <div class="earlybird-mini">
                    <span class="earlybird-mini__badge">&#127873; Webinar-pris</span>
                    <div class="earlybird-mini__title">Romankurs &ndash; oppstart 20. april</div>
                    <div>
                        <span class="earlybird-mini__price">fra kr 5 900</span>
                        <span class="earlybird-mini__original">kr 10 900</span>
                    </div>
                    <a href="{{ route('front.course.show', 121) }}" class="earlybird-mini__btn">Se kurset &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</section>

@stop

@section('scripts')
<script>
    @if($isFuture)
    // Countdown
    var webinarDate = new Date('{{ $startDate->toIso8601String() }}').getTime();

    function updateCountdown() {
        var now = new Date().getTime();
        var diff = webinarDate - now;

        if (diff <= 0) {
            document.getElementById('countdown').style.display = 'none';
            return;
        }

        var d = Math.floor(diff / 86400000);
        var h = Math.floor((diff % 86400000) / 3600000);
        var m = Math.floor((diff % 3600000) / 60000);
        var s = Math.floor((diff % 60000) / 1000);

        document.getElementById('cDays').textContent = d;
        document.getElementById('cHours').textContent = h < 10 ? '0' + h : h;
        document.getElementById('cMins').textContent = m < 10 ? '0' + m : m;
        document.getElementById('cSecs').textContent = s < 10 ? '0' + s : s;
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);

    // Form validation
    var form = document.getElementById('regForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            var termsConsent = document.getElementById('consent_terms');
            if (!termsConsent.checked) {
                e.preventDefault();
                alert('Du m\u00e5 godta vilk\u00e5rene for \u00e5 melde deg p\u00e5.');
            }
        });
    }
    @endif
</script>
@stop
