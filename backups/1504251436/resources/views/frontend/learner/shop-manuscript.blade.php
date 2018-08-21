@extends('frontend.layout')

@section('title')
<title>Shop Manuscripts &rsaquo; Forfatterskolen</title>
@stop


@section('content')
<div class="account-container">
	
	@include('frontend.partials.learner-menu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12">
			<h3 class="no-margin-top">Manuskripter</h3>
		
			<div class="row"> 
			@foreach( Auth::user()->shopManuscriptsTaken as $shopManuscriptTaken )
			<div class="col-sm-12 col-md-4">
				<div class="panel panel-default">
				  	<div class="panel-body">
						<h3 class="no-margin-top" style="margin-bottom: 4px">{{ $shopManuscriptTaken->shop_manuscript->title }}</h3>
				  		<p>
				  			@if( $shopManuscriptTaken->status == 'Finished' )
							<span class="label label-success">Finished</span>
							@elseif( $shopManuscriptTaken->status == 'Started' )
							<span class="label label-primary">Started</span>
							@elseif( $shopManuscriptTaken->status == 'Not started' )
							<span class="label label-warning">Not started</span>
							@endif
				  			<div style=" margin-top: 5px">Ord: {{ $shopManuscriptTaken->words }}</div>
				  		</p>
				  		<div>
				  		@if( $shopManuscriptTaken->is_active )
						<a class="btn btn-primary" href="{{ route('learner.shop-manuscript.show', $shopManuscriptTaken->id) }}">Se Manuskript</a>
				  		@else
						<a class="btn btn-warning disabled">Pending</a>
				  		@endif
				  		</div>
				  	</div>
				</div>
			</div>
			@endforeach
			</div>

		</div>
	</div>
	<div class="clearfix"></div>
</div>

@stop

