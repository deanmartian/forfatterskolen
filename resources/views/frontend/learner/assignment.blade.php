{{-- @extends('frontend.layout') --}}
@extends('frontend.layouts.course-portal')

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
	<style>
		.nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus {
			color: #555;
			cursor: default;
			background-color: #fff;
			border: 1px solid #ddd;
			border-bottom-color: transparent;
		}

		.nav-tabs {
			border-bottom: none;
		}

		.tab-content {
			border-top: 1px solid #dee2e6;
		}

		.editor-feedback-table > tbody > tr > td {
			padding: 1.5rem 1.5rem 0 1.5rem;
		}

		.editor-feedback-table > tbody > tr:last-child > td {
			padding-bottom: 1.5rem;
		}

        /* Media Queries */
        @media only screen and (max-width: 500px) {
            .nav-tabs {
                display: inline-grid;
				padding-left: 10px;
            }
        }
	</style>
@stop

@section('title')
<title>Assignments &rsaquo; Forfatterskolen</title>
@stop

@section('content')

	<div class="learner-container learner-assignment" id="app-container">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					@php
						$tabWithLabel = [
							[
								'name' => 'waiting-for-feedback',
								'label' => 'Venter på tilbakemelding'
							],
							[
								'name' => 'feedback-from-editor',
								'label' => trans('site.learner.feedback-from-editor')
							],
							[
								'name' => 'groups',
								'label' => trans('site.learner.groups')
							],
							[
								'name' => 'no-word-limit',
								'label' => trans('site.editing-year-course')
							]
						]
					@endphp

					<ul class="nav nav-tabs margin-top">
						<li @if(!in_array(Request::input('tab'), array_column($tabWithLabel, 'name'))) class="active" @endif>
							<a href="?tab=assignment">
								{{ trans('site.upcoming-assignment') }}
							</a>
						</li>

						@foreach($tabWithLabel as $tab)
							<li @if( Request::input('tab') == $tab['name'] ) class="active" @endif>
								<a href="?tab={{ $tab['name'] }}">
									{{ $tab['label'] }}
								</a>
							</li>
						@endforeach
					</ul>

					<div class="tab-content">
						<div class="tab-pane fade in active">
							@if( Request::input('tab') == 'waiting-for-feedback' )
								@include('frontend.partials.assignment._waiting_for_feedback')
							@elseif( Request::input('tab') == 'feedback-from-editor' )
								@include('frontend.partials.assignment._feedback_from_editor')
							@elseif( Request::input('tab') == 'groups' )
								@include('frontend.partials.assignment._group')
								{{-- <group-assignment :learners="{{ json_encode($assignmentGroupLearners) }}" 
								:current-user="{{ json_encode(Auth::user()) }}"></group-assignment> --}}
							@elseif( Request::input('tab') == 'upcoming' )
								<div class="row past-assignment grid mt-5">
									@foreach($upcomingPersonalAssignments as $assignment)
										<div class="col-md-6 mb-5 grid-item">
										<div class="card">
											<div class="card-header py-4">
												<div class="row">
													<div class="col-md-9">
														<h2><i class="contract-sign"></i> {{ $assignment->title }}</h2>
													</div>
												</div> <!-- end row-->
											</div> <!-- end card-header -->
											<div class="card-body">
												<p>
													{{ $assignment->description }}
												</p>

												<p>
													{{ trans('site.max-words') }}: {{ $assignment->max_words }}
												</p>

												<span class="font-barlow-regular">{{ trans('site.deadline') }}:</span>
												<span>{{ \App\Http\FrontendHelpers::formatDateTimeNor2($assignment->submission_date) }}</span>

											</div> <!-- end card-body -->
										</div> <!-- end card -->
									</div> <!-- end grid-item -->
									@endforeach
								</div>
							@elseif( Request::input('tab') == 'no-word-limit' )
								@include('frontend.partials.assignment._no_word_limit')
							@else
								@include('frontend.partials.assignment._current')
							@endif
						</div> <!-- end tab-pane-->
					</div> <!-- tab-content -->
				</div> <!-- end col-sm-12 -->
			</div> <!-- end row -->
		</div> <!-- end container -->
	</div> <!-- end learner-container -->

<div id="submitSuccessModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-body text-center">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <div style="color: green; font-size: 24px"><i class="fa fa-check"></i></div>
			  <p>
				  {{ trans('site.learner.submit-success-text') }}
			  </p>
		  </div>
		</div>
	</div>
</div>

<div id="errorMaxword" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body text-center">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<div style="color: red; font-size: 24px"><i class="fa fa-close"></i></div>
				<p>
					{{ strtr(trans('site.learner.error-max-word-text'),
                    ['_word_count_' => Session::get('editorMaxWord')]) }}
				</p>
			</div>
		</div>
	</div>
</div>

<div id="submitEditorManuscriptModal" class="modal fade new-global-modal" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">
					{{ trans('site.learner.upload-script') }}
				</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="" enctype="multipart/form-data"
				onsubmit="disableSubmit(this);">
					{{ csrf_field() }}
					<div class="form-group">
                                                <div class="file-upload" id="file-upload-area">
                                                        <i class="fa fa-cloud-upload-alt"></i>
                                                        <div class="file-upload-text" id="file-upload-text-editor-manu">
                                                                Drag and drop files or <a href="javascript:void(0)" class="file-upload-btn">Klikk her</a>
                                                        </div>
                                                        <input type="file" class="form-control hidden input-file-upload" name="filename"
                                                        id="file-upload" accept="application/msword,
                                                application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                                                  </div>
                                                <div class="alert alert-info manuscript-conversion-message d-none mt-3">
                                                        Konverterer dokumentet… Vennligst vent.
                                                </div>
                                                <div class="alert alert-danger manuscript-conversion-error d-none mt-3"></div>
                                                <label class="file-label">
                                                        * {{ trans('site.learner.manuscript.doc-format-text') }}
                                                </label>
                                        </div>

					<div class="form-group">
						<label>
							{{ trans('site.front.genre') }}
						</label>
						<select class="form-control" name="type" required>
							<option value="" disabled="disabled" selected>
								{{ trans('site.front.select-genre') }}
							</option>
							@foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
								<option value="{{ $type->id }}"> {{ $type->name }} </option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<label class="d-block">
							{{ trans('site.learner.manuscript.where-in-manuscript') }}
						</label>
						@foreach(\App\Http\FrontendHelpers::manuscriptType() as $manu)
							<div class="custom-radio">
								<input type="radio" name="manu_type" value="{{ $manu['id'] }}" id="{{ $manu['id'] }}" required>
								<label for="{{ $manu['id'] }}">
									{{ $manu['option'] }}
								</label>
							</div>
							{{-- <input type="radio" name="manu_type" value="{{ $manu['id'] }}" required> <label>{{ $manu['option'] }}</label> <br> --}}
						@endforeach
					</div>

					<div class="join-question-container hide">
						<div class="form-group">
							<label>{{ trans('site.learner.join-group-question') }}?</label> <br>
							<input type="checkbox" data-toggle="toggle" data-on="Ja" data-off="Nei" data-size="small" name="join_group">
						</div>
					</div>

					<div class="form-group letter-to-editor hide">
						<label>
							{{ trans('site.letter-to-editor') }}
						</label>
                                                <input type="file" class="form-control margin-top" name="letter_to_editor" accept="application/msword,
                                        application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                        application/vnd.oasis.opendocument.text,application/pdf">
					</div>

					<button type="submit" class="btn btn-primary submit-btn pull-right">
						{{ trans('site.front.upload') }}
					</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="submitManuscriptModal" class="modal fade new-global-modal" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
		  <div class="modal-header">
		    <h3 class="modal-title">
				{{ trans('site.learner.upload-script') }}
			</h3>
			  <button type="button" class="close" data-dismiss="modal">&times;</button>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this);">
		      	{{ csrf_field() }}
				<div class="form-group">
                                        <div class="file-upload" id="file-upload-area-submit-manu">
                                                <i class="fa fa-cloud-upload-alt"></i>
                                                <div class="file-upload-text">
                                                        Drag and drop files or <a href="javascript:void(0)" class="file-upload-btn">Klikk her</a>
                                                </div>
                                                <input type="file" class="form-control hidden input-file-upload" name="filename"
                                                id="file-upload" accept=".doc,.docx,.pdf,.odt,.pages,application/msword,
                                        application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf,
                                        application/vnd.oasis.opendocument.text,application/vnd.apple.pages,application/x-iwork-pages-sffpages">
                                          </div>
                                        <div class="alert alert-info manuscript-conversion-message d-none mt-3">
                                                Konverterer dokumentet… Vennligst vent.
                                        </div>
                                        <div class="alert alert-danger manuscript-conversion-error d-none mt-3"></div>
                                        <label class="file-label">
                                                * {{ trans('site.learner.manuscript.doc-format-text') }}
                                        </label>
                                </div>

				<div class="form-group">
					<label>
						{{ trans('site.front.genre') }}
					</label>
					<select class="form-control" name="type" required>
						<option value="" disabled="disabled" selected>
							{{ trans('site.front.select-genre') }}
						</option>
						@foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
							<option value="{{ $type->id }}"> {{ $type->name }} </option>
						@endforeach
					</select>
				</div>

				<div class="form-group">
					<label class="d-block">
						{{ trans('site.learner.manuscript.where-in-manuscript') }}
					</label>
					@foreach(\App\Http\FrontendHelpers::manuscriptType() as $manu)
						<div class="custom-radio">
							<input type="radio" name="manu_type" value="{{ $manu['id'] }}" id="submit-manu-{{ $manu['id'] }}" required>
							<label for="submit-manu-{{ $manu['id'] }}">
								{{ $manu['option'] }}
							</label>
						</div>
						{{-- <input type="radio" name="manu_type" value="{{ $manu['id'] }}" required> <label>{{ $manu['option'] }}</label> <br> --}}
					@endforeach
				</div>

				<div class="join-question-container hide">
					<div class="form-group">
						<label>{{ trans('site.learner.join-group-question') }}?</label> <br>
						<input type="checkbox" data-toggle="toggle" data-on="Ja" data-off="Nei" data-size="small" name="join_group">
					</div>
				</div>

				<div class="form-group letter-to-editor hide">
					<label>
						{{ trans('site.letter-to-editor') }}
					</label>
                                        <input type="file" class="form-control margin-top" name="letter_to_editor" accept="application/msword,
                                        application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                        application/vnd.oasis.opendocument.text,application/pdf">
				</div>

		      	<button type="submit" class="btn btn-primary submit-btn pull-right">
					{{ trans('site.front.upload') }}
				</button>
		      	<div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>

<div id="editManuscriptModal" class="modal new-global-modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">
					{{ trans('site.learner.manuscript.replace-manuscript') }}
				</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
                                                <div class="file-upload" id="file-upload-area-edit-manu">
                                                        <i class="fa fa-cloud-upload-alt"></i>
                                                        <div class="file-upload-text">
                                                                Drag and drop files or <a href="javascript:void(0)" class="file-upload-btn">Klikk her</a>
                                                        </div>
                                                        <input type="file" class="form-control hidden input-file-upload" name="filename"
                                                        id="file-upload" accept=".doc,.docx,.pdf,.odt,.pages,application/msword,
                                                application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf,
                                                application/vnd.oasis.opendocument.text,application/vnd.apple.pages,application/x-iwork-pages-sffpages">
                                                  </div>
                                                <div class="alert alert-info manuscript-conversion-message d-none mt-3">
                                                        Konverterer dokumentet… Vennligst vent.
                                                </div>
                                                <div class="alert alert-danger manuscript-conversion-error d-none mt-3"></div>
                                                <label class="file-label">
                                                        * {{ trans('site.learner.manuscript.doc-format-text') }}
                                                </label>
                                        </div>

					<button type="submit" class="btn btn-primary submit-btn pull-right">
						{{ trans('site.front.submit') }}
					</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="deleteManuscriptModal" class="modal new-global-modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">
					<i class="far fa-flag"></i>
				</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<h3>
					{{ trans('site.learner.delete-manuscript.title') }}
				</h3>
				<p>
					{{ trans('site.learner.delete-manuscript.question') }}
				</p>
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<button type="submit" class="btn btn-danger submit-btn pull-right margin-top">
						{{ trans('site.learner.delete') }}
					</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

	<div id="editLetterModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title">
						{{ trans('site.learner.manuscript.replace-manuscript') }}
					</h3>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
						{{ csrf_field() }}
						<div class="form-group">
							<label>
								{{ trans('site.letter-to-editor') }}
							</label>
                                                        <input type="file" class="form-control" required name="filename"
                                                                   accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
							* {{ trans('site.learner.manuscript.doc-pdf-odt-text') }}
						</div>

						<button type="submit" class="btn btn-primary pull-right">
							{{ trans('site.front.submit') }}
						</button>
						<div class="clearfix"></div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div id="submitFeedbackModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                  <h3 class="modal-title">{{ trans('site.learner.submit-feedback-to') }} <em></em></h3>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                  <form method="POST" action=""  enctype="multipart/form-data">
                      {{ csrf_field() }}
                      <div class="form-group">
                          <label>* {{ trans('site.learner.manuscript.doc-pdf-odt-text') }}</label>
                          <input type="file" class="form-control margin-top" required multiple name="filename[]"
                                 accept=".doc,.docx,.pdf,.odt,.pages,application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                 application/pdf, application/vnd.oasis.opendocument.text,application/vnd.apple.pages,application/x-iwork-pages-sffpages">
                      </div>
    
                      <button type="submit" class="btn btn-primary pull-right">{{ trans('site.front.submit') }}</button>
                      <div class="clearfix"></div>
                </form>
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
@stop

@section('scripts')
	<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.js"></script>
	<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
	<script src="{{ asset('/js/app.js?v='.time()) }}"></script>
<script>

    // call the function once fully loaded
    $(window).on('load', function() {
        /* $('.grid').masonry({
            // options
            itemSelector : '.grid-item'
        }); */

		const groupLearnerGroupId = '{{ $assignmentGroupLearners->count() ? $assignmentGroupLearners[0]->group->id : "" }}';
		if (groupLearnerGroupId) {
			console.log("inside if");
			showGroupDetails(groupLearnerGroupId);
		}
    });

	@if (Session::has('success'))
	$('#submitSuccessModal').modal('show');
	@endif

	@if (Session::has('errorMaxWord'))
		$('#errorMaxword').modal('show');
    @endif

	@if(Session::has('manuscript_test_error'))
    	$('#manuscriptTestErrorModal').modal('show');
	@endif


        setupFileUpload('file-upload-area');
        setupFileUpload('file-upload-area-submit-manu');
        setupFileUpload('file-upload-area-edit-manu');

	$('.submitManuscriptBtn').click(function(){
		let form = $('#submitManuscriptModal').find("form");
        let action = $(this).data('action');
        let show_group_question = $(this).data('show-group-question');
        let send_letter_to_editor = $(this).data('send-letter-to-editor');
		form.attr('action', action);

		if (show_group_question) {
		    form.find('.join-question-container').removeClass('hide');
		} else {
            form.find('.join-question-container').addClass('hide');
		}

		if (send_letter_to_editor) {
            form.find('.letter-to-editor').removeClass('hide');
		} else {
            form.find('.letter-to-editor').addClass('hide');
		}
	});

    $('.submitEditorManuscriptBtn').click(function(){
        let form = $('#submitEditorManuscriptModal').find("form");
        let action = $(this).data('action');
        let show_group_question = $(this).data('show-group-question');
        let send_letter_to_editor = $(this).data('send-letter-to-editor');
        form.attr('action', action);

        if (show_group_question) {
            form.find('.join-question-container').removeClass('hide');
        } else {
            form.find('.join-question-container').addClass('hide');
        }

        if (send_letter_to_editor) {
            form.find('.letter-to-editor').removeClass('hide');
        } else {
            form.find('.letter-to-editor').addClass('hide');
        }
    });

    $('.editManuscriptBtn').click(function(){
        let form = $('#editManuscriptModal form');
        let action = $(this).data('action');
        form.attr('action', action);
    });

    $('.deleteManuscriptBtn').click(function(){
        let form = $('#deleteManuscriptModal form');
        let action = $(this).data('action');
        form.attr('action', action)
    });

    $(".editLetterBtn").click(function() {
        let form = $('#editLetterModal').find('form');
        let action = $(this).data('action');
        form.attr('action', action)
	});

	function submitFeedbackFromGroup(self) {
		var modal = $('#submitFeedbackModal');
		var name = $(self).data('name');
		var action = $(self).data('action');
		modal.find('em').text(name);
		modal.find('form').attr('action', action);
	}

	function editFeedbackFromGroup(self) {
		let form = $('#editManuscriptModal form');
        let action = $(self).data('action');
        form.attr('action', action);
	}

	function deleteFeedbackFromGroup(self) {
		let form = $('#deleteManuscriptModal form');
        let action = $(self).data('action');
        form.attr('action', action);
	}

        function getCsrfToken() {
                const tokenElement = document.querySelector('meta[name="csrf-token"]');
                return tokenElement ? tokenElement.getAttribute('content') : null;
        }

        async function parseErrorBlob(blob) {
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
        }

        function createDocxFileName(originalName) {
                if (!originalName || typeof originalName !== 'string') {
                        return 'document.docx';
                }

                const dotIndex = originalName.lastIndexOf('.');

                if (dotIndex <= 0) {
                        return originalName.toLowerCase().endsWith('.docx') ? originalName : originalName + '.docx';
                }

                const baseName = originalName.substring(0, dotIndex);
                const extension = originalName.substring(dotIndex + 1).toLowerCase();

                if (extension === 'docx') {
                        return originalName;
                }

                return baseName + '.docx';
        }

        function extractFilenameFromContentDisposition(header) {
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
        }

        async function convertFileToDocx(file) {
                const formData = new FormData();
                formData.append('document', file);

                const csrfToken = getCsrfToken();

                if (csrfToken) {
                        formData.append('_token', csrfToken);
                }

                const fallbackName = createDocxFileName(file && file.name ? file.name : null);
                const mimeType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

                if (window.axios) {
                        try {
                                const response = await window.axios.post('/documents/convert-to-docx', formData, {
                                        responseType: 'blob',
                                        headers: csrfToken ? { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' } : { 'X-Requested-With': 'XMLHttpRequest' },
                                });

                                const headers = response.headers || {};
                                const contentDisposition = headers['content-disposition'] || headers['Content-Disposition'] || null;
                                const filename = extractFilenameFromContentDisposition(contentDisposition) || fallbackName;
                                const responseBlob = response.data instanceof Blob ? response.data : new Blob(response.data ? [response.data] : [], { type: mimeType });

                                return new File([responseBlob], filename, { type: mimeType, lastModified: Date.now() });
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
                                                        manuscript: ['Kunne ikke konvertere filen. Prøv igjen.']
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

                const contentDisposition = response.headers ? (response.headers.get('content-disposition') || response.headers.get('Content-Disposition')) : null;

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
                                                manuscript: ['Kunne ikke konvertere filen. Prøv igjen.']
                                        },
                                        message: 'Kunne ikke konvertere filen. Prøv igjen.'
                                }
                        };

                        throw error;
                }

                const data = await response.blob();
                const filename = extractFilenameFromContentDisposition(contentDisposition) || fallbackName;
                const responseBlob = data instanceof Blob ? data : new Blob([data], { type: mimeType });

                return new File([responseBlob], filename, { type: mimeType, lastModified: Date.now() });
        }

        function getFileExtension(filename) {
                if (!filename || typeof filename !== 'string') {
                        return '';
                }

                const parts = filename.split('.');
                return parts.length > 1 ? parts.pop().toLowerCase() : '';
        }

        function getErrorMessageFromConversion(error) {
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

                if (error.message && error.message.trim() !== '') {
                        return error.message;
                }

                return 'Kunne ikke konvertere filen. Prøv igjen.';
        }

        function assignFilesToInput(input, file) {
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
        }

        function setFormConversionState(form, isConverting) {
                if (!form) {
                        return;
                }

                const messageElement = form.querySelector('.manuscript-conversion-message');

                if (messageElement) {
                        if (isConverting) {
                                messageElement.classList.remove('d-none');
                        } else {
                                messageElement.classList.add('d-none');
                        }
                }

                const submitButton = form.querySelector('button[type="submit"]');

                if (submitButton) {
                        submitButton.disabled = !!isConverting;
                }
        }

        function showConversionError(form, message) {
                if (!form) {
                        window.alert(message);
                        return;
                }

                const errorElement = form.querySelector('.manuscript-conversion-error');

                if (errorElement) {
                        errorElement.textContent = message;
                        errorElement.classList.remove('d-none');
                } else {
                        window.alert(message);
                }
        }

        function clearConversionError(form) {
                if (!form) {
                        return;
                }

                const errorElement = form.querySelector('.manuscript-conversion-error');

                if (errorElement) {
                        errorElement.textContent = '';
                        errorElement.classList.add('d-none');
                }
        }

        function resetFileInput(input) {
                if (!input) {
                        return;
                }

                try {
                        input.value = '';
                } catch (error) {
                        input.value = null;
                }
        }

        function setupFileUpload(area) {
                const fileUploadArea = document.getElementById(area);

                if (!fileUploadArea) {
                        return;
                }

                const fileInput = fileUploadArea.querySelector('.input-file-upload');
                const fileUploadText = fileUploadArea.querySelector('.file-upload-text');
                const form = fileUploadArea.closest('form');
                const textWithBrowseButton = 'Drag and drop files or <a href="javascript:void(0)" class="file-upload-btn">Klikk her</a>';

                const openFileInput = () => {
                        if (fileInput) {
                                fileInput.click();
                        }
                };

                const attachBrowseButtonHandlers = () => {
                        if (!fileUploadArea) {
                                return;
                        }

                        const browseButtons = fileUploadArea.querySelectorAll('.file-upload-btn');

                        browseButtons.forEach((button) => {
                                if (button.dataset.handlerAttached === 'true') {
                                        return;
                                }

                                const handleBrowseInteraction = (event) => {
                                        if (event) {
                                                event.preventDefault();
                                        }

                                        openFileInput();
                                };

                                button.addEventListener('click', handleBrowseInteraction);
                                button.addEventListener('mousedown', handleBrowseInteraction);
                                button.dataset.handlerAttached = 'true';
                        });
                };

                const updateText = (text) => {
                        if (fileUploadText) {
                                fileUploadText.innerHTML = text;
                                attachBrowseButtonHandlers();
                        }
                };

                const handleFiles = async (files) => {
                        if (!fileInput) {
                                return;
                        }

                        if (!files || !files.length) {
                                clearConversionError(form);
                                setFormConversionState(form, false);
                                updateText(textWithBrowseButton);
                                return;
                        }

                        let selectedFile = files[0];

                        if (!selectedFile) {
                                clearConversionError(form);
                                setFormConversionState(form, false);
                                updateText(textWithBrowseButton);
                                return;
                        }

                        const extension = getFileExtension(selectedFile.name || '');

                        clearConversionError(form);

                        if (extension !== 'docx') {
                                setFormConversionState(form, true);

                                try {
                                        selectedFile = await convertFileToDocx(selectedFile);
                                } catch (error) {
                                        showConversionError(form, getErrorMessageFromConversion(error));
                                        resetFileInput(fileInput);
                                        updateText(textWithBrowseButton);
                                        setFormConversionState(form, false);
                                        return;
                                }

                                setFormConversionState(form, false);
                        }

                        const assigned = assignFilesToInput(fileInput, selectedFile);

                        if (!assigned) {
                                showConversionError(form, 'Kunne ikke oppdatere filen etter konvertering. Prøv en annen nettleser.');
                                resetFileInput(fileInput);
                                updateText(textWithBrowseButton);
                                setFormConversionState(form, false);
                                return;
                        }

                        updateText(selectedFile.name || textWithBrowseButton);
                        clearConversionError(form);
                        setFormConversionState(form, false);
                };

                fileUploadArea.addEventListener('dragover', (e) => {
                        e.preventDefault();
                        fileUploadArea.classList.add('dragover');
                        updateText('Release to upload');
                });

                fileUploadArea.addEventListener('dragleave', () => {
                        fileUploadArea.classList.remove('dragover');
                        updateText(textWithBrowseButton);
                });

                fileUploadArea.addEventListener('drop', (e) => {
                        e.preventDefault();
                        fileUploadArea.classList.remove('dragover');
                        const files = e.dataTransfer ? e.dataTransfer.files : null;
                        handleFiles(files);
                });

                fileUploadArea.addEventListener('click', (event) => {
                        openFileInput(event);
                });

                attachBrowseButtonHandlers();

                if (fileInput) {
                        fileInput.addEventListener('change', (event) => {
                                handleFiles(event.target.files);
                        });
                }

                const modal = fileUploadArea.closest('.modal');

                if (modal) {
                        const submitButton = modal.querySelector('[type=submit]');

                        if (submitButton) {
                                submitButton.addEventListener('click', function (e) {
                                        if (!fileInput || !fileInput.files || !fileInput.files.length) {
                                                alert('Please select a document file.');
                                                e.preventDefault();
                                        }
                                });
                        }
                }

                updateText(textWithBrowseButton);
        }

	function showGroupDetails(group_id) {
		$(".group-container").removeClass('active');
		$("#group-"+group_id).addClass('active');

		$.ajax({
			type: "GET",
			url: "/account/assignment/group/" + group_id + "/show-details",
			beforeSend: function() {
				$("#loading-wrapper").removeClass('d-none');
			},
			success:function(data) {
				$("#loading-wrapper").addClass('d-none');
				$("#group-details-container").html(data);
			}
		});
	}
</script>
@stop

