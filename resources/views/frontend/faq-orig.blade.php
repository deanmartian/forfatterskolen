@extends('frontend.layout')

@section('title')
<title>Forfatterskolen &rsaquo; FAQ</title>
@stop

@section('content')

    @if(Auth::user())
        <div class="account-container">
            @include('frontend.partials.learner-menu')
            <div class="col-sm-12 col-md-10 sub-right-content">
                <div class="col-sm-12">
    @endif
                    <div class="container">
                        <div class="courses-hero faq-hero text-center">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h2><span class="highlight">FA</span>Q</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container faq-container">
                        <div class="row">
                            <div class="col-sm-12">
                                <h3>SVAR PÅ DINE SPØRSMÅL OM KURSET</h3> <br />
                            </div>
                            <div class="col-sm-12">
                                <div class="panel-group" id="accordion">
                                    <?php $first = true; ?>
                                    @foreach( $faqs as $faq )
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse-{{ $faq->id }}" class="all-caps">
                                                    <span class="text-theme">Q:</span> {{ $faq->title }}
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapse-{{ $faq->id }}" class="panel-collapse collapse @if($first) in @endif">
                                            <div class="panel-body">
                                                <p class="no-margin-bottom">
                                                {!! nl2br($faq->description) !!}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php $first = false; ?>
                                    @endforeach
                                </div>
                                Alt godt,<br />
                                Rektor Kristine <br />
                                <div class="contact-feedback-image faq-rektor margin-top margin-bottom" style="background-image: url({{ asset('images/kristine.png') }}); margin-right: 20px"></div>
                                <div class="margin-bottom">
                                PS! Lurer du på noe mer, eller noe helt annet? Ikke nøl med å sende meg en mail: <a href="mailto:post@forfatterskolen.no">post@forfatterskolen.no</a>
                                </div>
                            </div>
                        </div>
                    </div>
    @if(Auth::user())
                </div>
            </div>

            <div class="clearfix"></div>
        </div>
    @endif

@stop