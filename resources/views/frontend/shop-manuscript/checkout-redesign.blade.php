@extends('frontend.layout')

@section('title')
<title>Bestill manusutvikling &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700&family=Source+Sans+3:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .manus-checkout {
            --wine: #862736;
            --wine-hover: #9c2e40;
            --wine-dark: #5c1a25;
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
            font-family: var(--font-body);
            -webkit-font-smoothing: antialiased;
        }

        /* ---- PROGRESS BAR ---- */
        .manus-checkout .checkout-progress {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: 1rem 2rem;
        }
        .manus-checkout .checkout-progress__inner {
            max-width: 900px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
        }
        .manus-checkout .checkout-step {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--text-muted);
        }
        .manus-checkout .checkout-step.active { color: var(--wine); font-weight: 600; }
        .manus-checkout .checkout-step.done { color: var(--green); }
        .manus-checkout .checkout-step__number {
            width: 26px; height: 26px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 700; flex-shrink: 0;
        }
        .manus-checkout .checkout-step.active .checkout-step__number { background: var(--wine); color: #fff; }
        .manus-checkout .checkout-step.done .checkout-step__number { background: var(--green-bg); color: var(--green); }
        .manus-checkout .checkout-step:not(.active):not(.done) .checkout-step__number { background: rgba(0,0,0,0.06); color: var(--text-muted); }
        .manus-checkout .checkout-step__divider {
            width: 48px; height: 1px;
            background: var(--border-strong);
            margin: 0 0.75rem;
        }

        /* ---- MAIN LAYOUT ---- */
        .manus-checkout .checkout-page {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 2rem;
            align-items: start;
        }
        .manus-checkout .checkout-main { min-width: 0; }
        .manus-checkout .checkout-main__heading {
            font-family: var(--font-display);
            font-size: 1.5rem; font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.35rem;
        }
        .manus-checkout .checkout-main__sub {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
        }

        /* ---- VIPPS HURTIGKASSE ---- */
        .manus-checkout .vipps-express-box {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.75rem;
            text-align: center;
            margin-bottom: 1.25rem;
        }
        .manus-checkout .vipps-express-box__title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.35rem;
        }
        .manus-checkout .vipps-express-box__desc {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }
        .manus-checkout .vipps-express-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            max-width: 360px;
            margin: 0 auto;
            padding: 0.9rem 1.5rem;
            background: #FF5B24;
            color: #fff;
            border-radius: 8px;
            font-family: var(--font-body);
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.15s;
        }
        .manus-checkout .vipps-express-btn:hover {
            background: #e84f1c;
            color: #fff;
            text-decoration: none;
        }
        .manus-checkout .vipps-express-box__note {
            font-size: 0.68rem;
            color: var(--text-secondary);
            margin-top: 0.75rem;
        }
        .manus-checkout .vipps-divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.5rem 0;
            font-size: 0.78rem;
            color: var(--text-secondary);
        }
        .manus-checkout .vipps-divider span {
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* ---- AUTH TABS ---- */
        .manus-checkout .auth-tabs {
            display: flex; gap: 0;
            border-bottom: 2px solid var(--border);
            margin-bottom: 1.5rem;
        }
        .manus-checkout .auth-tab {
            padding: 0.65rem 1.25rem;
            border: none; background: transparent;
            font-family: var(--font-body);
            font-size: 0.85rem; font-weight: 500;
            color: var(--text-muted);
            cursor: pointer; position: relative;
            transition: color 0.15s;
        }
        .manus-checkout .auth-tab:hover { color: var(--text-primary); }
        .manus-checkout .auth-tab.active { color: var(--wine); font-weight: 600; }
        .manus-checkout .auth-tab.active::after {
            content: '';
            position: absolute;
            bottom: -2px; left: 0; right: 0;
            height: 2px; background: var(--wine);
        }
        .manus-checkout .auth-panel { display: none; }
        .manus-checkout .auth-panel.active { display: block; }

        /* ---- FORM CARD ---- */
        .manus-checkout .form-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.75rem;
        }
        .manus-checkout .form-group { margin-bottom: 1rem; }
        .manus-checkout .form-group label {
            display: block; font-size: 0.78rem;
            font-weight: 600; color: var(--text-primary);
            margin-bottom: 0.3rem;
        }
        .manus-checkout .form-group input {
            width: 100%;
            padding: 0.65rem 0.9rem;
            border: 1px solid var(--border-strong);
            border-radius: 6px;
            font-family: var(--font-body);
            font-size: 0.875rem;
            color: var(--text-primary);
            outline: none;
            transition: border-color 0.15s;
        }
        .manus-checkout .form-group input:focus { border-color: var(--wine); }
        .manus-checkout .form-group input::placeholder { color: var(--text-muted); }
        .manus-checkout .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }
        .manus-checkout .form-submit {
            width: 100%; padding: 0.75rem;
            background: var(--wine); color: #fff;
            border: none; border-radius: 6px;
            font-family: var(--font-body);
            font-size: 0.9rem; font-weight: 600;
            cursor: pointer; transition: background 0.15s;
            margin-top: 0.5rem;
        }
        .manus-checkout .form-submit:hover { background: var(--wine-hover); }
        .manus-checkout .form-divider {
            display: flex; align-items: center;
            gap: 1rem; margin: 1.25rem 0;
            font-size: 0.78rem; color: var(--text-muted);
        }
        .manus-checkout .form-divider::before,
        .manus-checkout .form-divider::after {
            content: ''; flex: 1; height: 1px;
            background: var(--border);
        }

        /* ---- SOCIAL LOGIN ---- */
        .manus-checkout .social-logins { display: flex; flex-direction: column; gap: 0.6rem; }
        .manus-checkout .social-btn {
            display: flex; align-items: center; justify-content: center;
            gap: 0.6rem; width: 100%; padding: 0.65rem;
            border: 1px solid var(--border-strong); border-radius: 6px;
            background: #fff; font-family: var(--font-body);
            font-size: 0.85rem; font-weight: 500;
            color: var(--text-primary); cursor: pointer;
            transition: border-color 0.15s, background 0.15s;
            text-decoration: none;
        }
        .manus-checkout .social-btn:hover { border-color: var(--border-strong); background: var(--cream); }
        .manus-checkout .social-btn svg { width: 18px; height: 18px; }
        .manus-checkout .social-btn img { height: 20px; width: auto; }

        /* ---- FORGOT LINK ---- */
        .manus-checkout .forgot-link {
            display: block; text-align: right;
            font-size: 0.78rem; color: var(--wine);
            text-decoration: none;
            margin-top: -0.5rem; margin-bottom: 1rem;
        }
        .manus-checkout .forgot-link:hover { text-decoration: underline; }

        /* ---- LOGGED IN CARD ---- */
        .manus-checkout .logged-in-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.75rem;
            display: flex; align-items: center; gap: 1rem;
        }
        .manus-checkout .logged-in-card__avatar {
            width: 48px; height: 48px; border-radius: 50%;
            background: var(--wine-light-solid);
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; font-weight: 700;
            color: var(--wine); flex-shrink: 0;
        }
        .manus-checkout .logged-in-card__name { font-size: 1rem; font-weight: 600; color: var(--text-primary); }
        .manus-checkout .logged-in-card__email { font-size: 0.8rem; color: var(--text-muted); }
        .manus-checkout .logged-in-card__change {
            margin-left: auto;
            font-size: 0.78rem; color: var(--wine);
            text-decoration: none;
        }

        /* ---- ORDER SUMMARY ---- */
        .manus-checkout .order-summary {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            position: sticky; top: 2rem;
            overflow: hidden;
        }
        .manus-checkout .order-summary__header {
            padding: 1.25rem 1.5rem;
            background: var(--cream);
            border-bottom: 1px solid var(--border);
        }
        .manus-checkout .order-summary__title { font-size: 0.95rem; font-weight: 700; color: var(--text-primary); }
        .manus-checkout .order-summary__body { padding: 1.5rem; }
        .manus-checkout .order-summary__product {
            margin-bottom: 1.25rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid var(--border);
        }
        .manus-checkout .order-summary__product-name { font-size: 1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.35rem; }
        .manus-checkout .order-summary__product-desc { font-size: 0.8rem; color: var(--text-muted); line-height: 1.5; }
        .manus-checkout .order-summary__includes {
            margin-top: 0.75rem;
            display: flex; flex-direction: column; gap: 0.35rem;
        }
        .manus-checkout .order-summary__include-item {
            display: flex; align-items: center; gap: 0.5rem;
            font-size: 0.78rem; color: var(--text-secondary);
        }
        .manus-checkout .order-summary__include-item svg { width: 14px; height: 14px; stroke: var(--green); flex-shrink: 0; }

        /* Uploaded file */
        .manus-checkout .order-summary__file {
            margin-top: 1rem;
            padding: 0.85rem 1rem;
            background: var(--green-bg);
            border: 1px solid rgba(46, 125, 50, 0.15);
            border-radius: 8px;
            display: flex; align-items: center; gap: 0.75rem;
        }
        .manus-checkout .order-summary__file-icon {
            width: 36px; height: 36px; border-radius: 8px;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .manus-checkout .order-summary__file-icon svg { width: 18px; height: 18px; }
        .manus-checkout .order-summary__file-info { flex: 1; min-width: 0; }
        .manus-checkout .order-summary__file-name {
            font-size: 0.8rem; font-weight: 600;
            color: var(--text-primary);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .manus-checkout .order-summary__file-meta { font-size: 0.7rem; color: var(--green); }
        .manus-checkout .order-summary__file-change {
            font-size: 0.72rem; color: var(--wine);
            text-decoration: none; font-weight: 600; flex-shrink: 0;
        }
        .manus-checkout .order-summary__file-change:hover { text-decoration: underline; }
        .manus-checkout .order-summary__no-file {
            margin-top: 1rem;
            padding: 0.85rem 1rem;
            background: var(--cream);
            border: 1px dashed var(--border-strong);
            border-radius: 8px; text-align: center;
        }
        .manus-checkout .order-summary__no-file-text { font-size: 0.78rem; color: var(--text-muted); margin-bottom: 0.5rem; }
        .manus-checkout .order-summary__no-file-hint { font-size: 0.7rem; color: var(--text-muted); font-style: italic; }

        /* Price rows */
        .manus-checkout .order-row {
            display: flex; justify-content: space-between;
            padding: 0.45rem 0; font-size: 0.85rem;
        }
        .manus-checkout .order-row__label { color: var(--text-secondary); }
        .manus-checkout .order-row__value { font-weight: 600; color: var(--text-primary); }
        .manus-checkout .order-total {
            display: flex; justify-content: space-between;
            padding: 1rem 0 0; margin-top: 0.75rem;
            border-top: 2px solid var(--text-primary);
        }
        .manus-checkout .order-total__label { font-size: 1rem; font-weight: 600; color: var(--text-primary); }
        .manus-checkout .order-total__price {
            font-family: var(--font-display);
            font-size: 1.5rem; font-weight: 700; color: var(--wine);
        }
        .manus-checkout .order-total__price span {
            font-size: 0.8rem; font-weight: 400;
            font-family: var(--font-body);
            color: var(--text-muted);
        }
        .manus-checkout .order-note {
            font-size: 0.72rem; color: var(--text-muted);
            margin-top: 0.5rem; text-align: right;
        }

        /* Confirm button */
        .manus-checkout .order-confirm {
            width: 100%; padding: 0.85rem;
            background: var(--wine); color: #fff;
            border: none; border-radius: 8px;
            font-family: var(--font-body);
            font-size: 0.95rem; font-weight: 600;
            cursor: pointer; margin-top: 1.25rem;
            transition: background 0.15s;
        }
        .manus-checkout .order-confirm:hover { background: var(--wine-hover); }
        .manus-checkout .order-confirm:disabled {
            background: rgba(0,0,0,0.1);
            color: var(--text-muted);
            cursor: not-allowed;
        }
        .manus-checkout .order-secure {
            display: flex; align-items: center; justify-content: center;
            gap: 0.4rem; margin-top: 0.75rem;
            font-size: 0.72rem; color: var(--text-muted);
        }
        .manus-checkout .order-secure svg { width: 12px; height: 12px; stroke: var(--text-muted); }

        /* Back link */
        .manus-checkout .back-link {
            display: inline-flex; align-items: center; gap: 0.35rem;
            font-size: 0.825rem; color: var(--text-muted);
            text-decoration: none; margin-bottom: 1.5rem;
            transition: color 0.15s;
        }
        .manus-checkout .back-link:hover { color: var(--wine); }
        .manus-checkout .back-link svg { width: 16px; height: 16px; stroke: currentColor; }

        /* Alert box */
        .manus-checkout .checkout-alert {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            font-size: 0.82rem;
            color: #991b1b;
        }

        /* ---- COACHING ADD-ON ---- */
        .manus-checkout .coaching-addon {
            margin: 1.25rem 0;
            padding: 1rem;
            background: var(--cream);
            border: 1px solid rgba(134, 39, 54, 0.15);
            border-radius: 10px;
        }
        .manus-checkout .coaching-addon__header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--wine);
            margin-bottom: 0.35rem;
        }
        .manus-checkout .coaching-addon__desc {
            font-size: 0.78rem;
            color: var(--text-secondary);
            margin-bottom: 0.85rem;
        }
        .manus-checkout .coaching-addon__options {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }
        .manus-checkout .coaching-option {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.6rem 0.75rem;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: #fff;
            cursor: pointer;
            transition: border-color 0.15s;
        }
        .manus-checkout .coaching-option:has(input:checked) {
            border-color: var(--wine);
            background: rgba(134, 39, 54, 0.02);
        }
        .manus-checkout .coaching-option input { accent-color: var(--wine); }
        .manus-checkout .coaching-option__label {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.825rem;
            font-weight: 500;
            color: var(--text-primary);
        }
        .manus-checkout .coaching-option__price {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .manus-checkout .coaching-option__old {
            font-size: 0.72rem;
            color: var(--text-muted);
            text-decoration: line-through;
        }
        .manus-checkout .coaching-addon__note {
            font-size: 0.68rem;
            color: var(--text-muted);
            margin-top: 0.6rem;
        }
        .manus-checkout .coaching-file-upload {
            margin-top: 0.65rem;
        }
        .manus-checkout .coaching-file-upload label {
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--text-primary);
            display: block;
            margin-bottom: 0.25rem;
        }
        .manus-checkout .coaching-file-upload label span {
            font-weight: 400;
            color: var(--text-muted);
        }
        .manus-checkout .coaching-file-upload input[type="file"] {
            width: 100%;
            padding: 0.45rem 0.6rem;
            border: 1px dashed var(--border-strong);
            border-radius: 6px;
            font-size: 0.78rem;
            font-family: var(--font-body);
            color: var(--text-secondary);
            background: #fff;
            cursor: pointer;
        }
        .manus-checkout .coaching-file-upload input[type="file"]:hover {
            border-color: var(--wine);
        }
        .manus-checkout .coaching-file-upload .coaching-file-hint {
            font-size: 0.68rem;
            color: var(--green);
            margin-top: 0.3rem;
        }

        @media (max-width: 768px) {
            .manus-checkout .checkout-page { grid-template-columns: 1fr; }
            .manus-checkout .order-summary { position: static; order: -1; }
            .manus-checkout .form-row { grid-template-columns: 1fr; }
            .manus-checkout .checkout-progress__inner { flex-wrap: wrap; gap: 0.25rem; }
            .manus-checkout .checkout-step__divider { width: 24px; }
        }
    </style>
@stop

@section('content')

@php
    // ── Prisberegning basert på ordtelling ──────────────
    $words = $tempFile['word_count'] ?? 0;

    if ($words > 0) {
        if ($words <= 5000) {
            $basePrice = 1500;
            $priceLabel = 'Fastpris inntil 5 000 ord';
        } elseif ($words <= 17500) {
            $overWords = $words - 5000;
            $basePrice = 1500 + round($overWords * 0.112);
            $priceLabel = '1 500 + ' . number_format($overWords, 0, ',', ' ') . ' ord x 0,112 kr';
        } else {
            $overWords = $words - 17500;
            $basePrice = 2900 + round($overWords * 0.15);
            $priceLabel = '2 900 + ' . number_format($overWords, 0, ',', ' ') . ' ord x 0,15 kr';
        }
    } else {
        $basePrice = $shopManuscript->full_payment_price;
        $priceLabel = 'Estimert pris';
    }

    // Sjangerpåslag — beregnes fra genre_id, JS oppdaterer live
    $selectedGenreId = (int) ($tempFile['genre_id'] ?? 0);
    $genreMultiplier = match($selectedGenreId) {
        17 => 1.3,  // Novelle +30%
        10 => 1.5,  // Lyrikk +50%
        default => 1.0,
    };
    $genreSurcharge = round($basePrice * ($genreMultiplier - 1));
    $priceBeforeMva = round($basePrice * $genreMultiplier);

    // Mva-fritak: kun elever med AKTIV betalt undervisning
    $isMvaFree = auth()->check() && $userHasPaidCourse;
    $mva = $isMvaFree ? 0 : round($priceBeforeMva * 0.25);
    $totalPrice = $priceBeforeMva + $mva;
@endphp

<div class="manus-checkout">

    {{-- ═══════════ PROGRESS BAR ═══════════ --}}
    <div class="checkout-progress">
        <div class="checkout-progress__inner">
            <div class="checkout-step done">
                <span class="checkout-step__number">&#10003;</span>
                Velg pakke
            </div>
            <div class="checkout-step__divider"></div>
            @guest
                <div class="checkout-step active">
                    <span class="checkout-step__number">2</span>
                    Logg inn / registrer
                </div>
                <div class="checkout-step__divider"></div>
                <div class="checkout-step">
                    <span class="checkout-step__number">3</span>
                    Bekreft bestilling
                </div>
            @endguest
            @auth
                <div class="checkout-step done">
                    <span class="checkout-step__number">&#10003;</span>
                    Logg inn
                </div>
                <div class="checkout-step__divider"></div>
                <div class="checkout-step active">
                    <span class="checkout-step__number">3</span>
                    Bekreft bestilling
                </div>
                <div class="checkout-step__divider"></div>
                <div class="checkout-step">
                    <span class="checkout-step__number">4</span>
                    Betaling
                </div>
            @endauth
        </div>
    </div>

    {{-- ═══════════ CHECKOUT LAYOUT ═══════════ --}}
    <div class="checkout-page">

        {{-- LEFT: AUTH / CONFIRM --}}
        <div class="checkout-main">

            <a href="{{ route('front.shop-manuscript.index') }}" class="back-link">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                Tilbake til manusutvikling
            </a>

            @if(session('error'))
                <div class="checkout-alert">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="checkout-alert">
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            @guest
                {{-- ═══════════ STATE A: IKKE INNLOGGET ═══════════ --}}
                <h1 class="checkout-main__heading">Logg inn eller opprett konto</h1>
                <p class="checkout-main__sub">For å bestille trenger du en konto hos Forfatterskolen.</p>

                {{-- ═══════════ VIPPS HURTIGKASSE ═══════════ --}}
                <div class="vipps-express-box">
                    <div class="vipps-express-box__title">Raskeste vei til bestilling</div>
                    <div class="vipps-express-box__desc">Betal direkte — vi oppretter konto automatisk med Vipps-infoen din.</div>
                    <a href="{{ url('/manusutvikling/vipps-express?package=' . $shopManuscript->id) }}" class="vipps-express-btn">
                        <svg width="70" height="20" viewBox="0 0 481 134" fill="#fff" xmlns="http://www.w3.org/2000/svg">
                            <path d="M88.9 2.4L56.5 97.7c-2.7 8-10.1 13.4-18.5 13.4h-.3c-8.4 0-15.8-5.3-18.5-13.4L2.2 44.3c-2-5.9 1.2-12.3 7.1-14.3 5.9-2 12.3 1.2 14.3 7.1l13.7 40.7L56 10.5c1.5-4.5 5.7-7.5 10.4-7.5h12.2c5.5 0 10 4.5 10 10v.1c.3-.2.3-.5.3-.7z"/>
                            <path d="M104.2 11.7c0-6.5 5.3-11.7 11.7-11.7 6.5 0 11.7 5.3 11.7 11.7 0 6.5-5.3 11.7-11.7 11.7-6.4 0-11.7-5.2-11.7-11.7zm2.1 26.2c0-5.3 4.3-9.6 9.6-9.6 5.3 0 9.6 4.3 9.6 9.6v62.3c0 5.3-4.3 9.6-9.6 9.6-5.3 0-9.6-4.3-9.6-9.6V37.9z"/>
                            <path d="M157.4 28.3c23 0 39.9 18.2 39.9 41.3 0 23-16.9 41.5-39.9 41.5-12.4 0-22.3-5.3-28.8-13.4v33.5c0 5.3-4.3 9.6-9.6 9.6s-9.6-4.3-9.6-9.6V37.9c0-5.3 4.3-9.6 9.6-9.6s9.6 4.3 9.6 9.6v3.8c6.5-8.2 16.4-13.4 28.8-13.4zm-3.7 63.6c13.4 0 23-10.6 23-22.3s-9.6-22.1-23-22.1-23 10.1-23 22.1 9.6 22.3 23 22.3z"/>
                            <path d="M230.3 28.3c23 0 39.9 18.2 39.9 41.3 0 23-16.9 41.5-39.9 41.5-12.4 0-22.3-5.3-28.8-13.4v33.5c0 5.3-4.3 9.6-9.6 9.6s-9.6-4.3-9.6-9.6V37.9c0-5.3 4.3-9.6 9.6-9.6s9.6 4.3 9.6 9.6v3.8c6.5-8.2 16.4-13.4 28.8-13.4zm-3.7 63.6c13.4 0 23-10.6 23-22.3s-9.6-22.1-23-22.1-23 10.1-23 22.1 9.6 22.3 23 22.3z"/>
                            <path d="M303 84.6c5.1 3.5 12.2 7.3 21.6 7.3 8 0 12.7-3.2 12.7-8 0-5.6-7-6.5-15.3-8-13.2-2.4-29.3-5.3-29.3-24.7 0-15.3 13.2-24 29-24 11.7 0 21.1 3.5 27.8 8.5 4.5 3.2 5.3 9.4 2.1 13.7-3.2 4.3-9.1 5.3-13.4 2.4-5.1-3.5-11.2-6-17.2-6-6.7 0-10.9 2.9-10.9 7 0 4.8 6.5 6 14.8 7.5 13.2 2.7 29.8 5.6 29.8 25.2 0 16.4-13 25.7-31.5 25.7-13.7 0-24.2-4.8-31.2-10.6-4-3.5-4.5-9.6-1-13.7 3.6-3.8 8.7-4.8 12.7-2.4l-.7.1z"/>
                        </svg>
                        Hurtigkasse
                    </a>
                    <div class="vipps-express-box__note">Ingen innlogging nødvendig. Navn og e-post hentes fra Vipps.</div>
                </div>

                <div class="vipps-divider">
                    <span></span>
                    eller logg inn / registrer
                    <span></span>
                </div>

                <div class="auth-tabs">
                    <button class="auth-tab active" onclick="switchCheckoutAuth('login')">Logg inn</button>
                    <button class="auth-tab" onclick="switchCheckoutAuth('register')">Ny kunde</button>
                </div>

                {{-- Login panel --}}
                <div class="auth-panel active" id="checkout-panel-login">
                    <div class="form-card">
                        <form method="POST" action="{{ route('frontend.login.checkout.store') }}">
                            @csrf
                            <input type="hidden" name="redirect_url" value="{{ url()->current() }}">
                            <div class="form-group">
                                <label for="login-email">E-post</label>
                                <input type="email" id="login-email" name="email" placeholder="din@epost.no" value="{{ old('email') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="login-pw">Passord</label>
                                <input type="password" id="login-pw" name="password" placeholder="Ditt passord" required>
                            </div>
                            <a href="{{ route('frontend.passwordreset.store') }}" class="forgot-link" onclick="event.preventDefault(); window.location.href='/auth/login';">Glemt passord?</a>
                            <button type="submit" class="form-submit">Logg inn og gå til betaling</button>
                        </form>

                        <div class="form-divider">eller</div>

                        <div class="social-logins">
                            <a href="{{ route('auth.login.google') }}" class="social-btn">
                                <svg viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                                Fortsett med Google
                            </a>
                            <a href="{{ route('auth.login.vipps') }}" class="social-btn">
                                <img src="{{ asset('images-new/icon/vipps-text.png') }}" alt="Vipps">
                                Fortsett med Vipps
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Register panel --}}
                <div class="auth-panel" id="checkout-panel-register">
                    <div class="form-card">
                        <form method="POST" action="{{ route('frontend.register.store') }}">
                            @csrf
                            <input type="hidden" name="redirect" value="{{ url()->current() }}">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="reg-fornavn">Fornavn</label>
                                    <input type="text" id="reg-fornavn" name="register_first_name" placeholder="Ditt fornavn" value="{{ old('register_first_name') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="reg-etternavn">Etternavn</label>
                                    <input type="text" id="reg-etternavn" name="register_last_name" placeholder="Ditt etternavn" value="{{ old('register_last_name') }}" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="reg-email">E-post</label>
                                <input type="email" id="reg-email" name="register_email" placeholder="din@epost.no" value="{{ old('register_email') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="reg-pw">Passord</label>
                                <input type="password" id="reg-pw" name="register_password" placeholder="Minst 8 tegn" required>
                            </div>
                            {!! NoCaptcha::renderJs() !!}
                            {!! NoCaptcha::display() !!}
                            <button type="submit" class="form-submit" style="margin-top: 1rem;">Opprett konto og gå til betaling</button>
                        </form>

                        <div class="form-divider">eller</div>

                        <div class="social-logins">
                            <a href="{{ route('auth.login.google') }}" class="social-btn">
                                <svg viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                                Fortsett med Google
                            </a>
                            <a href="{{ route('auth.login.vipps') }}" class="social-btn">
                                <img src="{{ asset('images-new/icon/vipps-text.png') }}" alt="Vipps">
                                Fortsett med Vipps
                            </a>
                        </div>
                    </div>
                </div>
            @endguest

            @auth
                {{-- ═══════════ STATE B: INNLOGGET ═══════════ --}}
                <h1 class="checkout-main__heading">Bekreft bestilling</h1>
                <p class="checkout-main__sub">Du er logget inn og klar til å bestille.</p>

                <div class="logged-in-card">
                    <div class="logged-in-card__avatar">
                        {{ strtoupper(substr(Auth::user()->first_name ?? 'U', 0, 1) . substr(Auth::user()->last_name ?? '', 0, 1)) }}
                    </div>
                    <div>
                        <div class="logged-in-card__name">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                        <div class="logged-in-card__email">{{ Auth::user()->email }}</div>
                    </div>
                    <a href="{{ route('auth.login.show') }}" class="logged-in-card__change">Bytt konto</a>
                </div>
            @endauth
        </div>

        {{-- RIGHT: ORDER SUMMARY --}}
        <div class="order-summary">
            <div class="order-summary__header">
                <div class="order-summary__title">Din bestilling</div>
            </div>
            <div class="order-summary__body">
                <div class="order-summary__product">
                    <div class="order-summary__product-name">Manusutvikling</div>
                    @if($words > 0)
                        <div class="order-summary__product-desc">{{ number_format($words, 0, ',', ' ') }} ord &middot; Tilbakemelding fra profesjonell redaktør</div>
                    @else
                        <div class="order-summary__product-desc">Tilbakemelding fra profesjonell redaktør</div>
                    @endif

                    <div class="order-summary__includes">
                        <div class="order-summary__include-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Skriftlig tilbakemelding
                        </div>
                        <div class="order-summary__include-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Kommentarer i margen
                        </div>
                        @if($words > 5000)
                        <div class="order-summary__include-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Synopsis
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Opplastet manus --}}
                @if($tempFile && isset($tempFile['original_name']))
                    <div class="order-summary__file">
                        <div class="order-summary__file-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#2e7d32" stroke-width="1.5" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                        </div>
                        <div class="order-summary__file-info">
                            <div class="order-summary__file-name">{{ $tempFile['original_name'] }}</div>
                            <div class="order-summary__file-meta">{{ number_format($tempFile['word_count'] ?? 0, 0, ',', ' ') }} ord &middot; Lastet opp</div>
                        </div>
                        <a href="{{ route('front.shop-manuscript.index') }}" class="order-summary__file-change">Endre</a>
                    </div>
                @else
                    <div class="order-summary__no-file">
                        <div class="order-summary__no-file-text">Ingen manus lastet opp ennå</div>
                        <div class="order-summary__no-file-hint">Du kan også laste opp manuset etter bestilling.</div>
                    </div>
                @endif

                {{-- Sjanger --}}
                <div style="margin-top: 0.85rem;">
                    <label style="font-size: 0.72rem; font-weight: 600; color: var(--text-primary); display: block; margin-bottom: 0.25rem;">Sjanger</label>
                    <select id="genreSelect" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border-strong); border-radius: 6px; font-size: 0.825rem; font-family: var(--font-body); background: #fff; appearance: auto; color: var(--text-primary);">
                        <option value="0" data-multiplier="1">Velg sjanger</option>
                        @foreach($genres as $g)
                            <option value="{{ $g->id }}"
                                data-multiplier="{{ $g->id == 10 ? '1.5' : ($g->id == 17 ? '1.3' : '1') }}"
                                {{ $selectedGenreId == $g->id ? 'selected' : '' }}>
                                {{ $g->name }}@if($g->id == 10) (+50%)@elseif($g->id == 17) (+30%)@endif
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Synopsis (valgfritt) --}}
                <div style="margin-top: 0.85rem; padding-top: 0.85rem; border-top: 1px solid rgba(0,0,0,0.06);">
                    <label style="font-size: 0.72rem; font-weight: 600; color: var(--text-primary); display: block; margin-bottom: 0.25rem;">
                        Kort beskrivelse av manuset <span style="font-weight: 400; color: var(--text-muted);">(valgfritt)</span>
                    </label>
                    <textarea id="synopsisText" rows="3" placeholder="Hva handler manuset om? Sjanger, målgruppe, hvor langt du har kommet..."
                        style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border-strong); border-radius: 6px; font-size: 0.8rem; font-family: var(--font-body); resize: vertical; color: var(--text-primary);"></textarea>
                    <div style="font-size: 0.68rem; color: var(--text-muted); margin-top: 0.25rem;">
                        Hjelper redaktøren å gi mer relevant tilbakemelding. Du kan også legge dette til senere.
                    </div>
                </div>

                {{-- Coaching add-on --}}
                <div class="coaching-addon">
                    <div class="coaching-addon__header">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round" style="width:16px;height:16px;">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <span>Legg til coaching &ndash; spar 10%</span>
                    </div>
                    <div class="coaching-addon__desc">
                        Personlig gjennomgang av tilbakemeldingen med redaktøren.
                    </div>
                    <div class="coaching-addon__options">
                        <label class="coaching-option">
                            <input type="radio" name="coaching" value="" checked>
                            <span class="coaching-option__label">Ingen coaching</span>
                        </label>
                        <label class="coaching-option">
                            <input type="radio" name="coaching" value="30">
                            <span class="coaching-option__label">
                                30 min
                                <span class="coaching-option__price">
                                    <span class="coaching-option__old">1 190 kr</span>
                                    <strong>1 071 kr</strong>
                                </span>
                            </span>
                        </label>
                        <label class="coaching-option">
                            <input type="radio" name="coaching" value="60">
                            <span class="coaching-option__label">
                                60 min
                                <span class="coaching-option__price">
                                    <span class="coaching-option__old">1 690 kr</span>
                                    <strong>1 521 kr</strong>
                                </span>
                            </span>
                        </label>
                    </div>
                    {{-- Hva trenger du hjelp med — vises kun når coaching er valgt --}}
                    <div id="coachingTopicField" style="display: none; margin-top: 0.75rem;">
                        <label style="font-size: 0.72rem; font-weight: 600; color: var(--text-primary); display: block; margin-bottom: 0.25rem;">
                            Hva ønsker du hjelp med?
                        </label>
                        <textarea id="coachingTopicText" rows="2" placeholder="F.eks. struktur, dialog, perspektiv, hvordan komme videre..."
                            style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border-strong); border-radius: 6px; font-size: 0.8rem; font-family: var(--font-body); resize: vertical; color: var(--text-primary);"></textarea>

                        <div class="coaching-file-upload">
                            <label>Last opp manus for gjennomlesing <span>(valgfritt)</span></label>
                            <input type="file" id="coachingFileInput" accept=".docx,.doc,.pdf,.odt,.pages">
                            <div class="coaching-file-hint">✓ Gjennomlesing inkludert i manusutvikling.</div>
                        </div>
                    </div>

                    <div class="coaching-addon__note">Priser eks. mva. Timen bookes etter at tilbakemeldingen er levert.</div>
                </div>

                {{-- Prisrader --}}
                <div class="order-row" style="margin-top: 1rem;">
                    <span class="order-row__label">{{ $priceLabel }}</span>
                    <span class="order-row__value">kr {{ number_format($basePrice, 0, ',', ' ') }}</span>
                </div>

                {{-- Sjangerpåslag (vises dynamisk av JS) --}}
                <div class="order-row" id="genre-surcharge-row" style="{{ $genreMultiplier > 1 ? '' : 'display:none;' }}">
                    <span class="order-row__label" id="genre-surcharge-label">Sjangerpåslag{{ $genreMultiplier > 1 ? ($selectedGenreId == 17 ? ' (+30%)' : ' (+50%)') : '' }}</span>
                    <span class="order-row__value" id="genre-surcharge-value">kr {{ number_format($genreSurcharge, 0, ',', ' ') }}</span>
                </div>

                {{-- Coaching prisrad (vises dynamisk av JS) --}}
                <div class="order-row" id="coaching-price-row" style="display:none;">
                    <span class="order-row__label">Coaching <span id="coaching-duration-label"></span> (&ndash;10%)</span>
                    <span class="order-row__value" id="coaching-price-display"></span>
                </div>

                @auth
                    @if($isMvaFree)
                        <div class="order-total" id="order-total-block">
                            <span class="order-total__label">Totalt</span>
                            <span class="order-total__price" id="order-total-price">kr {{ number_format($priceBeforeMva, 0, ',', ' ') }}</span>
                        </div>
                        <div class="order-note">Elevpris &ndash; mva-fri (aktiv undervisning)</div>
                        <form method="POST" action="{{ route('front.shop-manuscript.create-order', $shopManuscript->id) }}" id="order-form-mvafri" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="shop_manuscript_id" value="{{ $shopManuscript->id }}">
                            <input type="hidden" name="price" value="{{ $priceBeforeMva }}" id="hidden-price-mvafri">
                            <input type="hidden" name="additional" value="0" id="hidden-additional-mvafri">
                            <input type="hidden" name="genre" value="{{ $selectedGenreId }}" id="hidden-genre-mvafri">
                            <input type="hidden" name="description" value="" id="hidden-description-mvafri">
                            <input type="hidden" name="payment_plan_id" value="8">
                            <input type="hidden" name="coaching_time_later" value="0" id="hidden-coaching-mvafri">
                            <input type="hidden" name="coaching_price" value="0" id="hidden-coaching-price-mvafri">
                            <input type="hidden" name="coaching_topic" value="" id="hidden-coaching-topic-mvafri">
                            <input type="hidden" name="email" value="{{ Auth::user()->email }}">
                            <input type="hidden" name="first_name" value="{{ Auth::user()->first_name }}">
                            <input type="hidden" name="last_name" value="{{ Auth::user()->last_name }}">
                            <input type="hidden" name="street" value="{{ Auth::user()->address->street ?? '' }}">
                            <input type="hidden" name="zip" value="{{ Auth::user()->address->zip ?? '' }}">
                            <input type="hidden" name="city" value="{{ Auth::user()->address->city ?? '' }}">
                            <input type="hidden" name="phone" value="{{ Auth::user()->address->phone ?? '' }}">
                            <button type="submit" class="order-confirm" id="order-btn-mvafri">
                                Bestill og betal kr {{ number_format($priceBeforeMva, 0, ',', ' ') }} &rarr;
                            </button>
                        </form>
                    @else
                        <div class="order-row" id="mva-row">
                            <span class="order-row__label">Mva (25%)</span>
                            <span class="order-row__value" id="mva-value">kr {{ number_format($mva, 0, ',', ' ') }}</span>
                        </div>
                        <div class="order-total" id="order-total-block">
                            <span class="order-total__label">Totalt</span>
                            <span class="order-total__price" id="order-total-price">kr {{ number_format($totalPrice, 0, ',', ' ') }} <span>inkl. mva</span></span>
                        </div>
                        <form method="POST" action="{{ route('front.shop-manuscript.create-order', $shopManuscript->id) }}" id="order-form-mva" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="shop_manuscript_id" value="{{ $shopManuscript->id }}">
                            <input type="hidden" name="price" value="{{ $priceBeforeMva }}" id="hidden-price-mva">
                            <input type="hidden" name="additional" value="{{ $mva }}" id="hidden-additional-mva">
                            <input type="hidden" name="genre" value="{{ $selectedGenreId }}" id="hidden-genre-mva">
                            <input type="hidden" name="description" value="" id="hidden-description-mva">
                            <input type="hidden" name="payment_plan_id" value="8">
                            <input type="hidden" name="coaching_time_later" value="0" id="hidden-coaching-mva">
                            <input type="hidden" name="coaching_price" value="0" id="hidden-coaching-price-mva">
                            <input type="hidden" name="coaching_topic" value="" id="hidden-coaching-topic-mva">
                            <input type="hidden" name="email" value="{{ Auth::user()->email }}">
                            <input type="hidden" name="first_name" value="{{ Auth::user()->first_name }}">
                            <input type="hidden" name="last_name" value="{{ Auth::user()->last_name }}">
                            <input type="hidden" name="street" value="{{ Auth::user()->address->street ?? '' }}">
                            <input type="hidden" name="zip" value="{{ Auth::user()->address->zip ?? '' }}">
                            <input type="hidden" name="city" value="{{ Auth::user()->address->city ?? '' }}">
                            <input type="hidden" name="phone" value="{{ Auth::user()->address->phone ?? '' }}">
                            <button type="submit" class="order-confirm" id="order-btn-mva">
                                Bestill og betal kr {{ number_format($totalPrice, 0, ',', ' ') }} &rarr;
                            </button>
                        </form>
                    @endif
                @else
                    <div class="order-row" id="mva-row">
                        <span class="order-row__label">Mva (25%)</span>
                        <span class="order-row__value" id="mva-value">kr {{ number_format($mva, 0, ',', ' ') }}</span>
                    </div>
                    <div class="order-total" id="order-total-block">
                        <span class="order-total__label">Totalt</span>
                        <span class="order-total__price" id="order-total-price">kr {{ number_format($totalPrice, 0, ',', ' ') }} <span>inkl. mva</span></span>
                    </div>
                    <div class="order-note">Pris beregnet fra ordtelling. Elever under aktiv undervisning er fritatt for mva.</div>
                    <button class="order-confirm" disabled>Logg inn for å bestille</button>
                @endauth

                <div class="order-secure">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Sikker betaling
                </div>
            </div>
        </div>

    </div>
</div>
@stop

@section('scripts')
<script>
    function switchCheckoutAuth(tabId) {
        document.querySelectorAll('.manus-checkout .auth-tab').forEach(function(t) { t.classList.remove('active'); });
        document.querySelectorAll('.manus-checkout .auth-panel').forEach(function(p) { p.classList.remove('active'); });
        event.target.classList.add('active');
        document.getElementById('checkout-panel-' + tabId).classList.add('active');
    }

    // ── Sjanger + coaching: live prisoppdatering ──────────────
    (function() {
        var basePrice = {{ $basePrice }};
        var isMvaFree = {{ $isMvaFree ? 'true' : 'false' }};
        var coachingPrices = { '30': 1071, '60': 1521 };

        function formatKr(n) {
            return 'kr ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        }

        function getGenreMultiplier() {
            var sel = document.getElementById('genreSelect');
            if (!sel) return 1;
            return parseFloat(sel.options[sel.selectedIndex].dataset.multiplier) || 1;
        }

        function getGenreId() {
            var sel = document.getElementById('genreSelect');
            return sel ? sel.value : '0';
        }

        function updateOrderTotal() {
            // ── Sjanger ──
            var multiplier = getGenreMultiplier();
            var genreSurcharge = Math.round(basePrice * (multiplier - 1));
            var priceBeforeMva = Math.round(basePrice * multiplier);
            var genreId = getGenreId();

            // Vis/skjul sjangerpåslag-rad
            var surchargeRow = document.getElementById('genre-surcharge-row');
            if (surchargeRow) {
                surchargeRow.style.display = multiplier > 1 ? '' : 'none';
            }
            var surchargeLabel = document.getElementById('genre-surcharge-label');
            if (surchargeLabel && multiplier > 1) {
                var pct = Math.round((multiplier - 1) * 100);
                surchargeLabel.textContent = 'Sjangerpåslag (+' + pct + '%)';
            }
            var surchargeValue = document.getElementById('genre-surcharge-value');
            if (surchargeValue) {
                surchargeValue.textContent = formatKr(genreSurcharge);
            }

            // ── Coaching ──
            var selected = document.querySelector('input[name="coaching"]:checked');
            var coachingVal = selected ? selected.value : '';
            var coachingPrice = coachingPrices[coachingVal] || 0;
            var coachingDuration = coachingVal || '0';

            // Vis/skjul coaching-tema felt
            var topicField = document.getElementById('coachingTopicField');
            if (topicField) {
                topicField.style.display = coachingVal ? 'block' : 'none';
            }

            var coachingRow = document.getElementById('coaching-price-row');
            if (coachingRow) {
                coachingRow.style.display = coachingPrice > 0 ? '' : 'none';
            }
            var durationLabel = document.getElementById('coaching-duration-label');
            if (durationLabel) {
                durationLabel.textContent = coachingVal ? coachingVal + ' min' : '';
            }
            var priceDisplay = document.getElementById('coaching-price-display');
            if (priceDisplay) {
                priceDisplay.textContent = coachingPrice > 0 ? formatKr(coachingPrice) : '';
            }

            // ── Synopsis ──
            var synopsisText = document.getElementById('synopsisText');
            var synopsisVal = synopsisText ? synopsisText.value : '';

            // ── Totaler ──
            var totalExMva = priceBeforeMva + coachingPrice;
            var newMva = isMvaFree ? 0 : Math.round(totalExMva * 0.25);
            var newTotal = totalExMva + newMva;

            if (isMvaFree) {
                var totalEl = document.getElementById('order-total-price');
                if (totalEl) totalEl.textContent = formatKr(totalExMva);

                var btnMvafri = document.getElementById('order-btn-mvafri');
                if (btnMvafri) btnMvafri.innerHTML = 'Bestill og betal ' + formatKr(totalExMva) + ' &rarr;';

                // Hidden fields — mva-fri form
                var el = function(id) { return document.getElementById(id); };
                if (el('hidden-price-mvafri')) el('hidden-price-mvafri').value = totalExMva;
                if (el('hidden-genre-mvafri')) el('hidden-genre-mvafri').value = genreId;
                if (el('hidden-description-mvafri')) el('hidden-description-mvafri').value = synopsisVal;
                if (el('hidden-coaching-mvafri')) el('hidden-coaching-mvafri').value = coachingDuration;
                if (el('hidden-coaching-price-mvafri')) el('hidden-coaching-price-mvafri').value = coachingPrice;
                if (el('hidden-coaching-topic-mvafri')) el('hidden-coaching-topic-mvafri').value = coachingVal ? (document.getElementById('coachingTopicText') || {}).value || '' : '';
            } else {
                var mvaVal = document.getElementById('mva-value');
                if (mvaVal) mvaVal.textContent = formatKr(newMva);

                var totalEl = document.getElementById('order-total-price');
                if (totalEl) totalEl.innerHTML = formatKr(newTotal) + ' <span>inkl. mva</span>';

                var btnMva = document.getElementById('order-btn-mva');
                if (btnMva) btnMva.innerHTML = 'Bestill og betal ' + formatKr(newTotal) + ' &rarr;';

                // Hidden fields — mva form
                var el = function(id) { return document.getElementById(id); };
                if (el('hidden-price-mva')) el('hidden-price-mva').value = totalExMva;
                if (el('hidden-additional-mva')) el('hidden-additional-mva').value = newMva;
                if (el('hidden-genre-mva')) el('hidden-genre-mva').value = genreId;
                if (el('hidden-description-mva')) el('hidden-description-mva').value = synopsisVal;
                if (el('hidden-coaching-mva')) el('hidden-coaching-mva').value = coachingDuration;
                if (el('hidden-coaching-price-mva')) el('hidden-coaching-price-mva').value = coachingPrice;
                if (el('hidden-coaching-topic-mva')) el('hidden-coaching-topic-mva').value = coachingVal ? (document.getElementById('coachingTopicText') || {}).value || '' : '';
            }
        }

        // Lytt på sjanger, coaching og synopsis
        var genreSelect = document.getElementById('genreSelect');
        if (genreSelect) genreSelect.addEventListener('change', updateOrderTotal);

        document.querySelectorAll('input[name="coaching"]').forEach(function(radio) {
            radio.addEventListener('change', updateOrderTotal);
        });

        // Synopsis + coaching_topic: oppdater hidden fields ved submit
        var forms = document.querySelectorAll('#order-form-mvafri, #order-form-mva');
        forms.forEach(function(form) {
            form.addEventListener('submit', function() {
                var synopsisText = document.getElementById('synopsisText');
                var val = synopsisText ? synopsisText.value : '';
                var hDesc = form.querySelector('input[name="description"]');
                if (hDesc) hDesc.value = val;

                var topicText = document.getElementById('coachingTopicText');
                var coachingSelected = document.querySelector('input[name="coaching"]:checked');
                var hTopic = form.querySelector('input[name="coaching_topic"]');
                if (hTopic) hTopic.value = (coachingSelected && coachingSelected.value && topicText) ? topicText.value : '';

                // Flytt coaching-fil inn i formen slik at den sendes med
                var coachingFile = document.getElementById('coachingFileInput');
                if (coachingFile && coachingSelected && coachingSelected.value && coachingFile.files.length > 0) {
                    coachingFile.setAttribute('name', 'coaching_file');
                    form.appendChild(coachingFile);
                }
            });
        });
    })();
</script>
@stop
