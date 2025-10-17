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
                                accept=".doc,.docx,.odt,.pdf,.pages">
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
                                accept=".doc,.docx,.odt,.pdf,.pages">
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
                                                accept=".doc,.docx,.odt,.pdf,.pages">
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
                                                accept=".doc,.docx,.odt,.pdf,.pages">
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

        (function(){
            const manuscriptForms = document.querySelectorAll('#uploadManuscriptModal form, #updateUploadedManuscriptModal form');

            if (!manuscriptForms.length) {
                return;
            }

            const docxMimeType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

            const showConversionError = (message) => {
                const text = (message && typeof message === 'string' && message.trim() !== '')
                    ? message
                    : 'Kunne ikke konvertere filen. Prøv igjen.';

                if (window.toastr && typeof window.toastr.error === 'function') {
                    window.toastr.error(text);
                    return;
                }

                if (window.toasted && window.toasted.global && typeof window.toasted.global.showErrorMsg === 'function') {
                    window.toasted.global.showErrorMsg({ message: text });
                    return;
                }

                window.alert(text);
            };

            const createDocxFileName = (originalName) => {
                if (!originalName || typeof originalName !== 'string') {
                    return 'document.docx';
                }

                const dotIndex = originalName.lastIndexOf('.');

                if (dotIndex <= 0) {
                    return originalName.toLowerCase().endsWith('.docx')
                        ? originalName
                        : `${originalName}.docx`;
                }

                const baseName = originalName.substring(0, dotIndex);
                const extension = originalName.substring(dotIndex + 1).toLowerCase();

                if (extension === 'docx') {
                    return originalName;
                }

                return `${baseName}.docx`;
            };

            const extractFilenameFromContentDisposition = (header) => {
                if (!header || typeof header !== 'string') {
                    return null;
                }

                const utf8Match = header.match(/filename\*=UTF-8''([^;]+)/i);
                if (utf8Match && utf8Match[1]) {
                    try {
                        return decodeURIComponent(utf8Match[1]);
                    } catch (error) {
                        console.error('Failed to decode UTF-8 filename', error);
                    }
                }

                const quotedMatch = header.match(/filename="?([^";]+)"?/i);
                if (quotedMatch && quotedMatch[1]) {
                    return quotedMatch[1];
                }

                return null;
            };

            const parseErrorBlob = async (blob) => {
                if (!blob || typeof blob.text !== 'function') {
                    return null;
                }

                const text = await blob.text();

                if (!text) {
                    return null;
                }

                try {
                    return JSON.parse(text);
                } catch (error) {
                    return { message: text };
                }
            };

            const getCsrfToken = () => {
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');

                if (!csrfMeta) {
                    return null;
                }

                const token = csrfMeta.getAttribute('content');

                return typeof token === 'string' && token.trim() !== '' ? token : null;
            };

            const getFileExtension = (name) => {
                if (!name || typeof name !== 'string') {
                    return '';
                }

                const parts = name.split('.');
                return parts.length > 1 ? parts.pop().toLowerCase() : '';
            };

            const assignFilesToInput = (input, file) => {
                if (!input || !file) {
                    return false;
                }

                const files = Array.isArray(file) ? file : [file];

                try {
                    if (typeof DataTransfer !== 'undefined') {
                        const dataTransfer = new DataTransfer();
                        files.forEach((item) => dataTransfer.items.add(item));
                        input.files = dataTransfer.files;
                        return true;
                    }
                } catch (error) {
                    console.warn('DataTransfer is not available for file assignment.', error);
                }

                try {
                    if (typeof ClipboardEvent !== 'undefined') {
                        const clipboardEvent = new ClipboardEvent('');
                        if (clipboardEvent.clipboardData) {
                            files.forEach((item) => clipboardEvent.clipboardData.items.add(item));
                            input.files = clipboardEvent.clipboardData.files;
                            return true;
                        }
                    }
                } catch (error) {
                    console.warn('ClipboardEvent fallback failed for file assignment.', error);
                }

                return false;
            };

            const setFormConvertingState = (form, isConverting) => {
                const submitButton = form ? form.querySelector('button[type="submit"]') : null;

                if (submitButton) {
                    if (isConverting) {
                        if (!submitButton.dataset.originalText) {
                            submitButton.dataset.originalText = submitButton.innerHTML;
                        }
                        submitButton.disabled = true;
                        submitButton.innerHTML = 'Konverterer…';
                    } else {
                        submitButton.disabled = false;
                        if (submitButton.dataset.originalText) {
                            submitButton.innerHTML = submitButton.dataset.originalText;
                        }
                    }
                }
            };

            const getErrorMessageFromConversion = (error) => {
                if (!error) {
                    return 'Kunne ikke konvertere filen. Prøv igjen.';
                }

                if (error.response && error.response.data) {
                    const data = error.response.data;

                    if (data.errors && data.errors.manuscript && data.errors.manuscript.length) {
                        return data.errors.manuscript[0];
                    }

                    if (typeof data.message === 'string' && data.message.trim() !== '') {
                        return data.message;
                    }
                }

                if (typeof error.message === 'string' && error.message.trim() !== '') {
                    return error.message;
                }

                return 'Kunne ikke konvertere filen. Prøv igjen.';
            };

            const convertFileToDocx = async (file) => {
                const formData = new FormData();
                formData.append('document', file);

                const csrfToken = getCsrfToken();

                if (csrfToken) {
                    formData.append('_token', csrfToken);
                }

                if (window.axios) {
                    try {
                        const headers = csrfToken
                            ? { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }
                            : { 'X-Requested-With': 'XMLHttpRequest' };
                        const response = await window.axios.post('/documents/convert-to-docx', formData, {
                            responseType: 'blob',
                            headers,
                        });

                        const responseHeaders = response.headers || {};
                        const contentDisposition = responseHeaders['content-disposition'] || responseHeaders['Content-Disposition'] || null;
                        const filename = extractFilenameFromContentDisposition(contentDisposition) || createDocxFileName(file && file.name ? file.name : null);
                        const responseBlob = response.data instanceof Blob
                            ? response.data
                            : new Blob(response.data ? [response.data] : [], { type: docxMimeType });

                        return new File([responseBlob], filename, { type: docxMimeType, lastModified: Date.now() });
                    } catch (error) {
                        if (error && error.response && error.response.data instanceof Blob) {
                            try {
                                const parsed = await parseErrorBlob(error.response.data);
                                if (parsed) {
                                    error.response.data = parsed;
                                }
                            } catch (parseError) {
                                console.error('Failed to parse conversion error response', parseError);
                            }
                        }

                        if (!error.response || !error.response.data) {
                            error.response = error.response || {};
                            error.response.data = {
                                errors: {
                                    manuscript: ['Kunne ikke konvertere filen. Prøv igjen.'],
                                },
                                message: 'Kunne ikke konvertere filen. Prøv igjen.'
                            };
                        }

                        throw error;
                    }
                }

                const headers = { 'X-Requested-With': 'XMLHttpRequest' };

                if (csrfToken) {
                    headers['X-CSRF-TOKEN'] = csrfToken;
                }

                const response = await fetch('/documents/convert-to-docx', {
                    method: 'POST',
                    body: formData,
                    headers,
                });

                const contentDisposition = response.headers
                    ? (response.headers.get('content-disposition') || response.headers.get('Content-Disposition'))
                    : null;

                if (!response.ok) {
                    const error = new Error('Kunne ikke konvertere filen. Prøv igjen.');
                    let errorData = null;

                    try {
                        errorData = await response.clone().json();
                    } catch (jsonError) {
                        try {
                            errorData = { message: await response.text() };
                        } catch (textError) {
                            errorData = null;
                        }
                    }

                    error.response = {
                        status: response.status,
                        data: errorData || {
                            errors: {
                                manuscript: ['Kunne ikke konvertere filen. Prøv igjen.'],
                            },
                            message: 'Kunne ikke konvertere filen. Prøv igjen.'
                        }
                    };

                    throw error;
                }

                const data = await response.blob();
                const filename = extractFilenameFromContentDisposition(contentDisposition) || createDocxFileName(file && file.name ? file.name : null);
                const responseBlob = data instanceof Blob ? data : new Blob([data], { type: docxMimeType });

                return new File([responseBlob], filename, { type: docxMimeType, lastModified: Date.now() });
            };

            manuscriptForms.forEach((form) => {
                let submittingAfterConversion = false;

                form.addEventListener('submit', async (event) => {
                    if (submittingAfterConversion) {
                        submittingAfterConversion = false;
                        return;
                    }

                    const fileInput = form.querySelector('input[name="manuscript"]');

                    if (!fileInput || !fileInput.files || !fileInput.files.length) {
                        return;
                    }

                    const [file] = fileInput.files;
                    const extension = getFileExtension(file && file.name ? file.name : fileInput.value);

                    if (extension === 'docx') {
                        return;
                    }

                    event.preventDefault();
                    setFormConvertingState(form, true);

                    try {
                        const convertedFile = await convertFileToDocx(file);
                        const assigned = assignFilesToInput(fileInput, convertedFile);

                        if (!assigned) {
                            showConversionError('Kunne ikke konvertere filen. Prøv igjen.');
                            setFormConvertingState(form, false);
                            return;
                        }

                        submittingAfterConversion = true;
                        setFormConvertingState(form, false);
                        form.submit();
                    } catch (error) {
                        const message = getErrorMessageFromConversion(error);
                        showConversionError(message);
                        setFormConvertingState(form, false);
                    }
                });
            });
        })();
</script>
@stop

