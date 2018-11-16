@extends('frontend.layout')

@section('title')
<title>Thank You &rsaquo; Forfatterskolen</title>
@stop

@section('content')
<div class="container thankyou-container">
	<div class="row">
		<div class="col-sm-6 col-sm-offset-3 text-center">
			<div class="thankyou">
				<div class="thankyou-header">
					<div><img src="{{asset('images/thankyou.png')}}"></div>
				</div>
				<div class="thankyou-content">
					<h3>Takk for bestillingen!</h3>
					@if( Request::input('gateway') )
					<p>Din betaling ble gjennomført <span class="thankyou-green">suksessfullt</span>.</p>
					@else
					<p>Vi gleder oss til å samarbeide med deg! Vi vil behandle din bestilling så snart som mulig og du
						vil få tilsendt faktura(er) til din registrerte e-postadresse. Dersom du har betalt
						via paypal gjelder fakturaen kun som en kvittering.</p>
					@endif
					<a class="btn btn-primary btn-lg" href="{{route('learner.course')}}"><i class="fa fa-graduation-cap"></i>&nbsp;&nbsp;&nbsp;Se på mine kurs</a>
				</div>
			</div>
		</div>
	</div>
</div>
@stop