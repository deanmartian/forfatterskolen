@extends('frontend.layout')

@section('title')
<title>Mine Kurs &rsaquo; Forfatterskolen</title>
@stop

@section('heading') {{ trans('site.learner.my-course') }} @stop

@section('content')

	<div class="learner-container">
		<div class="container">
			<div class="row">
				@include('frontend.partials.learner-search-new')
				<div class="row w-100 learner-courses-container adjust-left">
					@foreach( Auth::user()->coursesTaken as $courseTaken )
						<div class="col-md-12 col-lg-3 no-right-padding adjust-right-padding">
							<div class="learner-course card border-0">
								<div class="course-thumb" style="background-image: url({{$courseTaken->package->course->course_image}})"></div>
								<div class="course-details card-body">
									<h3 class="font-weight-normal font-barlow-regular">
										{{$courseTaken->package->course->title}}
									</h3>
									<p class="note-color">
										{{str_limit(strip_tags($courseTaken->package->course->description), 200)}}
									</p>
								</div>
								<div class="card-footer no-border p-0">
									@if( $courseTaken->is_active )
										@if($courseTaken->hasStarted)
											@if($courseTaken->hasEnded)
												<button class="btn btn-info w-100 rounded-0" data-toggle="modal"
														data-target="#renewAllModal">
													{{ trans('site.learner.renew-subscription') }}
												</button>
											@else
												<a class="btn site-btn-global-w-arrow w-100 rounded-0"
												   href="{{route('learner.course.show', ['id' => $courseTaken->id])}}">
													{{ trans('site.learner.continue-this-course') }}
												</a>
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
												@endif
											@endif
										@else
											<form method="POST" action="{{route('learner.course.take')}}">
												{{csrf_field()}}
												<input type="hidden" name="courseTakenId" value="{{$courseTaken->id}}">
												<button type="submit" class="btn site-btn-global-w-arrow w-100 rounded-0 btn-success">
													{{ trans('site.learner.start-course') }}
												</button>
											</form>
										@endif
									@else
										<a class="btn site-btn-global disabled w-100 rounded-0">
											{{ trans('site.learner.course-on-hold') }}
										</a>
									@endif
								</div>
							</div>
						</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>

<div id="renewModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">
					{{ trans('site.learner.renew-course-text') }}
				</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('learner.course.renew') }}" enctype="multipart/form-data">
					{{ csrf_field() }}

					<label for="">
						{{ trans('site.front.form.payment-method') }}
					</label>
							<select class="form-control" name="payment_mode_id" required>
								@foreach(App\PaymentMode::get() as $paymentMode)
									<option value="{{$paymentMode->id}}" data-mode="{{ $paymentMode->mode }}">{{$paymentMode->mode}}</option>
								@endforeach
							</select>
							<em><small>
									{{ trans('site.learner.renew-course.payment-note') }}
								</small></em>
						

					<input type="hidden" name="course_id">
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-primary">
							{{ trans('site.learner.renew-text') }}
						</button>
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
				<h3 class="modal-title">{{ trans('site.learner.renew-all.title') }}</h3>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('learner.renew-all-courses') }}" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}

					<p>{{ trans('site.learner.renew-all.description') }},?</p>
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-primary">{{ trans('site.front.yes') }}</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal">{{ trans('site.front.no') }}</button>
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
                let fields = $(this).data('fields');
                let modal = $('#renewModal');
                $("input[name=course_id]").val(fields.id);
            });

		    $(".renewAllBtn").click(function(){
                let form = $('#renewAllModal form');
                let action = $(this).data('action');
                form.attr('action', action)
			});
		})
	</script>
@stop

