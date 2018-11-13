@extends('frontend.layout')

@section('title')
<title>Mine Kurs &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
	<style>
		.table-users .table {
			margin-top: 12px;
			margin-bottom: 12px;
			background-color: #fff;
			border: solid 1px #ccc;
		}

		.table thead {
			background-color: #eee;
		}

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

@section('heading') Mine Kurs @stop

@section('content')
<div class="account-container">
	
	@include('frontend.partials.learner-menu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12">
			@include('frontend.partials.learner-search')
		
			<div class="row"> 
			@foreach( Auth::user()->coursesTaken as $courseTaken )
				<div class="col-sm-12 col-md-4">
				<div class="dashboard-courses">
					<div class="course-thumb" style="background-image: url({{$courseTaken->package->course->course_image}})"></div>
					<div class="course-meta">
						<strong>{{$courseTaken->package->course->title}}</strong>
						<p class="no-margin-bottom">
						{{str_limit(strip_tags($courseTaken->package->course->description), 200)}}
						</p>
						@if( $courseTaken->package->course->start_date )
						<div>Kursstart dato: {{ $courseTaken->package->course->start_date }}</div>
						@endif
						@if( $courseTaken->package->course->end_date )
						<div>Slutt dato: {{ $courseTaken->package->course->end_date }}</div>
						@endif
						@if( $courseTaken->start_date )
						<div>Start date: {{ $courseTaken->start_date }}</div>
						@endif
						@if( $courseTaken->end_date )
						<div>Slutt dato: {{ $courseTaken->end_date }}</div>
						@endif

						<div class="text-right margin-top">
							@if( $courseTaken->is_active )
								@if($courseTaken->hasStarted)
									@if($courseTaken->hasEnded)
									<button class="btn btn-info" data-toggle="modal" data-target="#renewAllModal">Forny abonnement</button>
									@else
									<a class="btn btn-primary" href="{{route('learner.course.show', ['id' => $courseTaken->id])}}">Fortsett med dette kurset&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></a>
										{{-- check if course is webinar-pakke --}}
										<?php
                                        $package = \App\Package::find($courseTaken->package_id);
										?>
										@if ($package && $package->course_id == 17)

											<?php

												$checkDate = date('m/Y', strtotime($courseTaken->started_at));

												if ($courseTaken->end_date) {
                                                    $checkDate = date('m/Y', strtotime($courseTaken->end_date));
												}

												$now = new DateTime();
												$input = DateTime::createFromFormat('m/Y', $checkDate);
												$diff = $input->diff($now); // Returns DateInterval

												// m is months
                                            	$withinSixMonths = $diff->y === 0 && $diff->m <= 6;  // true

											?>

											@if($withinSixMonths)
											<br>
											{{--<a href="#renewAllModal" class="btn btn-info margin-top renewAllBtn"
											   data-toggle="modal"
											   data-action="{{ route('learner.course.renew-all', $courseTaken->id) }}">Renew All</a>--}}
											@endif
										@endif
									@endif
								@else
								<form method="POST" action="{{route('learner.course.take')}}">
									{{csrf_field()}}
									<input type="hidden" name="courseTakenId" value="{{$courseTaken->id}}">
									<button type="submit" class="btn btn-success">Start dette kurset&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>
								</form>
								@endif
							@else
							<a class="btn btn-warning disabled">Kurs på vent</a>
							@endif

							{{--@if ($courseTaken->sent_renew_email)
								<br>
								<a href="#renewModal" class="btn btn-success renewCourse" data-toggle="modal" style="margin-top: 10px"
								   data-fields="{{ json_encode($courseTaken) }}">Renew</a>
							@endif--}}
						</div>
					</div>
				</div>
			</div>
			@endforeach
			</div>

			{{--<div class="table-users table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th>Survey</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						@foreach($surveys as $survey)
							<tr>
								<td>{{ $survey->title }}</td>
								<td>
									<a href="{{ route('learner.survey', $survey->id) }}">
										Take Survey
									</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>--}}

		</div>
	</div>
	<div class="clearfix"></div>
</div>

<div id="renewModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Renew Course</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('learner.course.renew') }}" enctype="multipart/form-data">
					{{ csrf_field() }}

					<label for="">Betalings Metode</label>
							<select class="form-control" name="payment_mode_id" required>
								@foreach(App\PaymentMode::get() as $paymentMode)
									<option value="{{$paymentMode->id}}" data-mode="{{ $paymentMode->mode }}">{{$paymentMode->mode}}</option>
								@endforeach
							</select>
							<em><small>Merk: Vi godtar kun full betaling på PAYPAL</small></em>
						

					<input type="hidden" name="course_id">
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-primary">Renew</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="renewAllModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Forny alle kursene for ett år</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('learner.renew-all-courses') }}" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}

					<p>Vil du fornye alle kursene dine for ett år ekstra for kroner 1490,?</p>
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-primary">ja</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal">Nei</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@stop

@section('scripts')
	<script>
		$(function(){
		    $(".renewCourse").click(function(){
                var fields = $(this).data('fields');
                var modal = $('#renewModal');
                $("input[name=course_id]").val(fields.id);
            });

		    $(".renewAllBtn").click(function(){
                var form = $('#renewAllModal form');
                var action = $(this).data('action');
                form.attr('action', action)
			});
		})
	</script>
@stop

