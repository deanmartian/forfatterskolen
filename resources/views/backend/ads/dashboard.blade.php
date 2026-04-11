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

    {{-- Charts --}}
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong><i class="fa fa-line-chart"></i> Forbruk og leads</strong>
                    <div class="pull-right">
                        <div class="btn-group btn-group-xs">
                            <button type="button" class="btn btn-default chart-period" data-days="7">7d</button>
                            <button type="button" class="btn btn-primary chart-period" data-days="14">14d</button>
                            <button type="button" class="btn btn-default chart-period" data-days="30">30d</button>
                        </div>
                        <small class="text-muted" id="lastUpdated" style="margin-left:8px;"></small>
                    </div>
                </div>
                <div class="panel-body" style="height: 300px;">
                    <canvas id="spendLeadsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading"><strong><i class="fa fa-pie-chart"></i> Budsjett denne mnd</strong></div>
                <div class="panel-body text-center" style="height: 300px; display: flex; align-items: center; justify-content: center;">
                    <canvas id="budgetChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><strong><i class="fa fa-area-chart"></i> CPA-trend</strong></div>
                <div class="panel-body" style="height: 250px;">
                    <canvas id="cpaChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><strong><i class="fa fa-bar-chart"></i> Kampanjer — forbruk vs leads</strong></div>
                <div class="panel-body" style="height: 250px;">
                    <canvas id="campaignCompareChart"></canvas>
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
            <a href="{{ route('admin.ads.strategist') }}" class="btn btn-primary"><i class="fa fa-magic"></i> AI Strategist</a>
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

@section('page_title', 'Ad OS — Forfatterskolen Admin')

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
(function() {
    var currentDays = 14;
    var spendLeadsChart, cpaChart, budgetChart, campaignChart;
    var wine = '#862736';
    var wineLight = 'rgba(134,39,54,0.15)';
    var blue = '#3b82f6';
    var blueLight = 'rgba(59,130,246,0.15)';
    var green = '#22c55e';
    var orange = '#f59e0b';

    // Budget doughnut (static data from Blade)
    var budgetCtx = document.getElementById('budgetChart');
    if (budgetCtx) {
        var spent = {{ $budgetInfo['spent_month'] ?? 0 }};
        var remaining = {{ $budgetInfo['remaining_monthly'] ?? 0 }};
        budgetChart = new Chart(budgetCtx, {
            type: 'doughnut',
            data: {
                labels: ['Brukt', 'Gjenstår'],
                datasets: [{
                    data: [spent, Math.max(0, remaining)],
                    backgroundColor: [wine, '#e8e4de'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 12 } } },
                    tooltip: { callbacks: { label: function(ctx) { return ctx.label + ': ' + Math.round(ctx.raw) + ' kr'; } } }
                }
            }
        });
    }

    // Campaign comparison (static from Blade — active campaigns)
    var campaignCtx = document.getElementById('campaignCompareChart');
    if (campaignCtx) {
        @php
            try {
                $activeCampaigns = \App\Models\AdOs\AdCampaign::where('status', 'published')
                    ->with(['metricSnapshots' => fn($q) => $q->where('date', '>=', now()->subDays(14))])
                    ->get();
            } catch (\Exception $e) {
                $activeCampaigns = collect();
            }
        @endphp
        campaignChart = new Chart(campaignCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($activeCampaigns->pluck('name')->map(fn($n) => \Illuminate\Support\Str::limit($n, 25))) !!},
                datasets: [
                    {
                        label: 'Forbruk (kr)',
                        data: {!! json_encode($activeCampaigns->map(fn($c) => round($c->metricSnapshots->sum('spend'), 0))) !!},
                        backgroundColor: wine,
                        barThickness: 20
                    },
                    {
                        label: 'Leads',
                        data: {!! json_encode($activeCampaigns->map(fn($c) => $c->metricSnapshots->sum('leads'))) !!},
                        backgroundColor: blue,
                        barThickness: 20
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } },
                scales: { x: { beginAtZero: true, grid: { display: false } } }
            }
        });
    }

    // Fetch time-series data via AJAX
    function fetchMetrics(days) {
        currentDays = days;
        fetch('{{ route("admin.ads.api.metrics") }}?days=' + days)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                renderSpendLeads(data);
                renderCPA(data);
                document.getElementById('lastUpdated').textContent = 'Oppdatert: ' + new Date().toLocaleTimeString('nb-NO', {hour:'2-digit',minute:'2-digit'});
            })
            .catch(function(err) { console.error('Metrics fetch error:', err); });
    }

    function renderSpendLeads(data) {
        var labels = data.map(function(d) { return d.date; });
        var spend = data.map(function(d) { return d.spend; });
        var leads = data.map(function(d) { return d.leads; });

        if (spendLeadsChart) spendLeadsChart.destroy();
        spendLeadsChart = new Chart(document.getElementById('spendLeadsChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Forbruk (kr)', data: spend, borderColor: wine, backgroundColor: wineLight, fill: true, tension: 0.3, yAxisID: 'y' },
                    { label: 'Leads', data: leads, borderColor: blue, backgroundColor: blueLight, fill: false, tension: 0.3, type: 'bar', yAxisID: 'y1', barThickness: 16 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } },
                scales: {
                    y: { type: 'linear', position: 'left', title: { display: true, text: 'Forbruk (kr)' }, beginAtZero: true, grid: { color: '#f0f0f0' } },
                    y1: { type: 'linear', position: 'right', title: { display: true, text: 'Leads' }, beginAtZero: true, grid: { display: false } }
                }
            }
        });
    }

    function renderCPA(data) {
        var labels = data.map(function(d) { return d.date; });
        var cpa = data.map(function(d) { return d.cpa; });
        var targetCpa = {{ $strategy?->target_cpa ?? 200 }};

        if (cpaChart) cpaChart.destroy();
        cpaChart = new Chart(document.getElementById('cpaChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    { label: 'CPA (kr)', data: cpa, borderColor: orange, backgroundColor: 'rgba(245,158,11,0.1)', fill: true, tension: 0.3 },
                    { label: 'Mål-CPA', data: Array(labels.length).fill(targetCpa), borderColor: green, borderDash: [5, 5], pointRadius: 0, fill: false }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } },
                scales: { y: { beginAtZero: true, grid: { color: '#f0f0f0' } } }
            }
        });
    }

    // Period toggle buttons
    document.querySelectorAll('.chart-period').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.chart-period').forEach(function(b) { b.classList.remove('btn-primary'); b.classList.add('btn-default'); });
            this.classList.remove('btn-default');
            this.classList.add('btn-primary');
            fetchMetrics(parseInt(this.dataset.days));
        });
    });

    // Initial load
    fetchMetrics(14);

    // Auto-refresh every 15 minutes
    setInterval(function() { fetchMetrics(currentDays); }, 15 * 60 * 1000);
})();
</script>
@stop
