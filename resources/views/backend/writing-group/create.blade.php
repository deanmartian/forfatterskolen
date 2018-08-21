@extends('backend.layout')

@section('title')
<title>Create New Writing Group &rsaquo; Forfatterskolen Admin</title>
@stop


@section('content')
<div class="container padding-top">
	<div class="row">
		@include('backend.writing-group.partials.form')
	</div>
</div>
@stop
