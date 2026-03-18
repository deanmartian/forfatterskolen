@extends('frontend.layouts.course-portal')

@section('title')
<title>{{ $lesson->title }} &rsaquo; {{ $lesson->course->title }} &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
<style>
/* ── LESSON REDESIGN — scoped under .lv-redesign ── */
.lv-redesign {
    font-family: 'Source Sans 3', -apple-system, sans-serif;
    color: #1a1a1a;
    -webkit-font-smoothing: antialiased;
    background: #f5f3f0;
    min-height: 100vh;
}

#topbar { display: none !important; }
#main-content { padding-top: 0 !important; margin-top: 0 !important; }

/* ── LAYOUT: content + sidebar ── */
.lv-layout { display: flex; min-height: 100vh; }

.lv-main {
    flex: 1; min-width: 0;
    padding: 2rem 2.5rem;
    max-width: 880px;
}

.lv-sidebar {
    width: 280px; flex-shrink: 0;
    background: #fff; border-left: 1px solid rgba(0,0,0,0.08);
    padding: 1.5rem 0; position: sticky; top: 0; height: 100vh; overflow-y: auto;
}

/* ── MOBILE TOGGLE ── */
.lv-sidebar-toggle {
    display: none;
}
@media (max-width: 991px) {
    .lv-sidebar-toggle {
        display: flex !important; position: fixed; top: 16px; right: 16px; z-index: 1050;
        width: 44px; height: 44px; border-radius: 12px; border: none;
        background: #862736; align-items: center; justify-content: center; cursor: pointer;
        box-shadow: 0 4px 12px rgba(134,39,54,0.35); padding: 0;
    }
    .lv-sidebar-toggle svg { width: 20px; height: 20px; stroke: #fff; stroke-width: 2; }
}

/* ── NAV BAR (top of content) ── */
.lv-nav {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 1.5rem; gap: 0.75rem;
}
.lv-nav__link {
    display: inline-flex; align-items: center; gap: 0.35rem;
    font-size: 0.8rem; color: #8a8580; text-decoration: none;
    padding: 0.4rem 0.75rem; border-radius: 6px; border: 1px solid rgba(0,0,0,0.08);
    background: #fff; transition: all 0.15s; white-space: nowrap;
}
.lv-nav__link:hover { color: #862736; border-color: #862736; text-decoration: none; }
.lv-nav__link.disabled { opacity: 0.4; pointer-events: none; }
.lv-nav__link svg { width: 14px; height: 14px; stroke: currentColor; }
.lv-nav__back {
    font-size: 0.8rem; color: #8a8580; text-decoration: none;
    display: inline-flex; align-items: center; gap: 0.3rem;
}
.lv-nav__back:hover { color: #862736; text-decoration: none; }
.lv-nav__back svg { width: 14px; height: 14px; stroke: currentColor; }

/* ── LESSON HEADER ── */
.lv-header {
    background: #fff; border: 1px solid rgba(0,0,0,0.08); border-radius: 14px;
    padding: 1.75rem 2rem; margin-bottom: 1.5rem;
}
.lv-header__course { font-size: 0.8rem; color: #862736; font-weight: 600; margin-bottom: 0.25rem; }
.lv-header__title { font-size: 1.4rem; font-weight: 700; color: #1a1a1a; margin: 0; }

/* ── LESSON CONTENT ── */
.lv-content {
    background: #fff; border: 1px solid rgba(0,0,0,0.08); border-radius: 14px;
    padding: 2rem 2.5rem; margin-bottom: 1.5rem;
    line-height: 1.8; font-size: 0.95rem; color: #333;
}
.lv-content h1, .lv-content h2, .lv-content h3 { color: #1a1a1a; margin-top: 1.5rem; margin-bottom: 0.75rem; }
.lv-content h1 { font-size: 1.3rem; font-weight: 700; }
.lv-content h2 { font-size: 1.15rem; font-weight: 700; }
.lv-content h3 { font-size: 1rem; font-weight: 600; }
.lv-content p { margin-bottom: 1rem; }
.lv-content img { max-width: 100%; height: auto; border-radius: 8px; margin: 1rem 0; }
.lv-content iframe { max-width: 100%; border-radius: 8px; }
.lv-content blockquote {
    border-left: 3px solid #862736; padding: 0.75rem 1.25rem; margin: 1rem 0;
    background: rgba(134,39,54,0.03); border-radius: 0 8px 8px 0; font-style: italic; color: #5a5550;
}
.lv-content ul, .lv-content ol { padding-left: 1.5rem; margin-bottom: 1rem; }
.lv-content li { margin-bottom: 0.3rem; }

/* ── DOCUMENTS ── */
.lv-docs { margin-bottom: 1.5rem; }
.lv-docs__title { font-size: 0.85rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.5rem; }
.lv-doc {
    display: inline-flex; align-items: center; gap: 0.5rem;
    padding: 0.6rem 1rem; background: #fff; border: 1px solid rgba(0,0,0,0.08);
    border-radius: 8px; text-decoration: none; color: #1a1a1a; font-size: 0.85rem;
    margin-right: 0.5rem; margin-bottom: 0.5rem; transition: border-color 0.15s;
}
.lv-doc:hover { border-color: #862736; text-decoration: none; color: #862736; }
.lv-doc i { color: #862736; }

/* ── QUIZ SECTION ── */
.lv-quiz {
    background: #fff; border: 1px solid rgba(0,0,0,0.08); border-radius: 14px;
    padding: 1.75rem 2rem; margin-bottom: 1.5rem;
}
.lv-quiz__title {
    font-size: 1rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.25rem;
    display: flex; align-items: center; gap: 0.5rem;
}
.lv-quiz__title svg { width: 20px; height: 20px; stroke: #862736; }
.lv-quiz__subtitle { font-size: 0.8rem; color: #8a8580; margin-bottom: 1.25rem; }

.lv-question { margin-bottom: 1.25rem; padding-bottom: 1.25rem; border-bottom: 1px solid rgba(0,0,0,0.06); }
.lv-question:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
.lv-question__text { font-size: 0.9rem; font-weight: 600; color: #1a1a1a; margin-bottom: 0.75rem; }

.lv-option {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 0.6rem 0.85rem; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px;
    margin-bottom: 0.4rem; cursor: pointer; transition: all 0.15s; font-size: 0.85rem;
}
.lv-option:hover { border-color: #862736; background: rgba(134,39,54,0.02); }
.lv-option input { display: none; }
.lv-option__radio {
    width: 18px; height: 18px; border-radius: 50%; border: 2px solid rgba(0,0,0,0.2);
    flex-shrink: 0; display: flex; align-items: center; justify-content: center; transition: all 0.15s;
}
.lv-option.selected .lv-option__radio { border-color: #862736; }
.lv-option.selected .lv-option__radio::after {
    content: ''; width: 8px; height: 8px; border-radius: 50%; background: #862736;
}
.lv-option.correct { border-color: #2e7d32; background: #e8f5e9; }
.lv-option.correct .lv-option__radio { border-color: #2e7d32; }
.lv-option.correct .lv-option__radio::after { background: #2e7d32; }
.lv-option.incorrect { border-color: #c62828; background: #fce8e8; }
.lv-option.incorrect .lv-option__radio { border-color: #c62828; }
.lv-option.incorrect .lv-option__radio::after { background: #c62828; }
.lv-option.show-correct { border-color: #2e7d32; background: #e8f5e9; }

.lv-quiz__submit {
    display: inline-flex; align-items: center; gap: 0.5rem;
    padding: 0.65rem 1.5rem; background: #862736; color: #fff; border: none;
    border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer;
    transition: background 0.15s; margin-top: 0.75rem;
}
.lv-quiz__submit:hover { background: #9c2e40; }
.lv-quiz__submit:disabled { opacity: 0.5; cursor: default; }
.lv-quiz__result {
    display: none; margin-top: 1rem; padding: 1rem; border-radius: 8px;
    font-size: 0.875rem; font-weight: 600;
}
.lv-quiz__result--good { background: #e8f5e9; color: #2e7d32; }
.lv-quiz__result--ok { background: #fff3e0; color: #e65100; }

/* ── COMPLETE BUTTON ── */
.lv-complete {
    background: #fff; border: 1px solid rgba(0,0,0,0.08); border-radius: 14px;
    padding: 1.5rem 2rem; margin-bottom: 1.5rem; text-align: center;
}
.lv-complete__btn {
    display: inline-flex; align-items: center; gap: 0.5rem;
    padding: 0.75rem 2rem; background: #2e7d32; color: #fff; border: none;
    border-radius: 8px; font-size: 0.9rem; font-weight: 600; cursor: pointer;
    transition: all 0.15s;
}
.lv-complete__btn:hover { background: #1b5e20; }
.lv-complete__btn svg { width: 18px; height: 18px; }
.lv-complete__btn--done { background: #e8f5e9; color: #2e7d32; cursor: default; border: 1px solid #2e7d32; }
.lv-complete__btn--done:hover { background: #e8f5e9; }

/* ── SIDEBAR LESSON LIST ── */
.lv-sidebar__title { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #8a8580; padding: 0 1.25rem; margin-bottom: 0.75rem; }

.lv-lesson-item {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 0.65rem 1.25rem; text-decoration: none; color: #5a5550;
    font-size: 0.825rem; transition: all 0.15s; border-left: 3px solid transparent;
}
.lv-lesson-item:hover { background: rgba(0,0,0,0.03); text-decoration: none; color: #1a1a1a; }
.lv-lesson-item--current { background: rgba(134,39,54,0.04); border-left-color: #862736; color: #862736; font-weight: 600; }
.lv-lesson-item--completed { color: #2e7d32; }
.lv-lesson-item--locked { color: #bbb; cursor: default; }
.lv-lesson-item__num {
    width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
    font-size: 0.7rem; font-weight: 700; flex-shrink: 0; background: rgba(0,0,0,0.06); color: #8a8580;
}
.lv-lesson-item--current .lv-lesson-item__num { background: #862736; color: #fff; }
.lv-lesson-item--completed .lv-lesson-item__num { background: #e8f5e9; color: #2e7d32; }
.lv-lesson-item__title { flex: 1; line-height: 1.3; }

/* ── DOWNLOAD BUTTON ── */
.lv-download {
    display: inline-flex; align-items: center; gap: 0.5rem;
    padding: 0.5rem 1rem; background: rgba(134,39,54,0.06); color: #862736;
    border: 1px solid rgba(134,39,54,0.15); border-radius: 8px; font-size: 0.8rem; font-weight: 600;
    text-decoration: none; transition: all 0.15s; margin-bottom: 0.5rem;
}
.lv-download:hover { background: #862736; color: #fff; text-decoration: none; }
.lv-download i { font-size: 0.9rem; }

/* ── RESPONSIVE ── */
@media (max-width: 991px) {
    .lv-sidebar { display: none; position: fixed; top: 0; right: 0; z-index: 1040; height: 100vh; box-shadow: -4px 0 24px rgba(0,0,0,0.15); }
    .lv-sidebar.open { display: block; }
    .lv-main { max-width: 100%; padding: 1.5rem 1rem; padding-top: 70px; }
    .lv-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.3); z-index: 1035; }
    .lv-overlay.open { display: block; }
}
@media (max-width: 576px) {
    .lv-main { padding: 1rem 0.75rem; padding-top: 65px; }
    .lv-header { padding: 1.25rem 1rem; }
    .lv-content { padding: 1.25rem 1rem; }
    .lv-nav { flex-wrap: wrap; }
    .lv-header__title { font-size: 1.15rem; }
}
</style>
@stop

@section('content')
@php
    $previousLesson = $course->lessons->where('order', '<', $lesson->order)->last();
    $nextLesson = $course->lessons->where('order', '>', $lesson->order)->first();
@endphp

<div class="lv-redesign">
    <div class="lv-layout">
        {{-- ═══ MAIN CONTENT ═══ --}}
        <div class="lv-main">

            {{-- Mobile sidebar toggle --}}
            <button class="lv-sidebar-toggle" id="lvSidebarToggle" aria-label="Leksjoner">
                <svg viewBox="0 0 24 24" fill="none" stroke-linecap="round">
                    <line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/>
                </svg>
            </button>

            {{-- Navigation --}}
            <div class="lv-nav">
                <a href="{{ route('learner.course.show', $courseTaken->id) }}" class="lv-nav__back">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                    Tilbake til kurset
                </a>
                <div style="display:flex;gap:0.5rem;">
                    @if($previousLesson)
                        @if(\App\Http\FrontendHelpers::isLessonAvailable($courseTaken->started_at, $previousLesson->delay, $previousLesson->period) || \App\Http\FrontendHelpers::hasLessonAccess($courseTaken, $previousLesson))
                            <a class="lv-nav__link" href="{{ route('learner.course.lesson', ['course_id' => $course->id, 'id' => $previousLesson->id]) }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                                Forrige
                            </a>
                        @else
                            <span class="lv-nav__link disabled">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                                Forrige
                            </span>
                        @endif
                    @endif
                    @if($nextLesson)
                        @if(\App\Http\FrontendHelpers::isLessonAvailable($courseTaken->started_at, $nextLesson->delay, $nextLesson->period) || \App\Http\FrontendHelpers::hasLessonAccess($courseTaken, $nextLesson))
                            <a class="lv-nav__link" href="{{ route('learner.course.lesson', ['course_id' => $course->id, 'id' => $nextLesson->id]) }}">
                                Neste
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
                            </a>
                        @else
                            <span class="lv-nav__link disabled">
                                Neste
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
                            </span>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Lesson header --}}
            <div class="lv-header">
                <div class="lv-header__course">{{ $course->title }}</div>
                <h1 class="lv-header__title">{{ $lesson->title }}</h1>
            </div>

            {{-- Download buttons --}}
            @if($course->id != 17 && $lesson->allow_lesson_download)
                <div style="margin-bottom:1rem;">
                    @if($lesson->whole_lesson_file)
                        <a class="lv-download" href="{{ asset($lesson->whole_lesson_file) }}" download>
                            <i class="fa fa-arrow-down"></i> Last ned PDF
                        </a>
                    @else
                        <a class="lv-download" href="{{ route('learner.course.download-lesson', ['course_id' => $course->id, 'id' => $lesson->id]) }}?v={{ time() }}" id="lvDownloadBtn">
                            <i class="fa fa-arrow-down"></i> Last ned PDF
                        </a>
                    @endif
                </div>
            @endif

            {{-- Documents --}}
            @if($lesson->documents->count())
                <div class="lv-docs">
                    <div class="lv-docs__title">Dokumenter og skjemaer</div>
                    @foreach($lesson->documents as $document)
                        <a href="{{ route('learner.lesson.download-lesson-document', $document->id) }}?v={{ time() }}" class="lv-doc">
                            <i class="fa fa-download"></i> {{ $document->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Lesson content --}}
            <div class="lv-content">
                @if($course->id == 17)
                    @if($lesson->id <= 169)
                        {!! html_entity_decode($lesson->content) !!}
                    @else
                        @foreach($lesson_content as $content)
                            <h1>{{ $content->title }}</h1>
                            {!! html_entity_decode($content->lesson_content) !!}
                        @endforeach
                    @endif
                @else
                    {!! html_entity_decode(\App\Http\FrontendHelpers::parseShortcodes($lesson->content)) !!}
                @endif
            </div>

            {{-- Search for webinar replay (lesson 191 only) --}}
            @if($lesson->id == 191)
                <div style="margin-bottom:1.5rem;">
                    <form method="get" action="" style="display:flex;gap:0.5rem;max-width:400px;">
                        <input type="text" name="search_replay" class="form-control" placeholder="Søk i webinar-repriser..." value="{{ Request::get('search_replay') }}" style="border-radius:8px;font-size:0.85rem;">
                        <button type="submit" class="lv-quiz__submit" style="margin:0;">Søk</button>
                    </form>
                </div>
            @endif

            {{-- ═══ QUIZ SECTION ═══ --}}
            @if(isset($quizzes) && $quizzes->count() > 0)
                <div class="lv-quiz" id="lvQuiz">
                    <div class="lv-quiz__title">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round">
                            <circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><circle cx="12" cy="17" r="0.5"/>
                        </svg>
                        Quiz
                    </div>
                    <div class="lv-quiz__subtitle">Test deg selv på denne leksjonen</div>

                    <form id="lvQuizForm">
                        @foreach($quizzes as $qi => $quiz)
                            <div class="lv-question" data-quiz-id="{{ $quiz->id }}">
                                <div class="lv-question__text">{{ $qi + 1 }}. {{ $quiz->question }}</div>
                                @foreach($quiz->options as $oi => $option)
                                    @php
                                        $prevAnswer = isset($quizAnswers[$quiz->id]) ? $quizAnswers[$quiz->id] : null;
                                        $isSelected = $prevAnswer && $prevAnswer->selected_option === $oi;
                                        $showResult = $prevAnswer !== null;
                                    @endphp
                                    <label class="lv-option{{ $isSelected ? ' selected' : '' }}{{ $showResult && $isSelected && $prevAnswer->is_correct ? ' correct' : '' }}{{ $showResult && $isSelected && !$prevAnswer->is_correct ? ' incorrect' : '' }}{{ $showResult && !$isSelected && $oi === $quiz->correct_option ? ' show-correct' : '' }}">
                                        <input type="radio" name="quiz_{{ $quiz->id }}" value="{{ $oi }}" {{ $isSelected ? 'checked' : '' }} {{ $showResult ? 'disabled' : '' }}>
                                        <span class="lv-option__radio"></span>
                                        {{ $option }}
                                    </label>
                                @endforeach
                            </div>
                        @endforeach

                        @php $allAnswered = isset($quizAnswers) && $quizAnswers->count() === $quizzes->count(); @endphp
                        @if(!$allAnswered)
                            <button type="button" class="lv-quiz__submit" id="lvQuizSubmit" onclick="lvSubmitQuiz()">
                                Sjekk svar
                            </button>
                        @endif
                    </form>

                    <div class="lv-quiz__result" id="lvQuizResult"
                         @if($allAnswered)
                             style="display:block;"
                             @php
                                 $correctCount = $quizAnswers->where('is_correct', true)->count();
                                 $resultClass = $correctCount === $quizzes->count() ? 'lv-quiz__result--good' : 'lv-quiz__result--ok';
                             @endphp
                             class="lv-quiz__result {{ $resultClass }}"
                         @endif
                    >
                        @if($allAnswered)
                            Du fikk {{ $correctCount }} av {{ $quizzes->count() }} riktig!
                        @endif
                    </div>
                </div>
            @endif

            {{-- ═══ COMPLETE BUTTON ═══ --}}
            <div class="lv-complete" id="lvComplete">
                @if(isset($isCompleted) && $isCompleted)
                    <button class="lv-complete__btn lv-complete__btn--done" disabled>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Fullført
                    </button>
                @else
                    <button class="lv-complete__btn" id="lvCompleteBtn" onclick="lvCompleteLesson()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Marker som fullført
                    </button>
                @endif
            </div>

        </div>

        {{-- ═══ SIDEBAR ═══ --}}
        <div class="lv-sidebar" id="lvSidebar">
            <div class="lv-sidebar__title">Leksjoner</div>
            @foreach($lessons->sortBy('order')->values() as $i => $lessonLoop)
                @php
                    $status = 'locked';
                    if (\App\Http\FrontendHelpers::isLessonAvailable($courseTaken->started_at, $lessonLoop->delay, $lessonLoop->period)
                        || \App\Http\FrontendHelpers::hasLessonAccess($courseTaken, $lessonLoop)) {
                        $status = 'active';
                    }
                    if ($lessonLoop->id === $lesson->id) $status = 'current';
                    $isLessonCompleted = in_array($lessonLoop->id, $completedLessonIds ?? []);
                    if ($isLessonCompleted && $status !== 'current') $status = 'completed';
                @endphp

                <a href="{{ $status !== 'locked' ? route('learner.course.lesson', ['course_id' => $course->id, 'id' => $lessonLoop->id]) : 'javascript:void(0)' }}"
                   class="lv-lesson-item lv-lesson-item--{{ $status }}">
                    <span class="lv-lesson-item__num">
                        @if($isLessonCompleted || $status === 'completed')
                            ✓
                        @else
                            {{ $i + 1 }}
                        @endif
                    </span>
                    <span class="lv-lesson-item__title">{{ $lessonLoop->title }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <div class="lv-overlay" id="lvOverlay"></div>
</div>

{{-- Manuscript upload modal (preserved) --}}
@if($courseTaken->manuscripts->count() < $courseTaken->package->manuscripts_count)
<div id="addManuscriptModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{ trans('site.learner.course-show.upload-manuscript') }}</h3>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data" action="{{ route('learner.course.uploadManuscript', $courseTaken->id) }}">
                    {{ csrf_field() }}
                    <div class="form-group">* {{ trans('site.learner.manuscript.doc-pdf-odt-text') }}</div>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <input type="file" class="form-control" required name="file"
                                   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary float-end">
                        {{ trans('site.learner.course-show.upload-manuscript') }}
                    </button>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@stop

@section('scripts')
<script src="https://fast.wistia.com/embed/medias/68ni4qzcad.jsonp" async></script>
<script src="https://fast.wistia.com/assets/external/E-v1.js" async></script>
<script>
    /* ── Sidebar toggle ── */
    (function() {
        var toggle = document.getElementById('lvSidebarToggle');
        var sidebar = document.getElementById('lvSidebar');
        var overlay = document.getElementById('lvOverlay');
        if (!toggle || !sidebar) return;
        toggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
            if (overlay) overlay.classList.toggle('open');
        });
        if (overlay) overlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            overlay.classList.remove('open');
        });
    })();

    /* ── Auto-collapse sidebar on mobile ── */
    setTimeout(function() {
        var sidebar = document.getElementById('sidebar');
        var mainContainer = document.getElementById('main-container');
        if (window.innerWidth <= 1026 && sidebar) {
            sidebar.classList.remove('sidebar-visible');
            if (mainContainer) mainContainer.classList.remove('enlarge');
            document.body.classList.remove('sidebar-open');
        }
    }, 150);

    /* ── Complete lesson ── */
    function lvCompleteLesson() {
        var btn = document.getElementById('lvCompleteBtn');
        if (!btn) return;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-pulse"></i> Lagrer...';

        fetch('{{ route("learner.course.lesson.complete", ["course_id" => $course->id, "id" => $lesson->id]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                document.getElementById('lvComplete').innerHTML =
                    '<button class="lv-complete__btn lv-complete__btn--done" disabled>' +
                    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> Fullført</button>';
            }
        })
        .catch(function() {
            btn.disabled = false;
            btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> Marker som fullført';
        });
    }

    /* ── Submit quiz ── */
    function lvSubmitQuiz() {
        var answers = {};
        var questions = document.querySelectorAll('.lv-question');
        var allAnswered = true;

        questions.forEach(function(q) {
            var quizId = q.dataset.quizId;
            var checked = q.querySelector('input[type=radio]:checked');
            if (checked) {
                answers[quizId] = parseInt(checked.value);
            } else {
                allAnswered = false;
            }
        });

        if (!allAnswered) {
            alert('Vennligst svar på alle spørsmålene');
            return;
        }

        var btn = document.getElementById('lvQuizSubmit');
        btn.disabled = true;
        btn.textContent = 'Sjekker...';

        fetch('{{ route("learner.course.lesson.quiz", ["course_id" => $course->id, "id" => $lesson->id]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ answers: answers })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                // Show results
                Object.keys(data.results).forEach(function(quizId) {
                    var r = data.results[quizId];
                    var q = document.querySelector('.lv-question[data-quiz-id="' + quizId + '"]');
                    if (!q) return;

                    var options = q.querySelectorAll('.lv-option');
                    options.forEach(function(opt, idx) {
                        var radio = opt.querySelector('input[type=radio]');
                        radio.disabled = true;

                        if (radio.checked && r.correct) {
                            opt.classList.add('correct');
                        } else if (radio.checked && !r.correct) {
                            opt.classList.add('incorrect');
                        }
                        if (idx === r.correct_option) {
                            opt.classList.add('show-correct');
                        }
                    });
                });

                // Show score
                var result = document.getElementById('lvQuizResult');
                var pct = data.total > 0 ? data.score / data.total : 0;
                result.className = 'lv-quiz__result ' + (pct >= 0.7 ? 'lv-quiz__result--good' : 'lv-quiz__result--ok');
                result.textContent = 'Du fikk ' + data.score + ' av ' + data.total + ' riktig!';
                result.style.display = 'block';

                btn.style.display = 'none';
            }
        })
        .catch(function() {
            btn.disabled = false;
            btn.textContent = 'Sjekk svar';
        });
    }

    /* ── Scroll to top ── */
    $(document).ready(function() {
        $(window).scroll(function() {
            if ($(this).scrollTop() > 250) { $('.scroll-top').fadeIn(); }
            else { $('.scroll-top').fadeOut(); }
        });
    });
</script>
@stop
