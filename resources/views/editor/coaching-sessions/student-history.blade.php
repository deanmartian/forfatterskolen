@extends('editor.layout')

@section('page_title')Elevhistorikk: {{ $student->full_name }} &rsaquo; Forfatterskolen@endsection

@section('page-title')
    Elevhistorikk: {{ $student->full_name }}
@stop

@section('styles')
<style>
    .sh-back-link { color: #852635; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 5px; margin-bottom: 15px; }
    .sh-back-link:hover { text-decoration: underline; color: #5F0000; }

    .sh-student-card {
        background: #fff; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        padding: 20px; margin-bottom: 20px; display: flex; align-items: center; gap: 15px;
    }
    .sh-avatar {
        width: 50px; height: 50px; border-radius: 50%;
        background: #852635; color: #fff; font-size: 20px; font-weight: 600;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .sh-student-info h3 { margin: 0 0 4px; font-size: 18px; color: #333; }
    .sh-student-info p { margin: 0; color: #888; font-size: 13px; }

    .sh-session-card {
        background: #fff; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        padding: 20px; margin-bottom: 15px; border-left: 4px solid #852635;
    }
    .sh-session-card--completed { border-left-color: #28a745; }
    .sh-session-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; flex-wrap: wrap; gap: 10px; }
    .sh-session-header h5 { margin: 0; font-size: 15px; color: #333; }

    .cs-badge {
        display: inline-block; padding: 4px 10px; border-radius: 12px;
        font-size: 12px; font-weight: 600;
    }
    .cs-badge--scheduled { background: #fff3cd; color: #856404; }
    .cs-badge--active { background: #d4edda; color: #155724; }
    .cs-badge--completed { background: #e2e3e5; color: #383d41; }

    .sh-summary {
        background: #fafafa; border: 1px solid #eee; border-radius: 4px;
        padding: 12px; font-size: 13px; line-height: 1.6; color: #555;
        max-height: 150px; overflow-y: auto;
    }

    .sh-btn {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 6px 14px; border-radius: 4px;
        font-size: 13px; font-weight: 600; text-decoration: none;
        background: #852635; color: #fff;
    }
    .sh-btn:hover { background: #5F0000; color: #fff; text-decoration: none; }

    .sh-empty { text-align: center; padding: 40px; color: #888; }
    .sh-empty i { font-size: 48px; margin-bottom: 10px; display: block; }
</style>
@stop

@section('content')
<div class="container-fluid" style="max-width: 900px;">
    <a href="{{ route('editor.coaching-sessions.index') }}" class="sh-back-link">
        <i class="fa fa-arrow-left"></i> Tilbake til oversikten
    </a>

    <div class="sh-student-card">
        <div class="sh-avatar">
            {{ strtoupper(substr($student->first_name ?? '?', 0, 1)) }}
        </div>
        <div class="sh-student-info">
            <h3>{{ $student->full_name }}</h3>
            <p>{{ $student->email }} &middot; Elev #{{ $student->id }}</p>
        </div>
    </div>

    <h4 style="margin-bottom: 15px; font-weight: 600;">Samtaler ({{ $sessions->count() }})</h4>

    @forelse($sessions as $session)
        <div class="sh-session-card {{ $session->status == 'completed' ? 'sh-session-card--completed' : '' }}">
            <div class="sh-session-header">
                <h5>
                    @if($session->started_at)
                        {{ $session->started_at->format('d.m.Y H:i') }}
                    @else
                        {{ $session->created_at->format('d.m.Y') }}
                    @endif
                    @if($session->started_at && $session->ended_at)
                        ({{ $session->started_at->diffInMinutes($session->ended_at) }} min)
                    @endif
                </h5>
                <div style="display: flex; align-items: center; gap: 10px;">
                    @if($session->status == 'scheduled')
                        <span class="cs-badge cs-badge--scheduled">Planlagt</span>
                    @elseif($session->status == 'active')
                        <span class="cs-badge cs-badge--active">Aktiv</span>
                    @else
                        <span class="cs-badge cs-badge--completed">Fullført</span>
                    @endif
                    <a href="{{ route('editor.coaching-sessions.show', $session->id) }}" class="sh-btn">
                        <i class="fa fa-eye"></i> Vis
                    </a>
                </div>
            </div>

            @if($session->manuscript && $session->manuscript->help_with)
                <p style="color: #666; font-size: 13px; margin-bottom: 10px;">
                    <strong>Tema:</strong> {{ $session->manuscript->help_with }}
                </p>
            @endif

            @if($session->summary)
                <div class="sh-summary">{!! nl2br(e($session->summary)) !!}</div>
            @endif
        </div>
    @empty
        <div class="sh-empty">
            <i class="fa fa-comments-o"></i>
            <p>Ingen veiledningssamtaler med denne eleven ennå.</p>
        </div>
    @endforelse
</div>
@endsection
