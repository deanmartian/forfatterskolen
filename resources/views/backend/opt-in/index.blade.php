@extends('backend.layout')

@section('title')
    <title>Opt-in &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file"></i>{{ trans('site.opt-in-page') }}</h3>
        <div class="clearfix"></div>
    </div>


    <div class="col-sm-12 margin-top">

        <a class="btn btn-success margin-top" href="#optInModal" data-toggle="modal"
        id="addOptInBtn" data-action="{{ route('admin.opt-in.store') }}">{{ trans('site.add-opt-in') }}</a>

        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ trans('site.id') }}</th>
                    <th>{{ trans('site.name') }}</th>
                    <th>{{ trans('site.slug') }}</th>
                    <th>{{ trans('site.list-id') }}</th>
                    <th width="500">{{ trans('site.description') }}</th>
                    <th width="100"></th>
                </tr>
                </thead>

                <tbody>
                    @foreach($optInList as $optIn)
                        <tr>
                            <td>{{ $optIn->id }}</td>
                            <td>{{ $optIn->name }}</td>
                            <td>{{ $optIn->slug }}</td>
                            <td>{{ $optIn->list_id }}</td>
                            <td>{{ str_limit(strip_tags($optIn->description), 120) }}</td>
                            <td>
                                <button class="btn btn-info btn-xs editOptInBtn"
                                data-toggle="modal" data-target="#optInModal"
                                data-action="{{ route('admin.opt-in.update', $optIn->id) }}"
                                data-fields="{{ json_encode($optIn) }}">
                                    <i class="fa fa-pencil"></i>
                                </button>

                                <button class="btn btn-danger btn-xs deleteOptInBtn"
                                data-action="{{ route('admin.opt-in.destroy', $optIn->id) }}"
                                data-toggle="modal" data-target="#deleteOptInModal">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pull-right">
            {{ $optInList->render() }}
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="col-sm-12 margin-top">
        <div class="panel panel-default ">
            <div class="panel-heading">
                <button type="button" class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#editTermsModal"><i class="fa fa-pencil"></i></button>
                <h4>{{ trans('site.terms') }}</h4>
            </div>
            <div class="panel-body">
                {!! nl2br(App\Settings::optInTerms()) !!}
            </div>
        </div>
    </div>

    <div class="col-sm-12 margin-top" style="display: none">
        <div class="panel panel-default">
            <div class="panel-heading">
                <button type="button" class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#editDescriptionModal"><i class="fa fa-pencil"></i></button>
                <h4>{{ trans('site.description') }}</h4>
            </div>
            <div class="panel-body">
                {!! nl2br(App\Settings::optInDescription()) !!}
            </div>
        </div>
    </div>

    <div class="col-sm-12 margin-top" style="display: none">
        <div class="panel panel-default ">
            <div class="panel-heading">
                <button type="button" class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#editDescriptionRektorModal"><i class="fa fa-pencil"></i></button>
                <h4>{{ trans('site.description-for-rektor-tips') }}</h4>
            </div>
            <div class="panel-body">
                {!! nl2br(App\Settings::optInRektorDescription()) !!}
            </div>
        </div>
    </div>


    <div id="editTermsModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.terms') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.settings.update.opt-in-terms') }}">
                        {{ csrf_field() }}
                        <textarea class="form-control ckeditor" name="opt_in_terms">{{ App\Settings::optInTerms() }}</textarea>
                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-primary">{{ trans('site.save') }}</button>
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
                    <h4 class="modal-title">{{ trans('site.description') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.settings.update.opt-in-description') }}">
                        {{ csrf_field() }}
                        <textarea class="form-control ckeditor" name="opt_in_description">{{ App\Settings::optInDescription() }}</textarea>
                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-primary">{{ trans('site.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div id="editDescriptionRektorModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.description-for-rektor-tips') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.settings.update.opt-in-rektor-description') }}">
                        {{ csrf_field() }}
                        <textarea class="form-control ckeditor" name="opt_in_description">{{ App\Settings::optInRektorDescription() }}</textarea>
                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-primary">{{ trans('site.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div id="optInModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" id="optInForm">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label>{{ trans('site.name') }}</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.slug') }}</label>
                            <input type="hidden" name="slug">
                            <input type="text" class="form-control" name="slug" readonly>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.list-id') }}</label>
                            <input type="number" class="form-control" name="list_id" required min="1">
                        </div>

                        <div class="form-group">
                            <label> {{ trans('site.description') }} </label>
                            <textarea class="form-control ckeditor" name="description"></textarea>
                        </div>
                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-primary">{{ trans('site.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteOptInModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.delete-opt-in') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}

                        <p>
                            {{ trans('site.delete-opt-in-question') }}
                        </p>

                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-danger">{{ trans('site.delete') }}</button>
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

        let optInModal = $("#optInModal");
        let translations = {
            add_opt_in: "{{ trans('site.add-opt-in') }}",
            edit_opt_in: "{{ trans('site.edit') }}"
        };

        let slugify = function(str) {
            let trimmed = $.trim(str);
            let slug = trimmed.replace(/[^a-z0-9-]/gi, '-').
            replace(/-+/g, '-').
            replace(/^-|-$/g, '');
            return slug.toLowerCase();
        };

        optInModal.find("[name=name]").keyup(function(){
           optInModal.find("[name=slug]").val(slugify(this.value));
        });

        $("#addOptInBtn").click(function(){
            emptyContent("#optInForm");
            let action = $(this).data('action');
            optInModal.find('form').attr('action', action);
            optInModal.find('.modal-title').text(translations.add_opt_in);
        });

        $(".editOptInBtn").click(function(){
            let fields = $(this).data('fields');
            let action = $(this).data('action');
            optInModal.find('form').attr('action', action);
            optInModal.find('form').find('[name=_method]').remove();
            optInModal.find('form').prepend('{{ method_field('PUT') }}');
            optInModal.find('.modal-title').empty().append(translations.edit_opt_in +' <em>'+fields.name+'</em>');
            $.each(fields, function(k, v){
                optInModal.find("[name="+k+"]").val(v);
                if (k === 'description') {
                    tinyMCE.activeEditor.setContent(v);
                }
            });
        });

        $(".deleteOptInBtn").click(function(){
            let action = $(this).data('action');
            let deleteModal = $("#deleteOptInModal");
            deleteModal.find('form').attr('action', action);
        });

        function emptyContent(sel) {
            tinyMCE.activeEditor.setContent('');
            $(sel).find('.form-control').each(function(){
                $(this).val('')
            });
        }
    </script>
@stop