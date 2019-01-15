@extends('frontend.layout')

@section('title')
<title>Workshops &rsaquo; Forfatterskolen</title>
@stop

@section('content')
<div class="learner-container">
	<div class="container">
		<h1 class="font-barlow-regular mb-0">Påmeldte skriveverksted(er)

			@if(!count(Auth::user()->workshopsTaken))
				<a href="{{ route('front.workshop.index') }}" class="btn site-btn-global float-right font-15">
					Klikk her for fremtidige workshoper eller for bestilling
				</a>
			@endif

		</h1>

		<div class="row">
			@foreach( Auth::user()->workshopsTaken as $workshop )
				<div class="col-sm-12 col-md-6 mt-5">
					<div class="card card-global">
						<div class="card-header learner-workshop-image rounded-0"
							 style="background-image: url({{$workshop->workshop->image}})"></div>
						<div class="card-body">
							<h2 class="font-weight-normal font-barlow-semi-bold">{{ $workshop->workshop->title }}</h2>
							<div>
								Når: <span class="font-barlow-semi-bold">
									{{ date_format(date_create($workshop->workshop->date), 'M d, Y H.i') }}
								</span>
							</div>
							<div>
								Hvor: <span class="font-barlow-semi-bold">{{ $workshop->workshop->location }}</span>
							</div>
							<div>
								Varighet:
								<span class="font-barlow-semi-bold">
									{{ $workshop->workshop->duration }} hours
								</span>
							</div>
							<div>
								Meny: <span class="font-barlow-semi-bold">{{ $workshop->menu->title }}</span>
							</div>
							<div>
								Notater: <span class="font-barlow-semi-bold">{{ $workshop->notes }}</span>
							</div>
							<div>
								@if( !$workshop->is_active )
									<a class="btn btn-warning disabled mt-4 color-white">Pending</a>
								@endif
							</div>
						</div>
						<div class="card-footer  no-border p-0">
							<a class="btn site-btn-global w-100 rounded-0" href="{{ route('front.workshop.show', $workshop->workshop_id) }}">Klikk her for fremtidige workshoper eller for bestilling</a>
						</div>
					</div>
				</div>
			@endforeach
		</div> <!-- end row -->

		<div class="row mt-5">
			<div class="col-md-12">
				<div class="card global-card">
					<div class="card-header">
						<h1>
							Coaching Timer
						</h1>
					</div>
					<div class="card-body py-0">
                        <?php
                        $packages = \App\Package::where('has_coaching', '>', 0)->pluck('id');
                        $coachingTimerTaken = Auth::user()->coachingTimersTaken()->pluck('course_taken_id');
                        $checkCourseTakenWithCoaching = Auth::user()->coursesTaken()->whereIn('package_id', $packages)
                            ->whereNotIn('id', $coachingTimerTaken)->get();
                        ?>
						@if ($checkCourseTakenWithCoaching->count())
							<button class="btn btn-xs btn-primary pull-right mt-2 mr-2 rounded-0" data-toggle="modal"
									data-target="#addCoachingSessionModal"
									data-action="{{ route('learner.course-taken.coaching-timer.add') }}"
									id="addCoachingSessionBtn">
								Add Coaching Lesson
							</button>
						@endif

						<div class="table-responsive">
							<table class="table table-global">
								<thead>
								<tr>
									<th>Manus</th>
									<th>Coaching Time</th>
									<th>Mine foreslåtte datoer</th>
									<th>Skriv litt her om hva du vil ha hjelp til</th>
									<th>Forfatterskolens foreslåtte datoer</th>
									<th>Avtalt dato og tid</th>
									<th>Reprise</th>
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
													<div class="mt-2">
														{{ \App\Http\FrontendHelpers::formatDateTimeNor($suggested_dates[$i]) }}
													</div>
												@endfor
											@endif

											@if (!$coachingTimer->approved_date)
												<a href="#suggestDateModal" data-toggle="modal"
												   class="suggestDateBtn"
												   data-action="{{ route('learner.coaching-timer.suggest_date', $coachingTimer->id) }}">Foreslå andre datoer</a>
											@endif
										</td>
										<td>
											<a href="#viewHelpWithModal" class="viewHelpWithBtn"
											   data-toggle="modal" data-details="{{ $coachingTimer->help_with }}"
											   data-action="{{ route('learner.coaching-timer.help_with', $coachingTimer->id) }}">
												Trykk her for å skrive hva du trenger hjelp til
											</a>
										</td>
										<td>
											<?php
											$suggested_dates_admin = json_decode($coachingTimer->suggested_date_admin);
											?>

											@if($suggested_dates_admin)
												@for($i =0; $i <= 2; $i++)
													<div class="mt-2">
														{{ \App\Http\FrontendHelpers::formatDateTimeNor($suggested_dates_admin[$i]) }}
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
											@if ($coachingTimer->replay_link)
												<a href="{{ $coachingTimer->replay_link }}" target="_blank">
													View Replay
												</a>
											@endif
										</td>
									</tr>
								@endforeach
								</tbody>
							</table>
						</div>
					</div> <!-- end card-body -->
					<div class="pull-right">
						{{$coachingTimers->render()}}
					</div>
				</div> <!-- end card -->
			</div> <!-- end col-md-12 -->
		</div> <!-- end row-->

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

	</div>
</div>

<!-- Approve Coaching Timer Date Modal -->
<div id="approveDateModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">Godkjenne dato</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="">
					{{csrf_field()}}
					Er du sikker på at du vil godkjenne denne avtalen?
					<input type="hidden" name="approved_date">
					<div class="text-right mt-4">
						<button type="submit" class="btn btn-success">Aksepter</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Avslå</button>
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
				<h3 class="modal-title">Add Coaching Session</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
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
							<label>min foreslåtte dato</label>
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

					<div class="text-right mt-4">
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
				<h3 class="modal-title">Foreslå en dato for coaching time</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="" id="suggestDateForm"
					  onsubmit="disableSubmit(this)">
					{{csrf_field()}}

					<div class="form-group">
						<label>Dato og tid</label>
						<input type="datetime-local" class="form-control" name="suggested_date[]" required>
					</div>

					<div class="form-group">
						<label>Dato og tid</label>
						<input type="datetime-local" class="form-control" name="suggested_date[]" required>
					</div>

					<div class="form-group">
						<label>Dato og tid</label>
						<input type="datetime-local" class="form-control" name="suggested_date[]" required>
					</div>

					<div class="text-right mt-4">
						<button type="submit" class="btn btn-success">Foreslå</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Avbryt</button>
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
				<h3 class="modal-title">Help With</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form action="" method="post" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<textarea name="help_with" id="" cols="30" rows="10" class="form-control"></textarea>

					<div class="text-right mt-4">
						<button type="submit" class="btn btn-success">Submit</button>
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

        $(".viewHelpWithBtn").click(function(){
            let details = $(this).data('details');
            let action = $(this).data('action');
            let modal = $("#viewHelpWithModal");

            modal.find('form').attr('action', action);
            modal.find('[name=help_with]').text(details);
        });

        function disableSubmit(t) {
            let submit_btn = $(t).find('[type=submit]');
            submit_btn.text('');
            submit_btn.append('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
            submit_btn.attr('disabled', 'disabled');
        }
	</script>
@stop

