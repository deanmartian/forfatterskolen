@extends('frontend.layout')

@section('title')
	<title>Forfatterskolen &rsaquo; Terms</title>
@stop

@section('content')
	<div class="container terms-page">
		<div class="col-xs-12 py-5">
			{!! $terms !!}
		</div>
	</div>
@stop