@extends('frontend.layout')

@section('title')
<title>{{ $shopManuscriptTaken->shop_manuscript->title }} &rsaquo; Forfatterskolen</title>
@stop


@section('content')
<div class="account-container">
	
	@include('frontend.partials.learner-menu')
	
	<?php $extension = explode('.', basename($shopManuscriptTaken->file)); ?>

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-md-12">
			<div class="margin-top">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="row">
								<div class="col-sm-12 col-md-7">
									@if( end($extension) == 'pdf' || end($extension) == 'odt' )
									<iframe src="/js/ViewerJS/#../..{{ $shopManuscriptTaken->file }}" style="width: 100%; border: 0; height: 600px"></iframe>
									@elseif( end($extension) == 'docx' || end($extension) == 'doc' )
									<iframe src="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$shopManuscriptTaken->file}}" style="width: 100%; border: 0; height: 600px"></iframe>
									@endif
								</div>
								<div class="col-sm-12 col-md-5">
									@if( $shopManuscriptTaken->status == 'Finished' )
									<span class="label label-success">Finished</span>
									@elseif( $shopManuscriptTaken->status == 'Started' )
									<span class="label label-primary">Started</span>
									@elseif( $shopManuscriptTaken->status == 'Not started' )
									<span class="label label-warning">Not started</span>
									@endif
									<h3 class="no-margin-top">{{ $shopManuscriptTaken->shop_manuscript->title }}</h3>
									Filename: {{ basename($shopManuscriptTaken->file) }}<br />
									@if($shopManuscriptTaken->words)
									Words: {{ basename($shopManuscriptTaken->words) }}<br />
									@endif
									Date uploaded: {{ $shopManuscriptTaken->created_at }}<br />
									<br />
									<h4>Feedbacks</h4>
									<div class="row margin-top">
										@foreach($shopManuscriptTaken->feedbacks as $feedback)
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
									<hr />
									<h4>Comments</h4>
									@if( $shopManuscriptTaken->feedbacks->count() > 0 )
										<?php 
										$feedbackFirst = $shopManuscriptTaken->feedbacks[0]; 
										$created_at = Carbon\Carbon::parse($feedbackFirst->created_at);
										$diff = $created_at->diffInDays();
										?>
										@if( $diff <= 7 )
										<form method="POST" class="margin-top" action="{{ route('learner.shop-manuscript.post-comment', $shopManuscriptTaken->id) }}">
											{{ csrf_field() }}
											<input type="text" placeholder="Comment" name="comment" class="form-control" required>
											<div class="text-right margin-top">
												<button class="btn btn-info btn-sm" type="submit">Add Comment</button>
											</div>
										</form>
										@else
										<div class="margin-top">
											<input type="text" placeholder="Comment" name="comment" class="form-control" required disabled>
											<div class="text-right margin-top">
												<button class="btn btn-info btn-sm" type="button" disabled>Add Comment</button>
											</div>
										</div>
										@endif
									@else
									<form method="POST" class="margin-top" action="{{ route('learner.shop-manuscript.post-comment', $shopManuscriptTaken->id) }}">
										{{ csrf_field() }}
										<input type="text" placeholder="Comment" name="comment" class="form-control" required>
										<div class="text-right margin-top">
											<button class="btn btn-info btn-sm" type="submit">Add Comment</button>
										</div>
									</form>
									@endif

									<div class="margin-top">
									@foreach( $shopManuscriptTaken->comments as $comment )
									@if( $comment->user_id == Auth::user()->id )
									<div class="text-right">
										<div class="comment owner">
											<div>{{ $comment->comment }}</div>
											<div><small><em>You</em></small></div>
											<small>{{ $comment->created_at }}</small>
										</div>
									</div>
									@else
									<div>
										<div class="comment">
											<div>{{ $comment->comment }}</div>
											<div><small><em>{{ $comment->user->full_name }}</em></small></div>
											<small>{{ $comment->created_at }}</small>
										</div>
									</div>
									@endif
									@endforeach
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="text-right">
						<button class="btn btn-theme btn-sm">Upgrade</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
</div>


@stop

