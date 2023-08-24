<footer id="home-footer-new">
    <div class="container">
        <div class="row mb-5">
            <div class="col-md-6">
                <img data-src="https://www.forfatterskolen.no/{{'images-new/home/logo_2.png'}}" class="logo"
                     alt="new footer logo">
            </div>
            <div class="col-md-6">
                <div class="col-sm-4">
                    <p>
                        Hva vil tilbyr
                    </p>

                    <ul>
                        <li>
                            <a href="{{route('front.course.index')}}" class="nav-link" 
                            title="View courses">
                                {{ trans('site.front.nav.course') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{route('front.shop-manuscript.index')}}" class="nav-link"
                            title="View manuscripts">
                                {{ trans('site.front.nav.manuscript') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('front.free-manuscript.index') }}" class="nav-link"
                            title="View manuscripts">
                                Gratis tilbakemelding
                            </a>
                        </li>
                        <li>
                            <a href="{{route('front.course.show', 17)}}" class="nav-link"
                            title="Mentormøter">
                                Mentormøter
                            </a>
                        </li>
                        <li>
                            <a href="https://indiemoon.no" class="nav-link"
                            title="Indiepublisering">
                                Indiepublisering
                            </a>
                        </li>
                        <li>
                            <a href="https://rskolen.no" class="nav-link"
                            title="Redaktørskolen">
                                Redaktørskolen
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-4">
                    <p>
                        Informasjon
                    </p>

                    <ul>
                        <li>
                            <a href="{{ route('front.contact-us') }}" class="nav-link"
                            title="Hvem er vi">
                                Hvem er vi
                            </a>
                        </li>
                        <li>
                            <a href="/terms/all" class="nav-link"
                            title="Vilkår og betingelser">
                                Vilkår og betingelser
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-4">
                    <p>
                        Sosiale medier
                    </p>

                    <ul>
                        <li>
                            <a class="nav-link" href="https://www.facebook.com/bliforfatter/" target="_blank">
                                Facebook
                            </a>
                        </li>
                        <li>
                            <a class="nav-link" href="https://www.instagram.com/forfatterskolen_norge/" target="_blank">
                                Instagram
                            </a>
                        </li>
                        <li>
                            <a class="nav-link" href="https://twitter.com/Forfatterrektor" target="_blank">
                                Twitter
                            </a>
                        </li>
                        <li>
                            <a class="nav-link" href="https://no.pinterest.com/forfatterskolen_norge/" target="_blank">
                                Pinterest
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div> <!-- end row first-row -->

        <div class="clearfix"></div>

        <div class="row mt-5">
            <div class="col-md-3">
                <p>Adresse</p>

                <h2>
                    Lihagen 21, 3029 DRAMMEN
                </h2>
            </div>
            <div class="col-md-3">
                <p>
                    E-post
                </p>

                <h2>
                    post@forfatterskolen.no
                </h2>
            </div>
            <div class="col-md-3">
                <p>
                    Telefon
                </p>

                <h2>
                    +47 411 23 555
                </h2>
            </div>
            <div class="col-md-3 text-right justify-content-center d-flex">
                <button class="btn site-btn-global" data-toggle="modal" data-target="#writingPlanModal">
                    Meld meg på nyhetsbrev
                </button>
            </div>
        </div> <!-- end row -->

        <div class="row footer-bottom pb-0 mt-5">
            <div class="col-md-12">
                <p>
                    Copyright © 2022 Forfatterskolen, All Rights Reserved
                </p>
            </div>
        </div>
    </div>
</footer>