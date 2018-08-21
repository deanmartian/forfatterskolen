@extends('backend.layout')

@section('title')
<title>{{ $assignment->title }} &rsaquo; {{$course->title}} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')

@include('backend.course.partials.toolbar')


<div class="course-container">
	
	@include('backend.partials.course_submenu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12">
			<a href="{{ route('admin.course.show', $course->id) }}?section=assignments" class="btn btn-sm btn-default margin-bottom" ><i class="fa fa-angle-left"></i> All assignments</a>

			<div class="pull-right">
				<button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#editAssignmentModal"><i class="fa fa-pencil"></i></button>
				<button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteAssignmentModal"><i class="fa fa-trash"></i></button>
			</div>
			
			<h3 class="no-margin-bottom">{{ $assignment->title }}</h3>
			<p class="margin-bottom">{{ $assignment->description }}</p>
			
			<div class="table-responsive">
				<h5>Manuscripts</h5>
				<table class="table table-side-bordered table-white">
					<thead>
						<tr>
							<th>Manuscript</th>
							<th>Learner</th>
						</tr>
					</thead>
					<tbody>
						@foreach( $assignment->manuscripts as $manuscript )
						<tr>
							<td><a href="">{{ basename($manuscript->filename) }}</a></td>
							<td>{{ $manuscript->user->full_name }}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>	
	
			<br />
			<div class="table-responsive margin-top">
				<button type="button" class="pull-right btn btn-primary btn-sm margin-bottom" data-toggle="modal" data-target="#addGroupModal">Create group</button>
				<h5>Groups</h5>
				<table class="table table-side-bordered table-white">
					<thead>
						<tr>
							<th>Group</th>
							<th>Learners</th>
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
		      <button type="submit" class="btn btn-primary pull-right margin-top">Save</button>
		      <div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>

@stop

@section('scripts')
<script>
	$('.removeLearnerBtn').click(function(){
		var form = $('#removeLearnerModal form');
		var action = $(this).data('action');
		form.attr('action', action)
	});
</script>
@stop