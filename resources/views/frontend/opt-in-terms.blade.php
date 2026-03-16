@extends('frontend.layout')

@section('title')
	<title>Forfatterskolen &rsaquo; Free Manuscripts</title>
@stop

@section('content')
	<div class="container terms-page">
		<div class="col-12 py-5">
			{!! App\Settings::optInTerms() !!}
		</div>
	</div>
@stop