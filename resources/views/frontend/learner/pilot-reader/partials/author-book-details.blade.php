<div class="book-title-wrapper">
    <div class="book-title-editor">
        <h1>{{ $book->title }}</h1>
        <div class="subhead">
            By {{ $book->display_name ? $book->display_name : $book->author->full_name }}
            <div class="edit-toggle-buttons pull-right" style="color: #3a3023">
                <button class="beta-button" data-fields="{{ json_encode($book) }}"
                        data-display-name="{{ $book->display_name}}"
                        data-author-placeholder="{{ Auth::user()->full_name }}"
                        onclick="editAuthorTitle(this)">
                    <i class="fa fa-pencil"></i>
                    <span>Edit</span>
                </button>
            </div>
        </div>
    </div> <!-- end book-title-editor -->
    <div class="hint">{{ $book->chapterWordSum() }} words</div>
</div> <!-- end book-title-wrapper -->

<div class="book-description">
    <h2>About the Book
        <div class="edit-toggle-buttons pull-right">
            <button class="beta-button" data-fields="{{ json_encode($book) }}"
                    onclick="editAboutBook(this)">
                <i class="fa fa-pencil"></i>
                <span>Edit</span>
            </button>
        </div>
    </h2>
    <div id="description-container">
        {!! $book->about_book !!}
    </div>
</div> <!-- end book-description -->

<div class="book-critique-guidance">
    <h2>Critique Guidance
        <div class="edit-toggle-buttons pull-right">
            <button class="beta-button" onclick="editCritique(this)">
                <i class="fa fa-pencil"></i>
                <span>Edit</span>
            </button>
        </div>
    </h2>

    <div id="description-container">
        {!! $book->critique_guidance !!}
    </div>
</div><!-- end book-critique-guidance -->

<h2>Contents</h2>

<table class="table draggable-table">
    <tbody>
    @foreach($book->chapters as $chapter)
        <tr id="chapter_{{ $chapter->id }}" class="@if($chapter->is_hidden) is-hidden @endif">
            <td class="drag-handle">
                <i class="fa fa-ellipsis-v"></i>
                <i class="fa fa-ellipsis-v"></i>
            </td>
            <td class="title">
                <span class="title" onclick="processTitle(this)">{{ $chapter->title ? $chapter->title : 'Untitled' }}</span>
                @if($chapter->is_hidden)
                    <span class="hide-indicator">(Hidden)</span>
                @endif
            </td>
            <td class="shrink-to-fit">
                <span class="hint">{{ $chapter->word_count }} words</span>
            </td>
            <td class="shrink-to-fit">
                <a href="{{ route('learner.book-author-book-view-chapter',
                                    ['book_id' => $book->id, 'chapter_id' => $chapter->id]) }}" class="button right-space">
                    <i class="fa fa-search"></i>
                    <span>View</span>
                </a>

                <a class="button right-space toggle-hide">
                    @if ($chapter->is_hidden)
                        <i class="fa fa-unlock"></i>
                        <span>Unhide</span>
                    @else
                        <i class="fa fa-lock"></i>
                        <span>Hide</span>
                    @endif
                </a>

                <a href="{{ route('learner.book-author-book-update-chapter',
                                    ['book_id' => $book->id, 'chapter_id' => $chapter->id]) }}" class="button right-space">
                    <i class="fa fa-pencil"></i>
                    <span>Edit</span>
                </a>

                <a class="button color danger deleteChapterBtn" data-toggle="modal" data-target="#deleteChapterModal"
                   data-action="{{ route('learner.book-author-book-delete-chapter',
                                    ['book_id' => $book->id, 'chapter_id' => $chapter->id]) }}">
                    <i class="fa fa-trash"></i>
                    <span>Delete</span>
                </a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="actions" style="margin-top: 26px">
    <a href="{{ route('learner.book-author-book-create-chapter', ['id' => $book->id, 'type' => 1]) }}" class="beta-button">
        <i class="fa fa-plus"></i>
        <span>Add a Chapter</span>
    </a>

    <a href="{{ route('learner.book-author-book-create-chapter', ['id' => $book->id, 'type' => 2]) }}" class="beta-button">
        <i class="fa fa-plus"></i>
        <span>Add a Questionnaire</span>
    </a>

    <a href="{{--{{ route('learner.book-author-book-import', $book->id) }}--}}#bulkImportsChaptersModal"
       class="beta-button" data-toggle="modal">
        <i class="fa fa-download"></i>
        <span>Bulk import chapters</span>
    </a>
</div>