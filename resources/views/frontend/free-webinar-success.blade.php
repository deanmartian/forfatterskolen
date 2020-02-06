@extends('frontend.layout')

@section('title')
	<title>Thank You for Subscribing &rsaquo; Forfatterskolen</title>
@stop

@section('content')
	<div class="free-webinar-thanks-page">
		<div class="header">
			<div class="container">
				<div class="row text-center">
					<h1 class="w-100 font-montserrat-semibold">{{ trans('site.free-webinars.thanks.main-title') }}</h1>
				</div>
			</div>
		</div>

		<div class="body">
			<div class="container">
				<div class="row first-row">
					<div class="first-container text-center">
						<h2 class="font-montserrat-regular">
							{!! str_replace('[webinar_datetime]','<span class="font-montserrat-regular theme-text">
								'.\App\Http\FrontendHelpers::formatDateTimeNor($freeWebinar->start_date).
							'</span>', trans('site.free-webinars.thanks.main-description')) !!}
						</h2>
					</div>
				</div>

				<div class="row second-row">
					<div class="col-sm-6 left-container text-center">
						<img src="{{ asset('images-new/free-webinar-thanks-left.png') }}" class="first-img">

						<div class="presenter">
							<div class="image" style="background-image: url({{ asset('images-new/free-webinar-thanks-presenter.png') }})">
							</div>

							<div class="name">
								<h1 class="font-montserrat-semibold mb-0">Kristine S.Henningsen</h1>
								<h2 class="font-montserrat-regular mb-0">Rektor på Forfatterskolen​</h2>
							</div>
						</div>
					</div>
					<div class="col-sm-6 details-container">
						<h1 class="font-montserrat-regular">{{ trans('site.free-webinars.thanks.description.title') }}</h1>

						{!! trans('site.free-webinars.thanks.description.text') !!}

						@if($freeWebinar->id == 16)
							<a href="https://www.bigmarker.com/system_check" target="_blank"
							   class="site-btn-global font-montserrat-regular rounded-0">
								{{ trans('site.free-webinars.thanks.description.button-text') }}</a>
						@else
							<a href="https://www.bigmarker.com/system_check" target="_blank"
							   class="site-btn-global font-montserrat-regular rounded-0">
								{{ trans('site.free-webinars.thanks.description.button-text') }}</a>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
@stop

@section('scripts')
@stop