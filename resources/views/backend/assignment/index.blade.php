@extends('backend.layout')

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
<title>Assignments &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3><i class="fa fa-file"></i> {{ ucwords(trans('site.all-assignments')) }}</h3>
	<div class="navbar-form navbar-right">
	  	<div class="form-group">
		  	<form role="search" method="get" action="">
				<div class="input-group">
				  	<input type="text" class="form-control" placeholder="{{ trans('site.search-assignment') }}..">
				    <span class="input-group-btn">
				    	<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
				    </span>
				</div>
			</form>
		</div>
	</div>
	<div class="clearfix"></div>
</div>

<div class="col-md-12">
	<ul class="nav nav-tabs margin-top">
		<li @if( !in_array(Request::input('tab'), ['learner', 'template'])) class="active" @endif>
			<a href="?tab=course">
				Course Assignments
			</a>
		</li>

		<li @if( Request::input('tab') == 'learner' ) class="active" @endif>
			<a href="?tab=learner">
				Learner Assignments
			</a>
		</li>

		<li @if( Request::input('tab') == 'template' ) class="active" @endif>
			<a href="?tab=template">
				Assignment Templates
			</a>
		</li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane fade in active">
			@if( Request::input('tab') == 'learner' )
				<div class="panel panel-default" style="border-top: 0">
					<div class="panel-body">
						<button type="button"
								class="btn btn-primary margin-bottom learnerAssignmentBtn" data-toggle="modal"
								data-target="#learnerAssignmentModal"
								data-action="{{ route('assignment.learner-assignment.save') }}"
						>
							{{ trans('site.add-assignment') }}
						</button>

						<button type="button"
								class="btn btn-success margin-bottom bulkLearnerAssignmentBtn" data-toggle="modal"
								data-target="#bulkLearnerAssignmentModal"
								data-action="{{ route('assignment.multiple-learner-assignment.save') }}"
						>
							Add Multiple Assignment
						</button>

						<div class="table-users table-responsive">
							<table class="table">
								<thead>
								<tr>
									<th>{{ trans_choice('site.assignments', 1) }}</th>
									<th>{{ trans('site.description') }}</th>
                                    <th width="250">{{ trans_choice('site.editors', 1) }}</th>
									<th>
										{{ trans_choice('site.learners', 1) }}
									</th>
									<th>
										{{ trans('site.submission-date') }}
									</th>
								</tr>
								</thead>

								<tbody>
									@foreach($learnerAssignments as $learnerAssignment)
										<tr>
											<td>
												<a href="{{ route('admin.learner.assignment',
												[$learnerAssignment->parent_id, $learnerAssignment->id]) }}">
													{{ $learnerAssignment->title }}
												</a>
											</td>
											<td>
												{{ $learnerAssignment->description }}
											</td>
                                            <td>
												<div>
													{{ $learnerAssignment->editor ? $learnerAssignment->editor->full_name : "" }}
												</div>
                                                <button class="btn btn-xs btn-primary assignEditorBtn" data-toggle="modal" data-target="#assignEditorModal"
                                                        data-action="{{ route('assignment.assign_editor', $learnerAssignment->id) }}"
                                                        data-editor="{{ $learnerAssignment->editor_id }}"
                                                        data-editor-name="{{ $learnerAssignment->editor ? $learnerAssignment->editor->full_name : "" }}"
														data-preferred-editor="{{ $learnerAssignment->learner->preferredEditor
								? $learnerAssignment->learner->preferredEditor->editor_id : '' }}"
														data-preferred-editor-name="{{ $learnerAssignment->learner->preferredEditor
								? $learnerAssignment->learner->preferredEditor->editor->full_name : '' }}">
                                                    {{ trans('site.assign-editor') }}
                                                </button>

                                                @if($learnerAssignment->editor)
                                                    <button class="btn btn-xs btn-danger removeEditorBtn"
                                                            data-action="{{ route('assignment.remove_editor', $learnerAssignment->id) }}"
                                                            data-toggle="modal" data-target="#removeEditorModal">
                                                        Remove Editor
                                                    </button>
                                                @endif
                                            </td>
											<td>
												<a href="{{ route('admin.learner.show', $learnerAssignment->parent_id) }}">
													{{ $learnerAssignment->learner->full_name }}
												</a>
											</td>
											<td>
												{{ $learnerAssignment->submission_date }}
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						<div class="pull-right">
							{{$learnerAssignments->appends(request()->except('page'))->links()}}
						</div>
					</div>
				</div>
			@elseif( Request::input('tab') == 'template' )

				<div class="panel panel-default" style="border-top: 0">
					<div class="panel-body">
						<button type="button"
								class="btn btn-success margin-bottom assignmentTemplateBtn" data-toggle="modal"
								data-target="#assignmentTemplateModal"
								data-action="{{ route('assignment.template.save') }}"
						>
							Assignment Template
						</button>

						<div class="table-users table-responsive">
							<table class="table">
								<thead>
								<tr>
									<th>{{ trans_choice('site.assignments', 1) }}</th>
									<th>{{ trans('site.description') }}</th>
									<th width="200"></th>
								</tr>
								</thead>

								<tbody>
								@foreach($templatePaginated as $assignmentTemplate)
									<tr>
										<td>
											{{ $assignmentTemplate->title }}
										</td>
										<td>
											{{ $assignmentTemplate->description }}
										</td>
										<td>
											<button class="btn btn-primary btn-xs assignmentTemplateBtn"
													data-toggle="modal"
													data-target="#assignmentTemplateModal"
													data-action="{{ route('assignment.template.save',
														$assignmentTemplate->id) }}"
													data-fields="{{ json_encode($assignmentTemplate) }}">
												<i class="fa fa-pencil"></i>
											</button>

											<button class="btn btn-danger btn-xs deleteAssignmentTemplateBtn"
													data-toggle="modal"
													data-target="#deleteAssignmentTemplateModal"
													data-action="{{ route('assignment.template.delete',
													$assignmentTemplate->id) }}">
												<i class="fa fa-trash"></i>
											</button>
										</td>
									</tr>
								@endforeach
								</tbody>
							</table>
						</div>
						<div class="pull-right">
							{{$templatePaginated->appends(request()->except('page'))->links()}}
						</div>
					</div>
				</div>

			@else
				<div class="panel panel-default" style="border-top: 0">
					<div class="panel-body">
						<div class="table-users table-responsive">
							<table class="table">
								<thead>
								<tr>
									<th>{{ trans('site.id') }}</th>
									<th>{{ trans('site.title') }}</th>
									<th>{{ trans_choice('site.courses', 1) }}</th>
									<th>{{ trans_choice('site.groups', 2) }}</th>
									<th>{{ trans('site.date-created') }}</th>
								</tr>
								</thead>

								<tbody>
								@foreach($assignments as $assignment)
									<tr>
										<td>{{$assignment->id}}</td>
										<td><a href="{{ route('admin.assignment.show',
										['course_id' => $assignment->course->id, 'assignment' => $assignment->id]) }}">
												{{$assignment->title}}
											</a>
										</td>
										<td>{{$assignment->course->title}}</td>
										<td>{{$assignment->groups->count()}}</td>
										<td>{{$assignment->created_at}}</td>
									</tr>
								@endforeach
								</tbody>
							</table>
						</div>
						<div class="pull-right">
							{{$assignments->appends(request()->except('page'))->links()}}
						</div>
					</div>
				</div>
			@endif
		</div>

		<div id="assignmentTemplateModal" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">
							Assignment Template
						</h4>
					</div>
					<div class="modal-body">
						<form method="POST" action="" onsubmit="disableSubmit(this)">
							{{ csrf_field() }}

							<div class="form-group">
								<label>{{ trans('site.title') }}</label>
								<input type="text" class="form-control" name="title"
									   placeholder="{{ trans('site.title') }}" required>
							</div>
							<div class="form-group">
								<label>{{ trans('site.description') }}</label>
								<textarea class="form-control" name="description"
										  placeholder="{{ trans('site.description') }}" rows="6"></textarea>
							</div>
							{{--<div class="form-group">
								<label>{{ trans('site.delay-type') }}</label>
								<select class="form-control assignment-delay-toggle">
									<option value="days">Days</option>
									<option value="date">Date</option>
								</select>
							</div>--}}
							<div class="form-group">
								<label>{{ trans('site.submission-date') }}</label>
								{{--<input type="datetime-local" class="form-control" name="submission_date" required>--}}
								<div class="input-group">
									<input type="datetime-local" class="form-control assignment-delay" name="submission_date"
										   required>
									<span class="input-group-addon assignment-delay-text" id="basic-addon2">
									days
									</span>
								</div>
							</div>

							<div class="form-group">
								<label>{{ trans('site.available-date') }}</label>
								<input type="date" class="form-control" name="available_date">
							</div>

							<div class="form-group">
								<label>{{ trans('site.max-words') }}</label>
								<input type="number" class="form-control" name="max_words" required>
							</div>

							<button type="submit" class="btn btn-success pull-right margin-top">
								{{ trans('site.save') }}
							</button>
							<div class="clearfix"></div>
						</form>
					</div>
				</div>
			</div>
		</div> <!-- end assignment template modal -->

		<div id="deleteAssignmentTemplateModal" class="modal fade" role="dialog">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">{{ trans('site.delete-learner') }}</h4>
					</div>
					<div class="modal-body">
						<form method="POST" action="">
							{{ csrf_field() }}
							{{ method_field('DELETE') }}

							<p>
								{!! trans('site.delete-item-question') !!}
							</p>

							<button type="submit" class="btn btn-danger pull-right">{{ trans('site.delete') }}</button>
							<div class="clearfix"></div>
						</form>
					</div>
				</div>

			</div>
		</div> <!-- end delete assignment template -->

		<div id="learnerAssignmentModal" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">
							Learner Assignment
						</h4>
					</div>
					<div class="modal-body">
						<form method="POST" action="" onsubmit="disableSubmit(this)">
							{{ csrf_field() }}

							<div class="form-group">
								<label>
									Assignment Template
								</label>
								<select class="form-control select2 template">
									<option value="" selected disabled>- Search Template -</option>
									@foreach($assignmentTemplates as $template)
										<option value="{{$template->id}}" data-fields="{{ json_encode($template) }}">
											{{$template->title}}
										</option>
									@endforeach
								</select>
							</div>

							<div class="form-group">
								<label>{{ trans('site.title') }}</label>
								<input type="text" class="form-control" name="title"
									   placeholder="{{ trans('site.title') }}" required>
							</div>
							<div class="form-group">
								<label>{{ trans('site.description') }}</label>
								<textarea class="form-control" name="description"
										  placeholder="{{ trans('site.description') }}" rows="6"></textarea>
							</div>
							{{--<div class="form-group">
								<label>{{ trans('site.delay-type') }}</label>
								<select class="form-control assignment-delay-toggle">
									<option value="days">Days</option>
									<option value="date">Date</option>
								</select>
							</div>--}}
							<div class="form-group">
								<label>{{ trans('site.submission-date') }}</label>
								{{--<input type="datetime-local" class="form-control" name="submission_date" required>--}}
								<div class="input-group">
									<input type="datetime-local" class="form-control assignment-delay" name="submission_date"
										   required>
									<span class="input-group-addon assignment-delay-text" id="basic-addon2">
										days
									</span>
								</div>
							</div>

							<div class="form-group">
								<label>{{ trans('site.available-date') }}</label>
								<input type="date" class="form-control" name="available_date">
							</div>
							<div class="form-group">
								<label>{{ trans('site.editor-expected-finish') }}</label>
								<input type="date" class="form-control" name="editor_expected_finish">
							</div>

							<div class="form-group">
								<label>{{ trans('site.max-words') }}</label>
								<input type="number" class="form-control" name="max_words">
							</div>

							<div class="form-group">
								<label>Allowed up to</label>
								<input type="number" class="form-control" name="allow_up_to"
								value="">
							</div>

							<div class="form-group">
								<label>{{ trans('site.send-letter-to-editor') }}</label> <br>
								<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" data-size="small"
									   name="send_letter_to_editor">
							</div>

							<div class="form-group">
								<label>{{ trans_choice('site.courses', 1) }}</label>
								<select class="form-control select2" name="course_id">
									<option value="" selected disabled>- Search Course -</option>
									@foreach(\App\Http\AdminHelpers::courseList() as $course)
										<option value="{{$course->id}}">
											{{$course->title}}
										</option>
									@endforeach
								</select>
							</div>

							<div class="form-group">
								<label>
									{{ trans_choice('site.learners', 1) }}
								</label>
								<select class="form-control select2" name="learner_id" required>
									<option value="" selected disabled>- Search Learner -</option>
									@foreach(\App\Http\AdminHelpers::getLearnerList() as $learner)
										<option value="{{$learner->id}}"
												data-preferred-editor="{{ $learner->preferredEditor ? $learner->preferredEditor->editor_id : '' }}">
											{{$learner->full_name}}
										</option>
									@endforeach
								</select>
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

							<button type="submit" class="btn btn-primary pull-right margin-top">
								{{ trans('site.save') }}
							</button>
							<div class="clearfix"></div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<div id="bulkLearnerAssignmentModal" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">
							Multiple Learner Assignment
						</h4>
					</div>
					<div class="modal-body">
						<form method="POST" action="" onsubmit="disableSubmit(this)">
							{{ csrf_field() }}

							<div class="form-group">
								<label>
									Assignment Template
								</label>
								<select name="templates[]" class="form-control select2 template" multiple="multiple">
									@foreach($assignmentTemplates as $template)
										<option value="{{$template->id}}" data-fields="{{ json_encode($template) }}">
											{{$template->title}}
										</option>
									@endforeach
								</select>
							</div>

							<div class="form-group">
								<label>{{ trans('site.editor-expected-finish') }}</label>
								<input type="date" class="form-control" name="editor_expected_finish">
							</div>

							<div class="form-group">
								<label>{{ trans('site.send-letter-to-editor') }}</label> <br>
								<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" data-size="small"
									   name="send_letter_to_editor">
							</div>

							<div class="form-group">
								<label>{{ trans_choice('site.courses', 1) }}</label>
								<select class="form-control select2" name="course_id">
									<option value="" selected disabled>- Search Course -</option>
									@foreach(\App\Http\AdminHelpers::courseList() as $course)
										<option value="{{$course->id}}">
											{{$course->title}}
										</option>
									@endforeach
								</select>
							</div>

							<div class="form-group">
								<label>
									{{ trans_choice('site.learners', 1) }}
								</label>
								<select class="form-control select2" name="learner_id" required>
									<option value="" selected disabled>- Search Learner -</option>
									@foreach(\App\Http\AdminHelpers::getLearnerList() as $learner)
										<option value="{{$learner->id}}"
												data-preferred-editor="{{ $learner->preferredEditor ? $learner->preferredEditor->editor_id : '' }}">
											{{$learner->full_name}}
										</option>
									@endforeach
								</select>
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

							<button type="submit" class="btn btn-primary pull-right margin-top">
								{{ trans('site.save') }}
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
                                    @foreach(\App\Http\AdminHelpers::editorList() as $editor)
                                        <option value="{{ $editor->id }}">
                                            {{ $editor->first_name . " " . $editor->last_name }}
                                        </option>
                                    @endforeach
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

        <div id="removeEditorModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Remove Editor</h4>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="" enctype="multipart/form-data">
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
	</div> <!-- end tab-content -->
	<div class="clearfix"></div>
</div>
@stop

@section('scripts')
	<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
	<script>
        $('#assignmentTemplateModal, #learnerAssignmentModal').find('.assignment-delay-toggle').change(function(){
            let delay = $(this).val();
            let modal = $("#assignmentTemplateModal, #learnerAssignmentModal");
            if(delay === 'days'){
                modal.find('.assignment-delay').attr('type', 'number');
            } else if(delay === 'date')
            {
                modal.find('.assignment-delay').attr('type', 'datetime-local');
            }
            modal.find('.assignment-delay-text').text(delay);
        });

		$(".assignmentTemplateBtn").click(function() {
            let action = $(this).data('action');
            let modal = $('#assignmentTemplateModal');
            modal.find('form').attr('action', action);

            if ($(this).data('fields')) {
                let fields = $(this).data('fields');
                let form = modal.find('form');
                form.find('[name=title]').val(fields.title);
                form.find('[name=description]').val(fields.description);
                form.find('[name=available_date]').val(fields.available_date);
                form.find('[name=max_words]').val(fields.max_words);

                if (fields.submission_is_date) {
                    $(".assignment-delay-toggle").val("date").trigger('change');
				} else {
                    $(".assignment-delay-toggle").val("days").trigger('change');
				}

                form.find('[name=submission_date]').val(fields.submission_date);
			} else {
                modal.find(".form-control").val('');
                modal.find('.assignment-delay-toggle').val('days').trigger('change');
			}
		});

		$(".deleteAssignmentTemplateBtn").click(function() {
            let action = $(this).data('action');
            let modal = $('#deleteAssignmentTemplateModal');
            modal.find('form').attr('action', action);
		});

		$("select.template").change(function(){
            let template = $(this).children("option:selected");
			let fields = template.data('fields');
			let modal = $("#learnerAssignmentModal");
            let form = modal.find('form');
            form.find('[name=title]').val(fields.title);
            form.find('[name=description]').val(fields.description);
            form.find('[name=available_date]').val(fields.available_date);
            form.find('[name=max_words]').val(fields.max_words);

            if (fields.submission_is_date) {
                $(".assignment-delay-toggle").val("date").trigger('change');
            } else {
                $(".assignment-delay-toggle").val("days").trigger('change');
            }

            form.find('[name=submission_date]').val(fields.submission_date);
        });

        $(".learnerAssignmentBtn").click(function() {
            let action = $(this).data('action');
            let modal = $('#learnerAssignmentModal');
            modal.find('form').attr('action', action);
        });

        $(".bulkLearnerAssignmentBtn").click(function() {
            let action = $(this).data('action');
            let modal = $('#bulkLearnerAssignmentModal');
            modal.find('form').attr('action', action);
        });

        $(".assignEditorBtn").click(function(){

            let modal = $("#assignEditorModal");
            let form = modal.find('form');
            let action = $(this).data('action');
            let editor = $(this).data('editor');
            let editor_name = $(this).data('editor-name');
            let preferred_editor = $(this).data('preferred-editor');
            let preferred_editor_name = $(this).data('preferred-editor-name');

            form.attr('action', action);
            form.find("select[name=editor_id]").val(editor ? editor : (preferred_editor ? preferred_editor :'')).trigger('change');

            if (editor || preferred_editor) {
                modal.find('.select2').hide();
                modal.find('.hidden-container').show();
                modal.find('.hidden-container').find('label').empty().text(editor ? editor_name : preferred_editor_name);
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

        $("select[name='learner_id']").change(function() {
            let preferred_editor = $(this).select2("data")[0].element.dataset['preferredEditor'];
            if (preferred_editor) {
                $("#learnerAssignmentModal").find('form').find("select[name=editor_id]")
					.val(preferred_editor).trigger('change');
			} else {
                $("#learnerAssignmentModal").find('form').find("select[name=editor_id]").val('').trigger('change')
			}
		});
	</script>
@stop