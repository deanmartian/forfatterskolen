@extends('backend.layout')

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-flag"></i> {{ $campaign->name }}</h3>
    <a href="{{ route('admin.ads.campaigns') }}" class="btn btn-default btn-sm pull-right"><i class="fa fa-arrow-left"></i> Tilbake</a>
</div>

<div class="col-md-12">
    {{-- Campaign Info --}}
    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading"><strong>Kampanjedetaljer</strong></div>
                <div class="panel-body">
                    <table class="table table-condensed">
                        <tr><td width="200"><strong>Plattform</strong></td><td><i class="fa fa-{{ $campaign->platform === 'facebook' ? 'facebook-square' : 'google' }}"></i> {{ ucfirst($campaign->platform) }}</td></tr>
                        <tr><td><strong>Status</strong></td><td><span class="label label-{{ $campaign->isActive() ? 'success' : 'default' }}">{{ ucfirst($campaign->status) }}</span></td></tr>
                        <tr><td><strong>Mål</strong></td><td>{{ ucfirst($campaign->objective) }}</td></tr>
                        <tr><td><strong>Daglig budsjett</strong></td><td>{{ $campaign->daily_budget ? number_format($campaign->daily_budget, 2) . ' kr' : '-' }}</td></tr>
                        <tr><td><strong>Totalt brukt</strong></td><td>{{ number_format($campaign->spent_total, 2) }} kr</td></tr>
                        <tr><td><strong>Landingsside</strong></td><td>{{ $campaign->landing_page ?? '-' }}</td></tr>
                        <tr><td><strong>Ekstern ID</strong></td><td><code>{{ $campaign->external_id ?? '-' }}</code></td></tr>
                        <tr><td><strong>Automasjonsnivå</strong></td><td>{{ $campaign->automation_level ?? 'Arver fra strategi' }}</td></tr>
                        <tr><td><strong>Publisert</strong></td><td>{{ $campaign->published_at?->format('d.m.Y H:i') ?? '-' }}</td></tr>
                    </table>
                    @if($campaign->ai_notes)
                        <div class="well well-sm"><strong>AI-notater:</strong> {{ $campaign->ai_notes }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            {{-- Health Analysis --}}
            <div class="panel panel-{{ $analysis['status'] === 'healthy' ? 'success' : ($analysis['status'] === 'action_needed' ? 'warning' : 'default') }}">
                <div class="panel-heading"><strong><i class="fa fa-heartbeat"></i> Helseanalyse</strong></div>
                <div class="panel-body">
                    @if(!empty($analysis['signals']))
                        @foreach($analysis['signals'] as $signal)
                            <div class="alert alert-{{ $signal['severity'] === 'critical' ? 'danger' : ($signal['severity'] === 'positive' ? 'success' : 'warning') }}" style="padding: 8px; margin-bottom: 5px;">
                                <small>{{ $signal['message'] }}</small>
                            </div>
                        @endforeach
                    @else
                        <p class="text-success"><i class="fa fa-check"></i> Alt ser bra ut</p>
                    @endif

                    @if(!empty($analysis['metrics_summary']))
                        <hr>
                        <table class="table table-condensed" style="margin-bottom: 0;">
                            <tr><td>Visninger (7d)</td><td class="text-right">{{ number_format($analysis['metrics_summary']['impressions_7d']) }}</td></tr>
                            <tr><td>Klikk (7d)</td><td class="text-right">{{ number_format($analysis['metrics_summary']['clicks_7d']) }}</td></tr>
                            <tr><td>Konv. (7d)</td><td class="text-right">{{ number_format($analysis['metrics_summary']['conversions_7d']) }}</td></tr>
                            <tr><td>Forbruk (7d)</td><td class="text-right">{{ number_format($analysis['metrics_summary']['spend_7d'], 2) }} kr</td></tr>
                            <tr><td>Snitt CPA</td><td class="text-right">{{ $analysis['metrics_summary']['avg_cpa'] ? number_format($analysis['metrics_summary']['avg_cpa'], 2) . ' kr' : '-' }}</td></tr>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Ad Sets --}}
    <div class="panel panel-default">
        <div class="panel-heading"><strong>Ad Sets / Annonsegrupper</strong></div>
        <div class="panel-body">
            <table class="table table-striped">
                <thead><tr><th>Navn</th><th>Status</th><th>Budsjett</th><th>Annonser</th></tr></thead>
                <tbody>
                    @forelse($campaign->adSets as $adSet)
                        <tr>
                            <td>{{ $adSet->name }}</td>
                            <td><span class="label label-{{ $adSet->status === 'active' ? 'success' : 'default' }}">{{ ucfirst($adSet->status) }}</span></td>
                            <td>{{ $adSet->daily_budget ? number_format($adSet->daily_budget, 0) . ' kr' : '-' }}</td>
                            <td>{{ $adSet->ads->count() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted text-center">Ingen ad sets</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent AI Decisions --}}
    <div class="panel panel-default">
        <div class="panel-heading"><strong><i class="fa fa-lightbulb-o"></i> AI-beslutninger for denne kampanjen</strong></div>
        <div class="panel-body">
            <table class="table table-condensed">
                <thead><tr><th>Dato</th><th>Type</th><th>Begrunnelse</th><th>Konfidens</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($campaign->decisions->take(10) as $decision)
                        <tr>
                            <td>{{ $decision->created_at->format('d.m H:i') }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $decision->decision_type)) }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($decision->reasoning_summary, 80) }}</td>
                            <td>{{ round($decision->confidence * 100) }}%</td>
                            <td><span class="label label-{{ $decision->status === 'executed' ? 'success' : ($decision->status === 'rejected' ? 'danger' : 'default') }}">{{ ucfirst($decision->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted text-center">Ingen beslutninger ennå</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
