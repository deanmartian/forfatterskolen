@extends('frontend.layout')

@section('title')
    <title>{{ $book->title }} &rsaquo; Forfatterskolen</title>
@stop

@section('heading') My Books @stop

@section('content')
    <div class="account-container">

        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content author-view-feedbacks white-background">
            <div class="col-sm-12">
                <div class="col-md-8 col-sm-offset-2 col-sm-12 margin-top">
                    @include('frontend.learner.pilot-reader.partials.nav')

                    @if($book->author->id == Auth::user()->id)
                        <div class="card">
                        <div class="card-body">
                            <h4 class="card-title with-border-b pb-3">
                                Notes
                            </h4>

                            <!-- display all author notes -->
                            @foreach($feedbacks as $feedback)
                                <div class="chapter-feedback-item mb-3">
                                    <div class="full-messages" id="feedback-{{ $feedback->id }}">
                                        <div class="title">
                                            Your notes on
                                            <a href="{{ route('learner.book-author-book-view-chapter',
                                            ['bookd_id' => $book->id, 'chapter_id' => $feedback->chapter_id]) }}">
                                                {{ $feedback->chapter->type == 1 ?
                            ($feedback->chapter->title ? $feedback->chapter->title : \App\Http\FrontendHelpers::getChapterTitle($book, $feedback->chapter->id))
                            : ($feedback->chapter->title ? $feedback->chapter->title : \App\Http\FrontendHelpers::getQuestionnaireTitle($book, $feedback->chapter->id)) }}
                                            </a>
                                        </div>
                                        <ul class="list-group note-list-container">
                                            @foreach($feedback->messages()->whereNotIn('mark',['ignore','done'])->get() as $message)
                                                <li class="list-group-item clearfix" id="note-li-{{ $message->id }}">
                                                    <div class="form-group mb-0 clearfix">
                                                        <span class="draft-label right-space {{ $message->published === 0 ? '' : 'hidden'}}">Draft</span>
                                                        <i class="fa fa-reply right-space text-muted {{ $message->is_reply === 1 ? '' : 'hidden'}}"></i>
                                                        <span class="text-muted float-left {{ $message->published === 0 ? 'mt-1' : ''}}">
                                                            You {{ $message->published === 0 ? 'saved' : 'posted'}} at
                                                            {{ \Carbon\Carbon::parse($message->created_at)->format('F d H:i a') }}</span>
                                                        @if (!$message->is_reply)
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
                                                            <a class="pull-right edit-note-btn" onclick="methods.editFeedback(this, {{ $message }})"
                                                            id="chp-{{ $feedback->chapter_id }}">
                                                                <i class="fa fa-pencil"></i> <span>Edit</span>
                                                            </a>
                                                        @endif
                                                    </div>

                                                    <div class="{{ $message->published === 0 ? 'mt-1' : ''}} ml-2 message-content">
                                                        {!! $message->message ?: $message->message !!}
                                                    </div>

                                                    <div class="form-group edit-note-form-container mt-2">

                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div> <!-- end full-messages -->

                                    <div class="actions">
                                        <a class="beta-button color1"  onclick="methods.addNote(this)" id="add-note-chp-{{ $feedback->chapter_id }}">
                                            <i class="fa fa-plus right-space"></i>Add
                                            @if($book->author->id == Auth::user()->id) Note @else Feedback @endif
                                        </a>
                                    </div>

                                </div> <!-- end chapter-feedback-item -->
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="card margin-top-16">
                        <div class="card-body">
                            <h4 class="card-title with-border-b pb-3">
                                Feedbacks
                            </h4>

                            <div class="feedback-list">
                                @foreach($readerFeedbacks as $feedback)
                                    <?php
                                        $messages = $feedback->messages()->whereNotIn('mark',['ignore','done'])->get();
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
                                    @if($messages->count() && $hasValidFeedback)
                                            <div class="chapter-feedback-item mb-3">
                                                <div class="full-messages" id="feedback-{{$feedback->id}}">
                                                    <div class="title">
                                                        {{ $feedback->user->full_name }}'s Feedback on
                                                        <a href="{{ route('learner.book-author-book-view-chapter',
                                            ['bookd_id' => $book->id, 'chapter_id' => $feedback->chapter_id]) }}">
                                                            {{ $feedback->chapter->type == 1 ?
                            ($feedback->chapter->title ? $feedback->chapter->title : \App\Http\FrontendHelpers::getChapterTitle($book, $feedback->chapter->id))
                            : ($feedback->chapter->title ? $feedback->chapter->title : \App\Http\FrontendHelpers::getQuestionnaireTitle($book, $feedback->chapter->id)) }}
                                                        </a>
                                                    </div> <!-- end title -->

                                                    <ul class="list-group feedback-list-container">
                                                        @foreach($messages as $message)
                                                            @if (!in_array($message->id, $notPubFeed) && !in_array($message->id, $notPubReplyByOther))
                                                                <?php
                                                                $postedBy = $feedback->user->full_name;
                                                                if ($message->is_reply) {
                                                                    $postedBy = $message->reply_from == Auth::user()->id ?
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
                                                                        @if ($message->is_reply && ($message->reply_from == Auth::user()->id))
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
                                                </div> <!-- end full-messages -->

                                                <div class="actions">
                                                    <a class="beta-button color1"  onclick="methods.addReply(this, {{ $feedback->id }})"
                                                    id="add-reply-chp-{{ $feedback->chapter->id }}">
                                                        <i class="fa fa-plus right-space"></i>Add Reply
                                                    </a>
                                                </div>

                                            </div> <!-- end chapter-feedback-item reader-feedback -->
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div> <!-- end col-md-8 col-sm-offset-2 col-sm-12 margin-top -->
            </div> <!-- end col-sm-12 -->
        </div> <!-- end col-sm-12 col-md-10 sub-right-content white-background -->

        <div class="clearfix"></div>
    </div>
@stop

@section('scripts')
    <script src="https://cdn.ckeditor.com/4.10.0/standard/ckeditor.js" integrity="sha384-BpuqJd0Xizmp9PSp/NTwb/RSBCHK+rVdGWTrwcepj1ADQjNYPWT2GDfnfAr6/5dn" crossorigin="anonymous"></script>
    <script src="{{ asset('js/showdown/dist/showdown.min.js') }}"></script>
    <script src="{{ asset('js/moment/min/moment.min.js') }}"></script>
    <script>
        let update_feed_link = '{{ route('learner.book-author-book-chapter-feedback-update') }}';
        let add_feed_link = ' {{ route('learner.book-author-book-chapter-feedback-create') }}';
        let delete_note_link = '/account/chapter/draft/delete';
        let autogrow_link = '{{ asset('js/autogrow/plugin.js') }}';
        let author_id = '{{ $book->author->id }}';
        let current_user = '{{ Auth::user()->id }}';
    </script>
    <script src="{{ asset('js/pilot-reader/feedback-tab.js') }}"></script>
@stop