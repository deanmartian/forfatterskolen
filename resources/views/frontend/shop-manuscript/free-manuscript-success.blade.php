@extends('frontend.layout')

@section('title')
<title>Forfatterskolen &rsaquo; Free Manuscripts</title>
@stop

@section('content')
<div class="container text-center">
	<div class="row">
		<div class="col-sm-12">
			<h2 style="margin-top: 120px">Takk for innsendt tekst. Du vil få svar innen fem virkedager.</h2>
			<small class="redirect" style="display: inline-block; margin-bottom: 150px"><em>(Du blir sendt til forsiden om
					<span>5</span> sekunder)</em></small>
		</div>
	</div>
</div>


@stop

@section('scripts')
<script>
	var time = 5;
	window.setInterval(
	  function() 
	  {
	  	time--;
	  	console.log(time);
	  	if(time == 0){
	  		window.location.href = '{{ url('') }}';
	  	}
	  	jQuery('.redirect span').text(time);
	  }, 
	1000);
</script>
@stop