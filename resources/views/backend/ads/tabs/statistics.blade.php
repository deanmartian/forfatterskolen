{{-- Statistikk --}}
<div class="row">
    <div class="col-md-12">
        <h5>Kampanjestatistikk</h5>

        <table class="table table-striped dt-table">
            <thead>
                <tr>
                    <th>Kampanje</th>
                    <th>Plattform</th>
                    <th>Status</th>
                    <th>Periode</th>
                    <th>Visninger</th>
                    <th>Klikk</th>
                    <th>CTR</th>
                    <th>Leads</th>
                    <th>Brukt</th>
                    <th>Kostnad/lead</th>
                </tr>
            </thead>
            <tbody>
                @foreach($campaigns as $campaign)
                    @php
                        $totalImpressions = $campaign->total_impressions;
                        $totalClicks = $campaign->total_clicks;
                        $totalLeads = $campaign->total_leads;
                        $totalSpend = $campaign->total_spend;
                        $ctr = $totalImpressions > 0 ? round(($totalClicks / $totalImpressions) * 100, 2) : 0;
                        $cpl = $campaign->cost_per_lead;
                    @endphp
                    <tr>
                        <td><strong>{{ $campaign->name }}</strong></td>
                        <td><i class="fa {{ $campaign->platform_icon }}" style="color: {{ $campaign->platform_color }};"></i> {{ ucfirst($campaign->platform) }}</td>
                        <td>{!! $campaign->status_badge !!}</td>
                        <td>
                            {{ $campaign->started_at?->format('d.m.Y') ?? '—' }}
                            @if($campaign->stopped_at) — {{ $campaign->stopped_at->format('d.m.Y') }} @endif
                        </td>
                        <td>{{ number_format($totalImpressions) }}</td>
                        <td>{{ number_format($totalClicks) }}</td>
                        <td>{{ $ctr }}%</td>
                        <td><strong>{{ number_format($totalLeads) }}</strong></td>
                        <td>kr {{ number_format($totalSpend, 0, ',', ' ') }}</td>
                        <td>
                            @if($cpl)
                                <strong>kr {{ number_format($cpl, 0, ',', ' ') }}</strong>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($campaigns->isEmpty())
            <div class="text-center text-muted" style="padding: 40px;">
                Ingen kampanjedata ennå.
            </div>
        @endif
    </div>
</div>
