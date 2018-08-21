@extends('frontend.layout')

@section('title')
<title>{{ $workshop->title }} &rsaquo; Forfatterskolen</title>
@stop

@section('content')
<div class="container course-details-container">
	<div class="row">
		<div class="col-sm-10 col-sm-offset-1">
			<div class="text-center">
				<h2 class="margin-bottom">{{ $workshop->title }}</h2>
				Starts at {{ date_format(date_create($workshop->date), 'M d, Y H.i') }}
			</div>
			<br />
			<div class="workshop-image">
				<img src="{{ $workshop->image }}">
				<div class="workshop-price">{{ FrontendHelpers::currencyFormat($workshop->price) }}</div>
			</div>
			<br />
			<div class="workshop-meta">
				<div><i class="fa fa-tag"></i> {{ FrontendHelpers::currencyFormat($workshop->price) }}</div>
				<div><i class="fa fa-map-marker"></i> {{ $workshop->location }}</div>
				<div><i class="fa fa-calendar"></i> {{ date_format(date_create($workshop->date), 'M d, Y H.i') }}</div>
				<div><i class="fa fa-info-circle"></i> {{ $workshop->duration }} hours</div>
			</div>
			<p class="margin-top text-center font-medium">{!! nl2br($workshop->description) !!}</p>
		</div>
	</div>

	<br />
	<div class="row workshop-details">
		<div class="col-sm-10 col-sm-offset-1 workshop-presenters">
			<h3>Foredragsholdere</h3><br />
			<div class="margin-bottom"> 
				@foreach( $workshop->presenters as $presenter )
				<div class="workshop-presenter">
					<div class="presenter-image" @if( $presenter->image ) style="background-image: url('{{ $presenter->image }}')" @endif></div>
					<div><strong>{{ $presenter->first_name }} {{ $presenter->last_name }}</strong></div>
					<div>{{ $presenter->email }}</div>
				</div>
				@endforeach
			</div>

			<br />
			<h3>Mat Meny</h3><br />
			<div class="row"> 
				@foreach( $workshop->menus as $menu )
				<div class="col-sm-4 workshop-menu">
					<div>
						<div class="menu-thumb" style="background-image: url('{{ $menu->image  }}')"></div>
						<div class="menu-meta">
							<h4 style="margin-bottom: 7px">{{ $menu->title }}</h4>
							<p class="no-margin-bottom">{!! nl2br($menu->description) !!}</p>
						</div>
					</div>
				</div>
				@endforeach
			</div>

			<br />
			<br />
			<a class="btn btn-theme btn-lg" href="{{ route('front.workshop.checkout', $workshop->id) }}">Bestill</a>
		</div>
	</div>

</div>
@stop
