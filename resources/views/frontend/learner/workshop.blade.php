@extends('frontend.layout')

@section('title')
<title>Workshops &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
	<style>
		.nav-tabs {
			border-bottom: 1px solid #ddd;
		}

		.nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus {
			color: #555;
			cursor: default;
			background-color: #fff;
			border: 1px solid #ddd;
			border-bottom-color: transparent;
		}

		.nav-tabs>li>a {
			background: #f0f2f4;
			color: #666;
			outline: medium none;
			padding: 10px 24px;
			line-height: 1.42857143;
			border: 1px solid transparent;
			border-bottom: 0 none;
			border-radius: 4px 4px 0 0;
			position: relative;
			display: block;
			margin-right: 8px;
		}
	</style>
@stop


@section('content')
<div class="account-container">
	
	@include('frontend.partials.learner-menu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12">
			<h3 class="no-margin-top">Påmeldte skriveverksted(er)</h3>
		
			<div class="row"> 
			@foreach( Auth::user()->workshopsTaken as $workshop )
			<div class="col-sm-12 col-md-6">
				<div class="panel panel-default">
					<div class="learner-workshop-image" style="background-image: url({{$workshop->workshop->image}})"></div>
				  	<div class="panel-body">
						<h3 class="no-margin-top">{{ $workshop->workshop->title }}</h3>
						<div>Når: <strong>{{ date_format(date_create($workshop->workshop->date), 'M d, Y H.i') }}</strong></div>
						<div>Hvor: <strong>{{ $workshop->workshop->location }}</strong></div>
						<div>Varighet: <strong>{{ $workshop->workshop->duration }} hours</strong></div>
						<div>Meny: <strong>{{ $workshop->menu->title }}</strong></div>
						<div>Notater: <strong>{{ $workshop->notes }}</strong></div>
				  		<div>
				  		@if( !$workshop->is_active )
						<a class="btn btn-warning disabled margin-top">Pending</a>
				  		@endif
				  		</div>
						{{-- FOR TASK 6 --}}
						<div class="clearfix"></div>
						<div style="text-align: center; margin-top: 20px">
							<a class="btn btn-theme btn-sm" href="{{ route('front.workshop.show', $workshop->workshop_id) }}">Klikk her for fremtidige workshoper eller for bestilling</a>
						</div>
				  	</div>
				</div>
			</div>
			@endforeach

				@if(!count(Auth::user()->workshopsTaken))
					<div style="text-align: center; margin-top: 20px">
						<a class="btn btn-theme btn-sm" href="{{ route('front.workshop.index') }}">Klikk her for fremtidige workshoper eller for bestilling</a>
					</div>
				@endif
			</div>

		</div>

		<div class="col-sm-12">
			<nav>
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#nav-coaching" data-toggle="tab">Coaching Timer</a>
					</li>
				</ul>
			</nav>
			<div class="tab-content">
				<div class="tab-pane fade in active" id="nav-coaching">
					<div class="panel panel-default" style="border-top: 0">
						<div class="panel-body">
							<?php
								$packages = \App\Package::where('has_coaching', '>', 0)->pluck('id');
                            	$coachingTimerTaken = Auth::user()->coachingTimersTaken()->pluck('course_taken_id');
								$checkCourseTakenWithCoaching = Auth::user()->coursesTaken()->whereIn('package_id', $packages)
									->whereNotIn('id', $coachingTimerTaken)->get();
							?>
							@if ($checkCourseTakenWithCoaching->count())
								<button class="btn btn-xs btn-primary pull-right" data-toggle="modal"
										data-target="#addCoachingSessionModal"
								data-action="{{ route('learner.course-taken.coaching-timer.add') }}"
								id="addCoachingSessionBtn">
									Add Coaching Lesson
								</button>
							@endif

							<div class="clearfix"></div>

							<div class="table-users table-responsive">
								<table class="table no-margin-bottom">
									<thead>
									<tr>
										<th>Manus</th>
										<th>Coaching Time</th>
										<th>Suggested Date</th>
										<th>Admin Suggested Date</th>
										<th>Approved Date</th>
										<th>Date Ordered</th>
									</tr>
									</thead>
									<tbody>
									<?php
										$coachingTimers = Auth::user()->coachingTimers()->paginate(5);
									?>
									@foreach($coachingTimers as $coachingTimer)
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
														</div>
													@endfor
												@endif

												@if (!$coachingTimer->approved_date)
													<a href="#suggestDateModal" data-toggle="modal"
													   class="suggestDateBtn"
													   data-action="{{ route('learner.coaching-timer.suggest_date', $coachingTimer->id) }}">Suggest Different Dates</a>
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
															@if (!$coachingTimer->approved_date)
																<button class="btn btn-success btn-xs approveDateBtn pull-right"
																		data-toggle="modal" data-target="#approveDateModal"
																		data-date="{{ $suggested_dates_admin[$i] }}"
																		data-action="{{ route('learner.coaching-timer.approve_date', $coachingTimer->id) }}">
																	<i class="fa fa-check"></i>
																</button>
															@endif
														</div>
													@endfor
												@endif
											</td>
											<td>
												{{ $coachingTimer->approved_date ?
                                                \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($coachingTimer->approved_date)
                                                 : ''}}
											</td>
											<td>
												{{ \App\Http\FrontendHelpers::formatDate($coachingTimer->created_at) }}
											</td>
										</tr>
									@endforeach
									</tbody>
								</table>
							</div>
						</div>
						<div class="pull-right">
							{{$coachingTimers->render()}}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	@if ( $errors->any() )
        <?php
        $alert_type = session('alert_type');
        if(!Session::has('alert_type')) {
            $alert_type = 'danger';
        }
        ?>
		<div class="alert alert-{{ $alert_type }}" id="fixed_to_bottom_alert">
			<a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
			<ul>
				@foreach($errors->all() as $error)
					<li>{{$error}}</li>
				@endforeach
			</ul>
		</div>
	@endif

	<div class="clearfix"></div>
</div>

<!-- Approve Coaching Timer Date Modal -->
<div id="approveDateModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Approve Date</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="">
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

<div id="addCoachingSessionModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Add Coaching Session</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action=""
					  onsubmit="disableSubmit(this)" enctype="multipart/form-data">
					{{csrf_field()}}

					<div class="form-group">
						<label>Manuscript</label>
						<input type="file" class="form-control" name="manuscript"
							   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document">
					</div>

					@for($i = 1; $i <= 3; $i++)
						<div class="form-group">
							<label>Suggested Date</label>
							<input type="datetime-local" class="form-control" name="suggested_date[]" required>
						</div>
					@endfor

					@if ($checkCourseTakenWithCoaching->count())
						<div class="form-group">
							<label>Use Included Session from Course</label>
							<select name="course_taken_id" class="form-control" required
							id="course_taken_id">
								<option value="" disabled selected> -- Select --</option>
								@foreach($checkCourseTakenWithCoaching as $courseTaken)
									<option value="{{ $courseTaken->id }}"
									data-plan="{{ $courseTaken->package->has_coaching }}">
										{{ $courseTaken->package->course->title }} - {{ \App\Http\FrontendHelpers::getCoachingTimerPlanType($courseTaken->package->has_coaching) }}
									</option>
								@endforeach
							</select>
						</div>
						<input type="hidden" name="plan_type">
					@endif

					<div class="text-right margin-top">
						<button type="submit" class="btn btn-success">Submit</button>
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
						<input type="datetime-local" class="form-control" name="suggested_date[]" required>
					</div>

					<div class="form-group">
						<label>Date</label>
						<input type="datetime-local" class="form-control" name="suggested_date[]" required>
					</div>

					<div class="form-group">
						<label>Date</label>
						<input type="datetime-local" class="form-control" name="suggested_date[]" required>
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

@stop

@section('scripts')
	<script>
        $(".approveDateBtn").click(function(){
            let action = $(this).data('action');
            let approved_date = $(this).data('date');
            let form = $("#approveDateModal").find('form');

            form.attr('action', action);
            form.find('[name=approved_date]').val(approved_date);
        });

        $("#addCoachingSessionBtn").click(function(){
            let action = $(this).data('action');
            let form = $("#addCoachingSessionModal").find('form');

            form.attr('action', action);
		});

        $("#course_taken_id").change(function(){
           let plan = $(this).find(':selected').data('plan');
            let form = $("#addCoachingSessionModal").find('form');

            form.find('[name=plan_type]').val(plan);
		});

        $(".suggestDateBtn").click(function(){
            let action = $(this).data('action');
            let form = $("#suggestDateModal").find('form');

            form.attr('action', action);
        });

        function disableSubmit(t) {
            let submit_btn = $(t).find('[type=submit]');
            submit_btn.text('');
            submit_btn.append('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
            submit_btn.attr('disabled', 'disabled');
        }
	</script>
@stop

