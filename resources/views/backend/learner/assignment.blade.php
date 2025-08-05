@extends('backend.layout')

@section('title')
    <title>Assignments &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file"></i>
            {{ $assignment->title }}
        </h3>

        <a href="{{ url('/assignment?tab=learner') }}" class="btn btn-default btn-sm" style="margin-left: 5px">
            << Learner assignments
        </a>
        <div class="clearfix"></div>
    </div>

    <div class="col-sm-10 col-sm-offset-1" style="margin-top: 10px">
        <div class="pull-right">
            <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#editAssignmentModal">
                <i class="fa fa-pencil"></i>
            </button>
            <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteAssignmentModal">
                <i class="fa fa-trash"></i>
            </button>
        </div>

        <div class="clearfix"></div>

        <h3 class="no-margin-bottom">{{ $assignment->title }}</h3>
        <p class="margin-bottom">
            {{ $assignment->description }} <br>
            <b>{{ trans('site.submission-date') }}:</b> <i>{{ $assignment->submission_date }}</i> <br>
            <b>{{ trans('site.available-date') }}:</b> <i>{{ $assignment->available_date }}</i>
        </p>

        <div class="table-responsive">
            @if($assignment->manuscripts->count())
                <button type="button" class="pull-right btn btn-info btn-sm margin-bottom margin-right-5"
                        data-toggle="modal" data-target="#sendEmailModal">{{ trans('site.send-email') }}</button>
            @else
                <button type="button" class="pull-right btn btn-primary btn-sm margin-bottom"
                        data-toggle="modal" data-target="#addManuscriptModal">{{ trans('site.add-manuscript') }}</button>
            @endif

            <h5>{{ trans_choice('site.manuscripts', 2) }}</h5>
            <table class="table table-side-bordered table-white" style="margin-bottom: 0">
                <thead>
                <tr>
                    <th>{{ trans_choice('site.manuscripts', 1) }}</th>
                    <th>{{ trans_choice('site.learners', 1) }}</th>
                    <th>{{ trans('site.grade') }}</th>
                    <th>{{ trans('site.type') }}</th>
                    <th>{{ trans('site.where') }}</th>
                    <th>{{ trans_choice('site.words', 2) }}</th>
                    <th>{{ trans_choice('site.editors', 1) }}</th>
                    <th width="250"></th>
                </tr>
                </thead>
                <tbody>
                @foreach( $assignment->manuscripts as $manuscript )
                    <tr>
                        <td>
                            {!! $manuscript->file_link !!}

                            @if ($manuscript->letter_to_editor)
                                <br>
                                <a href="{{ route('assignment.manuscript.download_letter', $manuscript->id) }}">Download Letter</a>
                            @endif
                        </td>
                        <td>
                            <a href="{{route('admin.learner.show', $manuscript->user->id)}}">
                                {{ $manuscript->user->full_name }}
                            </a>
                        </td>
                        <td>{{ $manuscript->grade }}</td>
                        <td>
                            <a href="javascript:void(0)" data-ass-type="{{ $manuscript->type }}"
                               class="updateTypeBtn" data-toggle="modal" data-target="#updateTypeModal"
                               data-action="{{ route('assignment.group.update_manu_types', $manuscript->id) }}">
                                {{ \App\Http\AdminHelpers::assignmentType($manuscript->type) }}
                            </a>
                        </td>
                        <td>
                            <a href="javascript:void(0)" data-manu-type="{{ $manuscript->manu_type }}"
                               class="updateManuTypeBtn" data-toggle="modal" data-target="#updateManuTypeModal"
                               data-action="{{ route('assignment.group.update_manu_types', $manuscript->id) }}">
                                {{ \App\Http\AdminHelpers::manuscriptType($manuscript->manu_type) }}
                            </a>
                        </td>
                        <td> {{ $manuscript->words }} </td>
                        <td>
                            <?php $editor = $manuscript->editor_id ? \App\User::find($manuscript->editor_id) : '';?>

                            {{ $editor ? $editor->full_name."\n" : "" }}
                            <button class="btn btn-xs btn-primary assignEditorBtn" data-toggle="modal"
                                    data-target="#assignEditorModal"
                                    data-action="{{ route('assignment.group.assign_manu_editor', $manuscript->id) }}"
                                    data-editor="{{ $editor ? $editor->id : "" }}">
                                {{ trans('site.assign-editor') }}
                            </button>
                        </td>
                        <td>
                            <div class="text-right">
                                <a href="{{ route('assignment.group.download_manuscript', $manuscript->id) }}"
                                   class="btn btn-primary btn-xs">{{ trans('site.download') }}</a>
                                <input type="checkbox" data-toggle="toggle" data-on="{{ trans('site.locked') }}"
                                       class="lock-toggle" data-off="{{ trans('site.unlocked') }}"
                                       data-id="{{$manuscript->id}}" data-size="mini"
                                @if($manuscript->locked) {{ 'checked' }} @endif>

                                <button type="button" class="btn btn-info btn-xs replaceManuscriptBtn"
                                        data-toggle="modal" data-target="#replaceManuscriptModal"
                                        data-action="{{ route('assignment.group.replace_manuscript',
                                        $manuscript->id) }}"
                                        data-grade="{{ $manuscript->grade }}"
                                        data-ass-type="{{ $manuscript->type }}"
                                        data-manu-type="{{ $manuscript->manu_type }}">
                                    {{ trans('site.replace-doc') }}
                                </button>

                                <div class="margin-top">
                                    <button type="button" class="btn btn-warning btn-xs setGradeBtn" data-toggle="modal"
                                            data-target="#setGradeModal"
                                            data-action="{{ route('assignment.group.set_grade', $manuscript->id) }}"
                                            data-grade="{{ $manuscript->grade }}">
                                        {{ trans('site.set-grade') }}
                                    </button>

                                    <button type="button" class="btn btn-danger btn-xs deleteManuscriptBtn"
                                            data-toggle="modal" data-target="#deleteManuscriptModal"
                                            data-action="{{ route('assignment.group.delete_manuscript',
                                            $manuscript->id) }}">
                                        <i class="fa fa-trash"></i>
                                    </button>

                                    <div class="margin-top">
                                        @if($manuscript->editor_id)

                                            <?php
                                            $learner_list = [];
                                            foreach($assignment->groups as $group) {
                                                foreach($group->learners as $learner) {
                                                    $learner_list[] = $learner['user_id'];
                                                }
                                            }
                                            $noGroupHaveFeedback = \App\AssignmentFeedbackNoGroup::where([
                                                'assignment_manuscript_id' => $manuscript->id,
                                                'learner_id' => $manuscript->user->id
                                            ])->get();
                                            ?>
                                            @if(!in_array($manuscript->user_id,$learner_list))
                                                @if($noGroupHaveFeedback->count())
                                                    <button type="button" class="btn btn-primary btn-xs submitFeedbackBtn"
                                                            data-toggle="modal" data-target="#submitFeedbackModal"
                                                            data-name="{{ $manuscript->user->full_name }}"
                                                            data-action="{{
                                                            route('assignment.group.manuscript-feedback-no-group-update',
																$noGroupHaveFeedback[0]['id']) }}"
                                                            data-edit="true">
                                                        {{ trans('site.edit-feedback-as-admin') }}
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-primary btn-xs submitFeedbackBtn"
                                                            data-toggle="modal" data-target="#submitFeedbackModal"
                                                            data-name="{{ $manuscript->user->full_name }}"
                                                            data-action="{{
                                                             route('assignment.group.manuscript-feedback-no-group',
																['id' => $manuscript->id,
																'learner_id' => $manuscript->user->id]) }}">
                                                        {{ trans('site.submit-feedback-as-admin') }}
                                                    </button>
                                                @endif
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div> <!-- end table-responsive -->

    <?php
    $assignment_manuscripts_list = $assignment->manuscripts->pluck('id')->toArray();
    $noGroupFeedbackList = \App\AssignmentFeedbackNoGroup::whereIn('assignment_manuscript_id', $assignment_manuscripts_list)
        ->get();
    ?>

    @if ($noGroupFeedbackList->count())
        <!-- start of feedback for assignment without a group -->
            <div class="panel panel-default" style="margin-top: 10px">
                <div class="panel-body">
                    <h4 class="margin-bottom">{{ trans('site.feedbacks-for-assignment-without-a-group') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered" style="background-color: #fff">
                            <thead>
                            <tr>
                                <th>{{ trans_choice('site.feedbacks', 1) }}</th>
                                <th>{{ trans('site.submitted-by') }}</th>
                                <th>{{ trans('site.submitted-to') }}</th>
                                <th>{{ trans('site.availability') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($noGroupFeedbackList as $feedback)
                                <tr>
                                    <td>
                                        {!! $feedback->file_link !!}
                                        <a href="{{ $feedback->filename }}" download=""
                                           class="btn btn-primary btn-xs pull-right">
                                            {{ trans('site.download') }}
                                        </a>
                                    </td>
                                    <td>
                                        @if( $feedback->is_admin ) [Admin] @endif
                                            {{ basename($feedback->feedbackUser->full_name) }}
                                    </td>
                                    <td>
                                        {{ $feedback->learner->full_name }}
                                    </td>
                                    <td>
                                        <a href="#" data-toggle="modal" class="updateAvailabilityBtn"
                                           data-availability="{{ $feedback->availability }}"
                                           data-target="#updateAvailabilityModal"
                                           data-action="{{
                                           route('assignment.group.manuscript-feedback-no-group-update-availability',
                                           $feedback->id)
                                           }}">
                                            {{ \App\Http\FrontendHelpers::formatDate($feedback->availability) }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- end of feedback for assignment without a group -->
        @endif

    </div> <!-- end col-sm-10 -->

    <div id="editAssignmentModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.edit-assignment') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{route('assignment.learner-assignment.save', $assignment->id)}}"
                        onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <input type="hidden" name="learner_id" value="{{ $learner->id }}">

                        <div class="form-group">
                            <label>{{ trans('site.title') }}</label>
                            <input type="text" class="form-control" name="title" placeholder="{{ trans('site.title') }}"
                                   required value="{{ $assignment->title }}">
                        </div>
                        <div class="form-group">
                            <label>{{ trans('site.description') }}</label>
                            <textarea class="form-control" name="description"
                                      placeholder="{{ trans('site.description') }}" rows="6">{{ $assignment->description }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('site.delay-type') }}</label>
                            <select class="form-control" id="assignment-delay-toggle">
                                <option value="days">Days</option>
                                <option value="date" @if(\App\Http\AdminHelpers::isDateWithFormat('M d, Y h:i A',
						$assignment->submission_date)) selected @endif>Date</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('site.submission-date') }}</label>
                            <div class="input-group">
                                @if(\App\Http\AdminHelpers::isDateWithFormat('M d, Y h:i A', $assignment->submission_date))
                                    <input type="datetime-local" class="form-control" name="submission_date"
                                           id="assignment-delay" min="0" required
                                           @if( $assignment->submission_date )
                                           value="{{ strftime('%Y-%m-%dT%H:%M:%S', strtotime($assignment->submission_date)) }}"
                                            @endif>
                                @else
                                    <input type="number" class="form-control" name="submission_date" id="assignment-delay"
                                           min="0" required value="{{$assignment->submission_date}}">
                                @endif
                                <span class="input-group-addon assignment-delay-text" id="basic-addon2">
						  	@if(\App\Http\AdminHelpers::isDateWithFormat('M d, Y h:i A', $assignment->submission_date))
                                        date
                                    @else
                                        days
                                    @endif
						  	</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.available-date') }}</label>
                            <input type="date" class="form-control" name="available_date"
                                   @if( $assignment->available_date )
                                   value="{{ strftime('%Y-%m-%d', strtotime($assignment->available_date)) }}" @endif>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.max-words') }}</label>
                            <input type="number" class="form-control" name="max_words"
                                   value="{{ $assignment->max_words }}">
                        </div>

                        <div class="form-group">
                            <label>Allowed up to</label>
                            <input type="number" class="form-control" name="allow_up_to"
                            value="{{ $assignment->allow_up_to }}">
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.send-letter-to-editor') }}</label> <br>
                            <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" data-size="small"
                                   name="send_letter_to_editor" @if ($assignment->send_letter_to_editor) checked @endif>
                        </div>

                        <div class="form-group">
                            <label>{{ trans_choice('site.editors', 1) }}</label>
                            <select class="form-control select2" name="editor_id">
                                <option value="" selected disabled>- Select Editor -</option>
                                @foreach(\App\Http\AdminHelpers::editorList() as $editor)
                                    <option value="{{ $editor->id }}" @if($assignment->editor_id === $editor->id) selected @endif>
                                        {{ $editor->first_name . " " . $editor->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>{{ trans_choice('site.courses', 1) }}</label>
                            <select class="form-control select2" name="course_id">
                                <option value="" selected>- Search Course -</option>
                                @foreach(\App\Http\AdminHelpers::courseList() as $course)
                                    <option value="{{$course->id}}"
                                            {{ $course->id === $assignment->course_id ? 'selected' : '' }}>
                                        {{$course->title}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary pull-right margin-top">
                            {{ trans('site.save') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div> <!-- end edit assignment modal -->

    <div id="deleteAssignmentModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.delete-assignment') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{route('assignment.learner-assignment.delete', $assignment->id)}}">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        {{ trans('site.delete-assignment-question') }}
                        <button type="submit" class="btn btn-danger pull-right margin-top">
                            {{ trans('site.delete') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div> <!-- end delete assignment modal -->

    <div id="addManuscriptModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.add-manuscript') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('assignment.group.upload_manuscript', $assignment->id) }}"
                          enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="learner_id" value="{{ $learner->id }}">
                        <input type="hidden" name="join_group" value="0">

                        <div class="form-group">
                            <label>{{ trans_choice('site.manuscripts', 1) }}</label>
                            <input type="file" class="form-control" required name="filename"
                                   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                    application/pdf, application/vnd.oasis.opendocument.text, application/msword">
                            * Godkjente fil formater er DOCX, PDF og ODT.
                        </div>

                        <button type="submit" class="btn btn-primary pull-right margin-top">
                            {{ trans('site.submit') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div> <!-- end add manuscript modal -->

    <div id="sendEmailModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.send-email') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{route('assignment.group.send-email-to-list', $assignment->id)}}"
                          onsubmit="formSubmitted(this)">
                        {{csrf_field()}}

                        <div class="form-group">
                            <label>{{ trans('site.subject') }}</label>
                            <input type="text" class="form-control" name="subject" required>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.message') }}</label>
                            <textarea name="message" id="" cols="30" rows="10" class="form-control" required></textarea>
                        </div>
                        <div class="text-right">
                            <input type="submit" class="btn btn-primary" value="{{ trans('site.send') }}"
                                   id="send_email_btn">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> <!-- end send email modal -->

    <div id="updateTypeModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.replace-type') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{ csrf_field() }}
                        <div class="form-group margin-top">
                            {{ trans('site.genre') }}
                            <select class="form-control" name="type" id="ass_type" required>
                                <option value="" disabled="disabled" selected>Select Type</option>
                                @foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
                                    <option value="{{ $type->id }}"> {{ $type->name }} </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary pull-right margin-top">
                            {{ trans('site.submit') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div> <!-- end update type modal -->

    <div id="updateManuTypeModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.replace-where-to-find') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{ csrf_field() }}
                        <div class="form-group">
                            {{ trans('site.where-in-the-script') }} <br>
                            @foreach(\App\Http\FrontendHelpers::manuscriptType() as $manu)
                                <input type="radio" name="manu_type" value="{{ $manu['id'] }}" required>
                                <label>{{ $manu['option'] }}</label> <br>
                            @endforeach
                        </div>
                        <button type="submit" class="btn btn-primary pull-right margin-top">
                            {{ trans('site.submit') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div> <!-- end update manu type modal -->

    <div id="assignEditorModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.assign-editor') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>{{ trans_choice('site.editors', 1) }}</label>
                            <select class="form-control select2" name="editor_id" required>
                                <option value="" disabled selected>- Select Editor -</option>
                                @foreach( $editors as $editor )
                                    <?php
                                        $selected = '';

                                        if ($manuscript) {
                                            if ($manuscript->user->preferredEditor
                                                && $manuscript->user->preferredEditor->editor_id === $editor->id) {
                                                $selected = 'selected';
                                            } else {
                                                if ($manuscript->editor_id === $editor->id) {
                                                    $selected = 'selected';
                                                }
                                            }
                                        }
                                    ?>
                                    <option value="{{ $editor->id }}" {{ $selected }}>
                                        {{ $editor->full_name }}
                                    </option>
                                @endforeach
                            </select>

                            @if($manuscript && $manuscript->user->preferredEditor)
                                <div class="hidden-container">
                                    <label>
                                        {{ $manuscript->user->preferredEditor->editor->full_name }}
                                    </label>
                                    <a href="javascript:void(0)" onclick="enableSelect('assignEditorModal')">Edit</a>
                                </div>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-primary pull-right margin-top">
                            {{ trans('site.submit') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div> <!-- end assign editor modal -->

    <div id="replaceManuscriptModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.replace-manuscript') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>{{ trans_choice('site.manuscripts', 1) }}</label>
                            <input type="file" class="form-control" required name="filename"
                                   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                    application/pdf, application/vnd.oasis.opendocument.text">
                            * Godkjente fil formater er DOCX, PDF og ODT.
                        </div>

                        <div class="form-group margin-top">
                            {{ trans('site.genre') }}
                            <select class="form-control" name="type" id="ass_type" required>
                                <option value="" disabled="disabled" selected>Select Type</option>
                                @foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
                                    <option value="{{ $type->id }}"> {{ $type->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            {{ trans('site.where-in-the-script') }} <br>
                            @foreach(\App\Http\FrontendHelpers::manuscriptType() as $manu)
                                <input type="radio" name="manu_type" value="{{ $manu['id'] }}" required>
                                <label>{{ $manu['option'] }}</label> <br>
                            @endforeach
                        </div>

                        <button type="submit" class="btn btn-primary pull-right margin-top">Submit</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div> <!-- end replace manuscript modal -->

    <div id="setGradeModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.set-grade') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>{{ trans('site.grade') }}</label>
                            <input type="number" class="form-control" step="0.01" name="grade" required>
                        </div>
                        <button type="submit" class="btn btn-primary pull-right">{{ trans('site.save') }}</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div> <!-- end set grade modal -->

    <div id="deleteManuscriptModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.delete-manuscript') }}</h4>
                </div>
                <div class="modal-body">
                    {{ trans('site.delete-manuscript-question') }}
                    <form method="POST" action="">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-danger pull-right margin-top">
                            {{ trans('site.delete') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div> <!-- end delete manuscript modal -->

    <div id="submitFeedbackModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.submit-feedback-to') }} <em></em></h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action=""  enctype="multipart/form-data">
                        <?php
                            $emailTemplate = \App\Http\AdminHelpers::emailTemplate('Assignment Manuscript Feedback');
                        ?>
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>{{ trans_choice('site.manuscripts', 1) }}</label>
                            <input type="file" class="form-control" required multiple name="filename[]"
                                   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                   application/pdf, application/vnd.oasis.opendocument.text">
                            * Accepted file formats are DOCX, PDF, ODT.
                        </div>
                        <div class="form-group">
                            <label>{{ trans('site.available-date') }}</label>
                            <input type="date" class="form-control" name="availability">
                        </div>
                        <div class="form-group">
                            <label>{{ trans('site.grade') }}</label>
                            <input type="number" class="form-control" step="0.01" name="grade">
                        </div>
                        <div class="form-group">
                            <label>{{ trans('site.subject') }}</label>
                            <input type="text" class="form-control" name="subject" value="{{ $emailTemplate->subject }}"
                                   required>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('site.from') }}</label>
                            <input type="text" class="form-control" name="from_email"
                                   value="{{ $emailTemplate->from_email }}" required>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('site.message') }}</label>
                            <textarea class="form-control tinymce" name="message" rows="6"
                                      required>{!! $emailTemplate->email_content !!}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary pull-right margin-top">
                            {{ trans('site.submit') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script>

        $('#assignment-delay-toggle').change(function(){
            let delay = $(this).val();
            if(delay === 'days'){
                $('#assignment-delay').attr('type', 'number');
            } else if(delay === 'date')
            {
                $('#assignment-delay').attr('type', 'datetime-local');
            }
            $('.assignment-delay-text').text(delay);
        });

        $('.updateTypeBtn').click(function(){
            let form = $('#updateTypeModal').find('form');
            let action = $(this).data('action');
            let type = $(this).data('ass-type') ? $(this).data('ass-type') : '';

            form.attr('action', action);
            form.find('#ass_type').val(type);
        });

        $('.updateManuTypeBtn').click(function(){
            let form = $('#updateManuTypeModal').find('form');
            let action = $(this).data('action');
            let manu_type = $(this).data('manu-type');

            form.attr('action', action);
            form.find("input[name=manu_type][value="+manu_type+"]").attr('checked', true);
        });

        $(".assignEditorBtn").click(function(){
            let form = $('#assignEditorModal').find('form');
            let action = $(this).data('action');
            let editor = $(this).data('editor');

            form.attr('action', action);
            @if($manuscript && !$manuscript->user->preferredEditor)
                form.find("select[name=editor_id]").val(editor);
            @endif
        });

        $(".lock-toggle").change(function(){
            let course_id = $(this).attr('data-id');
            let is_checked = $(this).prop('checked');
            let check_val = is_checked ? 1 : 0;
            $.ajax({
                type:'POST',
                url:'/assignment_manuscript/lock-status',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: { "manuscript_id" : course_id, 'locked' : check_val },
                success: function(data){
                }
            });
        });

        $('.replaceManuscriptBtn').click(function(){
            let form = $('#replaceManuscriptModal').find('form');
            let action = $(this).data('action');
            let type = $(this).data('ass-type') ? $(this).data('ass-type') : '';
            let manu_type = $(this).data('manu-type');

            form.attr('action', action);
            form.find('#ass_type').val(type);
            form.find("input[name=manu_type][value="+manu_type+"]").attr('checked', true);
        });

        $('.setGradeBtn').click(function(){
            let form = $('#setGradeModal').find('form');
            let action = $(this).data('action');
            let grade = $(this).data('grade');
            form.find('input[name=grade]').val(grade);
            form.attr('action', action)
        });

        $('.deleteManuscriptBtn').click(function(){
            let form = $('#deleteManuscriptModal').find('form');
            let action = $(this).data('action');
            form.attr('action', action)
        });

        $('.submitFeedbackBtn').click(function(){
            let modal = $('#submitFeedbackModal');
            let name = $(this).data('name');
            let action = $(this).data('action');
            let is_edit = $(this).data('edit');
            modal.find('em').text(name);
            modal.find('form').attr('action', action);
            if (is_edit) {
                modal.find('form').find('input[type=file]').removeAttr('required');
            } else {
                modal.find('form').find('input[type=file]').attr('required', 'required');
            }
        });

        $(document).ready(function() {
            @if($manuscript && $manuscript->user->preferredEditor )
                $("#assignEditorModal").find(".select2").hide();
            @endif
        });

        function formSubmitted(t) {
            let send_email = $(t).find("[type=submit]");
            send_email.val('Sending....').attr('disabled', true);
        }
    </script>
@stop