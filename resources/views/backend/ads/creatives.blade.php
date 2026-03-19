@extends('backend.layout')

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-paint-brush"></i> Ad OS - Kreative elementer</h3>
    <a href="{{ route('admin.ads.dashboard') }}" class="btn btn-default btn-sm pull-right"><i class="fa fa-arrow-left"></i> Tilbake</a>
</div>

<div class="col-md-12">
    @if(session('message'))
        <div class="alert alert-{{ session('alert_type', 'info') }}">{{ session('message') }}</div>
    @endif

    {{-- Generate New Creatives --}}
    <div class="panel panel-info">
        <div class="panel-heading"><strong><i class="fa fa-magic"></i> Generer nye kreative med AI</strong></div>
        <div class="panel-body">
            <form action="{{ route('admin.ads.creatives.generate') }}" method="POST" class="form-inline">
                @csrf
                <input type="text" name="product" class="form-control" placeholder="Produkt/tilbud" required style="width: 200px;">
                <input type="text" name="audience" class="form-control" placeholder="Målgruppe" required style="width: 200px;">
                <select name="goal" class="form-control">
                    <option value="leads">Leads</option>
                    <option value="conversions">Konverteringer</option>
                    <option value="traffic">Trafikk</option>
                    <option value="awareness">Kjennskap</option>
                </select>
                <select name="platform" class="form-control">
                    <option value="facebook">Facebook</option>
                    <option value="google">Google</option>
                    <option value="universal">Universell</option>
                </select>
                <input type="text" name="landing_page" class="form-control" placeholder="Landingsside URL" style="width: 200px;">
                <button type="submit" class="btn btn-info"><i class="fa fa-magic"></i> Generer</button>
            </form>
        </div>
    </div>

    {{-- Creative List --}}
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Navn</th>
                            <th>Plattform</th>
                            <th>Overskrifter</th>
                            <th>CTA</th>
                            <th>Gen.</th>
                            <th>Score</th>
                            <th>Status</th>
                            <th>Opprettet</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($creatives as $creative)
                            <tr>
                                <td>
                                    <strong>{{ $creative->name ?? 'Uten navn' }}</strong>
                                    @if($creative->variant_of)
                                        <br><small class="text-muted">Variant av #{{ $creative->variant_of }}</small>
                                    @endif
                                </td>
                                <td>{{ ucfirst($creative->platform) }}</td>
                                <td>
                                    @if($creative->headlines)
                                        @foreach(array_slice($creative->headlines, 0, 2) as $h)
                                            <small>{{ $h }}</small><br>
                                        @endforeach
                                        @if(count($creative->headlines) > 2)
                                            <small class="text-muted">+{{ count($creative->headlines) - 2 }} til</small>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $creative->cta ?? '-' }}</td>
                                <td>{{ $creative->generation }}</td>
                                <td>{{ $creative->performance_score ? number_format($creative->performance_score, 1) : '-' }}</td>
                                <td>
                                    <span class="label label-{{ $creative->status === 'active' ? 'success' : ($creative->status === 'archived' ? 'default' : 'info') }}">
                                        {{ ucfirst($creative->status) }}
                                    </span>
                                </td>
                                <td>{{ $creative->created_at->format('d.m.Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted">Ingen kreative elementer ennå. Bruk genereringen over for å starte.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $creatives->appends($filters)->links() }}
        </div>
    </div>
</div>
@stop
