@extends('backend.layout')

@section('page_title', 'Create New Writing Group &rsaquo; Forfatterskolen Admin')


@section('content')
<div class="container padding-top">
	<div class="row">
		@include('backend.writing-group.partials.form')
	</div>
</div>
@stop
