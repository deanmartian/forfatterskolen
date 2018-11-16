@extends('frontend.layout')

@section('title')
<title>Thank You &rsaquo; Forfatterskolen</title>
@stop

@section('content')
<div class="thank-you-page">
	<div class="container">
		<div class="row">
			<div class="col-sm-6 left-container">
				<img src="{{ asset('images-new/thumb-icon.png') }}" alt="" class="thumb">
				<h1>Takk for bestillingen!</h1>
				<p>
					@if( Request::input('gateway') )
						Din betaling ble gjennomført <span class="thankyou-green">suksessfullt</span>.
					@else
						Vi gleder oss til å samarbeide med deg! Vi vil behandle din bestilling så snart som mulig og du
						vil få tilsendt faktura(er) til din registrerte e-postadresse. Dersom du har betalt
						via paypal gjelder fakturaen kun som en kvittering.
					@endif
				</p>
				<a class="btn buy-btn" href="{{route('learner.course')}}"><i class="fa fa-graduation-cap"></i>&nbsp;&nbsp;&nbsp;Se på mine kurs</a>
			</div>

			<div class="col-sm-6 right-container">
				<img src="{{ asset('images-new/thankyou-hero.jpg') }}" alt="">
			</div>
		</div>
	</div>
</div>
@stop