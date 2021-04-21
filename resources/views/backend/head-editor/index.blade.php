@extends('backend.layout')

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
								{{ trans('site.personal-assignment') }}
                            </h4>
                        </div>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>{{ trans_choice('site.manuscripts', 1) }}</th>
                                <th>{{ trans_choice('site.learners', 1) }}</th>
                                <th>{{ trans('site.expected-finish') }}</th>
                                <th>{{ trans_choice('site.editors', 1) }}</th>
                                <th>{{ trans_choice('site.feedbacks', 1) }}</th>
                                <th>{{ trans('site.feedback-status') }}</th>
								<th>{{ trans_choice('site.notes', 2) }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($assignedAssignmentManuscripts as $assignedManuscript)
                                <?php $extension = explode('.', basename($assignedManuscript->filename)); ?>
                                <tr>
                                    <td>
									<a href="{{ $assignedManuscript->filename }}" download><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp;
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
                                    <td>{{ $assignedManuscript->user->full_name }}</td>
                                    <td>
                                        {{ $assignedManuscript->expected_finish }}
                                    </td>
                                    <td>{{ $assignedManuscript->editor->full_name}}</td>
                                    <td>
									@if($assignedManuscript->noGroupFeedbacks->first())
										<!-- <button class="btn btn-success btn-xs">
											<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
										</button> -->
                                        <button class="btn btn-success btn-xs personalAssignmentShowFeedbackBtn"
                                                data-target="#personalAssignmentShowFeedbackModal"
                                                data-toggle="modal"
                                                data-id = "{{$assignedManuscript->id}}"
												data-feedback_id = "{{ $assignedManuscript->noGroupFeedbacks->first()->id }}"
                                                data-feedback_file = "{{$assignedManuscript->noGroupFeedbacks->first()->filename}}"
                                                data-feedback_grade = "{{$assignedManuscript->grade}}"
                                                data-action="{{ route('head_editor.personal_assignment.feedbac_approve',
																['id' => $assignedManuscript->id,
																'learner_id' => $assignedManuscript->user->id]) }}">
                                                {{ trans('site.approve-feedback') }}
                                        </button> &nbsp;
										<?php $files = explode(',',$assignedManuscript->noGroupFeedbacks->first()->filename); ?>
										@foreach($files as $file)
											<a href="{{ $file }}" download><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp;
										@endforeach
										
									@endif
                                    </td>
                                    <td>
                                        <div>
                                            <span class="label label-default">{{ trans('site.pending') }}</span>
                                        </div>
                                    </td>
									<td>
										@if($assignedManuscript->noGroupFeedbacks->first()->notes_to_head_editor)
										<a class="notes" data-target="#notesModal" data-toggle="modal" data-notes="{{ $assignedManuscript->noGroupFeedbacks->first()->notes_to_head_editor }}">
											{{ substr($assignedManuscript->noGroupFeedbacks->first()->notes_to_head_editor, 0, 10) }}
											<i class="fa fa-file-text-o" aria-hidden="true"></i>
                                        </a>
										@endif
									</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Shop Manuscripts -->
            <div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>{{ trans_choice('site.shop-manuscripts', 2) }}</h4></div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans_choice('site.manuscripts', 1) }}</th>
								<th>{{ trans('site.genre') }}</th>
								<th>{{ trans_choice('site.learners', 1) }}</th>
								<th>{{ trans('site.learner.expected-finish') }}</th>
                                <th>{{ trans_choice('site.editors', 1) }}</th>
                                <th>{{ trans_choice('site.feedbacks', 1) }}</th>
								<th>{{ trans('site.feedback-status') }}</th>
								<th>{{ trans_choice('site.notes', 2) }}</th>
							</tr>
							</thead>
							<tbody>
							@foreach($assigned_shop_manuscripts as $shopManuscript)
								@if( $shopManuscript->status == 'Started' || $shopManuscript->status == 'Pending' )
									<tr>
										<td>
										<a href="{{ route('editor.backend.download_shop_manuscript', $shopManuscript->id) }}" download><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp;
										{{$shopManuscript->shop_manuscript->title}}
										</td>
										<td>
											@if($shopManuscript->genre > 0)
												{{ \App\Http\FrontendHelpers::assignmentType($shopManuscript->genre) }}
											@endif
										</td>
										<td>{{ $shopManuscript->user->full_name }}</td>
										<td>{{ $shopManuscript->expected_finish }}</td>
                                        <td>{{ $shopManuscript->admin->full_name }}</td>
                                        <td>
											<?php

												$feedbackFile = implode(",",$shopManuscript->feedbacks->first()->filename);

											?>
                                            <button class="btn btn-success btn-xs shopManuscriptShowFeedbackBtn"
                                                    data-target="#shopManuscriptShowFeedbackModal"
                                                    data-toggle="modal"
                                                    data-feedback_file = "{{$feedbackFile}}"
                                                    data-feedback_notes = "{{$shopManuscript->feedbacks->first()->notes}}"
                                                    data-action="{{ route('head_editor.shop-manuscript-taken-feedback.approve', 
														['id' => $shopManuscript->id,
														'learner_id' => $shopManuscript->user->id,
														'feedback_id' => $shopManuscript->feedbacks->first()->id]) }}">
                                                    {{ trans('site.approve-feedback') }}
                                            </button> &nbsp;
											<?php $files = $shopManuscript->feedbacks->first()->filename; ?>
											@foreach($files as $file)
												<a href="{{ $file }}" download><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp;
											@endforeach
                                        </td>
										<td><span class="label label-default">{{ trans('site.pending') }}</span></td>
										<td>
											@if($shopManuscript->feedbacks->first()->notes_to_head_editor)
											<a class="notes" data-target="#notesModal" data-toggle="modal" data-notes="{{ $shopManuscript->feedbacks->first()->notes_to_head_editor }}">
												{{ substr($shopManuscript->feedbacks->first()->notes_to_head_editor, 0, 10) }}
												<i class="fa fa-file-text-o" aria-hidden="true"></i>
											</a>
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
								<th>{{ trans_choice('site.learners', 1) }}</th>
								<th>{{ trans('site.expected-finish') }}</th>
								<th>{{ trans_choice('site.editors', 1) }}</th>
								<th>{{ trans_choice('site.feedbacks', 1) }}</th>
								<th>{{ trans('site.feedback-status') }}</th>
								<th>{{ trans_choice('site.notes', 2) }}</th>
							</tr>
							</thead>
							<tbody>
							@foreach ($assignedAssignments as $assignedAssignment)
								<tr>
									<td>
										<a href="{{ route('editor.backend.download_assigned_manuscript', $assignedAssignment->id) }}" download><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp;
										@if($assignedAssignment->assignment->course)
												{{ $assignedAssignment->assignment->course->title }}
										@else
												{{ $assignedAssignment->assignment->title }}
										@endif
									</td>
									<td>{{ $assignedAssignment->user->full_name }}</td>
									<td>{{$assignedAssignment->expected_finish}}</td>
									<td>{{$assignedAssignment->editor->full_name}}</td>
									<?php 
										// echo $assignedAssignment->user_id.' '.
										$groupDetails = DB::SELECT("SELECT A.id as assignment_group_id, B.id AS assignment_group_learner_id FROM assignment_groups A JOIN assignment_group_learners B ON A.id = B.assignment_group_id AND B.user_id = $assignedAssignment->user_id WHERE A.assignment_id = $assignedAssignment->assignment_id");
										if($groupDetails){ // Means the course assignment belongs to a group
											$feedback = DB::SELECT("SELECT A.* FROM assignment_feedbacks A JOIN assignment_group_learners B ON A.assignment_group_learner_id = B.id WHERE B.user_id = $assignedAssignment->user_id AND A.assignment_group_learner_id = ".$groupDetails[0]->assignment_group_learner_id);
											echo '<td>';
											echo '<button class="btn btn-success btn-xs courseAssignmentShowFeedbackBtn"
															data-target="#courseAssignmentShowFeedbackModal"
															data-toggle="modal"
															data-id = "'.$assignedAssignment->id.'"
															data-feedback_file = "'.$feedback[0]->filename.'"
															data-feedback_grade = "'.$assignedAssignment->grade.'"
															data-action="'.route('head_editor.course_assignment.feedback_approve',
																			['id' => $assignedAssignment->id,
																			'learner_id' => $assignedAssignment->user->id,
																			'feedback_id' => $feedback[0]->id]).'">
															'. trans('site.approve-feedback') .'
													</button> &nbsp';

													$files = explode(',',$feedback[0]->filename);
													foreach($files as $file){
														echo '<a href="'.$file.'" download><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp';
													}
											echo $feedback[0]->created_at;
											echo '</td>';
											echo '<td> <span class="label label-default">'.trans('site.pending').'</span> </td>';
											echo '<td>';
											if($feedback[0]->notes_to_head_editor){
												echo '<a class="notes" data-target="#notesModal" data-toggle="modal" data-notes="'.$feedback[0]->notes_to_head_editor.'">
												'.substr($feedback[0]->notes_to_head_editor, 0, 10).'
												<i class="fa fa-file-text-o" aria-hidden="true"></i>
											</a>';
											}
											echo '</td>';
										}else{ //the course assignment does not belong to a group
											echo '<td>';
											echo '<button class="btn btn-success btn-xs personalAssignmentShowFeedbackBtn"
															data-target="#personalAssignmentShowFeedbackModal"
															data-toggle="modal"
															data-id = "'.$assignedAssignment->id.'"
															data-feedback_file = "'.$assignedAssignment->noGroupFeedbacks->first()->filename.'"
															data-feedback_grade = "'.$assignedAssignment->grade.'"
															data-feedback_id = "'.$assignedAssignment->noGroupFeedbacks->first()->id.'"
															data-action="'. route('head_editor.personal_assignment.feedbac_approve',
																			['id' => $assignedAssignment->id,
																			'learner_id' => $assignedAssignment->user->id]) .'">
															'. trans('site.approve-feedback') .'
													</button> &nbsp';
											$files = explode(',',$assignedAssignment->noGroupFeedbacks->first()->filename);
											foreach($files as $file){
												echo '<a href="'.$file.'" download><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp';
											}
                                            echo $assignedAssignment->noGroupFeedbacks->first()->created_at;
											echo '</td>';
											echo '<td> <span class="label label-default">'.trans('site.pending').'</span> </td>';
											echo '<td>';
											if($assignedAssignment->noGroupFeedbacks->first()->notes_to_head_editor){
												echo '<a class="notes" data-target="#notesModal" data-toggle="modal" data-notes="'.$assignedAssignment->noGroupFeedbacks->first()->notes_to_head_editor.'">
												'.substr($assignedAssignment->noGroupFeedbacks->first()->notes_to_head_editor, 0, 10).'
												<i class="fa fa-file-text-o" aria-hidden="true"></i>
											</a>';
											}
											echo '</td>';
										}

									?>
									
									
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>

            <!-- My Coaching Timer -->
			<!-- <div class="row">
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
							<tr>
									<td></td>
									<td></td>
									<td></td>
							</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div> -->

            <!-- My Correction -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>{{ trans('site.my-correction') }}</h4></div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans_choice('site.manus', 2) }}</th>
								<th>{{ trans_choice('site.learners', 1) }}</th>
								<th>{{ trans('site.expected-finish') }}</th>
								<th>{{ trans_choice('site.editors', 1) }}</th>
								<th>{{ trans_choice('site.feedbacks', 1) }}</th>
								<th>{{ trans('site.feedback-status') }}</th>
								<th>{{ trans_choice('site.notes', 2) }}</th>
							</tr>
							</thead>
							<tbody>
							@foreach($corrections as $correction)
                                <?php $extension = explode('.', basename($correction->file)); ?>
								<tr>
									<td>
										<a href="{{ route('editor.other-service.download-doc', ['id' => $correction->id, 'type' => 2]) }}" download><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp;
										@if( end($extension) == 'pdf' || end($extension) == 'odt' )
											<a href="/js/ViewerJS/#../../{{ $correction->file }}">{{ basename($correction->file) }}</a>
										@elseif( end($extension) == 'docx' )
											<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$correction->file}}">{{ basename($correction->file) }}</a>
										@endif
									</td>
									<td>{{ $correction->user->full_name }}</td>
									<td>
										@if ($correction->expected_finish)
											{{ \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($correction->expected_finish) }}
											<br>
										@endif
									</td>
									<td>{{ $correction->editor->full_name }}</td>
									<td>
										<a href="#approveOtherServiceFeedbackModal" data-toggle="modal"
											class="btn btn-success btn-xs approveOtherServiceFeedbackBtn " 
											data-service="2"
											data-feedback_id = "{{ $correction->feedback->id }}"
											data-feedback_file = "{{ $correction->feedback->manuscript }}"
											data-action="{{ route('head_editor.other-service.approve-feedback',
											['id' => $correction->id, 'type' => 2]) }}">{{ trans('site.approve-feedback') }}</a> &nbsp;
											<?php $files = explode(',',$correction->feedback->manuscript); ?>
											@foreach($files as $file)
												<a href="{{ $file }}" download><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp;
											@endforeach
									</td>
									<td>
										@if( $correction->status == 2 )
											<span class="label label-success">{{ trans('site.finished') }}</span>
										@elseif( $correction->status == 1 )
											<span class="label label-primary">{{ trans('site.started') }}</span>
										@elseif( $correction->status == 0 )
											<span class="label label-warning">{{ trans('site.not-started') }}</span>
										@elseif( $correction->status == 3 )
										<span class="label label-default">{{ trans('site.pending') }}</span>
										@endif
									</td>
									<td>
										@if($correction->feedback->notes_to_head_editor)
											<a class="notes" data-target="#notesModal" data-toggle="modal" data-notes="{{ $correction->feedback->notes_to_head_editor }}">
												{{ substr($correction->feedback->notes_to_head_editor, 0, 10) }}
												<i class="fa fa-file-text-o" aria-hidden="true"></i>
											</a>
										@endif
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>

            <!-- My Copy Editing -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>{{ trans('site.my-copy-editing') }}</h4></div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans_choice('site.manus', 2) }}</th>
								<th>{{ trans_choice('site.learners', 1) }}</th>
								<th>{{ trans('site.expected-finish') }}</th>
								<th>{{ trans_choice('site.editors', 1) }}</th>
								<th>{{ trans_choice('site.feedbacks', 1) }}</th>
								<th>{{ trans('site.feedback-status') }}</th>
								<th>{{ trans_choice('site.notes', 2) }}</th>
							</tr>
							</thead>
							<tbody>
							@foreach($copyEditings as $copyEditing)
                                <?php $extension = explode('.', basename($copyEditing->file)); ?>
								<tr>
									<td>
										<a href="{{ route('editor.other-service.download-doc', ['id' => $copyEditing->id, 'type' => 1]) }}" download><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp;
										@if( end($extension) == 'pdf' || end($copyEditing) == 'odt' )
											<a href="/js/ViewerJS/#../../{{ $copyEditing->file }}">{{ basename($copyEditing->file) }}</a>
										@elseif( end($extension) == 'docx' )
											<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$copyEditing->file}}">{{ basename($copyEditing->file) }}</a>
										@endif
									</td>
									<td>{{ $copyEditing->user->full_name }}</td>
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
									<td>{{ $copyEditing->editor->full_name }}</td>
									<td>
										<a href="#approveOtherServiceFeedbackModal" data-toggle="modal"
											class="btn btn-success btn-xs approveOtherServiceFeedbackBtn" 
											data-service="2"
											data-feedback_file = "{{ $copyEditing->feedback->manuscript }}"
											data-action="{{ route('head_editor.other-service.approve-feedback',
											['id' => $copyEditing->id, 'type' => 1]) }}"> {{ trans('site.approve-feedback') }}</a> &nbsp;
										<?php $files = explode(',',$copyEditing->feedback->manuscript); ?>
										@foreach($files as $file)
											<a href="{{ $file }}" download><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp;
										@endforeach
									</td>
									<td>
										@if( $copyEditing->status == 2 )
											<span class="label label-success">{{ trans('site.finished') }}</span>
										@elseif( $copyEditing->status == 1 )
											<span class="label label-primary">{{ trans('site.started') }}</span>
										@elseif( $copyEditing->status == 0 )
											<span class="label label-warning">{{ trans('site.not-started') }}</span>
										@elseif( $copyEditing->status == 3 )
											<span class="label label-default">{{ trans('site.pending') }}</span>
										@endif
									</td>
									<td>
										@if($copyEditing->feedback->notes_to_head_editor)
											<a class="notes" data-target="#notesModal" data-toggle="modal" data-notes="{{ $copyEditing->feedback->notes_to_head_editor }}">
												{{ substr($copyEditing->feedback->notes_to_head_editor, 0, 10) }}
												<i class="fa fa-file-text-o" aria-hidden="true"></i>
											</a>
										@endif
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>

<!-- modal  -->
<div id="personalAssignmentShowFeedbackModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.approve-feedback') }}</h4>
			</div>
			<div class="modal-body">

                <form id="personalAssignmentApproveFeedback" method="POST" action=""  enctype="multipart/form-data">
                    <?php
                    	$emailTemplate = \App\Http\AdminHelpers::emailTemplate('Assignment Manuscript Feedback');
                    ?>
                    {{ csrf_field() }}
					<input type="hidden" class="form-control" name="feedback_id">
					<div class="form-group">
						<label>{{ trans_choice('site.feedback-file', 1) }}</label><br>
						<div id="feedbackFileAppend"></div>
					</div>
					<div class="form-group">
                        <label name="manuscriptLabel">{{ trans('site.replace-feedback-file') }}</label>
                        <input type="file" class="form-control" multiple name="filename[]"
                               accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                   application/pdf, application/vnd.oasis.opendocument.text">
						{{ trans('site.docx-pdf-odt-text') }} <br>
                    </div>
                    <div class="form-group">
						<label>{{ trans('site.grade') }}</label><br>
						<input class="form-control" type="number" step="0.01" name="grade">
					</div>
                    <hr>
                    <div class="form-group">
                        <label>{{ trans('site.available-date') }}</label>
                        <input required type="date" class="form-control" name="availability">
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
                    <button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('approve-feedback') }}</button>
                    <div class="clearfix"></div>
                </form>

			</div>
		</div>
	</div>
</div>

<div id="shopManuscriptShowFeedbackModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.approve-feedback') }}</h4>
			</div>
			<div class="modal-body">

                <form id="shopManuscriptTakenApproveFeedback" method="POST" action="" enctype="multipart/form-data">
					<?php
						$emailTemplate = \App\Http\AdminHelpers::emailTemplate('Shop Manuscript Feedback');
					?>
					{{csrf_field()}}
					<div class="form-group">
						<label>{{ trans('site.feedback-file') }}</label><br>
						<div id="feedbackFileAppend"></div>
					</div>
					<div class="form-group">
                        <label name="manuscriptLabel">{{ trans('site.replace-feedback-file') }}</label>
                        <input type="file" class="form-control" multiple name="files[]"
                               accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                   application/pdf, application/vnd.oasis.opendocument.text">
								   {{ trans('site.docx-pdf-odt-text') }} <br>
                    </div>
                    <div class="form-group">
						<label>{{ trans_choice('site.notes', 1) }}</label><br>
                        <textarea class="form-control" name="notes" rows="6"></textarea>
					</div>
                    <hr>
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
					{{ trans('site.add-feedback-note') }}
					<button type="submit" class="btn btn-primary pull-right">{{ trans('site.approve-feedback') }}</button>
					<div class="clearfix"></div>
				</form>

			</div>
		</div>
	</div>
</div>

<div id="courseAssignmentShowFeedbackModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.approve-feedback') }}</h4>
			</div>
			<div class="modal-body">

                <form id="courseAssignmentApproveFeedback" method="POST" action=""  enctype="multipart/form-data">
                    <?php
                    	$emailTemplate = \App\Http\AdminHelpers::emailTemplate('Assignment Manuscript Feedback');
                    ?>
                    {{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.feedback-file') }}</label><br>
						<div id="feedbackFileAppend"></div>
					</div>
					<div class="form-group">
                        <label name="manuscriptLabel">{{ trans('site.replace-feedback-file') }}</label>
                        <input type="file" class="form-control" multiple name="filename[]"
                               accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                   application/pdf, application/vnd.oasis.opendocument.text">
								   {{ trans('site.docx-pdf-odt-text') }} <br>
                    </div>
                    <div class="form-group">
						<label>{{ trans('site.grade') }}</label><br>
						<input class="form-control" type="number" step="0.01" name="grade">
					</div>
                    <hr>
                    <div class="form-group">
                        <label>{{ trans('site.available-date') }}</label>
                        <input required type="date" class="form-control" name="availability">
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
                    <button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.approve-feedback') }}</button>
                    <div class="clearfix"></div>
                </form>

			</div>
		</div>
	</div>
</div>

<div id="approveOtherServiceFeedbackModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.approve-feedback') }}</h4>
			</div>
			<div class="modal-body">

                <form id="approveOtherServiceFeedback" method="POST" action=""  enctype="multipart/form-data">
                    <?php
                    	$emailTemplate = \App\Http\AdminHelpers::emailTemplate('Other Services Feedback');
                    ?>
                    {{ csrf_field() }}
					<input type="hidden" class="form-control" name="feedback_id">
					<div class="form-group">
						<label>{{ trans('site.feedback-file') }}</label><br>
						<div id="feedbackFileAppend"></div>
					</div>
                    <hr>
					<div class="form-group">
                        <label name="manuscriptLabel">{{ trans('site.replace-feedback-file') }}</label>
                        <input type="file" class="form-control" multiple name="manuscript[]"
                               accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                   application/pdf, application/vnd.oasis.opendocument.text">
								   {{ trans('site.docx-pdf-odt-text') }} <br>
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
                    <button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.approve-feedback') }}</button>
                    <div class="clearfix"></div>
                </form>

			</div>
		</div>
	</div>
</div>

<div id="notesModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans_choice('site.notes', 2) }}</h4>
			</div>
			<div class="modal-body">

                <p name="notes"></p>

			</div>
		</div>
	</div>
</div>

@stop

@section('scripts')
	<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script>
	$('.personalAssignmentShowFeedbackBtn').click(function(){

		$("#personalAssignmentApproveFeedback").trigger('reset');

        var manuscript_id = $(this).data('id');
        var feedbackFileName =  $(this).data('feedback_file');
        var feedbackGrade =  $(this).data('feedback_grade');
		let modal = $('#personalAssignmentShowFeedbackModal');
        let action = $(this).data('action');
		let feedback_id = $(this).data('feedback_id');
		
		var feedbackArray = feedbackFileName.split(",");
		modal.find('#feedbackFileAppend').html('');
		feedbackArray.forEach(function (item, index){
			modal.find('#feedbackFileAppend').append('<a href="'+ item +'" name="feedback_filename" class="" download>'+ item +'</a><br>')
		})

        modal.find('[name=grade]').val(feedbackGrade);
        modal.find('[name=feedback_id]').val(feedback_id);
        modal.find('form#personalAssignmentApproveFeedback').attr('action', action);
	});
	$('.courseAssignmentShowFeedbackBtn').click(function(){

		$("#courseAssignmentApproveFeedback").trigger("reset");

        var manuscript_id = $(this).data('id');
        var feedbackFileName =  $(this).data('feedback_file');
        var feedbackGrade =  $(this).data('feedback_grade');
		let modal = $('#courseAssignmentShowFeedbackModal');
        let action = $(this).data('action');

		var feedbackArray = feedbackFileName.split(",");
		modal.find('#feedbackFileAppend').html('');
		feedbackArray.forEach(function (item, index){
			modal.find('#feedbackFileAppend').append('<a href="'+ item +'" name="feedback_filename" class="" download>'+ item +'</a><br>')
		})

        modal.find('[name=grade]').val(feedbackGrade);
        modal.find('form#courseAssignmentApproveFeedback').attr('action', action);
	});
    $('.shopManuscriptShowFeedbackBtn').click(function(){

		$("#shopManuscriptTakenApproveFeedback").trigger("reset");

        var feedbackFileName =  $(this).data('feedback_file');
        var feedbackNotes =  $(this).data('feedback_notes');
		let modal = $('#shopManuscriptShowFeedbackModal');
        let action = $(this).data('action');

		var feedbackArray = feedbackFileName.split(",");
		modal.find('#feedbackFileAppend').html('');
		feedbackArray.forEach(function (item, index){
			modal.find('#feedbackFileAppend').append('<a href="'+ item +'" name="feedback_filename" class="" download>'+ item +'</a><br>')
		})

        modal.find('[name=notes]').val(feedbackNotes);
        modal.find('form#shopManuscriptTakenApproveFeedback').attr('action', action);
	});
	$('.approveOtherServiceFeedbackBtn').click(function(){
		
		$("#approveOtherServiceFeedback").trigger("reset");

        var feedbackFileName =  $(this).data('feedback_file');
		let modal = $('#approveOtherServiceFeedbackModal');
        let action = $(this).data('action');
		let feedback_id = $(this).data('feedback_id');
        
		modal.find('[name=feedback_id]').val(feedback_id);

		var feedbackArray = feedbackFileName.split(",");
		modal.find('#feedbackFileAppend').html('');
		feedbackArray.forEach(function (item, index){
			modal.find('#feedbackFileAppend').append('<a href="'+ item +'" name="feedback_filename" class="" download>'+ item +'</a><br>')
		})

        modal.find('form#approveOtherServiceFeedback').attr('action', action);
	});
	$('.notes').click(function(){

		var notes = $(this).data('notes');
		let modal = $('#notesModal');
		modal.find('[name=notes]').text(notes);

	});
    function disableSubmit(t) {
        let submit_btn = $(t).find('[type=submit]');
        submit_btn.text('');
        submit_btn.append('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
        submit_btn.attr('disabled', 'disabled');
    }

</script>
@stop