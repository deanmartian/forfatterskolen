<div id="sidebar">
    <a class="navbar-brand" href="{{ route('front.home') }}" style="position: relative">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 43 41" fill="none">
            <path d="M0 0L21.5 2.90538V41L0 36.6077V0Z" fill="#E73946"/>
            <path d="M43 0L21.5 2.90612V40.9983L43 36.3185V0Z" fill="#852636"/>
        </svg>
        <span>
            FORFATTERSKOLEN
        </span>
    </a>

    @if(Request::is('account/community*'))
    {{-- Community-navigasjon --}}
    <div class="sidebar-community-nav">
        <div class="sidebar-community-label">Skrivefellesskap</div>
        <ul class="nav nav-sidebar">
            <li @if(Request::is('account/community') && !Request::is('account/community/*')) class="active" @endif>
                <a href="{{ route('learner.community.home') }}"><i class="fa fa-home"></i> Hjem</a>
            </li>
            <li @if(Request::is('account/community/discussions*')) class="active" @endif>
                <a href="{{ route('learner.community.discussions') }}"><i class="fa fa-comments"></i> Diskusjoner</a>
            </li>
            <li @if(Request::is('account/community/messages*')) class="active" @endif>
                <a href="{{ route('learner.community.messages') }}"><i class="fa fa-envelope"></i> Meldinger</a>
            </li>
            <li @if(Request::is('account/community/members*')) class="active" @endif>
                <a href="{{ route('learner.community.members') }}"><i class="fa fa-users"></i> Medlemmer</a>
            </li>
            <li @if(Request::is('account/community/manuscripts*')) class="active" @endif>
                <a href="{{ route('learner.community.manuscripts') }}"><i class="fa fa-book"></i> Manusrom</a>
            </li>
            <li @if(Request::is('account/community/notifications*')) class="active" @endif>
                <a href="{{ route('learner.community.notifications') }}"><i class="fa fa-bell"></i> Varsler</a>
            </li>
            <li @if(Request::is('account/community/course-groups*')) class="active" @endif>
                <a href="{{ route('learner.community.courseGroups') }}"><i class="fa fa-graduation-cap"></i> Kursgrupper</a>
            </li>
            <li @if(Request::is('account/community/profile*')) class="active" @endif>
                <a href="{{ route('learner.community.profile') }}"><i class="fa fa-user"></i> Min profil</a>
            </li>
        </ul>
    </div>
    @else
    {{-- Vanlig elevportal-navigasjon --}}
    <ul class="nav nav-sidebar">
        @foreach (FrontendHelpers::coursePortalNav() as $nav )
            <li @if($nav['is_active']) class="active" @endif>
                <a href="{{ route($nav['route_name']) }}">
                    <i class="{{ $nav['fa-icon'] }}"></i>
                    {{ $nav['label'] }}
                </a>
            </li>
        @endforeach
    </ul>
    @endif

    <div class="sidebar-bottom">
        <div class="learner-details-container">
            <em>Elevnummer:</em>
            <b>{{ Auth::id() }}</b>
        </div>
        @if(Request::is('account/community*'))
        <a href="{{ route('learner.dashboard') }}" class="btn portal-btn">
            <i class="fa fa-graduation-cap"></i> Kursportalen
        </a>
        @endif
        @if(!Request::is('account/community*'))
        <a href="{{ route('learner.community.home') }}" class="btn portal-btn">
            <i class="fa fa-comments"></i> Skrivefellesskap
        </a>
        @endif
        <a href="{{ route('learner.change-portal', 'self-publishing') }}" class="btn portal-btn">
            <i class="fa fa-book"></i> Selvpubliseringsportal
        </a>
        <form method="POST" action="{{route('auth.logout')}}" class="form-logout">
            {{csrf_field()}}
            <button type="submit" class="btn logout-btn">
                <i class="fa fa-sign-out-alt"></i> Logg av
            </button>
        </form>
    </div>
</div>