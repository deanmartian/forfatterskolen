@extends('frontend.layout')

@section('title')
    <title>Bestill {{ $course->title }} – Forfatterskolen</title>
@stop

@section('styles')
@php
    use Carbon\Carbon;
    $isEarlybird = false;
    $earlybirdDiscount = 0;
    $earlybirdDeadline = null;

    if ((int)$course->id === config('courses.romankurs.id')) {
        $deadlineStr = config('courses.romankurs.earlybird_deadline');
        if ($deadlineStr) {
            $earlybirdDeadline = Carbon::parse($deadlineStr);
            $isEarlybird = now()->isBefore($earlybirdDeadline);
            $earlybirdDiscount = config('courses.romankurs.earlybird_discount', 5500);
        }
    }

    $selectedPackage = $packages->firstWhere('id', $package_id) ?? $packages->first();
    $maxFreeMonths = $course->installment_months_free ?? 6;

    $startDate = $course->start_date ? Carbon::parse($course->start_date) : null;
    $startDateFormatted = $startDate ? \App\Http\FrontendHelpers::convertMonthLanguage($startDate->format('j. F Y')) : '';
@endphp
<style>
    /* Hide standard navbar and footer */
    .fs-nav, nav.fs-nav, header, .home-footer-new, .footer-newsletter, footer, .site-footer,
    section.footer-newsletter, .fixed_to_bottom_alert, .shop-manuscript-advisory { display: none !important; }
    body { background: #faf8f5 !important; }

    :root {
        --co-wine: #862736;
        --co-wine-hover: #9c2e40;
        --co-cream: #faf8f5;
        --co-green: #2e7d32;
        --co-green-bg: #e8f5e9;
        --co-text-primary: #1a1a1a;
        --co-text-secondary: #5a5550;
        --co-text-muted: #8a8580;
        --co-border: rgba(0, 0, 0, 0.08);
        --co-border-strong: rgba(0, 0, 0, 0.12);
        --co-font-display: 'Playfair Display', Georgia, serif;
        --co-font-body: 'Source Sans 3', -apple-system, sans-serif;
        --co-radius: 10px;
        --co-radius-lg: 14px;
        --co-vipps: #FF5B24;
    }

    .co-wrap * { margin: 0; padding: 0; box-sizing: border-box; }
    .co-wrap { font-family: var(--co-font-body); color: var(--co-text-primary); -webkit-font-smoothing: antialiased; }

    /* ── NAV ─────────────────────────────────── */
    .checkout-nav { background: #fff; border-bottom: 1px solid var(--co-border); padding: 0.85rem 2rem; display: flex; align-items: center; justify-content: space-between; }
    .checkout-nav__logo { display: flex; align-items: center; gap: 0.5rem; text-decoration: none; }
    .checkout-nav__back { font-size: 0.825rem; color: var(--co-text-muted); text-decoration: none; display: flex; align-items: center; gap: 0.35rem; }
    .checkout-nav__back:hover { color: var(--co-wine); }
    .checkout-nav__secure { font-size: 0.72rem; color: var(--co-text-muted); display: flex; align-items: center; gap: 0.3rem; }
    .checkout-nav__secure svg { width: 14px; height: 14px; stroke: var(--co-green); }

    /* ── PROGRESS BAR ────────────────────────── */
    .co-progress { max-width: 480px; margin: 1.75rem auto 0; display: flex; align-items: center; justify-content: center; gap: 0; }
    .co-step { display: flex; align-items: center; gap: 0.4rem; font-size: 0.72rem; font-weight: 500; color: var(--co-text-muted); }
    .co-step--active { color: var(--co-wine); font-weight: 600; }
    .co-step--done { color: var(--co-green); }
    .co-step__dot { width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 700; border: 2px solid var(--co-border-strong); color: var(--co-text-muted); }
    .co-step--active .co-step__dot { border-color: var(--co-wine); background: var(--co-wine); color: #fff; }
    .co-step--done .co-step__dot { border-color: var(--co-green); background: var(--co-green-bg); color: var(--co-green); }
    .co-line { width: 3rem; height: 2px; background: var(--co-border); margin: 0 0.5rem; }
    .co-line--done { background: var(--co-green); }

    /* ── LAYOUT ───────────────────────────────── */
    .co-layout { max-width: 960px; margin: 2rem auto; padding: 0 2rem; display: grid; grid-template-columns: 1fr 380px; gap: 2rem; align-items: start; }

    /* ── PACKAGE SELECTOR ────────────────────── */
    .co-pkg-selector { background: #fff; border: 1px solid var(--co-border); border-radius: var(--co-radius-lg); padding: 1.75rem; margin-bottom: 1.5rem; }
    .co-pkg-selector__heading { font-size: 1rem; font-weight: 700; margin-bottom: 1rem; }

    .co-pkg { border: 2px solid var(--co-border); border-radius: var(--co-radius); padding: 1rem 1.25rem; margin-bottom: 0.65rem; cursor: pointer; display: flex; align-items: center; gap: 1rem; transition: border-color 0.15s; position: relative; }
    .co-pkg:hover { border-color: rgba(134,39,54,0.2); }
    .co-pkg--selected { border-color: var(--co-wine); background: rgba(134,39,54,0.02); }
    .co-pkg--popular::after { content: 'MEST VALGT'; position: absolute; top: -9px; right: 1rem; background: var(--co-wine); color: #fff; font-size: 0.55rem; font-weight: 700; letter-spacing: 0.5px; padding: 0.15rem 0.5rem; border-radius: 3px; }
    .co-pkg input[type="radio"] { width: 18px; height: 18px; accent-color: var(--co-wine); cursor: pointer; }
    .co-pkg__info { flex: 1; }
    .co-pkg__name { font-size: 0.95rem; font-weight: 700; }
    .co-pkg__desc { font-size: 0.75rem; color: var(--co-text-muted); margin-top: 0.1rem; }
    .co-pkg__price { text-align: right; flex-shrink: 0; }
    .co-pkg__earlybird { font-family: var(--co-font-display); font-size: 1.15rem; font-weight: 700; color: var(--co-wine); }
    .co-pkg__original { font-size: 0.7rem; color: var(--co-text-muted); text-decoration: line-through; }
    .co-pkg__save { display: inline-block; font-size: 0.58rem; font-weight: 600; color: var(--co-green); background: var(--co-green-bg); padding: 0.1rem 0.35rem; border-radius: 3px; margin-top: 0.15rem; }

    /* ── AUTH SECTION ─────────────────────────── */
    .co-auth { background: #fff; border: 1px solid var(--co-border); border-radius: var(--co-radius-lg); padding: 1.75rem; margin-bottom: 1.5rem; }
    .co-auth__heading { font-size: 1rem; font-weight: 700; margin-bottom: 0.35rem; }
    .co-auth__sub { font-size: 0.825rem; color: var(--co-text-muted); margin-bottom: 1.25rem; }
    .co-auth__loggedin { font-size: 0.9rem; color: var(--co-text-secondary); }
    .co-auth__loggedin strong { color: var(--co-text-primary); }

    .co-vipps-btn { width: 100%; padding: 0.85rem; background: var(--co-vipps); color: #fff; border: none; border-radius: 8px; font-family: var(--co-font-body); font-size: 0.9rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: filter 0.15s; margin-bottom: 1.25rem; }
    .co-vipps-btn:hover { filter: brightness(1.05); }

    .co-google-btn { flex: 1; padding: 0.75rem; background: #fff; border: 1.5px solid var(--co-border-strong); border-radius: 8px; font-family: var(--co-font-body); font-size: 0.825rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; color: var(--co-text-primary); transition: border-color 0.15s; text-decoration: none; }
    .co-google-btn:hover { border-color: #4285F4; color: var(--co-text-primary); }

    .co-divider { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.25rem; color: var(--co-text-muted); font-size: 0.75rem; }
    .co-divider::before, .co-divider::after { content: ''; flex: 1; height: 1px; background: var(--co-border); }

    .co-auth-tabs { display: flex; gap: 0; margin-bottom: 1.25rem; background: var(--co-cream); border-radius: 8px; padding: 3px; }
    .co-auth-tab { flex: 1; padding: 0.5rem; text-align: center; font-size: 0.825rem; font-weight: 500; color: var(--co-text-muted); background: transparent; border: none; border-radius: 6px; cursor: pointer; transition: all 0.15s; font-family: var(--co-font-body); }
    .co-auth-tab--active { background: #fff; color: var(--co-text-primary); font-weight: 600; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }

    .co-form-group { margin-bottom: 0.85rem; }
    .co-form-group label { display: block; font-size: 0.75rem; font-weight: 600; color: var(--co-text-primary); margin-bottom: 0.3rem; }
    .co-form-group input { width: 100%; padding: 0.6rem 0.85rem; border: 1px solid var(--co-border-strong); border-radius: 6px; font-family: var(--co-font-body); font-size: 0.85rem; outline: none; transition: border-color 0.15s; }
    .co-form-group input:focus { border-color: var(--co-wine); }
    .co-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }

    .co-btn-primary { width: 100%; padding: 0.7rem; background: var(--co-wine); color: #fff; border: none; border-radius: 8px; font-family: var(--co-font-body); font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: background 0.15s; display: flex; align-items: center; justify-content: center; }
    .co-btn-primary:hover { background: var(--co-wine-hover); }

    /* ── PAYMENT OPTIONS ─────────────────────── */
    .co-pay-option { border: 2px solid var(--co-border); border-radius: var(--co-radius); padding: 0.65rem 0.85rem; margin-bottom: 0.4rem; cursor: pointer; display: flex; align-items: center; gap: 0.75rem; transition: border-color 0.15s; }
    .co-pay-option--selected { border-color: var(--co-wine); }
    .co-pay-option input[type="radio"] { accent-color: var(--co-wine); }
    .co-pay-option__info { flex: 1; }
    .co-pay-option__name { font-size: 0.8rem; font-weight: 600; }
    .co-pay-option__desc { font-size: 0.72rem; color: var(--co-text-muted); }

    .co-installment-config { background: var(--co-cream); border-radius: 8px; padding: 0.75rem; margin-bottom: 0.4rem; display: none; }
    .co-installment-config label { font-size: 0.7rem; font-weight: 600; display: block; margin-bottom: 0.35rem; }
    .co-installment-config select { width: 100%; padding: 0.4rem; border: 1px solid var(--co-border-strong); border-radius: 6px; font-family: var(--co-font-body); font-size: 0.8rem; }
    .co-installment-config__result { font-size: 0.78rem; color: var(--co-text-primary); font-weight: 600; margin-top: 0.5rem; }

    /* ── ORDER SUMMARY ───────────────────────── */
    .co-summary { background: #fff; border: 1px solid var(--co-border); border-radius: var(--co-radius-lg); padding: 1.75rem; position: sticky; top: 2rem; }
    .co-summary__heading { font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem; }
    .co-summary__course { display: flex; gap: 0.85rem; padding-bottom: 1.25rem; border-bottom: 1px solid var(--co-border); margin-bottom: 1rem; }
    .co-summary__img { width: 64px; height: 64px; border-radius: 8px; flex-shrink: 0; overflow: hidden; background: linear-gradient(135deg, #e8e4df, #d4cec6); }
    .co-summary__img img { width: 100%; height: 100%; object-fit: cover; }
    .co-summary__name { font-size: 0.9rem; font-weight: 600; }
    .co-summary__package { font-size: 0.78rem; color: var(--co-text-muted); margin-top: 0.1rem; }
    .co-summary__date { font-size: 0.72rem; color: var(--co-text-muted); margin-top: 0.15rem; }

    .co-summary__includes { margin-bottom: 1.25rem; }
    .co-summary__include { display: flex; align-items: center; gap: 0.4rem; font-size: 0.78rem; color: var(--co-text-secondary); padding: 0.25rem 0; }
    .co-summary__include svg { width: 14px; height: 14px; stroke: var(--co-green); flex-shrink: 0; }

    .co-earlybird-bar { background: linear-gradient(135deg, #1c1917, #2a2520); border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between; color: #fff; }
    .co-eb-badge { font-size: 0.6rem; font-weight: 700; color: #1c1917; background: #ffd54f; padding: 0.15rem 0.45rem; border-radius: 3px; }
    .co-eb-timer { font-size: 0.72rem; color: rgba(255,255,255,0.6); }

    .co-price-row { display: flex; justify-content: space-between; align-items: center; padding: 0.4rem 0; font-size: 0.85rem; }
    .co-price-row--original { text-decoration: line-through; color: var(--co-text-muted); }
    .co-price-row--discount { color: var(--co-green); font-weight: 600; }
    .co-price-row--total { padding-top: 0.85rem; margin-top: 0.5rem; border-top: 2px solid var(--co-text-primary); font-size: 1.05rem; font-weight: 700; }

    .co-submit-btn { width: 100%; padding: 0.9rem; background: var(--co-wine); color: #fff; border: none; border-radius: 8px; font-family: var(--co-font-body); font-size: 0.95rem; font-weight: 700; cursor: pointer; margin-top: 1.25rem; transition: background 0.15s; }
    .co-submit-btn:hover { background: var(--co-wine-hover); }
    .co-submit-btn:disabled { opacity: 0.5; cursor: not-allowed; }

    .co-trust-row { display: flex; justify-content: center; gap: 1rem; margin-top: 1rem; font-size: 0.68rem; color: var(--co-text-muted); }
    .co-trust-item { display: flex; align-items: center; gap: 0.25rem; }
    .co-trust-item svg { width: 12px; height: 12px; stroke: var(--co-green); }

    .co-terms { font-size: 0.68rem; color: var(--co-text-muted); text-align: center; margin-top: 0.85rem; line-height: 1.5; }
    .co-terms a { color: var(--co-wine); text-decoration: none; }

    .co-error { background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1rem; }
    .co-error ul { margin: 0; padding-left: 1.2rem; }
    .co-error li { font-size: 0.8rem; color: #991b1b; }

    @media (max-width: 768px) {
        .co-layout { grid-template-columns: 1fr; }
        .co-summary { position: static; }
        .co-progress { padding: 0 1rem; }
        .co-form-row { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="co-wrap">

{{-- ═══════════ MINIMAL NAV ═══════════ --}}
<div class="checkout-nav">
    <a href="{{ route('front.course.show', $course->id) }}" class="checkout-nav__back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:16px;height:16px;"><polyline points="15 18 9 12 15 6"/></svg>
        Tilbake til kurset
    </a>
    <a href="/" class="checkout-nav__logo">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="27" viewBox="0 0 43 41" fill="none" style="flex-shrink:0">
            <path d="M0 0L21.5 2.90538V41L0 36.6077V0Z" fill="#E73946"/>
            <path d="M43 0L21.5 2.90612V40.9983L43 36.3185V0Z" fill="#852636"/>
        </svg>
        <span style="font-family:var(--co-font-display);font-size:0.85rem;font-weight:700;letter-spacing:0.06em;color:var(--co-text-primary);">FORFATTERSKOLEN</span>
    </a>
    <div class="checkout-nav__secure">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        Sikker betaling
    </div>
</div>

{{-- ═══════════ PROGRESS BAR ═══════════ --}}
<div class="co-progress">
    <div class="co-step co-step--done">
        <div class="co-step__dot">✓</div>
        Velg pakke
    </div>
    <div class="co-line co-line--done"></div>
    <div class="co-step co-step--active">
        <div class="co-step__dot">2</div>
        Bestill
    </div>
    <div class="co-line"></div>
    <div class="co-step">
        <div class="co-step__dot">3</div>
        Betaling
    </div>
</div>

{{-- ═══════════ MAIN LAYOUT ═══════════ --}}
<div class="co-layout">
    <div class="co-main">

        {{-- Feilmeldinger --}}
        @if($errors->any())
            <div class="co-error">
                <p style="font-weight:600;margin:0 0 4px;">Vennligst fyll ut alle feltene under for å bestille.</p>
                <p style="margin:0;font-size:0.78rem;">Har du allerede konto? Klikk «Logg inn» lenger ned.</p>
            </div>
        @endif

        {{-- ── 1. PACKAGE SELECTOR ──────────────────── --}}
        <div class="co-pkg-selector">
            <div class="co-pkg-selector__heading">Velg pakke</div>

            @foreach($packages as $package)
                @php
                    $price = $package->full_payment_price;
                    $salePrice = $package->calculatedPrice;
                    $hasSale = $salePrice && $salePrice < $price;
                    $displayPrice = $isEarlybird ? ($price - $earlybirdDiscount) : ($hasSale ? $salePrice : $price);
                    $tierName = preg_replace('/^' . preg_quote($course->title, '/') . '\s*[-–]\s*/i', '', $package->variation);
                    if ($tierName === $package->variation) {
                        $parts = preg_split('/\s*[-–]\s*/', $package->variation);
                        $tierName = count($parts) > 1 ? end($parts) : $package->variation;
                    }
                    $tierName = trim($tierName);
                    $desc = \Illuminate\Support\Str::limit(html_entity_decode(strip_tags($package->description)), 80);
                @endphp
                <label class="co-pkg {{ $package->id == $package_id ? 'co-pkg--selected' : '' }} {{ $package->is_standard ? 'co-pkg--popular' : '' }}"
                       onclick="selectPackage(this, {{ $package->id }}, {{ $displayPrice }}, {{ $price }}, '{{ addslashes($tierName) }}')">
                    <input type="radio" name="package_id" value="{{ $package->id }}" {{ $package->id == $package_id ? 'checked' : '' }}>
                    <div class="co-pkg__info">
                        <div class="co-pkg__name">{{ $tierName }}</div>
                        <div class="co-pkg__desc">{{ $desc }}</div>
                    </div>
                    <div class="co-pkg__price">
                        <div class="co-pkg__earlybird">kr {{ number_format($displayPrice, 0, ',', ' ') }}</div>
                        @if($isEarlybird || $hasSale)
                            <div class="co-pkg__original">kr {{ number_format($price, 0, ',', ' ') }}</div>
                            <span class="co-pkg__save">Spar {{ number_format($earlybirdDiscount, 0, ',', ' ') }}</span>
                        @endif
                    </div>
                </label>
            @endforeach
        </div>

        {{-- ── 2. ACCOUNT / AUTH ──────────────────────── --}}
        <div class="co-auth">
            @if(Auth::guest())
                <div class="co-auth__heading">Opprett konto eller logg inn</div>
                <div class="co-auth__sub">Du trenger en konto for å få tilgang til kurset.</div>

                {{-- Hurtiginnlogging: Vipps + Google --}}
                <div style="display: flex; gap: 0.65rem; margin-bottom: 1.25rem;">
                    <a href="{{ route('auth.login.vipps', 'checkout_state') }}" class="co-vipps-btn" style="flex: 1; margin-bottom: 0; text-decoration: none; font-size: 1rem;">
                        Logg inn med <span style="font-weight:800;letter-spacing:0.5px;">Vipps</span>
                    </a>
                    <a href="{{ route('auth.login.google') }}" class="co-google-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        Google
                    </a>
                </div>

                <div class="co-divider">eller med e-post</div>

                {{-- Auth tabs --}}
                <div class="co-auth-tabs">
                    <button class="co-auth-tab co-auth-tab--active" onclick="switchTab('register', this)">Ny konto</button>
                    <button class="co-auth-tab" onclick="switchTab('login', this)">Logg inn</button>
                </div>

                {{-- Register form --}}
                <div id="panel-register">
                    <div class="co-form-row">
                        <div class="co-form-group">
                            <label>Fornavn</label>
                            <input type="text" name="first_name" id="reg_first_name" placeholder="Ola" value="{{ old('first_name') }}" required>
                        </div>
                        <div class="co-form-group">
                            <label>Etternavn</label>
                            <input type="text" name="last_name" id="reg_last_name" placeholder="Nordmann" value="{{ old('last_name') }}" required>
                        </div>
                    </div>
                    <div class="co-form-group">
                        <label>E-post</label>
                        <input type="email" name="email" id="reg_email" placeholder="ola@eksempel.no" value="{{ old('email') }}" required>
                    </div>
                    <div class="co-form-group">
                        <label>Passord</label>
                        <input type="password" name="password" id="reg_password" placeholder="Minst 8 tegn" required>
                    </div>
                    <div class="co-form-group">
                        <label>Adresse</label>
                        <input type="text" name="street" id="reg_street" placeholder="Gateadresse" value="{{ old('street') }}" required>
                    </div>
                    <div class="co-form-row">
                        <div class="co-form-group">
                            <label>Postnummer</label>
                            <input type="text" name="zip" id="reg_zip" placeholder="0000" value="{{ old('zip') }}" required>
                        </div>
                        <div class="co-form-group">
                            <label>Poststed</label>
                            <input type="text" name="city" id="reg_city" placeholder="Oslo" value="{{ old('city') }}" required>
                        </div>
                    </div>
                    <div class="co-form-group">
                        <label>Telefon</label>
                        <input type="tel" name="phone" id="reg_phone" placeholder="Mobilnummer" value="{{ old('phone') }}" required>
                    </div>
                </div>

                {{-- Login form --}}
                <div id="panel-login" style="display: none;">
                    {{-- Magic link --}}
                    <div id="magicLinkSection" style="margin-bottom: 16px;">
                        <div class="co-form-group">
                            <label>E-post</label>
                            <input type="email" id="magicLinkEmail" placeholder="ola@eksempel.no" value="{{ old('email') }}">
                        </div>
                        <button type="button" onclick="sendMagicLink()" id="magicLinkBtn"
                            style="width:100%;padding:0.7rem;background:#f8f4f0;border:1.5px solid #862736;color:#862736;border-radius:8px;font-size:0.85rem;font-weight:600;cursor:pointer;margin-bottom:8px;transition:all 0.2s;">
                            ✉️ Send meg innloggingslenke
                        </button>
                        <div id="magicLinkMsg" style="display:none;font-size:0.8rem;text-align:center;padding:8px;border-radius:6px;margin-bottom:12px;"></div>
                    </div>

                    <div class="co-divider" style="margin: 12px 0;">eller med passord</div>

                    <form method="POST" action="{{ route('frontend.login.checkout.store') }}">
                        @csrf
                        <div class="co-form-group">
                            <label>E-post</label>
                            <input type="email" name="email" placeholder="ola@eksempel.no" value="{{ old('email') }}" required>
                        </div>
                        <div class="co-form-group">
                            <label>Passord</label>
                            <input type="password" name="password" required>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
                            <a href="{{ route('auth.login.show') }}?t=passwordreset" style="font-size: 0.75rem; color: var(--co-wine); text-decoration: none;">Glemt passord?</a>
                            <button type="submit" class="co-btn-primary" style="width: auto; padding: 0.5rem 1.5rem;">Logg inn</button>
                        </div>
                    </form>
                </div>
            @else
                <div class="co-auth__heading">Konto</div>
                <div class="co-auth__loggedin">
                    <svg viewBox="0 0 24 24" fill="none" stroke="var(--co-green)" stroke-width="2" stroke-linecap="round" style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-right:0.3rem;"><polyline points="20 6 9 17 4 12"/></svg>
                    Logget inn som <strong>{{ Auth::user()->name ?? Auth::user()->email }}</strong>
                </div>
            @endif
        </div>

    </div>

    {{-- ── ORDER SUMMARY (SIDEBAR) ──────────────── --}}
    <div>
        <div class="co-summary">
            <div class="co-summary__heading">Din bestilling</div>

            <div class="co-summary__course">
                <div class="co-summary__img">
                    @if($course->course_image)
                        <img src="{{ $course->course_image }}" alt="{{ $course->title }}">
                    @endif
                </div>
                <div>
                    <div class="co-summary__name">{{ $course->title }}</div>
                    @php
                        $selTier = preg_replace('/^' . preg_quote($course->title, '/') . '\s*[-–]\s*/i', '', $selectedPackage->variation);
                        if ($selTier === $selectedPackage->variation) {
                            $parts = preg_split('/\s*[-–]\s*/', $selectedPackage->variation);
                            $selTier = count($parts) > 1 ? end($parts) : $selectedPackage->variation;
                        }
                    @endphp
                    <div class="co-summary__package" id="summaryPackage">{{ trim($selTier) }}-pakke</div>
                    @if($startDateFormatted)
                        <div class="co-summary__date">Oppstart {{ $startDateFormatted }}</div>
                    @endif
                </div>
            </div>

            {{-- Includes --}}
            <div class="co-summary__includes" id="summaryIncludes">
                @php
                    $descLines = preg_split('/[\r\n]+/', strip_tags($selectedPackage->description ?? ''));
                    $checkSvg = '<svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>';
                @endphp
                @foreach($descLines as $line)
                    @php $line = trim(ltrim(trim($line), '-–•')); @endphp
                    @if(!empty($line))
                        <div class="co-summary__include">{!! $checkSvg !!}{{ $line }}</div>
                    @endif
                @endforeach
            </div>

            {{-- Earlybird bar --}}
            @if($isEarlybird && $earlybirdDeadline)
                <div class="co-earlybird-bar">
                    <div><span class="co-eb-badge">⚡ Earlybird</span></div>
                    <div class="co-eb-timer" id="summaryTimer"></div>
                </div>
            @endif

            {{-- Pricing --}}
            @php
                $selPrice = $selectedPackage->full_payment_price;
                $selSalePrice = $selectedPackage->calculatedPrice;
                $selHasSale = $selSalePrice && $selSalePrice < $selPrice;
                $selDisplayPrice = $isEarlybird ? ($selPrice - $earlybirdDiscount) : ($selHasSale ? $selSalePrice : $selPrice);
                $selDiscount = $selPrice - $selDisplayPrice;
            @endphp
            @if($isEarlybird || $selHasSale)
                <div class="co-price-row co-price-row--original">
                    <span>Ordinær pris</span>
                    <span id="priceOriginal">kr {{ number_format($selPrice, 0, ',', ' ') }}</span>
                </div>
                <div class="co-price-row co-price-row--discount">
                    <span>{{ $isEarlybird ? 'Earlybird-rabatt' : 'Tilbudsrabatt' }}</span>
                    <span>– kr {{ number_format($selDiscount, 0, ',', ' ') }}</span>
                </div>
            @endif

            {{-- Kupong-rad (skjult default) --}}
            <div id="couponRow" class="co-price-row co-price-row--discount" style="display: none;">
                <span id="couponLabel">Rabattkode</span>
                <span id="couponValue"></span>
            </div>

            {{-- Rabattkode-felt --}}
            <div style="margin: 0.65rem 0;">
                <div id="couponToggle">
                    <a href="#" onclick="showCouponField(event)" style="font-size: 0.75rem; color: var(--co-wine); text-decoration: none;">Har du en rabattkode?</a>
                </div>
                <div id="couponField" style="display: none;">
                    <div style="display: flex; gap: 0.4rem;" id="couponInputWrap">
                        <input type="text" id="couponInput" placeholder="Skriv inn kode" style="flex: 1; padding: 0.45rem 0.65rem; border: 1px solid var(--co-border-strong); border-radius: 6px; font-family: var(--co-font-body); font-size: 0.8rem; text-transform: uppercase;">
                        <button onclick="applyCoupon()" id="couponBtn" style="padding: 0.45rem 0.85rem; background: var(--co-wine); color: #fff; border: none; border-radius: 6px; font-family: var(--co-font-body); font-size: 0.75rem; font-weight: 600; cursor: pointer; white-space: nowrap;">Bruk</button>
                    </div>
                    <div id="couponMessage" style="font-size: 0.68rem; margin-top: 0.3rem; display: none;"></div>
                    <div id="couponApplied" style="display: none; font-size: 0.72rem; color: var(--co-green); margin-top: 0.3rem;">
                        ✓ <span id="couponAppliedCode"></span> brukt
                        <a href="#" onclick="removeCoupon(event)" style="color: var(--co-wine); margin-left: 0.5rem;">Fjern</a>
                    </div>
                </div>
            </div>

            <div class="co-price-row co-price-row--total">
                <span>Totalt</span>
                <span id="priceTotal">kr {{ number_format($selDisplayPrice, 0, ',', ' ') }}</span>
            </div>

            {{-- Betalingsmetode --}}
            <div style="margin-top: 1.25rem; padding-top: 1.25rem; border-top: 1px solid var(--co-border);">
                <div style="font-size: 0.78rem; font-weight: 700; margin-bottom: 0.65rem;">Velg betalingsmetode</div>

                {{-- Vipps --}}
                <label class="co-pay-option co-pay-option--selected" onclick="selectPayment(this, 'vipps')">
                    <input type="radio" name="payment_method" value="vipps" checked>
                    <div class="co-pay-option__info">
                        <div class="co-pay-option__name">Betal nå</div>
                    </div>
                    <span style="font-size:0.75rem;font-weight:700;color:#FF5B24;flex-shrink:0;">Vipps</span>
                </label>

                {{-- Faktura / Bestill nå, betal senere --}}
                <label class="co-pay-option" onclick="selectPayment(this, 'pay_later')">
                    <input type="radio" name="payment_method" value="pay_later">
                    <div class="co-pay-option__info">
                        <div class="co-pay-option__name">Faktura</div>
                        <div class="co-pay-option__desc">Bestill nå, betal senere. Faktura gjennom Forfatterskolen, opptil 6 mnd delbetaling uten ekstra kostnader.</div>
                    </div>
                </label>

                {{-- Rentefri delbetaling (midlertidig skjult pga WAF-blokkering) --}}
                {{--
                <label class="co-pay-option" onclick="selectPayment(this, 'rentefri')">
                    <input type="radio" name="payment_method" value="rentefri">
                    <div class="co-pay-option__info">
                        <div class="co-pay-option__name">Delbetal opptil {{ $maxFreeMonths }} mnd</div>
                        <div class="co-pay-option__desc">Rentefritt</div>
                    </div>
                    <span style="font-size: 0.55rem; font-weight: 700; color: var(--co-green); background: var(--co-green-bg); padding: 0.1rem 0.35rem; border-radius: 3px;">0%</span>
                </label>

                <div id="rentefriConfig" class="co-installment-config">
                    <label>Fordel over</label>
                    <select id="rentefriMonths" onchange="updateRentefri()">
                        @for($i = 1; $i <= $maxFreeMonths; $i++)
                            <option value="{{ $i }}" {{ $i == $maxFreeMonths ? 'selected' : '' }}>{{ $i }} mnd</option>
                        @endfor
                    </select>
                    <div class="co-installment-config__result" id="rentefriResult"></div>
                </div>
                --}}

                {{-- Svea Finans --}}
                <label class="co-pay-option" onclick="selectPayment(this, 'svea')" style="margin-bottom: 0;">
                    <input type="radio" name="payment_method" value="svea">
                    <div class="co-pay-option__info">
                        <div class="co-pay-option__name">Delbetaling opptil 36 mnd</div>
                        <div class="co-pay-option__desc">Betal med Visa/Mastercard via Svea Finans</div>
                    </div>
                    <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
                        {{-- Visa official logo --}}
                        <svg width="40" height="26" viewBox="0 0 256 83" xmlns="http://www.w3.org/2000/svg">
                            <path d="M97.8 1.4L61.5 81.5H40.4L22.7 16.8C21.5 12.2 20.5 10.5 17 8.6 11.2 5.5 1.5 2.6 0 1.7l.5-2h33.6c4.3 0 8.1 2.8 9.1 7.7l8.3 44.2L72.7 1.4h21.1zm83.1 54c.1-21.2-29.3-22.4-29.1-31.8.1-2.9 2.8-5.9 8.8-6.7 3-.4 11.2-.7 20.5 3.5l3.7-17C181 1.6 176 0 169.7 0c-19.9 0-33.8 10.6-33.9 25.7-.2 11.2 10 17.4 17.6 21.2 7.8 3.8 10.4 6.3 10.4 9.7-.1 5.2-6.2 7.6-12 7.6-10.1.2-15.9-2.7-20.6-4.9l-3.6 17c4.7 2.1 13.3 4 22.2 4.1 21.1 0 34.9-10.4 35.1-26.6M226.4 1.4c-4.1 0-7.5 2.4-9 6L188.6 81.5h21.1l4.2-11.5h25.8l2.4 11.5H261L244.8 1.4h-18.4zm3 18.1l6.1 29.2h-16.7l10.6-29.2zM119 1.4l-16.6 80.1H82.6L99.2 1.4H119z" fill="#1A1F71"/>
                        </svg>
                        {{-- Mastercard official logo --}}
                        <svg width="40" height="26" viewBox="0 0 152 100" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="50" cy="50" r="50" fill="#EB001B"/>
                            <circle cx="102" cy="50" r="50" fill="#F79E1B"/>
                            <path d="M76 10.1c13.7 10.8 22.5 27.6 22.5 46.5S89.7 87.6 76 98.4C62.3 87.6 53.5 70.8 53.5 51.9S62.3 18.4 76 10.1z" fill="#FF5F00"/>
                        </svg>
                    </div>
                </label>

                <div id="sveaConfig" class="co-installment-config" style="display:none;"></div>
            </div>

            <button class="co-submit-btn" id="submitBtn" onclick="handleSubmit()">
                Betal kr {{ number_format($selDisplayPrice, 0, ',', ' ') }} med Vipps →
            </button>

            <div class="co-trust-row">
                <span class="co-trust-item"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>14 dagers angrefrist</span>
                <span class="co-trust-item"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>Sikker betaling</span>
            </div>

            <div class="co-terms">
                Ved å bestille godtar du våre <a href="{{ route('front.terms', 'course-terms') }}" target="_blank">vilkår og betingelser</a>.
            </div>
        </div>
    </div>
</div>

</div>
@stop

@section('scripts')
<script>
    // ── State ──────────────────────────────────
    var courseId = {{ $course->id }};
    var currentPrice = {{ $selDisplayPrice }};
    var currentOriginal = {{ $selPrice }};
    var couponDiscount = 0;
    var couponCode = '';
    var isEarlybird = {{ $isEarlybird ? 'true' : 'false' }};
    var earlybirdDiscount = {{ $earlybirdDiscount }};

    // Package data for JS
    var packageData = {};
    @foreach($packages as $package)
        @php
            $jprice = $package->full_payment_price;
            $jsalePrice = $package->calculatedPrice;
            $jhasSale = $jsalePrice && $jsalePrice < $jprice;
            $jdisplayPrice = $isEarlybird ? ($jprice - $earlybirdDiscount) : ($jhasSale ? $jsalePrice : $jprice);
        @endphp
        packageData[{{ $package->id }}] = {
            price: {{ $jprice }},
            displayPrice: {{ $jdisplayPrice }},
            name: '{{ addslashes(trim(preg_replace("/^" . preg_quote($course->title, "/") . "\s*[-–]\s*/i", "", $package->variation))) }}',
            desc: {!! json_encode(preg_split('/[\r\n]+/', strip_tags($package->description ?? ''))) !!}
        };
    @endforeach

    // ── Package selection ──────────────────────
    function selectPackage(el, id, price, original, name) {
        document.querySelectorAll('.co-pkg').forEach(function(o) { o.classList.remove('co-pkg--selected'); });
        el.classList.add('co-pkg--selected');
        currentPrice = price;
        currentOriginal = original;

        document.getElementById('summaryPackage').textContent = name + '-pakke';
        if (document.getElementById('priceOriginal')) {
            document.getElementById('priceOriginal').textContent = 'kr ' + original.toLocaleString('nb-NO');
        }

        updateTotalDisplay();
        updateRentefri();
        updateSvea();

        // Update includes
        var inc = document.getElementById('summaryIncludes');
        var checkSvg = '<svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>';
        var lines = packageData[id] ? packageData[id].desc : [];
        var html = '';
        lines.forEach(function(line) {
            line = line.replace(/^[\s\-–•]+/, '').trim();
            if (line) html += '<div class="co-summary__include">' + checkSvg + line + '</div>';
        });
        inc.innerHTML = html;
    }

    // ── Bring postnummer-oppslag ─────────────
    (function() {
        var zipInput = document.getElementById('reg_zip');
        var cityInput = document.getElementById('reg_city');
        if (!zipInput || !cityInput) return;

        zipInput.addEventListener('input', function() {
            var zip = this.value.trim();
            if (zip.length !== 4 || !/^\d{4}$/.test(zip)) { cityInput.value = ''; return; }

            fetch('https://api.bring.com/postal-code/api/v1/postal_codes/NO/search?q=' + zip, {
                headers: { 'Accept': 'application/json' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.postal_codes && data.postal_codes.length > 0) {
                    cityInput.value = data.postal_codes[0].city;
                }
            })
            .catch(function() {});
        });
    })();

    // ── Auth tabs ──────────────────────────────
    function switchTab(tab, btn) {
        document.querySelectorAll('.co-auth-tab').forEach(function(t) { t.classList.remove('co-auth-tab--active'); });
        btn.classList.add('co-auth-tab--active');
        var register = document.getElementById('panel-register');
        var login = document.getElementById('panel-login');
        if (register) register.style.display = tab === 'register' ? 'block' : 'none';
        if (login) login.style.display = tab === 'login' ? 'block' : 'none';
    }

    // ── Payment method ─────────────────────────
    function selectPayment(el, method) {
        document.querySelectorAll('.co-pay-option').forEach(function(o) { o.classList.remove('co-pay-option--selected'); });
        el.classList.add('co-pay-option--selected');

        // Eksplisitt sett radio-knappen
        var radio = el.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;

        document.getElementById('rentefriConfig').style.display = method === 'rentefri' ? 'block' : 'none';
        document.getElementById('sveaConfig').style.display = method === 'svea' ? 'block' : 'none';

        // Recalculate installment display values
        if (method === 'rentefri') updateRentefri();
        if (method === 'svea') updateSvea();

        updateButtonText(method);
    }

    function updateButtonText(method) {
        var btn = document.getElementById('submitBtn');
        var total = currentPrice - couponDiscount;
        if (total < 0) total = 0;

        if (!method) method = document.querySelector('input[name="payment_method"]:checked').value;

        if (method === 'vipps') {
            btn.textContent = 'Betal kr ' + total.toLocaleString('nb-NO') + ' med Vipps →';
        } else if (method === 'pay_later') {
            btn.textContent = 'Bestill nå — betal senere →';
        } else if (method === 'rentefri') {
            var m = parseInt(document.getElementById('rentefriMonths').value);
            btn.textContent = 'Bestill — kr ' + Math.ceil(total / m).toLocaleString('nb-NO') + '/mnd rentefritt';
        } else if (method === 'svea') {
            btn.textContent = 'Gå til Svea Finans →';
        }
    }

    // ── Rentefri ───────────────────────────────
    function updateRentefri() {
        var months = parseInt(document.getElementById('rentefriMonths').value);
        var total = currentPrice - couponDiscount;
        if (total < 0) total = 0;
        var monthly = Math.ceil(total / months);
        document.getElementById('rentefriResult').textContent = 'kr ' + monthly.toLocaleString('nb-NO') + ' / mnd · rentefritt';
        var checked = document.querySelector('input[name="payment_method"]:checked');
        if (checked && checked.value === 'rentefri') updateButtonText('rentefri');
    }

    // ── Svea ───────────────────────────────────
    function updateSvea() {
        var el = document.getElementById('sveaMonths');
        if (!el) return;
        var months = parseInt(el.value);
        var total = currentPrice - couponDiscount;
        if (total < 0) total = 0;
        var monthly = Math.ceil(total / months);
        document.getElementById('sveaResult').textContent = 'ca. kr ' + monthly.toLocaleString('nb-NO') + ' / mnd';
        var checked = document.querySelector('input[name="payment_method"]:checked');
        if (checked && checked.value === 'svea') updateButtonText('svea');
    }

    // ── Total display ──────────────────────────
    function updateTotalDisplay() {
        var total = currentPrice - couponDiscount;
        if (total < 0) total = 0;
        document.getElementById('priceTotal').textContent = 'kr ' + total.toLocaleString('nb-NO');
        var method = document.querySelector('input[name="payment_method"]:checked');
        if (method) updateButtonText(method.value);
    }

    // ── Coupon ─────────────────────────────────
    function showCouponField(e) {
        e.preventDefault();
        document.getElementById('couponToggle').style.display = 'none';
        document.getElementById('couponField').style.display = 'block';
        document.getElementById('couponInput').focus();
    }

    function applyCoupon() {
        var code = document.getElementById('couponInput').value.trim().toUpperCase();
        if (!code) return;
        var msg = document.getElementById('couponMessage');
        var btn = document.getElementById('couponBtn');
        btn.textContent = '...';

        var pkgId = document.querySelector('input[name="package_id"]:checked');

        // Bruk eksisterende kupongvalidering
        fetch('/course/' + courseId + '/check_discount?coupon=' + encodeURIComponent(code) + '&package_id=' + (pkgId ? pkgId.value : '') + '&payment_plan_id=1', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) {
            if (!r.ok) throw new Error('Invalid');
            return r.json();
        })
        .then(function(data) {
            btn.textContent = 'Bruk';
            couponCode = code;
            couponDiscount = data.discount || 0;

            document.getElementById('couponRow').style.display = 'flex';
            document.getElementById('couponLabel').textContent = 'Rabattkode: ' + code;
            document.getElementById('couponValue').textContent = '– kr ' + couponDiscount.toLocaleString('nb-NO');

            document.getElementById('couponInputWrap').style.display = 'none';
            document.getElementById('couponApplied').style.display = 'block';
            document.getElementById('couponAppliedCode').textContent = code;
            msg.style.display = 'none';

            updateTotalDisplay();
        })
        .catch(function() {
            btn.textContent = 'Bruk';
            msg.style.display = 'block';
            msg.style.color = '#c62828';
            msg.textContent = 'Ugyldig rabattkode';
        });
    }

    function removeCoupon(e) {
        e.preventDefault();
        couponCode = '';
        couponDiscount = 0;
        document.getElementById('couponRow').style.display = 'none';
        document.getElementById('couponInputWrap').style.display = 'flex';
        document.getElementById('couponApplied').style.display = 'none';
        document.getElementById('couponInput').value = '';
        document.getElementById('couponMessage').style.display = 'none';
        updateTotalDisplay();
    }

    // Enter i kupong-felt
    var couponInput = document.getElementById('couponInput');
    if (couponInput) {
        couponInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); applyCoupon(); }
        });
    }

    // ── Earlybird countdown ────────────────────
    @if($isEarlybird && $earlybirdDeadline)
    var deadline = new Date('{{ $earlybirdDeadline->toIso8601String() }}').getTime();
    function updateSummaryTimer() {
        var diff = deadline - new Date().getTime();
        var el = document.getElementById('summaryTimer');
        if (!el) return;
        if (diff <= 0) { el.textContent = 'Avsluttet'; return; }
        var d = Math.floor(diff / 86400000);
        var h = Math.floor((diff % 86400000) / 3600000);
        el.textContent = d + 'd ' + h + 't igjen';
    }
    updateSummaryTimer();
    setInterval(updateSummaryTimer, 60000);
    @endif

    // ── Submit handler ─────────────────────────
    function handleSubmit() {
      try {
        var btn = document.getElementById('submitBtn');
        var method = document.querySelector('input[name="payment_method"]:checked').value;
        var pkgId = document.querySelector('input[name="package_id"]:checked').value;

        @if(Auth::guest())
            // Validate & create user, then proceed
            var fields = {
                email: document.getElementById('reg_email') ? document.getElementById('reg_email').value : '',
                first_name: document.getElementById('reg_first_name') ? document.getElementById('reg_first_name').value : '',
                last_name: document.getElementById('reg_last_name') ? document.getElementById('reg_last_name').value : '',
                password: document.getElementById('reg_password') ? document.getElementById('reg_password').value : '',
                street: document.getElementById('reg_street') ? document.getElementById('reg_street').value : '-',
                zip: document.getElementById('reg_zip') ? document.getElementById('reg_zip').value : '0000',
                city: document.getElementById('reg_city') ? document.getElementById('reg_city').value : '-',
                phone: document.getElementById('reg_phone') ? document.getElementById('reg_phone').value : '-',
                terms: true,
                package_id: pkgId,
                payment_method: method,
                is_pay_later: method === 'pay_later' ? true : false,
                price: currentPrice - couponDiscount,
                coupon: couponCode,
                _token: '{{ csrf_token() }}'
            };

            btn.disabled = true;
            btn.textContent = 'Behandler...';

            fetch('{{ route("front.course.checkout.validate-form", $course->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(fields)
            })
            .then(function(r) {
                if (r.status === 422) {
                    return r.json().then(function(errors) {
                        var errorHtml = '<div class="co-error"><p style="font-weight:600;margin:0 0 4px;">Vennligst fyll ut alle feltene for å bestille.</p><p style="margin:0;font-size:0.78rem;">Har du allerede konto? Klikk «Logg inn» lenger ned.</p></div>';
                        var existing = document.querySelector('.co-error');
                        if (existing) existing.remove();
                        document.querySelector('.co-main').insertAdjacentHTML('afterbegin', errorHtml);
                        btn.disabled = false;
                        updateButtonText(method);
                        throw new Error('Validation failed');
                    });
                }
                return r.json();
            })
            .then(function(data) {
                if (data && data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else if (data && data.redirect_link) {
                    window.location.href = data.redirect_link;
                } else if (data && data.course_link) {
                    window.location.href = data.course_link;
                } else if (typeof data === 'string' && data.indexOf('<') !== -1) {
                    // Svea checkout snippet — vis i ny side
                    document.open();
                    document.write(data);
                    document.close();
                } else {
                    // Reload — user is now logged in
                    window.location.reload();
                }
            })
            .catch(function(e) {
                if (e.message !== 'Validation failed') {
                    btn.disabled = false;
                    updateButtonText(method);
                }
            });
        @else
            // User is already logged in — proceed based on payment method
            if (method === 'vipps') {
                proceedVipps(pkgId);
            } else if (method === 'pay_later') {
                btn.disabled = true;
                btn.textContent = 'Behandler bestilling...';
                var plTotal = currentPrice - couponDiscount;
                fetch('/course/{{ $course->id }}/checkout/validate-form', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
                    body: JSON.stringify({
                        email: '{{ e(Auth::user()->email ?? "") }}',
                        first_name: '{{ e(Auth::user()->first_name ?? "") }}',
                        last_name: '{{ e(Auth::user()->last_name ?? "") }}',
                        street: '{{ e(optional(optional(Auth::user())->address)->street ?? "-") }}',
                        zip: '{{ e(optional(optional(Auth::user())->address)->zip ?? "0000") }}',
                        city: '{{ e(optional(optional(Auth::user())->address)->city ?? "-") }}',
                        phone: '{{ e(optional(optional(Auth::user())->address)->phone ?? "-") }}',
                        terms: true, package_id: pkgId, payment_method: 'pay_later',
                        is_pay_later: true, price: plTotal, coupon: couponCode || ''
                    })
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.redirect_url) { window.location.href = data.redirect_url; }
                    else if (data.course_link) { window.location.href = data.course_link; }
                    else { alert('Feil: ' + JSON.stringify(data)); btn.disabled = false; updateButtonText('pay_later'); }
                })
                .catch(function(e) { alert('Nettverksfeil: ' + e.message); btn.disabled = false; updateButtonText('pay_later'); });
            } else if (method === 'rentefri') {
                // Redirect to payment page (delbetaling)
                var months = document.getElementById('rentefriMonths').value;
                window.location.href = '/course/' + courseId + '/payment?package=' + pkgId + '&months=' + months + (couponCode ? '&c=' + couponCode : '');
            } else if (method === 'svea') {
                proceedSvea(pkgId);
            }
        @endif
      } catch(e) { alert('Feil ved bestilling: ' + e.message); }
    }

    @if(!Auth::guest())
    function proceedVipps(pkgId) {
        var btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.textContent = 'Kobler til Vipps...';

        var total = currentPrice - couponDiscount;
        fetch('{{ route("front.course.checkout.vipps", $course->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                package_id: pkgId,
                payment_plan_id: 1,
                price: total,
                coupon: couponCode
            })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.redirect_link) {
                window.location.href = data.redirect_link;
            }
        })
        .catch(function() {
            btn.disabled = false;
            updateButtonText('vipps');
        });
    }

    function proceedSvea(pkgId) {
        // Redirect to Svea checkout
        window.location.href = '/course/' + courseId + '/checkout-svea?package=' + pkgId + (couponCode ? '&c=' + couponCode : '');
    }
    @endif

    // Init rentefri/svea display values (without changing button text)
    updateRentefri();
    updateSvea();
    // Set correct button text based on actually selected payment method
    var initMethod = document.querySelector('input[name="payment_method"]:checked');
    if (initMethod) updateButtonText(initMethod.value);

    // Pre-fill coupon from URL
    @if($coupon)
    document.getElementById('couponInput').value = '{{ $coupon }}';
    showCouponField(new Event('click'));
    setTimeout(applyCoupon, 500);
    @endif

    function sendMagicLink() {
        var email = document.getElementById('magicLinkEmail').value;
        var btn = document.getElementById('magicLinkBtn');
        var msg = document.getElementById('magicLinkMsg');

        if (!email) { msg.style.display = 'block'; msg.style.background = '#fde8e8'; msg.style.color = '#862736'; msg.textContent = 'Skriv inn e-postadressen din'; return; }

        btn.disabled = true;
        btn.textContent = 'Sender...';

        fetch('{{ route("auth.magic-link.send") }}', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
            body: JSON.stringify({ email: email })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            msg.style.display = 'block';
            msg.style.background = '#e8f5e9';
            msg.style.color = '#2e7d32';
            msg.textContent = 'Innloggingslenke sendt til ' + email + '. Sjekk innboksen din.';
            btn.textContent = 'Sendt ✓';
        })
        .catch(function() {
            msg.style.display = 'block';
            msg.style.background = '#fde8e8';
            msg.style.color = '#862736';
            msg.textContent = 'Kunne ikke sende lenke. Prøv igjen.';
            btn.disabled = false;
            btn.textContent = '✉️ Send meg innloggingslenke';
        });
    }
</script>
@stop
