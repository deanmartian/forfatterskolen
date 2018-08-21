@extends('frontend.layout')

@section('title')
<title>Mine Webinar &rsaquo; Forfatterskolen</title>
@stop


@section('content')
<div class="account-container">
	
	@include('frontend.partials.learner-menu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12">
			<h3 class="no-margin-top">Mine Webinar</h3>
		
			<div class="row"> 
			@foreach( Auth::user()->coursesTaken as $courseTaken )
				@foreach($courseTaken->package->course->webinars as $webinar)
				<div class="col-sm-12 col-md-4">
					<div class="webinar-thumb">
						<i class="fa fa-play-circle-o"></i>
						<div style="background-image: url({{ $webinar->image }})"></div>
					</div>
					<div class="dashboard-courses" style="padding-top: 40px">
						<div class="course-meta">
							<div style="margin-bottom: 3px;"><strong style="font-size: 16px;">{{ $webinar->title }}</strong></div>
							<div style="margin-bottom: 3px;">Kurs: <a href="{{ route('learner.course.show', ['id' => $courseTaken->id]) }}">{{ $webinar->course->title }}</a></div>
							<div style="margin-bottom: 7px;"><i class="fa fa-calendar"></i> {{ date_format(date_create($webinar->start_date), 'M d, Y H.i') }}</div>	
							<p class="margin-bottom">
							{{ $webinar->description }}
							</p>

							<div class="text-right margin-top"> 
								@if( FrontendHelpers::isWebinarAvailable($webinar) )
								<a class="btn btn-success" href="{{ $webinar->link }}" target="_blank">Bli med på webinar</a>
								@else
								<a class="btn btn-warning" href="{{ $webinar->link }}" target="_blank">Registrer deg</a>
								@endif
							</div>
						</div>
					</div>
				</div>
				@endforeach
			@endforeach
			</div>

		</div>
	</div>
	<div class="clearfix"></div>
</div>

@stop

