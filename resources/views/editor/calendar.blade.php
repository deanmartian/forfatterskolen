@extends('editor.layout')

@section('title')
<title>Calendar &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
	<link rel="stylesheet" href="{{asset('css/editor.css')}}">
	<style>
		.panel {
			overflow-x: auto;
		}

        /* Flex utilities for Bootstrap 3 */

        .d-flex {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
        }

        .flex-wrap {
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
        }

        .flex-column {
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
        }

        /* md breakpoint ≈ 992px in BS3 */
        @media (min-width: 992px) {
            .flex-md-row {
                -webkit-box-orient: horizontal;
                -webkit-box-direction: normal;
                -ms-flex-direction: row;
                flex-direction: row;
            }

            .align-items-md-center {
                -webkit-box-align: center;
                -ms-flex-align: center;
                align-items: center;
            }
        }

        .justify-content-between {
            -webkit-box-pack: justify;
            -ms-flex-pack: justify;
            justify-content: space-between;
        }

        /* small gap + margin helpers to mimic BS4/5 */
        .gap-3 {
            gap: 15px;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        /* custom classes */

        .editor-calendar {
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

        .calendar-guide {
            list-style: none;
            padding: 0;
            /* gap: 10px; */
            margin-top: 0;
        }

        .calendar-guide li {
            color: #ffffff;
            font-weight: 700;
            font-size: 13px;
            padding: 3px 15px;
            border-radius: 3px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .calendar-guide .guide-blue { background: #2496f2; }
        .calendar-guide .guide-green { background: #3bb26f; }
        .calendar-guide .guide-purple { background: #7662d8; }
        .calendar-guide .guide-pink { background: #f269c0; }
        .calendar-guide .guide-orange { background: #ffb322; }
        .calendar-guide .guide-inverse { background: #2d2d2d; }
        .calendar-guide .guide-red { background: #f44c3d; }

        .fc-header-toolbar .fc-button {
            background-color: #f9f9f9;
            background-image: none;
            border: none;
            border-radius: 0;
            box-shadow: none;
            font-family: "Barlow Regular", Arial, sans serif;
            padding: 0 25px;
            text-shadow: none;
        }

        .fc-header-toolbar .fc-next-button:before,
        .fc-header-toolbar .fc-prev-button:before {
            content: "";
            display: block;
            background-image: url(../images-new/left-arrow.png);
            background-repeat: no-repeat;
            background-size: 100% 100%;
            width: 18px;
            height: 18px;
        }

        .fc-header-toolbar .fc-prev-button:before {
            float: left;
            margin: 3px 15px 0 -10px;
        }

        .fc-header-toolbar .fc-next-button:before {
            -webkit-transform: scaleX(-1);
            transform: scaleX(-1);
            float: right;
            margin: 3px -10px 0 15px;
        }

        .fc-header-toolbar .fc-next-button, .fc-header-toolbar .fc-prev-button {
            background-color: #862736;
            color: #fff;
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

        .fc .fc-today-button.fc-button-primary:disabled,
        .fc .fc-today-button.fc-button-primary.fc-button-disabled,
        .learner-calendar .fc-header-toolbar .fc-today-button {
            color: #000000;
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
            background: #fff7d1;
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

        .fc .fc-event.event-warning,
        .fc .fc-event.event-warning:hover,
        .fc .fc-event.event-warning .fc-event-main {
            background-color: #ff9c00 !important;
            color: #fff;
        }

        .fc .fc-event.event-warning .fc-daygrid-event-dot {
            display: none;
        }

        .fc .fc-event.event-coaching,
        .fc .fc-event.event-coaching:hover,
        .fc .fc-event.event-coaching .fc-event-main {
            background-color: #f44c3d !important;
            color: #fff;
        }

        .fc .fc-event.event-coaching .fc-daygrid-event-dot {
            display: none;
        }

        .fc-event-tooltip {
            position: absolute;
            z-index: 9999;
            background: #fff;
            color: #1b1b1b;
            border-radius: 6px;
            padding: 10px 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.18);
            border: 1px solid #e4e8ed;
            pointer-events: none;
            max-width: 320px;
            line-height: 1.4;
        }

        .fc .fc-popover.fc-more-popover .fc-header .fc-title {
            color: #000;
        }

        .fc .fc-popover.fc-more-popover .fc-popover-body {
            max-height: 340px;
            overflow-y: auto;
        }

        .fc .fc-popover.fc-more-popover .fc-body .fc-title {
            color: white;
        }

        .fc-daygrid-dot-event .fc-event-title {
            font-weight: normal;
        }

	</style>
@stop

@section('content')
    <div class="editor-calendar">
        <div class="container-fluid calendar-wrapper" style="padding-top: 15px">
            <div class="row">
                <div class="col-md-12">
                    <div class="calendar-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <ul class="calendar-guide d-flex flex-wrap mb-0">
                            <li class="guide-blue">{{ trans('site.learner.script') }}</li>
                            <li class="guide-green">{{ trans('site.learner.assignment') }}</li>
                            <li class="guide-orange">{{ trans('site.learner.webinars') }}</li> <!-- course-webinars -->
                            <li class="guide-red">{{ trans('site.learner.coaching-time') }}</li>
                        </ul>

                        <div class="d-flex justify-content-md-end w-100 w-md-auto">
                            <a href="{{ route('editor.calendar.export') }}" class="btn btn-primary" target="_blank" rel="noopener">
                                    {{ trans('site.export-calendar') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div id="full-calendar"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

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
        allDay : "{{ trans('site.all-day') }}",
        };

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('full-calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'nb',
            timeZone: 'local',
            initialView: 'dayGridMonth',
            dayMaxEventRows: 4,
            headerToolbar: {
                left: 'title',
                right: 'prev today next dayGridMonth,timeGridWeek,timeGridDay'
            },
            dayHeaderFormat: { weekday: 'long' },
            allDayText: translations.allDay,
            buttonText: {
                today: translations.today,
                month: translations.month,
                week: translations.week,
                day: translations.day,
                prev: translations.prev,
                next: translations.next
            },
            eventTimeFormat: {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            },
            slotLabelFormat: {
                hour: 'numeric',
                minute: '2-digit',
                hour12: false
            },
            displayEventTime: false,
            eventDidMount: function(info) {
                if (info.event && info.event.title) {
                    info.el.removeAttribute('title');
                    info.el.setAttribute('data-event-title', info.event.title);
                }
            },
            eventMouseEnter: function(info) {
                if (!info.event || !info.event.title) {
                    return;
                }

                const tooltip = document.createElement('div');
                tooltip.className = 'fc-event-tooltip';
                tooltip.innerText = info.event.title;

                document.body.appendChild(tooltip);

                const rect = info.el.getBoundingClientRect();
                const scrollTop = window.scrollY || document.documentElement.scrollTop;
                const scrollLeft = window.scrollX || document.documentElement.scrollLeft;

                let top = rect.top + scrollTop - tooltip.offsetHeight - 8;
                const leftBoundary = scrollLeft + 8;
                let left = rect.left + scrollLeft;

                if (top < scrollTop) {
                    top = rect.bottom + scrollTop + 8;
                }

                if (left + tooltip.offsetWidth > scrollLeft + window.innerWidth) {
                    left = scrollLeft + window.innerWidth - tooltip.offsetWidth - 8;
                }

                if (left < leftBoundary) {
                    left = leftBoundary;
                }

                tooltip.style.top = `${top}px`;
                tooltip.style.left = `${left}px`;

                info.el._fcTooltip = tooltip;
            },
            eventMouseLeave: function(info) {
                if (info.el && info.el._fcTooltip) {
                    info.el._fcTooltip.remove();
                    info.el._fcTooltip = null;
                }
            },
            events: @json($events)
        });

        calendar.render();
    });

</script>
@stop