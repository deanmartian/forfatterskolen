<div class="cta">
    <div class="container">
        <div class="row">
            <div class="col-sm-7">
                <div class="h1 font-montserrat-light">
                    Vil du ha profesjonell tilbakemelding på en smakebit av din personlige tekst, helt gratis? Send den inn ved
                    å trykke på knappen under.
                </div>

                <a class="btn" href="/gratis-tekstvurdering" title="Free text assessment">
                    {{ trans('site.front.i-want-this') }}
                </a>
            </div>
        </div>
    </div>
</div>

<footer id="home-footer">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-sm-7">
                <img data-src="https://www.forfatterskolen.no/{{'images-new/home/logo_2.png'}}" class="logo" alt="Forfatterskolen-logo">
                <p class="mt-5">
                    <i class="icons marker"></i><span class="font-montserrat-semibold text-uppercase">Adresse:</span>
                    <span class="font-montserrat-light">Lihagen 21, 3029 DRAMMEN</span>
                </p>

                <p>
                    <i class="icons envelope"></i><span class="font-montserrat-semibold text-uppercase">E-post:</span>
                    <span class="font-montserrat-light">post@forfatterskolen.no</span>
                </p>

                {{--<p>
                    <i class="icons telephone"></i><span class="font-montserrat-semibold text-uppercase">Kontakt Telefon:</span>
                    <span class="font-montserrat-light">+47 411 23 555</span>
                </p>--}}

                <p>
                    <a href="https://twitter.com/Forfatterrektor" target="_blank" class="ml-0 mr-3"
                       title="View twitter page">
                        <i class="sprite-social twitter"></i>
                    </a>
                    <a href="https://no.pinterest.com/forfatterskolen_norge/" target="_blank" class="mr-3"
                       title="View pinterest page">
                        <i class="sprite-social pinterest"></i>
                    </a>
                    <a href="https://www.instagram.com/forfatterskolen_norge/" target="_blank" class="mr-3"
                       title="View instagram page">
                        <i class="sprite-social instagram"></i>
                    </a>
                    <a href="https://www.facebook.com/bliforfatter//" target="_blank" class="mr-3"
                       title="View facebook page">
                        <i class="sprite-social facebook"></i>
                    </a>
                    <a href="{{ url('/auth/login') }}" class="login-link" title="View login page">Login</a>
                </p>

                <p class="copyright">
                    Copyright © 2016 Forfatterskolen, All Rights Reserved |
                    <a href="{{ route('front.terms', 'all') }}" class="color-white" title="View terms">Vilkår</a>
                </p>
            </div>
            {{--<div class="col-sm-6 right-container">
            </div>--}}
        </div>
    </div>
</footer>