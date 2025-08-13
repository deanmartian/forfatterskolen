@extends('frontend.layout')

@section('title')
<title>{{$course->title}} &rsaquo; Forfatterskolen</title>
@stop

@section('metas')
	<meta property="og:title" content="{{ $course->meta_title }}">
	<meta property="og:description" content="{{ $course->meta_description }}">
	<meta name="description" content="{{ $course->meta_description }}">
	<meta property="og:site_name" content="Forfatterskolen">
	<meta property="og:url" content="{{ url()->current() }}">
	<meta property="og:type" content="website" />
	@if ($course->meta_image)
		<meta property="og:image" content="{{ url($course->meta_image) }}">
		<meta property="twitter:image" content="{{ url($course->meta_image) }}">
	@endif

	<meta property="twitter:title" content="{{ $course->meta_title }}">
	<meta property="twitter:description" content="{{ $course->meta_description }}">
	<meta name="twitter:card" content="summary" />
	<meta name="twitter:title" content="{{ $course->meta_title }}" />
	<meta name="twitter:description" content="{{ $course->meta_description }}" />
	<meta property="fb:app_id" content="300010277156315" />

	<title>
		{{ $course->meta_title }}
	</title>
@stop

@section('styles')
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">
	<style>
		.course-single-page .course-tabs ul li:before {
			content: '';
		}
	</style>
@stop

@section('content')

    <?php
		$today 	= \Carbon\Carbon::today()->format('Y-m-d');
		$from 	= \Carbon\Carbon::parse($course->packagesIsShow[0]->full_payment_sale_price_from)->format('Y-m-d');
		$to 	= \Carbon\Carbon::parse($course->packagesIsShow[0]->full_payment_sale_price_to)->format('Y-m-d');
		$isBetween = (($today >= $from) && ($today <= $to)) ? 1 : 0;
		$start_date = \Carbon\Carbon::parse($course->start_date);
    ?>

	<div class="course-single-page">
		<div class="header" data-bg="https://www.forfatterskolen.no/images-new/course-single-bg.png">
			<div class="container">
				<div class="row">
					<div class="col-md-6 details">
						<h1 class="position-relative" style="z-index:1;">{{$course->title}}</h1>

						<p>
							{{ trans('site.front.our-course.show.description') }}
						</p>

						@if (!$course->is_free && !$course->hide_price)
							<span class="course-price position-relative">
								<?php
									$price = \App\Http\FrontendHelpers::currencyFormat($course->packagesIsShow[0]->calculated_price);
								?>

								{{ str_replace('_price_', $price, trans('site.front.our-course.show.price')) }}
							</span>
						@endif

						@if(Auth::guest())
							@if ($course->for_sale && !$course->is_free && !$course->hide_price)
								@if ($course->pay_later_with_application)
									<a href="{{route($checkoutRoute, ['id' => $course->id])}}" class="btn buy-course">
										Søk kurset
									</a>
								@else
									<a href="{{route($checkoutRoute, ['id' => $course->id])}}" class="btn buy-course">
										{{ trans('site.front.our-course.show.buy-course') }}
									</a>
								@endif
							@endif
						@else
							<?php
							$course_packages = $course->allPackages->pluck('id')->toArray();
							$courseTaken = App\CoursesTaken::where('user_id', Auth::user()->id)->whereIn('package_id', $course_packages)->first();
							?>
							@if($courseTaken)
								<a href="{{route('learner.course.show', ['id' => $courseTaken->id])}}" class="btn buy-course">
									{{ trans('site.front.our-course.show.continue-course') }}
								</a>
							@else
								@if ($course->for_sale && !$course->is_free && !$course->hide_price)
									@if ($course->pay_later_with_application)
										<a href="{{route($checkoutRoute, ['id' => $course->id])}}" class="btn buy-course">
											Søk kurset
										</a>
									@else
										@if (Auth::user()->could_buy_course)
											<a href="{{route($checkoutRoute, ['id' => $course->id])}}" class="btn buy-course">
												{{ trans('site.front.our-course.show.buy-course') }}
											</a>
										@endif
									@endif
								@endif
							@endif
						@endif
					</div> <!-- end col-md-6 -->
					<div class="col-md-6">
						@if ($course->start_date)
							<div class="date-container">
								<h2>
									{{ $start_date->format('d') }}
								</h2>
								<h3>
									{{ ucwords(\App\Http\FrontendHelpers::convertMonthLanguage($start_date->format('n'))) }}
								</h3>
							</div>
						@endif
					</div>
				</div>
			</div>
		</div> <!-- end header -->

		<div class="container single-content">
			<div class="row course-image-row" data-bg="https://www.forfatterskolen.no/{{$course->course_image}}">
				@if($course->photographer)
					<div class="photographer-container">
						<div class="h1" style="margin-top: 0">{{ trans('site.front.our-course.show.photo') }}: {{ $course->photographer }}</div>
					</div>
				@endif
			</div> <!-- end course-image-row -->

			@if ($course->is_free)
				<div class="row free-course-form-row">
					<form action="{{ route('front.course.getFreeCourse', $course->id) }}" method="POST"
						  onsubmit="disableSubmit(this)" class="form-inline">
						{{ csrf_field() }}
							@if (Auth::guest())
								<div class="form-group col-md-3">
									<input type="text" class="form-control" placeholder="{{ trans('site.front.form.first-name') }}"
										   name="first_name"
										   value="{{ old('first_name') }}" required>
								</div>
								<div class="form-group col-md-3">
									<input type="text" class="form-control"
										   placeholder="{{ trans('site.front.form.last-name') }}"
										   name="last_name"
										   value="{{ old('last_name') }}" required>
								</div>
								<div class="form-group col-md-3">
									<input type="email" class="form-control"
										   placeholder="{{ trans('site.front.form.email') }}"
										   name="email"
										   value="{{ old('email') }}" required>
								</div>

								@if($course->status)
									<div class="form-group col-md-3">
										<button type="submit" class="btn btn-theme">
											{{ trans('site.front.form.get-free-course') }}
										</button>
									</div>
								@endif
							@else
								<?php
								$course_packages = $course->packages->pluck('id')->toArray();
								$courseTaken = App\CoursesTaken::where('user_id', Auth::user()->id)
									->whereIn('package_id', $course_packages)->first();
								?>
								@if (!$courseTaken && $course->status == 1)
									<button class="btn btn-theme" type="submit">
										{{ trans('site.front.form.get-free-course') }}
									</button>
								@endif
							@endif
					</form>

					@if (Session::has('email_exist'))
						<div class="modal fade" role="dialog" id="emailExistModal">
							<div class="modal-dialog" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h3 class="modal-title">
											{{ trans('site.front.our-course.email-exist.login') }}
										</h3>
										<button type="button" class="close" data-dismiss="modal">&times;</button>
									</div>
									<div class="modal-body">
										<p class="font-weight-bold">
											{{ trans('site.front.our-course.email-exist.message') }}
										</p>

										<form id="checkoutLogin" action="{{route('frontend.login.checkout.store')}}" method="POST">
											{{csrf_field()}}

											<div class="input-group mb-4">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fa at-icon"></i></span>
												</div>
												<input type="email" name="email" class="form-control no-border-left w-auto"
													   placeholder="{{ trans('site.front.form.email') }}" required
													   value="{{old('email')}}">
											</div>
											<div class="input-group mb-4">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fa lock-icon"></i></span>
												</div>
												<input type="password" name="password"
													   placeholder="{{ trans('site.front.form.password') }}"
													   class="form-control no-border-left w-auto" required>
											</div>

											<button type="submit" class="btn site-btn-global pull-right">
												{{ trans('site.front.our-course.email-exist.login-button-text') }}
											</button>
										</form>
									</div>
								</div>
							</div>
						</div>
					@endif

					@if ( $errors->any() )
						<div class="alert alert-danger margin-top">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
							<ul>
								@foreach($errors->all() as $error)
									<li>{{$error}}</li>
								@endforeach
							</ul>
						</div>
					@endif
				</div>
			@endif

			<div class="row details-container">
				<div class="theme-tabs">
					<ul class="nav nav-tabs" role="tablist">
						<li class="nav-item">
							<a data-toggle="tab" href="#overview" class="nav-link active" role="tab">
								<span>
									{{ trans('site.front.our-course.show.course-plan') }}
								</span> <!-- check if webinar-pakke -->
							</a>
						</li>
						@if (!$course->is_free && !$course->hide_price)
							<li class="nav-item">
								<a data-toggle="tab" href="#packages" class="nav-link" role="tab">
									<span>{{ trans('site.front.our-course.show.package-details-text') }}</span>
								</a>
							</li>
						@endif
						<li class="nav-item">
							<a data-toggle="tab" href="#kursplan" class="nav-link" role="tab">
								<span>{{ $course->id == 17 ? trans('site.front.our-course.show.scheduled-webinars') :
								'Kursplan' }}</span> <!-- check if webinar-pakke -->
							</a>
						</li>
						@if($course->testimonials->count())
							<li class="nav-item">
								<a data-toggle="tab" href="#testimonials" class="nav-link" role="tab">
									<span>{{ trans('site.front.our-course.show.testimonials') }}</span>
								</a>
							</li>
						@endif
					</ul>

					<div class="tab-content course-tabs pt-0">

						<div id="overview" class="tab-pane fade in active" role="tabpanel">
							<div class="row">
								<div class="col-md-8">
									<div class="left-container">
										{!! $course->description !!}
									</div>
								</div>
								<div class="col-md-4">
									<div class="right-container">
										<h2>
											{{ $course->title }}
										</h2>

										<hr>

										<img src="https://www.forfatterskolen.no/{{$course->course_image}}" alt="course-image"
										class="w-100">

										@if (!$course->is_free && !$course->hide_price)
											<?php
											$price = \App\Http\FrontendHelpers::currencyFormat($isBetween && $course->packagesIsShow[0]->full_payment_sale_price
												? $course->packagesIsShow[0]->full_payment_sale_price
												: $course->packagesIsShow[0]->calculated_price);
											?>

											@auth
												@php $canBuy = Auth::user()->could_buy_course; @endphp
											@else
												@php $canBuy = true; @endphp
											@endauth

											@if ($canBuy)
												<a href="{{ route($checkoutRoute, ['id' => $course->id]) }}" class="btn course-price w-100">
													{{ $course->pay_later_with_application ? 'Søk kurset' : str_replace('_price_', $price, trans('site.front.our-course.show.price')) }}
												</a>
											@endif
										@endif
									</div>
								</div>
							</div>
						</div> <!-- end overview -->

						@if (!$course->is_free)
							<div id="packages" class="tab-pane fade" role="tabpanel">
								<div class="row">
									@foreach($course->packages()->where('is_show', 1)->where('variation', '!=', 'Editor Package')->get() as $package)
										<?php
											$from 		= \Carbon\Carbon::parse($package->full_payment_sale_price_from)->format('Y-m-d');
											$to 			= \Carbon\Carbon::parse($package->full_payment_sale_price_to)->format('Y-m-d');
											$isBetween 	= (($today >= $from) && ($today <= $to)) ? 1 : 0;
											$fromOther 		= \Carbon\Carbon::parse($package->full_payment_other_sale_price_from)->format('Y-m-d');
											$toOther 		= \Carbon\Carbon::parse($package->full_payment_other_sale_price_to)->format('Y-m-d');
											$isBetweenOther = (($today >= $fromOther) && ($today <= $toOther)) ? 1 : 0;
										?>
										<div class="col-md-4 package-details-new {{ $package->course_type === 2 ? 'active' : '' }}">
											<h3>
												{{ $package->variation }}
											</h3>

											<h2>
												@if ($isBetween && $package->full_payment_sale_price)
													<span>
														{{FrontendHelpers::currencyFormat($package->full_payment_sale_price)}}
													</span>
													<strike class="line-through margin-right-5">
														{{FrontendHelpers::currencyFormat($package->full_payment_price)}}
													</strike>
												@elseif ($isBetweenOther && $package->full_payment_other_sale_price)
													<span>
														{{FrontendHelpers::currencyFormat($package->full_payment_other_sale_price)}}
													</span>
													<strike class="line-through margin-right-5">
														{{FrontendHelpers::currencyFormat($package->full_payment_price)}}
													</strike>
												@else
													{{FrontendHelpers::currencyFormat($package->full_payment_price)}}
												@endif
											</h2>

											<a class="btn site-btn-global" href="{{ route($checkoutRoute,
												[$course->id,'package' => $package->id]) }}">
												{{ trans('site.front.buy') }}
											</a>

											<div class="description-container">
												{!! ($package->description_with_check) !!}
											</div>
										</div>
									@endforeach
								</div>

							</div> <!-- end packages -->
						@endif

						<div id="kursplan" class="tab-pane fade" role="tabpanel">
							@if ($course->id == 17)
                                <?php
                                $webinars = $course->webinars()->active()->notReplay()->get();
                                ?>
								<div class="row webinars-container">

									<?php
										$webinars_chunk = $webinars->chunk(9);
									?>

									<div id="webinars-carousel" class="carousel slide global-carousel"
										 data-ride="carousel" data-interval="false">

										<!-- Indicators -->
										<ul class="carousel-indicators">
											@for($i=0; $i<=$webinars_chunk->count() - 1;$i++)
												<li data-target="#webinars-carousel" data-slide-to="{{$i}}"
													@if($i == 0) class="active" @endif></li>
											@endfor
										</ul>

										<!-- The slideshow -->
										<div class="container carousel-inner no-padding">
											@foreach($webinars_chunk as $k => $webinars)
												<div class="carousel-item {{ $k==0 ? 'active' : '' }}">
													<div class="row">
														@foreach($webinars as $webinar)
															<div class="col-md-4 col-sm-12 mt-5">
																<div class="card card-global border-0">
																	<div class="card-header p-0 border-0 webinar-thumb">
																		<div style="background-image:url({{ $webinar->image
																			 ?: asset('/images/no_image.png')}})"></div>
																	</div>
																	<div class="card-body">
																		<div class="webinar-header">
																			<h4>
																				<i class="calendar"></i>
																				{{ str_replace(['_date_', '_time_'],
																				[\Carbon\Carbon::parse($webinar->start_date)->format('d.m.Y'),
																				\Carbon\Carbon::parse($webinar->start_date)->format('H:i')],
																				trans('site.front.our-course.show.start-date')) }}
																			</h4>
																		</div>

																		<div class="webinar-details">
																			<h2 class="h2">
																				{{ $webinar->title }}
																			</h2>
																			<p class="note-color my-4">
																				{{ \Illuminate\Support\Str::limit(strip_tags($webinar->description), 180)}}
																			</p>
																		</div>
																	</div> <!-- end card-body -->
																	<div class="card-footer border-0 p-0">
																		@if(Auth::guest())
																			@if ($course->for_sale && !$course->is_free && !$course->hide_price)
																				<a href="{{route($checkoutRoute, ['id' => $course->id])}}"
																				   class="btn site-btn-global w-100 rounded-0">
																					{{ trans('site.front.our-course.show.buy-course') }}
																				</a>
																			@endif
																		@else
                                                                            <?php
                                                                            $course_packages = $course->allPackages->pluck('id')->toArray();
                                                                            $courseTaken = App\CoursesTaken::where('user_id', Auth::user()->id)->whereIn('package_id', $course_packages)->first();
                                                                            ?>
																			@if($courseTaken)
																				<a href="{{route('learner.course.show', ['id' => $courseTaken->id])}}"
																				   class="btn site-btn-global w-100 rounded-0">
																					{{ trans('site.front.our-course.show.continue-course') }}
																				</a>
																			@else
																				@if ($course->for_sale && !$course->is_free && !$course->hide_price)
																					<a href="{{route($checkoutRoute, ['id' => $course->id])}}"
																					   class="btn site-btn-global w-100 rounded-0">
																						{{ trans('site.front.our-course.show.buy-course') }}
																					</a>
																				@endif
																			@endif
																		@endif
																	</div>
																</div> <!-- end card -->
															</div>
														@endforeach
													</div>
												</div>
											@endforeach
										</div>

										<!-- Left and right controls -->
										<a class="carousel-control-prev" href="#webinars-carousel" data-slide="prev">
											<span class="carousel-control-prev-icon"></span>
										</a>
										<a class="carousel-control-next" href="#webinars-carousel" data-slide="next">
											<span class="carousel-control-next-icon"></span>
										</a>

									</div> <!-- end testimonials-carouse -->
								</div> <!-- end testimonial-container -->
							@else
								<div class="row">
									<div class="col-md-12">
										<div class="left-container">
											@if ($course->lesson_kursplan()->get()->count())
												{!! $course->lesson_kursplan()->get()[0]->content !!}
											@else
												{!! nl2br($course->course_plan) !!}
											@endif

											@if ($course->course_plan_data)
												<button class="btn buy-btn" data-toggle="modal" data-target="#coursePlanDataModal">
													View Schedule
												</button>
											@endif
										</div>
									</div> <!-- end col-md-8 -->
								</div> <!-- end row -->
							@endif
						</div> <!-- end kursplan -->

						@if($course->testimonials->count())
							<div id="testimonials" class="tab-pane fade course-testimonials text-center" role="tabpanel">
								<div class="card-columns global-card-columns">
									@foreach($course->testimonials->chunk(3) as $testimonial_chunk)
										<div class="card-container">
											@foreach($testimonial_chunk as $testimonial)
												<div class="card testimonial-card">
													@if($testimonial->is_video)
														<video controls>
															<source src="{{ URL::asset($testimonial['user_image']) }}">
														</video>
													@else
														<div class="card-header"></div>
													@endif

													<div class="card-body">
														@if(!$testimonial->is_video)
															<div class="avatar">
																<img src="{{$testimonial['user_image'] ? asset($testimonial['user_image']) : asset('images/user.png')}}"
																	 class="rounded-circle" alt="user image">
															</div>
															<div class="divider"></div>
														@endif

														<p class="dark-grey-text">{{ $testimonial['testimony'] }}</p>
													</div>
													<div class="card-footer">
														{{ $testimonial['name'] }}
													</div>
												</div>
											@endforeach
										</div>
									@endforeach
								</div> <!-- end card-columns -->
							</div> <!-- end testimonials -->
						@endif

					</div> <!-- end course-tabs -->
				</div> <!-- end theme-tabs -->
			</div> <!-- end details-container -->
		</div> <!-- end container -->

		<div class="similar-courses">
			<div class="container">
				<div class="text-center h1 mb-0 mt-0">
					{{ trans('site.front.our-course.show.similar-course') }}
				</div>

				<div class="row similar-courses-row">
					@foreach( $course->similar_courses as $similar_course )
						<div class="col-sm-4">
							<div class="course">
								<div class="course-header" style="background-image: url({{$course->course_image}})">
									<div class="header-content">
										@if ($similar_course->similar_course->instructor)
											<div class="left-container">
												<small>
													{{ trans('site.front.our-course.course-holder') }}
												</small>
												<h2><i class="img-icon"></i>{{ $similar_course->similar_course->instructor }}</h2>
											</div>
										@endif

										@if ($similar_course->similar_course->start_date)
											<div class="right-container">
												<small>{{ trans('site.front.date') }}</small>
												<h2><i class="img-icon"></i>{{ \App\Http\FrontendHelpers::formatDate($similar_course->similar_course->start_date) }}</h2>
											</div>
										@endif
									</div>

									<a href="{{ route('front.course.show', $similar_course->similar_course->id) }}"
									   class="btn btn-details">
										{{ trans('site.front.our-course.view-details') }}
									</a>
								</div>
								<div class="course-body">
									<h2>
										{{ $similar_course->similar_course->title }}
									</h2>

									<p class="color-b4">
										{!! \Illuminate\Support\Str::limit(strip_tags($similar_course->similar_course->description), 180) !!}
									</p>

									<a href="{{ route('front.course.show', $similar_course->similar_course->id) }}"
									   class="btn buy-btn">{{ trans('site.front.view') }}</a>
								</div>
							</div>
						</div>
					@endforeach
				</div>
			</div>
		</div> <!-- end similar courses -->
	</div> <!-- end course-single-page -->

	@if ($course->course_plan_data)
		<div class="modal fade global-modal" role="dialog" id="coursePlanDataModal">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-body text-center">
						{!! $course->course_plan_data !!}
					</div>
				</div>
			</div>
		</div>
	@endif


    <?php
		$url = Request::input('show_kursplan');
		$showKursplan = 0;
		if ($url) {
			$showKursplan = 1;
		}
    ?>
@stop

@section('scripts')
	<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>

	<script>

		let containers = ['overview', 'packages', 'kursplan', 'testimonials'];
		/*$.each(containers, function(k, v){
            $("#"+v).mCustomScrollbar({
                theme: "minimal-dark",
                scrollInertia: 500
            });
		});*/

		@if (Session::has('email_exist'))
			$("#emailExistModal").modal('show');
		@endif

        let showKursplan = parseInt('{{ $showKursplan }}');
        if (showKursplan === 1) {
            $('[href="#kursplan"]').trigger('click');
            $('html, body').animate({
                scrollTop: $(".course-tabs").offset().top
            }, 1000);
		}

		const url = window.location;
        if (url.hash) {
            $('[href="'+url.hash+'"]').trigger('click');
            $('html, body').animate({
                scrollTop: $(".course-tabs").offset().top
            }, 1000);
		}
	</script>
@stop
