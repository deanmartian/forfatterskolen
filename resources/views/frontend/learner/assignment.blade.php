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
	</style>
@stop

@section('title', "Assignments &rsaquo; Forfatterskolen")

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
						id="file-upload" accept="application/msword,
					application/vnd.openxmlformats-officedocument.wordprocessingml.document">
					  </div>
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
							id="file-upload" accept="application/msword,
						application/vnd.openxmlformats-officedocument.wordprocessingml.document">
						  </div>
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
                                 accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                 application/pdf, application/vnd.oasis.opendocument.text">
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

	function setupFileUpload(area) {
		const fileUploadArea = document.getElementById(area);
		const fileInput = fileUploadArea.querySelector('.input-file-upload');
		const fileUploadText = fileUploadArea.querySelector('.file-upload-text');

		// Function to open the file input dialog when the file-upload-area is clicked
		const openFileInput = () => {
			fileInput.click();
		};

		// Function to update the file upload text
		const updateText = (text) => {
			fileUploadText.innerHTML = text;
		};

		// Function to check if the file input is not empty
		const isFileInputNotEmpty = () => {
			return fileInput.files.length > 0;
		};

		fileUploadArea.querySelector('.file-upload-btn').addEventListener('mousedown', (e) => {
			// Check if the mousedown event was triggered by the button inside file-upload-area
			if (e.target.classList.contains('file-upload-btn')) {
				openFileInput();
			}
		});

		// Add a click event for the file-upload-btn in the current modal
		fileUploadArea.querySelector('.file-upload-btn').addEventListener('click', openFileInput);

		const textWithBrowseButton = 'Drag and drop files or <a href="javascript:void(0)" class="file-upload-btn">Klikk her</a>';

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

			const files = e.dataTransfer.files;

			for (let i = 0; i < files.length; i++) {
				console.log('Dropped file:', files[i].name);
			}

			fileInput.files = files;

			const selectedText = isFileInputNotEmpty() ? fileInput.files[0].name : textWithBrowseButton;
			updateText(selectedText);
		});

		fileInput.addEventListener('change', () => {
			const selectedText = isFileInputNotEmpty() ? fileInput.files[0].name : textWithBrowseButton;
			updateText(selectedText);
		});

		// Add a click event for the file-upload-area to open the file input dialog
		fileUploadArea.addEventListener('click', openFileInput);

		// Add a click event for the submit button in the current modal
		fileUploadArea.closest('.modal').querySelector('[type=submit]').addEventListener('click', function (e) {
			if (!isFileInputNotEmpty()) {
				alert('Please select a document file.');
				e.preventDefault();
			}
		});
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

