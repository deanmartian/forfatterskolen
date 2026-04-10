<link rel="stylesheet" href="{{ asset('css/pages/navbar.css') }}">

<nav class="fs-nav">
    <div class="fs-nav__inner">
        {{-- Logo --}}
        <a href="{{ route('front.home') }}" class="fs-nav__logo">
            <svg xmlns="http://www.w3.org/2000/svg" width="43" height="41" viewBox="0 0 43 41" fill="none">
                <path d="M0 0L21.5 2.90538V41L0 36.6077V0Z" fill="#E73946"/>
                <path d="M43 0L21.5 2.90612V40.9983L43 36.3185V0Z" fill="#852636"/>
            </svg>
            <span>FORFATTERSKOLEN</span>
        </a>

        {{-- Nav links --}}
        <ul class="fs-nav__links">
            <li><a href="{{ route('front.course.index') }}" class="fs-nav__link @if(Route::currentRouteName() == 'front.course.index') fs-active @endif">Kurs</a></li>
            <li><a href="{{ route('front.shop-manuscript.index') }}" class="fs-nav__link @if(Route::currentRouteName() == 'front.shop-manuscript.index') fs-active @endif">Manusutvikling</a></li>
            <li><a href="{{ route('front.publishing') }}" class="fs-nav__link @if(Route::currentRouteName() == 'front.publishing') fs-active @endif">Utgitte elever</a></li>
            <li><a href="{{ route('front.contact-us') }}" class="fs-nav__link @if(Route::currentRouteName() == 'front.contact-us') fs-active @endif">Om oss</a></li>
        </ul>

        {{-- Right side --}}
        <div class="fs-nav__right">
            <a href="{{ url('/arskurs') }}" class="fs-nav__cta">Årskurs 2026</a>

            @if (Auth::guest())
                <a href="{{ route('auth.login.show') }}" class="fs-nav__login">Logg inn</a>
            @else
                <div style="position: relative;">
                    <a href="javascript:void(0)" class="fs-nav__user-btn" onclick="fsToggleUserMenu(event)">
                        <div class="fs-nav__avatar">
                            @if(Auth::user()->profile_image)
                                <img src="{{ Auth::user()->profile_image }}" alt="">
                            @else
                                {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name ?? '', 0, 1)) }}
                            @endif
                        </div>
                        <span class="fs-nav__user-name">Hei {{ Auth::user()->first_name }}</span>
                        <span class="fs-nav__user-arrow">&#9662;</span>
                    </a>

                    <div class="fs-nav__dropdown" id="fsUserDropdown">
                        <div class="fs-nav__dropdown-header">
                            <div class="fs-nav__avatar">
                                @if(Auth::user()->profile_image)
                                    <img src="{{ Auth::user()->profile_image }}" alt="">
                                @else
                                    {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name ?? '', 0, 1)) }}
                                @endif
                            </div>
                            <div class="fs-nav__dropdown-info">
                                <strong>{{ ucfirst(Auth::user()->first_name) }}</strong>
                                {{ Auth::user()->email }}
                            </div>
                        </div>

                        <a href="{{ route('learner.dashboard') }}" class="fs-nav__dropdown-item">Oversikt</a>

                        <div class="fs-nav__dropdown-group-label">L&aelig;ring</div>
                        <a href="{{ route('learner.course') }}" class="fs-nav__dropdown-item">Mine kurs</a>
                        <a href="{{ route('learner.assignment') }}" class="fs-nav__dropdown-item">Oppgaver</a>
                        <a href="{{ route('learner.webinar') }}" class="fs-nav__dropdown-item">Webinarer</a>
                        <a href="{{ route('learner.shop-manuscript') }}" class="fs-nav__dropdown-item">Manusutviklinger</a>
                        <a href="{{ route('learner.workshop') }}" class="fs-nav__dropdown-item">Coaching</a>

                        <div class="fs-nav__dropdown-divider"></div>

                        <a href="{{ route('learner.profile') }}" class="fs-nav__dropdown-item">Profil</a>
                        <a href="{{ route('learner.invoice') }}" class="fs-nav__dropdown-item">Fakturaer</a>

                        <div class="fs-nav__dropdown-divider"></div>
                        <form method="POST" action="{{ route('auth.logout') }}">
                            @csrf
                            <button type="submit" class="fs-nav__logout-btn">Logg av</button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Hamburger (mobile) --}}
            <button class="fs-nav__hamburger" onclick="fsToggleMobile()" aria-label="Meny">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div class="fs-nav__mobile" id="fsMobileMenu">
        <a href="{{ route('front.course.index') }}" class="fs-nav__mobile-link">Kurs</a>
        <a href="{{ route('front.shop-manuscript.index') }}" class="fs-nav__mobile-link">Manusutvikling</a>
        <a href="{{ route('front.publishing') }}" class="fs-nav__mobile-link">Utgitte elever</a>
        <a href="{{ route('front.contact-us') }}" class="fs-nav__mobile-link">Om oss</a>
        @if (Auth::guest())
            <a href="{{ route('auth.login.show') }}" class="fs-nav__mobile-link">Logg inn</a>
        @endif
        <a href="{{ url('/arskurs') }}" class="fs-nav__mobile-cta">Årskurs 2026</a>
    </div>
</nav>

<script>
function fsToggleUserMenu(e) {
    e.preventDefault();
    e.stopPropagation();
    var dd = document.getElementById('fsUserDropdown');
    dd.classList.toggle('fs-dropdown--open');
}
function fsToggleMobile() {
    document.getElementById('fsMobileMenu').classList.toggle('fs-mobile--open');
}
document.addEventListener('click', function(e) {
    var dd = document.getElementById('fsUserDropdown');
    if (dd && !e.target.closest('.fs-nav__user-btn') && !e.target.closest('.fs-nav__dropdown')) {
        dd.classList.remove('fs-dropdown--open');
    }
});
</script>
