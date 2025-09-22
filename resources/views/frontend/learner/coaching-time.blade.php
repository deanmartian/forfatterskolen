@extends('frontend.layouts.course-portal')

@section('title')
    <title>Coaching Time &rsaquo; Forfatterskolen</title>
@endsection

@section('styles')
<style>
    .avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #f5f5f5;
        line-height: 50px;
        margin: 0 auto 10px;
        font-size: 24px;
    }

    .stats-card {
        background: #fff;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 20px;
        text-align: center;
    }

    .stats-card h2 {
        margin: 0;
        font-size: 36px;
    }

    .stats-card p {
        margin: 0;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 12px;
    }

    .black-btn {
        width: 100%;
        border: 1px solid #e4e4e7;
        background: #ffffff;
        border-radius: 5px;
        color: #000000;
    }

    .black-btn:hover {
        background: #000000;
        color: #ffffff;
    }
</style>
@stop

@section('content')
<div class="learner-container coaching-time-wrapper">
    <div class="container">
        <p style="color: red; font-weight:bold">Vennligst ikke bruk denne coaching funksjonen ennå.</p>
        <h1 class="page-title">
            {{ trans('site.coaching-timer-text') }}
        </h1>

        <?php
            $packages = \App\Package::where('has_coaching', '>', 0)->pluck('id');
            $coachingTimerTaken = Auth::user()->coachingTimersTaken()->pluck('course_taken_id');
            $checkCourseTakenWithCoaching = Auth::user()->coursesTaken()->whereIn('package_id', $packages)
                ->whereNotIn('id', $coachingTimerTaken)->get();
        ?>

        @if($checkCourseTakenWithCoaching->count())
            <div class="text-right mb-3">
                <button class="btn blue-outline-btn"
                        data-toggle="modal"
                        data-target="#addCoachingSessionModal"
                        data-action="{{ route('learner.course-taken.coaching-timer.add') }}"
                        id="addCoachingSessionBtn">
                    {{ trans('site.learner.add-coaching-lesson') }}
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                {{ session('success') }}
            </div>
        @endif

        @php
            $availableSlots = $editors->reduce(function ($carry, $group) {
                return $carry + $group->count();
            }, 0);
            $nextSession = $bookedSessions->first();
        @endphp

        <div class="row mb-5">
            <div class="col-sm-3">
                <div class="stats-card text-center">
                    <p>{{ trans('site.coaching-time-my-editors') }}</p>
                    <h2>{{ $bookedEditorsCount }}</h2>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="stats-card">
                    <p>{{ trans('site.coaching-time-next-editorial') }}</p>
                    @if($nextSession)
                        @php
                            $date = \Carbon\Carbon::parse(
                                $nextSession->timeSlot->date.' '.$nextSession->timeSlot->start_time,
                                'UTC'
                            )->setTimezone(config('app.timezone'));
                            if ($date->isToday()) {
                                $dateLabel = 'I dag';
                            } elseif ($date->isTomorrow()) {
                                $dateLabel = 'I morgen';
                            } elseif ($date->isSameWeek(\Carbon\Carbon::now(config('app.timezone')))) {
                                $dateLabel = ucfirst($date->locale(app()->getLocale())->dayName);
                            } else {
                                $dateLabel = $date->format('d.m.Y');
                            }
                        @endphp
                        <h2 style="font-size: 24px">
                            {{ $dateLabel }} 
                        </h2>
                        <p class="text-secondary">
                            {{ $date->format('H:i') }} - {{ optional($nextSession->editor)->full_name }}
                        </p>
                    @else
                        <h2>-</h2>
                    @endif
                </div>
            </div>
            <div class="col-sm-3">
                <div class="stats-card">
                    <p>{{ trans('site.coaching-time-this-month') }}</p>
                    <h2>{{ $bookedSessionsThisMonth }}</h2>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="stats-card">
                    <p>{{ trans('site.coaching-time-available-slots') }}</p>
                    <h2>{{ $availableSlots }}</h2>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-6">
                <div class="stats-card text-left">
                    <h3>{{ trans('site.coaching-time-book-editorial-class') }}</h3>
                    <span>{{ trans('site.coaching-time-book-editorial-class-description') }}</span>
                    
                    @if($coachingTimers->count() >= 1)
                        <form method="GET" action="{{ route('learner.coaching-time.available') }}">
                            @if($coachingTimers->count() > 1)
                                <div class="form-group mt-3">
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
                            <button type="submit" class="btn black-btn mt-4">
                                {{ trans('site.coaching-time-see-available-slots') }}
                            </button>
                        </form>
                    @else
                        <p class="mt-4">{{ trans('site.coaching-time-no-record') }}</p>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="stats-card text-left">
                    <h3>{{ trans('site.coaching-time-my-sessions') }}</h3>
                    @if($bookedSessions->isEmpty())
                        <span>{{ trans('site.coaching-time-no-upcoming-sessions') }}</span>
                    @else
                        <ul id="sessions-list" class="list-unstyled mb-0">
                            @foreach($bookedSessions as $session)
                                @php
                                    $date = \Carbon\Carbon::parse(
                                        $session->timeSlot->date.' '.$session->timeSlot->start_time,
                                        'UTC'
                                    )->setTimezone(config('app.timezone'));
                                    if ($date->isToday()) {
                                        $dateLabel = 'I dag';
                                    } elseif ($date->isTomorrow()) {
                                        $dateLabel = 'I morgen';
                                    } elseif ($date->isSameWeek(\Carbon\Carbon::now(config('app.timezone')))) {
                                        $dateLabel = ucfirst($date->locale(app()->getLocale())->dayName);
                                    } else {
                                        $dateLabel = $date->format('d.m.Y');
                                    }
                                    $duration = $session->plan_type == 1 ? '60 min' : '30 min';
                                @endphp
                                <li class="mb-3 {{ $loop->iteration > 2 ? 'd-none extra-session' : '' }}">
                                    <div>{{ $dateLabel }} {{ $date->format('H:i') }}</div>
                                    <div>{{ $duration }} med {{ optional($session->editor)->full_name }}</div>
                                </li>
                            @endforeach
                        </ul>
                        @if($bookedSessions->count() > 2)
                            <button id="toggle-sessions" class="btn black-btn mt-3" data-showing="false">
                                {{ trans('site.coaching-time-see-all-sessions') }}
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <h3>{{ trans('site.coaching-time-available-book-editors') }}</h3>
        <div class="row">
            @foreach($editors as $editorSlots)
                <div class="col-sm-3">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="media align-items-center">
                                <div class="media-left">
                                    <div class="avatar text-center">
                                        {{ substr($editorSlots->first()->editor->first_name, 0, 1) .''. 
                                            substr($editorSlots->first()->editor->last_name, 0, 1) }}
                                    </div>
                                </div>
                                <div class="media-body">
                                    <p class="media-heading" style="margin:0;">
                                        {{ $editorSlots->first()->editor->full_name }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>


        {{-- <h3>Hurtighandlinger</h3>
        <div class="row">
            <div class="col-sm-3"><button class="btn btn-default btn-block">Endre Tidspunkt</button></div>
            <div class="col-sm-3"><button class="btn btn-default btn-block">Avbryt Booking</button></div>
            <div class="col-sm-3"><button class="btn btn-default btn-block">Kontakt Redaktør</button></div>
            <div class="col-sm-3"><button class="btn btn-default btn-block">&nbsp;</button></div>
        </div> --}}

    </div>
</div>

<div id="addCoachingSessionModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{ trans('site.learner.add-coaching-session') }}</h3>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
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

                    <div class="text-right mt-4">
                        <button type="submit" class="btn btn-success">{{ trans('site.front.submit') }}</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('site.front.cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
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
            toggle.textContent = showing ? 'Se Alle Sesjoner' : 'Skjul Sesjoner';
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
        submit_btn.append('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
        submit_btn.attr('disabled', 'disabled');
    }
</script>
@endsection
