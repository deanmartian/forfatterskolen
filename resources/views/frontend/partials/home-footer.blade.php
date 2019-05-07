<div class="cta">
    <div class="container">
        <div class="row">
            <div class="col-sm-7">
                <div class="h1 font-montserrat-light">
                    Vil du ha profesjonell tilbakemelding på en smakebit av din personlige tekst, helt gratis? Send den inn ved
                    å trykke på knappen under.
                </div>

                <a class="btn" href="/gratis-tekstvurdering">{{ trans('site.front.i-want-this') }}</a>
            </div>
        </div>
    </div>
</div>

<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-sm-7">
                <img src="{{ asset('images-new/home/logo_small.png') }}" class="logo">
                <p class="mt-5">
                    <i class="icons marker"></i><span class="font-montserrat-semibold text-uppercase">Adresse:</span>
                    <span class="font-montserrat-light">Postboks 9233, 3028 Drammen</span>
                </p>

                <p>
                    <i class="icons envelope"></i><span class="font-montserrat-semibold text-uppercase">E-post:</span>
                    <span class="font-montserrat-light">post@forfatterskolen.no</span>
                </p>

                <p>
                    <i class="icons telephone"></i><span class="font-montserrat-semibold text-uppercase">Kontakt Telefon:</span>
                    <span class="font-montserrat-light">+47 411 23 555</span>
                </p>

                <p>
                    <a href="https://twitter.com/Forfatterrektor" target="_blank" class="ml-0 mr-3">
                        <i class="sprite-social twitter"></i>
                    </a>
                    <a href="https://no.pinterest.com/forfatterskolen_norge/" target="_blank" class="mr-3">
                        <i class="sprite-social pinterest"></i>
                    </a>
                    <a href="https://www.instagram.com/forfatterskolen_norge/" target="_blank" class="mr-3">
                        <i class="sprite-social instagram"></i>
                    </a>
                    <a href="https://www.pinterest.ph/forfatterskolenofficial/" target="_blank" class="mr-3">
                        <i class="sprite-social facebook"></i>
                    </a>
                    <a href="{{ url('/auth/login') }}" class="login-link">Login</a>
                </p>

                <p class="copyright">
                    Copyright © 2016 Forfatterskolen, All Rights Reserved |
                    <a href="{{ route('front.terms', 'all') }}" class="color-white">Vilkår</a>
                </p>
            </div>
            {{--<div class="col-sm-6 right-container">
            </div>--}}
        </div>
    </div>
</footer>