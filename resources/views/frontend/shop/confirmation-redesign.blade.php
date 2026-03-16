@extends('frontend.layout')

@section('title')
    <title>Bestilling bekreftet – {{ $course->title }} – Forfatterskolen</title>
@stop

@section('styles')
@php
    use Carbon\Carbon;

    $user = auth()->user();
    $package = $order->package;
    $plan = $order->paymentPlan;
    $division = $plan ? (int)$plan->division : 1;

    // Norwegian month names
    $norMonths = [1=>'januar',2=>'februar',3=>'mars',4=>'april',5=>'mai',6=>'juni',
                  7=>'juli',8=>'august',9=>'september',10=>'oktober',11=>'november',12=>'desember'];

    // ── Course start date ──
    $startDate = $course->start_date ? Carbon::parse($course->start_date) : null;
    $startDay = $startDate ? $startDate->format('j') : '';
    $startMonthYear = $startDate ? ($norMonths[(int)$startDate->format('n')] ?? $startDate->format('F')) . ' ' . $startDate->format('Y') : '';
    $startDateFull = $startDate ? $startDay . '. ' . $startMonthYear : '';
    $courseStarted = $startDate ? $startDate->isPast() : false;

    // ── Price calculations ──
    $regularPrice = 0;
    $saleActive = false;
    $salePrice = 0;

    switch ($division) {
        case 1:
            $regularPrice = (int)($package->full_payment_price ?? 0);
            $saleActive = (bool)($package->full_payment_is_sale ?? false);
            $salePrice = (int)($package->full_payment_sale_price ?? 0);
            break;
        case 3:
            $regularPrice = (int)($package->months_3_price ?? 0);
            $saleActive = (bool)($package->months_3_is_sale ?? false);
            $salePrice = (int)($package->months_3_sale_price ?? 0);
            break;
        case 6:
            $regularPrice = (int)($package->months_6_price ?? 0);
            $saleActive = (bool)($package->months_6_is_sale ?? false);
            $salePrice = (int)($package->months_6_sale_price ?? 0);
            break;
        case 12:
            $regularPrice = (int)($package->months_12_price ?? 0);
            $saleActive = (bool)($package->months_12_is_sale ?? false);
            $salePrice = (int)($package->months_12_sale_price ?? 0);
            break;
    }

    $saleDiscount = $saleActive ? ($regularPrice - $salePrice) : 0;
    $basePrice = $saleActive ? $salePrice : $regularPrice;

    // Earlybird label for course 121
    $isRomankurs = (int)$course->id === (int)config('courses.romankurs.id', 0);
    $isEarlybird = false;
    if ($isRomankurs) {
        $deadlineStr = config('courses.romankurs.earlybird_deadline');
        $isEarlybird = $deadlineStr && now()->isBefore(Carbon::parse($deadlineStr));
    }
    $saleLabel = ($isRomankurs && $saleActive) ? 'Earlybird-rabatt' : ($saleActive ? 'Kampanjerabatt' : '');

    // Coupon from session flash
    $couponCode = session('cf_coupon_code');
    $couponDiscount = (int)session('cf_coupon_discount', 0);

    $totalPrice = $basePrice - $couponDiscount;
    if ($totalPrice < 0) $totalPrice = 0;

    // Per-rate for installments
    $rateAmount = $division > 1 ? (int)ceil($totalPrice / $division) : 0;

    // Payment mode label
    $paymentMode = $order->paymentMode;
    $paymentModeLabel = 'Faktura';
    if ($paymentMode) {
        if ($paymentMode->mode === 'Vipps') $paymentModeLabel = 'Vipps';
        elseif ($paymentMode->mode === 'Paypal') $paymentModeLabel = 'PayPal';
        elseif ($paymentMode->mode === 'Faktura') {
            $paymentModeLabel = $division > 1 ? "Rentefri delbetaling ({$division} mnd)" : 'Faktura';
        }
    } elseif ($division > 1) {
        $paymentModeLabel = "Rentefri delbetaling ({$division} mnd)";
    }

    // Package tier name — extract just the tier part (BASIC, STANDARD, etc.)
    $variationFull = $package->variation ?? '';
    $tierName = $variationFull;
    $dashPos = mb_strrpos($tierName, ' – ');
    if ($dashPos === false) $dashPos = mb_strrpos($tierName, ' - ');
    if ($dashPos !== false) {
        $tierName = trim(mb_substr($tierName, $dashPos + 3));
    }

    // Order display data
    $orderNumber = '#FS-' . Carbon::parse($order->created_at)->format('Y') . '-' . $order->id;
    $orderDate = Carbon::parse($order->created_at)->format('d.m.Y');

    // Due dates for installment plan
    $issueDate = $package->issue_date ? Carbon::parse($package->issue_date) : Carbon::today();
    $dueDates = [];
    if ($division > 1) {
        for ($i = 1; $i <= $division; $i++) {
            $dueDates[] = $issueDate->copy()->addMonths($i);
        }
    }

    // Confetti: only on first visit (session flash)
    $showConfetti = session('cf_show_confetti', false);

    // Share URL
    $shareUrl = route('front.course.show', $course->id);
    $shareTitle = $course->title . ' – Forfatterskolen';
@endphp
<style>
    /* Hide standard navbar and footer */
    .fs-nav, nav.fs-nav, header, .home-footer-new, .footer-newsletter, footer, .site-footer,
    section.footer-newsletter, .fixed_to_bottom_alert, .shop-manuscript-advisory { display: none !important; }
    body { background: #faf8f5 !important; }

    :root {
        --cf-wine: #862736;
        --cf-wine-hover: #9c2e40;
        --cf-wine-light: #f4e8ea;
        --cf-cream: #faf8f5;
        --cf-green: #2e7d32;
        --cf-green-bg: #e8f5e9;
        --cf-text-primary: #1a1a1a;
        --cf-text-secondary: #5a5550;
        --cf-text-muted: #8a8580;
        --cf-border: rgba(0, 0, 0, 0.08);
        --cf-font-display: 'Playfair Display', Georgia, serif;
        --cf-font-body: 'Source Sans 3', -apple-system, sans-serif;
        --cf-radius: 10px;
        --cf-radius-lg: 14px;
        --cf-max-width: 900px;
    }

    .cf-wrap { font-family: var(--cf-font-body); color: var(--cf-text-primary); -webkit-font-smoothing: antialiased; }
    .cf-wrap * { margin: 0; padding: 0; box-sizing: border-box; }

    /* ── NAV ───────────────────────────────────────────── */
    .cf-nav {
        background: #fff;
        border-bottom: 1px solid var(--cf-border);
        padding: 0.85rem 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .cf-nav__logo {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        text-decoration: none;
    }
    .cf-nav__logo span {
        font-family: var(--cf-font-display);
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        color: var(--cf-text-primary);
    }

    /* ── HERO ──────────────────────────────────────────── */
    .cf-hero {
        background: #fff;
        text-align: center;
        padding: 3.5rem 2rem 2.5rem;
    }
    .cf-hero__check {
        width: 64px; height: 64px;
        border-radius: 50%;
        background: var(--cf-green-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
    }
    .cf-hero__check svg { width: 32px; height: 32px; stroke: var(--cf-green); }
    .cf-hero__title {
        font-family: var(--cf-font-display);
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .cf-hero__sub {
        font-size: 1rem;
        color: var(--cf-text-secondary);
        max-width: 480px;
        margin: 0 auto;
        line-height: 1.6;
    }

    /* ── LAYOUT ────────────────────────────────────────── */
    .cf-layout {
        max-width: var(--cf-max-width);
        margin: 2rem auto;
        padding: 0 2rem;
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 2rem;
        align-items: start;
    }

    .cf-card {
        background: #fff;
        border: 1px solid var(--cf-border);
        border-radius: var(--cf-radius-lg);
        padding: 1.75rem;
        margin-bottom: 1.25rem;
    }
    .cf-card__heading {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    /* ── NEXT STEPS ─────────────────────────────────────── */
    .cf-step {
        display: flex;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid var(--cf-border);
    }
    .cf-step:last-child { border-bottom: none; }
    .cf-step__num {
        width: 32px; height: 32px;
        border-radius: 50%;
        background: var(--cf-wine-light);
        color: var(--cf-wine);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        flex-shrink: 0;
    }
    .cf-step__title { font-size: 0.9rem; font-weight: 600; margin-bottom: 0.15rem; }
    .cf-step__desc { font-size: 0.8rem; color: var(--cf-text-muted); line-height: 1.5; }

    /* ── SHARE ──────────────────────────────────────────── */
    .cf-share { text-align: center; }
    .cf-share__title { font-size: 0.85rem; font-weight: 600; margin-bottom: 0.75rem; }
    .cf-share-btns { display: flex; justify-content: center; gap: 0.65rem; }
    .cf-share-btn {
        width: 42px; height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--cf-border);
        background: #fff;
        cursor: pointer;
        transition: all 0.15s;
        text-decoration: none;
    }
    .cf-share-btn:hover { border-color: var(--cf-wine); background: var(--cf-wine-light); }
    .cf-share-btn svg { width: 18px; height: 18px; }

    /* ── TIPS ───────────────────────────────────────────── */
    .cf-tip__num {
        width: 32px; height: 32px;
        border-radius: 50%;
        background: var(--cf-green-bg);
        color: var(--cf-green);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* ── ACCESS CARD ──────────────────────────────────── */
    .cf-access {
        background: linear-gradient(135deg, #1c1917, #2a2520);
        border-radius: var(--cf-radius-lg);
        padding: 1.75rem;
        color: #fff;
        text-align: center;
        margin-bottom: 1.25rem;
    }
    .cf-access__badge {
        display: inline-block;
        font-size: 0.6rem;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #1c1917;
        background: #ffd54f;
        padding: 0.2rem 0.6rem;
        border-radius: 3px;
        margin-bottom: 0.75rem;
    }
    .cf-access__date {
        font-size: 2rem;
        font-weight: 700;
        font-family: var(--cf-font-display);
        margin-bottom: 0.1rem;
    }
    .cf-access__month {
        font-size: 0.85rem;
        color: rgba(255,255,255,0.5);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 1.25rem;
    }
    .cf-access__desc {
        font-size: 0.8rem;
        color: rgba(255,255,255,0.6);
        margin-bottom: 1.15rem;
    }
    .cf-access__btn {
        display: inline-block;
        padding: 0.65rem 1.5rem;
        background: var(--cf-wine);
        color: #fff;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.85rem;
        transition: background 0.15s;
    }
    .cf-access__btn:hover { background: var(--cf-wine-hover); color: #fff; }
    .cf-access__note {
        font-size: 0.68rem;
        color: rgba(255,255,255,0.4);
        margin-top: 0.75rem;
    }

    /* ── RECEIPT ──────────────────────────────────────── */
    .cf-receipt-row {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.4rem 0;
        font-size: 0.825rem;
        color: var(--cf-text-secondary);
    }
    .cf-receipt-row span:last-child { white-space: nowrap; flex-shrink: 0; }
    .cf-receipt-row--label { color: var(--cf-text-muted); font-size: 0.75rem; }
    .cf-receipt-row--discount { color: var(--cf-green); font-weight: 600; }
    .cf-receipt-row--total {
        border-top: 2px solid var(--cf-text-primary);
        margin-top: 0.35rem;
        padding-top: 0.65rem;
        font-weight: 700;
        color: var(--cf-text-primary);
        font-size: 0.9rem;
    }
    .cf-receipt-divider {
        border: none;
        border-top: 1px solid var(--cf-border);
        margin: 0.75rem 0;
    }

    /* ── PAYMENT PLAN ────────────────────────────────── */
    .cf-plan__item {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.4rem 0;
        font-size: 0.78rem;
        color: var(--cf-text-secondary);
    }
    .cf-plan__dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .cf-plan__dot--first { background: var(--cf-wine); }
    .cf-plan__dot--pending { background: var(--cf-border); border: 1.5px solid var(--cf-text-muted); }
    .cf-plan__date { min-width: 70px; color: var(--cf-text-muted); }

    /* ── BOTTOM CTA ──────────────────────────────────── */
    .cf-bottom {
        max-width: var(--cf-max-width);
        margin: 0 auto 3rem;
        padding: 0 2rem;
    }
    .cf-bottom__card {
        background: #fff;
        border: 1px solid var(--cf-border);
        border-radius: var(--cf-radius-lg);
        padding: 2rem;
        text-align: center;
    }
    .cf-bottom__title {
        font-family: var(--cf-font-display);
        font-size: 1.15rem;
        font-weight: 700;
        margin-bottom: 0.35rem;
    }
    .cf-bottom__desc {
        font-size: 0.875rem;
        color: var(--cf-text-secondary);
        margin-bottom: 1rem;
    }
    .cf-bottom__btn {
        display: inline-block;
        padding: 0.65rem 1.5rem;
        background: var(--cf-wine);
        color: #fff;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.875rem;
        transition: background 0.15s;
    }
    .cf-bottom__btn:hover { background: var(--cf-wine-hover); color: #fff; }

    /* ── CONFETTI ──────────────────────────────────────── */
    .cf-confetti-wrap {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        pointer-events: none;
        z-index: 100;
        overflow: hidden;
    }
    .cf-confetti {
        position: absolute;
        width: 8px; height: 8px;
        border-radius: 2px;
        opacity: 0;
        animation: cfConfettiFall 3s ease-out forwards;
    }
    @keyframes cfConfettiFall {
        0% { opacity: 1; transform: translateY(-20px) rotate(0deg); }
        100% { opacity: 0; transform: translateY(100vh) rotate(720deg); }
    }

    /* ── CONTACT ──────────────────────────────────────── */
    .cf-contact { text-align: center; }
    .cf-contact__title { font-size: 0.85rem; font-weight: 600; margin-bottom: 0.25rem; }
    .cf-contact__text { font-size: 0.78rem; color: var(--cf-text-muted); line-height: 1.5; }
    .cf-contact__text a { color: var(--cf-wine); text-decoration: none; }

    /* ── COPY TOAST ──────────────────────────────────── */
    .cf-toast {
        position: fixed;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%) translateY(20px);
        background: #1c1917;
        color: #fff;
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 600;
        opacity: 0;
        transition: all 0.3s;
        z-index: 200;
        pointer-events: none;
    }
    .cf-toast--show { opacity: 1; transform: translateX(-50%) translateY(0); }

    /* ── RESPONSIVE ────────────────────────────────────── */
    @media (max-width: 768px) {
        .cf-layout { grid-template-columns: 1fr; }
        .cf-hero__title { font-size: 1.5rem; }
        .cf-hero { padding: 2.5rem 1.25rem 2rem; }
        .cf-layout { padding: 0 1.25rem; }
        .cf-bottom { padding: 0 1.25rem; }
    }
</style>
@stop

@section('content')
<div class="cf-wrap">

{{-- ═══════════ CONFETTI ═══════════ --}}
@if($showConfetti)
<div class="cf-confetti-wrap" id="cfConfetti"></div>
@endif

{{-- ═══════════ NAV ═══════════ --}}
<nav class="cf-nav">
    <a href="/" class="cf-nav__logo">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="27" viewBox="0 0 43 41" fill="none" style="flex-shrink:0">
            <path d="M0 0L21.5 2.90538V41L0 36.6077V0Z" fill="#E73946"/>
            <path d="M43 0L21.5 2.90612V40.9983L43 36.3185V0Z" fill="#852636"/>
        </svg>
        <span>FORFATTERSKOLEN</span>
    </a>
</nav>

{{-- ═══════════ HERO ═══════════ --}}
<div class="cf-hero">
    <div class="cf-hero__check">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <h1 class="cf-hero__title">Velkommen til {{ $course->title }}, {{ $user->first_name ?? 'der' }}!</h1>
    <p class="cf-hero__sub">
        Bestillingen din er bekreftet. Du får en bekreftelse på e-post med alle detaljer.
    </p>
</div>

{{-- ═══════════ MAIN LAYOUT ═══════════ --}}
<div class="cf-layout">

    {{-- ── LEFT COLUMN ──────────────────────────────── --}}
    <div>
        {{-- Hva skjer nå --}}
        <div class="cf-card">
            <div class="cf-card__heading">Hva skjer nå?</div>
            <div>
                <div class="cf-step">
                    <div class="cf-step__num">1</div>
                    <div>
                        <div class="cf-step__title">Bekreftelse på e-post</div>
                        <div class="cf-step__desc">Du mottar en ordrebekreftelse og kvittering på {{ $user->email }} innen noen minutter.</div>
                    </div>
                </div>
                <div class="cf-step">
                    <div class="cf-step__num">2</div>
                    <div>
                        <div class="cf-step__title">Bli med i gruppepraten</div>
                        <div class="cf-step__desc">Du får tilgang til gruppepraten i portalen der du møter medstudenter og kurslærerne. Her støtter vi hverandre!</div>
                    </div>
                </div>
                <div class="cf-step">
                    <div class="cf-step__num">3</div>
                    <div>
                        @if($startDate && !$courseStarted)
                            <div class="cf-step__title">Kurset åpner {{ $startDateFull }}</div>
                            <div class="cf-step__desc">Du får e-post med innloggingsdetaljer før kursstart. Første modul og webinar er klar fra dag én.</div>
                        @elseif($courseStarted)
                            <div class="cf-step__title">Kurset er allerede åpent</div>
                            <div class="cf-step__desc">Du kan gå rett inn i portalen og starte på kursinnholdet med en gang.</div>
                        @else
                            <div class="cf-step__title">Kurset åpner snart</div>
                            <div class="cf-step__desc">Du får e-post med innloggingsdetaljer før kursstart.</div>
                        @endif
                    </div>
                </div>
                <div class="cf-step">
                    <div class="cf-step__num">4</div>
                    <div>
                        <div class="cf-step__title">Mentormøter starter umiddelbart</div>
                        <div class="cf-step__desc">Du har allerede tilgang til ukentlige mentormøter mandager kl. 20:00, pluss hele arkivet med 100+ timer.</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Del med venner --}}
        <div class="cf-card cf-share">
            <div class="cf-share__title">Fortell en venn om Forfatterskolen</div>
            <div class="cf-share-btns">
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank" rel="noopener" class="cf-share-btn" title="Del på Facebook">
                    <svg viewBox="0 0 24 24" fill="#1877F2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                </a>
                <a href="fb-messenger://share?link={{ urlencode($shareUrl) }}" class="cf-share-btn" title="Del på Messenger">
                    <svg viewBox="0 0 24 24" fill="#0084FF"><path d="M12 2C6.48 2 2 6.04 2 11c0 2.83 1.41 5.35 3.61 7.01V22l3.68-2.02C10.2 20.3 11.08 20.5 12 20.5c5.52 0 10-3.54 10-8.5S17.52 2 12 2zm1.07 10.33l-2.54-2.73-4.97 2.73 5.47-5.83 2.6 2.73 4.91-2.73-5.47 5.83z"/></svg>
                </a>
                <a href="#" class="cf-share-btn" id="cfCopyLink" title="Kopier lenke" data-url="{{ $shareUrl }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#5a5550" stroke-width="2" stroke-linecap="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                </a>
                <a href="mailto:?subject={{ rawurlencode($shareTitle) }}&body={{ rawurlencode('Sjekk ut dette kurset: ' . $shareUrl) }}" class="cf-share-btn" title="Send e-post">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#5a5550" stroke-width="2" stroke-linecap="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </a>
            </div>
        </div>

        {{-- Tips før kursstart --}}
        <div class="cf-card">
            <div class="cf-card__heading">Tips før kursstart</div>
            <div>
                <div class="cf-step">
                    <div class="cf-tip__num">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:16px;height:16px;"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                    </div>
                    <div>
                        <div class="cf-step__title">Begynn å skrive</div>
                        <div class="cf-step__desc">Har du allerede en idé? Start å skrive ned tanker, scener, karakterskisser. Perfekt er fienden til ferdig!</div>
                    </div>
                </div>
                <div class="cf-step">
                    <div class="cf-tip__num">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:16px;height:16px;"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    </div>
                    <div>
                        <div class="cf-step__title">Les bevisst</div>
                        <div class="cf-step__desc">Legg merke til hvordan forfattere du liker bygger opp historier, bruker dialog og skaper spenning.</div>
                    </div>
                </div>
                <div class="cf-step">
                    <div class="cf-tip__num">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:16px;height:16px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div>
                        <div class="cf-step__title">Sett av tid</div>
                        <div class="cf-step__desc">Planlegg 4–6 timer i uken for kurs + egen skriving. Blokker det i kalenderen — det er en avtale med deg selv.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── RIGHT COLUMN ─────────────────────────────── --}}
    <div>
        {{-- Kursstart-kort --}}
        @if($startDate)
        <div class="cf-access">
            <span class="cf-access__badge">Kurset åpner</span>
            @if($courseStarted)
                <div class="cf-access__desc" style="margin-bottom:1.25rem;">Kurset er allerede i gang!</div>
                <a href="/learner/dashboard" class="cf-access__btn">Gå til kurset →</a>
            @else
                <div class="cf-access__date">{{ $startDay }}</div>
                <div class="cf-access__month">{{ $startMonthYear }}</div>
                <div class="cf-access__desc">Vi sender deg en påminnelse på e-post.</div>
            @endif
            <div class="cf-access__note">Du har tilgang til mentormøter og arkiv allerede nå.</div>
        </div>
        @endif

        {{-- Ordrekvittering --}}
        <div class="cf-card">
            <div class="cf-card__heading">Ordrekvittering</div>

            <div class="cf-receipt-row cf-receipt-row--label">
                <span>Ordrenummer</span>
                <span style="font-weight:600;color:var(--cf-text-primary);">{{ $orderNumber }}</span>
            </div>
            <div class="cf-receipt-row cf-receipt-row--label">
                <span>Dato</span>
                <span>{{ $orderDate }}</span>
            </div>

            <hr class="cf-receipt-divider">

            <div class="cf-receipt-row">
                <span>{{ $variationFull }}</span>
                <span>kr {{ number_format($regularPrice, 0, ',', ' ') }}</span>
            </div>

            @if($saleDiscount > 0)
            <div class="cf-receipt-row cf-receipt-row--discount">
                <span>{{ $saleLabel }}</span>
                <span>– kr {{ number_format($saleDiscount, 0, ',', ' ') }}</span>
            </div>
            @endif

            @if($couponCode && $couponDiscount > 0)
            <div class="cf-receipt-row cf-receipt-row--discount">
                <span>Rabattkode: {{ $couponCode }}</span>
                <span>– kr {{ number_format($couponDiscount, 0, ',', ' ') }}</span>
            </div>
            @endif

            <div class="cf-receipt-row cf-receipt-row--total">
                <span>Totalt</span>
                <span>kr {{ number_format($totalPrice, 0, ',', ' ') }}</span>
            </div>

            @if($division > 1 && count($dueDates) > 0)
            <hr class="cf-receipt-divider">
            <div style="margin-top:0.25rem;">
                <div style="font-size:0.75rem;font-weight:600;margin-bottom:0.5rem;">Betalingsplan (rentefritt)</div>
                <div>
                    @foreach($dueDates as $idx => $dueDate)
                    <div class="cf-plan__item">
                        <div class="cf-plan__dot {{ $idx === 0 ? 'cf-plan__dot--first' : 'cf-plan__dot--pending' }}"></div>
                        <span class="cf-plan__date">{{ $dueDate->format('d.m.Y') }}</span>
                        <span>Rate {{ $idx + 1 }} — kr {{ number_format($rateAmount, 0, ',', ' ') }}</span>
                    </div>
                    @endforeach
                </div>
                <div style="font-size:0.68rem;color:var(--cf-text-muted);margin-top:0.5rem;">
                    Faktura sendes per e-post. Betal i portalen med valgfri metode.
                </div>
            </div>
            @endif

            <hr class="cf-receipt-divider">

            <div class="cf-receipt-row cf-receipt-row--label">
                <span>E-post</span>
                <span>{{ $user->email }}</span>
            </div>
            <div class="cf-receipt-row cf-receipt-row--label">
                <span>Betalingsmetode</span>
                <span>{{ $paymentModeLabel }}</span>
            </div>

            <div style="text-align:center;margin-top:1rem;">
                <a href="{{ route('learner.invoice') }}" style="font-size:0.78rem;color:var(--cf-wine);text-decoration:none;font-weight:600;">Last ned kvittering (PDF) →</a>
            </div>
        </div>

        {{-- Kontakt --}}
        <div class="cf-card cf-contact">
            <div class="cf-contact__title">Spørsmål?</div>
            <div class="cf-contact__text">
                Send oss en e-post på <a href="mailto:post@forfatterskolen.no">post@forfatterskolen.no</a>
                <br>eller ring <a href="tel:+4741123555">411 23 555</a>.
            </div>
        </div>
    </div>
</div>

{{-- ═══════════ BOTTOM CTA ═══════════ --}}
<div class="cf-bottom">
    <div class="cf-bottom__card">
        <div class="cf-bottom__title">Kjenner du noen som også drømmer om å skrive?</div>
        <div class="cf-bottom__desc">Del Forfatterskolen med en venn — dere kan følge kurset sammen!</div>
        <a href="{{ $shareUrl }}" class="cf-bottom__btn">Del kurset →</a>
    </div>
</div>

{{-- Copy-toast --}}
<div class="cf-toast" id="cfToast">Lenke kopiert!</div>

</div>{{-- .cf-wrap --}}
@stop

@section('scripts')
<script>
    // ── Confetti ──
    @if($showConfetti)
    (function() {
        var container = document.getElementById('cfConfetti');
        if (!container) return;
        var colors = ['#862736', '#ffd54f', '#2e7d32', '#e8e4df', '#FF5B24'];
        for (var i = 0; i < 50; i++) {
            var piece = document.createElement('div');
            piece.className = 'cf-confetti';
            piece.style.left = Math.random() * 100 + '%';
            piece.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            piece.style.animationDelay = Math.random() * 2 + 's';
            piece.style.animationDuration = (2 + Math.random() * 2) + 's';
            piece.style.width = (4 + Math.random() * 8) + 'px';
            piece.style.height = (4 + Math.random() * 8) + 'px';
            container.appendChild(piece);
        }
        setTimeout(function() { container.remove(); }, 5000);
    })();
    @endif

    // ── Copy link ──
    (function() {
        var copyBtn = document.getElementById('cfCopyLink');
        var toast = document.getElementById('cfToast');
        if (!copyBtn) return;
        copyBtn.addEventListener('click', function(e) {
            e.preventDefault();
            var url = this.getAttribute('data-url');
            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(function() {
                    showToast();
                });
            } else {
                var ta = document.createElement('textarea');
                ta.value = url;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                showToast();
            }
        });
        function showToast() {
            toast.classList.add('cf-toast--show');
            setTimeout(function() { toast.classList.remove('cf-toast--show'); }, 2000);
        }
    })();
</script>
@stop
