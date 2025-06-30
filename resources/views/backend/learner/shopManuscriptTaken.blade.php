@extends('backend.layout')

@section('title')
<title>Shop Manuscript &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3> 
	<?php $extension = explode('.', basename($shopManuscriptTaken->file)); ?>
	@if( end($extension) == 'pdf' )
	<i class="fa fa-file-pdf-o"></i> 
	@elseif( end($extension) == 'docx' )
	<i class="fa fa-file-word-o"></i> 
	@elseif( end($extension) == 'odt' )
	<i class="fa fa-file-text-o"></i> 
	@endif
	{{ $shopManuscriptTaken->shop_manuscript->title }} <em>{{ basename($shopManuscriptTaken->file) }}</em></h3>
	<div class="navbar-form navbar-right">
	  	<div class="form-group">
		  	<form role="search" method="get" action="">
				<div class="input-group">
				  	<input type="text" class="form-control" placeholder="Search manuscript..">
				    <span class="input-group-btn">
				    	<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
				    </span>
				</div>
			</form>
		</div>
	</div>
	<div class="clearfix"></div>
</div>

<div class="col-md-12">
	<div class="margin-top">
		<div class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-12 col-md-7">
							@if( end($extension) == 'pdf' || end($extension) == 'odt' )
							<iframe src="/js/ViewerJS/#../..{{ $shopManuscriptTaken->file }}" style="width: 100%; border: 0; height: 600px"></iframe>
							@elseif( end($extension) == 'docx' || end($extension) == 'doc' )
							<iframe src="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$shopManuscriptTaken->file}}" style="width: 100%; border: 0; height: 600px"></iframe>
							@endif
						</div>
						<div class="col-sm-12 col-md-5">
							<div class="pull-right">
							<button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#synopsisModal">{{ trans('site.synopsis') }}</button>
							<button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#editManuscriptModal"><i class="fa fa-pencil"></i></button>
								@if( $shopManuscriptTaken->expected_finish )
									<button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#sendEmailModal"><i class="fa fa-envelope"></i></button>
								@endif
							<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#updateDocumentModal">{{ trans('site.update-document') }}</button>
							<button class="btn btn-success btn-sm" data-toggle="modal" data-target="#sendRequestToEditor"><i class="fa fa-paper-plane" aria-hidden="true"></i>&nbsp;&nbsp;{{ trans('site.send-request-to-editor') }}</button>
							</div>
				  			@if( $shopManuscriptTaken->status == 'Finished' )
							<span class="label label-success">Finished</span>
							@elseif( $shopManuscriptTaken->status == 'Started' )
							<span class="label label-primary">Started</span>
							@elseif( $shopManuscriptTaken->status == 'Not started' )
							<span class="label label-warning">Not started</span>
							@endif
							<h3 class="no-margin-top">{{ $shopManuscriptTaken->shop_manuscript->title }}</h3>
							{{ trans_choice('site.learners', 1) }}: <a href="{{ route('admin.learner.show', $shopManuscriptTaken->user_id) }}">{{ $shopManuscriptTaken->user->full_name }}</a><br />
							{{ trans('site.filename') }}: {{ basename($shopManuscriptTaken->file) }}<br />
							{{ trans_choice('site.words', 2) }}: {{ $shopManuscriptTaken->words }}<br />
							{{ trans('site.date-uploaded') }}: {{ $shopManuscriptTaken->manuscript_uploaded_date ?
							date_format(date_create($shopManuscriptTaken->manuscript_uploaded_date),'M d, Y H:i a') : '' }}<br />
							{{ trans('site.admin') }}:
							@if( $shopManuscriptTaken->admin )
							{{ $shopManuscriptTaken->admin->full_name }}
							@else
							<em>Not set</em>
							@endif<br />

							{{ trans('site.expected-finish') }}:
				            @if( $shopManuscriptTaken->expected_finish )
				            {{ date_format(date_create($shopManuscriptTaken->expected_finish), 'M d, Y') }}
				            @else
				            <em>Not set</em>
				            @endif
				            <br />

							{{ trans('site.editor-expected-finish') }}:
				            @if( $shopManuscriptTaken->editor_expected_finish )
				            {{ date_format(date_create($shopManuscriptTaken->editor_expected_finish), 'M d, Y') }}
				            @else
				            <em>Not set</em>
				            @endif
				            <br />

							<strong>{{ trans('site.grade') }}: @if($shopManuscriptTaken->grade)
									{{$shopManuscriptTaken->grade}}
								@else
									<em>Not set</em>
								@endif
							</strong>
							<br>
							{{ trans('site.genre') }}: @if ($shopManuscriptTaken->genre) {{ \App\Http\FrontendHelpers::assignmentType($shopManuscriptTaken->genre) }} @endif
							<a href="#" data-target="#editGenreModal" data-toggle="modal">{{ trans('site.edit-genre') }}</a>
							<br>
							{{ trans('site.front.form.coaching-time-later-in-manus') }}
							<a href="#" data-target="#coachingTimeModal" data-toggle="modal">
								{{ $shopManuscriptTaken->coaching_time_later ? 'Yes' : 'No' }}
							</a>
							<br>
							{{ trans('site.description') }}: {{ $shopManuscriptTaken->description }}
							<a href="#" data-target="#editDescriptionModal" data-toggle="modal">Edit</a>
							<br>
							@if ($shopManuscriptTaken->synopsis)
								<a href="{{ route('admin.learner.download_synopsis', $shopManuscriptTaken->id) }}">{{ trans('site.download-synopsis') }}</a>
							@endif
							<br><br>

							<h4>{{ trans_choice('site.feedbacks', 2) }}
							@if( $shopManuscriptTaken->feedbacks->count() == 0 )
							<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addFeedbackModal">+ {{ trans('site.add-feedback') }}</button>
							@endif</h4>
							<div class="row margin-top">
								@foreach($shopManuscriptTaken->feedbacks as $feedback)
								<div class="col-sm-12">
									<div class="panel panel-default">
										<div class="panel-body">
											<button type="button" class="btn btn-xs btn-danger btn-delete-feedback pull-right" data-action="{{ route('admin.shop-manuscript-taken-feedback.delete', $feedback->id) }}" data-toggle="modal" data-target="#deleteFeedbackModal"><i class="fa fa-trash"></i></button>
											<strong>{{ trans_choice('site.files', 2) }}:</strong>
												@foreach( $feedback->filename as $filename )<br />
												<a href="{{ $filename }}" target="_blank">{{ basename($filename) }}</a>
												@endforeach
											<br />
											<strong>{{ trans_choice('site.notes', 2) }}:</strong> {{ $feedback->notes }} <br />
											<strong>{{ trans('site.uploaded-on') }}:</strong> {{ $feedback->created_at }} <br />
										</div>
									</div>
								</div>
								@endforeach
							</div>


							<br />
							<h4>{{ trans_choice('site.comments', 2) }}</h4>
							<form method="POST" class="margin-top" action="{{ route('shop_manuscript_taken_comment', ['id' => $learner->id, 'shop_manuscript_taken_id' => $shopManuscriptTaken->id]) }}">
								{{ csrf_field() }}
								<input type="text" placeholder="{{ trans_choice('site.comments', 1) }}" name="comment" class="form-control" required>
								<div class="text-right margin-top">
									<button class="btn btn-info btn-sm" type="submit">{{ trans('site.add-comment') }}</button>
								</div>
							</form>
							<hr />
							<div class="margin-top">
								@foreach( $shopManuscriptTaken->comments as $comment )
									@if( $comment->user_id == Auth::user()->id )
										<div class="text-right">
											<div class="comment owner">
												<div>{{ $comment->comment }}</div>
												<div><small><em>{{ trans('site.you') }}</em></small></div>
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
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="editManuscriptModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('site.edit-manuscript') }}</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{ route('admin.shop-manuscript-taken.update_taken', $shopManuscriptTaken->id) }}" 
			onsubmit="disableSubmit(this)">
      		{{csrf_field()}}
      		<div class="form-group">
      			<label>{{ trans_choice('site.editors', 1) }}</label>
      			<select class="form-control select2" name="feedback_user_id" required>
					
						@if($editor->count()>0)
							<option value="" selected disabled>
								-- Select Editor --
							</option>
						@else
							<option value="" selected disabled>
								-- {{ trans('site.no-editor-found') }} --
							</option>
						@endif
						@foreach($editor as $admin)
							<?php
								$selected = '';


							if ($shopManuscriptTaken->feedback_user_id === $admin->id) {
								$selected = 'selected';
							}

							if ($shopManuscriptTaken->user->preferredEditor
								&& $shopManuscriptTaken->user->preferredEditor->editor_id === $admin->id) {
								$selected = 'selected';
							}
							?>
						<option value="{{ $admin->id }}" {{ $selected}}>
							{{ $admin->full_name }}
						</option>
						@endforeach
						
      			</select>
				@if($shopManuscriptTaken->user->preferredEditor)
					<div class="hidden-container">
						<label>
							{{ $shopManuscriptTaken->user->preferredEditor->editor->full_name }}
						</label>
						<a href="javascript:void(0)" onclick="enableSelect('editManuscriptModal')">Edit</a>
					</div>
				@endif
      		</div>
			<div class="form-group">
				<label>{{ trans('site.grade') }}</label>
				<input type="number" step=".1" class="form-control" name="grade" value="{{ $shopManuscriptTaken->grade }}">
			</div>
          	<div class="form-group">
            	<label>{{ trans('site.expected-finish') }}</label>
            	<input type="date" class="form-control" name="expected_finish" 
				value="{{ strftime('%Y-%m-%d', strtotime($shopManuscriptTaken->expected_finish)) }}">
          	</div>
			  <div class="form-group">
            	<label>{{ trans('site.editor-expected-finish') }}</label>
            	<input type="date" class="form-control" name="editor_expected_finish" 
				value="{{ $shopManuscriptTaken->editor_expected_finish 
				? strftime('%Y-%m-%d', strtotime($shopManuscriptTaken->editor_expected_finish)) : '' }}">
          	</div>
  			<button type="submit" class="btn btn-primary pull-right">{{ trans('site.update') }}</button>
  			<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>


@if( $shopManuscriptTaken->feedbacks->count() == 0 )
<div id="addFeedbackModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('site.add-feedback') }}</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{ route('admin.shop-manuscript-taken-feedback.store', $shopManuscriptTaken->id) }}"
			  enctype="multipart/form-data">
      		{{csrf_field()}}
            <?php
            $emailTemplate = \App\Http\AdminHelpers::emailTemplate('Shop Manuscript Feedback');
            ?>
      		<div class="form-group">
      			<label>{{ trans_choice('site.files', 2) }}</label>
				<input type="file" class="form-control" name="files[]" multiple
					   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf,
					    application/vnd.oasis.opendocument.text" required>
      		</div>
      		<div class="form-group">
      			<label>{{ trans_choice('site.notes', 2) }}</label>
				<textarea class="form-control" name="notes" rows="6"></textarea>
      		</div>
			<div class="form-group">
				<label>{{ trans('site.subject') }}</label>
				<input type="text" class="form-control" name="subject" value="{{ $emailTemplate->subject }}"
					   required>
			</div>
			<div class="form-group">
				<label>{{ trans('site.from') }}</label>
				<input type="text" class="form-control" name="from_email"
					   value="{{ $emailTemplate->from_email }}" required>
			</div>
			<div class="form-group">
				<label>{{ trans_choice('site.notes', 2) }}</label>
				<textarea class="form-control tinymce" name="message" rows="6"
						  required>{!! $emailTemplate->email_content !!}</textarea>
			</div>
			{{ trans('site.add-feedback-note') }}
  			<button type="submit" class="btn btn-primary pull-right">{{ trans('site.add-feedback') }}</button>
  			<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>
@endif

<div id="deleteFeedbackModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('site.delete-feedback') }}</h4>
      </div>
      <div class="modal-body">
		  {{ trans('site.delete-feedback-question') }}
      	<form method="POST" action="" class="margin-top">
      		{{csrf_field()}}
  			<button type="submit" class="btn btn-danger pull-right">{{ trans('site.delete-feedback') }}</button>
  			<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>

<!-- Update document Modal -->
<div id="updateDocumentModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('site.update-document') }}</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('shop_manuscript_taken.update_document', $shopManuscriptTaken->id) }}" enctype="multipart/form-data"
			onsubmit="disableSubmit(this)">
          {{ csrf_field() }}
          <div class="form-group">
          	<input type="file" name="manuscript" class="form-control" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text" required>
          </div>
          <div class="text-right margin-top">
            <button type="submit" class="btn btn-primary">{{ trans('site.update') }}</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

<!-- Synopsis Modal -->
<div id="synopsisModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.synopsis') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('shop_manuscript_taken.save_synopsis', $shopManuscriptTaken->id) }}" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="form-group">
						<input type="file" name="synopsis" class="form-control" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text" required>
					</div>
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-primary">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>

	</div>
</div>

@if( $shopManuscriptTaken->expected_finish )
	<!-- Send email Modal -->
	<div id="sendEmailModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">{{ trans('site.send-email') }}</h4>
				</div>
				<div class="modal-body">
                    <?php
						$subject = 'Forventet dato for tilbakemelding';
                    if ($emailTemplate) {
                        $replace_string = \Carbon\Carbon::parse($emailTemplate->expected_finish)->format('d.m.Y');
                        $replace_content = str_replace('_date_',$replace_string, $emailTemplate->email_content);
                        $subject = $emailTemplate->subject;
                    }
                    ?>
					<form method="POST" action="{{ route('admin.shop_manuscript_taken.email', $shopManuscriptTaken->user_id) }}" enctype="multipart/form-data"
						onsubmit="disableSubmit(this)">
						{{ csrf_field() }}
						<input type="hidden" name="shop_manuscripts_taken_id" value="{{ $shopManuscriptTaken->id }}">
						<div class="form-group">
							<label>{{ trans('site.subject') }}</label>
							<input type="text" name="subject" class="form-control" required value="{{ $subject }}">
						</div>
						<div class="form-group">
							<label>{{ trans('site.message') }}</label>
							<textarea name="message" class="form-control tinymce" required rows="8">{{ $emailTemplate ? $replace_content : '' }}</textarea>
						</div>
						<input type="hidden" name="from_email" value="{{ $emailTemplate ? $emailTemplate->from_email : 'post@forfatterskolen.no' }}">
						<div class="text-right margin-top">
							<button type="submit" class="btn btn-primary">{{ trans('site.send') }}</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endif

<div id="editGenreModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.edit-genre') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.shop-manuscript-taken.update-genre', $shopManuscriptTaken->id) }}">
					{{ csrf_field() }}
					<select class="form-control" name="genre" required>
						<option value="" disabled="disabled" selected>Select Genre</option>
						@foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
							<option value="{{ $type->id }}"
							@if ($shopManuscriptTaken->genre == $type->id) selected @endif> {{ $type->name }} </option>
						@endforeach
					</select>
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-primary">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="coachingTimeModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form method="POST" 
				action="{{ route('admin.shop-manuscript-taken.update-coaching-time-later', $shopManuscriptTaken->id) }}"
				onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					{{ trans('site.front.form.coaching-time-later-in-manus') }}
					<select class="form-control" name="coaching_time_later" required>
						<option value="1" {{ $shopManuscriptTaken->coaching_time_later ? 'selected' : '' }}>
							Yes
						</option>
						<option value="0" {{ !$shopManuscriptTaken->coaching_time_later ? 'selected' : '' }}>
							No
						</option>
					</select>
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-primary">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="editDescriptionModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.description') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" 
					action="{{ route('admin.shop-manuscript-taken.update-description', $shopManuscriptTaken->id) }}"
					onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<textarea name="description" class="form-control" cols="30" 
					rows="10">{{ $shopManuscriptTaken->description }}</textarea>
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-primary">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="sendRequestToEditor" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.send-request-to-editor') }}</h4>
			</div>
			<div class="modal-body">
				<table class="table">
					<thead>
						<tr>
							<th>{{ trans('site.date-sent') }}</th>
							<th>{{ trans_choice('site.editors', 1) }}</th>
							<th>{{ trans('site.answer-until') }}</th>
							<th>{{ trans('site.answer-text') }}</th>
						</tr>
					</thead>
					<tbody>
					@foreach($shopManuscriptTaken->requests as $key)
						<tr>
							<td>{{ $key->created_at }}</td>
							<td>{{ $key->editor->full_name }}</td>
							<td>{{ $key->answer_until }}</td>
							<td>{{ $key->answer?$key->answer:trans('site.no-answer') }}</td>
						</tr>
					@endforeach
					</tbody>
				</table>
				<hr>
				<label for="">{{ trans('site.can-you-take-this-manuscript') }}</label>
				<form method="POST" action="{{ route('admin.send-request-to-editor', $shopManuscriptTaken->id) }}">
					<?php
						$emailTemplate = \App\Http\AdminHelpers::emailTemplate('Request To Editor');
					?>
					{{ csrf_field() }}
					<div class="margin-top">
						<div class="form-group">
							<select class="form-control select2" name="editor_id" required>
								<option value="" disabled selected>- Select Editor -</option>
								@foreach( $editor as $key )
									<option value="{{ $key->id }}">{{ $key->full_name }}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group">
							<label>{{ trans('site.answer-until') }}</label>
							<input type="date" class="form-control" name="answer_until" required>
						</div>
						<div class="form-group">
							<label>{{ trans('site.editor-expected-finish') }}</label>
							<input type="date" class="form-control" name="editor_expected_finish" 
							@if( $shopManuscriptTaken->editor_expected_finish ) value="{{ strftime('%Y-%m-%d', strtotime($shopManuscriptTaken->editor_expected_finish)) }}" @endif>
						</div>
						<div class="form-group">
							<label>{{ trans('site.expected-finish') }}</label>
							<input type="date" class="form-control" name="expected_finish" 
							@if( $shopManuscriptTaken->expected_finish ) value="{{ strftime('%Y-%m-%d', strtotime($shopManuscriptTaken->expected_finish)) }}" @endif>
						</div>
						<div class="form-group">
							<label>{{ trans('site.subject') }}</label>
							<input type="text" class="form-control" name="subject" value="{{ $emailTemplate->subject }}"
								required>
						</div>
						<div class="form-group">
							<label>{{ trans('site.from') }}</label>
							<input type="text" class="form-control" name="from_email"
									value="{{ $emailTemplate->from_email }}" required>
						</div>
						<div class="form-group">
							<label>{{ trans('site.message') }}</label>
							<textarea class="form-control tinymce" name="message" rows="6"
									required>{!! $emailTemplate->email_content !!}</textarea>
						</div>
						<br>
						<hr>
						<button type="submit" class="btn btn-primary">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>

	</div>
</div>

@stop

@section('scripts')
<script>
$(document).ready(function(){
  $('.btn-delete-feedback').click(function(){
        var action = $(this).data('action');

        var form = $('#deleteFeedbackModal');
        form.find('form').attr('action', action);
    });

	@if($shopManuscriptTaken->user->preferredEditor )
    	$("#editManuscriptModal").find(".select2").hide();
    @endif
});
</script>
@stop