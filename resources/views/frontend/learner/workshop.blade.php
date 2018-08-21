@extends('frontend.layout')

@section('title')
<title>Workshops &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
	<style>
		.nav-tabs {
			border-bottom: 1px solid #ddd;
		}

		.nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus {
			color: #555;
			cursor: default;
			background-color: #fff;
			border: 1px solid #ddd;
			border-bottom-color: transparent;
		}

		.nav-tabs>li>a {
			background: #f0f2f4;
			color: #666;
			outline: medium none;
			padding: 10px 24px;
			line-height: 1.42857143;
			border: 1px solid transparent;
			border-bottom: 0 none;
			border-radius: 4px 4px 0 0;
			position: relative;
			display: block;
			margin-right: 8px;
		}
	</style>
@stop


@section('content')
<div class="account-container">
	
	@include('frontend.partials.learner-menu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12">
			<h3 class="no-margin-top">Påmeldte skriveverksted(er)</h3>
		
			<div class="row"> 
			@foreach( Auth::user()->workshopsTaken as $workshop )
			<div class="col-sm-12 col-md-6">
				<div class="panel panel-default">
					<div class="learner-workshop-image" style="background-image: url({{$workshop->workshop->image}})"></div>
				  	<div class="panel-body">
						<h3 class="no-margin-top">{{ $workshop->workshop->title }}</h3>
						<div>Når: <strong>{{ date_format(date_create($workshop->workshop->date), 'M d, Y H.i') }}</strong></div>
						<div>Hvor: <strong>{{ $workshop->workshop->location }}</strong></div>
						<div>Varighet: <strong>{{ $workshop->workshop->duration }} hours</strong></div>
						<div>Meny: <strong>{{ $workshop->menu->title }}</strong></div>
						<div>Notater: <strong>{{ $workshop->notes }}</strong></div>
				  		<div>
				  		@if( !$workshop->is_active )
						<a class="btn btn-warning disabled margin-top">Pending</a>
				  		@endif
				  		</div>
						{{-- FOR TASK 6 --}}
						<div class="clearfix"></div>
						<div style="text-align: center; margin-top: 20px">
							<a class="btn btn-theme btn-sm" href="{{ route('front.workshop.show', $workshop->workshop_id) }}">Klikk her for fremtidige workshoper eller for bestilling</a>
						</div>
				  	</div>
				</div>
			</div>
			@endforeach

				@if(!count(Auth::user()->workshopsTaken))
					<div style="text-align: center; margin-top: 20px">
						<a class="btn btn-theme btn-sm" href="{{ route('front.workshop.index') }}">Klikk her for fremtidige workshoper eller for bestilling</a>
					</div>
				@endif
			</div>

		</div>

		<div class="col-sm-12">
			<nav>
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#nav-coaching" data-toggle="tab">Coaching Timer</a>
					</li>
				</ul>
			</nav>
			<div class="tab-content">
				<div class="tab-pane fade in active" id="nav-coaching">
					<div class="panel panel-default" style="border-top: 0">
						<div class="panel-body">
							<div class="table-users table-responsive">
								<table class="table no-margin-bottom">
									<thead>
									<tr>
										<th>Manus</th>
										<th>Coaching Time</th>
										<th>Date Ordered</th>
									</tr>
									</thead>
									<tbody>
									@foreach(Auth::user()->coachingTimers as $coachingTimer)
                                        <?php $extension = explode('.', basename($coachingTimer->file)); ?>
										<tr>
											<td>
												@if( end($extension) == 'pdf' || end($extension) == 'odt' )
													<a href="/js/ViewerJS/#../../{{ $coachingTimer->file }}">{{ basename($coachingTimer->file) }}</a>
												@elseif( end($extension) == 'docx' )
													<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$coachingTimer->file}}">{{ basename($coachingTimer->file) }}</a>
												@endif
											</td>
											<td>
												{{ \App\Http\FrontendHelpers::getCoachingTimerPlanType($coachingTimer->plan_type) }}
											</td>
											<td>
												{{ \App\Http\FrontendHelpers::formatDate($coachingTimer->created_at) }}
											</td>
										</tr>
									@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
</div>

@stop

