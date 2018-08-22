@extends('frontend.learner.pilot-reader.private-groups.layout')

@section('private-content')
    <div class="global-card">
        <div class="card-body">
            <h1 class="font-weight-light mb-0">Book List</h1>
            <small class="text-muted d-block">This page lists all the books shared with the group and currently available to read.</small>
            @if($manager)
                <div class="jumbotron jumbotron-fluid mt-2 mb-0">
                    <strong>How "Book Visibility" works:</strong>
                    <ul class="mb-0 pl-3rem">
                        <li><strong>Featured</strong> - Setting books to "Featured" displays them on the group home page.</li>
                        <li><strong>Available</strong> - This is the default setting, books only show up on the book list.</li>
                        <li><strong>Hidden</strong> - Setting books to "Hidden" means that only managers can see them.</li>
                    </ul>
                </div> <!-- end jumbotron jumbotron-fluid mt-2 mb-0 -->
            @endif

            <div class="row mt-3">
                <div class="col-md-4 pull-right add-book-div">
                    <div class="form-group">
                        <div class="input-group-global">
                            <?php
                            $books = \App\PilotReaderBook::where('user_id', Auth::user()->id)
                                ->whereNotIn('id', function($query) use($privateGroup){
                                    $query->select('book_id')
                                        ->from('private_group_shared_books')
                                        ->where('private_group_id', $privateGroup->id);
                                })
                                ->whereNotIn('id', function($query){
                                    $query->select('book_id')
                                        ->from('pilot_reader_book_settings')
                                        ->where('is_deactivated', 1);
                                })->get(['id', 'title']);
                            ?>
                            @if($manager)
                                <select class="form-control" id="add_a_book_select" name="book_id">
                                    <option value="">--Select a Book--</option>
                                    @foreach($books as $book)
                                        <?php
                                            $book_settings = $book->settings;
                                            $showBook = 1;
                                            if ($book_settings && $book_settings->is_deactivated) {
                                                $showBook = 0;
                                            }
                                        ?>
                                        @if ($showBook)
                                            <option value="{{ $book->id }}">{{ $book->title }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-info border-color-grey" type="button"
                                            onclick="methods.shareBook()">Add</button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div> <!-- end row mt-3 -->

            <table id="books-table" class="table table-striped table-bordered mt-3 font-14-body dataTable-right-search" width="100%">
                <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Shared On</th>
                    <th class="manager-th">Visibility</th>
                    <th class="manager-th">Action</th>
                </tr>
                </thead>
            </table>

        </div> <!-- end card-body -->
    </div> <!-- end global-card -->
@stop