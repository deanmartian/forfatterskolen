@extends('frontend.layout')

@section('title')
<title>{{$course->title}} &rsaquo; Forfatterskolen</title>
@stop

@section('content')
<div class="container course-details-container">
    <?php
    $today 	= \Carbon\Carbon::today()->format('Y-m-d');
    $from 	= \Carbon\Carbon::parse($course->packages[0]->full_payment_sale_price_from)->format('Y-m-d');
    $to 	= \Carbon\Carbon::parse($course->packages[0]->full_payment_sale_price_to)->format('Y-m-d');
    $isBetween = (($today >= $from) && ($today <= $to)) ? 1 : 0;
    ?>

	<div class="row">
		<div class="col-sm-12 col-md-6">
			<div class="course-image" style="background-image: url({{$course->course_image}})"></div>

			@if ($course->is_free)
				<form action="{{ route('front.course.getFreeCourse', $course->id) }}" method="POST"
				onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					@if (Auth::guest())
						<div class="form-group mb-3">
							<input type="text" class="form-control" placeholder="Fornavn" name="first_name"
								   value="{{ old('first_name') }}" required>
						</div>

						<div class="form-group mb-3">
							<input type="text" class="form-control" placeholder="Etternavn" name="last_name"
								   value="{{ old('last_name') }}" required>
						</div>

						<div class="form-group mb-3">
							<input type="email" class="form-control" placeholder="Epost" name="email"
								   value="{{ old('email') }}" required>
						</div>
						<button class="btn btn-theme btn-block" type="submit">Get Free Course</button>
					@else
						<?php
                        	$course_packages = $course->packages->pluck('id')->toArray();
                        	$courseTaken = App\CoursesTaken::where('user_id', Auth::user()->id)->whereIn('package_id', $course_packages)->first();
						?>
						@if (!$courseTaken)
							<button class="btn btn-theme btn-block" type="submit">Get Free Course</button>
						@endif
					@endif
				</form>

				@if ( $errors->any() )
					<div class="alert alert-danger margin-top">
						<ul>
							@foreach($errors->all() as $error)
								<li>{{$error}}</li>
							@endforeach
						</ul>
					</div>
				@endif
			@endif

		</div>
		<div class="col-sm-12 col-md-6">
			<h2>{{$course->title}}</h2>

			@if (!$course->is_free)
				<div class="course-price">Fra {{FrontendHelpers::currencyFormat($isBetween && $course->packages[0]->full_payment_sale_price
				? $course->packages[0]->full_payment_sale_price
				: $course->packages[0]->full_payment_price)}} kroner</div>
				<br />
			@endif
			@if(Auth::guest())
				@if ($course->for_sale && !$course->is_free)
					<a class="btn btn-theme btn-lg" href="{{route('front.course.checkout', ['id' => $course->id])}}">Bestill Kurset</a>
				@endif
			@else
                <?php
                $course_packages = $course->packages->pluck('id')->toArray();
                $courseTaken = App\CoursesTaken::where('user_id', Auth::user()->id)->whereIn('package_id', $course_packages)->first();
                ?>
				@if($courseTaken)
					<a href="{{route('learner.course.show', ['id' => $courseTaken->id])}}" class="btn btn-theme btn-lg">Fortsett Kurset</a>
				@else
					@if ($course->for_sale && !$course->is_free)
						<a class="btn btn-theme btn-lg" href="{{route('front.course.checkout', ['id' => $course->id])}}">Bestill Kurset</a>
					@endif
				@endif
			@endif

			<br />
			<br />
			<p>
			{!! nl2br($course->description) !!}
			</p>

			@if (!$course->is_free)
				<div class="course-price">Fra {{FrontendHelpers::currencyFormat($isBetween && $course->packages[0]->full_payment_sale_price
				? $course->packages[0]->full_payment_sale_price
				: $course->packages[0]->full_payment_price)}} kroner</div>
				<br />
			@endif
			@if(Auth::guest())
				@if ($course->for_sale && !$course->is_free)
					<a class="btn btn-theme btn-lg" href="{{route('front.course.checkout', ['id' => $course->id])}}">Bestill Kurset</a>
				@endif
			@else
				<?php 
				$course_packages = $course->packages->pluck('id')->toArray(); 
				$courseTaken = App\CoursesTaken::where('user_id', Auth::user()->id)->whereIn('package_id', $course_packages)->first();
				?>
				@if($courseTaken)
				<a href="{{route('learner.course.show', ['id' => $courseTaken->id])}}" class="btn btn-theme btn-lg">Fortsett Kurset</a>
				@else
					@if ($course->for_sale && !$course->is_free)
						<a class="btn btn-theme btn-lg" href="{{route('front.course.checkout', ['id' => $course->id])}}">Bestill Kurset</a>
					@endif
				@endif
			@endif


		</div>
	</div>

	<br /><br /><br />
	
	<!-- Packages -->
	<div class="row">
		<div class="col-sm-12">
			<div class="theme-tabs">
				<ul class="nav nav-tabs">
					@if (!$course->is_free)
				  		<li class="active"><a data-toggle="tab" href="#packages"><span>Skrivepakke detaljer</span></a></li>
					@endif
				  <li {{ $course->is_free ? 'class=active' : '' }}>
					  <a data-toggle="tab" href="#kursplan">
						  <span>{{ $course->id == 17 ? 'Planlagte webinarer' : 'Kursplan' }}</span> <!-- check if webinar-pakke -->
					  </a>
				  </li>
					@if($course->testimonials->count())
					<li><a data-toggle="tab" href="#testimonial"><span>Tilbakemelding fra elever</span></a></li>
					@endif
				</ul>

				<div class="tab-content course-tabs">
					@if (!$course->is_free)
				  		<div id="packages" class="tab-pane fade in active package">
				  
					@foreach($course->packages as $package)
						<?php
                          $from 		= \Carbon\Carbon::parse($package->full_payment_sale_price_from)->format('Y-m-d');
                          $to 			= \Carbon\Carbon::parse($package->full_payment_sale_price_to)->format('Y-m-d');
                          $isBetween 	= (($today >= $from) && ($today <= $to)) ? 1 : 0;
						  ?>

					  @if ($isBetween && $package->full_payment_sale_price)
							<h4><i class="fa fa-cube package-icon"></i>{{$package->variation}} -
								<span class="line-through margin-right-5">
									{{FrontendHelpers::currencyFormat($package->full_payment_price)}}
								</span>
								<span class="font-red">
									Salg {{FrontendHelpers::currencyFormat($package->full_payment_sale_price)}}
								</span>
							</h4>
					  @else
							<h4><i class="fa fa-cube package-icon"></i>{{$package->variation}} -
								{{FrontendHelpers::currencyFormat($package->full_payment_price)}}</h4>
					  @endif
				  	<div class="package-details">
						<p>{!! nl2br($package->description) !!}</p>
						@if( $package->shop_manuscripts->count() > 0 || 
							$package->included_courses->count() > 0 ||
							$package->workshops > 0 || $package->has_coaching
							)
							<strong>Inkluderer</strong><br />
							@if( $package->shop_manuscripts->count() > 0 )
							@foreach( $package->shop_manuscripts as $shop_manuscripts )
							{{ $shop_manuscripts->shop_manuscript->title }} <br />
							@endforeach
							@endif

							@if( $package->workshops )
							{{ $package->workshops }} workshops <br />
							@endif

							@if( $package->included_courses->count() > 0 )
							@foreach( $package->included_courses as $included_course )
							{{ $included_course->included_package->course->title }} ({{ $included_course->included_package->variation }}) <br />
							@endforeach
							@endif

							@if ($package->has_coaching)
								{{ \App\Http\FrontendHelpers::getCoachingTimerPlanType($package->has_coaching) }} coaching session
							@endif
						@endif
				  	</div>
				  	@endforeach

				  </div>
					@endif
				  <div id="kursplan" class="tab-pane fade @if($course->is_free)in active @endif">
					  @if ($course->id == 17)
						  <?php
                          	$webinars = $course->webinars()->where('set_as_replay',0)->get();
						  ?>
					  		@foreach($webinars->chunk(4) as $webinars)
								<div class="row">
								  @foreach($webinars as $webinar)
									  <div class="col-sm-3">
										  <div class="all-course-course">
											  <div class="image" style="background-image: url({{ $webinar->image ?: asset('/images/no_image.png')}})"></div>
											  <div class="details">
												  <div class="course-info">
													  <h4>{{ $webinar->title }}</h4>
													  <p>{{ str_limit(strip_tags($webinar->description), 180)}}</p>
												  </div>
											  </div>
										  </div>
									  </div>
								  @endforeach
								</div>
							@endforeach
					  @else
						  @if ($course->lesson_kursplan()->get()->count())
							  {!! $course->lesson_kursplan()->get()[0]->content !!}
						  @else
							  {!! nl2br($course->course_plan) !!}
						  @endif
					  @endif
				  </div>

					@if($course->testimonials->count())
						<div id="testimonial" class="tab-pane fade course-testimonials text-center">
							@foreach($course->testimonials->chunk(3) as $testimonial_chunk)
								<div class="row">
									@foreach($testimonial_chunk as $testimonial)
										<div class="col-lg-4 col-md-12 mb-4">
											<!--card-->
											<div class="card testimonial-card">
												@if($testimonial->is_video)
													<video height="194" controls>
														<source src="{{ URL::asset($testimonial['user_image']) }}">
													</video>
												@else
													<div class="card-head"></div>
													<div class="avatar">
														<img src="{{$testimonial['user_image'] ? asset($testimonial['user_image']) : asset('images/user.png')}}">
													</div>
												@endif
												<div class="card-body">
													<h4>
														<strong>{{ $testimonial['name'] }}</strong>
													</h4>
													<hr>
													<p class="dark-grey-text">{{ $testimonial['testimony'] }}</p>
												</div>
											</div>
											<!--card-->
										</div>
									@endforeach
								</div>
							@endforeach
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>


	<div class="row similar-courses">
		<div class="col-sm-12 text-center margin-bottom all-caps"><h3><span class="highlight">Se</span> tilsvarende kurs</h3></div>
		@foreach( $course->similar_courses as $similar_course )
		<div class="col-sm-12 col-md-4">
            <div class="all-course-course">
                <div class="image" style="background-image: url({{ $similar_course->similar_course->course_image }})"></div>
                <div class="details">
                	<div class="course-info">
	                    <h4>{{ $similar_course->similar_course->title }}</h4>
						<p>{{ str_limit(strip_tags($similar_course->similar_course->description), 180)}}</p>
					</div>
                </div>
                <a class="buy_now" href="{{ route('front.course.show', $similar_course->similar_course->id) }}">Les mer</a>
            </div>
		</div>
		@endforeach
	</div>
</div>

	<?php
    	$url = Request::input('show_kursplan');
    	$showKursplan = 0;
		if ($url) {
		    $showKursplan = 1;
		}
	?>
@stop

@section('scripts')
	<script>
		let showKursplan = parseInt('{{ $showKursplan }}');
		if (showKursplan === 1) {
            $('[href="#kursplan"]').trigger('click');
            $('html, body').animate({
                scrollTop: $("#kursplan").offset().top
            }, 1000);
		}
	</script>
@stop
