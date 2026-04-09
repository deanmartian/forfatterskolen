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
       HERO
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
       SECTION TITLES
       ══════════════════════════════════════════════════════ */
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

    /* ══════════════════════════════════════════════════════
       KURSPLAN TIMELINE
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

    /* ══════════════════════════════════════════════════════
       PACKAGES
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

    .rk-package-card__features {
        padding: 0 1.5rem 1.5rem;
        font-size: 0.8rem;
        color: var(--text-secondary);
        line-height: 1.8;
    }

    .rk-package-card__features .checkmark {
        color: var(--green);
        margin-right: 0.25rem;
    }

    .rk-package-card__cta { padding: 0 1.5rem 1.5rem; }
    .rk-package-card__cta .rk-btn { width: 100%; justify-content: center; }

    /* ══════════════════════════════════════════════════════
       TESTIMONIALS
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

    .rk-testimonial-card__avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 0.5rem;
    }

    .rk-testimonial-card video {
        width: 100%;
        border-radius: var(--radius);
        margin-bottom: 0.75rem;
    }

    /* ══════════════════════════════════════════════════════
       RISK-FREE
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
       FINAL CTA
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
       FREE COURSE FORM
       ══════════════════════════════════════════════════════ */
    .rk-free-form { padding: 2.5rem 0; background: var(--cream); }
    .rk-free-form__inner { max-width: 500px; margin: 0 auto; }
    .rk-free-form input[type="text"],
    .rk-free-form input[type="email"] {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--border-strong);
        border-radius: var(--radius);
        font-family: var(--font-body);
        font-size: 0.9rem;
        margin-bottom: 0.75rem;
    }

    /* ══════════════════════════════════════════════════════
       WEBINAR CAROUSEL (kurs 17)
       ══════════════════════════════════════════════════════ */
    .rk-webinars { padding: 4rem 0; }

    .rk-webinar-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
    }

    .rk-webinar-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
    }

    .rk-webinar-card__thumb {
        height: 160px;
        background-size: cover;
        background-position: center 20%;
        background-color: #e8e4df;
    }

    .rk-webinar-card__body { padding: 1.25rem; }

    .rk-webinar-card__date {
        font-size: 0.72rem;
        color: var(--text-muted);
        margin-bottom: 0.35rem;
    }

    .rk-webinar-card__title {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .rk-webinar-card__desc {
        font-size: 0.825rem;
        color: var(--text-secondary);
        line-height: 1.6;
    }

    /* ══════════════════════════════════════════════════════
       RESPONSIVE
       ══════════════════════════════════════════════════════ */
    @media (max-width: 768px) {
        .rk-hero__inner { grid-template-columns: 1fr; }
        .rk-package-grid { grid-template-columns: 1fr; }
        .rk-package-card--popular { transform: none; }
        .rk-testimonial-grid { grid-template-columns: 1fr; }
        .rk-risk-grid { grid-template-columns: 1fr; }
        .rk-hero__trust { flex-wrap: wrap; gap: 0.75rem; }
        .rk-hero__title { font-size: 2rem; }
        .rk-webinar-grid { grid-template-columns: 1fr; }
    }
</style>
@stop

@section('content')
@php
    $packages = $course->packagesIsShow;
    $startDate = $course->start_date ? \Carbon\Carbon::parse($course->start_date) : null;
    $lessons = $course->lessons()->orderBy('order')->get();
    $testimonials = $course->testimonials;
    $cheapest = $packages->sortBy('calculated_price')->first();
    $shortDesc = \Illuminate\Support\Str::limit(html_entity_decode(strip_tags($course->description)), 200);

    // Auth state
    $courseTaken = null;
    if (Auth::check()) {
        $coursePackageIds = $course->allPackages->pluck('id')->toArray();
        $courseTaken = \App\CoursesTaken::where('user_id', Auth::user()->id)
            ->whereIn('package_id', $coursePackageIds)->first();
    }
@endphp

<div class="rk-page">

    {{-- ═══════════ HERO ═══════════ --}}
    <section class="rk-hero">
        <div class="rk-container">
            <div class="rk-hero__inner">
                <div>
                    @if($startDate)
                    <div class="rk-hero__eyebrow">
                        <span class="rk-hero__eyebrow-dot"></span>
                        Oppstart {{ $startDate->format('d') }}. {{ \App\Http\FrontendHelpers::convertMonthLanguage($startDate->format('n')) }}
                    </div>
                    @endif
                    <h1 class="rk-hero__title">{{ $course->title }}</h1>
                    @if($shortDesc)
                        <p class="rk-hero__desc">{{ $shortDesc }}</p>
                    @endif

                    {{-- CTA-knapper --}}
                    @if($courseTaken)
                        <div class="rk-hero__ctas">
                            <a href="{{ route('learner.course.show', ['id' => $courseTaken->id]) }}" class="rk-btn rk-btn--primary">
                                {{ trans('site.front.our-course.show.continue-course') }}
                            </a>
                        </div>
                    @elseif(!$course->is_free && !$course->hide_price && $packages->count())
                        <div class="rk-hero__ctas">
                            <a href="#pakker" class="rk-btn rk-btn--primary">
                                {{ $course->pay_later_with_application ? 'Se pakker og søk om plass ↓' : 'Se pakker og pris ↓' }}
                            </a>
                            @if($lessons->count())
                                <a href="#kursplan" class="rk-btn rk-btn--outline">Se kursplanen</a>
                            @endif
                        </div>
                    @elseif($course->is_free)
                        <div class="rk-hero__ctas">
                            <a href="#gratis" class="rk-btn rk-btn--primary">Få kurset gratis ↓</a>
                        </div>
                    @endif

                    @if(!$course->is_free)
                    <div class="rk-hero__trust">
                        <span class="rk-hero__trust-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                            14 dagers angrefrist
                        </span>
                        <span class="rk-hero__trust-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Avbetaling tilgjengelig
                        </span>
                    </div>
                    @endif
                </div>

                <div class="rk-hero__sidebar">
                    @if($course->course_image)
                    <div class="rk-hero__image">
                        <img src="{{ url($course->course_image) }}" alt="{{ $course->title }}">
                    </div>
                    @endif

                    @if($cheapest && !$course->is_free && !$course->hide_price)
                    <div class="rk-hero__quick-price">
                        <div class="rk-hero__quick-price-label">Pris fra</div>
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
                            Avbetaling tilgjengelig. Bestill nå, betal senere.
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════ GRATIS-KURS FORMULAR ═══════════ --}}
    @if($course->is_free)
    <section class="rk-free-form" id="gratis">
        <div class="rk-container">
            <h2 class="rk-section-title">Få kurset gratis</h2>
            <p class="rk-section-sub">Fyll inn skjemaet under for å få tilgang til kurset.</p>
            <div class="rk-free-form__inner">
                <form action="{{ route('front.course.getFreeCourse', $course->id) }}" method="POST" onsubmit="disableSubmit(this)">
                    {{ csrf_field() }}
                    @if(Auth::guest())
                        <input type="text" name="first_name" placeholder="{{ trans('site.front.form.first-name') }}"
                               value="{{ old('first_name') }}" required>
                        <input type="text" name="last_name" placeholder="{{ trans('site.front.form.last-name') }}"
                               value="{{ old('last_name') }}" required>
                        <input type="email" name="email" placeholder="{{ trans('site.front.form.email') }}"
                               value="{{ old('email') }}" required>
                        @if($course->status)
                            <button type="submit" class="rk-btn rk-btn--primary" style="width: 100%; justify-content: center;">
                                {{ trans('site.front.form.get-free-course') }}
                            </button>
                        @endif
                    @else
                        @if(!$courseTaken && $course->status == 1)
                            <button class="rk-btn rk-btn--primary" style="width: 100%; justify-content: center;" type="submit">
                                {{ trans('site.front.form.get-free-course') }}
                            </button>
                        @endif
                    @endif
                </form>

                @if(Session::has('email_exist'))
                    <div class="modal fade" role="dialog" id="emailExistModal">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="modal-title">{{ trans('site.front.our-course.email-exist.login') }}</h3>
                                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p class="font-weight-bold">{{ trans('site.front.our-course.email-exist.message') }}</p>
                                    <form id="checkoutLogin" action="{{ route('frontend.login.checkout.store') }}" method="POST">
                                        {{ csrf_field() }}
                                        <div class="input-group mb-4">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa at-icon"></i></span>
                                            </div>
                                            <input type="email" name="email" class="form-control no-border-left w-auto"
                                                   placeholder="{{ trans('site.front.form.email') }}" required value="{{ old('email') }}">
                                        </div>
                                        <div class="input-group mb-4">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa lock-icon"></i></span>
                                            </div>
                                            <input type="password" name="password"
                                                   placeholder="{{ trans('site.front.form.password') }}"
                                                   class="form-control no-border-left w-auto" required>
                                        </div>
                                        <button type="submit" class="btn site-btn-global float-end">
                                            {{ trans('site.front.our-course.email-exist.login-button-text') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div style="background: #fee; border: 1px solid #c00; border-radius: var(--radius); padding: 1rem; margin-top: 1rem;">
                        <ul style="margin: 0; padding-left: 1.25rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </section>
    @endif

    {{-- ═══════════ KURSPLAN ═══════════ --}}
    @if($course->id == 17)
        {{-- Webinar-pakke: spesialhåndtering --}}
        @php $webinars = $course->webinars()->active()->notReplay()->get(); @endphp
        @if($webinars->count())
        <section class="rk-webinars" id="kursplan">
            <div class="rk-container">
                <h2 class="rk-section-title">{{ trans('site.front.our-course.show.scheduled-webinars') }}</h2>
                <p class="rk-section-sub">{{ $webinars->count() }} kommende webinarer.</p>

                <div class="rk-webinar-grid">
                    @foreach($webinars as $webinar)
                        <div class="rk-webinar-card">
                            <div class="rk-webinar-card__thumb"
                                 style="background-image: url({{ $webinar->image ?: asset('/images/no_image.png') }})"></div>
                            <div class="rk-webinar-card__body">
                                <div class="rk-webinar-card__date">
                                    {{ \Carbon\Carbon::parse($webinar->start_date)->format('d.m.Y') }}
                                    kl. {{ \Carbon\Carbon::parse($webinar->start_date)->format('H:i') }}
                                </div>
                                <div class="rk-webinar-card__title">{{ $webinar->title }}</div>
                                <div class="rk-webinar-card__desc">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($webinar->description), 180) }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif
    @elseif($lessons->count())
        <section class="rk-kursplan" id="kursplan">
            <div class="rk-container">
                <h2 class="rk-section-title">Kursplan</h2>
                <p class="rk-section-sub">{{ $lessons->count() }} leksjoner i dette kurset.</p>

                <div class="rk-timeline">
                    @foreach($lessons as $index => $lesson)
                        @php
                            $lessonNum = $index + 1;
                            $lessonDate = ($lesson->delay && $startDate) ? $startDate->copy()->addDays((int) $lesson->delay) : null;
                        @endphp
                        <div class="rk-timeline-item">
                            <div class="rk-timeline-item__marker">
                                <div class="rk-timeline-item__week">
                                    <span class="rk-timeline-item__week-num">{{ $lessonNum }}</span>
                                    <span class="rk-timeline-item__week-label">Uke</span>
                                </div>
                            </div>
                            <div class="rk-timeline-item__content">
                                <div class="rk-timeline-item__title">{{ $lesson->title }}</div>
                                @if($lessonDate)
                                    <div class="rk-timeline-item__date">
                                        {{ $lessonDate->format('d') }}. {{ \App\Http\FrontendHelpers::convertMonthLanguage($lessonDate->format('n')) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ═══════════ PAKKER ═══════════ --}}
    @if(!$course->is_free && !$course->hide_price && $packages->count())
    <section class="rk-packages" id="pakker">
        <div class="rk-container">
            <h2 class="rk-section-title">Velg din pakke</h2>
            <p class="rk-section-sub">Velg pakken som passer best for deg.</p>

            <div class="rk-package-grid">
                @foreach($packages as $package)
                    @php
                        // Stripp kurstitel-prefix fra variation for å få kort tier-navn
                        $tierName = $package->variation;
                        $tierName = preg_replace('/^' . preg_quote($course->title, '/') . '\s*[\-–—]\s*/i', '', $tierName);
                        $tierName = preg_replace('/^.+?\)\s*[\-–—]\s*/', '', $tierName) ?: $tierName;
                        if (empty(trim($tierName)) || $tierName === $package->variation) {
                            // Fallback: prøv å strippe alt før siste bindestrek/tankestrek
                            if (preg_match('/[\-–—]\s*([^–—]+)$/', $package->variation, $m)) {
                                $tierName = trim($m[1]);
                            }
                        }
                        $isPopular = (bool) $package->is_standard;
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
                            {!! $package->description_with_check !!}
                        </div>
                        <div class="rk-package-card__cta">
                            @php $canBuy = Auth::guest() ? true : Auth::user()->could_buy_course; @endphp
                            @if($course->for_sale && $canBuy)
                                <a href="{{ route($checkoutRoute, [$course->id, 'package' => $package->id]) }}"
                                   class="rk-btn {{ $isPopular ? 'rk-btn--primary' : 'rk-btn--outline' }}">
                                    {{ $course->pay_later_with_application ? 'Søk om plass' : 'Velg ' . $tierName }}
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ═══════════ TESTIMONIALS ═══════════ --}}
    @if($testimonials->count())
    <section class="rk-testimonials">
        <div class="rk-container">
            <h2 class="rk-section-title">Hva elevene sier</h2>
            <p class="rk-section-sub">Tilbakemeldinger fra tidligere kursdeltakere.</p>

            <div class="rk-testimonial-grid">
                @foreach($testimonials as $testimonial)
                    <div class="rk-testimonial-card">
                        <div class="rk-testimonial-card__stars">★★★★★</div>
                        @if($testimonial->is_video)
                            <video controls>
                                <source src="{{ URL::asset($testimonial->user_image) }}">
                            </video>
                        @elseif($testimonial->user_image)
                            <img class="rk-testimonial-card__avatar"
                                 src="{{ asset($testimonial->user_image) }}" alt="{{ $testimonial->name }}">
                        @endif
                        <div class="rk-testimonial-card__text">{{ $testimonial->testimony }}</div>
                        <div class="rk-testimonial-card__author">— {{ $testimonial->name }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ═══════════ INGEN RISIKO ═══════════ --}}
    @if(!$course->is_free)
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
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div class="rk-risk-card__title">Bestill nå, betal senere</div>
                    <div class="rk-risk-card__desc">Sikre deg plassen uten å betale med en gang. Du velger selv når.</div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ═══════════ FINAL CTA ═══════════ --}}
    <section class="rk-final-cta">
        <div class="rk-container">
            <h2 class="rk-final-cta__title">Klar for å komme i gang?</h2>
            <p class="rk-final-cta__sub">
                @if($startDate)
                    Oppstart {{ $startDate->format('d') }}. {{ \App\Http\FrontendHelpers::convertMonthLanguage($startDate->format('n')) }}.
                @endif
                Sikre deg plassen din i dag.
            </p>
            @if(!$course->is_free && !$course->hide_price && $packages->count())
                <a href="#pakker" class="rk-btn rk-btn--primary" style="font-size: 1rem; padding: 0.85rem 2rem;">Se pakker og meld deg på →</a>
            @elseif($course->is_free)
                <a href="#gratis" class="rk-btn rk-btn--primary" style="font-size: 1rem; padding: 0.85rem 2rem;">Få kurset gratis →</a>
            @endif
        </div>
    </section>

</div>
@stop

@section('scripts')
@if(config('services.tracking.enabled'))
<script>
    // Meta Pixel ViewContent — hjelper Meta optimalisere mot kjøp.
    if (typeof fbq !== 'undefined') {
        fbq('track', 'ViewContent', {
            content_name: @json($course->title),
            content_category: 'course',
            content_ids: ['{{ $course->id }}'],
            content_type: 'product'
        });
    }
</script>
@endif
<script>
    // Smooth scroll for anchor links
    document.querySelectorAll('.rk-page a[href^="#"]').forEach(function(a) {
        a.addEventListener('click', function(e) {
            e.preventDefault();
            var target = document.querySelector(this.getAttribute('href'));
            if (target) {
                window.scrollTo({ top: target.offsetTop, behavior: 'smooth' });
            }
        });
    });

    @if(Session::has('email_exist'))
        $("#emailExistModal").modal('show');
    @endif
</script>
@stop
