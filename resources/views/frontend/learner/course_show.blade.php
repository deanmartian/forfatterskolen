@extends('frontend.layouts.course-portal')

@section('title')
<title>{{ $courseTaken->package->course->title }} &rsaquo; Forfatterskolen</title>
@stop

@section('heading') {{ $courseTaken->package->course->title }} @stop

@section('styles')
<style>
/* ── COURSE VIEW REDESIGN — scoped under .cv-redesign ── */
.cv-redesign {
    font-family: 'Source Sans 3', -apple-system, sans-serif;
    color: #1a1a1a;
    -webkit-font-smoothing: antialiased;
    padding: 2rem 2.5rem;
    background: #f5f3f0;
    min-height: 100vh;
}

#topbar { display: none !important; }
#main-content { padding-top: 0 !important; margin-top: 0 !important; }

.cv-inner { max-width: 880px; }

/* Mobile sidebar toggle — vinrød, stor og tydelig */
.cv-redesign .cv-sidebar-toggle {
    display: none;
}
@media (max-width: 1026px) {
    .cv-redesign .cv-sidebar-toggle {
        display: flex !important;
        position: fixed;
        top: 16px;
        left: 16px;
        z-index: 1050;
        width: 50px;
        height: 50px;
        border-radius: 14px;
        border: 2px solid rgba(255,255,255,0.3);
        background: #862736;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(134, 39, 54, 0.4), 0 0 0 3px rgba(134, 39, 54, 0.15);
        padding: 0;
        transition: background 0.15s, box-shadow 0.15s, transform 0.15s;
    }
    .cv-redesign .cv-sidebar-toggle:hover { background: #9c2e40; transform: scale(1.05); }
    .cv-redesign .cv-sidebar-toggle:active { transform: scale(0.96); }
    .cv-redesign .cv-sidebar-toggle svg { width: 24px; height: 24px; stroke: #fff; stroke-width: 2.5; }
}

/* ── COURSE HEADER ── */
.cv-header {
    background: #fff;
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 14px;
    padding: 1.75rem 2rem;
    margin-bottom: 1.5rem;
}

.cv-header__back {
    display: inline-flex; align-items: center; gap: 0.35rem;
    font-size: 0.8rem; color: #8a8580; text-decoration: none;
    margin-bottom: 1rem; transition: color 0.15s;
}
.cv-header__back:hover { color: #862736; text-decoration: none; }
.cv-header__back svg { width: 16px; height: 16px; stroke: currentColor; }

.cv-header__title { font-size: 1.5rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.25rem; }
.cv-header__instructor { font-size: 0.875rem; color: #8a8580; margin-bottom: 1.25rem; }

/* Progress bar */
.cv-progress { display: flex; align-items: center; gap: 1rem; }
.cv-progress__bar { flex: 1; height: 8px; background: rgba(0,0,0,0.06); border-radius: 4px; overflow: hidden; }
.cv-progress__fill { height: 100%; background: #862736; border-radius: 4px; transition: width 0.4s ease; }
.cv-progress__text { font-size: 0.825rem; font-weight: 600; color: #5a5550; white-space: nowrap; }

/* ── TABS ── */
.cv-tabs {
    display: flex; gap: 0;
    border-bottom: 2px solid rgba(0,0,0,0.08);
    margin-bottom: 1.5rem;
}
.cv-tab {
    padding: 0.7rem 1.15rem; border: none; background: transparent;
    font-family: 'Source Sans 3', -apple-system, sans-serif;
    font-size: 0.835rem; font-weight: 500; color: #8a8580;
    cursor: pointer; white-space: nowrap; position: relative;
    transition: color 0.15s;
}
.cv-tab:hover { color: #1a1a1a; }
.cv-tab.active { color: #862736; font-weight: 600; }
.cv-tab.active::after {
    content: ''; position: absolute; bottom: -2px; left: 0; right: 0;
    height: 2px; background: #862736; border-radius: 1px 1px 0 0;
}

.cv-panel { display: none; }
.cv-panel.active { display: block; }

/* ── QUICK RESOURCES ── */
.cv-quick { display: flex; gap: 0.75rem; margin-bottom: 1.5rem; }
.cv-quick-link {
    display: flex; align-items: center; gap: 0.6rem;
    padding: 0.75rem 1rem; background: #fff;
    border: 1px solid rgba(0,0,0,0.08); border-radius: 10px;
    text-decoration: none; color: #1a1a1a;
    font-size: 0.85rem; font-weight: 500;
    transition: border-color 0.15s, box-shadow 0.15s; flex: 1;
}
.cv-quick-link:hover { border-color: #862736; box-shadow: 0 2px 8px rgba(0,0,0,0.04); text-decoration: none; color: #1a1a1a; }
.cv-quick-link__icon {
    width: 36px; height: 36px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.cv-quick-link__icon--plan { background: #f4e8ea; }
.cv-quick-link__icon--reprise { background: #e3f2fd; }
.cv-quick-link__icon svg { width: 18px; height: 18px; }
.cv-quick-link__label { font-size: 0.7rem; color: #8a8580; }

/* ── MODULE LIST ── */
.cv-modules { display: flex; flex-direction: column; gap: 0.6rem; }

.cv-module {
    background: #fff; border: 1px solid rgba(0,0,0,0.08); border-radius: 10px;
    padding: 1rem 1.25rem; display: flex; align-items: center; gap: 1rem;
    text-decoration: none; color: inherit;
    transition: border-color 0.15s, box-shadow 0.15s, transform 0.15s;
}
.cv-module:hover { border-color: rgba(0,0,0,0.12); box-shadow: 0 2px 8px rgba(0,0,0,0.04); transform: translateX(2px); text-decoration: none; color: inherit; }
.cv-module--current { border-color: #862736; border-left: 3px solid #862736; background: rgba(134,39,54,0.02); }
.cv-module--locked { opacity: 0.6; cursor: default; }
.cv-module--locked:hover { transform: none; box-shadow: none; }

/* Module circle */
.cv-module__circle {
    width: 36px; height: 36px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.8rem; font-weight: 700; flex-shrink: 0;
}
.cv-module__circle--check { background: #e8f5e9; }
.cv-module__circle--check svg { width: 18px; height: 18px; }
.cv-module__circle--current { background: #862736; color: #fff; box-shadow: 0 0 0 3px #f4e8ea; }
.cv-module__circle--locked { background: rgba(0,0,0,0.05); color: #8a8580; }

/* Module info */
.cv-module__info { flex: 1; }
.cv-module__title { font-size: 0.9rem; font-weight: 600; color: #1a1a1a; margin-bottom: 0.1rem; }
.cv-module__meta { font-size: 0.75rem; color: #8a8580; }

/* Module status badge */
.cv-module__status {
    font-size: 0.65rem; font-weight: 600; padding: 0.2rem 0.55rem;
    border-radius: 4px; white-space: nowrap; flex-shrink: 0;
}
.cv-module__status--available { background: #e8f5e9; color: #2e7d32; }
.cv-module__status--current { background: #f4e8ea; color: #862736; }
.cv-module__status--locked { background: rgba(0,0,0,0.04); color: #8a8580; }

.cv-module__arrow { color: #8a8580; font-size: 1rem; flex-shrink: 0; transition: color 0.15s; }
.cv-module:hover .cv-module__arrow { color: #862736; }

/* ── KURSPLAN TAB ── */
.cv-kursplan {
    background: #fff; border: 1px solid rgba(0,0,0,0.08);
    border-radius: 14px; padding: 2rem;
}
.cv-kursplan h2 { font-size: 1.1rem; font-weight: 700; color: #1a1a1a; margin-bottom: 1rem; }
.cv-kursplan p { font-size: 0.9rem; color: #5a5550; line-height: 1.7; margin-bottom: 0.75rem; }
.cv-kursplan .cv-kursplan__meta {
    display: flex; gap: 2rem; flex-wrap: wrap;
    margin-bottom: 1.5rem; padding-bottom: 1rem;
    border-bottom: 1px solid rgba(0,0,0,0.08);
    font-size: 0.825rem; color: #8a8580;
}
.cv-kursplan .cv-kursplan__meta strong { color: #1a1a1a; font-weight: 600; }
.cv-kursplan .cv-kursplan__includes { margin-top: 1rem; }
.cv-kursplan .cv-kursplan__includes h3 { font-size: 0.9rem; font-weight: 600; color: #1a1a1a; margin-bottom: 0.5rem; }
.cv-kursplan .cv-kursplan__includes ul { list-style: none; padding: 0; margin: 0; }
.cv-kursplan .cv-kursplan__includes li { font-size: 0.85rem; color: #5a5550; padding: 0.25rem 0; padding-left: 1rem; position: relative; }
.cv-kursplan .cv-kursplan__includes li::before { content: '·'; position: absolute; left: 0; color: #862736; font-weight: bold; }

/* ── WEBINAR TAB ── */
.cv-webinars { display: flex; flex-direction: column; gap: 0.6rem; }

.cv-webinar {
    background: #fff; border: 1px solid rgba(0,0,0,0.08); border-radius: 10px;
    padding: 1rem 1.25rem; display: flex; align-items: center; gap: 1rem;
    transition: border-color 0.15s;
}
.cv-webinar:hover { border-color: rgba(0,0,0,0.12); }

.cv-webinar__date { text-align: center; min-width: 42px; flex-shrink: 0; }
.cv-webinar__day { font-size: 1.15rem; font-weight: 700; color: #862736; line-height: 1; }
.cv-webinar__month { font-size: 0.6rem; font-weight: 600; text-transform: uppercase; color: #862736; margin-top: 2px; }
.cv-webinar__info { flex: 1; }
.cv-webinar__title { font-size: 0.875rem; font-weight: 600; color: #1a1a1a; margin-bottom: 0.1rem; }
.cv-webinar__meta { font-size: 0.75rem; color: #8a8580; }
.cv-webinar__action {
    font-size: 0.78rem; font-weight: 600; color: #862736;
    text-decoration: none; padding: 0.35rem 0.85rem;
    border: 1px solid #862736; border-radius: 5px;
    transition: all 0.15s; white-space: nowrap;
}
.cv-webinar__action:hover { background: #862736; color: #fff; text-decoration: none; }

.cv-webinars__empty { text-align: center; padding: 2rem; color: #8a8580; font-size: 0.85rem; }

/* ── RESPONSIVE ── */
#main-content { overflow-x: hidden !important; max-width: 100vw; }
#main-container { overflow-x: hidden !important; }

@media (max-width: 768px) {
    .cv-redesign { padding: 1.25rem 1rem; padding-top: 80px; }
    .cv-inner { max-width: 100%; }
    .cv-quick { flex-direction: column; }
    .cv-module { padding: 0.85rem 1rem; }
    .cv-header { padding: 1.25rem 1rem; border-radius: 12px; }
    .cv-kursplan { padding: 1.25rem 1rem; }
    .cv-header__title { font-size: 1.15rem; }
    .cv-redesign, .cv-inner, .cv-header, .cv-module, .cv-kursplan { min-width: 0; }
    .cv-header__title, .cv-header__instructor, .cv-module__title {
        word-wrap: break-word; overflow-wrap: break-word;
    }
}
@media (max-width: 480px) {
    .cv-redesign { padding: 1rem 0.75rem; padding-top: 76px; }
    .cv-header { padding: 1rem 0.85rem; }
    .cv-header__title { font-size: 1.05rem; }
}
</style>
@stop

@section('content')
@php
    $course = $courseTaken->package->course;
    $lessons = $course->lessons ? $course->lessons->sortBy('order')->values() : collect();
    $totalLessons = $lessons->count();

    // ── Calculate lesson availability ──
    $lessonData = [];
    $availCount = 0;
    $lastAvailIdx = -1;

    $accessArr = $courseTaken->access_lessons;
    if (is_string($accessArr)) $accessArr = json_decode($accessArr, true) ?: [];
    if (!is_array($accessArr)) $accessArr = [];

    foreach ($lessons as $idx => $lesson) {
        $avail = false;
        $availDate = null;

        try {
            $avail = \App\Http\FrontendHelpers::isLessonAvailable(
                $courseTaken->started_at, $lesson->delay, $lesson->period
            );
        } catch (\Exception $e) {}

        if (!$avail && in_array($lesson->id, $accessArr)) {
            $avail = true;
        }

        if (!$avail) {
            try {
                $availDate = \App\Http\FrontendHelpers::lessonAvailability(
                    $courseTaken->started_at, $lesson->delay, $lesson->period
                );
            } catch (\Exception $e) {}
        }

        $lessonData[] = [
            'lesson' => $lesson,
            'available' => $avail,
            'availDate' => $availDate,
            'index' => $idx,
        ];

        if ($avail) {
            $availCount++;
            $lastAvailIdx = $idx;
        }
    }

    $progressPct = $totalLessons > 0 ? round(($availCount / $totalLessons) * 100) : 0;

    // Norwegian months
    $monthsNo = ['Jan','Feb','Mar','Apr','Mai','Jun','Jul','Aug','Sep','Okt','Nov','Des'];
@endphp

<div class="cv-redesign">

    {{-- Mobile sidebar toggle — vinrød, stor og tydelig --}}
    <button class="cv-sidebar-toggle" data-sidebar-toggle aria-label="Meny">
        <svg viewBox="0 0 24 24" fill="none" stroke-linecap="round">
            <line x1="4" y1="7" x2="20" y2="7"/>
            <line x1="4" y1="12" x2="20" y2="12"/>
            <line x1="4" y1="17" x2="20" y2="17"/>
        </svg>
    </button>

    <div class="cv-inner">

        {{-- ═══ COURSE HEADER ═══ --}}
        <div class="cv-header">
            <a href="{{ route('learner.course') }}" class="cv-header__back">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                Tilbake til mine kurs
            </a>
            <h1 class="cv-header__title">{{ $course->title }}</h1>
            <p class="cv-header__instructor">
                @if($courseTaken->started_at)
                    Oppstart {{ \Carbon\Carbon::parse($courseTaken->started_at)->format('d.m.Y') }}
                @endif
                · {{ $totalLessons }} {{ $totalLessons == 1 ? 'modul' : 'moduler' }}
            </p>

            @if($totalLessons > 0)
                <div class="cv-progress">
                    <div class="cv-progress__bar">
                        <div class="cv-progress__fill" style="width: {{ $progressPct }}%;"></div>
                    </div>
                    <span class="cv-progress__text">{{ $availCount }} av {{ $totalLessons }} moduler tilgjengelige</span>
                </div>
            @endif
        </div>

        {{-- ═══ TABS ═══ --}}
        <div class="cv-tabs">
            <button class="cv-tab active" onclick="cvSwitchTab('leksjoner', this)">Leksjoner</button>
            <button class="cv-tab" onclick="cvSwitchTab('kursplan', this)">Kursplan</button>
            <button class="cv-tab" onclick="cvSwitchTab('webinarer', this)">Kurs webinarer</button>
        </div>

        {{-- ═══ TAB 1: LEKSJONER ═══ --}}
        <div class="cv-panel active" id="cv-panel-leksjoner">

            {{-- Quick resources --}}
            <div class="cv-quick">
                <a href="javascript:void(0)" class="cv-quick-link" onclick="cvSwitchTab('kursplan', document.querySelectorAll('.cv-tab')[1])">
                    <div class="cv-quick-link__icon cv-quick-link__icon--plan">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/>
                            <line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="13" y2="17"/>
                        </svg>
                    </div>
                    <div>
                        <div>Kursplan</div>
                        <div class="cv-quick-link__label">Oversikt over hele kurset</div>
                    </div>
                </a>
                <a href="{{ route('learner.course-webinar') }}" class="cv-quick-link">
                    <div class="cv-quick-link__icon cv-quick-link__icon--reprise">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#1565c0" stroke-width="1.5" stroke-linecap="round">
                            <polygon points="5 3 19 12 5 21 5 3"/>
                        </svg>
                    </div>
                    <div>
                        <div>Repriser</div>
                        <div class="cv-quick-link__label">Se webinarer i reprise</div>
                    </div>
                </a>
            </div>

            {{-- Module list --}}
            <div class="cv-modules">
                @foreach($lessonData as $ld)
                    @php
                        $lesson = $ld['lesson'];
                        $avail = $ld['available'];
                        $isCurrent = ($ld['index'] === $lastAvailIdx && $availCount < $totalLessons);
                        $lessonUrl = route('learner.course.lesson', ['course_id' => $course->id, 'id' => $lesson->id]);
                    @endphp

                    @if($avail)
                        {{-- Available lesson --}}
                        <a href="{{ $lessonUrl }}" class="cv-module{{ $isCurrent ? ' cv-module--current' : '' }}">
                            @if($isCurrent)
                                <div class="cv-module__circle cv-module__circle--current">{{ $ld['index'] + 1 }}</div>
                            @else
                                <div class="cv-module__circle cv-module__circle--check">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="#2e7d32" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                                </div>
                            @endif
                            <div class="cv-module__info">
                                <div class="cv-module__title">{{ $lesson->title }}</div>
                                <div class="cv-module__meta">
                                    @if($isCurrent)
                                        Neste modul
                                    @else
                                        Tilgjengelig
                                    @endif
                                </div>
                            </div>
                            <span class="cv-module__status {{ $isCurrent ? 'cv-module__status--current' : 'cv-module__status--available' }}">
                                {{ $isCurrent ? 'Neste' : 'Tilgjengelig' }}
                            </span>
                            <span class="cv-module__arrow">›</span>
                        </a>
                    @else
                        {{-- Locked lesson --}}
                        <div class="cv-module cv-module--locked">
                            <div class="cv-module__circle cv-module__circle--locked">{{ $ld['index'] + 1 }}</div>
                            <div class="cv-module__info">
                                <div class="cv-module__title">{{ $lesson->title }}</div>
                                <div class="cv-module__meta">
                                    @if($ld['availDate'])
                                        Tilgjengelig {{ $ld['availDate'] }}
                                    @else
                                        Ikke tilgjengelig ennå
                                    @endif
                                </div>
                            </div>
                            <span class="cv-module__status cv-module__status--locked">Låst</span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- ═══ TAB 2: KURSPLAN ═══ --}}
        <div class="cv-panel" id="cv-panel-kursplan">
            <div class="cv-kursplan">
                <h2>{{ $course->title }}</h2>

                <div class="cv-kursplan__meta">
                    <div>
                        <strong>Oppstart:</strong>
                        {{ $courseTaken->started_at ? \Carbon\Carbon::parse($courseTaken->started_at)->format('d.m.Y') : '—' }}
                    </div>
                    <div>
                        <strong>Utløper:</strong>
                        @if($courseTaken->end_date)
                            {{ $courseTaken->end_date }}
                        @elseif($courseTaken->started_at)
                            {{ \Carbon\Carbon::parse($courseTaken->started_at)->addYears($courseTaken->years)->format('d.m.Y') }}
                        @else
                            —
                        @endif
                    </div>
                    <div>
                        <strong>Moduler:</strong> {{ $totalLessons }}
                    </div>
                </div>

                {!! $course->description !!}

                @if($courseTaken->package->shop_manuscripts->count() > 0 ||
                    $courseTaken->package->included_courses->count() > 0 ||
                    $courseTaken->package->workshops > 0)
                    <div class="cv-kursplan__includes">
                        <h3>Inkludert i kurset</h3>
                        <ul>
                            @foreach($courseTaken->package->shop_manuscripts as $sm)
                                <li>{{ $sm->shop_manuscript->title }}</li>
                            @endforeach
                            @if($courseTaken->package->workshops)
                                <li>{{ $courseTaken->package->workshops }} workshops</li>
                            @endif
                            @foreach($courseTaken->package->included_courses as $ic)
                                <li>{{ $ic->included_package->course->title }} ({{ $ic->included_package->variation }})</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        {{-- ═══ TAB 3: KURS WEBINARER ═══ --}}
        <div class="cv-panel" id="cv-panel-webinarer">
            <div class="cv-webinars">
                @php
                    $webinars = $course->activeWebinars ?? collect();
                @endphp

                @if($webinars->count() > 0)
                    @foreach($webinars as $webinar)
                        @php
                            $wDate = \Carbon\Carbon::parse($webinar->start_date);
                        @endphp
                        <div class="cv-webinar">
                            <div class="cv-webinar__date">
                                <div class="cv-webinar__day">{{ $wDate->format('d') }}</div>
                                <div class="cv-webinar__month">{{ $monthsNo[$wDate->month - 1] }}</div>
                            </div>
                            <div class="cv-webinar__info">
                                <div class="cv-webinar__title">{{ $webinar->title }}</div>
                                <div class="cv-webinar__meta">{{ $wDate->format('d.m.Y') }} kl. {{ $wDate->format('H:i') }}</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="cv-webinars__empty">
                        Ingen kurs-webinarer er planlagt for øyeblikket.
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

{{-- ═══ MANUSCRIPT UPLOAD MODAL (preserved) ═══ --}}
@if($courseTaken->manuscripts->count() < $courseTaken->package->manuscripts_count)
<div id="addManuscriptModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{ trans('site.learner.course-show.upload-manuscript') }}</h3>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data" action="{{ route('learner.course.uploadManuscript', $courseTaken->id) }}">
                    {{ csrf_field() }}
                    <div class="form-group">* {{ trans('site.learner.manuscript.doc-pdf-odt-text') }}</div>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <input type="file" class="form-control" required name="file"
                                   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary float-end">
                        {{ trans('site.learner.course-show.upload-manuscript') }}
                    </button>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<div id="submitSuccessModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                <div style="color: green; font-size: 24px"><i class="fa fa-check"></i></div>
                {{ trans('site.learner.upload-manuscript-success') }}
            </div>
        </div>
    </div>
</div>

@stop

@section('scripts')
<script>
    function cvSwitchTab(tabId, btn) {
        document.querySelectorAll('.cv-tab').forEach(function(t) { t.classList.remove('active'); });
        document.querySelectorAll('.cv-panel').forEach(function(p) { p.classList.remove('active'); });
        if (btn) btn.classList.add('active');
        var panel = document.getElementById('cv-panel-' + tabId);
        if (panel) panel.classList.add('active');
    }

    @if (Session::has('success'))
        $('#submitSuccessModal').modal('show');
    @endif

    /* Auto-collapse sidebar on mobile */
    setTimeout(function() {
        var sidebar = document.getElementById('sidebar');
        var mainContainer = document.getElementById('main-container');
        if (window.innerWidth <= 1026 && sidebar) {
            sidebar.classList.remove('sidebar-visible');
            if (mainContainer) mainContainer.classList.remove('enlarge');
            document.body.classList.remove('sidebar-open');
        }
    }, 150);
</script>
@stop
