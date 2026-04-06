@extends('backend.layout')

@section('styles')
<style>
    .inbox-sidebar { background: #f8f9fa; border-right: 1px solid #ddd; min-height: 70vh; }
    .inbox-sidebar .nav-item { padding: 8px 15px; cursor: pointer; border-bottom: 1px solid #eee; }
    .inbox-sidebar .nav-item:hover { background: #e9ecef; }
    .inbox-sidebar .nav-item.active { background: #fff; border-left: 3px solid #337ab7; }
    .inbox-row { padding: 12px 15px; border-bottom: 1px solid #f0f0f0; cursor: pointer; transition: background 0.15s; }
    .inbox-row:hover { background: #f5f8ff; }
    .inbox-row.unread { background: #f0f7ff; font-weight: 500; }
    .inbox-badge { font-size: 11px; padding: 2px 8px; border-radius: 10px; }
    .inbox-star { color: #ccc; cursor: pointer; font-size: 16px; }
    .inbox-star.starred { color: #f39c12; }
    .inbox-avatar { width: 36px; height: 36px; border-radius: 50%; background: #337ab7; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; }
    .inbox-snippet { color: #888; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 400px; }
</style>
@stop

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-inbox"></i> Inbox</h3>
    <div class="pull-right">
        <form action="{{ route('admin.inbox.import-helpwise') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-sm btn-default"><i class="fa fa-download"></i> Importer fra Helpwise</button>
        </form>
        <a href="{{ route('admin.inbox.canned-responses') }}" class="btn btn-sm btn-default"><i class="fa fa-bolt"></i> Hurtigsvar</a>
    </div>
</div>

<div class="col-md-12">
    @if(session('message'))
        <div class="alert alert-{{ session('alert_type', 'info') }}">{{ session('message') }}</div>
    @endif

    <div class="row">
        {{-- Sidebar --}}
        <div class="col-md-2" style="padding-right: 0;">
            <div class="inbox-sidebar">
                <a href="{{ route('admin.inbox.index', ['status' => 'open']) }}" class="nav-item {{ ($filters['status'] ?? '') === 'open' || empty($filters['status']) ? 'active' : '' }}" style="display:block; text-decoration:none; color: inherit;">
                    <i class="fa fa-inbox"></i> Åpne <span class="badge pull-right">{{ $stats['open'] }}</span>
                </a>
                <a href="{{ route('admin.inbox.index', ['status' => 'pending']) }}" class="nav-item {{ ($filters['status'] ?? '') === 'pending' ? 'active' : '' }}" style="display:block; text-decoration:none; color: inherit;">
                    <i class="fa fa-clock-o"></i> Venter <span class="badge pull-right">{{ $stats['pending'] }}</span>
                </a>
                <a href="{{ route('admin.inbox.index', ['assigned_to' => 'unassigned']) }}" class="nav-item {{ ($filters['assigned_to'] ?? '') === 'unassigned' ? 'active' : '' }}" style="display:block; text-decoration:none; color: inherit;">
                    <i class="fa fa-user-times"></i> Utildelt <span class="badge pull-right">{{ $stats['unassigned'] }}</span>
                </a>
                <a href="{{ route('admin.inbox.index', ['assigned_to' => auth()->id()]) }}" class="nav-item {{ ($filters['assigned_to'] ?? '') == auth()->id() ? 'active' : '' }}" style="display:block; text-decoration:none; color: inherit;">
                    <i class="fa fa-user"></i> Mine <span class="badge pull-right">{{ \App\Models\Inbox\InboxConversation::where('assigned_to', auth()->id())->whereIn('status', ['open', 'pending'])->count() }}</span>
                </a>
                <a href="{{ route('admin.inbox.index', ['starred' => 1]) }}" class="nav-item {{ !empty($filters['starred']) ? 'active' : '' }}" style="display:block; text-decoration:none; color: inherit;">
                    <i class="fa fa-star"></i> Stjernet <span class="badge pull-right">{{ $stats['starred'] }}</span>
                </a>
                <a href="{{ route('admin.inbox.index', ['status' => 'closed']) }}" class="nav-item {{ ($filters['status'] ?? '') === 'closed' ? 'active' : '' }}" style="display:block; text-decoration:none; color: inherit;">
                    <i class="fa fa-check-circle"></i> Lukket <span class="badge pull-right">{{ $stats['closed_today'] }}</span>
                </a>
                <a href="{{ route('admin.inbox.index', ['sent' => 1]) }}" class="nav-item {{ !empty($filters['sent']) ? 'active' : '' }}" style="display:block; text-decoration:none; color: inherit;">
                    <i class="fa fa-paper-plane"></i> Sendt
                </a>
                <a href="{{ route('admin.inbox.index', ['follow_up' => 1]) }}" class="nav-item {{ !empty($filters['follow_up']) ? 'active' : '' }}" style="display:block; text-decoration:none; color: inherit;">
                    <i class="fa fa-bell" style="color:#e65100;"></i> Oppfølging <span class="badge pull-right" style="background:#e65100;">{{ \App\Models\Inbox\InboxConversation::whereNotNull('follow_up_at')->where('follow_up_at', '<=', now())->count() }}</span>
                </a>

                <div style="padding: 10px 15px; border-top: 2px solid #ddd; margin-top: 10px;">
                    <strong style="font-size: 11px; text-transform: uppercase; color: #999;">Team</strong>
                </div>
                @foreach($teamMembers as $member)
                    <a href="{{ route('admin.inbox.index', ['assigned_to' => $member->id]) }}" class="nav-item {{ ($filters['assigned_to'] ?? '') == $member->id ? 'active' : '' }}" style="display:block; text-decoration:none; color: inherit; font-size: 13px;">
                        {{ $member->first_name }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Conversation List --}}
        <div class="col-md-10" style="padding-left: 0;">
            {{-- Search --}}
            <div style="padding: 10px 15px; border-bottom: 1px solid #ddd; background: #fff;">
                <form method="GET" class="form-inline">
                    @foreach($filters as $k => $v)
                        @if($k !== 'search')
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endif
                    @endforeach
                    <div class="input-group" style="width: 100%;">
                        <input type="text" name="search" class="form-control" placeholder="Søk i samtaler..." value="{{ $filters['search'] ?? '' }}">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </form>
            </div>

            {{-- Conversations --}}
            <div style="background: #fff;">
                @forelse($conversations as $conv)
                    <a href="{{ route('admin.inbox.show', $conv->id) }}" style="text-decoration: none; color: inherit; display: block;">
                        <div class="inbox-row">
                            <div class="row">
                                <div class="col-md-1" style="padding-top: 2px;">
                                    <div class="inbox-avatar">
                                        {{ strtoupper(substr($conv->customer_name ?? $conv->customer_email ?? '?', 0, 1)) }}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <strong>{{ $conv->customer_name ?? 'Ukjent' }}</strong><br>
                                    <small class="text-muted">{{ $conv->customer_email }}</small>
                                </div>
                                <div class="col-md-4">
                                    <strong>{{ $conv->subject ?? '(Uten emne)' }}</strong><br>
                                    <span class="inbox-snippet">
                                        {{ $conv->latestMessage?->clean_body ?? '' }}
                                    </span>
                                </div>
                                <div class="col-md-2">
                                    @if($conv->assignee)
                                        <span class="label label-info">{{ $conv->assignee->first_name }}</span>
                                    @else
                                        <span class="label label-default">Utildelt</span>
                                    @endif
                                    @if($conv->is_starred)
                                        <i class="fa fa-star" style="color: #f39c12;"></i>
                                    @endif
                                    @if($conv->user_id)
                                        <span class="label label-success" style="font-size: 10px;">Elev</span>
                                    @endif
                                </div>
                                <div class="col-md-2 text-right">
                                    <small class="text-muted">{{ $conv->updated_at->diffForHumans() }}</small><br>
                                    <span class="label label-{{ $conv->status === 'open' ? 'warning' : ($conv->status === 'closed' ? 'success' : 'default') }} inbox-badge">
                                        {{ $conv->status === 'open' ? 'Åpen' : ($conv->status === 'closed' ? 'Lukket' : ucfirst($conv->status)) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div style="padding: 40px; text-align: center; color: #999;">
                        <i class="fa fa-check-circle fa-3x" style="color: #28a745;"></i>
                        <h4>Ingen samtaler</h4>
                        <p>Alt er under kontroll!</p>
                    </div>
                @endforelse
            </div>

            <div style="padding: 15px;">
                {{ $conversations->appends($filters)->links() }}
            </div>
        </div>
    </div>
</div>
@stop
