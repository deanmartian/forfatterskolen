@extends('backend.layout')

@section('title')
<title>{{ $group->title }} &rsaquo; {{$course->title}} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('content')

@include('backend.course.partials.toolbar')


<div class="course-container">
	
	@include('backend.partials.course_submenu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12">
			<a href="{{ route('admin.assignment.show', ['course_id' => $course->id, 'assignment' => $assignment->id]) }}" class="btn btn-sm btn-default margin-bottom" ><i class="fa fa-angle-left"></i> {{ $assignment->title }}</a>

			<div class="pull-right">
				<button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#editGroupModal"><i class="fa fa-pencil"></i></button>
				<button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteGroupModal"><i class="fa fa-trash"></i></button>
			</div>
				
			<div class="text-center">
				<h3 class="no-margin-bottom">{{ $group->title }}</h3>
				<p>
					{{ trans_choice('site.assignments', 1) }}: {{ $group->assignment->title }} <br>
					{{ trans('site.submission-date') }}: {{ $group->submission_date }}
				</p>
			</div>
			
			<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addLearnerModal">{{ trans('site.add-learner') }}</button>
			<div class="row"> 
				@foreach( $group->learners as $learner )
				<div class="col-sm-4">
					<div class="panel panel-default margin-top">
						<div class="panel-body">
							<button class="btn btn-danger btn-xs pull-right removeLearnerBtn" data-action="{{route('assignment.group.remove_learner', ['course_id' => $course->id, 'assignment_id' => $assignment->id, 'group_id' => $group->id, 'id' => $learner->id])}}" data-toggle="modal" data-target="#removeLearnerModal"><i class="fa fa-trash"></i></button>
							<h4>{{ $learner->user->full_name }}
								<a href="{{ route('admin.learner.show', $learner->user->id) }}">
									<span class="pull-right" style="margin-right: 15px">id - {{ $learner->user->id }}</span>
								</a>
							</h4>
							<p class="margin-top no-margin-bottom">
								<?php $manuscript = $assignment->manuscripts->where('user_id', $learner->user_id)->first(); ?>
								@if( $manuscript )
									<?php $extension = explode('.', basename($manuscript->filename)); ?>
									@if( end($extension) == 'pdf' || end($extension) == 'odt' )
									<a href="/js/ViewerJS/#../..{{ $manuscript->filename }}">{{ basename($manuscript->filename) }}</a>
									@elseif( end($extension) == 'docx' || end($extension) == 'doc')
									<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$manuscript->filename}}">{{ basename($manuscript->filename) }}</a>
									<br />
											{{ trans('site.grade') }} : @if( $manuscript->grade ) {{ $manuscript->grade }} @else Not set @endif
									@endif
								@else
									<em>{{ trans('site.no-document-uploaded') }}</em>
								@endif
							</p>

								<button type="button" class="btn btn-primary btn-sm margin-top setGradeBtn" data-toggle="modal" data-target="#setGradeModal" data-action="{{ route('assignment.group.set_grade', $manuscript->id) }}" data-grade="{{ $manuscript->grade }}">{{ trans('site.set-grade') }}</button>
								<button class="btn btn-success btn-sm margin-top setFeedbackLearnerBtn" data-toggle="modal" data-target="#setFeedbackLearnerModal"
								data-action="{{ route('admin.assignment.group.learner.set-feedback-to-other',
								['group_id' => $group->id, 'group_learner_id' => $learner->id]) }}"
								data-get-learners="{{ route('learner.assignment.group.get-feedback-to-other-learners',
								 ['group_id' => $group->id, 'group_learner_id' => $learner->id]) }}">
									Allow Feedback to learners
								</button>
							<br>
								<?php $feedback = App\AssignmentFeedback::where('assignment_group_learner_id', $learner->id)->where('user_id', Auth::user()->id)->where('is_admin', 1)->first(); ?>
								@if( $feedback )
								<button type="button" class="btn btn-warning btn-sm margin-top disabled">
									{{--Feedback submitted as Admin--}}{{ trans('site.finished') }}</button> <br />
								@else
								<button type="button" class="btn btn-warning btn-sm margin-top submitFeedbackBtn" data-toggle="modal" data-target="#submitFeedbackModal" data-name="{{ $learner->user->full_name }}" data-action="{{ route('admin.assignment.group.submit_feedback', ['group_id' => $group->id, 'id' => $learner->id]) }}"
								data-manuscript="{{ $manuscript->id }}">{{ trans('site.submit-feedback-as-admin') }}</button>
								@endif
								<button type="button" class="btn btn-info btn-sm margin-top submitFeedbackBtnLearner" data-toggle="modal" data-target="#submitFeedbackLearnerModal" data-name="{{ $learner->user->full_name }}" data-action="{{ route('admin.assignment.group.submit_feedback_learner', ['group_id' => $group->id, 'id' => $learner->id]) }}" data-learner_id="{{ $learner->user->id }}">{{ trans('site.submit-feedback-as-learner') }}</button>
						</div>
					</div>
				</div>
				@endforeach
			</div>

			<?php 
			$groupLearners = $group->learners->pluck('id')->toArray();
			$feedbacks = App\AssignmentFeedback::whereIn('assignment_group_learner_id', $groupLearners)->orderBy('created_at', 'desc')->get(); 
			?>
			@if( $feedbacks->count() > 0 )
			<br />
			<h3>{{ trans_choice('site.feedbacks', 2) }}
				<a href="{{ route('assignment.group.download_all', ['course_id' => $course->id, 'assignment_id' => $assignment->id, 'group_id' => $group->id]) }}" class="pull-right btn btn-primary btn-sm">
					{{ trans('site.download-all') }}
				</a>
				<button type="button" class="pull-right btn btn-info btn-sm margin-right-5 updateGroupAvailabilityBtn"
						data-toggle="modal" data-target="#updateGroupAvailabilityModal" data-availability="{{ $group->availability }}"
				data-action="{{ route('assignment.group.feedback-availability', ['course_id' => $course->id, 'assignment_id' => $assignment->id, 'group_id' => $group->id]) }}">
					{{ trans('site.availability') }}
				</button>
			</h3>
			<div class="table-responsive">
				<table class="table table-bordered" style="background-color: #fff">
					<thead>
						<th>{{ trans_choice('site.feedbacks', 1) }}</th>
						<th>{{ trans('site.submitted-by') }}</th>
						<th>{{ trans('site.submitted-to') }}</th>
						<th>{{ trans('site.availability') }}</th>
						<th></th>
					</thead>
					<tbody>
						@foreach( $feedbacks as $feedback )
						<tr>
							<td>
                                <?php

								$files = explode(',',$feedback->filename);
                                $filesDisplay = '';

                                foreach ($files as $file) {
                                    $extension = explode('.', basename($file));

                                    if (end($extension) == 'pdf' || end($extension) == 'odt') {
                                        $filesDisplay .= '<a href="/js/ViewerJS/#../..'.trim($file).'">'.basename($file).'</a>, ';
									} else {
                                        $filesDisplay .= '<a href="https://view.officeapps.live.com/op/embed.aspx?src='.url('').trim($file).'">'.basename($file).'</a>, ';
									}
								}

                                echo trim($filesDisplay, ', ');

                                ?>
							</td>
							<td>@if( $feedback->is_admin ) [Admin] @endif {{ basename($feedback->user['full_name']) }}</td>
							<td>{{ basename($feedback->assignment_group_learner->user->full_name) }}</td>
							<td>{{ $feedback->availability }}</td>
							<td>
								<div class="text-right">
									<a href="{{ route('assignment.feedback.download_manuscript', $feedback->id) }}" class="btn btn-primary btn-xs">{{ trans('site.download') }}</a>
									<input type="checkbox" data-toggle="toggle" data-on="Locked"
										   class="lock-toggle" data-off="Unlocked"
										   data-id="{{$feedback->id}}" data-size="mini" @if($feedback->locked) {{ 'checked' }} @endif>

									@if( !$feedback->is_active )
						        	<button type="button" class="btn btn-warning btn-xs approveFeedbackAdminBtn" data-toggle="modal" data-target="#approveFeedbackAdminModal" data-action="{{ route('admin.assignment.group.approve', $feedback->id) }}"><i class="fa fa-check"></i></button>
									@endif
									@if( $feedback->is_admin )
									<button type="button" class="btn btn-xs btn-info updateFeedbackAdminBtn" data-toggle="modal" data-target="#updateFeedbackAdminModalAdmin" data-availability="{{ $feedback->availability }}" data-action="{{ route('admin.assignment.group.update_feedback_admin', $feedback->id) }}"><i class="fa fa-pencil"></i></button>
									@else
									<button type="button" class="btn btn-xs btn-info updateFeedbackBtn" data-toggle="modal" data-target="#updateFeedbackModalAdmin" data-action="{{ route('admin.assignment.group.update_feedback', $feedback->id) }}" data-availability="{{ $feedback->availability }}"><i class="fa fa-pencil"></i></button>
									@endif
									<button type="button" class="btn btn-xs btn-danger removeFeedbackAdminBtn" data-toggle="modal" data-target="#removeFeedbackAdminModal" data-action="{{ route('admin.assignment.group.remove_feedback', $feedback->id) }}"><i class="fa fa-trash"></i></button>

								</div>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			@endif
		</div>
	</div>
	<div class="clearfix"></div>
</div>


<div id="approveFeedbackAdminModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">{{ trans('site.approve-feedback') }}</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="">
		      {{ csrf_field() }}
				{{ trans('site.approve-feedback-question') }}
		      <div class="text-right margin-top">
		      	<button type="submit" class="btn btn-warning">{{ trans('site.approve') }}</button>
		      </div>
		    </form>
		  </div>
		</div>
	</div>
</div>

<div id="setGradeModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.set-grade') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
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
</div>

<div id="setFeedbackLearnerModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Set Feedback to Learners</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group" style="max-height: 300px; overflow-y: scroll; margin-top: 10px">
						<label>Learners</label> <br>
						<div class="learner-container"></div>
					</div>
					<button type="submit" class="btn btn-primary pull-right">{{ trans('site.submit') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="updateFeedbackModalAdmin" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">{{ trans('site.edit-feedback') }}</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="" enctype="multipart/form-data">
		      	{{ csrf_field() }}
	      		<div class="form-group">
		      		<label>{{ trans_choice('site.manuscripts', 1) }}</label>
	  				<input type="file" class="form-control" name="filename" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
	  				* Godkjente fil formater er DOCX, PDF og ODT.
  				</div>

	      		<div class="form-group">
		      		<label>{{ trans('site.availability') }}</label>
		      		<input type="date" class="form-control" name="availability">
	      		</div>

		      	<div class="text-right margin-top">
		      		<button type="submit" class="btn btn-primary">{{ trans('site.save') }}</button>
		      	</div>
		    </form>
		  </div>
		</div>
	</div>
</div>

<div id="updateGroupAvailabilityModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.set-availability') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}

					<div class="form-group">
						<label>{{ trans('site.availability') }}</label>
						<input type="date" class="form-control" name="availability">
					</div>

					<div class="text-right margin-top">
						<button type="submit" class="btn btn-primary">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<div id="updateFeedbackAdminModalAdmin" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">{{ trans('site.edit-feedback') }}</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
		      	{{ csrf_field() }}
	      		<div class="form-group">
		      		<label>{{ trans_choice('site.manuscripts', 1) }}</label>
	  				<input type="file" class="form-control" name="filename" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
	  				* Accepted file formats are DOCX, PDF, ODT.
  				</div>

	      		<div class="form-group">
		      		<label>{{ trans('site.availability') }}</label>
		      		<input type="date" class="form-control" name="availability">
	      		</div>

		      	<div class="text-right margin-top">
		      		<button type="submit" class="btn btn-primary">{{ trans('site.save') }}</button>
		      	</div>
		    </form>
		  </div>
		</div>
	</div>
</div>


<div id="removeFeedbackAdminModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">{{ trans('site.delete-feedback') }}</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="" onsubmit="disableSubmit(this)">
		      {{ csrf_field() }}
				{{ trans('site.delete-feedback-question') }}
		      <div class="text-right margin-top">
		      	<button type="submit" class="btn btn-danger">{{ trans('site.delete') }}</button>
		      </div>
		    </form>
		  </div>
		</div>
	</div>
</div>


<div id="submitFeedbackLearnerModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">{{ trans('site.submit-feedback-to') }} <em></em></h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action=""  enctype="multipart/form-data" onsubmit="disableSubmit(this)">
		      	{{ csrf_field() }}
				{{ trans('site.submit-feedback-to-note') }} <br /><br />
		      	<div class="form-group">
		      		<label>{{ trans('site.submit-feedback-as') }}</label>
		      		<select name="learner_id" class="form-control selects2" required>
						<option value="" selected disabled>- Select learner -</option>
						@foreach( $group->learners as $learner )
						<option value="{{ $learner->user->id }}">{{ $learner->user->full_name }}</option>
						@endforeach
		      		</select>
		      	</div>
		      	<div class="form-group">
		      		<label>{{ trans_choice('site.manuscripts', 1) }}</label>
	      			<input type="file" class="form-control" required name="filename" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
	      			* Godkjente fil formater er DOCX, PDF og ODT.
      			</div>
		      	<div class="form-group">
		      		<label>{{ trans('site.available-date') }}</label>
		      		<input type="date" class="form-control" name="availability">
		      	</div>

		      	<button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.submit') }}</button>
		      	<div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>

<div id="submitFeedbackModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">{{ trans('site.submit-feedback-to') }} <em></em></h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action=""  enctype="multipart/form-data" onsubmit="disableSubmit(this)">
		      	{{ csrf_field() }}
		      	<div class="form-group">
		      		<label>{{ trans_choice('site.manuscripts', 1) }}</label>
      				<input type="file" class="form-control" required multiple name="filename[]" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
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
				<input type="hidden" name="manuscript_id">
		      	<button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.submit') }}</button>
		      	<div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>

<?php
		$findAssignment 			= \App\Assignment::find($assignment_id);
		$assignmentGroups 			= $findAssignment->groups;
		$assignmentGroupLearners	= [];
		$filteredLearners 			= [];

		foreach ($assignmentGroups as $assignmentGroup) {
            $assignmentGroupLearners[] = $assignmentGroup->learners->pluck('user_id')->toArray();
        }

        foreach ($assignmentGroupLearners as $a) {
		    foreach ($a as $b) {
                $filteredLearners[] = $b;
			}
		}

$groupLearners = $group->learners->pluck('user_id')->toArray();
$manuscriptUsers = $assignment->manuscripts->whereNotIn('user_id', $groupLearners)->whereNotIn('user_id', $filteredLearners)
->where('join_group', '=', 1); // added the join group field to filter if the user wants to join a group
?>
<div id="addLearnerModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">{{ trans('site.add-learner') }}</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="{{route('assignment.group.add_learner', ['course_id' => $course->id, 'assignment_id' => $assignment->id, 'id' => $group->id])}}"
				onsubmit="disableSubmit(this)">
		      {{ csrf_field() }}
		      <label>{{ trans('site.learner-submitted-manuscript-for-assignment') }}</label>
		      <select class="form-control select2s" name="user_id">
		      	<option disabled selected value="">- Search learner -</option>
		      	@foreach( $manuscriptUsers as $manuscriptUser )
					<?php
					  	$learnerGrade = $manuscriptUser->grade;
					  	if (!$manuscriptUser->grade) {
					  	    $user = \App\AssignmentManuscript::where('user_id', $manuscriptUser->user->id)
								->whereNotNull('grade')
								->orderBy('updated_at', 'desc')
								->first();

					  	    if ($user) {
                                $learnerGrade = $user->grade;
							}
						}
					  ?>
		      	<option value="{{ $manuscriptUser->user->id }}">{{ $manuscriptUser->user->full_name }} @if($learnerGrade) ({{ $learnerGrade }}) @endif</option>
		      	@endforeach
		      </select>
		      <button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.add') }}</button>
		      <div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>


<div id="removeLearnerModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">{{ trans('site.remove-learner') }}</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="" onsubmit="disableSubmit(this)">
		      {{ csrf_field() }}
				{{ trans('site.remove-learner-question-only') }}
		      <button type="submit" class="btn btn-danger pull-right margin-top">{{ trans('site.delete') }}</button>
		      <div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>

<div id="deleteGroupModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">{{ trans('site.delete-group') }}</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="{{route('admin.assignment-group.destroy', ['course_id' => $course->id, 'assignment_id' => $assignment->id, 'group' => $group->id])}}">
		      {{ csrf_field() }}
		      {{ method_field('DELETE') }}
				{{ trans('site.delete-group-question') }}
		      <br />
		      <button type="submit" class="btn btn-danger pull-right margin-top">{{ trans('site.delete') }}</button>
		      <div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>


<div id="editGroupModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">{{ trans('site.edit-group') }}</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="{{route('admin.assignment-group.update', ['course_id' => $course->id, 'assignment_id' => $assignment->id, 'group' => $group->id])}}">
		      {{ csrf_field() }}
		      {{ method_field('PUT') }}
		      <div class="form-group">
		      	<label>{{ trans('site.title') }}</label>
		      	<input type="text" class="form-control" name="title" placeholder="{{ trans('site.title') }}" required value="{{ $group->title }}">
		      </div>
				<div class="form-group">
					<label>{{ trans('site.submission-date') }}</label>
					<input type="datetime-local" class="form-control" name="submission_date"
						   @if( $group->submission_date ) value="{{ strftime('%Y-%m-%dT%H:%M:%S', strtotime($group->submission_date)) }}" @endif
						   required>
				</div>
				<div class="form-group">
					<label>{{ trans('site.allow-download-all-feedback') }}</label> <br>
					<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
						   data-id="{{$group->allow_feedback_download}}" @if($group->allow_feedback_download) {{ 'checked' }} @endif
					name="allow_feedback_download">
				</div>
		      <button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.save') }}</button>
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
	$('.approveFeedbackAdminBtn').click(function(){
		var modal = $('#approveFeedbackAdminModal');
		var action = $(this).data('action');
		modal.find('form').attr('action', action);
	});

	$('.updateFeedbackBtn').click(function(){
		var modal = $('#updateFeedbackModalAdmin');
		var action = $(this).data('action');
		var availability = $(this).data('availability');
        modal.find('input[name=availability]').val(availability);
		modal.find('form').attr('action', action);
	});

    $('.updateGroupAvailabilityBtn').click(function(){
        var modal = $('#updateGroupAvailabilityModal');
        var action = $(this).data('action');
        var availability = $(this).data('availability');
        modal.find('input[name=availability]').val(availability);
        modal.find('form').attr('action', action);
    });

	$('.updateFeedbackAdminBtn').click(function(){
		var modal = $('#updateFeedbackAdminModalAdmin');
		var availability = $(this).data('availability');
		var action = $(this).data('action');
		modal.find('input[name=availability]').val(availability);
		modal.find('form').attr('action', action);
	});

	$('.removeFeedbackAdminBtn').click(function(){
		var modal = $('#removeFeedbackAdminModal');
		var action = $(this).data('action');
		modal.find('form').attr('action', action);
	});
	

	$('.submitFeedbackBtnLearner').click(function(){
		var modal = $('#submitFeedbackLearnerModal');
		var name = $(this).data('name');
		var learner_id = $(this).data('learner_id');
		var action = $(this).data('action');
		modal.find('select option').prop('disabled', false);
		modal.find('select option[value='+learner_id+']').prop('disabled', true);
		modal.find('em').text(name);
		modal.find('form').attr('action', action);
	});

	$('.submitFeedbackBtn').click(function(){
		var modal = $('#submitFeedbackModal');
		var name = $(this).data('name');
		var action = $(this).data('action');
		var manuscript_id = $(this).data('manuscript');
		modal.find('em').text(name);
		modal.find('form').attr('action', action);
		modal.find('form').find('input[name=manuscript_id]').val(manuscript_id);
	});
	$('.removeLearnerBtn').click(function(){
		var form = $('#removeLearnerModal form');
		var action = $(this).data('action');
		form.attr('action', action)
	});

    $('.setGradeBtn').click(function(){
        var form = $('#setGradeModal form');
        var action = $(this).data('action');
        var grade = $(this).data('grade');
        form.find('input[name=grade]').val(grade);
        form.attr('action', action)
    });

    $(".setFeedbackLearnerBtn").click(function(){
        let form = $('#setFeedbackLearnerModal').find('form');
        let action = $(this).data('action');
        let getLearnersLink = $(this).data('get-learners');
        form.attr('action', action);
        form.find('.learner-container').empty();
        $.ajax({
            type: 'GET',
            url: getLearnersLink,
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            data: {},
            success: function (data) {
                let other_learners = data.other_learners;
                let container = form.find('.learner-container');
                let learnerList = '';
                console.log(other_learners);
                console.log(data.could_send_feedback_to);
                $.each(other_learners, function(k, v){
                    if (data.could_send_feedback_to.includes(v.id)) {
                        learnerList += '<input type="checkbox" name="learners[]" value="'+ v.id +'" checked>';
					} else {
                        learnerList += '<input type="checkbox" name="learners[]" value="'+ v.id +'">';
					}
                    learnerList += ' <label>' + v.user.full_name + '</label> <br>';
				});

                container.append(learnerList);
            }
        });
    });

    $(".lock-toggle").change(function(){
        var course_id = $(this).attr('data-id');
        var is_checked = $(this).prop('checked');
        var check_val = is_checked ? 1 : 0;
        $.ajax({
            type:'POST',
            url:'/feedback/lock-status',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: { "feedback_id" : course_id, 'locked' : check_val },
            success: function(data){
            }
        });
    });
</script>
@stop