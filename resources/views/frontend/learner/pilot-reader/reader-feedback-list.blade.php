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

                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title pb-3">
                                {{ $book->title }} <br>
                                <span class="text-muted lead-17">My Feedback</span>
                            </h4>

                            <!-- display all reader feedback for a chapter -->
                            <div class="feedback-list">
                                @foreach($book->chapters as $chapter)
                                    <div class="chapter-feedback-item mb-3">
                                        <div class="full-messages">
                                            <div class="title">
                                                {{ $chapter->title }}
                                                <a href="{{ route('learner.book-author-book-view-chapter',
                                            ['bookd_id' => $book->id, 'chapter_id' => $chapter->id]) }}">
                                                    {{ $chapter->type == 1 ?
                            ($chapter->title ? $chapter->title : \App\Http\FrontendHelpers::getChapterTitle($book, $chapter->id))
                            : ($chapter->title ? $chapter->title : \App\Http\FrontendHelpers::getQuestionnaireTitle($book, $chapter->id)) }}
                                                </a>
                                            </div> <!-- end title -->
                                            <?php
                                                $getMyFeedbacks = $chapter->ownFeedback;
                                                $displayMessageCount = 0;
                                            ?>
                                            @if($getMyFeedbacks->count())
                                                <?php
                                                $notPubFeed = $chapter->ownFeedback[0]->messages->where('is_reply', 1)->where('published', 0)->pluck('id')->toArray();
                                                ?>
                                                <ul class="list-group note-list-container">
                                                    @foreach($getMyFeedbacks[0]->messages as $message)
                                                        @if (!in_array($message->id, $notPubFeed))
                                                            <?php
                                                            $postedBy = 'You';
                                                            if ($message->is_reply) {
                                                                $postedBy = $book->author->full_name;
                                                            }
                                                            $displayMessageCount++;
                                                            ?>
                                                            <li class="list-group-item clearfix" id="note-li-{{ $message->id }}">
                                                                <div class="form-group mb-0 clearfix">
                                                                    <span class="draft-label right-space {{ $message->published === 0 ? '' : 'hidden'}}">Draft</span>
                                                                    <i class="fa fa-reply right-space text-muted {{ $message->is_reply === 1 ? '' : 'hidden'}}"></i>
                                                                    <span class="text-muted float-left {{ $message->published === 0 ? 'mt-1' : ''}}">
                                                                {{ $postedBy }} {{ $message->published === 0 ? 'saved' : 'posted'}} at
                                                                        {{ \Carbon\Carbon::parse($message->created_at)->format('F d H:i a') }}</span>
                                                                    @if (!$message->is_reply)
                                                                        <a class="pull-right edit-reply-btn" onclick="methods.editFeedback(this, {{ $message }})">
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
                                            @endif

                                            @if(!$getMyFeedbacks->count() || $displayMessageCount == 0)
                                                <div class="chapter-prompt">
                                                    Finished reading and ready to comment? <br>
                                                    Click the 'Leave Feedback' button below.
                                                </div>
                                            @endif
                                        </div> <!-- end full-messages -->

                                        <div class="actions">
                                            <a class="beta-button color1"  onclick="methods.addNote(this)" id="add-note-chp-{{ $chapter->id }}">
                                                <i class="fa fa-plus right-space"></i>Add Feedback
                                            </a>
                                        </div>

                                    </div>
                                @endforeach
                            </div> <!-- end feedback-list -->
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
    <script src="{{ asset('js/pilot-reader/reader-feedback.js') }}"></script>
@stop