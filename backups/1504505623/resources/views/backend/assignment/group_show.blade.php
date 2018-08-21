@extends('backend.layout')

@section('title')
<title>{{ $group->title }} &rsaquo; {{$course->title}} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')

@include('backend.course.partials.toolbar')


<div class="course-container">
	
	@include('backend.partials.course_submenu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12">
			<a href="{{ route('admin.assignment.show', ['course_id' => $course->id, 'assignment_id' => $assignment->id]) }}" class="btn btn-sm btn-default margin-bottom" ><i class="fa fa-angle-left"></i> {{ $assignment->title }}</a>

			<div class="pull-right">
				<button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#editGroupModal"><i class="fa fa-pencil"></i></button>
				<button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteGroupModal"><i class="fa fa-trash"></i></button>
			</div>
				
			<div class="text-center">
				<h3 class="no-margin-bottom">{{ $group->title }}</h3>
				<p class="margin-bottom">Assignment: {{ $group->assignment->title }}</p>
			</div>
			
			<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addLearnerModal">Add learner</button>
			<div class="row"> 
				@foreach( $group->learners as $learner )
				<div class="col-sm-4">
					<div class="panel panel-default margin-top">
						<div class="panel-body">
							<button class="btn btn-danger btn-xs pull-right removeLearnerBtn" data-action="{{route('assignment.group.remove_learner', ['course_id' => $course->id, 'assignment_id' => $assignment->id, 'group_id' => $group->id, 'id' => $learner->id])}}" data-toggle="modal" data-target="#removeLearnerModal"><i class="fa fa-trash"></i></button>
							<h4>{{ $learner->user->full_name }}</h4>
							<p class="margin-top no-margin-bottom">
								<?php $filename = $assignment->manuscripts->where('user_id', $learner->user_id)->first(); ?>
								@if( $filename )
									<a href="">{{ basename($filename->filename) }}</a>
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


<?php 
$manuscriptUsers = $assignment->manuscripts->pluck('user_id')->toArray(); 
$groupLearners = $group->learners->pluck('user_id')->toArray(); 
?>
<div id="addLearnerModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">Add learner</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="{{route('assignment.group.add_learner', ['course_id' => $course->id, 'assignment_id' => $assignment->id, 'id' => $group->id])}}">
		      {{ csrf_field() }}
		      <label>Learners who submitted a manuscript for this assignment</label>
		      <select class="form-control select2" name="user_id">
		      	<option disabled selected value="">- Search learner -</option>
		      	@foreach( App\User::whereIn('id', $manuscriptUsers)->whereNotIn('id', $groupLearners)->get() as $user )
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

<div id="deleteGroupModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">Delete group</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="{{route('admin.assignment-group.destroy', ['course_id' => $course->id, 'assignment_id' => $assignment->id, 'id' => $group->id])}}">
		      {{ csrf_field() }}
		      {{ method_field('DELETE') }}
		      Are you sure to delete this group?
		      <br />
		      <button type="submit" class="btn btn-danger pull-right margin-top">Delete</button>
		      <div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>


<div id="editGroupModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">Edit group</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="{{route('admin.assignment-group.update', ['course_id' => $course->id, 'assignment_id' => $assignment->id, 'id' => $group->id])}}">
		      {{ csrf_field() }}
		      {{ method_field('PUT') }}
		      <div class="form-group">
		      	<label>Title</label>
		      	<input type="text" class="form-control" name="title" placeholder="Title" required value="{{ $group->title }}">
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