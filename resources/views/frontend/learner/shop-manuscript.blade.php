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
                        <small class="form-text text-muted conversion-status d-none"></small>
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
                                                <small class="form-text text-muted conversion-status d-none"></small>
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

    (function($) {
        const MANUSCRIPT_INPUT_SELECTOR = '#uploadManuscriptModal input[name="manuscript"], #updateUploadedManuscriptModal input[name="manuscript"]';
        const CONVERSION_MESSAGES = {
            start: 'Konverterer dokumentet… Vennligst vent.',
            success: 'Konvertering fullført. Filen er klar til opplasting.',
            failure: 'Kunne ikke konvertere filen. Prøv igjen.'
        };

        if (typeof axios === 'undefined') {
            return;
        }

        $(document).on('change', MANUSCRIPT_INPUT_SELECTOR, function() {
            handleManuscriptChange(this).catch(function(error) {
                console.error('Manuskriptkonvertering feilet', error);
            });
        });

        function getFileExtension(file) {
            if (!file || !file.name) {
                return '';
            }

            var parts = file.name.split('.');
            return parts.length > 1 ? parts.pop().toLowerCase() : '';
        }

        function createDocxFileName(originalName) {
            if (!originalName || typeof originalName !== 'string') {
                return 'document.docx';
            }

            var dotIndex = originalName.lastIndexOf('.');

            if (dotIndex <= 0) {
                return originalName.toLowerCase().endsWith('.docx') ? originalName : originalName + '.docx';
            }

            var baseName = originalName.substring(0, dotIndex);
            var extension = originalName.substring(dotIndex + 1).toLowerCase();

            if (extension === 'docx') {
                return originalName;
            }

            return baseName + '.docx';
        }

        function extractFilenameFromContentDisposition(header) {
            if (!header || typeof header !== 'string') {
                return null;
            }

            var utf8Match = header.match(/filename\*=UTF-8''([^;]+)/i);
            if (utf8Match && utf8Match[1]) {
                try {
                    return decodeURIComponent(utf8Match[1]);
                } catch (error) {
                    console.error('Klarte ikke å dekode UTF-8 filnavn', error);
                }
            }

            var quotedMatch = header.match(/filename="?([^";]+)"?/i);
            if (quotedMatch && quotedMatch[1]) {
                return quotedMatch[1];
            }

            return null;
        }

        function createDataTransfer() {
            if (typeof DataTransfer !== 'undefined') {
                return new DataTransfer();
            }

            if (typeof ClipboardEvent !== 'undefined') {
                try {
                    var clipboard = new ClipboardEvent('');
                    if (clipboard.clipboardData) {
                        return clipboard.clipboardData;
                    }
                } catch (error) {
                    console.warn('ClipboardEvent er ikke tilgjengelig for å opprette DataTransfer', error);
                }
            }

            return null;
        }

        function assignFileToInput(input, file) {
            var dataTransfer = createDataTransfer();

            if (!dataTransfer) {
                return null;
            }

            dataTransfer.items.add(file);
            input.files = dataTransfer.files;

            return file;
        }

        function setStatus($element, message, type) {
            if (!$element.length) {
                return;
            }

            $element.removeClass('d-none text-muted text-success text-danger');

            if (type === 'success') {
                $element.addClass('text-success');
            } else if (type === 'error') {
                $element.addClass('text-danger');
            } else {
                $element.addClass('text-muted');
            }

            $element.text(message);
        }

        function setStatusHtml($element, html, type) {
            if (!$element.length) {
                return;
            }

            $element.removeClass('d-none text-muted text-success text-danger');

            if (type === 'success') {
                $element.addClass('text-success');
            } else if (type === 'error') {
                $element.addClass('text-danger');
            } else {
                $element.addClass('text-muted');
            }

            $element.html(html);
        }

        function clearStatus($element) {
            if (!$element.length) {
                return;
            }

            $element.addClass('d-none text-muted');
            $element.removeClass('text-success text-danger');
            $element.text('');
        }

        async function parseErrorBlob(blob) {
            if (!blob || typeof blob.text !== 'function') {
                return null;
            }

            try {
                var text = await blob.text();

                if (!text) {
                    return null;
                }

                try {
                    return JSON.parse(text);
                } catch (error) {
                    return { message: text };
                }
            } catch (error) {
                console.error('Kunne ikke lese feilrespons', error);
                return null;
            }
        }

        async function convertFileToDocx(file) {
            var formData = new FormData();
            formData.append('document', file);

            try {
                var response = await axios.post('/documents/convert-to-docx', formData, {
                    responseType: 'blob'
                });

                var contentDisposition = response.headers ? response.headers['content-disposition'] : null;
                var fallbackName = createDocxFileName(file && file.name ? file.name : null);
                var filename = extractFilenameFromContentDisposition(contentDisposition) || fallbackName;
                var mimeType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                var responseBlob = response.data;
                var docxBlob = responseBlob instanceof Blob ? responseBlob : new Blob(responseBlob ? [responseBlob] : [], { type: mimeType });

                return new File([docxBlob], filename, { type: mimeType, lastModified: Date.now() });
            } catch (error) {
                if (error && error.response && error.response.data instanceof Blob) {
                    try {
                        var parsed = await parseErrorBlob(error.response.data);
                        if (parsed) {
                            error.response.data = parsed;
                        }
                    } catch (parseError) {
                        console.error('Kunne ikke tolke feilrespons fra konvertering', parseError);
                    }
                }

                throw error;
            }
        }

        async function getConversionErrorMessage(error) {
            if (!error || !error.response) {
                return CONVERSION_MESSAGES.failure;
            }

            var data = error.response.data;

            if (data && typeof data === 'object' && !(data instanceof Blob)) {
                if (data.errors && data.errors.manuscript && data.errors.manuscript.length) {
                    return data.errors.manuscript[0];
                }

                if (data.message) {
                    return data.message;
                }
            }

            if (typeof data === 'string') {
                return data;
            }

            return CONVERSION_MESSAGES.failure;
        }

        async function handleManuscriptChange(input) {
            if (!input || input.dataset.converting === 'true') {
                return;
            }

            var files = input.files;
            var file = files && files[0] ? files[0] : null;
            var $input = $(input);
            var $status = $input.closest('.form-group').find('.conversion-status');
            var $form = $input.closest('form');
            var $submit = $form.find('button[type="submit"]');

            if (!file) {
                clearStatus($status);
                return;
            }

            var extension = getFileExtension(file);

            if (extension === 'docx') {
                clearStatus($status);
                return;
            }

            input.dataset.converting = 'true';
            $input.prop('disabled', true);
            $submit.prop('disabled', true);
            setStatus($status, CONVERSION_MESSAGES.start, 'info');

            try {
                var convertedFile = await convertFileToDocx(file);
                var assignedFile = assignFileToInput(input, convertedFile);

                if (!assignedFile) {
                    var downloadUrl = URL.createObjectURL(convertedFile);
                    setStatusHtml(
                        $status,
                        'Filen ble konvertert til DOCX. <a href="' + downloadUrl + '" download="' + convertedFile.name + '">Last ned filen</a> og velg den manuelt.',
                        'info'
                    );
                    setTimeout(function() {
                        URL.revokeObjectURL(downloadUrl);
                    }, 60000);
                    return;
                }

                setStatus($status, CONVERSION_MESSAGES.success + ' (' + convertedFile.name + ')', 'success');
            } catch (error) {
                var message = await getConversionErrorMessage(error);
                setStatus($status, message, 'error');
                input.value = '';
            } finally {
                $input.prop('disabled', false);
                $submit.prop('disabled', false);
                delete input.dataset.converting;
            }
        }
    })(jQuery);

</script>
@stop

