@extends('frontend.layout')

@section('title')
<title>Mine Kurs &rsaquo; Forfatterskolen</title>
@stop


@section('content')
<div class="account-container">
	
	@include('frontend.partials.learner-menu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12">
			<div class="navbar-form navbar-right">
			  	<div class="form-group">
				  	<form role="search" method="get" action="">
						<div class="input-group">
						  	<input type="text" class="form-control" placeholder="Søk kurs..">
						    <span class="input-group-btn">
						    	<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
						    </span>
						</div>
					</form>
				</div>
			</div>
			<h3 class="no-margin-top">Mine Kurs</h3>
		
			<div class="row"> 
			@foreach( Auth::user()->coursesTaken as $courseTaken )
			<div class="col-sm-12 col-md-4">
				<div class="dashboard-courses">
					<div class="course-thumb" style="background-image: url({{$courseTaken->package->course->course_image}})"></div>
					<div class="course-meta">
						<strong>{{$courseTaken->package->course->title}}</strong>
						<p class="no-margin-bottom">
						{{str_limit(strip_tags($courseTaken->package->course->description), 200)}}
						</p>
						@if( $courseTaken->package->course->start_date )
						<div>Kursstart dato: {{ $courseTaken->package->course->start_date }}</div>	
						@endif
						@if( $courseTaken->package->course->end_date )
						<div>Kursstart dato: {{ $courseTaken->package->course->end_date }}</div>	
						@endif
						@if( $courseTaken->start_date )
						<div>Start date: {{ $courseTaken->start_date }}</div>	
						@endif
						@if( $courseTaken->end_date )
						<div>Slutt dato: {{ $courseTaken->end_date }}</div>	
						@endif

						<div class="text-right margin-top"> 
							@if( FrontendHelpers::isCourseAvailable($courseTaken->package->course) &&
								 FrontendHelpers::isCourseTakenAvailable($courseTaken) )
								@if( $courseTaken->is_active )
									@if($courseTaken->hasStarted)
										@if($courseTaken->hasEnded)
										<a class="btn btn-danger disabled">Endet</a>
										@else
										<a class="btn btn-primary" href="{{route('learner.course.show', ['id' => $courseTaken->id])}}">Fortsett med dette kurset&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></a>
										@endif
									@else
									<form method="POST" action="{{route('learner.course.take')}}">
										{{csrf_field()}}
										<input type="hidden" name="courseTakenId" value="{{$courseTaken->id}}">
										<button type="submit" class="btn btn-success">Start dette kurset&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>
									</form>
									@endif
								@else
								<a class="btn btn-warning disabled">Kurs på vent</a>
								@endif
							@else
							<a class="btn btn-warning disabled">Ikke tilgjengelig</a>
							@endif
						</div>
					</div>
				</div>
			</div>
			@endforeach
			</div>

		</div>
	</div>
	<div class="clearfix"></div>
</div>

@stop

