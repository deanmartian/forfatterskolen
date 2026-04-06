@extends($layout)

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <style>
        .dropdown-container {
            position: relative;
            width: 100%;
        }
        .dropdown-results {
            position: absolute;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ccc;
            background-color: #fff;
            z-index: 1000;
        }
        .dropdown-results div {
            padding: 8px;
            cursor: pointer;
        }
        .dropdown-results div:hover {
            background-color: #f1f1f1;
        }
        .selected-items {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-bottom: 10px;
        }
        .selected-item {
            background-color: #007bff;
            color: #fff;
            padding: 5px 10px;
            border-radius: 15px;
            display: flex;
            align-items: center;
        }
        .selected-item span {
            margin-left: 10px;
            cursor: pointer;
        }
    </style>
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
                         :task-list="{{ json_encode($tasks) }}" :book-critique-list="{{ json_encode($bookCritiques) }}"
                         :editors="{{ json_encode($editors) }}"></project-details>

        
        <div class="margin-top">
            <div class="col-md-6">
                <project-whole-book :current-project="{{ json_encode($project) }}" 
                :whole-book-list="{{ json_encode($wholeBooks) }}"
                :designers="{{ json_encode(AdminHelpers::giutbokUsers()) }}"></project-whole-book>

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
                                    {{ $publishing->title }} <br>
                                    @if ($publishing->poInvoice)
                                        <button class="btn btn-primary btn-xs powerOfficeOrderBtn" 
                                        data-action="{{ route('admin.power-office.self-publishing.view-po-order', 
                                        [$publishing->id, $publishing->poInvoice->id]) }}" 
                                            data-target="#powerOfficeOrderModal"
                                            data-toggle="modal">
                                            View Invoice
                                        </button>
                                    @else
                                        <a href="{{ route('admin.power-office.self-publishing.add-to-po', [$publishing->id]) }}" 
                                            class="btn btn-primary btn-xs">
                                            Add to PO
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    {{ $publishing->description }}
                                </td>
                                <td>
                                    {!! $publishing->dropbox_file_link_with_download !!}
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
                                    @if ($publishing->status !== 'finished')
                                        <button class="btn btn-warning btn-xs updatePublishingStatusBtn" type="button"
                                                data-toggle="modal" data-target="#updatePublishingStatusModal"
                                                data-status="finished"
                                                data-action="{{ route('admin.self-publishing.update-status', 
                                                ['id' => $publishing->id]) }}"><i class="fa fa-check"></i></button>
                                    @endif
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
                </div> <!-- end self publishing-->

                @if($project->user_id)
                    <!-- copy editing -->
                    <div class="margin-top">
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
                                                @if (strpos($copy_editing->file, 'project-'))
                                                    <a href="{{ url('/dropbox/download/' . trim($copy_editing->file)) }}">
                                                        <i class="fa fa-download" aria-hidden="true"></i>
                                                    </a>&nbsp;
                                                    <a href="{{ url('/dropbox/shared-link/' . trim($copy_editing->file)) }}" 
                                                        target="_blank">
                                                        {{ basename($copy_editing->file) }}
                                                    </a>
                                                @else
                                                    <a href="{{ route($downloadOtherService, ['id' => $copy_editing->id, 'type' => 1]) }}"
                                                        download>
                                                        <i class="fa fa-download" aria-hidden="true"></i>
                                                    </a>&nbsp;
                                                    @if( end($extension) == 'pdf' || end($extension) == 'odt' )
                                                        <a href="/js/ViewerJS/#../../{{ $copy_editing->file }}">
                                                            {{ basename($copy_editing->file) }}</a>
                                                    @elseif( end($extension) == 'docx' )
                                                        <a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$copy_editing->file}}">
                                                            {{ basename($copy_editing->file) }}</a>
                                                    @endif
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
                                                        data-action="{{ route($assignEditorRoute,
                                                        ['id' => $copy_editing->id, 'type' => 1]) }}">
                                                    {{ trans('site.assign-editor') }}
                                                </button>
                                            @endif
                                            <button class="btn btn-xs btn-info projectRequestToEditorBtn" data-toggle="modal"
                                                    data-target="#projectRequestToEditorModal"
                                                    data-item-id="{{ $copy_editing->id }}"
                                                    data-item-type="copy-editing"
                                                    data-action="{{ route('admin.project.send-request-to-editor', ['itemId' => $copy_editing->id, 'type' => 'copy-editing']) }}">
                                                Send forespørsel til redaktør
                                            </button>
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
                                                data-email-template="{{ json_encode($copyEditingFeedbackTemplate) }}">
                                                + {{ trans('site.add-feedback') }}</a>
                                            @else
                                                <?php //$files = explode(',',$copy_editing->feedback->manuscript); ?>
                                                {{-- @foreach($files as $file)
                                                    <a href="{{ route('dropbox.download_file', trim($file)) }}">
                                                        <i class="fa fa-download" aria-hidden="true"></i>
                                                    </a> &nbsp;
                                                @endforeach --}}
                                                <a href="{{ route($otherServiceDownloadFeedbackRoute, [$copy_editing->feedback->id, 1]) }}"
                                                    class="btn btn-success btn-xs">
                                                        Download Feedback
                                                </a>
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
                                                <button class="btn btn-{{ $btnColor }} btn-xs updateOtherServiceStatusBtn" 
                                                type="button"
                                                        data-toggle="modal" data-target="#updateOtherServiceStatusModal"
                                                        data-service="1"
                                                        data-action="{{ route($updateStatusRoute, 
                                                        ['id' => $copy_editing->id, 'type' => 1]) }}">
                                                    <i class="fa fa-check"></i>
                                                </button>
                                            @endif

                                            <button class="btn btn-danger btn-xs deleteOtherServiceBtn" type="button"
                                                    data-toggle="modal" data-target="#deleteOtherServiceModal"
                                                    data-action="{{ route($otherServiceDeleteRoute, 
                                                    ['id' => $copy_editing->id, 'type' => 1]) }}">
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
                    <div class="margin-top">
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
                                            @if (strpos($correction->file, 'project-'))
                                                <a href="{{ url('/dropbox/download/' . trim($correction->file)) }}">
                                                    <i class="fa fa-download" aria-hidden="true"></i>
                                                </a>&nbsp;
                                                <a href="{{ url('/dropbox/shared-link/' . trim($correction->file)) }}" target="_blank">
                                                    {{ basename($correction->file) }}
                                                </a>
                                            @else
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
                                            <button class="btn btn-xs btn-info projectRequestToEditorBtn" data-toggle="modal"
                                                    data-target="#projectRequestToEditorModal"
                                                    data-item-id="{{ $correction->id }}"
                                                    data-item-type="correction"
                                                    data-action="{{ route('admin.project.send-request-to-editor', ['itemId' => $correction->id, 'type' => 'correction']) }}">
                                                Send forespørsel til redaktør
                                            </button>
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
                                            <?php //$files = explode(',',$correction->feedback->manuscript); ?>
                                                {{-- @foreach($files as $file)
                                                    <a href="{{ route('dropbox.download_file', trim($file)) }}">
                                                        <i class="fa fa-download" aria-hidden="true"></i>
                                                    </a> &nbsp;
                                                @endforeach --}}
                                            <a href="{{ route($otherServiceDownloadFeedbackRoute, [$correction->feedback->id, 2]) }}"
                                                class="btn btn-success btn-xs">
                                                    Download Feedback
                                            </a>
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
            </div>
            <div class="col-md-6">
                <project-books :current-project="{{ json_encode($project) }}" 
                    :project-user="{{ json_encode($project->user) }}" :learners="{{ json_encode($learners) }}"></project-books>

                <project-tasks :current-project="{{ json_encode($project) }}" :task-list="{{ json_encode($tasks) }}"
                :editor-and-admin-list="{{ json_encode($editorAndAdminList) }}"></project-tasks>

                @if($project->user_id)
                    <project-time-register :current-project="{{ json_encode($project) }}"
                    :project-time-list="{{ json_encode($projectTimeRegisters) }}"></project-time-register>
                @endif

                <project-notes :current-project="{{ json_encode($project) }}"></project-notes>
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
                    <form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)"
                    id="learnerForm">
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
                            <div class="dropdown-container">
                                <input type="hidden" id="selectedLearnerId" name="learners[]">
                                <input type="text" id="searchInput" class="form-control" placeholder="Search for users">
                                <div id="dropdownResults" class="dropdown-results" style="display: none;"></div>
                            </div>
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
                                <input type="number" name="price" class="form-control" step="0.01">
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

    <div id="powerOfficeOrderModal" class="modal fade" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        <em>
                            Faktura - kopi
                        </em>
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="invoice-container"></div>
                    <div class="text-center loader-container" style="font-size: 50px">
                        <i class="fa fa-spinner fa-pulse"></i>
                    </div>
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
                                   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document">
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
                        <input type="hidden" name="project_id" value="{{ $project->id }}">
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
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script src="{{ asset('js/app.js?v='.time()) }}"></script>
    <script type="text/javascript" src="{{asset('select2/dist/js/select2.min.js')}}"></script>
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

        $(".powerOfficeOrderBtn").click(function() {
            let action = $(this).data('action');
            let modal = $('#powerOfficeOrderModal');
            
            modal.find(".invoice-container").empty();
            modal.find(".loader-container").show();
            
            $.ajax({
                type:'GET',
                url: action,
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: {},
                success: function(data){
                    modal.find(".invoice-container").html(data);
                    modal.find(".loader-container").hide();
                }
            });
        });

        $(document).on('click', '.downloadInvoice', function() {
            const self = $(this);
            const spinner = self.find('.fa-spinner');
            const action = self.data('action');
            self.attr('disabled', true);
            spinner.show();

            $.ajax({
                url: action,
                method: 'GET',
                xhrFields: {
                    responseType: 'blob' // Important for binary data
                },
                success: function(data, status, xhr) {
                    // Hide the loading indicator
                    spinner.hide();
                    self.removeAttr('disabled');

                    // Extract the file name from the response headers
                    var disposition = xhr.getResponseHeader('Content-Disposition');
                    var fileName = "invoice.pdf"; // Default file name

                    if (disposition && disposition.indexOf('filename=') !== -1) {
                        var matches = /filename="(.+)"/.exec(disposition);
                        if (matches != null && matches[1]) {
                            fileName = matches[1];
                        }
                    } else {
                        // Fallback to X-File-Name header
                        var headerFileName = xhr.getResponseHeader('X-File-Name');
                        if (headerFileName) {
                            fileName = headerFileName;
                        }
                    }

                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(new Blob([data]));
                    link.download = fileName;
                    link.click();
                    
                },
                error: function() {
                    // Hide the loading indicator
                    spinner.hide();
                    self.removeAttr('disabled');

                    alert('Failed to download the PDF. Please try again.');
                }
            });
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

        $(".updatePublishingStatusBtn").click(function(){
            let action = $(this).data('action');
            let modal = $('#updatePublishingStatusModal');
            let status = $(this).data('status');
            modal.find('form').attr('action', action);
            modal.find('[name=status]').val(status);
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

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const dropdownResults = document.getElementById('dropdownResults');
            const selectedLearnerId = document.getElementById('selectedLearnerId');

            searchInput.addEventListener('input', function() {
                const query = searchInput.value.trim();
                if (query.length > 1) {
                    fetch(`/learners/search?search=${query}`)
                        .then(response => response.json())
                        .then(data => {
                            dropdownResults.innerHTML = '';
                            if (data.length > 0) {
                                data.forEach(item => {
                                    const div = document.createElement('div');
                                    div.textContent = item.first_name + " " + item.last_name;
                                    div.dataset.id = item.id;
                                    div.addEventListener('click', () => {
                                        searchInput.value = item.first_name + " " + item.last_name;
                                        selectedLearnerId.value = item.id;
                                        dropdownResults.style.display = 'none';
                                    });
                                    dropdownResults.appendChild(div);
                                });
                                dropdownResults.style.display = 'block';
                            } else {
                                dropdownResults.style.display = 'none';
                            }
                        })
                        .catch(error => console.error('Error fetching data:', error));
                } else {
                    dropdownResults.style.display = 'none';
                }
            });

            document.addEventListener('click', function(event) {
                if (!dropdownResults.contains(event.target) && event.target !== searchInput) {
                    dropdownResults.style.display = 'none';
                }
            });
        });

        // Email preview toggle
        function toggleProjectEmailPreview() {
            var preview = document.getElementById('projectEmailPreview');
            var content = document.getElementById('projectEmailPreviewContent');
            if (preview.style.display === 'none') {
                var text = document.getElementById('projectRequestMessage').value;
                // Convert plain text to HTML
                var html = text
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/:login_link/g, '<a href="#" style="display:inline-block;padding:12px 28px;background:#862736;color:#fff;border-radius:6px;text-decoration:none;font-weight:600;">Logg inn i portalen →</a>')
                    .replace(/\n\n/g, '</p><p style="margin:0 0 14px;">')
                    .replace(/\n/g, '<br>');
                html = '<p style="margin:0 0 14px;">' + html + '</p>';
                content.innerHTML = html;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        }

        // Editor lists per type
        var editorsByType = {
            'copy-editing': {!! json_encode($copyEditingEditors->map(fn($e) => ['id' => $e->id, 'name' => $e->full_name])->values()) !!},
            'correction': {!! json_encode($correctionEditors->map(fn($e) => ['id' => $e->id, 'name' => $e->full_name])->values()) !!},
            'self-publishing': {!! json_encode($editors->map(fn($e) => ['id' => $e->id, 'name' => $e->full_name])->values()) !!}
        };

        // Project Request To Editor modal
        var projectRequestTexts = {
            'copy-editing': {
                title: 'Send forespørsel — Språkvask',
                subject: 'Kan du ta en språkvask?',
                message: 'Hei,\n\nVi har et manus som trenger språkvask. Har du kapasitet til å ta dette oppdraget?\n\nOmfang: Se vedlagt manus\nFrist for svar: Se svarfrist nedenfor\n\nLogg inn her for å se detaljer: :login_link\n\nMvh,\nForfatterskolen'
            },
            'correction': {
                title: 'Send forespørsel — Korrektur',
                subject: 'Kan du ta en korrektur?',
                message: 'Hei,\n\nVi har et manus som trenger korrekturlesing. Har du kapasitet til å ta dette oppdraget?\n\nOmfang: Se vedlagt manus\nFrist for svar: Se svarfrist nedenfor\n\nLogg inn her for å se detaljer: :login_link\n\nMvh,\nForfatterskolen'
            },
            'self-publishing': {
                title: 'Send forespørsel — Redaktørarbeid',
                subject: 'Kan du ta et redaktøroppdrag?',
                message: 'Hei,\n\nVi har et manus som trenger redaktørarbeid. Har du kapasitet til å lese og gi tilbakemelding på dette manuset?\n\nOmfang: Se vedlagt manus\nFrist for svar: Se svarfrist nedenfor\n\nLogg inn her for å se detaljer: :login_link\n\nMvh,\nForfatterskolen'
            }
        };

        $('.projectRequestToEditorBtn').click(function(){
            var action = $(this).data('action');
            var itemId = $(this).data('item-id');
            var itemType = $(this).data('item-type');
            var modal = $('#projectRequestToEditorModal');
            modal.find('form').attr('action', action);

            // Populate editor dropdown based on type
            var select = document.getElementById('projectRequestEditorSelect');
            select.innerHTML = '<option value="" disabled selected>- Velg redaktør -</option>';
            var editors = editorsByType[itemType] || editorsByType['self-publishing'];
            editors.forEach(function(e) {
                select.innerHTML += '<option value="' + e.id + '">' + e.name + '</option>';
            });

            // Set type-specific text
            var texts = projectRequestTexts[itemType] || projectRequestTexts['self-publishing'];
            modal.find('.modal-title').text(texts.title);
            modal.find('input[name="subject"]').val(texts.subject);
            if (typeof tinymce !== 'undefined' && tinymce.get('projectRequestMessage')) {
                tinymce.get('projectRequestMessage').setContent(texts.message.replace(/\n/g, '<br>'));
            } else {
                modal.find('textarea[name="message"]').val(texts.message);
            }

            // Load previous requests for this item
            var tableBody = modal.find('.previous-requests-body');
            tableBody.empty();

            var requests = $(this).data('requests');
            if (requests && requests.length > 0) {
                $.each(requests, function(i, req) {
                    tableBody.append('<tr><td>' + req.created_at + '</td><td>' + req.editor_name + '</td><td>' + req.answer_until + '</td><td>' + (req.answer || 'Ikke svart') + '</td></tr>');
                });
            }
        });

    </script>

{{-- Project Request To Editor Modal --}}
<div id="projectRequestToEditorModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Send forespørsel til redaktør</h4>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Dato sendt</th>
                            <th>Redaktør</th>
                            <th>Svarfrist</th>
                            <th>Svar</th>
                        </tr>
                    </thead>
                    <tbody class="previous-requests-body">
                    </tbody>
                </table>
                <hr>
                <label>Kan du ta dette oppdraget?</label>
                <form method="POST" action="">
                    {{ csrf_field() }}
                    <div class="margin-top">
                        <div class="form-group">
                            <label>Redaktør</label>
                            <select class="form-control" name="editor_id" id="projectRequestEditorSelect" required>
                                <option value="" disabled selected>- Velg redaktør -</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Svarfrist</label>
                            <input type="date" class="form-control" name="answer_until" required>
                        </div>
                        <div class="form-group">
                            <label>Emne</label>
                            <input type="text" class="form-control" name="subject"
                                value="{{ $requestToEditorEmailTemplate->subject ?? '' }}" required>
                        </div>
                        <div class="form-group">
                            <label>Fra</label>
                            <input type="text" class="form-control" name="from_email"
                                value="{{ $requestToEditorEmailTemplate->from_email ?? 'post@forfatterskolen.no' }}" required>
                        </div>
                        <div class="form-group">
                            <label>Melding</label>
                            <textarea class="form-control" name="message" id="projectRequestMessage" rows="8"
                                style="font-family:inherit;font-size:14px;line-height:1.6;" required></textarea>
                        </div>

                        {{-- Forhåndsvisning --}}
                        <div class="form-group">
                            <button type="button" class="btn btn-default btn-sm" onclick="toggleProjectEmailPreview()">
                                <i class="fa fa-eye"></i> Forhåndsvisning
                            </button>
                        </div>
                        <div id="projectEmailPreview" style="display:none;border:1px solid #ddd;border-radius:8px;padding:24px;background:#fafafa;margin-bottom:16px;">
                            <div style="text-align:center;margin-bottom:16px;">
                                <img src="{{ asset('photos/logos/fs-logo.png') }}" alt="Forfatterskolen" style="height:40px;">
                            </div>
                            <div id="projectEmailPreviewContent" style="font-family:-apple-system,sans-serif;font-size:14px;line-height:1.7;color:#333;">
                            </div>
                            <hr style="margin:20px 0;border-color:#eee;">
                            <div style="text-align:center;font-size:12px;color:#999;">
                                Spørsmål? Svar på denne e-posten eller ring 411 23 555<br>
                                Forfatterskolen · Lihagen 21, 3029 Drammen
                            </div>
                        </div>

                        <hr>
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@stop