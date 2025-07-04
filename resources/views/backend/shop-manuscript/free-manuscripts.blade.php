@extends('backend.layout')

@section('title')
<title>Shop Manuscripts &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
	<style>
		.btn-xs {
			margin-bottom: 5px;
		}

		.table-responsive {
			overflow-y: scroll;
		}

	</style>
@stop

@section('content')
<div class="page-toolbar">
	<h3><i class="fa fa-file-text-o"></i> {{ trans('site.free-manuscripts') }}</h3>
	<a href="#" data-toggle="modal" class="freeManuscriptEmailTemplateBtn loadScriptButton" data-target="#freeManuscriptEmailTemplate"
	   data-fields="{{ json_encode($emailTemplate) }}" data-action="{{ route('admin.manuscript.edit_email_template', $emailTemplate->id) }}">
		{{ trans('site.email-template') }}</a> |
	<a href="#" data-toggle="modal" class="freeManuscriptEmailTemplateBtn loadScriptButton" data-target="#freeManuscriptEmailTemplate"
	   data-fields="{{ json_encode($emailTemplate2) }}" data-action="{{ route('admin.manuscript.edit_email_template', $emailTemplate2->id) }}">
		{{ trans('site.email-template') }} 2</a>
</div>

<div class="col-md-12">

	<ul class="nav nav-tabs margin-top">
		<li @if( Request::input('tab') != 'archive' ) class="active" @endif><a href="?tab=new">{{ trans('site.new') }}</a></li>
		<li @if( Request::input('tab') == 'archive' ) class="active" @endif><a href="?tab=archive">{{ trans('site.archive') }}</a></li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane fade in active">
			@if( Request::input('tab') != 'archive' )

				<div class="table-users table-responsive">
					<table class="table">
						<thead>
						<tr>
							<th>{{ trans('site.name') }}</th>
							<th>{{ trans('site.genre') }}</th>
							<th>{{ trans_choice('site.emails', 1) }}</th>
							<th>From</th>
							<th>Has Paid Course</th>
							<th width="600">{{ trans('site.content') }}</th>
							<th>{{ trans('site.deadline') }}</th>
							<th>{{ trans('site.date-received') }}</th>
							<th>{{ trans_choice('site.editors', 1) }}</th>
							<th></th>
						</tr>
						</thead>

						<tbody>
						@foreach( $freeManuscripts as $freeManuscript )
							<tr>
								<td>{{ $freeManuscript->name }}</td>
								<td>{{ \App\Http\AdminHelpers::assignmentType($freeManuscript->genre) }}</td>
								<td>{{ $freeManuscript->email }}</td>
								<td>
									{{ $freeManuscript->from }}
								</td>
								<td>{{ $freeManuscript->hasPaidCourse ? 'Yes' : 'No' }}</td>
								<td>
									{{ \Illuminate\Support\Str::limit(strip_tags($freeManuscript->content), 120) }}<br>
									<a href="#editContentModal" data-toggle="modal" class="loadScriptButton editContentBtn"
									data-content="{{ $freeManuscript->content }}"
									data-action="{{ route('admin.free-manuscript.edit-content', $freeManuscript->id) }}">
										Her kan du også nå putte in ekstra tekst
									</a>
								</td>
								<td>{{ $freeManuscript->deadline_date }}</td>
								<td>{{ \App\Http\FrontendHelpers::formatDate($freeManuscript->created_at) }}</td>
								<td>@if( $freeManuscript->editor ) {{ $freeManuscript->editor->full_name }} @endif</td>
								<td>
									@if( $freeManuscript->editor )
										<button class="btn btn-xs btn-success sendFeedbackBtn loadScriptButton" data-toggle="modal" data-target="#feedbackModal"
												data-fields="{{ json_encode($freeManuscript) }}"
												data-action="{{ route('admin.free-manuscript.send_feedback', $freeManuscript->id) }}"
												data-email_template="{{ $freeManuscript->from === 'Giutbok'
																? $emailTemplate2->email_content
																: $emailTemplate->email_content }}">{{ trans('site.send-back-feedback') }}</button>
									@endif
									<button class="btn btn-xs btn-primary viewManuscriptBtn" data-toggle="modal" data-target="#viewManuscriptModal" data-fields="{{ json_encode($freeManuscript) }}"
									data-genre="{{ $freeManuscript->genre ? \App\Http\FrontendHelpers::assignmentType($freeManuscript->genre): '' }}"
									data-content="{{ html_entity_decode($freeManuscript->content) }}">{{ trans('site.view') }}</button>
									<button class="btn btn-xs btn-warning assignEditorBtn" data-toggle="modal" data-target="#assignEditorModal" data-action="{{ route('admin.free-manuscript.assign_editor', $freeManuscript->id) }}" data-editor="{{ $freeManuscript->editor_id }}">{{ trans('site.assign-editor') }}</button>
									<button class="btn btn-xs btn-danger deleteManuscriptBtn" data-toggle="modal" data-target="#deleteManuscriptModal" data-fields="{{ json_encode($freeManuscript) }}" data-action="{{ route('admin.free-manuscript.delete', $freeManuscript->id) }}">{{ trans('site.delete') }}</button>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>

			@else
				<div class="row" style="margin-right: 0;">
					<div class="navbar-form navbar-right">
						<div class="form-group">
							<form role="search" method="GET">
								<input type="hidden" name="tab" value="archive">
								<div class="input-group">
									<input type="text" class="form-control" name="search" value="{{Request::input('search')}}" placeholder="{{ trans('site.search-email') }}..">
									<span class="input-group-btn">
							<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
						</span>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="table-users table-responsive">
					<table class="table">
						<thead>
						<tr>
							<th>{{ trans('site.name') }}</th>
							<th>{{ trans_choice('site.emails', 1) }}</th>
							<th>From</th>
							<th>Has Paid Course</th>
							<th width="500">{{ trans('site.content') }}</th>
							<th>{{ trans('site.date-sent') }}</th>
							<th>{{ trans_choice('site.editors', 1) }}</th>
							<th></th>
						</tr>
						</thead>

						<tbody>
						@foreach( $archiveManuscripts as $freeManuscript )
							<tr>
								<td>{{ $freeManuscript->name }}</td>
								<td>{{ $freeManuscript->email }}</td>
								<td>{{ $freeManuscript->from ?: 'FS' }}</td>
								<td>{{ $freeManuscript->hasPaidCourse ? 'Yes' : 'No' }}</td>
								<td>{{ \Illuminate\Support\Str::limit(strip_tags($freeManuscript->content), 120) }}</td>
								<td class="text-center">
									{{ $freeManuscript->latestFeedbackHistory['date_sent'] }} <br>
									@if($freeManuscript->latestFeedbackHistory['date_sent'])
										<a href="#freeManuscriptFeedbackHistoryModal"
										data-toggle="modal"
										data-manuscript-id="{{ $freeManuscript->id }}"
										class="viewFreeManucriptFeedbackHistoryBtn">History</a>
									@endif
								</td>
								<td>@if( $freeManuscript->editor ) {{ $freeManuscript->editor->full_name }} @endif</td>
								<td>
									@if($freeManuscript->followUpEmail)
										<button class="btn btn-xs btn-success viewFollowUpBtn" data-toggle="modal"
												data-target="#viewFollowUpModal"
												data-fields="{{ json_encode($freeManuscript->followUpEmail) }}">
											View Follow up email
										</button>
									@endif
									<button class="btn btn-xs btn-success viewFeedbackBtn" data-toggle="modal" data-target="#viewFeedbackModal" data-fields="{{ json_encode($freeManuscript) }}">{{ trans('site.view-feedback') }}</button>
									<button class="btn btn-xs btn-primary viewManuscriptBtn" data-toggle="modal" data-target="#viewManuscriptModal" data-fields="{{ json_encode($freeManuscript) }}"
											data-genre="{{ $freeManuscript->genre ? \App\Http\FrontendHelpers::assignmentType($freeManuscript->genre): '' }}"
											data-content="{{ html_entity_decode($freeManuscript->content) }}">{{ trans('site.view') }}</button>
									<button class="btn btn-xs btn-warning assignEditorBtn" data-toggle="modal" data-target="#assignEditorModal" data-action="{{ route('admin.free-manuscript.assign_editor', $freeManuscript->id) }}" data-editor="{{ $freeManuscript->editor_id }}">{{ trans('site.assign-editor') }}</button>
									<button class="btn btn-xs btn-danger deleteManuscriptBtn" data-toggle="modal" data-target="#deleteManuscriptModal" data-fields="{{ json_encode($freeManuscript) }}" data-action="{{ route('admin.free-manuscript.delete', $freeManuscript->id) }}">{{ trans('site.delete') }}</button>
									<button class="btn btn-xs btn-info resendFeedbackBtn" data-toggle="modal" data-target="#resendFeedbackModal" data-action="{{ route('admin.free-manuscript.resend-feedback', $freeManuscript->id) }}">{{ trans('site.resend') }}</button>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>

				<div class="pull-right">{{$archiveManuscripts->render()}}</div>

			@endif
		</div>
	</div>
</div>

<div id="assignEditorModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-body">
		  	<form method="POST" action="" onsubmit="disableSubmit(this)">
		  		{{ csrf_field() }}
		  		<div class="form-group">
		  			<label>{{ trans('site.assign-editor') }}</label>
		  			<select name="editor_id" class="form-control">
		  				@foreach( \App\Http\AdminHelpers::editorList() as $editor )
		  				<option value="{{ $editor->id }}">{{ $editor->full_name }}</option>
		  				@endforeach
		  			</select>
		  		</div>
		  		<div class="text-right">
		  			<button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
		  		</div>
		  	</form>
		  </div>
		</div>
	</div>
</div>


<div id="viewManuscriptModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-body">
		  	<p>
		  		<strong>{{ trans('site.name') }}:</strong><br />
		  		<span id="name"></span><br />
		  		<br />
		  		<strong>{{ trans_choice('site.emails', 1) }}:</strong><br />
		  		<span id="email"></span><br />
		  		<br />
				<strong>{{ trans('site.genre') }}:</strong><br />
				<span id="genre"></span><br />
				<br />
		  		<strong>{{ trans_choice('site.manuscripts', 1) }}:</strong><br />
		  		<span id="content"></span>
		  	</p>
		  </div>
		</div>
	</div>
</div>

<div id="deleteManuscriptModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">{{ trans('site.delete-free-manuscript') }}</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="" onsubmit="disableSubmit(this)">
		      {{ csrf_field() }}
				{{ trans('site.delete-free-manuscript-question') }}
		      <button type="submit" class="btn btn-danger pull-right margin-top">{{ trans('site.delete') }}</button>
		      <div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>

<div id="feedbackModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{{ trans('site.send-feedback') }}</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="" id="sendFeedbackForm" onsubmit="disableSubmit(this)">
                    {{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.body') }}</label>
						<textarea name="email_content" cols="30" rows="10" class="form-control tinymce" id="FMEmailContentEditor" required></textarea>
					</div>
                    <div class="clearfix"></div>
                    <button type="submit" class="btn btn-success pull-right margin-top" id="sendFeedbackEmail">{{ trans('site.send') }}</button>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="viewFollowUpModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">View Follow up Email</h4>
			</div>
			<div class="modal-body">
			</div>
		</div>
	</div>
</div>

<div id="viewFeedbackModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.view-feedback') }}</h4>
			</div>
			<div class="modal-body">
			</div>
		</div>
	</div>
</div>

<div id="freeManuscriptEmailTemplate" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.email-template') }}</h4>
			</div>
			<div class="modal-body">
                <?php

                if ($isUpdate) {
                    $route = route($emailTemplateRoute, ['id' => $emailTemplate->id]);
                } else {
                    $route = route($emailTemplateRoute);
                }

                ?>
				<form method="POST" action="<?php echo e($route); ?>" onsubmit="disableSubmit(this)" novalidate>
                    <?php echo e(csrf_field()); ?>


                    <?php if($isUpdate): ?>
						<?php echo e(method_field('PUT')); ?>
					<?php endif; ?>
					<input type="hidden" name="from_email" value="">
					<div class="form-group">
						<label>{{ trans('site.body') }}</label>
						<textarea name="email_content" cols="30" rows="10" class="form-control tinymce" required
						id="freeManuscriptEmailContent"></textarea>
					</div>

					<input type="hidden" name="page_name" value="">

					<button type="submit" class="btn btn-primary pull-right">{{ trans('site.save') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="freeManuscriptFeedbackHistoryModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.email-template') }}</h4>
			</div>
			<div class="modal-body">
			</div>
		</div>

	</div>
</div>


<div id="resendFeedbackModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.resend-feedback') }}</h4>
			</div>
			<div class="modal-body">
				<form action="" method="POST" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<p>
						{{ trans('site.resend-feedback-question') }}
					</p>
					<button class="btn btn-primary pull-right">{{ trans('site.resend') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="editContentModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.edit-content') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.content') }}</label>
						<textarea name="manu_content" cols="30" rows="10" class="form-control tinymce" id="editContentEditor" required>

						</textarea>
					</div>
					<div class="clearfix"></div>
					<button type="submit" class="btn btn-success pull-right margin-top">{{ trans('site.save') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')
<script type="text/javascript">
    $('.viewManuscriptBtn').click(function(){
		var fields = $(this).data('fields');
		let genre = $(this).data('genre');
		let content = $(this).data('content');
		var modal = $('#viewManuscriptModal');
		modal.find('#name').text(fields.name);
		modal.find('#email').text(fields.email);
        modal.find('#genre').text(genre);
		modal.find('#content').empty().append(content);
	});

	$('.deleteManuscriptBtn').click(function(){
		var action = $(this).data('action');
		var modal = $('#deleteManuscriptModal');
		modal.find('form').attr('action', action);
	});

	$(".editContentBtn").click(function() {
        let action = $(this).data('action');
        let content = $(this).data('content');
        let modal = $('#editContentModal');
        modal.find('form').attr('action', action);

		setTimeout(() => {
			setEditorContent('editContentEditor', content);
		}, 500);

        //tinymce.get('editContentEditor').setContent(content);
	});

	$(".sendFeedbackBtn").click(function(){
        var action = $(this).data('action');
        var modal = $('#feedbackModal');
        modal.find('form').attr('action', action);
        let email_template = $(this).data('email_template');
        let fields = $(this).data('fields');
        let content = fields.feedback_content ? fields.feedback_content : email_template;
        //tinymce.get('FMEmailContentEditor').setContent(content);
		setTimeout(() => {
			setEditorContent('FMEmailContentEditor', content);
		}, 500);
    });

	$(".freeManuscriptEmailTemplateBtn").click(function() {
	    let modal = $("#freeManuscriptEmailTemplate");
        let action = $(this).data('action');
        let fields = $(this).data('fields');

        modal.find('form').attr('action', action);
        modal.find('input[name=from_email]').val(fields.from_email);
        modal.find('input[name=page_name]').val(fields.page_name);
        let content = fields.email_content;
        //tinymce.get('freeManuscriptEmailContent').setContent(content);
		setTimeout(() => {
			setEditorContent('freeManuscriptEmailContent', content);
		}, 500);
	});

	$('.assignEditorBtn').click(function(){
		var action = $(this).data('action');
		var editor = $(this).data('editor');
		var modal = $('#assignEditorModal');
		modal.find('select').val(editor);
		modal.find('form').attr('action', action);
	});

	$("#sendFeedbackEmail").click(function(){
        $(this).attr('disabled', true);
        $(this).text('Please wait...');
        $("#sendFeedbackForm").submit();
	});

    $(".viewFollowUpBtn").click(function(){
        let fields = $(this).data('fields');
        let modal = $('#viewFollowUpModal');
        modal.find('.modal-body').empty();
        modal.find('.modal-body').append(fields.message);
    });

	$(".viewFeedbackBtn").click(function(){
        var fields = $(this).data('fields');
        var modal = $('#viewFeedbackModal');
        modal.find('.modal-body').empty();
        modal.find('.modal-body').append(fields.feedback_content);
	});

	$(".viewFreeManucriptFeedbackHistoryBtn").click(function(){
	   var manuscript_id = $(this).data('manuscript-id');
        var modal = $("#freeManuscriptFeedbackHistoryModal");
        modal.find('.modal-body').empty();

        $.get('/free-manuscript/'+manuscript_id+'/feedback-history', function(response){

            if (response.success) {

                var history = '';
				history += '<ul>';
                $.each(response.data, function(k,v) {

                    history += '<li>'+v.date_sent+'</li>';

				});
                history += '</ul>';

                modal.find('.modal-body').append(history);

			} else {
                modal.find('.modal-body').append('<p>'+ response.data +'</p>');
			}
        });
	});

	$(".resendFeedbackBtn").click(function(){
	   var action = $(this).data('action'),
	   modal = $("#resendFeedbackModal");
	   modal.find('form').attr('action', action);
	});

    function disableSubmit(t) {
        let submit_btn = $(t).find('[type=submit]');
        submit_btn.text('');
        submit_btn.append('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
        submit_btn.attr('disabled', 'disabled');
    }

</script>
@stop