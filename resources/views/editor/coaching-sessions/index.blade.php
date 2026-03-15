@extends('editor.layout')

@section('title')
    <title>Veiledningssamtaler &rsaquo; Forfatterskolen</title>
@stop

@section('page-title', 'Veiledningssamtaler')

@section('styles')
<style>
    .cs-stats { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
    .cs-stat-card {
        background: #fff; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 20px; text-align: center; flex: 1; min-width: 150px;
    }
    .cs-stat-card h2 { margin: 0; font-size: 36px; color: #5F0000; }
    .cs-stat-card p { margin: 0; font-weight: bold; text-transform: uppercase; font-size: 12px; color: #666; }

    .cs-table th { font-size: 13px; text-transform: uppercase; color: #888; border-bottom: 2px solid #eee; }
    .cs-table td { vertical-align: middle !important; }

    .cs-badge {
        display: inline-block; padding: 4px 10px; border-radius: 12px;
        font-size: 12px; font-weight: 600;
    }
    .cs-badge--scheduled { background: #fff3cd; color: #856404; }
    .cs-badge--active { background: #d4edda; color: #155724; }
    .cs-badge--completed { background: #e2e3e5; color: #383d41; }

    .cs-btn {
        display: inline-block; padding: 6px 14px; border-radius: 4px;
        font-size: 13px; font-weight: 600; text-decoration: none;
        background: #852635; color: #fff; border: none; cursor: pointer;
    }
    .cs-btn:hover { background: #5F0000; color: #fff; text-decoration: none; }
    .cs-btn--outline {
        background: transparent; color: #852635; border: 1px solid #852635;
    }
    .cs-btn--outline:hover { background: #852635; color: #fff; }

    .cs-student-link { color: #852635; font-weight: 600; text-decoration: none; }
    .cs-student-link:hover { text-decoration: underline; color: #5F0000; }
</style>
@stop

@section('content')
<div class="container-fluid">
    @php
        $scheduled = $sessions->where('status', 'scheduled')->count();
        $active = $sessions->where('status', 'active')->count();
        $completed = $sessions->where('status', 'completed')->count();
    @endphp

    <div class="cs-stats">
        <div class="cs-stat-card">
            <p>Planlagte</p>
            <h2>{{ $scheduled }}</h2>
        </div>
        <div class="cs-stat-card">
            <p>Aktive</p>
            <h2>{{ $active }}</h2>
        </div>
        <div class="cs-stat-card">
            <p>Fullførte</p>
            <h2>{{ $completed }}</h2>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 style="margin:0;">Alle veiledningssamtaler</h4>
        </div>
        <div class="panel-body">
            <table class="table cs-table dt-table">
                <thead>
                    <tr>
                        <th>Dato</th>
                        <th>Elev</th>
                        <th>Manuskript</th>
                        <th>Varighet</th>
                        <th>Status</th>
                        <th>Oppsummering</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $session)
                        <tr>
                            <td>
                                @if($session->started_at)
                                    {{ $session->started_at->format('d.m.Y H:i') }}
                                @elseif($session->manuscript && $session->manuscript->timeSlot)
                                    @php
                                        $dt = \Carbon\Carbon::parse(
                                            $session->manuscript->timeSlot->date . ' ' . $session->manuscript->timeSlot->start_time,
                                            'UTC'
                                        )->setTimezone(config('app.timezone'));
                                    @endphp
                                    {{ $dt->format('d.m.Y H:i') }}
                                @else
                                    {{ $session->created_at->format('d.m.Y') }}
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('editor.coaching-sessions.student-history', $session->student_id) }}" class="cs-student-link">
                                    {{ $session->student->full_name ?? 'Ukjent' }}
                                </a>
                            </td>
                            <td>
                                @if($session->manuscript && $session->manuscript->help_with)
                                    {{ \Illuminate\Support\Str::limit($session->manuscript->help_with, 40) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($session->started_at && $session->ended_at)
                                    {{ $session->started_at->diffInMinutes($session->ended_at) }} min
                                @elseif($session->manuscript && $session->manuscript->timeSlot)
                                    {{ $session->manuscript->timeSlot->duration }} min
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($session->status == 'scheduled')
                                    <span class="cs-badge cs-badge--scheduled">Planlagt</span>
                                @elseif($session->status == 'active')
                                    <span class="cs-badge cs-badge--active">Aktiv</span>
                                @else
                                    <span class="cs-badge cs-badge--completed">Fullført</span>
                                @endif
                            </td>
                            <td>
                                @if($session->summary)
                                    <i class="fa fa-check-circle" style="color: #28a745;"></i>
                                @elseif($session->transcription)
                                    <i class="fa fa-spinner fa-spin" style="color: #ffc107;" title="Oppsummering pågår..."></i>
                                @elseif($session->recording_path)
                                    <i class="fa fa-spinner fa-spin" style="color: #17a2b8;" title="Transkripsjon pågår..."></i>
                                @else
                                    <span style="color: #ccc;">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('editor.coaching-sessions.show', $session->id) }}" class="cs-btn">
                                    <i class="fa fa-eye"></i> Vis
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">Ingen veiledningssamtaler ennå.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
