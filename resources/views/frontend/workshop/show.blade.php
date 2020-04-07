@extends('frontend.layout')

@section('title')
<title>{{ $workshop->title }} &rsaquo; Forfatterskolen</title>
@stop

@section('content')
	<div class="workshop-single-page">
		<div class="header">
			<div class="container text-center">
				<h1>
					{{ $workshop->title }}
				</h1>
				<span class="workshop-start">
					{{--{{ \App\Http\FrontendHelpers::formatDateTimeNor($workshop->date) }}--}}
					{{ str_replace(['_date_', '_time_'],
					[ \Carbon\Carbon::parse($workshop->date)->format('d.m.Y'),
					\Carbon\Carbon::parse($workshop->date)->format('H:i')], trans('site.front.workshop.start-date')) }}
				</span>

				<div class="sub-header text-left">

                    <?php
                    	$availedWorkshop = 0;
                    	if(Auth::user()) {
                            $availedWorkshop = Auth::user()->workshopsTaken()->where('workshop_id', '=', $workshop->id)->get()->count();
						}
                    ?>

					@if($availedWorkshop)
						<a class="btn buy-course" href="{{ route('learner.workshop') }}">
							{{ trans('site.front.workshop.learner-workshop') }}
						</a>
					@else
						<a href="{{ route('front.workshop.checkout', $workshop->id) }}" class="btn buy-course">
							{{ trans('site.front.buy') }}
						</a>
					@endif

					<div class="col-xs-4 pl-0 presenter-container">
						@foreach( $workshop->presenters()->take(1)->get() as $presenter )
							<div class="workshop-presenter">
								<div class="presenter-image pull-left" @if( $presenter->image ) style="background-image: url('{{ $presenter->image }}')" @endif></div>
								<div class="presenter-details">
									<span class="title">
										{{ trans('site.front.workshop.presenters') }}
									</span> <br>
									<span class="name">{{ $presenter->first_name }} {{ $presenter->last_name }}</span>
									{{ $presenter->email }}
								</div>
							</div>
						@endforeach
					</div>
					<div class="col-xs-8 workshop-info">
						<div class="date-time-cont">
							<div>
								<i class="img-icon16 icon-calendar"></i>
								<span>{{ \App\Http\FrontendHelpers::formatDateTimeNor($workshop->date) }}</span>
							</div>
							<div>
								<i class="img-icon16 icon-clock3"></i>
								<span>
									{{ str_replace('_duration_', $workshop->duration, trans('site.front.workshop.duration')) }}
								</span>
							</div>
						</div>

						<div class="mt-4 loc-price-cont">
							<div>
								<i class="img-icon16 icon-marker"></i>
								<span>{{ $workshop->location }}</span>
							</div>
							<div>
								<i class="img-icon16 icon-price-tag"></i>
								<span>{{ \App\Http\FrontendHelpers::currencyFormat($workshop->price) }}</span>
							</div>
						</div>
					</div>
				</div>
			</div> <!-- end container -->
		</div> <!-- end header -->

		<div class="container single-content">
			<div class="row workshop-image-row" style="background-image: url({{ $workshop->image }})">
				<div class="date-container">
					<?php
                    	$start_date = \Carbon\Carbon::parse($workshop->date);
					?>
					<h1>
						{{ $start_date->format('d') }}
					</h1>
					<h2>
						{{ strtoupper($start_date->format('M')) }}
					</h2>
				</div>
			</div> <!-- end workshop-image-row -->

			<div class="row details-container">
				<div>
					{!! nl2br($workshop->description) !!}
				</div>

				@if ($workshop->id !== 12)
					<div class="information-container">
						<h1>
							{{ ucwords(trans('site.front.workshop.practical-information')) }}
						</h1>

						@foreach( $workshop->menus as $menu )
							<div class="row">
								<div class="col-md-2 left-container">
									<img src="{{ $menu->image  }}" alt="" class="rounded-circle">
								</div>
								<div class="col-md-10 right-container">
									{!! nl2br($menu->description) !!}
								</div>
							</div>
						@endforeach
					</div>
				@endif
			</div> <!-- end details-container -->
		</div> <!-- end single-content -->
	</div>
@stop
