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
            <i class="fa fa-arrow-left"></i> Back
        </a>

        <h2>Manage Time Slots</h2>

        <div id="calendar"></div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="{{ asset('js/toastr/toastr.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let calendarEl = document.getElementById('calendar');

        let calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            timeZone: 'local', // 👈 force local time
            selectable: true,
            selectMirror: true,
            allDaySlot: false,
            slotMinTime: "08:00:00",
            slotMaxTime: "20:00:00",
            slotDuration: "00:30:00", // grid step is 30 minutes

            events: "{{ route('editor.coaching-time.time-slots.fetch') }}", //fetch saved data

            select: function(info) {
                const start = new Date(info.start);
                const end   = new Date(info.end);

                const fmt = { weekday:'short', month:'short', day:'numeric', hour:'2-digit', minute:'2-digit' };
                const startTxt = start.toLocaleString('en-US', fmt);
                const endTxt   = end.toLocaleString('en-US', fmt);

                const diffMinutes = (end - start) / 60000;
                if (![30, 60].includes(diffMinutes)) {
                    alert("Please select exactly 30 minutes or 1 hour.");
                    calendar.unselect();       // <- clear selection on invalid length
                    return;
                }

                if (!confirm(`Create a ${diffMinutes} min slot:\n\n${startTxt} → ${endTxt}`)) {
                    calendar.unselect();       // <- user clicked Cancel: remove highlighted selection
                    return;
                }

                fetch("{{ route('editor.coaching-time.time-slots.store') }}", {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                    body: JSON.stringify({ start: info.startStr, end: info.endStr })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        calendar.refetchEvents();
                        toastr.success('Your time slot was successfully stored.', "Success");
                    }
                });
            },

            eventContent: function(arg) {
                let start = new Date(arg.event.start);
                let end   = new Date(arg.event.end);

                // Format times like "08:00 - 09:00"
                let fmt = { hour: '2-digit', minute: '2-digit' };
                let startTxt = start.toLocaleTimeString([], fmt);
                let endTxt   = end.toLocaleTimeString([], fmt);

                let duration = (end - start) / 60000; // minutes

                // container
                let wrapper = document.createElement('div');
                wrapper.style.display = "flex";
                wrapper.style.justifyContent = "space-between";

                // left side (time + duration stacked)
                let left = document.createElement('div');
                left.innerHTML = `
                    <div>${startTxt} – ${endTxt}</div>
                    <div style="font-size: 12px;">${duration}min</div>
                `;

                // right side (delete ×)
                let closeBtn = document.createElement('span');
                closeBtn.innerHTML = '&times;';
                closeBtn.style.cursor = 'pointer';
                closeBtn.style.color = 'white';
                closeBtn.style.fontWeight = 'bold';

                // 👇 increase size of the ×
                closeBtn.style.fontSize = '20px';      // bigger font
                closeBtn.style.lineHeight = '1';       // keeps it compact
                closeBtn.style.marginLeft = '10px';    // spacing from text
                closeBtn.style.userSelect = 'none';    // prevent accidental text selection

                closeBtn.title = 'Delete slot';

                closeBtn.onclick = function(e) {
                    e.stopPropagation(); // don’t trigger eventClick

                    if (confirm(`Delete this slot?\n${startTxt} – ${endTxt}`)) {
                    fetch("{{ url('/coaching-time/time-slots') }}/" + arg.event.id, {
                        method: "DELETE",
                        headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            arg.event.remove();
                            toastr.success('Your time slot was successfully deleted.', "Success");
                        }
                    });
                    }
                };

                wrapper.appendChild(left);
                wrapper.appendChild(closeBtn);

                return { domNodes: [wrapper] };
            }

        });

        calendar.render();
    });
</script>
@stop