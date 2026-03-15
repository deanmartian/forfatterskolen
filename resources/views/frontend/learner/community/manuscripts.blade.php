@extends('frontend.layouts.course-portal')

@section('title')
<title>Manusrom › Skrivefellesskap › Forfatterskolen</title>
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
        <div class="d-flex" style="justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <h1 class="community-title">Manusrom</h1>
                <p class="community-subtitle">Private prosjektrom for manus og tilbakemeldinger.</p>
            </div>
            <button class="btn community-btn-primary" onclick="document.getElementById('create-manuscript').style.display = document.getElementById('create-manuscript').style.display === 'none' ? 'block' : 'none'">
                <i class="fa fa-plus"></i> Nytt prosjekt
            </button>
        </div>

        <div class="card community-card community-card-accent mb-4">
            <div class="card-body">
                <p style="margin: 0; font-size: 13px;">
                    <i class="fa fa-star" style="color: var(--community-accent);"></i>
                    <strong>Premium-funksjon:</strong> Manusrom er tilgjengelig for studenter i årskurs og mentorprogram. Del utdrag fra manuset ditt og få verdifulle tilbakemeldinger fra fellesskapet.
                </p>
            </div>
        </div>

        {{-- Create project form --}}
        <div id="create-manuscript" class="card community-card mb-4" style="display: none;">
            <div class="card-body">
                <h4 class="widget-title">Opprett nytt prosjekt</h4>
                <form action="{{ route('learner.community.storeManuscript') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <input type="text" name="title" class="form-control" placeholder="Prosjekttittel" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <select name="genre" class="form-control" required>
                                    <option value="">Velg sjanger</option>
                                    @foreach(['Krim','Thriller','Skjønnlitteratur','Historisk','Romantikk','Feelgood','Science Fiction','Fantasy','Novelle','Sakprosa','Poesi'] as $g)
                                        <option value="{{ $g }}">{{ $g }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="number" name="word_count" class="form-control" placeholder="Ordtelling" value="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <select name="status" class="form-control">
                                    @foreach(['Planlegger','Pågår','Førsteutkast ferdig','Redigering','Ferdig'] as $s)
                                        <option value="{{ $s }}" {{ $s === 'Pågår' ? 'selected' : '' }}>{{ $s }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <textarea name="description" class="form-control" rows="2" placeholder="Kort beskrivelse av prosjektet..."></textarea>
                    </div>
                    <button type="submit" class="btn community-btn-primary">Opprett prosjekt</button>
                </form>
            </div>
        </div>

        {{-- Project list --}}
        @forelse($projects as $project)
            @php
                $pProfile = $project->user->profile ?? null;
                $pName = $pProfile ? ucwords($pProfile->name) : 'Ukjent';
                $pInitials = collect(explode(' ', $pName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
            @endphp
            <a href="{{ route('learner.community.manuscript', $project->id) }}" class="discussion-link">
                <div class="card community-card mb-3 discussion-card">
                    <div class="card-body">
                        <div class="d-flex" style="gap: 15px;">
                            <div class="manuscript-icon">
                                <i class="fa fa-book"></i>
                            </div>
                            <div style="flex: 1;">
                                <div class="d-flex" style="align-items: center; gap: 8px; flex-wrap: wrap;">
                                    <h4 class="discussion-title" style="margin: 0;">{{ $project->title }}</h4>
                                    <span class="category-tag">{{ $project->genre }}</span>
                                    <span class="status-tag">{{ $project->status }}</span>
                                </div>
                                <p class="discussion-preview">{{ $project->description }}</p>
                                <div class="discussion-meta">
                                    <div class="avatar-circle avatar-xs">{{ $pInitials }}</div>
                                    <span>{{ $pName }}</span>
                                    <span>·</span>
                                    <span>{{ number_format($project->word_count, 0, ',', '.') }} ord</span>
                                    <span>·</span>
                                    <span>{{ $project->excerpts->count() }} utdrag</span>
                                    <span>·</span>
                                    <span>{{ $project->followers->count() }} følgere</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="card community-card">
                <div class="card-body text-center py-5">
                    <p class="text-muted">Ingen prosjekter ennå. Opprett det første!</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@stop
