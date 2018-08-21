<div class="book-title-wrapper">
    <div class="book-title-editor">
        <h1>{{ $book->title }}</h1>
        <div class="subhead">
            By {{ $book->display_name ? $book->display_name : $book->author->full_name }}
        </div>
    </div> <!-- end book-title-editor -->
    <div class="hint">{{ $book->chapterWordSum() }} words</div>
</div> <!-- end book-title-wrapper -->

<h2>Table of Contents</h2>


@if ($book->chapters->count())
    <table id="table-of-contents" class="table draggable-table">
        <tbody>
        @foreach($book->chapters as $k => $chapter)
            @if (!$chapter->is_hidden)
                <tr class="chapter">
                    <td class="status @if($chapter->readingChapter->count()) read @else unread @endif ">
                        <div class="icon"></div>
                    </td>
                    <td class="title">
                        <a href="{{ route('learner.book-author-book-view-chapter',
                        ['book_id' => $book->id, 'chapter_id' => $chapter->id]) }}">
                            {{ $chapter->type == 1 ?
                            ($chapter->title ? $chapter->title : \App\Http\FrontendHelpers::getChapterTitle($book, $chapter->id))
                            : ($chapter->title ? $chapter->title : \App\Http\FrontendHelpers::getQuestionnaireTitle($book, $chapter->id)) }}
                        </a>
                        <?php
                        $bookmarkerId = Auth::user()->id;
                        $hasBookmarks = \App\PilotReaderBookBookmark::where('chapter_id', $chapter->id)
                            ->where('bookmarker_id', $bookmarkerId)
                            ->get();
                        ?>
                        @foreach($hasBookmarks as $hasBookmark)
                            <a href="{{ url('/account/book-author/book/'.$hasBookmark->book_id
                                    .'/chapter/'.$hasBookmark->chapter_id.'?bookmark=true') }}" class="bookmark">
                                <i class="fa fa-bookmark text-danger"></i>
                            </a>
                        @endforeach
                    </td>
                    <td class="words text-right">
                        <div class="hint">{{ $chapter->word_count }} words</div>
                    </td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
@else
    <p>No chapters yet</p>
@endif