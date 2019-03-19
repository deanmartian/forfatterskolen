@extends('frontend.layout')

@section('title')
<title>Courses &rsaquo; Forfatterskolen</title>
@stop

@section('content')
	<div class="course-page">
		<div class="header">
			<div class="container text-center">
				<h1>
					Våre Kurs
				</h1>

				<p>
					{!! trans('site.front.course.main.description') !!}
				</p>
			</div>

			<div class="row sub-header">
				<p>
					{{ trans('site.front.course.second-description') }}
				</p>

				<p class="highlight">
					Vi er der for deg – hele veien!
				</p>
			</div> <!-- end sub-header -->
		</div> <!-- end header -->

		<div class="container courses-list-container">
			@foreach($courses->chunk(3) as $courses_chunk)
				<div class="row">
					@foreach($courses_chunk as $course)
						{{--@if( \App\Http\FrontendHelpers::isCourseAvailable($course) || $course->is_free) original have this--}}
							<div class="col-sm-4">
								<div class="course">
									<div class="course-header" style="background-image: url({{$course->course_image}})">
										<div class="header-content">
											@if ($course->instructor)
												<div class="left-container">
													<small>Kursholder</small>
													<h2><i class="img-icon"></i>{{ $course->instructor }}</h2>
												</div>
											@endif

											@if ($course->start_date)
												<div class="right-container">
													<small>Date</small>
													<h2><i class="img-icon"></i>{{ \App\Http\FrontendHelpers::formatDate($course->start_date) }}</h2>
												</div>
											@endif
										</div>

										<a href="{{ route('front.course.show', $course->id) }}" class="btn btn-details">Detaljer</a>
									</div>
									<div class="course-body">
										<h2>
											{{ $course->title }}
										</h2>

										<p class="color-b4">{{ str_limit(strip_tags($course->description), 180)}}</p>

										<a href="{{ route('front.course.show', $course->id) }}" class="btn buy-btn">Les mer</a>
									</div>
								</div>
							</div>
						{{--@endif--}}
					@endforeach
				</div>
			@endforeach
		</div> <!-- end courses-list-container -->
	</div>
@stop