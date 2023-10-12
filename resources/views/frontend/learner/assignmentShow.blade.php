{{-- @extends('frontend.layout') --}}
@extends('frontend.layouts.course-portal')

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
			{{ trans('site.front.course-text') }}: <a href="">{{ $assignment->course->title }}</a>
			<div class="row"> 
				<?php $i = 1; ?>
				@foreach( $assignment->learners as $learner )
				<div class="col-sm-4">
					<div class="panel panel-default margin-top">
						<div class="panel-body">
							<h4>
								@if( $learner->user->id == Auth::user()->id )
								{{ trans('site.learner.you-text') }}
								@else
								{{ trans('site.learner.learner-text') }} {{ $i }}
								@endif
							</h4>
							<p class="margin-top no-margin-bottom">
								@if( $learner->filename )
								@else
									<em>{{ trans('site.learner.no-uploaded-document') }}</em>
								@endif

								<br />
								@if( $learner->user->id == Auth::user()->id )
								<button type="button" class="btn btn-primary btn-sm margin-top">
									{{ trans('site.learner.upload-document') }}
								</button>
								@else
								<button type="button" class="btn btn-warning btn-sm margin-top">
									{{ trans('site.learner.upload-feedback') }}
								</button>
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

