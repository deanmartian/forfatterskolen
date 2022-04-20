@extends('frontend.layout')

@section('title')
<title>Mine Kurs &rsaquo; Forfatterskolen</title>
@stop

@section('heading') Search @stop

@section('content')
<div class="account-container">
	<div class="learner-container">
		<div class="container">
			@include('frontend.partials.learner-search-new')

			<p>
				{{ trans('site.learner.results-for') }} '<strong>{{ Request::input('search') }}</strong>'
			</p>

			@foreach($courses as $course)
				<div class="mb-4">
					<a href="{{route('learner.course')}}" class="font-20 font-barlow-regular">{{ $course->package->course->title }}</a>
					<p>{!! \Illuminate\Support\Str::limit($course->package->course->description, 120) !!}</p>
				</div>
			@endforeach

			@foreach($assignments as $assignment)
				@foreach( $assignment->package->course->assignments as $assignment_i )
					<div class="mb-4">
						<a href="{{route('learner.assignment')}}" class="font-20 font-barlow-regular">{{ $assignment_i->title }}</a>
						<p>{!! \Illuminate\Support\Str::limit($assignment_i->description, 120) !!}</p>
					</div>
				@endforeach
			@endforeach

			@foreach($webinars as $webinar)
				@foreach( $webinar->package->course->webinars as $webinar_i )
					<div class="mb-4">
						<a href="{{route('learner.webinar')}}" class="font-20 font-barlow-regular">{{ $webinar_i->title }}</a>
						<p>{!! \Illuminate\Support\Str::limit($webinar_i->description, 120) !!}</p>
					</div>
				@endforeach
			@endforeach


			@foreach($workshops as $workshop)
				<div class="mb-4">
					<a href="{{route('learner.workshop')}}" class="font-20 font-barlow-regular">{{ $workshop->workshop->title }}</a>
					<p>{!! \Illuminate\Support\Str::limit($workshop->workshop->description, 120) !!}</p>
				</div>
			@endforeach
		</div>
	</div>
</div>

@stop

