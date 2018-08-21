@extends('backend.layout')

@section('title')
    <title>Opt-in &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file"></i>Opt-in Page</h3>
        <div class="clearfix"></div>
    </div>


    <div class="col-sm-12 margin-top">
        <div class="panel panel-default ">
            <div class="panel-heading">
                <button type="button" class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#editTermsModal"><i class="fa fa-pencil"></i></button>
                <h4>Terms</h4>
            </div>
            <div class="panel-body">
                {!! nl2br(App\Settings::optInTerms()) !!}
            </div>
        </div>
    </div>

    <div class="col-sm-12 margin-top">
        <div class="panel panel-default ">
            <div class="panel-heading">
                <button type="button" class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#editDescriptionModal"><i class="fa fa-pencil"></i></button>
                <h4>Description</h4>
            </div>
            <div class="panel-body">
                {!! nl2br(App\Settings::optInDescription()) !!}
            </div>
        </div>
    </div>


    <div id="editTermsModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Terms</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.settings.update.opt-in-terms') }}">
                        {{ csrf_field() }}
                        <textarea class="form-control ckeditor" name="opt_in_terms">{{ App\Settings::optInTerms() }}</textarea>
                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div id="editDescriptionModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Description</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.settings.update.opt-in-description') }}">
                        {{ csrf_field() }}
                        <textarea class="form-control ckeditor" name="opt_in_description">{{ App\Settings::optInDescription() }}</textarea>
                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
    <script>
        // tinymce
        let editor_config = {
            path_absolute: "{{ URL::to('/') }}",
            height: '25em',
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