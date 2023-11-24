@extends('frontend.layout')

@section('title')
<title>Thank You &rsaquo; Forfatterskolen</title>
@stop

@section('content')
{{-- data-bg="https://www.forfatterskolen.no/images-new/thankyou-bg.png" --}}
	<div class="thank-you-page" >
		<div class="container">
			<img src="{{ asset('images-new/thankyou-banner.png') }}" class="w-100 banner" alt="thank-you-banner">
			<div class="row">

				<?php
                $header = trans('site.thankyou-page.manuscript.title');
                $message = trans('site.thankyou-page.manuscript.description');
                $button = '<a class="btn buy-btn" href="'.route('learner.shop-manuscript').'">
	<i class="fa fa-file"></i>&nbsp;&nbsp;&nbsp;Se på mine manuskripter</a>';
				?>

				<div class="col-sm-6 left-container">
					<img src="{{ asset('images-new/thumb-icon.png') }}" alt="" class="thumb">
					<h1>{{ $header }}</h1>
					<p>
						{!! $message !!}
					</p>
					{!! $button !!}
				</div>

				<div class="col-sm-6 right-container">
					<img src="{{ asset('images-new/thankyou-hero.png') }}" alt="">
				</div>
			</div>
		</div>
	</div>
@stop