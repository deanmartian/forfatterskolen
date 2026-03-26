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
                {{-- Welcome header --}}
                <div style="margin-bottom: 22px;">
                    <h1 class="community-title">Velkommen tilbake, {{ Auth::user()->first_name }} <span class="wave">👋</span></h1>
                    <p class="community-subtitle" style="margin-bottom: 0;">Del tanker, tekst og inspirasjon med fellesskapet.</p>
                    <div class="activity-pills">
                        @php
                            $memberCount = \App\Models\Profile::count();
                            $todayPostCount = \App\Models\Post::whereDate('created_at', today())->count();
                        @endphp
                        <span class="apill"><span class="apill-dot"></span> {{ $memberCount }} medlemmer</span>
                        <span class="apill">
                            <i class="fa fa-comment-o" style="font-size: 11px;"></i>
                            {{ $todayPostCount }} nye innlegg i dag
                        </span>
                    </div>
                </div>

                {{-- Ukens skriveoppgave banner --}}
                <div class="challenge-banner">
                    <div class="challenge-icon">
                        <i class="fa fa-star"></i>
                    </div>
                    <div class="challenge-text">
                        <div class="title">Ukens skriveoppgave: Skriv en scene uten dialog</div>
                        <div class="sub">Del teksten din i Manusrom</div>
                    </div>
                    <a href="{{ route('learner.community.manuscripts') }}" class="btn-challenge">Delta nå</a>
                </div>

                {{-- Composer --}}
                <div class="community-card mb-4">
                    <div class="card-body" style="padding: 0;">
                        <form action="{{ route('learner.community.storePost') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="composer-top">
                                <div class="avatar-circle">
                                    {{ strtoupper(substr($profile->name ?? '', 0, 1)) }}{{ strtoupper(substr(explode(' ', $profile->name ?? '')[1] ?? '', 0, 1)) }}
                                </div>
                                <div style="flex: 1;">
                                    <textarea name="content" id="post-textarea" class="community-textarea" style="width: 100%; border: none; box-shadow: none; height: 68px; background: transparent;" placeholder="Hva tenker du på? Del en tanke, tekstutdrag eller spørsmål…" required></textarea>
                                </div>
                            </div>
                            <div class="composer-footer">
                                <label for="post-image-input" class="composer-action" style="cursor: pointer; margin: 0;">
                                    <i class="fa fa-camera"></i> Bilde
                                </label>
                                <input type="file" name="image" id="post-image-input" accept="image/jpeg,image/png,image/gif,image/webp" style="display: none;">
                                <span id="post-image-name" style="font-size: 12px; color: var(--text-light);"></span>
                                <div class="emoji-picker-wrapper" data-bs-target="post-textarea">
                                    <button type="button" class="emoji-toggle-btn" title="Emoji"><i class="fa fa-smile-o"></i></button>
                                    <div class="emoji-popup"><emoji-picker></emoji-picker></div>
                                </div>
                                <button type="submit" class="btn-publish">Publiser</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Feed tabs --}}
                <div class="feed-tabs">
                    <button class="ftab active">Alle</button>
                    <button class="ftab">Populært</button>
                    <button class="ftab">Tekstdeling</button>
                    <button class="ftab">Diskusjoner</button>
                    <button class="ftab">Fra skolen</button>
                </div>

                {{-- Posts feed --}}
                @forelse($posts as $post)
                    @php
                        $isBotPost = $post->is_bot_post ?? false;
                        $postProfile = $post->user->profile ?? null;
                        $postName = $isBotPost ? 'Forfatterskolen' : ($postProfile ? ucwords($postProfile->name) : 'Ukjent');
                        $postInitials = $isBotPost ? 'FS' : collect(explode(' ', $postName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
                        $liked = $post->reactions->where('user_id', Auth::id())->count() > 0;
                        $avatarColors = ['pa-red', 'pa-blue', 'pa-teal', 'pa-purple', 'pa-amber'];
                        $avatarColor = $isBotPost ? 'pa-red' : $avatarColors[crc32($postName) % count($avatarColors)];
                    @endphp
                    <div class="post-card">
                        <div style="padding: 14px 16px 0;">
                            @if($post->pinned)
                                <div class="pinned-label"><i class="fa fa-thumb-tack"></i> Festet innlegg</div>
                            @endif
                            <div class="d-flex" style="gap: 10px; align-items: center;">
                                <div class="avatar-circle {{ $isBotPost ? 'avatar-bot' : $avatarColor }}">{{ $postInitials }}</div>
                                <div style="flex: 1;">
                                    <div>
                                        <strong style="font-size: 13.5px;">{{ $postName }}</strong>
                                        @if($isBotPost)
                                            <span class="user-badge badge-official">Offisiell</span>
                                        @elseif($postProfile && $postProfile->badge)
                                            <span class="user-badge">{{ $postProfile->badge }}</span>
                                        @endif
                                    </div>
                                    <span class="post-time">{{ \Carbon\Carbon::parse($post->created_at)->diffForHumans() }}</span>
                                </div>
                                @if($post->user_id === Auth::id() || ($profile && $profile->badge === 'admin'))
                                    <form action="{{ route('learner.community.deletePost', $post->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Sikker på at du vil slette?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action text-danger"><i class="fa fa-trash-o"></i></button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <div style="padding: 10px 16px 12px;">
                            <p class="post-content" style="margin-top: 0;">{{ $post->content }}</p>

                            @if($post->image_url)
                                <div style="margin: 8px 0 0; border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--border-light);">
                                    <img src="{{ $post->image_url }}" alt="Bilde" style="width: 100%; display: block; max-height: 280px; object-fit: cover;">
                                </div>
                            @endif
                        </div>

                        <div class="post-actions" style="padding: 8px 10px; border-top: 1px solid var(--border-light); margin-top: 0;">
                            <form action="{{ route('learner.community.toggleLike', $post->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn-action {{ $liked ? 'liked' : '' }}">
                                    <i class="fa fa-heart{{ $liked ? '' : '-o' }}"></i> {{ $post->reactions->count() }}
                                </button>
                            </form>

                            <button class="btn-action toggle-comments" data-bs-target="comments-{{ $post->id }}">
                                <i class="fa fa-comment-o"></i> {{ $post->comments->count() }} kommentarer
                            </button>
                        </div>

                        {{-- Comments section --}}
                        <div class="comments-section" id="comments-{{ $post->id }}" style="display: none; padding: 0 16px 12px;">
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
                                        <span class="comment-time">{{ \Carbon\Carbon::parse($comment->created_at)->diffForHumans() }}</span>
                                        <p class="comment-text">{{ $comment->content }}</p>
                                    </div>
                                </div>
                            @endforeach

                            <form action="{{ route('learner.community.storeComment', $post->id) }}" method="POST" class="comment-form">
                                @csrf
                                <input type="text" name="content" class="form-control" placeholder="Skriv en kommentar..." required>
                                <button type="submit" class="community-btn-primary" style="padding: 6px 14px; font-size: 12px;">Send</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="community-card">
                        <div class="card-body text-center py-5">
                            <p style="color: var(--text-muted);">Ingen innlegg ennå. Vær den første til å dele noe!</p>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Right sidebar --}}
            <div class="col-md-4">
                {{-- Stats widget --}}
                <div class="sidebar-widget" style="margin-bottom: 16px;">
                    <div class="stats-row">
                        @php
                            $totalMembers = \App\Models\Profile::count();
                            $todayPosts = \App\Models\Post::whereDate('created_at', today())->count();
                        @endphp
                        <div class="stat-cell">
                            <div class="stat-num">{{ $totalMembers }}</div>
                            <div class="stat-lbl">Medlemmer</div>
                        </div>
                        <div class="stat-cell">
                            <div class="stat-num" style="color: #22c55e;">●</div>
                            <div class="stat-lbl">Aktive</div>
                        </div>
                        <div class="stat-cell">
                            <div class="stat-num">{{ $todayPosts }}</div>
                            <div class="stat-lbl">I dag</div>
                        </div>
                    </div>
                </div>

                {{-- Active members --}}
                <div class="sidebar-widget">
                    <div class="widget-header">
                        <span class="widget-title">
                            <i class="fa fa-users" style="font-size: 13px;"></i>
                            Aktive medlemmer
                        </span>
                        <a href="{{ route('learner.community.members') }}" class="widget-link">Se alle</a>
                    </div>
                    @php
                        $activeMembers = \App\Models\Profile::with('user')->take(5)->get();
                    @endphp
                    @foreach($activeMembers as $member)
                        @php
                            $mName = ucwords($member->name);
                            $mInitials = collect(explode(' ', $mName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
                            $mColors = ['#2563eb', '#0d7a5f', '#7c3aed', '#b45309', '#862736'];
                            $mColor = $mColors[crc32($mName) % count($mColors)];
                            $mGenre = is_array($member->genres) ? implode(', ', array_slice($member->genres, 0, 2)) : 'Forfatter';
                        @endphp
                        <div class="member-item">
                            <div class="member-avatar-wrap">
                                <div class="avatar-circle avatar-sm" style="background: {{ $mColor }};">{{ $mInitials }}</div>
                            </div>
                            <div>
                                <div class="member-name">{{ $mName }}</div>
                                <div class="member-role">{{ $mGenre }}</div>
                            </div>
                            @if($member->user_id !== Auth::id())
                                <button class="member-action" onclick="window.location='{{ route('learner.community.conversation', $member->user_id) }}'">Følg</button>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Trending topics --}}
                @php
                    $trendingDiscussions = \App\Models\Discussion::withCount('replies')
                        ->orderByDesc('replies_count')
                        ->take(4)
                        ->get();
                @endphp
                @if($trendingDiscussions->count() > 0)
                <div class="sidebar-widget">
                    <div class="widget-header">
                        <span class="widget-title">
                            <i class="fa fa-line-chart" style="font-size: 13px;"></i>
                            Populære temaer
                        </span>
                    </div>
                    @foreach($trendingDiscussions as $idx => $trend)
                        <div class="trend-item" onclick="window.location='{{ route('learner.community.discussion', $trend->id) }}'">
                            <span class="trend-num">{{ $idx + 1 }}</span>
                            <div>
                                <div class="trend-title">{{ $trend->title }}</div>
                                <div class="trend-count">{{ $trend->replies_count }} svar · {{ $trend->category ?? 'Diskusjon' }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif

                {{-- Writing tip --}}
                <div class="sidebar-widget">
                    <div class="widget-header">
                        <span class="widget-title">
                            <i class="fa fa-lightbulb-o" style="font-size: 14px; color: #f59e0b;"></i>
                            Dagens skrivetips
                        </span>
                    </div>
                    <div class="tip-body">
                        @php
                            $tips = [
                                ['quote' => 'Den beste måten å begynne på, er å slutte å snakke og begynne å gjøre.', 'source' => 'Walt Disney'],
                                ['quote' => 'Skriv det du vet. Det er det første du lærer som forfatter.', 'source' => 'Mark Twain'],
                                ['quote' => 'Det finnes ingen regler for god skriving. Det er opp til deg.', 'source' => 'Ernest Hemingway'],
                            ];
                            $tip = $tips[array_rand($tips)];
                        @endphp
                        <div class="tip-quote">{{ $tip['quote'] }}</div>
                        <div class="tip-source">— {{ $tip['source'] }}</div>
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
        var target = document.getElementById(this.getAttribute('data-bs-target'));
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
