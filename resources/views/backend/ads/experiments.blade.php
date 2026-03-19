@extends('backend.layout')

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-flask"></i> Ad OS - Eksperimenter</h3>
    <a href="{{ route('admin.ads.dashboard') }}" class="btn btn-default btn-sm pull-right"><i class="fa fa-arrow-left"></i> Tilbake</a>
</div>

<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Navn</th>
                        <th>Hypotese</th>
                        <th>Kampanje</th>
                        <th>Varianter</th>
                        <th>Status</th>
                        <th>Vinner</th>
                        <th>Periode</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($experiments as $exp)
                        <tr>
                            <td><strong>{{ $exp->name }}</strong></td>
                            <td>{{ \Illuminate\Support\Str::limit($exp->hypothesis, 60) }}</td>
                            <td>{{ $exp->campaign?->name ?? '-' }}</td>
                            <td>{{ $exp->variants->count() }}</td>
                            <td>
                                <span class="label label-{{ $exp->status === 'completed' ? 'success' : ($exp->status === 'running' ? 'info' : 'default') }}">
                                    {{ ucfirst($exp->status) }}
                                </span>
                            </td>
                            <td>{{ $exp->winner?->label ?? '-' }}</td>
                            <td>
                                {{ $exp->started_at?->format('d.m') ?? '-' }}
                                {{ $exp->ended_at ? ' - ' . $exp->ended_at->format('d.m') : '' }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">Ingen eksperimenter ennå</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $experiments->links() }}
        </div>
    </div>
</div>
@stop
