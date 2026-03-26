@extends('frontend.layouts.course-portal')

@section('title')
<title>{{ $course->title }} › Kursgrupper › Forfatterskolen</title>
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
        <a href="{{ route('learner.community.courseGroups') }}" class="back-link">
            <i class="fa fa-arrow-left"></i> Tilbake til kursgrupper
        </a>

        {{-- Course header --}}
        <div class="community-card mb-4">
            <div class="card-body">
                <div class="d-flex" style="gap: 15px; align-items: flex-start;">
                    <div class="manuscript-icon">
                        <i class="fa fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h2 class="discussion-title" style="font-size: 1.4em; font-family: var(--font-display);">{{ $course->title }}</h2>
                        @if($course->description)
                            <p class="post-content">{{ Str::limit(html_entity_decode(strip_tags($course->description)), 200) }}</p>
                        @endif
                        <div class="discussion-meta mt-2">
                            <span><i class="fa fa-users"></i> {{ $learnerCount }} elever</span>
                            @if($course->start_date)
                                <span>Oppstart: {{ \Carbon\Carbon::parse($course->start_date)->format('d.m.Y') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                {{-- Post form --}}
                <div class="community-card mb-3">
                    <div class="card-body">
                        <form action="{{ route('learner.community.storeCourseGroupPost', $course->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @php
                                $profile = Auth::user()->profile ?? null;
                                $initials = $profile ? collect(explode(' ', ucwords($profile->name)))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('') : '?';
                            @endphp
                            <div class="d-flex" style="gap: 10px;">
                                <div class="avatar-circle">{{ $initials }}</div>
                                <div style="flex: 1;">
                                    <textarea name="content" id="cg-post-textarea" class="form-control community-textarea" rows="3" placeholder="Del noe med gruppen..." required></textarea>
                                    <div class="post-form-toolbar">
                                        <label for="cg-image-input" class="composer-action" style="cursor: pointer; margin: 0;">
                                            <i class="fa fa-camera"></i> Bilde
                                        </label>
                                        <input type="file" name="image" id="cg-image-input" accept="image/jpeg,image/png,image/gif,image/webp" style="display: none;">
                                        <span id="cg-image-name" style="font-size: 12px; color: var(--text-light);"></span>
                                        <div class="emoji-picker-wrapper" data-bs-target="cg-post-textarea">
                                            <button type="button" class="emoji-toggle-btn btn-action" title="Emoji"><i class="fa fa-smile-o"></i></button>
                                            <div class="emoji-popup"><emoji-picker></emoji-picker></div>
                                        </div>
                                        <div style="margin-left: auto;">
                                            <button type="submit" class="community-btn-primary">Publiser</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Posts --}}
                @forelse($posts as $post)
                    @php
                        $isBotPost = $post->is_bot_post ?? false;
                        $pProfile = $post->user->profile ?? null;
                        $pName = $isBotPost ? 'Forfatterskolen' : ($pProfile ? ucwords($pProfile->name) : 'Ukjent');
                        $pInitials = $isBotPost ? 'FS' : collect(explode(' ', $pName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
                        $pColors = ['#2563eb', '#0d7a5f', '#7c3aed', '#b45309', '#862736'];
                        $pColor = $isBotPost ? '#862736' : $pColors[crc32($pName) % count($pColors)];
                    @endphp
                    <div class="community-card mb-3">
                        <div class="card-body">
                            <div class="d-flex" style="gap: 10px;">
                                <div class="avatar-circle avatar-sm {{ $isBotPost ? 'avatar-bot' : '' }}" style="background: {{ $pColor }};">{{ $pInitials }}</div>
                                <div style="flex: 1;">
                                    <div class="post-header">
                                        <strong>{{ $pName }}</strong>
                                        @if($isBotPost)
                                            <span class="user-badge badge-official">Offisiell</span>
                                        @elseif($pProfile && $pProfile->badge)
                                            <span class="user-badge">{{ str_replace('_', ' ', $pProfile->badge) }}</span>
                                        @endif
                                        <span class="post-time">{{ \Carbon\Carbon::parse($post->created_at)->diffForHumans() }}</span>
                                    </div>
                                    <p class="post-content">{{ $post->content }}</p>

                                    @if($post->image_url)
                                        <img src="{{ $post->image_url }}" alt="Bilde" class="post-image">
                                    @endif

                                    @if($post->comments->count() > 0)
                                        <div class="comments-section">
                                            @foreach($post->comments as $comment)
                                                @php
                                                    $cProfile = $comment->user->profile ?? null;
                                                    $cName = $cProfile ? ucwords($cProfile->name) : 'Ukjent';
                                                    $cInitials = collect(explode(' ', $cName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
                                                @endphp
                                                <div class="comment-item">
                                                    <div class="avatar-circle avatar-xs">{{ $cInitials }}</div>
                                                    <div>
                                                        <strong class="comment-author">{{ $cName }}</strong>
                                                        <span class="comment-time">{{ \Carbon\Carbon::parse($comment->created_at)->diffForHumans() }}</span>
                                                        <p class="comment-text">{{ $comment->content }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="community-card">
                        <div class="card-body text-center py-4">
                            <p style="color: var(--text-muted);">Ingen innlegg i denne gruppen ennå. Bli den første!</p>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Sidebar: Members --}}
            <div class="col-md-4">
                <div class="sidebar-widget">
                    <div class="widget-header">
                        <span class="widget-title"><i class="fa fa-users" style="font-size: 13px;"></i> Medlemmer ({{ $learnerCount }})</span>
                    </div>
                    @foreach($members->take(10) as $member)
                        @php
                            $mName = ucwords($member->name);
                            $mInitials = collect(explode(' ', $mName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
                            $mColors = ['#2563eb', '#0d7a5f', '#7c3aed', '#b45309', '#862736'];
                            $mColor = $mColors[crc32($mName) % count($mColors)];
                        @endphp
                        <div class="member-item">
                            <div class="avatar-circle avatar-sm" style="background: {{ $mColor }};">{{ $mInitials }}</div>
                            <div>
                                <span class="member-name">{{ $mName }}</span>
                            </div>
                        </div>
                    @endforeach
                    @if($members->count() > 10)
                        <div style="padding: 8px 16px;">
                            <p style="font-size: 12px; color: var(--text-light); margin: 0;">...og {{ $members->count() - 10 }} til</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
<script>
var cgImgInput = document.getElementById('cg-image-input');
if (cgImgInput) {
    cgImgInput.addEventListener('change', function() {
        document.getElementById('cg-image-name').textContent = this.files.length > 0 ? this.files[0].name : '';
    });
}
</script>
@include('frontend.learner.community._emoji')
@stop
