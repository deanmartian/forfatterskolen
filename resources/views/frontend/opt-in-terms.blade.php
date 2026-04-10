@extends('frontend.layout')

@section('page_title', 'Forfatterskolen &rsaquo; Free Manuscripts')

@section('content')
	<div class="container terms-page">
		<div class="col-12 py-5">
			{!! App\Settings::optInTerms() !!}
		</div>
	</div>
@stop