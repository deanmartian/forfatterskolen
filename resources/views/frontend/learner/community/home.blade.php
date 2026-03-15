@extends('frontend.layouts.course-portal')

@section('title')
<title>Skrivefellesskap › Forfatterskolen</title>
@stop

@section('styles')
<link rel="stylesheet" href="{{ asset('css/community.css?v=' . time()) }}">
@stop

@section('content')
<div class="learner-container community-wrapper">
    <div class="container">
        @include('frontend.learner.community._nav')
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <p style="margin: 0;">{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <div class="row">
            <div class="col-md-8">
                <h1 class="community-title">Velkommen tilbake, {{ Auth::user()->first_name }} <span class="wave">👋</span></h1>
                <p class="community-subtitle">Del tanker, tekst og inspirasjon med fellesskapet.</p>

                {{-- Create post --}}
                <div class="card community-card mb-4">
                    <div class="card-body">
                        <form action="{{ route('learner.community.storePost') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="d-flex" style="gap: 12px;">
                                <div class="avatar-circle">
                                    {{ strtoupper(substr($profile->name ?? '', 0, 1)) }}{{ strtoupper(substr(explode(' ', $profile->name ?? '')[1] ?? '', 0, 1)) }}
                                </div>
                                <div style="flex: 1;">
                                    <textarea name="content" id="post-textarea" class="form-control community-textarea" rows="3" placeholder="Hva tenker du på?" required></textarea>
                                    <div class="post-form-toolbar">
                                        <label for="post-image-input" class="btn-action" style="cursor: pointer; margin: 0;" title="Legg til bilde">
                                            <i class="fa fa-camera"></i> Bilde
                                        </label>
                                        <input type="file" name="image" id="post-image-input" accept="image/jpeg,image/png,image/gif,image/webp" style="display: none;">
                                        <span id="post-image-name" style="font-size: 12px; color: #888;"></span>
                                        <div class="emoji-picker-wrapper" data-target="post-textarea">
                                            <button type="button" class="emoji-toggle-btn btn-action" title="Emoji"><i class="fa fa-smile-o"></i></button>
                                            <div class="emoji-popup"><emoji-picker></emoji-picker></div>
                                        </div>
                                        <div style="margin-left: auto;">
                                            <button type="submit" class="btn community-btn-primary">Publiser</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Posts feed --}}
                @forelse($posts as $post)
                    @php
                        $isBotPost = $post->is_bot_post ?? false;
                        $postProfile = $post->user->profile ?? null;
                        $postName = $isBotPost ? 'Forfatterskolen' : ($postProfile ? ucwords($postProfile->name) : 'Ukjent');
                        $postInitials = $isBotPost ? 'FS' : collect(explode(' ', $postName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
                        $liked = $post->reactions->where('user_id', Auth::id())->count() > 0;
                    @endphp
                    <div class="card community-card mb-3">
                        <div class="card-body">
                            @if($post->pinned)
                                <div class="pinned-label"><i class="fa fa-thumb-tack"></i> Festet innlegg</div>
                            @endif

                            <div class="d-flex" style="gap: 12px;">
                                <div class="avatar-circle {{ $isBotPost ? 'avatar-bot' : '' }}">{{ $postInitials }}</div>
                                <div style="flex: 1;">
                                    <div class="post-header">
                                        <strong>{{ $postName }}</strong>
                                        @if($isBotPost)
                                            <span class="user-badge bot-badge">Offisiell</span>
                                        @elseif($postProfile && $postProfile->badge)
                                            <span class="user-badge">{{ $postProfile->badge }}</span>
                                        @endif
                                        <span class="text-muted post-time">{{ \Carbon\Carbon::parse($post->created_at)->diffForHumans() }}</span>
                                    </div>

                                    <p class="post-content">{{ $post->content }}</p>

                                    @if($post->image_url)
                                        <img src="{{ $post->image_url }}" alt="Bilde" class="post-image">
                                    @endif

                                    <div class="post-actions">
                                        <form action="{{ route('learner.community.toggleLike', $post->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn-action {{ $liked ? 'liked' : '' }}">
                                                <i class="fa fa-heart{{ $liked ? '' : '-o' }}"></i> {{ $post->reactions->count() }}
                                            </button>
                                        </form>

                                        <button class="btn-action toggle-comments" data-target="comments-{{ $post->id }}">
                                            <i class="fa fa-comment-o"></i> {{ $post->comments->count() }}
                                        </button>

                                        @if($post->user_id === Auth::id() || ($profile && $profile->badge === 'admin'))
                                            <form action="{{ route('learner.community.deletePost', $post->id) }}" method="POST" class="d-inline float-right" onsubmit="return confirm('Sikker på at du vil slette?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action text-danger"><i class="fa fa-trash-o"></i></button>
                                            </form>
                                        @endif
                                    </div>

                                    {{-- Comments section --}}
                                    <div class="comments-section" id="comments-{{ $post->id }}" style="display: none;">
                                        @foreach($post->comments as $comment)
                                            @php
                                                $cProfile = $comment->user->profile ?? null;
                                                $cName = $cProfile ? ucwords($cProfile->name) : 'Ukjent';
                                                $cInitials = collect(explode(' ', $cName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
                                            @endphp
                                            <div class="comment-item">
                                                <div class="avatar-circle avatar-sm">{{ $cInitials }}</div>
                                                <div>
                                                    <strong class="comment-author">{{ $cName }}</strong>
                                                    <span class="text-muted comment-time">{{ \Carbon\Carbon::parse($comment->created_at)->diffForHumans() }}</span>
                                                    <p class="comment-text">{{ $comment->content }}</p>
                                                </div>
                                            </div>
                                        @endforeach

                                        <form action="{{ route('learner.community.storeComment', $post->id) }}" method="POST" class="comment-form">
                                            @csrf
                                            <input type="text" name="content" class="form-control" placeholder="Skriv en kommentar..." required>
                                            <button type="submit" class="btn community-btn-primary btn-sm">Send</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card community-card">
                        <div class="card-body text-center py-5">
                            <p class="text-muted">Ingen innlegg ennå. Vær den første til å dele noe!</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="col-md-4">
                {{-- Active members widget --}}
                <div class="card community-card mb-3">
                    <div class="card-body">
                        <h4 class="widget-title"><i class="fa fa-users"></i> Aktive medlemmer</h4>
                        @php
                            $activeMembers = \App\Models\Profile::with('user')->take(5)->get();
                        @endphp
                        @foreach($activeMembers as $member)
                            <div class="member-item">
                                <div class="avatar-circle avatar-sm">
                                    {{ collect(explode(' ', ucwords($member->name)))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('') }}
                                </div>
                                <div>
                                    <strong class="member-name">{{ ucwords($member->name) }}</strong>
                                    <span class="text-muted member-role">{{ is_array($member->genres) ? implode(', ', array_slice($member->genres, 0, 2)) : 'Forfatter' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Writing tip widget --}}
                <div class="card community-card community-card-accent">
                    <div class="card-body">
                        <h4 class="widget-title"><i class="fa fa-lightbulb-o"></i> Dagens skrivetips</h4>
                        <p class="writing-tip"><em>«Den beste måten å begynne på, er å slutte å snakke og begynne å gjøre.»</em></p>
                        <p class="tip-author">— Walt Disney</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
<script>
document.querySelectorAll('.toggle-comments').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var target = document.getElementById(this.getAttribute('data-target'));
        if (target) {
            target.style.display = target.style.display === 'none' ? 'block' : 'none';
        }
    });
});

// Image filename display
var imgInput = document.getElementById('post-image-input');
if (imgInput) {
    imgInput.addEventListener('change', function() {
        var nameSpan = document.getElementById('post-image-name');
        nameSpan.textContent = this.files.length > 0 ? this.files[0].name : '';
    });
}
</script>
@include('frontend.learner.community._emoji')
@stop
