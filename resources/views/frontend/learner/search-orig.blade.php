@extends('frontend.layout')

@section('title')
<title>Mine Kurs &rsaquo; Forfatterskolen</title>
@stop

@section('heading') Search @stop

@section('content')
<div class="account-container">
	
	@include('frontend.partials.learner-menu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12">

			@include('frontend.partials.learner-search')
		  	Results for '<strong>{{ Request::input('search') }}</strong>'
			
			@foreach($courses as $course) 
			<div style="margin-bottom: 18px">
				<h4 class="no-margin-bottom"><a href="{{route('learner.course')}}">{{ $course->package->course->title }}</a></h4>
				<p>{!! str_limit($course->package->course->description, 120) !!}</p>
			</div>
			@endforeach
			
			@foreach($assignments as $assignment) 
				@foreach( $assignment->package->course->assignments as $assignment_i )
				<div style="margin-bottom: 18px">
					<h4 class="no-margin-bottom"><a href="{{route('learner.assignment')}}">{{ $assignment_i->title }}</a></h4>
				<p>{!! str_limit($assignment_i->description, 120) !!}</p>
				</div>
				@endforeach
			@endforeach


			@foreach($webinars as $webinar) 
				@foreach( $webinar->package->course->webinars as $webinar_i )
				<div style="margin-bottom: 18px">
					<h4 class="no-margin-bottom"><a href="{{route('learner.webinar')}}">{{ $webinar_i->title }}</a></h4>
				<p>{!! str_limit($webinar_i->description, 120) !!}</p>
				</div>
				@endforeach
			@endforeach


			@foreach($workshops as $workshop) 
			<div style="margin-bottom: 18px">
				<h4 class="no-margin-bottom"><a href="{{route('learner.workshop')}}">{{ $workshop->workshop->title }}</a></h4>
				<p>{!! str_limit($workshop->workshop->description, 120) !!}</p>
			</div>
			@endforeach

		</div>
	</div>
	<div class="clearfix"></div>
</div>

@stop

