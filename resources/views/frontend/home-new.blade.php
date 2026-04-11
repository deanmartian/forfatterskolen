@extends('frontend.layout')

@section('page_title', 'Forfatterskolen — Skrivekurs på nett med erfarne forfattere')

@section('meta_desc', 'Skrivekurs på nett med erfarne forfattere og redaktører. Roman, barnebok, sakprosa. Fra idé til ferdig manus. 5000+ kursdeltagere siden 2015.')
@section('metas')
    <meta property="og:title" content="Forfatterskolen — for deg som vil gjøre alvor av skrivedrømmen">
    <meta property="og:description" content="Lær skrivehåndverket fra erfarne forfattere og redaktører. Vi hjelper deg fra første utkast til ferdig manus. 15+ skrivekurs, 5000+ kursdeltagere, 200+ utgitte forfattere.">
@stop

@section('styles')
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css"
          as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">
    </noscript>
    <link rel="stylesheet" href="{{asset('vendor/laraberg/css/laraberg.css')}}">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&family=Source+Sans+3:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/pages/home.css') }}">
@stop

@section('content')
<div class="front-page-new">

    {{-- Hero section --}}
    <div class="hero-wrapper">
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
    </div>

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

    {{-- ═══════════ SISTE NYTT ═══════════ --}}
    <section class="siste-nytt">
        <h2 class="siste-nytt__heading">Siste nytt</h2>

        {{-- ── Portal-annonse (featured) ──
             TODO: Fjern eller oppdater dette kortet etter at portal-redesignen er ferdig lansert. --}}
        <div class="announcement">
            <div>
                <div class="announcement__badge">
                    <span class="announcement__badge-dot"></span>
                    P&aring;g&aring;ende oppdatering
                </div>
                <h3 class="announcement__title">Vi bygger en helt ny elevportal</h3>
                <p class="announcement__desc">
                    Forfatterskolen f&aring;r nytt design fra topp til bunn. De f&oslash;rste oppdateringene rulles ut n&aring; i mars &mdash; og mye mer er p&aring; vei utover v&aring;ren.
                </p>
                <div class="announcement__features">
                    <span class="announcement__feature">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Nytt dashboard
                    </span>
                    <span class="announcement__feature">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Bedre kursoversikt
                    </span>
                    <span class="announcement__feature">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Coaching-bestilling
                    </span>
                    <span class="announcement__feature">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Varslingsinnstillinger
                    </span>
                </div>
                <div class="announcement__progress">
                    <div class="announcement__progress-bar">
                        <div class="announcement__progress-fill" style="width: {{ config('portal.redesign_progress', 25) }}%"></div>
                    </div>
                    <span class="announcement__progress-text">Rulles ut l&oslash;pende &middot; Startet mars 2026</span>
                </div>
            </div>
            <div class="announcement__visual">
                <div class="announcement__mockup">
                    <div class="mockup-bar mockup-bar--short mockup-bar--accent"></div>
                    <div class="mockup-bar mockup-bar--long"></div>
                    <div class="mockup-bar mockup-bar--medium"></div>
                    <div class="mockup-bar mockup-bar--short"></div>
                    <div class="mockup-bar mockup-bar--long"></div>
                    <div class="mockup-dots">
                        <div class="mockup-dot mockup-dot--done"></div>
                        <div class="mockup-dot mockup-dot--done"></div>
                        <div class="mockup-dot"></div>
                        <div class="mockup-dot"></div>
                        <div class="mockup-dot"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Nyhetskort ── --}}
        @php
            $groWebinarStart = \Carbon\Carbon::parse('2026-03-24 19:00:00');
            $groWebinarEnd = \Carbon\Carbon::parse('2026-03-24 21:00:00');
            $groReplayEnd = \Carbon\Carbon::parse('2026-04-07 23:59:00');
            $isBeforeWebinar = now()->isBefore($groWebinarStart);
            $isReplayPeriod = now()->isAfter($groWebinarEnd) && now()->isBefore($groReplayEnd);
        @endphp

        @if($isBeforeWebinar)
        {{-- Før webinaret: påmelding --}}
        <div style="max-width: 700px; margin: 0 auto;">
            <a href="{{ url('/gratis-webinar/94') }}" class="news-card" style="display:block; text-align:center; padding: 30px 24px;">
                <span class="news-card__badge news-card__badge--webinar">Gratis webinar &middot; Tirsdag 24. mars kl. 19:00</span>
                <h3 class="news-card__title" style="font-size: 1.5rem; margin: 12px 0 8px;">Slik skaper du karakterer som lever</h3>
                <p style="color: #666; font-size: 1.05rem; margin: 0 0 16px;">med <strong>Gro Dahle</strong> &mdash; en av Norges mest elskede forfattere</p>
                <span style="display:inline-block; padding: 12px 28px; background-color: #862736; color: #fff; border-radius: 6px; font-weight: 600; font-size: 15px; text-decoration: none;">Meld deg p&aring; gratis &rarr;</span>
            </a>
        </div>
        @elseif($isReplayPeriod)
        {{-- Etter webinaret: reprise (vises i 2 uker) --}}
        <div style="max-width: 700px; margin: 0 auto;">
            <a href="{{ url('/gratis-webinar/94') }}" class="news-card" style="display:block; text-align:center; padding: 30px 24px;">
                <span class="news-card__badge news-card__badge--reprise">Reprise tilgjengelig</span>
                <h3 class="news-card__title" style="font-size: 1.5rem; margin: 12px 0 8px;">Slik skaper du karakterer som lever</h3>
                <p style="color: #666; font-size: 1.05rem; margin: 0 0 16px;">med <strong>Gro Dahle</strong> &mdash; se webinaret i reprise</p>
                <span style="display:inline-block; padding: 12px 28px; background-color: #862736; color: #fff; border-radius: 6px; font-weight: 600; font-size: 15px; text-decoration: none;">Se reprisen &rarr;</span>
            </a>
        </div>
        @elseif($next_free_webinar && \Carbon\Carbon::parse($next_free_webinar->start_date)->isFuture())
        {{-- Kommende gratiswebinar — overstyrer 3-kort-gridden så lenge det finnes
             et FreeWebinar med start_date i fremtiden. Gjør det mulig å promote
             neste webinar tydelig fra forsiden uten å måtte vedlikeholde
             upcoming_sections-tabellen manuelt. --}}
        @php
            $nextFwStart = \Carbon\Carbon::parse($next_free_webinar->start_date);
            $nextFwDateStr = \App\Http\FrontendHelpers::formatDate($next_free_webinar->start_date);
            $nextFwTimeStr = \App\Http\FrontendHelpers::getTimeFromDT($next_free_webinar->start_date);
            $nextFwExcerpt = \Illuminate\Support\Str::limit(strip_tags($next_free_webinar->description), 140);
        @endphp
        <div style="max-width: 700px; margin: 0 auto;">
            <a href="{{ url('/gratis-webinar/' . $next_free_webinar->id) }}" class="news-card" style="display:block; text-align:center; padding: 30px 24px;">
                <span class="news-card__badge news-card__badge--webinar">
                    Gratis webinar &middot; {{ $nextFwDateStr }} kl. {{ $nextFwTimeStr }}
                </span>
                <h3 class="news-card__title" style="font-size: 1.5rem; margin: 12px 0 8px;">{{ $next_free_webinar->title }}</h3>
                @if($nextFwExcerpt)
                    <p style="color: #666; font-size: 1.05rem; margin: 0 0 16px; max-width: 560px; margin-left: auto; margin-right: auto;">{{ $nextFwExcerpt }}</p>
                @endif
                <span style="display:inline-block; padding: 12px 28px; background-color: #862736; color: #fff; border-radius: 6px; font-weight: 600; font-size: 15px; text-decoration: none;">Meld deg p&aring; gratis &rarr;</span>
            </a>
        </div>
        @else
        <div class="news-grid">
            @foreach($upcomingSections as $k => $upcomingSection)
                @php
                    $hasNextWebinar = $k === 1 && $next_webinar ? true : false;
                    $itemDate = $hasNextWebinar ? $next_webinar->start_date : $upcomingSection->date;
                    $itemTitle = $hasNextWebinar ? $next_webinar->title : $upcomingSection->title;
                    $itemLink = $hasNextWebinar ? '/course/17?show_kursplan=1' : $upcomingSection->link;
                    $itemName = $hasNextWebinar ? trans('site.front.next-webinar') : $upcomingSection->name;

                    $badgeClass = 'news-card__badge--webinar';
                    if (str_contains(strtolower($itemName), 'reprise')) {
                        $badgeClass = 'news-card__badge--reprise';
                    } elseif (str_contains(strtolower($itemName), 'kurs')) {
                        $badgeClass = 'news-card__badge--kurs';
                    }
                @endphp
                <a href="{{ url($itemLink) }}" class="news-card">
                    <span class="news-card__badge {{ $badgeClass }}">{{ $itemName }}</span>
                    <h3 class="news-card__title">{{ $itemTitle }}</h3>
                    @if ($itemDate)
                        <div class="news-card__meta">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            {{ \App\Http\FrontendHelpers::formatDate($itemDate) }}
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            {{ \App\Http\FrontendHelpers::getTimeFromDT($itemDate) }}
                        </div>
                    @endif
                </a>
            @endforeach
        </div>
        @endif
    </section>

    {{-- ═══════════ NESTE KURSSTART / ROMANKURS ═══════════ --}}
    @if($romankurs)
    @php
        $earlybirdDeadline = \Carbon\Carbon::parse(config('courses.romankurs.earlybird_deadline'));
        $isEarlybird = now()->isBefore($earlybirdDeadline);
        $discount = config('courses.romankurs.earlybird_discount');
    @endphp
    <section class="kurs-section">
        <div class="kurs-section__header">
            <h2 class="kurs-section__heading">Neste kursstart</h2>
            @if($isEarlybird)
                <p class="kurs-section__sub">Sikre deg plassen til webinar-pris &mdash; gjelder til {{ $earlybirdDeadline->format('j.') }} {{ \App\Http\FrontendHelpers::convertMonthLanguage($earlybirdDeadline->format('n')) }}.</p>
            @else
                <p class="kurs-section__sub">Sikre deg plassen &mdash; begrenset antall plasser.</p>
            @endif
        </div>

        {{-- Webinar-pris countdown --}}
        @if($isEarlybird)
        <div class="earlybird-banner">
            <span class="earlybird-banner__badge">&#127873; Webinar-pris</span>
            <span class="earlybird-banner__text">Spar <strong>kr {{ number_format($discount, 0, ',', ' ') }}</strong> &mdash; gjelder til {{ $earlybirdDeadline->format('j.') }} {{ \App\Http\FrontendHelpers::convertMonthLanguage($earlybirdDeadline->format('n')) }}</span>
            <div class="earlybird-countdown" id="earlybirdCountdown">
                <div class="earlybird-countdown__unit">
                    <div class="earlybird-countdown__number" id="ebDays">--</div>
                    <div class="earlybird-countdown__label">Dager</div>
                </div>
                <div class="earlybird-countdown__unit">
                    <div class="earlybird-countdown__number" id="ebHours">--</div>
                    <div class="earlybird-countdown__label">Timer</div>
                </div>
                <div class="earlybird-countdown__unit">
                    <div class="earlybird-countdown__number" id="ebMins">--</div>
                    <div class="earlybird-countdown__label">Min</div>
                </div>
            </div>
        </div>
        @endif

        {{-- Course hero card --}}
        <div class="course-hero">
            <div class="course-hero__info">
                <div class="course-hero__eyebrow">
                    <span class="course-hero__eyebrow-dot"></span>
                    P&aring;melding &aring;pen
                </div>
                <h3 class="course-hero__title">{{ $romankurs->title }}</h3>
                <p class="course-hero__date">Oppstart 20. april 2026 &middot; 10 uker intensivt + 1 &aring;r tilgang</p>

                <p class="course-hero__desc">
                    L&aelig;r skriveh&aring;ndverket fra erfarne forfattere og redakt&oslash;rer. 10 moduler,
                    ukentlige webinarer, profesjonell tilbakemelding p&aring; manus og tilgang til
                    menterm&oslash;ter med kjente norske forfattere.
                </p>

                <div class="course-hero__features">
                    <div class="course-hero__feature">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                        10 kursmoduler + live webinarer
                    </div>
                    <div class="course-hero__feature">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Tilbakemelding fra profesjonell redakt&oslash;r
                    </div>
                    <div class="course-hero__feature">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Menterm&oslash;ter med kjente forfattere i ett &aring;r
                    </div>
                    <div class="course-hero__feature">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                        14 dagers angrefrist &mdash; ingen risiko
                    </div>
                </div>

                <p class="course-hero__social-proof">
                    &ldquo;Det lureste jeg har gjort er &aring; melde meg p&aring; romankurs hos Forfatterskolen. S&aring; mye kunnskap, s&aring; profesjonelt og s&aring; mange flinke folk.&rdquo;
                </p>
            </div>

            {{-- Pricing cards --}}
            <div>
                <div class="pricing-cards">
                    @foreach($romankursPackages as $package)
                        <div class="pricing-card {{ $package->is_standard ? 'pricing-card--popular' : '' }}">
                            @if($package->is_standard)
                                <span class="pricing-card__popular-badge">MEST VALGT</span>
                            @endif
                            <div class="pricing-card__info">
                                <div class="pricing-card__name">{{ $package->variation }}</div>
                                <div class="pricing-card__desc">{{ $package->description }}</div>
                            </div>
                            <div class="pricing-card__price">
                                <div class="pricing-card__earlybird">
                                    kr {{ number_format($package->calculated_price, 0, ',', ' ') }}
                                </div>
                                @if($package->full_payment_is_sale)
                                    <div class="pricing-card__original">
                                        kr {{ number_format($package->full_payment_price, 0, ',', ' ') }}
                                    </div>
                                    <span class="pricing-card__save">
                                        Spar {{ number_format($package->sale_discount, 0, ',', ' ') }}
                                    </span>
                                @endif
                            </div>
                            <div class="pricing-card__cta">
                                <a href="/course/{{ config('courses.romankurs.id') }}/checkout?package={{ $package->id }}"
                                   class="{{ $package->is_standard ? 'btn-wine' : 'btn-wine-outline' }}">Velg</a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <p class="pricing-note">
                    @if($isEarlybird)
                        Webinar-pris gjelder til {{ $earlybirdDeadline->format('j.') }} {{ \App\Http\FrontendHelpers::convertMonthLanguage($earlybirdDeadline->format('n')) }} {{ $earlybirdDeadline->format('Y') }}. Deretter g&aring;r prisen opp.<br>
                    @endif
                    Bestill n&aring;, betal senere. 14 dagers angrefrist.
                </p>
            </div>
        </div>

        <div class="kurs-section__footer">
            <a href="{{ route('front.course.index') }}" class="btn-wine-outline">Alle kurs &rarr;</a>
        </div>
    </section>
    @endif

    {{-- ═══════════ NETTKURS FORDELER ═══════════ --}}
    <section class="benefits-section">
        <div class="benefits-section__inner">
            <h2 class="benefits-section__heading">Hva er fordelene ved nettkurs?</h2>
            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div class="benefit-card__title">I ditt eget tempo</div>
                    <div class="benefit-card__desc">Ta kurset hvor som helst, n&aring;r det passer deg. Du har tilgang til alt materialet i ett helt &aring;r.</div>
                </div>
                <div class="benefit-card">
                    <div class="benefit-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                    </div>
                    <div class="benefit-card__title">Profesjonell tilbakemelding</div>
                    <div class="benefit-card__desc">Tilbakemelding p&aring; manus fra erfaren redakt&oslash;r. Konkrete tips til forbedring.</div>
                </div>
                <div class="benefit-card">
                    <div class="benefit-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div class="benefit-card__title">Skrivemilj&oslash; som b&aelig;rer deg</div>
                    <div class="benefit-card__desc">Bli del av et yrende fellesskap med hundrevis av skriveglade. Livslangt medlemskap.</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════ MØT MENTORENE DINE ═══════════ --}}
    <section class="mentors-section">
        <div class="mentors-section__inner">
            <div class="mentors-section__text">
                <h2 class="mentors-section__heading">M&oslash;t mentorene dine</h2>
                <p class="mentors-section__desc">
                    Hver mandag har vi treff med kjente forfattere p&aring; skjermen. Du l&aelig;rer av landets beste skrivementorer, og av og til redigerer rektor innsendte tekster live.
                </p>
                <p class="mentors-section__names">
                    Blant gjestene: Maja Lunde, Tom Egeland, Ingvar Ambj&oslash;rnsen, Herbj&oslash;rg Wassmo, Gro Dahle, Simon Stranger, Gunnar Staalesen og mange flere. 100+ timer i arkivet.
                </p>
                <a href="{{ route('front.course.show', 17) }}" class="mentors-section__btn">Les mer om mentoerm&oslash;ter &rarr;</a>
            </div>
            <div class="mentors-section__image">
                <img src="https://www.forfatterskolen.no/images-new/home/online-course.png" alt="Skriving p&aring; laptop">
            </div>
        </div>
    </section>

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
                                        <img src="{{ url($author_image) }}"
                                             alt="{{ $book->title }}"
                                             class="img-fluid"
                                             loading="lazy"
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
                                    {{ \Illuminate\Support\Str::limit(strip_tags(html_entity_decode($book->description)), 120) }}
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

    {{-- ═══════════ GRATIS TEKSTVURDERING CTA ═══════════ --}}
    <section class="cta-section">
        <div class="cta-card">
            <h2 class="cta-card__title">Vil du ha profesjonell tilbakemelding &mdash; helt gratis?</h2>
            <p class="cta-card__desc">Send inn opptil 500 ord og f&aring; en vurdering fra en av v&aring;re redakt&oslash;rer. Uforpliktende.</p>
            <a href="{{ route('front.free-manuscript.index') }}" class="cta-card__btn">Ja, dette vil jeg ha! &rarr;</a>
        </div>
    </section>


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

        // Earlybird countdown
        (function() {
            var deadlineStr = '{{ config("courses.romankurs.earlybird_deadline") }}';
            if (!deadlineStr) return;
            var deadline = new Date(deadlineStr + 'T00:00:00');
            var daysEl = document.getElementById('ebDays');
            var hoursEl = document.getElementById('ebHours');
            var minsEl = document.getElementById('ebMins');
            if (!daysEl || !hoursEl || !minsEl) return;

            function update() {
                var now = new Date();
                var diff = deadline - now;
                if (diff <= 0) {
                    var banner = document.querySelector('.earlybird-banner');
                    if (banner) {
                        banner.innerHTML = '<span class="earlybird-banner__badge">&#9889; Earlybird</span><span class="earlybird-banner__text">Earlybird er avsluttet</span>';
                    }
                    return;
                }
                var days = Math.floor(diff / (1000 * 60 * 60 * 24));
                var hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                daysEl.textContent = days;
                hoursEl.textContent = hours;
                minsEl.textContent = mins;
            }
            update();
            setInterval(update, 60000);
        })();
    </script>
@stop