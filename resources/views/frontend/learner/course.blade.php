{{-- @extends('frontend.layout') --}}
@extends('frontend.layouts.course-portal')

@section('title')
<title>Mine Kurs &rsaquo; Forfatterskolen</title>
@stop

@section('heading') {{ trans('site.learner.my-course') }} @stop


@section('content')

	<div class="learner-container learner-course-wrapper">
		<div class="container">
			<div class="row">
				<div class="col-md-8">
					<div class="global-card">
						<div class="card-header">
							<h2>
								{{ trans('site.learner.my-course') }}
							</h2>
						</div>
						<div class="card-body">
							
							@foreach ($coursesTaken as $courseTaken)
								@php
									$status = '';
									$statusText = '';
									if( $courseTaken->is_active ) {
										if($courseTaken->hasStarted) {
											if($courseTaken->hasEnded) {
												$status = 'ended';
												$statusText = trans('site.learner.renew-subscription');
											} else {
												$status = 'active';
												$statusText = 'fortsette kurset';
											}
										} else {
											$status = 'start';
											$statusText = 'Start kurset';
										}
									} else {
										$status = 'on-hold';
										$statusText = trans('site.learner.course-on-hold');
									}
								@endphp
								<div class="course-item">
									<div class="col-md-5">
										<img data-src="https://www.forfatterskolen.no/{{$courseTaken->package->course->course_image}}" 
                                            alt="{{ $courseTaken->package->course->title }}">
									</div>
									<div class="col-md-7">
										<h3>
                                            {{$courseTaken->package->course->title}}
											<p class="custom-badge {{ $status }}">
												{{ $statusText }}
											</p>
                                        </h3>
                                        <p>
                                            {!! \Illuminate\Support\Str::limit(
                                                strip_tags($courseTaken->package->course->description), 200
                                                ) !!}
                                        </p>

										@if (!Auth::user()->isDisabled)
											@if( $courseTaken->is_active )
												@if($courseTaken->hasStarted)
													@if($courseTaken->hasEnded)
														@if(!$courseTaken->is_free)
															<button class="btn light-red-outline-btn" data-toggle="modal"
																	data-target="#renewAllModal">
																{{ trans('site.learner.renew-subscription') }}
															</button>
														@endif
													@else
														@if (!$courseTaken->isDisabled)
															<a class="btn light-red-outline-btn"
															href="{{route('learner.course.show', ['id' => $courseTaken->id])}}">
																{{ trans('site.learner.continue-this-course') }}
															</a>
														@endif
													@endif
												@else
													<form method="POST" action="{{route('learner.course.take')}}">
														{{csrf_field()}}
														<input type="hidden" name="courseTakenId" value="{{$courseTaken->id}}">
														<button type="submit" class="btn light-red-outline-btn">
															{{ trans('site.learner.start-course') }}
														</button>
													</form>
												@endif
											@else
												<a class="btn light-red-outline-btn disabled">
													{{ trans('site.learner.course-on-hold') }}
												</a>
											@endif
										@endif
									</div>
								</div>
							@endforeach
							
							<div class="text-center">
                                {{ $coursesTaken->appends(request()->except('page'))->links('pagination.custom-pagination') }}
                            </div>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="former-courses-wrapper">
						<div class="global-card">
							<div class="card-header">
								<h2>
									{{ trans('site.front.former-courses') }}
								</h2>
							</div>
							<div class="card-body">
								@foreach ($formerCourses as $formerCourse)
									<div class="card">
										<div class="card-header p-0">
											<img src="https://www.forfatterskolen.no/{{ $formerCourse->package->course->course_image }}" 
											alt="">
										</div>
										<div class="card-body p-3">
											<h3>
												{{ $formerCourse->package->course->title }}
											</h3>

											<p class="text-gray mb-0">
												{!! \Illuminate\Support\Str::limit(
													strip_tags($formerCourse->package->course->description
													), 200) !!}
											</p>
										</div>
									</div>
								@endforeach
							</div>
						</div>
					</div>
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

