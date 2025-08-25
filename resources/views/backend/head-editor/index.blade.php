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
                                    <td>
										<a href="{{ route('admin.learner.show', $assignedManuscript->user_id) }}">
											{{ $assignedManuscript->user->full_name }}
										</a>
									</td>
                                    <td>
                                        {{ $assignedManuscript->expected_finish }}
                                    </td>
                                    <td>
										{{ $assignedManuscript->editor->full_name}}
										<button class="btn btn-info btn-xs send-email"
										data-toggle="modal"
										data-target="#sendEmail"
										data-action="{{ route('admin.head-editor-to-editor', 
															['type' => 'assignment',
															'title' => basename($assignedManuscript->filename),
															'learner' => $assignedManuscript->user->id,
															'editor_id' => $assignedManuscript->editor->id]) }}"
										><i class="fa fa-paper-plane" aria-hidden="true"></i>&nbsp;{{ trans('site.send-email') }}</button>
									</td>
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
												data-availability="{{$assignedManuscript->noGroupFeedbacks->first()->availability}}"
                                                data-action="{{ route('head_editor.personal_assignment.feedbac_approve',
																['id' => $assignedManuscript->id,
																'learner_id' => $assignedManuscript->user->id]) }}">
                                                {{ trans('site.approve-feedback') }}
                                        </button> &nbsp;
										<?php $files = explode(',',$assignedManuscript->noGroupFeedbacks->first()->filename); ?>
										@foreach($files as $file)
											<a href="{{ $file }}" download><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp;
										@endforeach
										{{ $assignedManuscript->noGroupFeedbacks->first()->created_at }}
										
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
										<td>
											<a href="{{ route('admin.learner.show', $shopManuscript->user->id) }}">
												{{ $shopManuscript->user->full_name }}
											</a>
										</td>
										<td>{{ $shopManuscript->expected_finish }}</td>
                                        <td>
											@if ($shopManuscript->admin)
												{{ $shopManuscript->admin->full_name }}
												<button class="btn btn-info btn-xs send-email"
												data-toggle="modal"
												data-target="#sendEmail"
												data-action="{{ route('admin.head-editor-to-editor', 
																	['type' => 'shop-manuscript',
																	'title' => $shopManuscript->shop_manuscript->title,
																	'learner' => $shopManuscript->user->id,
																	'editor_id' => $shopManuscript->admin->id]) }}"
												><i class="fa fa-paper-plane" aria-hidden="true"></i>
												&nbsp;{{ trans('site.send-email') }}</button>
											@endif
										</td>
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
											{{ $shopManuscript->feedbacks->first()->created_at }}
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
									<td>
										<a href="{{ route('admin.learner.show', $assignedAssignment->user->id) }}">
											{{ $assignedAssignment->user->full_name }}
										</a>
									</td>
									<td>{{$assignedAssignment->expected_finish}}</td>
									<td>
										@if ($assignedAssignment->editor)
											{{$assignedAssignment->editor->full_name}}
											<?php
												$title = $assignedAssignment->assignment->course?$assignedAssignment->assignment->course->title:$assignedAssignment->assignment->title;
											?>
											<button class="btn btn-info btn-xs send-email"
											data-toggle="modal"
											data-target="#sendEmail"
											data-action="{{ route('admin.head-editor-to-editor',
																['type' => 'assignment-group',
																'title' => $title,
																'learner' => $assignedAssignment->user->id,
																'editor_id' => $assignedAssignment->editor->id]) }}"
											><i class="fa fa-paper-plane" aria-hidden="true"></i>&nbsp;{{ trans('site.send-email') }}</button>
										@else
											No Assigned Editor
										@endif
									</td>
									<?php 
										// echo $assignedAssignment->user_id.' '.
										$groupDetails = DB::SELECT("SELECT A.id as assignment_group_id, B.id AS assignment_group_learner_id FROM assignment_groups A JOIN assignment_group_learners B ON A.id = B.assignment_group_id AND B.user_id = $assignedAssignment->user_id WHERE A.assignment_id = $assignedAssignment->assignment_id");
										if($groupDetails){ // Means the course assignment belongs to a group
											$feedback = DB::SELECT("SELECT A.* FROM assignment_feedbacks A JOIN assignment_group_learners B ON A.assignment_group_learner_id = B.id WHERE B.user_id = $assignedAssignment->user_id AND A.assignment_group_learner_id = ".$groupDetails[0]->assignment_group_learner_id . " AND A.is_admin = 1");
											if ($feedback) {
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
											}
										}else{ //the course assignment does not belong to a group
											echo '<td>';
											echo '<button class="btn btn-success btn-xs personalAssignmentShowFeedbackBtn"
															data-target="#personalAssignmentShowFeedbackModal"
															data-toggle="modal"
															data-id = "'.$assignedAssignment->id.'"
															data-feedback_file = "'.$assignedAssignment->noGroupFeedbacks->first()->filename.'"
															data-feedback_grade = "'.$assignedAssignment->grade.'"
															data-feedback_id = "'.$assignedAssignment->noGroupFeedbacks->first()->id.'"
															data-availability="'.$assignedAssignment->noGroupFeedbacks->first()->availability.'"
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

			<!-- Free Manuscript-->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>Free Manuscript</h4></div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans('site.name') }}</th>
								<th>{{ trans('site.genre') }}</th>
								<th>{{ trans_choice('site.editors', 1) }}</th>
								<th>{{ trans_choice('site.feedbacks', 1) }}</th>
								<th>{{ trans('site.feedback-status') }}</th>
							</tr>
							</thead>
							<tbody>
							@foreach($freeManuscripts as $freeManuscript)
								<tr>
									<td>{{ $freeManuscript->name }}</td>
									<td>{{ \App\Http\AdminHelpers::assignmentType($freeManuscript->genre) }}</td>
									<td>@if( $freeManuscript->editor ) {{ $freeManuscript->editor->full_name }} @endif</td>
									<td>
										<button class="btn btn-xs btn-success sendFMApproveFeedbackBtn"
												data-toggle="modal" data-target="#freeManuscriptApproveFeedbackModal"
												data-fields="{{ json_encode($freeManuscript) }}"
												data-action="{{ route('head_editor.free-manuscript.feedback_approve', $freeManuscript->id) }}">
											{{ trans('site.approve-feedback') }}
										</button>
									</td>
									<td>
										<span class="label label-default">{{ trans('site.pending') }}</span>
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- self publishing-->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>Self Publishing</h4></div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans('site.title') }}</th>
								<th>{{ trans_choice('site.manus', 2) }}</th>
								<th>Feedback User</th>
								<th>{{ trans('site.expected-finish') }}</th>
								<th>{{ trans_choice('site.feedbacks', 1) }}</th>
								<th>{{ trans_choice('site.notes', 2) }}</th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							@foreach($selfPublishingList as $publishing)
								<tr>
									<td>
										{{ $publishing->title }}
									</td>
									<td>
										<a href="{{ route('admin.self-publishing.download-manuscript', $publishing->id) }}">
											<i class="fa fa-download" aria-hidden="true"></i>
										</a>
										{!! $publishing->file_link !!}
									</td>
									<td>
										{{ $publishing->editor ? $publishing->editor->full_name :
										($publishing->feedback && $publishing->feedback->feedbackUser
										? $publishing->feedback->feedbackUser->full_name : '') }}
									</td>
									<td>
										{{ $publishing->expected_finish }}
									</td>
									<td>
										<a href="{{ route('admin.self-publishing.download-feedback', $publishing->feedback->id) }}">
											<i class="fa fa-download" aria-hidden="true"></i>
										</a>
										{!! $publishing->feedback->file_link !!} <br>
									</td>
									<td>
										{{ $publishing->feedback->notes}}
									</td>
									<td>
										<button class="btn btn-xs btn-success selfPublishingApproveFeedbackBtn"
												data-toggle="modal" data-target="#selfPublishingApproveFeedbackModal"
												data-action="{{ route('head_editor.self-publishing-feedback.approve', $publishing->feedback->id) }}">
											{{ trans('site.approve-feedback') }}
										</button>
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>

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
										@if (strpos($correction->file, 'Forfatterskolen_app'))
											<a href="{{ url('/dropbox/download/' . trim($correction->file)) }}">
												<i class="fa fa-download" aria-hidden="true"></i>
											</a>&nbsp;
											<a href="{{ url('/dropbox/shared-link/' . trim($correction->file)) }}" target="_blank"> 
												{{ basename($correction->file) }}
											</a>
										@else
											<a href="{{ route('admin.other-service.download-doc', ['id' => $correction->id, 'type' => 2]) }}" download>
												<i class="fa fa-download" aria-hidden="true"></i>
											</a>&nbsp;
											@if( end($extension) == 'pdf' || end($extension) == 'odt' )
												<a href="/js/ViewerJS/#../../{{ $correction->file }}">{{ basename($correction->file) }}</a>
											@elseif( end($extension) == 'docx' )
												<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$correction->file}}">
													{{ basename($correction->file) }}
												</a>
											@endif
										@endif
									</td>
									<td>
										<a href="{{ route('admin.learner.show', $correction->user->id) }}">
											{{ $correction->user->full_name }}
										</a>
									</td>
									<td>
										@if ($correction->expected_finish)
											{{ \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($correction->expected_finish) }}
											<br>
										@endif
									</td>
									<td>
									{{ $correction->editor->full_name }}
									<button class="btn btn-info btn-xs send-email"
										data-toggle="modal"
										data-target="#sendEmail"
										data-action="{{ route('admin.head-editor-to-editor', 
															['type' => 'correction',
															'title' => basename($correction->file),
															'learner' => $correction->user->id,
															'editor_id' => $correction->editor->id]) }}"
									><i class="fa fa-paper-plane" aria-hidden="true"></i>&nbsp;{{ trans('site.send-email') }}</button>
									</td>
									<td>
										@if($correction->feedback)
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
											{{ $correction->feedback->created_at }}
										@endif
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
										@if($correction->feedback && $correction->feedback->notes_to_head_editor)
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
						<div class="panel-heading"><h4>{{ trans('site.my-copy-editing') }}d</h4></div>
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
										@if (strpos($copyEditing->file, 'Forfatterskolen_app'))
											<a href="{{ url('/dropbox/download/' . trim($copyEditing->file)) }}">
												<i class="fa fa-download" aria-hidden="true"></i>
											</a>&nbsp;
											<a href="{{ url('/dropbox/shared-link/' . trim($copyEditing->file)) }}" target="_blank"> 
												{{ basename($copyEditing->file) }}
											</a>
										@else
											<a href="{{ route('admin.other-service.download-doc', ['id' => $copyEditing->id, 'type' => 1]) }}">
												<i class="fa fa-download" aria-hidden="true"></i>
											</a>&nbsp;
											@if( end($extension) == 'pdf' || end($copyEditing) == 'odt' )
												<a href="/js/ViewerJS/#../../{{ $copyEditing->file }}">{{ basename($copyEditing->file) }}</a>
											@elseif( end($extension) == 'docx' )
												<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$copyEditing->file}}">{{ basename($copyEditing->file) }}</a>
											@endif
										@endif
									</td>
									<td>
										<a href="{{ route('admin.learner.show', $copyEditing->user->id) }}">
											{{ $copyEditing->user->full_name }}
										</a>
									</td>
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
									{{ $copyEditing->editor->full_name }}
									<button class="btn btn-info btn-xs send-email"
									data-toggle="modal"
									data-target="#sendEmail"
									data-action="{{ route('admin.head-editor-to-editor', 
															['type' => 'correction',
															'title' => basename($copyEditing->file),
															'learner' => $copyEditing->user->id,
															'editor_id' => $copyEditing->editor->id]) }}"
									><i class="fa fa-paper-plane" aria-hidden="true"></i>&nbsp;{{ trans('site.send-email') }}</button>
									</td>
									<td>
										@if($copyEditing->feedback)
											<a href="#approveOtherServiceFeedbackModal" data-toggle="modal"
												class="btn btn-success btn-xs approveOtherServiceFeedbackBtn"
												data-service="2"
												data-feedback_file = "{{ $copyEditing->feedback->manuscript }}"
												data-action="{{ route('head_editor.other-service.approve-feedback',
												['id' => $copyEditing->id, 'type' => 1]) }}"> {{ trans('site.approve-feedback') }}</a> &nbsp;
											<?php $files = explode(',',$copyEditing->feedback->manuscript); ?>
											@foreach($files as $file)
												@if(strpos($file, 'Forfatterskolen_app'))
													<a href="{{ route('dropbox.download_file', trim($file)) }}"><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp;
												@else
													<a href="{{ $file }}" download><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp;
												@endif
											@endforeach
											{{ $copyEditing->feedback->created_at }}
										@endif
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
										@if($copyEditing->feedback && $copyEditing->feedback->notes_to_head_editor)
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

			<!-- My Coaching Timer -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>{{ trans('site.my-coaching-timer') }}</h4></div>
						<table class="table">
							<thead>
								<tr>
									<th>{{ trans_choice('site.learners', 1) }}</th>
									<th>{{ trans('site.approved-date') }}</th>
									<th>{{ trans('site.session-length') }}</th>
									<th>{{ trans_choice('site.editors', 1) }}</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								@foreach($coachingTimes as $coachingTimer)
									<tr>
										<td>
											<a href="{{ route('admin.learner.show', $coachingTimer->user->id) }}">
												{{ $coachingTimer->user->full_name }}
											</a>
											
										</td>
										<td>
											{{ $coachingTimer->approved_date ?
											\App\Http\FrontendHelpers::formatToYMDtoPrettyDate($coachingTimer->approved_date)
											: ''}}
										</td>
										<td>
											{{ \App\Http\FrontendHelpers::getCoachingTimerPlanType($coachingTimer->plan_type) }}
										</td>
										<td>
											{{ $coachingTimer->editor->full_name }}
										</td>
										<td>
											<button class="btn btn-success btn-xs finishCoachingTimeBtn" data-toggle="modal" 
                                            data-target="#finishCoachingTimeModal" 
                                            data-action="{{ route('head_editor.other-service.mark-finished', $coachingTimer->id) }}">
                                                Mark as finished
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

                <form id="personalAssignmentApproveFeedback" method="POST" action=""  enctype="multipart/form-data"
					  onsubmit="disableSubmit(this)">
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
						<label>
							Email Template	
						</label>
						<select name="email_templates" class="form-control">
							<option value="" disabled selected>- Select Email - </option>
							@foreach ($assignmentFeedbackEmailTemplates as $assignmentFeedbackEmailTemplate)
								<option value="{{ $assignmentFeedbackEmailTemplate->subject }}" 
									data-fields="{{ $assignmentFeedbackEmailTemplate }}">
									{{ $assignmentFeedbackEmailTemplate->subject }}
								</option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<label>{{ trans('site.subject') }}</label>
						<input type="text" class="form-control" name="subject"
							   required>
					</div>
					<div class="form-group">
						<label>{{ trans('site.from') }}</label>
						<input type="text" class="form-control" name="from_email" required>
					</div>
					<div class="form-group">
						<label>{{ trans('site.message') }}</label>
						<textarea class="form-control tinymce" name="message" id="messageEditor" rows="6"></textarea>
					</div>

					<div class="form-group">
						<label>
							Send Email
						</label>
						<br>
						<input type="checkbox" data-toggle="toggle" data-on="Yes"
							   class="for-sale-toggle" data-off="No"
							   name="send_email" data-width="84" checked>
					</div>

                    <button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.approve-feedback') }}</button>
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

                <form id="shopManuscriptTakenApproveFeedback" method="POST" action="" enctype="multipart/form-data"
					  onsubmit="disableSubmit(this)">
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

					<div class="form-group">
						<label>
							Send Email
						</label>
						<br>
						<input type="checkbox" data-toggle="toggle" data-on="Yes"
							   class="for-sale-toggle" data-off="No"
							   name="send_email" data-width="84" checked>
					</div>

					{{ trans('site.add-feedback-note') }}

					<hr class="margin-top">
					<div class="form-group">
						<label>
							Follow Up Email
						</label>
						<br>
						<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
							   class="follow-up-email-toggle" name="follow_up_email" data-width="84">
					</div>

					<div class="follow-up-container hide">
						<?php
							$followUpEmailTemplate = \App\Http\AdminHelpers::emailTemplate('Shop Manuscript Follow-up Email');
						?>
						<div class="form-group">
							<label>Send Date</label>
							<input type="date" class="form-control" name="send_date"
								   value="{{ \Carbon\Carbon::today()->addDay(1)->format('Y-m-d') }}">
						</div>

						<div class="form-group">
							<label>{{ trans('site.subject') }}</label>
							<input type="text" class="form-control" name="follow_up_subject" value="{{ $followUpEmailTemplate->subject }}"
								   required>
						</div>
						<div class="form-group">
							<label>{{ trans('site.from') }}</label>
							<input type="text" class="form-control" name="follow_up_from_email"
								   value="{{ $followUpEmailTemplate->from_email }}" required>
						</div>
						<div class="form-group">
							<label>{{ trans('site.message') }}</label>
							<textarea class="form-control tinymce" name="follow_up_message" rows="6"
									  required>{!! $followUpEmailTemplate->email_content !!}</textarea>
						</div>
					</div>

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

                <form id="courseAssignmentApproveFeedback" method="POST" action=""  enctype="multipart/form-data"
					onsubmit="disableSubmit(this)">
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
						<div class="form-group">
							<label>
								Send Email
							</label>
							<br>
							<input type="checkbox" data-toggle="toggle" data-on="Yes"
								   class="for-sale-toggle" data-off="No"
								   name="send_email" data-width="84" checked>
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

                <form id="approveOtherServiceFeedback" method="POST" action=""  enctype="multipart/form-data"
					onsubmit="disableSubmit(this)">
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
						<div class="form-group">
							<label>
								Send Email
							</label>
							<br>
							<input type="checkbox" data-toggle="toggle" data-on="Yes"
								   class="for-sale-toggle" data-off="No"
								   name="send_email" data-width="84" checked>
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

<div id="sendEmail" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.send-email') }}</h4>
			</div>
			<div class="modal-body">

				<form method="POST" action="" onsubmit="disableSubmit(this)">
					<?php
						$emailTemplate = \App\Http\AdminHelpers::emailTemplate('Head Editor To Editor');
					?>
					{{ csrf_field() }}
					<div class="margin-top">
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
						<br>
						<hr>
						<button type="submit" class="btn btn-primary">{{ trans('site.save') }}</button>
					</div>
				</form>

			</div>
		</div>
	</div>
</div>

<div id="freeManuscriptApproveFeedbackModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.approve-feedback') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" id="sendFeedbackForm" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.body') }}</label>
						<textarea name="email_content" cols="30" rows="10" class="form-control tinymce" id="FMEmailContentEditor" required>
						</textarea>
					</div>

					<hr class="margin-top">
					<div class="form-group">
						<label>
							Follow Up Email
						</label>
						<br>
						<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
							   class="follow-up-email-toggle" name="follow_up_email" data-width="84">
					</div>

					<div class="follow-up-container hide">
                        <?php
                        $followUpEmailTemplate = \App\Http\AdminHelpers::emailTemplate('Free Manuscript Follow-up Email');
                        ?>
						<div class="form-group">
							<label>Send Date</label>
							<input type="date" class="form-control" name="send_date"
								   value="{{ \Carbon\Carbon::today()->addDay(1)->format('Y-m-d') }}">
						</div>

						<div class="form-group">
							<label>{{ trans('site.subject') }}</label>
							<input type="text" class="form-control" name="follow_up_subject" value="{{ $followUpEmailTemplate->subject }}"
								   required>
						</div>
						<div class="form-group">
							<label>{{ trans('site.from') }}</label>
							<input type="text" class="form-control" name="follow_up_from_email"
								   value="{{ $followUpEmailTemplate->from_email }}" required>
						</div>
						<div class="form-group">
							<label>{{ trans('site.message') }}</label>
							<textarea class="form-control tinymce" name="follow_up_message" rows="6"
									  required>{!! $followUpEmailTemplate->email_content !!}</textarea>
						</div>
					</div>

					<div class="clearfix"></div>
					<button type="submit" class="btn btn-primary pull-right margin-top" id="sendFeedbackEmail">{{ trans('site.approve-feedback') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="selfPublishingApproveFeedbackModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.approve-feedback') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)" enctype="multipart/form-data">
					{{ csrf_field() }}

					<p style="font-weight: bold">
						Are you sure you want to approve this feedback?
					</p>

					<hr>

					<div class="form-group">
						<label name="manuscriptLabel">{{ trans('site.replace-feedback-file') }}</label>
						<input type="file" class="form-control" multiple name="manuscript[]"
							   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                   application/pdf, application/vnd.oasis.opendocument.text">
						{{ trans('site.docx-pdf-odt-text') }} <br>
					</div>

					<div class="form-group">
						<label>
							Send Email
						</label>
						<br>
						<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"name="send_email" data-width="84"
							class="send-email-toggle">
					</div>

					<div class="send-email-container hide">
						<?php
							$emailTemplate = \App\Http\AdminHelpers::emailTemplate('Self Publishing Feedback');
						?>
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
							<textarea class="form-control tinymce" name="email_content" rows="6"
									  required>{!! $emailTemplate->email_content !!}</textarea>
						</div>
					</div>

					<div class="clearfix"></div>
					<button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.approve-feedback') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="finishCoachingTimeModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.finish-assignment') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{csrf_field()}}
					{{ trans('site.finish-assignment-question') }}
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-success">{{ trans('site.submit') }}</button>
					</div>
				</form>
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
		let availability = $(this).data('availability');
		
		var feedbackArray = feedbackFileName.split(",");
		modal.find('#feedbackFileAppend').html('');
		feedbackArray.forEach(function (item, index){
			modal.find('#feedbackFileAppend').append('<a href="'+ item +'" name="feedback_filename" class="" download>'+ item +'</a><br>')
		})

        modal.find('[name=grade]').val(feedbackGrade);
        modal.find('[name=feedback_id]').val(feedback_id);
        modal.find('[name=availability]').val(availability);
        modal.find('form#personalAssignmentApproveFeedback').attr('action', action);
		tinymce.get('messageEditor').setContent('');
	});

	$('#personalAssignmentShowFeedbackModal').find('[name=email_templates]').change(function() {
		let modal = $('#personalAssignmentShowFeedbackModal');
		let selectedOption = this.options[this.selectedIndex];
		let fields = JSON.parse(selectedOption.getAttribute('data-fields'));
		if (fields) {
			modal.find('[name=subject]').val(fields.subject);
			modal.find('[name=from_email]').val(fields.from_email);
			tinymce.get('messageEditor').setContent(fields.email_content);
		}
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
        $(".follow-up-container").addClass('hide');
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

    $(".follow-up-email-toggle").change(function() {
        let is_checked = $(this).prop('checked');
        let container = $(".follow-up-container");
        container.addClass('hide');
        if (is_checked) {
            container.removeClass('hide');
		}
	});

	$("#selfPublishingApproveFeedbackModal .send-email-toggle").change(function() {
		let is_checked = $(this).prop('checked');
		let container = $("#selfPublishingApproveFeedbackModal .send-email-container");
		container.addClass('hide');
        if (is_checked) {
            container.removeClass('hide');
		}
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
	$('.send-email').click(function(){

		var type = $(this).data('type');
		var assignmentTitle = $(this).data('assignmentTitle');
		var learner = $(this).data('leaner');
		var action = $(this).data('action');

		let modal = $('#sendEmail');

		modal.find('form').attr('action', action);

	});

    $(".sendFMApproveFeedbackBtn").click(function(){
        let action = $(this).data('action');
        $(".follow-up-container").addClass('hide');
        let modal = $('#freeManuscriptApproveFeedbackModal');
        modal.find('form').attr('action', action);
        let fields = $(this).data('fields');
        let content = fields.feedback_content;

        tinymce.get('FMEmailContentEditor').setContent(content);
    });

    $(".selfPublishingApproveFeedbackBtn").click(function() {
		let action = $(this).data('action');
		let modal = $('#selfPublishingApproveFeedbackModal');
		modal.find('form').attr('action', action);
	});

	$(".finishCoachingTimeBtn").click(function() {
		let action = $(this).data('action');
		let modal = $('#finishCoachingTimeModal');
		modal.find('form').attr('action', action);
	});

    function disableSubmit(t) {
        let submit_btn = $(t).find('[type=submit]');
        submit_btn.text('');
        submit_btn.append('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
        submit_btn.attr('disabled', 'disabled');
    }

</script>
@stop