@extends('backend.layout')

@section('styles')
<style>
    .msg-bubble { padding: 14px 18px; border-radius: 12px; margin-bottom: 12px; max-width: 85%; word-wrap: break-word; }
    .msg-inbound { background: #f0f0f0; border-left: 3px solid #95a5a6; margin-right: auto; }
    .msg-outbound { background: #e8f4fd; border-left: 3px solid #3498db; margin-left: auto; }
    .msg-ai-draft { background: #fef9e7; border-left: 3px solid #f39c12; margin-left: auto; border: 1px dashed #f39c12; }
    .msg-comment { background: #fff3cd; border-left: 3px solid #ffc107; margin: 0 20px 12px; padding: 10px 14px; border-radius: 8px; font-size: 13px; }
    .msg-assignment { text-align: center; color: #999; font-size: 12px; margin: 8px 0; }
    .msg-meta { font-size: 11px; color: #999; margin-bottom: 4px; }
    .reply-box { background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 15px; }
    .sidebar-card { background: #fff; border: 1px solid #eee; border-radius: 8px; padding: 15px; margin-bottom: 15px; }
    .sidebar-card h5 { margin-top: 0; font-size: 13px; text-transform: uppercase; color: #999; margin-bottom: 10px; }
    .student-info td { padding: 3px 8px; font-size: 13px; }
    .action-btn { margin: 2px; }
</style>
@stop

@section('content')
<div class="page-toolbar">
    <h3>
        <a href="{{ route('admin.inbox.index') }}" style="color: #333;"><i class="fa fa-arrow-left"></i></a>
        {{ $conversation->subject ?? '(Uten emne)' }}
    </h3>
    <div class="pull-right">
        <form action="{{ route('admin.inbox.toggle-star', $conversation->id) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-sm btn-default"><i class="fa fa-star{{ $conversation->is_starred ? '' : '-o' }}" style="color: {{ $conversation->is_starred ? '#f39c12' : '#ccc' }};"></i></button>
        </form>

        <form action="{{ route('admin.inbox.status', $conversation->id) }}" method="POST" style="display:inline;">
            @csrf
            @if($conversation->status !== 'closed')
                <input type="hidden" name="status" value="closed">
                <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-check"></i> Lukk</button>
            @else
                <input type="hidden" name="status" value="open">
                <button type="submit" class="btn btn-sm btn-warning"><i class="fa fa-refresh"></i> Gjenåpne</button>
            @endif
        </form>

        <form action="{{ route('admin.inbox.spam', $conversation->id) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Markere som spam?')"><i class="fa fa-ban"></i></button>
        </form>
    </div>
</div>

<div class="col-md-12">
    @if(session('message'))
        <div class="alert alert-{{ session('alert_type', 'info') }}">{{ session('message') }}</div>
    @endif

    <div class="row">
        {{-- Message Thread --}}
        <div class="col-md-8">
            {{-- Timeline --}}
            <div style="max-height: 500px; overflow-y: auto; padding: 15px; background: #fafafa; border-radius: 8px; margin-bottom: 15px;" id="message-thread">
                @foreach($timeline as $entry)
                    @if($entry['type'] === 'customer')
                        <div class="msg-bubble msg-inbound">
                            <div class="msg-meta">
                                <strong><i class="fa fa-envelope-o"></i> {{ $entry['item']->from_name ?? $entry['item']->from_email ?? 'Kunde' }}</strong>
                                <span class="pull-right">{{ $entry['at']->format('d.m.Y H:i') }}</span>
                            </div>
                            {!! nl2br(e($entry['item']->body_plain ?? strip_tags($entry['item']->body ?? ''))) !!}
                        </div>
                    @elseif($entry['type'] === 'reply')
                        <div class="msg-bubble msg-outbound">
                            <div class="msg-meta">
                                <strong><i class="fa fa-reply"></i> {{ $entry['item']->sender?->first_name ?? $entry['item']->from_name ?? 'Agent' }}</strong>
                                @if($entry['item']->is_draft)
                                    <span class="label label-warning">Utkast</span>
                                @endif
                                <span class="pull-right">{{ $entry['at']->format('d.m.Y H:i') }}</span>
                            </div>
                            {!! nl2br(e($entry['item']->body_plain ?? strip_tags($entry['item']->body ?? ''))) !!}
                        </div>
                    @elseif($entry['type'] === 'ai_draft')
                        <div class="msg-bubble msg-ai-draft">
                            <div class="msg-meta">
                                <strong><i class="fa fa-magic"></i> AI Draft</strong>
                                <span class="label label-warning">Utkast - ikke sendt</span>
                                <span class="pull-right">{{ $entry['at']->format('d.m.Y H:i') }}</span>
                            </div>
                            <div id="ai-draft-text">{!! nl2br(e($entry['item']->body_plain ?? $entry['item']->body ?? '')) !!}</div>
                            <div style="margin-top: 8px;">
                                <button class="btn btn-xs btn-info" onclick="document.getElementById('reply-body').value = document.getElementById('ai-draft-text').innerText;">
                                    <i class="fa fa-copy"></i> Bruk som svar
                                </button>
                            </div>
                        </div>
                    @elseif($entry['type'] === 'comment')
                        <div class="msg-comment">
                            <i class="fa fa-comment-o"></i>
                            <strong>{{ $entry['item']->user?->first_name ?? 'Ukjent' }}</strong>
                            <span class="pull-right text-muted">{{ $entry['at']->format('d.m.Y H:i') }}</span>
                            <br>{{ $entry['item']->body }}
                        </div>
                    @elseif($entry['type'] === 'assignment')
                        <div class="msg-assignment">
                            <i class="fa fa-user-plus"></i>
                            Tildelt til <strong>{{ $entry['item']->assignedTo?->first_name ?? 'ukjent' }}</strong>
                            av {{ $entry['item']->assignedBy?->first_name ?? 'system' }}
                            - {{ $entry['at']->format('d.m H:i') }}
                        </div>
                    @endif
                @endforeach

                @if($timeline->isEmpty())
                    <p class="text-muted text-center" style="padding: 30px;">Ingen meldinger ennå.</p>
                @endif
            </div>

            {{-- Reply Box --}}
            <div class="reply-box">
                <ul class="nav nav-tabs" style="margin-bottom: 15px;">
                    <li class="active"><a data-toggle="tab" href="#tab-reply"><i class="fa fa-reply"></i> Svar</a></li>
                    <li><a data-toggle="tab" href="#tab-comment"><i class="fa fa-comment-o"></i> Intern kommentar</a></li>
                </ul>

                <div class="tab-content">
                    <div id="tab-reply" class="tab-pane active">
                        <form action="{{ route('admin.inbox.reply', $conversation->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Til: <strong>{{ $conversation->customer_email }}</strong></label>
                            </div>
                            <div class="form-group">
                                <textarea name="body" id="reply-body" class="form-control" rows="6" placeholder="Skriv ditt svar her..." required></textarea>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane"></i> Send svar</button>
                                <button type="submit" name="send_and_close" value="1" class="btn btn-success"><i class="fa fa-check"></i> Send og lukk</button>
                                <button type="submit" name="save_as_draft" value="1" class="btn btn-default"><i class="fa fa-save"></i> Lagre utkast</button>
                                <button type="button" class="btn btn-warning" id="btn-ai-draft"><i class="fa fa-magic"></i> Generer AI-utkast</button>
                            </div>
                        </form>
                        <form id="ai-draft-form" action="{{ route('admin.inbox.ai-draft', $conversation->id) }}" method="POST" style="display:none;">
                            @csrf
                        </form>
                    </div>

                    <div id="tab-comment" class="tab-pane">
                        <form action="{{ route('admin.inbox.comment', $conversation->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <textarea name="body" class="form-control" rows="4" placeholder="Skriv intern kommentar (kun synlig for teamet)..." required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Nevn:</label>
                                @foreach($teamMembers as $member)
                                    <label style="margin-right: 10px; font-weight: normal;">
                                        <input type="checkbox" name="mentioned_user_ids[]" value="{{ $member->id }}">
                                        {{ $member->first_name }}
                                    </label>
                                @endforeach
                            </div>
                            <button type="submit" class="btn btn-info"><i class="fa fa-comment"></i> Legg til kommentar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Sidebar --}}
        <div class="col-md-4">
            {{-- Conversation Info --}}
            <div class="sidebar-card">
                <h5>Samtaleinformasjon</h5>
                <table class="table table-condensed" style="margin-bottom: 0;">
                    <tr><td><strong>Kunde</strong></td><td>{{ $conversation->customer_name ?? 'Ukjent' }}</td></tr>
                    <tr><td><strong>E-post</strong></td><td>{{ $conversation->customer_email }}</td></tr>
                    <tr><td><strong>Inbox</strong></td><td>{{ $conversation->inbox ?? '-' }}</td></tr>
                    <tr><td><strong>Status</strong></td><td>
                        <span class="label label-{{ $conversation->status === 'open' ? 'warning' : ($conversation->status === 'closed' ? 'success' : 'default') }}">
                            {{ ucfirst($conversation->status) }}
                        </span>
                    </td></tr>
                    <tr><td><strong>Prioritet</strong></td><td>{{ ucfirst($conversation->priority) }}</td></tr>
                    <tr><td><strong>Opprettet</strong></td><td>{{ $conversation->created_at->format('d.m.Y H:i') }}</td></tr>
                </table>
            </div>

            {{-- Assignment --}}
            <div class="sidebar-card">
                <h5>Tildeling</h5>
                <form action="{{ route('admin.inbox.assign', $conversation->id) }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <select name="assigned_to" class="form-control input-sm">
                            <option value="">Velg person...</option>
                            @foreach($teamMembers as $member)
                                <option value="{{ $member->id }}" {{ $conversation->assigned_to == $member->id ? 'selected' : '' }}>
                                    {{ $member->first_name }} {{ $member->last_name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-sm btn-default">Tildel</button>
                        </span>
                    </div>
                </form>
            </div>

            {{-- Student Info --}}
            @if(!empty($studentContext))
                <div class="sidebar-card">
                    <h5><i class="fa fa-graduation-cap"></i> Elevinfo</h5>
                    <table class="student-info">
                        @foreach($studentContext as $key => $value)
                            <tr><td><strong>{{ $key }}</strong></td><td>{{ $value }}</td></tr>
                        @endforeach
                    </table>
                </div>
            @endif

            {{-- Quick Status --}}
            <div class="sidebar-card">
                <h5>Status</h5>
                <form action="{{ route('admin.inbox.status', $conversation->id) }}" method="POST">
                    @csrf
                    <div class="btn-group btn-group-sm btn-group-justified">
                        <div class="btn-group"><button type="submit" name="status" value="open" class="btn btn-{{ $conversation->status === 'open' ? 'warning' : 'default' }}">Åpen</button></div>
                        <div class="btn-group"><button type="submit" name="status" value="pending" class="btn btn-{{ $conversation->status === 'pending' ? 'info' : 'default' }}">Venter</button></div>
                        <div class="btn-group"><button type="submit" name="status" value="closed" class="btn btn-{{ $conversation->status === 'closed' ? 'success' : 'default' }}">Lukket</button></div>
                    </div>
                </form>
            </div>

            {{-- Canned Responses --}}
            @if($cannedResponses->count() > 0)
                <div class="sidebar-card">
                    <h5><i class="fa fa-bolt"></i> Hurtigsvar</h5>
                    @foreach($cannedResponses as $cr)
                        <button class="btn btn-xs btn-default action-btn" onclick="document.getElementById('reply-body').value = '{{ addslashes($cr->body) }}';">
                            {{ $cr->title }}
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-scroll to bottom of thread
        var thread = document.getElementById('message-thread');
        if (thread) thread.scrollTop = thread.scrollHeight;

        // AI draft button
        var aiBtn = document.getElementById('btn-ai-draft');
        if (aiBtn) {
            aiBtn.addEventListener('click', function() {
                aiBtn.disabled = true;
                aiBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Genererer...';
                document.getElementById('ai-draft-form').submit();
            });
        }
    });
</script>
@stop
