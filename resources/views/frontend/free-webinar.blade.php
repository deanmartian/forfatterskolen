@extends('frontend.layout')

@section('page_title', 'Gratiswebinar: ' . $freeWebinar->title . ' — Forfatterskolen')

@section('meta_desc', Str::limit(strip_tags($freeWebinar->description), 160))
@section('metas')
    <meta property="og:title" content="Gratiswebinar: {{ $freeWebinar->title }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($freeWebinar->description), 160) }}">
<meta property="og:type" content="event">
    @if($freeWebinar->image)
        <meta property="og:image" content="{{ asset('storage/' . $freeWebinar->image) }}">
        <meta name="twitter:image" content="{{ asset('storage/' . $freeWebinar->image) }}">
    @endif
@stop

@section('styles')
<link rel="stylesheet" href="{{ asset('css/pages/free-webinar.css') }}">
@stop

@section('content')

<?php
    $startDate = \Carbon\Carbon::parse($freeWebinar->start_date);
    $isFuture = $startDate->isFuture();
    $presenter = $freeWebinar->webinar_presenters->first();
?>

{{-- ═══════════ HERO ═══════════ --}}
<section class="webinar-hero">
    <div class="webinar-hero__container">
        <div class="webinar-hero__inner">
            <div>
                <div class="webinar-hero__badge">
                    @if($isFuture)
                        <span class="webinar-hero__badge-dot"></span> Gratiswebinar
                    @else
                        Reprise tilgjengelig
                    @endif
                </div>

                <h1 class="webinar-hero__title">{{ $freeWebinar->title }}</h1>

                <div class="webinar-hero__meta">
                    <span class="webinar-hero__meta-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        {{ ucfirst($startDate->translatedFormat('l j. F Y')) }}
                    </span>
                    <span class="webinar-hero__meta-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Kl. {{ $startDate->format('H:i') }}
                    </span>
                    <span class="webinar-hero__meta-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                        Live webinar (gratis)
                    </span>
                </div>

                @if($presenter)
                <div class="webinar-hero__host">
                    <div class="webinar-hero__host-avatar">
                        @if($presenter->image)
                            <img src="{{ $presenter->image }}" alt="{{ $presenter->first_name }}">
                        @endif
                    </div>
                    <div>
                        <div class="webinar-hero__host-name">{{ $presenter->first_name }} {{ $presenter->last_name }}</div>
                        <div class="webinar-hero__host-role">Rektor & grunnlegger, Forfatterskolen</div>
                    </div>
                </div>
                @endif

                @if($isFuture)
                <div class="countdown" id="countdown">
                    <div class="countdown__unit"><div class="countdown__number" id="cDays">0</div><div class="countdown__label">Dager</div></div>
                    <div class="countdown__unit"><div class="countdown__number" id="cHours">0</div><div class="countdown__label">Timer</div></div>
                    <div class="countdown__unit"><div class="countdown__number" id="cMins">0</div><div class="countdown__label">Min</div></div>
                    <div class="countdown__unit"><div class="countdown__number" id="cSecs">0</div><div class="countdown__label">Sek</div></div>
                </div>
                @endif
            </div>

            {{-- ── REGISTRATION / REPRISE / SUCCESS ──── --}}
            @if($isFuture)
            <div class="reg-card" id="regCard">
                <div class="reg-card__title">Meld deg p&aring; gratis</div>
                <div class="reg-card__sub">F&aring; lenken til webinaret rett i innboksen.</div>

                @if($errors->any())
                <div class="fw-alert">
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
                @endif

                <form action="{{ route('front.free-webinar.submit', $freeWebinar->id) }}" method="POST" id="regForm">
                    @csrf
                    <div class="fw-form-group">
                        <label>Fornavn</label>
                        <input type="text" name="first_name" placeholder="Ola" required value="{{ old('first_name') }}">
                    </div>
                    <div class="fw-form-group">
                        <label>Etternavn</label>
                        <input type="text" name="last_name" placeholder="Nordmann" required value="{{ old('last_name') }}">
                    </div>
                    <div class="fw-form-group">
                        <label>E-post</label>
                        <input type="email" name="email" placeholder="ola@eksempel.no" required value="{{ old('email') }}">
                    </div>

                    <div class="consent-group">
                        <div class="consent-item">
                            <input type="checkbox" id="consent_terms" name="consent_terms" required>
                            <label for="consent_terms">
                                Jeg godtar <a href="/terms/all" target="_blank">vilk&aring;r og betingelser</a> og <a href="/privacy" target="_blank">personvernreglene</a>. <span style="color: var(--wine);">*</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="reg-btn" id="regBtn">
                        Meld meg p&aring; gratiswebinaret &rarr;
                    </button>

                    <div class="reg-note">
                        Vi deler aldri e-postadressen din. Du kan melde deg av n&aring;r som helst.
                    </div>
                </form>
            </div>

            {{-- Success state (shown via JS after submit or via server redirect) --}}
            <div class="success-card" id="successState" style="display: none;">
                <div class="success-card__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#2e7d32" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div class="reg-card__title">Du er p&aring;meldt!</div>
                <div class="reg-card__sub" style="margin-bottom: 0;">
                    Vi sender deg lenken til webinaret og en p&aring;minnelse p&aring; e-post. Sjekk innboksen din!
                </div>
            </div>

            @else
            {{-- ── REPRISE CARD ──── --}}
            <div class="reprise-card">
                <span class="reprise-card__badge">Reprisen er klar</span>
                <div class="reprise-card__title">Du gikk glipp av webinaret?</div>
                <div class="reprise-card__sub">Ingen fare &mdash; se hele opptaket gratis. Oppgi e-post for &aring; f&aring; tilgang.</div>

                @if($errors->any())
                <div class="fw-alert">
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
                @endif

                @if($freeWebinar->replay_url)
                    <a href="{{ route('front.free-webinar-reprise', $freeWebinar->id) }}" class="reg-btn" style="display:block;text-align:center;text-decoration:none;margin-top:16px;">Se reprisen gratis &rarr;</a>
                    <p style="font-size:13px;color:#888;text-align:center;margin-top:12px;">Ingen registrering nødvendig</p>
                @else
                    <form action="{{ route('front.free-webinar.submit', $freeWebinar->id) }}" method="POST">
                        @csrf
                        <div class="fw-form-group">
                            <label>Fornavn</label>
                            <input type="text" name="first_name" placeholder="Ola" required value="{{ old('first_name') }}">
                        </div>
                        <div class="fw-form-group">
                            <label>Etternavn</label>
                            <input type="text" name="last_name" placeholder="Nordmann" required value="{{ old('last_name') }}">
                        </div>
                        <div class="fw-form-group">
                            <label>E-post</label>
                            <input type="email" name="email" placeholder="ola@eksempel.no" required value="{{ old('email') }}">
                        </div>
                        <div class="consent-group">
                            <div class="consent-item">
                                <input type="checkbox" name="consent_terms" required>
                                <label>Jeg godtar <a href="/terms/all" target="_blank">vilk&aring;rene</a> og <a href="/privacy" target="_blank">personvernreglene</a>. *</label>
                            </div>
                            <div class="consent-item">
                                <input type="checkbox" name="consent_marketing">
                                <label>Jeg &oslash;nsker gratis skrivetips og info om kurs. (Valgfritt)</label>
                            </div>
                        </div>
                        <button type="submit" class="reg-btn">Se reprisen gratis &rarr;</button>
                    </form>
                @endif
            </div>
            @endif
        </div>
    </div>
</section>

{{-- ═══════════ CONTENT ═══════════ --}}
<section class="webinar-content">
    <div class="webinar-content__container">
        <div class="webinar-content__inner">
            <div>
                {{-- Description er HTML fra admin (inneholder <p>-tags).
                     Render direkte som HTML i stedet for å escape + nl2br
                     så <p>-tags vises som paragrafer, ikke som råtekst. --}}
                <div class="content-text">
                    {!! $freeWebinar->description !!}
                </div>

                @if($freeWebinar->learning_points)
                <div class="feature-list">
                    <h2 class="feature-list__title">P&aring; webinaret l&aelig;rer du:</h2>
                    @foreach(explode("\n", trim($freeWebinar->learning_points)) as $point)
                        @if(trim($point))
                        <div class="feature-list__item">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                            {{ trim($point) }}
                        </div>
                        @endif
                    @endforeach
                </div>
                @endif

                @if($freeWebinar->target_audience)
                <div class="audience-list">
                    <h3 class="audience-list__title">Webinaret passer for deg som:</h3>
                    @foreach(explode("\n", trim($freeWebinar->target_audience)) as $item)
                        @if(trim($item))
                        <div class="audience-list__item">{{ trim($item) }}</div>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div>
                <div class="sidebar-cta">
                    <div class="sidebar-cta__title">Kan ikke vente?</div>
                    <div class="sidebar-cta__desc">Send inn en smakebit av teksten din og f&aring; gratis tilbakemelding fra en profesjonell redakt&oslash;r.</div>
                    <a href="{{ route('front.free-manuscript.index') }}" class="sidebar-cta__btn">Gratis tekstvurdering &rarr;</a>
                    <div class="sidebar-cta__note">Opptil 500 ord. Svar innen 3 virkedager.</div>
                </div>

                <div class="earlybird-mini">
                    <span class="earlybird-mini__badge">&#127873; Webinar-pris</span>
                    <div class="earlybird-mini__title">Romankurs &ndash; oppstart 20. april</div>
                    <div>
                        <span class="earlybird-mini__price">fra kr 5 900</span>
                        <span class="earlybird-mini__original">kr 10 900</span>
                    </div>
                    <a href="{{ route('front.course.show', 121) }}" class="earlybird-mini__btn">Se kurset &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</section>

@stop

@section('scripts')
<script>
    @if($isFuture)
    // Countdown
    var webinarDate = new Date('{{ $startDate->toIso8601String() }}').getTime();

    function updateCountdown() {
        var now = new Date().getTime();
        var diff = webinarDate - now;

        if (diff <= 0) {
            document.getElementById('countdown').style.display = 'none';
            return;
        }

        var d = Math.floor(diff / 86400000);
        var h = Math.floor((diff % 86400000) / 3600000);
        var m = Math.floor((diff % 3600000) / 60000);
        var s = Math.floor((diff % 60000) / 1000);

        document.getElementById('cDays').textContent = d;
        document.getElementById('cHours').textContent = h < 10 ? '0' + h : h;
        document.getElementById('cMins').textContent = m < 10 ? '0' + m : m;
        document.getElementById('cSecs').textContent = s < 10 ? '0' + s : s;
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);

    // Form validation
    var form = document.getElementById('regForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            var termsConsent = document.getElementById('consent_terms');
            if (!termsConsent.checked) {
                e.preventDefault();
                alert('Du m\u00e5 godta vilk\u00e5rene for \u00e5 melde deg p\u00e5.');
            }
        });
    }
    @endif
</script>
@stop
