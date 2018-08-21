@extends('frontend.layout')

@section('title')
	<title>Forfatterskolen &rsaquo; Free Manuscripts</title>
@stop

@section('content')
	<div class="container">
		<div class="col-xs-12">
			{!! App\Settings::optInTerms() !!}
		</div>
	</div>
@stop