@extends('frontend.layout')

@section('page_title', 'Kurs &rsaquo; Forfatterskolen')
@section('meta_desc', 'Utforsk Forfatterskolens skrivekurs. Roman, barnebok, sakprosa, lyrikk og mer. Nettbaserte kurs med personlig veiledning.')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/course-index.css') }}">

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
