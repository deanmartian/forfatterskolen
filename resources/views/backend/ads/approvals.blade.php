@extends('backend.layout')

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-check-circle"></i> Ad OS - Godkjenningskø</h3>
    <a href="{{ route('admin.ads.dashboard') }}" class="btn btn-default btn-sm pull-right"><i class="fa fa-arrow-left"></i> Tilbake</a>
</div>

<div class="col-md-12">
    @if(session('message'))
        <div class="alert alert-{{ session('alert_type', 'info') }}">{{ session('message') }}</div>
    @endif

    @forelse($approvals as $approval)
        <div class="panel panel-warning">
            <div class="panel-heading">
                <strong>{{ ucfirst(str_replace('_', ' ', $approval->decision->decision_type ?? 'ukjent')) }}</strong>
                @if($approval->decision?->campaign)
                    - {{ $approval->decision->campaign->name }}
                @endif
                <span class="pull-right">{{ $approval->created_at->format('d.m.Y H:i') }}</span>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-8">
                        <p><strong>AI-oppsummering:</strong> {{ $approval->ai_summary }}</p>

                        <div class="well well-sm">
                            <strong>Handling:</strong><br>
                            <pre style="background: none; border: none; padding: 0; margin: 0;">{{ json_encode($approval->action_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>

                        @if($approval->decision)
                            <p>
                                <span class="label label-{{ $approval->decision->risk_level === 'low' ? 'success' : ($approval->decision->risk_level === 'medium' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($approval->decision->risk_level) }} risiko
                                </span>
                                <span class="label label-default">{{ round(($approval->decision->confidence ?? 0) * 100) }}% konfidens</span>
                            </p>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <form action="{{ route('admin.ads.approvals.approve', $approval->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <textarea name="notes" class="form-control" rows="2" placeholder="Notater (valgfritt)"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Godkjenn og utfør?')">
                                <i class="fa fa-check"></i> Godkjenn
                            </button>
                        </form>
                        <form action="{{ route('admin.ads.approvals.reject', $approval->id) }}" method="POST" style="margin-top: 5px;">
                            @csrf
                            <input type="hidden" name="reason" value="">
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fa fa-times"></i> Avvis
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="panel panel-default">
            <div class="panel-body text-center text-muted">
                <i class="fa fa-check-circle fa-3x" style="color: #28a745;"></i>
                <h4>Ingen ventende godkjenninger</h4>
            </div>
        </div>
    @endforelse
</div>
@stop
