@extends('frontend.layout')

@section('title')
<title>Manuscript for course {{ $manuscript->courseTaken->package->course->title }} &rsaquo; Forfatterskolen</title>
@stop


@section('content')
<div class="account-container">
	
	@include('frontend.partials.learner-menu')
	
	<?php $extension = explode('.', basename($manuscript->filename)); ?>

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-md-12">
			<div class="margin-top">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="row">
								<div class="col-sm-12 col-md-7">
									@if( end($extension) == 'pdf' || end($extension) == 'odt' )
									<iframe src="/js/ViewerJS/#../..{{ $manuscript->filename }}" style="width: 100%; border: 0; height: 600px"></iframe>
									@elseif( end($extension) == 'docx' )
									<iframe src="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$manuscript->filename}}" style="width: 100%; border: 0; height: 600px"></iframe>
									@endif
								</div>
								<div class="col-sm-12 col-md-5">
									@if( $manuscript->status == 'Finished' )
									<span class="label label-success">Finished</span>
									@elseif( $manuscript->status == 'Started' )
									<span class="label label-primary">Started</span>
									@elseif( $manuscript->status == 'Not started' )
									<span class="label label-warning">Not started</span>
									@endif
									<br />
									Filename: {{ basename($manuscript->filename) }}<br />
									Date uploaded: {{ $manuscript->created_at }}<br />
									Course: <a href="{{route('learner.course.show', ['id' => $manuscript->courseTaken->id])}}">{{ $manuscript->courseTaken->package->course->title }}</a><br />
									<br />
									<h4>Feedbacks</h4>
									<div class="row margin-top">
										@foreach($manuscript->feedbacks as $feedback)
										<div class="col-sm-12">
											<div class="panel panel-default">
												<div class="panel-body">
													<strong>Files:</strong> 
													@foreach( $feedback->filename as $filename )<br />
													<a href="{{ $filename }}" target="_blank">{{ basename($filename) }}</a>
													@endforeach
													<br />
													<strong>Notes:</strong> {{ $feedback->notes }} <br />
													<strong>Submitted on:</strong> {{ $feedback->created_at }} <br />
												</div>
											</div>
										</div>
										@endforeach
									</div>
								</div>
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

