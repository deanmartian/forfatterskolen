@extends('backend.layout')

@section('title')
<title>Create New Note &rsaquo; Forfatterskolen Admin</title>
@stop


@section('content')
<div class="container padding-top">
	<div class="row">
		@include('backend.calendar.partials.form')
	</div>
</div>
@stop
