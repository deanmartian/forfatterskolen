@extends('frontend.layout')

@section('title')
<title>Forfatterskolen &rsaquo; Free Manuscripts</title>
@stop

@section('content')
<div class="container text-center">
	<div class="row">
		<div class="col-sm-12">
			<h1 style="margin-top: 120px;" class="font-weight-bold"> 
				{{ trans('site.front.free-manuscript-success.title') }}
			</h1>
			{!! str_replace(['_start_redirect_', '_end_redirect_', '_start_span_', '_end_span_'],
                        ['<small class="redirect" style="display: inline-block; margin-bottom: 150px"><em>',
                        '</em></small>', '<span>', '</span>'],
                        trans('site.front.free-manuscript-success.note')) !!}
		</div>
	</div>
</div>


@stop

@section('scripts')
<script>
	let time = 5;
	window.setInterval(
	  function() 
	  {
	  	time--;
	  	console.log(time);
	  	if(time === 0){
	  		window.location.href = '{{ url('') }}';
	  	}
	  	jQuery('.redirect span').text(time);
	  }, 
	1000);
</script>
@stop