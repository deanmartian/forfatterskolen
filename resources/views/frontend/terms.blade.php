@extends('frontend.layout')

@section('title')
	<title>Forfatterskolen &rsaquo; Terms</title>
@stop

@section('content')
	<div class="container">
		<div class="col-xs-12">
			{!! $terms !!}
		</div>
	</div>
@stop