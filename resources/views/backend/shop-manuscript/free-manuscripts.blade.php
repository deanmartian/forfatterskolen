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
	<h3><i class="fa fa-file-text-o"></i> Free Manuscripts</h3>
	<a href="#" data-toggle="modal" data-target="#freeManuscriptEmailTemplate"> Email Template</a>
</div>

<div class="col-md-12">

	<ul class="nav nav-tabs margin-top">
		<li @if( Request::input('tab') != 'archive' ) class="active" @endif><a href="?tab=new">New</a></li>
		<li @if( Request::input('tab') == 'archive' ) class="active" @endif><a href="?tab=archive">Archive</a></li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane fade in active">
			@if( Request::input('tab') != 'archive' )

				<div class="table-users table-responsive">
					<table class="table">
						<thead>
						<tr>
							<th>Name</th>
							<th>Email</th>
							<th width="600">Content</th>
							<th>Date Received</th>
							<th>Editor</th>
							<th></th>
						</tr>
						</thead>

						<tbody>
						@foreach( $freeManuscripts as $freeManuscript )
							<tr>
								<td>{{ $freeManuscript->name }}</td>
								<td>{{ $freeManuscript->email }}</td>
								<td>{{ str_limit(strip_tags($freeManuscript->content), 120) }}</td>
								<td>{{ \App\Http\FrontendHelpers::formatDate($freeManuscript->created_at) }}</td>
								<td>@if( $freeManuscript->editor ) {{ $freeManuscript->editor->full_name }} @endif</td>
								<td>
									@if( $freeManuscript->editor )
										<button class="btn btn-xs btn-success sendFeedbackBtn" data-toggle="modal" data-target="#feedbackModal" data-fields="{{ json_encode($freeManuscript) }}" data-action="{{ route('admin.free-manuscript.send_feedback', $freeManuscript->id) }}">Send Back Feedback</button>
									@endif
									<button class="btn btn-xs btn-primary viewManuscriptBtn" data-toggle="modal" data-target="#viewManuscriptModal" data-fields="{{ json_encode($freeManuscript) }}"
									data-genre="{{ $freeManuscript->genre ? \App\Http\FrontendHelpers::assignmentType($freeManuscript->genre): '' }}"
									data-content="{{ html_entity_decode($freeManuscript->content) }}">View</button>
									<button class="btn btn-xs btn-warning assignEditorBtn" data-toggle="modal" data-target="#assignEditorModal" data-action="{{ route('admin.free-manuscript.assign_editor', $freeManuscript->id) }}" data-editor="{{ $freeManuscript->editor_id }}">Assign Editor</button>
									<button class="btn btn-xs btn-danger deleteManuscriptBtn" data-toggle="modal" data-target="#deleteManuscriptModal" data-fields="{{ json_encode($freeManuscript) }}" data-action="{{ route('admin.free-manuscript.delete', $freeManuscript->id) }}" style="margin-top: 5px">Delete</button>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>

			@else

				<div class="table-users table-responsive">
					<table class="table">
						<thead>
						<tr>
							<th>Name</th>
							<th>Email</th>
							<th width="500">Content</th>
							<th>Date Sent</th>
							<th>Editor</th>
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
									<button class="btn btn-xs btn-success viewFeedbackBtn" data-toggle="modal" data-target="#viewFeedbackModal" data-fields="{{ json_encode($freeManuscript) }}">View Feedback</button>
									<button class="btn btn-xs btn-primary viewManuscriptBtn" data-toggle="modal" data-target="#viewManuscriptModal" data-fields="{{ json_encode($freeManuscript) }}"
											data-genre="{{ $freeManuscript->genre ? \App\Http\FrontendHelpers::assignmentType($freeManuscript->genre): '' }}"
											data-content="{{ html_entity_decode($freeManuscript->content) }}">View</button>
									<button class="btn btn-xs btn-warning assignEditorBtn" data-toggle="modal" data-target="#assignEditorModal" data-action="{{ route('admin.free-manuscript.assign_editor', $freeManuscript->id) }}" data-editor="{{ $freeManuscript->editor_id }}">Assign Editor</button>
									<button class="btn btn-xs btn-danger deleteManuscriptBtn" data-toggle="modal" data-target="#deleteManuscriptModal" data-fields="{{ json_encode($freeManuscript) }}" data-action="{{ route('admin.free-manuscript.delete', $freeManuscript->id) }}">Delete</button>
									<button class="btn btn-xs btn-info resendFeedbackBtn" data-toggle="modal" data-target="#resendFeedbackModal" data-action="{{ route('admin.free-manuscript.resend-feedback', $freeManuscript->id) }}">Resend</button>
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
		  			<label>Assign editor</label>
		  			<select name="editor_id" class="form-control">
		  				@foreach( App\User::where('role', 1)->orderBy('created_at', 'desc')->get() as $editor )
		  				<option value="{{ $editor->id }}">{{ $editor->full_name }}</option>
		  				@endforeach
		  			</select>
		  		</div>
		  		<div class="text-right">
		  			<button class="btn btn-primary" type="submit">Save</button>
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
		  		<strong>Name:</strong><br />
		  		<span id="name"></span><br />
		  		<br />
		  		<strong>Email:</strong><br />
		  		<span id="email"></span><br />
		  		<br />
				<strong>Genre:</strong><br />
				<span id="genre"></span><br />
				<br />
		  		<strong>Manuscript:</strong><br />
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
		    <h4 class="modal-title">Delete free manuscript</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="">
		      {{ csrf_field() }}
		      Are you sure to delete this free manuscript?
		      <button type="submit" class="btn btn-danger pull-right margin-top">Delete</button>
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
                <h4 class="modal-title">Send Feedback</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="" id="sendFeedbackForm">
                    {{ csrf_field() }}
					<div class="form-group">
						<label>Body</label>
						<textarea name="email_content" cols="30" rows="10" class="form-control content" required><?php echo e($emailTemplate ? $emailTemplate->email_content : ''); ?></textarea>
					</div>
                    <div class="clearfix"></div>
                    <button type="submit" class="btn btn-success pull-right margin-top" id="sendFeedbackEmail">Send</button>
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
				<h4 class="modal-title">View Feedback</h4>
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
				<h4 class="modal-title">Email Template</h4>
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
						<label>Body</label>
						<textarea name="email_content" cols="30" rows="10" class="form-control test" required
						id="freeManuscriptEmailContent"><?php echo e($emailTemplate ? $emailTemplate->email_content : ''); ?></textarea>
					</div>

					<input type="hidden" name="page_name" value="Free Manuscript">

					<button type="submit" class="btn btn-primary pull-right">Save</button>
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
				<h4 class="modal-title">Email Template</h4>
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
				<h4 class="modal-title">Resend Feedback</h4>
			</div>
			<div class="modal-body">
				<form action="" method="POST">
					{{ csrf_field() }}
					<p>
						Are you sure you want to resend the feedback?
					</p>
					<button class="btn btn-primary pull-right">Resend</button>
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

    tinymce.init({
		selector:'textarea',
        height : "300",
        menubar: false,
        toolbar: 'insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help'
    });

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

</script>
@stop