@extends('frontend.layout')

@section('page_title', 'Velg betalingsmetode &rsaquo; Forfatterskolen')
@section('robots')<meta name="robots" content="noindex, follow">@endsection
@section('meta_desc', 'Velg betalingsmetode for din tekstvurdering hos Forfatterskolen.')

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

        /* ---- PAYMENT METHOD CARDS ---- */
        .payment-method {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.5rem 1.75rem;
            margin-bottom: 1rem;
            transition: border-color 0.15s;
        }
        .payment-method:hover {
            border-color: var(--border-strong);
        }
        .payment-method__header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
        }
        .payment-method__icon {
            width: 44px; height: 44px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-size: 1.3rem;
        }
        .payment-method__icon--vipps { background: #fff3ef; }
        .payment-method__icon--paypal { background: #eef4ff; }
        .payment-method__icon--faktura { background: var(--cream); }
        .payment-method__title {
            font-size: 1rem; font-weight: 700;
            color: var(--text-primary);
        }
        .payment-method__desc {
            font-size: 0.82rem;
            color: var(--text-muted);
        }
        .payment-method__body {
            margin-top: 0.75rem;
        }

        /* Vipps button */
        .pay-btn--vipps {
            display: flex; align-items: center; justify-content: center;
            gap: 0.6rem; width: 100%; padding: 0.85rem;
            background: #FF5B24; color: #fff;
            border: none; border-radius: 8px;
            font-family: var(--font-body);
            font-size: 0.95rem; font-weight: 600;
            cursor: pointer; transition: background 0.15s;
            text-decoration: none;
        }
        .pay-btn--vipps:hover { background: #e84f1c; color: #fff; text-decoration: none; }

        /* PayPal button */
        .pay-btn--paypal {
            display: flex; align-items: center; justify-content: center;
            gap: 0.6rem; width: 100%; padding: 0.85rem;
            background: #0070ba; color: #fff;
            border: none; border-radius: 8px;
            font-family: var(--font-body);
            font-size: 0.95rem; font-weight: 600;
            cursor: pointer; transition: background 0.15s;
        }
        .pay-btn--paypal:hover { background: #005ea6; }

        /* Faktura button */
        .pay-btn--faktura {
            display: flex; align-items: center; justify-content: center;
            gap: 0.5rem; width: 100%; padding: 0.85rem;
            background: var(--wine); color: #fff;
            border: none; border-radius: 8px;
            font-family: var(--font-body);
            font-size: 0.95rem; font-weight: 600;
            cursor: pointer; transition: background 0.15s;
        }
        .pay-btn--faktura:hover { background: var(--wine-hover); }

        /* Installment radio */
        .installment-options {
            display: flex; flex-direction: column;
            gap: 0.5rem; margin-bottom: 1rem;
        }
        .installment-option {
            display: flex; align-items: center; gap: 0.65rem;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-strong);
            border-radius: 8px;
            cursor: pointer;
            transition: border-color 0.15s, background 0.15s;
        }
        .installment-option:hover { background: var(--cream); }
        .installment-option.selected {
            border-color: var(--wine);
            background: var(--wine-light-solid);
        }
        .installment-option input[type="radio"] {
            accent-color: var(--wine);
            width: 16px; height: 16px;
        }
        .installment-option__label {
            font-size: 0.875rem; font-weight: 600;
            color: var(--text-primary);
        }
        .installment-option__detail {
            font-size: 0.78rem;
            color: var(--text-muted);
            margin-left: auto;
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
        .manus-checkout .order-secure {
            display: flex; align-items: center; justify-content: center;
            gap: 0.4rem; margin-top: 1rem;
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

        /* Alert */
        .manus-checkout .checkout-alert {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            font-size: 0.82rem;
            color: #991b1b;
        }

        @media (max-width: 768px) {
            .manus-checkout .checkout-page { grid-template-columns: 1fr; }
            .manus-checkout .order-summary { position: static; order: -1; }
            .manus-checkout .checkout-progress__inner { flex-wrap: wrap; gap: 0.25rem; }
            .manus-checkout .checkout-step__divider { width: 24px; }
        }

        /* Loading overlay */
        .checkout-loading-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(255,255,255,0.75);
            backdrop-filter: blur(2px);
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 1rem;
        }
        .checkout-loading-overlay.active { display: flex; }
        .checkout-loading-spinner {
            width: 40px; height: 40px;
            border: 3px solid var(--border-strong);
            border-top-color: var(--wine);
            border-radius: 50%;
            animation: ck-spin 0.7s linear infinite;
        }
        .checkout-loading-text {
            font-family: var(--font-body);
            font-size: 0.9rem;
            color: var(--text-secondary);
            font-weight: 500;
        }
        @keyframes ck-spin { to { transform: rotate(360deg); } }
    </style>
@stop

@section('content')

<div class="manus-checkout">
    {{-- ── PROGRESS BAR ─────────────────────────────── --}}
    <div class="checkout-progress">
        <div class="checkout-progress__inner">
            <div class="checkout-step done">
                <div class="checkout-step__number">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14"><path d="M20 6L9 17l-5-5"/></svg>
                </div>
                Velg pakke
            </div>
            <div class="checkout-step__divider"></div>
            <div class="checkout-step done">
                <div class="checkout-step__number">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14"><path d="M20 6L9 17l-5-5"/></svg>
                </div>
                Logg inn
            </div>
            <div class="checkout-step__divider"></div>
            <div class="checkout-step done">
                <div class="checkout-step__number">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14"><path d="M20 6L9 17l-5-5"/></svg>
                </div>
                Bekreft bestilling
            </div>
            <div class="checkout-step__divider"></div>
            <div class="checkout-step active">
                <div class="checkout-step__number">4</div>
                Betaling
            </div>
        </div>
    </div>

    {{-- Loading overlay --}}
    <div class="checkout-loading-overlay" id="checkoutLoading">
        <div class="checkout-loading-spinner"></div>
        <div class="checkout-loading-text">Vennligst vent…</div>
    </div>

    <div class="checkout-page">
        {{-- ── LEFT: BETALINGSMETODER ─────────────────── --}}
        <div class="checkout-main">
            <a href="{{ route('front.shop-manuscript.checkout', $shopManuscript->id) }}" class="back-link">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5"><path d="M19 12H5M5 12l7 7M5 12l7-7"/></svg>
                Tilbake til bestilling
            </a>

            <h1 class="checkout-main__heading">Velg betalingsmetode</h1>
            <p class="checkout-main__sub">Velg hvordan du vil betale for manusutvikling.</p>

            @if(session('error'))
                <div class="checkout-alert">{{ session('error') }}</div>
            @endif

            {{-- ── VIPPS ── --}}
            <div class="payment-method">
                <div class="payment-method__header">
                    <div class="payment-method__icon payment-method__icon--vipps">
                        <svg width="28" height="10" viewBox="0 0 481 134" fill="#FF5B24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M88.9 2.4L56.5 97.7c-2.7 8-10.1 13.4-18.5 13.4h-.3c-8.4 0-15.8-5.3-18.5-13.4L2.2 44.3c-2-5.9 1.2-12.3 7.1-14.3 5.9-2 12.3 1.2 14.3 7.1l13.7 40.7L56 10.5c1.5-4.5 5.7-7.5 10.4-7.5h12.2c5.5 0 10 4.5 10 10v.1c.3-.2.3-.5.3-.7z"/>
                            <path d="M104.2 11.7c0-6.5 5.3-11.7 11.7-11.7 6.5 0 11.7 5.3 11.7 11.7 0 6.5-5.3 11.7-11.7 11.7-6.4 0-11.7-5.2-11.7-11.7zm2.1 26.2c0-5.3 4.3-9.6 9.6-9.6 5.3 0 9.6 4.3 9.6 9.6v62.3c0 5.3-4.3 9.6-9.6 9.6-5.3 0-9.6-4.3-9.6-9.6V37.9z"/>
                            <path d="M157.4 28.3c23 0 39.9 18.2 39.9 41.3 0 23-16.9 41.5-39.9 41.5-12.4 0-22.3-5.3-28.8-13.4v33.5c0 5.3-4.3 9.6-9.6 9.6s-9.6-4.3-9.6-9.6V37.9c0-5.3 4.3-9.6 9.6-9.6s9.6 4.3 9.6 9.6v3.8c6.5-8.2 16.4-13.4 28.8-13.4zm-3.7 63.6c13.4 0 23-10.6 23-22.3s-9.6-22.1-23-22.1-23 10.1-23 22.1 9.6 22.3 23 22.3z"/>
                            <path d="M230.3 28.3c23 0 39.9 18.2 39.9 41.3 0 23-16.9 41.5-39.9 41.5-12.4 0-22.3-5.3-28.8-13.4v33.5c0 5.3-4.3 9.6-9.6 9.6s-9.6-4.3-9.6-9.6V37.9c0-5.3 4.3-9.6 9.6-9.6s9.6 4.3 9.6 9.6v3.8c6.5-8.2 16.4-13.4 28.8-13.4zm-3.7 63.6c13.4 0 23-10.6 23-22.3s-9.6-22.1-23-22.1-23 10.1-23 22.1 9.6 22.3 23 22.3z"/>
                            <path d="M303 84.6c5.1 3.5 12.2 7.3 21.6 7.3 8 0 12.7-3.2 12.7-8 0-5.6-7-6.5-15.3-8-13.2-2.4-29.3-5.3-29.3-24.7 0-15.3 13.2-24 29-24 11.7 0 21.1 3.5 27.8 8.5 4.5 3.2 5.3 9.4 2.1 13.7-3.2 4.3-9.1 5.3-13.4 2.4-5.1-3.5-11.2-6-17.2-6-6.7 0-10.9 2.9-10.9 7 0 4.8 6.5 6 14.8 7.5 13.2 2.7 29.8 5.6 29.8 25.2 0 16.4-13 25.7-31.5 25.7-13.7 0-24.2-4.8-31.2-10.6-4-3.5-4.5-9.6-1-13.7 3.6-3.8 8.7-4.8 12.7-2.4l-.7.1z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="payment-method__title">Vipps</div>
                        <div class="payment-method__desc">Rask og enkel betaling med Vipps</div>
                    </div>
                </div>
                <div class="payment-method__body">
                    <form method="POST" action="{{ route('front.shop-manuscript.payment.vipps', ['id' => $shopManuscript->id, 'order_id' => $order->id]) }}">
                        @csrf
                        <button type="submit" class="pay-btn--vipps">
                            <svg width="60" height="17" viewBox="0 0 481 134" fill="#fff" xmlns="http://www.w3.org/2000/svg">
                                <path d="M88.9 2.4L56.5 97.7c-2.7 8-10.1 13.4-18.5 13.4h-.3c-8.4 0-15.8-5.3-18.5-13.4L2.2 44.3c-2-5.9 1.2-12.3 7.1-14.3 5.9-2 12.3 1.2 14.3 7.1l13.7 40.7L56 10.5c1.5-4.5 5.7-7.5 10.4-7.5h12.2c5.5 0 10 4.5 10 10v.1c.3-.2.3-.5.3-.7z"/>
                                <path d="M104.2 11.7c0-6.5 5.3-11.7 11.7-11.7 6.5 0 11.7 5.3 11.7 11.7 0 6.5-5.3 11.7-11.7 11.7-6.4 0-11.7-5.2-11.7-11.7zm2.1 26.2c0-5.3 4.3-9.6 9.6-9.6 5.3 0 9.6 4.3 9.6 9.6v62.3c0 5.3-4.3 9.6-9.6 9.6-5.3 0-9.6-4.3-9.6-9.6V37.9z"/>
                                <path d="M157.4 28.3c23 0 39.9 18.2 39.9 41.3 0 23-16.9 41.5-39.9 41.5-12.4 0-22.3-5.3-28.8-13.4v33.5c0 5.3-4.3 9.6-9.6 9.6s-9.6-4.3-9.6-9.6V37.9c0-5.3 4.3-9.6 9.6-9.6s9.6 4.3 9.6 9.6v3.8c6.5-8.2 16.4-13.4 28.8-13.4zm-3.7 63.6c13.4 0 23-10.6 23-22.3s-9.6-22.1-23-22.1-23 10.1-23 22.1 9.6 22.3 23 22.3z"/>
                                <path d="M230.3 28.3c23 0 39.9 18.2 39.9 41.3 0 23-16.9 41.5-39.9 41.5-12.4 0-22.3-5.3-28.8-13.4v33.5c0 5.3-4.3 9.6-9.6 9.6s-9.6-4.3-9.6-9.6V37.9c0-5.3 4.3-9.6 9.6-9.6s9.6 4.3 9.6 9.6v3.8c6.5-8.2 16.4-13.4 28.8-13.4zm-3.7 63.6c13.4 0 23-10.6 23-22.3s-9.6-22.1-23-22.1-23 10.1-23 22.1 9.6 22.3 23 22.3z"/>
                                <path d="M303 84.6c5.1 3.5 12.2 7.3 21.6 7.3 8 0 12.7-3.2 12.7-8 0-5.6-7-6.5-15.3-8-13.2-2.4-29.3-5.3-29.3-24.7 0-15.3 13.2-24 29-24 11.7 0 21.1 3.5 27.8 8.5 4.5 3.2 5.3 9.4 2.1 13.7-3.2 4.3-9.1 5.3-13.4 2.4-5.1-3.5-11.2-6-17.2-6-6.7 0-10.9 2.9-10.9 7 0 4.8 6.5 6 14.8 7.5 13.2 2.7 29.8 5.6 29.8 25.2 0 16.4-13 25.7-31.5 25.7-13.7 0-24.2-4.8-31.2-10.6-4-3.5-4.5-9.6-1-13.7 3.6-3.8 8.7-4.8 12.7-2.4l-.7.1z"/>
                            </svg>
                            Betal med Vipps
                        </button>
                    </form>
                </div>
            </div>

            {{-- ── KREDITTKORT (PayPal) ── --}}
            <div class="payment-method">
                <div class="payment-method__header">
                    <div class="payment-method__icon payment-method__icon--paypal">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0070ba" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                        </svg>
                    </div>
                    <div>
                        <div class="payment-method__title">Kredittkort</div>
                        <div class="payment-method__desc">Betal med Visa, Mastercard via PayPal</div>
                    </div>
                </div>
                <div class="payment-method__body">
                    <form method="POST" action="{{ route('front.shop-manuscript.payment.paypal', ['id' => $shopManuscript->id, 'order_id' => $order->id]) }}">
                        @csrf
                        <button type="submit" class="pay-btn--paypal">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                            </svg>
                            Betal med kort
                        </button>
                    </form>
                </div>
            </div>

            {{-- ── FAKTURA ── --}}
            <div class="payment-method">
                <div class="payment-method__header">
                    <div class="payment-method__icon payment-method__icon--faktura">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
                        </svg>
                    </div>
                    <div>
                        <div class="payment-method__title">Faktura</div>
                        <div class="payment-method__desc">Betaling via Forfatterskolen</div>
                    </div>
                </div>
                <div class="payment-method__body">
                    <form method="POST" action="{{ route('front.shop-manuscript.payment.faktura', ['id' => $shopManuscript->id, 'order_id' => $order->id]) }}" id="fakturaForm">
                        @csrf
                        <div class="installment-options">
                            <label class="installment-option selected" onclick="selectInstallment(this, 'full')">
                                <input type="radio" name="installment_plan" value="full" checked>
                                <span class="installment-option__label">Hele beløpet</span>
                                <span class="installment-option__detail">kr {{ number_format($totalPrice, 0, ',', ' ') }}</span>
                            </label>
                            <label class="installment-option" onclick="selectInstallment(this, '3months')">
                                <input type="radio" name="installment_plan" value="3months">
                                <span class="installment-option__label">3 måneder</span>
                                <span class="installment-option__detail">kr {{ number_format(round($totalPrice / 3), 0, ',', ' ') }} / mnd</span>
                            </label>
                        </div>
                        <button type="submit" class="pay-btn--faktura">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                            </svg>
                            Bestill med faktura
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── RIGHT: ORDRESAMMENDRAG ──────────────── --}}
        <div class="order-summary">
            <div class="order-summary__header">
                <div class="order-summary__title">Din bestilling</div>
            </div>
            <div class="order-summary__body">
                <div class="order-summary__product">
                    <div class="order-summary__product-name">{{ $shopManuscript->title }}</div>
                    <div class="order-summary__product-desc">Profesjonell manusutvikling med redaktør{{ $genreName ? ' · ' . $genreName : '' }}</div>
                </div>

                <div class="order-row">
                    <span class="order-row__label">Manusutvikling</span>
                    <span class="order-row__value">kr {{ number_format($basePrice - $coachingPrice, 0, ',', ' ') }}</span>
                </div>

                @if($coachingDuration > 0)
                <div class="order-row">
                    <span class="order-row__label">Coaching {{ $coachingDuration }} min (&ndash;10%)</span>
                    <span class="order-row__value">kr {{ number_format($coachingPrice, 0, ',', ' ') }}</span>
                </div>
                @endif

                @if($userHasPaidCourse)
                    <div class="order-row">
                        <span class="order-row__label">Mva</span>
                        <span class="order-row__value" style="color: var(--green);">Fritatt (aktiv elev)</span>
                    </div>
                @else
                    <div class="order-row">
                        <span class="order-row__label">Mva (25%)</span>
                        <span class="order-row__value">kr {{ number_format($mva, 0, ',', ' ') }}</span>
                    </div>
                @endif

                <div class="order-total">
                    <span class="order-total__label">Totalt</span>
                    <span class="order-total__price">kr {{ number_format($totalPrice, 0, ',', ' ') }}
                        @if(!$userHasPaidCourse)
                            <span>inkl. mva</span>
                        @endif
                    </span>
                </div>
                @if($userHasPaidCourse)
                    <div class="order-note">Elevpris &ndash; mva-fri (aktiv undervisning)</div>
                @endif

                <div class="order-secure">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Sikker betaling
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectInstallment(el, value) {
    document.querySelectorAll('.installment-option').forEach(function(opt) {
        opt.classList.remove('selected');
    });
    el.classList.add('selected');
}

// Loading overlay – vis umiddelbart ved klikk på betalingsknapper
(function() {
    var overlay = document.getElementById('checkoutLoading');
    if (!overlay) return;

    function showLoading() {
        overlay.classList.add('active');
    }

    // Alle betalingsknapper: Vipps, kort, faktura
    document.querySelectorAll('.pay-btn--vipps, .pay-btn--paypal, .pay-btn--faktura').forEach(function(btn) {
        btn.addEventListener('click', function() {
            showLoading();
        });
    });

    // Tilbake-lenke
    var backLink = document.querySelector('.back-link');
    if (backLink) {
        backLink.addEventListener('click', showLoading);
    }
})();
</script>

@stop
