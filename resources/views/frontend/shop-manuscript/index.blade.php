@extends('frontend.layout')

@section('page_title', 'Manusutvikling &rsaquo; Forfatterskolen')

@section('styles')
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&family=Source+Sans+3:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .manus-redesign {
            --wine: #862736;
            --wine-hover: #9c2e40;
            --wine-dark: #5c1a25;
            --wine-light: rgba(134, 39, 54, 0.08);
            --wine-light-solid: #f4e8ea;
            --cream: #faf8f5;
            --dark-bg: #1c1917;
            --dark-surface: rgba(255, 255, 255, 0.06);
            --dark-border: rgba(255, 255, 255, 0.1);
            --text-primary: #1a1a1a;
            --text-secondary: #5a5550;
            --text-muted: #8a8580;
            --border: rgba(0, 0, 0, 0.08);
            --border-strong: rgba(0, 0, 0, 0.12);
            --font-display: 'Playfair Display', Georgia, serif;
            --font-body: 'Source Sans 3', -apple-system, sans-serif;
            --max-width: 1080px;
            --radius: 10px;
            --radius-lg: 14px;
            font-family: var(--font-body);
            -webkit-font-smoothing: antialiased;
        }

        /* ── HERO ─────────────────────────────────────────── */
        .manus-redesign .manus-hero {
            background: var(--dark-bg);
            padding: 4rem 2rem 3.5rem;
            position: relative;
            overflow: hidden;
        }

        .manus-redesign .manus-hero::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(134, 39, 54, 0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        .manus-redesign .manus-hero__inner {
            max-width: var(--max-width);
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .manus-redesign .manus-hero__eyebrow {
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--wine);
            margin-bottom: 1rem;
        }

        .manus-redesign .manus-hero__heading {
            font-family: var(--font-display);
            font-size: clamp(2rem, 3.5vw, 2.75rem);
            font-weight: 700;
            line-height: 1.15;
            color: #fff;
            margin-bottom: 1.25rem;
        }

        .manus-redesign .manus-hero__heading em { color: var(--wine); font-style: italic; }

        .manus-redesign .manus-hero__description {
            font-size: 1rem;
            font-weight: 300;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.6);
            max-width: 420px;
            margin-bottom: 1.75rem;
        }

        .manus-redesign .manus-hero__cta {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--wine);
            color: #fff;
            border: none;
            border-radius: 6px;
            font-family: var(--font-body);
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
        }

        .manus-redesign .manus-hero__cta:hover { background: var(--wine-hover); transform: translateY(-1px); color: #fff; text-decoration: none; }

        .manus-redesign .manus-hero__features { display: flex; flex-direction: column; gap: 0.75rem; }

        .manus-redesign .manus-feature-card {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.25rem;
            background: var(--dark-surface);
            border: 1px solid var(--dark-border);
            border-radius: var(--radius);
            transition: border-color 0.2s, background 0.2s;
        }

        .manus-redesign .manus-feature-card:hover {
            border-color: rgba(134, 39, 54, 0.3);
            background: rgba(255, 255, 255, 0.08);
        }

        .manus-redesign .manus-feature-card__icon {
            width: 40px; height: 40px;
            background: rgba(134, 39, 54, 0.2);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .manus-redesign .manus-feature-card__icon svg { width: 20px; height: 20px; }
        .manus-redesign .manus-feature-card__title { font-size: 0.9rem; font-weight: 600; color: #fff; margin-bottom: 0.2rem; }
        .manus-redesign .manus-feature-card__desc { font-size: 0.8rem; color: rgba(255, 255, 255, 0.5); line-height: 1.5; }

        /* ── HOW IT WORKS ─────────────────────────────────── */
        .manus-redesign .manus-how-it-works { padding: 4rem 2rem; text-align: center; }

        .manus-redesign .manus-section-heading {
            font-family: var(--font-display);
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .manus-redesign .manus-section-heading::after {
            content: '';
            display: block;
            width: 40px; height: 3px;
            background: var(--wine);
            border-radius: 2px;
            margin: 0.75rem auto 0;
        }

        .manus-redesign .manus-section-sub {
            font-size: 0.95rem;
            color: var(--text-secondary);
            margin-top: 0.75rem;
            margin-bottom: 2.5rem;
        }

        .manus-redesign .manus-steps {
            max-width: var(--max-width);
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 2rem;
        }

        .manus-redesign .manus-step { text-align: center; padding: 1.5rem; }

        .manus-redesign .manus-step__number {
            width: 48px; height: 48px;
            border-radius: 50%;
            background: var(--wine-light-solid);
            color: var(--wine);
            font-family: var(--font-display);
            font-size: 1.25rem; font-weight: 700;
            display: inline-flex; align-items: center; justify-content: center;
            margin-bottom: 1rem;
        }

        .manus-redesign .manus-step__title { font-size: 1rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.4rem; }
        .manus-redesign .manus-step__desc { font-size: 0.85rem; color: var(--text-secondary); line-height: 1.6; }

        /* ── PRICING CARDS ────────────────────────────────── */
        .manus-redesign .manus-pricing-section {
            padding: 0 2rem 3rem;
        }

        .manus-redesign .manus-pricing-section .manus-section-heading { text-align: center; }
        .manus-redesign .manus-pricing-section .manus-section-sub { text-align: center; }

        .manus-redesign .manus-pricing-cards {
            max-width: 720px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
        }

        .manus-redesign .manus-pricing-card {
            background: var(--cream);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.75rem;
        }

        .manus-redesign .manus-pricing-card__header {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 1.25rem;
        }

        .manus-redesign .manus-pricing-card__icon {
            width: 32px; height: 32px;
            background: var(--wine-light-solid);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .manus-redesign .manus-pricing-card__icon svg { width: 16px; height: 16px; }

        .manus-redesign .manus-pricing-card__title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .manus-redesign .manus-pricing-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding: 0.65rem 0;
            border-bottom: 1px solid var(--border);
        }

        .manus-redesign .manus-pricing-row:last-of-type { border-bottom: none; }

        .manus-redesign .manus-pricing-row__label {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .manus-redesign .manus-pricing-row__value {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-primary);
            text-align: right;
        }

        .manus-redesign .manus-pricing-row__value small {
            display: block;
            font-size: 0.75rem;
            font-weight: 400;
            color: var(--text-muted);
        }

        .manus-redesign .manus-pricing-card__note {
            font-size: 0.78rem;
            color: var(--text-muted);
            line-height: 1.5;
            margin-top: 1rem;
            font-style: italic;
        }

        /* ── CALCULATOR ───────────────────────────────────── */
        .manus-redesign .manus-calculator-section { padding: 1rem 2rem 4rem; }

        .manus-redesign .manus-calculator {
            max-width: 720px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
        }

        .manus-redesign .manus-calculator__header {
            text-align: center;
            padding: 2rem 2rem 1rem;
        }

        .manus-redesign .manus-calculator__heading {
            font-family: var(--font-display);
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.3rem;
        }

        .manus-redesign .manus-calculator__sub {
            font-size: 0.825rem;
            color: var(--text-secondary);
        }

        .manus-redesign .manus-calc-tabs {
            display: flex;
            justify-content: center;
            gap: 0.25rem;
            margin: 1.25rem auto 0;
            background: rgba(0,0,0,0.05);
            border-radius: 8px;
            padding: 3px;
            width: fit-content;
        }

        .manus-redesign .manus-calc-tab {
            padding: 0.5rem 1.25rem;
            border: none;
            background: transparent;
            font-family: var(--font-body);
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-muted);
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .manus-redesign .manus-calc-tab.active {
            background: #fff;
            color: var(--text-primary);
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        .manus-redesign .manus-calculator__body { padding: 1.5rem 2rem 2rem; }

        .manus-redesign .manus-calc-panel { display: none; }
        .manus-redesign .manus-calc-panel.active { display: block; }

        /* Slider */
        .manus-redesign .manus-slider-panel { max-width: 100%; margin: 0 auto; text-align: center; }

        .manus-redesign .manus-word-count-display {
            font-family: var(--font-display);
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.2rem;
        }

        .manus-redesign .manus-word-count-display span {
            font-size: 1rem; font-weight: 400;
            color: var(--text-muted);
            font-family: var(--font-body);
        }

        .manus-redesign .manus-page-estimate {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        .manus-redesign .manus-word-slider {
            width: 100%; height: 6px;
            -webkit-appearance: none; appearance: none;
            background: var(--border-strong);
            border-radius: 3px;
            outline: none;
            margin-bottom: 0.5rem;
        }

        .manus-redesign .manus-word-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 22px; height: 22px;
            background: var(--wine); border-radius: 50%;
            cursor: pointer;
            border: 3px solid #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }

        .manus-redesign .manus-word-slider::-moz-range-thumb {
            width: 22px; height: 22px;
            background: var(--wine); border-radius: 50%;
            cursor: pointer;
            border: 3px solid #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }

        .manus-redesign .manus-slider-labels {
            display: flex;
            justify-content: space-between;
            font-size: 0.7rem;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        /* Genre selector */
        .manus-redesign .manus-genre-select {
            display: flex; align-items: center; gap: 0.75rem;
            justify-content: center;
        }

        .manus-redesign .manus-genre-select label {
            font-size: 0.825rem; color: var(--text-secondary); font-weight: 500; white-space: nowrap;
        }

        .manus-redesign .manus-genre-select select {
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--border-strong);
            border-radius: 6px;
            font-family: var(--font-body);
            font-size: 0.85rem;
            background: #fff;
            color: var(--text-primary);
            cursor: pointer;
        }

        /* Upload */
        .manus-redesign .manus-upload-panel { max-width: 100%; margin: 0 auto; }

        .manus-redesign .manus-upload-zone {
            border: 2px dashed var(--border-strong);
            border-radius: var(--radius);
            padding: 2.5rem 2rem;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
        }

        .manus-redesign .manus-upload-zone:hover {
            border-color: var(--wine);
            background: rgba(134, 39, 54, 0.03);
        }

        .manus-redesign .manus-upload-zone.dragover {
            border-color: var(--wine);
            background: rgba(134, 39, 54, 0.06);
        }

        .manus-redesign .manus-upload-zone__icon {
            width: 48px; height: 48px;
            background: var(--wine-light-solid);
            border-radius: 12px;
            display: inline-flex; align-items: center; justify-content: center;
            margin-bottom: 1rem;
        }

        .manus-redesign .manus-upload-zone__icon svg { width: 24px; height: 24px; }
        .manus-redesign .manus-upload-zone__title { font-size: 0.95rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem; }
        .manus-redesign .manus-upload-zone__sub { font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.75rem; }

        .manus-redesign .manus-upload-zone__formats { display: flex; justify-content: center; gap: 0.4rem; flex-wrap: wrap; }

        .manus-redesign .manus-format-badge {
            font-size: 0.7rem; font-weight: 500;
            color: var(--text-muted);
            background: rgba(0,0,0,0.04);
            padding: 0.2rem 0.6rem;
            border-radius: 4px;
        }

        .manus-redesign .manus-upload-message {
            font-size: 0.825rem;
            color: var(--text-secondary);
            margin-top: 1rem;
            text-align: center;
        }

        .manus-redesign .manus-upload-message.error {
            color: #c0392b;
        }

        .manus-redesign .manus-upload-message.success {
            color: #27ae60;
        }

        /* Price result */
        .manus-redesign .manus-price-result {
            background: var(--cream);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.5rem 1.75rem;
            margin-top: 1.75rem;
        }

        .manus-redesign .manus-price-result__row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
        }

        .manus-redesign .manus-price-result__row + .manus-price-result__row {
            border-top: 1px solid var(--border);
        }

        .manus-redesign .manus-price-result__label { font-size: 0.85rem; color: var(--text-secondary); }
        .manus-redesign .manus-price-result__value { font-size: 0.85rem; font-weight: 600; color: var(--text-primary); }

        .manus-redesign .manus-price-result__total {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding: 1rem 0 0;
            border-top: 2px solid var(--text-primary);
            margin-top: 0.5rem;
        }

        .manus-redesign .manus-price-result__total-label { font-size: 1rem; font-weight: 600; color: var(--text-primary); }

        .manus-redesign .manus-price-result__total-price {
            font-family: var(--font-display);
            font-size: 1.75rem; font-weight: 700;
            color: var(--wine);
        }

        .manus-redesign .manus-price-result__total-price span {
            font-size: 0.85rem; font-weight: 400;
            font-family: var(--font-body);
            color: var(--text-muted);
        }

        .manus-redesign .manus-price-result__note {
            font-size: 0.75rem; color: var(--text-muted);
            margin-top: 0.5rem; text-align: right;
        }

        .manus-redesign .manus-price-result__cta {
            display: block; width: 100%;
            padding: 0.85rem;
            background: var(--wine); color: #fff;
            border: none; border-radius: 6px;
            font-family: var(--font-body);
            font-size: 0.95rem; font-weight: 600;
            cursor: pointer; margin-top: 1.25rem;
            transition: background 0.2s;
            text-align: center; text-decoration: none;
        }

        .manus-redesign .manus-price-result__cta:hover { background: var(--wine-hover); color: #fff; text-decoration: none; }

        /* ── INFO BANNER ──────────────────────────────────── */
        .manus-redesign .manus-info-banner {
            max-width: 720px;
            margin: 0 auto 3rem;
            padding: 0 2rem;
        }

        .manus-redesign .manus-info-banner__inner {
            background: rgba(134, 39, 54, 0.05);
            border: 1px solid rgba(134, 39, 54, 0.1);
            border-radius: var(--radius);
            padding: 1rem 1.5rem;
            display: flex; align-items: center; gap: 0.75rem;
            font-size: 0.85rem; color: var(--text-secondary);
        }

        .manus-redesign .manus-info-banner__inner strong { color: var(--wine-dark); }

        /* ── COACHING SECTION ─────────────────────────────── */
        .manus-redesign .manus-coaching {
            padding: 4rem 2rem;
            background: var(--cream);
        }

        .manus-redesign .manus-coaching__inner {
            max-width: 720px;
            margin: 0 auto;
            text-align: center;
        }

        .manus-redesign .manus-coaching__badge {
            display: inline-block;
            padding: 0.35rem 1rem;
            background: var(--wine-light-solid);
            color: var(--wine);
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            border-radius: 20px;
            margin-bottom: 1rem;
        }

        .manus-redesign .manus-coaching__title {
            font-family: var(--font-display);
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.75rem;
        }

        .manus-redesign .manus-coaching__desc {
            font-size: 0.95rem;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 2.5rem;
            max-width: 560px;
            margin-left: auto;
            margin-right: auto;
        }

        .manus-redesign .manus-coaching__cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            max-width: 620px;
            margin: 0 auto 1.5rem;
        }

        .manus-redesign .manus-coaching-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 2rem 1.5rem;
            text-align: center;
            position: relative;
            transition: border-color 0.2s;
        }

        .manus-redesign .manus-coaching-card:hover {
            border-color: var(--wine);
        }

        .manus-redesign .manus-coaching-card--popular {
            border: 2px solid var(--wine);
        }

        .manus-redesign .manus-coaching-card__popular-badge {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--wine);
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 0.3rem 0.85rem;
            border-radius: 20px;
            white-space: nowrap;
        }

        .manus-redesign .manus-coaching-card__label {
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .manus-redesign .manus-coaching-card__price {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 700;
            color: var(--wine);
            margin-bottom: 0.2rem;
        }

        .manus-redesign .manus-coaching-card__price-note {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 1.25rem;
        }

        .manus-redesign .manus-coaching-card__features {
            list-style: none;
            text-align: left;
            margin-bottom: 1.5rem;
        }

        .manus-redesign .manus-coaching-card__features li {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.825rem;
            color: var(--text-secondary);
            padding: 0.3rem 0;
        }

        .manus-redesign .manus-coaching-card__features li svg {
            width: 14px; height: 14px; stroke: #2e7d32; flex-shrink: 0;
        }

        .manus-redesign .manus-coaching-card__cta {
            display: inline-block;
            padding: 0.65rem 1.75rem;
            border: 2px solid var(--wine);
            border-radius: 8px;
            font-family: var(--font-body);
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--wine);
            text-decoration: none;
            transition: background 0.15s, color 0.15s;
        }

        .manus-redesign .manus-coaching-card__cta:hover {
            background: var(--wine);
            color: #fff;
        }

        .manus-redesign .manus-coaching-card--popular .manus-coaching-card__cta {
            background: var(--wine);
            color: #fff;
        }

        .manus-redesign .manus-coaching-card--popular .manus-coaching-card__cta:hover {
            background: var(--wine-hover);
        }

        .manus-redesign .manus-coaching__note {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
        }

        @media (max-width: 600px) {
            .manus-redesign .manus-coaching__cards { grid-template-columns: 1fr; }
        }

        /* ── TESTIMONIAL ──────────────────────────────────── */
        .manus-redesign .manus-testimonial { padding: 3rem 2rem 4rem; }

        .manus-redesign .manus-testimonial__inner {
            max-width: 640px; margin: 0 auto; text-align: center;
        }

        .manus-redesign .manus-testimonial__quote {
            font-family: var(--font-display);
            font-size: 1.35rem; font-style: italic;
            line-height: 1.6; color: var(--text-primary);
            margin-bottom: 1.25rem;
        }

        .manus-redesign .manus-testimonial__author {
            font-size: 0.85rem; font-weight: 500; color: var(--text-secondary);
        }

        .manus-redesign .manus-testimonial__author strong { color: var(--text-primary); }

        /* ── RESPONSIVE ───────────────────────────────────── */
        @media (max-width: 900px) {
            .manus-redesign .manus-hero__inner { grid-template-columns: 1fr; gap: 2rem; }
        }

        @media (max-width: 600px) {
            .manus-redesign .manus-steps { grid-template-columns: 1fr; gap: 1rem; }
            .manus-redesign .manus-pricing-cards { grid-template-columns: 1fr; }
        }
    </style>
@stop

@section('content')

<div class="manus-redesign">

    {{-- ═══════════ HERO ═══════════ --}}
    <section class="manus-hero">
        <div class="manus-hero__inner">
            <div>
                <p class="manus-hero__eyebrow">Profesjonell manusvurdering</p>
                <h1 class="manus-hero__heading">Få ditt manus vurdert av <em>erfarne redaktører</em></h1>
                <p class="manus-hero__description">Er du usikker på om utkastet ditt holder? Våre redaktører gir deg grundig tilbakemelding med kommentarer i margen — tekstens svake og sterke sider.</p>
                <a href="#priskalkulator" class="manus-hero__cta">Beregn pris →</a>
            </div>
            <div class="manus-hero__features">
                <div class="manus-feature-card">
                    <div class="manus-feature-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#c45" stroke-width="1.5" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/></svg>
                    </div>
                    <div>
                        <div class="manus-feature-card__title">Grundig tilbakemelding</div>
                        <div class="manus-feature-card__desc">Detaljerte kommentarer i margen med fokus på styrker og svakheter</div>
                    </div>
                </div>
                <div class="manus-feature-card">
                    <div class="manus-feature-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#c45" stroke-width="1.5" stroke-linecap="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div>
                        <div class="manus-feature-card__title">Erfarne redaktører</div>
                        <div class="manus-feature-card__desc">Våre redaktører har lang erfaring med å utvikle manuskripter</div>
                    </div>
                </div>
                <div class="manus-feature-card">
                    <div class="manus-feature-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#c45" stroke-width="1.5" stroke-linecap="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    </div>
                    <div>
                        <div class="manus-feature-card__title">Veien til forlag</div>
                        <div class="manus-feature-card__desc">Vi har hjulpet mange forfattere med å bli utgitt</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════ HOW IT WORKS ═══════════ --}}
    <section class="manus-how-it-works">
        <h2 class="manus-section-heading">Slik fungerer manusutvikling</h2>
        <p class="manus-section-sub">Forfatterskolen tilbyr profesjonell tilbakemelding på ditt manus. En erfaren redaktør vil gi deg en grundig og detaljert tilbakemelding med kommentarer i margen — tekstens svake og sterke sider.</p>
        <div class="manus-steps">
            <div class="manus-step">
                <div class="manus-step__number">1</div>
                <div class="manus-step__title">Beregn pris</div>
                <div class="manus-step__desc">Bruk kalkulatoren eller last opp manuset ditt for å få pris basert på antall ord.</div>
            </div>
            <div class="manus-step">
                <div class="manus-step__number">2</div>
                <div class="manus-step__title">Send inn manus</div>
                <div class="manus-step__desc">Bestill og last opp manuset. Vi matcher deg med en redaktør som passer din sjanger.</div>
            </div>
            <div class="manus-step">
                <div class="manus-step__number">3</div>
                <div class="manus-step__title">Få tilbakemelding</div>
                <div class="manus-step__desc">Motta grundig tilbakemelding med kommentarer i margen innen avtalt tid.</div>
            </div>
        </div>
    </section>

    {{-- ═══════════ PRICING CARDS ═══════════ --}}
    <section class="manus-pricing-section">
        <h2 class="manus-section-heading">Priser</h2>
        <p class="manus-section-sub">Alle priser er eks. mva.</p>

        <div class="manus-pricing-cards">
            <div class="manus-pricing-card">
                <div class="manus-pricing-card__header">
                    <div class="manus-pricing-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="13" y2="17"/></svg>
                    </div>
                    <div class="manus-pricing-card__title">Grunnpris</div>
                </div>
                <div class="manus-pricing-row">
                    <span class="manus-pricing-row__label">Inntil 5 000 ord</span>
                    <span class="manus-pricing-row__value">1 500 kr</span>
                </div>
                <div class="manus-pricing-row">
                    <span class="manus-pricing-row__label">5 000 – 17 500 ord</span>
                    <span class="manus-pricing-row__value">0,112 kr/ord</span>
                </div>
                <div class="manus-pricing-row">
                    <span class="manus-pricing-row__label">Over 17 500 ord</span>
                    <span class="manus-pricing-row__value">2 900 kr <small>+ 0,15 kr/ord</small></span>
                </div>
            </div>
            <div class="manus-pricing-card">
                <div class="manus-pricing-card__header">
                    <div class="manus-pricing-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="9" x2="15" y2="9"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="12" y2="17"/></svg>
                    </div>
                    <div class="manus-pricing-card__title">Påslag for sjanger</div>
                </div>
                <div class="manus-pricing-row">
                    <span class="manus-pricing-row__label">Novelle</span>
                    <span class="manus-pricing-row__value">+30%</span>
                </div>
                <div class="manus-pricing-row">
                    <span class="manus-pricing-row__label">Lyrikk</span>
                    <span class="manus-pricing-row__value">+50%</span>
                </div>
                <p class="manus-pricing-card__note">Kortprosa og poesi krever ofte mer detaljert arbeid per ord.</p>
            </div>
        </div>
    </section>

    {{-- ═══════════ CALCULATOR ═══════════ --}}
    <section class="manus-calculator-section" id="priskalkulator">
        <div class="manus-calculator">
            <div class="manus-calculator__header">
                <h2 class="manus-calculator__heading">Beregn pris for ditt manus</h2>
                <p class="manus-calculator__sub">Velg antall ord eller last opp manuset ditt for å se pris</p>
                <div class="manus-calc-tabs">
                    <button class="manus-calc-tab active" data-tab="slider" onclick="manusCalcSwitchTab('slider')">Bruk slider</button>
                    <button class="manus-calc-tab" data-tab="upload" onclick="manusCalcSwitchTab('upload')">Last opp manus</button>
                </div>
            </div>

            <div class="manus-calculator__body">
                {{-- Slider panel --}}
                <div class="manus-calc-panel active" id="manus-panel-slider">
                    <div class="manus-slider-panel">
                        <div class="manus-word-count-display" id="manusWordCountDisplay">17 500 <span>ord</span></div>
                        <div class="manus-page-estimate" id="manusPageEstimate">ca. 50 sider</div>
                        <input type="range" class="manus-word-slider" id="manusWordSlider" min="1000" max="175000" value="17500" step="500">
                        <div class="manus-slider-labels">
                            <span>1 000</span>
                            <span>50 000</span>
                            <span>100 000</span>
                            <span>175 000</span>
                        </div>
                        <div class="manus-genre-select">
                            <label for="manusGenreSelect">Sjanger:</label>
                            <select id="manusGenreSelect" onchange="manusCalcUpdatePrice()">
                                <option value="standard">Roman / sakprosa (standard)</option>
                                <option value="novelle">Novelle (+30%)</option>
                                <option value="lyrikk">Lyrikk (+50%)</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Upload panel --}}
                <div class="manus-calc-panel" id="manus-panel-upload">
                    <div class="manus-upload-panel">
                        <div class="manus-upload-zone" id="manusUploadZone">
                            <div class="manus-upload-zone__icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            </div>
                            <div class="manus-upload-zone__title" id="manusUploadTitle">Dra og slipp fil her</div>
                            <div class="manus-upload-zone__sub" id="manusUploadSub">eller klikk for å velge</div>
                            <div class="manus-upload-zone__formats" id="manusUploadFormats">
                                <span class="manus-format-badge">.doc</span>
                                <span class="manus-format-badge">.docx</span>
                                <span class="manus-format-badge">.odt</span>
                                <span class="manus-format-badge">.pdf</span>
                                <span class="manus-format-badge">.pages</span>
                            </div>
                        </div>
                        <input type="file" id="manusFileInput" hidden accept=".doc,.docx,.pdf,.odt,.pages,application/vnd.apple.pages,application/x-iwork-pages-sffpages">
                        <div class="manus-upload-message" id="manusUploadMessage" style="display:none;"></div>
                        <div class="manus-genre-select" style="margin-top: 1.5rem;">
                            <label for="manusGenreSelect2">Sjanger:</label>
                            <select id="manusGenreSelect2" onchange="manusCalcUpdatePrice()">
                                <option value="standard">Roman / sakprosa (standard)</option>
                                <option value="novelle">Novelle (+30%)</option>
                                <option value="lyrikk">Lyrikk (+50%)</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Price result --}}
                <div class="manus-price-result">
                    <div class="manus-price-result__row">
                        <span class="manus-price-result__label">Antall ord</span>
                        <span class="manus-price-result__value" id="manusResultWords">17 500</span>
                    </div>
                    <div class="manus-price-result__row">
                        <span class="manus-price-result__label">Grunnpris</span>
                        <span class="manus-price-result__value" id="manusResultBase">2 900 kr</span>
                    </div>
                    <div class="manus-price-result__row" id="manusSurchargeRow" style="display: none;">
                        <span class="manus-price-result__label">Sjangerpåslag</span>
                        <span class="manus-price-result__value" id="manusSurchargeValue">+30%</span>
                    </div>
                    <div class="manus-price-result__total">
                        <span class="manus-price-result__total-label">Totalt</span>
                        <span class="manus-price-result__total-price" id="manusTotalPrice">2 900 <span>kr</span></span>
                    </div>
                    <div class="manus-price-result__note">Alle priser eks. mva. for ikke-elever.</div>
                    <a href="#" class="manus-price-result__cta" id="manusCheckoutCta">Bestill manusutvikling →</a>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════ INFO BANNER ═══════════ --}}
    <div class="manus-info-banner">
        <div class="manus-info-banner__inner">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <span><strong>NB!</strong> Dersom du ikke er elev ved Forfatterskolen, er tjenesten momsbelagt (25%).</span>
        </div>
    </div>

    {{-- ═══════════ TESTIMONIAL ═══════════ --}}
    <section class="manus-testimonial">
        <div class="manus-testimonial__inner">
            <p class="manus-testimonial__quote">"Tilbakemeldingen fra redaktøren var grundig og konstruktiv. Det ga meg akkurat det dyttet jeg trengte for å ferdigstille manuset og sende det til forlag."</p>
            <p class="manus-testimonial__author"><strong>Utgitt elev</strong> — via Forfatterskolen</p>
        </div>
    </section>

    {{-- ═══════════ COACHING ═══════════ --}}
    <section class="manus-coaching" id="coaching">
        <div class="manus-coaching__inner">
            <span class="manus-coaching__badge">Tilleggstjeneste</span>
            <h2 class="manus-coaching__title">Coaching med redaktør</h2>
            <p class="manus-coaching__desc">Book en personlig gjennomgang med en av våre erfarne redaktører. Perfekt som supplement til manusutvikling &mdash; eller som en selvstendig tjeneste.</p>

            <div class="manus-coaching__cards">
                {{-- Halvtime --}}
                <div class="manus-coaching-card">
                    <div class="manus-coaching-card__label">Halvtime</div>
                    <div class="manus-coaching-card__price">kr 1 190</div>
                    <div class="manus-coaching-card__price-note">eks. mva</div>
                    <ul class="manus-coaching-card__features">
                        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> 30 min en-til-en med redaktør</li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> Fokus på ditt manus</li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> Video eller telefon</li>
                    </ul>
                    <a href="{{ route('front.shop-manuscript.checkout', 3) }}" class="manus-coaching-card__cta">Bestill 30 min &rarr;</a>
                </div>

                {{-- Hel time --}}
                <div class="manus-coaching-card manus-coaching-card--popular">
                    <span class="manus-coaching-card__popular-badge">Mest populær</span>
                    <div class="manus-coaching-card__label">Hel time</div>
                    <div class="manus-coaching-card__price">kr 1 690</div>
                    <div class="manus-coaching-card__price-note">eks. mva</div>
                    <ul class="manus-coaching-card__features">
                        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> 60 min en-til-en med redaktør</li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> Grundig gjennomgang av manus</li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> Konkrete råd til neste steg</li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> Video eller telefon</li>
                    </ul>
                    <a href="{{ route('front.shop-manuscript.checkout', 3) }}" class="manus-coaching-card__cta">Bestill 60 min &rarr;</a>
                </div>
            </div>

            <p class="manus-coaching__note">Spar 10% på coaching når du bestiller sammen med manusutvikling.</p>
        </div>
    </section>

</div>

{{-- ═══════════ MODALS (beholdes) ═══════════ --}}
<div class="modal fade" role="dialog" id="editorsModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card-columns">
                    @foreach($editors->chunk(3) as $editor_chunk)
                        <div class="card-container">
                        @foreach($editor_chunk as $editor)
                            <div class="card">
                                <div class="card-header">
                                </div>
                                <div class="card-body text-center">
                                    <div class="editor-circle">
                                        <img src="{{ asset($editor['editor_image']) }}" alt="editor image" class="rounded-circle">
                                    </div>
                                    <p>
                                        <strong class="editor-name">{{ $editor['name'] }}</strong> {{ $editor['description'] }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@if(Session::has('manuscript_test'))
    <div id="manuscriptTestModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center">
                    {!! Session::get('manuscript_test') !!}
                </div>
            </div>
        </div>
    </div>
@endif

@if(Session::has('manuscript_test_error'))
    <div id="manuscriptTestErrorModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div style="color: red; font-size: 24px"><i class="fa fa-close"></i></div>
                    {!! Session::get('manuscript_test_error') !!}
                </div>
            </div>
        </div>
    </div>
@endif

@stop

@section('scripts')
    <script src="https://unpkg.com/mammoth@1.4.21/mammoth.browser.min.js"></script>
    <script>
        // ── Product data for dynamic checkout URL ──────────
        var manusProducts = [
            @foreach($shopManuscripts->sortBy('max_words') as $product)
            {
                id: {{ $product->id }},
                maxWords: {{ $product->max_words }},
                price: {{ $product->full_payment_price }},
                checkoutUrl: "{{ route($checkoutRoute, $product->id) }}"
            },
            @endforeach
        ];

        // ── Pricing logic ──────────────────────────────────
        var manusGenreMultipliers = { standard: 1, novelle: 1.3, lyrikk: 1.5 };

        function manusCalcBasePrice(words) {
            if (words <= 5000) return 1500;
            if (words <= 17500) return 1500 + Math.round((words - 5000) * 0.112);
            return 2900 + Math.round((words - 17500) * 0.15);
        }

        function manusFormatNumber(n) {
            return n.toLocaleString('nb-NO');
        }

        function manusGetCheckoutUrl(words) {
            var url = '#';
            for (var i = 0; i < manusProducts.length; i++) {
                if (words <= manusProducts[i].maxWords) {
                    url = manusProducts[i].checkoutUrl;
                    break;
                }
            }
            if (url === '#' && manusProducts.length) {
                url = manusProducts[manusProducts.length - 1].checkoutUrl;
            }
            // Legg til ordtelling som URL-parameter
            return url + (url.indexOf('?') >= 0 ? '&' : '?') + 'words=' + words;
        }

        var manusSlider = document.getElementById('manusWordSlider');
        var manusActiveTab = 'slider';

        function manusCalcUpdatePrice() {
            var words = parseInt(manusSlider.value);
            var genreId = manusActiveTab === 'slider' ? 'manusGenreSelect' : 'manusGenreSelect2';
            var genre = document.getElementById(genreId).value;
            var multiplier = manusGenreMultipliers[genre];
            var basePrice = manusCalcBasePrice(words);
            var total = Math.round(basePrice * multiplier);

            document.getElementById('manusWordCountDisplay').innerHTML = manusFormatNumber(words) + ' <span>ord</span>';
            document.getElementById('manusPageEstimate').textContent = 'ca. ' + Math.round(words / 350) + ' sider';
            document.getElementById('manusResultWords').textContent = manusFormatNumber(words);
            document.getElementById('manusResultBase').textContent = manusFormatNumber(basePrice) + ' kr';

            var surchargeRow = document.getElementById('manusSurchargeRow');
            if (genre !== 'standard') {
                surchargeRow.style.display = 'flex';
                var surchargeAmount = Math.round(basePrice * (multiplier - 1));
                document.getElementById('manusSurchargeValue').textContent =
                    (genre === 'novelle' ? '+30%' : '+50%') + ' → ' + manusFormatNumber(surchargeAmount) + ' kr';
            } else {
                surchargeRow.style.display = 'none';
            }

            document.getElementById('manusTotalPrice').innerHTML = manusFormatNumber(total) + ' <span>kr</span>';
            document.getElementById('manusCheckoutCta').href = manusGetCheckoutUrl(words);
        }

        manusSlider.addEventListener('input', manusCalcUpdatePrice);
        manusCalcUpdatePrice();

        function manusCalcSwitchTab(tabName) {
            document.querySelectorAll('.manus-calc-tab').forEach(function(t) { t.classList.remove('active'); });
            document.querySelectorAll('.manus-calc-panel').forEach(function(p) { p.classList.remove('active'); });
            document.querySelector('[data-tab="' + tabName + '"]').classList.add('active');
            document.getElementById('manus-panel-' + tabName).classList.add('active');
            manusActiveTab = tabName;
        }

        // ── File upload & word count extraction ────────────
        (function() {
            var uploadZone = document.getElementById('manusUploadZone');
            var fileInput = document.getElementById('manusFileInput');
            var uploadTitle = document.getElementById('manusUploadTitle');
            var uploadSub = document.getElementById('manusUploadSub');
            var uploadFormats = document.getElementById('manusUploadFormats');
            var uploadMessage = document.getElementById('manusUploadMessage');

            var mammothAvailable = typeof window.mammoth !== 'undefined'
                && typeof window.mammoth.extractRawText === 'function';
            var mammothExtensions = ['doc', 'docx'];

            function getFileExtension(fileName) {
                if (!fileName) return '';
                var match = fileName.toLowerCase().match(/\.([^.]+)$/);
                return match ? match[1] : '';
            }

            function getCsrfToken() {
                var meta = document.querySelector('meta[name="csrf-token"]');
                return meta ? meta.getAttribute('content') : null;
            }

            function createDocxFileName(name) {
                if (!name) return 'document.docx';
                var dot = name.lastIndexOf('.');
                if (dot <= 0) return name + '.docx';
                return name.substring(0, dot) + '.docx';
            }

            function showMessage(text, type) {
                uploadMessage.textContent = text;
                uploadMessage.className = 'manus-upload-message' + (type ? ' ' + type : '');
                uploadMessage.style.display = 'block';
            }

            function hideMessage() {
                uploadMessage.style.display = 'none';
            }

            function resetUploadUI() {
                uploadTitle.textContent = 'Dra og slipp fil her';
                uploadSub.textContent = 'eller klikk for å velge';
                uploadFormats.style.display = 'flex';
                hideMessage();
            }

            function countWords(text) {
                if (typeof text !== 'string') return 0;
                var normalised = text.replace(/[\r\n\t]+/g, ' ').trim();
                if (!normalised) return 0;
                var matches = normalised.match(/\S+/g);
                return matches ? matches.length : 0;
            }

            function extractWithMammoth(file) {
                return new Promise(function(resolve, reject) {
                    if (!mammothAvailable) { resolve(null); return; }
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var buf = e.target ? e.target.result : null;
                        if (!buf) { resolve(null); return; }
                        window.mammoth.extractRawText({ arrayBuffer: buf })
                            .then(function(result) {
                                resolve(countWords(result && result.value ? result.value : ''));
                            })
                            .catch(reject);
                    };
                    reader.onerror = function() { reject(reader.error); };
                    reader.readAsArrayBuffer(file);
                });
            }

            async function convertToDocx(file) {
                var formData = new FormData();
                formData.append('document', file);
                var csrf = getCsrfToken();
                if (csrf) formData.append('_token', csrf);

                var headers = { 'X-Requested-With': 'XMLHttpRequest' };
                if (csrf) headers['X-CSRF-TOKEN'] = csrf;

                var response = await fetch('/documents/convert-to-docx', {
                    method: 'POST',
                    body: formData,
                    headers: headers
                });

                if (!response.ok) {
                    throw new Error('Konvertering feilet');
                }

                var blob = await response.blob();
                var mime = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                return new File([blob], createDocxFileName(file.name), { type: mime, lastModified: Date.now() });
            }

            async function handleFile(files) {
                if (!files || !files.length) return;
                var file = files[0];

                uploadTitle.textContent = file.name;
                uploadSub.textContent = 'Teller ord...';
                uploadFormats.style.display = 'none';
                hideMessage();

                var ext = getFileExtension(file.name);
                var processedFile = file;

                // Convert all non-docx formats (doc, pdf, odt, pages) to docx via server
                if (ext !== 'docx') {
                    try {
                        showMessage('Konverterer fil...', '');
                        processedFile = await convertToDocx(file);
                        hideMessage();
                    } catch (err) {
                        uploadSub.textContent = '';
                        showMessage('Kunne ikke konvertere filen. Last opp som .docx i stedet, eller bruk slideren for å beregne pris manuelt.', 'error');
                        return;
                    }
                }

                // Extract word count with mammoth
                try {
                    var wordCount = await extractWithMammoth(processedFile);
                    if (wordCount && wordCount > 0) {
                        uploadSub.textContent = manusFormatNumber(wordCount) + ' ord funnet';
                        showMessage('Ordtelling fullført! Prisen er oppdatert nedenfor.', 'success');

                        // Update slider and price
                        var clampedWords = Math.min(Math.max(wordCount, 1000), 175000);
                        manusSlider.value = clampedWords;
                        manusCalcUpdatePrice();
                    } else {
                        uploadSub.textContent = '';
                        showMessage('Kunne ikke telle ord i filen. Bruk slideren i stedet.', 'error');
                    }
                } catch (err) {
                    uploadSub.textContent = '';
                    showMessage('Kunne ikke lese filen. Bruk slideren i stedet.', 'error');
                }
            }

            // Click to upload
            uploadZone.addEventListener('click', function() {
                fileInput.click();
            });

            fileInput.addEventListener('change', function(e) {
                handleFile(e.target.files);
            });

            // Drag and drop
            uploadZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                uploadZone.classList.add('dragover');
            });

            uploadZone.addEventListener('dragleave', function() {
                uploadZone.classList.remove('dragover');
            });

            uploadZone.addEventListener('drop', function(e) {
                e.preventDefault();
                uploadZone.classList.remove('dragover');
                var files = e.dataTransfer ? e.dataTransfer.files : null;
                if (files && files.length) {
                    handleFile(files);
                }
            });
        })();

        // ── Session modals ─────────────────────────────────
        @if(Session::has('manuscript_test'))
            (function() {
                var el = document.getElementById('manuscriptTestModal');
                if (el) new bootstrap.Modal(el).show();
            })();
        @endif
        @if(Session::has('manuscript_test_error'))
            (function() {
                var el = document.getElementById('manuscriptTestErrorModal');
                if (el) new bootstrap.Modal(el).show();
            })();
        @endif
    </script>
@stop
