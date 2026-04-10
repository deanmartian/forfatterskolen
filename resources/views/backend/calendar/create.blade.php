@extends('backend.layout')

@section('page_title', 'Create New Note &rsaquo; Forfatterskolen Admin')


@section('content')
<div class="container padding-top">
	<div class="row">
		@include('backend.calendar.partials.form')
	</div>
</div>
@stop
