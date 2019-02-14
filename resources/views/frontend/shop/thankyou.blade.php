@extends('frontend.layout')

@section('title')
<title>Thank You &rsaquo; Forfatterskolen</title>
@stop

@section('content')
<div class="thank-you-page">
	<div class="container">
		<div class="row">

			<?php
				switch (Request::input('page')) {
					case 'paypal':
					    $header = 'Takk for betalingen!';
					    $message = 'Din betaling ble gjennomført <span class="thankyou-green">suksessfullt</span>.';
					    $button = '<a class="btn buy-btn" href="'.route('learner.invoice').'">
<i class="fa fa-list-alt"></i>&nbsp;&nbsp;&nbsp;Se på mine fakturaer</a>';
					    break;
                    case 'manuscript':
                        $header = 'Takk for din bestilling!';
                        $message = 'Vi gleder oss å lese ditt manus. Vi vil finne en passende redaktør for ditt manus og gi deg en forventet
tilbakemeldingsdato. Normalt innen 3 uker. Du har fått tilsendt faktura(er) til din registrerte e-postadresse.
Du kan også finne faktura(ene) inne på min side. Dersom du har betalt via paypal gjelder fakturaen kun som en
kvittering.';
                        $button = '<a class="btn buy-btn" href="'.route('learner.shop-manuscript').'">
<i class="fa fa-file"></i>&nbsp;&nbsp;&nbsp;Se på mine manuskripter</a>';
                        break;
                    case 'workshop':
                        $header = 'Takk for din bestilling!';
                        $message = 'Vi gleder oss til skriveverksted. Du har fått tilsendt faktura til din registrerte e-postadresse.
Du kan også finne faktura inne på min side. Dersom du har betalt via paypal gjelder fakturaen kun som en
kvittering.';
                        $button = '<a class="btn buy-btn" href="'.route('learner.workshop').'">
<i class="fa fa-briefcase"></i>&nbsp;&nbsp;&nbsp;Se på mine skriveverksted</a>';
                        break;
					default:
					    $header = 'Takk for bestillingen!';
					    $message = 'Vi gleder oss til å samarbeide med deg! Vi vil behandle din bestilling så snart som mulig og du
						vil få tilsendt faktura(er) til din registrerte e-postadresse. Dersom du har betalt
						via paypal gjelder fakturaen kun som en kvittering.';
					    $button = '<a class="btn buy-btn" href="'.route('learner.course').'">
<i class="fa fa-graduation-cap"></i>&nbsp;&nbsp;&nbsp;Se på mine kurs</a>';
					    break;
				}
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