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
			<button type="button" class="btn btn-sm btn-primary margin-bottom" data-toggle="modal" data-target="#addAssignmentModal">Add assignment</button>
			<div class="table-responsive">
				<table class="table table-side-bordered table-white">
					<thead>
						<tr>
							<th>{{ trans_choice('site.assignments', 1) }}</th>
							<th>{{ trans_choice('site.manuscripts', 2) }}</th>
						</tr>
					</thead>
					<tbody>
						@foreach( $course->assignments as $assignment )
						<tr>
							<td><a href="{{ route('admin.assignment.show', ['course_id' => $course->id, 'id' => $assignment->id]) }}">{{ $assignment->title }}</a></td>
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
		    <h4 class="modal-title">Add assignment</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="{{route('admin.assignment.store', $course->id)}}">
		      {{ csrf_field() }}
		      <div class="form-group">
		      	<label>Title</label>
		      	<input type="text" class="form-control" name="title" placeholder="Title" required>
		      </div>
		      <div class="form-group">
		      	<label>Description</label>
		      	<textarea class="form-control" name="description" placeholder="Description" rows="6"></textarea>
		      </div>
				<div class="form-group">
					<label>Submission Date</label>
					<input type="datetime-local" class="form-control" name="submission_date" required>
				</div>

				<div class="form-group">
					<label>Allowed Package</label>
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
					<label>Add On Price</label>
					<input type="number" class="form-control" name="add_on_price" required>
				</div>

				<div class="form-group">
					<label>Max words</label>
					<input type="number" class="form-control" name="max_words">
				</div>

				<div class="form-group">
					<label>For Editor</label> <br>
					<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" data-size="small" name="for_editor">
				</div>

		      <button type="submit" class="btn btn-primary pull-right margin-top">Add</button>
		      <div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>
@stop

@section('scripts')
	<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
@stop