<form method="POST" action="@if(Request::is('publisher-book/*/edit')){{route('admin.publisher-book.update', $book['id'])}}@else{{route('admin.publisher-book.store')}}@endif"
enctype="multipart/form-data">
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

    <div class="col-sm-12 col-md-8">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label>{{ trans('site.title') }}</label>
                    <input type="text" class="form-control" name="title" value="{{ old('title', $book['title']) }}" required>
                </div>
                <div class="form-group">
                    <label>{{ trans('site.description') }}</label>
                    <textarea name="description" cols="30" rows="10" class="form-control ckeditor">{{ old('description', $book['description']) }}</textarea>
                </div>
                <div class="form-group">
                    <label>{{ trans('site.quote-description') }}</label>
                    <textarea name="quote_description" cols="30" rows="10" class="form-control ckeditor">{{ old('quote_description', $book['quote_description']) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-md-4">
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

                <div class="form-group">
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
                </div>

                <div class="form-group">
                    <label>{{ trans('site.display-order') }}</label>
                    <input type="number" step="1" name="display_order" value="{{ old('display_order', $book['display_order']) }}"
                    class="form-control">
                </div>

                @if(Request::is('publisher-book/*/edit'))
                    <button type="submit" class="btn btn-primary">{{ trans('site.update-publisher-book') }}</button> <br>
                    <button type="button" class="btn btn-danger margin-top" data-toggle="modal" data-target="#deleteBlogModal">{{ trans('site.delete-publisher-book') }}</button>
                @else
                    <button type="submit" class="btn btn-primary btn-block btn-lg">{{ trans('site.create-publisher-book') }}</button>
                @endif
            </div>
        </div>
    </div>
</form>

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
    <script>
        // tinymce
        var editor_config = {
            path_absolute: "{{ URL::to('/') }}",
            height: '15em',
            selector: '.ckeditor',
            plugins: ['advlist autolink lists link image charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code fullscreen',
                'insertdatetime media nonbreaking save table contextmenu directionality',
                'emoticons template paste textcolor colorpicker textpattern'],
            toolbar1: 'formatselect fontselect fontsizeselect | bold italic underline strikethrough subscript superscript | forecolor backcolor | link | alignleft aligncenter alignright ' +
            'alignjustify  | removeformat',
            toolbar2: 'undo redo | bullist numlist | outdent indent blockquote | link unlink anchor image media code | print fullscreen',
            relative_urls: false,
            file_browser_callback : function(field_name, url, type, win) {
                var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
                var y = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;

                var cmsURL = editor_config.path_absolute + '/laravel-filemanager?field_name=' + field_name;
                if (type == 'image') {
                    cmsURL = cmsURL + '&type=Images';
                } else {
                    cmsURL = cmsURL + '&type=Files';
                }

                tinyMCE.activeEditor.windowManager.open({
                    file : cmsURL,
                    title : 'Filemanager',
                    width : x * 0.8,
                    height : y * 0.8,
                    resizable : 'yes',
                    close_previous : 'no'
                });
            }
        };
        tinymce.init(editor_config);
    </script>
@stop