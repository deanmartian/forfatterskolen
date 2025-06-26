@extends('backend.layout')

@section('title')
<title>{{ $assignment->title }} &rsaquo; {{$course->title}} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
	<style>
		.d-none {
			display: none;
		}
	</style>
@stop

@section('content')

@include('backend.course.partials.toolbar')


<div class="course-container">
	
	@include('backend.partials.course_submenu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12">
			<a href="{{ route('admin.course.show', $course->id) }}?section=assignments" class="btn btn-sm btn-default margin-bottom" ><i class="fa fa-angle-left"></i> {{ trans('site.all-assignments') }}</a>

			<div class="pull-right">
				<button type="button" class="btn btn-sm btn-info editAssignmentBtn" 
				data-toggle="modal" data-target="#editAssignmentModal"><i class="fa fa-pencil"></i></button>
				<button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteAssignmentModal"><i class="fa fa-trash"></i></button>
			</div>
			
			<h3 class="no-margin-bottom">{{ $assignment->title }}</h3>
			<p class="margin-bottom">
				{{ $assignment->description }} <br>
				<b>{{ trans('site.submission-date') }}:</b> <i>{{ $assignment->submission_date }}</i> <br>
				<b>{{ trans('site.available-date') }}:</b> <i>{{ $assignment->available_date }}</i>
			</p>
			
			<div class="table-responsive">
				<button type="button" class="pull-right btn btn-success btn-sm margin-bottom assignMultipleManuscriptsBtn"
				data-toggle="modal" data-target="#assignMultipleManuscriptsModal">
					Assign Multiple Manuscripts
				</button>
				<button type="button" class="pull-right btn btn-primary btn-sm margin-bottom margin-right-5" data-toggle="modal"
				 data-target="#addManuscriptModal">{{ trans('site.add-manuscript') }}</button>
				<a type="button" class="pull-right btn btn-warning btn-sm margin-bottom margin-right-5" data-toggle="modal"
				 data-target="#addAssignmentToLearnerModal">
					Add-on for Learner
				</a>
				<a href="{{ route('assignment.export-all-learners-include-add-on-learners', $assignment->id) }}" class="pull-right btn btn-secondary btn-sm margin-bottom margin-right-5"
						style="border: 1px solid #000">
					Export Learners
				</a>
				@if ($assignment->for_editor && $assignment->manuscripts->count())
					@if($assignment->generated_filepath)
						<a href="{{ route('assignment.group.download-generate-doc', $assignment->id) }}" class="pull-right btn btn-success btn-sm margin-bottom margin-right-5">{{ trans('site.download-generated-file') }}</a>
					@else
						<a href="{{ route('assignment.group.generate-doc', $assignment->id) }}" class="pull-right btn btn-success btn-sm margin-bottom margin-right-5">{{ trans('site.generate') }}</a>
					@endif
				@endif
				@if($assignment->manuscripts->count())
					<button type="button" class="pull-right btn btn-info btn-sm margin-bottom margin-right-5"  data-toggle="modal" data-target="#sendEmailModal">{{ trans('site.send-email') }}</button>
				@endif

				<button class="btn btn-primary btn-sm pull-right margin-right-5 disableLearnerBtn" data-toggle="modal" 
				data-target="#disableLearnerModal">
					Disable for Learner
				</button>

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
							<th>{{ trans('site.text-nr') }}</th>
							<th>{{ trans_choice('site.groups', 1) }}</th>
							<th>Join Group</th>
							<th>Editor Expected Finish</th>
							<th>{{ trans('site.feedback-out') }}</th>
							<th>{{ trans_choice('site.editors', 1) }}</th>
							<th width="250"></th>
						</tr>
					</thead>
					<tbody>
						@foreach( $assignmentManuscripts as $manuscript )
						<?php $extension = explode('.', basename($manuscript->filename)); ?>
						<tr @if($manuscript->status != 0 ) style="background-color: #e6ffe6" @endif>
							<td>
								@if( end($extension) == 'pdf' || end($extension) == 'odt' )
								<a href="/js/ViewerJS/#../..{{ $manuscript->filename }}">{{ basename($manuscript->filename) }}</a>
								@elseif( end($extension) == 'docx' || end($extension) == 'doc' )
								<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$manuscript->filename}}">{{ basename($manuscript->filename) }}</a>
								@endif

								@if ($manuscript->uploaded_date)
									<br>
									<span>
										{{ $manuscript->uploaded_date }}
									</span>
								@endif

								@if ($manuscript->letter_to_editor)
									<br>
									<a href="{{ route('assignment.manuscript.download_letter', $manuscript->id) }}">Download Letter</a>
								@endif
							</td>
							<td><a href="{{route('admin.learner.show', $manuscript->user->id)}}">{{ $manuscript->user->full_name }}</a></td>
							<td>{{ $manuscript->grade }}</td>
							<td>
								<a href="javascript:void(0)" data-ass-type="{{ $manuscript->type }}" class="updateTypeBtn" data-toggle="modal" data-target="#updateTypeModal"
								   data-action="{{ route('assignment.group.update_manu_types', $manuscript->id) }}">
									{{ \App\Http\AdminHelpers::assignmentType($manuscript->type) }}
								</a>
							</td>
							<td>
								<a href="javascript:void(0)" data-manu-type="{{ $manuscript->manu_type }}" class="updateManuTypeBtn" data-toggle="modal" data-target="#updateManuTypeModal"
								   data-action="{{ route('assignment.group.update_manu_types', $manuscript->id) }}">
										{{ \App\Http\AdminHelpers::manuscriptType($manuscript->manu_type) }}
								</a>
							</td>
							<td> {{ $manuscript->words }} </td>
							<td> {{ $manuscript->text_number }} </td>
							<td>
								@if (isset(\App\Http\AdminHelpers::getLearnerAssignmentGroup($assignment->id, $manuscript->user->id)['id']))
									<a href="{{ route('admin.assignment-group.show',
									['course_id' => $course->id,
									'assignment_id' => $assignment->id,
									'group' => \App\Http\AdminHelpers::getLearnerAssignmentGroup($assignment->id, $manuscript->user->id)['id']]
									) }}">
										{{ \App\Http\AdminHelpers::getLearnerAssignmentGroup($assignment->id, $manuscript->user->id)['title'] }}
									</a>
								@endif
							</td>
							<td>
								<a href="#" data-toggle="modal" data-target="#updateJoinGroupModal"
								data-action="{{ route('assignment.update-join-group', $manuscript->id) }}"
								   class="upateJoinGroupBtn"
								   data-answer="{{ $manuscript->join_group }}">
									{{ $manuscript->join_group ? 'Yes' : 'No' }}
								</a>
							</td>
							<td>
								{{ $manuscript->editor_expected_finish 
								? \App\Http\FrontendHelpers::formatDate($manuscript->editor_expected_finish)
								: ($assignment->editor_expected_finish ? $assignment->editor_expected_finish : '') }} <br>
								<button class="btn btn-xs btn-primary editEditorExpectedFinishBtn" data-toggle="modal" 
								data-target="#editEditorExpectedFinishModal" 
								data-action="{{ route('backend.assignment.edit-dates', $manuscript->id) }}"
								data-editor_expected_finish="{{ $manuscript->editor_expected_finish
									? strftime('%Y-%m-%d', strtotime($manuscript->editor_expected_finish)) : NULL }}">
									Edit
								</button>
							</td>
							<td>

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
											{{ \App\Http\FrontendHelpers::formatDate($noGroupHaveFeedback[0]->availability) }}
										@endif
									@endif

									@if (isset(\App\Http\AdminHelpers::getLearnerAssignmentGroup($assignment->id, $manuscript->user->id)['id']))
										@php
											$groupLearnerId = \App\Http\AdminHelpers::getLearnerAssignmentGroup($assignment->id, $manuscript->user->id)['group_learner_id'];
											$editorFeedback = \App\Http\AdminHelpers::getAssignmentFeedbackByGroupLearnerIdAndEditorId($groupLearnerId, $manuscript->editor_id);
										@endphp

										@if($editorFeedback)
											{{ \App\Http\FrontendHelpers::formatDate($editorFeedback->availability) }}
										@endif
									@endif
							</td>
							<td>
								<?php 
									$editor = $manuscript->editor_id ? \App\User::find($manuscript->editor_id) : '';
									$eEFDate = strftime('%Y-%m-%d', strtotime($manuscript->editor_expected_finish));
									$hiddenEditors = DB::select("CALL getIDWhereHidden('$eEFDate')");
									$hiddenEditorIds = [];
									reset($hiddenEditorIds);
									if($hiddenEditors){
										foreach ($hiddenEditors as $key) {
											$hiddenEditorIds[] = $key->editor_id;
										}
									}
									// dd($hiddenEditorIds);
									$genreEditors = \App\User::where(function($query){
												$query->where('role', 3)->orWhere('admin_with_editor_access', 1);
											})
											->whereHas('editorGenrePreferences', function($q) use ($manuscript){
												$q->where('genre_id', $manuscript->type);
											})
											->where('is_active', 1)
											->whereNotIn('users.id', $hiddenEditorIds)
											->orderBy('id', 'desc')
											->get();
									
									if($genreEditors->count() < 1){
										$genreEditors = \App\User::where(function($query){
											$query->where('role', 3)->orWhere('admin_with_editor_access', 1);
										})
										->where('is_active', 1)
										->whereNotIn('users.id', $hiddenEditorIds)
										->orderBy('id', 'desc')
										->get();
								}
								?>

								{{ $editor ? $editor->full_name."\n" : "" }}
								<br>
								<button class="btn btn-xs btn-primary assignEditorBtn" data-toggle="modal" data-target="#assignEditorModal"
								data-action="{{ route('assignment.group.assign_manu_editor', $manuscript->id) }}"
								data-editor="{{ $editor ? $editor->id : '' }}"
								data-genre_editors = "{{ $genreEditors }}"
								data-genre_editors_count = "{{ $genreEditors->count() }}"
								data-preferred-editor="{{ $manuscript->user->preferredEditor
								? $manuscript->user->preferredEditor->editor_id : '' }}"
								data-preferred-editor-name="{{ $manuscript->user->preferredEditor
								? $manuscript->user->preferredEditor->editor->full_name : '' }}">
									{{ trans('site.assign-editor') }}
								</button>

								@if($editor)
									<button class="btn btn-xs btn-danger removeEditorBtn"
											data-action="{{ route('assignment.group.remove_manu_editor', $manuscript->id) }}"
											data-toggle="modal" data-target="#removeEditorModal">
										Remove Editor
									</button>
								@endif

							</td>
							<td>
								<div class="text-right">
									<a href="{{ route('assignment.group.download_manuscript', $manuscript->id) }}" class="btn btn-primary btn-xs">{{ trans('site.download') }}</a>
									<input type="checkbox" data-toggle="toggle" data-on="{{ trans('site.locked') }}"
										   class="lock-toggle" data-off="{{ trans('site.unlocked') }}"
										   data-id="{{$manuscript->id}}" data-size="mini" @if($manuscript->locked) {{ 'checked' }} @endif>
									<button type="button" class="btn btn-info btn-xs replaceManuscriptBtn" data-toggle="modal" data-target="#replaceManuscriptModal" data-action="{{ route('assignment.group.replace_manuscript', $manuscript->id) }}" data-grade="{{ $manuscript->grade }}" data-ass-type="{{ $manuscript->type }}" data-manu-type="{{ $manuscript->manu_type }}">{{ trans('site.replace-doc') }}</button>
									<div class="margin-top">
										<input type="checkbox" data-toggle="toggle" data-on="In Dash"
										   class="dashboard-toggle" data-off="Hide Dash"
										   style="width: 200px"
										   data-id="{{$manuscript->id}}" data-size="mini" @if($manuscript->show_in_dashboard) {{ 'checked' }} @endif>
										<button type="button" class="btn btn-warning btn-xs setGradeBtn" data-toggle="modal" data-target="#setGradeModal" data-action="{{ route('assignment.group.set_grade', $manuscript->id) }}" data-grade="{{ $manuscript->grade }}">{{ trans('site.set-grade') }}</button>
										<button type="button" class="btn btn-danger btn-xs deleteManuscriptBtn" data-toggle="modal" data-target="#deleteManuscriptModal" data-action="{{ route('assignment.group.delete_manuscript', $manuscript->id) }}"><i class="fa fa-trash"></i></button>
										<button type="button" class="btn btn-info btn-xs moveAssignmentBtn" data-toggle="modal" data-target="#moveAssignmentModal" data-action="{{ route('assignment.group.move_manuscript', $manuscript->id) }}"><i class="fa fa-arrows"></i></button>
										<br>
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
																	data-action="{{ route('assignment.group.manuscript-feedback-no-group-update',
																$noGroupHaveFeedback[0]['id']) }}"
																	data-edit="true">
																{{ trans('site.edit-feedback-as-admin') }}
															</button>
													@else
														<button type="button" class="btn btn-primary btn-xs submitFeedbackBtn"
																data-toggle="modal" data-target="#submitFeedbackModal"
																data-name="{{ $manuscript->user->full_name }}"
																data-action="{{ route('assignment.group.manuscript-feedback-no-group',
																['id' => $manuscript->id, 'learner_id' => $manuscript->user->id]) }}">
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
				<div style="margin-bottom: 20px; background-color: #fff; padding: 8px; border: solid 1px #ddd; border-top: none">
					@if($assignment->manuscripts->count())
						<div class="text-center">
							<a href="{{ route('assignment.group.download_all_manuscript', $assignment->id) }}"
							   class="btn btn-primary btn-xs">
								{{ trans('site.download-all') }}
							</a>

							<a href="{{ route('assignment.group.export_email_list', $assignment->id) }}" class="btn btn-success btn-xs">
								Export Email List
							</a>
						</div>
					@endif
				</div>
			</div>

			<div class="table-responsive">
				<div class="panel panel-default">
					<div class="panel-body">
						<h4 class="margin-bottom">{{ trans('site.download-based-on-assigned-editor') }}</h4>
						<form method="POST" action="{{ route('assignment.group.download_editor_manuscript', $assignment->id) }}" enctype="multipart/form-data"
							  class="form-inline">
							{{ csrf_field() }}
							<div class="form-group">
								<label>{{ trans_choice('site.editors', 1) }}</label>
								<select class="form-control" name="editor_id" required>
									<option value="" disabled selected>- Select Editor -</option>
									@foreach( $editors as $editor )
										<option value="{{ $editor->id }}">{{ $editor->full_name }}</option>
									@endforeach
								</select>
							</div>

							<button type="submit" class="btn btn-primary">{{ trans('site.download') }}</button>
							<a href="{{ route('assignment.group.download-excel-sheet', $assignment->id) }}" class="btn btn-primary" style="margin-left: 100px">{{ trans('site.download-excel-sheet') }}</a>
						</form>
					</div>
				</div>
			</div>

			<?php
				$assignment_manuscripts_list = $assignment->manuscripts->where('status', 1)->pluck('id')->toArray();
				$noGroupFeedbackList = \App\AssignmentFeedbackNoGroup::whereIn('assignment_manuscript_id', $assignment_manuscripts_list)
				->get();
			?>

			@if ($noGroupFeedbackList->count())
			<!-- start of feedback for assignment without a group -->
				<div class="panel panel-default">
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
												<a href="{{ $feedback->filename }}" download=""
												   class="btn btn-primary btn-xs pull-right">Download</a>
										</td>
										<td>
											@if( $feedback->is_admin ) [Admin] @endif
												{{ $feedback->feedbackUser ? basename($feedback->feedbackUser->full_name) : '' }}
										</td>
										<td>
											{{ $feedback->learner->full_name }}
										</td>
										<td>
											<a href="#" data-toggle="modal" class="updateAvailabilityBtn"
											   data-availability="{{ $feedback->availability }}"
											   data-target="#updateAvailabilityModal"
											data-action="{{ route('assignment.group.manuscript-feedback-no-group-update-availability', $feedback->id) }}">
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

			<div class="table-responsive margin-top">
				<div class="pull-right">
					<button type="button" class="btn btn-primary btn-sm margin-bottom" data-toggle="modal" data-target="#addGroupModal">{{ trans('site.create-group') }}</button>
					<button type="button" data-toggle="modal" class="btn btn-primary btn-sm margin-bottom" data-target="#generateGroup">{{ trans('site.generate') }}</button>
				</div>
				<h5>{{ trans_choice('site.groups', 2) }}</h5>
				<table class="table table-side-bordered table-white">
					<thead>
						<tr>
							<th>{{ trans_choice('site.groups', 1) }}</th>
							<th>{{ trans_choice('site.learners', 2) }}</th>
						</tr>
					</thead>
					<tbody>
						@foreach( $assignment->groups as $group )
						<tr>
							<td><a href="{{ route('admin.assignment-group.show', ['course_id' => $course->id, 'assignment_id' => $assignment->id, 'group' => $group->id]) }}">{{ $group->title }}</a></td>
							<td>{{ $group->learners->count() }}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>	
		</div>
	</div>
	<div class="clearfix"></div>
</div>

<div id="assignMultipleManuscriptsModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">
				Assign Multiple Manuscripts
			</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="{{ route('admin.assignment.assign-editor-to-manuscripts', [$course->id, $assignment->id]) }}"
				onsubmit="disableSubmit(this)">
		      	{{ csrf_field() }}
				
				<div class="form-group">
					<label>{{ trans_choice('site.editors', 1) }}</label>
					<select class="form-control select2" name="editor_id" required>
						<option value="" disabled selected>- Select Editor -</option>
						@foreach( $editors as $editor )
							<option value="{{ $editor->id }}">{{ $editor->full_name }}</option>
						@endforeach
					</select>
				</div>

				<div class="form-group">
					<label>{{ trans_choice('site.learners', 1) }}</label>
					<select class="form-control select2 leaner-list" name="learner_id[]" multiple required>
					</select>
				</div>

				<div class="form-group">
					<label>Editor Expected Finish</label>
					<input type="date" name="editor_expected_finish" class="form-control">
				</div>

		      	<button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.submit') }}</button>
		      	<div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>

<div id="addManuscriptModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">{{ trans('site.add-manuscript') }}</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="{{ route('assignment.group.upload_manuscript', $assignment->id) }}" enctype="multipart/form-data"
				onsubmit="disableSubmit(this)">
		      	{{ csrf_field() }}
				<?php
					// get all learners that have already sent manuscript
				$assignmentManuscriptLearners = \App\AssignmentManuscript::where('assignment_id', $assignment->id)
					->pluck('user_id')
					->toArray();

				?>
		      	<div class="form-group">
			      	<label>{{ trans_choice('site.learners', 1) }}</label>
			      	<select class="form-control select2" name="learner_id" required>
			      		<option value="" disabled selected>- Search learner -</option>
			      		@foreach( $course->learners->whereNotIn('user_id', $assignmentManuscriptLearners)->get() as $learner )
			      		<option value="{{ $learner->user->id }}">{{ $learner->user->full_name }}</option>
			      		@endforeach
			      	</select>
		      	</div>
		      	<div class="form-group">
			      	<label>{{ trans_choice('site.manuscripts', 1) }}</label>
	      			<input type="file" class="form-control" required name="filename" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text, application/msword">
	      			* Godkjente fil formater er DOCX, PDF og ODT.
      			</div>

                <div class="form-group">
                    <label>Join Group</label>
                    <select name="join_group" class="form-control" required>
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>

		      	<button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.submit') }}</button>
		      	<div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>

<div id="addAssignmentToLearnerModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">
					Add-on for Learner
				</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('assignment.add-on-for-learner', $assignment->id) }}"
					onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
                    <?php
                    // get all learners that have already sent manuscript
                    $assignmentManuscriptLearners = \App\AssignmentManuscript::where('assignment_id', $assignment->id)
                        ->pluck('user_id')
                        ->toArray();

                    ?>
					<div class="form-group">
						<label>{{ trans_choice('site.learners', 1) }}</label>
						<select class="form-control select2" name="learner_id" required>
							<option value="" disabled selected>- Search learner -</option>
							@foreach( $course->learners->whereNotIn('user_id', $assignmentManuscriptLearners)->get() as $learner )
								<option value="{{ $learner->user->id }}">{{ $learner->user->full_name }}</option>
							@endforeach
						</select>
					</div>

					<button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.submit') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="assignEditorModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.assign-editor') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans_choice('site.editors', 1) }}</label>
						<select class="form-control select2" name="editor_id" required>
							<option value="" disabled selected>- Select Editor -</option>
						</select>

						<div class="hidden-container">
							<label>
							</label>
							<a href="javascript:void(0)" onclick="enableSelect('assignEditorModal')">Edit</a>
						</div>
					</div>

					<button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.submit') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="editEditorExpectedFinishModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Editor Expected Finish</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>Editor Expected Finish</label>
						<input type="date" class="form-control" name="editor_expected_finish" required>
					</div>

					<button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.submit') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="removeEditorModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Remove Editor</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					{{ method_field('DELETE') }}
					<p>
						Are you sure you want to remove the editor?
					</p>

					<button type="submit" class="btn btn-danger pull-right margin-top">Remove</button>
					<div class="clearfix"></div>
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

<div id="addGroupModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">{{ trans('site.create-group') }}</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="{{route('admin.assignment-group.store', ['course_id' => $course->id, 'assignment_id' => $assignment->id])}}"
				onsubmit="disableSubmit(this)">
		      {{ csrf_field() }}
		      <div class="form-group">
		      	<label>{{ trans('site.group-name') }}</label>
		      	<input type="text" name="title" class="form-control" placeholder="Group name" required>
		      </div>
				<div class="form-group">
					<label>{{ trans('site.submission-date') }}</label>
					<input type="datetime-local" class="form-control" name="submission_date" required>
				</div>
				<div class="form-group">
					<label>{{ trans('site.allow-download-all-feedback') }}</label> <br>
					<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
						   data-id="@if (isset($group)){{$group->allow_feedback_download}}@endif"
						   @if(isset($group) && $group->allow_feedback_download) {{ 'checked' }} @endif
						   name="allow_feedback_download">
				</div>
		      <button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.create') }}</button>
		      <div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>

<div id="generateGroup" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">{{ trans('site.generate-group') }}</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="{{ route('assignment.generate_assignment_group', $assignment->id) }}">
		      {{ csrf_field() }}
				<div class="form-group">
					<label>{{ trans('site.submission-date') }}</label>
					<input type="datetime-local" class="form-control" name="submission_date" required>
				</div>
				<div class="form-group">
					<label>{{ trans('site.allow-download-all-feedback') }}</label> <br>
					<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
						   data-id="@if (isset($group)){{$group->allow_feedback_download}}@endif"
						   @if(isset($group) && $group->allow_feedback_download) {{ 'checked' }} @endif
						   name="allow_feedback_download">
				</div>
		      <button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.generate') }}</button>
		      <div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>

<div id="deleteAssignmentModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">{{ trans('site.delete-assignment') }}</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="{{route('admin.assignment.destroy', ['course_id' => $course->id, 'assignment' => $assignment->id])}}"
				onsubmit="disableSubmit(this)">
		      {{ csrf_field() }}
		      {{ method_field('DELETE') }}
				{{ trans('site.delete-assignment-question') }}
		      <button type="submit" class="btn btn-danger pull-right margin-top">{{ trans('site.delete') }}</button>
		      <div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>


<div id="editAssignmentModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">{{ trans('site.edit-assignment') }}</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="{{route('admin.assignment.update', ['course_id' => $course->id, 'assignment' => $assignment->id])}}"
				onsubmit="disableSubmit(this)">
		      {{ csrf_field() }}
		      {{ method_field('PUT') }}
		      <div class="form-group">
		      	<label>{{ trans('site.title') }}</label>
		      	<input type="text" class="form-control" name="title" placeholder="{{ trans('site.title') }}" required value="{{ $assignment->title }}">
		      </div>
		      <div class="form-group">
		      	<label>{{ trans('site.description') }}</label>
		      	<textarea class="form-control" name="description" placeholder="{{ trans('site.description') }}" rows="6">{{ $assignment->description }}</textarea>
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
					{{--<input type="datetime-local" class="form-control" name="submission_date"
						   @if( $assignment->submission_date ) value="{{ strftime('%Y-%m-%dT%H:%M:%S', strtotime($assignment->submission_date)) }}" @endif
					required>--}}
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
						   @if( $assignment->available_date ) value="{{ strftime('%Y-%m-%d', strtotime($assignment->available_date)) }}" @endif>
				</div>

				<div class="form-group">
					<label>{{ trans('site.allowed-package') }}</label>
					@foreach($course->packages as $package)
						<?php
						$allowed_package = json_decode($assignment->allowed_package);
						?>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" value="{{ $package->id }}" name="allowed_package[]"
							@if (!is_null($allowed_package) && in_array($package->id, $allowed_package)) checked @endif>
							<label class="form-check-label" for="{{ $package->variation }}">
								{{ $package->variation }}
							</label>
						</div>
					@endforeach
				</div>

				<div class="form-group">
					<label>{{ trans('site.add-on-price') }}</label>
					<input type="number" class="form-control" name="add_on_price" value="{{ $assignment->add_on_price }}" required>
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
					<label>{{ trans('site.editor-expected-finish') }}</label>
					<input type="date" class="form-control" name="editor_expected_finish"
					@if( $assignment->editor_expected_finish ) value="{{ strftime('%Y-%m-%d', strtotime($assignment->editor_expected_finish)) }}" @endif>
				</div>
				<div class="form-group">
					<label>{{ trans('site.expected-finish') }}</label>
					<input type="date" class="form-control" name="expected_finish"
						   @if( $assignment->expected_finish ) value="{{ strftime('%Y-%m-%d', strtotime($assignment->expected_finish)) }}" @endif>
				</div>
				<div class="form-group">
					<label>Linked Assignment</label>
					<select name="linked_assignment" id="" class="form-control">
						<option value="" disabled selected="">- Select Assignment -</option>
						@foreach($course->assignments as $courseAssignment)
							<option value="{{ $courseAssignment->id }}"
									@if($courseAssignment->parent_id == $assignment->id) selected @endif>
								{{ $courseAssignment->title }}
							</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label>{{ trans('site.for-editor') }}</label> <br>
					<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" data-size="small" name="for_editor"
					@if ($assignment->for_editor) checked @endif>
				</div>

				<div class="form-group @if (!$assignment->for_editor) hide @endif" id="editor_manu_gen_count">
					<label>{{ trans('site.manuscript-generate-count') }}</label>
					<input type="number" name="editor_manu_generate_count" class="form-control" step="1"
					value="{{$assignment->editor_manu_generate_count}}">
				</div>

				<div class="form-group">
					<label>{{ trans('site.show-join-group-question') }}</label> <br>
					<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" data-size="small" name="show_join_group_question"
					   @if ($assignment->show_join_group_question) checked @endif>
				</div>

				<div class="form-group">
					<label>Check Max Words</label> <br>
					<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" data-size="small" name="check_max_words"
					@if ($assignment->check_max_words) checked @endif>
				</div>

				<div class="form-group" id="assigned-editor-container">
					<label>Assigned Editor</label> <br>
					<select name="assigned_editor" id="" class="form-control">
						<option value="" disabled selected="">- Select Editor -</option>
						@foreach(AdminHelpers::editorList() as $editor)
							<option value="{{ $editor->id }}" 
								@if($editor->id == $assignment->assigned_editor) selected @endif>
								{{ $editor->full_name }}
							</option>
						@endforeach
					</select>
				</div>

				<div class="form-group">
					<label>{{ trans('site.send-letter-to-editor') }}</label> <br>
					<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" data-size="small" name="send_letter_to_editor"
						   @if ($assignment->send_letter_to_editor) checked @endif>
				</div>

		      <button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.save') }}</button>
		      <div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>

<div id="replaceManuscriptModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.replace-manuscript') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans_choice('site.manuscripts', 1) }}</label>
						<input type="file" class="form-control" required name="filename" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
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
							<input type="radio" name="manu_type" value="{{ $manu['id'] }}" required> <label>{{ $manu['option'] }}</label> <br>
						@endforeach
					</div>

					<button type="submit" class="btn btn-primary pull-right margin-top">Submit</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>


<div id="deleteManuscriptModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.delete-manuscript') }}</h4>
			</div>
			<div class="modal-body">
				{{ trans('site.delete-manuscript-question') }}
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<button type="submit" class="btn btn-danger pull-right margin-top">{{ trans('site.delete') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="moveAssignmentModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.move-manuscript-to-assignment') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.move-to-assignment') }}</label>
						<select name="assignment_id" class="form-control" required>
							<option value="" disabled selected>Select Assignment</option>
							@foreach($assignments as $assign)
							<option value="{{ $assign->id }}">{{ $assign->title }}</option>
							@endforeach
						</select>
					</div>
					<button type="submit" class="btn btn-info pull-right margin-top">{{ trans('site.move') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

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
					<button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.submit') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

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
							<input type="radio" name="manu_type" value="{{ $manu['id'] }}" required> <label>{{ $manu['option'] }}</label> <br>
						@endforeach
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
				<form method="POST" action=""  enctype="multipart/form-data">
                    <?php
                    	$emailTemplate = \App\Http\AdminHelpers::emailTemplate('Assignment Manuscript Feedback');
                    ?>
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
					<button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.submit') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<!--send email modal-->

<div id="sendEmailModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.send-email') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{route('assignment.group.send-email-to-list', $assignment->id)}}" onsubmit="formSubmitted(this)">
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
						<input type="submit" class="btn btn-primary" value="{{ trans('site.send') }}" id="send_email_btn">
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!--end email modal-->

<div id="disableLearnerModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">
					Disable Learner
				</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="">
					{{csrf_field()}}
					
					<div class="disable-learners-container">
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="personalAssignmentModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Personal Assignment Modal</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit()">
					{{ csrf_field() }}
					<input type="hidden" name="course_id" value="{{ $assignment->course_id }}">
					<input type="hidden" name="learner_id">

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
						<label>{{ trans('site.submission-date') }}</label>
						<div class="input-group">
							@if(\App\Http\AdminHelpers::isDateWithFormat('M d, Y h:i A', $assignment->submission_date))
								<input type="datetime-local" class="form-control" name="submission_date"
										 min="0" required
										@if( $assignment->submission_date )
										value="{{ strftime('%Y-%m-%dT%H:%M:%S', strtotime($assignment->submission_date)) }}"
									@endif>
							@else
								<input type="number" class="form-control" name="submission_date"
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
								value="{{ strftime('%Y-%m-%d', strtotime($assignment->available_date)) }}" 
								@endif>
					</div>
	  
					<div class="form-group">
						<label>{{ trans('site.editor-expected-finish') }}</label>
						<input type="date" class="form-control" name="editor_expected_finish"
						@if( $assignment->editor_expected_finish ) 
						value="{{ strftime('%Y-%m-%d', strtotime($assignment->editor_expected_finish)) }}"
						 @endif>
					</div>

					<div class="form-group">
						<label>{{ trans('site.expected-finish') }}</label>
						<input type="date" class="form-control" name="expected_finish"
								@if( $assignment->expected_finish ) 
								value="{{ strftime('%Y-%m-%d', strtotime($assignment->expected_finish)) }}" 
								@endif>
					</div>

					<div class="form-group">
						<label>{{ trans('site.max-words') }}</label>
						<input type="number" class="form-control" name="max_words" value="{{ $assignment->max_words }}">
					</div>

					<div class="form-group">
						<label>{{ trans('site.send-letter-to-editor') }}</label> <br>
						<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" data-size="small"
							   name="send_letter_to_editor">
					</div>

					<div class="form-group">
						<label>{{ trans_choice('site.editors', 1) }}</label>
						<select class="form-control select2" name="editor_id">
							<option value="" selected disabled>- Select Editor -</option>
							@foreach(\App\Http\AdminHelpers::editorList() as $editor)
								<option value="{{ $editor->id }}">
									{{ $editor->first_name . " " . $editor->last_name }}
								</option>
							@endforeach
						</select>
					</div>
	  
					<button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.save') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- update join group modal -->
<div id="updateJoinGroupModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Update Join Group Answer</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="">
					{{csrf_field()}}

					<div class="form-group">
						<label>Join Group</label>
						<select name="join_group" class="form-control" required>
							<option value="0">No</option>
							<option value="1">Yes</option>
						</select>
					</div>


					<div class="text-right">
						<input type="submit" class="btn btn-primary" value="{{ trans('site.save') }}">
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- end update join group modal -->

<div id="updateAvailabilityModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Edit Availability</h4>
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
@stop

@section('scripts')
	<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script>

	$(".editAssignmentBtn").click(function(){
		let check_max_words = "{{ $assignment->check_max_words }}";
		
		if (check_max_words == 0) {
			console.log("inside if");
			$("#assigned-editor-container").removeClass('hidden');
		} else {
			$("#assigned-editor-container").addClass('hidden');
		}
	});

	$("[name=check_max_words]").change(function(){
			//if ($(this).prop('checked')) {
				$("#assigned-editor-container").toggleClass('hidden');
			//}
		});

	$('.setGradeBtn').click(function(){
		var form = $('#setGradeModal form');
		var action = $(this).data('action');
		var grade = $(this).data('grade');
		form.find('input[name=grade]').val(grade);
		form.attr('action', action)
	});
    $('.deleteManuscriptBtn').click(function(){
        var form = $('#deleteManuscriptModal form');
        var action = $(this).data('action');
        form.attr('action', action)
    });
    $(".moveAssignmentBtn").click(function(){
        var form = $('#moveAssignmentModal form');
        var action = $(this).data('action');
        form.attr('action', action)
	});
    $('.replaceManuscriptBtn').click(function(){
        var form = $('#replaceManuscriptModal form');
        var action = $(this).data('action');
        var type = $(this).data('ass-type') ? $(this).data('ass-type') : '';
        var manu_type = $(this).data('manu-type');

        form.attr('action', action);
        form.find('#ass_type').val(type);
        form.find("input[name=manu_type][value="+manu_type+"]").attr('checked', true);
    });
	$('.removeLearnerBtn').click(function(){
		var form = $('#removeLearnerModal form');
		var action = $(this).data('action');
		form.attr('action', action)
	});
    $(".lock-toggle").change(function(){
        var course_id = $(this).attr('data-id');
        var is_checked = $(this).prop('checked');
        var check_val = is_checked ? 1 : 0;
        $.ajax({
            type:'POST',
            url:'/assignment_manuscript/lock-status',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: { "manuscript_id" : course_id, 'locked' : check_val },
            success: function(data){
            }
        });
    });

	$(".dashboard-toggle").change(function(){
        var course_id = $(this).attr('data-id');
        var is_checked = $(this).prop('checked');
        var check_val = is_checked ? 1 : 0;
        $.ajax({
            type:'POST',
            url:'/assignment_manuscript/dashboard-status',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: { "manuscript_id" : course_id, 'locked' : check_val },
            success: function(data){
            }
        });
    });

	$(".assignMultipleManuscriptsBtn").click(function(){
		let url = "{{ request()->url() }}/list-manuscripts-without-editor";
		
		$.ajax({
            type:'GET',
            url: url,
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(data){
				let modal = $("#assignMultipleManuscriptsModal");
				let select = modal.find(".leaner-list");
				let options = "";

				select.empty();
				$.each(data, function(k, v) {
					options += "<option value=" + v.user_id + ">" + v.user.full_name + "</option>";
				});

				select.append(options);
            }
        });
	});

    $('.updateTypeBtn').click(function(){
        var form = $('#updateTypeModal form');
        var action = $(this).data('action');
        var type = $(this).data('ass-type') ? $(this).data('ass-type') : '';

        form.attr('action', action);
        form.find('#ass_type').val(type);
    });

    $('.updateManuTypeBtn').click(function(){
        var form = $('#updateManuTypeModal form');
        var action = $(this).data('action');
        var manu_type = $(this).data('manu-type');

        form.attr('action', action);
        form.find("input[name=manu_type][value="+manu_type+"]").attr('checked', true);
    });

    $(".assignEditorBtn").click(function(){
		
        let modal = $("#assignEditorModal");
        let form = modal.find('form');
        let action = $(this).data('action');
        let editor = $(this).data('editor');
        let preferred_editor = $(this).data('preferred-editor');
        let preferred_editor_name = $(this).data('preferred-editor-name');
		let genreEditors = $(this).data('genre_editors');
		let genreEditorsCount = $(this).data('genre_editors_count');
		modal.find('select[name=editor_id]').html('<option value="" disabled selected>- Select Editor -</option>');

		for(var i = 0; i<genreEditorsCount; i++){
			modal.find('select[name=editor_id]').append('<option value="'+genreEditors[i]['id']+'">'+genreEditors[i]['first_name']+' '+genreEditors[i]['last_name']+'</option>');
		}

        form.attr('action', action);
        form.find("select[name=editor_id]").val(preferred_editor ? preferred_editor : editor).trigger('change');

        if (preferred_editor) {
            modal.find('.select2').hide();
            modal.find('.hidden-container').show();
            modal.find('.hidden-container').find('label').empty().text(preferred_editor_name);
        } else {
            modal.find('.select2').show();
            modal.find('.hidden-container').hide();
        }
	});

    $(".removeEditorBtn").click(function(){
        let modal = $("#removeEditorModal");
        let form = modal.find('form');
        let action = $(this).data('action');
        form.attr('action', action);
	});

    $('.submitFeedbackBtn').click(function(){
        var modal = $('#submitFeedbackModal');
        var name = $(this).data('name');
        var action = $(this).data('action');
        var is_edit = $(this).data('edit');
        modal.find('em').text(name);
        modal.find('form').attr('action', action);
        if (is_edit) {
            modal.find('form').find('input[type=file]').removeAttr('required');
		} else {
            modal.find('form').find('input[type=file]').attr('required', 'required');
		}
    });

    $(".upateJoinGroupBtn").click(function () {
		let modal = $("#updateJoinGroupModal");
		let answer = $(this).data('answer');
		let action = $(this).data('action');

		modal.find('form').attr('action', action);
		modal.find('select').val(answer);
    });

	$(".editEditorExpectedFinishBtn").click(function(){
		let modal = $("#editEditorExpectedFinishModal");
		let action = $(this).data('action');
		let editor_expected_finish = $(this).data('editor_expected_finish');

		modal.find('form').attr('action', action);
		modal.find('[name=editor_expected_finish]').val(editor_expected_finish);
	});

    $('.updateAvailabilityBtn').click(function(){
        console.log("adsfadsf");
        let modal = $('#updateAvailabilityModal');
        let availability = $(this).data('availability');
        let action = $(this).data('action');
        modal.find('input[name=availability]').val(availability);
        modal.find('form').attr('action', action);
    });

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

    $("[name=for_editor]").change(function(){
        $("#editor_manu_gen_count").toggleClass('hide');
    });

	$(".disableLearnerBtn").click(function() {
		let assignment_id = "{{ $assignment->id }}";
		let course_id = "{{ $course->id }}";
		$.ajax({
			method: "GET",
			url: "/assignment/" + assignment_id + "/course/" + course_id + "/assignment-with-course-learners",
			success: function(data) {
				$(".disable-learners-container").html(data);

				$(".dt-table").DataTable({
					"lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
					pageLength: 10,
					"aaSorting": [],
					"createdRow": function(row, data, dataIndex) {
						$(row).find('[data-toggle="toggle"]').bootstrapToggle();
					}
				});
			}
		})
	});

	function personalAssignment(user_id) {
		let action = "/assignment/{{ $assignment->id }}/disabled-learner-assignment/save";
		let modal = $("#personalAssignmentModal");
		modal.find('form').attr('action', action);
		modal.find("[name=learner_id]").val(user_id);
	}

	/* 
	// Reinitialize Bootstrap toggle after DataTables has initialized the table
	$(".dt-table").on("draw.dt", function() {
		$('[data-toggle="toggle"]').bootstrapToggle();
	}); */

	$(document).on("change", ".disable-learner-toggle", function() {
		let userId = $(this).data("id");
		let isChecked = $(this).prop("checked");
		let assignmentId = "{{ $assignment->id }}";

		$.ajax({
			method: "POST",
			url: "/assignment/" + assignmentId + "/disable-learner",
			data: {isChecked: isChecked, user_id: userId},
			success: function(data) {
				console.log(data);
				$(".assignment-learner-" + userId).toggleClass('d-none');
			}
		})
	});

    function formSubmitted(t) {
        let send_email = $(t).find("[type=submit]");
        send_email.val('Sending....').attr('disabled', true);
    }
</script>
@stop