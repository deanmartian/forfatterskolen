{{-- @extends('frontend.layout') --}}
@extends('frontend.layouts.course-portal')

@section('title')
<title>Calendar &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
<style>
    .learner-calendar {
        padding: 0;
    }

    .calendar-wrapper {
        background: #f3f5f7;
        padding: 0 20px 32px;
    }

    @media (min-width: 992px) {
        .calendar-wrapper {
            padding-left: 36px;
            padding-right: 36px;
        }
    }

    .calendar-header {
        background: #ffffff;
        border-radius: 6px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        padding: 18px 22px;
        margin-bottom: 14px;
    }

    .calendar-header .calendar-guide {
        margin-bottom: 0;
    }

    #full-calendar {
        background: #ffffff;
        border-radius: 6px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        padding: 16px;
    }

    .fc-theme-standard .fc-scrollgrid,
    .fc-theme-standard th,
    .fc-theme-standard td {
        border-color: #e4e8ed;
    }

    .fc .fc-toolbar-title {
        font-size: 22px;
        font-weight: 600;
        color: #2e3a59;
    }

    .fc .fc-toolbar-chunk:last-child {
        display: grid;
        gap: 8px;
        grid-auto-flow: column;
        align-items: center;
    }

    .fc .fc-button-primary {
        background-color: #7d1a29;
        border-color: #7d1a29;
        border-radius: 4px;
        box-shadow: none;
        text-transform: none;
    }

    .fc .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-primary:not(:disabled):active,
    .fc .fc-button-primary:not(:disabled):focus,
    .fc .fc-button-primary:hover {
        background-color: #9a3141;
        border-color: #9a3141;
    }

    .fc .fc-button-group .fc-button-primary {
        background: #fff;
        color: #2e3a59;
        border: 1px solid #d6dbe1;
    }

    .fc .fc-button-primary:disabled,
    .fc .fc-button-primary.fc-button-disabled {
        color: #2e3a59;
        background: #fff;
        border-color: #d6dbe1;
        opacity: 1;
    }

    .fc .fc-button-group .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-group .fc-button-primary:not(:disabled):active,
    .fc .fc-button-group .fc-button-primary:hover {
        background: #eef2f6;
        color: #7d1a29;
        border-color: #d6dbe1;
    }

    .fc .fc-day-today {
        background: #f9f1f1;
    }

    .fc .fc-daygrid-day-number {
        color: #2e3a59;
        font-weight: 600;
    }

    .fc .fc-event {
        border-radius: 4px;
        border: none;
        padding: 4px 8px;
    }

    .fc .fc-popover.fc-more-popover .fc-header .fc-title {
        color: #000;
    }

    .fc .fc-popover.fc-more-popover .fc-body {
        max-height: 340px;
        overflow-y: auto;
    }

    .fc .fc-popover.fc-more-popover .fc-body .fc-title {
        color: white;
    }
</style>
@stop

@section('content')
        <div class="learner-container learner-calendar">
                <div class="container-fluid calendar-wrapper">
                        <div class="row">
                                <div class="col-12">
                                        <div class="calendar-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                                <ul class="calendar-guide d-flex flex-wrap mb-0">
                                                        <li class="guide-blue">{{ trans('site.learner.script') }}</li>
                                                        <li class="guide-green">{{ trans('site.learner.assignment') }}</li>
                                                        <li class="guide-purple">{{ trans('site.learner.webinars') }}</li>
                                                        <li class="guide-pink">{{ trans('site.learner.modules') }}</li>
                                                        <li class="guide-orange">{{ trans('site.learner.webinars') }}</li> <!-- course-webinars -->
                                                        <li class="guide-inverse">{{ trans('site.learner.notes-text') }}</li>
                                                        <li class="guide-red">{{ trans('site.learner.coaching-time') }}</li>
                                                </ul>
                                                <div class="d-flex justify-content-md-end w-100 w-md-auto">
                                                        <a href="{{ route('learner.calendar.export') }}" class="btn btn-primary" target="_blank" rel="noopener">
                                                                {{ __('Export Calendar') }}
                                                        </a>
                                                </div>
                                        </div>
                                </div>
                        </div>

                        <div class="row">
                                <div class="col-12">
                                        <div id="full-calendar"></div>
                                </div>
                        </div>
                </div>
        </div>
@stop


@section('scripts')
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
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'title',
            right: 'prev today next dayGridMonth,timeGridWeek,timeGridDay'
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

</script>
@stop
