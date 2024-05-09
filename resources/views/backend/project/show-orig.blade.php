@extends($layout)

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
    <title>Project &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')

    <div id="app-container">
        <project-details :current-project="{{ json_encode($project) }}" :learners="{{ json_encode($learners) }}"
                         :activities="{{ json_encode($activities) }}" :time-registers="{{ json_encode($timeRegisters) }}"
                         :project-time-list="{{ json_encode($projectTimeRegisters) }}" :projects="{{ json_encode($projects) }}"
                         :whole-book-list="{{ json_encode($wholeBooks) }}" :editor-and-admin-list="{{ json_encode($editorAndAdminList) }}"
                         :task-list="{{ json_encode($tasks) }}" :book-critique-list="{{ json_encode($bookCritiques) }}"></project-details>

        <div class="col-md-12">
            <button type="button" class="btn btn-success addSelfPublishingBtn" data-toggle="modal"
                    data-target="#selfPublishingModal" data-action="{{ route($selfPublishingStoreRoute) }}">
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
                                {!! $publishing->file_link_with_download !!}
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
                                            data-action="{{ route($selfPublishingAddFeedbackRoute, $publishing->id) }}">
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

                                        <a href="{{ route($selfPublishingDownloadFeedbackRoute, $publishing->feedback->id) }}"
                                           class="btn btn-success btn-xs">
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
                                <a href="{{ route($selfPublishingLearnersRoute, $publishing->id) }}" class="btn btn-success btn-xs">
                                    <i class="fa fa-user"></i>
                                </a>
                                <button class="btn btn-primary btn-xs editSelfPublishingBtn" data-toggle="modal"
                                        data-target="#selfPublishingModal" data-fields="{{ json_encode($publishing) }}"
                                        data-action="{{ route($selfPublishingUpdateRoute, $publishing->id) }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-xs deleteSelfPublishingBtn" data-toggle="modal"
                                        data-target="#deleteSelfPublishingModal"
                                        data-action="{{ route($selfPublishingDeleteRoute, $publishing->id) }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div> <!-- end self publishing -->

        @if($project->user_id)

            <!-- copy editing -->
            <div class="col-md-12 margin-top">
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addOtherServiceModal"
                        onclick="updateOtherServiceFields(1, this)" data-editors="{{ json_encode($copyEditingEditors) }}">
                        + {{ trans('site.add-copy-editing') }}
                    </button>
                <div class="table-users table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>{{ trans_choice('site.manus', 2) }}</th>
                            <th>{{ trans_choice('site.editors', 1) }}</th>
                            <th>{{ trans('site.date-ordered') }}</th>
                            <th>{{ trans('site.expected-finish') }}</th>
                            <th>{{ trans_choice('site.feedbacks', 1) }}</th>
                            <th>{{ trans('site.status') }}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($project->copyEditings as $copy_editing)
                            <?php $extension = explode('.', basename($copy_editing->file)); ?>
                            <tr>
                                <td>
                                    @if ($copy_editing->file)
                                        <a href="{{ route($downloadOtherService, ['id' => $copy_editing->id, 'type' => 1]) }}"
                                            download>
                                            <i class="fa fa-download" aria-hidden="true"></i>
                                        </a>&nbsp;
                                        @if( end($extension) == 'pdf' || end($extension) == 'odt' )
                                            <a href="/js/ViewerJS/#../../{{ $copy_editing->file }}">{{ basename($copy_editing->file) }}</a>
                                        @elseif( end($extension) == 'docx' )
                                            <a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$copy_editing->file}}">{{ basename($copy_editing->file) }}</a>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if ($copy_editing->editor_id)
                                        {{ $copy_editing->editor->full_name }} <br>

                                        <button class="btn btn-xs btn-primary assignEditorBtn" data-toggle="modal"
                                                data-target="#assignEditorModal"
                                                data-editor="{{ json_encode($copy_editing->editor) }}"
                                                data-action="{{ route($assignEditorRoute, ['id' => $copy_editing->id, 'type' => 1]) }}">
                                            {{ trans('site.assign-editor') }}
                                        </button>
                                    @else
                                        <button class="btn btn-xs btn-warning assignEditorBtn" data-toggle="modal"
                                                data-target="#assignEditorModal"
                                                data-action="{{ route($assignEditorRoute, ['id' => $copy_editing->id, 'type' => 1]) }}">
                                            {{ trans('site.assign-editor') }}
                                        </button>
                                    @endif
                                </td>
                                <td>
                                    {{ \App\Http\FrontendHelpers::formatDate($copy_editing->created_at) }}
                                </td>
                                <td>
                                    @if ($copy_editing->expected_finish)
                                        {{ $copy_editing->expected_finish_formatted }}
                                        <br>
                                    @endif

                                    @if ($copy_editing->status !== 2)
                                        <a href="#setOtherServiceFinishDateModal" data-toggle="modal"
                                           class="setOtherServiceFinishDateBtn"
                                           data-action="{{ route($updateExpectedFinishRoute,
                                               ['id' => $copy_editing->id, 'type' => 1]) }}"
                                           data-finish="{{ $copy_editing->expected_finish ?
                                            strftime('%Y-%m-%d', strtotime($copy_editing->expected_finish)) : '' }}">
                                            {{ trans('site.set-date') }}
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    <!-- show only if no feedback is given yet for this copyEditing -->
                                    @if (!$copy_editing->feedback)
                                        <a href="#addOtherServiceFeedbackModal" data-toggle="modal" style="color:#dc3545"
                                           class="addOtherServiceFeedbackBtn" data-service="1"
                                           data-action="{{ route($otherServiceFeedbackRoute,
                                                        ['id' => $copy_editing->id, 'type' => 1]) }}"
                                           data-email-template="{{ json_encode($copyEditingFeedbackTemplate) }}">+ {{ trans('site.add-feedback') }}</a>
                                    @else
                                        <?php $files = explode(',',$copy_editing->feedback->manuscript); ?>
                                        @foreach($files as $file)
                                            <a href="{{ $file }}" download><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp;
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    @if( $copy_editing->status == 2 )
                                        <span class="label label-success">Finished</span>
                                    @elseif( $copy_editing->status == 1 )
                                        <span class="label label-primary">Started</span>
                                    @elseif( $copy_editing->status == 0 )
                                        <span class="label label-warning">Not started</span>
                                    @endif
                                </td>
                                <td>
                                    <?php
                                    $btnColor = $copy_editing->status == 1 ? 'primary' : 'warning';
                                    ?>

                                        <input type="checkbox" data-toggle="toggle" data-on="Locked"
                                               class="lock-toggle" data-off="Unlocked"
                                               data-type="copy-editing" onchange="lockToggle(this)"
                                               data-id="{{$copy_editing->id}}" data-size="mini" @if($copy_editing->is_locked)
                                            {{ 'checked' }}
                                                @endif>

                                    @if ($copy_editing->status !== 2)
                                        <button class="btn btn-{{ $btnColor }} btn-xs updateOtherServiceStatusBtn" type="button"
                                                data-toggle="modal" data-target="#updateOtherServiceStatusModal"
                                                data-service="1"
                                                data-action="{{ route($updateStatusRoute, ['id' => $copy_editing->id, 'type' => 1]) }}">
                                            <i class="fa fa-check"></i>
                                        </button>
                                    @endif

                                    <button class="btn btn-danger btn-xs deleteOtherServiceBtn" type="button"
                                            data-toggle="modal" data-target="#deleteOtherServiceModal"
                                            data-action="{{ route($otherServiceDeleteRoute, ['id' => $copy_editing->id, 'type' => 1]) }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div> <!-- end copy editing -->

            <!-- correction -->
            <div class="col-md-12 margin-top">
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addOtherServiceModal"
                        onclick="updateOtherServiceFields(0, this)" data-editors="{{ json_encode($correctionEditors) }}">
                        + {{ trans('site.add-correction') }}
                    </button>
                <div class="table-users table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>{{ trans_choice('site.manus', 2) }}</th>
                            <th>{{ trans_choice('site.editors', 1) }}</th>
                            <th>{{ trans('site.date-ordered') }}</th>
                            <th>{{ trans('site.expected-finish') }}</th>
                            <th>{{ trans_choice('site.feedbacks', 1) }}</th>
                            <th>{{ trans('site.status') }}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($project->corrections as $correction)
                            <?php $extension = explode('.', basename($correction->file)); ?>
                            <tr>
                                <td>
                                    @if ($correction->file)
                                        <a href="{{ route($downloadOtherService, ['id' => $correction->id, 'type' => 2]) }}" download>
                                            <i class="fa fa-download" aria-hidden="true"></i>
                                        </a>&nbsp;
                                        @if( end($extension) == 'pdf' || end($extension) == 'odt' )
                                            <a href="/js/ViewerJS/#../../{{ $correction->file }}">{{ basename($correction->file) }}</a>
                                        @elseif( end($extension) == 'docx' )
                                            <a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$correction->file}}">{{ basename($correction->file) }}</a>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if ($correction->editor_id)
                                        {{ $correction->editor->full_name }} <br>

                                        <button class="btn btn-xs btn-primary assignEditorBtn" data-toggle="modal"
                                                data-target="#assignEditorModal"
                                                data-editor="{{ json_encode($correction->editor) }}"
                                                data-action="{{ route($assignEditorRoute, ['id' => $correction->id, 'type' => 2]) }}">
                                            {{ trans('site.assign-editor') }}
                                        </button>
                                    @else
                                        <button class="btn btn-xs btn-warning assignEditorBtn" data-toggle="modal"
                                                data-target="#assignEditorModal"
                                                data-action="{{ route($assignEditorRoute, ['id' => $correction->id, 'type' => 2]) }}">
                                            Assign Editor
                                        </button>
                                    @endif
                                </td>
                                <td>
                                    {{ \App\Http\FrontendHelpers::formatDate($correction->created_at) }}
                                </td>
                                <td>
                                    @if ($correction->expected_finish)
                                        {{ $correction->expected_finish_formatted }}
                                        <br>
                                    @endif

                                    @if ($correction->status !== 2)
                                        <a href="#setOtherServiceFinishDateModal" data-toggle="modal"
                                           class="setOtherServiceFinishDateBtn"
                                           data-action="{{ route($updateExpectedFinishRoute,
										   ['id' => $correction->id, 'type' => 2]) }}"
                                           data-finish="{{ $correction->expected_finish ?
										strftime('%Y-%m-%d', strtotime($correction->expected_finish)) : '' }}">
                                            Set Date
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    <!-- show only if no feedback is given yet for this copyEditing -->
                                    @if (!$correction->feedback)
                                        <a href="#addOtherServiceFeedbackModal" data-toggle="modal" style="color:#dc3545"
                                           class="addOtherServiceFeedbackBtn" data-service="2"
                                           data-action="{{ route($otherServiceFeedbackRoute,
                                                    ['id' => $correction->id, 'type' => 2]) }}"
                                           data-email-template="{{ json_encode($correctionFeedbackTemplate) }}">+ {{ trans('site.add-feedback') }}</a>
                                    @else
                                    <?php $files = explode(',',$correction->feedback->manuscript); ?>
                                        @foreach($files as $file)
                                            <a href="{{ $file }}" download><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp;
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    @if( $correction->status == 2 )
                                        <span class="label label-success">Finished</span>
                                    @elseif( $correction->status == 1 )
                                        <span class="label label-primary">Started</span>
                                    @elseif( $correction->status == 0 )
                                        <span class="label label-warning">Not started</span>
                                    @endif
                                </td>
                                <td>
                                    <?php
                                    $btnColor = $correction->status == 1 ? 'primary' : 'warning';
                                    ?>

                                        <input type="checkbox" data-toggle="toggle" data-on="Locked"
                                               class="lock-toggle" data-off="Unlocked"
                                               data-type="correction" onchange="lockToggle(this)"
                                               data-id="{{$correction->id}}" data-size="mini" @if($correction->is_locked)
                                            {{ 'checked' }}
                                                @endif>

                                    @if ($correction->status !== 2)
                                        <button class="btn btn-{{ $btnColor }} btn-xs updateOtherServiceStatusBtn" type="button"
                                                data-toggle="modal" data-target="#updateOtherServiceStatusModal"
                                                data-service="2"
                                                data-action="{{ route($updateStatusRoute, ['id' => $correction->id, 'type' => 2]) }}"><i class="fa fa-check"></i></button>
                                    @endif

                                        <button class="btn btn-danger btn-xs deleteOtherServiceBtn" type="button"
                                                data-toggle="modal" data-target="#deleteOtherServiceModal"
                                                data-action="{{ route($otherServiceDeleteRoute, ['id' => $correction->id, 'type' => 2]) }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div> <!-- end correction -->

        @endif
        <div class="col-md-12 margin-top">
            <div class="row">
                <div class="col-md-6">
                    <div class="panel">
                        <div class="panel-header" style="padding: 10px;">
                            <em><b>Book Pictures</b></em>
                            <button class="btn btn-success btn-xs pull-right saveBookPictureBtn" data-toggle="modal"
                                    data-target="#bookPicturesModal"
                                    data-action="{{ route($saveBookPicturesRoute, $project->id) }}">
                                Add
                            </button>
                        </div>
                        <div class="panel-body">

                            <div class="table-users table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Description</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($bookPictures as $bookPicture)
                                        <tr>
                                            <td>
                                                <a href="{{ asset( $bookPicture->image ) }}">
                                                    <img src="{{ asset( $bookPicture->image ) }}" width="100" height="100">
                                                </a>
                                            </td>
                                            <td>
                                                {!! $bookPicture->description !!}
                                            </td>
                                            <td>
                                                <button class="btn btn-primary btn-sm saveBookPictureBtn" data-toggle="modal"
                                                        data-target="#bookPicturesModal"
                                                        data-record="{{ json_encode($bookPicture) }}"
                                                        data-action="{{ route($saveBookPicturesRoute, $project->id) }}">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm deleteBookPictureBtn" data-toggle="modal"
                                                        data-target="#deleteModal"
                                                        data-action="{{ route($deleteBookPicturesRoute, $bookPicture->id) }}">
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
                </div>

                <div class="col-md-6">
                    <div class="panel">
                        <div class="panel-header" style="padding: 10px;">
                            <em><b>Page Format</b></em>
                            <button class="btn btn-success btn-xs pull-right bookFormattingBtn" data-toggle="modal"
                                    data-target="#bookFormattingModal"
                                    data-action="{{ route($saveBookFormattingRoute, $project->id) }}">
                                Add
                            </button>
                        </div>
                        <div class="panel-body">
                            <div class="table-users table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>File</th>
                                        <th width="300"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($bookFormattingList as $bookFormatting)
                                        <tr>
                                            <td>
                                                {!! $bookFormatting->file_link !!}
                                            </td>
                                            <td>
                                                <a href="{{ $bookFormatting->file }}" class="btn btn-sm btn-success" download>
                                                    <i class="fa fa-download"></i>
                                                </a>
                                                <button class="btn btn-primary btn-sm bookFormattingBtn" data-toggle="modal"
                                                        data-target="#bookFormattingModal"
                                                        data-record="{{ json_encode($bookFormatting) }}"
                                                        data-action="{{ route($saveBookFormattingRoute, $project->id) }}">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm deleteBtn" data-toggle="modal"
                                                        data-target="#deleteModal"
                                                        data-action="{{ route($deleteBookFormattingRoute, $bookFormatting->id) }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- This is a pdf. This is the last thing for the book before it gets printed -->
                        </div>
                    </div>
                </div>
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

    <div id="addOtherServiceModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data"
                          action="{{ route($addOtherServiceRoute, $project->id) }}"
                          onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>{{ trans_choice('site.manuscripts', 1) }}</label>
                            <input type="file" class="form-control" name="manuscript"
                                   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                   required>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.assign-to') }}</label>
                            <select name="editor_id" class="form-control select2">
                            </select>
                        </div>

                        <input type="hidden" name="is_copy_editing">
                        <button class="btn btn-success pull-right" type="submit">
                            {{ trans('site.add') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="assignEditorModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Assign editor</label>
                            <select name="editor_id" class="form-control select2" required>
                                <option value="" disabled="" selected>-- Select Editor --</option>
                                @foreach( AdminHelpers::editorList() as $editor )
                                    <option value="{{ $editor->id }}">{{ $editor->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="text-right">
                            <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="setOtherServiceFinishDateModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><span></span> Expected Finish</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Expected finish date</label>
                            <input type="date" name="expected_finish" class="form-control" required>
                        </div>
                        <div class="text-right">
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback Modal  -->
    <div id="addOtherServiceFeedbackModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><span></span> {{ trans('site.add-feedback') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                        {{csrf_field()}}
                        <div class="form-group">
                            <label>{{ trans_choice('site.manuscripts', 1) }}</label>
                            <input type="file" class="form-control" name="manuscript[]" multiple
                                   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf" required>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('site.subject') }}</label>
                            <input type="text" class="form-control" name="subject" value=""
                                   required>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('site.from') }}</label>
                            <input type="text" class="form-control" name="from_email"
                                   value="" required>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('site.message') }}</label>
                            <textarea class="form-control tinymce" name="message" rows="6"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary pull-right">{{ trans('site.add-feedback') }}</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div id="updateOtherServiceStatusModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Update <span></span> Status</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <p>
                            Are you sure to update the status of this record?
                        </p>
                        <div class="text-right">
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteOtherServiceModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        {{ trans('site.delete') }}
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action=""
                          onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <p>
                            {{ trans('site.delete-item-question') }}
                        </p>
                        <button class="btn btn-danger pull-right" type="submit">
                            {{ trans('site.delete') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="bookPicturesModal" class="modal fade" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        Book Picture
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="id">
                        <div class="form-group">
                            <label>Images</label>
                            <input type="file" name="images[]" class="form-control"
                                   accept="image/*" multiple>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" cols="30" rows="10" class="form-control"></textarea>
                        </div>

                        <div class="text-right">
                            <button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div id="deleteModal" class="modal fade" role="dialog">
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

    <div id="bookFormattingModal" class="modal fade" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        Book Formatting
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="id">
                        <div class="form-group">
                            <label>File</label>
                            <input type="file" name="file" class="form-control"
                                   accept="application/pdf">
                        </div>

                        <div class="text-right">
                            <button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script src="{{ asset('js/app.js?v='.time()) }}"></script>
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

        $('.assignEditorBtn').click(function(){
            let action = $(this).data('action');
            let editor = $(this).data('editor');
            let modal = $('#assignEditorModal');
            modal.find('select').val(editor);
            modal.find('form').attr('action', action);

            if (editor) {
                modal.find('form').find('select[name=editor_id]').val(editor.id).trigger('change');
            }
        });

        $(".setOtherServiceFinishDateBtn").click(function(){
            let action = $(this).data('action');
            let modal = $('#setOtherServiceFinishDateModal');
            let finish = $(this).data('finish');

            modal.find('form').attr('action', action);
            modal.find('form').find('[name=expected_finish]').val(finish);
        });

        $(".addOtherServiceFeedbackBtn").click(function(){
            let action = $(this).data('action');
            let modal = $('#addOtherServiceFeedbackModal');
            let service = $(this).data('service');
            let emailTemplate = $(this).data('email-template');
            let title = 'Korrektur';

            if (service === 1) {
                title = 'Språkvask';
            }
            modal.find('form').attr('action', action);
            modal.find('.modal-title').find('span').text(title);
            modal.find('.modal-body').find('[name=subject]').val(emailTemplate.subject);
            modal.find('.modal-body').find('[name=from_email]').val(emailTemplate.from_email);
            tinyMCE.activeEditor.setContent(emailTemplate.email_content);
        });

        $(".updateOtherServiceStatusBtn").click(function(){
            let action = $(this).data('action');
            let modal = $('#updateOtherServiceStatusModal');
            let service = $(this).data('service');
            let title = 'Korrektur';

            if (service === 1) {
                title = 'Språkvask';
            }
            modal.find('form').attr('action', action);
            modal.find('.modal-title').find('span').text(title);
        });

        $(".deleteOtherServiceBtn").click(function(){
            let action = $(this).data('action');
            let modal = $('#deleteOtherServiceModal');
            modal.find('form').attr('action', action);
        });

        $(".saveBookPictureBtn").click(function() {
            let action = $(this).data('action');
            let record = $(this).data('record');
            let modal = $('#bookPicturesModal');
            modal.find('form').attr('action', action);

            if (record) {
                modal.find('[name=id]').val(record.id);
            }
        });

        $(".deleteBookPictureBtn").click(function(){
            let action = $(this).data('action');
            let modal = $('#deleteModal');
            modal.find('form').attr('action', action);
        });

        $(".bookFormattingBtn").click(function(){
            let action = $(this).data('action');
            let record = $(this).data('record');
            let modal = $('#bookFormattingModal');
            modal.find('form').attr('action', action);

            if (record) {
                modal.find('[name=id]').val(record.id);
            }
        });

        $(".deleteBtn").click(function(){
            let action = $(this).data('action');
            let modal = $('#deleteModal');
            modal.find('form').attr('action', action);
        });

        function updateOtherServiceFields(type, self) {
            let editors = $(self).data('editors');
            let modal = $("#addOtherServiceModal");
            let add_correction_text = "{{ trans('site.add-correction') }}";
            let add_copy_editing_text = "{{ trans('site.add-copy-editing') }}";
            let modal_title = add_correction_text;
            if (type === 1) {
                modal_title = add_copy_editing_text;
            }

            let editorContainer = modal.find('[name=editor_id]');
            editorContainer.empty();

            let editorOptions = '<option value="" disabled="" selected>-- Select Editor --</option>';

            $.each(editors, function(k, editor) {
                editorOptions += "<option value='" + editor.id + "'>" + editor.full_name + "</option>";
            });

            editorContainer.append(editorOptions);

            modal.find('.modal-title').text(modal_title);
            modal.find('form').find('[name=is_copy_editing]').val(type);
        }

        function lockToggle(self) {
            let id = $(self).attr('data-id');
            let type = $(self).attr('data-type');
            let is_checked = $(self).prop('checked');
            let check_val = is_checked ? 1 : 0;
            $.ajax({
                type:'POST',
                url:'/other-service/' + id + '/lock-status/' + type,
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: { 'is_locked' : check_val },
                success: function(data){
                    console.log(data);
                }
            });
        }
    </script>
    <script type="text/javascript" src="{{asset('select2/dist/js/select2.min.js')}}"></script>
@stop