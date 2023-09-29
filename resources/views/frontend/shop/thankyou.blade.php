@extends('frontend.layout')

@section('title')
<title>Thank You &rsaquo; Forfatterskolen</title>
@stop

@section('content')
{{-- data-bg="https://www.forfatterskolen.no/images-new/thankyou-bg.png" --}}
	<div class="thank-you-page">
		<div class="container">
			<img src="{{ asset('images-new/thankyou-banner.png') }}" class="w-100 banner" alt="thank-you-banner">
			<div class="row">

				<?php
					switch (Request::input('page')) {
						case 'paypal':
							$header = trans('site.thankyou-page.paypal.title');
							$message = trans('site.thankyou-page.paypal.description');
							$button = '<a class="btn buy-btn" href="'.route('learner.invoice').'">
	<i class="fa fa-list-alt"></i>&nbsp;&nbsp;&nbsp;Se på mine fakturaer</a>';
							break;
						case 'vipps':
							$header = trans('site.thankyou-page.vipps.title');
							$message = trans('site.thankyou-page.vipps.description');
							$button = '<a class="btn buy-btn" href="'.route('learner.invoice').'">
	<i class="fa fa-list-alt"></i>&nbsp;&nbsp;&nbsp;Se på mine fakturaer</a>';
							break;
						case 'manuscript':
							$header = trans('site.thankyou-page.manuscript.title');
							$message = trans('site.thankyou-page.manuscript.description');
							$button = '<a class="btn buy-btn" href="'.route('learner.shop-manuscript').'">
	<i class="fa fa-file"></i>&nbsp;&nbsp;&nbsp;Se på mine manuskripter</a>';
							break;
						case 'workshop':
							$header = trans('site.thankyou-page.workshop.title');
							$message = trans('site.thankyou-page.workshop.description');
							$button = '<a class="btn buy-btn" href="'.route('learner.workshop').'">
	<i class="fa fa-briefcase"></i>&nbsp;&nbsp;&nbsp;Se skriveverksted</a>';
							break;
						default:
							$header = trans('site.thankyou-page.course.title');
							$message = trans('site.thankyou-page.course.description');
							$button = '<a class="btn buy-btn" href="'.route('learner.course').'">
	<i class="fa fa-graduation-cap"></i>&nbsp;&nbsp;&nbsp;Se på mine kurs</a>';
							break;
					}
				?>

				<div class="col-sm-6 left-container">
					{{-- <img src="{{ asset('images-new/thumb-icon.png') }}" alt="" class="thumb"> --}}
					<h1>{{ $header }}</h1>
					<p>
						{!! $message !!}
					</p>
					{!! $button !!}
				</div>

				<div class="col-sm-6 right-container">
					<img src="{{ asset('images-new/thankyou-hero.png') }}" class="w-100" alt="">
				</div>
			</div>
		</div>
	</div>
@stop