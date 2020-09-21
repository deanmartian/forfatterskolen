@extends('frontend.layout')

@section('title')
    <?php
        $pageMeta = \App\PageMeta::where('url', url()->current())->first();
    ?>

    @if ($pageMeta)
        <title>{{ $pageMeta->meta_title }}</title>
    @else
        <title>Shop Manuscripts &rsaquo; Forfatterskolen</title>
    @endif
@stop

@section('content')

    <div class="manuscript-page">
        <div class="container main-container">
            <div class="row">
                <div class="col-sm-12">
                    <h1 class="text-center mb-5">
                        {{ trans('site.front.shop-manuscript.title') }}
                    </h1>
                </div>
                {{--<div class="col-sm-12 top-page-container">
                    --}}{{--<img src="{{ asset('images-new/adult-reading-book.jpg') }}" alt="">--}}{{--
                    <iframe src="https://fast.wistia.com/embed/medias/scuv6yv5qy" frameborder="0" allowfullscreen="allowfullscreen"></iframe>
                </div>--}}
            </div>
        </div>

        <div class="row second-section mx-0" data-bg="https://www.forfatterskolen.no/images-new/coffee-book.png">
            <div class="container">
                <div class="col-sm-9 details">
                    <h1 class="title mb-5">
                        {{ trans('site.front.shop-manuscript.first-section.title') }}
                    </h1>

                    {!! trans('site.front.shop-manuscript.first-section.description') !!}
                    {{--<button class="btn site-btn-global-w-arrow" data-toggle="modal" data-target="#editorsModal">Redaktører</button>--}}
                </div>
            </div>
        </div>

        <div class="third-section" data-bg="https://www.forfatterskolen.no/images-new/notebook-pen.png">
            <div class="container">
                <div class="row">
                    {{--<div class="col-sm-6"></div>--}}
                    <div class="col-sm-6 col-xs-offset-6 details" id="testManuscript">
                        <h2 class="title mb-4">
                            {{ trans('site.front.shop-manuscript.form.title') }}
                        </h2>

                        <form method="POST" enctype="multipart/form-data" action="{{ route('front.shop-manuscript.test_manuscript') }}">
                            {{ csrf_field() }}
                            <input type="file" class="hidden" name="manuscript" accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
                            <label class="mb-4">
                                <span class="instruction">{{ trans('site.front.shop-manuscript.form.instruction') }}</span>
                                <br>
                                <span class="note"><i class="info-icon"></i> {{ trans('site.front.shop-manuscript.form.note') }}</span>
                            </label>
                            <div class="input-group mb-4">
                                <input type="text" readonly class="form-control disabled" required>
                                <div class="input-group-append">
                                    <button class="btn bg-site-red select-manuscript" type="button">
                                        {{ trans('site.front.shop-manuscript.form.select-document') }}
                                    </button>
                                </div>
                            </div>
                            <div class="margin-top">
                                <button class="btn site-btn-global-w-arrow" type="submit">
                                    {{ trans('site.front.upload') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="fourth-section">
            <div class="container">
                <div class="row">

                    <?php
                        $shopManuscripts_chunk = $shopManuscripts->chunk(4);
                    ?>

                    <div id="manuscripts-carousel" class="carousel slide" data-ride="carousel" data-interval="false">

                        <!-- Indicators -->
                        <ul class="carousel-indicators">
                            @for($i=0; $i<=$shopManuscripts_chunk->count() - 1;$i++)
                            <li data-target="#manuscripts-carousel" data-slide-to="{{$i}}" @if($i == 0) class="active" @endif></li>
                            @endfor
                        </ul>

                        <!-- The slideshow -->
                        <div class="container carousel-inner no-padding">
                            @foreach($shopManuscripts_chunk as $k => $shopManuscripts)
                                <div class="carousel-item {{ $k==0 ? 'active' : '' }}">
                                    @foreach($shopManuscripts as $shopManuscript)
                                        <div class="col-xs-3 col-sm-3 col-md-3">
                                            <div class="panel panel-default">
                                                <div class="overlay"></div>
                                                <div class="panel-body">
                                                    <div class="circle">
                                                        <div class="circle-white">
                                                            <h3 class="word-count">{{ $shopManuscript->max_words }}</h3>
                                                            <h2>ORD</h2>
                                                        </div>
                                                    </div>
                                                    <h1>{{ $shopManuscript->title }}</h1>
                                                    <p>{{ $shopManuscript->description }}</p>
                                                    <h1 class="price">{{ \App\Http\FrontendHelpers::formatCurrency($shopManuscript->full_payment_price) }} KR</h1>
                                                    <a class="btn buy-btn" href="{{ route('front.shop-manuscript.checkout', $shopManuscript->id) }}">
                                                        {{ trans('site.front.buy') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>

                        <!-- Left and right controls -->
                        <a class="carousel-control-prev" href="#manuscripts-carousel" data-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </a>
                        <a class="carousel-control-next" href="#manuscripts-carousel" data-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div> <!-- end fourth section -->

        <div class="last-section" data-bg="https://www.forfatterskolen.no/images-new/manu-books.png">
            <div class="container">
                <div class="col-sm-5 other-services-container">
                    <a href="{{ route('front.coaching-timer') }}">
                        <div class="row box-white">
                            <div class="col-sm-3">
                                <img data-src="https://www.forfatterskolen.no/images-new/other-services/open-book.png">
                            </div>

                            <div class="col-sm-9">
                                <h1>
                                    {{ trans('site.front.coaching-timer.title') }}
                                </h1>
                                <a href="{{ route('front.coaching-timer') }}" class="link-with-arrow">
                                    {{ ucwords(trans('site.front.view')) }}
                                </a>
                            </div>
                        </div>
                    </a>

                    {{--<a href="{{ route('front.correction') }}">
                        <div class="row box-white">
                            <div class="col-sm-3">
                                <img data-src="https://www.forfatterskolen.no/images-new/other-services/book.png">
                            </div>

                            <div class="col-sm-9">
                                <h1>
                                    {{ trans('site.front.correction.title') }}
                                </h1>
                                <a href="{{ route('front.correction') }}" class="link-with-arrow">
                                    {{ ucwords(trans('site.front.view')) }}
                                </a>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('front.copy-editing') }}">
                        <div class="row box-white">
                            <div class="col-sm-3">
                                <img data-src="https://www.forfatterskolen.no/images-new/other-services/list.png">
                            </div>

                            <div class="col-sm-9">
                                <h1>{{ trans('site.front.copy-editing.title') }}</h1>
                                <a href="{{ route('front.copy-editing') }}" class="link-with-arrow">
                                    {{ ucwords(trans('site.front.view')) }}
                                </a>
                            </div>
                        </div>
                    </a>--}}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" role="dialog" id="editorsModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        X
                    </button>
                </div>
                <div class="modal-body">
                    {{--@foreach($editors->chunk(3) as $editor_chunk)
                        <div class="row masonry-grid">
                            @foreach($editor_chunk as $editor)
                                <div class="col-sm-4 masonry-column">
                                    <div class="panel panel-default">
                                        <div class="panel-header">
                                        </div>
                                        <div class="panel-body text-center">
                                            <div class="editor-circle">
                                                <img src="{{ asset($editor['editor_image']) }}" alt="" class="rounded-circle">
                                            </div>
                                            <p>
                                                <strong class="editor-name">{{ $editor['name'] }}</strong> {{ $editor['description'] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach--}}
                    <div class="card-columns">
                        @foreach($editors->chunk(3) as $editor_chunk)
                            <div class="card-container">
                            @foreach($editor_chunk as $editor)
                                <div class="card">
                                    <div class="card-header">
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="editor-circle">
                                            <img src="{{ asset($editor['editor_image']) }}" alt="" class="rounded-circle">
                                        </div>
                                        <p>
                                            <strong class="editor-name">{{ $editor['name'] }}</strong> {{ $editor['description'] }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(Session::has('manuscript_test'))
        <div id="manuscriptTestModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        {!! Session::get('manuscript_test') !!}
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(Session::has('manuscript_test_error'))
        <div id="manuscriptTestErrorModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <div style="color: red; font-size: 24px"><i class="fa fa-close"></i></div>
                        {!! Session::get('manuscript_test_error') !!}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div id="testManuscriptModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Regn ut for meg</h4>
                </div>
                <div class="modal-body">

                </div>
            </div>

        </div>
    </div>

@stop

@section('scripts')
    <script>
        $(document).ready(function(){
            @if(Session::has('manuscript_test'))
                $('#manuscriptTestModal').modal('show');
            @endif

            @if(Session::has('manuscript_test_error'))
                $('#manuscriptTestErrorModal').modal('show');
            @endif

            let form = $('#testManuscript form');
            $('.select-manuscript').click(function(){
                form.find('input[type=file]').click();
            });
            form.find('input[type=text]').click(function(){
                form.find('input[type=file]').click();
            });
            form.find('input[type=file]').on('change', function(){
                let file = $(this).val().split('\\').pop();
                form.find('input[type=text]').val(file);
            });
            form.on('submit', function(e){
                let file = form.find('input[type=file]').val().split('\\').pop();
                if( file == '' ){
                    alert('Please select a document file.');
                    e.preventDefault();
                }
            });
        });
    </script>
@stop