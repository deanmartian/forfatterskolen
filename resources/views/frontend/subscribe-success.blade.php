@extends('frontend.layout')

@section('page_title', 'Thank You for Subscribing &rsaquo; Forfatterskolen')
@section('robots', '<meta name="robots" content="noindex, follow">')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-sm-12 text-center">
			<div class="subscribe-success">
				<img src="{{ asset('images-new/person-paper-plane.png') }}" alt="person paper plane">
				<h1>Takk for at du skrev deg på, din skriveplan kommer til din epost</h1>
				<div class="redirect">Videresender deg til hjemmesiden om <span>5</span> sekunder.</div>
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
		  	if(time == 0){
		  		window.location.href = '/';
		  	}
		  	jQuery('.redirect span').text(time);
		  }, 
		1000);
	});
</script>
@stop