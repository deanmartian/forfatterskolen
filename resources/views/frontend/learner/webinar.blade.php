@extends('frontend.layouts.course-portal')

@section('page_title', 'Mentormøter &rsaquo; Forfatterskolen')

@section('styles')
<style>
/* ══════════════════════════════════════════════════════════════
   FELLESWEBINARER REDESIGN — scoped under .fw-redesign
   ══════════════════════════════════════════════════════════════ */
.fw-redesign {
    --fw-wine: #862736;
    --fw-wine-hover: #9c2e40;
    --fw-wine-light: rgba(134, 39, 54, 0.08);
    --fw-wine-light-solid: #f4e8ea;
    --fw-cream: #faf8f5;
    --fw-green: #2e7d32;
    --fw-green-bg: #e8f5e9;
    --fw-blue: #1565c0;
    --fw-blue-bg: #e3f2fd;
    --fw-text: #1a1a1a;
    --fw-text-sec: #5a5550;
    --fw-text-muted: #8a8580;
    --fw-border: rgba(0, 0, 0, 0.08);
    --fw-border-strong: rgba(0, 0, 0, 0.12);
    --fw-font: 'Source Sans 3', -apple-system, sans-serif;
    --fw-radius: 10px;
    --fw-radius-lg: 14px;
    font-family: var(--fw-font);
    color: var(--fw-text);
    -webkit-font-smoothing: antialiased;
    padding: 2rem 2.5rem;
    background: #f5f3f0;
    min-height: 100vh;
    max-width: 920px;
    overflow-x: hidden;
    box-sizing: border-box;
}

/* Sidebar toggle — kun synlig på mobil/tablet */
.fw-redesign .fw-sidebar-toggle {
    display: none !important; position: fixed; top: 16px; left: 16px; z-index: 1050;
    width: 50px; height: 50px; border-radius: 14px; border: 2px solid rgba(255,255,255,0.3);
    background: var(--fw-wine); align-items: center; justify-content: center; cursor: pointer;
    box-shadow: 0 4px 16px rgba(134, 39, 54, 0.4), 0 0 0 3px rgba(134, 39, 54, 0.15);
    padding: 0; transition: background 0.15s, box-shadow 0.15s, transform 0.15s;
}
.fw-redesign .fw-sidebar-toggle:hover {
    background: var(--fw-wine-hover);
    box-shadow: 0 6px 24px rgba(134, 39, 54, 0.5), 0 0 0 4px rgba(134, 39, 54, 0.2);
    transform: scale(1.08);
}
.fw-redesign .fw-sidebar-toggle:active { transform: scale(0.95); }
.fw-redesign .fw-sidebar-toggle svg { width: 24px; height: 24px; stroke: #fff; stroke-width: 2.5; }
@media (max-width: 991px) {
    .fw-redesign .fw-sidebar-toggle { display: flex !important; }
}

/* ── PAGE HEADER ── */
.fw-header { margin-bottom: 1.5rem; }
.fw-header h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.25rem; }
.fw-header p { font-size: 0.875rem; color: var(--fw-text-sec); margin: 0; }

/* ── TABS ── */
.fw-tabs {
    display: flex; gap: 0; border-bottom: 2px solid var(--fw-border); margin-bottom: 1.5rem;
    flex-wrap: wrap;
}
.fw-tab {
    padding: 0.7rem 1.15rem; border: none; background: transparent;
    font-family: var(--fw-font); font-size: 0.835rem; font-weight: 500;
    color: var(--fw-text-muted); cursor: pointer; white-space: nowrap;
    position: relative; transition: color 0.15s; text-decoration: none;
}
.fw-tab:hover { color: var(--fw-text); text-decoration: none; }
.fw-tab.active { color: var(--fw-wine); font-weight: 600; }
.fw-tab.active::after {
    content: ''; position: absolute; bottom: -2px; left: 0; right: 0;
    height: 2px; background: var(--fw-wine);
}
.fw-tab__count {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 18px; height: 18px; padding: 0 5px; border-radius: 9px;
    font-size: 0.65rem; font-weight: 600; margin-left: 0.35rem;
}
.fw-tab.active .fw-tab__count { background: var(--fw-wine-light-solid); color: var(--fw-wine); }
.fw-tab:not(.active) .fw-tab__count { background: rgba(0,0,0,0.06); color: var(--fw-text-muted); }

.fw-panel { display: none; }
.fw-panel.active { display: block; }

/* ── INFO BANNER ── */
.fw-info-banner {
    display: flex; align-items: center; gap: 0.75rem; padding: 0.85rem 1.15rem;
    background: var(--fw-cream); border: 1px solid var(--fw-border);
    border-radius: var(--fw-radius); margin-bottom: 1.5rem; font-size: 0.825rem;
    color: var(--fw-text-sec); flex-wrap: wrap; justify-content: space-between;
}
.fw-info-banner__left { display: flex; align-items: center; gap: 0.75rem; }
.fw-info-banner__left svg { width: 18px; height: 18px; flex-shrink: 0; }
.fw-info-banner strong { color: var(--fw-text); }
.fw-auto-reg {
    display: flex; align-items: center; gap: 0.5rem; cursor: pointer;
    white-space: nowrap; font-size: 0.8rem; font-weight: 600; color: var(--fw-wine);
}
.fw-auto-reg input { accent-color: var(--fw-wine); width: 16px; height: 16px; }

/* ── MONTH LABEL ── */
.fw-month {
    font-size: 0.7rem; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase;
    color: var(--fw-text-muted); margin-bottom: 0.6rem; margin-top: 1.5rem; padding-left: 0.25rem;
}
.fw-month:first-child { margin-top: 0; }

/* ── WEBINAR LIST (kommende) ── */
.fw-list {
    background: #fff; border: 1px solid var(--fw-border);
    border-radius: var(--fw-radius-lg); overflow: hidden; margin-bottom: 0.5rem;
}
.fw-item {
    display: flex; align-items: center; gap: 1rem; padding: 0.85rem 1.25rem;
    border-bottom: 1px solid var(--fw-border); transition: background 0.1s;
}
.fw-item:last-child { border-bottom: none; }
.fw-item:hover { background: rgba(0,0,0,0.015); }
.fw-item--next { background: rgba(134, 39, 54, 0.02); border-left: 3px solid var(--fw-wine); }

/* Date */
.fw-item__date { text-align: center; min-width: 42px; flex-shrink: 0; }
.fw-item__day { font-size: 1.2rem; font-weight: 700; line-height: 1; }
.fw-item__month-label { font-size: 0.6rem; font-weight: 600; text-transform: uppercase; margin-top: 2px; }
.fw-item--next .fw-item__day,
.fw-item--next .fw-item__month-label { color: var(--fw-wine); }
.fw-item:not(.fw-item--next) .fw-item__day { color: var(--fw-text); }
.fw-item:not(.fw-item--next) .fw-item__month-label { color: var(--fw-text-muted); }

/* Avatar */
.fw-item__avatar {
    width: 40px; height: 40px; border-radius: 50%;
    background: linear-gradient(135deg, #e8e2da, #d4cec6); flex-shrink: 0;
    overflow: hidden; display: flex; align-items: center; justify-content: center;
}
.fw-item__avatar img { width: 100%; height: 100%; object-fit: cover; }

/* Info */
.fw-item__info { flex: 1; min-width: 0; }
.fw-item__name { font-size: 0.875rem; font-weight: 600; color: var(--fw-text); margin-bottom: 0.1rem; }
.fw-item__topic { font-size: 0.78rem; color: var(--fw-text-muted); overflow: hidden; text-overflow: ellipsis; }

/* Time + badge */
.fw-item__time { font-size: 0.75rem; color: var(--fw-text-muted); white-space: nowrap; text-align: right; flex-shrink: 0; }
.fw-item__badge {
    display: inline-block; font-size: 0.6rem; font-weight: 600;
    padding: 0.15rem 0.45rem; border-radius: 3px; margin-top: 0.2rem;
}
.fw-item__badge--tomorrow { background: var(--fw-green-bg); color: var(--fw-green); }
.fw-item__badge--live { background: #c62828; color: #fff; animation: fw-pulse 2s ease-in-out infinite; }
@keyframes fw-pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }

/* ── BUTTON STATES ── */
/* Meld på */
.fw-item__signup {
    display: inline-flex; align-items: center; gap: 0.3rem;
    font-size: 0.72rem; font-weight: 600; color: var(--fw-wine);
    padding: 0.35rem 0.75rem; background: transparent;
    border: 1px solid var(--fw-wine); border-radius: 5px;
    cursor: pointer; font-family: var(--fw-font); transition: all 0.15s;
    white-space: nowrap; flex-shrink: 0; text-decoration: none;
}
.fw-item__signup:hover { background: var(--fw-wine); color: #fff; text-decoration: none; }

/* Påmeldt */
.fw-item__enrolled {
    display: inline-flex; align-items: center; gap: 0.3rem;
    font-size: 0.72rem; font-weight: 600; color: var(--fw-green);
    padding: 0.35rem 0.75rem; background: var(--fw-green-bg);
    border-radius: 5px; border: none; cursor: default; white-space: nowrap; flex-shrink: 0;
}
.fw-item__enrolled svg { width: 12px; height: 12px; }

/* Se webinar → (aktiv lenke) */
.fw-item__join {
    display: inline-flex; align-items: center; gap: 0.35rem;
    font-size: 0.78rem; font-weight: 600; color: #fff;
    padding: 0.4rem 0.85rem; background: var(--fw-wine);
    border-radius: 5px; text-decoration: none; transition: background 0.15s;
    white-space: nowrap; flex-shrink: 0;
}
.fw-item__join:hover { background: var(--fw-wine-hover); text-decoration: none; color: #fff; }

/* Se reprise (etter webinar) */
.fw-item__replay {
    display: inline-flex; align-items: center; gap: 0.35rem;
    padding: 0.4rem 0.9rem; border: 1px solid var(--fw-wine);
    border-radius: 6px; font-size: 0.78rem; font-weight: 600;
    color: var(--fw-wine); text-decoration: none; white-space: nowrap;
    transition: all 0.15s; flex-shrink: 0;
}
.fw-item__replay:hover { background: var(--fw-wine); color: #fff; text-decoration: none; }
.fw-item__replay svg { width: 14px; height: 14px; fill: currentColor; }

/* Påmelding kommer */
.fw-item__pending {
    display: inline-block; font-size: 0.72rem; font-weight: 500;
    color: var(--fw-text-muted); padding: 0.35rem 0.75rem;
    background: rgba(0,0,0,0.03); border-radius: 5px;
    white-space: nowrap; flex-shrink: 0;
}

/* ── ENROLL AREA ── */
.fw-item__enroll { flex-shrink: 0; }

/* ── REPRISE LIST (tab 2) ── */
.fw-search-bar { display: flex; gap: 0.75rem; margin-bottom: 1.25rem; }
.fw-search-bar__input {
    flex: 1; padding: 0.6rem 1rem 0.6rem 2.25rem;
    border: 1px solid var(--fw-border-strong); border-radius: 8px;
    font-family: var(--fw-font); font-size: 0.85rem; color: var(--fw-text);
    background: #fff url("data:image/svg+xml,%3Csvg width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%238a8580' stroke-width='2' stroke-linecap='round' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E") no-repeat 0.75rem center;
    outline: none; transition: border-color 0.15s;
}
.fw-search-bar__input:focus { border-color: var(--fw-wine); }
.fw-search-bar__input::placeholder { color: var(--fw-text-muted); }
.fw-search-bar__clear {
    display: flex; align-items: center; justify-content: center;
    width: 36px; height: 36px; border-radius: 8px; border: 1px solid var(--fw-border-strong);
    background: #fff; color: var(--fw-text-muted); font-size: 1rem;
    text-decoration: none; flex-shrink: 0; transition: all 0.15s;
}
.fw-search-bar__clear:hover { border-color: var(--fw-wine); color: var(--fw-wine); text-decoration: none; }

.fw-reprise-list {
    background: #fff; border: 1px solid var(--fw-border);
    border-radius: var(--fw-radius-lg); overflow: hidden;
}
.fw-reprise-item {
    display: flex; align-items: center; gap: 1rem;
    padding: 0.85rem 1.25rem; border-bottom: 1px solid var(--fw-border); transition: background 0.1s;
}
.fw-reprise-item:last-child { border-bottom: none; }
.fw-reprise-item:hover { background: rgba(0,0,0,0.015); }
.fw-reprise-item__date { font-size: 0.78rem; color: var(--fw-text-muted); min-width: 80px; flex-shrink: 0; }
.fw-reprise-item__info { flex: 1; min-width: 0; }
.fw-reprise-item__name { font-size: 0.875rem; font-weight: 600; color: var(--fw-text); margin-bottom: 0.1rem; }
.fw-reprise-item__topic { font-size: 0.78rem; color: var(--fw-text-muted); overflow: hidden; text-overflow: ellipsis; }
.fw-reprise-item__play {
    display: inline-flex; align-items: center; gap: 0.35rem;
    padding: 0.4rem 0.9rem; border: 1px solid var(--fw-wine);
    border-radius: 6px; font-size: 0.78rem; font-weight: 600;
    color: var(--fw-wine); text-decoration: none; white-space: nowrap;
    transition: all 0.15s; flex-shrink: 0;
}
.fw-reprise-item__play:hover { background: var(--fw-wine); color: #fff; text-decoration: none; }
.fw-reprise-item__play svg { width: 14px; height: 14px; fill: currentColor; }

/* ── PAGINATION ── */
.fw-pagination {
    display: flex; align-items: center; justify-content: center;
    gap: 0.35rem; margin-top: 1.5rem; flex-wrap: wrap;
}
.fw-pagination .pagination { gap: 0.25rem; margin: 0; }
.fw-pagination .page-link {
    padding: 0.4rem 0.75rem; border: 1px solid var(--fw-border-strong);
    border-radius: 6px; background: #fff; font-family: var(--fw-font);
    font-size: 0.8rem; color: var(--fw-text-sec); transition: all 0.15s;
    text-decoration: none;
}
.fw-pagination .page-link:hover { border-color: var(--fw-wine); color: var(--fw-wine); }
.fw-pagination .page-item.active .page-link { background: var(--fw-wine); border-color: var(--fw-wine); color: #fff; }
.fw-pagination .page-item.disabled .page-link { opacity: 0.4; cursor: not-allowed; }

/* ── VIDEO MODAL ── */
.fw-modal .modal-content { border: none; border-radius: 14px; overflow: hidden; box-shadow: 0 24px 64px rgba(0,0,0,0.2); }
.fw-modal .modal-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--fw-border);
}
.fw-modal .modal-header h5 { font-size: 1.1rem; font-weight: 700; color: var(--fw-text); margin: 0; }
.fw-modal .modal-body { padding: 0; }
.fw-modal .modal-body iframe,
.fw-modal .modal-body video { width: 100%; display: block; }
.fw-modal .btn-close { background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--fw-text-muted); }

/* ── EMPTY STATE ── */
.fw-empty {
    text-align: center; padding: 3rem 1.5rem; background: #fff;
    border: 1px solid var(--fw-border); border-radius: var(--fw-radius-lg);
}
.fw-empty svg { width: 48px; height: 48px; margin-bottom: 1rem; }
.fw-empty__title { font-size: 1rem; font-weight: 600; margin-bottom: 0.25rem; }
.fw-empty__text { font-size: 0.85rem; color: var(--fw-text-muted); }

/* ══ RESPONSIVE ══ */
@media (max-width: 768px) {
    .fw-redesign { padding: 1.25rem 0.75rem; padding-top: 4.5rem; }
    .fw-header h1 { font-size: 1.25rem; }
    .fw-info-banner { flex-direction: column; gap: 0.5rem; align-items: flex-start; }
    .fw-auto-reg { margin-top: 0.25rem; }
    .fw-item { flex-wrap: wrap; gap: 0.5rem; padding: 0.75rem 1rem; }
    .fw-item__time { order: 5; width: 100%; text-align: left; padding-left: 58px; }
    .fw-item__enroll { order: 6; width: 100%; padding-left: 58px; }
    .fw-search-bar { flex-direction: column; }
    .fw-reprise-item { flex-wrap: wrap; gap: 0.5rem; }
    .fw-reprise-item__play { margin-left: auto; }
}

@media (max-width: 480px) {
    .fw-redesign { padding: 1rem 0.5rem; padding-top: 4.25rem; }
    .fw-header h1 { font-size: 1.1rem; }
    .fw-header p { font-size: 0.8rem; }
    .fw-item__avatar { width: 34px; height: 34px; }
    .fw-item__name { font-size: 0.82rem; }
    .fw-item__topic { font-size: 0.72rem; }
    .fw-item__time { padding-left: 50px; }
    .fw-item__enroll { padding-left: 50px; }
    .fw-reprise-item__date { min-width: 70px; font-size: 0.72rem; }
    .fw-reprise-item__name { font-size: 0.82rem; }
    .fw-tabs { gap: 0; }
    .fw-tab { padding: 0.6rem 0.75rem; font-size: 0.78rem; }
}
</style>
@stop

@section('content')
@php
    $months_no = ['Jan','Feb','Mar','Apr','Mai','Jun','Jul','Aug','Sep','Okt','Nov','Des'];
    $months_full = ['Januar','Februar','Mars','April','Mai','Juni','Juli','August','September','Oktober','November','Desember'];
    $now = \Carbon\Carbon::now();
    $activeTab = Request::input('tab', 'kommende');

    // Hent items fra paginator
    $webinarItems = collect();
    if (isset($subscriptionWebinars) && method_exists($subscriptionWebinars, 'items')) {
        $webinarItems = collect($subscriptionWebinars->items());
    } elseif (isset($subscriptionWebinars) && is_iterable($subscriptionWebinars)) {
        $webinarItems = collect($subscriptionWebinars);
    }

    // Grupper kommende webinarer etter måned
    $groupedWebinars = collect();
    if ($webinarItems->count() > 0) {
        $groupedWebinars = $webinarItems->groupBy(function ($w) use ($months_full) {
            $d = \Carbon\Carbon::parse($w->start_date);
            return $months_full[$d->month - 1] . ' ' . $d->year;
        });
    }

    // Finn neste webinar (for highlight)
    $nextWebinar = null;
    foreach ($webinarItems as $w) {
        $sd = \Carbon\Carbon::parse($w->start_date);
        if ($sd->isFuture() || $sd->isToday()) {
            $nextWebinar = $w;
            break;
        }
    }
@endphp

<div class="fw-redesign">

    {{-- Sidebar toggle --}}
    <button type="button" id="fwSidebarToggle" class="fw-sidebar-toggle" data-sidebar-toggle aria-label="Vis/skjul meny">
        <svg viewBox="0 0 24 24" fill="none" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>

    {{-- Header --}}
    <div class="fw-header">
        <h1>Mentormøter</h1>
        <p>Hver mandag kl. 20:00 med kjente forfattere og redaktører.</p>
    </div>

    {{-- Tabs --}}
    <div class="fw-tabs">
        <a href="{{ route('learner.webinar') }}" class="fw-tab {{ $activeTab !== 'replay' ? 'active' : '' }}">
            Kommende
            @if(isset($subscriptionWebinars) && method_exists($subscriptionWebinars, 'total'))
                <span class="fw-tab__count">{{ $subscriptionWebinars->total() }}</span>
            @elseif(isset($subscriptionWebinars) && count($subscriptionWebinars) > 0)
                <span class="fw-tab__count">{{ count($subscriptionWebinars) }}</span>
            @endif
        </a>
        <a href="{{ route('learner.webinar') }}?tab=replay" class="fw-tab {{ $activeTab === 'replay' ? 'active' : '' }}">
            Repriser
            @if(isset($replayWebinars) && $replayWebinars->total() > 0)
                <span class="fw-tab__count">{{ $replayWebinars->total() }}+</span>
            @endif
        </a>
    </div>

    {{-- ═══════════ TAB 1: KOMMENDE FELLESWEBINARER ═══════════ --}}
    <div class="fw-panel {{ $activeTab !== 'replay' ? 'active' : '' }}" id="panel-kommende">

        {{-- Info banner --}}
        <div class="fw-info-banner">
            <div class="fw-info-banner__left">
                <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                <span>Mandager kl. 20:00 (unntatt helligdager og ferier). Trykker du på «automatisk påmelding», vil du få en e-post kvelden før med lenke til webinaret.</span>
            </div>
            <label class="fw-auto-reg">
                <input type="checkbox" id="autoRegisterCheckbox" @if(Auth::user()->userAutoRegisterToCourseWebinar) checked @endif>
                Automatisk påmelding
            </label>
        </div>

        @if($groupedWebinars->count() > 0)
            @foreach($groupedWebinars as $monthLabel => $webinars)
                <div class="fw-month">{{ $monthLabel }}</div>
                <div class="fw-list">
                    @foreach($webinars as $webinar)
                        @php
                            $startDate = \Carbon\Carbon::parse($webinar->start_date);
                            $thirtyMinBefore = $startDate->copy()->subMinutes(30); // 19:30 samme dag
                            $oneHourAfter = $startDate->copy()->addHour(); // 21:00
                            $isNext = ($nextWebinar && $webinar->id === $nextWebinar->id);
                            $isRegistered = \App\Http\FrontendHelpers::checkIfWebinarRegistrant($webinar->id, Auth::user()->id);
                            $joinUrl = $isRegistered ? \App\Http\FrontendHelpers::getWebinarJoinURL($webinar->id, Auth::user()->id) : null;

                            // Badge-logikk
                            $badgeText = '';
                            $badgeClass = '';
                            if ($startDate->isToday()) {
                                if ($now->lt($startDate)) {
                                    $badgeText = 'I dag';
                                    $badgeClass = 'fw-item__badge--tomorrow';
                                } else {
                                    $badgeText = '● Direkte';
                                    $badgeClass = 'fw-item__badge--live';
                                }
                            } elseif ($startDate->isTomorrow()) {
                                $badgeText = 'I morgen';
                                $badgeClass = 'fw-item__badge--tomorrow';
                            }
                        @endphp

                        <div class="fw-item {{ $isNext ? 'fw-item--next' : '' }}">
                            <div class="fw-item__date">
                                <div class="fw-item__day">{{ $startDate->format('j') }}</div>
                                <div class="fw-item__month-label">{{ $months_no[$startDate->month - 1] }}</div>
                            </div>

                            <div class="fw-item__avatar">
                                @if($webinar->image)
                                    <img src="https://www.forfatterskolen.no/{{ $webinar->image }}" alt="{{ $webinar->title }}">
                                @endif
                            </div>

                            <div class="fw-item__info">
                                <div class="fw-item__name">{{ $webinar->title }}</div>
                                @if($webinar->description)
                                    <div class="fw-item__topic">{{ $webinar->description }}</div>
                                @endif
                            </div>

                            <div class="fw-item__time">
                                Kl. {{ $startDate->format('H:i') }}
                                @if($badgeText)
                                    <div><span class="fw-item__badge {{ $badgeClass }}">{{ $badgeText }}</span></div>
                                @endif
                            </div>

                            <div class="fw-item__enroll">
                                {{--
                                    TIDSLINJE (eksempel mandag 20:00):
                                    ─────────────────────────────────
                                    Søndag  20:30  → "Se webinar →" (lenke aktiv)
                                    Mandag  20:00  → Webinaret starter (lenke aktiv)
                                    Mandag  21:00  → "Se webinar →" forsvinner, "Se reprise" vises
                                --}}
                                @if(!$isRegistered)
                                    {{-- Ikke påmeldt — vis registreringsknapp --}}
                                    @if($webinar->link)
                                        <a href="{{ route('learner.webinar.register', [$webinar->link, $webinar->id]) }}" class="fw-item__signup webinarRegister">
                                            Meld på
                                        </a>
                                    @else
                                        <span class="fw-item__pending">Påmelding kommer</span>
                                    @endif
                                @elseif($now->between($thirtyMinBefore, $oneHourAfter))
                                    {{-- Påmeldt + vindu åpent: 30 min før start til 1 time etter --}}
                                    <a href="{{ $joinUrl ?: ($webinar->link ?: '#') }}" class="fw-item__join" target="_blank">
                                        Se webinar →
                                    </a>
                                @elseif($now->isAfter($oneHourAfter))
                                    {{-- Etter mandag 21:00: vis reprise --}}
                                    <a href="{{ $webinar->link ?: '#' }}" class="fw-item__replay" target="_blank">
                                        <svg viewBox="0 0 16 16"><polygon points="3,1 13,8 3,15"/></svg>
                                        Se reprise
                                    </a>
                                @else
                                    {{-- Påmeldt, venter på vindu --}}
                                    <span class="fw-item__enrolled">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                                        Påmeldt
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach

            <div class="fw-pagination">
                {{ $subscriptionWebinars->appends(request()->except('page'))->links('pagination.custom-pagination') }}
            </div>
        @else
            <div class="fw-empty">
                <svg viewBox="0 0 24 24" fill="none" stroke="#8a8580" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <div class="fw-empty__title">Ingen kommende webinarer</div>
                <div class="fw-empty__text">Nye webinarer publiseres fortløpende.</div>
            </div>
        @endif
    </div>

    {{-- ═══════════ TAB 2: REPRISER ═══════════ --}}
    <div class="fw-panel {{ $activeTab === 'replay' ? 'active' : '' }}" id="panel-reprise">

        <form class="fw-search-bar" method="get" action="{{ route('learner.webinar') }}" onsubmit="if(!this.search_replay.value.trim()){window.location='{{ route('learner.webinar') }}?tab=replay';return false;}">
            <input type="hidden" name="tab" value="replay">
            <input type="text" class="fw-search-bar__input" name="search_replay"
                   value="{{ Request::input('search_replay') }}"
                   placeholder="Søk etter mentor, tema eller dato...">
            @if(Request::input('search_replay'))
                <a href="{{ route('learner.webinar') }}?tab=replay" class="fw-search-bar__clear" title="Nullstill søk">✕</a>
            @endif
        </form>

        @if(isset($replayWebinars) && $replayWebinars->count() > 0)
            <div class="fw-reprise-list">
                @foreach($replayWebinars as $replay)
                    <div class="fw-reprise-item">
                        <span class="fw-reprise-item__date">
                            {{ $replay->date ? \App\Http\FrontendHelpers::formatDate($replay->date) : '' }}
                        </span>
                        <div class="fw-reprise-item__info">
                            <div class="fw-reprise-item__name">{{ $replay->title }}</div>
                            @if($replay->description)
                                <div class="fw-reprise-item__topic">{{ $replay->description }}</div>
                            @endif
                        </div>
                        @if(!Auth::user()->isDisabled)
                            <a href="#" class="fw-reprise-item__play videoBtn"
                               data-bs-toggle="modal" data-bs-target="#fwVideoModal"
                               data-record="{{ json_encode($replay) }}">
                                <svg viewBox="0 0 16 16"><polygon points="3,1 13,8 3,15"/></svg>
                                Se reprise
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="fw-pagination">
                {{ $replayWebinars->appends(Request::all())->links('pagination.custom-pagination') }}
            </div>
        @else
            <div class="fw-empty">
                <svg viewBox="0 0 24 24" fill="none" stroke="#8a8580" stroke-width="1.5" stroke-linecap="round"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                <div class="fw-empty__title">Ingen repriser funnet</div>
                <div class="fw-empty__text">
                    @if(Request::input('search_replay'))
                        Prøv et annet søkeord.
                    @else
                        Repriser blir tilgjengelig etter hvert webinar.
                    @endif
                </div>
            </div>
        @endif
    </div>

</div>

{{-- ═══ Suksess-modal (registrering) ═══ --}}
<div id="submitSuccessModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-body text-center" style="padding: 2rem;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="position: absolute; top: 0.75rem; right: 0.75rem;"></button>
                <div style="color: #2e7d32; font-size: 2rem; margin-bottom: 0.5rem;">✓</div>
                <p style="font-size: 0.9rem; color: #1a1a1a; margin: 0;">
                    {{ trans('site.learner.webinar-register-success') }}
                </p>
            </div>
        </div>
    </div>
</div>

{{-- ═══ Video-modal (repriser) ═══ --}}
<div id="fwVideoModal" class="modal fade fw-modal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Lukk"></button>
            </div>
            <div class="modal-body">
                <div id="fw-video-container" style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;">
                </div>
                <style>#fw-video-container iframe{position:absolute;top:0;left:0;width:100%!important;height:100%!important;border:none;}</style>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script>
    // Webinar registrering — spinner
    let translations = {
        pleaseWait: "{{ trans('site.please-wait') }}"
    };
    $(".webinarRegister").click(function () {
        let btn = $(this);
        btn.text('');
        btn.append('<i class="fa fa-spinner fa-pulse"></i> ' + translations.pleaseWait);
        btn.css('pointer-events', 'none');
    });

    // Suksess-modal etter registrering
    @if (Session::has('success'))
        $('#submitSuccessModal').modal('show');
    @endif

    // Video-modal for repriser
    $(".videoBtn").click(function () {
        let modal = $("#fwVideoModal");
        let record = $(this).data('record');
        modal.find(".modal-title").text(record.title);
        modal.find('#fw-video-container').html(record.lesson_content);
    });

    // Tøm video ved lukking
    $('#fwVideoModal').on('hidden.bs.modal', function () {
        $('#fw-video-container').html('');
    });

    // Auto-registrering toggle
    $('#autoRegisterCheckbox').change(function () {
        $.ajax({
            type: 'POST',
            url: '/account/webinar-auto-register-update',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: { 'auto_renew': $(this).prop('checked') ? 1 : 0 },
            success: function (data) {}
        });
    });

    // Auto-kollaps sidebar på smale skjermer
    (function () {
        setTimeout(function () {
            if (document.documentElement.clientWidth < 1026) {
                var sidebar = document.getElementById('sidebar');
                if (sidebar) {
                    sidebar.classList.remove('sidebar-visible');
                    document.body.classList.remove('sidebar-open');
                    var mc = document.getElementById('main-container');
                    if (mc) mc.classList.remove('enlarge');
                }
            }
        }, 150);
    })();
</script>
@stop
