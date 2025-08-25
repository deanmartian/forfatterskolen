<nav id="learnerNav" class="navbar navbar-light">
    <a class="navbar-brand" href="javascript:void(0)" style="cursor: default">{{--{{url('')}}--}}
        <img data-src="https://www.forfatterskolen.no/images-new/logo11.png" alt="Forfatterskolen-logo">
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav">
        {{--<img src="" alt="Menyikon">--}}
        <i></i>
    </button>

    <div class="navbar navbar-default" style="">
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
            </ul>
        </div>
    </div>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" href="https://twitter.com/Forfatterrektor" target="_blank">
                <i class="sprite-social twitter"></i>
                {{--<img src="{{asset('images-new/social-icons/twitter.png')}}" class="social-image" alt="Twitter">--}}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="https://no.pinterest.com/forfatterskolen_norge/" target="_blank">
                <i class="sprite-social pinterest"></i>
                {{--<img src="{{asset('images-new/social-icons/pinterest.png')}}" class="social-image" alt="Pinterest">--}}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="https://www.instagram.com/forfatterskolen_norge/" target="_blank">
                <i class="sprite-social instagram"></i>
                {{--<img src="{{asset('images-new/social-icons/instagram.png')}}" class="social-image" alt="Instagram">--}}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="https://www.facebook.com/bliforfatter/" target="_blank">
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
</nav>

<div id="dashboard-menu">
    <div class="container">
        <div class="px-15">
            <a href="{{ route('learner.dashboard') }}" style="color: #fff">
                <h2 class="w-100">Kontrollpanel</h2>
            </a>
            <p class="float-left">
                Velkommen til Forfatterskolens portal
            </p>

            @if (Auth::user())
                <div class="float-right dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">
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
                            <a href="{{route('learner.dashboard')}}" class="dropdown-item">
                                Kontrollpanel
                            </a>
                            <a href="{{route('learner.course')}}" class="dropdown-item">Mine Kurs</a>
                            <a href="{{route('learner.shop-manuscript')}}" class="dropdown-item">Manuskripter</a>
                            <a href="{{route('learner.workshop')}}" class="dropdown-item">Workshops</a>
                            <a href="{{route('learner.webinar')}}" class="dropdown-item">Webinars</a>
                            <a href="{{route('learner.assignment')}}" class="dropdown-item">Oppgaver</a>
                            <a href="{{route('learner.calendar')}}" class="dropdown-item">Kalender</a>
                            <a href="{{route('learner.profile')}}" class="dropdown-item">Profil</a>
                            <a href="{{route('learner.invoice')}}" class="dropdown-item">Fakturaer</a>
                            <a href="{{url('/terms/all')}}" class="dropdown-item">Terms</a>
                            <a href="#" class="dropdown-item redirectForum">Forum</a>
                            <a href="#" class="dropdown-item pilotleser-link">Pilotleser</a>

                            <a href="{{ route('learner.change-portal', 'self-publishing') }}"
                                class="dropdown-item d-inline-block w-auto mb-2 btn btn-circle">
                                Selvpubliseringsportal
                            </a>

                            {{-- @if(Auth::user()->is_self_publishing_learner)
                                <a href="{{ route('learner.change-portal', 'self-publishing') }}"
                                class="dropdown-item d-inline-block w-auto mb-2 btn btn-circle">
                                    Selvpubliseringsportal
                                </a>
                            @else
                                @if(!FrontendHelpers::checkSelfPublishingPortalRequest(Auth::id()))
                                    <a href="{{ route('learner.request-self-publishing-portal') }}" class="dropdown-item d-inline-block w-auto mb-1">
                                        <form method="POST" action="{{route('learner.request-self-publishing-portal')}}" class="form-logout">
                                            {{csrf_field()}}
                                            <button type="submit" class="btn btn-circle">Get access</button>
                                        </form>
                                    </a>
                                @else
                                    <a href="#" class="dropdown-item d-inline-block w-auto mb-2 btn btn-circle">
                                        Pending Request
                                    </a>
                                @endif
                            @endif --}}

                            <a href="{{ route('auth.logout-get') }}" class="dropdown-item d-inline-block w-auto mb-2">
                                <form method="POST" action="{{route('auth.logout')}}" class="form-logout">
                                    {{csrf_field()}}
                                    <button type="submit" class="btn btn-circle">Logg av</button>
                                </form>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="container">
        @include('frontend.partials.learner-menu-new')
    </div>
</div>

<div id="mobile-learner-menu" class="navbar navbar-light">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNavMobile">
        <img src="" alt="Åpne meny">
    </button>

    <a class="navbar-brand mx-auto" href="{{url('')}}">
        <img src="{{asset('images-new/logo.png')}}" alt="Forfatterskolen-logo">
    </a>
</div>

<div class="navbar navbar-default2 mobile-learner-menu">
    <div class="navbar-collapse collapse" id="mainNavMobile">
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
                <li class="nav-item">
                    <a href="" class="dropdown-item d-inline-block w-auto mb-2">
                        <form method="POST" action="{{route('auth.logout')}}" class="form-logout">
                            {{csrf_field()}}
                            <button type="submit" class="btn">Logg av</button>
                        </form>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</div>

<div class="portal-menu">
    <button class="navbar-toggler text-center w-100" type="button" data-toggle="collapse" data-target="#portalNav">
        PORTAL MENY &nbsp;<i class="fa fa-chevron-down font-white"></i>
    </button>

    <div class="navbar-collapse collapse" id="portalNav">
        <div class="col-sm-4 col-xs-6 @if(!Request::is('account/course-webinar') && Request::is('account/course*')) active @endif">
            <div>
                <a href="{{route('learner.course')}}">
                    <i class="sprite-menu student-cap d-block"></i>
                    {!! trans('site.learner.nav.course') !!}
                </a>
            </div>
        </div>
        <div class="col-sm-4 col-xs-6 @if(Request::is('account/shop-manuscript*')) active @endif">
            <div>
                <a href="{{route('learner.shop-manuscript')}}">
                    <i class="sprite-menu file d-block"></i>
                    {!! trans('site.learner.nav.manuscript') !!}
                </a>
            </div>
        </div>
        <div class="col-sm-4 col-xs-6 @if(Request::is('account/workshop*')) active @endif">
            <div>
                <a href="{{route('learner.workshop')}}">
                    <i class="sprite-menu briefcase d-block"></i>
                    {!! trans('site.learner.nav.workshop') !!}
                </a>
            </div>
        </div>
        <div class="col-sm-4 col-xs-6 @if(Request::is('account/webinar*')) active @endif">
            <div>
                <a href="{{route('learner.webinar')}}">
                    <i class="sprite-menu play-button d-block"></i>
                    {!! trans('site.learner.nav.webinars') !!}
                </a>
            </div>
        </div>
        <div class="col-sm-4 col-xs-6 @if(Request::is('account/course-webinar*')) active @endif">
            <div>
                <a href="{{route('learner.course-webinar')}}">
                    <i class="sprite-menu play-button d-block"></i>
                    {!! trans('site.learner.nav.course-webinars') !!}
                </a>
            </div>
        </div>
        <div class="col-sm-4 col-xs-6 @if(Request::is('account/assignment*')) active @endif">
            <div>
                <a href="{{route('learner.assignment')}}">
                    <i class="sprite-menu agenda d-block"></i>
                    {!! trans('site.learner.nav.assignment') !!}
                </a>
            </div>
        </div>
        <div class="col-sm-4 col-xs-6 @if(Request::is('account/calendar*')) active @endif">
            <div>
                <a href="{{route('learner.calendar')}}">
                    <i class="sprite-menu calendar d-block"></i>
                    {!! trans('site.learner.nav.calendar') !!}
                </a>
            </div>
        </div>
        <div class="col-sm-4 col-xs-6 @if(Request::is('account/invoice*')) active @endif">
            <div>
                <a href="{{route('learner.invoice')}}">
                    <i class="sprite-menu list-on-window d-block"></i>
                    {!! trans('site.learner.nav.invoice') !!}
                </a>
            </div>
        </div>
        <div class="col-sm-4 col-xs-6 @if(Request::is('account/upgrade*')) active @endif">
            <div>
                <a href="{{route('learner.upgrade')}}">
                    <i class="sprite-menu internet d-block"></i>
                    {!! trans('site.learner.nav.upgrade') !!}
                </a>
            </div>
        </div>
        {{--<div class="col-sm-4 col-xs-6 @if(Request::is('account/competition*')) active @endif">
            <div>
                <a href="{{route('learner.competition')}}">
                    <i class="sprite-menu star d-block"></i>
                    Konkurranser
                </a>
            </div>
        </div>--}}
        <div class="col-sm-4 col-xs-6 @if(Request::is('account/private-message*')) active @endif">
            <div>
                <a href="{{route('learner.private-message')}}">
                    <i class="fa fa-comment fa-use-comment d-block"></i>
                    {!! trans('site.learner.nav.message') !!}
                </a>
            </div>
        </div>
        <div class="col-sm-4 col-xs-6 @if(Request::is('account/profile*')) active @endif">
            <div>
                <a href="{{route('learner.profile')}}">
                    <i class="sprite-menu user d-block"></i>
                    {!! trans('site.learner.nav.profile') !!}
                </a>
            </div>
        </div>
    </div>
</div>