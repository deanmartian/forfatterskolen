@extends('frontend.layouts.course-portal')

@section('title')
<title>{{ $course->title }} > Kursgrupper > Forfatterskolen</title>
@stop

@section('styles')
<link rel="stylesheet" href="{{ asset('css/community.css?v=' . time()) }}">
@stop

@section('content')
<div class="learner-container community-wrapper">
    <div class="container">
        <a href="{{ route('learner.community.courseGroups') }}" class="back-link">
            <i class="fa fa-arrow-left"></i> Tilbake til kursgrupper
        </a>

        {{-- Course header --}}
        <div class="card community-card mb-4">
            <div class="card-body">
                <div class="d-flex" style="gap: 15px; align-items: flex-start;">
                    <div class="manuscript-icon">
                        <i class="fa fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h2 class="discussion-title" style="font-size: 1.4em;">{{ $course->title }}</h2>
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
                <div class="card community-card mb-3">
                    <div class="card-body">
                        <form action="{{ route('learner.community.storeCourseGroupPost', $course->id) }}" method="POST">
                            @csrf
                            @php
                                $profile = Auth::user()->profile ?? null;
                                $initials = $profile ? collect(explode(' ', ucwords($profile->name)))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('') : '?';
                            @endphp
                            <div class="d-flex" style="gap: 10px;">
                                <div class="avatar-circle">{{ $initials }}</div>
                                <div style="flex: 1;">
                                    <textarea name="content" class="form-control community-textarea" rows="3" placeholder="Del noe med gruppen..." required></textarea>
                                    <div class="text-right mt-2">
                                        <button type="submit" class="btn community-btn-primary">Publiser</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Posts --}}
                @forelse($posts as $post)
                    @php
                        $pProfile = $post->user->profile ?? null;
                        $pName = $pProfile ? ucwords($pProfile->name) : 'Ukjent';
                        $pInitials = collect(explode(' ', $pName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
                    @endphp
                    <div class="card community-card mb-3">
                        <div class="card-body">
                            <div class="d-flex" style="gap: 10px;">
                                <div class="avatar-circle avatar-sm">{{ $pInitials }}</div>
                                <div style="flex: 1;">
                                    <div class="post-header">
                                        <strong>{{ $pName }}</strong>
                                        @if($pProfile && $pProfile->badge)
                                            <span class="user-badge">{{ str_replace('_', ' ', $pProfile->badge) }}</span>
                                        @endif
                                        <span class="post-time text-muted">{{ \Carbon\Carbon::parse($post->created_at)->diffForHumans() }}</span>
                                    </div>
                                    <p class="post-content">{{ $post->content }}</p>

                                    {{-- Comments --}}
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
                                                        <span class="comment-time text-muted">{{ \Carbon\Carbon::parse($comment->created_at)->diffForHumans() }}</span>
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
                    <div class="card community-card">
                        <div class="card-body text-center py-4">
                            <p class="text-muted">Ingen innlegg i denne gruppen enna. Bli den forste!</p>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Sidebar: Members --}}
            <div class="col-md-4">
                <div class="card community-card">
                    <div class="card-body">
                        <h5 class="widget-title"><i class="fa fa-users"></i> Medlemmer ({{ $learnerCount }})</h5>
                        @foreach($members->take(10) as $member)
                            @php
                                $mName = ucwords($member->name);
                                $mInitials = collect(explode(' ', $mName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
                            @endphp
                            <div class="member-item">
                                <div class="avatar-circle avatar-sm">{{ $mInitials }}</div>
                                <div>
                                    <span class="member-name">{{ $mName }}</span>
                                </div>
                            </div>
                        @endforeach
                        @if($members->count() > 10)
                            <p class="text-muted" style="font-size: 12px; margin-top: 8px;">...og {{ $members->count() - 10 }} til</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
