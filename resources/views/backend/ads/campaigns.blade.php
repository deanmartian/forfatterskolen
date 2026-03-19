@extends('backend.layout')

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-flag"></i> Ad OS - Kampanjer</h3>
    <a href="{{ route('admin.ads.dashboard') }}" class="btn btn-default btn-sm pull-right"><i class="fa fa-arrow-left"></i> Tilbake</a>
</div>

<div class="col-md-12">
    {{-- Filters --}}
    <div class="panel panel-default">
        <div class="panel-body">
            <form method="GET" class="form-inline">
                <select name="status" class="form-control input-sm">
                    <option value="">Alle statuser</option>
                    @foreach(['draft', 'pending_approval', 'approved', 'active', 'paused', 'completed', 'archived'] as $s)
                        <option value="{{ $s }}" {{ ($filters['status'] ?? '') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
                <select name="platform" class="form-control input-sm">
                    <option value="">Alle plattformer</option>
                    <option value="facebook" {{ ($filters['platform'] ?? '') === 'facebook' ? 'selected' : '' }}>Facebook</option>
                    <option value="google" {{ ($filters['platform'] ?? '') === 'google' ? 'selected' : '' }}>Google</option>
                </select>
                <button type="submit" class="btn btn-sm btn-default">Filtrer</button>
            </form>
        </div>
    </div>

    {{-- Campaign Table --}}
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Kampanje</th>
                            <th>Plattform</th>
                            <th>Status</th>
                            <th>Mål</th>
                            <th>Daglig budsjett</th>
                            <th>Totalt brukt</th>
                            <th>Automasjon</th>
                            <th>Helse</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($campaigns as $campaign)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.ads.campaigns.show', $campaign->id) }}">
                                        <strong>{{ $campaign->name }}</strong>
                                    </a>
                                </td>
                                <td>
                                    <i class="fa fa-{{ $campaign->platform === 'facebook' ? 'facebook-square' : 'google' }}"></i>
                                    {{ ucfirst($campaign->platform) }}
                                </td>
                                <td>
                                    @php
                                        $statusColors = ['active' => 'success', 'paused' => 'warning', 'draft' => 'default', 'pending_approval' => 'info', 'error' => 'danger', 'completed' => 'primary'];
                                    @endphp
                                    <span class="label label-{{ $statusColors[$campaign->status] ?? 'default' }}">{{ ucfirst(str_replace('_', ' ', $campaign->status)) }}</span>
                                </td>
                                <td>{{ ucfirst($campaign->objective) }}</td>
                                <td>{{ $campaign->daily_budget ? number_format($campaign->daily_budget, 0) . ' kr' : '-' }}</td>
                                <td>{{ number_format($campaign->spent_total, 0) }} kr</td>
                                <td>
                                    <small>{{ $campaign->automation_level ?? '-' }}</small>
                                </td>
                                <td>
                                    @php $health = $campaign->health; @endphp
                                    <span class="label label-{{ $health === 'healthy' ? 'success' : ($health === 'warning' ? 'warning' : ($health === 'critical' ? 'danger' : 'default')) }}">
                                        {{ ucfirst($health) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.ads.campaigns.show', $campaign->id) }}" class="btn btn-xs btn-default">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">Ingen kampanjer funnet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $campaigns->appends($filters)->links() }}
        </div>
    </div>
</div>
@stop
