@extends('editor.layout')

@section('title')
    <title>Coaching Time &rsaquo; Forfatterskolen Admin</title>
@stop

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
            <div class="col-sm-3">
                <div class="stats-card">
                    <p>Mine Forfatter-studenter</p>
                    <h2>3</h2>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="stats-card">
                    <p>Denne Uken</p>
                    <h2>8</h2>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="stats-card">
                    <p>Ledige Slots</p>
                    <h2>15</h2>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="stats-card">
                    <p>Fullt</p>
                    <h2>24</h2>
                </div>
            </div>
        </div>

        <div class="row" style="margin-bottom:20px;">
            <div class="col-sm-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Redaktør Kalender</h4>
                    </div>
                    <div class="panel-body">
                        <p>Klikk på kalenderen for manuelt gjennomgang av redaksjonstimer</p>
                        <a href="{{ route('editor.coaching-time.calendar') }}" class="btn btn-default btn-block" style="margin-bottom:15px;">
                            Åpne Redaktørkalender
                        </a>
                        <a href="#" class="btn btn-default btn-block">Gjenåpne Redaksjonstimer</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
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
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Dagens Timeplan</h4>
                    </div>
                    <div class="panel-body">
                        <table class="table schedule-table">
                            <thead>
                                <tr>
                                    <th>Tid</th>
                                    <th>Student</th>
                                    <th>Varighet</th>
                                    <th>Tema</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>14:00</td>
                                    <td>Kristine S. Heningsen</td>
                                    <td>60 min</td>
                                    <td>Utfordring</td>
                                    <td><a href="#" class="btn btn-default btn-xs">Beskrivelse</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
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
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requests as $req)
                                    <tr>
                                        <td>{{ $req->manuscript->user->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($req->slot->date)->format('d.m.Y') }} {{ $req->slot->start_time }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('editor.coaching-time.request.accept', $req->id) }}">
                                                @csrf
                                                <button class="btn btn-primary btn-xs">Accept</button>
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
        </div>
    </div>
@endsection
