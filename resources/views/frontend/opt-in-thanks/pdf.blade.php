@extends('frontend.layout')

@section('page_title', 'Takk for p&aring;meldingen &mdash; PDF-guide &rsaquo; Forfatterskolen')
@section('meta_desc', 'Takk for påmeldingen. Sjekk e-posten din for PDF-guiden.')

@section('content')
    <div class="opt-in-thanks">
        <div class="container dikt-page">
            @if (!Request::input('ref_id'))
                <div class="row">
                    <div class="col-md-12">
                        <div class="main-image-container" style="background-image: url({{ asset('images-new/opt-in-thanks/poem-bg.jpg') }})"></div>
                        <div class="card thank-you-card">
                            <h1>
                                Tack för att du registrerade dig. Du kan nu ladda ner din gratis pdf
                            </h1>
                            <a href="{{ route('front.opt-in.download', $slug) }}" class="btn bg-site-red btn-block">
                                <i class="img-icon pdf-icon"></i>
                                Ladda ner din gratis PDF
                            </a>
                        </div>
                    </div>
                    <div class="col-md-5">
                       {{-- @include('frontend.opt-in-thanks.partials.description')
                        @include('frontend.opt-in-thanks.partials.form')--}}
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