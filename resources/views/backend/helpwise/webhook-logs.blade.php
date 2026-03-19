@extends('backend.layout')

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-list-alt"></i> Helpwise - Webhook-logger</h3>
    <a href="{{ route('admin.helpwise.index') }}" class="btn btn-default btn-sm pull-right"><i class="fa fa-arrow-left"></i> Tilbake</a>
</div>

<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped table-condensed">
                <thead>
                    <tr>
                        <th>Tid</th>
                        <th>Hendelse</th>
                        <th>Status</th>
                        <th>IP</th>
                        <th>Payload (utdrag)</th>
                        <th>Feil</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d.m.Y H:i:s') }}</td>
                            <td><code>{{ $log->event_type }}</code></td>
                            <td>
                                <span class="label label-{{ $log->status === 'processed' ? 'success' : ($log->status === 'failed' ? 'danger' : 'default') }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                            <td><small>{{ $log->ip_address }}</small></td>
                            <td>
                                <small><code>{{ \Illuminate\Support\Str::limit(json_encode($log->payload, JSON_UNESCAPED_UNICODE), 120) }}</code></small>
                            </td>
                            <td>
                                @if($log->error_message)
                                    <small class="text-danger">{{ $log->error_message }}</small>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">Ingen webhook-logger ennå. Konfigurer webhook i Helpwise for å starte.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $logs->links() }}
        </div>
    </div>

    <div class="panel panel-info">
        <div class="panel-heading"><strong><i class="fa fa-info-circle"></i> Webhook-oppsett i Helpwise</strong></div>
        <div class="panel-body">
            <p><strong>Callback URL:</strong> <code>{{ url('/api/webhooks/helpwise') }}</code></p>
            <p><strong>Webhook Secret Key:</strong> Sett samme verdi som <code>HELPWISE_WEBHOOK_SECRET</code> i .env</p>
            <p><strong>Anbefalte webhook-typer å aktivere:</strong></p>
            <ul>
                <li>Conversation created</li>
                <li>Conversation closed</li>
                <li>Conversation reopened</li>
                <li>Reply from the Agent</li>
                <li>Reply from the Customer</li>
                <li>Applied tag in Conversation</li>
            </ul>
        </div>
    </div>
</div>
@stop
