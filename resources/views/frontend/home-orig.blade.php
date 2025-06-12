@extends('frontend.layout')

@section('title')
<title>Forfatterskolen</title>
@stop

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">
@stop

@section('content')
    <div class="front-page">
        <div class="header">
            <div class="container">
            </div> <!-- end container -->
        </div> <!-- end header -->

        <div class="first-container">
            <div class="container">
                <div class="row first-container-row">
                    <div class="col-md-4">
                        <div class="card" style="background: linear-gradient(rgba(56, 56, 56, .45), rgba(56, 56, 56, .45)),
                                url({{ /*$next_webinar ? ($next_webinar->image ?: asset('/images-new/front-page/coffee-paper.png'))
                                :*/ asset('/images-new/front-page/coffee-paper.png')}}) center / cover;">
                            <div class="card-header">
                                <h3>
                                    Neste webinar
                                </h3>
                            </div>
                            <div class="card-body text-center">
                                @if ($next_webinar)
                                    <h1>
                                        {{ $next_webinar->title }}
                                    </h1>
                                    <div class="date-time-cont">
                                        <i class="img-icon16 icon-calendar-red"></i>
                                        <span>{{ \App\Http\FrontendHelpers::formatDate($next_webinar->start_date) }}</span>
                                        <i class="img-icon16 icon-clock-red ml-3"></i>
                                        <span>{{ \App\Http\FrontendHelpers::getTimeFromDT($next_webinar->start_date) }}</span>
                                    </div>

                                    <p>{{ str_limit(strip_tags($next_webinar->description), 180)}}</p>

                                    <div class="button-container">
                                        <a class="btn buy-btn" href="{{ url('/course/17?show_kursplan=1') }}">
                                            Se komplett liste her
                                        </a>
                                    </div>
                                @endif
                            </div> <!-- end card-body -->
                        </div> <!-- end card -->
                    </div> <!-- end col-md-4 -->

                    <div class="col-md-4">
                        <?php
                            $image = asset('/images-new/front-page/hand-pen.png');
                            /*if($next_free_webinar) {
                                $image = $next_free_webinar->image ?: asset('/images-new/front-page/hand-pen.png');
                            } else {
                                if ($next_workshop) {
                                    $image = $next_workshop->image ?: asset('/images-new/front-page/hand-pen.png');
                                }
                            }*/
                        ?>
                        <div class="card" style="background: linear-gradient(rgba(56, 56, 56, .45), rgba(56, 56, 56, .45)),
                                url({{ $image }}) center / cover;">
                            <div class="card-header">
                                <h3>
                                    {{ !$next_free_webinar && $next_workshop ? 'Neste Workshop' : 'Neste gratis webinar' }}
                                </h3>
                            </div>
                            <div class="card-body text-center">
                                @if($next_free_webinar)
                                    <h1>
                                        {{ $next_free_webinar->title }}
                                    </h1>
                                    <div class="date-time-cont">
                                        <i class="img-icon16 icon-calendar-red"></i>
                                        <span>{{ \App\Http\FrontendHelpers::formatDate($next_free_webinar->start_date) }}</span>
                                        <i class="img-icon16 icon-clock-red ml-3"></i>
                                        <span>{{ \App\Http\FrontendHelpers::getTimeFromDT($next_free_webinar->start_date) }}</span>
                                    </div>

                                    <p>{{ str_limit(strip_tags($next_free_webinar->description), 180)}}</p>

                                    <div class="button-container">
                                        <a class="btn buy-btn" href="{{ route('front.free-webinar', $next_free_webinar->id) }}">
                                            Registrer deg
                                        </a>
                                    </div>
                                @else
                                    @if($next_workshop)
                                        <h1>
                                            {{ $next_workshop->title }}
                                        </h1>
                                        <div class="date-time-cont">
                                            <i class="img-icon16 icon-calendar-red"></i>
                                            <span>{{ \App\Http\FrontendHelpers::formatDate($next_workshop->date) }}</span>
                                            <i class="img-icon16 icon-clock-red ml-3"></i>
                                            <span>{{ \App\Http\FrontendHelpers::getTimeFromDT($next_workshop->date) }}</span>
                                        </div>

                                        <p>{{ str_limit(strip_tags($next_workshop->description), 180)}}</p>

                                        <div class="button-container">
                                            <a class="btn buy-btn" href="{{ route('front.workshop.show', $next_workshop->id) }}">
                                                Les mer
                                            </a>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div> <!-- end card -->
                    </div> <!-- end col-md-4 -->

                    <div class="col-md-4">
                        <div class="card" style="background: linear-gradient(rgba(56, 56, 56, .45), rgba(56, 56, 56, .45)),
                                url({{ /*$latest_blog ? ($latest_blog->image ?: asset('/images-new/front-page/girl-coffee.png'))
                                :*/ asset('/images-new/front-page/girl-coffee.png')}}) center / cover;">
                            <div class="card-header">
                                <h3>
                                    Siste blogginnlegg
                                </h3>
                            </div>
                            <div class="card-body text-center">
                                @if ($latest_blog)
                                    <h1>
                                        {{ $latest_blog->title }}
                                    </h1>
                                    <div class="date-time-cont">
                                        <i class="img-icon16 icon-calendar-red"></i>
                                        <span>{{ \App\Http\FrontendHelpers::formatDate($latest_blog->created_at) }}</span>
                                    </div>

                                    <p>{{ str_limit(strip_tags($latest_blog->description), 180)}}</p>

                                    <div class="button-container">
                                        <a class="btn buy-btn" href="{{ route('front.read-blog', $latest_blog->id) }}">
                                            Les mer
                                        </a>
                                    </div>
                                @endif
                            </div> <!-- end card-body -->
                        </div> <!-- end card -->
                    </div> <!-- end col-md-4 -->
                </div> <!-- end first-container-row -->

                <div class="row testimonial-row">
                    <div class="col-md-12">
                        <h1 class="text-center">{{ trans('site.front.student-testimonial.heading') }}</h1>
                        <div id="testimonials-carousel" class="carousel slide global-carousel"
                             data-ride="carousel" data-interval="15000">

                            <ul class="carousel-indicators">
                                <li data-target="#testimonials-carousel" data-slide-to="0" class="active"></li>
                                <li data-target="#testimonials-carousel" data-slide-to="1"1></li>
                                <li data-target="#testimonials-carousel" data-slide-to="2"></li>
                            </ul>

                            <!-- The slideshow -->
                            <div class="container carousel-inner no-padding">
                                <div class="carousel-item active">
                                    <div class="col-md-12">
                                        <div class="row testimonial-details-row">
                                            <div class="col-md-3 image-container" style="background-image: url({{ asset('images/feedback1.jpg') }})">
                                                <img src="{{ asset('images/book-covers/linda.jpg') }}" alt="">
                                            </div>
                                            <div class="col-md-9 details-container">
                                                <h1>Linda Skomakerstuen</h1>
                                                <h3>debutant på Gyldendal i 2017</h3>
                                                <blockquote>
                                                    "Boken min, ”Uten vesentlige feil eller mangler” kom ut på Gyldendal
                                                    våren 2017. Og med hånden på hjertet: Jeg vet ikke om jeg hadde
                                                    klart det uten Forfatterskolen, og den støtten det ligger i å være
                                                    en del av et skrivefellesskap. Jeg vil fortsette å la meg inspirere
                                                    av Rektor Kristine og hennes medarbeidere på Forfatterskolen. Og
                                                    ikke minst: Elevene".
                                                </blockquote>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- end carousel-item -->

                                <div class="carousel-item">
                                    <div class="col-md-12">
                                        <div class="row testimonial-details-row">
                                            <div class="col-md-3 image-container" style="background-image: url({{ asset('images/feedback2.jpg') }})">
                                                <img src="{{ asset('images/book-covers/petter.jpg') }}" alt="">
                                            </div>
                                            <div class="col-md-9 details-container">
                                                <h1>Petter Fergestad</h1>
                                                <h3>Forfatterdrøm i 2017</h3>
                                                <blockquote>
                                                    "Det har vært en utrolig stor glede å bli kjent med rektor Kristine
                                                    Henningsen og resten av forfatterskolen. Jeg har lært å skrive med
                                                    hjertet uten å miste hodet, og lært å se forskjellen. Samarbeidet
                                                    har resultert i at min debutroman, Armageddon-algoritmen, kom ut i
                                                    2017. Kristine har enestående evner til å inspirere og oppmuntre, og
                                                    kan trekke på et imponerende nettverk av ressurspersoner. Hjertelig
                                                    anbefalt.".
                                                </blockquote>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- end carousel-item -->

                                <div class="carousel-item">
                                    <div class="col-md-12">
                                        <div class="row testimonial-details-row">
                                            <div class="col-md-3 image-container" style="background-image: url({{ asset('images/feedback3.jpg') }})">
                                                <img src="{{ asset('images/book-covers/wenche.jpg') }}" alt="">
                                            </div>
                                            <div class="col-md-9 details-container">
                                                <h1>Wenche Fuglseth Spjelkavik</h1>
                                                <h3>debutant på Pax Forlag i 2017</h3>
                                                <blockquote>
                                                    "Å samtidig være medlem i Forfatterskolen og denne fantastiske
                                                    gruppen har vært avgjørende for å greie å stå løpet ut. Har
                                                    diskutert prosjektet med Kristine tidligere og hun er velvilligheten
                                                    selv. Bøyer meg i støvet og har stor respekt for henne og jobben hun
                                                    gjør. Jeg vil være elev for alltid Wenche, utgitt fagbokforfatter
                                                    med: Å miste et barn"
                                                </blockquote>
                                            </div>
                                        </div>
                                    </div>
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
                </div> <!-- end testimonial-row -->
            </div><!-- end container -->
        </div> <!-- end first-container -->

        <div class="course-container">
            <div class="container">
                {{--<div class="row">
                    <div class="col-sm-12 video-container">
                        --}}{{--<iframe src="https://fast.wistia.com/embed/medias/scuv6yv5qy" frameborder="0" allowfullscreen="allowfullscreen"></iframe>--}}{{--
                        <img src="{{ asset('images-new/adult-reading-book.jpg') }}" alt="">
                    </div>
                </div>--}}

                <div class="row optin-row">
                    <div class="col-lg-6 col-md-offset-3 col-md-12">
                        <div class="form-container">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <h1 class="title">{{ trans('site.front.main-form.heading') }}</h1>
                                    <p class="mt-4">{{ trans('site.front.main-form.heading-description') }}</p>

                                    <div class="form-details">
                                        <h2>{{ trans('site.front.main-form.sub-heading') }}</h2>
                                        <hr>

                                        <form method="POST" action="{{ route('front.home.submit') }}">
                                            {{ csrf_field() }}

                                            <div class="form-group">
                                                <input type="text" name="name" class="form-control" placeholder="Fornavn" required>
                                            </div>

                                            <div class="form-group">
                                                <input type="email" name="email" class="form-control" placeholder="Epost" required>
                                            </div>

                                            <div class="form-group custom-checkbox">
                                                <input type="checkbox" name="terms" id="terms" required>
                                                <label for="terms">Jeg aksepterer <a href="{{ route('front.opt-in-terms') }}"
                                                                                     class="font-weight-bold" target="_blank">vilkårene</a></label>
                                            </div>

                                            {!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::renderJS() !!}
                                            {!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::display() !!}
                                            <span>PS! Vi deler ikke e-postadressen din med noen</span>

                                            <button type="submit" class="btn site-btn-global">Ja, jeg vil ha gratis tips!</button>

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

                                    </div>
                                </div>
                            </div>
                        </div> <!-- end form-container -->
                    </div> <!-- end col-md-6 -->
                </div> <!-- end row -->

                <div class="row all-course theme-tabs">
                    <div class="tabs-container">
                        <ul class="nav nav-tabs">
                            <li><a data-toggle="tab" href="#home" class="active"><span>Populære kurs</span></a></li>
                            <li><a data-toggle="tab" href="#menu1"><span>Gratis kurs</span></a></li>
                        </ul>
                    </div> <!-- end tabs-container -->

                    <div class="tab-content">
                        <div id="home" class="tab-pane fade in active">
                            <div class="container">
                                <?php $featured = 0 ?>
                                @foreach( $popular_courses as $popular_course )
                                    @if( FrontendHelpers::isCourseAvailable($popular_course) && $featured == 0)
                                        <div class="row featured-item">
                                            <div class="col-md-6 left-container">
                                                <div class="image-container">
                                                    <img src="{{$popular_course->course_image}}" alt="">
                                                    <h2>Kurs</h2>
                                                </div>
                                            </div>
                                            <div class="col-md-6 right-container">
                                                <div class="details-container">
                                                    <h1>{{ $popular_course->title}}</h1>
                                                    <p>{{ str_limit(strip_tags($popular_course->description), 250)}}</p>

                                                    <a href="{{ route('front.course.show', $popular_course->id) }}"
                                                       class="link-with-arrow-red font-16">Les mer</a>

                                                    <div class="date-time-cont">
                                                        @if ($popular_course->start_date)
                                                            <i class="img-icon16 icon-calendar"></i>
                                                            <span>{{ \App\Http\FrontendHelpers::formatDate($popular_course->start_date) }}</span>
                                                        @endif
                                                        {{--<i class="img-icon16 icon-clock ml-5"></i>
                                                        <span>{{ \App\Http\FrontendHelpers::getTimeFromDT($popular_course->start_date) }}</span>--}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php $featured++?>
                                    @endif
                                @endforeach

                                <?php $counter = 0 ?>
                                <div class="row courses-container">
                                    @foreach( $popular_courses as $popular_course )
                                        @if( FrontendHelpers::isCourseAvailable($popular_course) )
                                            @if ($counter == 0)
                                                <?php $counter++?>
                                            @else
                                                <div class="col-md-6 mt-5 course-item">
                                                    <div class="row">
                                                        <div class="col-sm-6 image-item">
                                                            <div class="image-container">
                                                                <img src="{{$popular_course->course_image}}" alt="">
                                                                <h4>Kurs</h4>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 details-item">
                                                            <div class="details-container">
                                                                <h3>{{ str_limit(strip_tags($popular_course->title), 40)}}</h3>
                                                                <p>{{ str_limit(strip_tags($popular_course->description), 130)}}</p>

                                                                <a href="{{ route('front.course.show', $popular_course->id) }}"
                                                                   class="link-with-arrow-red font-16">Les mer</a>

                                                                <div class="date-time-cont">
                                                                    @if ($popular_course->start_date)
                                                                        <i class="img-icon16 icon-calendar"></i>
                                                                        <span>{{ \App\Http\FrontendHelpers::formatDate($popular_course->start_date) }}</span>
                                                                    @endif
                                                                    {{--<i class="img-icon16 icon-clock ml-5"></i>
                                                                    <span>{{ \App\Http\FrontendHelpers::getTimeFromDT($popular_course->start_date) }}</span>--}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            <a class="btn site-btn-global mt-5" href="{{ route('front.course.index') }}">Se alle kurs</a>
                        </div>

                        <div id="menu1" class="tab-pane fade in">
                            <div class="container">
                                <?php $featured = 0 ?>
                                @foreach( $free_courses as $free_course )
                                    @if( \App\Http\FrontendHelpers::isCourseAvailable($free_course) && $featured == 0)
                                        <div class="row featured-item">
                                            <div class="col-md-6 left-container">
                                                <div class="image-container">
                                                    <img src="{{$free_course->course_image}}" alt="">
                                                    <h2>Course</h2>
                                                </div>
                                            </div>
                                            <div class="col-md-6 right-container">
                                                <div class="details-container">
                                                    <h1>{{ $free_course->title}}</h1>
                                                    <p>{{ str_limit(strip_tags($free_course->description), 250)}}</p>

                                                    <a href="{{ $free_course->url }}"
                                                       class="link-with-arrow-red font-16">Les mer</a>

                                                    <div class="date-time-cont">
                                                        @if ($free_course->start_date)
                                                            <i class="img-icon16 icon-calendar"></i>
                                                            <span>{{ \App\Http\FrontendHelpers::formatDate($free_course->start_date) }}</span>
                                                        @endif
                                                        {{--<i class="img-icon16 icon-clock ml-5"></i>
                                                        <span>{{ \App\Http\FrontendHelpers::getTimeFromDT($free_course->start_date) }}</span>--}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php $featured++?>
                                    @endif
                                @endforeach

                                <?php $counter = 0 ?>
                                <div class="row courses-container">
                                    @foreach( $free_courses as $free_course )
                                        @if( \App\Http\FrontendHelpers::isCourseAvailable($free_course) )
                                            @if ($counter == 0)
                                                <?php $counter++?>
                                            @else
                                                <div class="col-md-6 mt-5 course-item">
                                                    <div class="row">
                                                        <div class="col-sm-6 image-item">
                                                            <div class="image-container">
                                                                <img src="{{$free_course->course_image}}" alt="">
                                                                <h4>Course</h4>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 details-item">
                                                            <div class="details-container">
                                                                <h3>{{ str_limit(strip_tags($free_course->title), 40)}}</h3>
                                                                <p>{{ str_limit(strip_tags($free_course->description), 130)}}</p>

                                                                <a href="{{ $free_course->url }}"
                                                                   class="link-with-arrow-red font-16">Les mer</a>

                                                                <div class="date-time-cont">
                                                                    @if ($free_course->start_date)
                                                                        <i class="img-icon16 icon-calendar"></i>
                                                                        <span>{{ \App\Http\FrontendHelpers::formatDate($free_course->start_date) }}</span>
                                                                    @endif
                                                                    {{--<i class="img-icon16 icon-clock ml-5"></i>
                                                                    <span>{{ \App\Http\FrontendHelpers::getTimeFromDT($popular_course->start_date) }}</span>--}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> <!-- end course-item -->
                                            @endif
                                        @endif
                                    @endforeach
                                </div> <!-- end courses-container -->
                            </div> <!-- end container -->
                        </div> <!-- end menu1 -->
                    </div> <!-- end tab-content -->
                </div> <!-- end all-course -->
            </div> <!-- end container -->
        </div> <!-- end course-container -->

        <div class="poem-container">
            <div class="container">
                <div class="row">
                    <?php
                        $latestPoem = $poems->first();
                    ?>
                    <h1 class="font-barlow-regular text-center w-100 title">Ukens dikt</h1>

                        <div class="card w-100">
                            <div class="card-header">
                                <div class="title-container">
                                    <h1 class="font-quicksand-medium">{{ $latestPoem->title }}</h1>
                                    <h2 class="font-quicksand-medium">{{ $latestPoem->author }}</h2>
                                </div>
                                <div class="image-container"
                                     style="background-image: url({{ asset($latestPoem->author_image) }})"></div>
                            </div>
                            <div class="card-body">
                                <div class="poem-text-container text-center">
                                    <?php
                                        //$html   = $latestPoem->poem;
                                        // remove first empty array value using array_filter
                                        //$content = array_filter(explode("<p>", $html));
                                        // divided the array in 2
                                        /*$pieces = array_chunk($content, ceil(count($content) / 2));
                                        foreach($pieces as $piece) {
                                            echo '<div class="col-sm-6 px-0">';
                                                echo '<p>'.implode('</p><p>', $piece).'</p>';
                                            echo '</div>';
                                        }*/
                                    ?>
                                    {!! $latestPoem->poem !!}
                                </div>

                                <a class="btn site-btn-global mt-5 pull-right" href="{{ route('front.poems') }}">
                                    Les flere dikt
                                </a>
                            </div>
                        </div>
                </div> <!-- end row -->
            </div> <!-- end container -->
        </div> <!-- end poem container-->

        <div class="poems-container" style="display: none">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <h1 class="text-center">Dikt fra våre elever</h1>
                    </div>

                    <?php
                        $poems_chunk = $poems->chunk(3);
                    ?>
                    <div id="poems-carousel" class="carousel slide global-carousel" data-ride="carousel"
                         data-interval="10000">
                        <!-- Indicators -->
                        <ul class="carousel-indicators">
                            @for($i=0; $i<=$poems_chunk->count() - 1;$i++)
                                <li data-target="#poems-carousel" data-slide-to="{{$i}}" @if($i == 0) class="active" @endif></li>
                            @endfor
                        </ul>

                        <!-- The slideshow -->
                        <div class="container carousel-inner no-padding">
                            @foreach($poems_chunk as $k => $poems)
                                <div class="carousel-item {{ $k==0 ? 'active' : '' }}">
                                    @foreach($poems as $i => $poem)
                                        <?php $i+=2;/* to make it divisible by 3 */?>

                                        {{-- check if not divisible by 3 --}}
                                        <div class="row">
                                            @if ($i %3 != 0)
                                                <div class="col-sm-3">
                                                    <div class="panel panel-default">
                                                        <img src="{{ asset($poem->author_image) }}" alt="">
                                                    </div>
                                                </div>
                                                <div class="col-sm-9">
                                                    <div class="poem-details">
                                                        <h2> {{ $poem->title }} </h2>
                                                        <h3 class="mb-5"> {{ $poem->author }} </h3>
                                                        <?php
                                                            $html   = $poem->poem;
                                                            // remove first empty array value using array_filter
                                                            $content = array_filter(explode("<p>", $html));
                                                            // divided the array in 2
                                                            $pieces = array_chunk($content, ceil(count($content) / 2));
                                                            foreach($pieces as $piece) {
                                                                echo '<div class="col-md-6 px-0">';
                                                                    echo '<p>'.implode('</p><p>', $piece).'</p>';
                                                                echo '</div>';
                                                            }
                                                        ?>
                                                        {{--{!! $poem->poem !!}--}}
                                                    </div>
                                                </div>
                                            @else
                                                <div class="col-sm-9">
                                                    <div class="poem-details">
                                                        <h2> {{ $poem->title }} </h2>
                                                        <h3 class="mb-5"> {{ $poem->author }} </h3>
                                                        <?php
                                                            $html   = $poem->poem;
                                                            // remove first empty array value using array_filter
                                                            $content = array_filter(explode("<p>", $html));
                                                            // divided the array in 2
                                                            $pieces = array_chunk($content, ceil(count($content) / 2));
                                                            foreach($pieces as $piece) {
                                                                echo '<div class="col-md-6 px-0">';
                                                                    echo '<p>'.implode('</p><p>', $piece).'</p>';
                                                                echo '</div>';
                                                            }
                                                        ?>
                                                        {{--{!! $poem->poem !!}--}}
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="panel panel-default">
                                                        <img src="{{ asset($poem->author_image) }}" alt="">
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
            theme: "minimal-dark",
            scrollInertia: 500,

        });
    </script>
@stop