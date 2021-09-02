@extends('frontend.layout')

@section('title')
<title>Thank You &rsaquo; Forfatterskolen</title>
@stop

@section('content')
	<div class="thank-you-page" data-bg="https://www.forfatterskolen.no/images-new/thankyou-bg.png">
		<div class="container">
			<div class="row">

				<?php
                $header = trans('site.thankyou-page.course.title');
                $message = trans('site.thankyou-page.course.description');
                $button = '<a class="btn buy-btn" href="'.route('learner.invoice') . '?tab=gift'. '">
	<i class="fa fa-graduation-cap"></i>&nbsp;&nbsp;&nbsp;See gift</a>';
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
					<img src="{{ asset('images-new/thankyou-hero.jpg') }}" alt="">
				</div>
			</div>
		</div>
	</div>
@stop