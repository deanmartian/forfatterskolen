@extends('frontend.layouts.course-portal')

@section('title')
<title>{{ $excerpt->title }} › Manusrom › Forfatterskolen</title>
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
        <a href="{{ route('learner.community.manuscript', $excerpt->project->id) }}" class="back-link">
            <i class="fa fa-arrow-left"></i> Tilbake til prosjektet
        </a>

        {{-- Excerpt --}}
        <div class="community-card mb-4">
            <div class="card-body">
                <h2 class="discussion-title" style="font-size: 1.3em; font-family: var(--font-display);">{{ $excerpt->title }}</h2>
                <div class="discussion-meta mb-3">
                    <span>{{ $excerpt->word_count }} ord</span>
                    <span>·</span>
                    <span>{{ $excerpt->feedback->count() }} tilbakemeldinger</span>
                </div>
                <div class="excerpt-content">
                    <p>{{ $excerpt->content }}</p>
                </div>
            </div>
        </div>

        {{-- Feedback --}}
        <h3 class="replies-heading">Tilbakemeldinger</h3>

        @forelse($excerpt->feedback as $fb)
            @php
                $fbProfile = $fb->user->profile ?? null;
                $fbName = $fbProfile ? ucwords($fbProfile->name) : 'Ukjent';
                $fbInitials = collect(explode(' ', $fbName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
                $fbColors = ['#2563eb', '#0d7a5f', '#7c3aed', '#b45309', '#862736'];
                $fbColor = $fbColors[crc32($fbName) % count($fbColors)];
            @endphp
            <div class="community-card mb-3">
                <div class="card-body">
                    <div class="d-flex" style="gap: 12px;">
                        <div class="avatar-circle avatar-sm" style="background: {{ $fbColor }};">{{ $fbInitials }}</div>
                        <div>
                            <div class="post-header">
                                <strong>{{ $fbName }}</strong>
                                <span class="post-time">{{ \Carbon\Carbon::parse($fb->created_at)->diffForHumans() }}</span>
                            </div>
                            <p class="post-content">{{ $fb->content }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="community-card mb-3">
                <div class="card-body text-center py-4">
                    <p style="color: var(--text-muted);">Ingen tilbakemeldinger ennå. Bli den første!</p>
                </div>
            </div>
        @endforelse

        {{-- Feedback form --}}
        <div class="community-card">
            <div class="card-body">
                <h4 class="widget-title">Gi tilbakemelding</h4>
                <form action="{{ route('learner.community.storeFeedback', $excerpt->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <textarea name="content" class="form-control community-textarea" rows="4" placeholder="Skriv din tilbakemelding her..." required></textarea>
                    </div>
                    <button type="submit" class="community-btn-primary">Send tilbakemelding</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
