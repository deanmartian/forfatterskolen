@extends('frontend.layout')

@section('title')
<title>Thank You for Subscribing &rsaquo; Forfatterskolen</title>
@stop

@section('content')
<div class="container">
	<div class="row">
		<div class="col-sm-10 col-sm-offset-1 text-center">
			<div class="subscribe-success">
				<div class="panel panel-default">
					<div class="panel-body">
						<h2>Takk for at du skrev deg på, din skriveplan kommer til din epost</h2>
						<div class="redirect"><em>Videresender deg til hjemmesiden om <span>5</span> sekunder.</em></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')
<script>
	jQuery(window).on('load', function(){
		var time = 5;
		window.setInterval(
		  function() 
		  {
		  	time--;
		  	console.log(time);
		  	if(time == 0){
		  		window.location.href = '/';
		  	}
		  	jQuery('.redirect span').text(time);
		  }, 
		1000);
	});
</script>
@stop