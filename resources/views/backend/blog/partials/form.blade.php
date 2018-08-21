<form method="POST" action="@if(Request::is('blog/*/edit')){{route('admin.blog.update', $blog['id'])}}@else{{route('admin.blog.store')}}@endif"
      enctype="multipart/form-data">
    {{ csrf_field() }}
    @if(Request::is('blog/*/edit'))
        {{ method_field('PUT') }}
    @endif

    <div class="col-sm-12">
        @if(Request::is('blog/*/edit'))
            <h3>Edit <em>{{$blog['title']}}</em></h3>
        @else
            <h3>Add New Blog</h3>
        @endif
    </div>

    <div class="col-sm-12 col-md-8">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" class="form-control" name="title" value="{{ $blog['title'] }}" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" cols="30" rows="10" class="form-control ckeditor">{{ $blog['description'] }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-md-4">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label>Image</label>
                    <input type="file" name="image" class="form-control"
                           accept="image/*" @if(!Request::is('blog/*/edit')) required @endif>
                </div>

                <div class="form-group">
                    <label>Author Name</label>
                    <input type="text" name="author_name" class="form-control"
                    value="{{ $blog['author_name'] }}">
                </div>

                <div class="form-group">
                    <label>Author Image</label>
                    <input type="file" name="author_image" class="form-control"
                           accept="image/*">
                </div>

                @if(Request::is('blog/*/edit'))
                    <button type="submit" class="btn btn-primary">Update Blog</button>
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteBlogModal">Delete Blog</button>
                @else
                    <button type="submit" class="btn btn-primary btn-block btn-lg">Create Blog</button>
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