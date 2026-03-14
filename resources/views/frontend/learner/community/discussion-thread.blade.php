@extends('frontend.layouts.course-portal')

@section('title')
<title>{{ $discussion->title }} › Diskusjoner › Forfatterskolen</title>
@stop

@section('styles')
<link rel="stylesheet" href="{{ asset('css/community.css?v=' . time()) }}">
@stop

@section('content')
<div class="learner-container community-wrapper">
    <div class="container">
        <a href="{{ route('learner.community.discussions') }}" class="back-link">
            <i class="fa fa-arrow-left"></i> Tilbake til diskusjoner
        </a>

        @php
            $dProfile = $discussion->user->profile ?? null;
            $dName = $dProfile ? ucwords($dProfile->name) : 'Ukjent';
            $dInitials = collect(explode(' ', $dName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
        @endphp

        {{-- Discussion --}}
        <div class="card community-card mb-4">
            <div class="card-body">
                <div class="d-flex" style="gap: 12px;">
                    <div class="avatar-circle">{{ $dInitials }}</div>
                    <div style="flex: 1;">
                        <h2 class="discussion-title" style="font-size: 1.3em;">{{ $discussion->title }}</h2>
                        <div class="post-header mb-3">
                            <strong>{{ $dName }}</strong>
                            @if($dProfile && $dProfile->badge)
                                <span class="user-badge">{{ $dProfile->badge }}</span>
                            @endif
                            <span class="text-muted">{{ \Carbon\Carbon::parse($discussion->created_at)->diffForHumans() }}</span>
                            <span class="category-tag ml-2">{{ $discussion->category }}</span>
                        </div>
                        <p class="post-content">{{ $discussion->content }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Replies --}}
        <h3 class="replies-heading">{{ $discussion->replies->count() }} svar</h3>

        @foreach($discussion->replies as $reply)
            @php
                $rProfile = $reply->user->profile ?? null;
                $rName = $rProfile ? ucwords($rProfile->name) : 'Ukjent';
                $rInitials = collect(explode(' ', $rName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
            @endphp
            <div class="card community-card mb-3">
                <div class="card-body">
                    <div class="d-flex" style="gap: 12px;">
                        <div class="avatar-circle avatar-sm">{{ $rInitials }}</div>
                        <div style="flex: 1;">
                            <div class="post-header">
                                <strong>{{ $rName }}</strong>
                                @if($rProfile && $rProfile->badge)
                                    <span class="user-badge">{{ $rProfile->badge }}</span>
                                @endif
                                <span class="text-muted">{{ \Carbon\Carbon::parse($reply->created_at)->diffForHumans() }}</span>
                            </div>
                            <p class="post-content">{{ $reply->content }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Reply form --}}
        <div class="card community-card mt-3">
            <div class="card-body">
                <h4 class="widget-title">Skriv et svar</h4>
                <form action="{{ route('learner.community.storeReply', $discussion->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <textarea name="content" class="form-control community-textarea" rows="3" placeholder="Del dine tanker..." required></textarea>
                    </div>
                    <button type="submit" class="btn community-btn-primary">Publiser svar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
