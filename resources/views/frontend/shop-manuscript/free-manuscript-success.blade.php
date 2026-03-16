@extends('frontend.layout')

@section('title')
<title>Takk for innsendingen &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700&family=Source+Sans+3:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .takk-page {
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
            --font-display: 'Playfair Display', Georgia, serif;
            --font-body: 'Source Sans 3', -apple-system, sans-serif;
            --radius-lg: 14px;
            font-family: var(--font-body);
            -webkit-font-smoothing: antialiased;
            max-width: 620px;
            margin: 0 auto;
            padding: 3rem 2rem;
            text-align: center;
        }

        .takk-page .takk-icon {
            width: 64px; height: 64px;
            border-radius: 50%;
            background: var(--green-bg);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.25rem;
        }
        .takk-page .takk-icon svg { width: 32px; height: 32px; stroke: var(--green); }

        .takk-page .takk-title {
            font-family: var(--font-display);
            font-size: 1.75rem; font-weight: 700;
            color: var(--text-primary); margin-bottom: 0.5rem;
        }
        .takk-page .takk-desc {
            font-size: 1rem; color: var(--text-secondary);
            margin-bottom: 2rem; line-height: 1.6;
        }

        .takk-page .upsell-card {
            background: #fff; border: 1px solid var(--border);
            border-radius: var(--radius-lg); padding: 2rem;
            text-align: left;
        }
        .takk-page .upsell-card__label {
            font-size: 0.65rem; font-weight: 600;
            letter-spacing: 1px; text-transform: uppercase;
            color: var(--wine); margin-bottom: 0.5rem;
        }
        .takk-page .upsell-card__title {
            font-size: 1.15rem; font-weight: 700;
            color: var(--text-primary); margin-bottom: 0.5rem;
        }
        .takk-page .upsell-card__desc {
            font-size: 0.875rem; color: var(--text-secondary);
            line-height: 1.6; margin-bottom: 1.25rem;
        }
        .takk-page .upsell-card__features {
            display: flex; flex-direction: column;
            gap: 0.4rem; margin-bottom: 1.25rem;
        }
        .takk-page .upsell-card__feature {
            display: flex; align-items: center; gap: 0.5rem;
            font-size: 0.8rem; color: var(--text-secondary);
        }
        .takk-page .upsell-card__feature svg {
            width: 14px; height: 14px; stroke: var(--green); flex-shrink: 0;
        }
        .takk-page .upsell-card__price {
            font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1rem;
        }
        .takk-page .btn-primary {
            display: inline-block; padding: 0.75rem 1.5rem;
            background: var(--wine); color: #fff;
            border-radius: 6px; text-decoration: none;
            font-weight: 600; font-size: 0.9rem;
            transition: background 0.15s;
        }
        .takk-page .btn-primary:hover { background: var(--wine-hover); color: #fff; text-decoration: none; }
    </style>
@stop

@section('content')
<div class="takk-page">
    <div class="takk-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
    </div>

    <h1 class="takk-title">Takk for innsendingen!</h1>
    <p class="takk-desc">
        Vi har mottatt teksten din og sender tilbakemeldingen til
        @if(session('submitted_email'))
            <strong>{{ session('submitted_email') }}</strong>
        @else
            <strong>din e-post</strong>
        @endif
        innen 3 virkedager.
    </p>

    <div class="upsell-card">
        <div class="upsell-card__label">Neste steg</div>
        <div class="upsell-card__title">Vil du ha en grundigere vurdering?</div>
        <div class="upsell-card__desc">
            Med manusutvikling f&aring;r du detaljert tilbakemelding p&aring; hele manuset &mdash; ikke bare 500 ord.
        </div>
        <div class="upsell-card__features">
            <div class="upsell-card__feature">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                Kommentarer i margen gjennom hele teksten
            </div>
            <div class="upsell-card__feature">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                Synopsis med styrker, svakheter og r&aring;d
            </div>
            <div class="upsell-card__feature">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                Coaching-time med redakt&oslash;r
            </div>
        </div>
        <div class="upsell-card__price">Fra kr 1 500 basert p&aring; ordtelling</div>
        <a href="{{ route('front.shop-manuscript.index') }}" class="btn-primary">Se priser for manusutvikling &rarr;</a>
    </div>
</div>
@stop
