<div id="approveFeedbackAdminModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">{{ trans('site.approve-feedback') }}</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="" onsubmit="disableSubmit(this)">
		      {{ csrf_field() }}
				{{ trans('site.approve-feedback-question') }}
		      <div class="text-right margin-top">
		      	<button type="submit" class="btn btn-warning">{{ trans('site.approve') }}</button>
		      </div>
		    </form>
		  </div>
		</div>
	</div>
</div>

<div id="removeFeedbackAdminModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">{{ trans('site.delete-feedback') }}</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="" onsubmit="disableSubmit(this)">
		      {{ csrf_field() }}
				{{ trans('site.delete-feedback-question') }}
		      <div class="text-right margin-top">
		      	<button type="submit" class="btn btn-danger">{{ trans('site.delete') }}</button>
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
		  		<strong>{{ trans_choice('site.manuscripts', 1) }}:</strong><br />
		  		<span id="content"></span>
		  	</p>
		  </div>
		</div>
	</div>
</div>

<div id="submitFeedbackModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.submit-feedback-to') }} <em></em></h4>
			</div>
			<div class="modal-body">
				<form id="submitFeedbackForm" method="POST" action=""  enctype="multipart/form-data"
					  onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<input type="hidden" class="form-control" name="feedback_id">
					<div id="dates"></div>
					<div id="feedbackFileAppend">-</div>
					<div class="form-check" id="replaceAdd">
						<input class="form-check-input" type="checkbox" id="flexCheckDefault" name="replaceFiles">
						<label id="replace" class="form-check-label" for="flexCheckDefault">{{ trans('site.replace-feedback-file') }}</label>
					</div>
					<div class="form-group">
						<label name="manuscriptLabel">{{ trans_choice('site.manuscripts', 1) }}</label>
						<input type="file" class="form-control" required multiple name="filename[]" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
						{{ trans('site.docx-pdf-odt-text') }}
					</div>
					<div class="form-group">
						<label>{{ trans('site.grade') }}</label>
						<input type="number" class="form-control" step="0.01" name="grade">
					</div>
					<div class="form-group">
                        <label>{{ trans('site.notes_to_head_editor') }}</label>
                        <textarea name="notes_to_head_editor" class="form-control" cols="30" rows="3"></textarea>
                    </div>
					<!-- <div class="form-group">
                        <label>Hours Worked</label>
                        <input type="number" class="form-control" step="0.01" name="hours">
                    </div> -->
					<input type="hidden" name="manuscript_id">
					<button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.submit') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="addFeedbackModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.add-feedback') }}</h4>
			</div>
			<div class="modal-body">
				<form id="addFeedbackModalForm" method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
					<?php
						$emailTemplate = \App\Http\AdminHelpers::emailTemplate('Shop Manuscript Feedback');
					?>
					{{csrf_field()}}
					<input type="hidden" class="form-control" name="feedback_id">
					<div id="dates"></div>
					<div id="feedbackFileAppend">-</div>
					<div class="form-check" id="replaceAdd">
						<input class="form-check-input" type="checkbox" id="flexCheckDefault" name="replaceFiles">
						<label id="replace" class="form-check-label" for="flexCheckDefault">{{ trans('site.replace-feedback-file') }}</label>
					</div>
					<div class="form-group">
						<label name="manuscriptLabel">{{ trans_choice('site.files', 2) }}</label>
						<input type="file" class="form-control" name="files[]" multiple
							   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
							   application/pdf, application/vnd.oasis.opendocument.text" required>
							   {{ trans('site.docx-pdf-odt-text') }}
					</div>
					<div class="form-group">
						<label>{{ trans_choice('site.notes', 2) }}</label>
						<textarea class="form-control" name="notes" rows="6"></textarea>
					</div>
					<div class="form-group">
                        <label>{{ trans('site.hours-worked') }}</label>
                        <input type="number" class="form-control" step="0.01" name="hours">
                    </div>
					<div class="form-group">
                        <label>{{ trans('site.notes_to_head_editor') }}</label>
                        <textarea name="notes_to_head_editor" class="form-control" cols="30" rows="3"></textarea>
                    </div>
					<button type="submit" class="btn btn-primary pull-right">{{ trans('site.add-feedback') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="finishAssignmentManuscriptModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.finish-assignment') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{csrf_field()}}
					{{ trans('site.finish-assignment-question') }}
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-success">{{ trans('site.submit') }}</button>
					</div>
				</form>
			</div>
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
						<select name="editor_id" class="form-control select2" required>
							<option value="" disabled="" selected>-- Velg redaktør --</option>
							@foreach( App\User::whereIn('role', array(1,3))->orderBy('created_at', 'desc')->get() as $editor )
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

<div id="pendingAssignmentEditorModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.assign-editor') }}</label>
						<select name="editor_id" class="form-control select2" required>
							<option value="" disabled="" selected>-- Velg redaktør --</option>
							@foreach( App\User::whereIn('role', array(1,3))->orderBy('created_at', 'desc')->get() as $editor )
								<option value="{{ $editor->id }}">{{ $editor->full_name }}</option>
							@endforeach
						</select>

						<div class="hidden-container">
							<label>
							</label>
							<a href="javascript:void(0)" onclick="enableSelect('pendingAssignmentEditorModal')">Endre</a>
						</div>
					</div>
					<div class="form-group">
						<label>Forventet sluttdato</label>
						<input type="date" class="form-control" name="expected_finish">
					</div>
					<div class="text-right">
						<button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="updateOtherServiceStatusModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{!! str_replace('_SERVICE_','<span></span>',trans('site.update-service-status')) !!}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<p>
						{{ trans('site.update-service-status-question') }}
					</p>
					<div class="text-right">
						<button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="setOtherServiceFinishDateModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"><span></span> {{ trans('site.expected-finish') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.expected-finish-date') }}</label>
						<input type="datetime-local" name="expected_finish" class="form-control" required>
					</div>
					<div class="text-right">
						<button class="btn btn-primary" type="submit">{{ trans('site.submit') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="addOtherServiceFeedbackModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span></span> {{ trans('site.add-feedback') }}</h4>
            </div>
            <div class="modal-body">
                <form id="addOtherServiceFeedbackForm" method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                    {{csrf_field()}}
					<?php
					$emailTemplate = \App\Http\AdminHelpers::emailTemplate('Other Services Feedback');
					?>
					<input type="hidden" class="form-control" name="feedback_id">
					<div id="dates"></div>
					<div id="feedbackFileAppend">-</div>
					<div class="form-check" id="replaceAdd">
						<input class="form-check-input" type="checkbox" id="flexCheckDefault" name="replaceFiles">
						<label id="replace" class="form-check-label" for="flexCheckDefault">{{ trans('site.replace-feedback-file') }}</label>
					</div>
                    <div class="form-group">
                        <label name="manuscriptLabel">{{ trans_choice('site.manuscripts', 1) }}</label>
                        <input type="file" class="form-control" name="manuscript[]" multiple accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf" required>
						{{ trans('site.docx-pdf-odt-text') }}
                    </div>
					<div class="form-group">
                        <label>{{ trans('site.hours-worked') }}</label>
                        <input type="number" class="form-control" step="0.01" name="hours_worked">
                    </div>
					<div class="form-group">
                        <label>{{ trans('site.notes_to_head_editor') }}</label>
                        <textarea name="notes_to_head_editor" class="form-control" cols="30" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary pull-right">{{ trans('site.add-feedback') }}</button>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>

    </div>
</div>

<div id="approveCoachingSessionModal" class="modal fade" role="dialog"  data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.approve-coaching-timer') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<p>{{ trans('site.approve-coaching-timer-question') }}</p>
					<div class="text-right">
						<button class="btn btn-primary" type="submit">{{ trans('site.approve') }}</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('site.cancel') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="viewHelpWithModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Hjelp med</h4>
			</div>
			<div class="modal-body">
				<pre></pre>
			</div>
		</div>
	</div>
</div>

<div id="finishTaskModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Finish Task</h4>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action=""
					  onsubmit="disableSubmit(this)">
					{{ csrf_field() }}

					<p>Are you sure to finish this task?</p>

					<button type="submit" class="btn btn-success pull-right">Finish</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="editTaskModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Edit Task</h4>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action=""
					  onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					{{ method_field('PUT') }}
					<input type="hidden" name="user_id" value="">

					<div class="form-group">
						<label>
							Task
						</label>
						<textarea name="task" cols="30" rows="10" class="form-control" required></textarea>
					</div>

					<div class="form-group">
						<label>
							{{ trans('site.assign-to') }}
						</label>
						<select name="assigned_to" class="form-control select2" required>
							<option value="" disabled="" selected>-- Select Assignee --</option>
							@foreach( App\User::whereIn('role', array(1,3))->orderBy('created_at', 'desc')->get() as $editor )
								<option value="{{ $editor->id }}">{{ $editor->full_name }}</option>
							@endforeach
						</select>
					</div>

					<button type="submit" class="btn btn-primary pull-right">Update Task</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="deleteTaskModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Delete Task</h4>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action=""
					  onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					{{ method_field('DELETE') }}

					<p>Are you sure to delete this task?</p>

					<button type="submit" class="btn btn-danger pull-right">Delete</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="editExpectedFinishModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Edit Expected Finish</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.expected-finish') }}</label>
						<input type="date" name="expected_finish" class="form-control" required>
					</div>
					<div class="text-right">
						<button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="submitPersonalAssignmentFeedbackModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{{ trans('site.submit-feedback-to') }} <em></em></h4>
            </div>
            <div class="modal-body">
                <form id="submitPersonalAssignmentFeedbackForm" method="POST" action=""  enctype="multipart/form-data"
					onsubmit="disableSubmit(this)">
					<input type="hidden" class="form-control" name="feedback_id">
                    <?php
                    	$emailTemplate = \App\Http\AdminHelpers::emailTemplate('Assignment Manuscript Feedback');
                    ?>
                    {{ csrf_field() }}
					<div id="dates"></div>
					<div id="feedbackFileAppend">-</div>
					<div class="form-check" id="replaceAdd">
						<input class="form-check-input" type="checkbox" id="flexCheckDefault" name="replaceFiles">
						<label id="replace" class="form-check-label" for="flexCheckDefault">{{ trans('site.replace-feedback-file') }}</label>
					</div>
                    <div class="form-group">
                        <label name="manuscriptLabel">{{ trans_choice('site.manuscripts', 1) }}</label>
                        <input type="file" class="form-control" required multiple name="filename[]"
                               accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                   application/pdf, application/vnd.oasis.opendocument.text">
                        {{ trans('site.docx-pdf-odt-text') }} <br>
                    </div>
                    <div class="form-group">
                        <label>{{ trans('site.grade') }}</label>
                        <input type="number" class="form-control" step="0.01" name="grade">
                    </div>
					<div class="form-group">
                        <label>{{ trans('site.notes_to_head_editor') }}</label>
                        <textarea name="notes_to_head_editor" class="form-control" cols="30" rows="3"></textarea>
                    </div>
					<!-- <div class="form-group">
                        <label>Hours Worked</label>
                        <input type="number" class="form-control" step="0.01" name="hours">
                    </div> -->
                    <button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.submit') }}</button>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="setReplayModal" class="modal fade" role="dialog" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.replay-link') }}</label>
						<input type="url" name="replay_link" class="form-control">
					</div>
					<div class="form-group">
						<label>{{ trans_choice('site.comments', 1) }}</label>
						<textarea name="comment" cols="30" rows="10" class="form-control"></textarea>
					</div>
					<div class="form-group">
						<label>{{ trans('site.document') }}</label>
						<input type="file" name="document" class="form-control"
							   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                   application/msword,
                               application/pdf,">
					</div>
					<!-- <div class="form-group">
                        <label>Hours Worked</label>
                        <input type="number" class="form-control" step="0.01" name="hours_worked">
                    </div> -->
					<div class="form-group">
						<small>{{ trans('site.coaching-timer.form.note') }}</small>
					</div>
					<div class="text-right">
						<button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="selfPublishingFeedbackModal" class="modal fade" role="dialog" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">
					Add Feedback
				</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans_choice('site.manuscripts', 1) }}</label>
						<input type="file" name="manuscript[]" class="form-control"
							   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf,
					    application/vnd.oasis.opendocument.text" multiple>
					</div>

					<div class="form-group">
						<label>{{ trans_choice('site.notes', 1) }}</label>
						<textarea name="notes" cols="30" rows="10" class="form-control"></textarea>
					</div>
					<div class="text-right">
						<button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>

		</div>
	</div>
</div>

<div id="acceptRequest" class="modal fade" role="dialog" tabindex="-1">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title main-title"><em></em></h4>
				<h5 class="modal-title sub-title"></h5>
			</div>
			<div class="modal-body">
				<a href="#" style="width: 100px;" class="btn btn-success yesBtn">{{ trans('site.front.yes') }}</a>
				<a href="#" style="width: 100px;" class="btn btn-danger" data-dismiss="modal">{{ trans('site.front.no') }}</a>
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

<div id="freeManuscriptFeedbackModal" class="modal fade" role="dialog">
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
						<textarea name="email_content" cols="30" rows="10" class="form-control tinymce" id="FMEmailContentEditor" required>
						</textarea>
					</div>
					<div class="clearfix"></div>
					<button type="submit" class="btn btn-primary pull-right margin-top" id="sendFeedbackEmail">{{ trans('site.submit') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="projectHoursModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
		    	<button type="button" class="close" data-dismiss="modal">&times;</button>
		    	<h4 class="modal-title">
					Edit Project
				</h4>
		  	</div>
		  	<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
		    		{{ csrf_field() }}
					<div class="form-group">
						<label>
							Project Number
						</label>
						<input type="text" class="form-control" name="project_number" readonly>
					</div>

					<div class="form-group">
						<label>
							Name
						</label>
						<input type="text" class="form-control" name="name" readonly>
					</div>

					<div class="form-group">
						<label>Number of hours</label>
						<input type="text" name="editor_total_hours" class="form-control" id="timeInput" required>
		
						<button type="button" class="btn btn-xs" onclick="adjustTime(1)">+1</button>
						<button type="button" class="btn btn-xs" onclick="adjustTime(0.5)">+1/2</button>
						<button type="button" class="btn btn-xs" onclick="adjustTime(-0.5)">-1/2</button>
						<button type="button" class="btn btn-xs" onclick="adjustTime(-1)">-1</button>
					</div>

					<div class="text-right margin-top">
		      			<button type="submit" class="btn btn-primary">{{ trans('site.submit') }}</button>
		      		</div>
		    	</form>
		  	</div>
		</div>
	</div>
</div>