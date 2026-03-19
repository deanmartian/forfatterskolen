@extends('backend.layout')

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-lightbulb-o"></i> Ad OS - AI-anbefalinger</h3>
    <a href="{{ route('admin.ads.dashboard') }}" class="btn btn-default btn-sm pull-right"><i class="fa fa-arrow-left"></i> Tilbake</a>
</div>

<div class="col-md-12">
    @if(session('message'))
        <div class="alert alert-{{ session('alert_type', 'info') }}">{{ session('message') }}</div>
    @endif

    @forelse($recommendations as $rec)
        <div class="panel panel-default" style="border-left: 4px solid {{ $riskLevels[$rec->risk_level]['color'] ?? '#ccc' }};">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-7">
                        <h4 style="margin-top: 0;">
                            {{ ucfirst(str_replace('_', ' ', $rec->decision_type)) }}
                            <span class="label label-{{ $rec->risk_level === 'low' ? 'success' : ($rec->risk_level === 'medium' ? 'warning' : 'danger') }}">
                                {{ $riskLevels[$rec->risk_level]['label'] ?? ucfirst($rec->risk_level) }} risiko
                            </span>
                            <span class="label label-default">{{ round($rec->confidence * 100) }}% konfidens</span>
                        </h4>
                        @if($rec->campaign)
                            <p><strong>Kampanje:</strong> {{ $rec->campaign->name }} ({{ ucfirst($rec->campaign->platform) }})</p>
                        @endif
                        <p>{{ $rec->reasoning_summary }}</p>

                        @if($rec->proposed_action)
                            <div class="well well-sm" style="margin-bottom: 0;">
                                <strong>Foreslått handling:</strong>
                                <code>{{ json_encode($rec->proposed_action, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-5 text-right">
                        <form action="{{ route('admin.ads.recommendations.handle', $rec->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <input type="hidden" name="action" value="approve">
                            <div class="form-group">
                                <input type="text" name="notes" class="form-control input-sm" placeholder="Notater (valgfritt)" style="margin-bottom: 5px;">
                            </div>
                            <button type="submit" class="btn btn-success" onclick="return confirm('Godkjenn og utfør denne handlingen?')">
                                <i class="fa fa-check"></i> Godkjenn & utfør
                            </button>
                        </form>
                        <form action="{{ route('admin.ads.recommendations.handle', $rec->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="btn btn-danger">
                                <i class="fa fa-times"></i> Avvis
                            </button>
                        </form>
                        <br><br>
                        <small class="text-muted">{{ $rec->created_at->format('d.m.Y H:i') }}</small>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="panel panel-default">
            <div class="panel-body text-center text-muted">
                <i class="fa fa-check-circle fa-3x" style="color: #28a745;"></i>
                <h4>Ingen ventende anbefalinger</h4>
                <p>AI har ingen foreslåtte handlinger akkurat nå.</p>
            </div>
        </div>
    @endforelse
</div>
@stop
