@extends('frontend.layouts.course-portal')

@section('title')
    <title>Coaching &rsaquo; Forfatterskolen</title>
@endsection

@section('styles')
<style>
    /* ── STATS ROW ────────────────────────────────────── */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.75rem;
        margin-bottom: 1.75rem;
    }

    .stat-card {
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 10px;
        padding: 1rem;
        text-align: center;
    }

    .stat-card__number {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1a1a1a;
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .stat-card__number--wine { color: #862736; }
    .stat-card__number--green { color: #2e7d32; }

    .stat-card__label {
        font-size: 0.72rem;
        font-weight: 500;
        color: #8a8580;
    }

    /* ── CARD ─────────────────────────────────────────── */
    .coaching-card {
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 14px;
        margin-bottom: 1.25rem;
        overflow: hidden;
    }

    .coaching-card__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem 1.5rem 0.75rem;
    }

    .coaching-card__title {
        font-size: 1rem;
        font-weight: 700;
        color: #1a1a1a;
    }

    .coaching-card__desc {
        font-size: 0.8rem;
        color: #8a8580;
    }

    .coaching-card__body {
        padding: 0 1.5rem 1.5rem;
    }

    /* ── SECTION LABEL ────────────────────────────────── */
    .section-label {
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: #8a8580;
        margin-bottom: 0.75rem;
    }

    /* ── NEXT SESSION CARD ────────────────────────────── */
    .next-session {
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-left: 3px solid #862736;
        border-radius: 14px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .next-session__icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #f4e8ea;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .next-session__icon svg { width: 24px; height: 24px; }

    .next-session__info { flex: 1; }

    .next-session__label {
        font-size: 0.68rem;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #8a8580;
        margin-bottom: 0.2rem;
    }

    .next-session__title {
        font-size: 1rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 0.1rem;
    }

    .next-session__meta {
        font-size: 0.8rem;
        color: #8a8580;
    }

    .next-session__action {
        flex-shrink: 0;
    }

    /* ── EDITOR CARDS ─────────────────────────────────── */
    .editor-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 0.75rem;
    }

    .editor-card {
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 10px;
        padding: 1.25rem;
        text-align: center;
        transition: border-color 0.15s;
    }

    .editor-card:hover { border-color: rgba(0, 0, 0, 0.12); }

    .editor-card__avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #f4e8ea;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.75rem;
        font-size: 1rem;
        font-weight: 700;
        color: #862736;
    }

    .editor-card__name {
        font-size: 0.9rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 0.15rem;
    }

    .editor-card__specialty {
        font-size: 0.75rem;
        color: #8a8580;
        margin-bottom: 0.75rem;
    }

    .editor-card__availability {
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.2rem 0.55rem;
        border-radius: 4px;
        display: inline-block;
    }

    .editor-card__availability--available { background: #e8f5e9; color: #2e7d32; }
    .editor-card__availability--busy { background: #fff3e0; color: #e65100; }

    /* ── SESSION LIST ─────────────────────────────────── */
    .session-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .session-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.85rem 1rem;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 10px;
        transition: border-color 0.15s;
    }

    .session-item:hover { border-color: rgba(0, 0, 0, 0.12); }

    .session-item__date {
        text-align: center;
        min-width: 42px;
        flex-shrink: 0;
    }

    .session-item__day {
        font-size: 1.15rem;
        font-weight: 700;
        color: #862736;
        line-height: 1;
    }

    .session-item__month {
        font-size: 0.6rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #862736;
        margin-top: 2px;
    }

    .session-item__info { flex: 1; }

    .session-item__title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 0.1rem;
    }

    .session-item__meta {
        font-size: 0.75rem;
        color: #8a8580;
    }

    .session-item__badge {
        font-size: 0.65rem;
        font-weight: 600;
        padding: 0.2rem 0.55rem;
        border-radius: 4px;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .session-item__badge--completed { background: #e8f5e9; color: #2e7d32; }
    .session-item__badge--upcoming { background: #e3f2fd; color: #1565c0; }

    /* ── BOOKING SECTION ──────────────────────────────── */
    .booking-card {
        background: #faf8f5;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 14px;
        padding: 2rem;
        text-align: center;
    }

    .booking-card__title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 0.35rem;
    }

    .booking-card__desc {
        font-size: 0.85rem;
        color: #5a5550;
        margin-bottom: 1.25rem;
        max-width: 480px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.6;
    }

    /* ── EMPTY STATE ──────────────────────────────────── */
    .empty-state {
        text-align: center;
        padding: 2rem;
        color: #8a8580;
        font-size: 0.875rem;
    }

    /* ── BUTTONS ──────────────────────────────────────── */
    .btn-coaching {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.55rem 1.15rem;
        border-radius: 6px;
        font-size: 0.825rem;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.15s;
    }

    .btn-coaching--primary { background: #862736; color: #fff; }
    .btn-coaching--primary:hover { background: #9c2e40; color: #fff; text-decoration: none; }

    .btn-coaching--secondary {
        background: transparent;
        color: #5a5550;
        border: 1px solid rgba(0, 0, 0, 0.12);
    }
    .btn-coaching--secondary:hover { border-color: #862736; color: #862736; text-decoration: none; }

    .btn-coaching--outline {
        background: transparent;
        color: #862736;
        border: 1px solid #862736;
    }
    .btn-coaching--outline:hover { background: #862736; color: #fff; text-decoration: none; }

    /* ── ADD COACHING BTN ─────────────────────────────── */
    .add-coaching-btn {
        margin-bottom: 1rem;
        text-align: right;
    }

    /* ── BOOKING FORM ─────────────────────────────────── */
    .booking-card .form-group { margin-top: 1rem; }
    .booking-card .form-group label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #5a5550;
        margin-bottom: 0.35rem;
        display: block;
    }
    .booking-card .form-control {
        max-width: 360px;
        margin: 0 auto;
    }

    @media (max-width: 600px) {
        .stats-row { grid-template-columns: repeat(2, 1fr); }
        .editor-grid { grid-template-columns: 1fr; }
        .next-session { flex-direction: column; align-items: flex-start; }
        .session-item { flex-direction: column; align-items: flex-start; gap: 0.5rem; }
    }
</style>
@stop

@section('content')
<div class="learner-container coaching-time-wrapper">
    <div class="container" style="max-width: 880px;">

        {{-- ═══════ PAGE HEADER ═══════ --}}
        <div style="margin-bottom: 1.5rem;">
            <h1 class="page-title" style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.25rem;">
                {{ trans('site.coaching-timer-text') }}
            </h1>
            <p style="font-size: 0.875rem; color: #5a5550; margin: 0;">
                Book en-til-en-timer med våre redaktører for personlig veiledning på manuset ditt.
            </p>
        </div>

        {{-- ═══════ INLINE PHP: check coaching eligibility ═══════ --}}
        <?php
            $packages = \App\Package::where('has_coaching', '>', 0)->pluck('id');
            $coachingTimerTaken = Auth::user()->coachingTimersTaken()->pluck('course_taken_id');
            $checkCourseTakenWithCoaching = Auth::user()->coursesTaken()->whereIn('package_id', $packages)
                ->whereNotIn('id', $coachingTimerTaken)->get();
        ?>

        {{-- ═══════ ADD COACHING SESSION BUTTON ═══════ --}}
        @if($checkCourseTakenWithCoaching->count())
            <div class="add-coaching-btn">
                <button class="btn-coaching btn-coaching--outline"
                        data-bs-toggle="modal"
                        data-bs-target="#addCoachingSessionModal"
                        data-action="{{ route('learner.course-taken.coaching-timer.add') }}"
                        id="addCoachingSessionBtn">
                    {{ trans('site.learner.add-coaching-lesson') }}
                    <i class="fa fa-plus" style="margin-left: 0.25rem;"></i>
                </button>
            </div>
        @endif

        {{-- ═══════ SUCCESS ALERT ═══════ --}}
        @if(session('success'))
            <div class="alert alert-success">
                <a href="#" class="close" data-bs-dismiss="alert" aria-label="close" title="close">×</a>
                {{ session('success') }}
            </div>
        @endif

        {{-- ═══════ COMPUTE STATS ═══════ --}}
        @php
            $nextSession = $bookedSessions->first();
            $completedCount = $bookedSessions->filter(function ($s) {
                $d = \Carbon\Carbon::parse($s->timeSlot->date.' '.$s->timeSlot->start_time, 'UTC')
                    ->setTimezone(config('app.timezone'));
                return $d->isPast();
            })->count();
            $activeCount = $bookedSessions->count() - $completedCount;

            // Compute next session date label
            $nextDateLabel = '–';
            $nextTimeMeta = '';
            if ($nextSession) {
                $nextDate = \Carbon\Carbon::parse(
                    $nextSession->timeSlot->date.' '.$nextSession->timeSlot->start_time,
                    'UTC'
                )->setTimezone(config('app.timezone'));
                if ($nextDate->isToday()) {
                    $nextDateLabel = 'I dag';
                } elseif ($nextDate->isTomorrow()) {
                    $nextDateLabel = 'I morgen';
                } elseif ($nextDate->isSameWeek(\Carbon\Carbon::now(config('app.timezone')))) {
                    $nextDateLabel = ucfirst($nextDate->locale(app()->getLocale())->dayName);
                } else {
                    $nextDateLabel = $nextDate->format('d.m');
                }
                $nextTimeMeta = $nextDate->format('d.m.Y') . ' kl. ' . $nextDate->format('H:i');
            }
        @endphp

        {{-- ═══════ STATS ROW (3 kort) ═══════ --}}
        <div class="stats-row" style="grid-template-columns: repeat(3, 1fr);">
            <div class="stat-card">
                <div class="stat-card__number">{{ $activeCount }}</div>
                <div class="stat-card__label">{{ trans('site.coaching-time-booked-sessions') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__number">{{ $nextDateLabel }}</div>
                <div class="stat-card__label">{{ trans('site.coaching-time-next-editorial') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__number stat-card__number--green">{{ $completedCount }}</div>
                <div class="stat-card__label">{{ trans('site.coaching-time-completed') }}</div>
            </div>
        </div>

        {{-- ═══════ NEXT SESSION (if booked) ═══════ --}}
        @if($nextSession)
            <div class="next-session">
                <div class="next-session__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="next-session__info">
                    <div class="next-session__label">Neste coaching-time</div>
                    <div class="next-session__title">Med {{ optional($nextSession->editor)->full_name }}</div>
                    <div class="next-session__meta">{{ $nextTimeMeta }}</div>
                </div>
                <div class="next-session__action">
                    <a href="{{ route('learner.coaching-time.available') }}" class="btn-coaching btn-coaching--primary">Se ledige tider →</a>
                </div>
            </div>
        @endif

        {{-- ═══════ BOOK COACHING TIME CTA ═══════ --}}
        @if($coachingTimers->count() >= 1)
            {{-- Elev har coaching-timer fra kurs — vis direkte booking --}}
            <div class="booking-card">
                <div class="booking-card__title">{{ trans('site.coaching-time-book-editorial-class') }}</div>
                <div class="booking-card__desc">{{ trans('site.coaching-time-book-editorial-class-description') }}</div>
                <form method="GET" action="{{ route('learner.coaching-time.available') }}">
                    @if($coachingTimers->count() > 1)
                        <div class="form-group">
                            <label for="coaching_timer_id">
                                {{ trans('site.learner.coaching-time') }}
                            </label>
                            <select name="coaching_timer_id" id="coaching_timer_id" class="form-control">
                                @foreach($coachingTimers as $timer)
                                    <option value="{{ $timer->id }}">
                                        {{ trans('site.learner.coaching-time') }} -
                                        {{ FrontendHelpers::getCoachingTimerPlanType($timer->plan_type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="coaching_timer_id" value="{{ $coachingTimers->first()->id }}">
                    @endif
                    <button type="submit" class="btn-coaching btn-coaching--primary" style="margin-top: 1rem;">
                        {{ trans('site.coaching-time-see-available-slots') }}
                    </button>
                </form>
            </div>
        @endif

        {{-- ═══════ MY SESSIONS ═══════ --}}
        <div style="margin-top: 2rem;">
            <div class="section-label">{{ trans('site.coaching-time-my-sessions') }}</div>

            <div class="coaching-card">
                <div class="coaching-card__body" style="padding-top: 1.5rem;">
                    @if($bookedSessions->isEmpty())
                        <div class="empty-state">
                            {{ trans('site.coaching-time-no-upcoming-sessions') }}
                        </div>
                    @else
                        <div class="session-list">
                            @foreach($bookedSessions as $session)
                                @php
                                    $sessionDate = \Carbon\Carbon::parse(
                                        $session->timeSlot->date.' '.$session->timeSlot->start_time,
                                        'UTC'
                                    )->setTimezone(config('app.timezone'));

                                    $duration = $session->plan_type == 1 ? '60 min' : '30 min';
                                    $norwegianMonths = ['jan', 'feb', 'mar', 'apr', 'mai', 'jun', 'jul', 'aug', 'sep', 'okt', 'nov', 'des'];
                                    $monthShort = $norwegianMonths[$sessionDate->month - 1];
                                    $isCompleted = $sessionDate->isPast();
                                    $hasEditor = optional($session->editor)->full_name;
                                @endphp
                                <div class="session-item {{ $loop->iteration > 4 ? 'd-none extra-session' : '' }}"
                                     style="{{ !$isCompleted && !$hasEditor ? 'border-left: 3px solid #e65100;' : (!$isCompleted ? 'border-left: 3px solid #1565c0;' : '') }}">
                                    @if($isCompleted || $hasEditor)
                                        <div class="session-item__date">
                                            <div class="session-item__day">{{ $sessionDate->format('d') }}</div>
                                            <div class="session-item__month">{{ $monthShort }}</div>
                                        </div>
                                    @else
                                        <div style="text-align: center; min-width: 42px; flex-shrink: 0;">
                                            <div style="font-size: 0.7rem; font-weight: 600; color: #e65100;">{{ $duration }}</div>
                                        </div>
                                    @endif
                                    <div class="session-item__info">
                                        <div class="session-item__title">
                                            @if($hasEditor)
                                                Coaching med {{ $hasEditor }}
                                            @else
                                                Coaching – {{ $duration }}
                                            @endif
                                        </div>
                                        <div class="session-item__meta">
                                            @if($isCompleted)
                                                {{ $duration }} · {{ trans('site.coaching-time-completed') }}
                                            @elseif($hasEditor)
                                                {{ $duration }} · Kl. {{ $sessionDate->format('H:i') }}
                                            @else
                                                Tildeles redaktør
                                            @endif
                                        </div>
                                    </div>
                                    @if($isCompleted)
                                        <span class="session-item__badge session-item__badge--completed">{{ trans('site.coaching-time-completed') }}</span>
                                    @elseif(!$hasEditor)
                                        <span class="session-item__badge" style="background: #fff3e0; color: #e65100;">{{ trans('site.coaching-time-waiting-editor') }}</span>
                                    @else
                                        <a href="{{ route('learner.coaching-timer.prepare', $session->id) }}" class="btn-coaching btn-coaching--secondary" style="margin-right: 0.5rem;">
                                            Last opp manus
                                        </a>
                                        <span class="session-item__badge session-item__badge--upcoming">{{ trans('site.coaching-time-scheduled') }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        @if($bookedSessions->count() > 4)
                            <div style="text-align: center; margin-top: 1rem;">
                                <button id="toggle-sessions" class="btn-coaching btn-coaching--secondary" data-showing="false">
                                    {{ trans('site.coaching-time-see-all-sessions') }}
                                </button>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- ═══════ BESTILL COACHING CTA ═══════ --}}
        <div class="booking-card" style="margin-top: 1.5rem;">
            <div class="booking-card__title">{{ trans('site.coaching-time-order-coaching') }}</div>
            <div class="booking-card__desc">{{ trans('site.coaching-time-order-coaching-desc') }}</div>
            <a href="/manusutvikling#coaching" class="btn-coaching btn-coaching--primary">{{ trans('site.coaching-time-see-prices') }}</a>
        </div>

    </div>
</div>

{{-- ═══════ ADD COACHING SESSION MODAL ═══════ --}}
<div id="addCoachingSessionModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{ trans('site.learner.add-coaching-session') }}</h3>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="" onsubmit="disableSubmit(this)" enctype="multipart/form-data">
                    {{csrf_field()}}

                    <div class="form-group">
                        <label>{{ trans('site.learner.manuscript-text') }}</label>
                        <input type="file" class="form-control" name="manuscript"
                               accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                    </div>

                    @if ($checkCourseTakenWithCoaching->count())
                        <div class="form-group">
                            <label>{{ trans('site.learner.use-course-included-session') }}</label>
                            <select name="course_taken_id" class="form-control" required id="course_taken_id">
                                <option value="" disabled selected> -- {{ trans('site.learner.select-text') }} --</option>
                                @foreach($checkCourseTakenWithCoaching as $courseTaken)
                                    <option value="{{ $courseTaken->id }}" data-plan="{{ $courseTaken->package->has_coaching }}">
                                        {{ $courseTaken->package->course->title }} - {{ \App\Http\FrontendHelpers::getCoachingTimerPlanType($courseTaken->package->has_coaching) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="plan_type">
                    @endif

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-success">{{ trans('site.front.submit') }}</button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('site.front.cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let translations = {
        pleaseWait : "{{ trans('site.please-wait') }}"
    };

    document.addEventListener('DOMContentLoaded', function () {
        var toggle = document.getElementById('toggle-sessions');
        if (!toggle) {
            return;
        }
        toggle.addEventListener('click', function () {
            var extras = document.querySelectorAll('.extra-session');
            var showing = toggle.getAttribute('data-showing') === 'true';
            extras.forEach(function (item) {
                item.classList.toggle('d-none');
            });
            toggle.setAttribute('data-showing', showing ? 'false' : 'true');
            toggle.textContent = showing ? '{{ trans("site.coaching-time-see-all-sessions") }}' : 'Skjul timer';
        });
    });

    $("#addCoachingSessionBtn").click(function(){
        let action = $(this).data('action');
        let form = $("#addCoachingSessionModal").find('form');

        form.attr('action', action);
    });

    $("#course_taken_id").change(function(){
        let plan = $(this).find(':selected').data('plan');
        let form = $("#addCoachingSessionModal").find('form');

        form.find('[name=plan_type]').val(plan);
    });

    function disableSubmit(t) {
        let submit_btn = $(t).find('[type=submit]');
        submit_btn.text('');
        submit_btn.append('<i class="fa fa-spinner fa-pulse"></i> ' + translations.pleaseWait);
        submit_btn.attr('disabled', 'disabled');
    }
</script>
@endsection
