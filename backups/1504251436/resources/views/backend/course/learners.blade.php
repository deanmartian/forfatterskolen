@extends('backend.layout')

@section('title')
<title>Learners &rsaquo; {{$course->title}} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')

@include('backend.course.partials.toolbar')


<div class="course-container">
	
	@include('backend.partials.course_submenu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12 col-md-12">
			<button type="button" class="btn btn-primary margin-bottom" data-toggle="modal" data-target="#addLearnerModal">+ Add Learner</button>
			<div class="table-responsive">
				<table class="table table-side-bordered table-white">
					<thead>
						<tr>
							<th>Learner</th>
							<th>Package</th>
							<th>Progress</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						@if(count($course->learners) > 0)
						@foreach($course->getLearnersAttribute()->paginate(25) as $learner)
						<tr>
							<td><a href="{{route('admin.learner.show', $learner->user->id)}}">{{$learner->user->full_name}}</a></td>
							<td>{{$learner->package->variation}}</td>
							<td>
								<div class="progress learner-progress">
								  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="70"
								  aria-valuemin="0" aria-valuemax="100" style="width:70%">
								  70% Complete
								  </div>
								</div>
							</td>
							<td>
								<button type="submit" data-toggle="modal" data-target="#removeLearnerModal" class="btn btn-danger btn-xs pull-right btn-remove-learner" data-learner="{{$learner->user->full_name}}" data-package="{{$learner->package->id}}" data-learner-id="{{$learner->user->id}}">Remove Learner</button>
							</td>
						</tr>
						@endforeach
						@endif
					</tbody>
				</table>
			</div>

			@if(count($course->learners) > 0)
			<div class="pull-right">{!! $course->getLearnersAttribute()->paginate(25)->appends(Request::all())->render() !!}</div>
			<div class="clearfix"></div>
			@endif
		</div>
	</div>
	<div class="clearfix"></div>
</div>


<!-- Remove Learner Modal -->
<div id="removeLearnerModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Are you sure to remove <strong id="learner_name"></strong> from <strong>{{$course->title}}</strong>?</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{route('learner.course.remove.learner')}}">
      		{{csrf_field()}}
      		<input type="hidden" name="learner_id">
      		<input type="hidden" name="package_id">
      		<button type="submit" class="btn btn-danger btn-block">Remove Learner</button>
      	</form>
      </div>
    </div>

  </div>
</div>


<!-- Add Learner Modal -->
<div id="addLearnerModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Learner to {{$course->title}}</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{route('learner.course.add.learner')}}">
      		{{csrf_field()}}
      		<div class="form-group">
      			<select class="form-control select2" name="learner_id" required>
      				<option value="" selected disabled>- Search Learner -</option>
					@if(count($course->learners) > 0)
	      				@foreach(AdminHelpers::courseAddLearners($course->learners->pluck('user_id')->toArray()) as $learner)
	      				<option value="{{$learner->id}}">{{$learner->full_name}} ({{ $learner->email }})</option>
	      				@endforeach
      				@else
	      				@foreach(App\User::where('role', 2)->orderBy('first_name', 'asc')->get() as $learner)
	      				<option value="{{$learner->id}}">{{$learner->full_name}}</option>
	      				@endforeach
      				@endif
      			</select>
      		</div>
      		<div class="form-group">
      			<select class="form-control" name="package_id" required>
      				<option value="" selected disabled>- Select Package -</option>
      				@foreach($course->packages as $package)
      				<option value="{{$package->id}}">{{$package->variation}}</option>
      				@endforeach
      			</select>
      		</div>
      		<div class="text-right">
      			<button type="submit" class="btn btn-primary">Add Learner</button>
      		</div>
      	</form>
      </div>
    </div>

  </div>
</div>

@stop