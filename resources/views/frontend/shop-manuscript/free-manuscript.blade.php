@extends('frontend.layout')

@section('page_title', 'Gratis tekstvurdering &rsaquo; Forfatterskolen')
@section('meta_desc', 'Send inn inntil 5 sider av ditt manus for en gratis vurdering fra Forfatterskolens redaktører.')

@section('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Source+Sans+3:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .tekstvurdering {
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
            font-family: var(--font-body);
            -webkit-font-smoothing: antialiased;
        }

        /* ── HERO ───────────────────────────────────────── */
        .tekstvurdering .hero {
            background: #fff;
            padding: 4rem 2rem 3rem;
            text-align: center;
            border-bottom: 1px solid var(--border);
        }
        .tekstvurdering .hero__badge {
            display: inline-block;
            font-size: 0.72rem; font-weight: 600;
            letter-spacing: 1px; text-transform: uppercase;
            color: var(--wine);
            background: var(--wine-light-solid);
            padding: 0.3rem 0.85rem; border-radius: 20px;
            margin-bottom: 1.25rem;
        }
        .tekstvurdering .hero__title {
            font-family: var(--font-display);
            font-size: 2.25rem; font-weight: 700;
            color: var(--text-primary);
            max-width: 600px; margin: 0 auto 0.75rem;
            line-height: 1.2;
        }
        .tekstvurdering .hero__sub {
            font-size: 1.05rem; color: var(--text-secondary);
            max-width: 520px; margin: 0 auto; line-height: 1.6;
        }

        /* ── LAYOUT ─────────────────────────────────────── */
        .tekstvurdering .page {
            max-width: 960px; margin: 0 auto;
            padding: 2.5rem 2rem;
            display: grid; grid-template-columns: 1fr 300px;
            gap: 2.5rem; align-items: start;
        }

        /* ── FORM CARD ──────────────────────────────────── */
        .tekstvurdering .form-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 2rem;
        }
        .tekstvurdering .form-card__title {
            font-size: 1.1rem; font-weight: 700;
            color: var(--text-primary); margin-bottom: 1.25rem;
        }
        .tekstvurdering .form-row {
            display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;
        }
        .tekstvurdering .form-group { margin-bottom: 1rem; }
        .tekstvurdering .form-group label {
            display: block; font-size: 0.78rem; font-weight: 600;
            color: var(--text-primary); margin-bottom: 0.3rem;
        }
        .tekstvurdering .form-group input,
        .tekstvurdering .form-group select,
        .tekstvurdering .form-group textarea {
            width: 100%; padding: 0.65rem 0.9rem;
            border: 1px solid var(--border-strong); border-radius: 6px;
            font-family: var(--font-body); font-size: 0.875rem;
            color: var(--text-primary); outline: none;
            transition: border-color 0.15s; background: #fff;
        }
        .tekstvurdering .form-group input:focus,
        .tekstvurdering .form-group select:focus,
        .tekstvurdering .form-group textarea:focus { border-color: var(--wine); }
        .tekstvurdering .form-group input::placeholder,
        .tekstvurdering .form-group textarea::placeholder { color: var(--text-muted); }
        .tekstvurdering .form-group textarea {
            resize: vertical; min-height: 240px; line-height: 1.7;
        }
        .tekstvurdering .form-hint {
            font-size: 0.72rem; color: var(--text-muted); margin-top: 0.25rem;
        }

        /* Word counter */
        .tekstvurdering .textarea-footer {
            display: flex; align-items: center;
            justify-content: space-between; margin-top: 0.35rem;
        }
        .tekstvurdering .word-counter {
            font-size: 0.78rem; color: var(--text-muted);
            transition: color 0.15s;
        }
        .tekstvurdering .word-counter.over { color: #c62828; font-weight: 600; }
        .tekstvurdering .word-counter__bar {
            width: 120px; height: 3px;
            background: rgba(0,0,0,0.06); border-radius: 2px; overflow: hidden;
        }
        .tekstvurdering .word-counter__fill {
            height: 100%; background: var(--wine);
            border-radius: 2px; transition: width 0.2s, background 0.15s;
        }
        .tekstvurdering .word-counter__fill.over { background: #c62828; }

        /* Submit */
        .tekstvurdering .form-submit {
            width: 100%; padding: 0.85rem;
            background: var(--wine); color: #fff;
            border: none; border-radius: 8px;
            font-family: var(--font-body); font-size: 0.95rem; font-weight: 600;
            cursor: pointer; transition: background 0.15s; margin-top: 0.5rem;
        }
        .tekstvurdering .form-submit:hover { background: var(--wine-hover); }
        .tekstvurdering .form-submit:disabled {
            background: rgba(0,0,0,0.1); color: var(--text-muted); cursor: not-allowed;
        }
        .tekstvurdering .form-note {
            font-size: 0.75rem; color: var(--text-muted);
            text-align: center; margin-top: 0.85rem; line-height: 1.5;
        }

        /* Alert */
        .tekstvurdering .form-alert {
            background: #fef2f2; border: 1px solid #fecaca;
            border-radius: 8px; padding: 0.75rem 1rem;
            margin-bottom: 1rem; font-size: 0.82rem; color: #991b1b;
        }

        /* ── SIDEBAR ────────────────────────────────────── */
        .tekstvurdering .sidebar {
            display: flex; flex-direction: column; gap: 1.25rem;
        }
        .tekstvurdering .sidebar-card {
            background: #fff; border: 1px solid var(--border);
            border-radius: var(--radius-lg); padding: 1.5rem;
        }
        .tekstvurdering .sidebar-card__title {
            font-size: 0.9rem; font-weight: 700;
            color: var(--text-primary); margin-bottom: 0.85rem;
        }
        .tekstvurdering .sidebar-card__list {
            display: flex; flex-direction: column; gap: 0.6rem;
        }
        .tekstvurdering .sidebar-card__item {
            display: flex; align-items: flex-start; gap: 0.5rem;
            font-size: 0.825rem; color: var(--text-secondary); line-height: 1.5;
        }
        .tekstvurdering .sidebar-card__item svg {
            width: 16px; height: 16px; stroke: var(--green);
            flex-shrink: 0; margin-top: 2px;
        }

        /* Stats */
        .tekstvurdering .sidebar-stats {
            display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem;
        }
        .tekstvurdering .sidebar-stat {
            text-align: center; padding: 0.85rem;
            background: #fff; border: 1px solid var(--border);
            border-radius: var(--radius);
        }
        .tekstvurdering .sidebar-stat__number {
            font-size: 1.25rem; font-weight: 700;
            color: var(--wine); line-height: 1; margin-bottom: 0.15rem;
        }
        .tekstvurdering .sidebar-stat__label {
            font-size: 0.65rem; color: var(--text-muted);
        }

        /* Quote */
        .tekstvurdering .sidebar-quote {
            background: var(--wine-light-solid);
            border-radius: var(--radius-lg); padding: 1.5rem;
        }
        .tekstvurdering .sidebar-quote__text {
            font-family: var(--font-display);
            font-size: 0.95rem; font-style: italic;
            color: var(--text-primary); line-height: 1.6; margin-bottom: 0.75rem;
        }
        .tekstvurdering .sidebar-quote__author {
            font-size: 0.78rem; font-weight: 600; color: var(--wine);
        }

        @media (max-width: 768px) {
            .tekstvurdering .hero__title { font-size: 1.75rem; }
            .tekstvurdering .page { grid-template-columns: 1fr; }
            .tekstvurdering .form-row { grid-template-columns: 1fr; }
            .tekstvurdering .sidebar { order: -1; }
        }
    </style>
@stop

@section('content')
<div class="tekstvurdering">

    {{-- ═══════════ HERO ═══════════ --}}
    <div class="hero">
        <span class="hero__badge">Gratis og uforpliktende</span>
        <h1 class="hero__title">F&aring; en profesjonell vurdering av teksten din</h1>
        <p class="hero__sub">Send inn opptil 500 ord og f&aring; tilbakemelding fra en av v&aring;re redakt&oslash;rer &mdash; helt gratis.</p>
    </div>

    {{-- ═══════════ FORM + SIDEBAR ═══════════ --}}
    <div class="page">

        {{-- LEFT: SKJEMA --}}
        <div class="form-card">
            <div class="form-card__title">Send inn din tekst</div>

            @if($errors->any())
                <div class="form-alert">
                    @foreach($errors->all() as $error)
                        {!! $error !!}@if(!$loop->last)<br>@endif
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route($action) }}">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label for="fm-name">Fornavn *</label>
                        <input type="text" id="fm-name" name="name" required placeholder="Ditt fornavn" value="{{ old('name') }}">
                    </div>
                    <div class="form-group">
                        <label for="fm-email">E-post *</label>
                        <input type="email" id="fm-email" name="email" required placeholder="din@epost.no" value="{{ old('email') }}">
                        <span class="form-hint">Vi sender tilbakemeldingen hit.</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="fm-lastname">Etternavn *</label>
                    <input type="text" id="fm-lastname" name="last_name" required placeholder="Ditt etternavn" value="{{ old('last_name') }}">
                </div>

                <div class="form-group">
                    <label for="fm-genre">Sjanger</label>
                    <select id="fm-genre" name="genre" required>
                        <option value="" disabled selected>Velg sjanger</option>
                        @foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
                            <option value="{{ $type->id }}" {{ old('genre') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="fm-text">Din tekst (maks 500 ord) *</label>
                    <textarea id="fm-text" name="manuscript_content" required placeholder="Lim inn eller skriv teksten din her...">{{ old('manuscript_content') }}</textarea>
                    <div class="textarea-footer">
                        <div class="word-counter__bar">
                            <div class="word-counter__fill" id="wordBar" style="width: 0%;"></div>
                        </div>
                        <span class="word-counter" id="wordCounter">0 / 500 ord</span>
                    </div>
                </div>

                <button type="submit" class="form-submit" id="submitBtn">Send inn til vurdering</button>

                <p class="form-note">
                    Du mottar tilbakemeldingen p&aring; e-post innen 3 virkedager.<br>
                    Kun &eacute;n innsending per person.
                </p>
            </form>
        </div>

        {{-- RIGHT: SIDEBAR --}}
        <div class="sidebar">

            {{-- Hva du får --}}
            <div class="sidebar-card">
                <div class="sidebar-card__title">Hva du f&aring;r</div>
                <div class="sidebar-card__list">
                    <div class="sidebar-card__item">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Vurdering av spr&aring;k og stil
                    </div>
                    <div class="sidebar-card__item">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Tilbakemelding p&aring; fortellerstemme
                    </div>
                    <div class="sidebar-card__item">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Tekstens styrker og forbedringspunkter
                    </div>
                    <div class="sidebar-card__item">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Konkrete tips til videre arbeid
                    </div>
                </div>
            </div>

            {{-- Stats --}}
            <div class="sidebar-stats">
                <div class="sidebar-stat">
                    <div class="sidebar-stat__number">{{ number_format($totalEvaluations ?? 0, 0, ',', ' ') }}</div>
                    <div class="sidebar-stat__label">Tekster vurdert</div>
                </div>
                <div class="sidebar-stat">
                    <div class="sidebar-stat__number">3 dager</div>
                    <div class="sidebar-stat__label">Gjennomsnittlig svartid</div>
                </div>
            </div>

            {{-- Sitat --}}
            <div class="sidebar-quote">
                <div class="sidebar-quote__text">&laquo;Tilbakemeldingen ga meg troen p&aring; at dette var verdt &aring; jobbe videre med. N&aring; er boken ferdig!&raquo;</div>
                <div class="sidebar-quote__author">&mdash; Tidligere elev</div>
            </div>
        </div>

    </div>
</div>
@stop

@section('scripts')
<script>
    var textarea = document.getElementById('fm-text');
    var counter = document.getElementById('wordCounter');
    var bar = document.getElementById('wordBar');
    var submitBtn = document.getElementById('submitBtn');

    if (textarea) {
        textarea.addEventListener('input', function() {
            var words = this.value.trim().split(/\s+/).filter(function(w) { return w.length > 0; }).length;
            var pct = Math.min((words / 500) * 100, 100);
            var isOver = words > 500;

            counter.textContent = words + ' / 500 ord' + (isOver ? ' \u2014 for langt!' : '');
            counter.className = 'word-counter' + (isOver ? ' over' : '');

            bar.style.width = pct + '%';
            bar.className = 'word-counter__fill' + (isOver ? ' over' : '');

            submitBtn.disabled = isOver;
        });

        // Trigger on page load if old() text exists
        textarea.dispatchEvent(new Event('input'));
    }

    // Disable submit on form submit to prevent double-click
    var form = document.querySelector('.form-card form');
    if (form) {
        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sender...';
        });
    }
</script>
@stop
