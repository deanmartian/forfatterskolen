@extends('frontend.layout')

@section('title')
<title>Dashboard &rsaquo; Forfatterskolen</title>
@stop


@section('content')
<div class="account-container">
	
	@include('frontend.partials.learner-menu')

	<div class="col-sm-12 col-md-10 sub-right-content">

		<!-- Recent Courses -->
		<div class="col-sm-12">
			<h3>Recent Courses</h3>
			<div class="row">
				@foreach(Auth::user()->coursesTaken as $courseTaken)
				<div class="col-sm-12 col-md-3">
					<div class="dashboard-courses">
						<div class="course-thumb" style="background-image: url({{$courseTaken->package->course->course_image}})"></div>
						<div class="course-meta">
							<strong>{{$courseTaken->package->course->title}}</strong>
							<p>
							{{str_limit(strip_tags($courseTaken->package->course->description), 60)}}
							</p>
							<div class="progress">
							  <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="70"
							  aria-valuemin="0" aria-valuemax="100" style="width:70%">
							    <span class="sr-onlsy">70% Complete</span>
							  </div>
							</div>
							<br />
							@if($courseTaken->hasStarted)
							<a class="btn btn-primary btn-sm pull-right" href="{{route('learner.course.show', ['id' => $courseTaken->id])}}">Continue this Course&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></a>
							@else
							<form method="POST" action="{{route('learner.course.take')}}">
								{{csrf_field()}}
								<input type="hidden" name="courseTakenId" value="{{$courseTaken->id}}">
								<button type="submit" class="btn btn-sm btn-success pull-right">Start this course&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>
							</form>
							@endif
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
				@endforeach
			</div>
		</div>
		<!-- Webinars -->
		<div class="col-sm-12">
			<h3>Webinars</h3>
			<div class="row">
				<div class="col-sm-3">
					<div class="dashboard-webinar">
						<div class="webinar-thumb"></div>
						<div class="webinar-meta">
							Lorem ipsum dwad
							<div class="webinar-date">
								<i class="fa fa-calendar"></i> &nbsp; dwaaw
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="dashboard-webinar">
						<div class="webinar-thumb"></div>
						<div class="webinar-meta">
							Lorem ipsum dwad
							<div class="webinar-date">
								<i class="fa fa-calendar"></i> &nbsp; dwaaw
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="dashboard-webinar">
						<div class="webinar-thumb"></div>
						<div class="webinar-meta">
							Lorem ipsum dwad
							<div class="webinar-date">
								<i class="fa fa-calendar"></i> &nbsp; dwaaw
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="dashboard-webinar">
						<div class="webinar-thumb"></div>
						<div class="webinar-meta">
							Lorem ipsum dwad
							<div class="webinar-date">
								<i class="fa fa-calendar"></i> &nbsp; dwaaw
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Recent Manuscripts -->
		<div class="col-sm-12">
			<h3>Recent Manuscripts</h3>
			<div class="row">
				<div class="col-sm-4">
					<div class="dashboard-manuscripts">
						<div class="manuscript-thumb"></div>
						<div class="manuscript-meta">
							Lorem ipsum dwad
							<div class="manuscript-date">
								<i class="fa fa-calendar"></i> &nbsp; dwaaw
							</div>
							<strong><i class="fa fa-comments-o"></i>&nbsp; 4 Comments</strong>
						</div>
						<a href="" class="btn btn-theme"><i class="fa fa-angle-right"></i></a>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="dashboard-manuscripts">
						<div class="manuscript-thumb"></div>
						<div class="manuscript-meta">
							Lorem ipsum dwad
							<div class="manuscript-date">
								<i class="fa fa-calendar"></i> &nbsp; dwaaw
							</div>
							<strong><i class="fa fa-comments-o"></i>&nbsp; 4 Comments</strong>
						</div>
						<a href="" class="btn btn-theme"><i class="fa fa-angle-right"></i></a>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="dashboard-manuscripts">
						<div class="manuscript-thumb"></div>
						<div class="manuscript-meta">
							Lorem ipsum dwad
							<div class="manuscript-date">
								<i class="fa fa-calendar"></i> &nbsp; dwaaw
							</div>
							<strong><i class="fa fa-comments-o"></i>&nbsp; 4 Comments</strong>
						</div>
						<a href="" class="btn btn-theme"><i class="fa fa-angle-right"></i></a>
					</div>
				</div>
			</div>
		</div>

		<!-- Recent Assignments -->
		<div class="col-sm-12">
			<h3>Recent Assignments</h3>
			<div class="row">
				<div class="col-sm-6">
					<div class="dashboard-assignments">
						<div class="assignment-thumb"></div>
						<div class="assignment-meta">
							<strong>Stories about scdwa</strong>
							<p>wadawl djwkjda jknad dkawdka</p>
							<strong><i class="fa fa-comments-o"></i>&nbsp; 4 Comments</strong>
						</div>
					</div>
				</div>

				<div class="col-sm-6">
					<div class="dashboard-assignments">
						<div class="assignment-thumb"></div>
						<div class="assignment-meta">
							<strong>Stories about scdwa</strong>
							<p>wadawl djwkjda jknad dkawdka</p>
							<strong><i class="fa fa-comments-o"></i>&nbsp; 4 Comments</strong>
						</div>
					</div>
				</div>

				<div class="col-sm-6">
					<div class="dashboard-assignments">
						<div class="assignment-thumb"></div>
						<div class="assignment-meta">
							<strong>Stories about scdwa</strong>
							<p>wadawl djwkjda jknad dkawdka</p>
							<strong><i class="fa fa-comments-o"></i>&nbsp; 4 Comments</strong>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
	<div class="clearfix"></div>
</div>

@stop
