<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <link rel="alternate" href="{{ config('app.url') }}" hreflang="no" />
        <link rel="alternate" href="{{ config('app.url') }}/en" hreflang="en" />
        <link rel="alternate" href="{{ url()->current() }}" hreflang="{{ app()->getLocale() }}" />
        <link rel="canonical" href="{{ url()->current() }}">

        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-PBZBPBN2');</script>
        <!-- End Google Tag Manager -->

        <meta name="google-site-verification" content="PT1CQ7dxKhPpwvuFW6e2o_AVdp10XC-wUvvbHHuY0IE" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Source+Sans+3:wght@300;400;500;600&display=swap" rel="stylesheet">

        @include('frontend.partials.frontend-css')
        <link rel="stylesheet" href="{{ asset('css/learner.css?v='.time()) }}">

        @yield('title')

        <meta name="keywords" content="forfatterskolen, forfatter, kurs, manusutvikling, manus, manuskript, kikt, sakprosa, serieroman, krim, roman">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="p:domain_verify" content="eca72f9965922b1f82c80a1ef6e62743"/>
        @yield('metas')

        <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}" />
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"
              integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous">

        <style>
            /* ══════════════════════════════════════════
               CSS VARIABLES
            ══════════════════════════════════════════ */
            :root {
                --brand-primary: #862736;
                --brand-dark: #5e1a26;
                --brand-light: #a8344a;
                --brand-accent: #d4a853;
                --bg: #f6f5f3;
                --surface: #ffffff;
                --text: #2c2c2c;
                --text-secondary: #6b6b6b;
                --muted: #999;
                --border: #e4e1dc;
                --border-light: #f0ede8;
                --success: #2d8a56;
                --warning: #d4a020;
                --info: #2a7ab5;
                --danger: #c0392b;
                --radius: 10px;
                --radius-sm: 6px;
                --shadow-sm: 0 1px 3px rgba(0,0,0,.06);
                --shadow-md: 0 4px 16px rgba(0,0,0,.08);
                --sidebar-w: 260px;
                --topbar-h: 58px;
                --font-display: 'Playfair Display', Georgia, serif;
                --font-body: 'Source Sans 3', -apple-system, BlinkMacSystemFont, sans-serif;
            }

            /* ══════════════════════════════════════════
               BASE
            ══════════════════════════════════════════ */
            *, *::before, *::after { box-sizing: border-box; }
            body {
                font-family: var(--font-body);
                color: var(--text);
                background: var(--bg);
                margin: 0;
                padding: 0;
                min-height: 100vh;
                -webkit-font-smoothing: antialiased;
            }
            a { color: var(--brand-primary); text-decoration: none; }
            a:hover { color: var(--brand-light); }

            /* ══════════════════════════════════════════
               SIDEBAR
            ══════════════════════════════════════════ */
            #sp-sidebar {
                width: var(--sidebar-w);
                min-height: 100vh;
                background: linear-gradient(180deg, var(--brand-dark) 0%, var(--brand-primary) 100%);
                color: rgba(255,255,255,.85);
                display: flex;
                flex-direction: column;
                position: fixed;
                top: 0; left: 0; bottom: 0;
                z-index: 100;
                overflow-y: auto;
                transition: transform .3s ease;
            }

            .sidebar-brand {
                padding: 28px 24px 20px;
                text-align: center;
                border-bottom: 1px solid rgba(255,255,255,.1);
            }
            .sidebar-brand img { height: 52px; }
            .sidebar-brand-text {
                font-family: var(--font-display);
                font-size: 17px;
                font-weight: 600;
                letter-spacing: .02em;
                color: #fff;
                margin-top: 8px;
            }

            .sidebar-section-title {
                font-size: 10.5px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .12em;
                color: rgba(255,255,255,.4);
                padding: 22px 20px 8px;
            }

            .sidebar-nav { list-style: none; padding: 0; margin: 0; }

            .sidebar-nav .nav-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 11px 20px;
                font-size: 13.5px;
                color: rgba(255,255,255,.75);
                border-left: 3px solid transparent;
                cursor: pointer;
                transition: all .2s;
                text-decoration: none;
            }
            .sidebar-nav .nav-item:hover {
                background: rgba(255,255,255,.08);
                color: #fff;
                text-decoration: none;
            }
            .sidebar-nav .nav-item.active {
                background: rgba(255,255,255,.12);
                color: #fff;
                border-left-color: var(--brand-accent);
                font-weight: 500;
            }
            .sidebar-nav .nav-item i {
                width: 18px;
                text-align: center;
                opacity: .7;
                font-size: 14px;
            }
            .sidebar-nav .nav-item.active i { opacity: 1; }

            .sidebar-footer {
                margin-top: auto;
                padding: 16px 20px;
                border-top: 1px solid rgba(255,255,255,.1);
                display: flex;
                flex-direction: column;
                gap: 8px;
            }
            .btn-portal {
                display: block;
                text-align: center;
                padding: 9px 12px;
                border: 1.5px solid rgba(255,255,255,.25);
                border-radius: var(--radius-sm);
                color: rgba(255,255,255,.85);
                font-size: 12.5px;
                font-weight: 500;
                transition: all .2s;
                text-decoration: none;
            }
            .btn-portal:hover {
                border-color: #fff;
                color: #fff;
                background: rgba(255,255,255,.08);
                text-decoration: none;
            }

            .btn-logout {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                padding: 9px;
                background: rgba(255,255,255,.12);
                color: #fff;
                border-radius: var(--radius-sm);
                font-size: 12.5px;
                font-weight: 500;
                border: none;
                cursor: pointer;
                transition: background .2s;
                text-decoration: none;
            }
            .btn-logout:hover {
                background: rgba(255,255,255,.2);
                color: #fff;
                text-decoration: none;
            }

            /* ══════════════════════════════════════════
               TOPBAR
            ══════════════════════════════════════════ */
            #sp-topbar {
                position: fixed;
                top: 0;
                left: var(--sidebar-w);
                right: 0;
                height: var(--topbar-h);
                background: var(--surface);
                border-bottom: 1px solid var(--border);
                display: flex;
                align-items: center;
                padding: 0 32px;
                z-index: 90;
                box-shadow: var(--shadow-sm);
                transition: left .3s ease;
            }

            .topbar-hamburger {
                display: none;
                background: none;
                border: none;
                font-size: 20px;
                color: var(--text);
                cursor: pointer;
                margin-right: 16px;
            }

            .breadcrumbs {
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 13px;
                color: var(--muted);
            }
            .breadcrumbs a { color: var(--text-secondary); }
            .breadcrumbs a:hover { color: var(--brand-primary); }
            .breadcrumbs .sep { color: var(--border); }
            .breadcrumbs .bc-current { color: var(--text); font-weight: 500; }

            .topbar-right {
                margin-left: auto;
                display: flex;
                align-items: center;
                gap: 16px;
            }
            .topbar-right .notification-bell {
                position: relative;
                font-size: 17px;
                color: var(--text-secondary);
                cursor: pointer;
            }
            .topbar-right .notification-bell .badge-dot {
                position: absolute;
                top: -2px; right: -4px;
                width: 8px; height: 8px;
                background: var(--danger);
                border-radius: 50%;
                border: 2px solid var(--surface);
            }
            .topbar-avatar {
                width: 34px; height: 34px;
                border-radius: 50%;
                background: var(--brand-primary);
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 13px;
                font-weight: 600;
            }

            /* ══════════════════════════════════════════
               MAIN CONTENT AREA
            ══════════════════════════════════════════ */
            #sp-main {
                margin-left: var(--sidebar-w);
                margin-top: var(--topbar-h);
                padding: 32px 40px 60px;
                max-width: 1040px;
                transition: margin-left .3s ease;
            }

            /* ══════════════════════════════════════════
               REUSABLE COMPONENTS
            ══════════════════════════════════════════ */

            /* Cards */
            .sp-card {
                background: var(--surface);
                border: 1px solid var(--border);
                border-radius: var(--radius);
                box-shadow: var(--shadow-sm);
                margin-bottom: 24px;
            }
            .sp-card-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 18px 24px;
                border-bottom: 1px solid var(--border-light);
            }
            .sp-card-header h2 {
                font-family: var(--font-display);
                font-size: 18px;
                font-weight: 600;
                color: var(--text);
                margin: 0;
            }
            .sp-card-body { padding: 20px 24px; }

            /* Table */
            .sp-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 13.5px;
            }
            .sp-table th {
                text-align: left;
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .06em;
                color: var(--muted);
                padding: 10px 12px;
                border-bottom: 2px solid var(--border);
            }
            .sp-table td {
                padding: 14px 12px;
                border-bottom: 1px solid var(--border-light);
                vertical-align: middle;
            }
            .sp-table tbody tr:last-child td { border-bottom: none; }
            .sp-table tbody tr:hover { background: #fdfcfa; }
            .sp-table a { font-weight: 500; }

            /* Badges */
            .sp-badge {
                display: inline-block;
                padding: 3px 10px;
                border-radius: 20px;
                font-size: 11px;
                font-weight: 600;
                letter-spacing: .02em;
            }
            .sp-badge-active { background: #eaf7f0; color: var(--success); }
            .sp-badge-lead { background: #fef9e7; color: #b58a14; }
            .sp-badge-finished { background: #e8e8e8; color: #666; }
            .sp-badge-current { background: #fdf0f2; color: var(--brand-primary); }

            /* Buttons */
            .sp-btn {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 8px 18px;
                border-radius: var(--radius-sm);
                font-size: 13px;
                font-weight: 500;
                border: none;
                cursor: pointer;
                transition: all .2s;
                text-decoration: none;
            }
            .sp-btn-primary {
                background: var(--brand-primary);
                color: #fff;
            }
            .sp-btn-primary:hover {
                background: var(--brand-light);
                color: #fff;
                text-decoration: none;
            }
            .sp-btn-xs { padding: 4px 12px; font-size: 12px; }
            .sp-btn-outline {
                background: transparent;
                border: 1.5px solid var(--border);
                color: var(--text-secondary);
            }
            .sp-btn-outline:hover {
                border-color: var(--brand-primary);
                color: var(--brand-primary);
            }

            /* Modals (override Bootstrap) */
            .modal.fade .modal-dialog { transform: translate(0,0); }
            .modal-open .modal { background-color: rgba(0,0,0,.35); }
            .modal-content {
                border: none;
                border-radius: var(--radius);
                box-shadow: var(--shadow-md);
            }
            .modal-header {
                border-bottom: 1px solid var(--border-light);
                padding: 18px 24px;
            }
            .modal-header .modal-title {
                font-family: var(--font-display);
                font-size: 18px;
                font-weight: 600;
            }
            .modal-body { padding: 24px; }

            /* Form controls */
            .form-control {
                border: 1.5px solid var(--border);
                border-radius: var(--radius-sm);
                padding: 9px 14px;
                font-size: 14px;
                font-family: var(--font-body);
                transition: border-color .2s;
            }
            .form-control:focus {
                border-color: var(--brand-primary);
                box-shadow: 0 0 0 3px rgba(134,39,54,.1);
                outline: none;
            }

            /* Two column grid */
            .sp-grid-2col {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 24px;
            }

            /* Sidebar overlay */
            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,.4);
                z-index: 99;
            }
            .sidebar-overlay.active { display: block; }

            /* ── Modal ──────────────────────────────────────── */
            .sp-modal {
                border-radius: var(--radius, 10px);
                overflow: hidden;
                border: none;
                box-shadow: 0 20px 60px rgba(0,0,0,.15);
            }

            .sp-modal__header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 18px 24px;
                background: var(--brand-pale, #f9edef);
                border-bottom: 1px solid var(--border-color, #e5e7eb);
            }

            .sp-modal__title {
                font-size: 16px;
                font-weight: 700;
                color: #1f2937;
                margin: 0;
                display: flex;
                align-items: center;
            }

            .sp-modal__close {
                background: none;
                border: none;
                font-size: 22px;
                color: #6b7280;
                cursor: pointer;
                padding: 0;
                line-height: 1;
                transition: color .2s;
            }

            .sp-modal__close:hover {
                color: var(--brand-primary, #862736);
            }

            .sp-modal__body {
                padding: 24px;
            }

            .sp-modal__footer {
                display: flex;
                justify-content: flex-end;
                gap: 10px;
                padding: 16px 24px;
                background: #f9fafb;
                border-top: 1px solid var(--border-color, #e5e7eb);
            }

            /* ── Forms ──────────────────────────────────────── */
            .sp-form-group {
                margin-bottom: 18px;
            }

            .sp-label {
                display: block;
                font-size: 13px;
                font-weight: 600;
                color: #374151;
                margin-bottom: 6px;
            }

            .sp-required {
                color: var(--brand-primary, #862736);
            }

            .sp-input {
                width: 100%;
                border: 1.5px solid var(--border-color, #e5e7eb);
                border-radius: 8px;
                padding: 10px 14px;
                font-size: 14px;
                color: #1f2937;
                background: #fff;
                transition: border-color .2s, box-shadow .2s;
            }

            .sp-input:focus {
                border-color: var(--brand-primary, #862736);
                outline: none;
                box-shadow: 0 0 0 3px rgba(134,39,54,.12);
            }

            .sp-input:disabled {
                background: #f9fafb;
                color: #6b7280;
                cursor: not-allowed;
            }

            .sp-input.is-invalid {
                border-color: #dc2626;
                box-shadow: 0 0 0 3px rgba(220,38,38,.1);
            }

            .sp-input.is-valid {
                border-color: #16a34a;
            }

            .sp-textarea {
                resize: vertical;
                min-height: 100px;
            }

            .sp-select {
                appearance: none;
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
                background-position: right 12px center;
                background-repeat: no-repeat;
                background-size: 16px;
                padding-right: 36px;
            }

            .sp-error {
                display: none;
                font-size: 12px;
                color: #dc2626;
                margin-top: 4px;
            }

            .sp-input.is-invalid + .sp-error {
                display: block;
            }

            /* ── Buttons (for modals/forms) ─────────────────── */
            .btn-brand {
                background: var(--brand-primary, #862736);
                color: #fff;
                border: none;
                border-radius: 8px;
                padding: 8px 18px;
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
                transition: background .2s, transform .1s;
            }

            .btn-brand:hover {
                background: var(--brand-dark, #5f1a25);
                color: #fff;
            }

            .btn-outline-brand {
                background: transparent;
                color: var(--brand-primary, #862736);
                border: 1.5px solid var(--brand-primary, #862736);
                border-radius: 8px;
                padding: 7px 16px;
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
                transition: background .2s, color .2s;
            }

            .btn-outline-brand:hover {
                background: var(--brand-pale, #f9edef);
                color: var(--brand-dark, #5f1a25);
            }

            .btn-xs-brand {
                padding: 4px 12px;
                font-size: 12px;
            }

            /* ── File upload preview ────────────────────────── */
            .sp-file-preview {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 12px;
                background: #f9fafb;
                border: 1px solid var(--border-color, #e5e7eb);
                border-radius: 8px;
                margin-top: 8px;
            }

            .sp-file-preview__icon {
                width: 40px;
                height: 40px;
                border-radius: 8px;
                background: var(--brand-pale, #f9edef);
                display: flex;
                align-items: center;
                justify-content: center;
                color: var(--brand-primary, #862736);
                font-size: 16px;
                flex-shrink: 0;
            }

            .sp-file-preview__info {
                flex: 1;
                min-width: 0;
            }

            .sp-file-preview__name {
                font-size: 13px;
                font-weight: 600;
                color: #1f2937;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .sp-file-preview__size {
                font-size: 12px;
                color: #6b7280;
            }

            .sp-file-preview__remove {
                background: none;
                border: none;
                color: #dc2626;
                cursor: pointer;
                font-size: 14px;
                padding: 4px;
                transition: opacity .2s;
            }

            .sp-file-preview__remove:hover {
                opacity: .7;
            }

            /* ── Deduplicated icon helpers ─────────────────── */
            .fa-file-red:before   { content: "\f15b"; }
            .fa-file-red          { color: #862736 !important; font-size: 20px; }

            .fa-clock-red:before  { content: "\f017"; }
            .fa-clock-red         { color: #862736 !important; font-size: 20px; }

            .fa-shopping-cart-red:before { content: "\f07a"; }
            .fa-shopping-cart-red        { color: #862736 !important; font-size: 20px; }

            .fa-bar-chart-red:before { content: "\f080"; }
            .fa-bar-chart-red        { color: #862736 !important; font-size: 20px; }

            /* ── Accessibility: focus-visible ─────────────── */
            .sp-input:focus-visible,
            .sp-select:focus-visible,
            .sp-textarea:focus-visible,
            .btn-brand:focus-visible,
            .btn-outline-brand:focus-visible,
            a:focus-visible,
            button:focus-visible {
                outline: 2px solid var(--brand-primary, #862736);
                outline-offset: 2px;
            }

            :focus:not(:focus-visible) {
                outline: none;
            }

            /* ── Accessibility: screen-reader only ────────── */
            .sr-only {
                position: absolute;
                width: 1px;
                height: 1px;
                padding: 0;
                margin: -1px;
                overflow: hidden;
                clip: rect(0, 0, 0, 0);
                white-space: nowrap;
                border: 0;
            }

            /* ── Brand-pale for variable fallback ───────────── */
            /* Ensure --brand-pale is available as a CSS variable */
            :root {
                --brand-pale: #f9edef;
                --border-color: #e5e7eb;
            }

            /* Animations */
            @keyframes sp-fadeUp {
                from { opacity: 0; transform: translateY(12px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .sp-anim { animation: sp-fadeUp .5s ease forwards; opacity: 0; }
            .sp-d1 { animation-delay: .05s; }
            .sp-d2 { animation-delay: .1s; }
            .sp-d3 { animation-delay: .15s; }
            .sp-d4 { animation-delay: .2s; }
            .sp-d5 { animation-delay: .25s; }
            .sp-d6 { animation-delay: .3s; }

            /* ══════════════════════════════════════════
               RESPONSIVE
            ══════════════════════════════════════════ */
            @media (max-width: 1100px) {
                #sp-main { padding: 28px 24px 60px; }
            }
            @media (max-width: 900px) {
                #sp-sidebar { transform: translateX(-100%); }
                #sp-sidebar.open { transform: translateX(0); }
                #sp-topbar { left: 0; }
                .topbar-hamburger { display: block; }
                #sp-main { margin-left: 0; }
                .sp-grid-2col { grid-template-columns: 1fr; }
            }
            @media (max-width: 600px) {
                #sp-main { padding: 20px 16px 40px; }
            }
        </style>

        @yield('styles')

        <script async>
            window.Laravel = '{{ json_encode(['csrfToken' => csrf_token()]) }}';
        </script>
    </head>
    <body>

        {{-- ════════════════ SIDEBAR ════════════════ --}}
        <aside id="sp-sidebar">
            <div class="sidebar-brand">
                @if(file_exists(public_path('images/logo-white.png')))
                    <img src="{{ asset('images/logo-white.png') }}" alt="Indiemoon">
                @else
                    <div style="width:48px;height:48px;margin:0 auto;background:rgba(255,255,255,.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-feather-alt" style="font-size:22px;color:#fff;"></i>
                    </div>
                @endif
                <div class="sidebar-brand-text">Indiemoon</div>
            </div>

            <div class="sidebar-section-title">{{ trans('site.author-portal.your-book') ?? 'Din bok' }}</div>
            <ul class="sidebar-nav">
                <li>
                    <a href="{{ route('learner.dashboard') }}" 
                       class="nav-item {{ request()->routeIs('learner.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-th-large"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('learner.progress-plan') ?? '#' }}" 
                       class="nav-item {{ request()->routeIs('learner.progress-plan.*') ? 'active' : '' }}">
                        <i class="fas fa-tasks"></i> {{ trans('site.author-portal.progress-plan') ?? 'Fremdriftsplan' }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('learner.project.show', optional($standardProject)->id ?? 0) }}" 
                       class="nav-item {{ request()->routeIs('learner.project.*') ? 'active' : '' }}">
                        <i class="fas fa-book"></i> {{ trans('site.author-portal.book-project') ?? 'Bokprosjekt' }}
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-item {{ request()->routeIs('learner.manuscript.*') ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i> {{ trans('site.author-portal.manuscript') ?? 'Manuskript' }}
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-item {{ request()->routeIs('learner.cover.*') ? 'active' : '' }}">
                        <i class="fas fa-palette"></i> {{ trans('site.author-portal.cover-design') ?? 'Omslag & design' }}
                    </a>
                </li>
            </ul>

            <div class="sidebar-section-title">{{ trans('site.author-portal.economy') ?? 'Økonomi' }}</div>
            <ul class="sidebar-nav">
                <li>
                    @php $hasBookSale = FrontendHelpers::checkIfLearnerHasBookSale()->count() > 0; @endphp
                    <a href="{{ $hasBookSale ? route('learner.book-sale') . '?year=' . FrontendHelpers::getLearnerSaleYear() 
                    : 'javascript:void(0)' }}"  
                        class="nav-item {{ request()->routeIs('learner.sales.*') ? 'active' : '' }}"
                        style="{{ $hasBookSale ? '' : 'pointer-events: none; opacity: 0.6; cursor: not-allowed;' }}">
                        <i class="fas fa-chart-line"></i> {{ trans('site.author-portal-menu.sales') ?? 'Salg' }}
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-item {{ request()->routeIs('learner.inventory.*') ? 'active' : '' }}">
                        <i class="fas fa-boxes"></i> {{ trans('site.author-portal.inventory') ?? 'Lagerstatus' }}
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-item {{ request()->routeIs('learner.invoices.*') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice"></i> {{ trans_choice('site.invoices', 2) ?? 'Fakturaer' }}
                    </a>
                </li>
            </ul>

            <div class="sidebar-section-title">{{ trans('site.author-portal.account') ?? 'Konto' }}</div>
            <ul class="sidebar-nav">
                <li>
                    <a href="#" class="nav-item {{ request()->routeIs('learner.profile.*') ? 'active' : '' }}">
                        <i class="fas fa-user-circle"></i> {{ trans('site.learner.profile-text') ?? 'Profil' }}
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-item {{ request()->routeIs('learner.settings.*') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i> {{ trans('site.settings') ?? 'Innstillinger' }}
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <a href="{{ route('learner.change-portal', 'learner') }}" class="btn-portal">
                    <i class="fas fa-graduation-cap"></i> &nbsp;{{ trans('site.author-portal.back-to-courses') ?? 'Tilbake til kursportalen' }}
                </a>
                <a href="{{ route('auth.logout-get') }}" class="btn-logout"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> {{ trans('site.logout') ?? 'Logg ut' }}
                </a>
                <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" style="display:none;">
                    {{ csrf_field() }}
                </form>
            </div>
        </aside>

        {{-- ════════════════ SIDEBAR OVERLAY (mobile) ════════════════ --}}
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        {{-- ════════════════ TOPBAR ════════════════ --}}
        <header id="sp-topbar">
            <button class="topbar-hamburger" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <nav class="breadcrumbs">
                <a href="{{ route('learner.dashboard') }}"><i class="fas fa-home"></i></a>
                <span class="sep">›</span>
                <a href="{{ route('learner.dashboard') }}">Selvpublisering</a>
                <span class="sep">›</span>
                @yield('breadcrumbs')
            </nav>
            <div class="topbar-right">
                {{-- @include('frontend.partials._notifications_bell', ['fallback' => true]) --}}
                <div class="topbar-avatar">
                    {{ strtoupper(substr(auth()->user()->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? '', 0, 1)) }}
                </div>
            </div>
        </header>

        {{-- ════════════════ MAIN CONTENT ════════════════ --}}
        <main id="sp-main">
            @yield('content')
        </main>

        {{-- ════════════════ ALERTS ════════════════ --}}
        @if($errors->count())
            @php
                $alert_type = session('alert_type', 'danger');
            @endphp
            <div class="alert alert-{{ $alert_type }}" 
                 style="position:fixed;bottom:20px;right:20px;z-index:200;min-width:300px;border-radius:var(--radius);box-shadow:var(--shadow-md);"
                 id="fixed_to_bottom_alert">
                <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                <ul style="margin:0;padding-left:18px;">
                    @foreach($errors->all() as $error)
                        <li>{!! $error !!}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ════════════════ SCRIPTS ════════════════ --}}
        @include('frontend.partials.scripts')
        <script src="/js/lang.js"></script>
        <script>
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            // Service Worker
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/service-worker.js');
                });
            }

            // Form disable on submit
            function disableSubmit(t) {
                let btn = $(t).find('[type=submit]');
                btn.text('');
                btn.append('<i class="fa fa-spinner fa-pulse"></i> Vennligst vent...');
                btn.attr('disabled', 'disabled');
            }

            // Sidebar toggle (mobile)
            $('#sidebarToggle').click(function() {
                $('#sp-sidebar').toggleClass('open');
                $('#sidebarOverlay').toggleClass('active');
            });
            $('#sidebarOverlay').click(function() {
                $('#sp-sidebar').removeClass('open');
                $(this).removeClass('active');
            });

            // Auto-close sidebar on resize to desktop
            $(window).on('resize', function() {
                if (window.innerWidth > 900) {
                    $('#sp-sidebar').removeClass('open');
                    $('#sidebarOverlay').removeClass('active');
                }
            });

            /**
             * SP Forms — Forfatterskolen Selvpublisering
             * Validation, delete modal, and file preview components
             */

            (function() {
                'use strict';

                /* ═══════════════════════════════════════════
                1. FORM VALIDATION
                Bruk: Legg til data-sp-validate på <form>
                Regler via HTML5-attributter: required, minlength, maxlength, pattern, type="email"
                ═══════════════════════════════════════════ */
                document.addEventListener('DOMContentLoaded', function() {

                    document.querySelectorAll('[data-sp-validate]').forEach(function(form) {
                        var inputs = form.querySelectorAll('.sp-input[required], .sp-input[minlength], .sp-input[pattern], .sp-input[type="email"], .sp-input[type="number"]');

                        inputs.forEach(function(input) {
                            // Valider ved blur
                            input.addEventListener('blur', function() {
                                validateField(input);
                            });

                            // Fjern feilmelding ved input
                            input.addEventListener('input', function() {
                                if (input.classList.contains('is-invalid')) {
                                    validateField(input);
                                }
                            });
                        });

                        // Valider ved submit
                        form.addEventListener('submit', function(e) {
                            var isValid = true;
                            inputs.forEach(function(input) {
                                if (!validateField(input)) {
                                    isValid = false;
                                }
                            });

                            if (!isValid) {
                                e.preventDefault();
                                var firstError = form.querySelector('.is-invalid');
                                if (firstError) {
                                    firstError.focus();
                                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                }
                            }
                        });
                    });

                    function validateField(input) {
                        var errorEl = input.parentElement.querySelector('.sp-error') ||
                                    input.nextElementSibling;
                        var message = '';

                        if (input.hasAttribute('required') && !input.value.trim()) {
                            message = 'Dette feltet er påkrevd';
                        } else if (input.hasAttribute('minlength') && input.value.length < parseInt(input.getAttribute('minlength'))) {
                            message = 'Minimum ' + input.getAttribute('minlength') + ' tegn';
                        } else if (input.hasAttribute('maxlength') && input.value.length > parseInt(input.getAttribute('maxlength'))) {
                            message = 'Maksimum ' + input.getAttribute('maxlength') + ' tegn';
                        } else if (input.type === 'email' && input.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value)) {
                            message = 'Ugyldig e-postadresse';
                        } else if (input.type === 'number' && input.hasAttribute('min') && parseFloat(input.value) < parseFloat(input.getAttribute('min'))) {
                            message = 'Minimum verdi er ' + input.getAttribute('min');
                        } else if (input.hasAttribute('pattern') && input.value && !new RegExp(input.getAttribute('pattern')).test(input.value)) {
                            message = input.getAttribute('data-pattern-error') || 'Ugyldig format';
                        }

                        if (message) {
                            input.classList.add('is-invalid');
                            input.classList.remove('is-valid');
                            if (errorEl && errorEl.classList.contains('sp-error')) {
                                errorEl.textContent = message;
                                errorEl.style.display = 'block';
                            }
                            return false;
                        } else {
                            input.classList.remove('is-invalid');
                            if (input.value.trim()) {
                                input.classList.add('is-valid');
                            }
                            if (errorEl && errorEl.classList.contains('sp-error')) {
                                errorEl.textContent = '';
                                errorEl.style.display = 'none';
                            }
                            return true;
                        }
                    }

                    /* ═══════════════════════════════════════════
                    2. DELETE MODAL
                    Bruk: data-sp-delete, data-action, data-title, data-message
                    ═══════════════════════════════════════════ */
                    $(document).on('click', '[data-sp-delete]', function() {
                        var modal = $('#spDeleteModal');
                        var action = $(this).data('action');
                        var title = $(this).data('title') || 'Bekreft sletting';
                        var message = $(this).data('message') || 'Er du sikker på at du vil slette dette elementet? Denne handlingen kan ikke angres.';

                        modal.find('.sp-delete-title').text(title);
                        modal.find('.sp-delete-message').text(message);
                        modal.find('.sp-delete-form').attr('action', action);
                        modal.modal('show');
                    });

                    /* ═══════════════════════════════════════════
                    3. FILE PREVIEW
                    Bruk: data-sp-file-preview="previewContainerId" på file inputs
                    ═══════════════════════════════════════════ */
                    document.querySelectorAll('[data-sp-file-preview]').forEach(function(input) {
                        var previewContainer = document.getElementById(input.dataset.spFilePreview);
                        if (!previewContainer) return;

                        input.addEventListener('change', function() {
                            previewContainer.innerHTML = '';

                            Array.from(this.files).forEach(function(file) {
                                var size = file.size < 1024 * 1024
                                    ? (file.size / 1024).toFixed(1) + ' KB'
                                    : (file.size / (1024 * 1024)).toFixed(1) + ' MB';

                                var icon = getFileIcon(file.name);

                                var preview = document.createElement('div');
                                preview.className = 'sp-file-preview';
                                preview.innerHTML =
                                    '<div class="sp-file-preview__icon"><i class="fa ' + icon + '"></i></div>' +
                                    '<div class="sp-file-preview__info">' +
                                        '<div class="sp-file-preview__name">' + escapeHtml(file.name) + '</div>' +
                                        '<div class="sp-file-preview__size">' + size + '</div>' +
                                    '</div>' +
                                    '<button type="button" class="sp-file-preview__remove" title="Fjern">' +
                                        '<i class="fa fa-times"></i>' +
                                    '</button>';

                                preview.querySelector('.sp-file-preview__remove').addEventListener('click', function() {
                                    preview.remove();
                                    input.value = '';
                                });

                                // Vis bildeforhåndsvisning
                                if (file.type.startsWith('image/')) {
                                    var reader = new FileReader();
                                    reader.onload = function(e) {
                                        preview.querySelector('.sp-file-preview__icon').innerHTML =
                                            '<img src="' + e.target.result + '" style="width:40px;height:40px;object-fit:cover;border-radius:6px">';
                                    };
                                    reader.readAsDataURL(file);
                                }

                                previewContainer.appendChild(preview);
                            });
                        });
                    });

                    function getFileIcon(filename) {
                        var ext = filename.split('.').pop().toLowerCase();
                        var icons = {
                            pdf: 'fa-file-pdf-o', doc: 'fa-file-word-o', docx: 'fa-file-word-o',
                            xls: 'fa-file-excel-o', xlsx: 'fa-file-excel-o',
                            jpg: 'fa-file-image-o', jpeg: 'fa-file-image-o', png: 'fa-file-image-o',
                            gif: 'fa-file-image-o', mp3: 'fa-file-audio-o', wav: 'fa-file-audio-o',
                            epub: 'fa-book', zip: 'fa-file-archive-o',
                            odt: 'fa-file-text-o', indd: 'fa-file-o', mobi: 'fa-book'
                        };
                        return icons[ext] || 'fa-file-o';
                    }

                    function escapeHtml(text) {
                        var div = document.createElement('div');
                        div.appendChild(document.createTextNode(text));
                        return div.innerHTML;
                    }
                });
            })();
        </script>

        @yield('scripts')

        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PBZBPBN2"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
    </body>
</html>
