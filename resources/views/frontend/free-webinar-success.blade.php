@extends('frontend.layout')

@section('page_title')Du er p&aring;meldt! &ndash; {{ $freeWebinar->title }} &ndash; Forfatterskolen@endsection
@section('meta_desc', 'Du er påmeldt gratiswebinaret. Sjekk e-posten din for bekreftelse og lenke.')

@section('styles')
<style>
    .fw-success {
        background: linear-gradient(135deg, #1c1917, #2a2520);
        min-height: 60vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 4rem 2rem;
    }

    .fw-success__card {
        background: #fff;
        border-radius: 14px;
        padding: 3rem 2.5rem;
        max-width: 520px;
        width: 100%;
        text-align: center;
    }

    .fw-success__icon {
        width: 64px; height: 64px;
        border-radius: 50%;
        background: #e8f5e9;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }

    .fw-success__icon svg { width: 32px; height: 32px; }

    .fw-success__title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #1a1a1a;
    }

    .fw-success__sub {
        font-family: 'Source Sans 3', -apple-system, sans-serif;
        font-size: 0.95rem;
        color: #5a5550;
        line-height: 1.7;
        margin-bottom: 1.5rem;
    }

    .fw-success__details {
        background: #faf8f5;
        border-radius: 10px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        font-family: 'Source Sans 3', -apple-system, sans-serif;
    }

    .fw-success__detail-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        justify-content: center;
        font-size: 0.85rem;
        color: #5a5550;
        padding: 0.25rem 0;
    }

    .fw-success__detail-item svg { width: 16px; height: 16px; stroke: #8a8580; }

    .fw-success__note {
        font-family: 'Source Sans 3', -apple-system, sans-serif;
        font-size: 0.75rem;
        color: #8a8580;
    }

    .fw-success__btn {
        display: inline-block;
        padding: 0.7rem 1.75rem;
        background: #862736;
        color: #fff;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        font-family: 'Source Sans 3', -apple-system, sans-serif;
        margin-bottom: 1rem;
        transition: background 0.15s;
    }

    .fw-success__btn:hover { background: #9c2e40; color: #fff; text-decoration: none; }
</style>
@stop

@section('content')

<?php
    $startDate = \Carbon\Carbon::parse($freeWebinar->start_date);
?>

<section class="fw-success">
    <div class="fw-success__card">
        <div class="fw-success__icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="#2e7d32" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
        </div>

        <h1 class="fw-success__title">Du er p&aring;meldt!</h1>
        <p class="fw-success__sub">
            Vi sender deg lenken til webinaret og en p&aring;minnelse p&aring; e-post. Sjekk innboksen din!
        </p>

        <div class="fw-success__details">
            <div class="fw-success__detail-item">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                {{ ucfirst($startDate->translatedFormat('l j. F Y')) }}
            </div>
            <div class="fw-success__detail-item">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Kl. {{ $startDate->format('H:i') }}
            </div>
        </div>

        <a href="https://www.bigmarker.com/system_check" target="_blank" class="fw-success__btn">
            Sjekk systemet ditt &rarr;
        </a>

        <p class="fw-success__note">
            Tips: Sjekk at kamera og mikrofon fungerer f&oslash;r webinaret starter.
        </p>
    </div>
</section>

@stop

@section('scripts')
@if(config('services.tracking.enabled'))
<script>
    // Facebook Pixel — Lead-konvertering
    if (typeof fbq !== 'undefined') {
        fbq('track', 'Lead', {
            content_name: '{{ $freeWebinar->title }}',
            content_category: 'webinar'
        });
    }

    // Google Ads — Lead-konvertering
    if (typeof gtag !== 'undefined') {
        @if(config('services.google_ads.conversion_lead'))
        gtag('event', 'conversion', {
            'send_to': '{{ config('services.google_ads.conversion_lead') }}'
        });
        @endif
    }
</script>
@endif
@stop
