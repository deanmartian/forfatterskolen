@extends('frontend.layout')

@section('title')
    <title>Forfatterskolen &rsaquo; Kontakt Oss</title>
@stop

@section('content')
    <div class="contact-page" >
        <div class="header text-center" data-bg="https://www.forfatterskolen.no/images-new/contact-main-bg.jpg">
            <h1>
                {{ trans('site.front.nav.contact-us') }}
            </h1>
        </div>

        <div class="container">
            <div class="row main-container">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2 text-center">
                            <div class="editor-circle">
                                <img data-src="https://www.forfatterskolen.no/images-new/kristine1.png" alt="" class="rounded-circle">
                            </div>
                            <i>{{ trans('site.front.contact-us.main-container-photo-text') }}</i>
                        </div>
                        <div class="col-md-10 first-description">
                            <h2>
                                {{ trans('site.front.contact-us.first-description') }}
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 description-container">
                    {!! trans('site.front.contact-us.main-description') !!}
                </div> <!-- end description-container -->

            </div> <!-- end main-container -->

            @if($hasAdvisory)
                <div class="row advisory text-center">
                    <div class="col-sm-12">
                        <h2>{{ $advisory->advisory }}</h2>
                    </div>
                </div>
            @endif

            <div class="row secondary-container">
                <div class="col-md-6">
                    <div class="h1">{{ trans('site.front.contact-us.our-staff') }}</div>
                    <div class="row stab-row">
                        <ul>
                            @foreach(\App\Http\FrontendHelpers::getStaffs() as $staff)
                                <li>
                                    <div class="row">
                                        <div class="col-sm-2 stab-image">
                                            <img data-src="https://www.forfatterskolen.no/{{ ($staff->image ? $staff->image : 'images/user.png')  }}" class="rounded-circle">
                                        </div>
                                        <div class="col-sm-10">
                                            <h2>
                                                {{ $staff->name }}
                                            </h2>

                                            <p>
                                                {!! $staff->details !!}
                                            </p>

                                            <i class="fa fa-envelope"></i>
                                            <a href="mailto:{{ $staff->email }}">{{ $staff->email }}</a>
                                            <br>
                                            @if ($staff->teamviewer)
                                                <i class="sprite team-viewer"></i>
                                                <a href="{{ $staff->teamviewer }}">Fjernsupport</a>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="h1">{{ trans('site.front.contact-us.contact-us-today') }}</div>
                    <div class="row contact-row"data-bg="https://www.forfatterskolen.no/images-new/contact-bg.png">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-sm-2 contact-image">
                                    <img data-src="https://www.forfatterskolen.no/images-new/kristine1.png" class="rounded-circle">
                                </div>
                                <div class="col-sm-10">
                                    <h2 class="author">
                                        KRISTINE STORLI HENNINGSEN
                                    </h2>
                                    <p class="author">
                                        Rektor
                                    </p>
                                </div>
                            </div>

                            <div class="row contact-info-container">
                                <div class="col-md-10 pl-0">
                                    <p>
                                        <i class="sprite marker"></i>
                                        <span>Postboks 9233, 3028 Drammen</span>
                                    </p>
                                    <p>
                                        <i class="fa fa-envelope"></i>
                                        <a href="mailto:post@forfatterskolen.no" class="theme-text">post@forfatterskolen.no</a>
                                    </p>
                                    {{--<p>
                                        <i class="sprite telephone"></i>
                                        <span>+47 411 23 555</span>
                                    </p>--}}
                                    <p>
                                        <a href="https://twitter.com/Forfatterrektor" target="_blank" class="ml-0">
                                            <i class="sprite-social twitter"></i>
                                        </a>
                                        <a href="https://no.pinterest.com/forfatterskolen_norge/" target="_blank">
                                            <i class="sprite-social pinterest"></i>
                                        </a>
                                        <a href="https://www.instagram.com/forfatterskolen_norge/" target="_blank">
                                            <i class="sprite-social instagram"></i>
                                        </a>
                                        <a href="https://www.pinterest.ph/forfatterskolenofficial/" target="_blank">
                                            <i class="sprite-social facebook"></i>
                                        </a>
                                    </p>
                                </div>
                            </div> <!-- end contact-info-container -->

                            <div class="row contact-form-container">
                                <form method="POST" action="" onsubmit="disableSubmit(this)">
                                    {{ csrf_field() }}
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="fullname"
                                               placeholder="{{ trans('site.front.form.name') }}" required
                                               value="{{ old('fullname') }}">
                                    </div>
                                    <div class="form-group">
                                        <input type="email" class="form-control" name="email"
                                               placeholder="{{ trans('site.front.form.email-address') }}" required
                                               value="{{ old('email') }}">
                                    </div>
                                    <div class="form-group">
                                        <textarea class="form-control" rows="1" name="message"
                                                  placeholder="{{ trans('site.front.contact-us.message-placeholder') }}"
                                                  required>{{ old('message') }}</textarea>
                                    </div>
                                    <div class="form-group mb-0 custom-checkbox">
                                        <input type="checkbox" name="terms" required="" id="terms">
                                        {!! str_replace(['_start_label_', '_end_label_', '_start_link_','_end_link_'],
										['<label class="accept-terms" for="terms">',
										'</label>',
										'<a href="'.url('/opt-in-terms').'" target="_blank">','</a>'],
										trans('site.front.contact-us.accept-terms')) !!}
                                    </div>
                                    <p class="note">
                                        {{ trans('site.front.contact-us.note') }}
                                    </p>

                                    {!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::display() !!}

                                    <div class="mt-4">
                                        <button type="submit" class="btn site-btn-global">
                                            {{ trans('site.front.contact-us.send') }}
                                        </button>
                                    </div>
                                </form>

                                @if ( $errors->any() )
                                    <?php
                                        $alert_type = session('alert_type');
                                        if(!Session::has('alert_type')) {
                                            $alert_type = 'danger';
                                        }
                                    ?>
                                    <div class="alert alert-{{ $alert_type }} mt-4" style="width: 100%">
                                        <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                                        <ul>
                                            @foreach($errors->all() as $error)
                                                <li>{{$error}}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div> <!-- end contact-form-container -->
                        </div> <!-- end col-md-12 -->
                    </div> <!-- end contact-row -->

                    <div class="row save-data-container">
                        <div class="h1" style="font-size: 2.5rem; margin-bottom: .5rem; margin-top: 0">
                            {{ trans('site.front.contact-us.save-data-title') }}
                        </div>
                        <p>
                            {{ trans('site.front.contact-us.save-data-description') }}
                        </p>
                    </div> <!-- end save-data-container -->
                </div> <!-- end col-md-6 -->
            </div> <!-- end secondary-container -->
        </div>
    </div>
@stop

@section('scripts')
    {!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::renderJS() !!}
    <script>
        /* increase textarea height */
        let textarea = document.querySelector('textarea');

        textarea.addEventListener('keydown', autosize);

        function autosize(){
            let el = this;
            setTimeout(function(){
                el.style.cssText = 'height:auto; padding:0';
                // for box-sizing other than "content-box" use:
                // el.style.cssText = '-moz-box-sizing:content-box';
                let scrollHeight = el.scrollHeight + 15;
                el.style.cssText = 'height:' + scrollHeight + 'px';
            },0);
        }
    </script>
@stop