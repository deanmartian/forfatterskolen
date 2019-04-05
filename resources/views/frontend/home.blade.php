@extends('frontend.layout')

@section('title')
<title>Forfatterskolen</title>
@stop

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">
    <link rel="stylesheet" href="{{ asset('css/front-page.css?v='.time()) }}">
@stop

@section('content')
    <div class="front-page-new">
        <div class="header">
            <div class="container h-100 position-relative">
                <div class="main-form">
                    <div class="envelope-container">
                        <img src="{{ asset('images-new/home/envelope.png') }}" alt="">
                    </div>

                    <div class="form-container">

                        <form method="POST" action="{{ route('front.home') }}">
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
                                        <label for="terms">Jeg aksepterer
                                            <a href="{{ route('front.opt-in-terms') }}"
                                               target="_blank">vilkårene</a></label>
                                    </div>

                                    <small class="font-montserrat-light">PS! Vi deler ikke e-postadressen din med noen</small>
                                </div>

                                <div class="col-md-6 btn-container">
                                    <button type="submit" class="btn font-montserrat-light">
                                        Ja, jeg vil ha gratis tips!
                                    </button>
                                </div>
                            </div>

                            {!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::renderJS() !!}
                            {!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::display() !!}

                            @if ( $errors->any() )
                                <div class="alert alert-danger no-bottom-margin mt-3">
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>{{$error}}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </form>
                    </div> <!-- end form-container -->
                </div> <!-- end main-form -->
            </div> <!-- end container -->
        </div> <!-- end header -->

        <div class="latest-seminar-wrapper">
            <div class="container">
                <div class="row">
                    <div class="indicator">
                        <h1>Latest Seminars</h1>
                    </div>
                </div>
            </div>
            <div id="latest-seminar-carousel" class="carousel slide multi-item-carousel" data-ride="carousel" data-interval="false">
                <div class="carousel-inner" role="listbox">
                    <div class="item active">
                        <div class="item__third">
                            <div class="card">
                                <div class="card-header" style="">
                                    <img src="{{ asset('/images-new/home/hand-pen.png') }}" alt="">
                                    <span class="title">
                                        {{ !$next_free_webinar && $next_workshop ? 'Neste Workshop' : 'Neste gratis webinar' }}
                                    </span>
                                </div>
                                <div class="card-body">
                                    @if($next_free_webinar)
                                        <h1 class="font-montserrat-semibold">
                                            {{ $next_free_webinar->title }}
                                        </h1>

                                        <div class="date-time-cont">
                                            <i class="img-icon16 icon-calendar"></i>
                                            <span>{{ \App\Http\FrontendHelpers::formatDate($next_free_webinar->start_date) }}</span>
                                            <i class="img-icon16 icon-clock ml-3"></i>
                                            <span>{{ \App\Http\FrontendHelpers::getTimeFromDT($next_free_webinar->start_date) }}</span>
                                        </div>

                                        <p class="mt-4 text-justify">{{ str_limit(strip_tags($next_free_webinar->description), 200)}}</p>

                                        <a class="btn" href="{{ route('front.free-webinar', $next_free_webinar->id) }}">
                                            Registrer deg
                                        </a>
                                    @else
                                        @if($next_workshop)
                                            <h1 class="font-montserrat-semibold">
                                                {{ $next_workshop->title }}
                                            </h1>

                                            <div class="date-time-cont">
                                                <i class="img-icon16 icon-calendar"></i>
                                                <span>{{ \App\Http\FrontendHelpers::formatDate($next_workshop->date) }}</span>
                                                <i class="img-icon16 icon-clock ml-3"></i>
                                                <span>{{ \App\Http\FrontendHelpers::getTimeFromDT($next_workshop->date) }}</span>
                                            </div>

                                            <p class="mt-4 text-justify">{{ str_limit(strip_tags($next_workshop->description), 200)}}</p>

                                            <a class="btn" href="{{ route('front.workshop.show', $next_workshop->id) }}">
                                                Registrer deg
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div> <!-- end item__third -->
                    </div> <!-- end item -->

                    <div class="item">
                        <div class="item__third">
                            <div class="card">
                                <div class="card-header" style="">
                                    <img src="{{ asset('/images-new/home/girl-coffee.jpg') }}" alt="">
                                    <span class="title">
                                        Siste blogginnlegg
                                    </span>
                                </div>
                                <div class="card-body">
                                    @if ($latest_blog)
                                        <h1 class="font-montserrat-semibold">
                                            {{ $latest_blog->title }}
                                        </h1>

                                        <div class="date-time-cont">
                                            <i class="img-icon16 icon-calendar"></i>
                                            <span>{{ \App\Http\FrontendHelpers::formatDate($latest_blog->created_at) }}</span>
                                        </div>

                                        <p class="mt-4 text-justify">{{ str_limit(strip_tags($latest_blog->description), 200)}}</p>

                                        <a class="btn" href="{{ route('front.read-blog', $latest_blog->id) }}">
                                            Les mer
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div> <!-- end item__third -->
                    </div> <!-- end item -->

                    <div class="item">
                        <div class="item__third">
                            <div class="card">
                                <div class="card-header" style="">
                                    <img src="{{ asset('/images-new/home/coffee-paper.jpeg') }}" alt="">
                                    <span class="title">
                                        Neste webinar
                                    </span>
                                </div>
                                <div class="card-body">
                                    @if ($next_webinar)
                                        <h1 class="font-montserrat-semibold">
                                            {{ $next_webinar->title }}
                                        </h1>

                                        <div class="date-time-cont">
                                            <i class="img-icon16 icon-calendar"></i>
                                            <span>{{ \App\Http\FrontendHelpers::formatDate($next_webinar->start_date) }}</span>
                                            <i class="img-icon16 icon-clock ml-3"></i>
                                            <span>{{ \App\Http\FrontendHelpers::getTimeFromDT($next_webinar->start_date) }}</span>
                                        </div>

                                        <p class="mt-4 text-justify">{{ str_limit(strip_tags($next_webinar->description), 200)}}</p>

                                        <a class="btn" href="{{ url('/course/17?show_kursplan=1') }}">
                                            Se komplett liste her
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div> <!-- end item__third -->
                    </div> <!-- end item -->
                </div>
                <a href="#latest-seminar-carousel" class="left carousel-control" role="button" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left hide" aria-hidden="true"></span>
                </a>
                <a href="#latest-seminar-carousel" class="right carousel-control" role="button" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right hide" aria-hidden="true"></span>
                </a>
            </div>
        </div> <!-- end latest-seminar wrapper -->

        <div class="container">
            <div class="testimonial-row row">
                <div class="col-md-12">
                    <h1 class="text-center font-montserrat-semibold">
                        {{ trans('site.front.student-testimonial.heading') }}
                    </h1>
                    <div id="testimonials-carousel" class="carousel slide global-carousel"
                         data-ride="carousel" data-interval="15000">

                        <ul class="carousel-indicators">
                            <li data-target="#testimonials-carousel" data-slide-to="0" class="active"></li>
                            <li data-target="#testimonials-carousel" data-slide-to="1"></li>
                            <li data-target="#testimonials-carousel" data-slide-to="2"></li>
                        </ul>

                        <!-- The slideshow -->
                        <div class="container carousel-inner no-padding">
                            <div class="carousel-item active">
                                <div class="col-md-12">
                                    <div class="row testimonial-details-row">
                                        <p class="font-montserrat-medium">
                                            "Boken min, ”Uten vesentlige feil eller mangler” kom ut på Gyldendal
                                            våren 2017. Og med hånden på hjertet: Jeg vet ikke om jeg hadde
                                            klart det uten Forfatterskolen, og den støtten det ligger i å være
                                            en del av et skrivefellesskap. Jeg vil fortsette å la meg inspirere
                                            av Rektor Kristine og hennes medarbeidere på Forfatterskolen. Og
                                            ikke minst: Elevene".
                                        </p>
                                        <div class="user-details">
                                            <div class="images-container">
                                                <img src="{{ asset('images/feedback1.jpg') }}" class="user-image">
                                                <img src="{{ asset('images/book-covers/linda.jpg') }}" class="book-image">
                                            </div>
                                            <div class="user-info">
                                                <span class="font-montserrat-semibold theme-text">Linda Skomakerstuen</span>
                                                <span class="font-montserrat-regular">debutant på Gyldendal i 2017</span>
                                            </div>
                                        </div>
                                    </div> <!-- end testimonial-details-row -->
                                </div> <!-- end col-md-12 -->
                            </div> <!-- end carousel-item -->

                            <div class="carousel-item">
                                <div class="col-md-12">
                                    <div class="row testimonial-details-row">
                                        <p class="font-montserrat-medium">
                                            "Det har vært en utrolig stor glede å bli kjent med rektor Kristine
                                            Henningsen og resten av forfatterskolen. Jeg har lært å skrive med
                                            hjertet uten å miste hodet, og lært å se forskjellen. Samarbeidet
                                            har resultert i at min debutroman, Armageddon-algoritmen, kom ut i
                                            2017. Kristine har enestående evner til å inspirere og oppmuntre, og
                                            kan trekke på et imponerende nettverk av ressurspersoner. Hjertelig
                                            anbefalt.".
                                        </p>
                                        <div class="user-details">
                                            <div class="images-container">
                                                <img src="{{ asset('images/feedback2.jpg') }}" class="user-image">
                                                <img src="{{ asset('images/book-covers/petter.jpg') }}" class="book-image">
                                            </div>
                                            <div class="user-info">
                                                <span class="font-montserrat-semibold theme-text">Petter Fergestad</span>
                                                <span class="font-montserrat-regular">Forfatterdrøm i 2017</span>
                                            </div>
                                        </div>
                                    </div> <!-- end testimonial-details-row -->
                                </div> <!-- end col-md-12 -->
                            </div> <!-- end carousel-item -->

                            <div class="carousel-item">
                                <div class="col-md-12">
                                    <div class="row testimonial-details-row">
                                        <p class="font-montserrat-medium">
                                            "Å samtidig være medlem i Forfatterskolen og denne fantastiske
                                            gruppen har vært avgjørende for å greie å stå løpet ut. Har
                                            diskutert prosjektet med Kristine tidligere og hun er velvilligheten
                                            selv. Bøyer meg i støvet og har stor respekt for henne og jobben hun
                                            gjør. Jeg vil være elev for alltid Wenche, utgitt fagbokforfatter
                                            med: Å miste et barn"
                                        </p>
                                        <div class="user-details">
                                            <div class="images-container">
                                                <img src="{{ asset('images/feedback3.jpg') }}" class="user-image">
                                                <img src="{{ asset('images/book-covers/wenche.jpg') }}" class="book-image">
                                            </div>
                                            <div class="user-info">
                                                <span class="font-montserrat-semibold theme-text">Wenche Fuglseth Spjelkavik</span>
                                                <span class="font-montserrat-regular">debutant på Pax Forlag i 2017</span>
                                            </div>
                                        </div>
                                    </div> <!-- end testimonial-details-row -->
                                </div> <!-- end col-md-12 -->
                            </div> <!-- end carousel-item -->
                        </div> <!-- end carousel-inner -->

                        <!-- Left and right controls -->
                        <a class="carousel-control-prev" href="#testimonials-carousel" data-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </a>
                        <a class="carousel-control-next" href="#testimonials-carousel" data-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </a>

                    </div> <!-- end testimonials-carousel -->
                </div> <!-- end col-md-12 -->
            </div>
        </div> <!-- end container-->

        <div class="our-course-wrapper">
            <div class="container">
                <h1 class="font-montserrat-semibold">Våre Kurs</h1>
                <p class="font-montserrat-regular">
                    Å skrive et manus kan være en utfordring. Det mangler sjelden på ideer og lyst, men hvordan
                    komme i gang? Og hvordan lykkes med å skrive et godt manus – uten å gi opp på veien? Fortvil
                    ikke, vi har skreddersydde kurs til å hjelpe deg. I tillegg får du profesjonell veiledning og en
                    uunnværlig heiagjeng. Et godt bevis på at metoden vår funker: Vi har hjulpet mange elever med å
                    bli utgitt – og flere er på vei (se "utgitte elever").
                </p>
            </div> <!-- end container -->
        </div> <!-- end our-course-wrapper -->

        <div class="popular-course-wrapper">
            <div class="container">
                <div class="all-course theme-tabs">
                    <div class="tabs-container">
                        <ul class="nav nav-tabs">
                            <li><a data-toggle="tab" href="#home" class="active"><span>Populære kurs</span></a></li>
                        </ul>
                    </div> <!-- end tabs-container -->

                    <div class="tab-content">
                        <div id="home" class="tab-pane fade in active">
                            <div class="container">
                                <?php $featured = 0 ?>
                                @foreach( $popular_courses as $popular_course )
                                    @if( \App\Http\FrontendHelpers::isCourseAvailable($popular_course) && $featured == 0)
                                        <a href="{{ route('front.course.show', $popular_course->id) }}"
                                        class="featured-link">
                                            <div class="row featured-item" style="background-image: url({{$popular_course->course_image}})">
                                                <div class="details">
                                                    <div class="indicator">
                                                        Kurs
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
                                        @if( \App\Http\FrontendHelpers::isCourseAvailable($popular_course) )
                                            @if ($counter == 0)
                                                <?php $counter++?>
                                            @else
                                                <div class="col-md-6 mt-5 course-item">
                                                    <div class="card rounded-0 border-0">
                                                        <div class="card-header p-0 rounded-0"
                                                             style="background-image: url({{$popular_course->course_image}})">
                                                            <span>Kurs</span>
                                                        </div>
                                                        <div class="card-body">
                                                            <h3 class="font-montserrat-semibold">{{ str_limit(strip_tags($popular_course->title), 40)}}</h3>
                                                            <p class="font-montserrat-light mt-4">{{ str_limit(strip_tags($popular_course->description), 130)}}</p>
                                                            <a href="{{ route('front.course.show', $popular_course->id) }}"
                                                               class="site-btn-global rounded-0 mt-3 d-inline-block">Les mer</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    @endforeach
                                </div> <!-- end courses-container -->
                            </div> <!-- end container -->
                        </div> <!-- end #home -->
                    </div> <!-- end tab-content -->
                </div> <!-- end all-course -->
            </div> <!-- end container -->
        </div> <!-- end popular-course-wrapper -->

        <div id="poem-wrapper">
            <div class="container">
                <div class="heading">
                    <h1 class="d-inline-block font-montserrat-semibold">Ukens dikt</h1>
                    <a href="{{ route('front.poems') }}" class="btn d-inline-block">Les flere dikt</a>
                </div> <!-- end heading -->

                <?php
                    $latestPoem = $poems->first();
                ?>

                <div class="row poem-details">
                    <div class="col-sm-6 poem-author-container">
                            <img src="{{ asset($latestPoem->author_image) }}" class="author-image">
                        <div class="author-info">
                            <span class="indicator">Poem of the week</span>
                            <h3 class="font-weight-normal font-montserrat-regular">{{ $latestPoem->title }}</h3>
                            <h4 class="font-montserrat-light">{{ $latestPoem->author }}</h4>
                        </div>
                    </div>
                    <div class="col-sm-6 poem-container">
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
                    <h1 class="gdpr-title">Dine data, dine valg</h1>
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
                    <a href="{{ route('front.terms') }}">Vis meg mer</a>
                </div>
            </div>
        </div>
    @endif
@stop

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>
    <script>
        let url_link = '{{ route('front.agree-gdpr') }}';

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
            /*setTimeout(function(){
                $("#latest-seminar-carousel").find('.item').removeClass('slideLeft');
            },1000);*/
            //$(".carousel-inner").find('.item.active').removeClass('active').prev().addClass("active");
        });

        $(".multi-item-carousel .right").click(function(){
            currentHighlight = (currentHighlight + 1) % items.length;
            items.removeClass('active').eq(currentHighlight).addClass('active');
            /*setTimeout(function(){
                $("#latest-seminar-carousel").find('.item').removeClass('slideRight');
            },1000);*/
           //$(".carousel-inner").find('.item.active').removeClass('active').next().addClass("active");
        });

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
    </script>
@stop