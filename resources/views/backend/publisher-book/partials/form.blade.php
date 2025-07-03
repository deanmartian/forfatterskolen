<form method="POST" action="@if(Request::is('publisher-book/*/edit')){{route('admin.publisher-book.update', $book['id'])}}@else{{route('admin.publisher-book.store')}}@endif"
enctype="multipart/form-data" onsubmit="disableSubmit(this)">
    {{ csrf_field() }}
    @if(Request::is('publisher-book/*/edit'))
        {{ method_field('PUT') }}
    @endif

    <div class="col-sm-12">
        @if(Request::is('publisher-book/*/edit'))
            <h3>{{ trans('site.edit') }} <em>{{$book['title']}}</em></h3>
        @else
            <h3>{{ trans('site.add-new-publisher-book') }}</h3>
        @endif
    </div>

    <div class="col-sm-12 col-md-7">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label>{{ trans('site.title') }}</label>
                    <input type="text" class="form-control" name="title" value="{{ old('title', $book['title']) }}" required>
                </div>
                <div class="form-group">
                    <label>{{ trans('site.description') }}</label>
                    <textarea name="description" cols="30" rows="10" class="form-control tinymce">{{ old('description', $book['description']) }}</textarea>
                </div>
                <div class="form-group">
                    <label>{{ trans('site.quote-description') }}</label>
                    <textarea name="quote_description" cols="30" rows="10" class="form-control tinymce">{{ old('quote_description', $book['quote_description']) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-md-5">
        <div class="panel panel-default">
            <div class="panel-body">

                <div class="form-group">
                    <label>{{ trans('site.author-image') }}</label>
                    <input type="file" name="author_image" accept="image/*" class="form-control" @if(!Request::is('publisher-book/*/edit')) required @endif>
                    <p class="text-center">
                        <small class="text-muted">146*105</small>
                        <br>
                        <small class="text-muted">
                            <a href="{{ asset($book['author_image']) }}" target="_blank">
                                {{ \App\Http\AdminHelpers::extractFileName($book['author_image']) }}
                            </a>
                        </small>
                    </p>
                </div>

                {{--<div class="form-group">
                    <label>{{ trans('site.book-image') }}</label>
                    <input type="file" name="book_image" accept="image/*" class="form-control" @if(!Request::is('publisher-book/*/edit')) required @endif>
                    <p class="text-center">
                        <small class="text-muted">146*105</small>
                        <br>
                        <small class="text-muted">
                            <a href="{{ asset($book['book_image']) }}" target="_blank">
                                {{ \App\Http\AdminHelpers::extractFileName($book['book_image']) }}
                            </a>
                        </small>
                    </p>
                </div>

                <div class="form-group">
                    <label>{{ trans('site.book-image-link') }}</label>
                    <input type="url" name="book_image_link" value="{{ old('book_image_link', $book['book_image_link']) }}" class="form-control">
                </div>--}}

                <div class="form-group">
                    <label>{{ trans('site.display-order') }}</label>
                    <input type="number" step="1" name="display_order" value="{{ old('display_order', $book['display_order']) }}"
                    class="form-control">
                </div>

                @if(Request::is('publisher-book/*/edit'))
                    <button type="submit" class="btn btn-primary">{{ trans('site.update-publisher-book') }}</button> <br>
                    <button type="button" class="btn btn-danger margin-top" data-toggle="modal" 
                    data-target="#deletePublisherBookModal">{{ trans('site.delete-publisher-book') }}</button>
                @else
                    <button type="submit" class="btn btn-primary btn-block btn-lg">{{ trans('site.create-publisher-book') }}</button>
                @endif
            </div> <!-- end panel-body -->
        </div> <!-- end panel-default -->

        @if(Request::is('publisher-book/*/edit'))
            <div class="panel panel-default">
                <div class="panel-body" style="overflow: auto">
                    <b>Book Image list</b>
                    <button type="button" class="btn btn-success btn-sm pull-right margin-bottom addBookLibraryModal"
                            data-toggle="modal"
                            data-target="#bookLibraryModal"
                            data-action="{{ route('publisher-book-library.store', $book['id']) }}">
                        Add
                    </button>
                    <table class="table w-100">
                        <thead>
                            <tr>
                                <th>Image/Link</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($book->libraries as $library)
                                <tr>
                                    <td>
                                        <a href="{{ asset($library->book_image) }}" target="_blank">
                                            {{ \App\Http\AdminHelpers::extractFileName($library->book_image) }}
                                        </a> <br>

                                        <a href="{{ asset($library->book_link) }}" target="_blank">
                                            {!! $library->book_link !!}
                                        </a>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-xs btn-primary editBookLibraryBtn" data-toggle="modal"
                                                data-target="#bookLibraryModal"
                                                data-action="{{ route('publisher-book-library.update', $library->id) }}"
                                                data-library="{{ json_encode($library) }}">
                                            <i class="fa fa-edit"></i>
                                        </button>

                                        <button type="button" class="btn btn-xs btn-danger deleteBookBtn" data-toggle="modal"
                                                data-target="#deleteBookModal"
                                                data-action="{{ route('publisher-book-library.delete', $library->id) }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div> <!-- end col-sm-12 col-md-4-->
</form>

<div id="bookLibraryModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data" action="" onsubmit="disableSubmit(this)">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                    <div class="form-group">
                        <label>{{ trans('site.book-image') }}</label>
                        <input type="file" name="book_image" accept="image/*" class="form-control"  required>
                        <p class="text-center">
                            <small class="text-muted">146*105</small>
                            <br>
                            <small class="text-muted">
                                <a href="" target="_blank">
                                </a>
                            </small>
                        </p>
                    </div>

                    <div class="form-group">
                        <label>{{ trans('site.book-image-link') }}</label>
                        <input type="url" name="book_link" value="" class="form-control">
                    </div>
                    <div class="text-right margin-top">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="deleteBookModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Delete</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="" onsubmit="disableSubmit(this)">
                    {{ csrf_field() }}
                    {{method_field('DELETE')}}
                    Are you sure to delete this record?
                    <div class="text-right margin-top">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
    <script>
        let modal = $("#bookLibraryModal");
        let form = modal.find('form');

        $(".editBookLibraryBtn").click(function() {
            let library = $(this).data('library');
            modal.find(".modal-title").text("Edit Book");
            form.append("<input type='hidden' name='_method' value='PUT'>")
            form.attr('action', $(this).data('action'));
            form.find('.text-muted a').text(library.book_image_name).attr('href', library.book_image);
            form.find('[name=book_link]').val(library.book_link);
            form.find(".text-muted a").show();
        });

        $(".addBookLibraryModal").click(function() {
            modal.find(".modal-title").text("Add Book");
            form.find("[name=_method]").remove();
            form.attr('action', $(this).data('action'));
            form.find(".text-muted a").hide();
        });

        $(".deleteBookBtn").click(function() {
            let modal = $("#deleteBookModal");
            let form = modal.find('form');
            form.attr('action', $(this).data('action'));
        });
    </script>
@stop