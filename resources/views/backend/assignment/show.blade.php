@extends('backend.layout')

@section('title')
<title>{{ $assignment->title }} &rsaquo; {{$course->title}} &rsaquo; Forfatterskolen Admin</title>
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
			<a href="{{ route('admin.course.show', $course->id) }}?section=assignments" class="btn btn-sm btn-default margin-bottom" ><i class="fa fa-angle-left"></i> {{ trans('site.all-assignments') }}</a>

			<div class="pull-right">
				<button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#editAssignmentModal"><i class="fa fa-pencil"></i></button>
				<button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteAssignmentModal"><i class="fa fa-trash"></i></button>
			</div>
			
			<h3 class="no-margin-bottom">{{ $assignment->title }}</h3>
			<p class="margin-bottom">
				{{ $assignment->description }} <br>
				<b>{{ trans('site.submission-date') }}:</b> <i>{{ $assignment->submission_date }}</i>
			</p>
			
			<div class="table-responsive">
				<button type="button" class="pull-right btn btn-primary btn-sm margin-bottom" data-toggle="modal" data-target="#addManuscriptModal">{{ trans('site.add-manuscript') }}</button>
				@if ($assignment->for_editor && $assignment->manuscripts->count())
					@if($assignment->generated_filepath)
						<a href="{{ route('assignment.group.download-generate-doc', $assignment->id) }}" class="pull-right btn btn-success btn-sm margin-bottom margin-right-5">{{ trans('site.download-generated-file') }}</a>
					@else
						<a href="{{ route('assignment.group.generate-doc', $assignment->id) }}" class="pull-right btn btn-success btn-sm margin-bottom margin-right-5">{{ trans('site.generate') }}</a>
					@endif
				@endif
				<h5>{{ trans_choice('site.manuscripts', 2) }}</h5>
				<table class="table table-side-bordered table-white">
					<thead>
						<tr>
							<th>{{ trans_choice('site.manuscripts', 1) }}</th>
							<th>{{ trans_choice('site.learners', 1) }}</th>
							<th>{{ trans('site.grade') }}</th>
							<th>{{ trans('site.type') }}</th>
							<th>{{ trans('site.where') }}</th>
							<th>{{ trans_choice('site.words', 2) }}</th>
							<th>{{ trans('site.text-nr') }}</th>
							<th>{{ trans('site.feedback-out') }}</th>
							<th>{{ trans_choice('site.editors', 1) }}</th>
							<th width="250"></th>
						</tr>
					</thead>
					<tbody>
						@foreach( $assignment->manuscripts as $manuscript )
						<?php $extension = explode('.', basename($manuscript->filename)); ?>
						<tr>
							<td>
								@if( end($extension) == 'pdf' || end($extension) == 'odt' )
								<a href="/js/ViewerJS/#../..{{ $manuscript->filename }}">{{ basename($manuscript->filename) }}</a>
								@elseif( end($extension) == 'docx' )
								<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$manuscript->filename}}">{{ basename($manuscript->filename) }}</a>
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
							</td>
							<td>
								<?php $editor = $manuscript->editor_id ? \App\User::find($manuscript->editor_id) : '';?>

								{{ $editor ? $editor->full_name."\n" : "" }}
								<button class="btn btn-xs btn-primary assignEditorBtn" data-toggle="modal" data-target="#assignEditorModal"
								data-action="{{ route('assignment.group.assign_manu_editor', $manuscript->id) }}"
								data-editor="{{ $editor ? $editor->id : "" }}">
									{{ trans('site.assign-editor') }}
								</button>
							</td>
							<td>
								<div class="text-right">
									<a href="{{ route('assignment.group.download_manuscript', $manuscript->id) }}" class="btn btn-primary btn-xs">{{ trans('site.download') }}</a>
									<input type="checkbox" data-toggle="toggle" data-on="{{ trans('site.locked') }}"
										   class="lock-toggle" data-off="{{ trans('site.unlocked') }}"
										   data-id="{{$manuscript->id}}" data-size="mini" @if($manuscript->locked) {{ 'checked' }} @endif>
									<button type="button" class="btn btn-info btn-xs replaceManuscriptBtn" data-toggle="modal" data-target="#replaceManuscriptModal" data-action="{{ route('assignment.group.replace_manuscript', $manuscript->id) }}" data-grade="{{ $manuscript->grade }}" data-ass-type="{{ $manuscript->type }}" data-manu-type="{{ $manuscript->manu_type }}">{{ trans('site.replace-doc') }}</button>
									<div class="margin-top">
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
							</td>
						</tr>
						@endforeach
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td style="padding-right: 21%">
								<div class="text-right">
									<a href="{{ route('assignment.group.download_all_manuscript', $assignment->id) }}"
									   class="btn btn-primary btn-xs">
										{{ trans('site.download-all') }}
									</a>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
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
				$assignment_manuscripts_list = $assignment->manuscripts->pluck('id')->toArray();
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
										</td>
										<td>
											@if( $feedback->is_admin ) [Admin] @endif {{ basename($feedback->feedbackUser->full_name) }}
										</td>
										<td>
											{{ $feedback->learner->full_name }}
										</td>
										<td>
											{{ \App\Http\FrontendHelpers::formatDate($feedback->availability) }}
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
				<button type="button" class="pull-right btn btn-primary btn-sm margin-bottom" data-toggle="modal" data-target="#addGroupModal">{{ trans('site.create-group') }}</button>
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
							<td><a href="{{ route('admin.assignment-group.show', ['course_id' => $course->id, 'assignment_id' => $assignment->id, 'id' => $group->id]) }}">{{ $group->title }}</a></td>
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

<div id="addManuscriptModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">{{ trans('site.add-manuscript') }}</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="{{ route('assignment.group.upload_manuscript', $assignment->id) }}" enctype="multipart/form-data">
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
	      			<input type="file" class="form-control" required name="filename" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
	      			* Godkjente fil formater er DOCX, PDF og ODT.
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
				<form method="POST" action="" enctype="multipart/form-data">
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

					<button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.submit') }}</button>
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
		    <h4 class="modal-title">Set grade</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="">
		      {{ csrf_field() }}
		      <div class="form-group">
		      	<label>Grade</label>
		      	<input type="number" class="form-control" step="0.01" name="grade" required>
		      </div>
		      <button type="submit" class="btn btn-primary pull-right">Save</button>
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
		    <h4 class="modal-title">Create group</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="{{route('admin.assignment-group.store', ['course_id' => $course->id, 'id' => $assignment->id])}}">
		      {{ csrf_field() }}
		      <div class="form-group">
		      	<label>Group name</label>
		      	<input type="text" name="title" class="form-control" placeholder="Group name" required>
		      </div>
				<div class="form-group">
					<label>Submission Date</label>
					<input type="datetime-local" class="form-control" name="submission_date" required>
				</div>
				<div class="form-group">
					<label>Allow download all feedback</label> <br>
					<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
						   data-id="@if (isset($group)){{$group->allow_feedback_download}}@endif"
						   @if(isset($group) && $group->allow_feedback_download) {{ 'checked' }} @endif
						   name="allow_feedback_download">
				</div>
		      <button type="submit" class="btn btn-primary pull-right margin-top">Create</button>
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
		    <h4 class="modal-title">Delete assignment</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="{{route('admin.assignment.destroy', ['course_id' => $course->id, 'id' => $assignment->id])}}">
		      {{ csrf_field() }}
		      {{ method_field('DELETE') }} 
		      This will delete all manuscripts uploaded for this assignment, and all the groups created. <br />
		      Are you sure to delete this assignment?
		      <button type="submit" class="btn btn-danger pull-right margin-top">Delete</button>
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
		    <h4 class="modal-title">Edit assignment</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="{{route('admin.assignment.update', ['course_id' => $course->id, 'id' => $assignment->id])}}">
		      {{ csrf_field() }}
		      {{ method_field('PUT') }}
		      <div class="form-group">
		      	<label>Title</label>
		      	<input type="text" class="form-control" name="title" placeholder="Title" required value="{{ $assignment->title }}">
		      </div>
		      <div class="form-group">
		      	<label>Description</label>
		      	<textarea class="form-control" name="description" placeholder="Description" rows="6">{{ $assignment->description }}</textarea>
		      </div>
				<div class="form-group">
					<label>Submission Date</label>
					<input type="datetime-local" class="form-control" name="submission_date"
						   @if( $assignment->submission_date ) value="{{ strftime('%Y-%m-%dT%H:%M:%S', strtotime($assignment->submission_date)) }}" @endif
					required>
				</div>

				<div class="form-group">
					<label>Allowed Package</label>
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
					<label>Add On Price</label>
					<input type="number" class="form-control" name="add_on_price" value="{{ $assignment->add_on_price }}" required>
				</div>

				<div class="form-group">
					<label>Max words</label>
					<input type="number" class="form-control" name="max_words"
					value="{{ $assignment->max_words }}">
				</div>

				<div class="form-group">
					<label>For Editor</label> <br>
					<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" data-size="small" name="for_editor"
					@if ($assignment->for_editor) checked @endif>
				</div>

		      <button type="submit" class="btn btn-primary pull-right margin-top">Save</button>
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
				<h4 class="modal-title">Replace manuscript</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="form-group">
						<label>Manuscript</label>
						<input type="file" class="form-control" required name="filename" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
						* Godkjente fil formater er DOCX, PDF og ODT.
					</div>

					<div class="form-group margin-top">
						Sjanger
						<select class="form-control" name="type" id="ass_type" required>
							<option value="" disabled="disabled" selected>Select Type</option>
							@foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
								<option value="{{ $type['id'] }}"> {{ $type['option'] }} </option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						Hvor i manuset <br>
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
				<h4 class="modal-title">Delete manuscript</h4>
			</div>
			<div class="modal-body">
				Are you sure to delete this manuscript?
				Warning: This cannot be undone.
				<form method="POST" action="">
					{{ csrf_field() }}
					<button type="submit" class="btn btn-danger pull-right margin-top">Delete</button>
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
				<h4 class="modal-title">Move manuscript to assignment</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="">
					{{ csrf_field() }}
					<div class="form-group">
						<label>Move to Assignment</label>
						<select name="assignment_id" class="form-control" required>
							<option value="" disabled selected>Select Assignment</option>
							@foreach($assignments as $assign)
							<option value="{{ $assign->id }}">{{ $assign->title }}</option>
							@endforeach
						</select>
					</div>
					<button type="submit" class="btn btn-info pull-right margin-top">Move</button>
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
				<h4 class="modal-title">Replace type</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="">
					{{ csrf_field() }}
					<div class="form-group margin-top">
						Sjanger
						<select class="form-control" name="type" id="ass_type" required>
							<option value="" disabled="disabled" selected>Select Type</option>
							@foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
								<option value="{{ $type['id'] }}"> {{ $type['option'] }} </option>
							@endforeach
						</select>
					</div>
					<button type="submit" class="btn btn-primary pull-right margin-top">Submit</button>
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
				<h4 class="modal-title">Replace where to find</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="">
					{{ csrf_field() }}
					<div class="form-group">
						Hvor i manuset <br>
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


<div id="submitFeedbackModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Submit feedback to <em></em></h4>
			</div>
			<div class="modal-body">
				<form method="POST" action=""  enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="form-group">
						<label>Manuscript</label>
						<input type="file" class="form-control" required multiple name="filename[]" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
						* Accepted file formats are DOCX, PDF, ODT.
					</div>
					<div class="form-group">
						<label>Available date</label>
						<input type="date" class="form-control" name="availability">
					</div>
					<div class="form-group">
						<label>Grade</label>
						<input type="number" class="form-control" step="0.01" name="grade">
					</div>
					<button type="submit" class="btn btn-primary pull-right margin-top">Submit</button>
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
        var form = $('#assignEditorModal form');
        var action = $(this).data('action');
        var editor = $(this).data('editor');

        form.attr('action', action);
        form.find("select[name=editor_id]").val(editor);
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
</script>
@stop