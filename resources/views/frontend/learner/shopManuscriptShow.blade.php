@extends('frontend.layout')

@section('title')
<title>{{ $shopManuscriptTaken->shop_manuscript->title }} &rsaquo; Forfatterskolen</title>
@stop

@section('content')
<div class="learner-container">
	<div class="container">
        <?php $extension = explode('.', basename($shopManuscriptTaken->file)); ?>
		<div class="panel panel-default global-panel">
			<div class="panel-body mb-0">
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
							<span class="label label-success">
								{{ trans('site.learner.finished') }}
							</span>
						@elseif( $shopManuscriptTaken->status == 'Started' )
							<span class="label label-primary">
								{{ trans('site.learner.started') }}
							</span>
						@elseif( $shopManuscriptTaken->status == 'Not started' )
							<span class="label label-warning">
								{{ trans('site.learner.not-started') }}
							</span>
						@endif
						<h2 class="font-barlow-bold">{{ $shopManuscriptTaken->shop_manuscript->title }}</h2>
						<span class="font-barlow-regular">{{ trans('site.learner.filename-text') }}</span>:
							{{ basename($shopManuscriptTaken->file) }}<br />
						@if($shopManuscriptTaken->words)
							<span class="font-barlow-regular">{{ trans('site.learner.words-text') }}</span>: {{ basename($shopManuscriptTaken->words) }}<br />
						@endif
							<span class="font-barlow-regular">{{ trans('site.learner.date-uploaded') }}</span>: {{ $shopManuscriptTaken->created_at }}<br />
						<br />
						<h3 class="font-barlow-semi-bold font-weight-normal">
							{{ trans('site.learner.feedbacks-text') }}
						</h3>
						<div class="row margin-top">
							@foreach($shopManuscriptTaken->feedbacks as $feedback)
								<div class="col-sm-12">
									<div class="panel panel-default">
										<div class="panel-body">
											<strong>{{ trans('site.learner.files-text') }}:</strong>
											@foreach( $feedback->filename as $filename )<br />
											<a href="{{ $filename }}" target="_blank">{{ basename($filename) }}</a>
											@endforeach
											<br />
											<strong>{{ trans('site.learner.notes-text') }}:</strong> {{ $feedback->notes }} <br />
											<strong>{{ trans('site.learner.submitted-on') }}:</strong> {{ $feedback->created_at }} <br />
										</div>
									</div>
								</div>
							@endforeach
						</div>
						<hr />
						<h3 class="font-barlow-semi-bold font-weight-normal">
							{{ trans('site.learner.comments') }}
						</h3>
						@if( $shopManuscriptTaken->feedbacks->count() > 0 )
                            <?php
                            $feedbackFirst = $shopManuscriptTaken->feedbacks[0];
                            $created_at = Carbon\Carbon::parse($feedbackFirst->created_at);
                            $diff = $created_at->diffInDays();
                            ?>
							@if( $diff <= 7 )
								<form method="POST" class="mt-4" action="{{ route('learner.shop-manuscript.post-comment', $shopManuscriptTaken->id) }}">
									{{ csrf_field() }}
									<input type="text" placeholder="{{ trans('site.learner.comment') }}" name="comment"
										   class="form-control" required>
									<div class="text-right mt-4">
										<button class="btn btn-info btn-sm" type="submit">
											{{ trans('site.learner.add-comment') }}
										</button>
									</div>
								</form>
							@else
								<div class="mt-4">
									<input type="text" placeholder="{{ trans('site.learner.comment') }}" name="comment"
										   class="form-control" required disabled>
									<div class="text-right mt-4">
										<button class="btn btn-info btn-sm" type="button" disabled>
											{{ trans('site.learner.add-comment') }}
										</button>
									</div>
								</div>
							@endif
						@else
							<form method="POST" class="mt-4" action="{{ route('learner.shop-manuscript.post-comment', $shopManuscriptTaken->id) }}">
								{{ csrf_field() }}
								<input type="text" placeholder="{{ trans('site.learner.comment') }}" name="comment"
									   class="form-control" required>
								<div class="text-right mt-4">
									<button class="btn btn-info btn-sm" type="submit">
										{{ trans('site.learner.add-comment') }}
									</button>
								</div>
							</form>
						@endif

						<div class="mt-4">
							@foreach( $shopManuscriptTaken->comments as $comment )
								@if( $comment->user_id == Auth::user()->id )
									<div class="text-right">
										<div class="comment owner">
											<div>{{ $comment->comment }}</div>
											<div><small><em>{{ trans('site.learner.you-text') }}</em></small></div>
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
				</div> <!-- end row -->
			</div> <!-- end panel-body -->
		</div> <!-- end global-panel -->

			<div class="text-right">
				<button class="btn site-btn-global mt-4">{{ trans('site.learner.upgrade') }}</button>
			</div>

	</div> <!-- end container -->
</div>
@stop

