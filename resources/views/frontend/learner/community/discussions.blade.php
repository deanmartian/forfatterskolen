@extends('frontend.layouts.course-portal')

@section('title')
<title>Diskusjoner › Skrivefellesskap › Forfatterskolen</title>
@stop

@section('styles')
<link rel="stylesheet" href="{{ asset('css/community.css?v=' . time()) }}">
@stop

@section('content')
<div class="learner-container community-wrapper">
    <div class="container">
        <div class="d-flex" style="justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <h1 class="community-title">Diskusjoner</h1>
                <p class="community-subtitle">Start eller delta i samtaler om skriving.</p>
            </div>
            <button class="btn community-btn-primary" onclick="document.getElementById('create-discussion').style.display = document.getElementById('create-discussion').style.display === 'none' ? 'block' : 'none'">
                <i class="fa fa-plus"></i> Ny diskusjon
            </button>
        </div>

        {{-- Create discussion form --}}
        <div id="create-discussion" class="card community-card mb-4" style="display: none;">
            <div class="card-body">
                <h4 class="widget-title">Opprett ny diskusjon</h4>
                <form action="{{ route('learner.community.storeDiscussion') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <input type="text" name="title" class="form-control" placeholder="Tittel på diskusjonen" required>
                    </div>
                    <div class="form-group">
                        <select name="category" class="form-control" required>
                            <option value="">Velg kategori</option>
                            <option value="Skriveteknikk">Skriveteknikk</option>
                            <option value="Inspirasjon">Inspirasjon</option>
                            <option value="Sjanger">Sjanger</option>
                            <option value="Publisering">Publisering</option>
                            <option value="Tilbakemelding">Tilbakemelding</option>
                            <option value="Generelt">Generelt</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <textarea name="content" class="form-control community-textarea" rows="4" placeholder="Hva vil du diskutere?" required></textarea>
                    </div>
                    <button type="submit" class="btn community-btn-primary">Publiser diskusjon</button>
                </form>
            </div>
        </div>

        {{-- Discussions list --}}
        @forelse($discussions as $discussion)
            @php
                $dProfile = $discussion->user->profile ?? null;
                $dName = $dProfile ? ucwords($dProfile->name) : 'Ukjent';
                $dInitials = collect(explode(' ', $dName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
            @endphp
            <a href="{{ route('learner.community.discussion', $discussion->id) }}" class="discussion-link">
                <div class="card community-card mb-3 discussion-card">
                    <div class="card-body">
                        @if($discussion->pinned)
                            <div class="pinned-label"><i class="fa fa-thumb-tack"></i> Festet</div>
                        @endif
                        <div class="d-flex" style="gap: 12px;">
                            <div class="avatar-circle">{{ $dInitials }}</div>
                            <div style="flex: 1;">
                                <h4 class="discussion-title">{{ $discussion->title }}</h4>
                                <p class="discussion-preview">{{ Str::limit($discussion->content, 150) }}</p>
                                <div class="discussion-meta">
                                    <span class="category-tag">{{ $discussion->category }}</span>
                                    <span>{{ $dName }}</span>
                                    <span>·</span>
                                    <span>{{ $discussion->replies->count() }} svar</span>
                                    <span>·</span>
                                    <span>{{ \Carbon\Carbon::parse($discussion->created_at)->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="reply-count-circle">
                                {{ $discussion->replies->count() }}
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="card community-card">
                <div class="card-body text-center py-5">
                    <p class="text-muted">Ingen diskusjoner ennå. Start den første!</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@stop
