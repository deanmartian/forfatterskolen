@extends('frontend.layout')

@section('title')
    <title>Forfatterskolen &rsaquo; Kontakt Oss</title>
@stop

@section('content')
<div class="contact-page-new">
    <div class="header" data-bg="https://www.forfatterskolen.no/images-new/contact-header.png">
        <div class="container text-center position-relative">
            <h1>
                {{ trans('site.front.nav.contact-us') }}
            </h1>

            @if($hasAdvisory)
                <div class="row advisory text-center">
                    <div class="col-sm-12">
                        <h2>Advisory here</h2>
                    </div>
                </div>
            @endif
        </div>

        <div class="sub-header">
            <div class="row justify-content-center align-items-center">
                <div class="col-md-2 left-container">
                    <img data-src="https://www.forfatterskolen.no/images-new/kristine1.png" alt="" class="rounded-circle">
                </div>
                <div class="col-md-7">
                    <h1>
                        KRISTINE STORLI HENNINGSEN
                    </h1>
                    <span class="author">
                        {{ trans('site.front.contact-us.first-description') }}
                    </span>
                    <p>{{ trans('site.front.contact-us.main-container-photo-text') }}</p>
                </div>
                <div class="col-md-3 icons-container">
                    <a href="https://www.facebook.com/bliforfatter/" target="_blank">
                        <img src="{{ asset('images-new/icon/facebook.png') }}" alt="facebook-icon">
                    </a>
                    <a href="https://twitter.com/Forfatterrektor" target="_blank" class="ml-0">
                        <img src="{{ asset('images-new/icon/twitter.png') }}" alt="twitter-icon">
                    </a>
                    <a href="https://www.instagram.com/forfatterskolen_norge/" target="_blank">
                        <img src="{{ asset('images-new/icon/instagram.png') }}" alt="instagram-icon">
                    </a>
                    <a href="https://no.pinterest.com/forfatterskolen_norge/" target="_blank">
                        <img src="{{ asset('images-new/icon/pinterest.png') }}" alt="pinterest-icon">
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 details-container">
                    {!! trans('site.front.contact-us.main-description') !!}
                </div>
            </div>
        </div> <!-- end sub-header -->
    </div> <!-- end header -->
    
    <div class="container">
        <div class="row secondary-container">
            <div class="col-md-5">
                <div class="h1">{{ trans('site.front.contact-us.our-staff') }}</div>
                <div class="stab-row">
                    <ul>
                        @foreach(\App\Http\FrontendHelpers::getStaffs() as $staff)
                            <li>
                                <div class="row">
                                    <div class="col-md-3 stab-image">
                                        <img data-src="https://www.forfatterskolen.no/{{ ($staff->image 
                                            ? $staff->image : 'images/user.png')  }}" class="rounded-circle">
                                    </div>
                                    <div class="col-md-9">
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

                <div class="h1">
                    Redaktører
                </div>
                <div class="stab-row">
                    <ul>
                        @foreach(\App\Http\FrontendHelpers::getStaffs('editor') as $staff)
                            <li>
                                <div class="row">
                                    <div class="col-md-3 stab-image">
                                        <img data-src="https://www.forfatterskolen.no/{{ ($staff->image 
                                            ? $staff->image : 'images/user.png')  }}" class="rounded-circle">
                                    </div>
                                    <div class="col-md-9">
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
                {{-- <div class="save-data-container">
                    <div class="h1" style="font-size: 2.5rem; margin-bottom: .5rem; margin-top: 0">
                        {{ trans('site.front.contact-us.save-data-title') }}
                    </div>
                    <p>
                        {{ trans('site.front.contact-us.save-data-description') }}
                    </p>
                </div> --}}

                <div class="contact-info-container">
                    <p>
                        <img src="{{ asset('images-new/icon/outline-mail.png') }}" alt="outine mail">
                        <a href="mailto:{{ trans('site.front.contact-us.mail') }}" class="theme-text">
                            {{ trans('site.front.contact-us.mail') }}
                        </a>
                    </p>

                    <p>
                        <img src="{{ asset('images-new/icon/outline-marker.png') }}" alt="outine marker">
                        <span>{{ trans('site.front.contact-us.address') }}</span>
                    </p>
                </div> <!-- end contact-info-container -->
            </div> <!-- end col-md-5 -->

            <div class="col-md-7">
                <div class="h1">{{ trans('site.front.contact-us.contact-us-today') }}</div>
                <div class="contact-form-container">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>
                                {{ trans('site.front.form.name') }}
                            </label>
                            <input type="text" class="form-control" name="fullname" required
                                   value="{{ old('fullname') }}">
                        </div>
                        <div class="form-group">
                            <label>
                                {{ trans('site.front.form.email-address') }}
                            </label>
                            <input type="email" class="form-control" name="email" required
                                   value="{{ old('email') }}">
                        </div>
                        <div class="form-group">
                            <label>
                                {{ trans('site.front.contact-us.message-placeholder') }}
                            </label>
                            <textarea class="form-control" rows="1" name="message"
                                      placeholder="" required>{{ old('message') }}</textarea>
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
                                <img src="{{ asset('images-new/icon/plane.png') }}" alt="plane">
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
            </div>
        </div> <!-- end secondary-container -->
    </div>
</div>
@stop