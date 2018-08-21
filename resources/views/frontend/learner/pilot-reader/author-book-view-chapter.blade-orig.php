@extends('frontend.layout')

@section('title')
    <title>{{ $book->title }}, {{ $chapter->title ? $chapter->title : 'Chapter '.$key }} &rsaquo; Forfatterskolen</title>
@stop

@section('content')

    <div class="account-container">

        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content book-chapter-view">
            <div class="col-sm-12">

                <div class="col-md-8 col-sm-offset-2 col-sm-12 margin-top">

                    <div class="chapter-content">
                        <header class="chapter-name">
                            <h1> {{ $chapter->title ? $chapter->title : 'Chapter '.$key }} </h1>
                            <div class="hint">{{ ucwords(\App\Http\FrontendHelpers::convertMonthLanguage(
                            \Carbon\Carbon::parse($chapter->created_at)->format('n')
                            )) .' '. \Carbon\Carbon::parse($chapter->created_at)->format('d') .' - '
                            .$chapter->word_count.' words'
                            }}</div>
                        </header>

                        @if ($chapter->pre_read_guidance)
                            <div class="callout small sans margin-top">
                                <div class="title">Pre-Read Guidance for this Chapter</div>
                                <p class="margin-top">{{ $chapter->pre_read_guidance }}</p>
                            </div>
                        @endif

                        <article id="chapter">
                            {!! $chapter->chapter_content !!}
                        </article>

                        <div class="post-chapter-nav">
                            <nav class="chapters">
                                @if ($previous)
                                    <a href="{{ route('learner.book-author-book-view-chapter',
                                        ['book_id' => $book->id, 'chapter_id' => $previous]) }}" class="beta-button pull-left">
                                        <i class="fa fa-long-arrow-left"></i>
                                        Previous
                                    </a>
                                @endif

                                @if ($next)
                                    <a href="{{ route('learner.book-author-book-view-chapter',
                                        ['book_id' => $book->id, 'chapter_id' => $next]) }}" class="beta-button pull-right">
                                        <i class="fa fa-long-arrow-right"></i>
                                        Next
                                    </a>
                                @endif
                            </nav>
                        </div> <!-- end post-chapter-nav -->
                    </div> <!-- end chapter-content -->

                    <div id="feedback" class="chapter-feedback">
                        @if ($chapter->post_read_guidance)
                            <div class="callout small sans margin-top">
                                <div class="title">For this Chapter</div>
                                <p class="margin-top">{{ $chapter->post_read_guidance }}</p>
                            </div>
                        @endif

                        <div class="chapter-feedback-header">
                            <h4>Your notes</h4>
                            <p class="hint">
                                You can use this "feedback" on your own work to take notes, make todo items for this
                                chapter, or whatever else you like. It will show up with the rest of your feedback in
                                the <a href="#">Feedback Manager</a>.
                            </p>
                        </div> <!-- end chapter-feedback-header -->

                        <div class="chapter-feedback-item">
                            <div class="full-messages">

                                <div class="chapter-prompt">
                                    <p>You haven't added any notes on this chapter.</p>
                                </div>

                                <ul class="list-group" id="note-list-container"></ul>

                            </div> <!-- end full-messages -->

                            <div class="actions">
                                <a class="beta-button color1"  onclick="methods.addNote(this)">
                                    <i class="fa fa-plus right-space"></i>Add Note
                                </a>
                            </div>
                        </div>

                    </div> <!-- end chapter-feedback -->

                    <h4 class="margin-top">Reader Feedback</h4>

                </div> <!-- end col-xs-offset-2 col-sm-8 margin-top -->
            </div> <!-- end col-sm-12 -->
        </div> <!-- end col-sm-12 col-md-10 sub-right-content book-chapter-view -->

        <div class="clearfix"></div>

    </div>

@stop

@section('scripts')
    <script src="https://cdn.ckeditor.com/4.10.0/standard/ckeditor.js" integrity="sha384-BpuqJd0Xizmp9PSp/NTwb/RSBCHK+rVdGWTrwcepj1ADQjNYPWT2GDfnfAr6/5dn" crossorigin="anonymous"></script>
    <script src="{{ asset('js/showdown/dist/showdown.min.js') }}"></script>
    <script src="{{ asset('js/moment/min/moment.min.js') }}"></script>
    <script>
        let chapter_id = "{{ $chapter->id }}";
        let add_note_link = '{{ route('learner.book-author-book-chapter-note-create') }}';
        let update_note_link = '{{ route('learner.book-author-book-chapter-note-update') }}';
        let delete_note_link = '{{ route('learner.book-author-book-chapter-draft-delete') }}';
        let note_list_link = '{{ route('learner.book-author-book-chapter-note-list', $chapter->id) }}';
        let autogrow_link = '{{ asset('js/autogrow/plugin.js') }}';
    </script>
    <script src="{{ asset('js/pilot-reader/chapter-note.js') }}"></script>
@stop