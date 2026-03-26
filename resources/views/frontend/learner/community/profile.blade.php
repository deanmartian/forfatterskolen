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

        <div class="profile-page-wrapper">
            {{-- Profilhode --}}
            <div class="profile-header">
                @php
                    $fullName = trim($user->first_name . ' ' . $user->last_name);
                    $pInitials = collect(explode(' ', $fullName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
                    $hasImage = $user->profile_image && !str_contains($user->profile_image, 'user.png');
                @endphp
                <div class="profile-avatar-wrapper">
                    @if($hasImage)
                        <img src="{{ $user->profile_image }}" alt="{{ $fullName }}" class="profile-avatar-img">
                    @else
                        <div class="avatar-circle avatar-xl">{{ $pInitials }}</div>
                    @endif
                </div>
                <div class="profile-header-info">
                    <h2 class="profile-header-name">{{ ucwords($fullName) }}</h2>
                    @if($profile->author_name && $profile->use_author_name)
                        <p class="profile-header-author">Skriver som <strong>{{ $profile->author_name }}</strong></p>
                    @endif
                    @if($profile->badge)
                        <span class="user-badge">{{ $profile->badge }}</span>
                    @endif
                </div>
            </div>

            {{-- Redigeringsskjema --}}
            <div class="community-card">
                <div class="card-body profile-form-section">
                    <h4 class="widget-title">Rediger profil</h4>
                    <form action="{{ route('learner.community.updateProfile') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label>Navn</label>
                            <div class="profile-readonly-value">{{ ucwords($fullName) }}</div>
                            <span class="profile-hint"><i class="fa fa-info-circle"></i> Endres under Profil\Kursbevis i kursportalen</span>
                        </div>

                        <div class="form-group">
                            <label>Forfatternavn</label>
                            <input type="text" name="author_name" class="form-control" value="{{ $profile->author_name }}" placeholder="Ditt penname (valgfritt)">
                            <div class="profile-checkbox-row">
                                <label class="profile-checkbox-label">
                                    <input type="checkbox" name="use_author_name" value="1" {{ $profile->use_author_name ? 'checked' : '' }}>
                                    <span>Bruk forfatternavn i forumet</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Bio</label>
                            <textarea name="bio" class="form-control" rows="3" placeholder="Fortell litt om deg selv...">{{ $profile->bio }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Sjangre</label>
                            @php $selectedGenres = is_array($profile->genres) ? $profile->genres : []; @endphp
                            <div class="profile-genre-grid">
                                @foreach($allGenres as $genre)
                                    <label class="profile-genre-chip {{ in_array($genre, $selectedGenres) ? 'selected' : '' }}">
                                        <input type="checkbox" name="genres[]" value="{{ $genre }}" {{ in_array($genre, $selectedGenres) ? 'checked' : '' }}>
                                        <span>{{ $genre }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Nåværende prosjekt</label>
                            <input type="text" name="current_project" class="form-control" value="{{ $profile->current_project }}" placeholder="Hva jobber du med?">
                        </div>

                        <button type="submit" class="community-btn-primary">Lagre endringer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.profile-genre-chip input[type="checkbox"]').forEach(function(cb) {
    cb.addEventListener('change', function() {
        this.closest('.profile-genre-chip').classList.toggle('selected', this.checked);
    });
});
</script>
@stop
