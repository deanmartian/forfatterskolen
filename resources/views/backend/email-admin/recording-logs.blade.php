@extends('backend.layouts.app')
@section('content')
<div class="container-fluid" style="padding: 20px;">
    <h2><i class="fa fa-video"></i> Webinar Recording Nedlastingslogg</h2>
    <p class="text-muted">Oversikt over automatisk nedlasting av webinar-opptak til Wistia.</p>

    <div style="display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap;">
        <div style="background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px 20px; flex: 1; min-width: 120px; text-align: center;">
            <div style="font-size: 28px; font-weight: bold; color: #4CAF50;">{{ $logs->where('status', 'success')->count() }}</div>
            <div style="font-size: 12px; color: #888;">Lastet ned</div>
        </div>
        <div style="background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px 20px; flex: 1; min-width: 120px; text-align: center;">
            <div style="font-size: 28px; font-weight: bold; color: #F44336;">{{ $logs->where('status', 'failed')->count() }}</div>
            <div style="font-size: 12px; color: #888;">Feilet</div>
        </div>
        <div style="background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px 20px; flex: 1; min-width: 120px; text-align: center;">
            <div style="font-size: 28px; font-weight: bold; color: #862736;">{{ $logs->count() }}</div>
            <div style="font-size: 12px; color: #888;">Totalt</div>
        </div>
    </div>

    <table class="table table-striped" style="background: #fff;">
        <thead>
            <tr>
                <th>Dato</th>
                <th>Webinar</th>
                <th>Kurs</th>
                <th>Wistia ID</th>
                <th>Leksjon</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d.m.Y H:i') }}</td>
                <td>{{ $log->webinar_title }}</td>
                <td>{{ $log->course_name }}</td>
                <td>
                    @if($log->wistia_id)
                        <a href="https://forfatterskolen.wistia.com/medias/{{ $log->wistia_id }}" target="_blank">{{ $log->wistia_id }}</a>
                    @else
                        —
                    @endif
                </td>
                <td>{{ $log->lesson_title ?? '—' }}</td>
                <td>
                    @if($log->status === 'success')
                        <span style="background: #4CAF50; color: #fff; padding: 2px 8px; border-radius: 12px; font-size: 11px;">OK</span>
                    @elseif($log->status === 'failed')
                        <span style="background: #F44336; color: #fff; padding: 2px 8px; border-radius: 12px; font-size: 11px;" title="{{ $log->error_message }}">Feilet</span>
                    @else
                        <span style="background: #FF9800; color: #fff; padding: 2px 8px; border-radius: 12px; font-size: 11px;">{{ $log->status }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 30px; color: #888;">
                    <i class="fa fa-inbox" style="font-size: 24px;"></i><br>
                    Ingen nedlastinger logget enna. Scheduler kjorer hvert 30. minutt.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
