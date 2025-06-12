@extends('frontend.layout')

@section('title')
<title>Forfatterskolen – Din litterære familie. Skrivekurs for deg</title>
@stop

@section('styles')
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css"
    as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">
    </noscript>
@stop

@section('content')
    <div class="front-page-new">
        <div class="header" data-bg="https://www.forfatterskolen.no/images-new/home/main-image.png">
            <div class="container h-100 position-relative">
                <div class="main-form">
                    <div class="envelope-container">
                        <img data-src="https://www.forfatterskolen.no/images-new/home/envelope.png" alt="envelope icon">
                    </div>

                    <div class="form-container">

                        <form method="POST" action="{{ route('front.home.submit') }}">
                            {{ csrf_field() }}
                            <h1 class="mb-0 text-center font-montserrat-regular">
                                {{ trans('site.front.main-form.heading') }}
                            </h1>

                            <p class="text-center font-montserrat-regular mb-4">
                                {{ trans('site.front.main-form.heading-description') }}
                            </p>

                            <h2 class="text-center font-montserrat-light-italic">
                                {{ trans('site.front.main-form.sub-heading') }}
                            </h2>

                            <div class="btn-container text-center" style="margin-top: 50px">
                                <button type="button" class="btn font-montserrat-light" data-toggle="modal"
                                        data-target="#writingPlanModal">
                                    {{ trans('site.front.main-form.submit-text') }}
                                </button>
                            </div>
                        </form>
                    </div> <!-- end form-container -->
                </div> <!-- end main-form -->
            </div> <!-- end container -->
        </div> <!-- end header -->

        {{--<div class="container py-4">
            <a href="{{ route('front.skrive2020') }}">
            <img src="{{ url('/images-new/skrive.jpeg') }}" class="w-100 img-responsive"
                 style="max-height: 600px; object-fit: contain">
            </a>
        </div>--}}

        <div class="container upcoming-container">
            <video loop muted id="vid">
                <source src="{{ asset('video/Reisen_final.mp4') }}" type="video/mp4">
            </video>
            <div class="row upcoming-row">
                {{--<div style="position: fixed; top: 0; width: 100%; height: 100%; z-index: -1;">--}}
                    {{--<video id="video" style="width:100%; height:100%" src="{{ asset('video/Reisen_final.mov') }}">
                    </video>--}}
                {{--</div>--}}

                <div class="col-md-4">
                    <div class="column blog">
                        <div class="content-container">
                            @php
                                $next_free_webinar1 = \App\FreeWebinar::find(31);
                                $next_free_webinar2 = \App\FreeWebinar::find(32);
                            @endphp
                            <div class="title">
                                Reprise webinar
                            </div>
                            {{--@if ($next_free_webinar1)--}}
                                <div class="h2 mt-0 mb-4 font-montserrat-semibold">
                                    Markedsføring for forfattere
                                </div>

                                <a href="{{ route('front.reprise') }}" class="btn buy-btn mt-4">
                                    {{ trans('site.front.view') }}
                                </a>
                            {{--@endif--}}
                            {{--<div class="title">
                                {{ trans('site.front.latest-blog-post') }}
                            </div>

                            <div class="h2 mt-0 mb-4 font-montserrat-semibold">
                                {!! trans('site.front.competition.title') !!}
                            </div>

                            <a href="{{ route('front.competition') }}" class="btn buy-btn mt-4"
                               title="View blog link">
                                {{ trans('site.front.view') }}
                            </a>--}}
                            {{--@if ($latest_blog)
                                <div class="h2 mt-0 mb-4 font-montserrat-semibold">
                                    {{ $latest_blog->title }}
                                </div>

                                <div class="date-time-cont">
                                    <i class="img-icon16 icon-calendar"></i>
                                    <span>
                                        {{ \App\Http\FrontendHelpers::formatDate($latest_blog->schedule ?: $latest_blog->created_at) }}
                                    </span>
                                </div>

                                <a href="{{ route('front.read-blog', $latest_blog->id) }}" class="btn buy-btn mt-4"
                                   title="View blog link">
                                    {{ trans('site.front.view') }}
                                </a>
                            @endif--}}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="column webinar">
                        <div class="content-container">
                            <div class="title">
                                {{ trans('site.front.next-webinar') }}
                            </div>

                            @if ($next_webinar)
                                <div class="h2 mt-0 mb-4 font-montserrat-semibold">
                                    {{ $next_webinar->title }}
                                </div>

                                <div class="date-time-cont">
                                    <i class="img-icon16 icon-calendar"></i>
                                    <span>{{ \App\Http\FrontendHelpers::formatDate($next_webinar->start_date) }}</span>
                                    <i class="img-icon16 icon-clock ml-3"></i>
                                    <span>
                                        {{ \App\Http\FrontendHelpers::getTimeFromDT($next_webinar->start_date) }}
                                    </span>
                                </div>

                                <a href="{{ url('/course/17?show_kursplan=1') }}" class="btn buy-btn mt-4"
                                   title="View course plan tab on course">
                                    {{ trans('site.front.see-complete-list') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="column free-webinar" style="background-size: cover;
    background-position: 40%;
    background-repeat: no-repeat;">
                        <div class="content-container">

                            {{--<div class="title">
                                {{ trans('site.front.latest-blog-post') }}
                            </div>

                            <div class="h2 mt-0 mb-4 font-montserrat-semibold">
                                {!! trans('site.front.competition.title') !!}
                            </div>

                            <a href="{{ route('front.competition') }}" class="btn buy-btn mt-4"
                               title="View blog link">
                                {{ trans('site.front.view') }}
                            </a>--}}

                            {{--<div class="title">
                                Gratis webinar
                            </div>
                            @if ($next_free_webinar2)
                                <div class="h2 mt-0 mb-4 font-montserrat-semibold">
                                    {{ $next_free_webinar2->title }}
                                </div>

                                <div class="date-time-cont">
                                    <i class="img-icon16 icon-calendar"></i>
                                    <span>
                                        {{ \App\Http\FrontendHelpers::formatDate($next_free_webinar2->start_date) }}
                                    </span>
                                    <i class="img-icon16 icon-clock ml-3"></i>
                                    <span>
                                        {{ \App\Http\FrontendHelpers::getTimeFromDT($next_free_webinar2->start_date) }}
                                    </span>
                                </div>

                                <a href="{{ route('front.free-webinar', $next_free_webinar2->id) }}"
                                   class="btn buy-btn mt-4"
                                   title="View free webinar">
                                    {{ trans('site.front.view') }}
                                </a>
                            @endif--}}

                            <div class="title">
                                Reprise webinar
                            </div>

                            <div class="h2 mt-0 mb-4 font-montserrat-semibold">
                                Marit Reiersgård - Fra tegn til tegning
                            </div>

                            <a href="{{ route('front.here-i-am') }}" class="btn buy-btn mt-4"
                               title="View blog link">
                                {{ trans('site.front.view') }}
                            </a>
                        </div> <!-- end content container -->
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="carousel-onebyone">
                <div class="h1 text-center font-montserrat-semibold title">
                    {{ trans('site.front.student-testimonial.heading') }}
                </div>

                <div id="video-testimonial-carousel" class="carousel slide mt-4" data-ride="carousel"
                     data-interval="10000">
                    <div class="video-testimonial-row row carousel-inner row w-100 mx-auto" role="listbox">
                        @foreach($testimonials as $k => $testimonial)
                            <div class="carousel-item col-md-3 {{ $k == 0 ? 'active' : '' }}">
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#vooModal" class="vooBtn"
                                data-link="{{ $testimonial->testimony }}">
                                    <div class="img-container"
                                         data-bg="https://www.forfatterskolen.no/{{ $testimonial->author_image }}">
                                        <img data-src="https://www.forfatterskolen.no/{{ '/images-new/play-white.png' }}" class="play-image">
                                    </div> <!-- end image container -->
                                </a>

                                <div class="details-container">
                                    <span class="font-montserrat-semibold theme-text">{{ $testimonial->name }}</span>
                                    <br>
                                    <span class="font-montserrat-regular">{{ $testimonial->description }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div> <!-- end carousel-inner -->

                    <a class="carousel-control-prev" href="#video-testimonial-carousel" role="button" data-slide="prev">
                        <i class="fa fa-chevron-left fa-lg text-muted"></i>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next text-faded" href="#video-testimonial-carousel" role="button"
                       data-slide="next">
                        <i class="fa fa-chevron-right fa-lg text-muted"></i>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
            {{--<div class="testimonial-row row">
                <div class="col-md-12">
                    <div class="h1 mt-0 text-center font-montserrat-semibold">
                        {{ trans('site.front.student-testimonial.heading') }}
                    </div>
                    <div id="testimonials-carousel" class="carousel slide global-carousel"
                         data-ride="carousel" data-interval="15000">

                        <!-- The slideshow -->
                        <div class="container carousel-inner no-padding">

                            @foreach($testimonials as $k => $testimonial)
                                <div class="carousel-item {{ $k == 0 ? 'active' : '' }}">
                                    <div class="col-md-12">
                                        <div class="row testimonial-details-row">
                                            <p class="font-montserrat-medium w-100">
                                                {!! nl2br($testimonial->testimony) !!}
                                            </p>
                                            <div class="user-details">
                                                <div class="images-container">
                                                    <img data-src="https://www.forfatterskolen.no/{{ $testimonial->author_image }}" class="user-image"
                                                    alt="user icon">
                                                    <img data-src="https://www.forfatterskolen.no/{{ $testimonial->book_image }}" class="book-image"
                                                    alt="book icon">
                                                </div>
                                                <div class="user-info">
                                                    <span class="font-montserrat-semibold theme-text">{{ $testimonial->name }}</span>
                                                    <span class="font-montserrat-regular">{{ $testimonial->description }}</span>
                                                </div>
                                            </div>
                                        </div> <!-- end testimonial-details-row -->
                                    </div> <!-- end col-md-12 -->
                                </div> <!-- end carousel-item -->
                            @endforeach
                        </div> <!-- end carousel-inner -->

                        <!-- Left and right controls -->
                        <a class="carousel-control-prev" href="#testimonials-carousel" data-slide="prev"
                           title="View previous item">
                            <span class="carousel-control-prev-icon"></span>
                        </a>
                        <a class="carousel-control-next" href="#testimonials-carousel" data-slide="next"
                            title="View next item">
                            <span class="carousel-control-next-icon"></span>
                        </a>

                    </div> <!-- end testimonials-carousel -->
                </div> <!-- end col-md-12 -->
            </div>--}}
        </div> <!-- end container-->

        <div class="our-course-wrapper">
            <div class="container">
                <div class="h1 mt-0 font-montserrat-semibold">{{ trans('site.front.our-course.title') }}</div>
                <p class="font-montserrat-regular">
                    {{ trans('site.front.our-course.details') }}
                </p>
            </div> <!-- end container -->
        </div> <!-- end our-course-wrapper -->

        <div class="popular-course-wrapper">
            <div class="container">
                <div class="all-course theme-tabs">
                    <div class="tabs-container">
                        <ul class="nav nav-tabs">
                            <li>
                                <a data-toggle="tab" href="#home" class="active" title="Toggle popular course">
                                    <span>{{ trans('site.front.popular-course') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div> <!-- end tabs-container -->

                    <div class="tab-content">
                        <div id="home" class="tab-pane fade in active">
                            <div class="container">
                                <?php $featured = 0 ?>
                                @foreach( $popular_courses as $popular_course )
                                    @if( $featured == 0)
                                        <a href="{{ route('front.course.show', $popular_course->id) }}"
                                        class="featured-link" title="View course details">
                                            <div class="row featured-item" style="background-image: url({{$popular_course->course_image}})">
                                                <div class="details">
                                                    <div class="indicator">
                                                        {{ trans('site.front.course-text') }}
                                                    </div>
                                                    <h2 class="font-montserrat-semibold mb-4">{{ $popular_course->title}}</h2>
                                                    <p class="font-montserrat-regular">
                                                        {{ str_limit(strip_tags($popular_course->description), 300)}}
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                        <?php $featured++?>
                                    @endif
                                @endforeach

                                <?php $counter = 0 ?>
                                <div class="row courses-container">
                                    @foreach( $popular_courses as $popular_course )
                                            @if ($counter == 0)
                                                <?php $counter++?>
                                            @else
                                                <div class="col-md-6 mt-5 course-item" itemscope
                                                     itemtype="http://schema.org/CreativeWork">
                                                    <div class="card rounded-0 border-0">
                                                        <div class="card-header p-0 rounded-0"
                                                             data-bg="https://www.forfatterskolen.no/{{$popular_course->course_image}}">
                                                            <span>{{ trans('site.front.course-text') }}</span>
                                                        </div>
                                                        <div class="card-body">
                                                            <h3 class="font-montserrat-semibold" itemprop="headline">
                                                                {{ str_limit(strip_tags($popular_course->title), 40)}}
                                                            </h3>
                                                            <p class="font-montserrat-light mt-4"
                                                               itemprop="about">{{ str_limit(strip_tags($popular_course->description), 130)}}</p>
                                                            <a href="{{ route('front.course.show', $popular_course->id) }}"
                                                               class="site-btn-global rounded-0 mt-3 d-inline-block"
                                                            title="View course details" itemprop="url">
                                                                {{ trans('site.front.view') }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                    @endforeach
                                </div> <!-- end courses-container -->
                            </div> <!-- end container -->
                        </div> <!-- end #home -->
                    </div> <!-- end tab-content -->
                </div> <!-- end all-course -->
            </div> <!-- end container -->
        </div> <!-- end popular-course-wrapper -->

        <div id="poem-wrapper" class="d-none">
            <div class="container">
                <div class="heading">
                    <div class="h1 mt-0 d-inline-block font-montserrat-semibold">{{ trans('site.front.week-poem') }}</div>
                    <a href="{{ route('front.poems') }}" class="btn d-inline-block" title="View poems">
                        {{ trans('site.front.view-poem') }}
                    </a>
                </div> <!-- end heading -->

                <?php
                    $latestPoem = $poems->first();
                ?>

                <div class="row poem-details">
                    <div class="col-md-6 col-sm-12 poem-author-container">
                            <img data-src="https://www.forfatterskolen.no/{{ $latestPoem->author_image }}" class="author-image" alt="author image">
                        <div class="author-info">
                            <span class="indicator">{{ trans('site.front.poem-of-the-week') }}</span>
                            <h3 class="font-weight-normal font-montserrat-regular">{{ $latestPoem->title }}</h3>
                            <h4 class="font-montserrat-light">{{ $latestPoem->author }}</h4>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 poem-container">
                        <div class="poem-text-container">
                            {!! $latestPoem->poem !!}
                        </div>
                    </div>
                </div> <!-- end row -->
            </div> <!-- end container -->
        </div> <!-- end poem-wrapper -->
    </div>

    @if(!isset($_COOKIE['_gdpr']))
        <div class="col-sm-12 no-left-padding no-right-padding gdpr">
            <div class="container display-flex">
                <div class="gdpr-body">
                    <div class="h1 mt-0 gdpr-title">Dine data, dine valg</div>
                    <div>
                        <p>
                            Forfatterskolen er den som behandler dine data.
                        </p>
                        <p>
                            Dine data er trygge hos oss. Vi bruker dem til å tilpasse tjenestene og tilbudene for deg.
                        </p>
                    </div>
                </div>

                <div class="gdpr-actions">
                    <button class="btn btn-agree" onclick="agreeGdpr()">
                        JEG FORSTÅR
                    </button>
                    <a href="{{ route('front.terms') }}" title="View terms">Vis meg mer</a>
                </div>
            </div>
        </div>
    @endif

    <div id="vooModal" class="modal fade no-header-modal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <iframe allow="autoplay" allowtransparency="true" style="max-width:100%" allowfullscreen="true"
                            src="" scrolling="no" width="100%" height="430" frameborder="0"></iframe>
                </div>
            </div>

        </div>
    </div>

    <div id="writingPlanModal" class="modal fade no-header-modal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body main-form" style="padding: 30px">

                    <div class="form-container">

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa user-icon"></i></span>
                            </div>
                            <input type="text" name="name" class="form-control no-border-left"
                                   placeholder="Fornavn" required value="{{old('name')}}">
                        </div>

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa email-icon"></i></span>
                            </div>
                            <input type="email" name="email" placeholder="Epost"
                                   class="form-control no-border-left" required>
                        </div>

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

                                <small class="font-montserrat-light">{{ trans('site.front.main-form.note') }}</small>
                            </div>

                            <div class="col-md-6">
                                {!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::renderJS() !!}
                                {!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::display(['data-callback' => 'captchaCB']) !!}
                            </div>
                        </div>

                        <input type="hidden" name="captcha" value="">

                        <div class="btn-container text-right" style="margin-top: 20px">
                            <button type="button" class="btn font-montserrat-light" onclick="submitWritingPlan(this)">
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
@stop
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>
    <script>
        $(document).ready(function(){
            if ($(window).width() > 640) {
                document.getElementById('vid').play();
            }
        });

        $(window).resize(function() {
            if ($(window).width() <= 640) {
                document.getElementById('vid').pause();
            } else {
                document.getElementById('vid').play();
            }
        });

        let url_link = '{{ route('front.agree-gdpr') }}';
        let $carousel = jQuery('.carousel-onebyone .carousel');
        if($carousel.length){
            jQuery('.carousel-onebyone').on('slide.bs.carousel', carousel_onebyone);
            carousel_set($carousel);
            let resizeId;
            jQuery(window).resize(function() {
                clearTimeout(resizeId);
                resizeId = setTimeout(()=>carousel_set($carousel), 500);
            });
        }

        function carousel_set($carousel){
            if(!$carousel || !$carousel.length) return;

            $carousel.each((i, el)=>{
                let $el = jQuery(el);
                let itemsPerSlide = carousel_itemsPerSlide($el);
                let totalItems = $el.find('.carousel-item').length;

                if(itemsPerSlide < totalItems){
                    $el.find('.carousel-control').removeClass('hidden');
                }else{
                    $el.find('.carousel-control').addClass('hidden');
                }
            });
        }

        function carousel_onebyone(e){
            let carouselID = '#'+jQuery(this).find('.carousel').attr('id');
            let $carousel = jQuery(carouselID);
            let $inner = $carousel.find('.carousel-inner');
            let $items = $carousel.find('.carousel-item');

            let idx = jQuery(e.relatedTarget).index();
            let itemsPerSlide = carousel_itemsPerSlide($carousel);
            let totalItems = $items.length;

            if (idx >= totalItems-(itemsPerSlide-1)) {
                let it = itemsPerSlide - (totalItems - idx);
                for (let i=0; i<it; i++) {
                    if (e.direction === 'left') {
                        $items.eq(i).appendTo($inner);
                    }else {
                        $items.eq(0).appendTo($inner);
                    }
                }
            }
        }

        function carousel_itemsPerSlide($carousel){
            let itemW = $carousel.find('.carousel-item').width();
            let innerW = $carousel.find('.carousel-inner').width();

            return Math.floor(innerW/itemW);
        }

        function agreeGdpr() {
            $.post(url_link).then(function(){
                $(".gdpr").remove();
            });
        }

        $(".poem-text-container").mCustomScrollbar({
            theme: "light-thick",
            scrollInertia: 500,

        });

        if ($(window).width() > 640) {
            carouselMultiple();
        } else {
            $(".glyphicon").removeClass('hide');
        }

        $(window).on('resize', function(){
            if ($(window).width() > 640) {
                if ($(".item__third").length <= 3) {
                    carouselMultiple();
                }
                $(".glyphicon").addClass('hide');
            } else {
                $(".glyphicon").removeClass('hide');
                removeMultiple();
            }
        });

        // for multiple item carousel action
        let items = $(".multi-item-carousel").find('.carousel-inner .item'),
            currentHighlight = 0;
        $(".multi-item-carousel .left").click(function(){
            currentHighlight = (currentHighlight - 1) % items.length;
            items.removeClass('active').eq(currentHighlight).addClass('active');
        });

        $(".multi-item-carousel .right").click(function(){
            currentHighlight = (currentHighlight + 1) % items.length;
            items.removeClass('active').eq(currentHighlight).addClass('active');
        });

        $(".vooBtn").click(function(){
            const iframe = $("#vooModal").find('iframe');
            iframe.attr('src', $(this).data('link'));
        });

        $('#vooModal').on('hidden.bs.modal', function (e) {
            const iframe = $("#vooModal").find('iframe');
            iframe.attr('src', '');
        })

        function carouselMultiple() {
            $('.multi-item-carousel .item').each(function(){
                var next = $(this).next();
                if (!next.length) next = $(this).siblings(':first');
                next.children(':first-child').clone().appendTo($(this));
            }).each(function(){
                var prev = $(this).prev();
                if (!prev.length) prev = $(this).siblings(':last');
                prev.children(':nth-last-child(2)').clone().prependTo($(this));
            });
        }

        function removeMultiple(){
            let item_first = $('.multi-item-carousel .item:first-child');
            if (item_first.find('.item__third').length > 1) {
                item_first.find('.item__third').not(':eq(1)').remove();
            }

            let item_sec = $('.multi-item-carousel .item:nth-child(2)');
            if (item_sec.find('.item__third').length > 1) {
                item_sec.find('.item__third').not(':eq(1)').remove();
            }

            let item_third = $('.multi-item-carousel .item:nth-child(3)');
            if (item_third.find('.item__third').length > 1) {
                item_third.find('.item__third').not(':eq(1)').remove();
            }

        }

        function submitWritingPlan(self) {
            let modal = $("#writingPlanModal");
            let name = modal.find('[name=name]').val();
            let email = modal.find('[name=email]').val();
            let terms = modal.find('[name=terms]:checked').length ? 1 : '';
            let captcha = modal.find('[name=captcha]').val();

            let data = {
                name: name,
                email: email,
                terms: terms,
                'g-recaptcha-response': captcha
            };

            let error_container = modal.find('.alert-danger');
            error_container.find("li").remove();
            self.disabled = true;

            $.post("/", data).then(function(response) {

                error_container.addClass('d-none');
                window.location.href = response.redirect_link;

            }).catch( error => {
                self.disabled = false;
                $.each(error.responseJSON, function(k, v) {
                    let item = "<li>" + v[0] + "</li>";

                    if (error_container.hasClass('d-none')) {
                        error_container.removeClass('d-none');
                    }

                    error_container.find("ul").append(item);
                })

            } );

        }

        // callback function if the captcha is checked
        function captchaCB(captcha) {
            $("#writingPlanModal").find('[name=captcha]').val(captcha);
        }
    </script>
@stop