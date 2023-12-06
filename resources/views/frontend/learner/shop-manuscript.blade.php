{{-- @extends('frontend.layout') --}}
@extends('frontend.layouts.course-portal')

@section('title')
<title>Shop Manuscripts &rsaquo; Forfatterskolen</title>
@stop

@section('heading') {{ trans('site.learner.manuscript.title') }} @stop

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('content')
<div class="learner-container">
	<div class="container learner-manuscript-wrapper">
		@include('frontend.partials.learner-search-new')

		<div class="global-card mt-4 px-0">
			<div class="card-body p-0">
				@foreach ($shopManuscriptsTaken->chunk(2) as $shopManuscriptTaken_chunk)
					<div class="manuscript-taken-row">
						@foreach ($shopManuscriptTaken_chunk as $shopManuscriptTaken)
							<div class="col-md-6">
								<div class="global-card">
									<div class="card-body p-0">
										<h3>
											{{ $shopManuscriptTaken->shop_manuscript->title }}

											@if($shopManuscriptTaken->expected_finish)
												<p class="custom-badge active rounded-20">
													{{ trans('site.learner.expected-finish') }}:
													{{ $shopManuscriptTaken->expected_finish }}
												</p>
											@endif

											@if( $shopManuscriptTaken->status == 'Finished' )
												<p class="custom-badge start rounded-20">
													{{ trans('site.learner.finished') }}
												</p>
											@elseif( $shopManuscriptTaken->status == 'Pending' )
												<p class="custom-badge on-hold rounded-20">
													{{ trans('site.learner.pending') }}
												</p>
											@elseif( $shopManuscriptTaken->status == 'Started' )
												<p class="custom-badge ended rounded-20">
													{{ trans('site.learner.started') }}
												</p>
											@elseif( $shopManuscriptTaken->status == 'Not started' )
												<p class="custom-badge yellow rounded-20">
													{{ trans('site.learner.not-started') }}
												</p>
											@endif
										</h3>

										<p class="mb-5">
											{{ $shopManuscriptTaken->shop_manuscript->description }}
										</p>

										<div class="button-container">
											@if( $shopManuscriptTaken->is_active )
												@if( $shopManuscriptTaken->status == 'Not started' )
													<button type="button" class="btn red-global-btn uploadManuscriptBtn py-2 px-4 rounded-20"
															data-toggle="modal" data-target="#uploadManuscriptModal"
															data-action="{{ route('learner.shop-manuscript.upload', 
															$shopManuscriptTaken->id) }}">
														{{ trans('site.learner.upload-script') }}
														<i class="fa fa-upload"></i>
													</button>
												@else
													<a class="btn blue-outline-btn rounded-20 px-4" 
														href="{{ route('learner.shop-manuscript.show',
													$shopManuscriptTaken->id) }}">
														{{ trans('site.learner.see-manuscript') }}
													</a>
													@if (!$shopManuscriptTaken->is_manuscript_locked 
													&& $shopManuscriptTaken->status != 'Finished')
														<button class="btn btn-success updateManuscriptBtn" type="button" 
															data-toggle="modal" data-target="#updateUploadedManuscriptModal" 
															data-fields="{{ json_encode($shopManuscriptTaken) }}"
															data-action="{{ route('learner.shop-manuscript.update-uploaded-manuscript', 
															$shopManuscriptTaken->id) }}">
																<i class="fa fa-pen"></i>
														</button>
														<button class="btn btn-danger deleteManuscriptBtn" type="button" 
															data-toggle="modal" data-target="#deleteUploadedManuscriptModal"
															data-action="{{ route('learner.shop-manuscript.delete-uploaded-manuscript',
															$shopManuscriptTaken->id) }}">
																<i class="fa fa-trash"></i>
														</button>
													@endif

													@if( $shopManuscriptTaken->status == 'Finished' )
														<?php
															$feedback = $shopManuscriptTaken->feedbacks()->first();
														?>
														<a href="{{ route('learner.shop-manuscript.download-feedback',
														 [$shopManuscriptTaken->id, $feedback->id]) }}" 
														 class="btn blue-btn rounded-20 px-4 ml-2">
															{{ trans('site.learner.download-feedback') }}
															<i class="fa fa-download"></i>
														</a>
													@endif

												@endif
											@else
												<a class="btn btn-warning disabled" style="color: #fff">
													{{ trans('site.learner.pending') }}
												</a>
											@endif
										</div>
										<div class="word-container font-weight-bold">
											@if( $shopManuscriptTaken->status != 'Not started' )
												{{ trans('site.learner.word') }}: {{ $shopManuscriptTaken->words }} <br>
											@endif
										</div>

										<div class="clearfix"></div>
									</div>
								</div>
							</div>
						@endforeach
					</div>
				@endforeach

				<div class="text-center">
					{{ $shopManuscriptsTaken->appends(request()->except('page'))->links('pagination.custom-pagination') }}
				</div>
			</div>
		</div> <!-- end global-card -->
	</div>
</div>


<div id="uploadManuscriptModal" class="modal fade global-modal" role="dialog">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">{{ trans('site.learner.upload-script') }}</h3>
		  <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      	<form method="POST" enctype="multipart/form-data" action="" onsubmit="disableSubmit(this)">
      		{{ csrf_field() }}
      		<div class="form-group">
				<label>
					* {{ trans('site.learner.manuscript.doc-pdf-odt-text') }}
				</label>
      			<input type="file" class="form-control" required name="manuscript" 
				accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, 
				application/pdf, application/vnd.oasis.opendocument.text">
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
			<div class="form-group">
				<label for="">{{ trans('site.front.form.synopsis-optional') }}</label>
				<input type="file" class="form-control" name="synopsis" 
				accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document,
				 application/pdf, application/vnd.oasis.opendocument.text">
			</div>
			<div class="form-group">
				<label for="">{{ trans('site.front.form.manuscript-description') }}</label>
				<textarea name="description" id="" cols="30" rows="10" class="form-control"></textarea>
			</div>
      		<button type="submit" class="btn submit-btn pull-right">{{ trans('site.learner.upload-script') }}</button>
      		<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>

<div id="updateUploadedManuscriptModal" class="modal fade global-modal" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">{{ trans('site.learner.upload-script') }}</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>* {{ trans('site.learner.manuscript.doc-pdf-odt-text') }}</label>
						<input type="file" class="form-control" required name="manuscript" 
						accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document,
						application/pdf, application/vnd.oasis.opendocument.text">
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
					<div class="form-group synopsis">
						<label for="">{{ trans('site.front.form.synopsis-optional') }}</label>
						<input type="file" class="form-control" name="synopsis" 
						accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document,
						application/pdf, application/vnd.oasis.opendocument.text">
					</div>

					<div class="form-group synopsis">
						<label>{{ trans('site.front.form.coaching-time-later-in-manus') }}</label>
						<input type="checkbox" data-toggle="toggle" data-on="{{ trans('site.front.yes') }}"
							   class="is-free-toggle" data-off="{{ trans('site.front.no') }}"
							   name="coaching_time_later">
					</div>

					<div class="form-group">
						<label for="">{{ trans('site.front.form.manuscript-description') }}</label>
						<textarea name="description" id="" cols="30" rows="10" class="form-control"></textarea>
					</div>
					<button type="submit" class="btn submit-btn pull-right">{{ trans('site.learner.upload-script') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="deleteUploadedManuscriptModal" class="modal fade global-modal" role="dialog" onsubmit="disableSubmit(this)">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">{{ trans('site.learner.upload-script') }}</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action="">
					{{ csrf_field() }}
					{{ trans('site.learner.delete-manuscript-question') }}
					<div class="clearfix"></div>
					<button type="submit" class="btn btn-danger pull-right">{{ trans('site.learner.delete') }}</button>
					<div class="clearfix"></div>
				</form>
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

@stop

@section('scripts')
	<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script>
	var has_exceed = $("input[name=exceed]").length;

	if (has_exceed) {
	    $("#exceedModal").modal();
	}

	@if(Session::has('manuscript_test_error'))
    	$('#manuscriptTestErrorModal').modal('show');
	@endif

	$('.uploadManuscriptBtn').click(function(){
		var form = $('#uploadManuscriptModal form');
		var action = $(this).data('action');
		form.attr('action', action);
	});

	$(".updateManuscriptBtn").click(function(){
        var modal = $('#updateUploadedManuscriptModal');
        var form = $('#updateUploadedManuscriptModal form');
	    var fields = $(this).data('fields');
        var action = $(this).data('action');
	    if (fields.genre) {
            modal.find('select').val(fields.genre);
		}
        form.attr('action', action);
		modal.find('textarea[name=description]').text(fields.description);
		if (fields.shop_manuscript_id === 9) {
            modal.find('.synopsis').addClass('hide');
		} else {
            modal.find('.synopsis').removeClass('hide');

            if (fields.coaching_time_later) {
                $("input[name=coaching_time_later]").bootstrapToggle('on');
			} else {
                $("input[name=coaching_time_later]").bootstrapToggle('off');
			}
        }
	});

    $('.deleteManuscriptBtn').click(function(){
        var form = $('#deleteUploadedManuscriptModal form');
        var action = $(this).data('action');
        form.attr('action', action);
    });

</script>
@stop

