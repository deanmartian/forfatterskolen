<div id="writingPlanModal" class="modal fade global-modal" role="dialog"> {{-- no-header-modal --}}
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">
                    <h3>Gratis skrivetips</h3>                    
                    <p>
                        Rektor sine 66 beste tips 
                    </p>
                </div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="padding: 30px"> {{-- main-form --}}

                <div class="form-container">
                    <div class="form-group">
                        <label>
                            {{ trans('site.first-name') }}
                        </label>

                        <input type="text" name="name" class="form-control"
                               required value="{{old('name')}}">
                    </div>
                    {{-- <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa user-icon"></i></span>
                        </div>
                        <input type="text" name="name" class="form-control no-border-left"
                               placeholder="Fornavn" required value="{{old('name')}}">
                    </div> --}}

                    <div class="form-group">
                        <label>
                            {{ trans('site.front.form.email') }}
                        </label>

                        <input type="email" name="email"
                               class="form-control" required>
                    </div>
                    {{-- <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa email-icon"></i></span>
                        </div>
                        <input type="email" name="email" placeholder="Epost"
                               class="form-control no-border-left" required>
                    </div> --}}

                    <div class="row options-row">
                        <div class="col-md-6">
                            <div class="custom-checkbox">
                                <input type="checkbox" name="terms" id="terms" required>
                                <?php
                                $search_string = [
                                    '[start_link]', '[end_link]'
                                ];
                                $replace_string = [
                                    '<a href="'.route('front.opt-in-terms').'" title="View front page terms">','</a>'
                                ];
                                $terms_link = str_replace($search_string, $replace_string, trans('site.front.accept-terms'))
                                ?>
                                <label for="terms">{!! $terms_link !!}</label>
                            </div>

                            <em>
                                {{ trans('site.front.main-form.note') }}
                            </em>
                        </div>

                        <div class="col-md-6">
                            {!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::renderJS() !!}
                            {!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::display(['data-callback' => 'captchaCB']) !!}
                        </div>
                    </div>

                    <input type="hidden" name="captcha" value="">

                    <div class="btn-container text-right" style="margin-top: 20px">
                        <button type="button" class="btn submit-btn w-100" onclick="submitWritingPlan(this)">
                            {{ trans('site.front.main-form.submit-text') }}
                        </button>
                    </div>

                    <div class="alert alert-danger no-bottom-margin mt-3 d-none">
                        <ul>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<footer id="home-footer-new">
    <div class="container">
        <div class="row mb-5">
            <div class="col-md-6">
                <img data-src="https://www.forfatterskolen.no/{{'images-new/home/logo_2.png'}}" class="logo" alt="Forfatterskolen-logo">
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