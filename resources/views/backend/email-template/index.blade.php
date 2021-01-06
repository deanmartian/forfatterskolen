@extends('backend.layout')

@section('title')
    <title>Email Template &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file"></i> {{ trans('site.email-template') }}</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12 margin-top">
        <button class="btn btn-success addTemplateBtn" data-toggle="modal" data-target="#templateModal"
                data-action="{{ route('admin.manuscript.add_email_template') }}">
            Add Template
        </button>

        <div class="table-users table-responsive margin-top">
            <table class="table">
                <thead>
                    <tr>
                        <th>Identifier</th>
                        <th>{{ trans('site.subject') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templates as $template)
                        <tr>
                            <td>
                                {{ $template->page_name }}
                            </td>
                            <td>
                                {{ $template->subject }}
                            </td>
                            <td>
                                <button class="btn btn-primary btn-xs editTemplateBtn"
                                        data-toggle="modal"
                                        data-target="#templateModal"
                                        data-action="{{ route('admin.manuscript.edit_email_template', $template->id) }}"
                                        data-fields="{{ json_encode($template) }}"
                                >
                                    <i class="fa fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="templateModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.send-feedback') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label>
                                Identifier
                            </label>
                            <input type="text" name="page_name" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>
                                {{ trans('site.from') }}
                            </label>
                            <input type="email" name="from_email" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>
                                {{ trans('site.subject') }}
                            </label>
                            <input type="text" name="subject" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.body') }}</label>
                            <textarea name="email_content" cols="30" rows="10" class="form-control tinymce"></textarea>
                        </div>
                        <div class="clearfix"></div>
                        <button type="submit" class="btn btn-primary pull-right margin-top">
                            {{ trans('site.save') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        $(".addTemplateBtn").click(function() {
            let action = $(this).data('action');
            let modal = $('#templateModal');
            modal.find('form').attr('action', action);
            modal.find('form').find('[name=_method]').remove();

            modal.find('[name=page_name]').attr('disabled', false);
            modal.find('.form-control').val('');
            tinyMCE.activeEditor.setContent('');
        });

        $(".editTemplateBtn").click(function() {
            let action = $(this).data('action');
            let modal = $('#templateModal');
            let fields = $(this).data('fields');

            modal.find('form').prepend('<input type="hidden" name="_method" value="PUT">');

            modal.find('form').attr('action', action);
            modal.find('[name=page_name]').val(fields.page_name).attr('disabled', true);
            modal.find('[name=from_email]').val(fields.from_email);
            modal.find('[name=subject]').val(fields.subject);
            tinyMCE.activeEditor.setContent(fields.email_content);
        });
    </script>
@stop