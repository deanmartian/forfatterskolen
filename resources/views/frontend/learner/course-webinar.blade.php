@extends('frontend.layouts.course-portal')

@section('title')
    <title>Kurswebinarer &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
<style>
/* ══════════════════════════════════════════════════════════════
   KURSWEBINARER REDESIGN — scoped under .cw-redesign
   ══════════════════════════════════════════════════════════════ */
.cw-redesign {
    --cw-wine: #862736;
    --cw-wine-hover: #9c2e40;
    --cw-wine-light-solid: #f4e8ea;
    --cw-cream: #faf8f5;
    --cw-green: #2e7d32;
    --cw-green-bg: #e8f5e9;
    --cw-blue: #1565c0;
    --cw-blue-bg: #e3f2fd;
    --cw-text: #1a1a1a;
    --cw-text-sec: #5a5550;
    --cw-text-muted: #8a8580;
    --cw-border: rgba(0, 0, 0, 0.08);
    --cw-border-strong: rgba(0, 0, 0, 0.12);
    --cw-radius: 10px;
    --cw-radius-lg: 14px;
    color: var(--cw-text);
    -webkit-font-smoothing: antialiased;
    max-width: 880px;
    box-sizing: border-box;
}

.cw-redesign *, .cw-redesign *::before, .cw-redesign *::after { box-sizing: border-box; }

/* ── PAGE HEADER ── */
.cw-header { margin-bottom: 1.5rem; }
.cw-header h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.25rem; }
.cw-header p { font-size: 0.875rem; color: var(--cw-text-sec); margin: 0; }

/* ── TABS ── */
.cw-tabs { display: flex; gap: 0; border-bottom: 2px solid var(--cw-border); margin-bottom: 1.5rem; }
.cw-tab {
    padding: 0.7rem 1.15rem; border: none; background: transparent;
    font-size: 0.835rem; font-weight: 500; color: var(--cw-text-muted);
    cursor: pointer; white-space: nowrap; position: relative; transition: color 0.15s;
}
.cw-tab:hover { color: var(--cw-text); }
.cw-tab.active { color: var(--cw-wine); font-weight: 600; }
.cw-tab.active::after {
    content: ''; position: absolute; bottom: -2px; left: 0; right: 0;
    height: 2px; background: var(--cw-wine);
}
.cw-tab__count {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 18px; height: 18px; padding: 0 5px; border-radius: 9px;
    font-size: 0.65rem; font-weight: 600; margin-left: 0.35rem;
}
.cw-tab.active .cw-tab__count { background: var(--cw-wine-light-solid); color: var(--cw-wine); }
.cw-tab:not(.active) .cw-tab__count { background: rgba(0,0,0,0.06); color: var(--cw-text-muted); }

.cw-panel { display: none; }
.cw-panel.active { display: block; }

/* ── INFO BANNER ── */
.cw-info-banner {
    display: flex; align-items: center; gap: 0.75rem; padding: 0.85rem 1.15rem;
    background: var(--cw-cream); border: 1px solid var(--cw-border);
    border-radius: var(--cw-radius); margin-bottom: 1.5rem; font-size: 0.825rem;
    color: var(--cw-text-sec);
}
.cw-info-banner svg { width: 18px; height: 18px; flex-shrink: 0; }
.cw-info-banner strong { color: var(--cw-text); }

/* ── FILTER BAR ── */
.cw-filter-bar { display: flex; gap: 0.75rem; margin-bottom: 1.25rem; align-items: center; }
.cw-filter-bar__select {
    padding: 0.55rem 2rem 0.55rem 0.85rem; border: 1px solid var(--cw-border-strong);
    border-radius: 8px; font-size: 0.825rem; color: var(--cw-text-sec);
    background: #fff; cursor: pointer; appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1.5L6 6.5L11 1.5' stroke='%238a8580' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 0.75rem center;
}
.cw-filter-bar__search {
    flex: 1; max-width: 300px; padding: 0.55rem 1rem 0.55rem 2.25rem;
    border: 1px solid var(--cw-border-strong); border-radius: 8px;
    font-size: 0.825rem; color: var(--cw-text); background: #fff;
    background-image: url("data:image/svg+xml,%3Csvg width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%238a8580' stroke-width='2' stroke-linecap='round' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: 0.75rem center; outline: none;
}
.cw-filter-bar__search:focus { border-color: var(--cw-wine); }
.cw-filter-bar__search::placeholder { color: var(--cw-text-muted); }

/* ── MONTH LABEL ── */
.cw-month-label {
    font-size: 0.7rem; font-weight: 600; letter-spacing: 1.5px;
    text-transform: uppercase; color: var(--cw-text-muted);
    margin-bottom: 0.6rem; margin-top: 1.5rem; padding-left: 0.25rem;
}
.cw-month-label:first-child { margin-top: 0; }

/* ── WEBINAR LIST ── */
.cw-webinar-list {
    background: #fff; border: 1px solid var(--cw-border);
    border-radius: var(--cw-radius-lg); overflow: hidden; margin-bottom: 0.5rem;
}
.cw-webinar-item {
    display: flex; align-items: center; gap: 1rem;
    padding: 0.85rem 1.25rem; border-bottom: 1px solid var(--cw-border);
    transition: background 0.1s;
}
.cw-webinar-item:last-child { border-bottom: none; }
.cw-webinar-item:hover { background: rgba(0,0,0,0.015); }
.cw-webinar-item--next { background: rgba(134, 39, 54, 0.02); border-left: 3px solid var(--cw-wine); }

.cw-webinar-item__date { text-align: center; min-width: 42px; flex-shrink: 0; }
.cw-webinar-item__day { font-size: 1.2rem; font-weight: 700; line-height: 1; }
.cw-webinar-item__month-label { font-size: 0.6rem; font-weight: 600; text-transform: uppercase; margin-top: 2px; }
.cw-webinar-item--next .cw-webinar-item__day,
.cw-webinar-item--next .cw-webinar-item__month-label { color: var(--cw-wine); }
.cw-webinar-item:not(.cw-webinar-item--next) .cw-webinar-item__day { color: var(--cw-text); }
.cw-webinar-item:not(.cw-webinar-item--next) .cw-webinar-item__month-label { color: var(--cw-text-muted); }

.cw-webinar-item__info { flex: 1; min-width: 0; }
.cw-webinar-item__name { font-size: 0.875rem; font-weight: 600; color: var(--cw-text); margin-bottom: 0.1rem; }
.cw-webinar-item__topic { font-size: 0.78rem; color: var(--cw-text-muted); }

.cw-webinar-item__course-tag {
    font-size: 0.6rem; font-weight: 600; padding: 0.15rem 0.5rem;
    border-radius: 3px; background: var(--cw-blue-bg); color: var(--cw-blue);
    white-space: nowrap; flex-shrink: 0;
}

.cw-webinar-item__time { font-size: 0.75rem; color: var(--cw-text-muted); white-space: nowrap; flex-shrink: 0; }

.cw-webinar-item__status {
    flex-shrink: 0; font-size: 0.72rem; font-weight: 600;
    padding: 0.3rem 0.65rem; border-radius: 5px; white-space: nowrap;
    display: inline-flex; align-items: center; gap: 0.3rem; text-decoration: none;
}
.cw-webinar-item__status--auto { background: var(--cw-green-bg); color: var(--cw-green); cursor: default; }
.cw-webinar-item__status--join { background: var(--cw-wine); color: #fff; transition: background 0.15s; }
.cw-webinar-item__status--join:hover { background: var(--cw-wine-hover); color: #fff; text-decoration: none; }
.cw-webinar-item__status--pending { background: rgba(0,0,0,0.04); color: var(--cw-text-muted); cursor: default; }
.cw-webinar-item__status--register {
    background: transparent; color: var(--cw-wine); border: 1px solid var(--cw-wine);
    transition: all 0.15s;
}
.cw-webinar-item__status--register:hover { background: var(--cw-wine); color: #fff; text-decoration: none; }

/* ── REPRISE LIST ── */
.cw-reprise-list {
    background: #fff; border: 1px solid var(--cw-border);
    border-radius: var(--cw-radius-lg); overflow: hidden;
}
.cw-reprise-item {
    display: flex; align-items: center; gap: 1rem;
    padding: 0.85rem 1.25rem; border-bottom: 1px solid var(--cw-border);
    transition: background 0.1s;
}
.cw-reprise-item:last-child { border-bottom: none; }
.cw-reprise-item:hover { background: rgba(0,0,0,0.015); }
.cw-reprise-item__date { font-size: 0.78rem; color: var(--cw-text-muted); min-width: 80px; flex-shrink: 0; }
.cw-reprise-item__info { flex: 1; min-width: 0; }
.cw-reprise-item__name { font-size: 0.875rem; font-weight: 600; color: var(--cw-text); margin-bottom: 0.1rem; }
.cw-reprise-item__topic { font-size: 0.78rem; color: var(--cw-text-muted); }
.cw-reprise-item__play {
    display: inline-flex; align-items: center; gap: 0.35rem;
    padding: 0.4rem 0.9rem; border: 1px solid var(--cw-wine); border-radius: 6px;
    font-size: 0.78rem; font-weight: 600; color: var(--cw-wine);
    text-decoration: none; white-space: nowrap; transition: all 0.15s; flex-shrink: 0;
}
.cw-reprise-item__play:hover { background: var(--cw-wine); color: #fff; text-decoration: none; }
.cw-reprise-item__play svg { width: 14px; height: 14px; fill: currentColor; }

/* ── PAGINATION ── */
.cw-pagination { display: flex; align-items: center; justify-content: center; gap: 0.35rem; margin-top: 1.5rem; }
.cw-pagination__info { font-size: 0.78rem; color: var(--cw-text-muted); margin-right: 1rem; }
.cw-pagination a {
    padding: 0.4rem 0.75rem; border: 1px solid var(--cw-border-strong);
    border-radius: 6px; background: #fff; font-size: 0.8rem; color: var(--cw-text-sec);
    text-decoration: none; transition: all 0.15s;
}
.cw-pagination a:hover { border-color: var(--cw-wine); color: var(--cw-wine); }
.cw-pagination .active span {
    padding: 0.4rem 0.75rem; border: 1px solid var(--cw-wine);
    border-radius: 6px; background: var(--cw-wine); font-size: 0.8rem; color: #fff;
}
.cw-pagination .disabled span {
    padding: 0.4rem 0.75rem; border: 1px solid var(--cw-border);
    border-radius: 6px; background: #fff; font-size: 0.8rem; color: var(--cw-text-muted);
}

/* ── EMPTY STATE ── */
.cw-empty { text-align: center; padding: 2.5rem 1rem; color: var(--cw-text-muted); font-size: 0.875rem; }

@media (max-width: 700px) {
    .cw-webinar-item {
        flex-wrap: wrap; gap: 0.4rem 0.75rem; padding: 0.75rem 1rem;
    }
    .cw-webinar-item__date { min-width: 36px; }
    .cw-webinar-item__info {
        flex: 1; min-width: 0;
    }
    .cw-webinar-item__course-tag { display: none; }
    .cw-webinar-item__time {
        font-size: 0.7rem; order: 10;
        margin-left: 48px;
    }
    .cw-webinar-item__status {
        order: 11; font-size: 0.68rem;
        margin-left: auto;
    }
    .cw-reprise-item {
        flex-wrap: wrap; gap: 0.4rem 0.75rem; padding: 0.75rem 1rem;
    }
    .cw-reprise-item__date { min-width: auto; }
    .cw-reprise-item__info { flex-basis: 100%; order: 2; padding-left: 0; }
    .cw-reprise-item__play { order: 3; margin-left: auto; font-size: 0.72rem; }
    .cw-filter-bar { flex-direction: column; }
    .cw-filter-bar__search { max-width: 100%; }
    .cw-filter-bar__select { width: 100%; }
    .cw-info-banner { font-size: 0.78rem; }
}
</style>
@stop

@section('content')
<div class="learner-container">
    <div class="container">
        <div class="cw-redesign">

            {{-- ═══ PAGE HEADER ═══ --}}
            <div class="cw-header">
                <h1>Kurswebinarer</h1>
                <p>Live-webinarer knyttet til dine aktive kurs.</p>
            </div>

            {{-- ═══ TABS ═══ --}}
            <div class="cw-tabs">
                <button class="cw-tab active" onclick="cwSwitchTab('kommende', this)">
                    Kommende <span class="cw-tab__count">{{ $upcoming->count() }}</span>
                </button>
                <button class="cw-tab" onclick="cwSwitchTab('reprise', this)">
                    Repriser <span class="cw-tab__count">{{ $replays->total() }}</span>
                </button>
            </div>

            {{-- ═══════════ TAB 1: KOMMENDE ═══════════ --}}
            <div class="cw-panel active" id="cw-panel-kommende">

                {{-- Info banner --}}
                <div class="cw-info-banner">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    <span>Du blir automatisk påmeldt kurswebinarer <strong>dagen før</strong>. «Se webinar»-lenken aktiveres <strong>30 minutter</strong> før webinaret starter.</span>
                </div>

                {{-- Course filter --}}
                @if($userCourses->count() > 1)
                    <div class="cw-filter-bar">
                        <select class="cw-filter-bar__select" id="cwCourseFilter" onchange="cwFilterCourse()">
                            <option value="">Alle kurs</option>
                            @foreach($userCourses as $courseName)
                                <option value="{{ $courseName }}">{{ $courseName }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if($upcoming->isEmpty())
                    <div class="cw-empty">Ingen kommende kurswebinarer.</div>
                @else
                    @php
                        $norwegianMonths = [
                            1 => 'Januar', 2 => 'Februar', 3 => 'Mars', 4 => 'April',
                            5 => 'Mai', 6 => 'Juni', 7 => 'Juli', 8 => 'August',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                        $norwegianMonthsShort = [
                            1 => 'jan', 2 => 'feb', 3 => 'mar', 4 => 'apr',
                            5 => 'mai', 6 => 'jun', 7 => 'jul', 8 => 'aug',
                            9 => 'sep', 10 => 'okt', 11 => 'nov', 12 => 'des'
                        ];
                        $grouped = $upcoming->groupBy(function ($w) {
                            return \Carbon\Carbon::parse($w->start_date)->format('Y-m');
                        });
                        $isFirst = true;
                    @endphp

                    @foreach($grouped as $monthKey => $monthWebinars)
                        @php
                            $monthDate = \Carbon\Carbon::parse($monthKey . '-01');
                            $monthLabel = $norwegianMonths[$monthDate->month] . ' ' . $monthDate->year;
                        @endphp
                        <div class="cw-month-label">{{ $monthLabel }}</div>
                        <div class="cw-webinar-list">
                            @foreach($monthWebinars as $webinar)
                                @php
                                    $startDate = \Carbon\Carbon::parse($webinar->start_date);
                                    $dayBefore = $startDate->copy()->subDay();
                                    $thirtyMinBefore = $startDate->copy()->subMinutes(30);
                                    $oneHourAfter = $startDate->copy()->addHour();
                                    $isRegistered = \App\Http\FrontendHelpers::checkIfWebinarRegistrant($webinar->id, Auth::user()->id);
                                    $joinUrl = $isRegistered ? \App\Http\FrontendHelpers::getWebinarJoinURL($webinar->id, Auth::user()->id) : null;
                                    $isNextWebinar = $isFirst;
                                    $isFirst = false;
                                @endphp
                                <div class="cw-webinar-item {{ $isNextWebinar ? 'cw-webinar-item--next' : '' }}"
                                     data-course="{{ $webinar->course_title }}">
                                    <div class="cw-webinar-item__date">
                                        <div class="cw-webinar-item__day">{{ $startDate->format('d') }}</div>
                                        <div class="cw-webinar-item__month-label">{{ $norwegianMonthsShort[$startDate->month] }}</div>
                                    </div>
                                    <div class="cw-webinar-item__info">
                                        <div class="cw-webinar-item__name">{{ $webinar->title }}</div>
                                        <div class="cw-webinar-item__topic">{{ $webinar->course_title }}</div>
                                    </div>
                                    <span class="cw-webinar-item__course-tag">{{ $webinar->course_title }}</span>
                                    <div class="cw-webinar-item__time">Kl. {{ $startDate->format('H:i') }}</div>

                                    {{-- Status button --}}
                                    @if(!Auth::user()->isDisabled)
                                        @if(now()->isBefore($dayBefore))
                                            <span class="cw-webinar-item__status cw-webinar-item__status--pending">Påmeldes automatisk</span>
                                        @elseif($isRegistered && now()->lt($thirtyMinBefore))
                                            <span class="cw-webinar-item__status cw-webinar-item__status--auto">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="width:12px;height:12px;"><polyline points="20 6 9 17 4 12"/></svg>
                                                Auto-påmeldt
                                            </span>
                                        @elseif($isRegistered && $joinUrl && now()->gte($thirtyMinBefore) && now()->lt($oneHourAfter))
                                            <a href="{{ $joinUrl }}" class="cw-webinar-item__status cw-webinar-item__status--join" target="_blank">Se webinar →</a>
                                        @elseif(!$isRegistered && $webinar->link)
                                            <a href="{{ route('learner.webinar.register', [$webinar->link, $webinar->id]) }}"
                                               class="cw-webinar-item__status cw-webinar-item__status--register webinarRegister">
                                                Meld på
                                            </a>
                                        @else
                                            <span class="cw-webinar-item__status cw-webinar-item__status--pending">Påmeldes automatisk</span>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- ═══════════ TAB 2: REPRISER ═══════════ --}}
            <div class="cw-panel" id="cw-panel-reprise">
                <div class="cw-filter-bar">
                    <select class="cw-filter-bar__select" id="cwRepriseFilter" onchange="cwFilterReprise()">
                        <option value="">Alle kurs</option>
                        @foreach($userCourses as $courseName)
                            <option value="{{ $courseName }}">{{ $courseName }}</option>
                        @endforeach
                    </select>
                    <form method="get" action="{{ route('learner.course-webinar') }}" style="display:contents;">
                        <input type="text" name="search_replay" class="cw-filter-bar__search"
                               placeholder="Søk etter tema eller kursholder..."
                               value="{{ request('search_replay') }}">
                    </form>
                </div>

                @if($replays->isEmpty())
                    <div class="cw-empty">Ingen repriser tilgjengelig.</div>
                @else
                    <div class="cw-reprise-list">
                        @foreach($replays as $replay)
                            <div class="cw-reprise-item" data-course="{{ $replay->course_title }}">
                                <span class="cw-reprise-item__date">{{ $replay->date ? \Carbon\Carbon::parse($replay->date)->format('d.m.Y') : '' }}</span>
                                <div class="cw-reprise-item__info">
                                    <div class="cw-reprise-item__name">{{ $replay->title }}</div>
                                    <div class="cw-reprise-item__topic">{{ $replay->course_title }}</div>
                                </div>
                                @if(!Auth::user()->isDisabled)
                                    <a href="#" class="cw-reprise-item__play cwVideoBtn"
                                       data-bs-toggle="modal" data-bs-target="#cwVideoModal"
                                       data-title="{{ $replay->title }}"
                                       data-video="{{ $replay->video_url }}">
                                        <svg viewBox="0 0 16 16"><polygon points="3,1 13,8 3,15"/></svg>
                                        Se reprise
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if($replays->hasPages())
                        <div class="cw-pagination">
                            {!! $replays->appends(request()->except('page'))->links() !!}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</div>

{{-- Video modal --}}
<div id="cwVideoModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="background: #1a1a1a; border: none; border-radius: 12px; overflow: hidden;">
            <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.1); padding: 0.75rem 1rem;">
                <h5 class="modal-title" style="color: #fff; font-size: 0.9rem; font-weight: 600;"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Lukk"></button>
            </div>
            <div class="modal-body" style="padding: 0;">
                <div id="cw-video-container" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Success modal --}}
<div id="submitSuccessModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                <div style="color: green; font-size: 24px"><i class="fa fa-check"></i></div>
                <p>{{ trans('site.learner.webinar-register-success') }}</p>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script>
    // Tab switching
    function cwSwitchTab(tabId, btn) {
        document.querySelectorAll('.cw-tab').forEach(function(t) { t.classList.remove('active'); });
        document.querySelectorAll('.cw-panel').forEach(function(p) { p.classList.remove('active'); });
        btn.classList.add('active');
        document.getElementById('cw-panel-' + tabId).classList.add('active');
    }

    // Course filter for upcoming
    function cwFilterCourse() {
        var val = document.getElementById('cwCourseFilter').value;
        document.querySelectorAll('#cw-panel-kommende .cw-webinar-item').forEach(function(item) {
            if (!val || item.getAttribute('data-course') === val) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Course filter for replays
    function cwFilterReprise() {
        var val = document.getElementById('cwRepriseFilter').value;
        document.querySelectorAll('#cw-panel-reprise .cw-reprise-item').forEach(function(item) {
            if (!val || item.getAttribute('data-course') === val) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Video modal for repriser
    $(".cwVideoBtn").click(function(e) {
        e.preventDefault();
        var title = $(this).data('title');
        var videoUrl = $(this).data('video');
        var modal = $("#cwVideoModal");
        modal.find(".modal-title").text(title);
        modal.find('#cw-video-container').html(
            '<iframe src="' + videoUrl + '" style="position:absolute;top:0;left:0;width:100%;height:100%;border:none;" allowfullscreen></iframe>'
        );
    });

    // Stopp video når modal lukkes
    $('#cwVideoModal').on('hidden.bs.modal', function () {
        $(this).find('#cw-video-container').html('');
    });

    // Webinar register spinner
    $(".webinarRegister").click(function(){
        let register_btn = $(this);
        register_btn.text('');
        register_btn.append('<i class="fa fa-spinner fa-pulse"></i>');
        register_btn.attr('disabled', 'disabled');
    });

    // Show Repriser tab if search_replay is in URL
    @if(request('search_replay'))
        document.querySelector('.cw-tab:nth-child(2)').click();
    @endif

    @if (Session::has('success'))
    $('#submitSuccessModal').modal('show');
    @endif
</script>
@stop
