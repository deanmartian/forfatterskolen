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
                                    <?php
                                        $otherFeedbacks = \App\PilotReaderChapterFeedback::where('chapter_id', $chapter->id)
                                            ->where('user_id', '!=', $book->author->id)->get();
                                    ?>
                                    @if(count($otherFeedbacks))
                                        <div class="card">
                                            <div class="card-body">
                                                <h4 class="card-title pb-3 with-border text-center pt-3">
                                                    {{ $chapter->title }}
                                                    <a href="{{ route('learner.book-author-book-view-chapter',
                                                ['bookd_id' => $book->id, 'chapter_id' => $chapter->id]) }}">
                                                        {{ $chapter->type == 1 ?
                                ($chapter->title ? $chapter->title : \App\Http\FrontendHelpers::getChapterTitle($book, $chapter->id))
                                : ($chapter->title ? $chapter->title : \App\Http\FrontendHelpers::getQuestionnaireTitle($book, $chapter->id)) }}
                                                    </a>
                                                </h4>

                                                <div class="feedback-list" style="padding: 10px">
                                                    @foreach($otherFeedbacks as $feedback)
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
                                                            $messages = $feedback->messages;
                                                        ?>
                                                        @if($feedback->messages->count() && $hasValidFeedback)
                                                            <div class="chapter-feedback-item mb-3">
                                                            <div class="full-messages">
                                                                <div class="title">
                                                                    {{ $feedback->user->id == Auth::user()->id ? "Your Feedback"
                                                                    :$feedback->user->full_name."'s Feedback" }}
                                                                </div>

                                                                <ul class="list-group feedback-list-container">
                                                                    @foreach($messages as $message)
                                                                        @if (!in_array($message->id, $notPubFeed) && !in_array($message->id, $notPubReplyByOther))
                                                                            <?php
                                                                            $postedBy = $feedback->user->id == Auth::user()->id ? 'You' :$feedback->user->full_name;
                                                                            if ($message->is_reply) {
                                                                                $postedBy = $message->reply_from == Auth::user()->id || $book->author->id == Auth::user()->id ?
                                                                                    'You' : \App\User::find($message->reply_from)->full_name;
                                                                            }
                                                                            ?>
                                                                            <li class="list-group-item clearfix" id="feedback-li-{{ $message->id }}">
                                                                                <div class="form-group mb-0 clearfix">
                                                                                    <i class="fa fa-reply right-space text-muted {{ $message->is_reply === 1 ? '' : 'hidden'}}"></i>
                                                                                    <span class="text-muted float-left {{ $message->published === 0 ? 'mt-1' : ''}}">
                                                                                        {{ $postedBy }} {{ $message->published === 0 ? 'saved' : 'posted'}} at
                                                                                        {{ \Carbon\Carbon::parse($message->created_at)->format('F d H:i a') }}</span>
                                                                                </div>

                                                                                <div class="{{ $message->published === 0 ? 'mt-1' : ''}} ml-2 message-content">
                                                                                    {!! $message->message ?: $message->message !!}
                                                                                </div>
                                                                            </li>
                                                                        @endif
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
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