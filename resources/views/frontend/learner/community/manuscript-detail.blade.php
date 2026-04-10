@extends('frontend.layouts.course-portal')

@section('page_title', $project->title . ' › Manusrom › Forfatterskolen')
@section('robots')<meta name="robots" content="noindex, follow">@endsection

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
        <a href="{{ route('learner.community.manuscripts') }}" class="back-link">
            <i class="fa fa-arrow-left"></i> Tilbake til manusrom
        </a>

        @php
            $pProfile = $project->user->profile ?? null;
            $pName = $pProfile ? ucwords($pProfile->name) : 'Ukjent';
            $pInitials = collect(explode(' ', $pName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
            $isOwner = $project->user_id === Auth::id();
            $isFollowing = $project->followers->contains('id', Auth::id());
        @endphp

        {{-- Project header --}}
        <div class="community-card mb-4">
            <div class="card-body">
                <div class="d-flex" style="justify-content: space-between; align-items: flex-start;">
                    <div>
                        <h2 class="discussion-title" style="font-size: 1.4em; font-family: var(--font-display);">{{ $project->title }}</h2>
                        <p class="post-content">{{ $project->description }}</p>
                    </div>
                    @if(!$isOwner)
                        <form action="{{ route('learner.community.toggleFollow', $project->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="{{ $isFollowing ? 'community-btn-outline' : 'community-btn-primary' }}" style="font-size: 12px; padding: 6px 14px;">
                                <i class="fa fa-heart{{ $isFollowing ? '' : '-o' }}"></i>
                                {{ $isFollowing ? 'Slutter å følge' : 'Følg' }}
                            </button>
                        </form>
                    @endif
                </div>

                <div class="discussion-meta mt-3">
                    <span class="category-tag">{{ $project->genre }}</span>
                    <span class="status-tag">{{ $project->status }}</span>
                    <span><i class="fa fa-file-text-o"></i> {{ number_format($project->word_count, 0, ',', '.') }} ord</span>
                    <span><i class="fa fa-users"></i> {{ $project->followers->count() }} følgere</span>
                </div>

                <div class="d-flex mt-3" style="gap: 8px; align-items: center;">
                    <div class="avatar-circle avatar-sm">{{ $pInitials }}</div>
                    <strong style="font-size: 13px;">{{ $pName }}</strong>
                </div>
            </div>
        </div>

        {{-- Excerpts --}}
        <div class="d-flex" style="justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 class="replies-heading" style="margin: 0;">Utdrag</h3>
            @if($isOwner)
                <button class="community-btn-primary" style="font-size: 12px; padding: 6px 14px;" onclick="document.getElementById('create-excerpt').style.display = document.getElementById('create-excerpt').style.display === 'none' ? 'block' : 'none'">
                    <i class="fa fa-plus"></i> Nytt utdrag
                </button>
            @endif
        </div>

        @if($isOwner)
            <div id="create-excerpt" class="community-card mb-4" style="display: none;">
                <div class="card-body">
                    <h4 class="widget-title">Nytt utdrag</h4>
                    <form action="{{ route('learner.community.storeExcerpt', $project->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <input type="text" name="title" class="form-control" placeholder="Tittel på utdraget (f.eks. Kapittel 1: Åpningen)" required style="border: 1px solid var(--border); border-radius: 8px; font-size: 14px; padding: 10px 12px;">
                        </div>
                        <div class="form-group">
                            <textarea name="content" class="form-control community-textarea" rows="8" placeholder="Lim inn utdraget ditt her (maks 3000 ord)..." required></textarea>
                        </div>
                        <button type="submit" class="community-btn-primary">Publiser utdrag</button>
                    </form>
                </div>
            </div>
        @endif

        @forelse($project->excerpts as $excerpt)
            <a href="{{ route('learner.community.excerpt', $excerpt->id) }}" class="discussion-link">
                <div class="community-card mb-3 discussion-card">
                    <div class="card-body">
                        <h4 class="discussion-title">{{ $excerpt->title }}</h4>
                        <p class="discussion-preview">{{ Str::limit($excerpt->content, 200) }}</p>
                        <div class="discussion-meta">
                            <span>{{ $excerpt->word_count }} ord</span>
                            <span>·</span>
                            <span><i class="fa fa-comment-o"></i> {{ $excerpt->feedback->count() }} tilbakemeldinger</span>
                            <span>·</span>
                            <span>{{ \Carbon\Carbon::parse($excerpt->created_at)->format('d.m.Y') }}</span>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="community-card">
                <div class="card-body text-center py-4">
                    <p style="color: var(--text-muted);">Ingen utdrag ennå. {{ $isOwner ? 'Klikk «Nytt utdrag» for å dele teksten din.' : '' }}</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@stop
