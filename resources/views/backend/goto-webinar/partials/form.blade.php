<form method="POST" action="@if(Request::is('goto-webinar/*/edit')){{route('admin.goto-webinar.update', $webinar['id'])}}@else{{route('admin.goto-webinar.store')}}@endif">

    {{ csrf_field() }}
    @if(Request::is('goto-webinar/*/edit'))
        {{ method_field('PUT') }}
    @endif

    <div class="col-sm-12">
        @if(Request::is('goto-webinar/*/edit'))
            <h3>{{ trans('site.edit') }} <em>{{$webinar['title']}}</em></h3>
        @else
            <h3>GotoWebinar Create Email Notification</h3>
        @endif
    </div>

    <div class="col-sm-12 col-md-8">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label>{{ trans('site.title') }}</label>
                    <input type="text" class="form-control" name="title" value="{{ old('title', $webinar['title']) }}" required>
                </div>
                <div class="form-group">
                    <label>Webinar Key</label>
                    <input type="text" class="form-control" name="gt_webinar_key" value="{{ old('gt_webinar_key', $webinar['gt_webinar_key']) }}" required>
                </div>
                <div class="form-group">
                    <label>Confirmation Email</label>
                    <textarea name="confirmation_email" cols="30" rows="10" class="form-control editor">{{ old('confirmation_email', $webinar['confirmation_email']) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-md-4">
        <div class="panel panel-default">
            <div class="panel-body">
                @if(Request::is('goto-webinar/*/edit'))
                    <button type="submit" class="btn btn-primary">Update Webinar Notification</button> <br>
                    <button type="button" class="btn btn-danger margin-top" data-toggle="modal" data-target="#deleteWebinarModal">Delete Webinar Notification</button>
                @else
                    <button type="submit" class="btn btn-primary btn-block btn-lg">Create Webinar Notification</button>
                @endif
            </div>
        </div>
    </div>
</form>

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
    <script>
        // tinymce
        let editor_config = {
            path_absolute: "{{ URL::to('/') }}",
            height: '15em',
            selector: '.editor',
            plugins: ['advlist autolink lists link image charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code fullscreen',
                'insertdatetime media nonbreaking save table contextmenu directionality',
                'emoticons template paste textcolor colorpicker textpattern'],
            toolbar1: 'formatselect fontselect fontsizeselect | bold italic underline strikethrough subscript superscript | forecolor backcolor | link | alignleft aligncenter alignright ' +
            'alignjustify  | removeformat',
            toolbar2: 'undo redo | bullist numlist | outdent indent blockquote | link unlink anchor image media code | print fullscreen',
            relative_urls: false,
            file_browser_callback : function(field_name, url, type, win) {
                let x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
                let y = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;

                let cmsURL = editor_config.path_absolute + '/laravel-filemanager?field_name=' + field_name;
                if (type === 'image') {
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