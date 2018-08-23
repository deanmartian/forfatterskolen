@extends('frontend.layout')

@section('title')
<title>Forfatterskolen</title>
@stop

@section('content')
    {{--<div id="banner">
        <video autoplay muted loop id="myVideo">
            <source src="{{ URL::asset('video/background.mp4') }}" type="video/mp4">
        </video>

        <div class="content">
            <h1>Vi tilbyr kurs og veiledning</h1>
            <h3>for deg som vil gjøre alvor av skrivedrømmen</h3>

            <div class="container">
                <form method="POST" action="https://forfatterskolen.activehosted.com/proc.php" id="_form_1312_">
                    <p>Vil du ha vår inspirerende skriveplan? Få den gratis her!</p>
                    <div class="row">
                        <input type="hidden" name="u" value="1312" />
                        <input type="hidden" name="f" value="1312" />
                        <input type="hidden" name="s" />
                        <input type="hidden" name="c" value="0" />
                        <input type="hidden" name="m" value="0" />
                        <input type="hidden" name="act" value="sub" />
                        <input type="hidden" name="v" value="2" />
                        <div class="col-sm-4"><input type="text" name="fullname" class="form-control input-lg" placeholder="Fornavn" required></div>
                        <div class="col-sm-4"><input type="email" name="email" class="form-control input-lg" placeholder="Epost" required></div>
                        <div class="col-sm-4"><button type="submit" class="btn btn-orange btn-block btn-lg">Ja, jeg vil ha gratis tips!</button></div>
                        <span><em>PS! Vi deler ikke e-postadressen din med noen</em></span>
                    </div>
                </form>
            </div>
        </div>
    </div>--}}
<div class="hero">
<div>
<h1>Vi tilbyr kurs og veiledning</h1>
<h3>for deg som vil gjøre alvor av skrivedrømmen</h3>

<div class="container">
<form method="POST" action="https://forfatterskolen.activehosted.com/proc.php" id="_form_1312_">
    <p>Vil du ha vår inspirerende skriveplan? Få den gratis her!</p>
    <div class="row">
            <input type="hidden" name="u" value="1312" />
            <input type="hidden" name="f" value="1312" />
            <input type="hidden" name="s" />
            <input type="hidden" name="c" value="0" />
            <input type="hidden" name="m" value="0" />
            <input type="hidden" name="act" value="sub" />
            <input type="hidden" name="v" value="2" />
            <div class="col-sm-4"><input type="text" name="fullname" class="form-control input-lg" placeholder="Fornavn" required></div>
            <div class="col-sm-4"><input type="email" name="email" class="form-control input-lg" placeholder="Epost" required></div>
            <div class="col-sm-4"><button type="submit" class="btn btn-orange btn-block btn-lg">Ja, jeg vil ha gratis tips!</button></div>
            <span><em>PS! Vi deler ikke e-postadressen din med noen</em></span>
    </div>
</form>
</div>
</div>
</div>

{{--<h3 class="all-course-header"><span class="highlight">Våre</span> kurs</h3>--}}

    <div class="col-sm-12 all-course">
        <div class="container">
            <div class="col-sm-4">
                <h3 class="highlight">
                    Neste webinar
                </h3>
                @if ($next_webinar)
                    <div class="all-course-course">
                        <div class="image" style="background-image: url({{ $next_webinar->image ?: asset('/images/no_image.png')}})"></div>
                        <div class="details">
                            <div class="course-info">
                                <h4>{{ $next_webinar->title }}</h4>
                                <p>{{ str_limit(strip_tags($next_webinar->description), 180)}}</p>
                            </div>
                        </div>
                        <a class="buy_now" href="{{ url('/course/17?show_kursplan=1') }}">
                            Se komplett liste her
                        </a>
                    </div>
                @endif
            </div> <!-- end next webinar -->

            <div class="col-sm-4">
                <h3 class="highlight">
                    Neste gratis webinar
                </h3>
                <div class="all-course-course">
                    @if($next_free_webinar)
                        <div class="all-course-course">
                            <div class="image" style="background-image: url({{ $next_free_webinar->image ?: asset('/images/no_image.png')}})"></div>
                            <div class="details">
                                <div class="course-info">
                                    <h4>{{ $next_free_webinar->title }}</h4>
                                    <p>{{ str_limit(strip_tags($next_free_webinar->description), 180)}}</p>
                                </div>
                            </div>
                            <a class="buy_now" href="{{ route('front.free-webinar', $next_free_webinar->id) }}">Register Deg</a>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-sm-4">
                <h3 class="highlight">
                    Siste blogginnlegg
                </h3>
                <div class="all-course-course">
                    @if($latest_blog)
                        <div class="all-course-course">
                            <div class="image" style="background-image: url({{ $latest_blog->image ?: asset('/images/no_image.png')}})"></div>
                            <div class="details">
                                <div class="course-info">
                                    <h4>{{ $latest_blog->title }}</h4>
                                    <p>{{ str_limit(strip_tags($latest_blog->description), 180)}}</p>
                                </div>
                            </div>
                            <a class="buy_now" href="{{ route('front.read-blog', $latest_blog->id) }}">Les</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12" style="background-color: #f00; height: 2px;">
    </div>

    <div class="clearfix"></div>

<div class="all-course theme-tabs">
    <div class="tabs-container">
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#home"><span>Populære kurs</span></a></li>
          <li><a data-toggle="tab" href="#menu1"><span>Gratis kurs</span></a></li>
        </ul>
    </div>
    <div class="tab-content">
      <div id="home" class="tab-pane fade in active">
        <div class="container">
            <div class="row">
                @foreach( $popular_courses as $popular_course )
                @if( FrontendHelpers::isCourseAvailable($popular_course) )
                <div class="col-sm-4">
                    <div class="all-course-course">
                        <div class="image" style="background-image: url({{$popular_course->course_image}})"></div>
                        <div class="details">
                            <div class="course-info">
                                <h4>{{ $popular_course->title }}</h4>
                                <p>{{ str_limit(strip_tags($popular_course->description), 180)}}</p>
                            </div>
                        </div>
                        <a class="buy_now" href="{{ route('front.course.show', $popular_course->id) }}">Les mer</a>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>

        <br />
        <a class="btn btn-theme" href="{{ route('front.course.index') }}">Se alle kurs</a>
      </div>

      <div id="menu1" class="tab-pane fade">
        <div class="container">
            <div class="row">
                @foreach( $free_courses as $free_course )
                <div class="col-sm-4">
                    <div class="all-course-course">
                        <div class="image" style="background-image: url({{ $free_course->course_image }})"></div>
                        <div class="details">
                            <div class="course-info">
                                <h4>{{ $free_course->title }}</h4>
                                <p>{{ str_limit(strip_tags($free_course->description), 180)}}</p>
                            </div>
                        </div>
                        <a class="buy_now" href="{{ $free_course->url }}">Les mer</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
      </div>
    </div>
</div>

    <div class="clearfix"></div>

<div class="feedbacks">
    <h3><span class="highlight">Hva</span> våre elever sier</h3>
    <div id="myCarousel" class="carousel slide" data-ride="carousel" data-interval="10000">
      <ol class="carousel-indicators">
        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#myCarousel" data-slide-to="1"></li>
        <li data-target="#myCarousel" data-slide-to="2"></li>
      </ol>

      <div class="carousel-inner text-center">
        <div class="item active">
            <div class="feedback">
                <div class="feedback-thumb" style="background-image: url({{ asset('images/feedback1.jpg') }})"></div>
                <div class="book-cover"><img src="{{ asset('images/book-covers/linda.jpg') }}" height="130"></div>
                <em>"Boken min, ”Uten vesentlige feil eller mangler” kom ut på Gyldendal våren 2017. Og med hånden på hjertet: Jeg vet ikke om jeg hadde klart det uten Forfatterskolen, og den støtten det ligger i å være en del av et skrivefellesskap. Jeg vil fortsette å la meg inspirere av Rektor Kristine og hennes medarbeidere på Forfatterskolen. Og ikke minst: Elevene".</em>
                <span>- Linda Skomakerstuen, debutant på Gyldendal i 2017</span>
            </div>
        </div>

        <div class="item">
            <div class="feedback">
                <div class="feedback-thumb" style="background-image: url({{ asset('images/feedback2.jpg') }})"></div>
                <div class="book-cover"><img src="{{ asset('images/book-covers/petter.jpg') }}" height="130"></div>
                <em>"Det har vært en utrolig stor glede å bli kjent med rektor Kristine Henningsen og resten av forfatterskolen. Jeg har lært å skrive med hjertet uten å miste hodet, og lært å se forskjellen. Samarbeidet har resultert i at min debutroman, Armageddon-algoritmen, kom ut i 2017. Kristine har enestående evner til å inspirere og oppmuntre, og kan trekke på et imponerende nettverk av ressurspersoner. Hjertelig anbefalt.".</em>
                <span>- Petter Fergestad, Forfatterdrøm i 2017</span>
            </div>
        </div>

        <div class="item">
            <div class="feedback">
                <div class="feedback-thumb" style="background-image: url({{ asset('images/feedback3.jpg') }})"></div>
                <div class="book-cover"><img src="{{ asset('images/book-covers/wenche.jpg') }}" height="130"></div>
                <em>"Å samtidig være medlem i Forfatterskolen og denne fantastiske gruppen har vært avgjørende for å greie å stå løpet ut. Har diskutert prosjektet med Kristine tidligere og hun er velvilligheten selv. Bøyer meg i støvet og har stor respekt for henne og jobben hun gjør. Jeg vil være elev for alltid Wenche, utgitt fagbokforfatter med: Å miste et barn".</em>
                <span>- Wenche Fuglseth Spjelkavik, debutant på Pax Forlag i 2017</span>
            </div>
        </div>
      </div>
    </div>
</div>


<div class="this-we">
    <h3 class="text-center"><span class="highlight">Dette</span> tilbyr vi</h3>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="ic text-center">
                    <img src="{{asset('/images/ic_inkpot.png')}}">
                    <div>
                    <h5>Unikt skrivemiljø</h5>
                    Vi har et støttende, inspirerende og utviklende skrivemiljø på skolen – med plass til flere!
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="ic text-center">
                    <img src="{{asset('/images/ic_light.png')}}">
                    <div>
                    <h5>Hva skjer?</h5>
                    Vi tipser og holder deg oppdatert på alt som skjer på skrivefronten
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="ic">
                    <img src="{{asset('/images/ic_about.png')}}">
                    <div>
                    <h5>Om oss</h5>
                    Forfatterskolen tilbyr flere typer kurs og veiledning for deg som vil gjøre alvor av forfatterdrømmen
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop