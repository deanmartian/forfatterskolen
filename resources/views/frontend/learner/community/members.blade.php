@extends('frontend.layouts.course-portal')

@section('title')
<title>Medlemmer › Skrivefellesskap › Forfatterskolen</title>
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
        <h1 class="community-title">Medlemmer</h1>
        <p class="community-subtitle">Bli kjent med andre skrivere i fellesskapet.</p>

        <div class="row">
            @foreach($members as $member)
                @php
                    $mName = ucwords($member->name);
                    $mInitials = collect(explode(' ', $mName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
                    $mGenres = is_array($member->genres) ? implode(', ', array_slice($member->genres, 0, 3)) : '';
                @endphp
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="card community-card member-card">
                        <div class="card-body text-center">
                            <div class="avatar-circle avatar-lg mx-auto">{{ $mInitials }}</div>
                            <h4 class="member-card-name">{{ $mName }}</h4>
                            @if($member->badge)
                                <span class="user-badge">{{ $member->badge }}</span>
                            @endif
                            @if($mGenres)
                                <p class="text-muted member-card-genres">{{ $mGenres }}</p>
                            @endif
                            @if($member->bio)
                                <p class="member-card-bio">{{ Str::limit($member->bio, 80) }}</p>
                            @endif
                            @if($member->user_id !== Auth::id())
                                <a href="{{ route('learner.community.conversation', $member->user_id) }}" class="btn community-btn-outline btn-sm mt-2">
                                    <i class="fa fa-envelope-o"></i> Send melding
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@stop
