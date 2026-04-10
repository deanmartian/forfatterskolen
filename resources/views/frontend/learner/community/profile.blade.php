@extends('frontend.layouts.course-portal')

@section('page_title', 'Min profil › Skrivefellesskap › Forfatterskolen')
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

            {{-- Push-varsler for community --}}
            <div style="background:#fff;border:1px solid #e8e4de;border-radius:12px;padding:24px;margin-top:20px;">
                <h3 style="margin:0 0 4px;font-size:18px;">🔔 Push-varsler for fellesskapet</h3>
                <p style="color:#888;font-size:13px;margin:0 0 16px;">Velg hva du vil få varsler om på telefonen.</p>

                <form method="POST" action="{{ route('learner.community.updatePushPreferences') }}">
                    @csrf
                    @php $prefs = Auth::user(); @endphp

                    <div style="display:flex;flex-direction:column;gap:12px;">
                        <label style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:#faf9f7;border-radius:8px;cursor:pointer;">
                            <div>
                                <strong style="font-size:14px;">📝 Nye innlegg</strong>
                                <div style="font-size:12px;color:#888;">Når noen publiserer et nytt innlegg i fellesskapet</div>
                            </div>
                            <input type="checkbox" name="push_community_posts" value="1" {{ $prefs->wantsPushNotification('community_posts') ? 'checked' : '' }} style="width:20px;height:20px;">
                        </label>

                        <label style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:#faf9f7;border-radius:8px;cursor:pointer;">
                            <div>
                                <strong style="font-size:14px;">💬 Kommentarer på mine innlegg</strong>
                                <div style="font-size:12px;color:#888;">Når noen kommenterer på noe du har skrevet</div>
                            </div>
                            <input type="checkbox" name="push_community_comments" value="1" {{ $prefs->wantsPushNotification('community_comments') ? 'checked' : '' }} style="width:20px;height:20px;">
                        </label>

                        <label style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:#faf9f7;border-radius:8px;cursor:pointer;">
                            <div>
                                <strong style="font-size:14px;">💬 Diskusjoner</strong>
                                <div style="font-size:12px;color:#888;">Nye diskusjoner og svar i diskusjoner du følger</div>
                            </div>
                            <input type="checkbox" name="push_community_discussions" value="1" {{ $prefs->wantsPushNotification('community_discussions') ? 'checked' : '' }} style="width:20px;height:20px;">
                        </label>

                        <label style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:#faf9f7;border-radius:8px;cursor:pointer;">
                            <div>
                                <strong style="font-size:14px;">📚 Kursgruppe-innlegg</strong>
                                <div style="font-size:12px;color:#888;">Nye innlegg i kursgruppene dine</div>
                            </div>
                            <input type="checkbox" name="push_community_groups" value="1" {{ $prefs->wantsPushNotification('community_groups') ? 'checked' : '' }} style="width:20px;height:20px;">
                        </label>

                        <label style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:#faf9f7;border-radius:8px;cursor:pointer;">
                            <div>
                                <strong style="font-size:14px;">@ Mentions</strong>
                                <div style="font-size:12px;color:#888;">Når noen nevner deg i et innlegg eller kommentar</div>
                            </div>
                            <input type="checkbox" name="push_community_mentions" value="1" {{ $prefs->wantsPushNotification('community_mentions') ? 'checked' : '' }} style="width:20px;height:20px;">
                        </label>

                        <label style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:#faf9f7;border-radius:8px;cursor:pointer;">
                            <div>
                                <strong style="font-size:14px;">❤️ Likes</strong>
                                <div style="font-size:12px;color:#888;">Når noen liker innleggene eller kommentarene dine</div>
                            </div>
                            <input type="checkbox" name="push_community_likes" value="1" {{ $prefs->wantsPushNotification('community_likes') ? 'checked' : '' }} style="width:20px;height:20px;">
                        </label>
                    </div>

                    <button type="submit" class="community-btn-primary" style="margin-top:16px;">Lagre push-innstillinger</button>
                </form>
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
