@extends('frontend.layout')

@section('title')
    <title>Betaling – {{ $course->title }} – Forfatterskolen</title>
@stop

@section('styles')
@php
    use Carbon\Carbon;
    $startDate = $course->start_date ? Carbon::parse($course->start_date) : null;
    $startDateFormatted = $startDate ? \App\Http\FrontendHelpers::convertMonthLanguage($startDate->format('j. F Y')) : '';
    $firstInvoiceDate = $startDate ? $startDate->copy()->addMonth() : null;
    $firstInvoiceDateFormatted = $firstInvoiceDate ? \App\Http\FrontendHelpers::convertMonthLanguage($firstInvoiceDate->format('j. F Y')) : '';

    $tierName = preg_replace('/^' . preg_quote($course->title, '/') . '\s*[-–]\s*/i', '', $package->variation);
    if ($tierName === $package->variation) {
        $parts = preg_split('/\s*[-–]\s*/', $package->variation);
        $tierName = count($parts) > 1 ? end($parts) : $package->variation;
    }
    $tierName = trim($tierName);
@endphp
<style>
    /* Hide standard navbar and footer */
    .fs-nav, nav.fs-nav, header, .home-footer-new, .footer-newsletter, footer, .site-footer,
    section.footer-newsletter, .fixed_to_bottom_alert, .shop-manuscript-advisory { display: none !important; }
    body { background: #faf8f5 !important; }

    :root {
        --py-wine: #862736;
        --py-wine-hover: #9c2e40;
        --py-cream: #faf8f5;
        --py-green: #2e7d32;
        --py-green-bg: #e8f5e9;
        --py-text-primary: #1a1a1a;
        --py-text-secondary: #5a5550;
        --py-text-muted: #8a8580;
        --py-border: rgba(0, 0, 0, 0.08);
        --py-border-strong: rgba(0, 0, 0, 0.12);
        --py-font-display: 'Playfair Display', Georgia, serif;
        --py-font-body: 'Source Sans 3', -apple-system, sans-serif;
        --py-radius: 10px;
        --py-radius-lg: 14px;
    }

    .py-wrap * { margin: 0; padding: 0; box-sizing: border-box; }
    .py-wrap { font-family: var(--py-font-body); color: var(--py-text-primary); -webkit-font-smoothing: antialiased; }

    /* ── NAV ─────────────────────────────────── */
    .checkout-nav { background: #fff; border-bottom: 1px solid var(--py-border); padding: 0.85rem 2rem; display: flex; align-items: center; justify-content: space-between; }
    .checkout-nav__logo img { height: 28px; }
    .checkout-nav__back { font-size: 0.825rem; color: var(--py-text-muted); text-decoration: none; display: flex; align-items: center; gap: 0.35rem; }
    .checkout-nav__back:hover { color: var(--py-wine); }
    .checkout-nav__secure { font-size: 0.72rem; color: var(--py-text-muted); display: flex; align-items: center; gap: 0.3rem; }
    .checkout-nav__secure svg { width: 14px; height: 14px; stroke: var(--py-green); }

    /* ── PROGRESS ────────────────────────────── */
    .py-progress { max-width: 480px; margin: 1.75rem auto 0; display: flex; align-items: center; justify-content: center; }
    .py-step { display: flex; align-items: center; gap: 0.4rem; font-size: 0.72rem; font-weight: 500; color: var(--py-text-muted); }
    .py-step--active { color: var(--py-wine); font-weight: 600; }
    .py-step--done { color: var(--py-green); }
    .py-step__dot { width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 700; border: 2px solid var(--py-border-strong); color: var(--py-text-muted); }
    .py-step--active .py-step__dot { border-color: var(--py-wine); background: var(--py-wine); color: #fff; }
    .py-step--done .py-step__dot { border-color: var(--py-green); background: var(--py-green-bg); color: var(--py-green); }
    .py-line { width: 3rem; height: 2px; background: var(--py-border); margin: 0 0.5rem; }
    .py-line--done { background: var(--py-green); }

    /* ── LAYOUT ───────────────────────────────── */
    .py-layout { max-width: 860px; margin: 2rem auto; padding: 0 2rem; display: grid; grid-template-columns: 1fr 340px; gap: 2rem; align-items: start; }

    /* ── CARD ──────────────────────────────────── */
    .py-card { background: #fff; border: 1px solid var(--py-border); border-radius: var(--py-radius-lg); padding: 2rem; }
    .py-card__heading { font-size: 1.1rem; font-weight: 700; margin-bottom: 0.35rem; }
    .py-card__sub { font-size: 0.85rem; color: var(--py-text-muted); margin-bottom: 1.5rem; }

    /* ── PLAN SUMMARY ──────────────────────────── */
    .py-plan { background: var(--py-cream); border-radius: var(--py-radius); padding: 1.25rem; margin-bottom: 1.75rem; }
    .py-plan__header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; }
    .py-plan__title { font-size: 0.9rem; font-weight: 700; }
    .py-plan__badge { font-size: 0.6rem; font-weight: 700; color: var(--py-green); background: var(--py-green-bg); padding: 0.15rem 0.5rem; border-radius: 3px; }

    /* Month buttons */
    .py-month-btn { padding: 0.5rem 0.85rem; border: 1.5px solid var(--py-border-strong); border-radius: 6px; background: #fff; font-family: var(--py-font-body); font-size: 0.8rem; font-weight: 500; color: var(--py-text-secondary); cursor: pointer; transition: all 0.15s; }
    .py-month-btn:hover { border-color: var(--py-wine); color: var(--py-wine); }
    .py-month-btn.py-month-btn--active { border-color: var(--py-wine); background: var(--py-wine); color: #fff !important; }

    /* Timeline */
    .py-timeline { margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--py-border); }
    .py-timeline__title { font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: var(--py-text-muted); margin-bottom: 0.6rem; }
    .py-timeline__item { display: flex; align-items: center; gap: 0.65rem; padding: 0.4rem 0; font-size: 0.8rem; color: var(--py-text-secondary); }
    .py-timeline__dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .py-timeline__dot--future { background: var(--py-border-strong); }
    .py-timeline__dot--first { background: var(--py-wine); }
    .py-timeline__date { color: var(--py-text-muted); min-width: 65px; }

    /* ── FORM ──────────────────────────────────── */
    .py-form-heading { font-size: 0.9rem; font-weight: 700; margin-bottom: 0.25rem; }
    .py-form-sub { font-size: 0.78rem; color: var(--py-text-muted); margin-bottom: 1.25rem; }
    .py-form-group { margin-bottom: 0.85rem; }
    .py-form-group label { display: block; font-size: 0.75rem; font-weight: 600; color: var(--py-text-primary); margin-bottom: 0.3rem; }
    .py-form-group input { width: 100%; padding: 0.7rem 0.85rem; border: 1px solid var(--py-border-strong); border-radius: 6px; font-family: var(--py-font-body); font-size: 0.9rem; outline: none; transition: border-color 0.15s; }
    .py-form-group input:focus { border-color: var(--py-wine); }
    .py-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }

    /* Buyer tabs */
    .py-buyer-tabs { display: flex; gap: 0; margin-bottom: 1.25rem; background: var(--py-cream); border-radius: 8px; padding: 3px; }
    .py-buyer-tab { flex: 1; padding: 0.5rem; text-align: center; font-family: var(--py-font-body); font-size: 0.825rem; font-weight: 500; color: var(--py-text-muted); background: transparent; border: none; border-radius: 6px; cursor: pointer; transition: all 0.15s; }
    .py-buyer-tab--active { background: #fff; color: var(--py-text-primary); font-weight: 600; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }

    /* Submit */
    .py-submit { width: 100%; padding: 0.9rem; background: var(--py-wine); color: #fff; border: none; border-radius: 8px; font-family: var(--py-font-body); font-size: 0.95rem; font-weight: 700; cursor: pointer; transition: background 0.15s; margin-top: 0.5rem; }
    .py-submit:hover { background: var(--py-wine-hover); }
    .py-trust-row { display: flex; justify-content: center; gap: 1rem; margin-top: 0.85rem; font-size: 0.68rem; color: var(--py-text-muted); flex-wrap: wrap; }
    .py-trust-item { display: flex; align-items: center; gap: 0.25rem; }
    .py-trust-item svg { width: 12px; height: 12px; stroke: var(--py-green); }
    .py-terms { font-size: 0.68rem; color: var(--py-text-muted); text-align: center; margin-top: 0.85rem; line-height: 1.5; }
    .py-terms a { color: var(--py-wine); text-decoration: none; }

    /* ── SIDEBAR ───────────────────────────────── */
    .py-sidebar { background: #fff; border: 1px solid var(--py-border); border-radius: var(--py-radius-lg); padding: 1.5rem; }
    .py-sidebar__heading { font-size: 0.85rem; font-weight: 700; margin-bottom: 1rem; }
    .py-sidebar-course { display: flex; gap: 0.75rem; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--py-border); }
    .py-sidebar-course__img { width: 56px; height: 56px; border-radius: 8px; flex-shrink: 0; overflow: hidden; background: linear-gradient(135deg, #e8e4df, #d4cec6); }
    .py-sidebar-course__img img { width: 100%; height: 100%; object-fit: cover; }
    .py-sidebar-course__name { font-size: 0.85rem; font-weight: 600; }
    .py-sidebar-course__package { font-size: 0.72rem; color: var(--py-text-muted); }
    .py-sidebar-course__date { font-size: 0.68rem; color: var(--py-text-muted); }
    .py-sidebar-price { display: flex; justify-content: space-between; font-size: 0.8rem; padding: 0.25rem 0; color: var(--py-text-secondary); }
    .py-sidebar-price--total { border-top: 1px solid var(--py-border); margin-top: 0.35rem; padding-top: 0.5rem; font-weight: 700; color: var(--py-text-primary); }
    .py-sidebar-price--discount { color: var(--py-green); font-weight: 600; }

    /* Info box */
    .py-info-box { background: var(--py-green-bg); border-radius: 8px; padding: 1rem; margin-top: 1rem; display: flex; gap: 0.6rem; align-items: flex-start; }
    .py-info-box svg { width: 18px; height: 18px; stroke: var(--py-green); flex-shrink: 0; margin-top: 1px; }
    .py-info-box__text { font-size: 0.78rem; color: var(--py-text-secondary); line-height: 1.5; }
    .py-info-box__text strong { color: var(--py-text-primary); }

    /* FAQ mini */
    .py-faq { margin-top: 1.25rem; }
    .py-faq__item { padding: 0.5rem 0; border-bottom: 1px solid var(--py-border); }
    .py-faq__q { font-size: 0.78rem; font-weight: 600; cursor: pointer; display: flex; justify-content: space-between; align-items: center; }
    .py-faq__a { font-size: 0.72rem; color: var(--py-text-muted); line-height: 1.5; margin-top: 0.35rem; display: none; }
    .py-faq__item--open .py-faq__a { display: block; }

    .py-error { background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1rem; }
    .py-error ul { margin: 0; padding-left: 1.2rem; }
    .py-error li { font-size: 0.8rem; color: #991b1b; }

    @media (max-width: 768px) {
        .py-layout { grid-template-columns: 1fr; }
        .py-form-row { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="py-wrap">

{{-- ═══════════ NAV ═══════════ --}}
<div class="checkout-nav">
    <a href="{{ route('front.course.checkout', $course->id) }}?package={{ $package->id }}" class="checkout-nav__back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:16px;height:16px;"><polyline points="15 18 9 12 15 6"/></svg>
        Tilbake
    </a>
    <a href="/" class="checkout-nav__logo" style="display:flex;align-items:center;gap:0.5rem;text-decoration:none;">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="27" viewBox="0 0 43 41" fill="none" style="flex-shrink:0">
            <path d="M0 0L21.5 2.90538V41L0 36.6077V0Z" fill="#E73946"/>
            <path d="M43 0L21.5 2.90612V40.9983L43 36.3185V0Z" fill="#852636"/>
        </svg>
        <span style="font-family:var(--py-font-display);font-size:0.85rem;font-weight:700;letter-spacing:0.06em;color:var(--py-text-primary);">FORFATTERSKOLEN</span>
    </a>
    <div class="checkout-nav__secure">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        Sikker betaling
    </div>
</div>

{{-- ═══════════ PROGRESS ═══════════ --}}
<div class="py-progress">
    <div class="py-step py-step--done"><div class="py-step__dot">✓</div>Velg pakke</div>
    <div class="py-line py-line--done"></div>
    <div class="py-step py-step--done"><div class="py-step__dot">✓</div>Bestill</div>
    <div class="py-line py-line--done"></div>
    <div class="py-step py-step--active"><div class="py-step__dot">3</div>Betaling</div>
</div>

{{-- ═══════════ LAYOUT ═══════════ --}}
<form method="POST" action="/course/{{ $course->id }}/checkout/complete" id="paymentForm">
    @csrf
    <input type="hidden" name="package_id" value="{{ $package->id }}">
    <input type="hidden" name="payment_mode_id" value="{{ \App\PaymentMode::where('mode', 'Faktura')->first()->id ?? 3 }}">
    <input type="hidden" name="payment_plan_id" id="hiddenPlanId" value="1">
    <input type="hidden" name="months" id="hiddenMonths" value="1">
    <input type="hidden" name="down_payment" id="hiddenDownPayment" value="0">
    <input type="hidden" name="buyer_type" id="hiddenBuyerType" value="private">
    @if($coupon)
        <input type="hidden" name="coupon" value="{{ $coupon }}">
    @endif

    <div class="py-layout">

        {{-- ── LEFT: PAYMENT FORM ─────────────────── --}}
        <div class="py-card">
            <div class="py-card__heading">Sett opp betalingsplan</div>
            <div class="py-card__sub">
                @if($startDateFormatted && $firstInvoiceDateFormatted)
                    Kurset åpner {{ $startDateFormatted }}. Første faktura sendes {{ $firstInvoiceDateFormatted }}.
                @else
                    Velg hvor mange måneder du vil fordele betalingen over.
                @endif
            </div>

            @if($errors->any())
                <div class="py-error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{!! $error !!}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Betalingsplan --}}
            <div class="py-plan">
                <div class="py-plan__header">
                    <span class="py-plan__title">Velg betalingsplan</span>
                    <span class="py-plan__badge">RENTEFRITT</span>
                </div>

                {{-- Forskudd --}}
                <div style="margin-bottom: 1.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 600; display: block; margin-bottom: 0.3rem;">Eventuelt forskudd (valgfritt)</label>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="font-size: 0.85rem; color: var(--py-text-muted);">kr</span>
                        <input type="number" id="downPayment" value="0" min="0" step="100" style="width: 120px; padding: 0.55rem 0.75rem; border: 1px solid var(--py-border-strong); border-radius: 6px; font-family: var(--py-font-body); font-size: 0.85rem;" oninput="updatePlan()">
                        <span style="font-size: 0.72rem; color: var(--py-text-muted);">betales før kursstart</span>
                    </div>
                </div>

                {{-- Antall måneder --}}
                <label style="font-size: 0.75rem; font-weight: 600; display: block; margin-bottom: 0.5rem;">Fordel resten over</label>
                <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap;" id="monthButtons">
                    <button type="button" class="py-month-btn py-month-btn--active" onclick="setMonths(1)" data-months="1">Hele beløpet</button>
                    <button type="button" class="py-month-btn" onclick="setMonths(3)" data-months="3">3 mnd</button>
                    <button type="button" class="py-month-btn" onclick="setMonths(6)" data-months="6">6 mnd</button>
                    <div style="display: flex; align-items: center; gap: 0.35rem;">
                        <span style="font-size: 0.72rem; color: var(--py-text-muted);">eller</span>
                        <input type="number" id="customMonths" min="1" max="{{ $maxFreeMonths }}" placeholder="Antall" style="width: 70px; padding: 0.45rem 0.5rem; border: 1px solid var(--py-border-strong); border-radius: 6px; font-size: 0.8rem; text-align: center;" oninput="setCustomMonths(this.value)">
                    </div>
                </div>

                {{-- Beregnet månedspris --}}
                <div style="background: #fff; border: 1px solid var(--py-border); border-radius: 8px; padding: 1rem; text-align: center;">
                    <div style="font-size: 0.72rem; color: var(--py-text-muted); margin-bottom: 0.2rem;" id="planLabel">Én faktura på</div>
                    <div style="font-family: var(--py-font-display); font-size: 1.75rem; font-weight: 700; color: var(--py-wine);" id="monthlyAmount">kr {{ number_format($price, 0, ',', ' ') }}</div>
                    <div style="font-size: 0.72rem; color: var(--py-text-muted); margin-top: 0.15rem;">rentefritt · ingen gebyrer</div>
                </div>

                {{-- Betalingstidslinje --}}
                <div class="py-timeline">
                    <div class="py-timeline__title">Betalingsplan</div>
                    <div id="timelineItems"></div>
                </div>
            </div>

            {{-- Fakturaadresse --}}
            <div style="margin-top: 0.5rem;">
                <div class="py-form-heading">Fakturaadresse</div>
                <div class="py-form-sub">Fakturaen sendes til din e-post med betalingslenke. Du kan også betale i portalen.</div>

                {{-- Privat / Bedrift --}}
                <div class="py-buyer-tabs">
                    <button type="button" class="py-buyer-tab py-buyer-tab--active" onclick="setBuyerType('private', this)">Privat</button>
                    <button type="button" class="py-buyer-tab" onclick="setBuyerType('company', this)">Bedrift</button>
                </div>

                {{-- Bedrift-felter --}}
                <div id="companyFields" style="display: none;">
                    <div class="py-form-group">
                        <label>Firmanavn</label>
                        <input type="text" name="company_name" placeholder="Firma AS">
                    </div>
                    <div class="py-form-group">
                        <label>Org.nummer</label>
                        <input type="text" name="org_number" placeholder="999 999 999" maxlength="11">
                    </div>
                    <div class="py-form-group">
                        <label>Referanse / PO-nummer <span style="font-weight: 400; color: var(--py-text-muted);">(valgfritt)</span></label>
                        <input type="text" name="po_number" placeholder="Internreferanse">
                    </div>
                </div>

                <div class="py-form-group">
                    <label>Fullt navn</label>
                    <input type="text" name="billing_name" placeholder="Fullt navn" value="{{ Auth::check() ? Auth::user()->first_name . ' ' . Auth::user()->last_name : '' }}" required>
                </div>
                <div class="py-form-group">
                    <label>Adresse</label>
                    <input type="text" name="street" placeholder="Storgata 1" value="{{ Auth::user()->address['street'] ?? '' }}" required>
                </div>
                <div class="py-form-row">
                    <div class="py-form-group">
                        <label>Postnummer</label>
                        <input type="text" name="zip" id="zipInput" placeholder="0123" maxlength="4" value="{{ Auth::user()->address['zip'] ?? '' }}" oninput="lookupZip(this.value)" required>
                    </div>
                    <div class="py-form-group">
                        <label>Sted</label>
                        <input type="text" name="city" id="cityInput" placeholder="Hentes automatisk" value="{{ Auth::user()->address['city'] ?? '' }}" readonly style="background: var(--py-cream); color: var(--py-text-secondary);">
                    </div>
                </div>
                <div class="py-form-group">
                    <label>E-post for faktura</label>
                    <input type="email" name="invoice_email" placeholder="ola@eksempel.no" value="{{ Auth::user()->email ?? '' }}" required>
                    <div style="font-size: 0.68rem; color: var(--py-text-muted); margin-top: 0.2rem;">Hit sender vi fakturaer med betalingslenke.</div>
                </div>
                <div class="py-form-group">
                    <label>Telefon <span style="font-weight: 400; color: var(--py-text-muted);">(valgfritt)</span></label>
                    <input type="tel" name="phone" placeholder="+47 XXX XX XXX" value="{{ Auth::user()->address['phone'] ?? '' }}">
                </div>

                {{-- EHF --}}
                <div id="ehfField" style="display: none;">
                    <div class="py-form-group">
                        <label>EHF-adresse <span style="font-weight: 400; color: var(--py-text-muted);">(valgfritt — for elektronisk faktura)</span></label>
                        <input type="text" name="ehf_address" placeholder="0192:999999999">
                        <div style="font-size: 0.68rem; color: var(--py-text-muted); margin-top: 0.2rem;">Oppgi EHF-adresse hvis dere ønsker elektronisk faktura.</div>
                    </div>
                </div>
            </div>

            <button type="submit" class="py-submit" id="submitBtn">
                Bekreft bestilling — kr {{ number_format($price, 0, ',', ' ') }} faktura
            </button>

            <div class="py-trust-row">
                <span class="py-trust-item"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>14 dagers angrefrist</span>
                <span class="py-trust-item"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>Ingen binding</span>
                <span class="py-trust-item"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>Betal med Vipps, kort eller bank</span>
            </div>

            <div class="py-terms">
                Ved å bekrefte godtar du <a href="{{ route('front.terms', 'course-terms') }}" target="_blank">vilkår for delbetaling</a>.<br>
                Fakturaer sendes per e-post med betalingslenke. Betal i portalen med valgfri metode. Ingen renter eller gebyrer.
            </div>
        </div>

        {{-- ── RIGHT: SIDEBAR ───────────────────── --}}
        <div>
            <div class="py-sidebar">
                <div class="py-sidebar__heading">Din bestilling</div>

                <div class="py-sidebar-course">
                    <div class="py-sidebar-course__img">
                        @if($course->course_image)
                            <img src="{{ $course->course_image }}" alt="{{ $course->title }}">
                        @endif
                    </div>
                    <div>
                        <div class="py-sidebar-course__name">{{ $course->title }}</div>
                        <div class="py-sidebar-course__package">{{ $tierName }}-pakke</div>
                        @if($startDateFormatted)
                            <div class="py-sidebar-course__date">Oppstart {{ $startDateFormatted }}</div>
                        @endif
                    </div>
                </div>

                @if($discount > 0)
                    <div class="py-sidebar-price">
                        <span>Ordinær pris</span>
                        <span style="text-decoration: line-through; color: var(--py-text-muted);">kr {{ number_format($package->full_payment_price, 0, ',', ' ') }}</span>
                    </div>
                    <div class="py-sidebar-price py-sidebar-price--discount">
                        <span>Earlybird-rabatt</span>
                        <span>– kr {{ number_format($discount, 0, ',', ' ') }}</span>
                    </div>
                @endif
                <div class="py-sidebar-price py-sidebar-price--total">
                    <span>Totalt</span>
                    <span>kr {{ number_format($price, 0, ',', ' ') }}</span>
                </div>

                <div class="py-info-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                    <div class="py-info-box__text">
                        <strong>Plassen din er sikret.</strong><br>
                        @if($startDateFormatted && $firstInvoiceDateFormatted)
                            Kurset åpner {{ $startDateFormatted }}. Første faktura sendes {{ $firstInvoiceDateFormatted }} — 30 dager etter kursstart.
                        @else
                            Første faktura sendes 30 dager etter kursstart.
                        @endif
                    </div>
                </div>
            </div>

            {{-- FAQ --}}
            <div class="py-sidebar" style="margin-top: 1rem;">
                <div class="py-sidebar__heading">Vanlige spørsmål</div>
                <div class="py-faq">
                    <div class="py-faq__item" onclick="this.classList.toggle('py-faq__item--open')">
                        <div class="py-faq__q">Når får jeg tilgang til kurset?<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:14px;height:14px;flex-shrink:0;"><polyline points="6 9 12 15 18 9"/></svg></div>
                        <div class="py-faq__a">Kurset åpner {{ $startDateFormatted ?: 'på oppstartsdato' }}. Du får en e-post med innloggingsdetaljer i god tid før kursstart.</div>
                    </div>
                    <div class="py-faq__item" onclick="this.classList.toggle('py-faq__item--open')">
                        <div class="py-faq__q">Hva skjer hvis jeg angrer?<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:14px;height:14px;flex-shrink:0;"><polyline points="6 9 12 15 18 9"/></svg></div>
                        <div class="py-faq__a">Du har 14 dagers angrefrist fra kursstart. Vi refunderer alt — ingen spørsmål stilt.</div>
                    </div>
                    <div class="py-faq__item" onclick="this.classList.toggle('py-faq__item--open')">
                        <div class="py-faq__q">Kan jeg betale alt med en gang?<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:14px;height:14px;flex-shrink:0;"><polyline points="6 9 12 15 18 9"/></svg></div>
                        <div class="py-faq__a">Ja! Gå <a href="{{ route('front.course.checkout', $course->id) }}?package={{ $package->id }}" style="color: var(--py-wine);">tilbake til bestilling</a> og velg "Betal nå med Vipps".</div>
                    </div>
                    <div class="py-faq__item" onclick="this.classList.toggle('py-faq__item--open')">
                        <div class="py-faq__q">Er det virkelig rentefritt?<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:14px;height:14px;flex-shrink:0;"><polyline points="6 9 12 15 18 9"/></svg></div>
                        <div class="py-faq__a">Ja, 100% rentefritt. Ingen renter, ingen gebyrer, ingen ekstra kostnader. Du betaler kun kursprisen fordelt over {{ $maxFreeMonths }} måneder.</div>
                    </div>
                    <div class="py-faq__item" onclick="this.classList.toggle('py-faq__item--open')">
                        <div class="py-faq__q">Hvordan betaler jeg fakturaene?<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:14px;height:14px;flex-shrink:0;"><polyline points="6 9 12 15 18 9"/></svg></div>
                        <div class="py-faq__a">Du får faktura på e-post med betalingslenke. Du kan også betale direkte i portalen med Vipps, kort eller bankoverføring — du velger selv.</div>
                    </div>
                    <div class="py-faq__item" onclick="this.classList.toggle('py-faq__item--open')">
                        <div class="py-faq__q">Kan firmaet mitt betale?<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:14px;height:14px;flex-shrink:0;"><polyline points="6 9 12 15 18 9"/></svg></div>
                        <div class="py-faq__a">Ja! Velg "Bedrift" i fakturaadresse-seksjonen. Oppgi firmanavn, org.nummer og eventuelt EHF-adresse for elektronisk faktura.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

</div>
@stop

@section('scripts')
<script>
    var totalPrice = {{ $price }};
    var selectedMonths = 1;
    var maxMonths = {{ $maxFreeMonths }};
    // Kursstart
    var courseStart = new Date('{{ $startDate ? $startDate->format("Y-m-d") : now()->addMonth()->format("Y-m-d") }}');

    var monthNames = ['januar', 'februar', 'mars', 'april', 'mai', 'juni', 'juli', 'august', 'september', 'oktober', 'november', 'desember'];

    function setMonths(m) {
        selectedMonths = m;
        document.getElementById('customMonths').value = '';
        document.querySelectorAll('.py-month-btn').forEach(function(b) { b.classList.remove('py-month-btn--active'); });
        var btn = document.querySelector('.py-month-btn[data-months="' + m + '"]');
        if (btn) btn.classList.add('py-month-btn--active');
        updatePlan();
    }

    function setCustomMonths(v) {
        var m = parseInt(v);
        if (!m || m < 1) m = 1;
        if (m > maxMonths) { m = maxMonths; document.getElementById('customMonths').value = maxMonths; }
        selectedMonths = m;
        document.querySelectorAll('.py-month-btn').forEach(function(b) { b.classList.remove('py-month-btn--active'); });
        updatePlan();
    }

    function updatePlan() {
        var down = parseInt(document.getElementById('downPayment').value) || 0;
        if (down < 0) down = 0;
        if (down >= totalPrice) down = totalPrice - 100;

        var remaining = totalPrice - down;
        var monthly = Math.ceil(remaining / selectedMonths);
        var lastPayment = remaining - (monthly * (selectedMonths - 1));

        // Update hidden fields
        document.getElementById('hiddenMonths').value = selectedMonths;
        document.getElementById('hiddenDownPayment').value = down;

        // Finn riktig payment_plan_id basert på months
        var planMap = {1: 1, 3: 2, 6: 3, 12: 4}; // Standard plan IDs
        document.getElementById('hiddenPlanId').value = planMap[selectedMonths] || 1;

        // Update display
        if (selectedMonths === 1) {
            document.getElementById('planLabel').textContent = 'Én faktura på';
            document.getElementById('monthlyAmount').textContent = 'kr ' + remaining.toLocaleString('nb-NO');
        } else {
            document.getElementById('planLabel').textContent = selectedMonths + ' månedlige fakturaer à';
            document.getElementById('monthlyAmount').textContent = 'kr ' + monthly.toLocaleString('nb-NO');
        }

        // Update button
        if (down > 0 && selectedMonths === 1) {
            document.getElementById('submitBtn').textContent = 'Bekreft — kr ' + down.toLocaleString('nb-NO') + ' nå + kr ' + remaining.toLocaleString('nb-NO') + ' faktura';
        } else if (selectedMonths === 1) {
            document.getElementById('submitBtn').textContent = 'Bekreft bestilling — kr ' + remaining.toLocaleString('nb-NO') + ' faktura';
        } else {
            document.getElementById('submitBtn').textContent = 'Bekreft bestilling — kr ' + monthly.toLocaleString('nb-NO') + '/mnd →';
        }

        // Timeline
        var html = '';
        if (down > 0) {
            html += '<div class="py-timeline__item">';
            html += '<div class="py-timeline__dot py-timeline__dot--first"></div>';
            html += '<span class="py-timeline__date">Før kursstart</span>';
            html += '<span>Forskudd — kr ' + down.toLocaleString('nb-NO') + '</span>';
            html += '</div>';
        }

        for (var i = 0; i < selectedMonths; i++) {
            var date = new Date(courseStart);
            date.setMonth(date.getMonth() + 1 + i);
            var amount = (i === selectedMonths - 1) ? lastPayment : monthly;
            var isFirst = (i === 0 && down === 0);
            var dateStr = date.getDate() + '. ' + monthNames[date.getMonth()] + ' ' + date.getFullYear();

            html += '<div class="py-timeline__item">';
            html += '<div class="py-timeline__dot ' + (isFirst ? 'py-timeline__dot--first' : 'py-timeline__dot--future') + '"></div>';
            html += '<span class="py-timeline__date">' + dateStr + '</span>';
            html += '<span>Rate ' + (i + 1) + ' — kr ' + amount.toLocaleString('nb-NO') + '</span>';
            html += '</div>';
        }

        document.getElementById('timelineItems').innerHTML = html;
    }

    // Init
    updatePlan();

    // ── Bring postal code lookup ──────────────
    var zipTimeout;
    function lookupZip(zip) {
        clearTimeout(zipTimeout);
        var cityInput = document.getElementById('cityInput');

        if (zip.length !== 4) {
            if (!cityInput.value) {
                cityInput.placeholder = 'Hentes automatisk';
            }
            return;
        }

        cityInput.value = '';
        cityInput.placeholder = 'Søker...';

        zipTimeout = setTimeout(function() {
            fetch('https://api.bring.com/shippingguide/api/postalCode.json?clientUrl=forfatterskolen.no&pnr=' + zip)
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.valid) {
                        cityInput.value = data.result;
                        cityInput.style.color = 'var(--py-text-primary)';
                    } else {
                        cityInput.value = '';
                        cityInput.placeholder = 'Ukjent postnummer';
                    }
                })
                .catch(function() {
                    cityInput.placeholder = 'Kunne ikke hente sted';
                    cityInput.removeAttribute('readonly');
                });
        }, 300);
    }

    // ── Private / Company toggle ──────────────
    function setBuyerType(type, btn) {
        document.querySelectorAll('.py-buyer-tab').forEach(function(t) { t.classList.remove('py-buyer-tab--active'); });
        btn.classList.add('py-buyer-tab--active');
        document.getElementById('companyFields').style.display = type === 'company' ? 'block' : 'none';
        document.getElementById('ehfField').style.display = type === 'company' ? 'block' : 'none';
        document.getElementById('hiddenBuyerType').value = type;
    }

    // Pre-fill zip lookup if value exists
    var existingZip = document.getElementById('zipInput').value;
    if (existingZip && existingZip.length === 4) {
        lookupZip(existingZip);
    }

    // Pre-select months from URL
    @if(request('months'))
        setMonths(parseInt('{{ request("months") }}') || 1);
    @endif

    function submitPayment() {
        var btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.textContent = 'Behandler...';
        var form = document.getElementById('paymentForm');
        var data = new URLSearchParams(new FormData(form));
        fetch(form.action, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'text/html'},
            body: data,
            redirect: 'manual'
        }).then(function(r) {
            if (r.type === 'opaqueredirect' || r.status === 302 || r.status === 301) {
                window.location.href = r.headers.get('Location') || '/course/{{ $course->id }}/thank-you';
            } else if (r.ok || r.status === 302) {
                window.location.href = '/course/{{ $course->id }}/thank-you';
            } else {
                return r.text().then(function(html) {
                    document.open(); document.write(html); document.close();
                });
            }
        }).catch(function(e) {
            alert('Feil: ' + e.message);
            btn.disabled = false;
            btn.textContent = 'Bekreft bestilling';
        });
    }
</script>
@stop
