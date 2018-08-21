@extends('frontend.layout')

@section('title')
<title>Workshops &rsaquo; Forfatterskolen</title>
@stop


@section('content')
<div class="account-container">
	
	@include('frontend.partials.learner-menu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12">
			<h3 class="no-margin-top">Workshops</h3>
		
			<div class="row"> 
			@foreach( Auth::user()->workshopsTaken as $workshop )
			<div class="col-sm-12 col-md-6">
				<div class="panel panel-default">
					<div class="learner-workshop-image" style="background-image: url({{$workshop->workshop->image}})"></div>
				  	<div class="panel-body">
						<h3 class="no-margin-top">{{ $workshop->workshop->title }}</h3>
						<div>When: <strong>{{ date_format(date_create($workshop->workshop->date), 'M d, Y H.i') }}</strong></div>
						<div>Where: <strong>{{ $workshop->workshop->location }}</strong></div>
						<div>Duration: <strong>{{ $workshop->workshop->duration }} hours</strong></div>
						<div>Menu: <strong>{{ $workshop->menu->title }}</strong></div>
						<div>Notes: <strong>{{ $workshop->notes }}</strong></div>
				  		<div>
				  		@if( !$workshop->is_active )
						<a class="btn btn-warning disabled margin-top">Pending</a>
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

