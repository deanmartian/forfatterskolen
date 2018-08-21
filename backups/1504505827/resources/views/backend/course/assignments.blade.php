@extends('backend.layout')

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
							<th>Assignment</th>
							<th>Manuscripts</th>
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
		      <button type="submit" class="btn btn-primary pull-right margin-top">Add</button>
		      <div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>
@stop