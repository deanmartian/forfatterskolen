@extends('editor.layout')

@section('title')
<title>Dashboard &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
	<style>
		.panel {
			overflow-x: auto;
		}
	</style>
@stop

@section('content')
<div class="col-sm-12 dashboard-left">
	<div class="row">
		<div class="col-sm-12">

			<!-- My assigned manuscripts -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4>
								Assignments/Personal assignments
							</h4>
						</div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans_choice('site.manuscripts', 1) }}</th>
								<th>{{ trans('site.learner-id') }}</th>
								<th>Expected Finish</th>
								<th></th>
								<th>Feedback Status</th>
							</tr>
							</thead>
							<tbody>
							@foreach($assignedAssignmentManuscripts as $assignedManuscript)
                                <?php $extension = explode('.', basename($assignedManuscript->filename)); ?>
								<tr>
									<td>
										@if( end($extension) == 'pdf' || end($extension) == 'odt' )
											<a href="/js/ViewerJS/#../..{{ $assignedManuscript->filename }}">
												{{ basename($assignedManuscript->filename) }}
											</a>
										@elseif( end($extension) == 'docx' || end($extension) == 'doc' )
											<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$assignedManuscript->filename}}">
												{{ basename($assignedManuscript->filename) }}
											</a>
										@endif
									</td>
									<td>{{ $assignedManuscript->user->id }}</td>
									<td>
										{{ $assignedManuscript->expected_finish }}
									</td>
									<td>
										<a href="{{ $assignedManuscript->filename }}" class="btn btn-primary btn-xs"
										   download>
											{{ trans('site.download') }}
										</a>
									</td>
									<td>
										<div>
											@if($assignedManuscript->has_feedback)
												<span class="label label-default">Pending</span>
											@else
												<button class="btn btn-warning btn-xs d-block
												submitPersonalAssignmentFeedbackBtn"
														data-target="#submitPersonalAssignmentFeedbackModal"
														data-toggle="modal"
														data-name="{{ $assignedManuscript->user->id }}"
														data-action="{{ route('editor.assignment.group.manuscript-feedback-no-group',
																	['id' => $assignedManuscript->id,
																	'learner_id' => $assignedManuscript->user->id]) }}">
													+ {{ trans('site.add-feedback') }}
												</button>
											@endif
										</div>
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>

            <!-- Shop manuscripts -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>{{ trans_choice('site.shop-manuscripts', 2) }}</h4></div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans_choice('site.manuscripts', 1) }}</th>
								<th>{{ trans('site.genre') }}</th>
								<th>{{ trans('site.learner-id') }}</th>
								<th>Expected Finish</th>
								<th></th>
								<th>Feedback Status</th>
							</tr>
							</thead>
							<tbody>
							@foreach($assigned_shop_manuscripts as $shopManuscript)
								@if( $shopManuscript->status == 'Started' || $shopManuscript->status == 'Pending' )
									<tr>
										<td>{{$shopManuscript->shop_manuscript->title}}</td>
										<td>
											@if($shopManuscript->genre > 0)
												{{ \App\Http\FrontendHelpers::assignmentType($shopManuscript->genre) }}
											@endif
										</td>
										<td>{{ $shopManuscript->user->id }}</td>
										<td>{{ $shopManuscript->expected_finish }}</td>
										<td>
											<a href="{{ route('editor.backend.download_shop_manuscript', $shopManuscript->id) }}"
											   class="btn btn-primary btn-xs">{{ trans('site.download') }}</a> <br>
										</td>
										<td>
											@if($shopManuscript->status == 'Started')
											
												<button type="button" class="btn btn-warning btn-xs addShopManuscriptFeedback" data-toggle="modal"
													data-target="#addFeedbackModal"
												data-action="{{ route('editor.admin.shop-manuscript-taken-feedback.store',
												$shopManuscript->id) }}">+ {{ trans('site.add-feedback') }}</button>

											@elseif($shopManuscript->status == 'Pending')

												<span class="label label-default">Pending</span>

											@endif
										</td>
									
									</tr>
								@endif
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
            
            <!-- My Assignments -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>{{ trans('site.my-assignments') }}</h4></div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans_choice('site.courses', 1) }}</th>
								<th>{{ trans('site.learner-id') }}</th>
								<th></th>
								<th>Feedback Status</th>
							</tr>
							</thead>
							<tbody>
							@foreach ($assignedAssignments as $assignedAssignment)
								<tr>
									<td>
										@if($assignedAssignment->assignment->course)
												{{ $assignedAssignment->assignment->course->title }}
										@else
												{{ $assignedAssignment->assignment->title }}
										@endif
									</td>
									<td>{{ $assignedAssignment->user_id }}</td>
									<td>
										<a href="{{ route('editor.backend.download_assigned_manuscript', $assignedAssignment->id) }}"
										   class="btn btn-primary btn-xs">{{ trans('site.download') }}</a>
									</td>
									<td>
									<?php
									if($assignedAssignment->has_feedback){
										echo '<span class="label label-default">Pending</span>';
									}else{
										// $groupDetails = DB::SELECT("SELECT s.`id`, s.`assignment_group_id` FROM `assignment_group_learners` s WHERE s.`user_id` = $assignedAssignment->user_id AND s.`assignment_group_id` = (SELECT `id` FROM assignment_groups g WHERE g.`assignment_id` = $assignedAssignment->assignment_id)");
										$groupDetails = DB::SELECT("SELECT A.id as assignment_group_id, B.id AS assignment_group_learner_id FROM assignment_groups A JOIN assignment_group_learners B ON A.id = B.assignment_group_id AND B.user_id = $assignedAssignment->user_id WHERE A.assignment_id = $assignedAssignment->assignment_id");
										if($groupDetails){ // Means the course assignment belongs to a group
											echo '<button type="button" class="btn btn-warning btn-xs submitFeedbackBtn"
														data-toggle="modal" data-target="#submitFeedbackModal"
														data-name="'.$assignedAssignment->user->id.'"
														data-action="'.route('editor.assignment.group.submit_feedback',
														['group_id' => $groupDetails[0]->assignment_group_id, 'id' => $groupDetails[0]->assignment_group_learner_id]).'"
														data-manuscript="'.$assignedAssignment->id.'">'.
													 '+ '.trans('site.add-feedback').'</button>';
										}else{ //the course assignment does not belong to a group
											echo '<button type="button" class="btn btn-warning btn-xs submitFeedbackBtn"
														data-toggle="modal" data-target="#submitFeedbackModal"
														data-name="'.$assignedAssignment->user->id.'"
														data-action="'.route('editor.assignment.group.manuscript-feedback-no-group',
														['id' => $assignedAssignment->id, 'learner_id' => $assignedAssignment->user_id]).'"
														data-manuscript="'.$assignedAssignment->id.'">'.
														'+ '.trans('site.add-feedback').'</button>';
										}
									}
									?>
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- My coaching timer -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>{{ trans('site.my-coaching-timer') }}</h4></div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans('site.learner-id') }}</th>
								<th>{{ trans('site.approved-date') }}</th>
								<th>{{ trans('site.session-length') }}</th>
							</tr>
							</thead>
							<tbody>
							@foreach($coachingTimers as $coachingTimer)
                                <?php $extension = explode('.', basename($coachingTimer->file)); ?>
								<tr>
									<td>
										<a href="{{ route('admin.learner.show', $coachingTimer->user->id) }}">
											{{ $coachingTimer->user->id }}
										</a>

										@if ($coachingTimer->help_with)
											<br>
											<a href="#viewHelpWithModal" style="color:#eea236" class="viewHelpWithBtn"
											   data-toggle="modal" data-details="{{ $coachingTimer->help_with }}">
												{{ trans('site.view-help-with') }}
											</a>
										@endif
									</td>
									<td>
										{{ $coachingTimer->approved_date ?
                                        \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($coachingTimer->approved_date)
                                         : ''}}
									</td>
									<td>
										{{ \App\Http\FrontendHelpers::getCoachingTimerPlanType($coachingTimer->plan_type) }}
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- end My coaching timer -->

			<!-- My corrections -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>{{ trans('site.my-correction') }}</h4></div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans_choice('site.manus', 2) }}</th>
								<th>{{ trans('site.learner-id') }}</th>
								<th>{{ trans('site.expected-finish') }}</th>
								<th></th>
								<th>Feedback Status</th>
							</tr>
							</thead>
							<tbody>
							@foreach($corrections as $correction)
                                <?php $extension = explode('.', basename($correction->file)); ?>
								<tr>
									<td>
										@if( end($extension) == 'pdf' || end($extension) == 'odt' )
											<a href="/js/ViewerJS/#../../{{ $correction->file }}">{{ basename($correction->file) }}</a>
										@elseif( end($extension) == 'docx' )
											<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$correction->file}}">{{ basename($correction->file) }}</a>
										@endif
									</td>
									<td>{{ $correction->user->id }}</td>
									<td>
										@if ($correction->expected_finish)
											{{ \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($correction->expected_finish) }}
											<br>
										@endif

										<!-- @if ($correction->status !== 2)
											<a href="#setOtherServiceFinishDateModal" data-toggle="modal"
											   class="setOtherServiceFinishDateBtn"
											   data-action="{{ route('admin.other-service.update-expected-finish',
										   ['id' => $correction->id, 'type' => 2]) }}"
											   data-finish="{{ $correction->expected_finish ?
										strftime('%Y-%m-%dT%H:%M:%S', strtotime($correction->expected_finish)) : '' }}">
												{{ trans('site.set-date') }}
											</a>
										@endif -->
									</td>
									<td>
										<a class="btn btn-primary btn-xs" href="{{ route('editor.other-service.download-doc',
										   ['id' => $correction->id, 'type' => 2]) }}">{{ trans('site.download') }}</a>
									</td>
									<td>
									
										<!-- show only if no feedback is given yet for this correction -->
										@if (!$correction->feedback)
											<a href="#addOtherServiceFeedbackModal" data-toggle="modal"
											class="btn btn-warning btn-xs addOtherServiceFeedbackBtn" data-service="2"
											data-action="{{ route('editor.other-service.add-feedback',
											['id' => $correction->id, 'type' => 2]) }}">+ {{ trans('site.add-feedback') }}</a>
										@else
											@if( $correction->status == 2 )
												<span class="label label-success">Finished</span>
											@elseif( $correction->status == 1 )
												<span class="label label-primary">Started</span>
											@elseif( $correction->status == 0 )
												<span class="label label-warning">Not started</span>
											@elseif( $correction->status == 3 )
											<span class="label label-default">Pending</span>
											@endif
										@endif
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- end My corrections -->

			<!-- My Copy Editing -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>{{ trans('site.my-copy-editing') }}</h4></div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans_choice('site.manus', 2) }}</th>
								<th>{{ trans('site.learner-id') }}</th>
								<th>{{ trans('site.expected-finish') }}</th>
								<th></th>
								<th>Feedback Status</th>
							</tr>
							</thead>
							<tbody>
							@foreach($copyEditings as $copyEditing)
                                <?php $extension = explode('.', basename($copyEditing->file)); ?>
								<tr>
									<td>
										@if( end($extension) == 'pdf' || end($copyEditing) == 'odt' )
											<a href="/js/ViewerJS/#../../{{ $copyEditing->file }}">{{ basename($copyEditing->file) }}</a>
										@elseif( end($extension) == 'docx' )
											<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$copyEditing->file}}">{{ basename($copyEditing->file) }}</a>
										@endif
									</td>
									<td>{{ $copyEditing->user->id }}</td>
									<td>
										@if ($copyEditing->expected_finish)
											{{ \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($copyEditing->expected_finish) }}
											<br>
										@endif

										<!-- @if ($copyEditing->status !== 2)
											<a href="#setOtherServiceFinishDateModal" data-toggle="modal"
											   class="setOtherServiceFinishDateBtn"
											   data-action="{{ route('admin.other-service.update-expected-finish',
										   ['id' => $copyEditing->id, 'type' => 2]) }}"
											   data-finish="{{ $copyEditing->expected_finish ?
										strftime('%Y-%m-%dT%H:%M:%S', strtotime($copyEditing->expected_finish)) : '' }}">
												{{ trans('site.set-date') }}
											</a>
										@endif -->
									</td>
									<td>
										<a class="btn btn-primary btn-xs" href="{{ route('editor.other-service.download-doc',
										   ['id' => $copyEditing->id, 'type' => 1]) }}">{{ trans('site.download') }}</a>
									</td>
									<td>
										<!-- show only if no feedback is given yet for this copyEditing -->
										@if (!$copyEditing->feedback)
											<a href="#addOtherServiceFeedbackModal" data-toggle="modal" class="btn btn-warning btn-xs addOtherServiceFeedbackBtn" data-service="1"
											   data-action="{{ route('editor.other-service.add-feedback',
											['id' => $copyEditing->id, 'type' => 1]) }}">+ {{ trans('site.add-feedback') }}</a>
										@else
											@if( $copyEditing->status == 2 )
												<span class="label label-success">Finished</span>
											@elseif( $copyEditing->status == 1 )
												<span class="label label-primary">Started</span>
											@elseif( $copyEditing->status == 0 )
												<span class="label label-warning">Not started</span>
											@elseif( $copyEditing->status == 3 )
												<span class="label label-default">Pending</span>
											@endif
										@endif
										
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- end My Copy Editing -->

		</div>
	</div>
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

<div id="removeFeedbackAdminModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">{{ trans('site.delete-feedback') }}</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="">
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

<div id="viewManuscriptModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-body">
		  	<p>
		  		<strong>{{ trans('site.name') }}:</strong><br />
		  		<span id="name"></span><br />
		  		<br />
		  		<strong>{{ trans_choice('site.emails', 1) }}:</strong><br />
		  		<span id="email"></span><br />
		  		<br />
		  		<strong>{{ trans_choice('site.manuscripts', 1) }}:</strong><br />
		  		<span id="content"></span>
		  	</p>
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
				<form method="POST" action=""  enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans_choice('site.manuscripts', 1) }}</label>
						<input type="file" class="form-control" required multiple name="filename[]" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
						* Accepted file formats are DOCX, PDF, ODT.
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

<div id="addFeedbackModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.add-feedback') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" enctype="multipart/form-data">
					<?php
						$emailTemplate = \App\Http\AdminHelpers::emailTemplate('Shop Manuscript Feedback');
					?>
					{{csrf_field()}}
					<div class="form-group">
						<label>{{ trans_choice('site.files', 2) }}</label>
						<input type="file" class="form-control" name="files[]" multiple
							   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
							   application/pdf, application/vnd.oasis.opendocument.text" required>
					</div>
					<div class="form-group">
						<label>{{ trans_choice('site.notes', 2) }}</label>
						<textarea class="form-control" name="notes" rows="6"></textarea>
					</div>
					{{ trans('site.add-feedback-note') }}
					<button type="submit" class="btn btn-primary pull-right">{{ trans('site.add-feedback') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="finishAssignmentModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.finish-assignment') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="">
					{{csrf_field()}}
					{{ trans('site.finish-assignment-question') }}
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-success">{{ trans('site.submit') }}</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('site.cancel') }}</button>
					</div>
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
						<label>{{ trans('site.assign-editor') }}</label>
						<select name="editor_id" class="form-control select2" required>
							<option value="" disabled="" selected>-- Select Editor --</option>
							@foreach( App\User::whereIn('role', array(1,3))->orderBy('created_at', 'desc')->get() as $editor )
								<option value="{{ $editor->id }}">{{ $editor->full_name }}</option>
							@endforeach
						</select>
					</div>
					<div class="text-right">
						<button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="pendingAssignmentEditorModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.assign-editor') }}</label>
						<select name="editor_id" class="form-control select2" required>
							<option value="" disabled="" selected>-- Select Editor --</option>
							@foreach( App\User::whereIn('role', array(1,3))->orderBy('created_at', 'desc')->get() as $editor )
								<option value="{{ $editor->id }}">{{ $editor->full_name }}</option>
							@endforeach
						</select>

						<div class="hidden-container">
							<label>
							</label>
							<a href="javascript:void(0)" onclick="enableSelect('pendingAssignmentEditorModal')">Edit</a>
						</div>
					</div>
					<div class="form-group">
						<label>Expected Finish</label>
						<input type="date" class="form-control" name="expected_finish">
					</div>
					<div class="text-right">
						<button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
					</div>
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
				<h4 class="modal-title">{!! str_replace('_SERVICE_','<span></span>',trans('site.update-service-status')) !!}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<p>
						{{ trans('site.update-service-status-question') }}
					</p>
					<div class="text-right">
						<button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
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
				<h4 class="modal-title"><span></span> {{ trans('site.expected-finish') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.expected-finish-date') }}</label>
						<input type="datetime-local" name="expected_finish" class="form-control" required>
					</div>
					<div class="text-right">
						<button class="btn btn-primary" type="submit">{{ trans('site.submit') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

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
					<?php
					$emailTemplate = \App\Http\AdminHelpers::emailTemplate('Other Services Feedback');
					?>
                    <div class="form-group">
                        <label>{{ trans_choice('site.manuscripts', 1) }}</label>
                        <input type="file" class="form-control" name="manuscript" multiple accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf" required>
                    </div>
                    <button type="submit" class="btn btn-primary pull-right">{{ trans('site.add-feedback') }}</button>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>

    </div>
</div>

<div id="approveCoachingSessionModal" class="modal fade" role="dialog"  data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.approve-coaching-timer') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<p>{{ trans('site.approve-coaching-timer-question') }}</p>
					<div class="text-right">
						<button class="btn btn-primary" type="submit">{{ trans('site.approve') }}</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('site.cancel') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="viewHelpWithModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Help With</h4>
			</div>
			<div class="modal-body">
				<pre></pre>
			</div>
		</div>
	</div>
</div>

<div id="finishTaskModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Finish Task</h4>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action=""
					  onsubmit="disableSubmit(this)">
					{{ csrf_field() }}

					<p>Are you sure to finish this task?</p>

					<button type="submit" class="btn btn-success pull-right">Finish</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="editTaskModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Edit Task</h4>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action=""
					  onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					{{ method_field('PUT') }}
					<input type="hidden" name="user_id" value="">

					<div class="form-group">
						<label>
							Task
						</label>
						<textarea name="task" cols="30" rows="10" class="form-control" required></textarea>
					</div>

					<div class="form-group">
						<label>
							{{ trans('site.assign-to') }}
						</label>
						<select name="assigned_to" class="form-control select2" required>
							<option value="" disabled="" selected>-- Select Assignee --</option>
							@foreach( App\User::whereIn('role', array(1,3))->orderBy('created_at', 'desc')->get() as $editor )
								<option value="{{ $editor->id }}">{{ $editor->full_name }}</option>
							@endforeach
						</select>
					</div>

					<button type="submit" class="btn btn-primary pull-right">Update Task</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="deleteTaskModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Delete Task</h4>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action=""
					  onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					{{ method_field('DELETE') }}

					<p>Are you sure to delete this task?</p>

					<button type="submit" class="btn btn-danger pull-right">Delete</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="submitPersonalAssignmentFeedbackModal" class="modal fade" role="dialog">
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
                        <label>{{ trans('site.grade') }}</label>
                        <input type="number" class="form-control" step="0.01" name="grade">
                    </div>
                    <button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.submit') }}</button>
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
	$('.viewManuscriptBtn').click(function(){
		var fields = $(this).data('fields');
		var modal = $('#viewManuscriptModal');
		modal.find('#name').text(fields.name);
		modal.find('#email').text(fields.email);
		modal.find('#content').text(fields.content);
	});
	$('.approveFeedbackAdminBtn').click(function(){
		var modal = $('#approveFeedbackAdminModal');
		var action = $(this).data('action');
		modal.find('form').attr('action', action);
	});
	$('.removeFeedbackAdminBtn').click(function(){
		var modal = $('#removeFeedbackAdminModal');
		var action = $(this).data('action');
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

    $(".addShopManuscriptFeedback").click(function(){
        var modal = $('#addFeedbackModal');
        var action = $(this).data('action');
        modal.find('form').attr('action', action);
	});

    $(".finishAssignmentBtn").click(function(){
        let modal = $('#finishAssignmentModal');
        let action = $(this).data('action');
        modal.find('form').attr('action', action);
	});

    $('.assignEditorBtn').click(function(){
        let action = $(this).data('action');
        let editor = $(this).data('editor');
        let modal = $('#assignEditorModal');
        modal.find('select').val(editor);
        modal.find('form').attr('action', action);
    });

    $(".pendingAssignmentEditorBtn").click(function(){
        let action = $(this).data('action');
        let editor = $(this).data('editor');
        let preferred_editor = $(this).data('preferred-editor');
        let preferred_editor_name = $(this).data('preferred-editor-name');
        let modal = $('#pendingAssignmentEditorModal');
        modal.find('select').val(preferred_editor).trigger('change');
        modal.find('form').attr('action', action);

        if (preferred_editor) {
            modal.find('.select2').hide();
            modal.find('.hidden-container').show();
            modal.find('.hidden-container').find('label').empty().text(preferred_editor_name);
        } else {
            modal.find('.select2').show();
            modal.find('.hidden-container').hide();
        }
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
        let title = 'Korrektur';

        if (service === 1) {
            title = 'Språkvask';
        }
        modal.find('form').attr('action', action);
        modal.find('.modal-title').find('span').text(title);
    });

    $(".approveCoachingSessionBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#approveCoachingSessionModal');
        modal.find('form').attr('action', action);
	});

    $(".viewHelpWithBtn").click(function(){
       let details = $(this).data('details');
       let modal = $("#viewHelpWithModal");

       modal.find('.modal-body').find('pre').text(details);
	});

    $(".is-manuscript-locked-toggle").change(function(){
        let shopManuscriptTakenId = $(this).attr('data-id');
        let is_checked = $(this).prop('checked');
        let check_val = is_checked ? 1 : 0;
        $.ajax({
            type:'POST',
            url:'/is-manuscript-locked-status',
            data: { "shop_manuscript_taken_id" : shopManuscriptTakenId, 'is_manuscript_locked' : check_val },
            success: function(data){
            }
        });
    });

    $(".finishTaskBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#finishTaskModal');
        modal.find('form').attr('action', action);
    });

    $(".editTaskBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#editTaskModal');
        let fields = $(this).data('fields');
        modal.find('form').attr('action', action);
        modal.find('[name=task]').text(fields.task);
        modal.find('[name=user_id]').val(fields.user_id);
        modal.find('form').find('[name=assigned_to]').val(fields.assigned_to).trigger('change');
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

    $(".deleteTaskBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#deleteTaskModal');
        modal.find('form').attr('action', action);
    });

    $('.submitPersonalAssignmentFeedbackBtn').click(function(){
        let modal = $('#submitPersonalAssignmentFeedbackModal');
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

    function disableSubmit(t) {
        let submit_btn = $(t).find('[type=submit]');
        submit_btn.text('');
        submit_btn.append('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
        submit_btn.attr('disabled', 'disabled');
    }

</script>
@stop