@extends('backend.layout')

@section('title')
<title>Learners &rsaquo; {{$course->title}} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')

@include('backend.course.partials.toolbar')


<div class="course-container">
	
	@include('backend.partials.course_submenu')

    <?php
    $search = Request::input('search');
    if( $search ) :
        $learners = $course->learners
            ->whereHas('user', function($query) use ($search){
                $query->where('first_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('email', 'LIKE', '%' . $search . '%')
                ;
            })
            ->paginate(25);
    else :
        $learners = $course->learners->paginate(25);
    endif;
    ?>

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12 col-md-12">
			<form class="pull-right form-inline" method="GET">
				<div class="input-group">
					<input type="hidden" name="section"  value="{{ Request::input('section') }}">
				    <input type="search" class="form-control" name="search" placeholder="{{ trans('site.search') }}" value="{{ Request::input('search') }}">
				    <div class="input-group-btn">
				      <button class="btn btn-default" type="submit">
				        <i class="glyphicon glyphicon-search"></i>
				      </button>
				    </div>
				  </div> 
			</form>
			<button type="button" class="btn btn-primary margin-bottom" data-toggle="modal" data-target="#addLearnerModal">+ Add Learner</button>
			@if(count($learners) > 0)
				<button type="button" class="btn btn-success margin-bottom" data-toggle="modal" data-target="#sendEmailModal">Send Email</button>
				<a href="{{ route('learner.course.learner-list-excel', $course->id) }}" class="btn btn-default margin-bottom">Export Learners</a>
			@endif
			<div class="table-responsive">
				<table class="table table-side-bordered table-white">
					<thead>
						<tr>
							<th>{{ trans_choice('site.learners', 1) }}</th>
							<th>{{ trans('site.learner-id') }}</th>
							<th>{{ trans_choice('site.packages', 1) }}</th>
							<th>{{ trans('site.progress') }}</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						@if(count($learners) > 0)
						@foreach( $learners as $learner)
						<tr>
							<td><a href="{{route('admin.learner.show', $learner->user->id)}}">{{$learner->user->full_name}}</a></td>
							<td>{{ $learner->user->id }}</td>
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
			<div class="pull-right">{!! $learners->appends(Request::all())->render() !!}</div>
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

	<!--send email modal-->

<div id="sendEmailModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Send email</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{route('learner.course.send-email-to-learners', $course->id)}}"onsubmit="formSubmitted()">
				{{csrf_field()}}

					<div class="form-group">
						<label>Subject</label>
						<input type="text" class="form-control" name="subject" required>
					</div>
					
					<div class="form-group">
						<label>Message</label>
						<textarea name="message" id="" cols="30" rows="10" class="form-control" required></textarea>
					</div>
					<div class="text-right">
						<input type="submit" class="btn btn-primary" value="Send" id="send_email_btn">
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!--end email modal-->

@stop

@section('scripts')
	<script>
		function formSubmitted() {
		    var send_email = $("#send_email_btn");
            send_email.val('Sending....').attr('disabled', true);
		}
	</script>
@stop