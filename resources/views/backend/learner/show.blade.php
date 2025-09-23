@extends('backend.layout')

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
	<style>
		.former-course-container {
			margin-top: 30px;
		}
		.secondary-emails li:not(:last-child) {
			padding-bottom: 10px
		}

		#viewOrderModal .modal-header {
			padding: 0;
			border-bottom: 1px solid #e5e5e5;
		}

		#viewOrderModal table.no-border td, #viewOrderModal table.no-border tr {
			border: none;
		}

		.d-none {
			display: none;
		}

		.started-container {
			display: inline-block;
			width: 100%;
		}

		.started-container a {
			display: none;
		}

		.started-container:hover a {
			display: inline-block;
		}
	</style>
@stop

@section('title')
<title>{{ $learner->first_name }} &rsaquo; Learners &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3><i class="fa fa-users"></i> {{ trans('site.all-learners') }}</h3>
	<div class="navbar-form navbar-right">
	  	<div class="form-group">
		  	<form role="search" method="GET" action="{{route('admin.learner.index')}}">
				<div class="input-group">
				  	<input type="text" class="form-control" name="search" value="{{Request::input('search')}}" placeholder="{{ trans('site.search-learner') }}..">
				    <span class="input-group-btn">
				    	<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
				    </span>
				</div>
			</form>
		</div>
	</div>
	<div class="clearfix"></div>
</div>

<div class="col-md-10 col-md-offset-1" id="app-container">
	<div class="row">
		<div class="col-md-12">
		<a href="{{route('admin.learner.index')}}" class="btn btn-default margin-bottom margin-top"><i class="fa fa-angle-left"></i> {{ trans('site.all-learners') }}</a>
		</div>
		<div class="col-md-3">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="text-center">
						<div class="learner-profile-image" style="background-image: url({{$learner->profile_image}})"></div>
						<h2>{{$learner->fullName}}</h2>
						<span>{{$learner->email}}</span>
					</div>


					<div class="margin-top">
						<b class="d-block">Secondary Emails</b>
						@if ($learner->secondaryEmails->count())
							<ul class="secondary-emails">
								@foreach($learner->secondaryEmails as $secondary)
									<li>
										{{ $secondary->email }}
										<button class="btn btn-danger btn-xs pull-right removeSecondaryEmailBtn"
										data-toggle="modal" data-target="#removeSecondaryEmailModal"
										data-action="{{ route('admin.learner.remove-secondary-email', $secondary->id) }}">
											<i class="fa fa-close"></i>
										</button>

										<button class="btn btn-success btn-xs pull-right setPrimaryEmailBtn"
												style="margin-right: 2px"
												data-toggle="modal" data-target="#setPrimaryEmailModal"
										data-action="{{ route('admin.learner.set-primary-email', $secondary->id) }}">
											<i class="fa fa-check"></i>
										</button>
									</li>
								@endforeach
							</ul>
						@endif
						<button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#addSecondaryEmail">
							Add Email Address
						</button>
					</div>
				</div>
				<div class="panel-footer">
					<i class="fa fa-map-marker"></i> 
					@if($learner->address->street)
					{{$learner->address->street}},
					@endif
					@if($learner->address->city)
					{{$learner->address->city}},
					@endif
					@if($learner->address->zip)
					{{$learner->address->zip}}
					@endif
					<br />
					<i class="fa fa-phone"></i>
					@if($learner->address->phone)
					{{$learner->address->phone}}
					@endif
					<br> <br>
					<b>{{ trans('site.auto-renew-course') }}:</b>
					<a href="#" data-toggle="modal" data-target="#autoRenewModal">
					{{ $learner->auto_renew_courses ? 'Yes' : 'No' }}
					</a> <br>

					<div>
						<b>Could buy course:</b>
						<a href="#" data-toggle="modal" data-target="#couldBuyCourseModal">
							{{ $learner->could_buy_course ? 'Yes' : 'No' }}
						</a>
					</div>

					{{-- <div>
						<b>Self Publishing Learner:</b>
						<input type="checkbox" data-toggle="toggle" data-on="Yes"
							   class="is-publishing-learner-toggle" data-off="No" data-id="{{ $learner->id }}"
							   name="is_self_publishing_learner" data-size="mini" @if($learner->is_self_publishing_learner) {{ 'checked' }} @endif>
					</div> --}}

					<b>Preferred Editor:</b>
					<span>{{ $learner->preferredEditor ? $learner->preferredEditor->editor->fullname : '' }}</span><br>
					<b>Vipps Efaktura:</b>
					<span>{{ $learner->address ? $learner->address->vipps_phone_number : '' }}</span>

				</div>
			</div>
			<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editPasswordModal">{{ trans('site.edit-password') }}</button>
			<button type="button" class="btn btn-info" data-toggle="modal" data-target="#editContactModal">{{ trans('site.edit-contact-info') }}</button>
			<button type="button" class="margin-top btn btn-danger" data-toggle="modal" data-target="#deleteLearnerModal">{{ trans('site.delete-learner') }}</button>
			<button type="button" class="margin-top btn btn-success" data-toggle="modal" data-target="#learnerNotesModal">{{ trans_choice('site.notes', 2) }}</button>
			<button type="button" class="margin-top btn btn-primary loadScriptButton" data-toggle="modal" 
				data-target="#sendEmailModal">{{ trans('site.send-email') }}</button>
			<button type="button" class="margin-top btn btn-warning" data-toggle="modal" data-target="#preferredEditorModal">Preferred Editor</button>
			<button type="button" class="margin-top btn btn-success setVippsEFakturaBtn" data-toggle="modal"
					data-target="#setVippsEFakturaModal"
					data-vipps-number="{{ $learner->address ? $learner->address->vipps_phone_number : NULL}}">
				{!! trans('site.set-vipps-efaktura') !!}
			</button>
			<a href="{{ route('auth.login.email', encrypt($learner->email)) }}" class="btn btn-info margin-top" target="_blank">
				Login as user
			</a>

			<button type="button" class="margin-top btn btn-primary loadScriptButton" data-toggle="modal" data-target="#sendUsernameAndPasswordModal">
				Send Username and Password
			</button>

			@if ($learner->disable_start_date) 
				<br> <br>
				<b>Disable Date: </b>
				{{  \Carbon\Carbon::parse($learner->disable_start_date)->format('M d, Y') }} - 
				{{  \Carbon\Carbon::parse($learner->disable_end_date)->format('M d, Y') }}
				<button class="btn btn-xs btn-danger removeCourseTakenDisableBtn"
					data-toggle="modal" data-target="#removeCourseTakenDisableModal"
					data-action="{{ route('admin.learner.remove_disable_date', $learner->id) }}">
					X
				</button>
			@else
				<button type="button" class="btn d-block margin-top btn-primary setDisableUserBtn"
					data-title="{{ $learner->full_name }}"
					data-toggle="modal"
					data-target="#setDisableCourseModal"
					data-action="{{ route('admin.learner.set_disable_date', $learner->id) }}"
					data-disable_start_date="{{ $learner->disable_start_date }}"
					data-disable_end_date="{{ $learner->disable_end_date }}">
					Set Disable Date
				</button>
			@endif

			<div class="former-course-container">
				<h4>{{ trans('site.former-courses') }}</h4>
				<ul>
					<?php $expiredCoursePackageManuscripts = array(); /*$learner->coursesTakenOld = formerCourses*/ ?>

					@foreach($learner->formerCourses as $formerCourse)
						<li>
							{{ $formerCourse->package->course->title }} ({{ $formerCourse->package->variation }})
							<span class="text-danger">{{ \Carbon\Carbon::parse($formerCourse->end_date)->format('M d, Y') }}</span>
							<button class="btn btn-success btn-xs restoreCourseBtn" data-toggle="modal" data-target="#restoreCourseModal"
									data-action="{{ route('admin.learner.restore-course', [$learner->id, $formerCourse->id]) }}">
								Restore
							</button>
						</li>
							<ul>
								@foreach( $formerCourse->package->shop_manuscripts as $shop_manuscripts )
                                    <?php array_push($expiredCoursePackageManuscripts, $shop_manuscripts->id);?>
									<li>{{ $shop_manuscripts->shop_manuscript->title }}</li>
								@endforeach
							</ul>
					@endforeach
				</ul>
			</div>

			@if ($learner->notes)
			<div class="col-md-12 no-padding margin-top">
				<b><i>{{ trans_choice('site.notes', 2) }}</i></b> <br>
				{!! nl2br($learner->notes) !!}
			</div>
			@endif

            @if(session()->has('profile_success'))
            <br />
            <br />
		    <div class="alert alert-success">
		        {{ session()->get('profile_success') }}
		    </div>
			@endif

			@if ( $errors->any() && !session()->has('not-former-courses'))
            <br />
            <br />
            <div class="alert alert-danger no-bottom-margin">
                <ul>
                @foreach($errors->all() as $error)
                <li>{{$error}}</li>
                @endforeach
                </ul>
            </div>
            @endif
		</div>
		<div class="col-md-9">
			<h4 class="no-margin-top">{{ trans('site.courses-taken') }}</h4>
			<div class="row">
				@foreach($learner->coursesTakenNotOld->chunk(2) as $coursesTaken)
					<div class="col-sm-12">
						<div class="row">
							@foreach($coursesTaken as $courseTaken)
								<div class="col-sm-6">
									<div class="panel panel-default">
										<div class="panel-body">
											<h4 style="margin-bottom: 7px"><a href="{{route('admin.course.show', $courseTaken->package->course->id)}}?section=learners">{{$courseTaken->package->course->title}}</a></h4>
											<p class="no-margin-bottom">
												{{ trans('site.status') }}: @if($courseTaken->is_active)
													Active
												@else
													Pending
												@endif
												<br />
												{{ trans('site.plan') }}: {{ $courseTaken->package->variation }} <br />
												@if( $courseTaken->hasStarted )
													<div class="started-container">
														<span>
															{{ trans('site.started-at') }}: 
															{{ Carbon\Carbon::parse($courseTaken->started_at)->format('M d, Y H.i') }}
														</span>
														@php
															$started_at_parse = \Carbon\Carbon::parse($courseTaken->started_at);
														@endphp
														<a href="#" class="btn btn-primary btn-xs setCourseTakenStartedAtBtn" data-toggle="modal"
														data-target="#updateCourseTakenStartedAtModal"
														data-started_at="{{ $started_at_parse->format('Y-m-d').'T'.$started_at_parse->format('H:i') }}"
															data-action="{{ route('admin.course_taken.updated_started_at', $courseTaken->id) }}">Edit</a>
													</div>
													
												@else
													{{ trans('site.started-at') }}: <em>Not yet started</em>
												@endif
												<br />
												{{ trans('site.expires-on') }}
												@if( $courseTaken->hasStarted )
													@if( $courseTaken->end_date )
														{{ $courseTaken->end_date }}
													@else
														{{ Carbon\Carbon::parse($courseTaken->started_at)->addYears($courseTaken->years)->format('M d, Y H.i') }}
													@endif
												@else
													<em>Not yet started</em>
												@endif

												@if( $courseTaken->start_date )
													<br />
													{{ ucfirst(strtolower(trans('site.start-date'))) }}: {{ $courseTaken->start_date }}
												@endif
												{{--@if( $courseTaken->end_date )--}}
												<br />
												{{ ucfirst(strtolower(trans('site.end-date'))) }}: {{ $courseTaken->end_date ? $courseTaken->end_date
								: ($courseTaken->started_at ? \Carbon\Carbon::parse($courseTaken->started_at)->addYear(1)->format('M d, Y') : '') }}
												{{--@endif--}}

												@if ($courseTaken->package->course->id != 17)
													<br>
													Is Pay Later: {{ $courseTaken->is_pay_later ? 'Yes' : 'No' }}
												@endif

												@if ($courseTaken->disable_start_date) 
													<br>
													Disable Date: 
													{{  \Carbon\Carbon::parse($courseTaken->disable_start_date)->format('M d, Y') }} - 
													{{  \Carbon\Carbon::parse($courseTaken->disable_end_date)->format('M d, Y') }}
													<button class="btn btn-xs btn-danger removeCourseTakenDisableBtn"
														data-toggle="modal" data-target="#removeCourseTakenDisableModal"
														data-action="{{ route('admin.course_taken.remove_disable_date', $courseTaken->id) }}">
														X
													</button>
												@endif

												@if ($courseTaken->package->course->id == 17)
													<br>
													<label>Send Expiry Reminder:</label>
													<input type="checkbox" data-toggle="toggle" data-on="Yes"
														   class="expiry-reminder-toggle" data-off="No"
														   data-id="{{$courseTaken->id}}" data-size="mini"
													@if($courseTaken->send_expiry_reminder) {{ 'checked' }} @endif>

													<br>
													<label>Automatisk registert for felleswebinarer:</label>
													<input type="checkbox" data-toggle="toggle" data-on="Yes"
														   class="webinar-auto-register-toggle" data-off="No"
														   data-id="{{$learner->id}}" data-size="mini"
													@if($courseTaken->user->userAutoRegisterToCourseWebinar) {{ "checked" }} @endif>
												@endif

											</p>
											<button type="button" class="btn btn-xs btn-primary setAvailabilityBtn" style="margin-top: 7px"
													data-title="{{ $courseTaken->package->course->title }}"
													data-toggle="modal"
													data-target="#setAvailabilityModal"
													data-action="{{ route('admin.course_taken.set_availability', $courseTaken->id) }}"
													@if( $courseTaken->start_date )
													data-start_date="{{ date_format(date_create($courseTaken->start_date), 'Y-m-d') }}"
													@endif
													@if( $courseTaken->end_date )
													data-end_date="{{ date_format(date_create($courseTaken->end_date), 'Y-m-d') }}"
													@endif
											>
												{{ trans('site.set-availability') }}</button>

											<button type="button" class="btn btn-xs d-block margin-top btn-primary setDisableCourseBtn"
												data-title="{{ $courseTaken->package->course->title }}"
												data-toggle="modal"
												data-target="#setDisableCourseModal"
												data-action="{{ route('admin.course_taken.set_disable_date', $courseTaken->id) }}"
												data-disable_start_date="{{ $courseTaken->disable_start_date }}"
												data-disable_end_date="{{ $courseTaken->disable_end_date }}">
												Set Disable Date
											</button>

											<button class="btn btn-xs btn-info d-block sendRegretFormBtn margin-top"
													data-toggle="modal"
													data-target="#sendRegretFormModal"
													data-action="{{ route('admin.course_taken.send_regret_form', $courseTaken->id) }}">
												Send Regret Form
											</button>

											@if( !$courseTaken->is_active )
												<form method="POST" action="{{ route('activate_course_taken') }}" style="margin-top: 7px">
													{{ csrf_field() }}
													<input type="hidden" name="coursetaken_id" value="{{ $courseTaken->id }}">
													<button class="btn btn-warning btn-xs" type="submit"><i class="fa fa-check"></i></button>
												</form>
											@endif

											<div class="margin-top">
												<button data-toggle="collapse" class="btn btn-xs btn-success" data-target="#lessons-{{ $courseTaken->id }}">{{ trans_choice('site.lessons', 2) }}</button>
											</div>

											<!-- check if webinar-pakke -->
											<div class="margin-top">
												<button class="btn btn-xs btn-danger deleteFromCourseBtn" data-target="#deleteFromCourseModal"
														data-toggle="modal"
														data-action="{{ route('admin.learner.delete-from-course', $courseTaken->id) }}"
												data-course-title="{{$courseTaken->package->course->title}}">{{ trans('site.delete-from-course') }}</button>
											</div>

											@if ($courseTaken->package->course->id == 17)
												<div class="margin-top">
													<button class="btn btn-xs btn-info renewCourseBtn" data-toggle="modal"
													data-target="#renewCourseModal"
													data-action="{{ route('admin.learner.renew-course', ['learner_id' => $learner->id,
													'course_taken_id' => $courseTaken->id]) }}">
														Renew Course
													</button>
												</div>
											@endif

											<div class="collapse" id="lessons-{{ $courseTaken->id }}">
												<div class="margin-top"><strong>{{ trans_choice('site.lessons', 2) }}</strong></div>
												<div class="table-responsive">
													<table class="table table-bordered no-margin-bottom">
														@foreach( $courseTaken->package->course->lessons as $lesson )
															<tr>
																<td><a href="{{ route('admin.lesson.edit', ['course_id' => $courseTaken->package->course->id, 'lesson' => $lesson->id]) }}">{{ $lesson->title }}</a></td>
																<td>
																	@if( FrontendHelpers::hasLessonAccess($courseTaken, $lesson) )
																		<button class="btn btn-primary btn-xs defaultAllowAccessBtn" data-toggle="modal" data-target="#lessonDefaultAccessModal" data-action="{{ route('admin.course_taken.default_lesson_access', ['course_taken_id' => $courseTaken->id, 'lesson_id' => $lesson->id]) }}">{{ trans('site.default-access') }}</button>
																	@else
																		<button class="btn btn-success btn-xs allowAccessBtn" data-toggle="modal" data-target="#lessonAccessModal" data-action="{{ route('admin.course_taken.allow_lesson_access', ['course_taken_id' => $courseTaken->id, 'lesson_id' => $lesson->id]) }}">{{ trans('site.allow-access') }}</button>
																	@endif
																</td>
															</tr>
														@endforeach
													</table>
												</div>
											</div>
										</div>
									</div>
								</div>
							@endforeach
						</div>
					</div>
				@endforeach
			</div>


			<div class="panel panel-default">
				<div class="panel-body">
					<button class="btn btn-primary pull-right btn-xs" data-toggle="modal" data-target="#addShopManuscriptModal">+ {{ ucfirst(trans('site.add-shop-manuscript')) }}</button>
					<h4>{{ trans_choice('site.shop-manuscripts', 2) }}</h4>
				</div>
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>{{ trans_choice('site.manuscripts', 1) }}</th>
								<th>{{ trans('site.date-ordered') }}</th>
								<th>Assigned Admin</th>
								<th>{{ trans('site.status') }}</th>
								<th>{{ trans_choice('site.notes', 2) }}</th>
								<th>{{ trans_choice('site.feedbacks', 1) }}</th>
								<th>Feedback Date</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach($learner->shopManuscriptsTaken as $shopManuscriptTaken)
								{{--@if (!in_array($shopManuscriptTaken->package_shop_manuscripts_id,$expiredCoursePackageManuscripts))--}}
									<tr>
										<td>
											@if($shopManuscriptTaken->is_active)
												<a href="{{ route('shop_manuscript_taken', ['id' => $learner->id, 
												'shop_manuscript_taken_id' => $shopManuscriptTaken->id]) }}">
													{{$shopManuscriptTaken->shop_manuscript->title}}
												</a> <br>
												({{ $shopManuscriptTaken->words }} {{ trans('site.learner.words-text') }})
											@else
												{{$shopManuscriptTaken->shop_manuscript->title}}
											@endif
										</td>
										<td>{{$shopManuscriptTaken->created_at}}</td>
										<td>
											@if($shopManuscriptTaken->admin)
												{{ $shopManuscriptTaken->admin->full_name }}
											@endif
										</td>
										<td>
											@if( $shopManuscriptTaken->status == 'Finished' )
												<span class="label label-success">Finished</span>
                                            @elseif( $shopManuscriptTaken->status == 'Pending' )
                                                <span class="label label-info">Pending</span>
											@elseif( $shopManuscriptTaken->status == 'Started' )
												<span class="label label-primary">Started</span>
											@elseif( $shopManuscriptTaken->status == 'Not started' )
												<span class="label label-warning">Not started</span>
											@endif
										</td>
										<td>
											<?php
                                            	$manuscriptFeedback = $shopManuscriptTaken->feedbacks->first();
											?>
											@if($manuscriptFeedback && $manuscriptFeedback->notes_to_head_editor)
												<a class="pointer notes" data-target="#scriptNotesModal"
												   data-toggle="modal" data-notes="{{ $manuscriptFeedback->notes_to_head_editor }}"
												   style="cursor: pointer;">
													{{ substr($manuscriptFeedback->notes_to_head_editor, 0, 10) }}
													<i class="fa fa-file-text-o" aria-hidden="true"></i>
												</a>
											@endif
										</td>
										<td>
											@if($manuscriptFeedback)
												@foreach( $manuscriptFeedback->filename as $filename )
													<?php
														$fileLink = '';

														$extension = explode('.', basename($filename));
														if( end($extension) == 'pdf' || end($extension) == 'odt' ) {
															$fileLink = '<a href="/js/ViewerJS/#../..'.$filename.'" class="d-block">'.basename($filename).'</a>';
														} elseif( end($extension) == 'docx' || end($extension) == 'doc' ) {
															$fileLink = '<a href="https://view.officeapps.live.com/op/embed.aspx?src='.url('').$filename.'" class="d-block">'
																.basename($filename).'</a>';
														}
													?>
													{!! $fileLink !!}
													<a href="{{ $filename }}" class="btn btn-success btn-xs" download>
														<i class="fa fa-download"></i>
													</a>
												@endforeach
											@endif
										</td>
										<td>
											@if ($manuscriptFeedback)
												{{ $manuscriptFeedback->created_at }}
											@endif
										</td>
										<td class="text-right">
											@if(!$shopManuscriptTaken->is_active)
												<form method="POST" action="{{ route('activate_shop_manuscript_taken') }}" class="inline-block">
													{{ csrf_field() }}
													<input type="hidden" name="shop_manuscript_id" value="{{ $shopManuscriptTaken->id }}">
													<button class="btn btn-warning btn-xs" type="submit"><i class="fa fa-check"></i></button>
												</form>
											@endif
											@if ($shopManuscriptTaken->file)
												<input type="checkbox" data-toggle="toggle" data-on="Locked"
													   class="is-manuscript-locked-toggle" data-off="Unlocked"
													   data-id="{{$shopManuscriptTaken->id}}" data-size="mini"
												@if($shopManuscriptTaken->is_manuscript_locked) {{ 'checked' }} @endif>
											@endif
											<form method="POST" action="{{ route('delete_shop_manuscript_taken') }}" class="inline-block">
												{{ csrf_field() }}
												<input type="hidden" name="shop_manuscript_id" value="{{ $shopManuscriptTaken->id }}">
												<button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash"></i></button>
											</form>
										</td>
									</tr>
								{{--@endif--}}
							@endforeach
						</tbody>
					</table>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-body">
					<button class="btn btn-primary pull-right btn-xs" data-toggle="modal" data-target="#addTaskModal">
						+ Add Task
					</button>
					<h4>Tasks</h4>
				</div>
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>Task</th>
								<th>{{ trans('site.assigned-to') }}</th>
								<th>Available Date</th>
								<th width="150"></th>
							</tr>
						</thead>
						<tbody>
							@foreach($tasks as $task)
								<tr>
									<td>{!! nl2br($task->task) !!}</td>
									<td>{{ \App\User::find($task->assigned_to)->full_name }}</td>
									<td>
										{{ $task->available_date ? \App\Http\FrontendHelpers::formatDate($task->available_date) : '' }}
									</td>
									<td>
										<button class="btn btn-success btn-xs finishTaskBtn" data-toggle="modal"
												data-target="#finishTaskModal"
												data-action="{{ route('admin.task.finish', $task->id)}}">
											<i class="fa fa-check"></i>
										</button>
										<button class="btn btn-primary btn-xs editTaskBtn" data-toggle="modal"
												data-target="#editTaskModal"
												data-fields="{{ json_encode($task) }}"
												data-action="{{ route('admin.task.update', $task->id) }}">
											<i class="fa fa-edit"></i>
										</button>
										<button class="btn btn-danger btn-xs deleteTaskBtn" data-toggle="modal"
												data-target="#deleteTaskModal"
												data-action="{{ route('admin.task.destroy', $task->id) }}">
											<i class="fa fa-trash"></i>
										</button>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>

			{{--<time-register :time-registers="{{ json_encode($timeRegisters) }}" :learner-id="{{ $learner->id }}"
						   :projects="{{ json_encode($projects) }}"></time-register>--}}

			<div class="panel panel-default">
				<div class="panel-body">
					<button class="btn btn-primary pull-right btn-xs addTimeRegisterBtn" data-toggle="modal"
							data-target="#timeRegisterModal">
						+ Add Time Register
					</button>
					<h4>Time Register</h4>
				</div>

				<div class="table-responsive">
					<table class="table">
						<thead>
						<tr>
							<th>Project</th>
							<th>Date</th>
							<th>Number of hours</th>
							<th>Invoice</th>
							<th width="150"></th>
						</tr>
						</thead>
						<tbody>
						@foreach($timeRegisters as $timeList)
							<tr v-for="timeList in timeLists">
								<td>
									{{ $timeList->project_id ? $timeList->project->name : '' }}
								</td>
								<td>{{ $timeList->date }}</td>
								<td>{{ $timeList->time }}</td>
								<td>
									{!! $timeList->file_link !!}
								</td>
								<td>
									<button class="btn btn-xs btn-primary editTimeRegisterBtn"
											data-toggle="modal"
											data-record="{{ json_encode($timeList) }}"
											data-target="#timeRegisterModal">
										<i class="fa fa-edit"></i>
									</button>

									<button class="btn btn-xs btn-danger deleteTimeRegisterBtn" data-toggle="modal"
											data-target="#deleteTimeRegisterModal"
											data-action="{{ route('admin.time-register.delete', $timeList->id) }}">
										<i class="fa fa-trash"></i>
									</button>

									<button class="btn btn-success btn-xs timeUsedBtn" data-toggle="modal"
											data-target="#timeUsedModal"
											data-time-register="{{ json_encode($timeList) }}"
									>
										Time Used
									</button>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-body">
					<?php 
					$courseWorkshops = 0;
					$workshopTakenCount = 0;

					if ($learner->workshopTakenCount) {
					    $workshopTakenCount = $learner->workshopTakenCount->workshop_count;
					}

					foreach( $learner->coursesTaken as $courseTaken ) :
						$courseWorkshops += $courseTaken->package->workshops;
					endforeach;
					?>
					<button class="btn btn-primary pull-right btn-xs" data-toggle="modal" data-target="#addToWorkshopModal">+ {{ trans('site.add-to-workshop') }}</button>
						<button class="btn btn-info pull-right btn-xs margin-right-5" data-toggle="modal" data-target="#updateWorkshopCountModal">+ {{ trans('site.update-workshop-count') }}</button>
					<h4>{{ trans_choice('site.workshops', 2) }} <span class="badge">{{ /*$workshopTakenCount >= 0*/ $learner->workshopTakenCount ? $workshopTakenCount : $learner->workshopsTaken->count() + $courseWorkshops }}</span></h4>
				</div>
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>{{ trans_choice('site.workshops', 1) }}</th>
								<th>{{ trans('site.date-ordered') }}</th>
								<th width="250">{{ trans_choice('site.notes', 2) }}</th>
								<th>{{ trans('site.status') }}</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach($learner->workshopsTaken as $workshopTaken)
							<tr>
								<td>
									<a href="{{ route('admin.workshop.show', $workshopTaken->workshop_id) }}">{{ $workshopTaken->workshop->title }}</a>
								</td>
								<td>{{$workshopTaken->created_at}}</td>
								<td>
									{{ $workshopTaken->notes }} @if($workshopTaken->notes)<br> @endif
									<button class="btn btn-primary btn-xs editWorkshopNoteBtn" data-toggle="modal"
									data-target="#editWorkshopNoteModal"
											data-action="{{ route('admin.learner.workshop-taken.update-notes', $workshopTaken->id) }}"
									data-notes="{{ $workshopTaken->notes }}">
										{{ trans('site.edit-note') }}
									</button>
								</td>
								<td>
									@if($workshopTaken->is_active)
									Active
									@else
									Pending
									@endif
								</td>
								<td class="text-right">
									@if(!$workshopTaken->is_active)
						        	<form method="POST" action="{{ route('admin.package_workshop.approve', $workshopTaken->id) }}" class="inline-block">
										{{ csrf_field() }}
										<input type="hidden" name="workshop_user_id" value="{{ $workshopTaken->user_id }}">
										<input type="hidden" name="workshop_id" value="{{ $workshopTaken->workshop_id }}">
										<button class="btn btn-warning btn-xs" type="submit"><i class="fa fa-check"></i></button>
									</form>
									@endif
						        	<form method="POST" action="{{ route('admin.package_workshop.disapprove', $workshopTaken->id) }}" class="inline-block">
										{{ csrf_field() }}
										<button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash"></i></button>
									</form>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>


			<div class="panel panel-default">
				<div class="panel-body">
					<button class="btn btn-primary pull-right btn-xs" data-toggle="modal" data-target="#addInvoiceModal">+ {{ trans('site.add-invoice') }}</button>
					<button class="btn btn-success pull-right btn-xs" data-toggle="modal"
							style="margin-right: 10px"
							data-target="#createInvoiceModal">+ {{ trans('site.create-invoice') }}</button>
					<h4>{{ trans_choice('site.invoices', 2) }}</h4>
				</div>
				<div class="table-responsive" style="padding: 10px">
					<table class="table dt-table" id="invoice-table">
						<thead>
							<tr>
								<th>{{ trans_choice('site.invoices', 1) }} #</th>
								<th>Fiken Invoice ID</th>
								<th>{{ trans('site.status') }}</th>
								<th>{{ trans('site.created-at') }}</th>
								<th>{{ trans('site.due-date') }}</th>
								<th width="200"></th>
							</tr>
						</thead>
						<tbody>
							@foreach($learner->invoices as $invoice)
							<?php
							/*$fikenURL = false;
							foreach( $fikenInvoices as $fikenInvoice ) :
							    if( $invoice->fiken_url == $fikenInvoice->_links->alternate->href ) :
							      $fikenURL = true;
							      break;
							    endif;
							endforeach;
							$fikenError = false;*/
                            /*if( $fikenURL ) :
                              $sale = FrontendHelpers::FikenConnect($fikenInvoice->sale);
                              $status = $sale->paid ? "BETALT" : "UBETALT";
                            else :
                              $fikenError = true;
                            endif;*/
							?>
							<tr>
		    					<td>
		    						<a href="{{route('admin.invoice.show', $invoice->id)}}">{{ $invoice->invoice_number }}</a>
		    					</td>
								<td>
									{{ $invoice->fiken_invoice_id }}
								</td>
								<td>
									@if($invoice->fiken_is_paid === 1)
										<span class="label label-success">BETALT</span>
									@elseif($invoice->fiken_is_paid === 2)
										<span class="label label-warning text-uppercase">sendt til inkasso</span>
									@elseif($invoice->fiken_is_paid === 3)
										<span class="label label-primary text-uppercase">Kreditert</span>
									@else
										<span class="label label-danger">UBETALT</span>
									@endif
		    						{{--@if( !$fikenError )
									@if($sale->paid)
									<span class="label label-success">{{$status}}</span>
									@else
									<span class="label label-danger">{{$status}}</span>
									@endif
									@endif--}}
								</td>
								<td>{{$invoice->created_at}}</td>
								<td>
									<a href="#" data-toggle='modal' data-target='#updateInvoiceDueModal'
									   class="updateDueBtn"
									data-action="{{ route('admin.learner.invoice.update-due', $invoice->id) }}"
									data-date="{{ $invoice->fiken_dueDate }}">
										{{ $invoice->fiken_dueDate
											? \Carbon\Carbon::parse($invoice->fiken_dueDate)->format('d.m.Y')
											: 'Add Due Date' }}
									</a>
								</td>
								<td>
									{{--@if (Auth::user()->isSuperUser())--}}
										<button class="btn btn-danger btn-xs deleteInvoiceBtn" data-toggle="modal"
										data-target="#deleteInvoiceModal"
										data-action="{{ route('admin.learner.invoice.delete', $invoice->id) }}"
										 style="margin-top: 5px">
											<i class="fa fa-trash"></i>
										</button>
									{{--@endif--}}

									@if ($invoice->fiken_invoice_id && !$invoice->fiken_is_paid)
										<button class="btn btn-success btn-xs vippsFakturaBtn" style="margin-top: 5px"
												data-toggle="modal"
											data-target="#vippsFakturaModal"
												data-action="{{ route('admin.learner.invoice.vipps-e-faktura', $invoice->id) }}"
												data-vipps-number="{{ $learner->address ? $learner->address->vipps_phone_number : NULL}}">
											{!! trans('site.vipps-efaktura') !!}
										</button>
										{{-- <button class="btn btn-success btn-xs sendEfakturaBtn" style="margin-top: 5px"
												data-toggle="modal"
											data-target="#sendEfakturaModal"
												data-action="{{ route('admin.learner.invoice.send-efaktura', $invoice->id) }}">
											Send efaktura
										</button> --}}
									@endif

									@if($invoice->fiken_is_paid === 0)
										<button class="btn btn-primary btn-xs fikenCreditNoteBtn" data-toggle="modal"
												data-target="#fikenCreditNoteModal"
												data-action="{{ route("admin.learner.invoice.create-fiken-credit-note",
												$invoice->id) }}"
												style="margin-top: 5px">
											Add Credit Note
										</button>
									@endif
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-body">
					<h4>{{ trans('site.order-history.title') }}</h4>
				</div>
				<div class="table-responsive" style="padding: 10px">
					<table class="table" id="orders-table">
						<thead>
						<tr>
							<th>{{ trans('site.details') }}</th>
							<th>Svea Payment Type</th>
							<th>Svea Payment Plan</th>
							<th>{{ trans('site.date-ordered') }}</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						@foreach($learner->orders as $order)
							<tr>
								<td>
									{!! \App\Http\AdminHelpers::getOrderDetails($order) !!}
								</td>
								<td>
									{{ $order->svea_payment_type }}
								</td>
								<td>
									{{ $order->svea_payment_type_description }}
								</td>
								<td>{{ \App\Http\FrontendHelpers::formatDate($order->created_at) }}</td>
								<td>
									<button class="btn btn-primary btn-xs viewOrderBtn" data-toggle="modal"
											data-target="#viewOrderModal"
											data-fields="{{ json_encode($order) }}">
										Receipt
									</button>
									@if ($order->svea_delivery_id && !$order->is_credited_amount)
										<br>
										<button class="btn btn-info btn-xs addSveaCreditNoteBtn" data-toggle="modal"
												data-target="#addSveaCreditNoteModal"
												data-action="{{ route("admin.learner.svea.create-credit-note",
													$order->id) }}"
												data-fields="{{ json_encode($order) }}" style="margin-top: 5px">
											Credit Order
										</button>
									@endif
									@if($order->svea_order_id && !$order->svea_delivery_id)
										<br>
										<button class="btn btn-success btn-xs sveaDeliverBtn" data-toggle="modal"
												data-target="#sveaDeliverModal"
												data-action="{{ route("admin.learner.svea.deliver-order", $order->id) }}"
												style="margin-top: 5px">
											<i class="fa fa-truck"></i> Deliver
										</button>
									@endif
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
			</div> <!-- end order panel -->

			<div class="panel panel-default">
				<div class="panel-body">
					<h4>Course Attachments</h4>
				</div>
				<div class="table-responsive" style="padding: 10px">
					<table class="table" id="course-order-attachments-table">
						<thead>
						<tr>
							<th>{{ trans_choice('site.courses', 1) }}</th>
							<th>File</th>
						</tr>
						</thead>
						<tbody>
						@foreach($learner->courseOrderAttachments as $attachment)
							<tr>
								<td>
									<a href="{{ route('admin.course.show', $attachment->course_id) }}">
										{{ $attachment->course->title }}
									</a> - {{ $attachment->package->variation }}
								</td>
								<td>
									<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$attachment->file_path}}">
										{{ basename($attachment->file_path) }}
									</a>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
			</div> <!-- end order panel -->

			<div class="panel panel-default">
				<div class="panel-body">
					<button class="btn btn-primary pull-right btn-xs" data-toggle="modal" data-target="#addManuscriptModal">+ {{ trans('site.upload-manuscript') }}</button>
					<h4>{{ trans_choice('site.manuscripts', 2) }}</h4>
				</div>
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>{{ trans('site.id') }}</th>
								<th>{{ trans_choice('site.manuscripts', 1) }}</th>
								<th>{{ ucwords(trans('site.words-count')) }}</th>
								<th>{{ trans('site.grade') }}</th>
								<th>{{ trans_choice('site.feedbacks', 2) }}</th>
								<th>{{ trans_choice('site.courses', 1) }}</th>
								<th>{{ trans('site.date-uploaded') }}</th>
							</tr>
						</thead>
						<tbody>
							@foreach($learner->manuscripts as $manuscript)
							<tr>
								<td>{{ $manuscript->id }}</td>
								<td>
									<?php $extension = explode('.', basename($manuscript->filename)); ?>
									@if( end($extension) == 'pdf' )
									<i class="fa fa-file-pdf-o"></i> 
									@elseif( end($extension) == 'docx' )
									<i class="fa fa-file-word-o"></i> 
									@elseif( end($extension) == 'odt' )
									<i class="fa fa-file-text-o"></i> 
									@endif
									<a href="{{ route('admin.manuscript.show', $manuscript->id) }}">{{ basename($manuscript->filename) }}</a>
								</td>
								<td>{{$manuscript->word_count}}</td>
								<td>
									@if($manuscript->grade)
									{{$manuscript->grade}}
									@else
									<em>Not set</em>
									@endif
								</td>
								<td>{{count($manuscript->feedbacks)}}</td>
								<td><a href="{{route('admin.course.show', $manuscript->courseTaken->package->course->id)}}">{{$manuscript->courseTaken->package->course->title}}</a></td>
								<td>{{$manuscript->created_at}}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>



			<div class="panel panel-default">
				<div class="panel-body">
					<h4>{{ trans_choice('site.assignments', 2) }}</h4>
				</div>
				<div class="table-responsive">
					<table class="table" id="course-assignment-table">
						<thead>
							<tr>
								<th>{{ trans_choice('site.assignments', 1) }}</th>
								<th>Is Disabled</th>
								<th>Personal Assignment</th>
								<th>{{ trans_choice('site.courses', 1) }}</th>
								<th>Words</th>
								<th>Editor</th>
								<th>{{ trans_choice('site.manuscripts', 1) }}</th>
								<th>{{ trans_choice('site.feedbacks', 1) }}</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php
					        $assignments = [];
					        $addOns = $learner->assignmentAddOns->pluck('assignment_id')->toArray();
					        foreach( $learner->coursesTaken()->withTrashed()->get() as $courseTaken ) :
					            foreach( $courseTaken->package->course->assignments as $assignment ) :
                            		$allowed_package = json_decode($assignment->allowed_package);
                            		$package_id = $courseTaken->package->id;
                            		$course = $courseTaken->package->course;

                            		$manuscript = $assignment->manuscripts->where('user_id', $learner->id)->first();
									if ($manuscript) {
										$assignments[] = $assignment;
									} else {
										// check if the assignment is allowed on the learners package or there's no set package allowed
										if ((!is_null($allowed_package) && in_array($package_id,$allowed_package))
							|| is_null($allowed_package) || in_array($assignment->id, $addOns)) {
											// added the condition because of the update for submission date
											// the original is the else
											if (!AdminHelpers::isDateWithFormat('M d, Y h:i A',$assignment->submission_date)) {
												$assignments[] = $assignment;
												/*
												disable this to always display assignment even if it's already finished
												if ($course->type == 'Single' && $assignment->submission_date == '365') {
													if(\Carbon\Carbon::parse($courseTaken->end_date)->gt(\Carbon\Carbon::now())) {
														$assignments[] = $assignment;
													}
												} else {
													if(\Carbon\Carbon::parse($courseTaken->started_at)->addDays($assignment->submission_date)->gt(\Carbon\Carbon::now())) {
														$assignments[] = $assignment;
													}
												} */
											} else {
												if ($assignment->course_id === 17) {
													if (\Carbon\Carbon::parse($assignment->submission_date)->greaterThan(\Carbon\Carbon::now()->subMonths(3))) {
														$assignments[] = $assignment;
													}
												} else {
													$assignments[] = $assignment;
												}
												/*
												disable this to always display assignment even if it's already finished
												if (\Carbon\Carbon::parse($assignment->submission_date)->gt(\Carbon\Carbon::now())) {
													$assignments[] = $assignment;
												} */
											}
										}
									}

					            endforeach;
					        endforeach;
					        ?>
							@foreach($assignments as $assignment)
								<?php
								$manuscript = $assignment->manuscripts->where('user_id', $learner->id)->first();
								$assignmentCourse = $assignment->course;

								?>
								{{--@if( $manuscript )--}}
								<?php $extension = $manuscript ? explode('.', basename($manuscript->filename)) : ''; ?>
								<tr>
									<td>
										<a href="{{ route('admin.assignment.show',[$assignmentCourse->id, $assignment->id]) }}">
											{{ $assignment->title }}
										</a>
										<?php
										$learnerExist 	= \App\AssignmentGroupLearner::where('user_id', $learner->id)->get();

										if ($learnerExist) {
										    foreach ($learnerExist as $l) {
										        $assignment_group_id = $l->assignment_group_id;
										        $assignment_group = \App\AssignmentGroup::where('id', $assignment_group_id)->where('assignment_id', $assignment->id)->first();
										        if ($assignment_group) {
                                                    echo " - <a href='".route('admin.assignment-group.show',
                                                            ['course_id' => $assignmentCourse->id,
                                                                'assignment_id' => $assignment->id,
																'group' => $assignment_group_id]
														)."'>".$assignment_group['title']."</a>";
												}
											}
										}

										?>
									</td>
									<td>
										@php
											$disabledAssignment = AdminHelpers::assignmentDisabledForLearner($assignment->id, $learner->id);
											$personalAssignment = $assignment->getLinkedPersonalAssignment($learner->id);
											$disabledLearners = $assignment->disabledLearners()->pluck('user_id')->toArray();
										@endphp

										@if (!$personalAssignment)
											<input type="checkbox" data-toggle="toggle" data-on="{{ trans('site.front.yes') }}"
													class="disable-learner-toggle" data-off="{{ trans('site.front.no') }}"
													data-id="{{ $learner->id }}" data-size="small" 
													data-assignment-id="{{ $assignment->id }}"
													@if ($disabledAssignment)
														checked
													@endif>
										@else
											{{ trans('site.front.yes') }}
										@endif
									</td>
									<td>
										@if (!$personalAssignment)
											<button class="btn btn-primary btn-sm personalAssignmentBtn 
											assignment-{{ $assignment->id }} 
												{{ in_array($learner->id, $disabledLearners) ? '' : 'd-none'  }}"
												data-toggle="modal" data-target="#personalAssignmentModal" type="button"
												onclick="personalAssignment({{ $learner->id }}, {{ $assignment }})">
												Assign as Personal Assignment
											</button>
										@else
											<a href="{{ route('admin.learner.assignment',
											[$personalAssignment->parent_id, $personalAssignment->id]) }}">
												{{ $personalAssignment->title }}
											</a>
										@endif
									</td>
									<td>
										<a href="{{ route('admin.course.show', $assignment->course->id) }}">
											{{ $assignment->course->title }}
										</a>
									</td>
									<td>
										@if ($manuscript)
											{{ $manuscript->words }}
										@endif
									</td><td>
										@if ($manuscript && $manuscript->editor)
											{{ $manuscript->editor->full_name }}
										@endif
									</td>
									<td>
										@if($manuscript)
											@if( end($extension) == 'pdf' || end($extension) == 'odt' )
											<a href="/js/ViewerJS/#../..{{ $manuscript->filename }}">{{ basename($manuscript->filename) }}</a>
											@elseif( end($extension) == 'docx' )
											<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$manuscript->filename}}">{{ basename($manuscript->filename) }}</a>
											@endif

												<a href="{{ $manuscript->filename }}" download>
													<i class="fa fa-download"></i>
												</a>
										@endif
									</td>
									<td>
										<?php
											$groupFeedbacks = \App\AssignmentFeedback::leftJoin('assignment_group_learners', 'assignment_group_learners.id', '=', 'assignment_feedbacks.assignment_group_learner_id')
											->leftJoin('assignment_groups', 'assignment_group_learners.assignment_group_id', '=', 'assignment_groups.id')
											->where('assignment_group_learners.user_id', $learner->id)
                                        	->where('assignment_id', $assignment->id)->get();
											if ($groupFeedbacks->count() > 0) {
												foreach($groupFeedbacks as $groupFeedback) {
                                        			$files = explode(',',$groupFeedback->filename);
                                        			foreach($files as $file) {
														echo "<a href='" . $file . "' class='d-block' download>"
														. basename($file) . "</a>
														<a href='" . $file . "' download><i class='fa fa-download'></i></a>";
													}
												}
											} else {
											    if ($manuscript) {
                                        			$feedback = \App\AssignmentFeedbackNoGroup::where('learner_id', $learner->id)
													->where('assignment_manuscript_id', $manuscript->id)->first();
                                        			if ($feedback) {
														echo "<a href='" . $feedback->filename . "' class='d-block' download>"
														. basename($feedback->filename) . "</a>
														<a href='" . $feedback->filename . "' download><i class='fa fa-download'></i></a>";
													}
												}
											}
										?>
									</td>
									<td>
										@if($manuscript)
											<button class="btn btn-primary btn-xs assignmentManuscriptEmailBtn" data-toggle="modal"
													data-target="#assignmentManuscriptEmailModal"
													data-action="{{ route('assignment.send-email-to-manuscript-user', $manuscript->id) }}">
												Send Email
											</button>
										@endif

										@if (in_array($assignment->id, $addOns))
											<button class="btn btn-danger btn-xs deleteAssignmentAddOnBtn" data-toggle="modal"
													data-target="#deleteAssignmentAddOnModal"
											data-action="{{ route('admin.learner.assignment.delete-add-one', [$learner->id, $assignment->id]) }}">
												Delete Add-on
											</button>
										@endif
									</td>
								</tr>
								{{--@endif--}}
							@endforeach
						</tbody>
					</table>
				</div>
			</div> <!-- end assignments -->

			<div class="panel panel-default">
				<div class="panel-body">
					<button class="btn btn-primary pull-right btn-xs learnerAssignmentBtn" data-toggle="modal"
							data-target="#learnerAssignmentModal"
							data-action="{{ route('assignment.learner-assignment.save') }}">
						Add Assignment
					</button>
					<h4>
						Personal Assignments
					</h4>
				</div>
				<div class="table-responsive">
					<table class="table">
						<thead>
						<tr>
							<th>{{ trans_choice('site.assignments', 1) }}</th>
							<th>{{ trans('site.submission-date') }}</th>
							<th>{{ trans('site.available-date') }}</th>
							<th>{{ trans('site.max-words') }}</th>
							<th>{{ trans_choice('site.courses', 1) }}</th>
							<th>Editor</th>
							<th>{{ trans_choice('site.manuscripts', 1) }}</th>
							<th>{{ trans_choice('site.feedbacks', 1) }}</th>
						</tr>
						</thead>
						<tbody>
							@foreach($learnerAssignments as $assignment)
                                <?php $manuscript = $assignment->manuscripts->where('user_id', $learner->id)->first();
                                $assignmentCourse = $assignment->course;
                                ?>
								<tr>
									<td>
										<a href="{{ route('admin.learner.assignment',
											[$assignment->parent_id, $assignment->id]) }}">
											{{ $assignment->title }}
										</a>
									</td>
									<td>
										{{ $assignment->submission_date }}
										<button class="btn btn-primary btn-xs editSubmissionDateBtn" data-toggle="modal"
												data-target="#editSubmissionDateModal"
												data-action="{{ route('assignment.update-submission-date', $assignment->id) }}"
												data-submission_date="{{ $assignment->submission_date
												? strftime('%Y-%m-%dT%H:%M:%S', strtotime($assignment->submission_date)) : NULL }}">
											<i class="fa fa-edit"></i> Edit
										</button>
									</td>
									<td>
										{{ $assignment->available_date }}
										<button class="btn btn-primary btn-xs editAvailableDateBtn" data-toggle="modal"
												data-target="#editAvailableDateModal"
												data-action="{{ route('assignment.update-available-date', $assignment->id) }}"
												data-available_date="{{ $assignment->available_date
												? strftime('%Y-%m-%d', strtotime($assignment->available_date)) : NULL }}">
											<i class="fa fa-edit"></i> Edit
										</button>
									</td>
									<td>
										{{ $assignment->max_words }}
										<button class="btn btn-primary btn-xs editMaxWordsBtn" data-toggle="modal"
												data-target="#editMaxWordsModal"
												data-action="{{ route('assignment.update-max-words', $assignment->id) }}"
												data-max_words="{{ $assignment->max_words }}"
												data-allow_up_to="{{ $assignment->allow_up_to }}">
											<i class="fa fa-edit"></i> Edit
										</button>
									</td>
									<td>
										@if($assignment->course)
											<a href="{{ route('admin.course.show', $assignment->course->id) }}">
												{{ $assignment->course->title }}
											</a>
										@endif
									</td>
									<td>
										@if ($manuscript && $manuscript->editor)
											{{ $manuscript->editor->full_name }}
										@endif
									</td>
									<td>
										@if ($manuscript)
											{!! $manuscript->file_link_with_download !!}
										@endif
									</td>
									<td>
										@php
											if ($manuscript) {
												$feedback = \App\AssignmentFeedbackNoGroup::where('learner_id', $learner->id)
												->where('assignment_manuscript_id', $manuscript->id)->first();
												if ($feedback) {
													echo "<a href='" . $feedback->filename . "' class='d-block' download>"
													. basename($feedback->filename) . "</a> <a href='" . $feedback->filename . "' download><i class='fa fa-download'></i></a>";
												}
											}
										@endphp
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div> <!-- end personal assignments -->

			<div class="panel panel-default">
				<div class="panel-body">
					<h4>Projects</h4>
				</div>

				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>Project Number</th>
								<th>Name</th>
								<th>Status</th>
								{{-- <th width="700">Description</th> --}}
								<th>Date</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($projects as $project)
								<tr>
									<td>
										{{ $project->identifier }}
									</td>
									<td>
										<a href="/project/{{ $project->id }}">
											{{ $project->name }}
										</a>
									</td>
									<td>
										{{ strtoupper($project->status) }}
									</td>
									{{-- <td>
										{{ $project->description }}
									</td> --}}
									<td>
										{{ $project->start_date}} 
										@if($project->end_date) 
											- {{ $project->end_date }}
										@endif 
										<br>
										@if ($project->is_finished)
											<span class="small">Finished</span>
										@endif
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div> <!-- end projects -->

			<div class="panel panel-default">
				<div class="panel-body">
					<button class="btn btn-primary pull-right btn-xs" data-toggle="modal" data-target="#selfPublishingModal">
						+ Add to Self publishing
					</button>
					<h4>Self publishing</h4>
				</div>

				<div class="table-responsive">
					<table class="table">
						<thead>
						<tr>
							<th>Title</th>
							<th width="150"></th>
						</tr>
						</thead>
						<tbody>
						@foreach($learnerSelfPublishingList as $selfPublishing)
							<tr>
								<td>
									<a href="{{ route('admin.self-publishing.index') }}">
										{{ $selfPublishing->selfPublishing->title }}
									</a>
								</td>
								<td>
									<button class="btn btn-danger btn-xs deleteSelfPublishingBtn" data-toggle="modal"
											data-target="#deleteSelfPublishingModal"
											data-action="{{ route('admin.learner.remove-self-publishing', $selfPublishing->id) }}">
										<i class="fa fa-trash"></i>
									</button>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
			</div> <!-- end self publishing-->

			<!-- correction -->
			<div class="panel panel-default">
				<div class="panel-body">
					<button class="btn btn-primary pull-right btn-xs" data-toggle="modal" data-target="#addOtherServiceModal"
					onclick="updateOtherServiceFields(0)">+ {{ trans('site.add-correction') }}</button>
					<h4>{{ trans('site.correction') }}</h4>
				</div>

				<div class="table-responsive">
					<table class="table">
						<thead>
						<tr>
							<th>{{ trans_choice('site.manus', 2) }}</th>
							<th>{{ trans_choice('site.editors', 1) }}</th>
							<th>Project</th>
							<th>{{ trans('site.date-ordered') }}</th>
							<th>{{ trans('site.expected-finish') }}</th>
							<th>{{ trans('site.status') }}</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						@foreach($learner->corrections as $correction)
                            <?php $extension = explode('.', basename($correction->file)); ?>
							<tr>
								<td>
									@if( end($extension) == 'pdf' || end($extension) == 'odt' )
										<a href="/js/ViewerJS/#../../{{ $correction->file }}">{{ basename($correction->file) }}</a>
									@elseif( end($extension) == 'docx' )
										<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$correction->file}}">{{ basename($correction->file) }}</a>
									@endif
								</td>
								<td>
									@if ($correction->editor_id)
										{{ $correction->editor->full_name }} <br>

										<button class="btn btn-xs btn-primary assignEditorBtn" data-toggle="modal"
												data-target="#assignEditorModal"
												data-editor="{{ json_encode($correction->editor) }}"
												data-action="{{ route('admin.other-service.assign-editor', 
												['id' => $correction->id, 'type' => 2]) }}">
											{{ trans('site.assign-editor') }}
										</button>
									@else
										<button class="btn btn-xs btn-warning assignEditorBtn" 
										data-toggle="modal" data-target="#assignEditorModal" 
										data-action="{{ route('admin.other-service.assign-editor', 
										['id' => $correction->id, 'type' => 2]) }}">
											{{ trans('site.assign-editor') }}
										</button>
									@endif
								</td>
								<td>
									{{ $correction->project?->name }}
								</td>
								<td>
									{{ \App\Http\FrontendHelpers::formatDate($correction->created_at) }}
								</td>
								<td>
									@if ($correction->expected_finish)
										{{ $correction->expected_finish_formatted }}
										<br>
									@endif

									@if ($correction->status !== 2)
										<a href="#setOtherServiceFinishDateModal" data-toggle="modal"
										   class="setOtherServiceFinishDateBtn"
										   data-action="{{ route('admin.other-service.update-expected-finish',
										   ['id' => $correction->id, 'type' => 2]) }}"
										   data-finish="{{ $correction->expected_finish ?
										strftime('%Y-%m-%d', strtotime($correction->expected_finish)) : '' }}">
											Set Date
										</a>
									@endif
								</td>
								<td>
									@if( $correction->status == 2 )
										<span class="label label-success">Finished</span>
									@elseif( $correction->status == 1 )
										<span class="label label-primary">Started</span>
									@elseif( $correction->status == 0 )
										<span class="label label-warning">Not started</span>
									@endif
								</td>
								<td>
									<?php
										$btnColor = $correction->status == 1 ? 'primary' : 'warning';
									?>

									@if ($correction->status !== 2)
										<button class="btn btn-{{ $btnColor }} btn-xs updateOtherServiceStatusBtn" type="button"
										data-toggle="modal" data-target="#updateOtherServiceStatusModal"
										data-service="2"
										data-action="{{ route('admin.other-service.update-status', ['id' => $correction->id, 'type' => 2]) }}"><i class="fa fa-check"></i></button>
									@endif
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>

			</div>
			<!-- end correction -->

			<!-- copy editing -->
			<div class="panel panel-default">
				<div class="panel-body">
					<button class="btn btn-primary pull-right btn-xs" data-toggle="modal" data-target="#addOtherServiceModal"
							onclick="updateOtherServiceFields(1)">+ {{ trans('site.add-copy-editing') }}</button>
					<h4>{{ trans('site.copy-editing') }}</h4>
				</div>

				<div class="table-responsive">
					<table class="table">
						<thead>
						<tr>
							<th>{{ trans_choice('site.manus', 2) }}</th>
							<th>{{ trans_choice('site.editors', 1) }}</th>
							<th>Project</th>
							<th>{{ trans('site.date-ordered') }}</th>
							<th>{{ trans('site.expected-finish') }}</th>
							<th>{{ trans('site.status') }}</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						@foreach($learner->copyEditings as $copy_editing)
                            <?php $extension = explode('.', basename($copy_editing->file)); ?>
							<tr>
								<td>
									@if( end($extension) == 'pdf' || end($extension) == 'odt' )
										<a href="/js/ViewerJS/#../../{{ $copy_editing->file }}">{{ basename($copy_editing->file) }}</a>
									@elseif( end($extension) == 'docx' )
										<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$copy_editing->file}}">{{ basename($copy_editing->file) }}</a>
									@endif
								</td>
								<td>
									@if ($copy_editing->editor_id)
										{{ $copy_editing->editor->full_name }} <br>

										<button class="btn btn-xs btn-primary assignEditorBtn" data-toggle="modal"
												data-target="#assignEditorModal"
												data-editor="{{ json_encode($copy_editing->editor) }}"
												data-action="{{ route('admin.other-service.assign-editor', 
												['id' => $copy_editing->id, 'type' => 1]) }}">
											{{ trans('site.assign-editor') }}
										</button>
									@else
										<button class="btn btn-xs btn-warning assignEditorBtn" 
										data-toggle="modal" data-target="#assignEditorModal" 
										data-action="{{ route('admin.other-service.assign-editor', 
										['id' => $copy_editing->id, 'type' => 1]) }}">
											{{ trans('site.assign-editor') }}
										</button>
									@endif
								</td>
								<td>
									{{ $copy_editing->project?->name }}
								</td>
								<td>
									{{ \App\Http\FrontendHelpers::formatDate($copy_editing->created_at) }}
								</td>
								<td>
									@if ($copy_editing->expected_finish)
										{{ $copy_editing->expected_finish_formatted }}
										<br>
									@endif

									@if ($copy_editing->status !== 2)
										<a href="#setOtherServiceFinishDateModal" data-toggle="modal"
										   class="setOtherServiceFinishDateBtn"
										   data-action="{{ route('admin.other-service.update-expected-finish',
										   ['id' => $copy_editing->id, 'type' => 1]) }}"
										   data-finish="{{ $copy_editing->expected_finish ?
										strftime('%Y-%m-%d', strtotime($copy_editing->expected_finish)) : '' }}">
											{{ trans('site.set-date') }}
										</a>
									@endif
								</td>
								<td>
									@if( $copy_editing->status == 2 )
										<span class="label label-success">Finished</span>
									@elseif( $copy_editing->status == 1 )
										<span class="label label-primary">Started</span>
									@elseif( $copy_editing->status == 0 )
										<span class="label label-warning">Not started</span>
									@endif
								</td>
								<td>
                                    <?php
                                    $btnColor = $copy_editing->status == 1 ? 'primary' : 'warning';
                                    ?>

									@if ($copy_editing->status !== 2)
										<button class="btn btn-{{ $btnColor }} btn-xs updateOtherServiceStatusBtn" type="button"
												data-toggle="modal" data-target="#updateOtherServiceStatusModal"
												data-service="1"
												data-action="{{ route('admin.other-service.update-status', ['id' => $copy_editing->id, 'type' => 1]) }}"><i class="fa fa-check"></i></button>
									@endif

										<button class="btn btn-danger btn-xs deleteOtherServiceBtn" type="button"
												data-toggle="modal" data-target="#deleteOtherServiceModal"
												data-action="{{ route('admin.other-service.delete', ['id' => $copy_editing->id, 'type' => 1]) }}"><i class="fa fa-trash"></i></button>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>

			</div>
			<!-- end copy editing -->

			<!-- coaching timer -->
			<div class="panel panel-default" style="overflow: auto">
				<div class="panel-body">
					<button class="btn btn-primary pull-right btn-xs" data-toggle="modal"
							data-target="#addCoachingSessionModal">
						+ {{ trans('site.add-coaching-session') }}
					</button>
					<h4>{{ trans('site.coaching-timer-text') }}</h4>
				</div>

				<div class="table-responsive">
					<table class="table">
						<thead>
						<tr>
							<th>{{ trans_choice('site.manus', 2) }}</th>
							<th>{{ trans_choice('site.learners', 1) }}</th>
							<th>{{ trans('site.length') }}</th>
							{{--<th>{{ trans('site.learner-suggestion') }}</th>
							<th>{{ trans('site.admin-suggestion') }}</th>--}}
							<th>{{ trans('site.approved-date') }}</th>
							<th>{{ trans('site.assigned-to') }}</th>
							<th>{{ trans('site.replay') }}</th>
							<th>{{ trans('site.status') }}</th>
						</tr>
						</thead>
						<tbody>

                        <?php
							$packages = \App\Package::where('has_coaching', '>', 0)->pluck('id');
							$coachingTimerTaken = $learner->coachingTimersTaken()->pluck('course_taken_id');
							$checkCourseTakenWithCoaching = $learner->coursesTaken()->whereIn('package_id', $packages)
								->whereNotIn('id', $coachingTimerTaken)->get();
							// not yet used coaching session
                        ?>
						@foreach($checkCourseTakenWithCoaching as $courseTaken)
							<tr>
								<td></td>
								<td>
									<a href="{{ route('admin.learner.show', $courseTaken->user->id) }}">
										{{ $courseTaken->user->full_name }}
									</a>
								</td>
								<td>
									{{ \App\Http\FrontendHelpers::getCoachingTimerPlanType($courseTaken->package->has_coaching) }}
								</td>
								<!--<td>

								</td>
								<td></td>-->
								<td>
									<button class="btn btn-xs btn-warning setApprovedDateBtn" data-toggle="modal" data-target="#setApprovedDateModal"
									data-course_taken_id="{{ $courseTaken->id }}">
										{{ trans('site.set-approved-date') }}
									</button>
								</td>
								<td></td>
								<td></td>
								<td></td>
							</tr>

						@endforeach

						@foreach($learner->coachingTimers as $coachingTimer)
                            <?php $extension = explode('.', basename($coachingTimer->file)); ?>
							<tr>
								<td>
									@if( end($extension) == 'pdf' || end($extension) == 'odt' )
										<a href="/js/ViewerJS/#../../{{ $coachingTimer->file }}">{{ basename($coachingTimer->file) }}</a>
									@elseif( end($extension) == 'docx' )
										<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$coachingTimer->file}}">{{ basename($coachingTimer->file) }}</a>
									@endif
								</td>
								<td>
									<a href="{{ route('admin.learner.show', $coachingTimer->user->id) }}">
										{{ $coachingTimer->user->full_name }}
									</a>

									@if ($coachingTimer->help_with)
										<br>
										<a href="#viewHelpWithModal" style="color:#eea236" class="viewHelpWithBtn"
										   data-toggle="modal" data-details="{{ $coachingTimer->help_with }}">
											{{ trans('site.view-help-with') }}
										</a>
									@endif
								</td>
								<td>
									{{ \App\Http\FrontendHelpers::getCoachingTimerPlanType($coachingTimer->plan_type) }}
								</td>
								<!--<td>
                                    <?php
                                    $suggested_dates = json_decode($coachingTimer->suggested_date);
                                    ?>
									@if($suggested_dates)
										@for($i =0; $i <= 2; $i++)
											<div style="margin-top: 5px">
												{{ \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($suggested_dates[$i]) }}
												@if (!$coachingTimer->approved_date)
													<button class="btn btn-success btn-xs approveDateBtn"
															data-toggle="modal" data-target="#approveDateModal"
															data-date="{{ $suggested_dates[$i] }}"
															data-action="{{ route('admin.other-service.coaching-timer.approve_date', $coachingTimer->id) }}">
														<i class="fa fa-check"></i>
													</button>
												@endif
											</div>
										@endfor
										{{--@if (!$coachingTimer->approved_date)
											<a href="#suggestDateModal" data-toggle="modal"
											   class="suggestDateBtn"
											   data-action="{{ route('admin.other-service.coaching-timer.suggestDate', $coachingTimer->id) }}">Suggest Different Dates</a>
										@endif--}}
									@endif
								</td>
								<td>
                                    <?php
                                    $suggested_dates_admin = json_decode($coachingTimer->suggested_date_admin);
                                    ?>
									@if($suggested_dates_admin)
										@for($i =0; $i <= 2; $i++)
											<div style="margin-top: 5px">
												{{ \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($suggested_dates_admin[$i]) }}
											</div>
										@endfor
									@endif
									@if (!$coachingTimer->approved_date)
										<a href="#suggestDateModal" data-toggle="modal"
										   class="suggestDateBtn"
										   data-action="{{ route('admin.other-service.coaching-timer.suggestDate', $coachingTimer->id) }}">{{ trans('site.suggest-different-dates') }}</a>
									@endif
								</td>-->
								<td>
									{{ $coachingTimer->approved_date ?
                                    \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($coachingTimer->approved_date)
                                     : ''}}

									<button data-target="#setCoachingApprovedDateModal" class="btn btn-success btn-xs setCoachingApprovedDateBtn"
									   data-toggle="modal" data-approved_date="{{ $coachingTimer->approved_date }}"
									   data-action="{{ route('admin.other-service.coaching-timer.set-coaching-approve-date', $coachingTimer->id) }}"
									style="display: block">
										Set approve date
									</button>
								</td>
								<td>
									@php
										$activeEditors = AdminHelpers::editorList()->pluck('id')->toArray();
									@endphp
									@if ($coachingTimer->editor_id && in_array($coachingTimer->editor_id, $activeEditors))
										{{ $coachingTimer->editor->full_name }}
									@else
										<button class="btn btn-xs btn-warning assignEditorBtn" data-toggle="modal" data-target="#assignEditorModal" data-action="{{ route('admin.other-service.assign-editor', ['id' => $coachingTimer->id, 'type' => 3]) }}">{{ trans('site.assign-editor') }}</button>
									@endif
								</td>
								<td>
									@if ($coachingTimer->replay_link)
										<a href="{{ $coachingTimer->replay_link }}" target="_blank">
											{{ trans('site.view-replay') }}
										</a>
									@endif

									@if ($coachingTimer->comment)
										<p>
											{{ $coachingTimer->comment }}
										</p>
									@endif

									@if ($coachingTimer->document)
										<?php $extension = explode('.', basename($coachingTimer->document)); ?>
										@if( end($extension) == 'pdf' || end($extension) == 'odt' )
											<a href="/js/ViewerJS/#../../{{ $coachingTimer->document }}">{{ basename($coachingTimer->document) }}</a>
										@elseif( end($extension) == 'docx')
											<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$coachingTimer->document}}">{{ basename($coachingTimer->document) }}</a>
										@elseif( end($extension) == 'doc')
												<a href="{{ asset($coachingTimer->document) }}">{{ basename($coachingTimer->document) }}</a>
										@endif
									@endif

									<button class="btn btn-xs btn-primary setReplayBtn" data-toggle="modal"
											data-target="#setReplayModal" data-action="{{ route('admin.other-service.coaching-timer.set_replay', $coachingTimer->id) }}">{{ trans('site.set-replay') }}</button>
								</td>
								<td>
									@if ($coachingTimer->status === 1)
										<span class="label label-success">Finished</span>
									@endif

									@if($coachingTimer->status === 2 && !$coachingTimer->approved_date)
										<span class="label label-info" style="font-size: 13px">
											Pending approval
										</span> <br>
									@endif

									<button class="btn btn-xs btn-danger deleteCoachingBtn margin-top" data-toggle="modal"
											data-target="#deleteCoachingModal" data-action="{{ route('admin.other-service.coaching-timer.delete', $coachingTimer->id) }}">
										{{ trans('site.remove-coaching-session') }}
									</button>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>

			</div>

			<!-- end coaching timer-->

			<div class="panel panel-default">
				<div class="panel-body">
					<h4>{{ trans_choice('site.emails', 2) }}</h4>
				</div>
				<div class="table-responsive" style="padding: 10px">
					<table class="table dt-table">
						<thead>
							<tr>
								<th>{{ trans('site.subject') }}</th>
								<th>{{ trans('site.date') }}</th>
								<th>{{ trans_choice('site.attachments', 1) }}</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach($learner->emails as $email)
								<tr>
									<td>
										{{ $email->subject }}
									</td>
									<td>
										{{ $email->created_at }}
									</td>
									<td>
										@if ($email->attachment)
                                            <?php
                                            $file = explode('/',$email->attachment);
                                            $filename = $file[2];
                                            $extension = explode('.', $filename);
                                            ?>
												@if( end($extension) == 'pdf' || end($extension) == 'odt' )
													<a href="/js/ViewerJS/#../..{{ $email->attachment }}">{{ basename($email->attachment) }}</a>
												@elseif( end($extension) == 'docx' )
													<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$email->attachment}}">{{ basename($email->attachment) }}</a>
												@else
													<a href="{{public_path()."/".$email->attachment}}" download>{{ basename($email->attachment) }}</a>
												@endif
										@endif
									</td>
									<td class="text-center">
										<button class="btn btn-info btn-xs" data-toggle="modal"
										data-target="#showEmailModal"
										data-message="{{ $email->email }}" onclick="showEmailMessage(this)">Show Message</button>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div> <!-- end emails section -->

			<div class="panel panel-default">
				<div class="panel-body">
					<a href="{{ route('admin.learner.email-history', $learner->id) }}" class="btn btn-primary pull-right btn-xs">
						View More
					</a>
					<h4>
						Email History
					</h4>
				</div>
				<div class="table-responsive" style="padding: 10px">
					<table class="table dt-table">
						<thead>
						<tr>
							<th>{{ trans('site.subject') }}</th>
							<th>{{ trans('site.from') }}</th>
							<th>{{ trans('site.date-sent') }}</th>
							<th>Date Opened</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
							@foreach($emailHistories as $emailHistory)
								<tr>
									<td>
										{{ $emailHistory->subject }}
									</td>
									<td>
										{{ $emailHistory->from_email }}
									</td>
									<td>
										{{ $emailHistory->created_at }}
									</td>
									<td>
										{{ $emailHistory->date_open }}
									</td>
									<td class="text-center">
										<button class="btn btn-info btn-xs" data-toggle="modal"
												data-target="#showEmailModal"
												data-message="{{ $emailHistory->message }}" onclick="showEmailMessage(this)">
												Show Message
											</button>
										<button class="btn btn-success btn-xs resendEmailHistoryBtn loadScriptButton" data-toggle="modal" 
											data-target="#resendEmailHistoryModal" data-record="{{ json_encode($emailHistory) }}"
											style="margin-top: 5px;">
											Resend Email
										</button>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div> <!-- end email history section -->

			{{-- @if($learner->is_self_publishing_learner) --}}
				<div class="panel panel-default">
					<div class="panel-body">
						<button class="btn btn-primary pull-right btn-xs booksForSaleBtn" data-toggle="modal"
								data-action=""
								data-target="#booksForSaleModal">
							+ Add Books for Sale
						</button>
						<h4>
							Books for sale
						</h4>
					</div>
					<div class="table-responsive" style="padding: 10px">
						<table class="table dt-table">
							<thead>
							<tr>
								<th>Project</th>
								<th>ISBN</th>
								{{-- <th>Ebook ISBN</th> --}}
								<th>Title</th>
								<th>Description</th>
								<th>Price</th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							@foreach($learner->booksForSale as $bookForSale)
								<tr>
									<td>
										@if ($bookForSale->project)
											<a href="/project/{{ $bookForSale->project_id }}">
												{{ $bookForSale->project->name }}
											</a>
										@endif
									</td>
									{{-- <td>{{ $bookForSale->isbn }}</td>
									<td>{{ $bookForSale->ebook_isbn }}</td> --}}
									<td>
										@if ($bookForSale->project)
											<ul>
												@foreach ($bookForSale->project->registrations as $registration)
													@if ($registration->field === 'isbn')
														<li>{{ $registration->value }}</li>
													@endif
												@endforeach
											</ul>
										@endif
									</td>
									<td>
										{{ $bookForSale->project ? $bookForSale->project->book_name : '' }}
									</td>
									<td>{{ $bookForSale->description }}</td>
									<td>{{ $bookForSale->price_formatted }}</td>
									<td>
										<button class="btn btn-primary btn-xs booksForSaleBtn" data-toggle="modal"
												data-record="{{ json_encode($bookForSale) }}"
												data-target="#booksForSaleModal">
											<i class="fa fa-edit"></i>
										</button>

										<button class="btn btn-danger btn-xs deleteRecordBtn" data-toggle="modal"
												data-target="#deleteRecordModal"
												data-title="Delete Books for Sale"
												data-action="{{ route('admin.learner.delete-for-sale-books',
												 [$bookForSale->user_id, $bookForSale->id]) }}">
											<i class="fa fa-trash"></i>
										</button>
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div> <!-- books for sale -->

				<div class="panel panel-default">
					<div class="panel-body">
						<button class="btn btn-primary pull-right btn-xs bookSalesBtn" data-toggle="modal"
								data-books="{{ json_encode($learner->booksForSale) }}"
								data-target="#bookSalesModal">
							+ Book Sales
						</button>
						<h4>
							Books sales
						</h4>
					</div>
					<div class="table-responsive" style="padding: 10px">
						<table class="table dt-table">
							<thead>
							<tr>
								<th>Book</th>
								<th>Type</th>
								<th>Quantity</th>
								<th>Amount</th>
								<th>Date</th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							@foreach($learner->bookSales as $bookSale)
								<tr>
									<td>
										{{ $bookSale->book->title }}
									</td>
									<td>
										{{ $bookSale->sale_type_text }}
									</td>
									<td>
										{{ $bookSale->quantity }}
									</td>
									<td>
										{{ $bookSale->total_amount_formatted }}
									</td>
									<td>
										{{ $bookSale->date }}
									</td>
									<td>
										<button class="btn btn-primary btn-xs bookSalesBtn" data-toggle="modal"
												data-record="{{ json_encode($bookSale) }}"
												data-books="{{ json_encode($learner->booksForSale) }}"
												data-target="#bookSalesModal">
											<i class="fa fa-edit"></i>
										</button>

										<button class="btn btn-danger btn-xs deleteRecordBtn" data-toggle="modal"
												data-target="#deleteRecordModal"
												data-title="Delete Book Sale"
												data-action="{{ route('admin.learner.delete-book-sales',
												 [$bookSale->user_id, $bookSale->id]) }}">
											<i class="fa fa-trash"></i>
										</button>
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			{{-- @endif --}}

			<div class="panel panel-default">
				<div class="panel-body">
					<h4>
						Registered to Webinars
					</h4>
				</div>
				<div class="table-responsive" style="padding: 10px">
					<table class="table dt-table">
						<thead>
						<tr>
							<th>{{ trans_choice('site.webinars', 1) }}</th>
							<th width="200">Join Url</th>
							<th>Start Date</th>
							<th width="200"></th>
						</tr>
						</thead>
						<tbody>
						@foreach($registeredWebinars as $registeredWebinar)
							<tr>
								<td>
									<a href="{{ route('admin.course.show', $registeredWebinar->webinar->course_id) }}?section=webinars">
										{{ $registeredWebinar->webinar->title }}
									</a>
								</td>
								<td>
									<a href="{{ $registeredWebinar->join_url }}">
										{{ $registeredWebinar->join_url }}
									</a>
								</td>
								<td>{{ $registeredWebinar->webinar->start_date }}</td>
								<td>
									<button class="btn btn-primary btn-xs registeredWebinarEmailBtn loadScriptButton" data-toggle="modal"
											data-url="{{ $registeredWebinar->join_url }}"
											data-target="#registeredWebinarEmailModal"
											data-action="{{ route('admin.learner.send-webinar-registrant-email',
											[$learner->id, $registeredWebinar->id])}}">
										{{ trans('site.send-email') }}
									</button>
									<button class="btn btn-danger btn-xs" data-toggle="modal"
											data-target="#registeredWebinarRemoveModal"
											onclick="registeredWebinarRemove('{{ route('admin.webinar.remove-registrant', 
											$registeredWebinar->id) }}')">
										Remove from Webinar
									</button>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
			</div> <!-- end registered to webinars section -->

			<div class="panel panel-default">
				<div class="panel-body">
					<button class="btn btn-primary pull-right btn-xs addPrivateMessageBtn loadScriptButton" data-toggle="modal"
							data-action="{{ route('admin.learner.add-private-message', $learner->id) }}"
							data-target="#privateMessageModal">
						+ Private beskjeder
					</button>
					<h4>
						Private beskjeder
					</h4>
				</div>

				<div class="table-responsive">
					<table class="table">
						<thead>
						<tr>
							<th>Beskjeder</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						@foreach($learner->messages as $message)
							<tr>
								<td>
									{!! $message->message !!}
								</td>
								<td>
									<button class="btn btn-warning btn-xs editPrivateMessageBtn loadScriptButton"
											data-toggle="modal" data-target="#privateMessageModal"
											data-action="{{ route('admin.learner.update-private-message',
											[$learner->id, $message->id]) }}"
											data-fields="{{ json_encode($message) }}"
											>
										<i class="fa fa-pencil"></i>
									</button>
									<button class="btn btn-danger btn-xs deletePrivateMessageBtn" data-toggle="modal"
											data-target="#deletePrivateMessageModal"
											data-action="{{ route('admin.learner.delete-private-message' ,
											[$learner->id, $message->id]) }}">
										<i class="fa fa-trash"></i>
									</button>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
			</div> <!-- end private message -->

            <div class="panel panel-default">
                <div class="panel-body">
                    <button class="btn btn-primary pull-right btn-xs" data-toggle="modal" data-target="#addDiplomaModal">
                        + {{ trans('site.add-diploma') }}
                    </button>
                    <h4>{{ trans_choice('site.diplomas', 2) }}</h4>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>{{ trans_choice('site.courses', 1) }}</th>
                            <th>{{ trans_choice('site.diplomas', 1) }}</th>
                            <th>{{ trans('site.date-uploaded') }}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
						
						@foreach($learner->diplomas()->orderBy('created_at', 'DESC')->get() as $diploma)
							<tr>
								<td>
									<a href="{{ route('admin.course.show', $diploma->course_id) }}">
										{{ $diploma->course->title }}
									</a>
								</td>
								<td>{{ \App\Http\AdminHelpers::extractFileName($diploma->diploma) }}</td>
								<td>{{ $diploma->created_at }}</td>
								<td>
									<a href="{{ route('admin.learner.download-diploma', $diploma->id) }}">
										{{ trans('site.download') }}
									</a>
									<button class="btn btn-warning btn-xs editDiplomaBtn"
									data-toggle="modal" data-target="#editDiplomaModal"
									data-action="{{ route('admin.learner.edit-diploma', $diploma->id) }}"
									data-course="{{ $diploma->course_id }}">
										<i class="fa fa-pencil"></i>
									</button>
									<button class="btn btn-danger btn-xs deleteDiplomaBtn" data-toggle="modal" data-target="#deleteDiplomaModal"
									data-action="{{ route('admin.learner.delete-diploma', $diploma->id) }}">
										<i class="fa fa-trash"></i>
									</button>
								</td>
							</tr>
						@endforeach
						
						</tbody>
                    </table>
                </div>
            </div> <!-- end diploma -->

			<div class="panel panel-default">
				<div class="panel-body">
					<h4>Course Certificate</h4>
				</div>

				<div class="table-responsive">
					<table class="table">
						<thead>
						<tr>
							<th>{{ trans_choice('site.courses', 1) }}</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						@foreach($certificates as $certificate)
							<tr>
								<td>
									<a href="{{ route('admin.course.show', $certificate->course_id) }}">
										{{ $certificate->course_title }}
									</a>
								</td>
								<td>
									<a href="{{ route('admin.learner.download-course-certificate', [$learner->id, $certificate->id]) }}"
									   class="btn btn-success btn-sm">{{ trans('site.learner.download-text') }}</a>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
			</div> <!-- end course certificate -->

			<!-- words written -->
			<div class="panel panel-default">
				<div class="panel-body">
					<h4>{{ trans('site.words-written') }}</h4>
				</div>

				<div class="table-responsive">
					<table class="table">
						<thead>
						<tr>
							<th>{{ trans('site.words-written') }}</th>
							<th>{{ trans('site.date') }}</th>
						</tr>
						</thead>
						<tbody>
						@foreach($learner->wordWritten()->paginate(15) as $word)
							<tr>
								<td>{{ $word->words }}</td>
								<td>{{ $word->date }}</td>
							</tr>
						@endforeach
						</tbody>
					</table>

					<div class="pull-right">
						{{ $learner->wordWritten()->paginate(15)->render() }}
					</div>
				</div>
			</div><!-- end of words written -->

			<!-- words written goal -->
			<div class="panel panel-default">
				<div class="panel-body">
					<h4>{{ trans('site.words-written-goal') }}</h4>
				</div>

				<div class="table-responsive">
					<table class="table">
						<thead>
						<tr>
							<th>{{ trans('site.from') }}</th>
							<th>{{ trans('site.to') }}</th>
							<th>{{ trans('site.total-words') }}</th>
						</tr>
						</thead>
						<tbody>
						@foreach($learner->wordWrittenGoal()->paginate(15) as $goal)
							<tr>
								<td>{{ $goal->from_date }}</td>
								<td>{{ $goal->to_date }}</td>
								<td>
									<a href="#" data-target="#statisticsModal" data-toggle="modal"
									   class="showStatisticsBtn"
									   data-action="{{ route('admin.learner.goal-statistic', $goal->id) }}"
									   data-maximum="{{ $goal->total_words }}"
									   data-from-month="{{ ucfirst(\App\Http\FrontendHelpers::convertMonthLanguage(date('n', strtotime($goal->from_date)))) }}"
									   data-to-month="{{ ucfirst(\App\Http\FrontendHelpers::convertMonthLanguage(date('n', strtotime($goal->to_date)))) }}">
										{{ $goal->total_words }}
									</a>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>

					<div class="pull-right">
						{{ $learner->wordWrittenGoal()->paginate(15)->render() }}
					</div>
				</div>
			</div><!-- end of words written goal -->

			<div class="panel panel-default">
				<div class="panel-body">
					<h4>{{ str_replace('_COUNT_', 15 , trans('site.last-login-count')) }}</h4>
				</div>
				<div class="table-responsive">
					<table class="table">
						<thead>
						<tr>
							<th>{{ trans('site.time') }}</th>
							<th>{{ trans('site.ip-address') }}</th>
							<th>{{ trans('site.country') }}</th>
							<th>{{ trans('site.provider') }}</th>
							<th>{{ trans('site.platform') }}</th>
						</tr>
						</thead>
						<tbody>
						@foreach ($learner->logins as $login)
							<tr>
								<td>
									<a href="{{route('admin.learner.login_activity', $login->id)}}" target="_blank">
										{{ $login->created_at }}
									</a>
								</td>
								<td>{{ $login->ip }}</td>
								<td>{{ $login->country }}</td>
								<td>{{ $login->provider }}</td>
								<td>{{ $login->platform }}</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
			</div>

		</div>
	</div>
</div>

<div id="updateCourseTakenStartedAtModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Update Start Date <strong></strong></h4>
			</div>

			<div class="modal-body">
				<form method="POST">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ ucfirst(strtolower(trans('site.started-at'))) }}</label>
						<input type="datetime-local" class="form-control" name="started_at">
					</div>
					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="renewCourseModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Renew Course</h4>
			</div>

			<div class="modal-body">
				<form method="POST" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<p>
						Are you sure to renew course?
					</p>
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-primary">{{ trans('site.confirm') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="lessonDefaultAccessModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('site.set-default-access-for-this-lesson') }}</h4>
      </div>

      <div class="modal-body">
      	<form method="POST">
      		{{ csrf_field() }}
			{{ trans('site.set-default-access-for-this-lesson-question') }}
      		<div class="text-right margin-top">
      			<button type="submit" class="btn btn-primary">{{ trans('site.confirm') }}</button>
      		</div>
      	</form>
      </div>
    </div>

  </div>
</div>



<div id="lessonAccessModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('site.allow-access-for-this-lesson') }}</h4>
      </div>

      <div class="modal-body">
      	<form method="POST" onsubmit="disableSubmit(this)">
      		{{ csrf_field() }}
			{{ trans('site.allow-access-for-this-lesson-question') }}
      		<div class="text-right margin-top">
      			<button type="submit" class="btn btn-primary">{{ trans('site.confirm') }}</button>
      		</div>
      	</form>
      </div>
    </div>

  </div>
</div>



<div id="setAvailabilityModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('site.set-dates-for') }} <strong></strong></h4>
      </div>

      <div class="modal-body">
      	<form method="POST" onsubmit="disableSubmit(this)">
      		{{ csrf_field() }}
      		<div class="form-group">
      			<label>{{ ucfirst(strtolower(trans('site.start-date'))) }}</label>
      			<input type="date" class="form-control" name="start_date">
      		</div>
      		<div class="form-group">
      			<label>{{ ucfirst(strtolower(trans('site.end-date'))) }}</label>
      			<input type="date" class="form-control" name="end_date">
      		</div>
      		<div class="text-right">
      			<button type="submit" class="btn btn-primary">{{ trans('site.save') }}</button>
      		</div>
      	</form>
      </div>
    </div>

  </div>
</div>

<div id="setDisableCourseModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Set Disable Date for <strong></strong></h4>
			</div>

			<div class="modal-body">
				<form method="POST" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ ucfirst(strtolower(trans('site.start-date'))) }}</label>
						<input type="date" class="form-control" name="disable_start_date" required>
					</div>
					<div class="form-group">
						<label>{{ ucfirst(strtolower(trans('site.end-date'))) }}</label>
						<input type="date" class="form-control" name="disable_end_date" required>
					</div>
					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="removeCourseTakenDisableModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Remove Disable Date</h4>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action=""
					  onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					{{ method_field('DELETE') }}

					<p>Are you sure to remove the disable date?</p>

					<button type="submit" class="btn btn-danger pull-right">Delete</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="sendRegretFormModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Send Regret Form</h4>
			</div>

			<div class="modal-body">
				<form method="POST" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}

					<div class="form-group">
						<label>
							Email Content
						</label>
						<textarea name="email_content" class="form-control" cols="30" rows="10"></textarea>
					</div>
					
					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ trans('site.send') }}</button>
					</div>
				</form>
			</div>
		</div>

	</div>
</div>



<div id="addShopManuscriptModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ ucwords(trans('site.add-shop-manuscript')) }}</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" enctype="multipart/form-data" action="{{ route('admin.shop-manuscript.add_learner', $learner->id) }}"
			onsubmit="disableSubmit(this)">
      		{{ csrf_field() }}
      		<?php 
			$shopManuscripts = \App\ShopManuscript::all();
			?>
      		<div class="form-group">
      			<label>{{ trans_choice('site.shop-manuscripts', 1) }}</label>
      			<select class="form-control select2" name="shop_manuscript_id" required>
      				<option value="" selected disabled>- Search shop manuscript -</option>
					@foreach($shopManuscripts as $shopManuscript)
					<option value="{{ $shopManuscript->id }}">{{ $shopManuscript->title }}</option>>
					@endforeach
  				</select>
      		</div>
      		<div class="form-group">
      			<label>{{ trans_choice('site.files', 1) }}</label>
      			<div><em>* Godkjente fil formater er DOCX, PDF og ODT.</em></div>
      			<input type="file" class="form-control" name="manuscript" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
      		</div>
      		<button type="submit" class="btn btn-primary pull-right">{{ trans('site.add-shop-manuscript') }}</button>
      		<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>


<div id="addTaskModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Add Task</h4>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action="{{ route('admin.task.store') }}"
				onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<input type="hidden" name="user_id" value="{{ $learner->id }}">

					<div class="form-group">
						<label>
							Task
						</label>
						<textarea name="task" cols="30" rows="10" class="form-control" required></textarea>
					</div>

					<div class="form-group">
						<label>
							Available Date
						</label>
						<input type="date" class="form-control" name="available_date" required>
					</div>

					<div class="form-group">
						<label>
							{{ trans('site.assign-to') }}
						</label>
						<select name="assigned_to" class="form-control select2" required>
							<option value="" disabled="" selected>-- Select Assignee --</option>
							@foreach( App\User::whereIn('role', array(1,3))->orderBy('created_at', 'desc')->get() as $editor )
								<option value="{{ $editor->id }}">{{ $editor->full_name }}</option>
							@endforeach
						</select>
					</div>

					<button type="submit" class="btn btn-primary pull-right">Add Task</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="finishTaskModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Finish Task</h4>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action=""
					  onsubmit="disableSubmit(this)">
					{{ csrf_field() }}

					<p>Are you sure to finish this task?</p>

					<button type="submit" class="btn btn-success pull-right">Finish</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="editTaskModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Edit Task</h4>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action=""
					  onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					{{ method_field('PUT') }}
					<input type="hidden" name="user_id" value="{{ $learner->id }}">

					<div class="form-group">
						<label>
							Task
						</label>
						<textarea name="task" cols="30" rows="10" class="form-control" required></textarea>
					</div>

					<div class="form-group">
						<label>
							Available Date
						</label>
						<input type="date" class="form-control" name="available_date" required>
					</div>

					<div class="form-group">
						<label>
							{{ trans('site.assign-to') }}
						</label>
						<select name="assigned_to" class="form-control select2" required>
							<option value="" disabled="" selected>-- Select Assignee --</option>
							@foreach( App\User::whereIn('role', array(1,3))->orderBy('created_at', 'desc')->get() as $editor )
								<option value="{{ $editor->id }}">{{ $editor->full_name }}</option>
							@endforeach
						</select>
					</div>

					<button type="submit" class="btn btn-primary pull-right">Update Task</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="deleteTaskModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Delete Task</h4>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action=""
					  onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					{{ method_field('DELETE') }}

					<p>Are you sure to delete this task?</p>

					<button type="submit" class="btn btn-danger pull-right">Delete</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="selfPublishingModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Add To Self Publishing</h4>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data"
                      action="{{ route('admin.learner.add-self-publishing', $learner->id) }}"
					  onsubmit="disableSubmit(this)">
					{{ csrf_field() }}

					<div class="form-group">
						<label>Self Publishing</label>
						<select name="self_publishing_id" class="form-control" required>
							<option value="" disabled selected>- Select -</option>
                            @foreach($selfPublishingList as $publishing)
                                <option value="{{ $publishing->id }}">
                                    {{ $publishing->title }}
                                </option>
                            @endforeach
						</select>
					</div>

					<button type="submit" class="btn btn-success pull-right">Save</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="deleteSelfPublishingModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Delete Self Publishing</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action=""
					  onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					{{ method_field('DELETE') }}

					<p>
						Are you sure you want to remove this learner from self-publishing?
					</p>

					<button type="submit" class="btn btn-danger pull-right">
						{{ trans('site.delete') }}
					</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>


<div id="addInvoiceModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('site.add-invoice-for') }} {{ $learner->fullname }}</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{ route('admin.invoice.store') }}">
      		{{ csrf_field() }}
      		<input type="hidden" name="learner_id" value="{{ $learner->id }}">
      		<div class="form-group">
  				<label>Fiken URL</label>
  				<input type="text" name="fiken_url" class="form-control" required>
      		</div>
      		<div class="form-group">
  				<label>PDF URL</label>
  				<input type="text" name="pdf_url" class="form-control" required>
      		</div>
      		<button type="submit" class="btn btn-primary pull-right">{{ trans('site.create-invoice') }}</button>
      		<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>

<div id="createInvoiceModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.create-invoice') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.invoice.new') }}" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<input type="hidden" name="learner_id" value="{{ $learner->id }}">

					<div class="form-group">
						<label>{{ trans('site.front.form.payment-plan') }}</label> <br>
						@foreach(App\PaymentPlan::orderBy('division', 'asc')->get() as $paymentPlan)
							<div class="col-sm-6">
								<input type="radio" @if($paymentPlan->plan == 'Full Payment') checked @endif
								name="payment_plan_id" value="{{$paymentPlan->id}}" data-plan="{{trim($paymentPlan->plan)}}"
									   id="{{$paymentPlan->plan}}" onchange="payment_plan_change(this)"
									   data-plan-id="{{ $paymentPlan->id }}">
								<label>{{$paymentPlan->plan}} </label>
							</div>
						@endforeach

						<div class="col-sm-6">
							<input type="radio" @if($paymentPlan->plan == 'Full Payment') checked @endif
							name="payment_plan_id" value="10" data-plan="{{trim('24 måneder')}}"
								   id="24 måneder" onchange="payment_plan_change(this)"
								   data-plan-id="10">
							<label>24 måneder</label>
						</div>

						<div class="col-sm-6">
							<input type="number" class="form-control" name="payment_plan_in_months" placeholder="Custom month"
							onchange="payment_plan_in_month_change(this)" data-plan-id="0">
						</div>
						<div class="clearfix"></div>
					</div>

					<div class="form-group">
						<div>
							<label class="split-faktura">
								{{ trans('site.front.form.monthly-payment') }}?*</label>
						</div>
						<div class="payment-option custom-radio col-sm-6">
							<input type="radio" name="split_invoice" value="1" disabled required
								   id="yes_option">
							<label for="yes_option">
								{{ trans('site.front.yes') }}
							</label>
						</div>
						<div class="payment-option custom-radio col-sm-6">
							<input type="radio" name="split_invoice" value="0" disabled required
								   id="no_option">
							<label for="no_option">
								{{ trans('site.front.no') }}
							</label>
						</div>
					</div>

					<div class="form-group">
						<label>Product Type</label>
						<select name="product_type" class="form-control">
							<option value="course" data-product-id="884373255">Course</option>
							<option value="manuscript" data-product-id="884373255">Manuscript</option>
							<option value="manuscript_vat" data-product-id="5686476118">Manuscript MVA</option>
						</select>
					</div>

					<div class="form-group">
						<label>Product ID</label>
						<input type="text" class="form-control" required name="product_id" value="884373255">
					</div>

					<div class="form-group">
						<label for="">Price</label>
						<input type="text" class="form-control" required name="price">
					</div>

					<div class="form-group">
						<label>{{ trans('site.payment-from') }}</label>
						<input type="date" name="issue_date" placeholder="{{ trans('site.payment-from') }}" class="form-control">
					</div>

					<div class="form-group">
						<label>Comment</label>
						<textarea class="form-control" name="comment" rows="10" cols="10" onkeyup="countChar(this)"></textarea>
						<div class="charNum">136 characters left</div>
					</div>

					<button type="submit" class="btn btn-primary pull-right submitInvoice">
						{{ trans('site.create-invoice') }}
					</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="addManuscriptModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('site.upload-manuscript') }}</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" enctype="multipart/form-data" action="{{ route('admin.manuscript.store') }}">
      		{{ csrf_field() }}
      		<div class="form-group">
      		* Accepted file formats are DOCX, PDF, ODT.</div>
      		<div class="form-group row">
      			<div class="col-sm-6">
      				<input type="file" class="form-control" required name="file" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
      			</div>
      		</div>
      		<div class="form-group row">
      			<div class="col-sm-6">
      				<select class="form-control" name="coursetaken_id" required>
      					<option disabled selected value="">- Select course -</option>
						@foreach($learner->coursesTaken as $courseTaken)
						<option value="{{ $courseTaken->id }}">{{ $courseTaken->package->course->title }}</option>>
						@endforeach
      				</select>
      			</div>
      		</div>
      		<button type="submit" class="btn btn-primary pull-right">{{ trans('site.upload-manuscript') }}</button>
      		<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>

<div id="autoRenewModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">
					Auto Renew Course
				</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.learner.update-auto-renew', $learner->id) }}"
					  onsubmit="disableSubmit(this)">
					{{ csrf_field() }}

					<div class="form-group">
						<label>Auto Renew Course</label>
						<select name="auto_renew" class="form-control">
							<option value="1" {{ $learner->auto_renew_courses ? 'selected' : '' }}>Yes</option>
							<option value="0" {{ !$learner->auto_renew_courses ? 'selected' : '' }}>No</option>
						</select>
					</div>

					<button type="submit" class="btn btn-primary pull-right">{{ trans('site.save') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="couldBuyCourseModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">
					Allow to buy course
				</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.learner.update-could-buy-course', $learner->id) }}"
					  onsubmit="disableSubmit(this)">
					{{ csrf_field() }}

					<div class="form-group">
						<label>Allow user to buy course</label>
						<select name="could_buy_course" class="form-control">
							<option value="1" {{ $learner->could_buy_course ? 'selected' : '' }}>Yes</option>
							<option value="0" {{ !$learner->could_buy_course ? 'selected' : '' }}>No</option>
						</select>
					</div>

					<button type="submit" class="btn btn-primary pull-right">{{ trans('site.save') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="vippsFakturaModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">
					{!! trans('site.vipps-efaktura') !!}
				</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}

					<div class="form-group">
						<label>{!! trans('site.mobile-number') !!}</label>
						<input type="text" class="form-control" name="mobile_number" required>
					</div>

					<button type="submit" class="btn btn-primary pull-right">{{ trans('site.send') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="editPasswordModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('site.edit-password') }}</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{ route('admin.learner.update', $learner->id) }}" onsubmit="disableSubmit(this)">
      		{{ csrf_field() }}
      		{{ method_field('PUT') }}
      		<input type="hidden" name="field" value="password">
      		<div class="form-group">
      			<label>{{ trans('site.new-password') }}</label>
      			<input type="password" class="form-control" name="password" required>
      		</div>
      		<div class="form-group">
      			<label>{{ trans('site.confirm-password') }}</label>
      			<input type="password" class="form-control" name="password_confirmation" required>
      		</div>
      		<button type="submit" class="btn btn-primary pull-right">{{ trans('site.save') }}</button>
      		<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>


<div id="editContactModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('site.edit-contact-info') }}</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{ route('admin.learner.update', $learner->id) }}" onsubmit="disableSubmit(this)">
      		{{ csrf_field() }}
      		{{ method_field('PUT') }}
      		<input type="hidden" name="field" value="contact">
      		<div class="row form-group">
      			<div class="col-sm-6">
	      			<label>{{ trans('site.first-name') }}</label>
	      			<input type="tel" class="form-control" name="first_name" value="{{ $learner->first_name }}">
      			</div>
      			<div class="col-sm-6">
	      			<label>{{ trans('site.last-name') }}</label>
	      			<input type="text" class="form-control" name="last_name" value="{{ $learner->last_name }}">
	      		</div>
      		</div>
      		<div class="row form-group">
      			<div class="col-sm-6">
	      			<label>{{ trans('site.phone') }}</label>
	      			<input type="tel" class="form-control" name="phone" value="{{ $learner->address->phone }}">
      			</div>
      			<div class="col-sm-6">
	      			<label>{{ trans('site.street') }}</label>
	      			<input type="text" class="form-control" name="street" value="{{ $learner->address->street }}">
	      		</div>
      		</div>
      		<div class="row form-group">
      			<div class="col-sm-6">
	      			<label>{{ strtoupper(trans('site.zip')) }}</label>
	      			<input type="text" class="form-control" name="zip" value="{{ $learner->address->zip }}">
	      		</div>
      			<div class="col-sm-6">
	      			<label>{{ trans('site.city') }}</label>
	      			<input type="text" class="form-control" name="city" value="{{ $learner->address->city }}">
	      		</div>
      		</div>
      		<button type="submit" class="btn btn-primary pull-right">{{ trans('site.save') }}</button>
      		<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>


<div id="deleteLearnerModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('site.delete-learner') }}</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{ route('admin.learner.delete', $learner->id) }}" onsubmit="disableSubmit(this)">
      		{{ csrf_field() }}
      		{{ method_field('DELETE') }}
			{!! trans('site.delete-learner-question') !!}

      		<div class="checkbox margin-top">
				<label><input type="checkbox" id="moveToggle" name="moveStatus">{{ trans('site.move-learner-course-manu-invoice') }}</label>
			</div>

      		<div id="moveRelationships" class="hidden">
	      		<div class="form-group margin-top">
	      			<select class="form-control select2" name="move_learner_id">
	      				<option value="" disabled selected>- Select learner -</option>
	      				@foreach( App\User::where('id', '<>', $learner->id)->orderBy('created_at', 'desc')->get() as $moveLearner )
	      				<option value="{{ $moveLearner->id }}">{{ $moveLearner->full_name }}</option>
	      				@endforeach
	      			</select>
	      		</div>
	      		<div class="checkbox">
					<label><input type="checkbox" name="moveItems[]" value="courses_taken">{{ trans('site.courses-taken') }}</label>
				</div>
	      		<div class="checkbox">
					<label><input type="checkbox" name="moveItems[]" value="shop_manuscripts">{{ trans_choice('site.shop-manuscripts', 2) }}</label>
				</div>
	      		<div class="checkbox">
					<label><input type="checkbox" name="moveItems[]" value="invoices">{{ trans_choice('site.invoices', 2) }}</label>
				</div>
				<div class="checkbox">
					<label><input type="checkbox" name="moveItems[]" value="assignments">{{ trans_choice('site.assignments', 2) }}</label>
				</div>
				<div class="checkbox">
					<label><input type="checkbox" name="moveItems[]" value="diplomas">{{ trans_choice('site.diplomas', 2) }}</label>
				</div>
      		</div>

      		<button type="submit" class="btn btn-danger pull-right">{{ trans('site.delete') }}</button>
      		<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>

<div id="timeRegisterModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"></h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.time-register.save') }}" enctype="multipart/form-data"
					  onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<input type="hidden" name="id">
					<input type="hidden" name="learner_id" value="{{ $learner->id }}">
					<div class="form-group">
						<label>Project</label>
						<select class="form-control select2" name="project_id">
							<option value="" selected disabled>- Search project -</option>
							@foreach($projects as $project)
								<option value="{{ $project->id }}">{{ $project->name }}</option>>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<label>Date</label>
						<input type="date" name="date" class="form-control" required>
					</div>

					<div class="form-group">
						<label>Number of hours</label>
						<input type="text" name="time" class="form-control" required>

						<button type="button" class="btn btn-xs adjustTime" data-time="1">+1</button>
						<button type="button" class="btn btn-xs adjustTime" data-time="0.5">+1/2</button>
						<button type="button" class="btn btn-xs adjustTime" data-time="-0.5">-1/2</button>
						<button type="button" class="btn btn-xs adjustTime" data-time="-1">-1</button>
					</div>

					<div class="form-group">
						<label>Invoice file</label>
						<input type="file" name="invoice_file" class="form-control" accept="application/pdf">
					</div>

					<button type="submit" class="btn btn-primary pull-right">{{ trans('site.save') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="deleteTimeRegisterModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Delete Time Register</h4>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action=""
					  onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					{{ method_field('DELETE') }}

					<p>Are you sure to delete this time register?</p>

					<button type="submit" class="btn btn-danger pull-right">Delete</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="timeUsedModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Time Used</h4>
			</div>
			<div class="modal-body">
				<input type="hidden" name="time_register_id">
				<button class="btn btn-success btn-sm addTimeUsedBtn pull-right" data-toggle="modal"
						data-target="#timeUsedFormModal">
					Add Time Used
				</button>
				<div class="clearfix"></div>
				<div class="table-responsive margin-top">
					<table class="table">
						<thead>
						<tr>
							<th>Date</th>
							<th>Time Used</th>
							<th>Description</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>

	</div>
</div>

<div id="timeUsedFormModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"></h4>
			</div>
			<div class="modal-body">
				<input type="hidden" name="time_used_id">
				<div class="form-group">
					<label>Date</label>
					<input type="date" name="date" class="form-control" required>
				</div>

				<div class="form-group">
					<label>Time Used</label>
					<input type="number" name="time_used" class="form-control" required>
				</div>

				<div class="form-group">
					<label>Description</label>
					<textarea name="description" cols="30" rows="10" class="form-control"></textarea>
				</div>

				<button type="button" class="btn btn-primary pull-right saveTimeUsedBtn">Save</button>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</div>

<div id="deleteTimeUsedModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Delete Time Used</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action=""
					  onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					{{ method_field('DELETE') }}

					<p>Are you sure to delete this time used?</p>

					<button type="submit" class="btn btn-danger pull-right">Delete</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="addToWorkshopModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.add-to-workshop') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('learner.add_to_workshop') }}" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
                    <?php
                    $workshops = \App\Workshop::where('is_active', 1)->get();
                    ?>
					<div class="form-group">
						<label>{{ trans_choice('site.shop-manuscripts', 1) }}</label>
						<select class="form-control select2" name="workshop_id" required>
							<option value="" selected disabled>- Search workshop -</option>
							@foreach($workshops as $workshop)
								<?php
									$availableSeats = $workshop->seats - $workshop->attendees->count();
								?>
								@if($availableSeats > 0)
									<option value="{{ $workshop->id }}">{{ $workshop->title }}</option>>
								@endif
							@endforeach
						</select>
					</div>
					<input type="hidden" name="user_id" value="{{ $learner->id }}">
					<button type="submit" class="btn btn-primary pull-right">{{ trans('site.submit') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="updateWorkshopCountModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.update-workshop-count') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.learner.update_workshop_count', $learner->id) }}">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.workshop-count') }}</label>
						<input type="number" name="workshop_count" step="1" class="form-control"
							   value="{{ $learner->workshopTakenCount ? $learner->workshopTakenCount->workshop_count : ''}}"
							   required>
					</div>

					<button type="submit" class="btn btn-primary pull-right">{{ trans('site.submit') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="editWorkshopNoteModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans_choice('site.notes', 2) }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<textarea name="notes" cols="30" rows="10" class="form-control" required></textarea>
					</div>
					<button type="submit" class="btn btn-primary pull-right">{{ trans('site.submit') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

	<div id="learnerNotesModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">{{ trans_choice('site.notes', 2) }}</h4>
				</div>
				<div class="modal-body">
					<form method="POST" action="{{ route('learner.add_notes', $learner->id) }}">
						{{ csrf_field() }}
						<div class="form-group">
							<textarea name="notes" cols="30" rows="10" class="form-control" required>{!! $learner->notes !!}</textarea>
						</div>
						<button type="submit" class="btn btn-primary pull-right">{{ trans('site.submit') }}</button>
						<div class="clearfix"></div>
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
				<form method="POST" action="{{route('admin.learner.send-email', $learner->id)}}"
					  enctype="multipart/form-data" onsubmit="disableSubmit(this)">
					{{csrf_field()}}

					<div class="form-group">
						<label>
							Email Template
						</label>
						<select class="form-control select2 template">
							<option value="" selected disabled>- Search Template -</option>
							@foreach(\App\Http\AdminHelpers::learnerEmailTemplate() as $template)
								<option value="{{$template->id}}" data-fields="{{ json_encode($template) }}">
									{{$template->page_name}}
								</option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<label>{{ trans('site.subject') }}</label>
						<input type="text" class="form-control" name="subject" required>
					</div>

					<div class="form-group">
						<label>{{ trans('site.message') }}</label>
						<textarea name="message" cols="30" rows="10"
								  class="form-control tinymce" id="sendEmailEditor"></textarea>
					</div>

					<div class="form-group">
						<label style="display: block">From</label>
						<input type="text" class="form-control" placeholder="Name" style="width: 49%; display: inline;"
							   name="from_name">
						<input type="email" class="form-control" placeholder="Email" style="width: 49%; display: inline;"
							   name="from_email">
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

<div id="showEmailModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Message Body</h4>
			</div>
			<div class="modal-body">

			</div>
		</div>
	</div>
</div>

<div id="resendEmailHistoryModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Resend Email</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.send-email-to-queue') }}" onsubmit="disableSubmit(this)">
					{{csrf_field()}}
					<input type="hidden" name="parent">
					<input type="hidden" name="parent_id">
					<input type="hidden" name="recipient" value="{{ $learner->email }}">

					<div class="form-group">
						<label>{{ trans('site.subject') }}</label>
						<input type="text" class="form-control" name="subject" required>
					</div>

					<div class="form-group">
						<label>{{ trans('site.message') }}</label>
						<textarea name="message" cols="30" rows="10"
								  class="form-control tinymce" id="sendEmailHistoryEditor"></textarea>
					</div>

					<div class="form-group">
						<label>From</label>
						<input type="email" class="form-control" placeholder="Email"
							   name="from_email">
					</div>

					<div class="text-right">
						<input type="submit" class="btn btn-primary" value="{{ trans('site.send') }}" id="send_email_btn">
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="booksForSaleModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Books for sale</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.learner.save-for-sale-books', $learner->id) }}" onsubmit="disableSubmit(this)">
				{{ csrf_field() }}
					<input type="hidden" name="id">

					<div class="form-group">
						<label>Project</label>
						<select name="project_id" class="form-control" onchange="projectChanged(this)">
							<option value="">- Select Project -</option>
							@foreach ($projects as $project)
								<option value="{{ $project->id }}" data-registrations="{{ json_encode($project->registrations) }}"
									data-book_name="{{ $project->book_name }}">
									{{ $project->name }}
								</option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<label>ISBN</label>
						<div class="isbn-container"></div>
					</div>

					{{-- <div class="form-group">
						<label>Ebook ISBN</label>
						<input type="text" class="form-control" name="ebook_isbn">
					</div> --}}

					<div class="form-group">
						<label>Title</label>
						<input type="text" class="form-control" name="title" disabled>
					</div>

					<div class="form-group">
						<label>Description</label>
						<textarea class="form-control" name="description" rows="10" cols="30"></textarea>
					</div>

					<div class="form-group">
						<label>Price</label>
						<input type="number" class="form-control" name="price" required>
					</div>

					<button class="btn btn-primary pull-right" type="submit">
						{{ trans('site.save') }}
					</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="bookSalesModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Book sales</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.learner.save-book-sales', $learner->id) }}" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<input type="hidden" name="id">

					<div class="form-group">
						<label>Book</label>
						<select name="book_id" class="form-control" required></select>
					</div>

					<div class="form-group">
						<label>Sale Type</label>
						<select name="sale_type" class="form-control" required>
							<option value="" disabled selected>
								- Select Sale Type-
							</option>
							@foreach ($bookSaleTypes as $key => $saleType)
								<option value="{{ $key }}">
									{{ $saleType }}
								</option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<label>Quantity</label>
						<input type="number" class="form-control" name="quantity" required>
					</div>

					<div class="form-group">
						<label>Amount</label>
						<input type="number" class="form-control" name="amount">
					</div>

					<div class="form-group">
						<label>Date</label>
						<input type="date" class="form-control" name="date" required>
					</div>

					<button class="btn btn-primary pull-right" type="submit">
						{{ trans('site.save') }}
					</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="deleteRecordModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"></h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					{{ method_field('DELETE') }}

					<p>{{ trans('site.delete-item-question') }}</p>

					<div class="text-right margin-top">
						<button type="submit" class="btn btn-danger">{{ trans('site.delete') }}</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('site.cancel') }}</button>
					</div>
				</form>
			</div>

		</div>

	</div>
</div>

	<div id="statisticsModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Statistics</h4>
				</div>
				<div class="modal-body">
					<div id="chartContainer" style="height: 430px;width: 100%;"></div>
				</div>
			</div>
		</div>
	</div>

<div id="fikenCreditNoteModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Credit Note</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>Issue Date</label>
						<input type="date" class="form-control" name="issue_date" required>
					</div>
					<div class="form-group">
						<label>
							{{ trans('site.learner.notes-text') }}
						</label>
						<textarea name="credit_note" cols="30" rows="10" class="form-control"></textarea>
					</div>
					<button class="btn btn-primary pull-right" type="submit">
						{{ trans('site.add-note') }}
					</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="viewOrderModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" style="padding: 2rem; font-size: 3rem">&times;</button>
			</div>
			<div class="modal-body" style="padding: 22px 30px;">

				<div class="row">
					<div class="col-sm-6">
						<span>Retur:</span> <br>
						<span>Forfatterskolen AS</span> <br>
						<span>Lihagen 21</span> <br>
						<span>3029 DRAMMEN</span> <br>
						<span>Norge</span>
					</div>

					<div class="col-sm-6">
						<img src="{{ asset('/images-new/logo-tagline.png') }}" alt="Logo" class="w-100"
							 style="height: 100px;object-fit: contain;">
					</div>
				</div>

				<div class="row mt-3">
					<div class="col-sm-6">
						<span>{{ $learner->full_name }}</span> <br>
						<span>{{ $learner->address->street }}</span> <br>
						<span>{{ $learner->address->zip }} {{ $learner->address->city }}</span>
					</div>
					<div class="col-sm-6">
						<span class="mr-2">{{ trans('site.date') }}: </span> <span id="displayDate"></span>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-12">
						<h3 class="mt-4 mb-0 font-weight-bold">Ordre</h3>
					</div>
				</div>

				<div class="col-sm-12 mt-4">
					<table class="table no-border" id="order-list-table">
						<tbody>
						<tr>
							<td>
								<b class="mr-2">Kjøp av:</b>
								<b class="package-variation"></b>
								<br>

								{{--<span>
										{{ trans('site.front.form.payment-method') }}: <i class="payment-mode"></i>
									</span>,

								<span>
										{{ trans('site.front.form.payment-plan') }}: <i class="payment-plan"></i>
									</span>--}}
							</td>
							<td>
							</td>
						</tr>
						</tbody>
					</table>
					<div id="editing-services-container" class="hidden"></div>
				</div>

				<div class="col-sm-5 col-sm-offset-7">
					<table class="table">
						<tbody>
						<tr>
							<td>
								<b>{{ trans('site.front.price') }}</b>
							</td>
							<td class="price-formatted">
							</td>
						</tr>
						<tr class="discount-row">
							<td>
								<b>{{ trans('site.front.discount') }}</b>
							</td>
							<td class="discount-formatted">
							</td>
						</tr>
						<tr class="per-month-row">
							<td>
								<b>{{ trans('site.front.per-month') }}</b>
							</td>
							<td class="per-month">
							</td>
						</tr>
						<tr>
							<td>
								<b>{{ trans('site.front.total') }}</b>
							</td>
							<td class="total-formatted">
							</td>
						</tr>
						</tbody>
					</table>
				</div>
				<div class="clearfix"></div>
			</div> <!-- end modal-body -->
		</div> <!-- end modal content -->
	</div> <!-- view order modal -->
</div>

<div id="addSveaCreditNoteModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">
					Credit Order
				</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}

					<p>
						Do you want to Credit this order?
					</p>
					<button class="btn btn-primary pull-right" type="submit">
						{{ trans('site.submit') }}
					</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="sveaDeliverModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">
					Deliver Order
				</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}

					<p>
						Do you want to Deliver this order?
					</p>
					<button class="btn btn-primary pull-right" type="submit">
						{{ trans('site.submit') }}
					</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>


<div id="deleteInvoiceModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.delete-invoice') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="">
					{{ csrf_field() }}
					{{ method_field('delete') }}
					<p>
						{{ trans('site.delete-invoice-question') }}
					</p>
					<button class="btn btn-danger pull-right" id="submitDeleteInvoice">{{ trans('site.delete-invoice') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="updateInvoiceDueModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Update Due Date</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					
					<div class="form-group">
						<label>
							Due Date
						</label>
						<input type="date" class="form-control" name="due_date" required>
					</div>
					
					<button class="btn btn-primary pull-right" type="submit">
						{{ trans('site.submit') }}
					</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="deleteFromCourseModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.delete-from-course') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					{{ method_field('delete') }}
					<p>
						{{--{!! trans('site.delete-from-webinar-pakke-question') !!}--}}
					</p>

					<div class="form-group">
						<label>Delete Permanently:</label> <br>
						<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" name="is_permanent">
					</div>

					<button class="btn btn-danger pull-right" 
					onclick="checkFormAction(this)" type="button">{{ trans('site.delete') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="addOtherServiceModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"></h4>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action="{{ route('admin.learner.add-other-service', $learner->id) }}"
					  onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans_choice('site.manuscripts', 1) }}</label>
						<input type="file" class="form-control" name="manuscript" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document"
							   required>
					</div>

					<div class="form-group">
						<label>{{ trans('site.send-invoice') }}</label> <br>
						<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
							   name="send_invoice">
					</div>

					<div class="form-group">
						<label>{{ trans('site.assign-to') }}</label>
						<select name="editor_id" class="form-control select2">
							<option value="" disabled="" selected>-- Select Editor --</option>
							@foreach( App\User::whereIn('role', array(1,3))->orderBy('created_at', 'desc')->get() as $editor )
								<option value="{{ $editor->id }}">{{ $editor->full_name }}</option>
							@endforeach
						</select>
					</div>

					<input type="hidden" name="is_copy_editing">
					<button class="btn btn-success pull-right" type="submit">
						{{ trans('site.add') }}
					</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="deleteOtherServiceModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">
					{{ trans('site.delete') }}
				</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action=""
					  onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<p>
						{{ trans('site.delete-item-question') }}
					</p>
					<button class="btn btn-danger pull-right" type="submit">
						{{ trans('site.delete') }}
					</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="editSubmissionDateModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Edit Submission Date</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.submission-date') }}</label>
						<input type="datetime-local" name="submission_date" class="form-control" required>
					</div>
					<div class="text-right">
						<button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<div id="editAvailableDateModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Edit Available Date</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.available-date') }}</label>
						<input type="date" name="available_date" class="form-control" required>
					</div>
					<div class="text-right">
						<button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="editMaxWordsModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Edit Max Words</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.max-words') }}</label>
						<input type="number" name="max_words" class="form-control" required>
					</div>

					<div class="form-group">
						<label>Allowed up to</label>
						<input type="number" class="form-control" name="allow_up_to">
					</div>
					<div class="text-right">
						<button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="assignEditorModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>Assign editor</label>
						<select name="editor_id" class="form-control select2" required>
							<option value="" disabled="" selected>-- Select Editor --</option>
							@foreach( AdminHelpers::editorList() as $editor )
								<option value="{{ $editor->id }}">{{ $editor->full_name }}</option>
							@endforeach
						</select>
					</div>
					<div class="text-right">
						<button class="btn btn-primary" type="submit">Save</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="setReplayModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.set-replay') }}</label>
						<input type="url" name="replay_link" class="form-control">
					</div>
					<div class="form-group">
						<label>Comment</label>
						<textarea name="comment" cols="30" rows="10" class="form-control"></textarea>
					</div>
					<div class="form-group">
						<label>Document</label>
						<input type="file" name="document" class="form-control"
							   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                   application/msword,
                               application/pdf,">
					</div>
					<div class="form-group">
						<small>*Note: If any of the fields are inputted it would mark as Finished</small>
					</div>
					<div class="text-right">
						<button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="deleteCoachingModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4>{{ trans('site.remove-coaching-session') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)" enctype="multipart/form-data">
					{{ csrf_field() }}
					{{ method_field('delete') }}
					<div class="form-group">
						<b>Are you sure to remove this coaching session?</b>
					</div>
					<div class="text-right">
						<button class="btn btn-danger btn-sm" type="submit">{{ trans('site.delete') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="updateOtherServiceStatusModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Update <span></span> Status</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<p>
						Are you sure to update the status of this record?
					</p>
					<div class="text-right">
						<button class="btn btn-primary" type="submit">Submit</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="setOtherServiceFinishDateModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"><span></span> Expected Finish</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>Expected finish date</label>
						<input type="date" name="expected_finish" class="form-control" required>
					</div>
					<div class="text-right">
						<button class="btn btn-primary" type="submit">Submit</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Approve Coaching Timer Date Modal -->
<div id="approveDateModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Approve Date</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" id="approveDateForm"
					  onsubmit="disableSubmit(this)">
					{{csrf_field()}}
					Are you sure you want to approve this date?
					<input type="hidden" name="approved_date">
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-success">Approve</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					</div>
				</form>
			</div>

		</div>

	</div>
</div>

<!-- Suggest Date Modal -->
<div id="suggestDateModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.suggest-session-dates') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" id="suggestDateForm"
					  onsubmit="disableSubmit(this)">
					{{csrf_field()}}

					<div class="form-group">
						<label>{{ trans('site.date') }}</label>
						<input type="datetime-local" class="form-control" name="suggested_date_admin[]" required>
					</div>

					<div class="form-group">
						<label>{{ trans('site.date') }}</label>
						<input type="datetime-local" class="form-control" name="suggested_date_admin[]" required>
					</div>

					<div class="form-group">
						<label>{{ trans('site.date') }}</label>
						<input type="datetime-local" class="form-control" name="suggested_date_admin[]" required>
					</div>

					<div class="text-right margin-top">
						<button type="submit" class="btn btn-success">{{ trans('site.submit') }}</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('site.cancel') }}</button>
					</div>
				</form>
			</div>

		</div>

	</div>
</div>

<div id="addCoachingSessionModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.add-coaching-session') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.learner.add-coaching-timer', $learner->id) }}"
					  onsubmit="disableSubmit(this)" enctype="multipart/form-data">
					{{csrf_field()}}

					<div class="form-group">
						<label>{{ trans_choice('site.manuscripts', 1) }}</label>
						<input type="file" class="form-control" name="manuscript"
							   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, 
							   application/pdf, application/vnd.oasis.opendocument.text">
					</div>
					
					<div class="form-group">
						<label>{{ trans('site.session-length') }}</label>
						<select name="plan_type" class="form-control" required>
							<option value="" disabled="" selected>-- Select --</option>
							<option value="2">30 min</option>
							<option value="1">1 hr</option>
						</select>
					</div>

					<div class="form-group">
						<label>{{ ucwords(trans('site.assign-to')) }}</label>
						<select name="editor_id" class="form-control select2">
							<option value="" disabled="" selected>-- Select Editor --</option>
							@foreach( AdminHelpers::editorList() as $editor )
								<option value="{{ $editor->id }}">{{ $editor->full_name }}</option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<label>{{ trans('site.send-invoice') }}</label> <br>
						<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
							   name="send_invoice">
					</div>

					<div class="text-right margin-top">
						<button type="submit" class="btn btn-success">{{ trans('site.submit') }}</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('site.cancel') }}</button>
					</div>
				</form>
			</div>

		</div>

	</div>
</div>

<div id="addDiplomaModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{{ trans('site.add-diploma') }}</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.learner.add-diploma', $learner->id) }}"
                      onsubmit="disableSubmit(this)" enctype="multipart/form-data">
                    {{csrf_field()}}

                    <div class="form-group">
                        <label>{{ trans_choice('site.courses', 1) }}</label>
                        <select name="course_id" class="form-control select2" required>
                            <option value="" disabled selected>-- Select Course --</option>
                            @foreach(\App\Course::all() as $course)
                                <option value="{{ $course->id }}"> {{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>{{ trans_choice('site.diplomas', 1) }}</label>
                        <input type="file" class="form-control" name="diploma"
                               accept="application/pdf" required>
                    </div>

                    <div class="text-right margin-top">
                        <button type="submit" class="btn btn-success">{{ trans('site.submit') }}</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('site.cancel') }}</button>
                    </div>
                </form>
            </div>

        </div>

    </div>
</div>

<div id="editDiplomaModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.edit-diploma') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action=""
					  onsubmit="disableSubmit(this)" enctype="multipart/form-data">
					{{csrf_field()}}

					<div class="form-group">
						<label>{{ trans_choice('site.courses', 1) }}</label>
						<select name="course_id" class="form-control select2" required>
							<option value="" disabled selected>-- Select Course --</option>
							@foreach(\App\Course::all() as $course)
								<option value="{{ $course->id }}"> {{ $course->title }}</option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<label>{{ trans_choice('site.diplomas', 1) }}</label>
						<input type="file" class="form-control" name="diploma"
							   accept="application/pdf">
					</div>

					<div class="text-right margin-top">
						<button type="submit" class="btn btn-success">{{ trans('site.submit') }}</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('site.cancel') }}</button>
					</div>
				</form>
			</div>

		</div>

	</div>
</div>

<div id="deleteDiplomaModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.delete-diploma') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					{{ method_field('DELETE') }}

					<p>{{ trans('site.delete-diploma-question') }}</p>

					<div class="text-right margin-top">
						<button type="submit" class="btn btn-danger">{{ trans('site.delete') }}</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('site.cancel') }}</button>
					</div>
				</form>
			</div>

		</div>

	</div>
</div>

<div id="viewHelpWithModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Help With</h4>
			</div>
			<div class="modal-body">
				<pre></pre>
			</div>
		</div>
	</div>
</div>

<div id="setCoachingApprovedDateModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Approve Date</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>Approve Date</label>
						<input type="datetime-local" class="form-control" name="approved_date">
					</div>

					<div class="text-right margin-top">
						<button type="submit" class="btn btn-primary">{{ trans('site.submit') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="setApprovedDateModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.set-approved-date') }}</h4>
			</div>
			<div class="modal-body">
				<form action="{{ route('admin.other-service.coaching-timer.set-approved-date') }}" method="POST">
					{{ csrf_field() }}
					<input type="hidden" name="user_id" value="{{ $learner->id }}">
					<input type="hidden" name="course_taken_id" value="{{ $learner->id }}">
					<div class="form-group">
						<label>{{ trans('site.approved-date') }}</label>
						<input type="datetime-local" name="approved_date" class="form-control" required>
					</div>
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-primary">{{ trans('site.submit') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="addSecondaryEmail" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Add Secondary Email</h4>
			</div>
			<div class="modal-body">
				<form action="{{ route('admin.learner.add-email', $learner->id) }}" method="POST"
					onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.learner.email-addresses-text') }}</label>
						<input type="email" name="email" class="form-control" required>
					</div>
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-primary">{{ trans('site.submit') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="setPrimaryEmailModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Set Primary Email</h4>
			</div>
			<div class="modal-body">
				<form action="" method="POST" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<p>Are you sure to set this as a primary email?</p>
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-primary">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="removeSecondaryEmailModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Remove Secondary Email</h4>
			</div>
			<div class="modal-body">
				<form action="" method="POST" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					{{ method_field('delete') }}
					<p>Are you sure to remove this email?</p>
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-danger">{{ trans('site.delete') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="privateMessageModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">
					Private beskjeder
				</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="">
					{{csrf_field()}}

					<div class="form-group">
						<label>Message</label>
						<textarea name="message" cols="30" rows="10" class="form-control tinymce"></textarea>
					</div>

					<div class="text-right margin-top">
						<button type="submit" class="btn btn-success">{{ trans('site.submit') }}</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('site.cancel') }}</button>
					</div>
				</form>
			</div>

		</div>

	</div>
</div>

<div id="deletePrivateMessageModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.delete') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					{{ method_field('DELETE') }}

					<p>{{ trans('site.delete-item-question') }}</p>

					<div class="text-right margin-top">
						<button type="submit" class="btn btn-danger">{{ trans('site.delete') }}</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('site.cancel') }}</button>
					</div>
				</form>
			</div>

		</div>

	</div>
</div>

<div id="preferredEditorModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">
					Preferred Editor
				</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.learner.set-preferred-editor', $learner->id) }}"
					onsubmit="disableSubmit(this)">
					{{csrf_field()}}
					<div class="form-group">
						<label>{{ trans_choice('site.editors', 1) }}</label>
						<select class="form-control select2" name="editor_id" required>
							<option value="" selected disabled>
								-- Select Editor --
							</option>
							@foreach( \App\Http\AdminHelpers::editorList()  as $admin)
								<option value="{{ $admin->id }}"
										{{ $learner->preferredEditor && $learner->preferredEditor->editor_id === $admin->id
										? 'selected' : '' }}>
									{{ $admin->full_name }}
								</option>
							@endforeach
						</select>
					</div>
					<button type="submit" class="btn btn-primary pull-right">{{ trans('site.update') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="assignmentManuscriptEmailModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.send-email') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="formSubmitted(this)">
					{{csrf_field()}}

					<div class="form-group">
						<label>{{ trans('site.subject') }}</label>
						<input type="text" class="form-control" name="subject" required>
					</div>

					<div class="form-group">
						<label>{{ trans('site.from') }}</label>
						<input type="text" class="form-control" name="from_email">
					</div>

					<div class="form-group">
						<label>{{ trans('site.message') }}</label>
						<textarea name="message" cols="30" rows="10" class="form-control" required></textarea>
					</div>
					<div class="text-right">
						<input type="submit" class="btn btn-primary" value="{{ trans('site.send') }}">
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!--end email modal-->

<div id="deleteAssignmentAddOnModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">
					Delete Add-on
				</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="formSubmitted(this)">
					{{csrf_field()}}

					<p>
						Are you sure to delete this record?
					</p>

					<div class="text-right">
						<input type="submit" class="btn btn-danger" value="{{ trans('site.delete') }}">
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

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
					<input type="hidden" name="learner_id" value="{{ $learner->id }}">
					<div class="form-group">
						<label>
							Assignment Template
						</label>
						<select class="form-control select2 assignment-template">
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

<div id="registeredWebinarEmailModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.send-email') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="">
					{{csrf_field()}}

                    <?php
                    	$emailTemplate = \App\Http\AdminHelpers::emailTemplate('Webinar-reminder');
                    ?>

					<div class="form-group">
						<label>{{ trans('site.subject') }}</label>
						<input type="text" class="form-control" name="subject" value="{{ $emailTemplate->subject }}"
							   required>
					</div>

					<div class="form-group">
						<label>From</label>
						<input type="email" class="form-control" placeholder="Email" name="from_email"
							   value="{{ $emailTemplate->from_email }}">
					</div>

					<input type="hidden" class="form-control join-url" placeholder="Email" name="join_url"
							value="">

					<div class="form-group">
						<label>{{ trans('site.message') }}</label>
						<textarea name="message" cols="30" rows="10"
								  class="form-control tinymce">{!! $emailTemplate->email_content !!}</textarea>
					</div>

					<div class="text-right">
						<button class="btn btn-primary" type="submit">
							{{ trans('site.send') }}
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!--end email modal-->

<div id="registeredWebinarRemoveModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Remove learner from webinar</h4>
			</div>
			<div class="modal-body">

				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{csrf_field()}}
					{{ method_field('DELETE') }}

					<p>Are you sure you want to remove this learner from webinar?</p>

					<div class="text-right">
						<button type="submit" class="btn btn-danger">{{ trans('site.delete') }}</button>
					</div>
				</form>

			</div>
		</div>
	</div>
</div>

<div id="setVippsEFakturaModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">
					{!! trans('site.set-vipps-efaktura') !!}
				</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.learner.set-vipps-e-faktura', $learner->id) }}" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}

					<div class="form-group">
						<label>{!! trans('site.mobile-number') !!}</label>
						<input type="text" class="form-control" name="mobile_number">
					</div>

					<button type="submit" class="btn btn-primary pull-right">{{ trans('site.save') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="sendUsernameAndPasswordModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">
					Send Username and Password
				</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.learner.send-username-and-password', $learner->id) }}" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}

					<?php
                    	$emailTemplate = \App\Http\AdminHelpers::emailTemplate('Send Username and Password');
                    ?>

					<div class="form-group">
						<label>{{ trans('site.subject') }}</label>
						<input type="text" class="form-control" name="subject" value="{{ $emailTemplate->subject }}"
							required>
					</div>

					<div class="form-group">
						<label>From</label>
						<input type="email" class="form-control" placeholder="Email" name="from_email"
							value="{{ $emailTemplate->from_email }}">
					</div>

					<div class="form-group">
						<label>{{ trans('site.message') }}</label>
						<textarea name="message" cols="30" rows="10"
								class="form-control tinymce">{!! $emailTemplate->email_content !!}</textarea>
					</div>

					<button type="submit" class="btn btn-primary pull-right">{{ trans('site.send') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="restoreCourseModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">
					Restore Course
				</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}

					<div class="form-group">
						<label>{!! trans('site.end-date') !!}</label>
						<input type="date" class="form-control" name="end_date" required>
					</div>

					<button type="submit" class="btn btn-primary pull-right">Restore</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="scriptNotesModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans_choice('site.notes', 2) }}</h4>
			</div>
			<div class="modal-body">

				<p></p>

			</div>
		</div>
	</div>
</div>

<div id="personalAssignmentModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Personal Assignment Modal</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<input type="hidden" name="course_id" value="">
					<input type="hidden" name="learner_id">

					<div class="form-group">
						<label>{{ trans('site.title') }}</label>
						<input type="text" class="form-control" name="title" placeholder="{{ trans('site.title') }}"
						 required value="">
					</div>

					<div class="form-group">
						<label>{{ trans('site.description') }}</label>
						<textarea class="form-control" name="description"
						 placeholder="{{ trans('site.description') }}" rows="6"></textarea>
					</div>

					<div class="form-group">
						<label>{{ trans('site.submission-date') }}</label>
						<div class="input-group submission-date-group">
							
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
						<label>{{ trans('site.expected-finish') }}</label>
						<input type="date" class="form-control" name="expected_finish">
					</div>

					<div class="form-group">
						<label>{{ trans('site.max-words') }}</label>
						<input type="number" class="form-control" name="max_words">
					</div>

					<div class="form-group">
						<label>{{ trans('site.send-letter-to-editor') }}</label> <br>
						<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" data-size="small"
							   name="send_letter_to_editor">
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
	  
					<button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.save') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')
	<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
	<script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>
<script>
    let translations = {
        delete_course : "{!! trans('site.delete-from-webinar-pakke-question') !!}"
    };

	jQuery(document).ready(function(){

        // tinymce editor config and intitalization

		$(".showEmailBtn").click(function(){
		   let modal = $("#showEmailModal");
		   let message = $(this).data('message');
		   modal.find('.modal-body').html(message);
		});

		$('.defaultAllowAccessBtn').click(function(){
			var action = $(this).data('action');
			$('#lessonDefaultAccessModal form').attr('action', action)
		});


		$('.allowAccessBtn').click(function(){
			var action = $(this).data('action');
			$('#lessonAccessModal form').attr('action', action)
		});

        $(".editWorkshopNoteBtn").click(function(){
            let notes = $(this).data('notes');
            let action = $(this).data('action');
            let modal = $("#editWorkshopNoteModal");
            let form = modal.find('form');

            form.attr('action', action);
            form.find('[name=notes]').text(notes);
        });


		$('.setAvailabilityBtn').click(function(){
			var title = $(this).data('title');
			var start_date = $(this).data('start_date');
			var end_date = $(this).data('end_date');
			var action = $(this).data('action');
			var modal = $('#setAvailabilityModal');
			var form = modal.find('form');

			modal.find('.modal-title strong').text(title);
			form.attr('action', action);
			form.find('input[name=start_date]').val(start_date);
			form.find('input[name=end_date]').val(end_date);
		});

		$(".setDisableCourseBtn").click(function(){
			var title = $(this).data('title');
			var start_date = $(this).data('disable_start_date');
			var end_date = $(this).data('disable_end_date');
			var action = $(this).data('action');
			var modal = $('#setDisableCourseModal');
			var form = modal.find('form');

			modal.find('.modal-title strong').text(title);
			form.attr('action', action);
			form.find('input[name=disable_start_date]').val(start_date);
			form.find('input[name=disable_end_date]').val(end_date);
		});

		$(".setDisableUserBtn").click(function(){
			var title = $(this).data('title');
			var start_date = $(this).data('disable_start_date');
			var end_date = $(this).data('disable_end_date');
			var action = $(this).data('action');
			var modal = $('#setDisableCourseModal');
			var form = modal.find('form');

			modal.find('.modal-title strong').text(title);
			form.attr('action', action);
			form.find('input[name=disable_start_date]').val(start_date);
			form.find('input[name=disable_end_date]').val(end_date);
		});

		$(".removeCourseTakenDisableBtn").click(function(){
			var action = $(this).data('action');
			var modal = $('#removeCourseTakenDisableModal');
			var form = modal.find('form');

			form.attr('action', action);
		});

		$(".sendRegretFormBtn").click(function() {
            let modal = $('#sendRegretFormModal');
            let action = $(this).data('action');
            let form = modal.find('form');

            form.attr('action', action);
		});

		$(".setCourseTakenStartedAtBtn").click(function(){
            let started_at = $(this).data('started_at');
            let modal = $('#updateCourseTakenStartedAtModal');
            let form = modal.find('form');
            let action = $(this).data('action');

            form.attr('action', action);
            form.find('input[name=started_at]').val(started_at);
		});

		$("#moveToggle").change(function() {
		    if(this.checked) {
		    	$('select[name=move_learner_id]').prop('required', true);
		    	$('#moveRelationships').removeClass('hidden');
		    } else {
		    	$('select[name=move_learner_id]').prop('required', false);
		    	$('#moveRelationships').addClass('hidden');
		    }
		});

		var deleteForm = $('#deleteLearnerModal form');

		deleteForm.on('submit', function(e){
			if( $('#moveToggle').is(':checked') ){
				var checkedItems = deleteForm.find('input[name="moveItems[]"]:checked');
				if( checkedItems.length < 1 || $('select[name=move_learner_id]').val() == null ) {
					if( checkedItems.length < 1 ){
						deleteForm.find('input[name="moveItems[]"]').parent().css('color', 'red');
					}
					e.preventDefault();
					return false;
				}
			}
		});

        $(".is-manuscript-locked-toggle").change(function(){
            var shopManuscriptTakenId = $(this).attr('data-id');
            var is_checked = $(this).prop('checked');
            var check_val = is_checked ? 1 : 0;
            $.ajax({
                type:'POST',
                url:'/is-manuscript-locked-status',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: { "shop_manuscript_taken_id" : shopManuscriptTakenId, 'is_manuscript_locked' : check_val },
                success: function(data){
                }
            });
        });

        let invoice_table = $("#invoice-table");

        invoice_table.on("click", ".fikenCreditNoteBtn", function(){
            let action = $(this).data('action');
            $("#fikenCreditNoteModal").find('form').attr('action', action);
        });

        invoice_table.on("click", ".updateDueBtn", function(){
            let action = $(this).data('action');
            let form = $("#updateInvoiceDueModal").find('form');
            form.attr('action', action);
            let due = $(this).data('date');
            form.find("[type=date]").val(due);

        });

        invoice_table.on("click", ".deleteInvoiceBtn", function(){
            let action = $(this).data('action');
            $("#deleteInvoiceModal").find('form').attr('action', action);
        });

        invoice_table.on("click", ".vippsFakturaBtn", function(){
            let action = $(this).data('action');
            let vipps_phone_number = $(this).data('vipps-number');
            let modal = $("#vippsFakturaModal");
            modal.find('form').attr('action', action);
            modal.find('input[name=mobile_number]').val(vipps_phone_number);
        });

		$("#createInvoiceModal").find("[name=product_type]").change(function() {
			var selectedOption = $(this).find('option:selected');
            // Get the data-id attribute value
            var dataId = selectedOption.data('product-id');
			$("#createInvoiceModal").find("[name=product_id]").val(dataId);
		})

        $(".viewOrderBtn").click(function(){
            let fields = $(this).data('fields');
            let modal = $("#viewOrderModal");
			let orderListTable = modal.find("#order-list-table");
			let editingServiceContainer = modal.find("#editing-services-container");

			orderListTable.removeClass('hidden');
			editingServiceContainer.addClass('hidden');

			if (fields.type === 10) {
				orderListTable.addClass('hidden');
				editingServiceContainer.removeClass('hidden');

				$.ajax({
					type: "GET",
					url: "/learner/order/" + fields.id + "/editing-services",
					success: function(data) {
						editingServiceContainer.html(data);
					}
				})
			}

            modal.find("#displayDate").text(fields.created_at_formatted);
            modal.find(".package-variation").text(fields.payment_mode_id === 1 ? fields.packageVariation : fields.item);
            modal.find(".payment-mode").text(fields.payment_mode_id === 1 ? 'Bankoverføring' : '');
            modal.find(".payment-plan").text(fields.payment_plan ? fields.payment_plan.plan : '');

            modal.find('.price-formatted').text(fields.price_formatted);

            modal.find('.discount-row').removeClass('hide');
            modal.find('.discount-formatted').text(fields.discount_formatted);

            if (!fields.discount) {
                modal.find('.discount-row').addClass('hide');
            }

            modal.find('.per-month-row').addClass('hide');
            if (fields.plan_id !== 8) {
                modal.find('.per-month-row').removeClass('hide');
            }

            modal.find('.per-month').text(fields.monthly_price_formatted);
            modal.find('.total-formatted').text(fields.total_formatted);
		});

		$(".submitInvoice").click(function(){
			let form = $(this).closest('form');
			let hasPaymentPlanSelected = form.find('input[name="payment_plan_id"]:checked').length;
			let planInMonths = parseInt(form.find('[name=payment_plan_in_months]').val());
			let price = parseInt(form.find('[name=price]').val());

			if ((planInMonths === 0 || isNaN(planInMonths)) && !hasPaymentPlanSelected) {
				alert('Please select plan details or input custom month.');
				return;
			}

			if (price === 0 || isNaN(price)) {
				alert('Please add a price.');
				return;
			}

			form.trigger('submit');
		});

        $("#submitDeleteInvoice").click(function(e) {
           e.preventDefault();
            $(this).attr('disabled','disabled');
            $("#deleteInvoiceModal").find('form').submit();
		});

		$(".addSveaCreditNoteBtn").click(function() {
			let modal = $("#addSveaCreditNoteModal");
			let action = $(this).data('action');
			let fields = $(this).data('fields');

			let form = modal.find('form');

			form.attr('action', action);
		});

		$(".sveaDeliverBtn").click(function(){
			let modal = $("#sveaDeliverModal");
			let action = $(this).data('action');
			let form = modal.find('form');

			form.attr('action', action);
		});

        $(".deleteFromCourseBtn").click(function(){
            let action = $(this).data('action');
            let title = $(this).data('course-title');
            title = translations.delete_course.replace("_course_title_", title);
            $("#deleteFromCourseModal").find('form').attr('action', action);
            $("#deleteFromCourseModal").find('p').html(title);
		});

        $(".renewCourseBtn").click(function(){
            let action = $(this).data('action');
            $("#renewCourseModal").find('form').attr('action', action);
        });

        /* $("#submitDeleteFromCourse").click(function(e){
            e.preventDefault();
            $(this).attr('disabled','disabled');
            $("#deleteFromCourseModal").find('form').submit();
		}); */

        $(".setApprovedDateBtn").click(function(){
            let course_taken_id = $(this).data('course_taken_id');
            $("#setApprovedDateModal").find('[name=course_taken_id]').val(course_taken_id);
		});

        /*
        * for statistics
        * */

        var dataPoints = [];

        var options = {
            animationEnabled: true,
            title: {
                text: ""
            },
            axisY: {
                title: "Target Goal",
                suffix: "CHR",
                includeZero: true
            },
            axisX: {
                title: "Months"
            },
            data: [{
                type: "column",
                yValueFormatString: "#,###"
                //dataPoints: dataPoints
            }]
        };


        var chart = new CanvasJS.Chart("chartContainer",options);

        $(".showStatisticsBtn").click(function() {
            var action = $(this).data('action');
            var maximum = $(this).data('maximum');
            var from_month = $(this).data('from-month');
            var to_month = $(this).data('to-month');
            //options.axisY.maximum = $(this).data('maximum'); // set a max value for the y axis

            chart.options.data[0].dataPoints = [];
            $.getJSON(action, function(data){
                $.each(data,function(k,v) {
                    chart.options.data[0].dataPoints.push({
                        label: v.month,
                        y: v.words
                    });
                });

                chart.options.data[0].dataPoints.push({
                    label: "Target Total",
                    y: maximum
                });

                options.title.text = from_month+' - '+to_month;
                chart.render();
            });
        });
	});


    $(".approveDateBtn").click(function(){
        let action = $(this).data('action');
        let approved_date = $(this).data('date');
        let form = $("#approveDateModal").find('form');

        form.attr('action', action);
        form.find('[name=approved_date]').val(approved_date);
    });

    $(".suggestDateBtn").click(function(){
        let action = $(this).data('action');
        let form = $("#suggestDateModal").find('form');

        form.attr('action', action);
    });

    $('.assignEditorBtn').click(function(){
        let action = $(this).data('action');
        let editor = $(this).data('editor');
        let modal = $('#assignEditorModal');
        modal.find('select').val(editor);
        modal.find('form').attr('action', action);

		if (editor) {
			modal.find('form').find('select[name=editor_id]').val(editor.id).trigger('change');
		}
    });

    $(".setReplayBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#setReplayModal');
        modal.find('form').attr('action', action);
	});

    $(".deleteCoachingBtn").click(function() {
        let action = $(this).data('action');
        let modal = $('#deleteCoachingModal');
        modal.find('form').attr('action', action);
	});

	$(".resendEmailHistoryBtn").click(function(){
		let record = $(this).data('record');
		let modal = $("#resendEmailHistoryModal");

		modal.find("[name=parent]").val(record.parent);
		modal.find("[name=parent_id]").val(record.parent_id);
		//modal.find("[name=message]").innerHTML(record.message);
		modal.find("[name=subject]").val(record.subject);
		modal.find("[name=from_email]").val(record.from_email);
		//modal.find("[name=recipient]").val(record.recipient_email);

console.log(record);
		tinymce.get('sendEmailHistoryEditor').execCommand('mceRefresh');
		setTimeout(function(){
			console.log("inside set timeout");
			console.log(record.message);
            tinymce.activeEditor.setContent(record.message);
		}, 200);
	});

    $(".booksForSaleBtn").click(function() {
        let record = $(this).data('record');
        let modal = $('#booksForSaleModal');
        modal.find('[name=id]').val('');
		modal.find('[name=project_id]').val('').trigger('change');
		/* modal.find('[name=isbn]').val('');
		modal.find('[name=ebook_isbn]').val('');
		modal.find('[name=title]').val(''); */
		modal.find('[name=description]').text('');
		modal.find('[name=price]').val('');

        if (record) {
            modal.find('[name=id]').val(record.id);
			modal.find('[name=project_id]').val(record.project_id).trigger('change');
            /* modal.find('[name=isbn]').val(record.isbn);
            modal.find('[name=ebook_isbn]').val(record.ebook_isbn);
            modal.find('[name=title]').val(record.title); */
            modal.find('[name=description]').text(record.description);
            modal.find('[name=price]').val(record.price);
		}
	});

    $(".bookSalesBtn").click(function() {
        let modal = $("#bookSalesModal");
        let books = $(this).data('books');
        let record = $(this).data('record');
        modal.find('[name=id]').val('');

        let bookContainer = modal.find("[name=book_id]");
        bookContainer.empty();
        let generateBooks = "<option value='' selected disabled>- Select Book -</option>";

        $.each(books, function(k, book) {
            generateBooks += "<option value='" + book.id + "'>" + book.title + "</option>";
		});

        bookContainer.append(generateBooks);

        if (record) {
            modal.find('[name=id]').val(record.id);
            modal.find('[name=book_id]').val(record.user_book_for_sale_id);
            modal.find('[name=sale_type]').val(record.sale_type);
            modal.find('[name=quantity]').val(record.quantity);
            modal.find('[name=amount]').val(record.amount);
            modal.find('[name=date]').val(record.date);
        }
    });

    $(".deleteRecordBtn").click(function() {
        let modal = $("#deleteRecordModal");
        let action = $(this).data('action');
        let title = $(this).data('title');
        modal.find('.modal-title').text(title);
        modal.find('form').attr('action', action);
	});

	$(".updateOtherServiceStatusBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#updateOtherServiceStatusModal');
        let service = $(this).data('service');
        let title = 'Korrektur';

        if (service === 1) {
            title = 'Språkvask';
		}
        modal.find('form').attr('action', action);
        modal.find('.modal-title').find('span').text(title);
	});

	$(".deleteOtherServiceBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#deleteOtherServiceModal');
        modal.find('form').attr('action', action);
	});

    $(".setOtherServiceFinishDateBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#setOtherServiceFinishDateModal');
        let finish = $(this).data('finish');

        modal.find('form').attr('action', action);
        modal.find('form').find('[name=expected_finish]').val(finish);
    });

    $(".viewHelpWithBtn").click(function(){
        let details = $(this).data('details');
        let modal = $("#viewHelpWithModal");

        modal.find('.modal-body').find('pre').text(details);
    });

    $(".setCoachingApprovedDateBtn").click(function(){
        let approved_date = $(this).data('approved_date');
        let action = $(this).data('action');
        let modal = $("#setCoachingApprovedDateModal");

        modal.find('form').attr('action', action);
        modal.find('.modal-body').find('[name=approved_date]').val(approved_date);
	});

    $(".editDiplomaBtn").click(function(){
       let action = $(this).data('action');
       let course = $(this).data('course');
       let modal = $('#editDiplomaModal');

       modal.find('form').attr('action', action);
       modal.find('[name=course_id]').val(course).trigger('change');

	});

    $(".deleteDiplomaBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#deleteDiplomaModal');
        modal.find('form').attr('action', action);
	});

    $(".setPrimaryEmailBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#setPrimaryEmailModal');
        modal.find('form').attr('action', action);
	});

    $(".removeSecondaryEmailBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#removeSecondaryEmailModal');
        modal.find('form').attr('action', action);
	});

    $(".finishTaskBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#finishTaskModal');
        modal.find('form').attr('action', action);
	});

    $(".editTaskBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#editTaskModal');
        let fields = $(this).data('fields');
        modal.find('form').attr('action', action);
        modal.find('[name=task]').text(fields.task);
        modal.find('[name=user_id]').val(fields.user_id);
        modal.find('[name=available_date]').val(fields.available_date);
        modal.find('form').find('[name=assigned_to]').val(fields.assigned_to).trigger('change');
	});

    $(".deleteTaskBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#deleteTaskModal');
        modal.find('form').attr('action', action);
	});

	$(".deleteSelfPublishingBtn").click(function(){
		let action = $(this).data('action');
		let modal = $('#deleteSelfPublishingModal');
		modal.find('form').attr('action', action);
	});

	$(".addTimeRegisterBtn").click(function() {
        let modal = $("#timeRegisterModal");
        let modal_title = 'Add Time';
        modal.find('.modal-title').text(modal_title);
        modal.find('[name=id]').val('');
	});

	$(".editTimeRegisterBtn").click(function() {
        let modal = $("#timeRegisterModal");
        let modal_title = 'Edit Time';
        let data = $(this).data('record');
        modal.find('.modal-title').text(modal_title);
        modal.find('[name=id]').val(data.id);
        modal.find('[name=project_id]').val(data.project_id).trigger('change');
        modal.find('[name=date]').val(data.date);
        modal.find('[name=time]').val(data.time);
        modal.find('[name=time_used]').val(data.time_used);
        modal.find('[name=description]').val(data.description);
        modal.find('.edit-container').removeClass('hide');
	});

	$(".deleteTimeRegisterBtn").click(function() {
        let action = $(this).data('action');
        let modal = $('#deleteTimeRegisterModal');
        modal.find('form').attr('action', action);
	});

	$(".adjustTime").click(function() {
	    let time = parseFloat($(this).data('time'));
	    let modal = $("#timeRegisterModal");
	    let timeField = isNaN(parseFloat(modal.find('[name=time]').val())) ? 0 : parseFloat(modal.find('[name=time]').val());
        modal.find('[name=time]').val( timeField + time);
	});

	$(".timeUsedBtn").click(function() {
        let timeRegister = $(this).data('time-register');
        let modal = $("#timeUsedModal");
        modal.find("[name=time_register_id]").val(timeRegister.id);

        $.ajax({
            type:'GET',
            url:'/time-register/' + timeRegister.id + '/time-used-list',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: {},
            success: function(data){
                modal.find('tbody').empty();
                let tr = "";
                $.each(data, function(k, record) {
                    console.log(record);
                    tr += "<tr>";
                    	tr += "<td>" + record.date + "</td>";
                    	tr += "<td>" + record.time_used + "</td>";
                    	tr += "<td>" + record.description + "</td>";
                    	tr += "<td>" +
								"<button class='btn btn-primary btn-xs editTimeUsedBtn' data-toggle='modal'" +
							" data-target='#timeUsedFormModal' onclick='editTimeUsed(" + JSON.stringify(record) + ")'><i class='fa fa-edit'></i></button>" +
							"<button class='btn btn-danger btn-xs' data-toggle='modal' data-target='#deleteTimeUsedModal'" +
							" onclick='deleteTimeUsed(" + JSON.stringify(record) + ")' style='margin-left:5px'><i class='fa fa-trash'></i></button>";
							"</td>";
                    tr += "</tr>";
				});

                modal.find('tbody').append(tr);
            }
        });
	});

	$(".addTimeUsedBtn").click(function() {
        let timeUsedFormModal = $("#timeUsedFormModal");
        timeUsedFormModal.find('.modal-title').text('Add time used');
        timeUsedFormModal.find("[name=time_used_id]").val('');
        timeUsedFormModal.find("[name=date]").val('');
        timeUsedFormModal.find("[name=time_used]").val('');
        timeUsedFormModal.find("[name=description]").val('');
	});

	$(".saveTimeUsedBtn").click(function() {
	    let modal = $("#timeUsedFormModal");
	    let time_register_id = $("#timeUsedModal").find("[name=time_register_id]").val();
	    let time_used_id = modal.find("[name=time_used_id]").val();
	    let date = modal.find("[name=date]").val();
	    let time_used = modal.find("[name=time_used]").val();
	    let description = modal.find("[name=description]").val();
	    let self = $(this);
        self.attr('disabled', true);
        $.ajax({
            type:'POST',
            url:'/time-register/' + time_register_id + '/save-time-used',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: {
                time_used_id: time_used_id,
                date: date,
                time_used: time_used,
                description: description,
			},
            success: function(data){
                let timeUsedModal = $("#timeUsedModal");
                timeUsedModal.find('tbody').empty();
                let tr = "";
                $.each(data, function(k, record) {
                    console.log(record);
                    tr += "<tr>";
                    tr += "<td>" + record.date + "</td>";
                    tr += "<td>" + record.time_used + "</td>";
                    tr += "<td>" + record.description + "</td>";
                    tr += "<td>" +
                        "<button class='btn btn-primary btn-xs editTimeUsedBtn' data-toggle='modal'" +
                        " data-target='#timeUsedFormModal' onclick='editTimeUsed(" + JSON.stringify(record) + ")'><i class='fa fa-edit'></i></button>"+
                        "<button class='btn btn-danger btn-xs' data-toggle='modal' data-target='#deleteTimeUsedModal'" +
                        " onclick='deleteTimeUsed(" + JSON.stringify(record) + ")' style='margin-left:5px'><i class='fa fa-trash'></i></button>";
                    	"</td>";
                    tr += "</tr>";
                });

                timeUsedModal.find('tbody').append(tr);

                self.attr('disabled', false);
                modal.modal('hide');
            }
        });
	});

    $('#orders-table, #course-order-attachments-table, #course-assignment-table').dataTable( {
        "ordering": false
    } );

    $(".addPrivateMessageBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#privateMessageModal');
        modal.find('form').attr('action', action);
        modal.find('form').find("[name=_method]").remove();
        setTimeout(function(){
            tinymce.activeEditor.setContent("");
        }, 100);
	});

    $(".editPrivateMessageBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#privateMessageModal');
        let fields = $(this).data('fields');
        modal.find('form').prepend("<input type='hidden' name='_method' value='PUT'>");
        modal.find('form').attr('action', action);

        setTimeout(function(){
            tinymce.activeEditor.setContent(fields.message);
		}, 200);
	});

    $(".deletePrivateMessageBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#deletePrivateMessageModal');
        modal.find('form').attr('action', action);
    });

    $(".assignmentManuscriptEmailBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#assignmentManuscriptEmailModal');
        modal.find('form').attr('action', action);
    });

    $(".deleteAssignmentAddOnBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#deleteAssignmentAddOnModal');
        modal.find('form').attr('action', action);
    });

    $(".expiry-reminder-toggle").change(function(){
        let course_taken_id = $(this).attr('data-id');
        let is_checked = $(this).prop('checked');
        let check_val = is_checked ? 1 : 0;
        $.ajax({
            type:'POST',
            url:'/course_taken/' + course_taken_id + '/set-expiry-reminder',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: { 'send_expiry_reminder' : check_val },
            success: function(data){
            }
        });

    });

    $(".webinar-auto-register-toggle").change(function(){
        let learner_id = $(this).attr('data-id');
        let is_checked = $(this).prop('checked');
        let check_val = is_checked ? 1 : 0;
        $.ajax({
            type:'POST',
            url:'/learner/' + learner_id + '/webinar-auto-register-update',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: { 'auto_renew' : check_val },
            success: function(data){
            }
        });

    });

    $(".registeredWebinarEmailBtn").click(function(){
        let action = $(this).data('action');
		let joinUrl = $(this).data('url');
        let modal = $('#registeredWebinarEmailModal');
        modal.find('form').attr('action', action);
		modal.find('[name=join_url]').attr('value', joinUrl);
    });

    $(".registeredWebinarRemoveBtn").click(function() {
		let action = $(this).data('action');
		let modal = $('#registeredWebinarRemoveModal');
		modal.find('form').attr('action', action);
	});

    $("select.template").change(function() {
        let template = $(this).children("option:selected");
        let fields = template.data('fields');
        let modal = $("#sendEmailModal");
        let form = modal.find('form');

        form.find('[name=subject]').val(fields.subject);
        tinymce.get('sendEmailEditor').setContent(fields.email_content);
        form.find('[name=from_email]').val(fields.from_email);
	});

    $("select.assignment-template").change(function(){
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

    $(".is-publishing-learner-toggle").change(function(){
        let learner_id = $(this).attr('data-id');
        let is_checked = $(this).prop('checked');
        let check_val = is_checked ? 1 : 0;

        $.ajax({
            type:'POST',
            url:'/learner/' + learner_id + '/update-is-publishing-learner',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: { 'is_self_publishing_learner' : check_val },
            success: function(data){
            }
        });
    });

    $(".setVippsEFakturaBtn").click(function(){
        let vipps_phone_number = $(this).data('vipps-number');
        $("#setVippsEFakturaModal").find('input[name=mobile_number]').val(vipps_phone_number);
    });

    $(".restoreCourseBtn").click(function(){
        let modal = $('#restoreCourseModal');
        let action = $(this).data('action');
        modal.find('form').attr('action', action);
    });

    $('.notes').click(function(){
        let notes = $(this).data('notes');
        let modal = $('#scriptNotesModal');
        modal.find('p').text(notes);

    });

    $(".editSubmissionDateBtn").click(function() {
        let submission_date = $(this).data('submission_date');
        let modal = $('#editSubmissionDateModal');
        let action = $(this).data('action');
        modal.find('form').attr('action', action);
        modal.find('[name=submission_date]').val(submission_date);
	});

    $(".editAvailableDateBtn").click(function() {
        let available_date = $(this).data('available_date');
        let modal = $('#editAvailableDateModal');
        let action = $(this).data('action');
        modal.find('form').attr('action', action);
        modal.find('[name=available_date]').val(available_date);
	});

    $(".editMaxWordsBtn").click(function() {
        let max_words = $(this).data('max_words');
        let allow_up_to = $(this).data('allow_up_to');
        let modal = $('#editMaxWordsModal');
        let action = $(this).data('action');
        modal.find('form').attr('action', action);
        modal.find('[name=max_words]').val(max_words);
        modal.find('[name=allow_up_to]').val(allow_up_to);
    });


	$(document).on("change", ".disable-learner-toggle", function() {
		let userId = $(this).data("id");
		let isChecked = $(this).prop("checked");
		let assignmentId = $(this).data("assignment-id");

		$.ajax({
			method: "POST",
			url: "/assignment/" + assignmentId + "/disable-learner",
			data: {isChecked: isChecked, user_id: userId},
			success: function(data) {
				$(".assignment-" + assignmentId).toggleClass('d-none');
			}
		})
	});

	function personalAssignment(user_id, assignment) {
		console.log(assignment);
		let action = "/assignment/" + assignment.id + "/disabled-learner-assignment/save";
		let modal = $("#personalAssignmentModal");
		let submissionGroup = modal.find('.submission-date-group');
		modal.find('form').attr('action', action);
		modal.find("[name=course_id]").val(assignment.course_id);
		modal.find("[name=learner_id]").val(user_id);		
		modal.find("[name=title]").val(assignment.title);		
		modal.find("[name=description]").text(assignment.description);		

		submissionGroup.empty();
		let submissionDate = '';
		let submissionDateText = '';
		if(hasLetter(assignment.submission_date)) {
			submissionDate = "<input type='datetime-local' class='form-control' " +
			" name='submission_date' min='0' required value='" + formatSubmissionDate(assignment.submission_date) + "'>";
			submissionDateText = 'date';
		} else {
			submissionDate = "<input type='number' class='form-control' " +
			" name='submission_date' min='0' required value='" + assignment.submission_date + "'>";
			submissionDateText = 'days';
		}

		submissionGroup.append(submissionDate 
		+ '<span class="input-group-addon assignment-delay-text" id="basic-addon2">' + submissionDateText + '</span>');

		modal.find("[name=available_date]").val(formatDate(assignment.available_date));
		modal.find("[name=editor_expected_finish]").val(formatDate(assignment.editor_expected_finish));
		modal.find("[name=expected_finish]").val(formatDate(assignment.expected_finish));
		modal.find("[name=max_words]").val(assignment.max_words);

		modal.find("[name=send_letter_to_editor]").bootstrapToggle('off');
		if (assignment.send_letter_to_editor) {
			modal.find("[name=send_letter_to_editor]").bootstrapToggle('on');
		}

		modal.find("[name=editor_id]").val(assignment.editor_id);
	}

	function hasLetter(str) {
		return /[a-zA-Z]/.test(str);
	}

	function formatSubmissionDate(dateString) {
		if (!dateString) {
			return '';
		}

		const date = new Date(dateString);
		const year = date.getFullYear();
		const month = String(date.getMonth() + 1).padStart(2, '0');
		const day = String(date.getDate()).padStart(2, '0');
		const hours = String(date.getHours()).padStart(2, '0');
		const minutes = String(date.getMinutes()).padStart(2, '0');
		const seconds = String(date.getSeconds()).padStart(2, '0');

		return `${year}-${month}-${day}T${hours}:${minutes}:${seconds}`;
	}

	function formatDate(dateString) {
		if (!dateString) {
			return '';
		}

		const date = new Date(dateString);
		const year = date.getFullYear();
		const month = String(date.getMonth() + 1).padStart(2, '0');
		const day = String(date.getDate()).padStart(2, '0');

		return `${year}-${month}-${day}`;
	}

	function updateOtherServiceFields(type) {
	    let modal = $("#addOtherServiceModal");
	    let add_correction_text = "{{ trans('site.add-correction') }}";
	    let add_copy_editing_text = "{{ trans('site.add-copy-editing') }}";
	    let modal_title = add_correction_text;
	    if (type === 1) {
	        modal_title = add_copy_editing_text;
		}

		modal.find('.modal-title').text(modal_title);
	    modal.find('form').find('[name=is_copy_editing]').val(type);
	}

	function disableSubmit(t) {
        let submit_btn = $(t).find('[type=submit]');
        submit_btn.text('');
        submit_btn.append('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
        submit_btn.attr('disabled', 'disabled');
	}

    function payment_plan_change(t) {
        let plan = $(t).data('plan');
        let split_invoice = $('input:radio[name=split_invoice]');
        split_invoice.prop('disabled', false);

        if( plan === 'Hele beløpet' ) {
            split_invoice.prop('disabled', true);
            split_invoice.prop('checked', false);
        }
    }

	function payment_plan_in_month_change(t) {
		let plan = $(t).data('plan');
        let split_invoice = $('input:radio[name=split_invoice]');
        let payment_plan_id = $('input:radio[name=payment_plan_id]');

        split_invoice.prop('disabled', false);

		payment_plan_id.prop('checked', false);
        if( plan === 'Hele beløpet' ) {
            split_invoice.prop('disabled', true);
            split_invoice.prop('checked', false);
        }
	}

    function showEmailMessage(t) {
        let modal = $("#showEmailModal");
        let message = $(t).data('message');
        modal.find('.modal-body').html(message);
    }

	function showEmailHistoryMessage(t) {
		let modal = $("#showEmailModal");
        let id = $(t).data('id');
		
		$.ajax({
			method: "GET",
			url: "/learner/email-history/" + id + "/details",
			data: {},
			success: function(data) {
				console.log(data);
			}
		})
	}

    function countChar(val) {
        let len = val.value.length;
        if (len >= 136) {
            val.value = val.value.substring(0, 136);
            $('.charNum').text(0 + " character left");
        } else {
            let charText = "characters left";
            if (136 - len === 1) {
                charText = "character left";
            }
            $('.charNum').text(136 - len + " "+charText);
        }
    }

    function editTimeUsed(record) {
        let timeUsedFormModal = $("#timeUsedFormModal");
        timeUsedFormModal.find('.modal-title').text('Edit time used');
        timeUsedFormModal.find("[name=time_used_id]").val(record.id);
        timeUsedFormModal.find("[name=date]").val(record.date);
        timeUsedFormModal.find("[name=time_used]").val(record.time_used);
        timeUsedFormModal.find("[name=description]").val(record.description);
	}

	function deleteTimeUsed(record) {
	    let modal = $("#deleteTimeUsedModal");
        modal.find('form').attr('action', '/time-register/time-used/' + record.id + '/delete' );
	}

	function projectChanged(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];

        // Get the value and data-info attribute of the selected option
        const selectedValue = selectedOption.value;
        const selectedDataRegistrations = selectedOption.getAttribute('data-registrations');
        const selectedDataBookname = selectedOption.getAttribute('data-book_name');

        let isbnContainer = $(".isbn-container");
        let bookTitleContainer = $("#booksForSaleModal").find("[name=title]");
        let list = "<ul>";
            
        isbnContainer.empty();
        bookTitleContainer.val(selectedDataBookname);

        $.each(JSON.parse(selectedDataRegistrations), function(k, registration) {
			if (registration.field === 'isbn') {
				list += "<li>" + registration.value + "</li>";
			}
        });

        list += "</ul>";
        isbnContainer.append(list);

    }

	function registeredWebinarRemove(url) {
		let modal = $('#registeredWebinarRemoveModal');
		modal.find('form').attr('action', url);
	}
</script>
	{{--<script type="text/javascript" src="{{ mix('js/app.js') }}"></script>--}}
@stop