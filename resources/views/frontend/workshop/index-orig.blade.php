@extends('frontend.layout')

@section('title')
<title>Workshops &rsaquo; Forfatterskolen</title>
@stop

@section('content')

	@if(Auth::user())
		<div class="account-container">
			@include('frontend.partials.learner-menu')
			<div class="col-sm-12 col-md-10 sub-right-content">
				<div class="col-sm-12">
	@endif

					<div class="container">
						<div class="courses-hero workshop-hero text-center">
							<div class="row">
								<div class="col-sm-12">
									<h2><span class="highlight">WO</span>RKSHOP</h2>
								</div>
							</div>
						</div>
					</div>

					<div class="container">
						<div class="row">
							<div class="col-sm-10 col-sm-offset-1">
								<p class="text-center courses-description">
								Vil du være med oss på workshop, der vi møtes ansikt til ansikt og jobber med din personlige tekst? Det gir gass til skrivemotoren og setter deg deg i kontakt med andre skriveivrige. Vi tilbyr workshops over hele landet, alltid med dyktige kursholdere.
								</p>
							</div>
						</div>

						<div class="row courses-list">
							<div class="col-sm-12 margin-bottom">
								<h3 class="text-center margin-bottom">Workshop</h3>
								<br />
							</div>
							@foreach( $workshops as $workshop )
							@if (!$workshop->is_free)
							<div class="col-sm-12 col-md-4">
								<div class="all-course-course">
									<div class="image" style="background-image: url({{ $workshop->image }})"></div>
									<div class="details">
										<div class="course-info">
											<h4>{{ $workshop->title }}</h4>
											<p>{{ str_limit(strip_tags($workshop->description), 180)}}</p>
										</div>
									</div>
									<a class="buy_now" href="{{ route('front.workshop.show', $workshop->id) }}">Les mer</a>
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