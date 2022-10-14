@extends('backend.layout')

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
    <title>Project &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')

    <div id="app-container">
        <project-details :current-project="{{ json_encode($project) }}" :learners="{{ json_encode($learners) }}"
                         :time-registers="{{ json_encode($timeRegisters) }}"
                         :project-time-list="{{ json_encode($projectTimeRegisters) }}"></project-details>

        <div class="col-md-12">
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
                    @foreach($project->selfPublishingList as $publishing)
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
                        <input type="hidden" name="project_id" value="{{ $project->id }}">

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
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
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
@stop

@section('scripts')
    <script src="{{ mix('/js/app.js') }}"></script>
    <script>
        $(".addSelfPublishingBtn").click(function() {
            let modal = $("#selfPublishingModal");
            let form = modal.find('form');

            $("#add-files").addClass('hide');

            modal.find('.modal-title').text('Add Self Publishing');
            form.find('[name=_method]').remove();
            $("#learner-list").show();

            let action = $(this).data('action');
            form.attr('action', action);
            form.find('input[name=title]').val('');
            form.find('textarea[name=description]').val('');
            form.find('input[name=expected_finish]').val('');
            form.find('input[name=price]').val('');
            form.find('input[name=editor_share]').val('');
            form.find('select[name=editor_id]').val('').trigger('change');
        });

        $(".editSelfPublishingBtn").click(function() {
            let modal = $("#selfPublishingModal");
            let form = modal.find('form');
            let fields = $(this).data('fields');
            $("#add-files").removeClass('hide');

            modal.find('.modal-title').text('Edit Self Publishing');
            form.find('[name=_method]').remove();
            form.prepend("<input type='hidden' name='_method' value='PUT'>");
            $("#learner-list").hide();

            let action = $(this).data('action');
            form.attr('action', action);
            form.find('input[name=title]').val(fields.title);
            form.find('textarea[name=description]').val(fields.description);
            form.find('select[name=editor_id]').val(fields.editor_id).trigger('change');
            form.find('input[name=expected_finish]').val(fields.expected_finish);
            form.find('input[name=price]').val(fields.price);
            form.find('input[name=editor_share]').val(fields.editor_share);
        });

        $(".deleteSelfPublishingBtn").click(function() {
            let action = $(this).data('action');
            let modal = $("#deleteSelfPublishingModal");

            let form = modal.find('form');
            form.attr('action', action);
        });

        $(".selfPublishingFeedbackBtn").click(function(){
            let action = $(this).data('action');
            let modal = $('#selfPublishingFeedbackModal');
            modal.find('form').attr('action', action);
        });

        $(".viewFeedbackBtn").click(function(){
            let modal = $("#viewFeedbackModal");
            let fields = $(this).data('fields');
            modal.find("#manus-container").html(fields.feedback.file_link);
            modal.find("#notes-container").text(fields.feedback.notes);
        });
    </script>
    <script type="text/javascript" src="{{asset('select2/dist/js/select2.min.js')}}"></script>
@stop