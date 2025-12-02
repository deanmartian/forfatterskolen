{{-- @extends('frontend.layout') --}}
@extends('frontend.layouts.course-portal')

@section('title')
<title>Calendar &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
<link rel="stylesheet" href="{{asset('bootstrap-calendar/css/calendar.min.css')}}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
<style>
    .fc-popover.fc-more-popover .fc-header .fc-title {
        color: #000;
    }

    .fc-popover.fc-more-popover .fc-body {
        max-height: 340px;
        overflow-y: auto;
    }

    .fc-popover.fc-more-popover .fc-body .fc-title {
        color: white;
    }
</style>
@stop

@section('content')
        <div class="learner-container learner-calendar">
                <div class="container">
                        <div class="row">
                                <div class="card w-100 rounded-0 py-4">
                                        <ul class="calendar-guide">
                                                <li class="guide-blue">{{ trans('site.learner.script') }}</li>
                                                <li class="guide-green">{{ trans('site.learner.assignment') }}</li>
                                                <li class="guide-purple">{{ trans('site.learner.webinars') }}</li>
                                                <li class="guide-pink">{{ trans('site.learner.modules') }}</li>
                                                <li class="guide-orange">{{ trans('site.learner.webinars') }}</li> <!-- course-webinars -->
                                                <li class="guide-inverse">{{ trans('site.learner.notes-text') }}</li>
                                                <li class="guide-red">{{ trans('site.learner.coaching-time') }}</li>
                                        </ul>
                                        <div class="px-3" style="text-align: right;">
                                                <a href="{{ route('learner.calendar.export') }}" class="btn btn-primary" target="_blank" rel="noopener">
                                                        {{ __('Export Calendar') }}
                                                </a>
                                        </div>
                                </div>
                        </div>

                        <div class="row">
                                <div id="full-calendar"></div>
                        </div>
                </div>
        </div>
@stop


@section('scripts')
<script type="text/javascript" src="{{asset('js/underscore-min.js')}}"></script>
<script type="text/javascript" src="{{asset('bootstrap-calendar/js/language/no-NO.js')}}"></script>
<script type="text/javascript" src="{{asset('bootstrap-calendar/js/calendar.js')}}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script type="text/javascript">

    let translations = {
        today : "{{ trans('site.learner.today-text') }}",
                month : "{{ trans('site.learner.month-text') }}",
                week : "{{ trans('site.learner.week-text') }}",
        day : "{{ trans('site.learner.day-text') }}",
        prev : "{{ trans('site.learner.prev-text') }}",
        next : "{{ trans('site.learner.next-text') }}",
        };

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('full-calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'nb',
        timeZone: 'local',
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: translations.today,
            month: translations.month,
            week: translations.week,
            day: translations.day,
            prev: translations.prev,
            next: translations.next
        },
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },
        events: @json($events)
    });

    calendar.render();
});

        let calendar = $("#calendar").calendar(
                {
                        language: 'no-NO',
                        tmpl_path: "{{asset('bootstrap-calendar/tmpls')}}/",
                        events_source: [
                                @foreach($events as $event)
                                {!! json_encode($event) !!},
                                @endforeach
                        ],
                }
        );

        $('.btn-group button[data-calendar-nav]').each(function() {
        let $this = $(this);
                $this.click(function() {
                        calendar.navigate($this.data('calendar-nav'));
                });
        });

        $('.btn-group button[data-calendar-view]').each(function() {
        let $this = $(this);
                $this.click(function() {
                        calendar.view($this.data('calendar-view'));
                });
        });

</script>
@stop
