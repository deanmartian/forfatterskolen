<form method="POST" action="@if(Request::is('poem/*/edit')){{route('admin.poem.update', $poem['id'])}}@else{{route('admin.poem.store')}}@endif"
      enctype="multipart/form-data" onsubmit="disableSubmit(this)">
    {{ csrf_field() }}
    @if(Request::is('poem/*/edit'))
        {{ method_field('PUT') }}
    @endif

    <div class="col-sm-12">
        @if(Request::is('poem/*/edit'))
            <h3>{{ trans('site.edit') }} <em>{{$poem['title']}}</em></h3>
        @else
            <h3>Add Poem</h3>
        @endif
    </div>

    <div class="col-sm-12 col-md-8">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label>{{ trans('site.title') }}</label>
                    <input type="text" class="form-control" name="title" value="{{ old('title', $poem['title']) }}" required>
                </div>
                <div class="form-group">
                    <label>Poem</label>
                    <textarea name="poem" cols="30" rows="10" class="form-control ckeditor">{{ old('poem', $poem['poem']) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-md-4">
        <div class="panel panel-default">
            <div class="panel-body">

                <div class="form-group">
                    <label>{{ trans('site.author-image') }}</label>
                    <input type="file" name="author_image" accept="image/*" class="form-control">
                </div>

                <div class="form-group">
                    <label>Author Name</label>
                    <input type="text" name="author" class="form-control" value="{{ old('author', $poem['author']) }}" required>
                </div>

                @if(Request::is('poem/*/edit'))
                    <button type="submit" class="btn btn-primary">Update Poem</button>
                    <button type="button" class="btn btn-danger deletePoemBtn" data-toggle="modal" data-target="#deletePoemModal"
                            data-action="{{ route('admin.poem.destroy', $poem['id']) }}">Delete Poem</button>
                @else
                    <button type="submit" class="btn btn-primary btn-block btn-lg">Create Poem</button>
                @endif
            </div>
        </div>
    </div>
</form>

@if(Request::is('poem/*/edit'))
    @include('backend.poem.partials.delete')
@endif

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
    <script>
        // tinymce
        let editor_config = {
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
            fontsize_formats: "8px 10px 12px 14px 16px 18px 20px 24px 36px 40px",
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

        $(".deletePoemBtn").click(function(){
            let action        = $(this).data('action'),
                modal           = $("#deletePoemModal"),
                form          = modal.find('form');
            form.attr('action', action);
        });
    </script>
@stop