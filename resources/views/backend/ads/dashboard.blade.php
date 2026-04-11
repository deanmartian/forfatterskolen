@extends('backend.layout')

@section('page_title', 'Annonser — Forfatterskolen Admin')

@section('content')
@php
    // === EKTE DATA FRA FACEBOOK ===
    $webinarsWithAds = \App\FreeWebinar::where(function ($q) {
        $q->whereNotNull('facebook_campaign_id')->orWhereNotNull('google_search_campaign_id');
    })->orderByDesc('start_date')->get();

    $totalFbSpend = $webinarsWithAds->sum('facebook_spend');
    $totalFbLeads = $webinarsWithAds->sum('facebook_leads_count');
    $totalFbClicks = $webinarsWithAds->sum('facebook_clicks');
    $totalFbImpressions = $webinarsWithAds->sum('facebook_impressions');
    $avgCpa = $totalFbLeads > 0 ? $totalFbSpend / $totalFbLeads : 0;
    $ctr = $totalFbImpressions > 0 ? ($totalFbClicks / $totalFbImpressions) * 100 : 0;

    // Siste leads fra Facebook
    $recentLeads = \DB::table('webinar_registrants')
        ->where('source', 'facebook')
        ->orderByDesc('created_at')
        ->limit(15)
        ->get();

    // AdCampaign-data (enklere modell)
    $adCampaigns = \App\Models\AdCampaign::orderByDesc('created_at')->limit(20)->get();
    $activeCampaigns = $adCampaigns->where('status', 'active');

    // Siste stats-oppdatering
    $lastUpdate = $webinarsWithAds->max('ad_stats_updated_at');
@endphp

<div class="page-toolbar">
    <h3><i class="fa fa-bullhorn"></i> Annonser</h3>
    <div class="pull-right">
        <button class="btn btn-sm btn-default" onclick="location.reload()"><i class="fa fa-refresh"></i> Oppdater</button>
        @if($lastUpdate)
            <small class="text-muted">Sist synket: {{ \Carbon\Carbon::parse($lastUpdate)->diffForHumans() }}</small>
        @endif
    </div>
</div>

<div class="col-md-12">

    {{-- KPI-kort --}}
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-md-2">
            <div class="panel panel-default" style="border-left: 4px solid #862736;">
                <div class="panel-body text-center">
                    <h2 style="margin:0;color:#862736;">{{ number_format($totalFbSpend, 0) }} kr</h2>
                    <small>Total forbruk</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="panel panel-default" style="border-left: 4px solid #3b82f6;">
                <div class="panel-body text-center">
                    <h2 style="margin:0;color:#3b82f6;">{{ number_format($totalFbLeads) }}</h2>
                    <small>Leads totalt</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="panel panel-default" style="border-left: 4px solid #f59e0b;">
                <div class="panel-body text-center">
                    <h2 style="margin:0;color:#f59e0b;">{{ $avgCpa > 0 ? number_format($avgCpa, 0) . ' kr' : '—' }}</h2>
                    <small>Snitt CPA</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="panel panel-default" style="border-left: 4px solid #22c55e;">
                <div class="panel-body text-center">
                    <h2 style="margin:0;color:#22c55e;">{{ number_format($totalFbClicks) }}</h2>
                    <small>Klikk totalt</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="panel panel-default" style="border-left: 4px solid #8b5cf6;">
                <div class="panel-body text-center">
                    <h2 style="margin:0;color:#8b5cf6;">{{ number_format($totalFbImpressions) }}</h2>
                    <small>Visninger</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="panel panel-default" style="border-left: 4px solid #06b6d4;">
                <div class="panel-body text-center">
                    <h2 style="margin:0;color:#06b6d4;">{{ number_format($ctr, 2) }}%</h2>
                    <small>CTR</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Webinar-kampanjer (ekte Facebook-data) --}}
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong><i class="fa fa-facebook-square" style="color:#1877f2;"></i> Facebook-kampanjer — Webinarer</strong>
                </div>
                <div class="panel-body" style="padding: 0;">
                    <table class="table table-striped table-condensed" style="margin:0;">
                        <thead>
                            <tr>
                                <th>Webinar</th>
                                <th>Startdato</th>
                                <th class="text-right">Forbruk</th>
                                <th class="text-right">Leads</th>
                                <th class="text-right">CPA</th>
                                <th class="text-right">Klikk</th>
                                <th class="text-right">Visninger</th>
                                <th class="text-right">CTR</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($webinarsWithAds as $w)
                                @php
                                    $wCpa = $w->facebook_leads_count > 0 ? $w->facebook_spend / $w->facebook_leads_count : 0;
                                    $wCtr = $w->facebook_impressions > 0 ? ($w->facebook_clicks / $w->facebook_impressions) * 100 : 0;
                                    $isLive = $w->start_date && \Carbon\Carbon::parse($w->start_date)->isFuture();
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $w->title }}</strong>
                                        <br><small class="text-muted">ID: {{ $w->facebook_campaign_id }}</small>
                                    </td>
                                    <td>{{ $w->start_date ? \Carbon\Carbon::parse($w->start_date)->format('d.m.Y H:i') : '—' }}</td>
                                    <td class="text-right"><strong>{{ number_format($w->facebook_spend, 0) }} kr</strong></td>
                                    <td class="text-right"><strong style="color:#3b82f6;">{{ $w->facebook_leads_count }}</strong></td>
                                    <td class="text-right">
                                        @if($wCpa > 0)
                                            <span style="color: {{ $wCpa < 50 ? '#22c55e' : ($wCpa < 100 ? '#f59e0b' : '#ef4444') }};">
                                                {{ number_format($wCpa, 0) }} kr
                                            </span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="text-right">{{ number_format($w->facebook_clicks) }}</td>
                                    <td class="text-right">{{ number_format($w->facebook_impressions) }}</td>
                                    <td class="text-right">{{ $wCtr > 0 ? number_format($wCtr, 2) . '%' : '—' }}</td>
                                    <td>
                                        @if($w->facebook_ad_status === 'paused' || !$isLive)
                                            <span class="label label-default">Avsluttet</span>
                                        @else
                                            <span class="label label-success">Aktiv</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-muted text-center">Ingen Facebook-kampanjer ennå.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Siste Facebook-leads --}}
    @if($recentLeads->count() > 0)
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong><i class="fa fa-users" style="color:#1877f2;"></i> Siste Facebook-leads</strong>
                    <span class="badge pull-right">{{ $recentLeads->count() }}</span>
                </div>
                <div class="panel-body" style="padding: 0; max-height: 400px; overflow-y: auto;">
                    <table class="table table-condensed" style="margin:0;">
                        @foreach($recentLeads as $lead)
                            <tr>
                                <td>
                                    <strong>{{ $lead->first_name ?? '' }} {{ $lead->last_name ?? '' }}</strong>
                                    <br><small class="text-muted">{{ $lead->email ?? '—' }}</small>
                                </td>
                                <td class="text-right">
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($lead->created_at)->diffForHumans() }}</small>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>

        {{-- Webinar-påmeldinger per dag (chart) --}}
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><strong><i class="fa fa-line-chart"></i> Leads per dag (siste 14 dager)</strong></div>
                <div class="panel-body" style="height: 370px;">
                    <canvas id="leadsPerDayChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Ad Campaigns (fra ad_campaigns-tabellen) --}}
    @if($adCampaigns->count() > 0)
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong><i class="fa fa-flag"></i> Kampanjer i systemet ({{ $activeCampaigns->count() }} aktive)</strong>
                </div>
                <div class="panel-body" style="padding: 0;">
                    <table class="table table-striped table-condensed" style="margin:0;">
                        <thead>
                            <tr>
                                <th>Kampanje</th>
                                <th>Type</th>
                                <th>Plattform</th>
                                <th>Status</th>
                                <th>Budsjett</th>
                                <th class="text-right">Forbruk</th>
                                <th class="text-right">Leads</th>
                                <th>Opprettet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($adCampaigns as $camp)
                                <tr>
                                    <td><strong>{{ \Illuminate\Support\Str::limit($camp->name, 45) }}</strong></td>
                                    <td><small>{{ $camp->type }}</small></td>
                                    <td>
                                        <span class="label label-{{ $camp->platform === 'facebook' ? 'primary' : 'warning' }}">
                                            <i class="fa fa-{{ $camp->platform === 'facebook' ? 'facebook' : 'google' }}"></i>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="label label-{{ $camp->status === 'active' ? 'success' : ($camp->status === 'paused' ? 'warning' : 'default') }}">
                                            {{ $camp->status === 'active' ? 'Aktiv' : ($camp->status === 'paused' ? 'Pauset' : ucfirst($camp->status)) }}
                                        </span>
                                    </td>
                                    <td>{{ $camp->daily_budget ? number_format($camp->daily_budget, 0) . ' kr/dag' : '—' }}</td>
                                    <td class="text-right">{{ $camp->total_spend > 0 ? number_format($camp->total_spend, 0) . ' kr' : '—' }}</td>
                                    <td class="text-right">{{ $camp->total_leads > 0 ? $camp->total_leads : '—' }}</td>
                                    <td><small>{{ $camp->created_at?->format('d.m.Y') }}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Quick Navigation --}}
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-md-12">
            <a href="{{ route('admin.ads.strategist') }}" class="btn btn-primary"><i class="fa fa-magic"></i> AI Strategist</a>
            <a href="{{ route('admin.ads.strategy') }}" class="btn btn-default"><i class="fa fa-cogs"></i> Strategi</a>
            <a href="{{ route('admin.ads.campaigns') }}" class="btn btn-default"><i class="fa fa-flag"></i> Alle kampanjer</a>
            <a href="{{ route('admin.ads.rules') }}" class="btn btn-default"><i class="fa fa-gavel"></i> Regler</a>
            <a href="{{ route('admin.ads.logs') }}" class="btn btn-default"><i class="fa fa-list"></i> Logger</a>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
(function() {
    var ctx = document.getElementById('leadsPerDayChart');
    if (!ctx) return;

    @php
        $leadsPerDay = \DB::table('webinar_registrants')
            ->where('source', 'facebook')
            ->where('created_at', '>=', now()->subDays(14))
            ->selectRaw('DATE(created_at) as date, count(*) as cnt')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dates = [];
        $counts = [];
        $cursor = now()->subDays(13)->startOfDay();
        for ($i = 0; $i < 14; $i++) {
            $d = $cursor->copy()->addDays($i)->format('Y-m-d');
            $dates[] = $cursor->copy()->addDays($i)->format('d.m');
            $row = $leadsPerDay->firstWhere('date', $d);
            $counts[] = $row ? $row->cnt : 0;
        }
    @endphp

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($dates) !!},
            datasets: [{
                label: 'Facebook-leads',
                data: {!! json_encode($counts) !!},
                backgroundColor: 'rgba(24,119,242,0.7)',
                borderRadius: 4,
                barThickness: 20
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { grid: { display: false } }
            }
        }
    });
})();
</script>
@stop
