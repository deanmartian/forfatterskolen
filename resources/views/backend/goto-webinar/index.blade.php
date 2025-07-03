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
        <a class="btn btn-primary margin-top" href="#reminderModal" data-toggle="modal">Reminder Email Template</a>

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
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="no-margin">Confirmation Email Template</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.settings.update.gt_confirmation_email') }}"
                        onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <textarea class="form-control tinymce" name="gt_confirmation_email">{{ App\Settings::gtWebinarEmailNotification() }}</textarea>
                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="reminderModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="no-margin">Reminder Email Template</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.settings.update.gt_reminder_email_template') }}"
                        onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <textarea class="form-control tinymce" name="gt_reminder_email_template">{{ App\Settings::gtReminderEmailTemplate() }}</textarea>
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
    <script>
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