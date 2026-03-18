{{-- Oversikt over alle kampanjer --}}
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row" style="margin-bottom: 15px;">
    <div class="col-md-6">
        <form class="form-inline" method="GET" action="{{ route('admin.ads.index') }}">
            <input type="hidden" name="tab" value="overview">
            <select name="platform" class="form-control form-control-sm" style="margin-right: 8px;">
                <option value="">Alle plattformer</option>
                <option value="facebook" {{ request('platform') === 'facebook' ? 'selected' : '' }}>Facebook</option>
                <option value="google" {{ request('platform') === 'google' ? 'selected' : '' }}>Google</option>
            </select>
            <select name="status" class="form-control form-control-sm" style="margin-right: 8px;">
                <option value="">Alle statuser</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktiv</option>
                <option value="paused" {{ request('status') === 'paused' ? 'selected' : '' }}>Pauset</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Utkast</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Avsluttet</option>
            </select>
            <button type="submit" class="btn btn-sm btn-outline-primary"><i class="fa fa-filter"></i> Filtrer</button>
        </form>
    </div>
    <div class="col-md-6 text-right">
        <a href="{{ route('admin.ads.index', ['tab' => 'create']) }}" class="btn btn-sm btn-primary">
            <i class="fa fa-plus"></i> Opprett ny annonse
        </a>
    </div>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Plattform</th>
            <th>Navn</th>
            <th>Type</th>
            <th>Koblet til</th>
            <th>Status</th>
            <th>Daglig budsjett</th>
            <th>Brukt totalt</th>
            <th>Leads/klikk</th>
            <th>Kostnad/lead</th>
            <th>Handlinger</th>
        </tr>
    </thead>
    <tbody>
        @forelse($campaigns as $campaign)
            <tr>
                <td><i class="fa {{ $campaign->platform_icon }}" style="color: {{ $campaign->platform_color }}; font-size: 18px;"></i></td>
                <td><strong>{{ $campaign->name }}</strong></td>
                <td>
                    @switch($campaign->type)
                        @case('lead') <span class="badge badge-primary">Lead</span> @break
                        @case('retargeting') <span class="badge badge-info">Retargeting</span> @break
                        @case('search') <span class="badge badge-warning">Søk</span> @break
                        @case('display') <span class="badge badge-secondary">Display</span> @break
                    @endswitch
                </td>
                <td>{{ $campaign->webinar?->title ?? '—' }}</td>
                <td>{!! $campaign->status_badge !!}</td>
                <td>{{ $campaign->daily_budget ? 'kr ' . number_format($campaign->daily_budget, 0, ',', ' ') : '—' }}</td>
                <td>kr {{ number_format($campaign->total_spend, 0, ',', ' ') }}</td>
                <td>{{ $campaign->total_leads }} / {{ $campaign->total_clicks }}</td>
                <td>{{ $campaign->cost_per_lead ? 'kr ' . number_format($campaign->cost_per_lead, 0, ',', ' ') : '—' }}</td>
                <td>
                    @if($campaign->status === 'active')
                        <form method="POST" action="{{ route('admin.ads.pause', $campaign) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-xs btn-warning" title="Paus"><i class="fa fa-pause"></i></button>
                        </form>
                    @elseif($campaign->status === 'paused' || $campaign->status === 'draft')
                        <form method="POST" action="{{ route('admin.ads.activate', $campaign) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-xs btn-success" title="Aktiver"><i class="fa fa-play"></i></button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('admin.ads.sync-stats', $campaign) }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-xs btn-info" title="Oppdater stats"><i class="fa fa-refresh"></i></button>
                    </form>
                    @if($campaign->status !== 'active')
                        <form method="POST" action="{{ route('admin.ads.destroy', $campaign) }}" style="display:inline;" onsubmit="return confirm('Slett denne kampanjen?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-danger" title="Slett"><i class="fa fa-trash"></i></button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center text-muted" style="padding: 40px;">
                    <i class="fa fa-bullhorn" style="font-size: 48px; opacity: 0.3;"></i><br>
                    Ingen kampanjer ennå. <a href="{{ route('admin.ads.index', ['tab' => 'create']) }}">Opprett din første annonse</a>.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

{{ $campaigns->appends(request()->query())->links() }}
