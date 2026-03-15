@extends('frontend.layouts.course-portal')

@section('title')
<title>Mine kurs &rsaquo; Forfatterskolen</title>
@stop

@section('heading') Mine kurs @stop

@section('styles')
<style>
/* ── MINE KURS REDESIGN — scoped under .mk-redesign ── */
.mk-redesign {
    font-family: 'Source Sans 3', -apple-system, sans-serif;
    color: #1a1a1a;
    -webkit-font-smoothing: antialiased;
    padding: 2rem 2.5rem;
    background: #f5f3f0;
    min-height: 100vh;
}

/* Hide topbar on this page */
#topbar { display: none !important; }
#main-content { padding-top: 0 !important; margin-top: 0 !important; }

.mk-inner { max-width: 860px; }

/* Mobile sidebar toggle */
@media (max-width: 1025px) {
    .mk-redesign .mk-mobile-toggle {
        position: fixed; top: 0.75rem; right: 0.75rem; z-index: 100;
        background: #fff; border: 1px solid rgba(0,0,0,0.12);
        border-radius: 8px; padding: 0.5rem 0.75rem; cursor: pointer;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
}

/* ── PAGE HEADER ── */
.mk-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 0.75rem;
}
.mk-header h1 { font-size: 1.5rem; font-weight: 700; color: #1a1a1a; margin: 0; }

/* ── SECTION LABEL ── */
.mk-section-label {
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: #8a8580;
    margin-bottom: 0.75rem;
}

/* ── COURSE CARDS CONTAINER ── */
.mk-cards {
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
    margin-bottom: 2.5rem;
}

/* ── COURSE CARD (active) ── */
.mk-card {
    background: #fff;
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 14px;
    padding: 1.5rem;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.mk-card:hover {
    border-color: rgba(0,0,0,0.12);
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}

/* Card top row */
.mk-card__top {
    display: flex;
    align-items: flex-start;
    gap: 1.25rem;
    margin-bottom: 1.25rem;
}
.mk-card__thumb {
    width: 72px; height: 72px;
    border-radius: 10px;
    background: linear-gradient(135deg, #e8e2da, #d4cec6);
    flex-shrink: 0;
    overflow: hidden;
}
.mk-card__thumb img { width: 100%; height: 100%; object-fit: cover; }
.mk-card__info { flex: 1; }
.mk-card__title { font-size: 1.05rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.2rem; }
.mk-card__instructor { font-size: 0.8rem; color: #8a8580; margin-bottom: 0.4rem; }
.mk-card__next-label { font-size: 0.75rem; color: #5a5550; margin: 0; }
.mk-card__next-label strong { color: #1a1a1a; font-weight: 600; }

/* Badge */
.mk-card__badge {
    font-size: 0.65rem; font-weight: 600;
    padding: 0.25rem 0.6rem; border-radius: 4px;
    white-space: nowrap; flex-shrink: 0; margin-top: 0.15rem;
}
.mk-card__badge--active { background: #e8f5e9; color: #2e7d32; }
.mk-card__badge--renewal { background: #fff3e0; color: #e65100; }
.mk-card__badge--hold { background: rgba(0,0,0,0.05); color: #8a8580; }
.mk-card__badge--new { background: #e3f2fd; color: #1565c0; }

/* ── MODULE DOTS ── */
.mk-modules {
    display: flex;
    gap: 0.35rem;
    flex-wrap: wrap;
    margin-bottom: 1rem;
}
.mk-dot {
    width: 28px; height: 28px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.65rem; font-weight: 600;
    text-decoration: none;
    transition: all 0.15s;
}
.mk-dot--done { background: #f4e8ea; color: #862736; }
.mk-dot--current { background: #862736; color: #fff; box-shadow: 0 0 0 3px #f4e8ea; }
.mk-dot--locked { background: rgba(0,0,0,0.04); color: #8a8580; }

/* ── PROGRESS BAR ── */
.mk-progress {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
}
.mk-progress__bar {
    flex: 1; height: 6px;
    background: rgba(0,0,0,0.06);
    border-radius: 3px;
    overflow: hidden;
}
.mk-progress__fill {
    height: 100%; border-radius: 3px;
    background: #862736;
    transition: width 0.3s ease;
}
.mk-progress__text {
    font-size: 0.75rem; font-weight: 600;
    color: #8a8580; white-space: nowrap;
    min-width: 70px; text-align: right;
}

/* ── CARD ACTIONS ── */
.mk-card__actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}
.mk-btn {
    display: inline-flex; align-items: center; gap: 0.35rem;
    padding: 0.55rem 1.1rem; border-radius: 6px;
    font-family: 'Source Sans 3', -apple-system, sans-serif;
    font-size: 0.825rem; font-weight: 600;
    text-decoration: none; cursor: pointer;
    transition: all 0.15s; border: none;
}
.mk-btn:hover { text-decoration: none; }
.mk-btn--primary { background: #862736; color: #fff; }
.mk-btn--primary:hover { background: #9c2e40; color: #fff; }
.mk-btn--secondary { background: transparent; color: #5a5550; border: 1px solid rgba(0,0,0,0.12); }
.mk-btn--secondary:hover { border-color: #862736; color: #862736; }
.mk-btn--renewal { background: transparent; color: #e65100; border: 1px solid rgba(230,81,0,0.3); }
.mk-btn--renewal:hover { background: #e65100; color: #fff; }

/* Not started card */
.mk-card--not-started .mk-card__top { margin-bottom: 0.75rem; }

/* ── EXPIRED / PREVIOUS CARDS ── */
.mk-expired {
    background: #fff;
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 10px;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    opacity: 0.7;
    transition: opacity 0.15s;
}
.mk-expired:hover { opacity: 1; }

.mk-expired__thumb {
    width: 48px; height: 48px;
    border-radius: 8px;
    background: linear-gradient(135deg, #e8e2da, #d4cec6);
    flex-shrink: 0;
    overflow: hidden;
}
.mk-expired__thumb img { width: 100%; height: 100%; object-fit: cover; }
.mk-expired__info { flex: 1; }
.mk-expired__name { font-size: 0.85rem; font-weight: 600; color: #1a1a1a; }
.mk-expired__status { font-size: 0.72rem; color: #8a8580; }
.mk-expired__action {
    font-size: 0.78rem; font-weight: 600;
    color: #862736; text-decoration: none;
    padding: 0.35rem 0.85rem; border: 1px solid #862736;
    border-radius: 5px; white-space: nowrap;
    transition: all 0.15s; background: transparent; cursor: pointer;
}
.mk-expired__action:hover { background: #862736; color: #fff; text-decoration: none; }

/* ── EMPTY STATE ── */
.mk-empty {
    text-align: center;
    padding: 3rem 1.5rem;
    background: #fff;
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 14px;
}
.mk-empty p { color: #8a8580; font-size: 0.9rem; margin-bottom: 1rem; }

/* ── RESPONSIVE ── */
@media (max-width: 600px) {
    .mk-card__top { flex-direction: column; gap: 0.75rem; }
    .mk-card__thumb { width: 100%; height: 120px; }
    .mk-header { flex-direction: column; gap: 1rem; align-items: flex-start; }
    .mk-modules { gap: 0.25rem; }
    .mk-redesign { padding: 1.5rem; }
    .mk-card__actions { flex-direction: column; align-items: stretch; }
    .mk-btn { justify-content: center; }
}
</style>
@stop

@section('content')
@php
    // ── Separate courses into active vs previous ──
    $activeList = [];
    $previousList = [];

    foreach ($coursesTaken as $ct) {
        if ($ct->is_active && !$ct->hasEnded) {
            $activeList[] = $ct;
        } else {
            $previousList[] = $ct;
        }
    }
@endphp

<div class="mk-redesign">

    {{-- Mobile sidebar toggle --}}
    <button id="sidebarCollapse" class="mk-mobile-toggle d-xl-none">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2" stroke-linecap="round">
            <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
    </button>

    <div class="mk-inner">

        {{-- ═══ PAGE HEADER ═══ --}}
        <div class="mk-header">
            <h1>Mine kurs</h1>
        </div>

        {{-- ═══════════ ACTIVE COURSES ═══════════ --}}
        @if(count($activeList) > 0)
            <div class="mk-section-label">Aktive kurs ({{ count($activeList) }})</div>

            <div class="mk-cards">
                @foreach($activeList as $ct)
                    @php
                        if (!$ct->package || !$ct->package->course) continue;
                        $course = $ct->package->course;
                        $lessons = $course->lessons ? $course->lessons->sortBy('order')->values() : collect();
                        $totalLessons = $lessons->count();

                        // ── Calculate lesson availability ──
                        $lessonStates = [];
                        $availCount = 0;
                        $lastAvailLesson = null;

                        if ($ct->hasStarted && $ct->started_at && $totalLessons > 0) {
                            $accessArr = $ct->access_lessons;
                            if (is_string($accessArr)) $accessArr = json_decode($accessArr, true) ?: [];
                            if (!is_array($accessArr)) $accessArr = [];

                            foreach ($lessons as $lesson) {
                                $avail = false;
                                try {
                                    $avail = \App\Http\FrontendHelpers::isLessonAvailable(
                                        $ct->started_at, $lesson->delay, $lesson->period
                                    );
                                } catch (\Exception $e) {}
                                if (!$avail && in_array($lesson->id, $accessArr)) {
                                    $avail = true;
                                }
                                $lessonStates[$lesson->id] = $avail;
                                if ($avail) {
                                    $availCount++;
                                    $lastAvailLesson = $lesson;
                                }
                            }
                        }

                        $progressPct = $totalLessons > 0 ? round(($availCount / $totalLessons) * 100) : 0;
                        $continueUrl = route('learner.course.show', ['id' => $ct->id]);
                    @endphp

                    <div class="mk-card{{ !$ct->hasStarted ? ' mk-card--not-started' : '' }}">
                        <div class="mk-card__top">
                            <div class="mk-card__thumb">
                                @if($course->course_image)
                                    <img src="https://www.forfatterskolen.no/{{ $course->course_image }}"
                                         alt="{{ $course->title }}" loading="lazy">
                                @endif
                            </div>
                            <div class="mk-card__info">
                                <h3 class="mk-card__title">{{ $course->title }}</h3>
                                <p class="mk-card__instructor">
                                    @if($ct->started_at)
                                        Oppstart {{ \Carbon\Carbon::parse($ct->started_at)->format('d.m.Y') }}
                                    @else
                                        Forfatterskolen
                                    @endif
                                </p>
                                @if($ct->hasStarted && $lastAvailLesson)
                                    <p class="mk-card__next-label">Neste: <strong>{{ $lastAvailLesson->title }}</strong></p>
                                @elseif(!$ct->hasStarted)
                                    <p class="mk-card__next-label">Kurset er klart til å starte</p>
                                @endif
                            </div>
                            @if($ct->hasStarted)
                                <span class="mk-card__badge mk-card__badge--active">Aktiv</span>
                            @elseif($ct->isDisabled)
                                <span class="mk-card__badge mk-card__badge--hold">På vent</span>
                            @else
                                <span class="mk-card__badge mk-card__badge--new">Ny</span>
                            @endif
                        </div>

                        @if($ct->hasStarted && $totalLessons > 0)
                            {{-- Module dots --}}
                            <div class="mk-modules">
                                @foreach($lessons as $idx => $lesson)
                                    @php
                                        $avail = $lessonStates[$lesson->id] ?? false;
                                        $isCurrent = ($lastAvailLesson && $lesson->id === $lastAvailLesson->id);
                                        $dotClass = $isCurrent ? 'mk-dot--current' : ($avail ? 'mk-dot--done' : 'mk-dot--locked');
                                    @endphp
                                    <span class="mk-dot {{ $dotClass }}" title="{{ $lesson->title }}">{{ $idx + 1 }}</span>
                                @endforeach
                            </div>

                            {{-- Progress bar --}}
                            <div class="mk-progress">
                                <div class="mk-progress__bar">
                                    <div class="mk-progress__fill" style="width: {{ $progressPct }}%;"></div>
                                </div>
                                <span class="mk-progress__text">{{ $availCount }} av {{ $totalLessons }} moduler</span>
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="mk-card__actions">
                            @if(!Auth::user()->isDisabled)
                                @if($ct->hasStarted && !$ct->isDisabled)
                                    <a href="{{ $continueUrl }}" class="mk-btn mk-btn--primary">
                                        Fortsett →
                                    </a>
                                    <a href="{{ $continueUrl }}" class="mk-btn mk-btn--secondary">
                                        Kursplan
                                    </a>
                                @elseif(!$ct->hasStarted)
                                    <form method="POST" action="{{ route('learner.course.take') }}" style="display:inline;">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="courseTakenId" value="{{ $ct->id }}">
                                        <button type="submit" class="mk-btn mk-btn--primary">Start kurs →</button>
                                    </form>
                                @elseif($ct->isDisabled)
                                    <span class="mk-btn mk-btn--secondary" style="cursor: default; opacity: 0.6;">Kurset er midlertidig deaktivert</span>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ═══════════ PREVIOUS / EXPIRED COURSES ═══════════ --}}
        @php
            $hasPrevious = count($previousList) > 0 || (isset($formerCourses) && count($formerCourses) > 0);
        @endphp

        @if($hasPrevious)
            <div class="mk-section-label">Tidligere kurs</div>

            <div class="mk-cards">
                {{-- Ended/on-hold courses from coursesTaken --}}
                @foreach($previousList as $ct)
                    @php
                        if (!$ct->package || !$ct->package->course) continue;
                        $course = $ct->package->course;
                        $statusLabel = 'Utløpt';
                        if (!$ct->is_active) {
                            $statusLabel = 'På vent';
                        } elseif ($ct->hasEnded) {
                            $statusLabel = 'Utløpt';
                        }
                    @endphp
                    <div class="mk-expired">
                        <div class="mk-expired__thumb">
                            @if($course->course_image)
                                <img src="https://www.forfatterskolen.no/{{ $course->course_image }}"
                                     alt="{{ $course->title }}" loading="lazy">
                            @endif
                        </div>
                        <div class="mk-expired__info">
                            <div class="mk-expired__name">{{ $course->title }}</div>
                            <div class="mk-expired__status">
                                {{ $statusLabel }}
                                @if($ct->hasEnded && $ct->started_at)
                                    · Avsluttet
                                @endif
                            </div>
                        </div>
                        @if($ct->hasEnded && !$ct->is_free)
                            <button class="mk-expired__action" data-bs-toggle="modal" data-bs-target="#renewAllModal">
                                Forny
                            </button>
                        @endif
                    </div>
                @endforeach

                {{-- Former courses --}}
                @if(isset($formerCourses))
                    @foreach($formerCourses as $fc)
                        @php
                            if (!$fc->package || !$fc->package->course) continue;
                            $fCourse = $fc->package->course;
                        @endphp
                        <div class="mk-expired">
                            <div class="mk-expired__thumb">
                                @if($fCourse->course_image)
                                    <img src="https://www.forfatterskolen.no/{{ $fCourse->course_image }}"
                                         alt="{{ $fCourse->title }}" loading="lazy">
                                @endif
                            </div>
                            <div class="mk-expired__info">
                                <div class="mk-expired__name">{{ $fCourse->title }}</div>
                                <div class="mk-expired__status">
                                    @if($fc->is_free)
                                        Gratiskurs · Fullført
                                    @else
                                        Tidligere kurs
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        @endif

        {{-- Empty state --}}
        @if(count($activeList) == 0 && !$hasPrevious)
            <div class="mk-empty">
                <p>Du har ingen kurs ennå.</p>
                <a href="/" class="mk-btn mk-btn--primary">Utforsk kurs</a>
            </div>
        @endif

        {{-- Pagination --}}
        <div class="text-center mt-3">
            {{ $coursesTaken->appends(request()->except('page'))->links('pagination.custom-pagination') }}
        </div>

    </div>
</div>

{{-- ═══ RENEW MODAL ═══ --}}
<div id="renewModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    {{ trans('site.learner.renew-course-text') }}
                </h3>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('learner.course.renew') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <label>{{ trans('site.front.form.payment-method') }}</label>
                    <select class="form-control" name="payment_mode_id" required>
                        @foreach(App\PaymentMode::get() as $paymentMode)
                            <option value="{{ $paymentMode->id }}" data-mode="{{ $paymentMode->mode }}">{{ $paymentMode->mode }}</option>
                        @endforeach
                    </select>
                    <em><small>{{ trans('site.learner.renew-course.payment-note') }}</small></em>
                    <input type="hidden" name="course_id">
                    <div class="text-end margin-top">
                        <button type="submit" class="btn btn-primary">{{ trans('site.learner.renew-text') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ═══ RENEW ALL MODAL ═══ --}}
<div id="renewAllModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{ trans('site.learner.renew-all.title') }}</h3>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('learner.renew-all-courses') }}" onsubmit="disableSubmit(this)">
                    {{ csrf_field() }}
                    <p>{{ trans('site.learner.renew-all.description') }},?</p>
                    <div class="text-end margin-top">
                        <button type="submit" class="btn btn-primary">{{ trans('site.front.yes') }}</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ trans('site.front.no') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@stop

@section('scripts')
    <script>
        $(function(){
            $(".renewCourse").click(function(){
                let fields = $(this).data('fields');
                $("input[name=course_id]").val(fields.id);
            });

            $(".renewAllBtn").click(function(){
                let form = $('#renewAllModal form');
                let action = $(this).data('action');
                form.attr('action', action);
            });
        });
    </script>
@stop
