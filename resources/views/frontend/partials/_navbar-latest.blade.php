<style>
/* ── NAVBAR REDESIGN — scoped under .fs-nav ── */
.fs-nav {
    --fs-wine: #862736;
    --fs-wine-hover: #9c2e40;
    --fs-text: #1a1a1a;
    --fs-text-sec: #5a5550;
    --fs-text-muted: #8a8580;
    --fs-border: rgba(0, 0, 0, 0.08);
    --fs-nav-h: 64px;
    --fs-font: 'Source Sans 3', -apple-system, sans-serif;
    background: #fff;
    border-bottom: 1px solid var(--fs-border);
    position: sticky;
    top: 0;
    z-index: 1030;
    font-family: var(--fs-font);
}
.fs-nav__inner {
    max-width: 1180px;
    margin: 0 auto;
    height: var(--fs-nav-h);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 1.5rem;
}
/* Logo */
.fs-nav__logo {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    flex-shrink: 0;
}
.fs-nav__logo svg { height: 30px; width: auto; }
.fs-nav__logo span {
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 3px;
    color: var(--fs-text);
    text-transform: uppercase;
}
/* Nav links */
.fs-nav__links {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    list-style: none;
    margin: 0;
    padding: 0;
}
.fs-nav__link {
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--fs-text-sec);
    padding: 0.5rem 0.85rem;
    border-radius: 6px;
    transition: color 0.15s, background 0.15s;
    white-space: nowrap;
}
.fs-nav__link:hover {
    color: var(--fs-text);
    background: rgba(0, 0, 0, 0.03);
    text-decoration: none;
}
.fs-nav__link.fs-active {
    color: var(--fs-text);
    font-weight: 600;
}
/* Right side */
.fs-nav__right {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
/* CTA */
.fs-nav__cta {
    display: inline-flex;
    align-items: center;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    color: #fff;
    background: var(--fs-wine);
    padding: 0.5rem 1.1rem;
    border-radius: 6px;
    transition: background 0.2s, transform 0.1s;
    white-space: nowrap;
}
.fs-nav__cta:hover {
    background: var(--fs-wine-hover);
    transform: translateY(-1px);
    color: #fff;
    text-decoration: none;
}
/* User dropdown */
.fs-nav__user-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.35rem 0.75rem 0.35rem 0.35rem;
    border-radius: 20px;
    border: 1px solid var(--fs-border);
    cursor: pointer;
    transition: border-color 0.15s, background 0.15s;
    text-decoration: none;
    color: var(--fs-text);
    background: transparent;
    position: relative;
}
.fs-nav__user-btn:hover {
    border-color: rgba(0, 0, 0, 0.15);
    background: rgba(0, 0, 0, 0.02);
    text-decoration: none;
    color: var(--fs-text);
}
.fs-nav__avatar {
    width: 28px; height: 28px;
    border-radius: 50%;
    background: var(--fs-wine);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    font-weight: 600;
    color: #fff;
    overflow: hidden;
}
.fs-nav__avatar img {
    width: 100%; height: 100%;
    object-fit: cover;
    border-radius: 50%;
}
.fs-nav__user-name {
    font-size: 0.825rem;
    font-weight: 500;
    color: var(--fs-text);
}
.fs-nav__user-arrow {
    font-size: 0.65rem;
    color: var(--fs-text-muted);
    margin-left: 0.15rem;
}
/* Dropdown menu */
.fs-nav__dropdown {
    display: none;
    position: absolute;
    top: calc(100% + 0.5rem);
    right: 0;
    background: #fff;
    border: 1px solid var(--fs-border);
    border-radius: 10px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.1);
    min-width: 220px;
    z-index: 1050;
    padding: 0.5rem 0;
    overflow: hidden;
}
.fs-nav__dropdown.fs-dropdown--open { display: block; }
.fs-nav__dropdown-header {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid var(--fs-border);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.fs-nav__dropdown-header .fs-nav__avatar { width: 36px; height: 36px; font-size: 0.8rem; }
.fs-nav__dropdown-info { font-size: 0.8rem; color: var(--fs-text-sec); line-height: 1.3; }
.fs-nav__dropdown-info strong { color: var(--fs-text); font-weight: 600; display: block; }
.fs-nav__dropdown a.fs-nav__dropdown-item {
    display: block;
    padding: 0.45rem 1rem;
    font-size: 0.85rem;
    color: var(--fs-text-sec);
    text-decoration: none;
    transition: background 0.1s;
}
.fs-nav__dropdown a.fs-nav__dropdown-item:hover {
    background: rgba(0,0,0,0.03);
    color: var(--fs-text);
}
.fs-nav__dropdown-group-label {
    font-size: 0.6rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #8a8580;
    padding: 0.5rem 1rem 0.25rem;
}
.fs-nav__dropdown-divider {
    height: 1px;
    background: var(--fs-border);
    margin: 0.25rem 0;
}
.fs-nav__dropdown .fs-nav__logout-btn {
    background: none;
    border: none;
    color: var(--fs-wine);
    font-size: 0.85rem;
    font-weight: 500;
    cursor: pointer;
    padding: 0.45rem 1rem;
    width: 100%;
    text-align: left;
    font-family: var(--fs-font);
}
.fs-nav__dropdown .fs-nav__logout-btn:hover { background: rgba(0,0,0,0.03); }
/* Login link (guest) */
.fs-nav__login {
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 500;
    color: var(--fs-text-sec);
    padding: 0.5rem 0.85rem;
    border-radius: 6px;
    transition: color 0.15s, background 0.15s;
    white-space: nowrap;
}
.fs-nav__login:hover {
    color: var(--fs-text);
    background: rgba(0,0,0,0.03);
    text-decoration: none;
}
/* Hamburger */
.fs-nav__hamburger {
    display: none;
    flex-direction: column;
    justify-content: center;
    gap: 5px;
    width: 36px; height: 36px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 6px;
}
.fs-nav__hamburger span {
    display: block; height: 2px;
    background: var(--fs-text);
    border-radius: 1px;
    transition: transform 0.2s, opacity 0.2s;
}
/* Mobile menu */
.fs-nav__mobile {
    display: none;
    background: #fff;
    border-bottom: 1px solid var(--fs-border);
    padding: 0.75rem 1.5rem 1.25rem;
}
.fs-nav__mobile.fs-mobile--open { display: block; }
.fs-nav__mobile-link {
    display: block;
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 500;
    color: var(--fs-text-sec);
    padding: 0.65rem 0;
    border-bottom: 1px solid var(--fs-border);
    transition: color 0.15s;
}
.fs-nav__mobile-link:last-of-type { border-bottom: none; }
.fs-nav__mobile-link:hover { color: var(--fs-text); text-decoration: none; }
.fs-nav__mobile-cta {
    display: block;
    text-align: center;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 600;
    color: #fff;
    background: var(--fs-wine);
    padding: 0.7rem;
    border-radius: 6px;
    margin-top: 0.75rem;
}
.fs-nav__mobile-cta:hover { color: #fff; text-decoration: none; background: var(--fs-wine-hover); }

/* Responsive */
@media (max-width: 820px) {
    .fs-nav__links { display: none; }
    .fs-nav__cta { display: none; }
    .fs-nav__hamburger { display: flex; }
}
</style>

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
