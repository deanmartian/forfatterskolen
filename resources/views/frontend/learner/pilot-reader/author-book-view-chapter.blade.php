@extends('frontend.layout')

@section('title')
    <title>{{ $book->title }}, {{ $chapter->title ? $chapter->title : \App\Http\FrontendHelpers::getChapterTitle($book, $chapter->id)}} &rsaquo; Forfatterskolen</title>
@stop

@section('content')

    <?php
        $reader = $book->readers()->where('user_id', Auth::user()->id)->first();
        $current_version = \App\Http\FrontendHelpers::getCurrentChapterVersion($chapter);
        $version_count = \App\Http\FrontendHelpers::getChapterVersionNumber($chapter);
    ?>
    <div class="account-container">

        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content book-chapter-view">
            <div class="col-sm-12">

                <div class="col-md-8 col-sm-offset-2 col-sm-12 margin-top">

                    <div class="chapter-content">
                        <header class="chapter-name">
                            <div class="form-group">
                            <h1>
                                @if ($chapter->type == 1)
                                    {{ $chapter->title ? $chapter->title : \App\Http\FrontendHelpers::getChapterTitle($book, $chapter->id) }}
                                @else
                                    {{ $chapter->title ? $chapter->title : \App\Http\FrontendHelpers::getQuestionnaireTitle($book, $chapter->id) }}
                                @endif

                                @if ($book->author->id !== Auth::user()->id)
                                    <span class="pull-right" style="cursor: pointer; font-size: 16px"
                                    onclick="bookmark.startBookmarking(this)">
                                        <i class="fa fa-bookmark-o text-danger"></i>
                                    </span>
                                @endif
                            </h1>
                            </div>
                            <div class="hint">Version {{ $version_count }}, {{ ucwords(\App\Http\FrontendHelpers::convertMonthLanguage(
                            \Carbon\Carbon::parse($chapter->created_at)->format('n')
                            )) .' '. \Carbon\Carbon::parse($chapter->created_at)->format('d') .' - '
                            .\App\Http\FrontendHelpers::countWords($current_version->content).' words'
                            }}</div>
                        </header>

                        @if ($chapter->pre_read_guidance)
                            <div class="callout small sans margin-top">
                                <div class="title">Pre-Read Guidance for this Chapter</div>
                                <p class="margin-top">{{ $chapter->pre_read_guidance }}</p>
                            </div>
                        @endif

                        <article id="chapter">
                            {!! $current_version->content !!}
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
                            @if($book->author->id == Auth::user()->id)
                                <h4>Your notes</h4>
                                <p class="hint">
                                    You can use this "feedback" on your own work to take notes, make todo items for this
                                    chapter, or whatever else you like. It will show up with the rest of your feedback in
                                    the <a href="#">Feedback Manager</a>.
                                </p>
                            @else
                                @if($reader && $reader->role != "viewer")
                                    <h4>Your Feedback</h4>
                                @endif
                            @endif
                        </div> <!-- end chapter-feedback-header -->

                        @if($book->author->id == Auth::user()->id || ($reader && $reader->role != "viewer"))
                            <div class="chapter-feedback-item">
                            <div class="full-messages">

                                <?php
                                    /*$current_version = \App\Http\FrontendHelpers::getCurrentChapterVersion($chapter);
                                    $ownFeedback = \App\PilotReaderChapterFeedback::where('chapter_id','=',$chapter->id)
                                        ->where('chapter_version_id','=', $current_version->id)
                                        ->where('user_id', '=', Auth::user()->id)
                                        ->first();*/
                                    /*!$chapter->ownFeedback->count()*/
                                    $ownFeedback = $chapter->ownFeedback()->where('chapter_version_id','=', $current_version->id)
                                    ->first();
                                ?>

                                @if (!$ownFeedback || ($ownFeedback && !$ownFeedback->messages->count()))
                                    <div class="chapter-prompt">
                                        @if($book->author->id == Auth::user()->id)
                                            <p>You haven't added any notes on this chapter.</p>
                                        @else
                                            <p>
                                                Finished reading and ready to comment? <br>
                                                Click the 'Leave Feedback' button below.
                                            </p>
                                        @endif
                                    </div>
                                @endif


                                <ul class="list-group note-list-container">
                                    @if ($chapter->ownFeedback->count())
                                        <?php
                                            $notPubFeed = $chapter->ownFeedback[0]->messages->where('is_reply', 1)->where('published', 0)->pluck('id')->toArray();
                                        ?>
                                        @foreach($chapter->ownFeedback[0]->messages as $feedback)
                                            @if (!in_array($feedback->id, $notPubFeed))
                                                <?php
                                                    $postedBy = 'You';
                                                    if ($feedback->is_reply) {
                                                        $postedBy = $book->author->full_name;
                                                    }
                                                ?>
                                                <li class="list-group-item clearfix" id="note-li-{{ $feedback->id }}">
                                                    <div class="form-group mb-0 clearfix">
                                                        <span class="draft-label right-space {{ $feedback->published === 0 ? '' : 'hidden'}}">Draft</span>
                                                        <i class="fa fa-reply right-space text-muted {{ $feedback->is_reply === 1 ? '' : 'hidden'}}"></i>
                                                        <span class="text-muted float-left {{ $feedback->published === 0 ? 'mt-1' : ''}}">
                                                            {{ $postedBy }} {{ $feedback->published === 0 ? 'saved' : 'posted'}} at
                                                            {{ \Carbon\Carbon::parse($feedback->created_at)->format('F d H:i a') }}</span>
                                                        @if (!$feedback->is_reply)
                                                            <span class="feedback-marker unmarked">
                                                                <select onchange="methods.setMark(this, {{ $feedback->id }})"
                                                                        class="pull-right btn btn-sm select-{{ $feedback->mark }} {{ $feedback->published === 0 ?'hidden' : ''}}">
                                                                    @foreach(\App\Http\FrontendHelpers::feedbackMarks() as $feedbackMark)
                                                                        <option value="{{ $feedbackMark['option'] }}"
                                                                        @if($feedbackMark['option'] == $feedback->mark) selected @endif>
                                                                            {{ $feedbackMark['label'] }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </span>
                                                            <a class="pull-right edit-note-btn" onclick="methods.editFeedback(this, {{ $feedback }})">
                                                                <i class="fa fa-pencil"></i> <span>Edit</span>
                                                            </a>
                                                        @endif
                                                    </div>
                                                    <div class="{{ $feedback->published === 0 ? 'mt-1' : ''}} ml-2 message-content">
                                                        {!! $feedback->message ?: $feedback->message !!}
                                                    </div>
                                                    <div class="form-group edit-note-form-container mt-2">

                                                    </div>
                                                </li>
                                            @endif
                                        @endforeach
                                    @endif
                                </ul>

                            </div> <!-- end full-messages -->

                            <div class="actions">
                                <a class="beta-button color1"  onclick="methods.addNote(this)">
                                    <i class="fa fa-plus right-space"></i>Add
                                    @if($book->author->id == Auth::user()->id) Note @else Feedback @endif
                                </a>
                            </div>
                        </div> <!-- chapter-feedback-item -->
                        @endif

                            <?php
                                $book_settings = $book->settings;
                            ?>

                        @if($book->author->id == Auth::user()->id)
                            <h4 class="margin-top">Reader Feedback</h4>
                        @else
                                @if ($reader->role !== "reader")
                                    <h4 class="margin-top">Other Feedback</h4>
                                    <small class="d-block text-muted other-feedback-desc">
                                        Here's what other have to say
                                    </small>
                                @else
                                    @if($reader->role == "reader")
                                        @if ($book_settings && $book_settings->is_feedback_shared)
                                            <h4 class="margin-top">Other Feedback</h4>
                                            <small class="d-block text-muted other-feedback-desc">
                                                @if ($chapter->ownFeedback->count())
                                                    Here's what other have to say
                                                @else
                                                    You'll be able to see feedback from other readers after you've submitted your own.
                                                @endif
                                            </small>
                                        @endif
                                    @else
                                        <h4 class="margin-top">Other Feedback</h4>
                                        <small class="d-block text-muted other-feedback-desc">
                                            @if ($chapter->ownFeedback->count())
                                                Here's what other have to say
                                            @else
                                                You'll be able to see feedback from other readers after you've submitted your own.
                                            @endif
                                        </small>
                                    @endif
                                @endif
                        @endif
                        <div class="feedback-list">
                            <?php
                                $chapter_feedbacks = $book->author->id == Auth::user()->id ? $chapter->feedbacks
                                    : $chapter->feedbacks()->where('chapter_id', $chapter->id)
                                        ->where('user_id', '!=', $book->author->id)->get();
                                $showOtherFeedback = 1;
                            ?>
                            @if ($book->author->id != Auth::user()->id && $reader && $reader->role === "reader")
                                @if (!$chapter->ownFeedback->count() || ($book_settings && !$book_settings->is_feedback_shared))
                                    <?php
                                        $showOtherFeedback = 0;
                                    ?>
                                @endif
                            @endif

                            @foreach($chapter_feedbacks as $feedback)
                            <?php
                                $notPubFeed = $feedback->messages->where('is_reply', 0)->where('published', 0)->pluck('id')->toArray();
                                $notPubReplyByOther = $feedback->messages->where('is_reply', 1)->where('published', 0)
                                    ->where('reply_from', '!=', Auth::user()->id)->pluck('id')->toArray();
                                $hasValidFeedback = 0;
                                foreach($feedback->messages as $m) {
                                    if (!in_array($m->id, $notPubFeed) && !in_array($m->id, $notPubReplyByOther)) {
                                        $hasValidFeedback++;
                                    }
                                }
                            ?>
                            @if($feedback->messages->count() && $hasValidFeedback)
                                <div class="chapter-feedback-item reader-feedback {{ !$showOtherFeedback ? 'display-none' : '' }}">
                                <div class="full-messages" id="feedback-{{$feedback->id}}">
                                    <div class="title">
                                        {{ $feedback->user->full_name }}'s Feedback
                                    </div> <!-- end title -->

                                    <ul class="list-group feedback-list-container">
                                        @foreach($feedback->messages as $message)
                                            @if (!in_array($message->id, $notPubFeed) && !in_array($message->id, $notPubReplyByOther))
                                                <?php
                                                    $postedBy = $feedback->user->full_name;
                                                    if ($message->is_reply) {
                                                        $postedBy = $message->reply_from == Auth::user()->id || $book->author->id == Auth::user()->id ?
                                                            'You' : \App\User::find($message->reply_from)->full_name;
                                                    }
                                                ?>
                                                <li class="list-group-item clearfix" id="feedback-li-{{ $message->id }}">
                                                    <div class="form-group mb-0 clearfix">
                                                        <span class="draft-label right-space {{ $message->published === 0 ? '' : 'hidden'}}">Draft</span>
                                                        <i class="fa fa-reply right-space text-muted {{ $message->is_reply === 1 ? '' : 'hidden'}}"></i>
                                                        <span class="text-muted float-left {{ $message->published === 0 ? 'mt-1' : ''}}">
                                                        {{ $postedBy }} {{ $message->published === 0 ? 'saved' : 'posted'}} at
                                                            {{ \Carbon\Carbon::parse($message->created_at)->format('F d H:i a') }}</span>
                                                        @if ($message->is_reply && ($message->reply_from == Auth::user()->id))
                                                            <span class="feedback-marker unmarked">
                                                                <select onchange="methods.setMark(this, {{ $message->id }})"
                                                                        class="pull-right btn btn-sm select-{{ $message->mark }} {{ $message->published === 0 ?'hidden' : ''}}">
                                                                    @foreach(\App\Http\FrontendHelpers::feedbackMarks() as $feedbackMark)
                                                                        <option value="{{ $feedbackMark['option'] }}"
                                                                                @if($feedbackMark['option'] == $message->mark) selected @endif>
                                                                            {{ $feedbackMark['label'] }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </span>
                                                            <a class="pull-right edit-reply-btn" onclick="methods.editReply(this, {{ $message }})">
                                                                <i class="fa fa-pencil"></i> <span>Edit</span>
                                                            </a>
                                                        @endif
                                                    </div> <!-- form-group mb-0 clearfix -->

                                                    <div class="{{ $message->published === 0 ? 'mt-1' : ''}} ml-2 message-content">
                                                        {!! $message->message ?: $message->message !!}
                                                    </div>
                                                    <div class="form-group edit-note-form-container mt-2">

                                                    </div>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>

                                @if($book->author->id == Auth::user()->id || ($reader && $reader->role != "viewer"))
                                    <div class="actions">
                                        <a class="beta-button color1"  onclick="methods.addReply(this, {{ $feedback->id }})">
                                            <i class="fa fa-plus right-space"></i>Add Reply
                                        </a>
                                    </div>
                                @endif
                            </div> <!-- end chapter-feedback-item -->
                            @endif
                        @endforeach
                        </div>

                    </div> <!-- end chapter-feedback -->

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
        let book_id = "{{ $book->id }}";
        let add_note_link = '{{ route('learner.book-author-book-chapter-note-create') }}';
        let update_note_link = '{{ route('learner.book-author-book-chapter-note-update') }}';
        let add_feed_link = ' {{ route('learner.book-author-book-chapter-feedback-create') }}';
        let update_feed_link = '{{ route('learner.book-author-book-chapter-feedback-update') }}';
        let delete_note_link = '{{ route('learner.book-author-book-chapter-draft-delete') }}';
        let note_list_link = '{{ route('learner.book-author-book-chapter-note-list', $chapter->id) }}';
        let autogrow_link = '{{ asset('js/autogrow/plugin.js') }}';
        let author_id = '{{ $book->author->id }}';
        let current_user = '{{ Auth::user()->id }}';
        let hasBookmark = '{{ Request::input('bookmark') }}';
        let showOtherFeedback = parseInt('{{ $showOtherFeedback }}');
    </script>
    <script src="{{ asset('js/pilot-reader/chapter-feedback.js') }}"></script>

    <script>

        const bookmark = {
            startBookmarking: function(el) {
                let icon = $(el).find(".fa");
                let chapter_content = $("#chapter");
                let methods = this;
                let bookmark_element = chapter_content.find(".bookmark");

                if(icon.hasClass("fa-bookmark-o")){
                    icon.addClass("fa-bookmark").removeClass("fa-bookmark-o");
                    chapter_content.addClass("bookmark-active");
                    this.setHover(bookmark_element);
                    $("#chapter > *").on('click', function(){
                        let self = this;
                        let paragraph_order = $(this).index();
                        let paragraph_text = $(this).text();
                        if($(this).is(".bookmark.hover")){
                            $(this).removeClass();
                            methods.setBookMark(paragraph_order, paragraph_text, 'remove')
                        }else{
                            $(this).addClass("bookmark");
                            methods.setBookMark(paragraph_order, paragraph_text, 'add')
                        }
                        methods.setHover(this);
                        $("#chapter > .bookmark").each(function(){
                            if(self === this){
                                return true
                            }
                            $(this).removeClass("bookmark");
                            $(this).off('mouseover').off('mouseout')
                        })
                    })
                } else {
                    icon.addClass("fa-bookmark-o").removeClass("fa-bookmark");
                    chapter_content.removeClass("bookmark-active");
                    $("#chapter > *").off('click');
                    $(bookmark_element).off('mouseover').off('mouseout');
                }
            },

            setHover : function(el){
                $(el).off('mouseover').off('mouseout');
                if($(el).hasClass("bookmark")){
                    $(el).on('mouseover', function(){
                        $(el).addClass('hover')
                    }).on('mouseout', function(){
                        $(el).removeClass('hover')
                    })
                }
            },

            setBookMark : function(paragraph_order, paragraph_text, action){
                $.post('/account/book-author/chapter/bookmark/set', {
                    chapter_id : chapter_id,
                    book_id: book_id,
                    paragraph_order : paragraph_order,
                    paragraph_text : paragraph_text,
                    action : action
                })
                    .then(function(response){
                    })
                    .catch(function(error){
                    })
            },

            getBookMark : function(){
                let self = this;
                $.get('/account/book-author/chapter/bookmark/get/' + chapter_id)
                    .then(function(response){
                        $(`#chapter > *:eq(${ response.paragraph_order })`).addClass("bookmark");
                        if (hasBookmark) {
                            $('html, body').animate({
                                scrollTop: $(`#chapter > *:eq(${ response.paragraph_order })`).offset().top
                            }, 500);
                        }
                    });
            }
        };


        bookmark.getBookMark();

    </script>
@stop