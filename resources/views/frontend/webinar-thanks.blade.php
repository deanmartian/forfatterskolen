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
		<div class="col-sm-10 col-sm-offset-1 mt-5">
			<div class="panel">
				<div class="panel-body">
					<h1 class="text-center font-weight-bold" style="color:#E83945">TAKK FOR AT DU MELDTE DEG PÅ!</h1>
					<section class="clearfix margin-top-50">
						<p>
							Dette er en bekreftelse på din påmelding. Du vil motta en mail fra oss med lenken du skal bruke til webinaret i løpet av kort tid.
						</p>

						<p class="margin-top">
							Sven Inge <br>
							Support​
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