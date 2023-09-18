@extends('frontend.layout')

@section('title')
<title>Courses &rsaquo; Forfatterskolen</title>
@stop

@section('content')
	<div class="course-page">
		<div class="header" data-bg="https://www.forfatterskolen.no/images-new/course-header-new.png">
			<div class="container text-center position-relative">
				<h1>
					{{ trans('site.front.our-course.title') }}
				</h1>
			</div>

			<div class="row sub-header">

				<div class="col-md-6">
					<p>
						{!! trans('site.front.our-course.details') !!}
					</p>
				</div>

				<div class="col-md-6">
					<p>
						{{ trans('site.front.our-course.second-description') }}
					</p>
				</div>

				<p class="highlight">
					{{ trans('site.front.our-course.highlight') }}
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
									<div class="course-header"
									data-bg="https://www.forfatterskolen.no/{{$course->course_image}}">
										<div class="header-content">
											@if ($course->instructor)
												<div class="left-container">
													<small>{{ trans('site.front.our-course.course-holder') }}</small>
													<h2><i class="img-icon"></i>{{ $course->instructor }}</h2>
												</div>
											@endif

											@if ($course->start_date)
												<div class="right-container">
													<small>{{ trans('site.front.our-course.date') }}</small>
													<h2><i class="img-icon"></i>{{ \App\Http\FrontendHelpers::formatDate($course->start_date) }}</h2>
												</div>
											@endif
										</div>

										<a href="{{ route($showRoute, $course->id) }}" class="btn btn-details">
											{{ trans('site.front.our-course.view-details') }}
										</a>
									</div>
									<div class="course-body">
										<h2>
											{{ $course->title }}
										</h2>

										<p class="color-b4">{!! \Illuminate\Support\Str::limit(strip_tags($course->description), 180) !!}</p>

										<a href="{{ route($showRoute, $course->id) }}" class="btn buy-btn">
											{{ trans('site.front.view') }}
										</a>
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