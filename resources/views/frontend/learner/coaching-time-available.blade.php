@extends('frontend.layouts.course-portal')

@section('page_title', 'Ledige tider &rsaquo; Forfatterskolen')
@section('meta_desc', 'Se ledige coaching-tider hos Forfatterskolen.')

@section('styles')
<style>
    .ca { font-family: 'Source Sans 3', -apple-system, sans-serif; -webkit-font-smoothing: antialiased; }

    .ca-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.75rem;
    }
    .ca-header__back {
        display: inline-flex; align-items: center; gap: 0.35rem;
        font-size: 0.825rem; font-weight: 600; color: #5a5550;
        text-decoration: none; padding: 0.45rem 0.85rem;
        border: 1px solid rgba(0,0,0,0.12); border-radius: 6px;
        transition: all 0.15s;
    }
    .ca-header__back:hover { border-color: #862736; color: #862736; text-decoration: none; }
    .ca-header__title { font-size: 1.35rem; font-weight: 700; color: #1a1a1a; margin: 0; }

    /* ── EDITOR SECTION ───────────────────────────────── */
    .ca-editor {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.08);
        border-radius: 14px;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    .ca-editor__header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(0,0,0,0.06);
        display: flex; align-items: center; gap: 1rem;
    }
    .ca-editor__avatar {
        width: 44px; height: 44px; border-radius: 50%;
        background: #f4e8ea; display: flex; align-items: center; justify-content: center;
        font-size: 0.95rem; font-weight: 700; color: #862736; flex-shrink: 0;
    }
    .ca-editor__name { font-size: 1.05rem; font-weight: 700; color: #1a1a1a; }
    .ca-editor__body { padding: 1.25rem 1.5rem; }

    /* ── DATE GROUP ────────────────────────────────────── */
    .ca-date {
        margin-bottom: 1.5rem;
    }
    .ca-date:last-child { margin-bottom: 0; }
    .ca-date__label {
        font-size: 0.8rem; font-weight: 600; color: #5a5550;
        margin-bottom: 0.65rem; display: flex; align-items: center; gap: 0.5rem;
    }
    .ca-date__day {
        font-weight: 700; color: #1a1a1a; text-transform: capitalize;
    }

    /* ── SLOT GRID ────────────────────────────────────── */
    .ca-slots {
        display: flex; flex-wrap: wrap; gap: 0.5rem;
    }

    .ca-slot {
        background: #fff;
        border: 2px solid rgba(0,0,0,0.08);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        min-width: 110px;
        text-align: center;
        transition: all 0.15s;
        cursor: default;
    }
    .ca-slot--available {
        cursor: pointer;
        border-color: #862736;
        background: #fdf5f6;
    }
    .ca-slot--available:hover {
        background: #862736;
        border-color: #862736;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(134,39,54,0.2);
    }
    .ca-slot--available:hover .ca-slot__time,
    .ca-slot--available:hover .ca-slot__duration,
    .ca-slot--available:hover .ca-slot__action { color: #fff; }

    .ca-slot--unavailable {
        opacity: 0.45;
        border-style: dashed;
    }
    .ca-slot--requested {
        border-color: #1565c0;
        background: #e3f2fd;
    }

    .ca-slot__time {
        font-size: 1.05rem; font-weight: 700; color: #1a1a1a;
        line-height: 1; margin-bottom: 0.2rem;
    }
    .ca-slot__duration {
        font-size: 0.7rem; color: #8a8580; margin-bottom: 0.3rem;
    }
    .ca-slot__action {
        font-size: 0.7rem; font-weight: 600;
    }
    .ca-slot--available .ca-slot__action { color: #862736; }
    .ca-slot--unavailable .ca-slot__action { color: #8a8580; }
    .ca-slot--requested .ca-slot__action { color: #1565c0; }

    /* ── PAGINATION ───────────────────────────────────── */
    .ca-pager {
        display: flex; justify-content: flex-end; gap: 0.35rem; margin-bottom: 0.75rem;
    }
    .ca-pager__btn {
        width: 34px; height: 34px; border-radius: 8px;
        border: 1px solid rgba(0,0,0,0.12); background: #fff;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.15s;
    }
    .ca-pager__btn:hover { border-color: #862736; }
    .ca-pager__btn:disabled { opacity: 0.3; cursor: default; }
    .ca-pager__btn svg { width: 16px; height: 16px; }

    /* ── SUGGEST SECTION ──────────────────────────────── */
    .ca-suggest {
        background: #faf8f5;
        border: 1px solid rgba(0,0,0,0.08);
        border-radius: 14px;
        padding: 2rem;
        text-align: center;
        margin-top: 1.5rem;
    }
    .ca-suggest__icon { margin-bottom: 0.5rem; }
    .ca-suggest__title { font-size: 1.05rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.25rem; }
    .ca-suggest__desc { font-size: 0.825rem; color: #5a5550; margin-bottom: 1.25rem; }

    .ca-suggest__form {
        max-width: 480px; margin: 0 auto; text-align: left;
    }
    .ca-suggest__row {
        display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;
        margin-bottom: 0.75rem;
    }
    .ca-suggest__group { margin-bottom: 0.75rem; }
    .ca-suggest__label {
        display: block; font-size: 0.8rem; font-weight: 600;
        color: #1a1a1a; margin-bottom: 0.35rem;
    }
    .ca-suggest__input {
        width: 100%; padding: 0.6rem 0.75rem;
        border: 1px solid rgba(0,0,0,0.15); border-radius: 6px;
        font-size: 0.85rem; transition: border-color 0.15s;
    }
    .ca-suggest__input:focus { outline: none; border-color: #862736; }

    .ca-btn {
        display: inline-flex; align-items: center; gap: 0.3rem;
        padding: 0.55rem 1.15rem; border-radius: 6px;
        font-size: 0.825rem; font-weight: 600;
        text-decoration: none; cursor: pointer; border: none;
        transition: all 0.15s;
    }
    .ca-btn--primary { background: #862736; color: #fff; }
    .ca-btn--primary:hover { background: #9c2e40; }

    /* ── EMPTY STATE ──────────────────────────────────── */
    .ca-empty {
        text-align: center; padding: 2rem; color: #8a8580;
    }
    .ca-empty__text { font-size: 0.875rem; margin-top: 0.5rem; }

    @media (max-width: 600px) {
        .ca-slots { gap: 0.4rem; }
        .ca-slot { min-width: 90px; padding: 0.65rem 0.5rem; }
        .ca-suggest__row { grid-template-columns: 1fr; }
        .ca-header { flex-direction: column; align-items: flex-start; }
    }
</style>
@stop

@section('content')
<div class="learner-container ca">
    <div class="container" style="max-width: 880px;">

        <div class="ca-header">
            <a href="{{ route('learner.coaching-time') }}" class="ca-header__back">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                Tilbake
            </a>
            <h1 class="ca-header__title">{{ trans('site.coaching-time-available-slots') }}</h1>
            <div></div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger" style="border-radius: 10px; margin-bottom: 1.25rem;">
                {{ session('error') }}
            </div>
        @endif

        @php
            $hasPendingRequest = $coachingTimer && $coachingTimer->requests->where('status', 'pending')->isNotEmpty();
        @endphp

        @if($coachingTimers->count())
            @foreach($editors as $editorSlots)
                @php
                    $editor = $editorSlots->first()->editor;
                    $initials = mb_substr($editor->first_name, 0, 1) . mb_substr($editor->last_name, 0, 1);
                    $dateGroups = $editorSlots->groupBy('date')->sortKeys();
                    $chunks = $dateGroups->chunk(7);
                    $norwegianDays = ['søndag','mandag','tirsdag','onsdag','torsdag','fredag','lørdag'];
                    $norwegianMonths = ['januar','februar','mars','april','mai','juni','juli','august','september','oktober','november','desember'];
                @endphp

                <div class="ca-editor">
                    <div class="ca-editor__header">
                        <div class="ca-editor__avatar">{{ $initials }}</div>
                        <div class="ca-editor__name">{{ $editor->full_name }}</div>
                    </div>

                    <div class="ca-editor__body">
                        <div class="editor-slots" id="editor-{{ $loop->index }}">
                            @if($chunks->count() > 1)
                                <div class="ca-pager">
                                    <button type="button" class="ca-pager__btn prev-btn" data-editor="{{ $loop->index }}" disabled>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                                    </button>
                                    <button type="button" class="ca-pager__btn next-btn" data-editor="{{ $loop->index }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
                                    </button>
                                </div>
                            @endif

                            @foreach($chunks as $page => $chunk)
                                <div class="editor-page" data-page="{{ $page }}" @if($page > 0) style="display:none;" @endif>
                                    @foreach($chunk as $date => $slots)
                                        @php
                                            $dt = \Carbon\Carbon::parse($date, 'UTC');
                                            $dayName = $norwegianDays[$dt->dayOfWeek];
                                            $monthName = $norwegianMonths[$dt->month - 1];
                                        @endphp
                                        <div class="ca-date">
                                            <div class="ca-date__label">
                                                <span class="ca-date__day">{{ $dayName }}</span>
                                                {{ $dt->day }}. {{ $monthName }}
                                            </div>
                                            <div class="ca-slots">
                                                @foreach($slots->sortBy('start_time') as $slot)
                                                    @php
                                                        $requested = $slot->requests
                                                            ->where('status', 'pending')
                                                            ->whereIn('coaching_timer_manuscript_id', $coachingTimers->pluck('id'))
                                                            ->isNotEmpty();

                                                        $declined = $slot->requests
                                                            ->where('status', 'declined')
                                                            ->whereIn('coaching_timer_manuscript_id', $coachingTimers->pluck('id'))
                                                            ->isNotEmpty();

                                                        $canBook = !$requested && !$declined && !$hasPendingRequest
                                                            && $coachingTimer
                                                            && (($coachingTimer->plan_type == 1 && $slot->duration == 60)
                                                                || ($coachingTimer->plan_type == 2 && $slot->duration == 30));
                                                    @endphp

                                                    @if($canBook)
                                                        <div class="ca-slot ca-slot--available book-slot-btn" data-slot-id="{{ $slot->id }}">
                                                            <div class="ca-slot__time slot-time" data-time="{{ \Carbon\Carbon::parse($slot->date.' '.$slot->start_time, 'UTC')->toIso8601String() }}"></div>
                                                            <div class="ca-slot__duration">{{ $slot->duration }} min</div>
                                                            <div class="ca-slot__action">{{ trans('site.coaching-time-book') }}</div>
                                                        </div>
                                                    @elseif($requested)
                                                        <div class="ca-slot ca-slot--requested">
                                                            <div class="ca-slot__time slot-time" data-time="{{ \Carbon\Carbon::parse($slot->date.' '.$slot->start_time, 'UTC')->toIso8601String() }}"></div>
                                                            <div class="ca-slot__duration">{{ $slot->duration }} min</div>
                                                            <div class="ca-slot__action">{{ trans('site.coaching-time-requested') }}</div>
                                                        </div>
                                                    @else
                                                        <div class="ca-slot ca-slot--unavailable">
                                                            <div class="ca-slot__time slot-time" data-time="{{ \Carbon\Carbon::parse($slot->date.' '.$slot->start_time, 'UTC')->toIso8601String() }}"></div>
                                                            <div class="ca-slot__duration">{{ $slot->duration }} min</div>
                                                            <div class="ca-slot__action">{{ trans('site.coaching-time-unavailable') }}</div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="ca-editor">
                <div class="ca-editor__body">
                    <div class="ca-empty">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#8a8580" stroke-width="1" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <div class="ca-empty__text">{{ trans('site.coaching-time-no-coaching-hours-available') }}</div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ═══════ SUGGEST OWN TIME ═══════ --}}
        @if($coachingTimer)
            <div class="ca-suggest">
                <div class="ca-suggest__icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="ca-suggest__title">Finner du ikke en tid som passer?</div>
                <div class="ca-suggest__desc">Foreslå et tidspunkt, så ser redaktøren det i sin portal.</div>

                <form action="{{ route('learner.coaching-time.suggest') }}" method="POST" onsubmit="disableSubmit(this)" class="ca-suggest__form">
                    @csrf
                    <input type="hidden" name="coaching_timer_id" value="{{ $coachingTimer->id }}">

                    <div class="ca-suggest__row">
                        <div>
                            <label class="ca-suggest__label">Ønsket dato</label>
                            <input type="date" name="suggested_date" class="ca-suggest__input" required
                                   min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}">
                        </div>
                        <div>
                            <label class="ca-suggest__label">Ønsket klokkeslett</label>
                            <input type="time" name="suggested_time" class="ca-suggest__input" required value="10:00">
                        </div>
                    </div>

                    <div class="ca-suggest__group">
                        <label class="ca-suggest__label">{{ trans('site.call-type') }}</label>
                        <select name="call_type" class="ca-suggest__input" required>
                            <option value="phone">{{ trans('site.phone-call') }}</option>
                            <option value="video">{{ trans('site.video-call') }}</option>
                        </select>
                    </div>

                    <div class="ca-suggest__group">
                        <label class="ca-suggest__label">Melding (valgfritt)</label>
                        <textarea name="message" class="ca-suggest__input" rows="3" style="resize: vertical;"
                                  placeholder="Er det noe spesielt du vil jobbe med?"></textarea>
                    </div>

                    <div style="text-align: center; margin-top: 1rem;">
                        <button type="submit" class="ca-btn ca-btn--primary">Send forslag</button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>

{{-- ═══════ BOOKING MODAL ═══════ --}}
@if ($coachingTimer)
    <button data-bs-target="#bookSlotModal" data-bs-toggle="modal" class="hidden" id="bookSlotModalTriggerBtn"></button>
    <div id="bookSlotModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-header" style="background: #faf8f5; border-bottom: 1px solid rgba(0,0,0,0.08); padding: 1.25rem 1.5rem;">
                    <h3 class="modal-title" style="font-size: 1.1rem; font-weight: 700; margin: 0;">Bekreft booking</h3>
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" style="padding: 1.5rem;">
                    <form action="{{ route('learner.coaching-time.request') }}" method="POST" id="bookSlotForm"
                        onsubmit="disableSubmit(this)">
                        @csrf
                        <input type="hidden" name="coaching_timer_id" value="{{ $coachingTimer->id }}">
                        <input type="hidden" name="editor_time_slot_id" value="">

                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 0.35rem;">
                                Hva ønsker du å jobbe med? (valgfritt)
                            </label>
                            <textarea name="help_with" rows="4" class="form-control" style="border-radius: 8px;"
                                      placeholder="Fortell redaktøren hva du ønsker å fokusere på i timen..."></textarea>
                        </div>

                        <div class="form-group" style="margin-bottom: 1.25rem;">
                            <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 0.35rem;">
                                {{ trans('site.call-type') }}
                            </label>
                            <select name="call_type" class="form-control" required style="border-radius: 8px;">
                                <option value="phone">{{ trans('site.phone-call') }}</option>
                                <option value="video">{{ trans('site.video-call') }}</option>
                            </select>
                        </div>

                        <div style="text-align: right;">
                            <button type="button" class="ca-btn" style="color: #5a5550; border: 1px solid rgba(0,0,0,0.12); margin-right: 0.5rem;" data-bs-dismiss="modal">
                                Avbryt
                            </button>
                            <button type="submit" class="ca-btn ca-btn--primary">
                                Bekreft booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@section('scripts')
<script>
    let translations = {
        pleaseWait: "{{ trans('site.please-wait') }}"
    };

    document.addEventListener('DOMContentLoaded', function () {
        // Format times to local timezone
        document.querySelectorAll('.slot-time').forEach(function (el) {
            const dt = new Date(el.dataset.time);
            el.textContent = dt.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
        });

        // Pagination
        document.querySelectorAll('.editor-slots').forEach(function (wrapper) {
            const pages = wrapper.querySelectorAll('.editor-page');
            if (pages.length <= 1) return;

            let current = 0;
            const prevBtn = wrapper.querySelector('.prev-btn');
            const nextBtn = wrapper.querySelector('.next-btn');

            function update() {
                prevBtn.disabled = current === 0;
                nextBtn.disabled = current === pages.length - 1;
            }
            function show(i) {
                pages[current].style.display = 'none';
                current = i;
                pages[current].style.display = 'block';
                update();
            }

            prevBtn.addEventListener('click', function () { if (current > 0) show(current - 1); });
            nextBtn.addEventListener('click', function () { if (current < pages.length - 1) show(current + 1); });
            update();
        });

        // Booking modal
        document.querySelectorAll('.book-slot-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const slotId = this.dataset.slotId;
                const modal = $('#bookSlotModal');
                modal.find('[name=editor_time_slot_id]').val(slotId);
                modal.find('[name=help_with]').val('');
                $("#bookSlotModalTriggerBtn").trigger('click');
            });
        });
    });

    function disableSubmit(t) {
        let btn = $(t).find('[type=submit]');
        btn.text('');
        btn.append('<i class="fa fa-spinner fa-pulse"></i> ' + translations.pleaseWait);
        btn.attr('disabled', 'disabled');
    }
</script>
@endsection
