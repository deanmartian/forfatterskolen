@if( Route::currentRouteName() != 'front.free-manuscript.index')
    @if (Auth::guest())
        <div class="start-today text-center" data-bg="https://www.forfatterskolen.no/images-new/cta.png">
            <h5 class="font-regular">
                {{ trans('site.front.start-today.details') }}
            </h5>
            <div></div>
            <a class="btn" href="/gratis-tekstvurdering" title="Free text assessment">
                {{ trans('site.front.start-today.button-text') }}
            </a>
            {{--<div>
                <small>Du får svar innen fem virkedager</small>
            </div>--}}
        </div>
    @endif
@endif

<footer>
    <div class="navbar navbar-inverse navbar-fixed-bottom navbar-expand-md">
        <a class="navbar-brand mx-auto" href="{{url('')}}">
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#footer-body">
            <img src="{{asset('images-new/menu.png')}}" alt="mobile menu toggle">
        </button>

        <div class="navbar-collapse collapse" id="footer-body">
            {{--<ul class="nav navbar-nav w-100" id="footer-options">
                <li class="nav-item"><a href="#" class="nav-link">Om oss</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Skriveblogg</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Kontakt oss</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Logg inn</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Register deg her tt</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Kjop kurs</a></li>
            </ul>--}}

            <ul class="navbar-nav nav-fill" id="footer-options">
                <li class="nav-item @if(Route::currentRouteName() == 'front.course.index') active @endif">
                    <a href="{{route('front.course.index')}}" class="nav-link">Våre Kurs</a>
                </li>
                <li class="nav-item @if(Route::currentRouteName() == 'front.shop-manuscript.index') active @endif">
                    <a href="{{route('front.shop-manuscript.index')}}" class="nav-link">Manusutvikling</a>
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

            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="https://twitter.com/Forfatterrektor" target="_blank">
                        <i class="sprite-social twitter-white"></i>
                        {{--<img src="{{asset('images-new/social-icons/twitter-white.png')}}" class="social-image" alt="Twitter">--}}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://no.pinterest.com/forfatterskolen_norge/" target="_blank">
                        <i class="sprite-social pinterest-white"></i>
                        {{--<img src="{{asset('images-new/social-icons/pinterest-white.png')}}" class="social-image" alt="Pinterest">--}}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://www.instagram.com/forfatterskolen_norge/" target="_blank">
                        <i class="sprite-social instagram-white"></i>
                        {{--<img src="{{asset('images-new/social-icons/instagram-white.png')}}" class="social-image" alt="Instagram">--}}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://www.facebook.com/bliforfatter/" target="_blank">
                        <i class="sprite-social facebook-white"></i>
                        {{--<img src="{{asset('images-new/social-icons/facebook-white.png')}}" class="social-image" alt="Facebook">--}}
                    </a>
                </li>
            </ul>
        </div>
    </div> <!-- end navbar -->

    <div class="footer-info">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-xl-3 col-lg-4 col-md-4">
                    <img data-src="https://www.forfatterskolen.no/images-new/marker.png" alt="Adresseikon">
                    <h2 class="mt-4">Adresse</h2>
                    <p class="mt-4">
                        Postboks 9233, 3028 Drammen
                    </p>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-4">
                    <img data-src="https://www.forfatterskolen.no/images-new/email-envelope.png" alt="envelope icon">
                    <div class="mt-4 h2">E-post</div>
                    <p class="mt-4">
                        post@forfatterskolen.no
                    </p>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-4">
                    <img data-src="https://www.forfatterskolen.no/images-new/telephone.png" alt="Telefonikon">
                    <div class="mt-4 h2">Kontakt Telefon</div>
                    <p class="mt-4">
                        +47 411 23 555
                    </p>
                </div>
            </div>
        </div> <!-- end container -->
    </div> <!-- end footer-info -->

    <div class="col-sm-12 footer-bottom text-center">
        <p>
            Copyright &copy; 2016 Forfatterskolen, All Rights Reserved |
            <a href="{{ route('front.terms', 'all') }}" class="color-white">Vilkår</a>
        </p>
    </div>
</footer>