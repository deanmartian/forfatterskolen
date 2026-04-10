@extends('editor.layout')

@section('page_title', 'Coaching timer &rsaquo; Forfatterskolen Redaktørportal')

@section('styles')
    <style>
        .coaching-time-index .stats-card {
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }

        .coaching-time-index .stats-card h2 {
            margin: 0;
            font-size: 36px;
        }

        .coaching-time-index .stats-card p {
            margin: 0;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
        }

        .coaching-time-index .panel-heading h4 {
            margin: 0;
        }

        .coaching-time-index .student-list li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .coaching-time-index .student-list li:last-child {
            border-bottom: none;
        }

        .coaching-time-index .schedule-table th,
        .coaching-time-index .schedule-table td {
            vertical-align: middle !important;
        }
    </style>
@stop

@section('content')
    <div class="container-fluid coaching-time-index dashboard-left">
        <div class="row" style="margin-bottom:20px;">
            <div class="col-sm-4">
                <div class="stats-card">
                    <p>{{ trans('site.coaching-time- my-author-students') }}</p>
                    <h2>{{ $bookings->count() }}</h2>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="stats-card">
                    <p>{{ trans('site.coaching-time-this-week') }}</p>
                    <h2>{{ $bookingsThisWeek }}</h2>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="stats-card">
                    <p>{{ trans('site.coaching-time-available-slots') }}</p>
                    <h2>{{ $availableSlots }}</h2>
                </div>
            </div>
        </div>

        <div class="row" style="margin-bottom:20px;">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>{{ trans('site.coaching-time-editor-calendar') }}</h4>
                    </div>
                    <div class="panel-body">
                        <p>{{ trans('site.coaching-time-editor-calendar-description') }}</p>
                        <a href="{{ route('editor.coaching-time.calendar') }}" class="btn btn-default btn-block" style="margin-bottom:15px;">
                            {{ trans('site.coaching-time-open-editor-calendar') }}
                        </a>
                    </div>
                </div>
            </div>
            {{-- <div class="col-sm-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Mine Forfatter-studenter</h4>
                    </div>
                    <div class="panel-body">
                        <ul class="list-unstyled student-list">
                            <li>Kristine S. Heningsen <span class="text-muted pull-right">I dag 10:43</span></li>
                            <li>Armina Forsland <span class="text-muted pull-right">I dag 10:43</span></li>
                            <li>Søvn Inge Heningsen <span class="text-muted pull-right">I dag 10:43</span></li>
                        </ul>
                        <a href="#" class="btn btn-default btn-block" style="margin-top:15px;">Se Alle Forfatter-studenter</a>
                    </div>
                </div>
            </div> --}}
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>{{ trans('site.coaching-time-timeplan') }}</h4>
                    </div>
                    <div class="panel-body">
                        <table class="table schedule-table">
                            <thead>
                                <tr>
                                    <th>{{ trans('site.learner.date-time') }}</th>
                                    <th>{{ trans('site.learner-id') }}</th>
                                    <th>{{ trans('site.front.form.email') }}</th>
                                    <th>{{ trans('site.front.form.phone-number') }}</th>
                                    <th>{{ trans_choice('site.learners', 1) }}</th>
                                    <th>{{ trans('site.call-type') }}</th>
                                    <th>{{ trans('site.learner.duration-text') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $booking)
                                    @php
                                        $dt = \Carbon\Carbon::parse(
                                            $booking->slot->date.' '.$booking->slot->start_time,
                                            'UTC'
                                        )->setTimezone(config('app.timezone'));
                                        if ($dt->isToday()) {
                                            $dateLabel = 'I dag';
                                        } elseif ($dt->isTomorrow()) {
                                            $dateLabel = 'I morgen';
                                        } elseif ($dt->isSameWeek(\Carbon\Carbon::now(config('app.timezone')))) {
                                            $dateLabel = ucfirst($dt->locale(app()->getLocale())->dayName);
                                        } else {
                                            $dateLabel = $dt->format('d.m.Y');
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $dateLabel }} {{ $dt->format('H:i') }}</td>
                                        <td>
                                            {{ $booking->manuscript->user->id }}
                                        </td>
                                        <td>
                                            {{ $booking->manuscript->user->email }}
                                        </td>
                                        <td>
                                            {{ $booking->manuscript->user->address->phone ?? null }}
                                        </td>
                                        <td>
                                            {{ $booking->manuscript->user->full_name }}
                                            @if ($booking->manuscript->help_with)
                                                <br>
                                                <a href="#viewHelpWithModal" style="color:#eea236" class="viewHelpWithBtn"
                                                data-toggle="modal" data-details="{{ $booking->manuscript->help_with }}">
                                                    {{ trans('site.view-help-with') }}
                                                </a>
                                            @endif
                                        </td>
                                        <td>{{ $booking->manuscript->call_type_label }}</td>
                                        <td>{{ $booking->slot->duration }} min</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5">Ingen bookinger.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @if($suggestions->count())
        <div class="row" style="margin-top: 20px;">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading" style="background: #fff3e0; border-color: #ffe0b2;">
                        <h4 style="color: #e65100; margin: 0;">
                            <i class="fa fa-clock-o"></i>
                            Tidsforslag fra elever ({{ $suggestions->count() }})
                        </h4>
                    </div>
                    <div class="panel-body">
                        <p style="color: #666; margin-bottom: 15px;">Disse elevene fant ingen ledige tider som passet og har foreslått egne tidspunkter.</p>
                        <table class="table schedule-table">
                            <thead>
                                <tr>
                                    <th>Elev</th>
                                    <th>E-post</th>
                                    <th>Telefon</th>
                                    <th>Foreslått tid</th>
                                    <th>Type</th>
                                    <th>Varighet</th>
                                    <th>Melding</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($suggestions as $suggestion)
                                    @php
                                        $suggestedDates = json_decode($suggestion->suggested_date, true) ?: [];
                                    @endphp
                                    <tr>
                                        <td>{{ $suggestion->user->full_name }}</td>
                                        <td>{{ $suggestion->user->email }}</td>
                                        <td>{{ optional($suggestion->user->address)->phone }}</td>
                                        <td><strong>{{ implode(', ', $suggestedDates) }}</strong></td>
                                        <td>{{ $suggestion->call_type_label }}</td>
                                        <td>{{ $suggestion->plan_type == 1 ? '60 min' : '30 min' }}</td>
                                        <td>
                                            @if($suggestion->help_with)
                                                <a href="#viewHelpWithModal" style="color:#eea236" class="viewHelpWithBtn"
                                                   data-toggle="modal" data-details="{{ $suggestion->help_with }}">
                                                    Se melding
                                                </a>
                                            @else
                                                <span class="text-muted">–</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Forespørsler fra studenter</h4>
                    </div>
                    <div class="panel-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Tid</th>
                                    <th>Duration</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requests as $req)
                                    <tr>
                                        <td>{{ $req->manuscript->user->full_name }}</td>
                                        <td class="slot-time" data-time="{{ \Carbon\Carbon::parse($req->slot->date.' '.$req->slot->start_time, 'UTC')->toIso8601String() }}"></td>
                                        <td>{{ $req->slot->duration }} min</td>
                                        <td>
                                            <form method="POST" action="{{ route('editor.coaching-time.request.accept', $req->id) }}" style="display: inline">
                                                @csrf
                                                <button type="button" class="btn btn-primary btn-xs confirm-action" data-message="Er du sikker på at du vil godta denne forespørselen?">Godta</button>
                                            </form>
                                            <form method="POST" action="{{ route('editor.coaching-time.request.decline', $req->id) }}" style="display: inline">
                                                @csrf
                                                <button type="button" class="btn btn-danger btn-xs confirm-action" data-message="Er du sikker på at du vil avslå denne forespørselen?">Avslå</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3">Ingen forespørsler.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="modal fade" id="actionConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Bekreft handling</h4>
                    </div>
                    <div class="modal-body">
                        <p id="actionConfirmMessage">Er du sikker?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="actionConfirmBtn">Ja</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Nei</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="viewHelpWithModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.learner.help-with-text') }}</h4>
                </div>
                <div class="modal-body">
                    <pre></pre>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let formToSubmit;
        document.querySelectorAll('.confirm-action').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                formToSubmit = this.closest('form');
                document.getElementById('actionConfirmMessage').textContent = this.dataset.message;
                $('#actionConfirmModal').modal('show');
            });
        });

        document.getElementById('actionConfirmBtn').addEventListener('click', function () {
            if (formToSubmit) {
                formToSubmit.submit();
            }
        });
    });

    $(".viewHelpWithBtn").click(function(){
       let details = $(this).data('details');
       let modal = $("#viewHelpWithModal");

       modal.find('.modal-body').find('pre').text(details);
	});
</script>
@endsection
