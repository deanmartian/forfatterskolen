{{-- @extends('frontend.layout') --}}
@extends('frontend.layouts.course-portal')

@section('title')
<title>Workshops &rsaquo; Forfatterskolen</title>
@stop

@section('content')
<div class="learner-container learner-workshop-wrapper">
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<h1 class="page-title">{{ trans('site.learner.registered-workshop') }}

					@if(!count(Auth::user()->workshopsTaken))
						<a href="{{ route('front.workshop.index') }}" class="btn blue-btn">
							{{ trans('site.learner.workshop-order-text') }}
							<i class="fa fa-book-open"></i>
						</a>
					@endif

				</h1>
			</div>
		</div>

		<div class="row">
			@foreach( Auth::user()->workshopsTaken as $workshop )
				<div class="col-sm-12 col-md-6 mt-5">
					<div class="card card-global">
						<div class="card-header learner-workshop-image rounded-0"
							 style="background-image: url({{$workshop->workshop->image}})"></div>
						<div class="card-body">
							<h2 class="font-weight-normal font-barlow-semi-bold">{{ $workshop->workshop->title }}</h2>
							<div>
								{{ trans('site.learner.when-text') }}: <span class="font-barlow-semi-bold">
									{{ date_format(date_create($workshop->workshop->date), 'M d, Y H.i') }}
								</span>
							</div>
							<div>
								{{ trans('site.learner.where-text') }}: <span class="font-barlow-semi-bold">{{ $workshop->workshop->location }}</span>
							</div>
							<div>
								{{ trans('site.learner.duration-text') }}:
								<span class="font-barlow-semi-bold">
									{{ $workshop->workshop->duration }} hours
								</span>
							</div>
							<div>
								{{ trans('site.learner.menu-text') }}: <span class="font-barlow-semi-bold">{{ $workshop->menu->title }}</span>
							</div>
							<div>
								{{ trans('site.learner.notes-text') }}: <span class="font-barlow-semi-bold">{{ $workshop->notes }}</span>
							</div>
							<div>
								@if( !$workshop->is_active )
									<a class="btn btn-warning disabled mt-4 color-white">{{ trans('site.learner.pending') }}</a>
								@endif
							</div>
						</div>
						<div class="card-footer  no-border p-0">
							<a class="btn site-btn-global w-100 rounded-0" href="{{ route('front.workshop.show', $workshop->workshop_id) }}">
								{{ trans('site.learner.workshop-order-text') }}</a>
						</div>
					</div>
				</div>
			@endforeach
		</div> <!-- end row -->

		<?php
			$packages = \App\Package::where('has_coaching', '>', 0)->pluck('id');
			$coachingTimerTaken = Auth::user()->coachingTimersTaken()->pluck('course_taken_id');
			$checkCourseTakenWithCoaching = Auth::user()->coursesTaken()->whereIn('package_id', $packages)
				->whereNotIn('id', $coachingTimerTaken)->get();
		?>
		<div class="row mt-5">
			<div class="col-md-12">
				<div class="card global-card">
					<div class="card-header">
						<h2 class="pull-left">
							{{ trans('site.front.coaching-timer.title') }}
						</h2>

						@if ($checkCourseTakenWithCoaching->count())
							<button class="btn blue-outline-btn pull-right px-4" data-toggle="modal"
									data-target="#addCoachingSessionModal"
									data-action="{{ route('learner.course-taken.coaching-timer.add') }}"
									id="addCoachingSessionBtn">
								{{ trans('site.learner.add-coaching-lesson') }}
								<i class="fa fa-plus"></i>
							</button>
						@endif
					</div>
					<div class="card-body py-0">

						<div class="table-responsive">
							<table class="table gray-table">
								<thead>
								<tr>
									<th>{{ trans('site.learner.script') }}</th>
									<th>{{ trans('site.learner.coaching-timer') }}</th>
									{{--<th>{{ trans('site.learner.my-suggested-dates') }}</th>--}}
									<th>{{ trans('site.front.coaching-timer.help-with-text') }}</th>
									{{--<th>{{ trans('site.learner.admin-proposed-dates') }}</th>--}}
									<th>{{ trans('site.learner.agreed-date-time') }}</th>
									<th>
										Status
									</th>
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
												<a href="/js/ViewerJS/#../../{{ $coachingTimer->file }}" class="font-weight-bold">
													{{ basename($coachingTimer->file) }}
												</a>
											@elseif( end($extension) == 'docx' )
												<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$coachingTimer->file}}"
													class="font-weight-bold">
													{{ basename($coachingTimer->file) }}
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
													<div class="mt-2">
														{{ \App\Http\FrontendHelpers::formatDateTimeNor($suggested_dates[$i]) }}
													</div>
												@endfor
											@endif

											@if (!$coachingTimer->approved_date)
												<a href="#suggestDateModal" data-toggle="modal"
												   class="suggestDateBtn"
												   data-action="{{ route('learner.coaching-timer.suggest_date', $coachingTimer->id) }}">
													{{ trans('site.learner.suggest-other-dates') }}</a>
											@endif

										</td>-->
										<td>
											@if ($coachingTimer->status !== 1)
												<a href="#viewHelpWithModal" class="viewHelpWithBtn font-weight-bold"
												data-toggle="modal" data-details="{{ $coachingTimer->help_with }}"
												data-action="{{ route('learner.coaching-timer.help_with', $coachingTimer->id) }}">
													{{ trans('site.learner.need-help-with-text') }}
												</a>
											@endif
										</td>
										<!--<td>
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
										</td> -->
										<td class="font-weight-bold">
											{!! $coachingTimer->approved_date ?
											"<i class='fa fa-calendar-check mr-2' style='color:#000000'></i>" 
											. \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($coachingTimer->approved_date)
											 : ''!!}
										</td>
										<td id="coaching-time-{{ $coachingTimer->id }}">
											@if ($coachingTimer->status === 1)
												<b class="custom-badge start rounded-20 px-3 py-1">
													{{ trans('site.learner.finished') }}
												</b>
											@endif

											@if($coachingTimer->status === 0 && !$coachingTimer->approved_date)
												<div class="form-group">
													<button data-et-click-type="start-scheduling"
															data-et-agent-id="610cd73bc02659717c0355b4" type="button"
															onclick="triggerConsoltoAction('open-widget');"
															class="consolto-btn btn site-btn-global font-15"
															data-fields="{{ json_encode($coachingTimer) }}">Book an appointment</button>
												</div>
											@endif

											@if($coachingTimer->status === 2 && !$coachingTimer->approved_date)
												<span class="label label-info">
													Pending Approval
												</span>
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
				<h3 class="modal-title">{{ trans('site.learner.approve-date') }}</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="">
					{{csrf_field()}}
					{{ trans('site.learner.approve-date-question') }}
					<input type="hidden" name="approved_date">
					<div class="text-right mt-4">
						<button type="submit" class="btn btn-success">{{ trans('site.learner.approve-date-accept') }}</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('site.learner.approve-date-decline') }}</button>
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
				<h3 class="modal-title">{{ trans('site.learner.add-coaching-session') }}</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form method="POST" action=""
					  onsubmit="disableSubmit(this)" enctype="multipart/form-data">
					{{csrf_field()}}

					<div class="form-group">
						<label>{{ trans('site.learner.manuscript-text') }}</label>
						<input type="file" class="form-control" name="manuscript"
							   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document">
					</div>

					@for($i = 1; $i <= 3; $i++)
						<div class="form-group">
							<label>{{ trans('site.learner.my-proposed-date') }}</label>
							<input type="datetime-local" class="form-control" name="suggested_date[]" required>
						</div>
					@endfor

					@if ($checkCourseTakenWithCoaching->count())
						<div class="form-group">
							<label>{{ trans('site.learner.use-course-included-session') }}</label>
							<select name="course_taken_id" class="form-control" required
							id="course_taken_id">
								<option value="" disabled selected> -- <{{ trans('site.learner.select-text') }} --</option>
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
						<button type="submit" class="btn btn-success">{{ trans('site.front.submit') }}</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('site.front.cancel') }}</button>
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
				<h3 class="modal-title">{{ trans('site.learner-suggest-date-coaching-time') }}</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="" id="suggestDateForm"
					  onsubmit="disableSubmit(this)">
					{{csrf_field()}}

					<div class="form-group">
						<label>{{ trans('site.learner.date-time') }}</label>
						<input type="datetime-local" class="form-control" name="suggested_date[]" required>
					</div>

					<div class="form-group">
						<label>{{ trans('site.learner.date-time') }}</label>
						<input type="datetime-local" class="form-control" name="suggested_date[]" required>
					</div>

					<div class="form-group">
						<label>{{ trans('site.learner.date-time') }}</label>
						<input type="datetime-local" class="form-control" name="suggested_date[]" required>
					</div>

					<div class="text-right mt-4">
						<button type="submit" class="btn btn-success">{{ trans('site.learner.suggest-text') }}</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('site.learner.cancel-text') }}</button>
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
				<h3 class="modal-title">{{ trans('site.learner.help-with-text') }}</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form action="" method="post" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<textarea name="help_with" id="" cols="30" rows="10" class="form-control"></textarea>

					<div class="text-right mt-4">
						<button type="submit" class="btn btn-success">{{ trans('site.front.submit') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@stop

@section('scripts')
	{{-- <script id="et-iframe" data-version="0.5" data-widgetId="610cd762c02659717c0355e1" src="https://client.consolto.com/iframeApp/iframeApp.js"  ></script> --}}
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

        /* let selectedCoaching = '';
        $(".consolto-btn").click(function(){
            selectedCoaching = $(this).data('fields');
		});

        window.addEventListener('consoltoEvent', function (e) {
            if (e.detail.action === 'SEND_BOOKING_REQUEST') {
                $.ajax({
                    type:'POST',
                    url:'/account/coaching-timer/' + selectedCoaching.id + '/set-status',
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    data: { "status" : 2 },
                    success: function(data){
                        let tr = $("#coaching-time-" + selectedCoaching.id);
                        tr.find('.consolto-btn').remove();
                        tr.html('<span class="label label-info">Pending Approval</span>');
                    }
                });
			}

        }); */
	</script>
@stop

