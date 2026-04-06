<style>
/* ── SIDEBAR REDESIGN — scoped overrides on #sidebar ── */
#sidebar {
    width: 220px !important;
    background: #1c1917 !important;
    position: fixed !important;
    top: 0; left: 0; bottom: 0;
    display: flex !important;
    flex-direction: column !important;
    overflow-y: auto !important;
    z-index: 50 !important;
    padding: 0 !important;
    border: none !important;
    box-shadow: none !important;
}
#sidebar * { box-sizing: border-box; }

/* Logo */
#sidebar .sb-logo {
    display: flex; align-items: center; gap: 0.5rem;
    padding: 1.25rem 1.25rem 1.5rem;
    text-decoration: none;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}
#sidebar .sb-logo img {
    height: 28px; width: auto;
    filter: brightness(0) invert(1);
}
#sidebar .sb-logo span {
    font-family: 'Source Sans 3', -apple-system, sans-serif;
    font-size: 0.85rem; font-weight: 700;
    letter-spacing: 0.5px; color: #fff;
}

/* Nav groups */
#sidebar .sb-group { padding: 1rem 0.75rem 0.5rem; }
#sidebar .sb-group-label {
    font-family: 'Source Sans 3', sans-serif;
    font-size: 0.6rem; font-weight: 600;
    letter-spacing: 1.5px; text-transform: uppercase;
    color: rgba(255,255,255,0.25);
    padding: 0 0.5rem; margin-bottom: 0.5rem;
}
#sidebar .sb-link {
    display: flex !important; align-items: center; gap: 0.6rem;
    padding: 0.55rem 0.75rem; border-radius: 6px;
    text-decoration: none !important;
    color: rgba(255,255,255,0.6) !important;
    font-family: 'Source Sans 3', sans-serif;
    font-size: 0.835rem; font-weight: 500;
    transition: background 0.15s, color 0.15s;
    margin-bottom: 2px;
}
#sidebar .sb-link:hover {
    background: rgba(255,255,255,0.06);
    color: #fff !important;
}
#sidebar .sb-link.sb-active {
    background: rgba(134, 39, 54, 0.3);
    color: #fff !important;
}
#sidebar .sb-link svg {
    width: 18px; height: 18px;
    stroke: currentColor; fill: none;
    stroke-width: 1.5; stroke-linecap: round; stroke-linejoin: round;
    flex-shrink: 0;
}
#sidebar .sb-badge {
    margin-left: auto;
    font-size: 0.65rem; font-weight: 600;
    background: #862736; color: #fff;
    padding: 0.1rem 0.45rem; border-radius: 10px;
    min-width: 18px; text-align: center;
}

/* Footer */
#sidebar .sb-footer {
    margin-top: auto;
    padding: 1rem 0.75rem;
    border-top: 1px solid rgba(255,255,255,0.06);
}
#sidebar .sb-portal-link {
    display: flex; align-items: center; gap: 0.5rem;
    padding: 0.55rem 0.75rem;
    border: 1px solid rgba(255,255,255,0.1) !important;
    border-radius: 6px;
    text-decoration: none !important;
    color: rgba(255,255,255,0.6) !important;
    font-family: 'Source Sans 3', sans-serif;
    font-size: 0.8rem; font-weight: 500;
    margin-bottom: 0.5rem;
    transition: border-color 0.15s, color 0.15s;
    background: transparent !important;
    width: 100%;
}
#sidebar .sb-portal-link:hover {
    border-color: rgba(255,255,255,0.2) !important;
    color: #fff !important;
}
#sidebar .sb-portal-link svg {
    width: 16px; height: 16px;
    stroke: currentColor; fill: none; stroke-width: 1.5;
}
#sidebar .sb-user {
    display: flex; align-items: center; gap: 0.6rem;
    padding: 0.5rem 0.75rem; border-radius: 6px;
    cursor: default; transition: background 0.15s;
}
#sidebar .sb-user-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    background: #862736;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.7rem; font-weight: 600; color: #fff;
    flex-shrink: 0; overflow: hidden;
}
#sidebar .sb-user-avatar img {
    width: 100%; height: 100%; object-fit: cover; border-radius: 50%;
}
#sidebar .sb-user-name {
    font-family: 'Source Sans 3', sans-serif;
    font-size: 0.8rem; font-weight: 500; color: #fff;
}
#sidebar .sb-user-id {
    font-family: 'Source Sans 3', sans-serif;
    font-size: 0.65rem; color: rgba(255,255,255,0.3);
}
#sidebar .sb-logout {
    display: flex; align-items: center; gap: 0.5rem;
    padding: 0.45rem 0.75rem; border-radius: 6px;
    text-decoration: none !important;
    color: rgba(255,255,255,0.4) !important;
    font-family: 'Source Sans 3', sans-serif;
    font-size: 0.78rem; font-weight: 500;
    background: transparent !important; border: none !important;
    cursor: pointer; transition: color 0.15s;
    width: 100%; margin-top: 0.5rem;
}
#sidebar .sb-logout:hover { color: rgba(255,255,255,0.7) !important; }
#sidebar .sb-logout svg {
    width: 15px; height: 15px;
    stroke: currentColor; fill: none; stroke-width: 1.5;
}

/* Hide all the old sidebar content */
#sidebar > .navbar-brand,
#sidebar > .nav.nav-sidebar,
#sidebar > .sidebar-bottom,
#sidebar > .sidebar-community-nav {
    display: none !important;
}

/* Mobile toggle fix */
#sidebar .sb-mobile-close {
    display: none;
    position: absolute; top: 1rem; right: 1rem;
    background: none; border: none; color: rgba(255,255,255,0.5);
    cursor: pointer; font-size: 1.25rem;
}

/* Community nav override */
#sidebar .sb-community-nav { padding: 0 0.75rem 0.5rem; }
#sidebar .sb-community-label {
    font-family: 'Source Sans 3', sans-serif;
    font-size: 0.65rem; font-weight: 600;
    letter-spacing: 1px; text-transform: uppercase;
    color: rgba(255,255,255,0.35);
    padding: 0.75rem 0.5rem 0.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    margin-bottom: 0.5rem;
}

@media (max-width: 1026px) {
    #sidebar {
        display: none !important;
        width: 220px !important;
    }
    #sidebar.sidebar-visible {
        display: flex !important;
        z-index: 1001 !important;
    }
    #sidebar .sb-mobile-close { display: block; }
}
</style>

{{-- ═══════════ NEW SIDEBAR CONTENT ═══════════ --}}
<div id="sidebar">

{{-- Logo --}}
<a href="{{ route('front.home') }}" class="sb-logo">
    <img src="{{ asset('images-new/logo.png') }}" alt="Forfatterskolen">
</a>

@if(Request::is('account/community*'))
    {{-- Community-navigasjon --}}
    <div class="sb-community-nav">
        <div class="sb-community-label">Skrivefellesskap</div>
        <a href="{{ route('learner.community.home') }}" class="sb-link {{ Request::is('account/community') && !Request::is('account/community/*') ? 'sb-active' : '' }}">
            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Hjem
        </a>
        <a href="{{ route('learner.community.discussions') }}" class="sb-link {{ Request::is('account/community/discussions*') ? 'sb-active' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            Diskusjoner
        </a>
        <a href="{{ route('learner.community.messages') }}" class="sb-link {{ Request::is('account/community/messages*') ? 'sb-active' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            Meldinger
        </a>
        <a href="{{ route('learner.community.members') }}" class="sb-link {{ Request::is('account/community/members*') ? 'sb-active' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
            Medlemmer
        </a>
        <a href="{{ route('learner.community.manuscripts') }}" class="sb-link {{ Request::is('account/community/manuscripts*') ? 'sb-active' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
            Manusrom
        </a>
        <a href="{{ route('learner.community.notifications') }}" class="sb-link {{ Request::is('account/community/notifications*') ? 'sb-active' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            Varsler
        </a>
        <a href="{{ route('learner.community.courseGroups') }}" class="sb-link {{ Request::is('account/community/course-groups*') ? 'sb-active' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            Kursgrupper
        </a>
        <a href="{{ route('learner.community.profile') }}" class="sb-link {{ Request::is('account/community/profile*') ? 'sb-active' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Min profil
        </a>
    </div>
@else
    {{-- ── Oversikt ── --}}
    <div class="sb-group">
        <div class="sb-group-label">Oversikt</div>
        <a href="{{ route('learner.dashboard') }}" class="sb-link {{ Route::currentRouteName() === 'learner.dashboard' ? 'sb-active' : '' }}">
            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Kontrollpanel
        </a>
        <a href="{{ route('learner.course') }}" class="sb-link {{ !Request::is('account/course-webinar') && Request::is('account/course*') ? 'sb-active' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            Mine kurs
            @php
                $newFeedbackCount = \App\AssignmentSubmission::where('user_id', Auth::id())
                    ->where('status', 'approved')
                    ->whereNull('seen_at')
                    ->count();
            @endphp
            @if($newFeedbackCount > 0)
                <span class="sb-badge" title="Nye tilbakemeldinger">{{ $newFeedbackCount }}</span>
            @endif
        </a>
        <a href="{{ route('learner.assignment') }}" class="sb-link {{ Request::is('account/assignment*') ? 'sb-active' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            Oppgaver
            @php
                $pendingAssignments = isset($assignments) ? collect($assignments)->filter(function($a) {
                    return !$a->manuscripts->where('user_id', Auth::user()->id)->first();
                })->count() : 0;
            @endphp
            @if($pendingAssignments > 0)
                <span class="sb-badge">{{ $pendingAssignments }}</span>
            @endif
        </a>
        <a href="{{ route('learner.private-message') }}" class="sb-link {{ Request::is('account/private-message*') ? 'sb-active' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            Beskjeder
        </a>
    </div>

    {{-- ── Læring ── --}}
    <div class="sb-group">
        <div class="sb-group-label">Læring</div>
        <a href="{{ route('learner.webinar') }}" class="sb-link {{ Request::is('account/webinar*') ? 'sb-active' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
            Mentormøter
        </a>
        <a href="{{ route('learner.course-webinar') }}" class="sb-link {{ Request::is('account/course-webinar*') ? 'sb-active' : '' }}">
            <svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            Kurswebinarer
        </a>
        <a href="{{ route('learner.shop-manuscript') }}" class="sb-link {{ Request::is('account/manus*') ? 'sb-active' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="13" y2="17"/></svg>
            Manusutviklinger
        </a>
        <a href="{{ route('learner.coaching-time') }}" class="sb-link {{ Request::is('account/coaching-time*') ? 'sb-active' : '' }}">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Coaching
        </a>
        @if(Auth::user()->coursesTaken()->whereHas('package', fn($q) => $q->where('course_id', 120))->where('is_active', 1)->exists())
        <a href="{{ route('learner.pabygg-treff') }}" class="sb-link {{ Request::is('account/pabygg-treff*') ? 'sb-active' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Samling
        </a>
        @endif
        @if(in_array(Auth::user()->role, [1, 3]))
        <a href="{{ route('learner.editor-courses') }}" class="sb-link {{ Request::is('account/editor-courses*') ? 'sb-active' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            Redaktørkurs
        </a>
        @endif
    </div>

    {{-- ── Konto ── --}}
    <div class="sb-group">
        <div class="sb-group-label">Konto</div>
        <a href="{{ route('learner.upgrade') }}" class="sb-link {{ Request::is('account/upgrade*') ? 'sb-active' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
            Kurspakker &amp; oppgradering
        </a>
        <a href="{{ route('learner.calendar') }}" class="sb-link {{ Request::is('account/calendar*') ? 'sb-active' : '' }}">
            <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Kalender
        </a>
        <a href="{{ route('learner.invoice') }}" class="sb-link {{ Request::is('account/invoice*') ? 'sb-active' : '' }}">
            <svg viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
            Fakturaer
        </a>
        <a href="{{ route('learner.profile') }}" class="sb-link {{ Request::is('account/profile*') ? 'sb-active' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Profil &amp; innstillinger
        </a>
    </div>
@endif

{{-- ── Footer ── --}}
<div class="sb-footer">
    @if(Request::is('account/community*'))
        <a href="{{ route('learner.dashboard') }}" class="sb-portal-link">
            <svg viewBox="0 0 24 24" stroke-linecap="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            Kursportalen
        </a>
    @else
        <a href="{{ route('learner.community.home') }}" class="sb-portal-link">
            <svg viewBox="0 0 24 24" stroke-linecap="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
            Skrivefellesskap
        </a>
    @endif
    <a href="{{ route('learner.change-portal', 'self-publishing') }}" class="sb-portal-link">
        <svg viewBox="0 0 24 24" stroke-linecap="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
        Selvpubliseringsportal
    </a>

    <div class="sb-user">
        <div class="sb-user-avatar">
            @if(Auth::user()->profile_image && !str_contains(Auth::user()->profile_image, 'user.png'))
                <img src="{{ Auth::user()->profile_image }}" alt="">
            @else
                {{ strtoupper(substr(Auth::user()->first_name ?? 'U', 0, 1) . substr(Auth::user()->last_name ?? '', 0, 1)) }}
            @endif
        </div>
        <div>
            <div class="sb-user-name">{{ Auth::user()->first_name ?? Auth::user()->name }}</div>
            <div class="sb-user-id">Elev #{{ Auth::id() }}</div>
        </div>
    </div>

    <form method="POST" action="{{ route('auth.logout') }}" style="margin:0;padding:0;">
        {{ csrf_field() }}
        <button type="submit" class="sb-logout">
            <svg viewBox="0 0 24 24" stroke-linecap="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Logg av
        </button>
    </form>
</div>
</div> {{-- /#sidebar --}}
