<nav id="navbar-latest" class="navbar navbar-expand-md">
    <div class="container">
        <!-- Logo or Brand -->
        <a class="navbar-brand" href="{{ route('front.home') }}" style="position: relative">
            {{-- <img src="{{asset('images-new/logo11.png')}}" alt="Your Logo"> --}}
            <svg xmlns="http://www.w3.org/2000/svg" width="43" height="41" viewBox="0 0 43 41" fill="none">
                <path d="M0 0L21.5 2.90538V41L0 36.6077V0Z" fill="#E73946"/>
                <path d="M43 0L21.5 2.90612V40.9983L43 36.3185V0Z" fill="#852636"/>
            </svg>
            <span>
                FORFATTERSKOLEN
            </span>
        </a>

        <!-- Toggler/collapsibe Button -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Items -->
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav nav-fill">
                <li class="nav-item @if(Route::currentRouteName() == 'front.course.index') active @endif">
                    <a href="{{route('front.course.index')}}" class="nav-link"
                       title="View courses">{{ trans('site.front.nav.course') }}</a>
                </li>
                <li class="nav-item @if(Route::currentRouteName() == 'front.shop-manuscript.index') active @endif">
                    <a href="{{route('front.shop-manuscript.index')}}" class="nav-link"
                       title="View manuscripts">{{ trans('site.front.nav.manuscript') }}</a>
                </li>
                <li class="nav-item @if(Route::currentRouteName() == 'front.publishing') active @endif">
                    <a href="{{ route('front.publishing') }}" class="nav-link"
                       title="View publishing">{{ trans('site.front.nav.publishing') }}</a>
                </li>
                <li class="nav-item">
                    <a href="https://arskurs.forfatterskolen.no/" class="nav-link"
                       title="Årskurs" target="_blank">Årskurs</a>
                </li>
                <li class="nav-item">
                    <a href="https://blog.forfatterskolen.no" class="nav-link">
                        Blogg</a>
                </li>
                <li class="nav-item @if(Route::currentRouteName() == 'front.contact-us') active @endif">
                    <a href="{{route('front.contact-us')}}" class="nav-link"
                       title="View contact page">{{ trans('site.front.nav.contact-us') }}</a>
                </li>
                @if (Auth::guest())
                    <li class="nav-item">
                        <a class="nav-link login-link" href="{{route('auth.login.show')}}" title="View login page">
                            {{-- <span>Min Side</span> --}}
                            <span>{{ trans('site.front.form.login') }}</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" title="Toggle user drop-down">
                            <span class="nav-user-thumb mr-2" style="background-image: url('{{Auth::user()->profile_image}}')"></span>
                            Hei {{Auth::user()->first_name}}
                        </a>

                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="account-details">
                                <div class="row align-items-center mx-0">
                                    <div class="col-sm-4 text-center">
                                        <span class="user-thumb mr-2" style="background-image: url('{{Auth::user()->profile_image}}')"></span>
                                    </div>
                                    <div class="col-sm-8 info">
                                        <p>{{ ucfirst(Auth::user()->first_name)}} <br>
                                            {{Auth::user()->email}}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="link-container">
                                <a href="{{route('learner.course')}}" class="dropdown-item"
                                   title="View learner course">Mine Kurs</a>
                                <a href="{{route('learner.shop-manuscript')}}" class="dropdown-item"
                                   title="View learner manuscripts">Manuskripter</a>
                                <a href="{{route('learner.workshop')}}" class="dropdown-item"
                                   title="View learner workshops">Workshops</a>
                                <a href="{{route('learner.webinar')}}" class="dropdown-item"
                                   title="View learner webinars">Webinars</a>
                                <a href="{{route('learner.assignment')}}" class="dropdown-item"
                                   title="View learner assignments">Oppgaver</a>
                                <a href="{{route('learner.calendar')}}" class="dropdown-item"
                                   title="View learner calendar">Kalender</a>
                                <a href="{{route('learner.profile')}}" class="dropdown-item"
                                   title="View learner profile">Profil</a>
                                <a href="{{route('learner.invoice')}}" class="dropdown-item"
                                   title="View learner invoices">Fakturaer</a>
    
                                <a href="{{ route('auth.logout-get') }}" class="dropdown-item d-inline-block w-auto mb-2" title="Logout">
                                    <form method="POST" action="{{route('auth.logout')}}" class="form-logout">
                                        {{csrf_field()}}
                                        <button type="submit" class="btn btn-circle">Logg av</button>
                                    </form>
                                </a>
                            </div>
                        </div>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>