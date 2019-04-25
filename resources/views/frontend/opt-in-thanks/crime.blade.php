@extends('frontend.layout')

@section('title')
    <title>Forfatterskolen Opt-in</title>
@stop

@section('content')
    <div class="opt-in-thanks" style="background-color: #f9f9f9">
        <div class="container crime-page">
            @if (!Request::input('ref_id'))
                <div class="row">
                    <div class="col-md-12">
                        <div class="main-container">
                            <img src="{{ asset('images-new/opt-in-thanks/crime-video-image.jpg') }}" alt="">
                            <div class="thank-you-card">
                                <h1>
                                    {{ trans('site.front.opt-in-thanks.thank-you-for-signing') }}
                                </h1>
                                <a href="{{ route('front.opt-in.download', 'gratis-krimkurs') }}" class="btn bg-site-red">
                                    <i class="img-icon pdf-icon"></i> {{ trans('site.front.opt-in-thanks.download-pdf') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="details-section">
                            <div class="col-md-6">
                                @include('frontend.opt-in-thanks.partials.description')
                            </div>
                            <div class="col-md-6">
                                <div class="form-column">
                                    @include('frontend.opt-in-thanks.partials.form')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    @include('frontend.opt-in-thanks.partials.testimonials')
                </div>
            @else
                <div class="col-md-6 col-sm-offset-3 dikt-page">
                    @include('frontend.opt-in-thanks.partials.form')
                </div>
            @endif
        </div>
    </div>
@stop