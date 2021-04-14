@extends('frontend.layout')

@section('title')
<title>Forfatterskolen &rsaquo; FAQ</title>
@stop

@section('content')

    <div class="faq-page" data-bg="https://www.forfatterskolen.no/images-new/faq-bg.png">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <h1 class="page-title">
                        {{ trans('site.front.faq.title') }}
                    </h1>
                </div>
            </div> <!-- end row -->

            {{--<div class="row sub-header">
                <div class="col-md-4">
                    <a href="{{ route('front.support-articles', 3) }}" class="red-underline-hover">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <img data-src="https://www.forfatterskolen.no/images-new/go-to-webinar.png" alt="">
                                <h2>
                                    {{ trans('site.front.gt-webinar.title') }}
                                </h2>
                                <p>
                                    {{ trans('site.front.gt-webinar.description') }}
                                </p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4">
                    <a href="{{ route('front.support-articles', 4) }}" class="red-underline-hover">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <img data-src="https://www.forfatterskolen.no/images-new/pen-paper.png" alt="">
                                <h2>
                                    {{ trans('site.front.get-started.title') }}
                                </h2>
                                <p>
                                    {{ trans('site.front.get-started.description') }}
                                </p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <img data-src="https://www.forfatterskolen.no/images-new/document.png" alt="">
                            <h2>
                                {{ trans('site.front.replays.title') }}
                            </h2>
                            <p>
                                {{ trans('site.front.replays.description') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>--}} <!-- end sub-header -->

            <div class="row">
                <div class="col-sm-12 faq-container">
                    <div class="panel-group" id="accordion">
                        <?php $first = true; ?>
                        @foreach( $faqs as $faq )
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse-{{ $faq->id }}" class="all-caps collapsed">
                                            <i class="img-icon"></i> {{ $faq->title }}
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapse-{{ $faq->id }}" class="panel-collapse collapse">
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
                </div>
            </div> <!-- end row -->

            <div class="row">
                <div class="col-sm-12 author-container">
                    <div class="pull-left mr-4">
                        <img data-src="https://www.forfatterskolen.no/images/kristine.png" alt="" class="rounded-circle">
                    </div>
                    <div class="pull-left">
                        <h2>
                            {!! str_replace('_break_',"<br/>",trans('site.front.faq.person')) !!}
                        </h2>
                        <p>
                            {!! str_replace(['_break_', '_start_link_', '_end_link_'],
                            ['<br/>', '<a href="mailto:post@forfatterskolen.no" class="theme-text">', '</a>'],
                            trans('site.front.faq.person-details')) !!}
                        </p>
                    </div>
                </div>
            </div>
        </div> <!-- end container -->
    </div>

@stop

@section('scripts')
    <script>
        // trigger the click for the first panel
        $(".panel-default:first-child").find('.panel-heading').find('a').trigger('click');
    </script>
@stop