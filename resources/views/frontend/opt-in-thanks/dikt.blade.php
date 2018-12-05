@extends('frontend.layout')

@section('title')
    <title>Forfatterskolen Opt-in</title>
@stop

@section('content')
    <div class="opt-in-thanks">
        <div class="container dikt-page">
            @if (!Request::input('ref_id'))
                <div class="row">
                    <div class="col-md-7">
                        <div class="main-image-container" style="background-image: url({{ asset('images-new/opt-in-thanks/poem-bg.jpg') }})"></div>
                        <div class="card thank-you-card">
                            <h1>
                                Thank you for signing up for the free pdf.
                            </h1>
                            <button class="btn bg-site-red btn-block">
                                <i class="img-icon pdf-icon"></i> Download the free pdf here
                            </button>
                        </div>
                    </div>
                    <div class="col-md-5">
                        @include('frontend.opt-in-thanks.partials.description')
                        @include('frontend.opt-in-thanks.partials.form')
                    </div>
                </div>

                <div class="row">
                    @include('frontend.opt-in-thanks.partials.testimonials')
                </div>
            @else
                <div class="col-md-6 col-sm-offset-3">
                    @include('frontend.opt-in-thanks.partials.form')
                </div>
            @endif
        </div>
    </div>
@stop