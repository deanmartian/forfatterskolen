@extends('frontend.layout')

@section('title')
<title>Courses &rsaquo; Forfatterskolen</title>
@stop

@section('content')
	@if(Auth::user())
		<div class="account-container">
		@include('frontend.partials.learner-menu')
			<div class="col-sm-12 col-md-10 sub-right-content">
				<div class="col-sm-12">
	@endif
					<div class="container">
			<div class="courses-hero text-center">
				<div class="row">
					<div class="col-sm-12">
						<h2><span class="highlight">VÅRE</span> KURS</h2>
						Å skrive et manus kan være en utfordring. Det mangler sjelden på ideer og lyst, <br />
						men hvordan komme i gang? Og hvordan lykkes med å skrive et godt manus – uten å gi opp på veien? Fortvil ikke, vi har skreddersydde kurs til å hjelpe deg. I tillegg får du profesjonell veiledning og en uunnværlig heiagjeng.
					</div>
				</div>
			</div>
		</div>

					<div class="container">
			<div class="row">
				<div class="col-sm-10 col-sm-offset-1">
					<p class="text-center courses-description">
					Forfatterskolens nettbaserte skrivekurs tar deg fem steg til ferdig manus – i ditt tempo. Gjennom praktiske maler, nyttige tips og inspirerende øvelser, får du kunnskapen og selvtilliten du trenger for å skrive ditt manus – med profesjonelle veiledere og en heiagjeng som følger deg gjennom hele kursperioden.<br /><br />
					Vi er der for deg – hele veien!
					</p>
				</div>
			</div>

			<div class="row courses-list">
				@foreach( $courses as $course )
					@if( \App\Http\FrontendHelpers::isCourseAvailable($course) || $course->is_free)
					<div class="col-sm-12 col-md-4">
						<div class="all-course-course">
							<div class="image" style="background-image: url({{$course->course_image}})"></div>
							<div class="details">
								<div class="course-info">
									<h4>{{ $course->title }}</h4>
									<p>{{ str_limit(strip_tags($course->description), 180)}}</p>
								</div>
							</div>
							<a class="buy_now" href="{{ route('front.course.show', $course->id) }}">Les mer</a>
						</div>
					</div>
					@endif
				@endforeach
			</div>
		</div>
	@if(Auth::user())
				</div>
			</div>

		<div class="clearfix"></div>
	</div>
	@endif

@stop