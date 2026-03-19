@extends('backend.layout')

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-comments"></i> Helpwise - Samtaler</h3>
    <div class="pull-right">
        <form action="{{ route('admin.helpwise.link-users') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-sm btn-default"><i class="fa fa-link"></i> Koble til elever</button>
        </form>
        <a href="{{ route('admin.helpwise.webhook-logs') }}" class="btn btn-sm btn-default"><i class="fa fa-list-alt"></i> Webhook-logger</a>
    </div>
</div>

<div class="col-md-12">
    @if(session('message'))
        <div class="alert alert-{{ session('alert_type', 'info') }}">{{ session('message') }}</div>
    @endif

    {{-- Stats --}}
    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-2">
            <div class="panel panel-info"><div class="panel-body text-center">
                <h2 style="margin:0;">{{ $stats['total_conversations'] }}</h2><small>Totalt samtaler</small>
            </div></div>
        </div>
        <div class="col-md-2">
            <div class="panel panel-warning"><div class="panel-body text-center">
                <h2 style="margin:0;">{{ $stats['open_conversations'] }}</h2><small>Åpne</small>
            </div></div>
        </div>
        <div class="col-md-2">
            <div class="panel panel-success"><div class="panel-body text-center">
                <h2 style="margin:0;">{{ $stats['linked_to_users'] }}</h2><small>Koblet til elever</small>
            </div></div>
        </div>
        <div class="col-md-2">
            <div class="panel panel-danger"><div class="panel-body text-center">
                <h2 style="margin:0;">{{ $stats['unlinked'] }}</h2><small>Ikke koblet</small>
            </div></div>
        </div>
        <div class="col-md-2">
            <div class="panel panel-default"><div class="panel-body text-center">
                <h2 style="margin:0;">{{ $stats['total_messages'] }}</h2><small>Totalt meldinger</small>
            </div></div>
        </div>
        <div class="col-md-2">
            <div class="panel panel-default"><div class="panel-body text-center">
                <h2 style="margin:0;">{{ $stats['conversations_today'] }}</h2><small>I dag</small>
            </div></div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="panel panel-default">
        <div class="panel-body">
            <form method="GET" class="form-inline">
                <input type="text" name="search" class="form-control input-sm" placeholder="Søk navn/e-post/emne..." value="{{ $filters['search'] ?? '' }}" style="width: 250px;">
                <select name="inbox" class="form-control input-sm">
                    <option value="">Alle inboxer</option>
                    @foreach($inboxes as $inbox)
                        <option value="{{ $inbox }}" {{ ($filters['inbox'] ?? '') === $inbox ? 'selected' : '' }}>{{ $inbox }}</option>
                    @endforeach
                </select>
                <select name="status" class="form-control input-sm">
                    <option value="">Alle statuser</option>
                    <option value="open" {{ ($filters['status'] ?? '') === 'open' ? 'selected' : '' }}>Åpen</option>
                    <option value="closed" {{ ($filters['status'] ?? '') === 'closed' ? 'selected' : '' }}>Lukket</option>
                    <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Venter</option>
                </select>
                <input type="text" name="assigned_to" class="form-control input-sm" placeholder="Tildelt til..." value="{{ $filters['assigned_to'] ?? '' }}">
                <button type="submit" class="btn btn-sm btn-default"><i class="fa fa-search"></i> Filtrer</button>
                @if(!empty(array_filter($filters)))
                    <a href="{{ route('admin.helpwise.index') }}" class="btn btn-sm btn-link">Nullstill</a>
                @endif
            </form>
        </div>
    </div>

    {{-- Conversations Table --}}
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Kunde</th>
                            <th>Emne</th>
                            <th>Inbox</th>
                            <th>Status</th>
                            <th>Tildelt</th>
                            <th>Elev</th>
                            <th>Siste aktivitet</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($conversations as $conv)
                            <tr>
                                <td>
                                    <strong>{{ $conv->customer_name ?? 'Ukjent' }}</strong>
                                    <br><small class="text-muted">{{ $conv->customer_email }}</small>
                                </td>
                                <td>{{ \Illuminate\Support\Str::limit($conv->subject, 50) ?? '-' }}</td>
                                <td><span class="label label-info">{{ $conv->inbox ?? '-' }}</span></td>
                                <td>
                                    <span class="label label-{{ $conv->status === 'open' ? 'warning' : ($conv->status === 'closed' ? 'success' : 'default') }}">
                                        {{ ucfirst($conv->status) }}
                                    </span>
                                </td>
                                <td>{{ $conv->assigned_to ?? '-' }}</td>
                                <td>
                                    @if($conv->user)
                                        <a href="{{ route('admin.helpwise.student', $conv->user_id) }}">
                                            {{ $conv->user->first_name }} {{ $conv->user->last_name }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $conv->updated_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.helpwise.show', $conv->id) }}" class="btn btn-xs btn-default">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted">Ingen samtaler funnet. Webhook-data kommer inn automatisk fra Helpwise.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $conversations->appends($filters)->links() }}
        </div>
    </div>
</div>
@stop
