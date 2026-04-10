@extends('frontend.layouts.course-portal')

@section('page_title', 'Coaching &rsaquo; Forfatterskolen')
@section('robots', '<meta name="robots" content="noindex, follow">')
@section('meta_desc', 'Book og administrer coaching-timer.')

@section('styles')
<style>
    .ct { font-family: 'Source Sans 3', -apple-system, sans-serif; -webkit-font-smoothing: antialiased; }

    /* ── HEADER ───────────────────────────────────────── */
    .ct-header {
        background: linear-gradient(135deg, #862736 0%, #5e1a26 100%);
        border-radius: 14px;
        padding: 2rem 2.25rem;
        color: #fff;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .ct-header::after {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 180px; height: 180px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }
    .ct-header__title { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.25rem; position: relative; }
    .ct-header__desc { font-size: 0.875rem; opacity: 0.85; margin: 0; position: relative; line-height: 1.5; }

    /* ── STATS ────────────────────────────────────────── */
    .ct-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }
    .ct-stat {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.08);
        border-radius: 12px;
        padding: 1.15rem 1rem;
        text-align: center;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .ct-stat:hover { border-color: rgba(0,0,0,0.12); box-shadow: 0 2px 12px rgba(0,0,0,0.04); }
    .ct-stat__icon { margin-bottom: 0.5rem; }
    .ct-stat__number { font-size: 1.75rem; font-weight: 700; color: #1a1a1a; line-height: 1; margin-bottom: 0.2rem; }
    .ct-stat__number--wine { color: #862736; }
    .ct-stat__number--green { color: #2e7d32; }
    .ct-stat__label { font-size: 0.72rem; font-weight: 500; color: #8a8580; }

    /* ── NEXT SESSION ─────────────────────────────────── */
    .ct-next {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.08);
        border-left: 4px solid #862736;
        border-radius: 14px;
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        transition: box-shadow 0.15s;
    }
    .ct-next:hover { box-shadow: 0 4px 16px rgba(134,39,54,0.08); }
    .ct-next__avatar {
        width: 52px; height: 52px;
        border-radius: 50%;
        background: #f4e8ea;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        font-size: 1.1rem; font-weight: 700; color: #862736;
    }
    .ct-next__info { flex: 1; }
    .ct-next__label { font-size: 0.68rem; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; color: #8a8580; margin-bottom: 0.15rem; }
    .ct-next__title { font-size: 1.05rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.1rem; }
    .ct-next__meta { font-size: 0.8rem; color: #8a8580; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
    .ct-next__meta-dot { width: 3px; height: 3px; border-radius: 50%; background: #ccc; }
    .ct-next__actions { display: flex; gap: 0.5rem; flex-shrink: 0; flex-wrap: wrap; }

    .ct-next__countdown {
        display: inline-flex; align-items: center; gap: 0.3rem;
        font-size: 0.7rem; font-weight: 600;
        padding: 0.2rem 0.6rem; border-radius: 4px;
        background: #fce8ea; color: #862736;
    }

    /* ── SECTION LABEL ────────────────────────────────── */
    .ct-section-label {
        font-size: 0.7rem; font-weight: 600; letter-spacing: 1.5px;
        text-transform: uppercase; color: #8a8580; margin-bottom: 0.75rem;
    }

    /* ── SESSION CARD ─────────────────────────────────── */
    .ct-sessions {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.08);
        border-radius: 14px;
        overflow: hidden;
    }
    .ct-sessions__body { padding: 1.25rem; }

    .ct-session {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.15rem;
        border: 1px solid rgba(0,0,0,0.08);
        border-radius: 12px;
        margin-bottom: 0.5rem;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .ct-session:last-child { margin-bottom: 0; }
    .ct-session:hover { border-color: rgba(0,0,0,0.12); box-shadow: 0 2px 8px rgba(0,0,0,0.03); }

    .ct-session--upcoming { border-left: 3px solid #1565c0; }
    .ct-session--waiting { border-left: 3px solid #e65100; }
    .ct-session--completed { border-left: 3px solid #c3e6cb; opacity: 0.75; }
    .ct-session--completed:hover { opacity: 1; }

    .ct-session__date {
        text-align: center; min-width: 46px; flex-shrink: 0;
        background: #faf8f5; border-radius: 8px; padding: 0.4rem 0.35rem;
    }
    .ct-session__day { font-size: 1.15rem; font-weight: 700; color: #862736; line-height: 1; }
    .ct-session__month { font-size: 0.6rem; font-weight: 600; text-transform: uppercase; color: #862736; margin-top: 2px; }

    .ct-session__info { flex: 1; min-width: 0; }
    .ct-session__title { font-size: 0.875rem; font-weight: 600; color: #1a1a1a; margin-bottom: 0.1rem; }
    .ct-session__meta { font-size: 0.75rem; color: #8a8580; display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap; }

    .ct-session__tags { display: flex; align-items: center; gap: 0.4rem; flex-shrink: 0; flex-wrap: wrap; }

    .ct-badge {
        font-size: 0.65rem; font-weight: 600;
        padding: 0.2rem 0.55rem; border-radius: 4px;
        white-space: nowrap;
    }
    .ct-badge--completed { background: #e8f5e9; color: #2e7d32; }
    .ct-badge--upcoming { background: #e3f2fd; color: #1565c0; }
    .ct-badge--waiting { background: #fff3e0; color: #e65100; }
    .ct-badge--uploaded { background: #e8f5e9; color: #2e7d32; }

    /* ── BUTTONS ──────────────────────────────────────── */
    .ct-btn {
        display: inline-flex; align-items: center; gap: 0.3rem;
        padding: 0.5rem 1rem; border-radius: 6px;
        font-size: 0.8rem; font-weight: 600;
        text-decoration: none; cursor: pointer; border: none;
        transition: all 0.15s;
    }
    .ct-btn--primary { background: #862736; color: #fff; }
    .ct-btn--primary:hover { background: #9c2e40; color: #fff; text-decoration: none; }
    .ct-btn--secondary { background: transparent; color: #5a5550; border: 1px solid rgba(0,0,0,0.12); }
    .ct-btn--secondary:hover { border-color: #862736; color: #862736; text-decoration: none; }
    .ct-btn--outline { background: transparent; color: #862736; border: 1px solid #862736; }
    .ct-btn--outline:hover { background: #862736; color: #fff; text-decoration: none; }
    .ct-btn--small { padding: 0.35rem 0.75rem; font-size: 0.75rem; }

    /* ── BOOKING CTA ─────────────────────────────────── */
    .ct-booking {
        background: #faf8f5;
        border: 1px solid rgba(0,0,0,0.08);
        border-radius: 14px;
        padding: 2.5rem 2rem;
        text-align: center;
        margin-top: 1.5rem;
    }
    .ct-booking__icon { margin-bottom: 0.75rem; }
    .ct-booking__title { font-size: 1.1rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.35rem; }
    .ct-booking__desc {
        font-size: 0.85rem; color: #5a5550; margin-bottom: 1.25rem;
        max-width: 440px; margin-left: auto; margin-right: auto; line-height: 1.6;
    }
    .ct-booking .form-group { margin-top: 1rem; }
    .ct-booking .form-group label { font-size: 0.8rem; font-weight: 600; color: #5a5550; margin-bottom: 0.35rem; display: block; }
    .ct-booking .form-control { max-width: 360px; margin: 0 auto; }

    /* ── ADD SESSION BTN ─────────────────────────────── */
    .ct-add-btn { margin-bottom: 1rem; text-align: right; }

    /* ── EMPTY STATE ──────────────────────────────────── */
    .ct-empty {
        text-align: center; padding: 2.5rem 1.5rem; color: #8a8580;
    }
    .ct-empty__icon { margin-bottom: 0.75rem; opacity: 0.4; }
    .ct-empty__text { font-size: 0.875rem; margin-bottom: 1rem; }

    /* ── HOW IT WORKS ─────────────────────────────────── */
    .ct-steps {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-top: 1.5rem;
    }
    .ct-step {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.08);
        border-radius: 12px;
        padding: 1.25rem;
        text-align: center;
        transition: border-color 0.15s;
    }
    .ct-step:hover { border-color: rgba(134,39,54,0.2); }
    .ct-step__num {
        width: 28px; height: 28px; border-radius: 50%;
        background: #f4e8ea; color: #862736;
        font-size: 0.75rem; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 0.65rem;
    }
    .ct-step__title { font-size: 0.85rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.2rem; }
    .ct-step__desc { font-size: 0.75rem; color: #8a8580; line-height: 1.5; }

    @media (max-width: 768px) {
        .ct-header { padding: 1.5rem; }
        .ct-stats { grid-template-columns: repeat(3, 1fr); }
        .ct-next { flex-direction: column; align-items: flex-start; }
        .ct-next__actions { width: 100%; }
        .ct-session { flex-wrap: wrap; }
        .ct-session__tags { width: 100%; margin-top: 0.25rem; }
        .ct-steps { grid-template-columns: 1fr; }
    }
    @media (max-width: 480px) {
        .ct-stats { grid-template-columns: 1fr; }
        .ct-session { flex-direction: column; align-items: flex-start; gap: 0.5rem; }
    }
</style>
@stop

@section('content')
<div class="learner-container ct">
    <div class="container" style="max-width: 880px;">

        {{-- ═══════ INLINE PHP: check coaching eligibility ═══════ --}}
        <?php
            $packages = \App\Package::where('has_coaching', '>', 0)->pluck('id');
            $coachingTimerTaken = Auth::user()->coachingTimersTaken()->pluck('course_taken_id');
            $checkCourseTakenWithCoaching = Auth::user()->coursesTaken()->whereIn('package_id', $packages)
                ->whereNotIn('id', $coachingTimerTaken)->get();
        ?>

        {{-- ═══════ COMPUTE STATS ═══════ --}}
        @php
            $nextSession = $bookedSessions->first();
            $completedCount = $bookedSessions->filter(function ($s) {
                $d = \Carbon\Carbon::parse($s->timeSlot->date.' '.$s->timeSlot->start_time, 'UTC')
                    ->setTimezone(config('app.timezone'));
                return $d->isPast();
            })->count();
            $activeCount = $bookedSessions->count() - $completedCount;

            $nextDateLabel = '–';
            $nextTimeMeta = '';
            $nextDate = null;
            $daysUntilNext = null;
            if ($nextSession) {
                $nextDate = \Carbon\Carbon::parse(
                    $nextSession->timeSlot->date.' '.$nextSession->timeSlot->start_time, 'UTC'
                )->setTimezone(config('app.timezone'));
                $daysUntilNext = (int) \Carbon\Carbon::now(config('app.timezone'))->diffInDays($nextDate, false);
                if ($nextDate->isToday()) {
                    $nextDateLabel = 'I dag';
                } elseif ($nextDate->isTomorrow()) {
                    $nextDateLabel = 'I morgen';
                } elseif ($nextDate->isSameWeek(\Carbon\Carbon::now(config('app.timezone')))) {
                    $norwegianDays = ['søndag','mandag','tirsdag','onsdag','torsdag','fredag','lørdag'];
                    $nextDateLabel = ucfirst($norwegianDays[$nextDate->dayOfWeek]);
                } else {
                    $nextDateLabel = $nextDate->format('d.m');
                }
                $nextTimeMeta = $nextDate->format('d.m.Y') . ' kl. ' . $nextDate->format('H:i');
            }
        @endphp

        {{-- ═══════ HEADER ═══════ --}}
        <div class="ct-header">
            <h1 class="ct-header__title">Coaching</h1>
            <p class="ct-header__desc">
                Personlig veiledning med en erfaren redaktør — en-til-en, tilpasset ditt manus og dine mål.
            </p>
        </div>

        {{-- ═══════ SUCCESS ALERT ═══════ --}}
        @if(session('success'))
            <div class="alert alert-success" style="border-radius: 10px; margin-bottom: 1.25rem;">
                <a href="#" class="close" data-bs-dismiss="alert" aria-label="close" title="close">&times;</a>
                {{ session('success') }}
            </div>
        @endif

        {{-- ═══════ ADD COACHING SESSION BUTTON ═══════ --}}
        @if($checkCourseTakenWithCoaching->count())
            <div class="ct-add-btn">
                <button class="ct-btn ct-btn--outline"
                        data-bs-toggle="modal"
                        data-bs-target="#addCoachingSessionModal"
                        data-action="{{ route('learner.course-taken.coaching-timer.add') }}"
                        id="addCoachingSessionBtn">
                    {{ trans('site.learner.add-coaching-lesson') }}
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </button>
            </div>
        @endif

        {{-- ═══════ STATS ═══════ --}}
        <div class="ct-stats">
            <div class="ct-stat">
                <div class="ct-stat__icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <div class="ct-stat__number ct-stat__number--wine">{{ $activeCount }}</div>
                <div class="ct-stat__label">{{ trans('site.coaching-time-booked-sessions') }}</div>
            </div>
            <div class="ct-stat">
                <div class="ct-stat__icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1565c0" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="ct-stat__number">{{ $nextDateLabel }}</div>
                <div class="ct-stat__label">{{ trans('site.coaching-time-next-editorial') }}</div>
            </div>
            <div class="ct-stat">
                <div class="ct-stat__icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2e7d32" stroke-width="1.5" stroke-linecap="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div class="ct-stat__number ct-stat__number--green">{{ $completedCount }}</div>
                <div class="ct-stat__label">{{ trans('site.coaching-time-completed') }}</div>
            </div>
        </div>

        {{-- ═══════ NEXT SESSION (if booked) ═══════ --}}
        @if($nextSession)
            @php
                $editorName = optional($nextSession->editor)->full_name;
                $editorInitials = optional($nextSession->editor)->first_name
                    ? mb_substr($nextSession->editor->first_name, 0, 1) . mb_substr($nextSession->editor->last_name, 0, 1)
                    : '?';
                $hasPrep = !empty($nextSession->preparation_file);
            @endphp
            <div class="ct-next">
                <div class="ct-next__avatar">{{ $editorInitials }}</div>
                <div class="ct-next__info">
                    <div class="ct-next__label">Neste coaching-time</div>
                    <div class="ct-next__title">{{ $editorName ?: 'Tildeles redaktør' }}</div>
                    <div class="ct-next__meta">
                        <span>{{ $nextTimeMeta }}</span>
                        <span class="ct-next__meta-dot"></span>
                        <span>{{ $nextSession->plan_type == 1 ? '60 min' : '30 min' }}</span>
                        @if($daysUntilNext !== null && $daysUntilNext >= 0 && $daysUntilNext <= 7)
                            <span class="ct-next__countdown">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                @if($daysUntilNext == 0)
                                    I dag!
                                @elseif($daysUntilNext == 1)
                                    I morgen
                                @else
                                    Om {{ $daysUntilNext }} dager
                                @endif
                            </span>
                        @endif
                    </div>
                </div>
                <div class="ct-next__actions">
                    @if($editorName)
                        <a href="{{ route('learner.coaching-timer.prepare', $nextSession->id) }}" class="ct-btn ct-btn--{{ $hasPrep ? 'secondary' : 'primary' }}">
                            {{ $hasPrep ? 'Manus lastet opp' : 'Last opp manus' }}
                        </a>
                    @endif
                </div>
            </div>
        @endif

        {{-- ═══════ BOOK COACHING TIME (if available) ═══════ --}}
        @if($coachingTimers->count() >= 1)
            <div class="ct-booking" style="margin-top: 0; margin-bottom: 1.5rem;">
                <div class="ct-booking__icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="12" y1="14" x2="12" y2="18"/><line x1="10" y1="16" x2="14" y2="16"/></svg>
                </div>
                <div class="ct-booking__title">{{ trans('site.coaching-time-book-editorial-class') }}</div>
                <div class="ct-booking__desc">{{ trans('site.coaching-time-book-editorial-class-description') }}</div>
                <form method="GET" action="{{ route('learner.coaching-time.available') }}">
                    @if($coachingTimers->count() > 1)
                        <div class="form-group">
                            <label for="coaching_timer_id">{{ trans('site.learner.coaching-time') }}</label>
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
                    <button type="submit" class="ct-btn ct-btn--primary" style="margin-top: 1rem;">
                        {{ trans('site.coaching-time-see-available-slots') }}
                    </button>
                </form>
            </div>
        @endif

        {{-- ═══════ MY SESSIONS ═══════ --}}
        <div style="margin-top: 0.5rem;">
            <div class="ct-section-label">{{ trans('site.coaching-time-my-sessions') }}</div>

            <div class="ct-sessions">
                <div class="ct-sessions__body">
                    @if($bookedSessions->isEmpty())
                        <div class="ct-empty">
                            <div class="ct-empty__icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#8a8580" stroke-width="1" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            </div>
                            <div class="ct-empty__text">{{ trans('site.coaching-time-no-upcoming-sessions') }}</div>
                        </div>
                    @else
                        @foreach($bookedSessions as $session)
                            @php
                                $sessionDate = \Carbon\Carbon::parse(
                                    $session->timeSlot->date.' '.$session->timeSlot->start_time, 'UTC'
                                )->setTimezone(config('app.timezone'));

                                $duration = $session->plan_type == 1 ? '60 min' : '30 min';
                                $norwegianMonths = ['jan', 'feb', 'mar', 'apr', 'mai', 'jun', 'jul', 'aug', 'sep', 'okt', 'nov', 'des'];
                                $monthShort = $norwegianMonths[$sessionDate->month - 1];
                                $isCompleted = $sessionDate->isPast();
                                $hasEditor = optional($session->editor)->full_name;
                                $hasPrep = !empty($session->preparation_file);
                            @endphp
                            <div class="ct-session {{ $loop->iteration > 5 ? 'd-none extra-session' : '' }} {{ $isCompleted ? 'ct-session--completed' : ($hasEditor ? 'ct-session--upcoming' : 'ct-session--waiting') }}">
                                <div class="ct-session__date">
                                    <div class="ct-session__day">{{ $sessionDate->format('d') }}</div>
                                    <div class="ct-session__month">{{ $monthShort }}</div>
                                </div>
                                <div class="ct-session__info">
                                    <div class="ct-session__title">
                                        @if($hasEditor)
                                            Coaching med {{ $hasEditor }}
                                        @else
                                            Coaching &ndash; {{ $duration }}
                                        @endif
                                    </div>
                                    <div class="ct-session__meta">
                                        <span>{{ $duration }}</span>
                                        @if($hasEditor && !$isCompleted)
                                            <span class="ct-next__meta-dot"></span>
                                            <span>Kl. {{ $sessionDate->format('H:i') }}</span>
                                        @endif
                                        @if($isCompleted)
                                            <span class="ct-next__meta-dot"></span>
                                            <span>{{ $sessionDate->format('d.m.Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="ct-session__tags">
                                    @if($isCompleted)
                                        <span class="ct-badge ct-badge--completed">{{ trans('site.coaching-time-completed') }}</span>
                                    @elseif(!$hasEditor)
                                        <span class="ct-badge ct-badge--waiting">{{ trans('site.coaching-time-waiting-editor') }}</span>
                                    @else
                                        @if($hasPrep)
                                            <span class="ct-badge ct-badge--uploaded">
                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="vertical-align: -1px;"><polyline points="20 6 9 17 4 12"/></svg>
                                                Manus
                                            </span>
                                        @else
                                            <a href="{{ route('learner.coaching-timer.prepare', $session->id) }}" class="ct-btn ct-btn--secondary ct-btn--small">
                                                Last opp manus
                                            </a>
                                        @endif
                                        <span class="ct-badge ct-badge--upcoming">{{ trans('site.coaching-time-scheduled') }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        @if($bookedSessions->count() > 5)
                            <div style="text-align: center; margin-top: 1rem;">
                                <button id="toggle-sessions" class="ct-btn ct-btn--secondary" data-showing="false">
                                    {{ trans('site.coaching-time-see-all-sessions') }}
                                </button>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- ═══════ HOW IT WORKS ═══════ --}}
        @if($bookedSessions->isEmpty())
        <div class="ct-steps">
            <div class="ct-step">
                <div class="ct-step__num">1</div>
                <div class="ct-step__title">Velg time</div>
                <div class="ct-step__desc">Finn en ledig tid som passer deg, med din foretrukne redaktør.</div>
            </div>
            <div class="ct-step">
                <div class="ct-step__num">2</div>
                <div class="ct-step__title">Last opp manus</div>
                <div class="ct-step__desc">Send inn teksten du vil jobbe med, slik at redaktøren kan forberede seg.</div>
            </div>
            <div class="ct-step">
                <div class="ct-step__num">3</div>
                <div class="ct-step__title">Ha timen</div>
                <div class="ct-step__desc">Få konkrete tilbakemeldinger og veiledning tilpasset ditt manus.</div>
            </div>
        </div>
        @endif

        {{-- ═══════ BESTILL COACHING CTA ═══════ --}}
        <div class="ct-booking">
            <div class="ct-booking__icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="ct-booking__title">{{ trans('site.coaching-time-order-coaching') }}</div>
            <div class="ct-booking__desc">{{ trans('site.coaching-time-order-coaching-desc') }}</div>
            <a href="/manusutvikling#coaching" class="ct-btn ct-btn--primary">{{ trans('site.coaching-time-see-prices') }}</a>
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
        if (!toggle) return;
        toggle.addEventListener('click', function () {
            var extras = document.querySelectorAll('.extra-session');
            var showing = toggle.getAttribute('data-showing') === 'true';
            extras.forEach(function (item) { item.classList.toggle('d-none'); });
            toggle.setAttribute('data-showing', showing ? 'false' : 'true');
            toggle.textContent = showing ? '{{ trans("site.coaching-time-see-all-sessions") }}' : 'Skjul timer';
        });
    });

    $("#addCoachingSessionBtn").click(function(){
        let action = $(this).data('action');
        $("#addCoachingSessionModal").find('form').attr('action', action);
    });

    $("#course_taken_id").change(function(){
        let plan = $(this).find(':selected').data('plan');
        $("#addCoachingSessionModal").find('[name=plan_type]').val(plan);
    });

    function disableSubmit(t) {
        let submit_btn = $(t).find('[type=submit]');
        submit_btn.text('');
        submit_btn.append('<i class="fa fa-spinner fa-pulse"></i> ' + translations.pleaseWait);
        submit_btn.attr('disabled', 'disabled');
    }
</script>
@endsection
