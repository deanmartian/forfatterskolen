@extends('backend.layout')

@section('title')
<title>Edit Descriptions &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('content')
<div class="container padding-top">
    <div class="row">
        <form method="POST" action="{{ route('admin.sos-children.post-main-description') }}">
            {{ csrf_field() }}

            <div class="col-sm-12">
                <h3>{{ trans('site.edit-descriptions') }}</h3>
            </div>

            <div class="col-sm-12 col-md-8">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group">
                            <label>{{ trans('site.main-description') }}</label>
                            <textarea name="description" cols="30" rows="10" class="form-control ckeditor">{!! $hasMainDescription ? $hasMainDescription->description : '' !!}</textarea>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.bottom-description') }}</label>
                            <textarea name="bottom_description" cols="30" rows="10" class="form-control ckeditor">{{ $hasMainDescription ? $hasMainDescription->bottom_description : '' }}</textarea>
                        </div>

                        <input type="hidden" name="id" value="{{ $hasMainDescription ? $hasMainDescription->id : '' }}">
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-md-4">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <button type="submit" class="btn btn-primary btn-block btn-lg">{{ trans('site.save-details') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@stop

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
