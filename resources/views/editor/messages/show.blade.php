@extends('editor.layout')

@section('page_title'){{ $conversation->subject }} &rsaquo; Meldinger@endsection

@section('page-title', 'Meldinger')

@section('styles')
<style>
    .msg-thread { max-height: 600px; overflow-y: auto; padding: 20px; }
    .msg-bubble { margin-bottom: 16px; display: flex; gap: 12px; }
    .msg-bubble--mine { flex-direction: row-reverse; }
    .msg-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        background: var(--wine, #862736); color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 600; flex-shrink: 0;
    }
    .msg-body {
        max-width: 70%; padding: 12px 16px;
        border-radius: 12px; font-size: 14px; line-height: 1.5;
    }
    .msg-bubble:not(.msg-bubble--mine) .msg-body {
        background: #f0ede8; color: #2c2c2c;
        border-top-left-radius: 4px;
    }
    .msg-bubble--mine .msg-body {
        background: var(--wine, #862736); color: #fff;
        border-top-right-radius: 4px;
    }
    .msg-meta { font-size: 11px; color: var(--ink-soft, #999); margin-top: 4px; }
    .msg-bubble--mine .msg-meta { text-align: right; }
    .msg-reply { padding: 20px; border-top: 1px solid var(--border, #e4e1dc); }
</style>
@stop

@section('content')
<div class="ed-section">
    <div class="ed-section__header">
        <h2 class="ed-section__title">
            <a href="{{ route('editor.messages.index') }}" style="color: var(--ink-soft); text-decoration: none; margin-right: 8px;">
                <i class="fa fa-arrow-left"></i>
            </a>
            {{ $conversation->subject }}
            @if($conversation->is_broadcast)
                <span class="label label-warning" style="margin-left: 8px; vertical-align: middle;">Broadcast</span>
            @endif
        </h2>
    </div>

    <div class="panel panel-default">
        <div class="msg-thread">
            @foreach($conversation->messages as $message)
                @php $isMine = $message->user_id == Auth::id(); @endphp
                <div class="msg-bubble {{ $isMine ? 'msg-bubble--mine' : '' }}">
                    <div class="msg-avatar">
                        {{ strtoupper(substr($message->sender->first_name ?? '?', 0, 1)) }}{{ strtoupper(substr($message->sender->last_name ?? '', 0, 1)) }}
                    </div>
                    <div>
                        <div class="msg-body">{!! nl2br(e($message->body)) !!}</div>
                        <div class="msg-meta">
                            {{ $message->sender->full_name }} &middot; {{ $message->created_at->format('d.m.Y H:i') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="msg-reply">
            <form method="POST" action="{{ route('editor.messages.reply', $conversation->id) }}">
                {{ csrf_field() }}
                <div class="form-group">
                    <textarea name="body" class="form-control" rows="3" placeholder="Skriv et svar..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-paper-plane"></i> Send
                </button>
            </form>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script>
    // Auto-scroll to bottom of thread
    var thread = document.querySelector('.msg-thread');
    if (thread) { thread.scrollTop = thread.scrollHeight; }
</script>
@stop
