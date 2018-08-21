@extends('frontend.layout')

@section('title')
<title>Thank You for Subscribing &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
	<style>
		.margin-top-50 {
			margin-top: 50px;
		}
	</style>
@stop

@section('content')
<div class="container">
	<div class="row">
		<div class="col-sm-10 col-sm-offset-1">
			<div class="panel">
				<div class="panel-body">
					<h1 class="text-center">Gøy at du meldte deg på!</h1>
					<section class="clearfix margin-top-50">
						<p>
							Vi bekrefter at vi har mottatt din påmeldelse til vårt webinar den
							{{ \App\Http\FrontendHelpers::formatDateTimeNor($freeWebinar->start_date) }}.
						</p>

						<p>
							Du skal nå ha fått en mail av meg, med en lenke du skal bruke når webinaret starter. Har du
							ikke fått lenken i løpet av en halvtime, sjekk spamfilteret ditt.
						</p>

						<p>
							Gleder meg til å treffe deg på webinaret!
						</p>

						<p>
							​Om du ikke har vært på webinar før, kan du sjekke tilkoblingen din her:
							<a href="https://support.logmeininc.com/gotowebinar/joincheck?c_name=email&c_prod=g2w?
							role=attendee&source=registrantReminderEmail&language=spanish" target="_blank" class="">
								Trykk her for å teste!</a>
						</p>

							<img src="{{ asset('images/kristine.png') }}"
							width="188" height="143" class="margin-top">

						<p class="margin-top">
							Kristine S.Henningsen
						</p>

						<p>
							Rektor på Forfatterskolen​
						</p>
					</section>
				</div>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')
<script>
	/*jQuery(window).on('load', function(){
		var time = 5;
		window.setInterval(
		  function() 
		  {
		  	time--;
		  	if(time == 0){
		  		window.location.href = '/';
		  	}
		  }, 
		1000);
	});*/
</script>
@stop