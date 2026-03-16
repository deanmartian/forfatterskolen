@extends('frontend.layout')

@section('title')
    <title>Kurs &rsaquo; Forfatterskolen</title>
@stop

@section('content')
<style>
/* ── KURS REDESIGN — scoped under .kurs-redesign ── */
.kurs-redesign {
    --kr-wine: #862736;
    --kr-wine-hover: #9c2e40;
    --kr-wine-dark: #5c1a25;
    --kr-wine-light: rgba(134, 39, 54, 0.08);
    --kr-wine-light-solid: #f4e8ea;
    --kr-cream: #faf8f5;
    --kr-green: #2e7d32;
    --kr-green-bg: #e8f5e9;
    --kr-blue: #1565c0;
    --kr-blue-bg: #e3f2fd;
    --kr-amber: #e65100;
    --kr-amber-bg: #fff3e0;
    --kr-text: #1a1a1a;
    --kr-text-sec: #5a5550;
    --kr-text-muted: #8a8580;
    --kr-border: rgba(0, 0, 0, 0.08);
    --kr-border-strong: rgba(0, 0, 0, 0.12);
    --kr-font-display: 'Playfair Display', Georgia, serif;
    --kr-font-body: 'Source Sans 3', -apple-system, sans-serif;
    --kr-max-w: 1080px;
    --kr-radius: 10px;
    --kr-radius-lg: 14px;
    font-family: var(--kr-font-body);
    color: var(--kr-text);
    -webkit-font-smoothing: antialiased;
}

/* ── HERO ── */
.kr-hero {
    background: var(--kr-cream);
    border-bottom: 1px solid var(--kr-border);
    padding: 3.5rem 2rem 3rem;
    text-align: center;
}
.kr-hero__inner {
    max-width: 640px;
    margin: 0 auto;
}
.kr-hero__eyebrow {
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--kr-wine);
    margin-bottom: 0.75rem;
}
.kr-hero__heading {
    font-family: var(--kr-font-display);
    font-size: clamp(1.75rem, 3.5vw, 2.25rem);
    font-weight: 700;
    line-height: 1.2;
    color: var(--kr-text);
    margin-bottom: 0.75rem;
}
.kr-hero__desc {
    font-size: 1rem;
    font-weight: 300;
    line-height: 1.7;
    color: var(--kr-text-sec);
}

/* ── FEATURED ── */
.kr-featured {
    max-width: var(--kr-max-w);
    margin: 0 auto;
    padding: 2.5rem 2rem 1rem;
}
.kr-featured__label {
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--kr-wine);
    margin-bottom: 1rem;
}
.kr-featured__grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.25rem;
}
.kr-featured-card {
    background: var(--kr-wine);
    border-radius: var(--kr-radius-lg);
    padding: 2rem;
    text-decoration: none;
    color: #fff;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
    display: flex;
    flex-direction: column;
}
.kr-featured-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 28px rgba(134, 39, 54, 0.25);
    text-decoration: none;
    color: #fff;
}
.kr-featured-card::after {
    content: '';
    position: absolute;
    top: -40%; right: -20%;
    width: 250px; height: 250px;
    background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
    pointer-events: none;
}
.kr-featured-card--secondary {
    background: var(--kr-text);
}
.kr-featured-card--secondary:hover {
    box-shadow: 0 8px 28px rgba(0, 0, 0, 0.2);
}
.kr-featured-card__badge {
    display: inline-block;
    font-size: 0.65rem;
    font-weight: 600;
    letter-spacing: 1px;
    text-transform: uppercase;
    padding: 0.25rem 0.6rem;
    border-radius: 4px;
    background: rgba(255,255,255,0.15);
    color: rgba(255,255,255,0.8);
    margin-bottom: 1rem;
    width: fit-content;
}
.kr-featured-card__title {
    font-family: var(--kr-font-display);
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 0.6rem;
    position: relative;
    z-index: 1;
}
.kr-featured-card__desc {
    font-size: 0.875rem;
    line-height: 1.6;
    color: rgba(255,255,255,0.65);
    flex: 1;
    position: relative;
    z-index: 1;
}
.kr-featured-card__meta {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(255,255,255,0.12);
    position: relative;
    z-index: 1;
}
.kr-featured-card__meta-item {
    font-size: 0.78rem;
    color: rgba(255,255,255,0.55);
    display: flex;
    align-items: center;
    gap: 0.35rem;
}
.kr-featured-card__meta-item svg {
    width: 14px; height: 14px;
    stroke: rgba(255,255,255,0.5);
}
.kr-featured-card__arrow {
    font-size: 0.85rem;
    font-weight: 600;
    color: #fff;
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 0.3rem;
    transition: gap 0.2s;
}
.kr-featured-card:hover .kr-featured-card__arrow { gap: 0.5rem; }

/* ── COURSE GRID ── */
.kr-courses {
    max-width: var(--kr-max-w);
    margin: 0 auto;
    padding: 2.5rem 2rem 4rem;
}
.kr-courses__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}
.kr-courses__title {
    font-family: var(--kr-font-display);
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--kr-text);
}
.kr-filter-tabs {
    display: flex;
    gap: 0.35rem;
    flex-wrap: wrap;
}
.kr-filter-tab {
    padding: 0.4rem 0.9rem;
    border: 1px solid var(--kr-border-strong);
    background: #fff;
    border-radius: 20px;
    font-family: var(--kr-font-body);
    font-size: 0.78rem;
    font-weight: 500;
    color: var(--kr-text-sec);
    cursor: pointer;
    transition: all 0.15s;
}
.kr-filter-tab:hover {
    border-color: var(--kr-wine);
    color: var(--kr-wine);
}
.kr-filter-tab.kr-active {
    background: var(--kr-wine);
    border-color: var(--kr-wine);
    color: #fff;
}
.kr-course-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
    gap: 1.25rem;
}

/* Course card */
.kr-course-card {
    border: 1px solid var(--kr-border);
    border-radius: var(--kr-radius-lg);
    overflow: hidden;
    transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
}
.kr-course-card:hover {
    border-color: var(--kr-border-strong);
    box-shadow: 0 4px 16px rgba(0,0,0,0.06);
    transform: translateY(-2px);
    text-decoration: none;
    color: inherit;
}
.kr-course-card__image {
    height: 160px;
    background: linear-gradient(135deg, #e8e2da, #d4cec6);
    position: relative;
    overflow: hidden;
}
.kr-course-card__image img {
    width: 100%; height: 100%;
    object-fit: cover;
}
.kr-course-card__badges {
    position: absolute;
    top: 0.75rem; left: 0.75rem;
    display: flex;
    gap: 0.35rem;
    flex-wrap: wrap;
}
.kr-badge {
    font-size: 0.65rem;
    font-weight: 600;
    padding: 0.2rem 0.55rem;
    border-radius: 4px;
    white-space: nowrap;
}
.kr-badge--date {
    background: #fff;
    color: var(--kr-text);
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.kr-badge--free {
    background: var(--kr-green-bg);
    color: var(--kr-green);
}
.kr-badge--self-paced {
    background: var(--kr-blue-bg);
    color: var(--kr-blue);
}
.kr-course-card__body {
    padding: 1.25rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.kr-course-card__instructor {
    font-size: 0.72rem;
    font-weight: 600;
    color: var(--kr-wine);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.35rem;
}
.kr-course-card__title {
    font-family: var(--kr-font-display);
    font-size: 1.1rem;
    font-weight: 700;
    line-height: 1.3;
    color: var(--kr-text);
    margin-bottom: 0.5rem;
}
.kr-course-card__desc {
    font-size: 0.82rem;
    color: var(--kr-text-sec);
    line-height: 1.6;
    flex: 1;
}
.kr-course-card__footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 1rem;
    padding-top: 0.85rem;
    border-top: 1px solid var(--kr-border);
}
.kr-course-card__format {
    font-size: 0.75rem;
    color: var(--kr-text-muted);
    display: flex;
    align-items: center;
    gap: 0.3rem;
}
.kr-course-card__format svg {
    width: 13px; height: 13px;
    stroke: var(--kr-text-muted);
}
.kr-course-card__cta {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--kr-wine);
    display: flex;
    align-items: center;
    gap: 0.25rem;
    transition: gap 0.15s;
}
.kr-course-card:hover .kr-course-card__cta { gap: 0.4rem; }

/* ── MENTORMØTER ── */
.kr-mentor {
    background: var(--kr-cream);
    border-top: 1px solid var(--kr-border);
    border-bottom: 1px solid var(--kr-border);
    padding: 3rem 2rem;
}
.kr-mentor__inner {
    max-width: var(--kr-max-w);
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2.5rem;
    align-items: center;
}
.kr-mentor__text h2 {
    font-family: var(--kr-font-display);
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--kr-text);
    margin-bottom: 0.6rem;
}
.kr-mentor__text p {
    font-size: 0.95rem;
    color: var(--kr-text-sec);
    line-height: 1.7;
    margin-bottom: 1.25rem;
}
.kr-mentor__cta {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--kr-wine);
    text-decoration: none;
    padding: 0.6rem 1.25rem;
    border: 1.5px solid var(--kr-wine);
    border-radius: 6px;
    transition: all 0.2s;
}
.kr-mentor__cta:hover {
    background: var(--kr-wine);
    color: #fff;
    text-decoration: none;
}
.kr-mentor__image {
    border-radius: var(--kr-radius-lg);
    overflow: hidden;
    aspect-ratio: 16 / 10;
    background: linear-gradient(135deg, #e8e2da, #d4cec6);
}
.kr-mentor__image img {
    width: 100%; height: 100%;
    object-fit: cover;
}

/* ── CTA BANNER ── */
.kr-cta {
    padding: 3rem 2rem;
}
.kr-cta__banner {
    max-width: var(--kr-max-w);
    margin: 0 auto;
    background: var(--kr-wine);
    border-radius: var(--kr-radius-lg);
    padding: 2.5rem 3rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 2rem;
    position: relative;
    overflow: hidden;
}
.kr-cta__banner::after {
    content: '';
    position: absolute;
    top: -40%; right: -10%;
    width: 350px; height: 350px;
    background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, transparent 70%);
    pointer-events: none;
}
.kr-cta__heading {
    font-family: var(--kr-font-display);
    font-size: 1.5rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 0.35rem;
    position: relative;
    z-index: 1;
}
.kr-cta__sub {
    font-size: 0.9rem;
    color: rgba(255,255,255,0.6);
    font-weight: 300;
    position: relative;
    z-index: 1;
}
.kr-cta__btn {
    padding: 0.75rem 1.5rem;
    background: #fff;
    color: var(--kr-wine);
    border-radius: 6px;
    font-family: var(--kr-font-body);
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    white-space: nowrap;
    transition: background 0.2s, transform 0.1s;
    position: relative;
    z-index: 1;
}
.kr-cta__btn:hover {
    background: var(--kr-cream);
    transform: translateY(-1px);
    text-decoration: none;
    color: var(--kr-wine);
}

/* ── RESPONSIVE ── */
@media (max-width: 900px) {
    .kr-featured__grid { grid-template-columns: 1fr; }
    .kr-mentor__inner { grid-template-columns: 1fr; gap: 2rem; }
}
@media (max-width: 600px) {
    .kr-course-grid { grid-template-columns: 1fr; }
    .kr-courses__header { flex-direction: column; align-items: flex-start; }
    .kr-cta__banner {
        flex-direction: column;
        text-align: center;
        padding: 2rem;
    }
    .kr-hero { padding: 2.5rem 1.25rem 2rem; }
    .kr-featured { padding: 2rem 1.25rem 1rem; }
    .kr-courses { padding: 2rem 1.25rem 3rem; }
    .kr-mentor { padding: 2.5rem 1.25rem; }
    .kr-cta { padding: 2rem 1.25rem; }
}
</style>

@php
    // Separate featured courses (Årskurs + Påbygg) and mentormøter from the rest
    $featuredCourses = $courses->filter(function($c) {
        return str_contains(mb_strtolower($c->title), 'årskurs') || str_contains(mb_strtolower($c->title), 'påbygging');
    });
    $mentorCourse = $courses->first(function($c) {
        return $c->id == 17 || str_contains(mb_strtolower($c->title), 'mentormøter');
    });
    $regularCourses = $courses->filter(function($c) use ($featuredCourses, $mentorCourse) {
        return !$featuredCourses->contains($c) && $c !== $mentorCourse;
    });
@endphp

<div class="kurs-redesign">

    {{-- ═══════════ HERO ═══════════ --}}
    <section class="kr-hero">
        <div class="kr-hero__inner">
            <p class="kr-hero__eyebrow">Skrivekurs på nett</p>
            <h1 class="kr-hero__heading">Gode verktøy, profesjonell veiledning og et støttende miljø</h1>
            <p class="kr-hero__desc">Fra idé til ferdig manus — i ditt eget tempo. Velg kurset som passer deg.</p>
        </div>
    </section>

    {{-- ═══════════ FEATURED: ÅRSKURS + PÅBYGG ═══════════ --}}
    @if($featuredCourses->count())
    <section class="kr-featured">
        <p class="kr-featured__label">Våre hovedkurs</p>
        <div class="kr-featured__grid">
            @foreach($featuredCourses as $i => $fc)
                <a href="{{ route($showRoute, $fc->id) }}" class="kr-featured-card {{ $i > 0 ? 'kr-featured-card--secondary' : '' }}">
                    <span class="kr-featured-card__badge">{{ $i === 0 ? 'Kjernekurs' : 'Fordypning' }}</span>
                    <h2 class="kr-featured-card__title">{{ $fc->title }}</h2>
                    <p class="kr-featured-card__desc">{!! \Illuminate\Support\Str::limit(html_entity_decode(strip_tags($fc->description)), 180) !!}</p>
                    <div class="kr-featured-card__meta">
                        @if($fc->start_date)
                            <span class="kr-featured-card__meta-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                Oppstart {{ \App\Http\FrontendHelpers::formatDate($fc->start_date) }}
                            </span>
                        @endif
                        <span class="kr-featured-card__meta-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            12 måneder
                        </span>
                        <span class="kr-featured-card__arrow">Les mer →</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ═══════════ ALL COURSES ═══════════ --}}
    <section class="kr-courses">
        <div class="kr-courses__header">
            <h2 class="kr-courses__title">Alle kurs</h2>
            <div class="kr-filter-tabs">
                <button class="kr-filter-tab kr-active" onclick="krFilterCourses('alle', this)">Alle</button>
                <button class="kr-filter-tab" onclick="krFilterCourses('skrivekurs', this)">Skrivekurs</button>
                <button class="kr-filter-tab" onclick="krFilterCourses('gratis', this)">Gratis</button>
                <button class="kr-filter-tab" onclick="krFilterCourses('selvstudium', this)">Selvstudium</button>
            </div>
        </div>

        <div class="kr-course-grid" id="krCourseGrid">
            @foreach($regularCourses as $course)
                @php
                    // Derive categories
                    $cats = [];
                    if ($course->is_free) $cats[] = 'gratis';
                    if ($course->start_date && \Carbon\Carbon::parse($course->start_date)->isFuture()) {
                        $cats[] = 'skrivekurs';
                    }
                    if (!$course->start_date || str_contains(mb_strtolower($course->title), 'øyeblikkelig')) {
                        $cats[] = 'selvstudium';
                    }
                    if (!in_array('gratis', $cats) && !in_array('selvstudium', $cats)) {
                        $cats[] = 'skrivekurs';
                    }
                    $catStr = implode(' ', $cats);

                    $isFree = $course->is_free;
                    $hasDate = $course->start_date && \Carbon\Carbon::parse($course->start_date)->isFuture();
                    $isSelfPaced = !$hasDate && !$isFree;
                @endphp
                <a href="{{ route($showRoute, $course->id) }}" class="kr-course-card" data-category="{{ $catStr }}">
                    <div class="kr-course-card__image">
                        @if($course->course_image)
                            <img src="https://www.forfatterskolen.no/{{ $course->course_image }}" alt="{{ $course->title }}" loading="lazy">
                        @endif
                        <div class="kr-course-card__badges">
                            @if($hasDate)
                                <span class="kr-badge kr-badge--date">Oppstart {{ \App\Http\FrontendHelpers::formatDate($course->start_date) }}</span>
                            @endif
                            @if($isFree)
                                <span class="kr-badge kr-badge--free">Gratis</span>
                            @endif
                            @if($isSelfPaced)
                                <span class="kr-badge kr-badge--self-paced">Selvstudium</span>
                            @endif
                        </div>
                    </div>
                    <div class="kr-course-card__body">
                        @if($course->instructor)
                            <p class="kr-course-card__instructor">Med {{ $course->instructor }}</p>
                        @endif
                        <h3 class="kr-course-card__title">{{ $course->title }}</h3>
                        <p class="kr-course-card__desc">{!! \Illuminate\Support\Str::limit(html_entity_decode(strip_tags($course->description)), 150) !!}</p>
                        <div class="kr-course-card__footer">
                            <span class="kr-course-card__format">
                                @if($hasDate)
                                    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    Kurs med oppstart
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    Øyeblikkelig tilgang
                                @endif
                            </span>
                            <span class="kr-course-card__cta">Les mer →</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ═══════════ MENTORMØTER ═══════════ --}}
    @if($mentorCourse)
    <section class="kr-mentor">
        <div class="kr-mentor__inner">
            <div class="kr-mentor__text">
                <h2>Mentormøter</h2>
                <p>Hver mandag møter du kjente forfattere og redaktører — live og direkte — på skjermen. Av og til redigerer rektor innsendte tekster live, så du lærer å bearbeide eget manus.</p>
                <p>Mentormøtene er inkludert i alle betalende kurs, eller kan kjøpes separat.</p>
                <a href="{{ route($showRoute, $mentorCourse->id) }}" class="kr-mentor__cta">Se alle mentormøter →</a>
            </div>
            <div class="kr-mentor__image">
                @if($mentorCourse->course_image)
                    <img src="https://www.forfatterskolen.no/{{ $mentorCourse->course_image }}" alt="Mentormøter" loading="lazy">
                @endif
            </div>
        </div>
    </section>
    @endif

    {{-- ═══════════ CTA ═══════════ --}}
    <section class="kr-cta">
        <div class="kr-cta__banner">
            <div>
                <h2 class="kr-cta__heading">Usikker på hvor du skal starte?</h2>
                <p class="kr-cta__sub">Send inn en smakebit av teksten din og få gratis tilbakemelding fra en profesjonell redaktør.</p>
            </div>
            <a href="{{ url('/gratis-tekstvurdering') }}" class="kr-cta__btn">Gratis tekstvurdering →</a>
        </div>
    </section>

</div>

<script>
function krFilterCourses(category, btn) {
    document.querySelectorAll('.kr-filter-tab').forEach(function(t) { t.classList.remove('kr-active'); });
    btn.classList.add('kr-active');
    document.querySelectorAll('.kr-course-card').forEach(function(card) {
        if (category === 'alle') {
            card.style.display = 'flex';
        } else {
            var cats = card.getAttribute('data-category') || '';
            card.style.display = cats.indexOf(category) !== -1 ? 'flex' : 'none';
        }
    });
}
</script>
@stop
