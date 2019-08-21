@extends('backend.layout')

@section('title')
<title>Shop Manuscripts &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
	<style>
		.btn-xs {
			margin-bottom: 5px;
		}
	</style>
@stop

@section('content')
<div class="page-toolbar">
	<h3><i class="fa fa-file-text-o"></i> {{ trans('site.free-manuscripts') }}</h3>
	<a href="#" data-toggle="modal" data-target="#freeManuscriptEmailTemplate"> {{ trans('site.email-template') }}</a>
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
							<th>{{ trans_choice('site.emails', 1) }}</th>
							<th width="600">{{ trans('site.content') }}</th>
							<th>{{ trans('site.date-received') }}</th>
							<th>{{ trans_choice('site.editors', 1) }}</th>
							<th></th>
						</tr>
						</thead>

						<tbody>
						@foreach( $freeManuscripts as $freeManuscript )
							<tr>
								<td>{{ $freeManuscript->name }}</td>
								<td>{{ $freeManuscript->email }}</td>
								<td>
									{{ str_limit(strip_tags($freeManuscript->content), 120) }}<br>
									<a href="#editContentModal" data-toggle="modal" class="editContentBtn"
									data-content="{{ $freeManuscript->content }}"
									data-action="{{ route('admin.free-manuscript.edit-content', $freeManuscript->id) }}">
										Her kan du også nå putte in ekstra tekst
									</a>
								</td>
								<td>{{ \App\Http\FrontendHelpers::formatDate($freeManuscript->created_at) }}</td>
								<td>@if( $freeManuscript->editor ) {{ $freeManuscript->editor->full_name }} @endif</td>
								<td>
									@if( $freeManuscript->editor )
										<button class="btn btn-xs btn-success sendFeedbackBtn" data-toggle="modal" data-target="#feedbackModal" data-fields="{{ json_encode($freeManuscript) }}" data-action="{{ route('admin.free-manuscript.send_feedback', $freeManuscript->id) }}">{{ trans('site.send-back-feedback') }}</button>
									@endif
									<button class="btn btn-xs btn-primary viewManuscriptBtn" data-toggle="modal" data-target="#viewManuscriptModal" data-fields="{{ json_encode($freeManuscript) }}"
									data-genre="{{ $freeManuscript->genre ? \App\Http\FrontendHelpers::assignmentType($freeManuscript->genre): '' }}"
									data-content="{{ html_entity_decode($freeManuscript->content) }}">{{ trans('site.view') }}</button>
									<button class="btn btn-xs btn-warning assignEditorBtn" data-toggle="modal" data-target="#assignEditorModal" data-action="{{ route('admin.free-manuscript.assign_editor', $freeManuscript->id) }}" data-editor="{{ $freeManuscript->editor_id }}">{{ trans('site.assign-editor') }}</button>
									<button class="btn btn-xs btn-danger deleteManuscriptBtn" data-toggle="modal" data-target="#deleteManuscriptModal" data-fields="{{ json_encode($freeManuscript) }}" data-action="{{ route('admin.free-manuscript.delete', $freeManuscript->id) }}" style="margin-top: 5px">{{ trans('site.delete') }}</button>
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
								<td>{{ str_limit(strip_tags($freeManuscript->content), 120) }}</td>
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
		  	<form method="POST" action="">
		  		{{ csrf_field() }}
		  		<div class="form-group">
		  			<label>{{ trans('site.assign-editor') }}</label>
		  			<select name="editor_id" class="form-control">
		  				@foreach( App\User::where('role', 1)->orderBy('created_at', 'desc')->get() as $editor )
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
		    <form method="POST" action="">
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
                <form method="POST" action="" id="sendFeedbackForm">
                    {{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.body') }}</label>
						<textarea name="email_content" cols="30" rows="10" class="form-control content" required><?php echo e($emailTemplate ? $emailTemplate->email_content : ''); ?></textarea>
					</div>
                    <div class="clearfix"></div>
                    <button type="submit" class="btn btn-success pull-right margin-top" id="sendFeedbackEmail">{{ trans('site.send') }}</button>
                    <div class="clearfix"></div>
                </form>
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
				<form method="POST" action="<?php echo e($route); ?>" novalidate>
                    <?php echo e(csrf_field()); ?>


                    <?php if($isUpdate): ?>
						<?php echo e(method_field('PUT')); ?>
					<?php endif; ?>
					<input type="hidden" name="from_email" value="post@forfatterskolen.no">
					<div class="form-group">
						<label>{{ trans('site.body') }}</label>
						<textarea name="email_content" cols="30" rows="10" class="form-control test" required
						id="freeManuscriptEmailContent"><?php echo e($emailTemplate ? $emailTemplate->email_content : ''); ?></textarea>
					</div>

					<input type="hidden" name="page_name" value="Free Manuscript">

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
				<form action="" method="POST">
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
						<textarea name="manu_content" cols="30" rows="10" class="form-control content" required>

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
<script src="https://cdn.tinymce.com/4/tinymce.min.js"></script>
<script type="text/javascript">

    /*tinymce.init({
		selector:'textarea',
        height : "300",
        menubar: false,
        toolbar: 'insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help'
    });*/

    // tinymce
    let editor_config = {
        path_absolute: "{{ URL::to('/') }}",
        height: '20em',
        selector: 'textarea',
        plugins: ['advlist autolink lists link image charmap print preview hr anchor pagebreak',
            'searchreplace wordcount visualblocks visualchars code fullscreen',
            'insertdatetime media nonbreaking save table contextmenu directionality',
            'emoticons template paste textcolor colorpicker textpattern'],
        toolbar1: 'formatselect fontselect fontsizeselect | bold italic underline strikethrough subscript superscript | forecolor backcolor | link | alignleft aligncenter alignright ' +
        'alignjustify  | removeformat',
        toolbar2: 'undo redo | bullist numlist | outdent indent blockquote | link unlink anchor image media code | print fullscreen',
        relative_urls: false,
        file_browser_callback : function(field_name, url, type, win) {
            let x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
            let y = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;

            let cmsURL = editor_config.path_absolute + '/laravel-filemanager?field_name=' + field_name;
            if (type == 'image') {
                cmsURL = cmsURL + '&type=Images';
            } else {
                cmsURL = cmsURL + '&type=Files';
            }

            tinyMCE.activeEditor.windowManager.open({
                file : cmsURL,
                title : 'Filemanager',
                width : x * 0.8,
                height : y * 0.8,
                resizable : 'yes',
                close_previous : 'no'
            });
        }
    };
    tinymce.init(editor_config);

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

        tinyMCE.activeEditor.setContent(content);
	});

	$(".sendFeedbackBtn").click(function(){
        var action = $(this).data('action');
        var modal = $('#feedbackModal');
        modal.find('form').attr('action', action);
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