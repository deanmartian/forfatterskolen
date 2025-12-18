@extends('editor.layout')

@section('title')
    <title>Coaching Time &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
	<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('js/toastr/toastr.min.css') }}">
    @stop

@section('content')
<div class="container-fluid dashboard-left">
    <div class="panel panel-default" style="padding: 10px">
        <a href="{{ route('editor.coaching-time.index') }}" class="btn btn-default margin-bottom">
            <i class="fa fa-arrow-left"></i> {{ trans('site.back') }}
        </a>

        <h2>{{ trans('site.coaching-time-manage-time-slots') }}</h2>

        <div id="calendar"></div>
    </div>
</div>

<div class="modal fade" id="slotDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    {{ trans('site.coaching-time-details') }}
                </h4>
            </div>
            <div class="modal-body">
                <p><strong>{{ trans('site.learner-id') }}:</strong> <span id="slotStudentNr"></span></p>
                <p><strong>{{ trans('site.front.home.students') }}:</strong> <span id="slotStudent"></span></p>
                <p><strong>{{ trans('site.front.form.email') }}:</strong> <span id="slotEmail"></span></p>
                <p><strong>{{ trans('site.front.form.phone-number') }}:</strong> <span id="slotPhone"></span></p>
                <p><strong>{{ trans('site.learner.date-start') }}:</strong> <span id="slotStart"></span></p>
                <p><strong>{{ trans('site.end-date') }}:</strong> <span id="slotEnd"></span></p>
                <p><strong>{{ trans('site.call-type') }}:</strong> <span id="slotCallType"></span></p>
                <p><strong>{{ trans('site.learner.duration-text') }}:</strong> <span id="slotDuration"></span></p>
                <p><strong>{{ trans('site.front.coaching-timer.help-with-text') }}:</strong></p>
                <pre id="slotHelpsWith"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    {{ trans('site.learner.close') }}
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="slotConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{ trans('site.create-time-slot') }}</h4>
            </div>
            <div class="modal-body">
                <p><strong>{{ trans('site.time') }}:</strong> <span id="slotConfirmRange"></span></p>
                <div class="checkbox" id="slotConfirmExtendWrap">
                    <label>
                        <input type="checkbox" id="slotConfirmExtend">
                        {{ trans('site.make-1hr-slot') }}
                    </label>
                    <div id="slotConfirmExtendNote" style="font-size: 12px; color: #777;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="slotConfirmCancel" data-dismiss="modal">
                    {{ trans('site.cancel') }}
                </button>
                <button type="button" class="btn btn-primary" id="slotConfirmSubmit">
                    {{ trans('site.create-slot') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="{{ asset('js/toastr/toastr.min.js') }}"></script>
<script>
    let translations = {
        pleaseSelect30min1hr : "{{ trans('site.please-select-30min-1hr') }}",
        timeSlotStoredSuccess : "{{ trans('site.time-slot-stored-success') }}",
        view : "{{ trans('site.view') }}",
        deleteSlot : "{{ trans('site.delete-slot') }}",
        deleteThisSlot : "{{ trans('site.delete-this-slot') }}",
        deleteSlotSuccess : "{{ trans('site.delete-slot-success') }}",
        checkToExtendTime : "{{ trans('site.check-to-extend-time') }}",
        cannotExtendTo1hr : "{{ trans('site.cannot-extend-to-1hr') }}",
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        let calendarEl = document.getElementById('calendar');

        let calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            timeZone: 'local', // 👈 force local time
            selectable: true,
            // Use the default highlight (no mirror event) to avoid overlapping text when selecting near existing events
            selectMirror: false,
            allDaySlot: false,
            slotMinTime: "08:00:00",
            slotMaxTime: "23:00:00",
            slotDuration: "00:30:00", // grid step is 30 minutes

            events: "{{ route('editor.coaching-time.time-slots.fetch') }}", //fetch saved data

            select: function(info) {
                const start = new Date(info.start);
                const end   = new Date(info.end);

                const fmt = {
                    weekday:'short',
                    month:'short',
                    day:'numeric',
                    hour:'2-digit',
                    minute:'2-digit',
                    hour12: false
                };

                let adjustedEnd = new Date(end);
                // Guard against floating-point precision issues by rounding to the nearest minute
                let diffMinutes = Math.round((adjustedEnd - start) / 60000);
                const hourEnd = new Date(start.getTime() + 60 * 60000);

                // Offer a single confirmation with a built-in toggle to extend to 60 minutes
                const canExtend = diffMinutes === 30 && !calendar.getEvents().some(ev => ev.start < hourEnd && ev.end > end);

                if (![30, 60].includes(diffMinutes)) {
                    alert(translations.pleaseSelect30min1hr);
                    calendar.unselect();       // <- clear selection on invalid length
                    return;
                }

                const startTxt = start.toLocaleString('no-NO', fmt);
                const endTxt   = adjustedEnd.toLocaleString('no-NO', fmt);
                const hourEndTxt = hourEnd.toLocaleString('no-NO', fmt);

                showSlotConfirmModal({ startTxt, endTxt, hourEndTxt, canExtend })
                    .then(({ confirmed, extend }) => {
                        if (!confirmed) {
                            calendar.unselect();       // <- user canceled in the modal
                            return;
                        }

                        if (extend) {
                            adjustedEnd = hourEnd;
                            diffMinutes = 60;
                        }

                        fetch("{{ route('editor.coaching-time.time-slots.store') }}", {
                            method: "POST",
                            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                            body: JSON.stringify({ start: start.toISOString(), end: adjustedEnd.toISOString() })
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                calendar.refetchEvents();
                                toastr.success(translations.timeSlotStoredSuccess, "Success");
                            }
                        });
                    });
            },

            eventContent: function(arg) {
                let start = new Date(arg.event.start);
                let end   = new Date(arg.event.end);

                // Format times like "08:00 - 09:00"
                let fmt = { hour: '2-digit', minute: '2-digit', hour12: false };
                let startTxt = start.toLocaleTimeString([], fmt);
                let endTxt   = end.toLocaleTimeString([], fmt);

                let duration = (end - start) / 60000; // minutes

                // container
                let wrapper = document.createElement('div');
                wrapper.style.display = "flex";
                wrapper.style.alignItems = "center";
                wrapper.style.justifyContent = "space-between";
                wrapper.style.gap = "8px";
                wrapper.style.fontSize = "12px";
                wrapper.style.lineHeight = "1.2";

                // single-line label to avoid overflowing small (30min) slots
                let left = document.createElement('span');
                left.textContent = `${startTxt} – ${endTxt} (${duration}min)`;

                wrapper.appendChild(left);

                if (arg.event.extendedProps.booked) {
                    let viewBtn = document.createElement('span');
                    viewBtn.innerHTML = translations.view;
                    viewBtn.style.cursor = 'pointer';
                    viewBtn.style.color = 'white';
                    viewBtn.style.fontSize = '12px';

                    viewBtn.onclick = function(e) {
                        e.stopPropagation();
                        showSlotDetails(arg.event);
                    };

                    wrapper.appendChild(viewBtn);
                } else {
                    let closeBtn = document.createElement('span');
                    closeBtn.innerHTML = '&times;';
                    closeBtn.style.cursor = 'pointer';
                    closeBtn.style.color = 'white';
                    closeBtn.style.fontWeight = 'bold';

                    closeBtn.style.fontSize = '16px';
                    closeBtn.style.lineHeight = '1';
                    closeBtn.style.userSelect = 'none';

                    closeBtn.title = translations.deleteSlot;

                    closeBtn.onclick = function(e) {
                        e.stopPropagation();

                        if (confirm(translations.deleteThisSlot+`\n${startTxt} – ${endTxt}`)) {
                        fetch("{{ url('/coaching-time/time-slots') }}/" + arg.event.id, {
                            method: "DELETE",
                            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                arg.event.remove();
                                toastr.success(translations.deleteSlotSuccess, "Success");
                            }
                        });
                        }
                    };

                    wrapper.appendChild(closeBtn);
                }

                return { domNodes: [wrapper] };
            },

            eventClick: function(info) {
                if (info.event.extendedProps.booked) {
                    showSlotDetails(info.event);
                }
            }

        });

        function showSlotDetails(event) {
            let start = new Date(event.start);
            let end   = new Date(event.end);
            let fmt = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', hour12: false };

            document.getElementById('slotStudentNr').textContent = event.extendedProps.studentnr || '';
            document.getElementById('slotStudent').textContent = event.extendedProps.student || '';
            document.getElementById('slotEmail').textContent = event.extendedProps.email || '';
            document.getElementById('slotPhone').textContent = event.extendedProps.phone || '';
            document.getElementById('slotStart').textContent = start.toLocaleString('no-NO', fmt);
            document.getElementById('slotEnd').textContent = end.toLocaleString('no-NO', fmt);
            document.getElementById('slotCallType').textContent = event.extendedProps.call_type;
            document.getElementById('slotDuration').textContent = (event.extendedProps.duration || ((end - start)/60000)) + ' min';
            document.getElementById('slotHelpsWith').textContent = event.extendedProps.helps_with || '';

            $('#slotDetailsModal').modal('show');
        }

        function showSlotConfirmModal({ startTxt, endTxt, hourEndTxt, canExtend }) {
            return new Promise(resolve => {
                let resolved = false;
                let modal = $('#slotConfirmModal');
                let extendCheckbox = $('#slotConfirmExtend');
                let note = $('#slotConfirmExtendNote');

                $('#slotConfirmRange').text(`${startTxt} → ${endTxt}`);

                extendCheckbox.prop('checked', false);
                extendCheckbox.prop('disabled', !canExtend);
                $('#slotConfirmExtendWrap').toggleClass('disabled', !canExtend);
                note.text(canExtend ? translations.checkToExtendTime.replace(':time', hourEndTxt) 
                    : translations.cannotExtendTo1hr);

                function finish(result) {
                    if (resolved) return;
                    resolved = true;
                    cleanup();
                    modal.modal('hide');
                    resolve(result);
                }

                function cleanup() {
                    modal.off('hidden.bs.modal', onCancel);
                    $('#slotConfirmSubmit').off('click', onSubmit);
                }

                function onCancel() {
                    finish({ confirmed: false, extend: false });
                }

                function onSubmit() {
                    finish({ confirmed: true, extend: canExtend && extendCheckbox.is(':checked') });
                }

                $('#slotConfirmSubmit').one('click', onSubmit);
                modal.one('hidden.bs.modal', onCancel);
                modal.modal('show');
            });
        }

        calendar.render();
    });
</script>
@stop