@extends('backend.layout')

@section('title')
    <title>E-post-logg &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
<style>
    .log-filters {
        background: #fff; border-radius: 8px; padding: 1rem 1.25rem;
        box-shadow: 0 1px 3px rgba(0,0,0,.08); margin-bottom: 1.5rem;
        display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;
    }
    .log-filters .form-group { margin-bottom: 0; }
    .log-filters label { font-size: 0.75rem; text-transform: uppercase; color: #999; display: block; margin-bottom: 3px; }
    .log-filters input, .log-filters select { font-size: 0.85rem; }
    .log-table { width: 100%; border-collapse: collapse; }
    .log-table th {
        text-align: left; font-size: 0.75rem; text-transform: uppercase;
        color: #999; padding: .5rem .75rem; border-bottom: 1px solid #eee;
    }
    .log-table td { padding: .6rem .75rem; border-bottom: 1px solid #f5f5f5; font-size: 0.9rem; }
    .log-table tr:hover td { background: #fafafa; }
    .badge-status-sent { background: #27ae60; color: #fff; border-radius: 10px; padding: 2px 8px; font-size: 0.75rem; }
    .badge-status-failed { background: #e74c3c; color: #fff; border-radius: 10px; padding: 2px 8px; font-size: 0.75rem; }
</style>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-list"></i> E-post-logg</h3>
        <div class="pull-right">
            <a href="{{ route('admin.emails.index') }}" class="btn btn-default btn-sm">
                <i class="fa fa-arrow-left"></i> Tilbake til oversikt
            </a>
        </div>
    </div>

    {{-- Filtere --}}
    <form method="GET" action="{{ route('admin.emails.log') }}">
        <div class="log-filters">
            <div class="form-group">
                <label>Type</label>
                <select name="type" class="form-control input-sm" style="min-width:180px;">
                    <option value="">Alle typer</option>
                    @foreach($registry as $type => $info)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                            {{ $info['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control input-sm">
                    <option value="">Alle</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sendt</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Feilet</option>
                </select>
            </div>
            <div class="form-group">
                <label>Fra dato</label>
                <input type="date" name="from_date" class="form-control input-sm" value="{{ request('from_date') }}">
            </div>
            <div class="form-group">
                <label>Til dato</label>
                <input type="date" name="to_date" class="form-control input-sm" value="{{ request('to_date') }}">
            </div>
            <div class="form-group">
                <label>S&oslash;k</label>
                <input type="text" name="search" class="form-control input-sm" placeholder="E-post, emne..."
                       value="{{ request('search') }}" style="min-width:180px;">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Filtrer</button>
                <a href="{{ route('admin.emails.log') }}" class="btn btn-default btn-sm">Nullstill</a>
            </div>
        </div>
    </form>

    {{-- Tabell --}}
    <div style="background:#fff; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,.08);">
        <table class="log-table">
            <thead>
                <tr>
                    <th>Tid</th>
                    <th>Til</th>
                    <th>Emne</th>
                    <th>Type</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td style="white-space:nowrap; color:#999; font-size:0.85rem;">
                            {{ $log->created_at->format('d.m.Y H:i') }}
                        </td>
                        <td>
                            <strong>{{ $log->to_email }}</strong>
                            @if($log->to_name)
                                <br><small style="color:#999;">{{ $log->to_name }}</small>
                            @endif
                        </td>
                        <td>{{ \Illuminate\Support\Str::limit($log->subject, 60) }}</td>
                        <td style="font-size:0.85rem; color:#777;">
                            {{ $mailableNames[$log->mailable_class] ?? class_basename($log->mailable_class) }}
                        </td>
                        <td>
                            <span class="badge-status-{{ $log->status }}">{{ $log->status }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center; color:#999; padding:2rem;">
                            Ingen e-poster funnet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pull-right" style="margin-top:1rem;">
        {{ $logs->links() }}
    </div>
@stop
