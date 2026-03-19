@extends('backend.layout')

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-bullhorn"></i> Ad OS - Kontrollpanel</h3>
</div>

<div class="col-md-12">
    {{-- Kill Switch & Strategy Status --}}
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-md-8">
            @if($strategy)
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong><i class="fa fa-shield"></i> Aktiv strategi: {{ $strategy->name }}</strong>
                        <span class="label label-{{ $strategy->automation_level === 'full_operator' ? 'danger' : ($strategy->automation_level === 'supervised' ? 'warning' : 'info') }}" style="margin-left: 10px;">
                            {{ config('ad_os.automation_levels.' . $strategy->automation_level . '.label', $strategy->automation_level) }}
                        </span>
                        @if($strategy->approvalPolicy && $strategy->approvalPolicy->emergency_kill_switch)
                            <span class="label label-danger" style="margin-left: 5px;"><i class="fa fa-ban"></i> KILL SWITCH AKTIV</span>
                        @endif
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Mål:</strong> {{ config('ad_os.objectives.' . $strategy->primary_goal, $strategy->primary_goal) }}
                            </div>
                            <div class="col-md-3">
                                <strong>Mål-CPA:</strong> {{ $strategy->target_cpa ? number_format($strategy->target_cpa, 0) . ' kr' : '-' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Mål-ROAS:</strong> {{ $strategy->target_roas ?? '-' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Risikotoleranse:</strong> {{ ucfirst($strategy->risk_tolerance) }}
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i> Ingen aktiv strategiprofil. <a href="{{ route('admin.ads.strategy') }}">Konfigurer strategi</a>
                </div>
            @endif
        </div>
        <div class="col-md-4">
            <div class="panel panel-{{ ($strategy && $strategy->approvalPolicy && $strategy->approvalPolicy->emergency_kill_switch) ? 'danger' : 'default' }}">
                <div class="panel-body text-center">
                    <form action="{{ route('admin.ads.kill-switch') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-{{ ($strategy && $strategy->approvalPolicy && $strategy->approvalPolicy->emergency_kill_switch) ? 'success' : 'danger' }} btn-lg" onclick="return confirm('Er du sikker?')">
                            <i class="fa fa-{{ ($strategy && $strategy->approvalPolicy && $strategy->approvalPolicy->emergency_kill_switch) ? 'play' : 'stop' }}"></i>
                            {{ ($strategy && $strategy->approvalPolicy && $strategy->approvalPolicy->emergency_kill_switch) ? 'Deaktiver Kill Switch' : 'EMERGENCY KILL SWITCH' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-md-2">
            <div class="panel panel-info">
                <div class="panel-body text-center">
                    <h2 style="margin:0;">{{ $campaignStats['active'] ?? 0 }}</h2>
                    <small>Aktive kampanjer</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="panel panel-warning">
                <div class="panel-body text-center">
                    <h2 style="margin:0;">{{ $pendingApprovals }}</h2>
                    <small>Venter godkjenning</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="panel panel-default">
                <div class="panel-body text-center">
                    <h2 style="margin:0;">{{ number_format($budgetInfo['spent_today'], 0) }} kr</h2>
                    <small>Brukt i dag</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="panel panel-default">
                <div class="panel-body text-center">
                    <h2 style="margin:0;">{{ number_format($budgetInfo['remaining_daily'], 0) }} kr</h2>
                    <small>Gjenstår i dag</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="panel panel-default">
                <div class="panel-body text-center">
                    <h2 style="margin:0;">{{ number_format($budgetInfo['spent_month'], 0) }} kr</h2>
                    <small>Brukt denne mnd</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="panel panel-default">
                <div class="panel-body text-center">
                    <h2 style="margin:0;">{{ number_format($budgetInfo['remaining_monthly'], 0) }} kr</h2>
                    <small>Gjenstår mnd</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Daily Performance Summary --}}
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><strong><i class="fa fa-bar-chart"></i> Dagens ytelse</strong></div>
                <div class="panel-body">
                    @if($dailySummary['metrics'])
                        <table class="table table-condensed">
                            <tr><td>Visninger</td><td class="text-right"><strong>{{ number_format($dailySummary['metrics']['total_impressions']) }}</strong></td></tr>
                            <tr><td>Klikk</td><td class="text-right"><strong>{{ number_format($dailySummary['metrics']['total_clicks']) }}</strong></td></tr>
                            <tr><td>Konverteringer</td><td class="text-right"><strong>{{ number_format($dailySummary['metrics']['total_conversions']) }}</strong></td></tr>
                            <tr><td>Forbruk</td><td class="text-right"><strong>{{ number_format($dailySummary['metrics']['total_spend'], 2) }} kr</strong></td></tr>
                            <tr><td>Snitt CPA</td><td class="text-right"><strong>{{ $dailySummary['metrics']['avg_cpa'] ? number_format($dailySummary['metrics']['avg_cpa'], 2) . ' kr' : '-' }}</strong></td></tr>
                            <tr><td>Snitt CTR</td><td class="text-right"><strong>{{ $dailySummary['metrics']['avg_ctr'] ? number_format($dailySummary['metrics']['avg_ctr'] * 100, 2) . '%' : '-' }}</strong></td></tr>
                        </table>
                    @else
                        <p class="text-muted">Ingen data for i dag ennå.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- AI Recommendations --}}
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong><i class="fa fa-lightbulb-o"></i> AI-anbefalinger</strong>
                    <a href="{{ route('admin.ads.recommendations') }}" class="pull-right">Se alle</a>
                </div>
                <div class="panel-body">
                    @if($pendingRecommendations->count() > 0)
                        @foreach($pendingRecommendations as $rec)
                            <div style="border-left: 3px solid {{ config('ad_os.risk_levels.' . $rec->risk_level . '.color', '#ccc') }}; padding: 8px 12px; margin-bottom: 8px; background: #f9f9f9;">
                                <strong>{{ ucfirst(str_replace('_', ' ', $rec->decision_type)) }}</strong>
                                @if($rec->campaign)
                                    <small class="text-muted">- {{ $rec->campaign->name }}</small>
                                @endif
                                <br>
                                <small>{{ $rec->reasoning_summary }}</small>
                                <span class="pull-right">
                                    <span class="label label-default">{{ round($rec->confidence * 100) }}%</span>
                                </span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">Ingen ventende anbefalinger.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Actions --}}
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong><i class="fa fa-history"></i> Siste handlinger</strong>
                    <a href="{{ route('admin.ads.logs') }}" class="pull-right">Se alle</a>
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-condensed">
                        <thead>
                            <tr>
                                <th>Tid</th>
                                <th>Handling</th>
                                <th>Utløst av</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentActions as $action)
                                <tr>
                                    <td>{{ $action->created_at->format('d.m H:i') }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $action->action_type)) }}</td>
                                    <td>
                                        <span class="label label-{{ $action->triggered_by === 'ai' ? 'primary' : ($action->triggered_by === 'rule' ? 'warning' : 'default') }}">
                                            {{ ucfirst($action->triggered_by) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="label label-{{ $action->status === 'success' ? 'success' : 'danger' }}">
                                            {{ ucfirst($action->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Navigation --}}
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-md-12">
            <a href="{{ route('admin.ads.strategy') }}" class="btn btn-default"><i class="fa fa-cogs"></i> Strategi</a>
            <a href="{{ route('admin.ads.campaigns') }}" class="btn btn-default"><i class="fa fa-flag"></i> Kampanjer</a>
            <a href="{{ route('admin.ads.creatives') }}" class="btn btn-default"><i class="fa fa-paint-brush"></i> Kreative</a>
            <a href="{{ route('admin.ads.recommendations') }}" class="btn btn-default"><i class="fa fa-lightbulb-o"></i> Anbefalinger</a>
            <a href="{{ route('admin.ads.approvals') }}" class="btn btn-default"><i class="fa fa-check-circle"></i> Godkjenninger</a>
            <a href="{{ route('admin.ads.rules') }}" class="btn btn-default"><i class="fa fa-gavel"></i> Regler</a>
            <a href="{{ route('admin.ads.experiments') }}" class="btn btn-default"><i class="fa fa-flask"></i> Eksperimenter</a>
            <a href="{{ route('admin.ads.logs') }}" class="btn btn-default"><i class="fa fa-list"></i> Logger</a>
        </div>
    </div>
</div>
@stop
