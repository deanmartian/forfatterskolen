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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&family=Source+Sans+3:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .hero-section {
            max-width: 1140px;
            margin: 0 auto;
            padding: 3.5rem 2rem 0;
            display: grid;
            grid-template-columns: 1fr 420px;
            gap: 3rem;
            align-items: start;
            min-height: 85vh;
        }

        .hero-section__content {
            padding-top: 2rem;
        }

        .hero-section__eyebrow {
            font-family: 'Source Sans 3', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 0.75rem;
            font-weight: 500;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: #8a8580;
            margin-bottom: 1.25rem;
        }

        .hero-section__heading {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: clamp(2.5rem, 4.5vw, 3.5rem);
            font-weight: 700;
            line-height: 1.1;
            color: #1a1a1a;
            margin-bottom: 1.5rem;
            max-width: 520px;
        }

        .hero-section__heading em {
            font-style: italic;
            color: var(--secondary-red, #852635);
        }

        .hero-section__description {
            font-family: 'Source Sans 3', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 1.125rem;
            font-weight: 300;
            line-height: 1.7;
            color: #5a5550;
            max-width: 440px;
            margin-bottom: 2rem;
        }

        .hero-section__ctas {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }

        .hero-cta {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-family: 'Source Sans 3', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            border-radius: 6px;
            padding: 0.8rem 1.75rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .hero-cta--primary {
            background: var(--secondary-red, #852635);
            color: #fff;
            border: 2px solid var(--secondary-red, #852635);
        }

        .hero-cta--primary:hover {
            background: #9c2e40;
            border-color: #9c2e40;
            transform: translateY(-1px);
            color: #fff;
            text-decoration: none;
        }

        .hero-cta--primary .hero-cta__arrow {
            transition: transform 0.2s;
        }

        .hero-cta--primary:hover .hero-cta__arrow {
            transform: translateX(3px);
        }

        .hero-cta--secondary {
            background: transparent;
            color: #1a1a1a;
            border: 1.5px solid rgba(0, 0, 0, 0.1);
        }

        .hero-cta--secondary:hover {
            border-color: var(--secondary-red, #852635);
            color: var(--secondary-red, #852635);
            text-decoration: none;
        }

        .hero-section__stats {
            display: flex;
            gap: 2.5rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        .hero-stat {
            display: flex;
            flex-direction: column;
        }

        .hero-stat__number {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 2rem;
            font-weight: 700;
            color: #1a1a1a;
            line-height: 1;
            margin-bottom: 0.25rem;
        }

        .hero-stat__label {
            font-family: 'Source Sans 3', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 0.8rem;
            font-weight: 400;
            color: #8a8580;
            letter-spacing: 0.3px;
        }

        .hero-section__image-wrapper {
            position: relative;
            align-self: stretch;
        }

        .hero-section__image-container {
            position: relative;
            width: 100%;
            height: 100%;
            min-height: 560px;
            border-radius: 12px;
            overflow: hidden;
            background: #e8e4df;
        }

        .hero-section__image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top center;
        }

        .hero-section__quote {
            position: absolute;
            bottom: 1.5rem;
            right: -1.5rem;
            background: #fff;
            border-radius: 8px;
            padding: 1.25rem 1.5rem;
            max-width: 260px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
        }

        .hero-section__quote::before {
            content: '';
            position: absolute;
            top: 1.25rem;
            left: -4px;
            width: 4px;
            height: 32px;
            background: var(--secondary-red, #852635);
            border-radius: 2px;
        }

        .hero-section__quote-text {
            font-family: 'Playfair Display', Georgia, serif;
            font-style: italic;
            font-size: 0.95rem;
            line-height: 1.5;
            color: #1a1a1a;
            margin-bottom: 0.5rem;
        }

        .hero-section__quote-author {
            font-family: 'Source Sans 3', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 0.75rem;
            color: #8a8580;
            font-weight: 500;
        }

        .hero-banner {
            max-width: 1140px;
            margin: 0 auto;
            padding: 0 2rem 3rem;
        }

        .hero-banner__inner {
            background: #faf8f5;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 1.5rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
        }

        .hero-banner__left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .hero-banner__icon {
            width: 44px;
            height: 44px;
            background: rgba(134, 39, 54, 0.08);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .hero-banner__icon svg {
            width: 22px;
            height: 22px;
        }

        .hero-banner__title {
            font-family: 'Source Sans 3', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 0.15rem;
        }

        .hero-banner__sub {
            font-family: 'Source Sans 3', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 0.825rem;
            color: #5a5550;
            font-weight: 400;
        }

        .hero-banner__cta {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-family: 'Source Sans 3', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--secondary-red, #852635);
            text-decoration: none;
            white-space: nowrap;
            padding: 0.6rem 1.25rem;
            border: 1.5px solid var(--secondary-red, #852635);
            border-radius: 6px;
            transition: all 0.2s;
        }

        .hero-banner__cta:hover {
            background: var(--secondary-red, #852635);
            color: #fff;
            text-decoration: none;
        }

        @media (max-width: 900px) {
            .hero-section {
                grid-template-columns: 1fr;
                gap: 2rem;
                min-height: auto;
                padding: 2rem 1.5rem;
            }

            .hero-section__content {
                padding-top: 0;
            }

            .hero-section__image-container {
                min-height: 400px;
            }

            .hero-section__quote {
                right: 1rem;
            }

            .hero-section__stats {
                gap: 1.5rem;
            }

            .hero-banner__inner {
                flex-direction: column;
                text-align: center;
            }

            .hero-banner__left {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .hero-section__heading {
                font-size: 2.2rem;
            }

            .hero-section__stats {
                flex-wrap: wrap;
                gap: 1.5rem;
            }

            .hero-stat {
                min-width: 80px;
            }
        }

        /* Fix: anchor absolute-positioned dates inside their cards */
        .second-row .content-container {
            position: relative;
        }
    </style>
@stop

@section('content')
<div class="front-page-new">

    {{-- Hero section --}}
    <section class="hero-section">
        <div class="hero-section__content">
            <p class="hero-section__eyebrow">Din litterære familie siden 2015</p>

            <h1 class="hero-section__heading">
                For deg som vil gjøre <em>alvor</em> av skrivedrømmen
            </h1>

            <p class="hero-section__description">
                Lær skrivehåndverket fra erfarne forfattere og redaktører. Vi hjelper deg fra første utkast til ferdig manus.
            </p>

            <div class="hero-section__ctas">
                <a href="{{ route('front.course.index') }}" class="hero-cta hero-cta--primary">
                    Utforsk våre kurs
                    <span class="hero-cta__arrow">&rarr;</span>
                </a>
                <a href="{{ route('front.free-manuscript.index') }}" class="hero-cta hero-cta--secondary">
                    Gratis tekstvurdering
                </a>
            </div>

            <div class="hero-section__stats">
                <div class="hero-stat">
                    <span class="hero-stat__number">15+</span>
                    <span class="hero-stat__label">Skrivekurs</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat__number">5000+</span>
                    <span class="hero-stat__label">Kursdeltagere</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat__number">200+</span>
                    <span class="hero-stat__label">Utgitte forfattere</span>
                </div>
            </div>
        </div>

        <div class="hero-section__image-wrapper">
            <div class="hero-section__image-container">
                <img src="https://www.forfatterskolen.no/images-new/home/kristine.png"
                     alt="Kristine, grunnlegger av Forfatterskolen">
            </div>

            <div class="hero-section__quote">
                <p class="hero-section__quote-text">&ldquo;Alle har en historie å fortelle&rdquo;</p>
                <p class="hero-section__quote-author">&ndash; Kristine, grunnlegger</p>
            </div>
        </div>
    </section>

    {{-- Gratis tekstvurdering banner --}}
    <div class="hero-banner">
        <div class="hero-banner__inner">
            <div class="hero-banner__left">
                <div class="hero-banner__icon">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="#862736" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <polyline points="14,2 14,8 20,8" stroke="#862736" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="9" y1="13" x2="15" y2="13" stroke="#862736" stroke-width="1.5" stroke-linecap="round"/>
                        <line x1="9" y1="17" x2="13" y2="17" stroke="#862736" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <div>
                    <p class="hero-banner__title">Usikker på om teksten din holder?</p>
                    <p class="hero-banner__sub">Send inn en smakebit og få profesjonell tilbakemelding &mdash; helt gratis.</p>
                </div>
            </div>
            <a href="{{ route('front.free-manuscript.index') }}" class="hero-banner__cta">
                Prøv gratis &rarr;
            </a>
        </div>
    </div>

    <div class="container">
        <div class="col-md-12">
            <div class="row second-row">
                <h2 class="w-100 text-center">
                    {!! trans('site.front.latest-seminars') !!}
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
                                    <i class="img-icon16 icon-clock ms-3"></i>
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
            <h2 class="float-start">
                {!! trans('site.front.home.most-popular-course') !!}
            </h2>
            <a href="{{ route('front.course.index') }}" class="btn float-end btn-outline-maroon">
                {{ trans('site.front.home.all-course') }}
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
                    <h2>{!! trans('site.front.home.advantages-of-online-course') !!}</h2>
                    <p>
                        {!! trans('site.front.home.advantages-of-online-course-description') !!} 
                    </p>
                    <ul>
                        <li>
                            <img data-src="https://www.forfatterskolen.no/images-new/home/arrow.svg" alt="arrow">
                            {!! trans('site.front.home.advantages-of-online-course-1') !!}
                        </li>
                        <li>
                            <img data-src="https://www.forfatterskolen.no/images-new/home/arrow.svg" alt="arrow">
                            {!! trans('site.front.home.advantages-of-online-course-2') !!}
                        </li>
                        <li>
                            <img data-src="https://www.forfatterskolen.no/images-new/home/arrow.svg" alt="arrow">
                            {!! trans('site.front.home.advantages-of-online-course-3') !!}
                        </li>
                    </ul>
                </div>
            </div> <!-- end top-container -->

            <div class="bottom-container">
                <div class="col-md-5">
                    <h2>
                        {!! trans('site.front.home.meet-your-mentors') !!}
                    </h2>
                    <p>
                        {!! trans('site.front.home.meet-your-mentors-details') !!}
                    </p>

                    <a href="{{ route('front.course.show', 17) }}" class="btn btn-red">
                        {!! trans('site.front.home.see-more-mentors') !!}
                    </a>
                </div>
            </div>
        </div> <!-- end container -->
    </div> <!-- end online-courses-row-->

    {{-- ============================================================
        NYTT: Utgitte elever (erstatter video-testimonial karusellen)
        Krever $publishedBooks fra controlleren:
        $publishedBooks = \App\PublisherBook::orderBy('id', 'desc')->take(3)->get();
    ============================================================ --}}
    @php
        $publishedBooks = \App\PublisherBook::orderBy('id', 'desc')->take(3)->get();
    @endphp
    <div class="testimonials-row">
        <div class="container">
            <h2>
                Våre nyeste utgivelser
            </h2>

            <div class="row justify-content-center">
                @foreach($publishedBooks as $book)
                    @php
                        $author_image = $book->author_image ? \App\Http\FrontendHelpers::checkJpegImg($book->author_image) : '';
                    @endphp
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 border-0 text-center">
                            <div class="card-body d-flex flex-column align-items-center">
                                {{-- Bokomslag --}}
                                <div class="published-book-cover mb-3">
                                    @if($book->book_image_link)
                                        <a href="{{ $book->book_image_link }}" target="_blank">
                                    @endif
                                        <img data-src="https://www.forfatterskolen.no/{{ $author_image }}"
                                             alt="{{ $book->title }}"
                                             class="img-fluid"
                                             style="max-height: 320px; width: auto; box-shadow: 0 4px 16px rgba(0,0,0,0.15); border-radius: 4px;">
                                    @if($book->book_image_link)
                                        </a>
                                    @endif
                                </div>

                                {{-- Forfatternavn (title-feltet er forfatternavnet i PublisherBook) --}}
                                <h3 class="font-montserrat-semibold theme-text mt-3 mb-1" style="font-size: 1.1rem;">
                                    {{ $book->title }}
                                </h3>

                                {{-- Kort beskrivelse (description inneholder HTML) --}}
                                <p class="font-montserrat-regular text-muted" style="font-size: 0.9rem;">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($book->description), 120) }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- "Se alle" lenke --}}
            <div class="text-center mt-3">
                <a href="{{ route('front.publishing') }}" class="btn site-btn-global">
                    Se alle utgitte elever
                </a>
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
                        {!! trans('site.front.home.like-pro-feedback') !!}
                    </h2>

                    <a href="{{ route('front.free-manuscript.index') }}" class="btn site-btn-global mt-5">
                        {!! trans('site.front.home.like-pro-feedback-yes') !!}
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
                <div class="h1 mt-0 gdpr-title">{!! trans('site.front.home.gdpr-title') !!}</div>
                <div>
                    <p>
                        {!! trans('site.front.home.gdpr-description-1') !!}
                    </p>
                    <p>
                        {!! trans('site.front.home.gdpr-description-2') !!}
                    </p>
                </div>
            </div>

            <div class="gdpr-actions">
                <button class="btn btn-agree" onclick="agreeGdpr()">
                    {!! trans('site.front.home.gdpr-understand') !!}
                </button>
                <a href="{{ route('front.terms') }}" title="View terms">{!! trans('site.front.home.gdpr-view-terms') !!}</a>
            </div>
        </div>
    </div>
@endif

{{-- Fjernet: #vooModal (video-modal) – ikke lenger i bruk etter fjerning av video-testimonials --}}

@stop
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>
    <script>
        $(document).ready(function(){
            if ($(window).width() > 640) {
                var vid = document.getElementById('vid');
                if (vid) vid.play();
            }
        });

        $(window).resize(function() {
            var vid = document.getElementById('vid');
            if (!vid) return;
            if ($(window).width() <= 640) {
                vid.pause();
            } else {
                vid.play();
            }
        });

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
                $.each(error.responseJSON.errors, function(k, v) {
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