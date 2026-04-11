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
            <input type="hidden" name="return_url" value="{{ session('inbox_return_url', route('admin.inbox.index', ['assigned_to' => auth()->id()])) }}">
            @if($conversation->status !== 'closed')
                <input type="hidden" name="status" value="closed">
                <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-check"></i> Lukk</button>
            @else
                <input type="hidden" name="status" value="open">
                <button type="submit" class="btn btn-sm btn-warning"><i class="fa fa-refresh"></i> Gjenåpne</button>
            @endif
        </form>

        @php
            $isMentioned = $conversation->comments()->where(function ($q) {
                $uid = auth()->id();
                $q->whereJsonContains('mentioned_user_ids', $uid)
                  ->orWhereJsonContains('mentioned_user_ids', (string) $uid);
            })->exists();
        @endphp
        @if($isMentioned)
        <form action="{{ route('admin.inbox.dismiss-mention', $conversation->id) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-sm btn-info"><i class="fa fa-check-circle"></i> Bekreft nevning</button>
        </form>
        @endif

        @if($conversation->private_to_user_id === auth()->id())
            <form action="{{ route('admin.inbox.make-public', $conversation->id) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-sm btn-default"
                        onclick="return confirm('Gjøre denne samtalen offentlig? Alle admins vil kunne se den etterpå.');"
                        title="Gjør offentlig — alle admins ser den etterpå">
                    <i class="fa fa-users"></i> Del med teamet
                </button>
            </form>
        @elseif(!$conversation->private_to_user_id)
            <form action="{{ route('admin.inbox.make-private', $conversation->id) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-sm btn-default"
                        onclick="return confirm('Gjøre denne samtalen privat? Bare du vil kunne se den etterpå.');"
                        title="Gjør privat — kun du ser den etterpå">
                    <i class="fa fa-lock"></i> Gjør privat
                </button>
            </form>
        @endif

        <form action="{{ route('admin.inbox.spam', $conversation->id) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Markere som spam?')"><i class="fa fa-ban"></i></button>
        </form>
    </div>
</div>

@if($conversation->private_to_user_id)
    <div style="background:#fef3c7; border-left:3px solid #f59e0b; color:#92400e; padding:8px 14px; margin: 0 15px 10px; border-radius:4px; font-size:12px;">
        <i class="fa fa-lock"></i>
        <strong>Privat samtale</strong> — bare {{ $conversation->privateToUser?->first_name ?? 'eieren' }} (og evt. tildelt redaktør) ser denne. Klikk "Del med teamet" for å gjøre den offentlig.
    </div>
@endif

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
                            @if($entry['item']->attachments)
                                <div style="margin-top:10px;padding-top:10px;border-top:1px solid rgba(0,0,0,0.1);">
                                    <small style="color:#888;"><i class="fa fa-paperclip"></i> Vedlegg:</small>
                                    @foreach($entry['item']->attachments as $att)
                                        <div style="margin-top:4px;">
                                            <a href="{{ route('admin.inbox.attachment', basename($att['path'])) }}" target="_blank" style="font-size:13px;">
                                                <i class="fa fa-file-o"></i> {{ $att['filename'] }}
                                                @if(isset($att['size']))
                                                    <span style="color:#888;">({{ number_format($att['size'] / 1024, 0) }} KB)</span>
                                                @endif
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
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
                            {!! \App\Helpers\InboxBodyFormatter::toHtml($entry['item']->body ?? $entry['item']->body_plain ?? '') !!}
                        </div>
                    @elseif($entry['type'] === 'ai_draft')
                        @php
                            $aiDraftRaw = $entry['item']->body ?? $entry['item']->body_plain ?? '';
                        @endphp
                        <div class="msg-bubble msg-ai-draft">
                            <div class="msg-meta">
                                <strong><i class="fa fa-magic"></i> AI Draft</strong>
                                <span class="label label-warning">Utkast - ikke sendt</span>
                                <span class="pull-right">{{ $entry['at']->format('d.m.Y H:i') }}</span>
                            </div>
                            <div id="ai-draft-text" data-raw="{{ $aiDraftRaw }}">{!! \App\Helpers\InboxBodyFormatter::toHtml($aiDraftRaw) !!}</div>
                            <div style="margin-top: 10px; display:flex; gap:6px; flex-wrap:wrap;">
                                <button class="btn btn-xs btn-success" onclick="
                                    if (!confirm('Sende AI-utkastet som det er og lukke samtalen?')) return;
                                    document.getElementById('reply-body').value = document.getElementById('ai-draft-text').dataset.raw;
                                    var f = document.getElementById('reply-form');
                                    var hidden = document.createElement('input');
                                    hidden.type = 'hidden';
                                    hidden.name = 'send_and_close';
                                    hidden.value = '1';
                                    f.appendChild(hidden);
                                    f.submit();
                                ">
                                    <i class="fa fa-paper-plane"></i> Send direkte og lukk
                                </button>
                                <button class="btn btn-xs btn-info" onclick="
                                    document.getElementById('reply-body').value = document.getElementById('ai-draft-text').dataset.raw;
                                    document.getElementById('reply-body').focus();
                                    document.getElementById('reply-body').scrollIntoView({behavior:'smooth', block:'center'});
                                ">
                                    <i class="fa fa-pencil"></i> Bruk som svar (rediger)
                                </button>
                            </div>
                            <div style="margin-top:6px; font-size:11px; color:#888;">
                                <i class="fa fa-info-circle"></i> Tips: i tekstboksen blir <code>[tekst](url)</code> til en klikkbar lenke i e-posten.
                            </div>

                            @include('backend.inbox.partials.ai-tool-actions', [
                                'aiMessage' => $entry['item'],
                                'conversation' => $conversation,
                            ])
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
                        <form id="reply-form" action="{{ route('admin.inbox.reply', $conversation->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label>Til: <strong>{{ $conversation->customer_email }}</strong></label>
                            </div>
                            <div class="form-group">
                                <textarea name="body" id="reply-body" class="form-control" rows="6" placeholder="Skriv ditt svar her... (lim inn bilder med Ctrl+V eller dra dem hit — de havner inline i e-posten)" required></textarea>
                                <input type="hidden" name="sender_name" value="{{ Auth::user()->full_name }}">
                                <div style="margin-top:8px;">
                                    <label style="cursor:pointer;font-size:13px;color:#666;"><i class="fa fa-paperclip"></i> Vedlegg (PDF, Word osv.)
                                        <input type="file" id="reply-attachments" name="attachments[]" multiple style="margin-left:6px;font-size:12px;">
                                    </label>
                                    <div id="pasted-images-list" style="margin-top:6px;font-size:12px;"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane"></i> Send svar</button>
                                <button type="submit" name="send_and_close" value="1" class="btn btn-success"><i class="fa fa-check"></i> Send og lukk</button>
                                <button type="submit" name="save_as_draft" value="1" class="btn btn-default"><i class="fa fa-save"></i> Lagre utkast</button>
                                <button type="button" class="btn btn-warning" id="btn-ai-draft"><i class="fa fa-magic"></i> Generer AI-utkast</button>
                                <button type="button" class="btn btn-default btn-sm" id="btn-polish-reply" onclick="polishReply()" title="AI polerer teksten din til en varmere tone — uten å endre innholdet"><i class="fa fa-paint-brush"></i> Forbedr svar</button>
                                <button type="button" class="btn btn-info btn-sm" onclick="toggleInboxPreview()"><i class="fa fa-eye"></i> Forhåndsvisning</button>
                            </div>
                            <div id="inboxEmailPreview" style="display:none;border:1px solid #ddd;border-radius:8px;overflow:hidden;margin-top:10px;">
                                <div style="background:#f8f8f8;padding:16px;text-align:center;border-bottom:1px solid #eee;">
                                    <img src="{{ asset('photos/logos/fs-logo.png') }}" alt="Forfatterskolen" style="height:36px;">
                                </div>
                                <div id="inboxPreviewContent" style="padding:24px;font-family:-apple-system,sans-serif;font-size:14px;line-height:1.7;color:#333;">
                                </div>
                                <div style="padding:16px;background:#f8f8f8;text-align:center;font-size:11px;color:#999;border-top:1px solid #eee;">
                                    Spørsmål? Svar på denne e-posten eller ring 411 23 555<br>
                                    Forfatterskolen · Lihagen 21, 3029 Drammen
                                </div>
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
                    <tr><td><strong>Kunde</strong></td><td>
                        @php $inboxUser = App\User::where('email', $conversation->customer_email)->first(); @endphp
                        @if($inboxUser)
                            <a href="{{ route('admin.learner.show', $inboxUser->id) }}" style="color:#862736;font-weight:600;">
                                {{ $conversation->customer_name ?? 'Ukjent' }}
                            </a>
                        @else
                            {{ $conversation->customer_name ?? 'Ukjent' }}
                        @endif
                    </td></tr>
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

            {{-- Oppfølging --}}
            <div class="sidebar-card">
                <h5><i class="fa fa-bell"></i> Oppfølging</h5>
                @if($conversation->follow_up_at)
                    <div style="background:#fff3e0;padding:8px 12px;border-radius:6px;margin-bottom:8px;">
                        <i class="fa fa-clock-o" style="color:#e65100;"></i>
                        <strong style="color:#e65100;">{{ \Carbon\Carbon::parse($conversation->follow_up_at)->format('d.m.Y H:i') }}</strong>
                        <form action="{{ route('admin.inbox.follow-up', $conversation->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <input type="hidden" name="follow_up_at" value="">
                            <button type="submit" style="background:none;border:none;color:#c62828;cursor:pointer;font-size:12px;margin-left:6px;"><i class="fa fa-times"></i> Fjern</button>
                        </form>
                    </div>
                @endif
                <form action="{{ route('admin.inbox.follow-up', $conversation->id) }}" method="POST">
                    @csrf
                    <div style="display:flex;gap:6px;">
                        <input type="datetime-local" name="follow_up_at" class="form-control input-sm" required min="{{ now()->format('Y-m-d\TH:i') }}">
                        <button type="submit" class="btn btn-sm btn-warning"><i class="fa fa-bell"></i></button>
                    </div>
                    <div style="margin-top:6px;display:flex;gap:4px;flex-wrap:wrap;">
                        <button type="button" class="btn btn-xs btn-default" onclick="setFollowUp(1)">I morgen</button>
                        <button type="button" class="btn btn-xs btn-default" onclick="setFollowUp(3)">3 dager</button>
                        <button type="button" class="btn btn-xs btn-default" onclick="setFollowUp(7)">1 uke</button>
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

        // ═══════════════════════════════════════════════════════════
        // Paste + drag-and-drop for bilder i svar-feltet. Bildet lastes
        // opp til serveren med en gang og markdown-referansen
        // ![bilde](https://...) settes inn ved markøren i tekstfeltet.
        // Når meldingen sendes, konverterer InboxBodyFormatter markdown
        // til <img>-tag i HTML-e-posten så mottakeren ser bildet inline.
        // ═══════════════════════════════════════════════════════════
        function wireImagePaste(textareaId, listId) {
            var textarea = document.getElementById(textareaId);
            var list = document.getElementById(listId);
            if (!textarea) return;

            var csrfToken = document.querySelector('meta[name="csrf-token"]');
            csrfToken = csrfToken ? csrfToken.getAttribute('content') : '';

            function showStatus(message, type) {
                if (!list) return;
                var row = document.createElement('div');
                var bg = type === 'error' ? '#fef2f2' : (type === 'loading' ? '#fffbeb' : '#fef5f6');
                var border = type === 'error' ? '#fecaca' : (type === 'loading' ? '#fde68a' : '#f0d6da');
                var color = type === 'error' ? '#991b1b' : (type === 'loading' ? '#78350f' : '#862736');
                row.style.cssText = 'display:inline-flex;align-items:center;gap:6px;background:' + bg + ';border:1px solid ' + border + ';border-radius:6px;padding:4px 10px;margin-right:6px;margin-top:4px;color:' + color + ';font-size:12px;';
                row.innerHTML = message;
                list.appendChild(row);
                return row;
            }

            function insertAtCursor(text) {
                var start = textarea.selectionStart;
                var end = textarea.selectionEnd;
                var before = textarea.value.substring(0, start);
                var after = textarea.value.substring(end);
                // Sørg for linjeskift før og etter bildet for ren formatering
                var prefix = (before.length && !before.endsWith('\n')) ? '\n' : '';
                var suffix = '\n';
                var newContent = prefix + text + suffix;
                textarea.value = before + newContent + after;
                var newPos = start + newContent.length;
                textarea.selectionStart = textarea.selectionEnd = newPos;
                textarea.focus();
            }

            function uploadImage(file) {
                var loadingRow = showStatus('<i class="fa fa-spinner fa-spin"></i> Laster opp ' + (file.name || 'bilde') + '...', 'loading');

                var fd = new FormData();
                fd.append('image', file, file.name || 'limt-inn.png');
                fd.append('_token', csrfToken);

                fetch('{{ route('admin.inbox.paste-image') }}', {
                    method: 'POST',
                    body: fd,
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    credentials: 'same-origin'
                })
                .then(function(r) {
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.json();
                })
                .then(function(data) {
                    if (!data.url) throw new Error('Mangler URL i svaret');
                    var alt = (file.name || 'bilde').replace(/\.[^.]+$/, '');
                    insertAtCursor('![' + alt + '](' + data.url + ')');
                    if (loadingRow) loadingRow.remove();
                    showStatus('<i class="fa fa-check"></i> <strong>' + data.filename + '</strong> limt inn', 'success');
                })
                .catch(function(err) {
                    if (loadingRow) loadingRow.remove();
                    showStatus('<i class="fa fa-exclamation-triangle"></i> Opplasting feilet: ' + err.message, 'error');
                });
            }

            // Paste-handler
            textarea.addEventListener('paste', function(e) {
                var items = (e.clipboardData || e.originalEvent.clipboardData || {}).items;
                if (!items) return;
                for (var i = 0; i < items.length; i++) {
                    var item = items[i];
                    if (item.kind === 'file' && item.type.indexOf('image/') === 0) {
                        e.preventDefault();
                        var blob = item.getAsFile();
                        if (!blob) continue;
                        var ext = (blob.type.split('/')[1] || 'png').split('+')[0];
                        var filename = 'limt-inn-' + Date.now() + '.' + ext;
                        var file = new File([blob], filename, { type: blob.type });
                        uploadImage(file);
                    }
                }
            });

            // Drag-and-drop-handler (bare for bilder — andre filer ignoreres)
            textarea.addEventListener('dragover', function(e) {
                e.preventDefault();
                textarea.style.background = '#fef5f6';
            });
            textarea.addEventListener('dragleave', function(e) {
                textarea.style.background = '';
            });
            textarea.addEventListener('drop', function(e) {
                e.preventDefault();
                textarea.style.background = '';
                var files = e.dataTransfer.files;
                for (var i = 0; i < files.length; i++) {
                    if (files[i].type.indexOf('image/') === 0) {
                        uploadImage(files[i]);
                    }
                }
            });
        }

        wireImagePaste('reply-body', 'pasted-images-list');
    });

    // Follow-up quick buttons
    function setFollowUp(days) {
        var d = new Date();
        d.setDate(d.getDate() + days);
        d.setHours(9, 0, 0);
        var input = document.querySelector('input[name="follow_up_at"]');
        input.value = d.toISOString().slice(0, 16);
    }

    // "Forbedr svar" — send teksten til AI for polering
    function polishReply() {
        var textarea = document.getElementById('reply-body');
        var body = textarea.value.trim();
        if (!body || body.length < 5) {
            alert('Skriv et svar først, så polerer AI det for deg.');
            return;
        }

        var btn = document.getElementById('btn-polish-reply');
        var originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fa fa-spinner fa-pulse"></i> Polerer...';
        btn.disabled = true;

        fetch('{{ route("admin.inbox.polish-reply", $conversation->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ body: body }),
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.polished) {
                textarea.value = data.polished;
                textarea.style.borderColor = '#2e7d32';
                setTimeout(function() { textarea.style.borderColor = ''; }, 3000);
            } else {
                alert(data.error || 'Noe gikk galt med AI-poleringen.');
            }
        })
        .catch(function(err) {
            alert('Feil: ' + err.message);
        })
        .finally(function() {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    }

    // Email preview (global scope) — matches InboxBodyFormatter::toHtml logic
    function toggleInboxPreview() {
        var preview = document.getElementById('inboxEmailPreview');
        var content = document.getElementById('inboxPreviewContent');
        if (preview.style.display === 'none') {
            var text = document.getElementById('reply-body').value;
            if (!text) { alert('Skriv en melding først'); return; }

            // Extract markdown images BEFORE escape så de ikke blir HTML-escaped
            var imagePlaceholders = {};
            var imgCounter = 0;
            text = text.replace(/!\[([^\]]*)\]\((https?:\/\/[^\s\)]+\.(?:png|jpg|jpeg|gif|webp|svg))\)/gi, function(m, alt, url) {
                var key = '\u0000IMG_' + (imgCounter++) + '\u0000';
                imagePlaceholders[key] = '<img src="' + url.replace(/"/g, '&quot;') + '" alt="' + (alt || 'bilde').replace(/"/g, '&quot;') + '" style="max-width:100%;height:auto;border-radius:8px;margin:8px 0;display:block;">';
                return key;
            });

            // Extract markdown links FØR escape
            var linkPlaceholders = {};
            var linkCounter = 0;
            text = text.replace(/\[([^\]]+)\]\((https?:\/\/[^\s\)]+)\)/g, function(m, linkText, url) {
                var key = '\u0000LINK_' + (linkCounter++) + '\u0000';
                linkPlaceholders[key] = '<a href="' + url.replace(/"/g, '&quot;') + '" target="_blank">' + linkText.replace(/</g, '&lt;') + '</a>';
                return key;
            });

            // HTML-escape the rest
            var html = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

            // Restore images + links
            for (var k in imagePlaceholders) html = html.split(k).join(imagePlaceholders[k]);
            for (var k in linkPlaceholders) html = html.split(k).join(linkPlaceholders[k]);

            // Newlines to <br>
            html = html.replace(/\n/g, '<br>');
            // Remove <br>-er rett ved siden av <img> så vi ikke får dobbel luft
            html = html.replace(/(<br\s*\/?>\s*)+(<img)/gi, '$2').replace(/(<img[^>]*>)(\s*<br\s*\/?>)+/gi, '$1');

            content.innerHTML = html;
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    }
</script>
@stop
