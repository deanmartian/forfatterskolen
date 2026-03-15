@extends('frontend.layouts.course-portal')

@section('title')
<title>Min profil › Skrivefellesskap › Forfatterskolen</title>
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
        <h1 class="community-title">Min profil</h1>

        <div class="row">
            <div class="col-md-4">
                <div class="card community-card mb-3">
                    <div class="card-body text-center">
                        @php
                            $pName = ucwords($profile->name ?? '');
                            $pInitials = collect(explode(' ', $pName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
                        @endphp
                        <div class="avatar-circle avatar-xl mx-auto">{{ $pInitials }}</div>
                        <h3 class="member-card-name mt-3">{{ $pName }}</h3>
                        @if($profile->author_name)
                            <p class="text-muted">{{ $profile->author_name }}</p>
                        @endif
                        @if($profile->badge)
                            <span class="user-badge">{{ $profile->badge }}</span>
                        @endif
                        @if($profile->bio)
                            <p class="mt-3" style="font-size: 13px;">{{ $profile->bio }}</p>
                        @endif
                        @if(is_array($profile->genres) && count($profile->genres) > 0)
                            <div class="mt-2">
                                @foreach($profile->genres as $genre)
                                    <span class="category-tag">{{ $genre }}</span>
                                @endforeach
                            </div>
                        @endif
                        @if($profile->current_project)
                            <p class="mt-3" style="font-size: 12px; color: #888;">
                                <i class="fa fa-pencil"></i> Jobber med: {{ $profile->current_project }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card community-card">
                    <div class="card-body">
                        <h4 class="widget-title">Rediger profil</h4>
                        <form action="{{ route('learner.community.updateProfile') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Navn</label>
                                <input type="text" name="name" class="form-control" value="{{ $profile->name }}">
                            </div>
                            <div class="form-group">
                                <label>Forfatternavn</label>
                                <input type="text" name="author_name" class="form-control" value="{{ $profile->author_name }}" placeholder="Ditt penname (valgfritt)">
                            </div>
                            <div class="form-group">
                                <label>Bio</label>
                                <textarea name="bio" class="form-control" rows="3" placeholder="Fortell litt om deg selv...">{{ $profile->bio }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Sjangre (kommaseparert)</label>
                                <input type="text" name="genres" class="form-control" value="{{ is_array($profile->genres) ? implode(',', $profile->genres) : '' }}" placeholder="Krim, Thriller, Fantasy">
                            </div>
                            <div class="form-group">
                                <label>Nåværende prosjekt</label>
                                <input type="text" name="current_project" class="form-control" value="{{ $profile->current_project }}" placeholder="Hva jobber du med?">
                            </div>
                            <button type="submit" class="btn community-btn-primary">Lagre endringer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
