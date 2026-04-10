@extends('frontend.layouts.course-portal')

@section('page_title', 'Kursgrupper › Skrivefellesskap › Forfatterskolen')
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
        <h1 class="community-title">Kursgrupper</h1>
        <p class="community-subtitle">Private grupper for kursdeltagere. Diskuter med andre elever på samme kurs.</p>

        @if($courses->count() > 0)
            <div class="row">
                @foreach($courses as $course)
                    <div class="col-md-6">
                        <a href="{{ route('learner.community.courseGroup', $course->id) }}" class="discussion-link">
                            <div class="community-card mb-3 discussion-card">
                                <div class="card-body">
                                    <div class="d-flex" style="gap: 15px;">
                                        <div class="manuscript-icon">
                                            <i class="fa fa-graduation-cap"></i>
                                        </div>
                                        <div style="flex: 1;">
                                            <h4 class="discussion-title" style="margin: 0 0 4px;">{{ $course->title }}</h4>
                                            @if($course->description)
                                                <p class="discussion-preview">{{ Str::limit(html_entity_decode(strip_tags($course->description)), 100) }}</p>
                                            @endif
                                            <div class="discussion-meta">
                                                <span><i class="fa fa-users"></i> {{ $course->learner_count }} elever</span>
                                                @if($course->start_date)
                                                    <span>Oppstart: {{ \Carbon\Carbon::parse($course->start_date)->format('d.m.Y') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="community-card">
                <div class="card-body text-center py-5">
                    <p style="color: var(--text-muted);">Du er ikke meldt på noen kurs ennå.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@stop
