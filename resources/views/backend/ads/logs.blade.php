@extends('backend.layout')

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-list"></i> Ad OS - Handlingslogg</h3>
    <a href="{{ route('admin.ads.dashboard') }}" class="btn btn-default btn-sm pull-right"><i class="fa fa-arrow-left"></i> Tilbake</a>
</div>

<div class="col-md-12">
    {{-- Filters --}}
    <div class="panel panel-default">
        <div class="panel-body">
            <form method="GET" class="form-inline">
                <select name="action_type" class="form-control input-sm">
                    <option value="">Alle handlinger</option>
                    @foreach($actionTypes as $type)
                        <option value="{{ $type }}" {{ ($filters['action_type'] ?? '') === $type ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                    @endforeach
                </select>
                <select name="triggered_by" class="form-control input-sm">
                    <option value="">Alle kilder</option>
                    <option value="human" {{ ($filters['triggered_by'] ?? '') === 'human' ? 'selected' : '' }}>Menneske</option>
                    <option value="ai" {{ ($filters['triggered_by'] ?? '') === 'ai' ? 'selected' : '' }}>AI</option>
                    <option value="rule" {{ ($filters['triggered_by'] ?? '') === 'rule' ? 'selected' : '' }}>Regel</option>
                    <option value="system" {{ ($filters['triggered_by'] ?? '') === 'system' ? 'selected' : '' }}>System</option>
                </select>
                <input type="date" name="date_from" class="form-control input-sm" value="{{ $filters['date_from'] ?? '' }}" placeholder="Fra dato">
                <input type="date" name="date_to" class="form-control input-sm" value="{{ $filters['date_to'] ?? '' }}" placeholder="Til dato">
                <button type="submit" class="btn btn-sm btn-default">Filtrer</button>
            </form>
        </div>
    </div>

    {{-- Log Table --}}
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped table-condensed">
                <thead>
                    <tr>
                        <th>Tid</th>
                        <th>Handling</th>
                        <th>Mål</th>
                        <th>Utløst av</th>
                        <th>Bruker</th>
                        <th>Status</th>
                        <th>Detaljer</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d.m.Y H:i:s') }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $log->action_type)) }}</td>
                            <td>
                                @if($log->target_type)
                                    {{ ucfirst(str_replace('_', ' ', $log->target_type)) }} #{{ $log->target_id }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="label label-{{ $log->triggered_by === 'ai' ? 'primary' : ($log->triggered_by === 'rule' ? 'warning' : ($log->triggered_by === 'human' ? 'info' : 'default')) }}">
                                    {{ ucfirst($log->triggered_by) }}
                                </span>
                            </td>
                            <td>{{ $log->user?->full_name ?? '-' }}</td>
                            <td>
                                <span class="label label-{{ $log->status === 'success' ? 'success' : ($log->status === 'failed' ? 'danger' : 'default') }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                            <td>
                                @if($log->payload)
                                    <small><code>{{ \Illuminate\Support\Str::limit(json_encode($log->payload, JSON_UNESCAPED_UNICODE), 80) }}</code></small>
                                @endif
                                @if($log->error_message)
                                    <br><small class="text-danger">{{ $log->error_message }}</small>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">Ingen logger funnet</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $logs->appends($filters)->links() }}
        </div>
    </div>
</div>
@stop
