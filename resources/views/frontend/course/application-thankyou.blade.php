@extends('frontend.layout')

@section('title')
<title>Thank You &rsaquo; Forfatterskolen</title>
@stop

@section('content')
	<div class="thank-you-page">
		<div class="container">
			<img src="{{ asset('images-new/thankyou-banner.png') }}" class="w-100 banner" alt="thank-you-banner">
			<div class="row">
				<div class="col-sm-6 left-container">
					{{-- <img src="{{ asset('images-new/thumb-icon.png') }}" alt="" class="thumb"> --}}
					<h1>{{ trans('site.application-thankyou-title') }}</h1>
					<p>
						{{ trans('site.application-thankyou-description') }}
					</p>
					<a class="btn buy-btn" href="{{ route('learner.course') }}'">
                    <i class="fa fa-graduation-cap"></i>&nbsp;&nbsp;&nbsp;{{ trans('site.view-my-courses') }}</a>
				</div>

				<div class="col-sm-6 right-container">
					<img src="{{ asset('images-new/thankyou-hero.png') }}" class="w-100" alt="">
				</div>
			</div>
		</div>
	</div>
@stop