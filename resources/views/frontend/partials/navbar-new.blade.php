<nav id="topNav" class="navbar navbar-expand-md navbar-light">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav">
        <img src="{{asset('images-new/menu.png')}}" alt="">
    </button>

    <a class="navbar-brand mx-auto" href="{{url('')}}">
        <img src="{{asset('images-new/logo.png')}}">
    </a>

    <div class="navbar-collapse collapse pr-0">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="#" style="color: #777">Velkommen til Forfatterskolen</a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="https://twitter.com/Forfatterrektor" target="_blank">
                    <img src="{{asset('images-new/social-icons/twitter.png')}}" class="social-image">
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="https://www.pinterest.ph/forfatterskolenofficial/" target="_blank">
                    <img src="{{asset('images-new/social-icons/pinterest.png')}}" class="social-image">
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="https://www.instagram.com/forfatterskolen_norge/" target="_blank">
                    <img src="{{asset('images-new/social-icons/instagram.png')}}" class="social-image">
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="https://www.facebook.com/bliforfatter/" target="_blank">
                    <img src="{{asset('images-new/social-icons/facebook.png')}}" class="social-image">
                </a>
            </li>
            @if (Auth::guest())
                <li class="nav-item divider-container">
                    <span class="nav-link divider">
                        &nbsp;
                    </span>
                </li>
            @endif
        </ul>
    </div>

    @if (Auth::guest())
        <ul class="navbar-nav login-nav">
            <li class="nav-item">
                <a class="nav-link login-link" href="{{route('auth.login.show')}}">
                    <span>Min Side</span>
                </a>
            </li>
        </ul>
    @endif
</nav>

<div class="navbar navbar-default navbar-expand-md">
    <div class="navbar-collapse collapse" id="mainNav">
        <ul class="navbar-nav nav-fill">
            <li class="nav-item @if(Route::currentRouteName() == 'front.course.index') active @endif">
                <a href="{{route('front.course.index')}}" class="nav-link">Våre Kurs</a>
            </li>
            <li class="nav-item @if(Route::currentRouteName() == 'front.shop-manuscript.index') active @endif">
                <a href="{{route('front.shop-manuscript.index')}}" class="nav-link">Manusutvikling</a>
            </li>
            <li class="nav-item @if(Route::currentRouteName() == 'front.publishing') active @endif">
                <a href="{{ route('front.publishing') }}" class="nav-link">Utgitte Elever</a>
            </li>
            <li class="nav-item @if(Route::currentRouteName() == 'front.blog') active @endif">
                <a href="{{ route('front.blog') }}" class="nav-link">Blogg</a>
            </li>
            <li class="nav-item @if(Route::currentRouteName() == 'front.workshop.index') active @endif">
                <a href="{{route('front.workshop.index')}}" class="nav-link">Workshop</a>
            </li>
            <li class="nav-item @if(Route::currentRouteName() == 'front.faq') active @endif">
                <a href="{{route('front.faq')}}" class="nav-link">FAQ</a>
            </li>
            <li class="nav-item @if(Route::currentRouteName() == 'front.contact-us') active @endif">
                <a href="{{route('front.contact-us')}}" class="nav-link">Kontakt Oss</a>
            </li>

            @if (Auth::user())
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">
                        Hei {{Auth::user()->first_name}}
                        &nbsp;<span class="nav-user-thumb" style="background-image: url('{{Auth::user()->profile_image}}')"></span>
                    </a>
                    <div class="dropdown-menu">
                        <a href="{{route('learner.course')}}" class="dropdown-item">Mine Kurs</a>
                        <a href="{{route('learner.shop-manuscript')}}" class="dropdown-item">Manuskripter</a>
                        <a href="{{route('learner.workshop')}}" class="dropdown-item">Workshops</a>
                        <a href="{{route('learner.webinar')}}" class="dropdown-item">Webinars</a>
                        <a href="{{route('learner.assignment')}}" class="dropdown-item">Oppgaver</a>
                        <a href="{{route('learner.calendar')}}" class="dropdown-item">Kalender</a>
                        <a href="{{route('learner.profile')}}" class="dropdown-item">Profil</a>
                        <a href="{{route('learner.invoice')}}" class="dropdown-item">Fakturaer</a>

                        <a href="" class="dropdown-item">
                            <form method="POST" action="{{route('auth.logout')}}" class="form-logout">
                                {{csrf_field()}}
                                <button type="submit" class="btn">Logg av</button>
                            </form>
                        </a>
                    </div>
                </li>
            @endif
        </ul>
    </div>
</div>