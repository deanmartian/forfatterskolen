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

<div class="col-md-10 col-md-offset-1">
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
					</a>
				</div>
			</div>
			<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editPasswordModal">{{ trans('site.edit-password') }}</button>
			<button type="button" class="btn btn-info" data-toggle="modal" data-target="#editContactModal">{{ trans('site.edit-contact-info') }}</button>
			<button type="button" class="margin-top btn btn-danger" data-toggle="modal" data-target="#deleteLearnerModal">{{ trans('site.delete-learner') }}</button>
			<button type="button" class="margin-top btn btn-success" data-toggle="modal" data-target="#learnerNotesModal">{{ trans_choice('site.notes', 2) }}</button>
			<button type="button" class="margin-top btn btn-primary" data-toggle="modal" data-target="#sendEmailModal">{{ trans('site.send-email') }}</button>
			<button type="button" class="margin-top btn btn-warning" data-toggle="modal" data-target="#preferredEditorModal">Preferred Editor</button>

			<div class="former-course-container">
				<h4>{{ trans('site.former-courses') }}</h4>
				<ul>
					<?php $expiredCoursePackageManuscripts = array(); /*$learner->coursesTakenOld = formerCourses*/ ?>

					@foreach($learner->formerCourses as $formerCourse)
						<li>
							{{ $formerCourse->package->course->title }} ({{ $formerCourse->package->variation }})
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
													{{ trans('site.started-at') }}: {{ Carbon\Carbon::parse($courseTaken->started_at)->format('M d, Y H.i') }}
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

												@if ($courseTaken->package->course->id == 17)
													<br>
													<label>Send Expiry Reminder:</label>
													<input type="checkbox" data-toggle="toggle" data-on="Yes"
														   class="expiry-reminder-toggle" data-off="No"
														   data-id="{{$courseTaken->id}}" data-size="mini"
													@if($courseTaken->send_expiry_reminder) {{ 'checked' }} @endif>

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
																<td><a href="{{ route('admin.lesson.edit', ['course_id' => $courseTaken->package->course->id, 'lesson_id' => $lesson->id]) }}">{{ $lesson->title }}</a></td>
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
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach($learner->shopManuscriptsTaken as $shopManuscriptTaken)
								{{--@if (!in_array($shopManuscriptTaken->package_shop_manuscripts_id,$expiredCoursePackageManuscripts))--}}
									<tr>
										<td>
											@if($shopManuscriptTaken->is_active)
												<a href="{{ route('shop_manuscript_taken', ['id' => $learner->id, 'shop_manuscript_taken_id' => $shopManuscriptTaken->id]) }}">{{$shopManuscriptTaken->shop_manuscript->title}}</a>
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
											@elseif( $shopManuscriptTaken->status == 'Started' )
												<span class="label label-primary">Started</span>
											@elseif( $shopManuscriptTaken->status == 'Not started' )
												<span class="label label-warning">Not started</span>
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
								<th width="150"></th>
							</tr>
						</thead>
						<tbody>
							@foreach($learner->tasks as $task)
								<tr>
									<td>{!! nl2br($task->task) !!}</td>
									<td>{{ \App\User::find($task->assigned_to)->full_name }}</td>
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
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>{{ trans_choice('site.invoices', 1) }} #</th>
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

									@if ($invoice->fiken_invoice_id)
										<button class="btn btn-success btn-xs vippsFakturaBtn" style="margin-top: 5px"
												data-toggle="modal"
											data-target="#vippsFakturaModal"
												data-action="{{ route('admin.learner.invoice.vipps-e-faktura', $invoice->id) }}">
											VIPPS eFaktura
										</button>
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
					<h4>{{ trans('site.order-history') }}</h4>
				</div>
				<div class="table-responsive" style="padding: 10px">
					<table class="table" id="orders-table">
						<thead>
						<tr>
							<th>{{ trans('site.details') }}</th>
							<th>{{ trans('site.date-ordered') }}</th>
						</tr>
						</thead>
						<tbody>
						@foreach($learner->orders as $order)
							<tr>
								<td>
									{!! \App\Http\AdminHelpers::getOrderDetails($order) !!}
								</td>
								<td>{{ \App\Http\FrontendHelpers::formatDate($order->created_at) }}</td>
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
					<table class="table">
						<thead>
							<tr>
								<th>{{ trans_choice('site.assignments', 1) }}</th>
								<th>{{ trans_choice('site.courses', 1) }}</th>
								<th>Editor</th>
								<th>{{ trans_choice('site.manuscripts', 1) }}</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php
					        $assignments = [];
					        foreach( $learner->coursesTaken as $course ) :
					            foreach( $course->package->course->assignments as $assignment ) :
					                $assignments[] = $assignment;
					            endforeach;
					        endforeach;
					        ?>
							@foreach($assignments as $assignment)
								<?php $manuscript = $assignment->manuscripts->where('user_id', $learner->id)->first();
								$assignmentCourse = $assignment->course;
								?>
								@if( $manuscript )
								<?php $extension = explode('.', basename($manuscript->filename)); ?>
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
																'id' => $assignment_group_id]
														)."'>".$assignment_group['title']."</a>";
												}
											}
										}

										?>
									</td>
									<td>
										<a href="{{ route('admin.course.show', $assignment->course->id) }}">
											{{ $assignment->course->title }}
										</a>
									</td>
									<td>
										@if ($manuscript->editor)
											{{ $manuscript->editor->full_name }}
										@endif
									</td>
									<td>
										@if( end($extension) == 'pdf' || end($extension) == 'odt' )
										<a href="/js/ViewerJS/#../..{{ $manuscript->filename }}">{{ basename($manuscript->filename) }}</a>
										@elseif( end($extension) == 'docx' )
										<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$manuscript->filename}}">{{ basename($manuscript->filename) }}</a>
										@endif
									</td>
									<td>
										<button class="btn btn-primary btn-xs assignmentManuscriptEmailBtn" data-toggle="modal"
												data-target="#assignmentManuscriptEmailModal"
												data-action="{{ route('assignment.send-email-to-manuscript-user', $manuscript->id) }}">
											Send Email
										</button>
									</td>
								</tr>
								@endif
							@endforeach
						</tbody>
					</table>
				</div>
			</div> <!-- end assignments -->

			<div class="panel panel-default">
				<div class="panel-body">
					<h4>
						Personal Assignments
					</h4>
				</div>
				<div class="table-responsive">
					<table class="table">
						<thead>
						<tr>
							<th>{{ trans_choice('site.assignments', 1) }}</th>
							<th>{{ trans_choice('site.courses', 1) }}</th>
							<th>Editor</th>
							<th>{{ trans_choice('site.manuscripts', 1) }}</th>
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
											{!! $manuscript->file_link !!}
										@endif
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div> <!-- end personal assignments -->

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
										{{ $correction->editor->full_name }}
									@else
										<button class="btn btn-xs btn-warning assignEditorBtn" data-toggle="modal" data-target="#assignEditorModal" data-action="{{ route('admin.other-service.assign-editor', ['id' => $correction->id, 'type' => 2]) }}">Assign Editor</button>
									@endif
								</td>
								<td>
									{{ \App\Http\FrontendHelpers::formatDate($correction->created_at) }}
								</td>
								<td>
									@if ($correction->expected_finish)
										{{ \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($correction->expected_finish) }}
										<br>
									@endif

									@if ($correction->status !== 2)
										<a href="#setOtherServiceFinishDateModal" data-toggle="modal"
										   class="setOtherServiceFinishDateBtn"
										   data-action="{{ route('admin.other-service.update-expected-finish',
										   ['id' => $correction->id, 'type' => 2]) }}"
										   data-finish="{{ $correction->expected_finish ?
										strftime('%Y-%m-%dT%H:%M:%S', strtotime($correction->expected_finish)) : '' }}">
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
										{{ $copy_editing->editor->full_name }}
									@else
										<button class="btn btn-xs btn-warning assignEditorBtn" data-toggle="modal" data-target="#assignEditorModal" data-action="{{ route('admin.other-service.assign-editor', ['id' => $copy_editing->id, 'type' => 1]) }}">{{ trans('site.assign-editor') }}</button>
									@endif
								</td>
								<td>
									{{ \App\Http\FrontendHelpers::formatDate($copy_editing->created_at) }}
								</td>
								<td>
									@if ($copy_editing->expected_finish)
										{{ \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($copy_editing->expected_finish) }}
										<br>
									@endif

									@if ($copy_editing->status !== 2)
										<a href="#setOtherServiceFinishDateModal" data-toggle="modal"
										   class="setOtherServiceFinishDateBtn"
										   data-action="{{ route('admin.other-service.update-expected-finish',
										   ['id' => $copy_editing->id, 'type' => 1]) }}"
										   data-finish="{{ $copy_editing->expected_finish ?
										strftime('%Y-%m-%dT%H:%M:%S', strtotime($copy_editing->expected_finish)) : '' }}">
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
												data-action="{{ route('admin.other-service.delete', ['id' => $copy_editing->id, 'type' => 1]) }}"><i class="fa fa-check"></i></button>
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
					<h4>{{ trans('site.coaching-timer') }}</h4>
				</div>

				<div class="table-responsive">
					<table class="table">
						<thead>
						<tr>
							<th>{{ trans_choice('site.manus', 2) }}</th>
							<th>{{ trans_choice('site.learners', 1) }}</th>
							<th>{{ trans('site.length') }}</th>
							<th>{{ trans('site.learner-suggestion') }}</th>
							<th>{{ trans('site.admin-suggestion') }}</th>
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
								<td>

								</td>
								<td></td>
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
								<td>
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
								</td>
								<td>
									{{ $coachingTimer->approved_date ?
                                    \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($coachingTimer->approved_date)
                                     : ''}}
								</td>
								<td>
									@if ($coachingTimer->editor_id)
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
										<button class="btn btn-info btn-xs showEmailBtn" data-toggle="modal"
										data-target="#showEmailModal"
										data-message="{{ $email->email }}">Show Message</button>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div> <!-- end emails section -->

			<div class="panel panel-default">
				<div class="panel-body">
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
										<button class="btn btn-info btn-xs showEmailBtn" data-toggle="modal"
												data-target="#showEmailModal"
												data-message="{{ $emailHistory->message }}">Show Message</button>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div> <!-- end email history section -->

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
							<th></th>
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
									<button class="btn btn-primary btn-xs registeredWebinarEmailBtn" data-toggle="modal"
											data-target="#registeredWebinarEmailModal"
											data-action="{{ route('admin.learner.send-webinar-registrant-email',
											[$learner->id, $registeredWebinar->id]) }}">
										{{ trans('site.send-email') }}
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
					<button class="btn btn-primary pull-right btn-xs addPrivateMessageBtn" data-toggle="modal"
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
									<button class="btn btn-warning btn-xs editPrivateMessageBtn"
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
            </div>

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
      	<form method="POST">
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
      	<form method="POST">
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



<div id="addShopManuscriptModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ ucwords(trans('site.add-shop-manuscript')) }}</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" enctype="multipart/form-data" action="{{ route('admin.shop-manuscript.add_learner', $learner->id) }}">
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
							{{ trans('site.assign-to') }}
						</label>
						<select name="assigned_to" class="form-control select2" required>
							<option value="" disabled="" selected>-- Select Assignee --</option>
							@foreach( App\User::where('role', 1)->orderBy('created_at', 'desc')->get() as $editor )
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
							{{ trans('site.assign-to') }}
						</label>
						<select name="assigned_to" class="form-control select2" required>
							<option value="" disabled="" selected>-- Select Assignee --</option>
							@foreach( App\User::where('role', 1)->orderBy('created_at', 'desc')->get() as $editor )
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
									   id="{{$paymentPlan->plan}}" required onchange="payment_plan_change(this)"
									   data-plan-id="{{ $paymentPlan->id }}">
								<label>{{$paymentPlan->plan}} </label>
							</div>
						@endforeach
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

					<button type="submit" class="btn btn-primary pull-right">{{ trans('site.create-invoice') }}</button>
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

<div id="vippsFakturaModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">
					VIPPS eFaktura
				</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}

					<div class="form-group">
						<label>Mobile Number</label>
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
      	<form method="POST" action="{{ route('admin.learner.update', $learner->id) }}">
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
      	<form method="POST" action="{{ route('admin.learner.update', $learner->id) }}">
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
      	<form method="POST" action="{{ route('admin.learner.delete', $learner->id) }}">
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
					  enctype="multipart/form-data">
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
				<form method="POST" action="">
					{{ csrf_field() }}
					{{ method_field('delete') }}
					<p>
						{{--{!! trans('site.delete-from-webinar-pakke-question') !!}--}}
					</p>
					<button class="btn btn-danger pull-right" id="submitDeleteFromCourse">{{ trans('site.delete') }}</button>
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
							@foreach( App\User::where('role', 1)->orderBy('created_at', 'desc')->get() as $editor )
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
							@foreach( App\User::where('role', 1)->orderBy('created_at', 'desc')->get() as $editor )
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
						<input type="datetime-local" name="expected_finish" class="form-control" required>
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
							   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document">
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
						<select name="editor_id" class="form-control select2" required>
							<option value="" disabled="" selected>-- Select Editor --</option>
							@foreach( App\User::where('role', 1)->orderBy('created_at', 'desc')->get() as $editor )
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
				<form action="{{ route('admin.learner.add-email', $learner->id) }}" method="POST">
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
				<form method="POST" action="{{ route('admin.learner.set-preferred-editor', $learner->id) }}">
					{{csrf_field()}}
					<div class="form-group">
						<label>{{ trans_choice('site.editors', 1) }}</label>
						<select class="form-control select2" name="editor_id" required>
							<option value="" selected disabled>
								-- Select Editor --
							</option>
							@foreach( App\User::where('role', 1)->orderBy('id', 'desc')->get()  as $admin)
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

        $(".deleteInvoiceBtn").click(function(){
           let action = $(this).data('action');
           $("#deleteInvoiceModal").find('form').attr('action', action);
		});

        $(".vippsFakturaBtn").click(function() {
            let action = $(this).data('action');
            $("#vippsFakturaModal").find('form').attr('action', action);
		});

        $(".fikenCreditNoteBtn").click(function(){
            let action = $(this).data('action');
            $("#fikenCreditNoteModal").find('form').attr('action', action);
        });

        $("#submitDeleteInvoice").click(function(e) {
           e.preventDefault();
            $(this).attr('disabled','disabled');
            $("#deleteInvoiceModal").find('form').submit();
		});

        $(".updateDueBtn").click(function(){
            let action = $(this).data('action');
            let form = $("#updateInvoiceDueModal").find('form');
            form.attr('action', action);
            let due = $(this).data('date');
            form.find("[type=date]").val(due);

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

        $("#submitDeleteFromCourse").click(function(e){
            e.preventDefault();
            $(this).attr('disabled','disabled');
            $("#deleteFromCourseModal").find('form').submit();
		});

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
        modal.find('form').find('[name=assigned_to]').val(fields.assigned_to).trigger('change');
	});

    $(".deleteTaskBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#deleteTaskModal');
        modal.find('form').attr('action', action);
	});

    $('#orders-table, #course-order-attachments-table').dataTable( {
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

    $(".registeredWebinarEmailBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#registeredWebinarEmailModal');
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
</script>
@stop