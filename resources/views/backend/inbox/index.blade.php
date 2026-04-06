@extends('backend.layout')

@section('styles')
<style>
    .inbox-sidebar { background: #faf9f7; border-right: 1px solid #e8e4de; min-height: 70vh; }
    .inbox-sidebar .nav-item { padding: 9px 16px; cursor: pointer; border-radius: 6px; margin: 2px 8px; font-size: 13.5px; color: #4a4a4a; display: flex; align-items: center; gap: 8px; }
    .inbox-sidebar .nav-item:hover { background: rgba(134,39,54,0.06); color: #862736; }
    .inbox-sidebar .nav-item.active { background: rgba(134,39,54,0.1); color: #862736; font-weight: 600; }
    .inbox-sidebar .nav-item i { width: 16px; text-align: center; font-size: 13px; }
    .inbox-sidebar .badge { font-size: 10px; padding: 2px 7px; border-radius: 10px; background: #e8e4de; color: #666; font-weight: 500; }
    .inbox-sidebar .nav-item.active .badge { background: #862736; color: #fff; }

    .inbox-row { padding: 14px 16px; border-bottom: 1px solid #f0ede8; cursor: pointer; transition: background 0.12s; display: flex; align-items: center; gap: 12px; }
    .inbox-row:hover { background: #faf8f5; }
    .inbox-row.unread { background: #f5f0eb; }
    .inbox-avatar { width: 40px; height: 40px; border-radius: 50%; background: #862736; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 15px; flex-shrink: 0; }
    .inbox-row__content { flex: 1; min-width: 0; display: grid; grid-template-columns: 200px 1fr auto; gap: 12px; align-items: center; }
    .inbox-row__sender { font-weight: 600; font-size: 13.5px; color: #1a1a1a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .inbox-row__email { font-size: 12px; color: #8e8e8e; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .inbox-row__subject { font-weight: 600; font-size: 13.5px; color: #1a1a1a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .inbox-snippet { color: #8e8e8e; font-size: 12.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px; }
    .inbox-row__meta { text-align: right; white-space: nowrap; flex-shrink: 0; }
    .inbox-row__time { font-size: 12px; color: #8e8e8e; }
    .inbox-row__badges { display: flex; gap: 4px; margin-top: 4px; justify-content: flex-end; }
    .inbox-row__badges .label { font-size: 10px; padding: 2px 6px; border-radius: 3px; }
    .inbox-row__status { font-size: 11px; font-weight: 500; }
    .inbox-row__status--open { color: #e65100; }
    .inbox-row__status--closed { color: #2e7d32; }

    @media (max-width: 1200px) {
        .inbox-row__content { grid-template-columns: 160px 1fr auto; }
    }
    @media (max-width: 900px) {
        .inbox-row__content { grid-template-columns: 1fr; }
        .inbox-row__meta { display: none; }
    }
</style>
@stop

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-inbox"></i> Inbox</h3>
    <div class="pull-right">
        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#newEmailModal"><i class="fa fa-pencil"></i> Ny e-post</button>
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
                <a href="{{ route('admin.inbox.index', ['mentions' => 1]) }}" class="nav-item {{ !empty($filters['mentions']) ? 'active' : '' }}" style="display:block; text-decoration:none; color: inherit;">
                    <i class="fa fa-at"></i> Nevnt meg <span class="badge pull-right">{{ $stats['mentions'] }}</span>
                </a>
                <a href="{{ route('admin.inbox.index', ['awaiting' => 1]) }}" class="nav-item {{ !empty($filters['awaiting']) ? 'active' : '' }}" style="display:block; text-decoration:none; color: inherit;">
                    <i class="fa fa-hourglass-half"></i> Venter på svar <span class="badge pull-right">{{ $stats['awaiting'] }}</span>
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
                {{-- Bulk actions bar --}}
                <div id="bulkBar" style="display:none;background:#862736;color:#fff;padding:8px 16px;border-radius:6px;margin-bottom:10px;align-items:center;gap:12px;flex-wrap:wrap;">
                    <span id="bulkCount">0</span> valgt
                    <button type="button" class="btn btn-sm btn-success" onclick="bulkAction('close')"><i class="fa fa-check"></i> Lukk</button>
                    <button type="button" class="btn btn-sm btn-warning" onclick="bulkAction('reopen')"><i class="fa fa-refresh"></i> Gjenåpne</button>
                    <select id="bulkAssignSelect" style="padding:6px 10px;border-radius:4px;border:1px solid rgba(255,255,255,0.3);font-size:13px;background:#fff;color:#333;">
                        <option value="">Tildel til...</option>
                        @foreach($teamMembers as $member)
                            <option value="{{ $member->id }}">{{ $member->first_name }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-sm btn-info" onclick="bulkAssign()"><i class="fa fa-user"></i> Tildel</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('delete')"><i class="fa fa-trash"></i> Slett</button>
                    <button type="button" class="btn btn-sm btn-default" onclick="bulkSelectNone()">Avbryt</button>
                    <label style="margin-left:auto;cursor:pointer;font-size:12px;"><input type="checkbox" id="selectAll" onchange="bulkSelectAll(this)"> Velg alle</label>
                </div>
                <form id="bulkForm" method="POST" action="{{ route('admin.inbox.bulk') }}">@csrf<input type="hidden" name="action" id="bulkAction"><input type="hidden" name="ids" id="bulkIds"><input type="hidden" name="assign_to" id="bulkAssignTo"></form>

                @forelse($conversations as $conv)
                    <div class="inbox-row">
                        <input type="checkbox" class="bulk-check" value="{{ $conv->id }}" onchange="bulkChanged()" style="flex-shrink:0;">
                        <a href="{{ route('admin.inbox.show', $conv->id) }}" style="text-decoration:none;color:inherit;display:flex;align-items:center;gap:12px;flex:1;min-width:0;">
                            <div class="inbox-avatar">
                                {{ strtoupper(substr($conv->customer_name ?? $conv->customer_email ?? '?', 0, 1)) }}
                            </div>
                            <div class="inbox-row__content">
                                <div style="min-width:0;">
                                    <div class="inbox-row__sender">{{ $conv->customer_name ?? 'Ukjent' }}</div>
                                    <div class="inbox-row__email">{{ $conv->customer_email }}</div>
                                </div>
                                <div style="min-width:0;">
                                    <div class="inbox-row__subject">
                                        {{ $conv->subject ?? '(Uten emne)' }}
                                        @if($conv->is_starred) <i class="fa fa-star" style="color:#f39c12;font-size:11px;"></i> @endif
                                    </div>
                                    <div class="inbox-snippet">{{ \Illuminate\Support\Str::limit($conv->latestMessage?->clean_body ?? '', 100) }}</div>
                                </div>
                                <div class="inbox-row__meta">
                                    <div class="inbox-row__time">{{ $conv->updated_at->diffForHumans() }}</div>
                                    <div class="inbox-row__badges">
                                        @if($conv->assignee)
                                            <span class="label label-info">{{ $conv->assignee->first_name }}</span>
                                        @else
                                            <span class="label label-default">Utildelt</span>
                                        @endif
                                        @if($conv->user_id)
                                            <span class="label label-success">Elev</span>
                                        @endif
                                    </div>
                                    <div class="inbox-row__status inbox-row__status--{{ $conv->status }}">
                                        {{ $conv->status === 'open' ? 'Åpen' : ($conv->status === 'closed' ? 'Lukket' : ucfirst($conv->status)) }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
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

{{-- Ny e-post modal --}}
<div class="modal fade" id="newEmailModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:10px;overflow:hidden;">
            <div style="background:#862736;padding:18px 24px;color:#fff;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:0.8;">&times;</button>
                <h4 style="margin:0;font-size:17px;"><i class="fa fa-pencil"></i> Ny e-post</h4>
            </div>
            <form action="{{ route('admin.inbox.compose') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="padding:24px;">
                    <div class="form-group">
                        <label>Til</label>
                        <input type="email" name="to" class="form-control" placeholder="e-post@eksempel.no" required>
                    </div>
                    <div class="form-group">
                        <label>Emne</label>
                        <input type="text" name="subject" class="form-control" placeholder="Emne" required>
                    </div>
                    <div class="form-group">
                        <label>Melding</label>
                        <textarea name="body" class="form-control" rows="8" placeholder="Skriv meldingen din her..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label><i class="fa fa-paperclip"></i> Vedlegg</label>
                        <input type="file" name="attachments[]" multiple class="form-control">
                    </div>
                    <div style="background:#f8f8f8;border-radius:6px;padding:10px 14px;font-size:13px;color:#888;">
                        <em>Signatur legges til automatisk:</em><br>
                        <span style="color:#333;">Skrivevarm hilsen,<br>{{ Auth::user()->full_name }}<br>Forfatterskolen / Easywrite / Indiemoon Publishing</span>
                    </div>
                </div>
                <div style="padding:0 24px 24px;display:flex;gap:10px;">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane"></i> Send</button>
                    <button type="submit" name="save_draft" value="1" class="btn btn-default"><i class="fa fa-save"></i> Lagre som utkast</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Avbryt</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function bulkChanged() {
    var checked = document.querySelectorAll('.bulk-check:checked');
    var bar = document.getElementById('bulkBar');
    document.getElementById('bulkCount').textContent = checked.length;
    bar.style.display = checked.length > 0 ? 'flex' : 'none';
}
function bulkSelectAll(el) {
    document.querySelectorAll('.bulk-check').forEach(function(cb) { cb.checked = el.checked; });
    bulkChanged();
}
function bulkSelectNone() {
    document.querySelectorAll('.bulk-check').forEach(function(cb) { cb.checked = false; });
    document.getElementById('selectAll').checked = false;
    bulkChanged();
}
function bulkAction(action) {
    if (action === 'delete' && !confirm('Er du sikker på at du vil slette valgte samtaler?')) return;
    var ids = [];
    document.querySelectorAll('.bulk-check:checked').forEach(function(cb) { ids.push(cb.value); });
    document.getElementById('bulkAction').value = action;
    document.getElementById('bulkIds').value = JSON.stringify(ids);
    document.getElementById('bulkForm').submit();
}
function bulkAssign() {
    var assignTo = document.getElementById('bulkAssignSelect').value;
    if (!assignTo) { alert('Velg en person å tildele til'); return; }
    var ids = [];
    document.querySelectorAll('.bulk-check:checked').forEach(function(cb) { ids.push(cb.value); });
    document.getElementById('bulkAction').value = 'assign';
    document.getElementById('bulkIds').value = JSON.stringify(ids);
    document.getElementById('bulkAssignTo').value = assignTo;
    document.getElementById('bulkForm').submit();
}
</script>
@stop
