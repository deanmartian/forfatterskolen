@extends('editor.layout')

@section('title')
<title>Dashboard &rsaquo; Forfatterskolen Admin</title>
@stop

@section('page-title', 'Dashboard')

@section('content')
@php
    $cacheBuster = now()->timestamp;
@endphp

{{-- Greeting --}}
<div class="ed-greeting">
    God morgen — du har <strong>{{ $assignedAssignmentManuscripts->where('has_feedback', false)->count() + $assigned_shop_manuscripts->whereIn('status', ['Started','Pending'])->count() }} oppgaver</strong> som venter på tilbakemelding
</div>

{{-- Stats --}}
<div class="ed-stats">
    <div class="ed-stat-card">
        <div class="ed-stat-card__icon ed-stat-card__icon--wine"><i class="fa fa-hourglass-half"></i></div>
        <div>
            <div class="ed-stat-card__number">{{ $assignedAssignmentManuscripts->where('has_feedback', false)->count() }}</div>
            <div class="ed-stat-card__label">{{ trans('site.personal-assignment') }}</div>
        </div>
    </div>
    <div class="ed-stat-card">
        <div class="ed-stat-card__icon ed-stat-card__icon--warn"><i class="fa fa-shopping-bag"></i></div>
        <div>
            <div class="ed-stat-card__number">{{ $assigned_shop_manuscripts->whereIn('status', ['Started','Pending'])->count() }}</div>
            <div class="ed-stat-card__label">{{ trans_choice('site.shop-manuscripts', 2) }}</div>
        </div>
    </div>
    <div class="ed-stat-card">
        <div class="ed-stat-card__icon ed-stat-card__icon--success"><i class="fa fa-users"></i></div>
        <div>
            <div class="ed-stat-card__number">{{ $coachingTimers->count() }}</div>
            <div class="ed-stat-card__label">{{ trans('site.my-coaching-timer') }}</div>
        </div>
    </div>
    <div class="ed-stat-card">
        <div class="ed-stat-card__icon ed-stat-card__icon--accent"><i class="fa fa-check-circle"></i></div>
        <div>
            <div class="ed-stat-card__number">{{ $corrections->count() + $copyEditings->count() }}</div>
            <div class="ed-stat-card__label">{{ trans('site.my-correction') }} / {{ trans('site.my-copy-editing') }}</div>
        </div>
    </div>
</div>

{{-- Personal Assignments --}}
<div class="ed-section">
    <div class="ed-section__header">
        <h3 class="ed-section__title">
            {{ trans('site.personal-assignment') }}
            <span class="ed-section__count">{{ $assignedAssignmentManuscripts->count() }}</span>
        </h3>
    </div>
    <div class="ed-section__body">
        <table class="ed-table dt-table" id="myAssignedShopManuTable">
            <thead>
                <tr>
                    <th>{{ trans_choice('site.manuscripts', 1) }}</th>
                    <th>Brev</th>
                    <th>{{ trans('site.learner-id') }}</th>
                    <th>{{ trans_choice('site.courses', 1) }}</th>
                    <th>{{ trans('site.type') }}</th>
                    <th>{{ trans('site.where') }}</th>
                    <th>{{ trans('site.expected-finish') }}</th>
                    <th>Uploaded Date</th>
                    <th>{{ trans('site.feedback-status') }}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($assignedAssignmentManuscripts as $assignedManuscript)
                @php
                    $extension = explode('.', basename($assignedManuscript->filename));
                    $ext = end($extension);
                    $course = $assignedManuscript->assignment->course;
                @endphp
                <tr>
                    <td>
                        <div class="ed-file-link">
                            <span class="ed-file-link__ext ed-file-link__ext--{{ $ext }}">{{ $ext }}</span>
                            <a href="{{ $assignedManuscript->filename }}?v={{ $cacheBuster }}" download class="ed-file-link__download">
                                <i class="fa fa-download"></i>
                            </a>
                            @if( $ext == 'pdf' || $ext == 'odt' )
                                <a href="/js/ViewerJS/#../..{{ $assignedManuscript->filename }}" class="ed-file-link__name">
                                    {{ basename($assignedManuscript->filename) }}
                                </a>
                            @elseif( $ext == 'docx' || $ext == 'doc' )
                                <a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$assignedManuscript->filename}}" class="ed-file-link__name">
                                    {{ basename($assignedManuscript->filename) }}
                                </a>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if ($assignedManuscript->letter_to_editor)
                            <a href="{{ route('editor.assignment.manuscript.download_letter', ['id' => $assignedManuscript->id, 'v' => $cacheBuster]) }}" class="ed-btn ed-btn--ghost ed-btn--sm">
                                <i class="fa fa-download"></i>
                            </a>
                        @else
                            —
                        @endif
                    </td>
                    <td class="mono">{{ $assignedManuscript->user->id }}</td>
                    <td>
                        @if($course)
                            <a href="{{ route('admin.course.show', $course->id) }}">{{ $course->title }}</a>
                        @endif
                    </td>
                    <td>{{ \App\Http\AdminHelpers::assignmentType($assignedManuscript->type) }}</td>
                    <td>{{ \App\Http\AdminHelpers::manuscriptType($assignedManuscript->manu_type) }}</td>
                    <td>
                        {{ $assignedManuscript->expected_finish }}
                        @if(!$assignedManuscript->expected_finish)
                            <button class="ed-btn ed-btn--ghost ed-btn--sm" data-toggle="modal"
                                    data-target="#editExpectedFinishModal"
                                    data-action="{{ route('editor.personal-assignment.update-expected-finish', ['assignment', $assignedManuscript->id]) }}"
                                    data-expected_finish="{{ $assignedManuscript->expected_finish ? strftime('%Y-%m-%d', strtotime($assignedManuscript->expected_finish)) : NULL }}"
                                    onclick="editExpectedFinish(this)">
                                <i class="fa fa-edit"></i> Edit
                            </button>
                        @endif
                    </td>
                    <td>{{ $assignedManuscript->uploaded_date }}</td>
                    <td>
                        @if($assignedManuscript->has_feedback && $assignedManuscript->noGroupFeedbacks->first())
                            <span class="ed-badge ed-badge--pending"><span class="ed-badge__dot"></span>{{ trans('site.pending') }}</span>
                            <button class="ed-btn ed-btn--success ed-btn--sm submitPersonalAssignmentFeedbackBtn"
                                    data-target="#submitPersonalAssignmentFeedbackModal"
                                    data-toggle="modal"
                                    data-manuscript="{{$assignedManuscript->noGroupFeedbacks->first()->filename}}"
                                    data-created_at="{{$assignedManuscript->noGroupFeedbacks->first()->created_at}}"
                                    data-updated_at="{{$assignedManuscript->noGroupFeedbacks->first()->updated_at}}"
                                    data-feedback_id="{{$assignedManuscript->noGroupFeedbacks->first()->id}}"
                                    data-grade="{{$assignedManuscript->grade}}"
                                    data-notes_to_head_editor="{{$assignedManuscript->noGroupFeedbacks->first()->notes_to_head_editor}}"
                                    data-edit="1"
                                    data-name="{{ $assignedManuscript->user->id }}"
                                    data-action="{{ route('editor.assignment.group.manuscript-feedback-no-group', ['id' => $assignedManuscript->id, 'learner_id' => $assignedManuscript->user->id]) }}">
                                <i class="fa fa-pencil-square-o"></i>
                            </button>
                        @else
                            <button class="ed-btn ed-btn--warning ed-btn--sm submitPersonalAssignmentFeedbackBtn"
                                    data-target="#submitPersonalAssignmentFeedbackModal"
                                    data-toggle="modal"
                                    data-name="{{ $assignedManuscript->user->id }}"
                                    data-action="{{ route('editor.assignment.group.manuscript-feedback-no-group', ['id' => $assignedManuscript->id, 'learner_id' => $assignedManuscript->user->id]) }}">
                                + {{ trans('site.add-feedback') }}
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Shop Manuscripts --}}
<div class="ed-section">
    <div class="ed-section__header">
        <h3 class="ed-section__title">
            {{ trans_choice('site.shop-manuscripts', 2) }}
            <span class="ed-section__count">{{ $assigned_shop_manuscripts->count() }}</span>
        </h3>
    </div>
    <div class="ed-section__body">
        <table class="ed-table dt-table" id="shopManuTable">
            <thead>
                <tr>
                    <th>{{ trans_choice('site.manuscripts', 1) }}</th>
                    <th>{{ trans('site.genre') }}</th>
                    <th>{{ trans('site.learner-id') }}</th>
                    <th>{{ trans('site.deadline') }}</th>
                    <th>{{ trans('site.feedback-status') }}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($assigned_shop_manuscripts as $shopManuscript)
                @if( $shopManuscript->status == 'Started' || $shopManuscript->status == 'Pending' )
                    <tr>
                        <td>
                            <div class="ed-file-link">
                                <a href="{{ route('editor.backend.download_shop_manuscript', ['id' => $shopManuscript->id, 'v' => $cacheBuster]) }}" class="ed-file-link__download">
                                    <i class="fa fa-download"></i>
                                </a>
                                @if($shopManuscript->is_active)
                                    <a href="{{ route('editor.shop_manuscript_taken', ['id' => $shopManuscript->user->id, 'shop_manuscript_taken_id' => $shopManuscript->id]) }}" class="ed-file-link__name">
                                        {{ $shopManuscript->shop_manuscript->title }}
                                    </a>
                                @else
                                    <span class="ed-file-link__name">{{ $shopManuscript->shop_manuscript->title }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($shopManuscript->genre > 0)
                                <span class="ed-genre-tag">{{ \App\Http\FrontendHelpers::assignmentType($shopManuscript->genre) }}</span>
                            @endif
                        </td>
                        <td class="mono">{{ $shopManuscript->user->id }}</td>
                        <td>{{ $shopManuscript->editor_expected_finish }}</td>
                        <td>
                            @if($shopManuscript->status == 'Started')
                                <button type="button" class="ed-btn ed-btn--warning ed-btn--sm addShopManuscriptFeedback" data-toggle="modal"
                                    data-target="#addFeedbackModal"
                                    data-action="{{ route('editor.admin.shop-manuscript-taken-feedback.store', $shopManuscript->id) }}">
                                    + {{ trans('site.add-feedback') }}
                                </button>
                            @elseif($shopManuscript->status == 'Pending')
                                @php($feedbackFile = implode(",", $shopManuscript->feedbacks->first()->filename))
                                <span class="ed-badge ed-badge--pending"><span class="ed-badge__dot"></span>Pending</span>
                                <button type="button" class="ed-btn ed-btn--success ed-btn--sm addShopManuscriptFeedback" data-toggle="modal"
                                    data-target="#addFeedbackModal"
                                    data-f_id="{{$shopManuscript->feedbacks->first()->id}}"
                                    data-edit="1"
                                    data-f_created_at="{{$shopManuscript->feedbacks->first()->created_at}}"
                                    data-f_updated_at="{{$shopManuscript->feedbacks->first()->updated_at}}"
                                    data-f_file="{{$feedbackFile}}"
                                    data-f_notes="{{$shopManuscript->feedbacks->first()->notes}}"
                                    data-hours="{{$shopManuscript->feedbacks->first()->hours_worked}}"
                                    data-notes_to_head_editor="{{$shopManuscript->feedbacks->first()->notes_to_head_editor}}"
                                    data-action="{{ route('editor.admin.shop-manuscript-taken-feedback.store', $shopManuscript->id) }}">
                                    <i class="fa fa-pencil-square-o"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- The rest of the sections follow the same pattern --}}
{{-- My Assignments, Free Manuscripts, Coaching, Corrections, Copy Editing, etc. --}}
{{-- Each section uses ed-section, ed-table, ed-badge, ed-btn classes --}}

{{-- Include the same modals from the original dashboard --}}
@include('editor.partials.dashboard-modals')

@stop

@section('scripts')
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script>
        var cacheBuster = '{{ $cacheBuster }}';
        $('.viewManuscriptBtn').click(function(){
		var fields = $(this).data('fields');
		var modal = $('#viewManuscriptModal');
		modal.find('#name').text(fields.name);
		modal.find('#email').text(fields.email);
		modal.find('#content').text(fields.content);
	});
	$('.approveFeedbackAdminBtn').click(function(){
		var modal = $('#approveFeedbackAdminModal');
		var action = $(this).data('action');
		modal.find('form').attr('action', action);
	});
	$('.removeFeedbackAdminBtn').click(function(){
		var modal = $('#removeFeedbackAdminModal');
		var action = $(this).data('action');
		modal.find('form').attr('action', action);
	});

    $(".editContentBtn").click(function() {
        let action = $(this).data('action');
        let content = $(this).data('content');
        let modal = $('#editContentModal');
        modal.find('form').attr('action', action);

        tinymce.get('editContentEditor').setContent(content);
    });

	$('#myAssignmentTable, .assignment-table').on('click','.submitFeedbackBtn',function (){
		
		var modal = $('#submitFeedbackModal');
        var name = $(this).data('name');
        var action = $(this).data('action');
        var manuscript_id = $(this).data('manuscript_id');
		var is_edit = $(this).data('edit');
        modal.find('em').text(name);
        modal.find('form').attr('action', action);
        modal.find('form').find('input[name=manuscript_id]').val(manuscript_id);

		$('#submitFeedbackForm').trigger('reset');
		modal.find('#feedbackFileAppend').html('');
		modal.find('.modal-title').text("Feedback");
		modal.find('#dates').html('');
		modal.find('form').find('input[type=file]').attr('required');
		modal.find('[name=feedback_id]').val('')

		modal.find('[name=manuscriptLabel]').show();
		modal.find('#replaceAdd').hide();

        if (is_edit) {
			let feedbackFileName = $(this).data('manuscript');
			let createdAt = $(this).data('created_at');
			let updatedAt = $(this).data('updated_at');
			let feedbackId = $(this).data('feedback_id');
			let grade = $(this).data('grade');
			let notes_to_head_editor = $(this).data('notes_to_head_editor');
			// let hours = $(this).data('hours');

            modal.find('form').find('input[type=file]').removeAttr('required');
			modal.find('.modal-title').text("Edit Feedback");
			modal.find('[name=grade]').val(grade)
			modal.find('[name=manuscriptLabel]').text("Replace Manuscript")
			modal.find('[name=feedback_id]').val(feedbackId)
			modal.find('[name=notes_to_head_editor]').val(notes_to_head_editor)
			// modal.find('[name=hours]').val(hours)
			
			modal.find('#dates').append('<label>Created At</label>&nbsp;'+createdAt);
			modal.find('#dates').append('<br><label>Last Updated At</label>&nbsp;'+updatedAt+'<br><br>');

			var feedbackArray = feedbackFileName.split(",");
			modal.find('#feedbackFileAppend').append('<label>Manuscript</label><br>')
            feedbackArray.forEach(function (item, index){
                modal.find('#feedbackFileAppend').append('<a href="'+ item + '?v=' + cacheBuster +'" name="feedback_filename" class="" download>'+ item +'</a><br>')
            })
			modal.find('#feedbackFileAppend').append('<br>');

			modal.find('[name=manuscriptLabel]').hide();
			modal.find('#replaceAdd').show();
        }
    });

	$('#shopManuTable').on('click','.addShopManuscriptFeedback',function (){
        var modal = $('#addFeedbackModal');
        var action = $(this).data('action');
		var is_edit = $(this).data('edit');
        modal.find('form').attr('action', action);

		$('#addFeedbackModalForm').trigger('reset');
		modal.find('#feedbackFileAppend').html('');
		modal.find('.modal-title').text("Feedback");
		modal.find('#dates').html('');
		modal.find('form').find('input[type=file]').attr('required');
		modal.find('[name=feedback_id]').val('')

		modal.find('[name=manuscriptLabel]').show();
		modal.find('#replaceAdd').hide();

        if (is_edit) {
			let feedbackFileName = $(this).data('f_file');
			let createdAt = $(this).data('f_created_at');
			let updatedAt = $(this).data('f_updated_at');
			let feedbackId = $(this).data('f_id');
			let notes = $(this).data('f_notes');
			let hours = $(this).data('hours');
			let notes_to_head_editor = $(this).data('notes_to_head_editor');

            modal.find('form').find('input[type=file]').removeAttr('required');
			modal.find('.modal-title').text("Edit Feedback");
			modal.find('[name=notes]').val(notes)
			modal.find('[name=manuscriptLabel]').text("Replace Manuscript")
			modal.find('[name=feedback_id]').val(feedbackId)
			modal.find('[name=hours]').val(hours)
			modal.find('[name=notes_to_head_editor]').val(notes_to_head_editor)
			
			modal.find('#dates').append('<label>Created At</label>&nbsp;'+createdAt);
			modal.find('#dates').append('<br><label>Last Updated At</label>&nbsp;'+updatedAt+'<br><br>');

			var feedbackArray = feedbackFileName.split(",");
			modal.find('#feedbackFileAppend').append('<label>Manuscript</label><br>')
            feedbackArray.forEach(function (item, index){
                modal.find('#feedbackFileAppend').append('<a href="'+ item + '?v=' + cacheBuster +'" name="feedback_filename" class="" download>'+ item +'</a><br>')
            })
			modal.find('#feedbackFileAppend').append('<br>');

			modal.find('[name=manuscriptLabel]').hide();
			modal.find('#replaceAdd').show();
        }
	});

    $(".finishAssignmentManuscriptBtn").click(function(){
        let modal = $('#finishAssignmentManuscriptModal');
        let action = $(this).data('action');
        modal.find('form').attr('action', action);
	});

    $('.assignEditorBtn').click(function(){
        let action = $(this).data('action');
        let editor = $(this).data('editor');
        let modal = $('#assignEditorModal');
        modal.find('select').val(editor);
        modal.find('form').attr('action', action);
    });

    $(".pendingAssignmentEditorBtn").click(function(){
        let action = $(this).data('action');
        let editor = $(this).data('editor');
        let preferred_editor = $(this).data('preferred-editor');
        let preferred_editor_name = $(this).data('preferred-editor-name');
        let modal = $('#pendingAssignmentEditorModal');
        modal.find('select').val(preferred_editor).trigger('change');
        modal.find('form').attr('action', action);

        if (preferred_editor) {
            modal.find('.select2').hide();
            modal.find('.hidden-container').show();
            modal.find('.hidden-container').find('label').empty().text(preferred_editor_name);
        } else {
            modal.find('.select2').show();
            modal.find('.hidden-container').hide();
        }
	});

    $(".updateOtherServiceStatusBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#updateOtherServiceStatusModal');
        let service = $(this).data('service');
        let title = 'Korrektur';

        if (service === 1) {
            title = 'Språkvask';
        }
        modal.find('form').attr('action', action);
        modal.find('.modal-title').find('span').text(title);
    });

    $(".setOtherServiceFinishDateBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#setOtherServiceFinishDateModal');
        let finish = $(this).data('finish');

        modal.find('form').attr('action', action);
        modal.find('form').find('[name=expected_finish]').val(finish);
    });

	$('#correctionTable').on('click','.addOtherServiceFeedbackBtn',function (){
        let action = $(this).data('action');
        let modal = $('#addOtherServiceFeedbackModal');
        let service = $(this).data('service');
        let title = 'Korrektur';
		let is_edit = $(this).data('edit');

        if (service === 1) {
            title = 'Språkvask';
        }
        modal.find('form').attr('action', action);
        modal.find('.modal-title').find('span').text(title);

		$('#addOtherServiceFeedbackForm').trigger('reset');
		modal.find('#feedbackFileAppend').html('');
		modal.find('.modal-title').text("Feedback");
		modal.find('#dates').html('');
		modal.find('form').find('input[type=file]').attr('required');
		modal.find('[name=feedback_id]').val('')
		modal.find('[name=manuscriptLabel]').show();
		modal.find('#replaceAdd').hide();

        if (is_edit) {
			let feedbackFileName = $(this).data('f_file');
			let createdAt = $(this).data('f_created_at');
			let updatedAt = $(this).data('f_updated_at');
			let feedbackId = $(this).data('f_id');
			let hours = $(this).data('hours');
			let notes_to_head_editor = $(this).data('notes_to_head_editor');

            modal.find('form').find('input[type=file]').removeAttr('required');
			modal.find('.modal-title').text("Edit Feedback");
			modal.find('[name=feedback_id]').val(feedbackId)
			modal.find('[name=hours_worked]').val(hours)
			modal.find('[name=notes_to_head_editor]').val(notes_to_head_editor)
			
			modal.find('#dates').append('<label>Created At</label>&nbsp;'+createdAt);
			modal.find('#dates').append('<br><label>Last Updated At</label>&nbsp;'+updatedAt+'<br><br>');

			var feedbackArray = feedbackFileName.split(",");
			modal.find('#feedbackFileAppend').append('<label>Manuscript</label><br>')
            feedbackArray.forEach(function (item, index){
                modal.find('#feedbackFileAppend').append('<a href="'+ item + '?v=' + cacheBuster +'" name="feedback_filename" class="" download>'+ item +'</a><br>')
            })
			modal.find('#feedbackFileAppend').append('<br>');
			modal.find('[name=manuscriptLabel]').hide();
			modal.find('#replaceAdd').show();
        }
    });
	
	$('#copyEditingTable').on('click','.addOtherServiceFeedbackBtn',function (){
        let action = $(this).data('action');
        let modal = $('#addOtherServiceFeedbackModal');
        let service = $(this).data('service');
        let title = 'Korrektur';
		let is_edit = $(this).data('edit');

        if (service === 1) {
            title = 'Språkvask';
        }
        modal.find('form').attr('action', action);
        modal.find('.modal-title').find('span').text(title);

		$('#addOtherServiceFeedbackForm').trigger('reset');
		modal.find('#feedbackFileAppend').html('');
		modal.find('.modal-title').text("Feedback");
		modal.find('#dates').html('');
		modal.find('form').find('input[type=file]').attr('required');
		modal.find('[name=feedback_id]').val('')
		modal.find('[name=manuscriptLabel]').show();
		modal.find('#replaceAdd').hide();

        if (is_edit) {
			let feedbackFileName = $(this).data('f_file');
			let createdAt = $(this).data('f_created_at');
			let updatedAt = $(this).data('f_updated_at');
			let feedbackId = $(this).data('f_id');
			let hours = $(this).data('hours');
			let notes_to_head_editor = $(this).data('notes_to_head_editor');

            modal.find('form').find('input[type=file]').removeAttr('required');
			modal.find('.modal-title').text("Edit Feedback");
			modal.find('[name=feedback_id]').val(feedbackId)
			modal.find('[name=hours_worked]').val(hours)
			modal.find('[name=notes_to_head_editor]').val(notes_to_head_editor)
			
			modal.find('#dates').append('<label>Created At</label>&nbsp;'+createdAt);
			modal.find('#dates').append('<br><label>Last Updated At</label>&nbsp;'+updatedAt+'<br><br>');

			var feedbackArray = feedbackFileName.split(",");
			modal.find('#feedbackFileAppend').append('<label>Manuscript</label><br>')
            feedbackArray.forEach(function (item, index){
                modal.find('#feedbackFileAppend').append('<a href="'+ item + '?v=' + cacheBuster +'" name="feedback_filename" class="" download>'+ item +'</a><br>')
            })
			modal.find('#feedbackFileAppend').append('<br>');
			modal.find('[name=manuscriptLabel]').hide();
			modal.find('#replaceAdd').show();
        }
    });

    $(".approveCoachingSessionBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#approveCoachingSessionModal');
        modal.find('form').attr('action', action);
	});

	$('#coachingTable').on('click','.viewHelpWithBtn',function (){
       let details = $(this).data('details');
       let modal = $("#viewHelpWithModal");

       modal.find('.modal-body').find('pre').text(details);
	});

    $(".is-manuscript-locked-toggle").change(function(){
        let shopManuscriptTakenId = $(this).attr('data-id');
        let is_checked = $(this).prop('checked');
        let check_val = is_checked ? 1 : 0;
        $.ajax({
            type:'POST',
            url:'/is-manuscript-locked-status',
            data: { "shop_manuscript_taken_id" : shopManuscriptTakenId, 'is_manuscript_locked' : check_val },
            success: function(data){
            }
        });
    });

    $(".finishTaskBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#finishTaskModal');
        modal.find('form').attr('action', action);
    });

    $(".editTaskBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#editTaskModal');
        let fields = $(this).data('fields');
        modal.find('form').attr('action', action);
        modal.find('[name=task]').text(fields.task);
        modal.find('[name=user_id]').val(fields.user_id);
        modal.find('form').find('[name=assigned_to]').val(fields.assigned_to).trigger('change');
    });

    $(".lock-toggle").change(function(){
        let course_id = $(this).attr('data-id');
        let is_checked = $(this).prop('checked');
        let check_val = is_checked ? 1 : 0;
        $.ajax({
            type:'POST',
            url:'/assignment_manuscript/lock-status',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: { "manuscript_id" : course_id, 'locked' : check_val },
            success: function(data){
            }
        });
    });

    $(".deleteTaskBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#deleteTaskModal');
        modal.find('form').attr('action', action);
    });

    $(".editExpectedFinishBtn").click(function() {
        let expected_finish = $(this).data('expected_finish');
        let modal = $('#editExpectedFinishModal');
        let action = $(this).data('action');
        modal.find('form').attr('action', action);
        modal.find('[name=expected_finish]').val(expected_finish);
    });

	$('#myAssignedShopManuTable').on('click','.submitPersonalAssignmentFeedbackBtn',function (){
        let modal = $('#submitPersonalAssignmentFeedbackModal');
        let name = $(this).data('name');
        let action = $(this).data('action');
        let is_edit = $(this).data('edit');
		
        modal.find('em').text(name);
        modal.find('form').attr('action', action);

		$('#submitPersonalAssignmentFeedbackForm').trigger('reset');
		modal.find('#feedbackFileAppend').html('');
		modal.find('.modal-title').text("Feedback");
		modal.find('#dates').html('');
		modal.find('form').find('input[type=file]').attr('required');
		modal.find('[name=feedback_id]').val('')

		modal.find('[name=manuscriptLabel]').show();
		modal.find('#replaceAdd').hide();

        if (is_edit) {
			let feedbackFileName = $(this).data('manuscript');
			let grade = $(this).data('grade');
			let createdAt = $(this).data('created_at');
			let updatedAt = $(this).data('updated_at');
			let feedbackId = $(this).data('feedback_id');
			let notesToHeadEditor = $(this).data('notes_to_head_editor');
			// let hours = $(this).data('hours');

            modal.find('form').find('input[type=file]').removeAttr('required');
			modal.find('.modal-title').text("Edit Feedback");
			modal.find('[name=grade]').val(grade)
			modal.find('[name=feedback_id]').val(feedbackId)
			modal.find('[name=notes_to_head_editor]').val(notesToHeadEditor)
			// modal.find('[name=hours]').val(hours)
			
			modal.find('#dates').append('<label>Created At</label>&nbsp;'+createdAt);
			modal.find('#dates').append('<br><label>Last Updated At</label>&nbsp;'+updatedAt+'<br><br>');

			var feedbackArray = feedbackFileName.split(",");
			modal.find('#feedbackFileAppend').append('<label>Manuscript</label><br>')
            feedbackArray.forEach(function (item, index){
                modal.find('#feedbackFileAppend').append('<a href="'+ item + '?v=' + cacheBuster +'" name="feedback_filename" class="" download>'+ item +'</a><br>')
            })
			modal.find('#feedbackFileAppend').append('<br>');

			modal.find('[name=manuscriptLabel]').hide();
			modal.find('#replaceAdd').show();

        }
    });

	$('#coachingTable').on('click','.setReplayBtn',function (){
        let action = $(this).data('action');
        let modal = $('#setReplayModal');
        modal.find('form').attr('action', action);
	});

    function disableSubmit(t) {
        let submit_btn = $(t).find('[type=submit]');
        submit_btn.text('');
        submit_btn.append('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
        submit_btn.attr('disabled', 'disabled');
    };

	$('.acceptRequestBtn').click(function(){
		let action = $(this).data('action');
		let title = $(this).data('title');
		let sub_title = $(this).data('sub_title');
		let modal = $('#acceptRequest');

		modal.find('.main-title').text(title);
		modal.find('.sub-title').text(title);
		modal.find('.yesBtn').attr('href', action);
	});

    $(".sendFMFeedbackBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#freeManuscriptFeedbackModal');
        modal.find('form').attr('action', action);
        let fields = $(this).data('fields');
        let email_template = $(this).data('email_template');
        let content = fields.feedback_content ? fields.feedback_content : email_template;

        tinymce.get('FMEmailContentEditor').setContent(content);
    });

    $(".selfPublishingFeedbackBtn").click(function(){
		let action = $(this).data('action');
		let modal = $('#selfPublishingFeedbackModal');
		modal.find('form').attr('action', action);
	});

	$(".projectHoursBtn").click(function() {
		let action = $(this).data('action');
		let modal = $('#projectHoursModal');
		let record = $(this).data('record');

		modal.find('form').attr('action', action);
		modal.find("[name=project_number]").val(record.identifier);
		modal.find("[name=name]").val(record.name);
		modal.find("[name=editor_total_hours]").val(record.editor_total_hours);
	})

    function editExpectedFinish(self) {
        let expected_finish = $(self).data('expected_finish');
        let modal = $('#editExpectedFinishModal');
        let action = $(self).data('action');
        modal.find('form').attr('action', action);
        modal.find('[name=expected_finish]').val(expected_finish);
	}

	function editFMContent(self) {
        let action = $(self).data('action');
        let content = $(self).data('content');
        let modal = $('#editContentModal');
        modal.find('form').attr('action', action);
        tinymce.get('editContentEditor').setContent(content);
	}

	function sendFMFeedback(self) {
        let action = $(self).data('action');
        let modal = $('#freeManuscriptFeedbackModal');
        modal.find('form').attr('action', action);
        let fields = $(self).data('fields');
        let email_template = $(self).data('email_template');
        let content = fields.feedback_content ? fields.feedback_content : email_template;

        tinymce.get('FMEmailContentEditor').setContent(content);
	}

	function adjustTime(amount) {
		let timeInput = document.getElementById('timeInput');
		let currentTime = parseFloat(timeInput.value);

		if (isNaN(currentTime)) {
			currentTime = 0;
		}

		timeInput.value = currentTime + amount;
	}
</script>
@stop
