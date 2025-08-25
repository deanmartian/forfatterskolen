<nav id="topNav" class="navbar navbar-expand-md navbar-light">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav">
        <img src="{{asset('images-new/menu.png')}}" alt="mobile menu toggle">
    </button>

    <a class="navbar-brand mx-auto" href="{{url('')}}" title="View front page">
        <img src="https://www.forfatterskolen.no/{{'images-new/logo_new_black.png'}}" alt="logo with tagline" style="height: 67px; margin-top: 20px">
        {{--asset('images-new/logo-tagline.png')--}}
    </a>

    <div class="navbar-collapse collapse pr-0">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="#"  title="View front page" style="color: #000">Velkommen til Forfatterskolen</a> {{--style="color: #000"--}}
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="https://twitter.com/Forfatterrektor" target="_blank"
                   title="View twitter page">
                    <i class="sprite-social twitter"></i>
                    {{--<img src="{{asset('images-new/social-icons/twitter.png')}}" class="social-image" alt="Twitter">--}}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="https://no.pinterest.com/forfatterskolen_norge/" target="_blank"
                   title="View pinterest page">
                    <i class="sprite-social pinterest"></i>
                    {{--<img src="{{asset('images-new/social-icons/pinterest.png')}}" class="social-image" alt="Pinterest">--}}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="https://www.instagram.com/forfatterskolen_norge/" target="_blank"
                    title="View instagram page">
                    <i class="sprite-social instagram"></i>
                    {{--<img src="{{asset('images-new/social-icons/instagram.png')}}" class="social-image" alt="Instagram">--}}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="https://www.facebook.com/bliforfatter/" target="_blank"
                    title="View facebook page">
                    <i class="sprite-social facebook"></i>
                    {{--<img src="{{asset('images-new/social-icons/facebook.png')}}" class="social-image" alt="Facebook">--}}
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
                <a class="nav-link login-link" href="{{route('auth.login.show')}}" title="View login page">
                    {{-- <span>Min Side</span> --}}
                    <span>{{ trans('site.front.form.login') }}</span>
                </a>
            </li>

            {{-- <li class="nav-item">
                <a class="nav-link login-link" href="{{route('auth.login.self-publishing-show')}}" title="View login page">
                    <span>Selvpubliseringsportal</span>
                </a>
            </li> --}}
        </ul>
    @endif
</nav>

<div class="navbar navbar-default navbar-expand-md" id="mainSiteNav">
    <div class="navbar-collapse collapse" id="mainNav">
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
            {{--<li class="nav-item @if(Route::currentRouteName() == 'front.blog') active @endif">
                <a href="{{ route('front.blog') }}" class="nav-link"
                   title="View blog">{{ trans('site.front.nav.blog') }}</a>
            </li>--}}
            {{--<li class="nav-item @if(Route::currentRouteName() == 'front.workshop.index') active @endif">
                <a href="{{route('front.workshop.index')}}" class="nav-link"
                   title="View workshops">{{ trans('site.front.nav.workshop') }}</a>
            </li>--}}
            <li class="nav-item">
                <a href="https://romankurs.no" class="nav-link"
                   title="Årskurs" target="_blank">Årskurs</a>
            </li>
            <li class="nav-item">
                <a href="https://blog.forfatterskolen.no" class="nav-link">
                    Blogg</a>
            </li>
            {{--<li class="nav-item @if(Route::currentRouteName() == 'front.faq') active @endif">
                <a href="{{route('front.faq')}}" class="nav-link"
                   title="View faqs">{{ trans('site.front.nav.faq') }}</a>
            </li>--}}
            <li class="nav-item @if(Route::currentRouteName() == 'front.contact-us') active @endif">
                <a href="{{route('front.contact-us')}}" class="nav-link"
                   title="View contact page">{{ trans('site.front.nav.contact-us') }}</a>
            </li>

            {{--<li class="nav-item @if(Route::currentRouteName() == 'front.gift-cards') active @endif">
                <a href="{{route('front.gift-cards')}}" class="nav-link">
                    Gift Cards
                </a>
            </li>--}}

            @if (Auth::user())
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