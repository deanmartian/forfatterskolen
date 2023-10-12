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
    <!-- Sidebar content goes here -->
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

    <div class="learner-details-container">
        <em>Elevnummer:</em>
        <b>{{ Auth::id() }}</b>
    </div>

    <div class="icons-container">
        <a href="https://www.facebook.com/bliforfatter/" target="_blank">
            <img src="{{ asset('images-new/icon/facebook.png') }}" alt="facebook-icon">
        </a>
        <a href="https://twitter.com/Forfatterrektor" target="_blank" class="ml-0">
            <img src="{{ asset('images-new/icon/twitter.png') }}" alt="twitter-icon">
        </a>
        <a href="https://www.instagram.com/forfatterskolen_norge/" target="_blank">
            <img src="{{ asset('images-new/icon/instagram.png') }}" alt="instagram-icon">
        </a>
        <a href="https://no.pinterest.com/forfatterskolen_norge/" target="_blank">
            <img src="{{ asset('images-new/icon/pinterest.png') }}" alt="pinterest-icon">
        </a>
    </div>

    <a href="{{ route('learner.change-portal', 'self-publishing') }}" class="btn portal-btn">
        Selvpubliseringsportal
    </a>

    <a href="{{ route('auth.logout-get') }}" style="display: block">
        <form method="POST" action="{{route('auth.logout')}}" class="form-logout">
            {{csrf_field()}}
            <button type="submit" class="btn logout-btn">
                <i class="fa fa-sign-out-alt"></i> Logg av
            </button>
        </form>
    </a>
</div>