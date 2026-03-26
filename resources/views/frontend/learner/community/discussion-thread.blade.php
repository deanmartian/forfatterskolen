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
        <a href="{{ route('learner.community.discussions') }}" class="back-link">
            <i class="fa fa-arrow-left"></i> Tilbake til diskusjoner
        </a>

        @php
            $dProfile = $discussion->user->profile ?? null;
            $dName = $dProfile ? ucwords($dProfile->name) : 'Ukjent';
            $dInitials = collect(explode(' ', $dName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
            $dColors = ['pa-red', 'pa-blue', 'pa-teal', 'pa-purple', 'pa-amber'];
            $dColor = $dColors[crc32($dName) % count($dColors)];
        @endphp

        {{-- Discussion --}}
        <div class="community-card mb-4">
            <div class="card-body">
                <div class="d-flex" style="gap: 12px;">
                    <div class="avatar-circle {{ $dColor }}">{{ $dInitials }}</div>
                    <div style="flex: 1;">
                        <h2 class="discussion-title" style="font-size: 1.3em; font-family: var(--font-display);">{{ $discussion->title }}</h2>
                        <div class="post-header mb-3">
                            <strong>{{ $dName }}</strong>
                            @if($dProfile && $dProfile->badge)
                                <span class="user-badge">{{ $dProfile->badge }}</span>
                            @endif
                            <span class="post-time">{{ \Carbon\Carbon::parse($discussion->created_at)->diffForHumans() }}</span>
                            <span class="category-tag" style="margin-left: 4px;">{{ $discussion->category }}</span>
                        </div>
                        <p class="post-content">{{ $discussion->content }}</p>
                        @if($discussion->image_url)
                            <div style="margin-top: 10px;">
                                <img src="{{ $discussion->image_url }}" alt="Diskusjonsbilde" style="max-width: 100%; border-radius: var(--radius-sm); border: 1px solid var(--border-light);">
                            </div>
                        @endif
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
                $rColors = ['#2563eb', '#0d7a5f', '#7c3aed', '#b45309', '#862736'];
                $rColor = $rColors[crc32($rName) % count($rColors)];
            @endphp
            <div class="community-card mb-3">
                <div class="card-body">
                    <div class="d-flex" style="gap: 12px;">
                        <div class="avatar-circle avatar-sm" style="background: {{ $rColor }};">{{ $rInitials }}</div>
                        <div style="flex: 1;">
                            <div class="post-header">
                                <strong>{{ $rName }}</strong>
                                @if($rProfile && $rProfile->badge)
                                    <span class="user-badge">{{ $rProfile->badge }}</span>
                                @endif
                                <span class="post-time">{{ \Carbon\Carbon::parse($reply->created_at)->diffForHumans() }}</span>
                            </div>
                            <p class="post-content">{{ $reply->content }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Reply form --}}
        <div class="community-card mt-3">
            <div class="card-body">
                <h4 class="widget-title">Skriv et svar</h4>
                <form action="{{ route('learner.community.storeReply', $discussion->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <textarea name="content" id="reply-textarea" class="form-control community-textarea" rows="3" placeholder="Del dine tanker..." required></textarea>
                        <div class="post-form-toolbar" style="justify-content: flex-start;">
                            <div class="emoji-picker-wrapper" data-bs-target="reply-textarea">
                                <button type="button" class="emoji-toggle-btn btn-action" title="Emoji"><i class="fa fa-smile-o"></i></button>
                                <div class="emoji-popup"><emoji-picker></emoji-picker></div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="community-btn-primary">Publiser svar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
@include('frontend.learner.community._emoji')
@stop
