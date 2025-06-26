@extends('backend.layout')

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
<title>Assignments &rsaquo; {{$course->title}} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')

@include('backend.course.partials.toolbar')


<div class="course-container">
	
	@include('backend.partials.course_submenu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12">
			<button type="button" class="btn btn-sm btn-primary margin-bottom" data-toggle="modal" data-target="#addAssignmentModal">{{ trans('site.add-assignment') }}</button>
			<div class="table-responsive">
				<table class="table table-side-bordered table-white">
					<thead>
						<tr>
							<th>{{ trans_choice('site.assignments', 1) }}</th>
							<th>Linked Assignment</th>
							<th>{{ trans_choice('site.manuscripts', 2) }}</th>
						</tr>
					</thead>
					<tbody>
						@foreach( $course->assignments as $assignment )
						<tr>
							<td><a href="{{ route('admin.assignment.show', ['course_id' => $course->id, 'assignment' => $assignment->id]) }}">{{ $assignment->title }}</a></td>
							<td>
								@if($assignment->parent === 'assignment')
									<a href="{{ route('admin.assignment.show', ['course_id' => $course->id,
									 'assignment' => $assignment->linkedAssignment->id]) }}">
										{{ $assignment->linkedAssignment->title }}
									</a>
								@endif
							</td>
							<td>{{ $assignment->manuscripts->count() }}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>	
		</div>
	</div>
	<div class="clearfix"></div>
</div>

<div id="addAssignmentModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">{{ trans('site.add-assignment') }}</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="{{route('admin.assignment.store', $course->id)}}" onsubmit="disableSubmit(this)">
		      {{ csrf_field() }}
		      <div class="form-group">
		      	<label>{{ trans('site.title') }}</label>
		      	<input type="text" class="form-control" name="title" placeholder="{{ trans('site.title') }}" required>
		      </div>
		      <div class="form-group">
		      	<label>{{ trans('site.description') }}</label>
		      	<textarea class="form-control" name="description" placeholder="{{ trans('site.description') }}" rows="6"></textarea>
		      </div>
				<div class="form-group">
					<label>{{ trans('site.delay-type') }}</label>
					<select class="form-control" id="assignment-delay-toggle">
						<option value="days">Days</option>
						<option value="date">Date</option>
					</select>
				</div>
				<div class="form-group">
					<label>{{ trans('site.submission-date') }}</label>
					{{--<input type="datetime-local" class="form-control" name="submission_date" required>--}}
					<div class="input-group">
						<input type="number" class="form-control" name="submission_date" id="assignment-delay" min="0"
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
					<label>{{ trans('site.allowed-package') }}</label>
						@foreach($course->packages as $package)
						<div class="form-check">
							<input class="form-check-input" type="checkbox" value="{{ $package->id }}" name="allowed_package[]">
							<label class="form-check-label" for="{{ $package->variation }}">
								{{ $package->variation }}
							</label>
						</div>
						@endforeach
				</div>

				<div class="form-group">
					<label>{{ trans('site.add-on-price') }}</label>
					<input type="number" class="form-control" name="add_on_price" required>
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
					<label>{{ trans('site.editor-expected-finish') }}</label>
					<input type="date" class="form-control" name="editor_expected_finish">
				</div>
				<div class="form-group">
					<label>Linked Assignment</label>
					<select name="linked_assignment" id="" class="form-control">
						<option value="" disabled selected="">- Select Assignment -</option>
						@foreach($course->assignments as $assignment)
							<option value="{{ $assignment->id }}">
								{{ $assignment->title }}
							</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label>{{ trans('site.for-editor') }}</label> <br>
					<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" data-size="small" name="for_editor">
				</div>

				<div class="form-group hide" id="editor_manu_gen_count">
					<label>{{ trans('site.manuscript-generate-count') }}</label>
					<input type="number" name="editor_manu_generate_count" class="form-control" step="1">
				</div>

				<div class="form-group">
					<label>{{ trans('site.show-join-group-question') }}</label> <br>
					<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" data-size="small" name="show_join_group_question"
					checked>
				</div>

				<div class="form-group">
					<label>Check Max Words</label> <br>
					<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" data-size="small" name="check_max_words"
					checked>
				</div>

				<div class="form-group hidden" id="assigned-editor-container">
					<label>Assigned Editor</label> <br>
					<select name="assigned_editor" id="" class="form-control">
						<option value="" disabled selected="">- Select Editor -</option>
						@foreach(AdminHelpers::editorList() as $editor)
							<option value="{{ $editor->id }}">
								{{ $editor->full_name }}
							</option>
						@endforeach
					</select>
				</div>

				<div class="form-group">
					<label>{{ trans('site.send-letter-to-editor') }}</label> <br>
					<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" data-size="small" name="send_letter_to_editor">
				</div>

		      <button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.add') }}</button>
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

        $("[name=for_editor]").change(function(){
            $("#editor_manu_gen_count").toggleClass('hide');
		});

		$("[name=check_max_words]").change(function(){
			$("#assigned-editor-container").toggleClass('hidden');
		});
	</script>
@stop