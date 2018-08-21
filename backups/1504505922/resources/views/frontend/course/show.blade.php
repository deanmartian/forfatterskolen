@extends('frontend.layout')

@section('title')
<title>{{$course->title}} &rsaquo; Forfatterskolen</title>
@stop

@section('content')
<div class="container course-details-container">
	<div class="row">
		<div class="col-sm-12 col-md-6">
			<div class="course-image" style="background-image: url({{$course->course_image}})"></div>
		</div>
		<div class="col-sm-12 col-md-6">
			<h2>{{$course->title}}</h2>
			<br />
			<p>
			{{strip_tags($course->description)}}
			</p>

			
			<div class="course-price">Fra {{FrontendHelpers::currencyFormat($course->packages[0]->full_payment_price)}} kroner</div>
			<br />
			@if(Auth::guest())
				<a class="btn btn-theme btn-lg" href="{{route('front.course.checkout', ['id' => $course->id])}}">Bestill Kurset</a>
			@else
				<?php 
				$course_packages = $course->packages->pluck('id')->toArray(); 
				$courseTaken = App\CoursesTaken::where('user_id', Auth::user()->id)->whereIn('package_id', $course_packages)->first();
				?>
				@if($courseTaken)
				<a href="{{route('learner.course.show', ['id' => $courseTaken->id])}}" class="btn btn-theme btn-lg">Fortsett Kurset</a>
				@else
				<a class="btn btn-theme btn-lg" href="{{route('front.course.checkout', ['id' => $course->id])}}">Bestill Kurset</a>
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
				  <li class="active"><a data-toggle="tab" href="#packages"><span>Skrivepakke detaljer</span></a></li>
				  <li><a data-toggle="tab" href="#kursplan"><span>Kursplan</span></a></li>
				</ul>

				<div class="tab-content course-tabs">
				  <div id="packages" class="tab-pane fade in active package">
				  
					@foreach($course->packages as $package)
				  	<h4><i class="fa fa-cube package-icon"></i>{{$package->variation}} - {{FrontendHelpers::currencyFormat($package->full_payment_price)}}</h4>
				  	<div class="package-details">
						<p>{!! nl2br($package->description) !!}</p>
						@if( $package->shop_manuscripts->count() > 0 || 
							$package->included_courses->count() > 0 ||
							$package->workshops > 0
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
						@endif
				  	</div>
				  	@endforeach

				  </div>
				  <div id="kursplan" class="tab-pane fade">
				  {!! nl2br($course->course_plan) !!}
				  </div>
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
@stop
