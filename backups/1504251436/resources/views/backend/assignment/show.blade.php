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
			<br />
			
			<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addLearnerModal">Add learner</button>
			<div class="row"> 
				@foreach( $assignment->learners as $learner )
				<div class="col-sm-4">
					<div class="panel panel-default margin-top">
						<div class="panel-body">
							<button class="btn btn-danger btn-xs pull-right removeLearnerBtn" data-action="{{route('admin.assignment.removeLearner', ['course_id' => $course->id, 'assignment_id' => $assignment->id, 'id' => $learner->id])}}" data-toggle="modal" data-target="#removeLearnerModal"><i class="fa fa-trash"></i></button>
							<h4>{{ $learner->user->full_name }}</h4>
							<p class="margin-top no-margin-bottom">
								@if( $learner->filename )
								@else
									<em>No document uploaded</em>
								@endif
							</p>
						</div>
					</div>
				</div>
				@endforeach
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
</div>


<div id="removeLearnerModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">Remove learner</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="">
		      {{ csrf_field() }}
		      Are you sure to remove this learner?
		      <button type="submit" class="btn btn-danger pull-right margin-top">Delete</button>
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


<?php $learnerIDs = $assignment->learners->pluck('user_id')->toArray(); ?>
<div id="addLearnerModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">Add learner</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="{{route('admin.assignment.addLearner', ['course_id' => $course->id, 'assignment_id' => $assignment->id])}}">
		      {{ csrf_field() }}
		      <select class="form-control select2" name="user_id">
		      	<option disabled selected value="">- Search learner -</option>
		      	@foreach( App\User::whereNotIn('id', $learnerIDs)->get() as $user )
		      	<option value="{{ $user->id }}">{{ $user->full_name }}</option>
		      	@endforeach
		      </select>
		      <button type="submit" class="btn btn-primary pull-right margin-top">Add</button>
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