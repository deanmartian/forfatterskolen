@extends('frontend.layout')

@section('title')
    <title>Forfatterskolen Opt-in</title>
@stop

@section('content')
    <div class="opt-in-thanks" style="background-color: #f9f9f9">
        <div class="container fiction-page">
            @if (!Request::input('ref_id'))
                <div class="row">
                    <div class="col-md-6 left-container">
                        <div class="thank-you-card">
                            <div class="cloud-container"></div>
                            <h1>
                                {{ trans('site.front.opt-in-thanks.thank-you-for-signing') }}
                            </h1>
                            <a href="{{ route('front.opt-in.download', 'fiction') }}" class="btn bg-site-red btn-block">
                                <i class="img-icon pdf-icon"></i> {{ trans('site.front.opt-in-thanks.download-pdf') }}
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 right-container">
                        <div class="main-image-container" style="background-image: url({{ asset('images-new/opt-in-thanks/fiction-video-image.jpg') }})"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="details-section">
                            <div class="col-md-6 left-column">
                                @include('frontend.opt-in-thanks.partials.description')
                            </div>
                            <div class="col-md-6 right-column">
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