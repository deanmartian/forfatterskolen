@extends('frontend.layout')

@section('page_title', 'Vilk&aring;r for gratis materiale &rsaquo; Forfatterskolen')
@section('meta_desc', 'Vilkår for gratis materiale fra Forfatterskolen.')

@section('content')
	<div class="container terms-page">
		<div class="col-12 py-5">
			{!! App\Settings::optInTerms() !!}
		</div>
	</div>
@stop