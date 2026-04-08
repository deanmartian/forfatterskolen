@extends('backend.layout')

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-history"></i> AI-handlinger audit-logg</h3>
    <a href="{{ route('admin.inbox.index') }}" class="btn btn-default btn-sm pull-right">
        <i class="fa fa-arrow-left"></i> Tilbake til inbox
    </a>
</div>

<div class="col-md-12">
    <div class="alert alert-info">
        <strong>Hva er dette?</strong> Alle AI-foreslåtte handlinger i inbox-systemet loggføres her — både de som er utført og de som ble foreslått men aldri klikket. Bruk dette for å se hva AI-en har anbefalt, hva admin faktisk har gjort, og eventuelle feil som har oppstått.
    </div>

    {{-- Statistikk --}}
    <div class="row" style="margin-bottom:20px;">
        <div class="col-md-2 col-sm-4">
            <div style="background:#f0f9ff; border-left:3px solid #3b82f6; padding:12px; border-radius:4px;">
                <div style="font-size:11px; color:#64748b; text-transform:uppercase;">Totalt</div>
                <div style="font-size:24px; font-weight:600; color:#1e40af;">{{ $stats['total'] }}</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4">
            <div style="background:#fefce8; border-left:3px solid #eab308; padding:12px; border-radius:4px;">
                <div style="font-size:11px; color:#64748b; text-transform:uppercase;">Foreslått</div>
                <div style="font-size:24px; font-weight:600; color:#a16207;">{{ $stats['suggested'] }}</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4">
            <div style="background:#f0fdf4; border-left:3px solid #22c55e; padding:12px; border-radius:4px;">
                <div style="font-size:11px; color:#64748b; text-transform:uppercase;">Utført</div>
                <div style="font-size:24px; font-weight:600; color:#166534;">{{ $stats['executed'] }}</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4">
            <div style="background:#fef2f2; border-left:3px solid #ef4444; padding:12px; border-radius:4px;">
                <div style="font-size:11px; color:#64748b; text-transform:uppercase;">Feilet</div>
                <div style="font-size:24px; font-weight:600; color:#b91c1c;">{{ $stats['failed'] }}</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4">
            <div style="background:#f9fafb; border-left:3px solid #9ca3af; padding:12px; border-radius:4px;">
                <div style="font-size:11px; color:#64748b; text-transform:uppercase;">Utløpt</div>
                <div style="font-size:24px; font-weight:600; color:#4b5563;">{{ $stats['expired'] }}</div>
            </div>
        </div>
    </div>

    {{-- Filtre --}}
    <div class="panel panel-default">
        <div class="panel-body">
            <form method="GET" action="{{ route('admin.ai-actions.index') }}" class="form-inline">
                <label style="margin-right:10px;">Status:
                    <select name="status" class="form-control input-sm">
                        <option value="">Alle</option>
                        <option value="suggested" @if($status === 'suggested') selected @endif>Foreslått</option>
                        <option value="executed" @if($status === 'executed') selected @endif>Utført</option>
                        <option value="failed" @if($status === 'failed') selected @endif>Feilet</option>
                        <option value="expired" @if($status === 'expired') selected @endif>Utløpt</option>
                    </select>
                </label>
                <label style="margin-right:10px;">Verktøy:
                    <select name="tool" class="form-control input-sm">
                        <option value="">Alle</option>
                        @foreach($toolNames as $name)
                            <option value="{{ $name }}" @if($toolName === $name) selected @endif>{{ $name }}</option>
                        @endforeach
                    </select>
                </label>
                <label style="margin-right:10px;">Fra dato:
                    <input type="date" name="since" value="{{ $since }}" class="form-control input-sm">
                </label>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filtrer</button>
                <a href="{{ route('admin.ai-actions.index') }}" class="btn btn-default btn-sm">Nullstill</a>
            </form>
        </div>
    </div>

    {{-- Tabell --}}
    <div class="panel panel-default">
        <div class="panel-body" style="padding:0;">
            @if($actions->isEmpty())
                <p style="padding:30px; text-align:center; color:#999;">Ingen handlinger å vise — prøv å endre filtrene.</p>
            @else
                <table class="table table-striped" style="margin-bottom:0;">
                    <thead>
                        <tr>
                            <th>Tidspunkt</th>
                            <th>Verktøy</th>
                            <th>Beskrivelse</th>
                            <th>Samtale</th>
                            <th>Status</th>
                            <th>Utført av</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($actions as $action)
                            <tr>
                                <td style="white-space:nowrap;">
                                    <small>{{ $action->created_at->format('d.m.Y H:i') }}</small>
                                </td>
                                <td><code style="font-size:11px;">{{ $action->tool_name }}</code></td>
                                <td>
                                    <strong>{{ $action->ui_label }}</strong>
                                    @if($action->error_message)
                                        <br><small style="color:#dc2626;"><i class="fa fa-exclamation-circle"></i> {{ $action->error_message }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($action->conversation_id)
                                        <a href="{{ route('admin.inbox.show', $action->conversation_id) }}">
                                            #{{ $action->conversation_id }}
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusColor = match($action->status->value) {
                                            'suggested' => '#eab308',
                                            'executed' => '#22c55e',
                                            'failed' => '#ef4444',
                                            'expired' => '#9ca3af',
                                            default => '#6b7280',
                                        };
                                    @endphp
                                    <span style="background:{{ $statusColor }}; color:#fff; padding:2px 8px; border-radius:4px; font-size:11px; text-transform:uppercase;">
                                        {{ $action->status->label() }}
                                    </span>
                                </td>
                                <td>
                                    @if($action->executedBy)
                                        <small>{{ $action->executedBy->first_name }} {{ $action->executedBy->last_name }}</small>
                                        @if($action->executed_at)
                                            <br><small style="color:#9ca3af;">{{ $action->executed_at->diffForHumans() }}</small>
                                        @endif
                                    @else
                                        <small style="color:#9ca3af;">—</small>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div>{{ $actions->links() }}</div>
</div>
@endsection
