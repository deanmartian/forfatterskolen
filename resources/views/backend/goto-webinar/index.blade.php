@extends('backend.layout')

@section('title')
    <title>GoToWebinar &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-desktop"></i> GoToWebinar Email Notification</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <a class="btn btn-success margin-top" href="{{ route('admin.goto-webinar.create') }}">Create Email Notification</a>
        <a class="btn btn-primary margin-top" href="#templateModal" data-toggle="modal">Email Notification Template</a>

        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Webinar Key</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                @foreach($webinars as $webinar)
                    <tr>
                        <td>
                            {{ $webinar->title }}
                        </td>
                        <td>
                            {{ $webinar->gt_webinar_key }}
                        </td>
                        <td>
                            <a href="{{ route('admin.goto-webinar.edit', $webinar->id) }}" class="btn btn-info btn-xs">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <button class="btn btn-danger btn-xs deleteWebinarBtn"
                                    data-toggle="modal" data-target="#deleteWebinarModal"
                                    data-action="{{ route('admin.goto-webinar.destroy', $webinar->id) }}"
                            ><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="pull-right">
            {{ $webinars->render() }}
        </div>
        <div class="clearfix"></div>
    </div>

    <div id="templateModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="no-margin">Confirmation Email Template</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.settings.update.gt_confirmation_email') }}">
                        {{ csrf_field() }}
                        <textarea class="form-control editor" name="gt_confirmation_email">{{ App\Settings::gtWebinarEmailNotification() }}</textarea>
                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('backend.goto-webinar.partials.delete')
@stop

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
    <script>
        // tinymce
        let editor_config = {
            path_absolute: "{{ URL::to('/') }}",
            height: '20em',
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

        $(document).ready(function(){

            $(".deleteWebinarBtn").click(function(){
                let action        = $(this).data('action'),
                    modal           = $("#deleteWebinarModal"),
                    form          = modal.find('form');
                form.attr('action', action);
            });
        });
    </script>
@stop