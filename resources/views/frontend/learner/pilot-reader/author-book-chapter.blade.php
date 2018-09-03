@extends('frontend.layout')

@section('title')
    <title>{{ $book->title }}, Chapter &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@stop

@section('content')

    <?php
        $current_version = isset($chapterObj) ? \App\Http\FrontendHelpers::getCurrentChapterVersion($chapterObj) : '';
        $current_version_content = isset($chapterObj) ? $current_version['content'] : '';
    ?>
    <div class="account-container">

        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content book-chapter">
            <div class="col-sm-12">

                <div class="col-md-8 col-sm-offset-2 col-sm-12 margin-top">

                    <h1>
                        @if(Request::is('account/book-author/book/*/chapter/new/*'))
                            New {{ $chapter['type'] == 1 ? 'Chapter' : 'Questionnaire' }}
                        @else
                            Edit {{ $chapter['type'] == 1 ? 'Chapter' : 'Questionnaire' }}
                        @endif
                    </h1>

                    <form action="@if(Request::is('account/book-author/book/*/chapter/new/*')){{ route('learner.book-author-book-create-chapter', ['id' => $book->id,'type' => $chapter['type']]) }}@else{{ route('learner.book-author-book-update-chapter',
                    ['book_id' => $book->id, 'chapter_id' => $chapter['id']]) }}@endif" method="POST">

                        {{ csrf_field() }}
                        @if(!Request::is('account/book-author/book/*/chapter/new/*'))
                            {{ method_field('PUT') }}
                        @endif
                        <div class="form-group">
                            <?php
                                $settings = $book->settings;
                                $chapter_title = $chapter['type'] == 1 ? ($settings ? $settings->book_units:'Chapter'):'Questionnaire';
                            ?>
                            <label class="display-block">Title</label>
                            <input type="text" class="form-control" placeholder="{{ $chapter_title }} {{ $chapter['type'] == 1
                            ? (Request::is('account/book-author/book/*/chapter/new/*') ? $book->chaptersOnly->count() + 1 : $book->chaptersOnly->count())
                            : (Request::is('account/book-author/book/*/chapter/new/*') ? $book->chapterQuestionnaire->count() + 1 : $book->chapterQuestionnaire->count()) }}"
                            name="title" value="{{ $chapter['title'] }}">
                        </div>

                        @if ($chapter['type'] == 1)
                        <div class="form-group">
                            <label class="display-block">Beginning of Chapter Guidance</label>
                            <textarea name="pre_read_guidance" class="form-control">{{ $chapter['pre_read_guidance'] }}</textarea>

                            <div class="hint">
                                If you enter guidance here, it will appear at the top of the Chapter, before the text.
                                Put any guidance you want to give the reader <em>before</em> they read this Chapter here.
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="display-block">End of Chapter Guidance</label>
                            <textarea name="post_read_guidance" class="form-control">{{ $chapter['post_read_guidance'] }}</textarea>

                            <div class="hint">
                                This will appear at the end of the Chapter, right before the reader's feedback prompt.
                                Put any guidance you want them to see <em>after</em> they read here.
                            </div>
                        </div>
                        @endif

                        <div class="form-group">
                            <label class="display-block">Content</label>
                            <div id="editor" style="height: 145px">
                                {!! $current_version_content !!}
                            </div>
                            <input type="hidden" name="chapter_content" value="{{ $current_version_content }}">
                        </div>

                        @if($chapter['type'] == 1)
                            <div class="form-group">
                                <label for="notify-readers">
                                    <input id="notify-readers" name="notify_readers" type="checkbox" value="true"
                                    @if($chapter['notify_readers']) checked @endif> Notify Readers?
                                </label>
                                <div class="hint">
                                    If you check this box, your readers will receive a notification to let them know that you added this chapter.
                                </div>
                            </div>
                            @if(!Request::is('account/book-author/book/*/chapter/new/*'))
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox mt-2 no-left-padding">
                                        <input type="checkbox" class="custom-control-input" name="save_new_version" id="saveNewVerCheckbox">
                                        <label class="custom-control-label" for="saveNewVerCheckbox"> <b>Save New Version?</b> </label>
                                    </div>
                                    <small class="text-muted d-block is-required-msg">Check this box to save a new version of your work. If you have received feedback on this , we recommend you make changes on a new version, otherwise the feedback left before may no longer make sense. You can compare changes between versions on the chapter screen.</small>
                                </div>
                                <div class="form-group mt-2 change_desc_div">
                                    <label for="" class="label-control font-weight-light">Description of changes</label>
                                    <textarea name="change_description" cols="30" rows="3" class="form-control"></textarea>
                                    <small class="text-muted d-block">If you enter a message here it will be shown on the versions menu and compare versions page.</small>
                                </div>
                            @endif
                        @endif

                        <button class="beta-button color1" id="submit_form">
                            @if(Request::is('account/book-author/book/*/chapter/new/*'))
                                Create
                            @else
                                Save Changes
                            @endif

                        </button>
                        <a href="{{ route('learner.book-author-book-show', $book->id) }}" class="beta-button">Cancel</a>
                    </form>
                </div> <!-- end col-xs-offset-2 col-xs-8 margin-top -->
            </div> <!-- end col-sm-12 -->
        </div> <!-- end col-sm-12 col-md-10 sub-right-content show-book -->

        <div class="clearfix"></div>

    </div>

@stop

@section('scripts')
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <!-- Initialize Quill editor -->
    <script>
        var quill = new Quill('#editor', {
            theme: 'snow'
        });


        $("#submit_form").click(function(e){
            e.preventDefault();
            $("input[name=chapter_content]").val(quill.container.firstChild.innerHTML);
            $("form").submit();
        });
    </script>
@stop