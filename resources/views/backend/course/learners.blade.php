@extends('backend.layout')

@section('title')
<title>Learners &rsaquo; {{$course->title}} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
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

    	if (is_numeric($search)) :
            $emailOutLearnerSearch = $course->emailOutLog()->where('id', $search)->first();
            if ($emailOutLearnerSearch) {
                $emailOutLearnerSearch = json_decode($emailOutLearnerSearch->learners);
                $learners = $course->learners->whereIn('user_id', $emailOutLearnerSearch)->paginate(25);
            }
		endif;

    else :
        $learners = $course->learnersWithExpired->paginate(25);
    endif;

    $packageIdsOfCourse = $course->packages()->pluck('id')->toArray();
    $packageCourses = \App\PackageCourse::whereIn('included_package_id', $packageIdsOfCourse)->get()
        ->pluck('package_id')
        ->toArray();

    $learnerWithCourse = \App\CoursesTaken::whereIn('package_id', $packageCourses)
        ->where('is_active', true)
        ->orderBy('updated_at', 'desc')
        ->get();

    $hasActiveUsers = 0;
    if ($learnerWithCourse->count()) {
        $hasActiveUsers = 1;
	}

    $emailOutLog = $course->emailOutLog()->paginate(20);
    $expiryReminder = $course->expiryReminders;
    $paymentPlans = \App\PaymentPlan::orderBy('division')->orderBy('plan')->get();
    $coursePaymentPlans = collect($course->payment_plan_ids ?? []);
    ?>

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12 col-md-12">
			<form class="pull-right form-inline" method="GET">
				<div class="input-group">
					<input type="hidden" name="section"  value="{{ Request::input('section') }}">
				    <input type="search" class="form-control" name="search" placeholder="{{ trans('site.search') }}" value="{{ is_numeric(Request::input('search'))
				    ?'': Request::input('search') }}">
				    <div class="input-group-btn">
				      <button class="btn btn-default" type="submit">
				        <i class="glyphicon glyphicon-search"></i>
				      </button>
				    </div>
				  </div>
			</form>
			<button type="button" class="btn btn-primary margin-bottom" data-toggle="modal" data-target="#addLearnerModal">+ {{ trans('site.add-learner') }}</button>
			{{-- <button type="button" class="btn btn-primary margin-bottom" data-toggle="modal" data-target="#addBulkLearnerModal">
				+ Add Bulk Learner
			</button> --}}
			@if(count($learners) > 0)
				<button type="button" class="btn btn-success margin-bottom loadScriptButton" data-toggle="modal" data-target="#sendEmailModal">{{ trans('site.send-email') }}</button>
				<a href="{{ route('learner.course.learner-list-excel', $course->id) }}" class="btn btn-default margin-bottom">{{ trans('site.export-learners') }}</a>
				<a href="{{ route('learner.course.learner-list-excel', $course->id) .'/address' }}" class="btn btn-default margin-bottom">
					Export Learner Address
				</a>
				<button type="button" class="btn btn-primary margin-bottom" data-toggle="modal"
						data-target="#addLearnersToWebinarsModal">
					Add learners to webinar
				</button>
				<button type="button" class="btn btn-success margin-bottom" data-toggle="modal"
						data-target="#certificateDatesModal">
					Certificate Dates
				</button>
				<button type="button" class="btn btn-success margin-bottom" data-toggle="modal"
						data-target="#coachingTimeModal">
					Add Coaching Time
				</button>
				<a href="{{ route('learner.course.pay-later', $course->id) }}" 
					class="btn btn-default margin-bottom">
					Export Pay Later Learners
				</a>
				@if ($course->is_free)
					<button type="button" class="btn btn-info margin-bottom" data-toggle="modal"
							data-target="#reminderEmailModal">Send Reminder</button>

					<button type="button" class="btn btn-primary margin-bottom" data-toggle="modal"
							data-target="#setEndDateModal">Set Course Taken End Date</button>
				@endif
			@endif

			{{-- for webinar pakke only --}}
			@if (/*$hasActiveUsers*/ $course->id == 17)
				<a href="{{ route('learner.course.learner-active-list-excel', $course->id) }}" class="btn btn-info margin-bottom">{{ trans('site.export-active-learners') }}</a>
				<button class="btn btn-primary margin-bottom" data-toggle="modal" data-target="#expirationEmailReminder">
					Expiration Email Reminder
				</button>
			@endif

			<ul class="nav nav-tabs margin-top">
				<li class="active"><a href="#learners" data-toggle="tab">Learners</a></li>
				<li><a href="#logs" data-toggle="tab">Email Out Log</a></li>
				<li><a href="#packages" data-toggle="tab">Packages</a></li>
				<li><a href="#payLaterOptions" data-toggle="tab">Pay Later Options</a></li>
				@if ($course->is_free)
						<li><a href="#templateTab" data-toggle="tab">Email Reminder Template</a></li>
				@endif
			</ul>

			<div class="tab-content">
				<div class="tab-pane fade in active margin-top" id="learners" role="tabpanel">
					<div class="table-responsive">
						<table class="table table-side-bordered table-white">
							<thead>
							<tr>
								<th>{{ trans_choice('site.learners', 1) }}</th>
								<th>{{ trans('site.learner-id') }}</th>
								<th>{{ trans_choice('site.packages', 1) }}</th>
								<th>Preferred Editor</th>
								<th>Include in email list</th>
								<th>Exclude in Scheduled Registration</th>
								<th>Facebook Group</th>
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
											{{--<div class="progress learner-progress">
												<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="70"
													 aria-valuemin="0" aria-valuemax="100" style="width:70%">
													70% Complete
												</div>
											</div>--}}
											{{ $learner->user->preferredEditor ? $learner->user->preferredEditor->editor->fullname : '' }}
										</td>
										<td>
											<input type="checkbox" data-toggle="toggle" data-on="Yes"
												   class="receive-email-toggle" data-off="No" data-id="{{ $learner->id }}"
												   name="can_receive_email" data-size="mini" @if($learner->can_receive_email) {{ 'checked' }} @endif>
										</td>
										<td>
											<input type="checkbox" data-toggle="toggle" data-on="Yes"
												   class="exclude-in-registration-toggle" data-off="No" data-id="{{ $learner->id }}"
												   name="exclude_in_scheduled_registration" data-size="mini" 
												   @if($learner->exclude_in_scheduled_registration) {{ 'checked' }} @endif>
										</td>
										<td>
											<input type="checkbox" data-toggle="toggle" data-on="Yes"
												   class="facebook-group-toggle" data-off="No" data-id="{{ $learner->id }}"
												   name="in_facebook_group" data-size="mini" @if($learner->in_facebook_group) {{ 'checked' }} @endif>
										</td>
										<td>
											@if (!$learner->deleted_at)
												<button type="submit" data-toggle="modal" data-target="#removeLearnerModal" 
												class="btn btn-danger btn-xs pull-right btn-remove-learner"
												data-learner="{{$learner->user->full_name}}" data-package="{{$learner->package->id}}"
												data-learner-id="{{$learner->user->id}}">{{ trans('site.remove-learner') }}</button>
											@else
												<button type="submit" data-toggle="modal" data-target="#removeLearnerModal" 
													class="btn btn-danger btn-xs pull-right btn-remove-learner-permanently"
													data-learner="{{$learner->user->full_name}}" data-package="{{$learner->package->id}}"
													data-learner-id="{{$learner->user->id}}">Delete Permanently</button>
											@endif
											
										</td>
									</tr>
								@endforeach
							@endif
							</tbody>
						</table>
					</div>

					@if($course->learners->count() > 0)
						<div class="pull-right">{!! $learners->appends(Request::all())->render() !!}</div>
						<div class="clearfix"></div>
					@endif
				</div> <!-- end learner-tab -->

				<div class="tab-pane fade margin-top" id="logs" role="tabpanel">
					<div class="table-responsive">
						<table class="table table-side-bordered table-white">
							<thead>
							<tr>
								<th>Subject</th>
								<th>Message</th>
								<th width="200">Date Sent</th>
								<th>From</th>
								<th>Attachment</th>
								<th></th>
							</tr>
							</thead>
							<tbody>
								@foreach($emailOutLog as $log)
									<tr>
										<td>{{ $log->subject }}</td>
										<td>{!!  $log->message !!}</td>
										<td>{{ $log->date_sent }}</td>
										<td>
											{{ $log->from_name ?: 'Forfatterskolen' }} <br>
											{{ $log->from_email ?: 'post@forfaterskolen.no' }}
										</td>
										<td>
											<a href="{{ asset($log->attachment) }}" download>
												{{ $log->attachment
													? \App\Http\AdminHelpers::extractFileName($log->attachment)
													: '' }}
											</a>
										</td>
										<td>
											@if($log->learners)
												<form class="" method="GET">
													<div class="input-group">
														<input type="hidden" name="section" value="{{ Request::input('section') }}">
														<input type="hidden" class="form-control" name="search" value="{{ $log->id }}">
														<button class="btn btn-primary" type="submit">
															Filter Learners
														</button>
													</div>
												</form>
											@endif
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>

					@if(count($emailOutLog) > 0)
						<div class="pull-right">{!! $emailOutLog->appends(Request::all())->render() !!}</div>
						<div class="clearfix"></div>
					@endif
				</div> <!-- end send email out log -->

				<div class="tab-pane fade margin-top" id="templateTab" role="tabpanel">
					<div class="table-responsive">
						<!-- Reminder Email -->
						<div class="col-sm-12">
							<div class="panel panel-default ">
								<div class="panel-heading">
									<button type="button" class="btn btn-primary btn-xs pull-right" data-toggle="modal"
											data-target="#reminderEmailTemplateModal"><i class="fa fa-pencil"></i></button>
									<h4>Reminder Email</h4>
								</div>
								<div class="panel-body">
									{!! nl2br(App\Settings::courseNotStartedReminder()) !!}
								</div>
							</div>
						</div>
					</div>
				</div> <!-- end send email out log -->

				<div class="tab-pane fade margin-top" id="packages" role="tabpanel">
					<div class="table-responsive">
						<table class="table table-side-bordered table-white">
							<thead>
								<tr>
									<th>Package</th>
									<th>Learners</th>
									<th width="350"></th>
									<th width="350"></th>
								</tr>
							</thead>
							<tbody>
								@foreach ($course->packages as $package)
									<tr>
										<td>
												{{ $package->variation }}
										</td>
										<td>
												{{ \App\CoursesTaken::where('package_id', $package->id)
												->where('is_active', true)->count() }}
										</td>
										<td>
											<button type="submit" data-toggle="modal" data-target="#importLearnersModal"
												class="btn btn-primary btn-xs pull-right import-learners-btn"
												data-package="{{ json_encode($package) }}"
												data-action="{{ route('admin.course.package.import-learners') }}">
												Import Learners
											</button>
										</td>
										<td>
											{{-- <button type="submit" data-toggle="modal" data-target="#copyPackageModal"
															class="btn btn-primary btn-xs pull-right copy-package-btn"
															data-package="{{ json_encode($package) }}"
															data-action="{{ route('admin.course.package.copy-package-and-learners') }}">
															Copy Package
											</button> --}}

											<button type="submit" data-toggle="modal" data-target="#copyLearnersModal"
												class="btn btn-primary btn-xs pull-right copy-learners-btn"
												data-package="{{ json_encode($package) }}"
												data-action="{{ route('admin.course.package.copy-learners') }}"
												style="margin-right: 3px">
												Copy Learners
											</button>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
				<div class="tab-pane fade margin-top" id="payLaterOptions" role="tabpanel">
					<div class="table-responsive">
						@if ($paymentPlans->count())
							<table class="table table-side-bordered table-white">
								<thead>
									<tr>
										<th>Division</th>
										<th>Payment Plan</th>
										<th width="200">Active</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($paymentPlans as $paymentPlan)
										<tr>
											<td>{{ $paymentPlan->division }}</td>
											<td>{{ $paymentPlan->plan }}</td>
											<td>
												<input type="checkbox"
													data-toggle="toggle"
													data-on="Yes"
													data-off="No"
													data-size="mini"
													class="course-payment-plan-toggle"
													data-payment-plan-id="{{ $paymentPlan->id }}"
													data-url="{{ route('learner.course.payment-plans.toggle', $course->id) }}"
													@if ($coursePaymentPlans->contains($paymentPlan->id)) checked @endif>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
							@else
									<p class="text-center">No payment plans available.</p>
							@endif
						</div>
					</div>
				</div>
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
        <h4 class="modal-title">{!! str_replace('_LEARNER_', '<strong id="learner_name"></strong>',trans('site.remove-learner-question')) !!}  <strong>{{$course->title}}</strong>?</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{route('learner.course.remove.learner')}}" onsubmit="disableSubmit(this)">
      		{{csrf_field()}}
      		<input type="hidden" name="learner_id">
      		<input type="hidden" name="package_id">

			<div class="form-group">
				<label>Delete Permanently:</label> <br>
				<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" name="is_permanent">
			</div>

      		<button type="submit" class="btn btn-danger btn-block">{{ trans('site.remove-learner') }}</button>
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
        <h4 class="modal-title">{{ trans('site.add-learner-to') }} {{$course->title}}</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{route('learner.course.add.learner')}}" onsubmit="disableSubmit(this)">
      		{{csrf_field()}}
      		<div class="form-group">
      			<select class="form-control select2" name="learner_id" required>
      				<option value="" selected disabled>- Search Learner -</option>
					@if($course->learners->count() > 0)
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
      			<button type="submit" class="btn btn-primary">{{ trans('site.add-learner') }}</button>
      		</div>
      	</form>
      </div>
    </div>

  </div>
</div>

<div id="addBulkLearnerModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Learners to {{$course->title}}</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{route('learner.course.add-bulk.learner')}}" onsubmit="disableSubmit(this)">
      		{{csrf_field()}}
      		<div class="form-group">
				<label>
					Learners
				</label>
      			<select class="form-control select2" name="learner_ids[]" multiple required>
					@if($course->learners->count() > 0)
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
				<label>
					Package
				</label>
      			<select class="form-control" name="package_id" required>
      				<option value="" selected disabled>- Select Package -</option>
      				@foreach($course->packages as $package)
      				<option value="{{$package->id}}">{{$package->variation}}</option>
      				@endforeach
      			</select>
      		</div>
      		<div class="text-right">
      			<button type="submit" class="btn btn-primary">{{ trans('site.add-learner') }}</button>
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
				<h4 class="modal-title">{{ trans('site.send-email') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{route('learner.course.send-email-to-learners', $course->id)}}"
					  onsubmit="formSubmitted()" enctype="multipart/form-data">
				{{csrf_field()}}

					<div class="form-group">
						<label>{{ trans('site.subject') }}</label>
						<input type="text" class="form-control" name="subject" required>
					</div>
					
					<div class="form-group">
						<label>{{ trans('site.message') }}</label>
						<textarea name="message" id="" cols="30" rows="10" class="form-control tinymce"></textarea>
					</div>

					<div class="form-group">
						<label style="display: block">From</label>
						<input type="text" class="form-control" placeholder="Name" style="width: 49%; display: inline;"
							   name="from_name">
						<input type="email" class="form-control" placeholder="Email" style="width: 49%; display: inline;"
							   name="from_email">
					</div>

					<div class="form-group">
						<label>Attachment</label>
						<input type="file" class="form-control" name="attachment"
							   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                   application/msword,
                               application/pdf,
                               application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
					</div>

					<label>Learners</label> <br>
					<input type="checkbox" name="check_all"> <label for="">Check/Uncheck All</label> <br>
					<input type="checkbox" name="not_facebook_group" value="not-fb-group"> 
					<label for="not_facebook">Not In Facebook Group</label>

					<div class="form-group">
						@if(count($course->packages) > 0)
							@foreach ($course->packages as $package)
								<input type="checkbox" name="packages[]" class="check-packages" value="{{ $package->id }}">
								<label>{{ $package->variation }}</label> <br>
							@endforeach
						@endif
					</div>

					<div class="form-group" style="max-height: 300px; overflow-y: scroll; margin-top: 10px">
						@if(count($course->learners->get()) > 0)
							@foreach( $course->learners->where('can_receive_email', 1)->get() as $learner)
								<input type="checkbox" name="learners[]" value="{{ $learner->user->id }}" 
								class="{{ !$learner->in_facebook_group ? 'not-in-facebook-group' : '' }}" 
								data-package="{{ $learner->package_id }}">
								<label>{{ $learner->user->full_name }}</label> <br>
							@endforeach
						@endif
					</div>

					<div class="text-right">
						<input type="submit" class="btn btn-primary" value="{{ trans('site.send') }}" id="send_email_btn">
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!--end email modal-->

<!-- reminder email modal -->
<div id="reminderEmailModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Send Reminder Email</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{route('learner.course.not-started-reminder', $course->id)}}"
					  onsubmit="formSubmitted()" enctype="multipart/form-data">
					{{csrf_field()}}

					<div class="form-group">
						<label>{{ trans('site.subject') }}</label>
						<input type="text" class="form-control" name="subject" required value="{{ App\Settings::courseNotStartedReminderSubject() }}">
					</div>

					<div class="form-group">
						<label>{{ trans('site.message') }}</label>
						<textarea name="message" id="" cols="30" rows="10" class="form-control tinymce">{{ App\Settings::courseNotStartedReminder() }}</textarea>
					</div>

					<div class="form-group">
						<label>Send To (Testing)</label>
						<input type="email" class="form-control" name="send_to" value="">
					</div>

					<div class="text-right">
						<input type="submit" class="btn btn-primary" value="{{ trans('site.send') }}">
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- end reminder email modal -->

<div id="setEndDateModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Set Course Taken End Date</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('learner.course.set-end-date', $course->id) }}"
					  onsubmit="disableSubmit(this)" enctype="multipart/form-data">
					{{csrf_field()}}

					<div class="form-group">
						<label>{{ trans('site.date') }}</label>
						<input type="date" class="form-control" name="date" required>
					</div>

					<div class="text-right">
						<input type="submit" class="btn btn-primary" value="{{ trans('site.save') }}">
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="reminderEmailTemplateModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Reminder Email</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.settings.update.course_not_started_reminder') }}"
				onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>Subject</label>
						<input type="text" name="subject" class="form-control" value="{{ App\Settings::courseNotStartedReminderSubject() }}">
					</div>
					<div class="form-group">
						<label>Message</label>
						<textarea class="form-control tinymce" name="email_content" rows="6">{{ App\Settings::courseNotStartedReminder() }}</textarea>
					</div>
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="expirationEmailReminder" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Expiration Email Reminder</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.course.expiration-reminder', $course->id) }}" onsubmit="disableSubmit(this)">
					{{csrf_field()}}

					<div class="form-group">
						<label>Subject 28 days</label>
						<input type="text" class="form-control" name="subject_28_days" required
							   value="{{ $expiryReminder ? $expiryReminder->subject_28_days : ''}}">
					</div>

					<div class="form-group">
						<label>Message for 28 days</label>
						<textarea name="message_28_days" cols="30" rows="10" class="form-control tinymce">{{ $expiryReminder ? $expiryReminder->message_28_days : ''}}</textarea>
					</div>

					<div class="form-group">
						<label>Subject 1 week</label>
						<input type="text" class="form-control" name="subject_1_week" required
							   value="{{ $expiryReminder ? $expiryReminder->subject_1_week : ''}}">
					</div>

					<div class="form-group">
						<label>Message 1 week</label>
						<textarea name="message_1_week" cols="30" rows="10" class="form-control tinymce">{{ $expiryReminder ? $expiryReminder->message_1_week : ''}}</textarea>
					</div>

					<div class="form-group">
						<label>Subject 1 day</label>
						<input type="text" class="form-control" name="subject_1_day" required
							   value="{{ $expiryReminder ? $expiryReminder->subject_1_day : ''}}">
					</div>

					<div class="form-group">
						<label>Message 1 day</label>
						<textarea name="message_1_day" cols="30" rows="10" class="form-control tinymce">{{ $expiryReminder ? $expiryReminder->message_1_day : ''}}</textarea>
					</div>

					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ trans('site.submit') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="addLearnersToWebinarsModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Add Learners to Webinar</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.course.add-learners-to-webinars', $course->id) }}"
					  onsubmit="disableSubmit(this)">
					{{csrf_field()}}

					<div class="form-group">
						<label>Webinar</label>
						<select name="webinar_id" class="form-control select2" required>
							<option value="" disabled selected> - Select Webinar -</option>
							@foreach($course->activeWebinars as $webinar)
								<option value="{{ $webinar->id }}">{{ $webinar->title }}</option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<label>Date</label>
						<input type="date" name="date" class="form-control" required>
					</div>

					<div class="form-group">
						<label>
							Run the cron after save?
						</label>
						<br>
						<input type="checkbox" data-toggle="toggle" data-on="Yes"
							   class="for-sale-toggle" data-off="No"
							   name="run_cron" data-width="84">
					</div>

					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ trans('site.submit') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="certificateDatesModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Certificate Dates</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.course.update-certificate-dates', $course->id) }}"
					  onsubmit="disableSubmit(this)">
				{{csrf_field()}}

					<div class="form-group">
						<label>Completed Date</label>
						<input type="date" name="completed_date" class="form-control" value="{{ $course->completed_date }}">
					</div>

					<div class="form-group">
						<label>Issue Date</label>
						<input type="date" name="issue_date" class="form-control" value="{{ $course->issue_date }}">
					</div>

					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ trans('site.submit') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="coachingTimeModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Coaching Time</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.course.add-coaching-time', $course->id) }}" 
					onsubmit="disableSubmit(this)">
					{{csrf_field()}}

					<div class="form-group">
						<label>Coaching Time</label>
						<select name="coaching_time" class="form-control">
							<option value="2">
								{{ FrontendHelpers::getCoachingTimerPlanType(2) }}
							</option>
							<option value="1">
								{{ FrontendHelpers::getCoachingTimerPlanType(1) }}
							</option>
						</select>
					</div>

					<div class="form-group">
						<label>Packages</label> <br>
						@if(count($course->packages) > 0)
							@foreach ($course->packages()->where('variation', '!=', 'Editor Package')->get() as $package)
								<input type="checkbox" name="packages[]" value="{{ $package->id }}">
								<label>{{ $package->variation }}</label> <br>
							@endforeach
						@endif
					</div>

					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ trans('site.submit') }}</button>
					</div>

				</form>
			</div>
		</div>
	</div>
</div>

<div id="copyLearnersModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Copy Learners</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
				{{csrf_field()}}
					<div class="form-group">
						<label>From Package</label>
						<input type="hidden" class="form-control" name="from_package">
						<input type="text" class="form-control" name="from_package_name" disabled>
					</div>

					<div class="form-group">
						<label>To Package</label>
						@php
							$allPackage = App\Package::where('id', '!=', $package->id)->orderBy('course_id')->get();
						@endphp
						<select name="to_package" class="form-control select2" required>
							<option value="" disabled selected> - Select Package -</option>
							@foreach($allPackage as $package)
								<option value="{{ $package->id }}">{{ $package->course->title . " - " .$package->variation }}</option>
							@endforeach
						</select>
					</div>

					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ trans('site.submit') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="importLearnersModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Copy Learners</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
				{{csrf_field()}}
					<div class="form-group">
						<label>From Package</label>
						@php
							$allPackage = App\Package::where('id', '!=', $package->id)->orderBy('course_id')->get();
						@endphp
						<select name="from_package" class="form-control select2" required>
							<option value="" disabled selected> - Select Package -</option>
							@foreach($allPackage as $package)
								<option value="{{ $package->id }}">{{ $package->course->title . " - " .$package->variation }}</option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<label>To Package</label>
						<input type="hidden" class="form-control" name="to_package">
						<input type="text" class="form-control" name="to_package_name" disabled>
					</div>

					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ trans('site.submit') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="copyPackageModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Copy Package</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
				{{csrf_field()}}
					<div class="form-group">
						<label>Package</label>
						<input type="hidden" class="form-control" name="from_package">
						<input type="text" class="form-control" name="from_package_name" disabled>
					</div>

					<div class="form-group">
						<label>Course</label>
						@php
							$courses = \App\Course::whereHas('packages', function ($query) use ($package) {
								$query->where('id', '!=', $package->id);
							})->get();
						@endphp
						<select name="course_id" class="form-control select2" required>
							<option value="" disabled selected> - Select Course -</option>
							@foreach($courses as $course)
								<option value="{{ $course->id }}">{{ $course->title }}</option>
							@endforeach
						</select>
					</div>

					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ trans('site.submit') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@stop

@section('scripts')
	<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
	<script>
		function formSubmitted() {
		    var send_email = $("#send_email_btn");
            send_email.val('Sending....').attr('disabled', true);
		}

		$("[name=check_all]").click(function() {
			if ($(this).prop('checked')) {
				$("[type=checkbox][name!=not_facebook_group]").prop('checked', true);
				$("[name=not_facebook_group]").prop('checked', false);
				$(".check-packages").prop('checked', false);
			} else {
				$("[type=checkbox]").prop('checked', false);
			}
		});

		$("[name=not_facebook_group]").click(function(){
			// uncheck all at first
			$("[type=checkbox][name!=not_facebook_group]").prop('checked', false);
			if ($(this).prop('checked')) {
				$(".not-in-facebook-group").prop('checked', true);
			}
		});

		$('.check-packages').change(function() {
            var packageValue = $(this).val();
            var isChecked = $(this).prop('checked');
            $('input[name="learners[]"][data-package="' + packageValue + '"]').prop('checked', isChecked);
		});

        $(".receive-email-toggle").change(function(){
            let learner_id = $(this).attr('data-id');
            let is_checked = $(this).prop('checked');
            let check_val = is_checked ? 1 : 0;
            $.ajax({
                type:'POST',
                url:'/course-taken/' + learner_id + '/update-can-receive-email',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: { 'can_receive_email' : check_val },
                success: function(data){
                }
            });
        });

		$(".exclude-in-registration-toggle").change(function(){
            let learner_id = $(this).attr('data-id');
            let is_checked = $(this).prop('checked');
            let check_val = is_checked ? 1 : 0;
            $.ajax({
                type:'POST',
                url:'/course-taken/' + learner_id + '/exclude-in-registration',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: { 'exclude_in_scheduled_registration' : check_val },
                success: function(data){
                }
            });
        });

        $(".facebook-group-toggle").change(function(){
            let learner_id = $(this).attr('data-id');
            let is_checked = $(this).prop('checked');
            let check_val = is_checked ? 1 : 0;
            $.ajax({
                type:'POST',
                url:'/course-taken/' + learner_id + '/update-in-facebook-group',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: { 'in_facebook_group' : check_val },
                success: function(data){
                }
            });
        });

        $(".course-payment-plan-toggle").change(function(){
            let toggle = $(this);
            let isChecked = toggle.prop('checked');
            $.ajax({
                type:'POST',
                url: toggle.data('url'),
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: {
                    payment_plan_id: toggle.data('payment-plan-id'),
                    is_active: isChecked ? 1 : 0
                },
                error: function(){
                    toggle.bootstrapToggle(isChecked ? 'off' : 'on');
                    alert('Unable to update payment plan. Please try again.');
                }
            });
        });

                $(".btn-remove-learner").click(function() {
                        $("[name=is_permanent]").prop('checked', false).trigger('change');
                })

		$(".btn-remove-learner-permanently").click(function() {
			var form = $('#removeLearnerModal form');
			form.find('[name=learner_id]').val($(this).data('learner-id'));
			form.find('[name=package_id]').val($(this).data('package'));
			$("[name=is_permanent]").prop('checked', true).trigger('change');
		});

		$(".copy-learners-btn").click(function() {
			let package = $(this).data('package');
			let action = $(this).data('action');
			let modal = $("#copyLearnersModal");

			modal.find('form').attr('action', action);
			modal.find("[name=from_package]").val(package.id);
			modal.find("[name=from_package_name]").val(package.variation);
		});

		$(".import-learners-btn").click(function() {
			let package = $(this).data('package');
			let action = $(this).data('action');
			let modal = $("#importLearnersModal");

			modal.find('form').attr('action', action);
			modal.find("[name=to_package]").val(package.id);
			modal.find("[name=to_package_name]").val(package.variation);
		});

		$(".copy-package-btn").click(function() {
			let package = $(this).data('package');
			let action = $(this).data('action');
			let modal = $("#copyPackageModal");

			modal.find('form').attr('action', action);
			modal.find("[name=from_package]").val(package.id);
			modal.find("[name=from_package_name]").val(package.variation);
		});
	</script>
@stop