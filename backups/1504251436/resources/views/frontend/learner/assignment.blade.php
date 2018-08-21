@extends('frontend.layout')

@section('title')
<title>Assignments &rsaquo; Forfatterskolen</title>
@stop


@section('content')
<div class="account-container">
	
	@include('frontend.partials.learner-menu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12">
			<h3 class="no-margin-top">Assignments</h3>

			<div class="row">
			
				<?php 
				$assignments = [];
				$courseTaken =  Auth::user()->coursesTaken;
				foreach( $courseTaken as $taken ) :
					foreach( $taken->package->course->assignments as $taken_assignment ) :
						$assignments[] = $taken_assignment;
					endforeach;
				endforeach;
				?>
			
				@foreach( $assignments as $assignment )
				<div class="col-sm-12 col-md-4">
					<div class="panel panel-default">
						<div class="panel-body">
							<h4 class="no-margin-top margin-bottom"><a href="{{ route('learner.assignment.show', $assignment->id) }}">{{ $assignment->title }}</a></h4>
							{{ $assignment->description }}
							<div class="margin-top">
							Kurs: {{ $assignment->course->title }}
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

