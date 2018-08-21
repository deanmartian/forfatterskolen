@extends('frontend.layout')

@section('title')
<title>{{ $assignment->title }} &rsaquo; Assignments &rsaquo; Forfatterskolen</title>
@stop


@section('content')
<div class="account-container">
	
	@include('frontend.partials.learner-menu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12">
			<h3 class="no-margin-top no-margin-bottom">{{ $assignment->title }}</h3>
			{{ $assignment->description }}
			<br /><br />
			Kurs: <a href="">{{ $assignment->course->title }}</a>
			<div class="row"> 
				<?php $i = 1; ?>
				@foreach( $assignment->learners as $learner )
				<div class="col-sm-4">
					<div class="panel panel-default margin-top">
						<div class="panel-body">
							<h4>
								@if( $learner->user->id == Auth::user()->id )
								You
								@else
								Learner {{ $i }}
								@endif
							</h4>
							<p class="margin-top no-margin-bottom">
								@if( $learner->filename )
								@else
									<em>No document uploaded</em>
								@endif

								<br />
								@if( $learner->user->id == Auth::user()->id )
								<button type="button" class="btn btn-primary btn-sm margin-top">Upload document</button>
								@else
								<button type="button" class="btn btn-warning btn-sm margin-top">Upload feedback</button>
								@endif
							</p>
						</div>
					</div>
				</div>
				<?php $i++; ?>
				@endforeach
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
</div>

@stop

