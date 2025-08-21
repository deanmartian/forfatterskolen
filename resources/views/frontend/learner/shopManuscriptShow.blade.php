{{-- @extends('frontend.layout') --}}
@extends('frontend.layouts.course-portal')

@section('title')
<title>{{ $shopManuscriptTaken->shop_manuscript->title }} &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
	<style>
		.global-alert-box {
			position: fixed;
			bottom: 0;
			right: 0;
			min-width: 300px;
		}
	</style>
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
						<div class="mb-3">
							<h3>
								{{ $shopManuscriptTaken->shop_manuscript->title }}
	
								@if( $shopManuscriptTaken->status == 'Finished' )
									<p class="custom-badge start rounded-20 mb-0">
										{{ trans('site.learner.finished') }}
									</p>
								@elseif( $shopManuscriptTaken->status == 'Started' )
									<p class="custom-badge ended rounded-20 mb-0">
										{{ trans('site.learner.started') }}
									</p>
								@elseif( $shopManuscriptTaken->status == 'Not started' )
									<p class="custom-badge yellow rounded-20 mb-0">
										{{ trans('site.learner.not-started') }}
									</p>
								@endif
							</h3>
							<a href="{{ route('learner.shop-manuscript.download', [$shopManuscriptTaken->id, 'manuscript']) }}" 
								class="btn blue-outline-btn">
								{{ trans('site.learner.download-original-script') }}
								<i class="fa fa-download"></i>
							</a>
						</div>
						<span class="font-barlow-regular mt-3">{{ trans('site.learner.filename-text') }}</span>:
							{{ basename($shopManuscriptTaken->file) }}<br />
						@if($shopManuscriptTaken->words)
							<span class="font-barlow-regular">{{ trans('site.learner.words-text') }}</span>: {{ basename($shopManuscriptTaken->words) }}<br />
						@endif
							<span class="font-barlow-regular">{{ trans('site.learner.date-uploaded') }}</span>:
							{{ \App\Http\FrontendHelpers::formatDate($shopManuscriptTaken->created_at) }}<br />
							<br>
							@if ($shopManuscriptTaken->synopsis)
								<a href="{{ route('learner.shop-manuscript.download', [$shopManuscriptTaken->id, 'synopsis']) }}">
									{{ trans('site.download-synopsis') }}
								</a>
								<br>
							@endif
						<br />
						<h3>
							{{ trans('site.learner.feedbacks-text') }}
						</h3>
						<div class="row margin-top">
							@if($shopManuscriptTaken->status == 'Finished')
								@foreach($shopManuscriptTaken->feedbacks as $feedback)
									<div class="col-sm-12 mt-3">
										<a href="{{ route('learner.shop-manuscript.download-feedback', 
										[$shopManuscriptTaken->id, $feedback->id]) }}" class="btn short-red-outline-btn mb-2">
											{{ trans('site.learner.download-feedback') }}
											<i class="fa fa-download"></i>
										</a> <br>
										<strong>{{ trans('site.learner.notes-text') }}:</strong> 
										{{ $feedback->notes }} <br />
										<strong>{{ trans('site.learner.submitted-on') }}:</strong> 
										{{ \App\Http\FrontendHelpers::formatDateTimeNor($feedback->created_at) }} <br />
									</div>
								@endforeach
							@endif
						</div>
						<hr />
						<h3 class="font-barlow-semi-bold font-weight-normal">
							{{ trans('site.learner.comments') }}
						</h3>
						@if( $shopManuscriptTaken->feedbacks->count() > 0 )
                            <?php

							$feedbackFirst = $shopManuscriptTaken->feedbacks[0];
							$created_at = \Carbon\Carbon::parse($feedbackFirst->created_at);

							// Signed difference in days from now
							$diff = (int) round(\Carbon\Carbon::now()->diffInDays($created_at, false));
                            ?>
							<div class="mt-4">
								<input type="text" placeholder="{{ trans('site.learner.comment') }}" name="comment"
										class="form-control" required disabled>
								<div class="text-right mt-4">
									<button class="btn btn-info btn-sm" type="button" disabled>
										{{ trans('site.learner.add-comment') }}
									</button>
								</div>
							</div>
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

						@if (!$shopManuscriptTaken->is_manuscript_locked)
							<div>
								<button type="button" class="btn btn-primary uploadManuscriptBtn"
										data-toggle="modal" data-target="#uploadManuscriptModal"
										data-action="{{ route('learner.shop-manuscript.upload', $shopManuscriptTaken->id) }}">
									{{ trans('site.learner.upload-script') }}
								</button>

								<button type="button" class="btn btn-success uploadSynopsisBtn"
										data-toggle="modal" data-target="#uploadSynopsisModal"
										data-action="{{ route('learner.shop-manuscript.upload_synopsis', $shopManuscriptTaken->id) }}">
									{{ trans('site.synopsis') }}
								</button>
							</div>
						@endif
					</div>
				</div> <!-- end row -->
			</div> <!-- end panel-body -->
		</div> <!-- end global-panel -->

			@if( $shopManuscriptTaken->status == 'Not started' )
				<div class="text-right">
					<a class="btn site-btn-global mt-4" href="{{ route('learner.upgrade') }}">
						{{ trans('site.learner.upgrade') }}
					</a>
				</div>
			@endif

	</div> <!-- end container -->

	<div id="uploadManuscriptModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title">{{ trans('site.learner.upload-script') }}</h3>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<form method="POST" enctype="multipart/form-data" action="" onsubmit="disableSubmitOrigText(this)">
						{{ csrf_field() }}
						<div class="form-group">
							<label>
								* {{ trans('site.learner.manuscript.doc-pdf-odt-text') }}
							</label>
							<input type="file" class="form-control" required name="manuscript"
								   accept="application/msword,
								   application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf,
								   application/vnd.oasis.opendocument.text">
						</div>
						<div class="form-group">
							<label for="">{{ trans('site.front.genre') }}</label>
							<select class="form-control" name="genre" required>
								<option value="" disabled="disabled" selected>{{ trans('site.front.select-genre') }}</option>
								@foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
									<option value="{{ $type->id }}"> {{ $type->name }} </option>
								@endforeach
							</select>
						</div>
						<button type="submit" class="btn btn-primary pull-right">{{ trans('site.learner.upload-script') }}</button>
						<div class="clearfix"></div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div id="uploadSynopsisModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title">{{ trans('site.synopsis') }}</h3>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<form method="POST" enctype="multipart/form-data" action="" onsubmit="disableSubmitOrigText(this)">
						{{ csrf_field() }}
						<div class="form-group">
							<label for="">{{ trans('site.synopsis') }}</label>
							<input type="file" class="form-control" name="synopsis"
								   accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document,
								   application/pdf, application/vnd.oasis.opendocument.text" required>
						</div>
						<button type="submit" class="btn btn-primary pull-right">{{ trans('site.front.submit') }}</button>
						<div class="clearfix"></div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="exceedModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">{{ trans('site.learner.upgrade') }}</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">

				<div id="exceed_message">
					<p>
						{!! str_replace(['_break_', '_exceed_', '_max_words_'],
						['<br/>', session('exceed'), session('max_words')] ,
						trans('site.learner.upgrade-exceed-message')) !!}
					</p>
					<button class="btn btn-default" data-dismiss="modal">{{ trans('site.learner.close') }}</button>
					<a href="{{ url('upgrade-manuscript/'.session('plan').'/checkout') }}" class="btn btn-primary pull-right">{{
					trans('site.learner.upgrade-script') }}</a>
				</div>
				<div class="clearfix"></div>

			</div>
		</div>

	</div>
</div>

@if(Session::has('manuscript_test_error'))
	<div id="manuscriptTestErrorModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-body text-center">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<div style="color: red; font-size: 24px"><i class="fa fa-close"></i></div>
					{!! Session::get('manuscript_test_error') !!}
				</div>
			</div>
		</div>
	</div>
@endif

@if (session('exceed'))
	<input type="hidden" name="exceed">
@endif

@if($errors->count())
    <?php
    $alert_type = session('alert_type');
    if(!Session::has('alert_type')) {
        $alert_type = 'danger';
    }
    ?>
	<div class="alert alert-{{ $alert_type }} global-alert-box" style="z-index: 9">
		<a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
		<ul>
			@foreach($errors->all() as $error)
				<li>{!! $error !!}</li>
			@endforeach
		</ul>
	</div>
@endif

@stop

@section('scripts')
	<script>
        let has_exceed = $("input[name=exceed]").length;

        if (has_exceed) {
            $("#exceedModal").modal();
        }

        $('.uploadManuscriptBtn').click(function(){
            let form = $('#uploadManuscriptModal').find('form');
            let action = $(this).data('action');
            form.attr('action', action);
        });

        $('.uploadSynopsisBtn').click(function(){
            let form = $('#uploadSynopsisModal').find('form');
            let action = $(this).data('action');
            form.attr('action', action);
        });
	</script>
@stop

