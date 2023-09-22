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
    <link rel="stylesheet" href="{{asset('vendor/laraberg/css/laraberg.css')}}">
@stop

@section('content')
<div class="front-page-new">
    <div class="header-new">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h1>
                        For deg som vil gjøre alvor av skrivedrømmen
                    </h1>
                    <p>
                        Vi hos Forfatterskolen har fulgt mange elever tett frem mot utgivelse, 
                        og ønsker å hjelpe flere. Er du den neste?
                    </p>

                    <a href="{{ route('front.course.index') }}" class="btn btn-red" style="margin-right: 20px">
                        Alle Kurs
                    </a>
                    <button class="btn btn-outline-red" data-toggle="modal"
                    data-target="#writingPlanModal">
                        Gratis skrivetips
                    </button>
                </div>
                <div class="col-md-6">
                    <img class="w-100" data-src="https://www.forfatterskolen.no/images-new/home/kristine.png" alt="kristine">
                </div>
            </div>
        </div> <!-- end container -->
    </div> <!-- end header-new -->

    <div class="container">
        <div class="col-md-12">
            <div class="row first-row">
                <div class="col-md-4">
                    <h2>
                        20+
                    </h2>
                    <p>
                        Høykvalitets kurs
                    </p>
                </div>
                <div class="col-md-4">
                    <h2>
                        1000+
                    </h2>
                    <p>
                        Studenter
                    </p>
                </div>
                <div class="col-md-4">
                    <h2>
                        15+
                    </h2>
                    <p>
                        Erfarne mentorer
                    </p>
                </div>
            </div> <!-- end first-row -->
            
            <div class="row second-row">
                <h2 class="w-100 text-center">
                    Siste nytt
                </h2>

                @foreach($upcomingSections as $k => $upcomingSection)
                    @php
                        $hasNextWebinar = $k === 1 && $next_webinar ? true : false;
                    @endphp
                    <div class="col-md-4">
                        <div class="content-container">
                            <div class="title">
                                <a href="{{ url($hasNextWebinar ? '/course/17?show_kursplan=1' : $upcomingSection->link) }}" 
                                    style="color: inherit">
                                    {{ $hasNextWebinar ? trans('site.front.next-webinar') : $upcomingSection->name }}
                                </a>
                            </div>
                            <h3>
                                {{ $hasNextWebinar ? $next_webinar->title : $upcomingSection->title }}
                            </h3>
                            @if ($upcomingSection->date || $hasNextWebinar)
                                <div class="date-time-cont">
                                    <i class="img-icon16 icon-calendar"></i>
                                    <span>{{ \App\Http\FrontendHelpers::formatDate($hasNextWebinar ? $next_webinar->start_date : $upcomingSection->date) }}</span>
                                    <i class="img-icon16 icon-clock ml-3"></i>
                                    <span>
                                    {{ \App\Http\FrontendHelpers::getTimeFromDT($hasNextWebinar ? $next_webinar->start_date : $upcomingSection->date) }}
                                </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div> <!-- end second-row-->
        </div><!-- end col-md-12 -->
    </div> <!-- end container -->

    <div class="popular-courses-row">
        <div class="container">
            <h2 class="float-left">
                Våre mest populære kurs
            </h2>
            <a href="{{ route('front.course.index') }}" class="btn float-right btn-outline-maroon">
                Alle kurs
            </a>

            <div class="clearfix"></div>

            <div class="row">
                @foreach( $popular_courses as $popular_course )
                    <div class="col-md-4 course-container">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="image-container">
                                    <img src="https://www.forfatterskolen.no/{{ $popular_course->course_image }}">
                                    <span>{{ trans('site.front.course-text') }}</span>
                                </div>

                                <h3 class="font-montserrat-semibold" itemprop="headline">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($popular_course->title), 40)}}
                                </h3>

                                <p class="font-montserrat-light mt-4"
                                    itemprop="about">{!! \Illuminate\Support\Str::limit(strip_tags($popular_course->description), 110) !!}</p>
                                <a href="{{ route('front.course.show', $popular_course->id) }}"
                                    class="site-btn-global rounded-0 mt-3 d-inline-block"
                                    title="View course details" itemprop="url">
                                    {{ trans('site.front.view') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div> <!-- end container -->
    </div> <!-- end popular-courses-new -->

    <div class="online-courses-row">
        <div class="container">
            <div class="top-container">
                <img data-src="https://www.forfatterskolen.no/images-new/home/online-course.png" alt="online-course"
                 class="inline-course-img">
                <div class="details">
                    <h2>Hva er fordelene ved nettkurs?</h2>
                    <p>
                        Fordelen med nettkurs er at du kan ta det hvor som helst, og i ditt helt eget tempo. 
                        På kurset får du også: 
                    </p>
                    <ul>
                        <li>
                            <img data-src="https://www.forfatterskolen.no/images-new/home/arrow.svg" alt="arrow">
                            Et unikt innblikk i skrivehåndverket, gode verktøy og nyttige tips.
                        </li>
                        <li>
                            <img data-src="https://www.forfatterskolen.no/images-new/home/arrow.svg" alt="arrow">
                            Tilbakemelding på manus fra profesjonell redaktør.
                        </li>
                        <li>
                            <img data-src="https://www.forfatterskolen.no/images-new/home/arrow.svg" alt="arrow">
                            Skjermtreff med kjente forfattere og flere hundre andre skriveglade.
                        </li>
                    </ul>
                </div>
            </div> <!-- end top-container -->

            <div class="bottom-container">
                <div class="col-md-5">
                    <h2>
                        Møt mentorene dine
                    </h2>
                    <p>
                        Hver mandag har vi treff med kjente forfattere på skjermen – og av og til en 
                        profesjonell redaktør, dramaturg eller språkvasker. Alt for at du skal lære og 
                        bli inspirert av landets beste skrivementorer. Enkelte av mandagene redigerer 
                        også rektor innsendte tekster, live og direkte, så du lærer å bearbeide eget manus.
                    </p>

                    <a href="{{ route('front.course.show', 17) }}" class="btn btn-red">
                        Les mer om mentormøter
                    </a>
                </div>
            </div>
        </div> <!-- end container -->
    </div> <!-- end online-courses-row-->

    <div class="testimonials-row">
        <div class="container">
            <h2>
                {{ trans('site.front.student-testimonial.heading') }}
            </h2>

            <div class="carousel-onebyone">
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
        </div>
    </div> <!-- end testimonials-row -->

    <div class="professional-feedback-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-5 text-center">
                    <img src="https://www.forfatterskolen.no/{{ '/images-new/illustrationcomputer.png' }}" 
                    alt="illustration-computer">
                </div>
                <div class="col-md-7">
                    <h2>
                        Vil du ha profesjonell tilbakemelding på en smakebit av din personlige tekst, helt gratis?
                    </h2>

                    <a href="{{ route('front.free-manuscript.index') }}" class="btn site-btn-global mt-5">
                        Ja, dette vil jeg ha!
                    </a>
                </div>
            </div>
        </div>
    </div>
</div> <!-- end front-page-new -->

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