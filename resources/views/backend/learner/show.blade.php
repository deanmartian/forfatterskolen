@extends('backend.layout')

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
	<style>
		.former-course-container {
			margin-top: 30px;
		}
	</style>
@stop

@section('title')
<title>{{ $learner->first_name }} &rsaquo; Learners &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3><i class="fa fa-users"></i> All Learners</h3>
	<div class="navbar-form navbar-right">
	  	<div class="form-group">
		  	<form role="search" method="GET" action="{{route('admin.learner.index')}}">
				<div class="input-group">
				  	<input type="text" class="form-control" name="search" value="{{Request::input('search')}}" placeholder="Search learner..">
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
		<a href="{{route('admin.learner.index')}}" class="btn btn-default margin-bottom margin-top"><i class="fa fa-angle-left"></i> All Learners</a>
		</div>
		<div class="col-md-3">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="text-center">
						<div class="learner-profile-image" style="background-image: url({{$learner->profile_image}})"></div>
						<h2>{{$learner->fullName}}</h2>
						{{$learner->email}}
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
					<b>Auto renew course:</b> {{ $learner->auto_renew_courses ? 'Yes' : 'No' }}
				</div>
			</div>
			<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editPasswordModal">Edit Password</button>
			<button type="button" class="btn btn-info" data-toggle="modal" data-target="#editContactModal">Edit Contact Info</button>
			<button type="button" class="margin-top btn btn-danger" data-toggle="modal" data-target="#deleteLearnerModal">Delete Learner</button>
			<button type="button" class="margin-top btn btn-success" data-toggle="modal" data-target="#learnerNotesModal">Notes</button>

			<div class="former-course-container">
				<h4>Former Courses</h4>
				<ul>
					<?php $expiredCoursePackageManuscripts = array(); ?>
				@foreach ($learner->coursesTakenOld as $oldCourse)
					<li>{{ $oldCourse->package->course->title }} ({{ $oldCourse->package->variation }})</li>
						<ul>
						@foreach( $oldCourse->package->shop_manuscripts as $shop_manuscripts )
							<?php array_push($expiredCoursePackageManuscripts, $shop_manuscripts->id);?>
							<li>{{ $shop_manuscripts->shop_manuscript->title }}</li>
						@endforeach
						</ul>
				@endforeach
				</ul>
			</div>

			@if ($learner->notes)
			<div class="col-md-12 no-padding margin-top">
				<b><i>Notes</i></b> <br>
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
			<h4 class="no-margin-top">Courses Taken</h4>
			<div class="row">
				@foreach($learner->coursesTakenNotOld as $courseTaken)
				<div class="col-sm-6">
					<div class="panel panel-default">
						<div class="panel-body">
							<h4 style="margin-bottom: 7px"><a href="{{route('admin.course.show', $courseTaken->package->course->id)}}?section=learners">{{$courseTaken->package->course->title}}</a></h4>
							<p class="no-margin-bottom">
								Status: @if($courseTaken->is_active)
								Active
								@else
								Pending
								@endif
								<br />
								Plan: {{ $courseTaken->package->variation }} <br />
								@if( $courseTaken->hasStarted )
								Started at: {{ Carbon\Carbon::parse($courseTaken->started_at)->format('M d, Y H.i') }}
								@else
								Started at: <em>Not yet started</em>
								@endif
								<br />
								Expires on: 
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
								Start date: {{ $courseTaken->start_date }}
								@endif
								{{--@if( $courseTaken->end_date )--}}
								<br />
								End date: {{ $courseTaken->end_date ? $courseTaken->end_date
								: ($courseTaken->started_at ? \Carbon\Carbon::parse($courseTaken->started_at)->addYear(1)->format('M d, Y') : '') }}
								{{--@endif--}}
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
							Set availability</button> 

							@if( !$courseTaken->is_active )
							<form method="POST" action="{{ route('activate_course_taken') }}" style="margin-top: 7px">
								{{ csrf_field() }}
								<input type="hidden" name="coursetaken_id" value="{{ $courseTaken->id }}">
								<button class="btn btn-warning btn-xs" type="submit"><i class="fa fa-check"></i></button>
							</form>
							@endif
							
							<div class="margin-top">
								<button data-toggle="collapse" class="btn btn-xs btn-success" data-target="#lessons-{{ $courseTaken->id }}">Lessons</button>
							</div>

							<!-- check if webinar-pakke -->
							@if ($courseTaken->package->course->id == 17)
								<div class="margin-top">
									<button class="btn btn-xs btn-danger deleteFromCourseBtn" data-target="#deleteFromCourseModal"
									data-toggle="modal"
									data-action="{{ route('admin.learner.delete-from-course', $courseTaken->id) }}">Delete from course</button>
								</div>
							@endif

							<div class="collapse" id="lessons-{{ $courseTaken->id }}">
								<div class="margin-top"><strong>Lessons</strong></div>
								<div class="table-responsive">
									<table class="table table-bordered no-margin-bottom">
										@foreach( $courseTaken->package->course->lessons as $lesson )
										<tr>
											<td><a href="{{ route('admin.lesson.edit', ['course_id' => $courseTaken->package->course->id, 'lesson_id' => $lesson->id]) }}">{{ $lesson->title }}</a></td>
											<td>
												@if( FrontendHelpers::hasLessonAccess($courseTaken, $lesson) )
												<button class="btn btn-primary btn-xs defaultAllowAccessBtn" data-toggle="modal" data-target="#lessonDefaultAccessModal" data-action="{{ route('admin.course_taken.default_lesson_access', ['course_taken_id' => $courseTaken->id, 'lesson_id' => $lesson->id]) }}">Default access</button>
												@else
												<button class="btn btn-success btn-xs allowAccessBtn" data-toggle="modal" data-target="#lessonAccessModal" data-action="{{ route('admin.course_taken.allow_lesson_access', ['course_taken_id' => $courseTaken->id, 'lesson_id' => $lesson->id]) }}">Allow access</button>
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


			<div class="panel panel-default">
				<div class="panel-body">
					<button class="btn btn-primary pull-right btn-xs" data-toggle="modal" data-target="#addShopManuscriptModal">+ Add Shop Manuscript</button>
					<h4>Shop Manuscripts</h4>
				</div>
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>Manuscript</th>
								<th>Date Ordered</th>
								<th>Status</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach($learner->shopManuscriptsTaken as $shopManuscriptTaken)
								@if (!in_array($shopManuscriptTaken->package_shop_manuscripts_id,$expiredCoursePackageManuscripts))
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
								@endif
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
					<button class="btn btn-primary pull-right btn-xs" data-toggle="modal" data-target="#addToWorkshopModal">+ Add to workshop</button>
						<button class="btn btn-info pull-right btn-xs margin-right-5" data-toggle="modal" data-target="#updateWorkshopCountModal">+ Update workshop count</button>
					<h4>Workshops <span class="badge">{{ /*$workshopTakenCount >= 0*/ $learner->workshopTakenCount ? $workshopTakenCount : $learner->workshopsTaken->count() + $courseWorkshops }}</span></h4>
				</div>
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>Workshop</th>
								<th>Date Ordered</th>
								<th width="250">Notes</th>
								<th>Status</th>
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
									{{ $workshopTaken->notes }} <br>
									<button class="btn btn-primary btn-xs editWorkshopNoteBtn" data-toggle="modal"
									data-target="#editWorkshopNoteModal"
											data-action="{{ route('admin.learner.workshop-taken.update-notes', $workshopTaken->id) }}"
									data-notes="{{ $workshopTaken->notes }}">
										Edit Note
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
					<button class="btn btn-primary pull-right btn-xs" data-toggle="modal" data-target="#addInvoiceModal">+ Add Invoice</button>
					<h4>Invoices</h4>
				</div>
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>Invoice #</th>
								<th>Status</th>
								<th>Created At</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach($learner->invoices as $invoice)
							<?php
							$fikenURL = false;
							foreach( $fikenInvoices as $fikenInvoice ) :
							    if( $invoice->fiken_url == $fikenInvoice->_links->alternate->href ) :
							      $fikenURL = true;
							      break;
							    endif;
							endforeach;
							$fikenError = false;
                            /*if( $fikenURL ) :
                              $sale = FrontendHelpers::FikenConnect($fikenInvoice->sale);
                              $status = $sale->paid ? "BETALT" : "UBETALT";
                            else :
                              $fikenError = true;
                            endif;*/
							?>
							<tr>
		    					<td>
		    						@if( !$fikenError )
		    						<a href="{{route('admin.invoice.show', $invoice->id)}}">{{ $fikenInvoice->invoiceNumber }}</a>
		    						@endif
		    					</td>
								<td>
									@if($invoice->fiken_is_paid)
										<span class="label label-success">BETALT</span>
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
									<button class="btn btn-danger btn-xs deleteInvoiceBtn" data-toggle="modal"
									data-target="#deleteInvoiceModal"
									data-action="{{ route('admin.learner.invoice.delete', $invoice->id) }}"><i class="fa fa-trash"></i></button>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>


			<div class="panel panel-default">
				<div class="panel-body">
					<button class="btn btn-primary pull-right btn-xs" data-toggle="modal" data-target="#addManuscriptModal">+ Upload Manuscript</button>
					<h4>Manuscripts</h4>
				</div>
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>ID</th>
								<th>Manuscript</th>
								<th>Words Count</th>
								<th>Grade</th>
								<th>Feedbacks</th>
								<th>Course</th>
								<th>Date Uploaded</th>
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
					<h4>Assignments</h4>
				</div>
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>Assignment</th>
								<th>Manuscript</th>
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
										{{ $assignment->title }}
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
										@if( end($extension) == 'pdf' || end($extension) == 'odt' )
										<a href="/js/ViewerJS/#../..{{ $manuscript->filename }}">{{ basename($manuscript->filename) }}</a>
										@elseif( end($extension) == 'docx' )
										<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$manuscript->filename}}">{{ basename($manuscript->filename) }}</a>
										@endif
									</td>
								</tr>
								@endif
							@endforeach
						</tbody>
					</table>
				</div>
			</div>

			<!-- correction -->
			<div class="panel panel-default">
				<div class="panel-body">
					<button class="btn btn-primary pull-right btn-xs" data-toggle="modal" data-target="#addOtherServiceModal"
					onclick="updateOtherServiceFields(0)">+ Add Korrektur</button>
					<h4>Korrektur</h4>
				</div>

				<div class="table-responsive">
					<table class="table">
						<thead>
						<tr>
							<th>Manus</th>
							<th>Editor</th>
							<th>Date Ordered</th>
							<th>Expected Finish</th>
							<th>Status</th>
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
							onclick="updateOtherServiceFields(1)">+ Add Språkvask</button>
					<h4>Språkvask</h4>
				</div>

				<div class="table-responsive">
					<table class="table">
						<thead>
						<tr>
							<th>Manus</th>
							<th>Editor</th>
							<th>Date Ordered</th>
							<th>Expected Finish</th>
							<th>Status</th>
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
										<button class="btn btn-xs btn-warning assignEditorBtn" data-toggle="modal" data-target="#assignEditorModal" data-action="{{ route('admin.other-service.assign-editor', ['id' => $copy_editing->id, 'type' => 1]) }}">Assign Editor</button>
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
											Set Date
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
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>

			</div>
			<!-- end copy editing -->

			<!-- coaching timer -->
			<div class="panel panel-default">
				<div class="panel-body">
					<button class="btn btn-primary pull-right btn-xs" data-toggle="modal"
							data-target="#addCoachingSessionModal">
						+ Add Coaching Session
					</button>
					<h4>Coaching Timer</h4>
				</div>

				<div class="table-responsive">
					<table class="table">
						<thead>
						<tr>
							<th>Manus</th>
							<th>Learner</th>
							<th>Length</th>
							<th>Learner Suggestion</th>
							<th>Admin Suggestion</th>
							<th>Approved Date</th>
							<th>Assigned To</th>
							<th>Replay</th>
						</tr>
						</thead>
						<tbody>
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
											View Help With
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
										   data-action="{{ route('admin.other-service.coaching-timer.suggestDate', $coachingTimer->id) }}">Suggest Different Dates</a>
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
										<button class="btn btn-xs btn-warning assignEditorBtn" data-toggle="modal" data-target="#assignEditorModal" data-action="{{ route('admin.other-service.assign-editor', ['id' => $coachingTimer->id, 'type' => 3]) }}">Assign Editor</button>
									@endif
								</td>
								<td>
									@if ($coachingTimer->replay_link)
										<a href="{{ $coachingTimer->replay_link }}" target="_blank">
											View Replay
										</a>
									@endif
									<button class="btn btn-xs btn-primary setReplayBtn" data-toggle="modal"
											data-target="#setReplayModal" data-action="{{ route('admin.other-service.coaching-timer.set_replay', $coachingTimer->id) }}">Set Replay</button>
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
					<h4>Emails</h4>
				</div>
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>Subject</th>
								<th>Date</th>
								<th>Attachment</th>
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
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>

            <div class="panel panel-default">
                <div class="panel-body">
                    <button class="btn btn-primary pull-right btn-xs" data-toggle="modal" data-target="#addDiplomaModal">
                        + Add Diploma
                    </button>
                    <h4>Kursbevis</h4>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Course</th>
                            <th>Diploma</th>
                            <th>Date Uploaded</th>
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
										Download
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
					<h4>Words Written</h4>
				</div>

				<div class="table-responsive">
					<table class="table">
						<thead>
						<tr>
							<th>Words Written</th>
							<th>Date</th>
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
					<h4>Words Written Goal</h4>
				</div>

				<div class="table-responsive">
					<table class="table">
						<thead>
						<tr>
							<th>From</th>
							<th>To</th>
							<th>Total Words</th>
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
					<h4>Last 5 Logins</h4>
				</div>
				<div class="table-responsive">
					<table class="table">
						<thead>
						<tr>
							<th>Time</th>
							<th>IP Address</th>
							<th>Country</th>
							<th>Provider</th>
							<th>Platform</th>
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

<div id="lessonDefaultAccessModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Set default access for this lesson</h4>
      </div>

      <div class="modal-body">
      	<form method="POST">
      		{{ csrf_field() }}
      		Set default learner access for this lesson?
      		<div class="text-right margin-top">
      			<button type="submit" class="btn btn-primary">Confirm</button>
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
        <h4 class="modal-title">Allow access for this lesson</h4>
      </div>

      <div class="modal-body">
      	<form method="POST">
      		{{ csrf_field() }}
      		Allow learner access for this lesson?
      		<div class="text-right margin-top">
      			<button type="submit" class="btn btn-primary">Confirm</button>
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
        <h4 class="modal-title">Set dates for <strong></strong></h4>
      </div>

      <div class="modal-body">
      	<form method="POST">
      		{{ csrf_field() }}
      		<div class="form-group">
      			<label>Start date</label>
      			<input type="date" class="form-control" name="start_date">
      		</div>
      		<div class="form-group">
      			<label>End date</label>
      			<input type="date" class="form-control" name="end_date">
      		</div>
      		<div class="text-right">
      			<button type="submit" class="btn btn-primary">Save</button>
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
        <h4 class="modal-title">Add Shop Manuscript</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" enctype="multipart/form-data" action="{{ route('admin.shop-manuscript.add_learner', $learner->id) }}">
      		{{ csrf_field() }}
      		<?php 
			$shopManuscripts = \App\ShopManuscript::all();
			?>
      		<div class="form-group">
      			<label>Shop manuscript</label>
      			<select class="form-control select2" name="shop_manuscript_id" required>
      				<option value="" selected disabled>- Search shop manuscript -</option>
					@foreach($shopManuscripts as $shopManuscript)
					<option value="{{ $shopManuscript->id }}">{{ $shopManuscript->title }}</option>>
					@endforeach
  				</select>
      		</div>
      		<div class="form-group">
      			<label>File</label>
      			<div><em>* Godkjente fil formater er DOCX, PDF og ODT.</em></div>
      			<input type="file" class="form-control" name="manuscript" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
      		</div>
      		<button type="submit" class="btn btn-primary pull-right">Add shop manuscript</button>
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
        <h4 class="modal-title">Add Invoice for {{ $learner->fullname }}</h4>
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
      		<button type="submit" class="btn btn-primary pull-right">Create Invoice</button>
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
        <h4 class="modal-title">Upload Manuscript</h4>
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
      		<button type="submit" class="btn btn-primary pull-right">Upload manuscript</button>
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
        <h4 class="modal-title">Edit password</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{ route('admin.learner.update', $learner->id) }}">
      		{{ csrf_field() }}
      		{{ method_field('PUT') }}
      		<input type="hidden" name="field" value="password">
      		<div class="form-group">
      			<label>New password</label>
      			<input type="password" class="form-control" name="password" required>
      		</div>
      		<div class="form-group">
      			<label>Confirm password</label>
      			<input type="password" class="form-control" name="password_confirmation" required>
      		</div>
      		<button type="submit" class="btn btn-primary pull-right">Save</button>
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
        <h4 class="modal-title">Edit contact info</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{ route('admin.learner.update', $learner->id) }}">
      		{{ csrf_field() }}
      		{{ method_field('PUT') }}
      		<input type="hidden" name="field" value="contact">
      		<div class="row form-group">
      			<div class="col-sm-6">
	      			<label>First name</label>
	      			<input type="tel" class="form-control" name="first_name" value="{{ $learner->first_name }}">
      			</div>
      			<div class="col-sm-6">
	      			<label>Last name</label>
	      			<input type="text" class="form-control" name="last_name" value="{{ $learner->last_name }}">
	      		</div>
      		</div>
      		<div class="row form-group">
      			<div class="col-sm-6">
	      			<label>Phone</label>
	      			<input type="tel" class="form-control" name="phone" value="{{ $learner->address->phone }}">
      			</div>
      			<div class="col-sm-6">
	      			<label>Street</label>
	      			<input type="text" class="form-control" name="street" value="{{ $learner->address->street }}">
	      		</div>
      		</div>
      		<div class="row form-group">
      			<div class="col-sm-6">
	      			<label>ZIP</label>
	      			<input type="text" class="form-control" name="zip" value="{{ $learner->address->zip }}">
	      		</div>
      			<div class="col-sm-6">
	      			<label>City</label>
	      			<input type="text" class="form-control" name="city" value="{{ $learner->address->city }}">
	      		</div>
      		</div>
      		<button type="submit" class="btn btn-primary pull-right">Save</button>
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
        <h4 class="modal-title">Delete learner</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{ route('admin.learner.delete', $learner->id) }}">
      		{{ csrf_field() }}
      		{{ method_field('DELETE') }}
      		Are you sure to delete this learner? <br />
      		<em>Warning: This cannot be undone.</em>

      		<div class="checkbox margin-top">
				<label><input type="checkbox" id="moveToggle" name="moveStatus">Move courses taken, shop manuscripts, and invoices to an account</label>
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
					<label><input type="checkbox" name="moveItems[]" value="courses_taken">Courses Taken</label>
				</div>
	      		<div class="checkbox">
					<label><input type="checkbox" name="moveItems[]" value="shop_manuscripts">Shop Manuscripts</label>
				</div>
	      		<div class="checkbox">
					<label><input type="checkbox" name="moveItems[]" value="invoices">Invoices</label>
				</div>
      		</div>

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
				<h4 class="modal-title">Add to workshop</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('learner.add_to_workshop') }}" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
                    <?php
                    $workshops = \App\Workshop::where('is_active', 1)->get();
                    ?>
					<div class="form-group">
						<label>Shop manuscript</label>
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
					<button type="submit" class="btn btn-primary pull-right">Submit</button>
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
				<h4 class="modal-title">Update workshop count</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.learner.update_workshop_count', $learner->id) }}">
					{{ csrf_field() }}
					<div class="form-group">
						<label>Workshop Count</label>
						<input type="number" name="workshop_count" step="1" class="form-control"
							   value="{{ $learner->workshopTakenCount ? $learner->workshopTakenCount->workshop_count : ''}}"
							   required>
					</div>

					<button type="submit" class="btn btn-primary pull-right">Submit</button>
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
				<h4 class="modal-title">Notes</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<textarea name="notes" cols="30" rows="10" class="form-control" required></textarea>
					</div>
					<button type="submit" class="btn btn-primary pull-right">Submit</button>
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
					<h4 class="modal-title">Notes</h4>
				</div>
				<div class="modal-body">
					<form method="POST" action="{{ route('learner.add_notes', $learner->id) }}">
						{{ csrf_field() }}
						<div class="form-group">
							<textarea name="notes" id="" cols="30" rows="10" class="form-control" required>{!! $learner->notes !!}</textarea>
						</div>
						<button type="submit" class="btn btn-primary pull-right">Submit</button>
						<div class="clearfix"></div>
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

<div id="deleteInvoiceModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Delete Invoice</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="">
					{{ csrf_field() }}
					{{ method_field('delete') }}
					<p>
						Are you sure you want to delete this invoice?
					</p>
					<button class="btn btn-danger pull-right" id="submitDeleteInvoice">Delete Invoice</button>
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
				<h4 class="modal-title">Delete from Course</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="">
					{{ csrf_field() }}
					{{ method_field('delete') }}
					<p>
						Are you sure you want to delete this learner from Webinar-pakke? <br>
						<em>This cannot be undone</em>
					</p>
					<button class="btn btn-danger pull-right" id="submitDeleteFromCourse">Delete</button>
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
						<label>Manuscript</label>
						<input type="file" class="form-control" name="manuscript" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document"
							   required>
					</div>

					<div class="form-group">
						<label>Send Invoice</label> <br>
						<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
							   name="send_invoice">
					</div>

					<div class="form-group">
						<label>Assign to</label>
						<select name="editor_id" class="form-control select2">
							<option value="" disabled="" selected>-- Select Editor --</option>
							@foreach( App\User::where('role', 1)->orderBy('created_at', 'desc')->get() as $editor )
								<option value="{{ $editor->id }}">{{ $editor->full_name }}</option>
							@endforeach
						</select>
					</div>

					<input type="hidden" name="is_copy_editing">
					<button class="btn btn-success pull-right" type="submit">
						Add
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
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>Set Replay</label>
						<input type="url" name="replay_link" class="form-control" required>
					</div>
					<div class="text-right">
						<button class="btn btn-primary" type="submit">Save</button>
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
				<h4 class="modal-title">Suggest Session Dates</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" id="suggestDateForm"
					  onsubmit="disableSubmit(this)">
					{{csrf_field()}}

					<div class="form-group">
						<label>Date</label>
						<input type="datetime-local" class="form-control" name="suggested_date_admin[]" required>
					</div>

					<div class="form-group">
						<label>Date</label>
						<input type="datetime-local" class="form-control" name="suggested_date_admin[]" required>
					</div>

					<div class="form-group">
						<label>Date</label>
						<input type="datetime-local" class="form-control" name="suggested_date_admin[]" required>
					</div>

					<div class="text-right margin-top">
						<button type="submit" class="btn btn-success">Submit</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
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
				<h4 class="modal-title">Add Coaching Session</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.learner.add-coaching-timer', $learner->id) }}"
					  onsubmit="disableSubmit(this)" enctype="multipart/form-data">
					{{csrf_field()}}

					<div class="form-group">
						<label>Manuscript</label>
						<input type="file" class="form-control" name="manuscript"
							   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document">
					</div>
					
					<div class="form-group">
						<label>Session Length</label>
						<select name="plan_type" class="form-control" required>
							<option value="" disabled="" selected>-- Select --</option>
							<option value="2">30 min</option>
							<option value="1">1 hr</option>
						</select>
					</div>

					<div class="form-group">
						<label>Assign To</label>
						<select name="editor_id" class="form-control select2" required>
							<option value="" disabled="" selected>-- Select Editor --</option>
							@foreach( App\User::where('role', 1)->orderBy('created_at', 'desc')->get() as $editor )
								<option value="{{ $editor->id }}">{{ $editor->full_name }}</option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<label>Send Invoice</label> <br>
						<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
							   name="send_invoice">
					</div>

					<div class="text-right margin-top">
						<button type="submit" class="btn btn-success">Submit</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
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
                <h4 class="modal-title">Add Diploma</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.learner.add-diploma', $learner->id) }}"
                      onsubmit="disableSubmit(this)" enctype="multipart/form-data">
                    {{csrf_field()}}

                    <div class="form-group">
                        <label>Course</label>
                        <select name="course_id" class="form-control select2" required>
                            <option value="" disabled selected>-- Select Course --</option>
                            @foreach(\App\Course::all() as $course)
                                <option value="{{ $course->id }}"> {{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Diploma</label>
                        <input type="file" class="form-control" name="diploma"
                               accept="application/pdf" required>
                    </div>

                    <div class="text-right margin-top">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
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
				<h4 class="modal-title">Edit Diploma</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action=""
					  onsubmit="disableSubmit(this)" enctype="multipart/form-data">
					{{csrf_field()}}

					<div class="form-group">
						<label>Course</label>
						<select name="course_id" class="form-control select2" required>
							<option value="" disabled selected>-- Select Course --</option>
							@foreach(\App\Course::all() as $course)
								<option value="{{ $course->id }}"> {{ $course->title }}</option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<label>Diploma</label>
						<input type="file" class="form-control" name="diploma"
							   accept="application/pdf">
					</div>

					<div class="text-right margin-top">
						<button type="submit" class="btn btn-success">Submit</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
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
				<h4 class="modal-title">Delete Diploma</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					{{ method_field('DELETE') }}

					<p>Are you sure you want to delete this diploma?</p>

					<div class="text-right margin-top">
						<button type="submit" class="btn btn-danger">Delete</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
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
@stop

@section('scripts')
	<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
	<script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>

<script>
	jQuery(document).ready(function(){


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

        $("#submitDeleteInvoice").click(function(e) {
           e.preventDefault();
            $(this).attr('disabled','disabled');
            $("#deleteInvoiceModal").find('form').submit();
		});

        $(".deleteFromCourseBtn").click(function(){
            let action = $(this).data('action');
            $("#deleteFromCourseModal").find('form').attr('action', action);
		});

        $("#submitDeleteFromCourse").click(function(e){
            e.preventDefault();
            $(this).attr('disabled','disabled');
            $("#deleteFromCourseModal").find('form').submit();
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

	function updateOtherServiceFields(type) {
	    let modal = $("#addOtherServiceModal");
	    let modal_title = 'Add Korrektur';
	    if (type === 1) {
	        modal_title = 'Add Språkvask';
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
</script>
@stop