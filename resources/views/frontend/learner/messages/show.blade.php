@extends('frontend.layouts.course-portal')

@section('page_title', $conversation->subject . ' &rsaquo; Meldinger')
@section('robots', '<meta name="robots" content="noindex, follow">')

@section('heading') Meldinger @stop

@section('styles')
<style>
    .msg-thread { max-height: 600px; overflow-y: auto; padding: 20px; }
    .msg-bubble { margin-bottom: 16px; display: flex; gap: 12px; }
    .msg-bubble--mine { flex-direction: row-reverse; }
    .msg-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        background: #862736; color: #fff;
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
        background: #862736; color: #fff;
        border-top-right-radius: 4px;
    }
    .msg-meta { font-size: 11px; color: #999; margin-top: 4px; }
    .msg-bubble--mine .msg-meta { text-align: right; }
    .msg-reply { padding: 20px; border-top: 1px solid #e4e1dc; }
</style>
@stop

@section('content')
<div class="learner-container">
    <div class="container">
        <div class="row">
            @include('frontend.partials.learner-search-new')
        </div>

        <div class="row mt-4">
            <div class="col-sm-12">
                <div class="mb-3">
                    <a href="{{ route('learner.messages.index') }}" style="color: #666; text-decoration: none;">
                        <i class="fa fa-arrow-left"></i> Tilbake til innboks
                    </a>
                </div>

                <h4>{{ $conversation->subject }}</h4>

                <div class="card global-card">
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
                        <form method="POST" action="{{ route('learner.messages.reply', $conversation->id) }}">
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
        </div>
    </div>
</div>
@stop

@section('scripts')
<script>
    var thread = document.querySelector('.msg-thread');
    if (thread) { thread.scrollTop = thread.scrollHeight; }
</script>
@stop
