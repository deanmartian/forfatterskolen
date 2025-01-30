@extends('backend.layout')

@section('title')
    <title>Publishing &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file"></i> Self Publishing</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12 margin-top">

        <button type="button" class="btn btn-success addSelfPublishingBtn" data-toggle="modal"
                data-target="#selfPublishingModal" data-action="{{ route('admin.self-publishing.store') }}">
            Add Self Publishing
        </button>

        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ trans('site.title') }}</th>
                    <th>{{ trans('site.description') }}</th>
                    <th>File</th>
                    <th>Editor</th>
                    <th>{{ trans('site.expected-finish') }}</th>
                    @if (Auth::user()->isSuperUser())
                        <th>Price</th>
                        <th>Editor Share</th>
                    @endif
                    <th>Feedback</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($publishingList as $publishing)
                    <tr>
                        <td>
                            {{ $publishing->title }}
                        </td>
                        <td>
                            {{ $publishing->description }}
                        </td>
                        <td>
                            {!! $publishing->file_link !!}
                        </td>
                        <td>
                            {{ $publishing->editor ? $publishing->editor->full_name : '' }}
                        </td>
                        <td>
                            {{ $publishing->expected_finish }}
                        </td>
                        @if (Auth::user()->isSuperUser())
                            <td>
                                {{ $publishing->price ? \App\Http\FrontendHelpers::currencyFormat($publishing->price) : '' }}
                            </td>
                            <td>
                                {{ $publishing->editor_share ? \App\Http\FrontendHelpers::currencyFormat($publishing->editor_share) : '' }}
                            </td>
                        @endif
                        <td>
                            @if(!$publishing->feedback)
                                <button class="btn btn-info btn-xs selfPublishingFeedbackBtn"
                                        data-target="#selfPublishingFeedbackModal"
                                        data-toggle="modal"
                                        data-action="{{ route('admin.self-publishing.add-feedback', $publishing->id) }}">
                                    + {{ trans('site.add-feedback') }}
                                </button>
                            @else
                                @if($publishing->feedback->is_approved)
                                    <button class="btn btn-primary btn-xs viewFeedbackBtn"
                                            data-target="#viewFeedbackModal"
                                            data-toggle="modal"
                                            data-fields="{{ json_encode($publishing) }}">
                                        View Feedback
                                    </button>

                                    <a href="{{ route('admin.self-publishing.download-feedback', $publishing->feedback->id) }}"
                                       class="btn btn-success btn-xs margin-top">
                                        Download Feedback
                                    </a>
                                @else
                                    <label class="label label-warning" style="margin-right: 5px;">
                                        Pending
                                    </label>
                                @endif
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.self-publishing.learners', $publishing->id) }}" class="btn btn-success btn-xs">
                                <i class="fa fa-user"></i>
                            </a>
                            @if ($publishing->status !== 'finished')
                                <button class="btn btn-warning btn-xs updatePublishingStatusBtn" type="button"
                                        data-toggle="modal" data-target="#updatePublishingStatusModal"
                                        data-status="finished"
                                        data-action="{{ route('admin.self-publishing.update-status', 
                                        ['id' => $publishing->id]) }}"><i class="fa fa-check"></i></button>
                            @endif
                            <button class="btn btn-primary btn-xs editSelfPublishingBtn" data-toggle="modal"
                                    data-target="#selfPublishingModal" data-fields="{{ json_encode($publishing) }}"
                                    data-action="{{ route('admin.self-publishing.update', $publishing->id) }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteSelfPublishingBtn" data-toggle="modal"
                                    data-target="#deleteSelfPublishingModal"
                                    data-action="{{ route('admin.self-publishing.destroy', $publishing->id) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="selfPublishingModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label>{{ trans('site.title') }}</label>
                            <input type="text" name="title" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.description') }}</label>
                            <textarea name="description"cols="30" rows="10" class="form-control"></textarea>
                        </div>

                        <div class="form-group">
                            <label>{{ trans_choice('site.manuscripts', 1) }}</label>
                            <input type="file" name="manuscript[]" class="form-control" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf,
					    application/vnd.oasis.opendocument.text" multiple>
                        </div>

                        <div class="form-group hide" id="add-files">
                            <label>Add Files</label>
                            <input type="file" name="add_files[]" class="form-control"
                                   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf,
					    application/vnd.oasis.opendocument.text" multiple>
                        </div>

                        <div class="form-group">
                            <label>{{ trans_choice('site.editors', 1) }}</label>
                            <select name="editor_id" class="form-control select2 template">
                                <option value="" selected="" disabled>- Select Editor -</option>
                                @foreach($editors as $editor)
                                    <option value="{{ $editor->id }}">
                                        {{$editor->full_name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" id="learner-list">
                            <label>
                                {{ trans_choice('site.learners', 2) }}
                            </label>
                            <select name="learners[]" class="form-control select2 template" multiple="multiple">
                                @foreach($learners as $learner)
                                    <option value="{{$learner->id}}">
                                        {{$learner->full_name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>
                                {{ trans('site.expected-finish') }}
                            </label>
                            <input type="date" class="form-control" name="expected_finish">
                        </div>

                        <div class="form-group">
                            <label>
                                Project
                            </label>
                            <select name="project_id" class="form-control select2">
                                <option value="" selected disabled> - Select Project - </option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if (Auth::user()->isSuperUser())
                            <div class="form-group">
                                <label>Price</label>
                                <input type="number" name="price" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Editor Share</label>
                                <input type="number" name="editor_share" class="form-control">
                            </div>
                        @endif

                        <button type="submit" class="btn btn-primary pull-right">{{ trans('site.save') }}</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div id="selfPublishingFeedbackModal" class="modal fade" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        Add Feedback
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>{{ trans_choice('site.manuscripts', 1) }}</label>
                            <input type="file" name="manuscript[]" class="form-control"
                                   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf,
					    application/vnd.oasis.opendocument.text" multiple>
                        </div>

                        <div class="form-group">
                            <label>{{ trans_choice('site.notes', 1) }}</label>
                            <textarea name="notes" cols="30" rows="10" class="form-control"></textarea>
                        </div>
                        <div class="text-right">
                            <button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div id="viewFeedbackModal" class="modal fade" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ trans_choice('site.manuscripts', 1) }}</label>
                        <div id="manus-container"></div>
                    </div>

                    <div class="form-group">
                        <label>{{ trans_choice('site.notes', 1) }}</label>
                        <div id="notes-container" style="white-space: pre;max-height: 500px;overflow: auto;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteSelfPublishingModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.delete') }} <em></em></h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        Are you sure you want to delete this record?
                        <div class="text-right margin-top">
                            <button class="btn btn-danger" type="submit">{{ trans('site.delete') }}</button>
                        </div>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="updatePublishingStatusModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        Update Status
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <input type="hidden" name="status">
                        <p>
                            {{ trans('site.update-service-status-question') }}
                        </p>
                        <div class="text-right">
                            <button class="btn btn-primary" type="submit">{{ trans('site.submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        let modal = $("#selfPublishingModal");
        $(".addSelfPublishingBtn").click(function() {
            let form = modal.find('form');

            $("#add-files").addClass('hide');

            modal.find('.modal-title').text('Add Self Publishing');
            form.find('[name=_method]').remove();
            $("#learner-list").show();

            var action = $(this).data('action');
            form.attr('action', action);
            form.find('input[name=title]').val('');
            form.find('textarea[name=description]').val('');
            form.find('input[name=expected_finish]').val('');
            form.find('input[name=price]').val('');
            form.find('input[name=editor_share]').val('');
            form.find('select[name=editor_id]').val('').trigger('change');
        });

        $(".updatePublishingStatusBtn").click(function(){
            let action = $(this).data('action');
            let modal = $('#updatePublishingStatusModal');
            let status = $(this).data('status');
            modal.find('form').attr('action', action);
            modal.find('[name=status]').val(status);
        });

        $(".editSelfPublishingBtn").click(function() {
            let form = modal.find('form');
            var fields = $(this).data('fields');
            $("#add-files").removeClass('hide');

            modal.find('.modal-title').text('Edit Self Publishing');
            form.find('[name=_method]').remove();
            form.prepend("<input type='hidden' name='_method' value='PUT'>");
            $("#learner-list").hide();

            var action = $(this).data('action');
            form.attr('action', action);
            form.find('input[name=title]').val(fields.title);
            form.find('textarea[name=description]').val(fields.description);
            form.find('select[name=editor_id]').val(fields.editor_id).trigger('change');
            form.find('input[name=expected_finish]').val(fields.expected_finish);
            form.find('select[name=project_id]').val(fields.project_id).trigger('change');
            form.find('input[name=price]').val(fields.price);
            form.find('input[name=editor_share]').val(fields.editor_share);
        });

        $(".selfPublishingFeedbackBtn").click(function(){
            let action = $(this).data('action');
            let modal = $('#selfPublishingFeedbackModal');
            modal.find('form').attr('action', action);
        });

        $(".viewFeedbackBtn").click(function(){
            let modal = $("#viewFeedbackModal");
            let fields = $(this).data('fields');
            console.log(fields);
            modal.find("#manus-container").html(fields.feedback.file_link);
            modal.find("#notes-container").text(fields.feedback.notes);
        });

        $(".deleteSelfPublishingBtn").click(function() {
            var action = $(this).data('action');
            let modal = $("#deleteSelfPublishingModal");

            let form = modal.find('form');
            form.attr('action', action);
        })
    </script>
@stop