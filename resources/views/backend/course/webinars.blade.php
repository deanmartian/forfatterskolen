@extends('backend.layout')

@section('title')
<title>Webinars &rsaquo; {{$course->title}} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('css/cropper.min.css') }}">
	<style>
		.image_container, .image_container_edit {
			display: none;
			height: 300px;
			margin-bottom: 10px;
		}

		.webinar-img img{
			width: 100%;
			height: 170px;
			margin-bottom: 12px;
		}

		.webinar-list-container {
			padding-right: 0;
			padding-left: 0;
		}
	</style>
@stop

@section('content')

@include('backend.course.partials.toolbar')


<div class="course-container">
	
	@include('backend.partials.course_submenu')
	<?php 
		$editors = \App\User::where(function($query){
            $query->where('role', 3)->orWhere('admin_with_editor_access', 1);
        })
        ->orderBy('id', 'desc')
        ->get();
	?>
	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12">
			<div class="row">
				<div class="col-sm-12">
					<button class="btn btn-primary margin-bottom" data-toggle="modal" data-target="#addWebinarModal"
							data-backdrop="static">{{ trans('site.add-webinar') }}</button>

					@if (in_array($course->id, [17, 23]) || $course->is_free) {{-- check if webinar-pakke --}}
						<button class="btn btn-success margin-bottom" data-toggle="modal" data-target="#webinarEmailTempModal"
							data-backdrop="static">Email Template</button>
					@endif
				</div>

				@foreach($course->webinars()->active()->get()->chunk(3) as $webinar_chunk)
					<div class="col-sm-12 webinar-list-container">
						@foreach($webinar_chunk as $webinar)
							<div class="col-md-4">
								<div class="panel panel-default">
									<div class="panel-body">
										{{--<div class="webinar-image" style="background-image:url('{{ $webinar->image }}')"></div>--}}
										<div class="webinar-img">
											<img src="{{ $webinar->image ? $webinar->image : asset('images/no_image.png') }}">
										</div>
										<div class="pull-right">
											<a class="btn btn-xs btn-info editWebinarBtn"
											   data-toggle="modal"
											   data-target="#editWebinarModal"
											   data-action="{{ route('admin.webinar.update', $webinar->id) }}"
											   data-title="{{ $webinar->title }}"
											   data-description="{{ $webinar->description }}"
											   data-start_date="{{ strftime('%Y-%m-%dT%H:%M:%S', strtotime($webinar->start_date)) }}"
											   data-image="{{ $webinar->image }}"
											   data-link="{{ $webinar->link }}"
											>
												<i class="fa fa-pencil"></i></a>

											<a class="btn btn-xs btn-danger deleteWebinarBtn"
											   data-toggle="modal"
											   data-target="#deleteWebinarModal"
											   data-action="{{ route('admin.webinar.delete', $webinar->id) }}"
											   data-title="{{ $webinar->title }}"
											><i class="fa fa-trash"></i></a>
											<a class="btn btn-xs btn-warning hideWebinarBtn"
											   data-toggle="modal"
											   data-target="#hideWebinarModal"
											   data-action="{{ route('admin.webinar.update-field', $webinar->id) }}"
											   data-title="{{ $webinar->title }}"
											><i class="fa fa-eye"></i></a>
										</div>
										<strong>{{ $webinar->title }}</strong>
										<br />
										{!! nl2br($webinar->description) !!}
										<br />
										<p style="line-height: 1.8em; margin-top: 7px; word-break: break-all">
											<i class="fa fa-link"></i>&nbsp;&nbsp;{{ $webinar->link }} <br />
											@if ($webinar->id != 24)
												<i class="fa fa-calendar-o"></i>&nbsp;&nbsp;{{ $webinar->start_date }} <br />
										@endif

											<i class="fa fa-calendar-o"></i> <b>Scheduled Registration:</b>
											<span>{{ $webinar->schedule ? $webinar->schedule->date : NULL }}</span> <br>
											<i class="fa fa-user-o"></i> <b> Host:</b>
											<span>{{ $webinar->host }}</span>

										<!-- <i class="fa fa-users"></i>&nbsp;&nbsp;Attendees (20) -->
										</p>

										<button class="btn btn-primary btn-xs makeReplayBtn"
										data-toggle="modal" data-target="#makeReplayModal"
										data-action="{{ route('admin.webinar.make-replay', ['id' => $webinar->id]) }}"
										data-replay="{{ $webinar->set_as_replay }}">
											{{ trans('site.make-as-replay') }}
										</button>

										<button class="btn btn-warning btn-xs viewRegistrantsBtn"
												data-webinar-id="{{ $webinar->id }}">
											View Registrants
										</button>

										<button class="btn btn-info btn-xs scheduleWebinarBtn" data-toggle="modal"
										data-target="#scheduleWebinarModal" 
										data-action="{{ route('admin.webinar.schedule', $webinar->id) }}"
										data-date="{{ $webinar->schedule
											? strftime('%Y-%m-%d', strtotime($webinar->schedule->date))
											: '' }}">
											Schedule
										</button>

										@if (in_array($course->id, [17, 23, 89]) || $course->is_free) {{-- check if webinar-pakke --}}
											<?php
												$webinarEmailOut = \App\Http\AdminHelpers::getWebinarEmailOut($webinar->id, $course->id);
											?>
											<button class="btn btn-success btn-xs emailOutBtn loadScriptButton" data-toggle="modal"
													data-target="#webinarEmailOutModal" data-backdrop="static"
											data-action="{{ route('admin.webinar.email-out', [$webinar->id, $course->id]) }}"
											data-send-date="{{ $webinarEmailOut
												? strftime('%Y-%m-%d', strtotime($webinarEmailOut->send_date))
												: '' }}"
											data-message="{{ $webinarEmailOut ? $webinarEmailOut->message
											 	: App\Settings::webinarEmailTemplate()}}"
											data-subject="{{ $webinarEmailOut ? $webinarEmailOut->subject : '' }}">
												Set Email Out
											</button>
										@endif

										{{-- check if webinar-pakke --}}
										@if($course->is_webinar_pakke)
											<button class="btn btn-xs btn-info registerLearnersToWebinarBtn"
													data-target="#registerLearnersToWebinarModal"
													data-toggle="modal"
													data-action="{{ route('admin.webinar.auto-register-learners',
													[$webinar->id, $webinar->course_id]) }}">
												Register Learners
											</button>
										@endif

										<hr />
										<div >
											<?php 
												$selectEditor = $editors->filter(function ($value, $key) use($webinar){
													return !in_array($value['id'], $webinar->webinar_editors->pluck('editor_id')->toArray());
												});
											?>
											<button class="btn btn-xs btn-primary margin-bottom addPresenterBtn pull-right"
													data-toggle="modal"
													data-target="#addPresenterModal"
													data-title="{{ trans('site.add-editor-to').' '.$webinar->title }}"
													data-editors = "{{ $selectEditor }}"
													data-editors_count = "{{ $selectEditor->keys()->last() }}"
													data-action="{{ route('admin.webinar.webinar-editor.store', $webinar->id) }}">
												{{ trans('site.add-editor') }}</button>
											<strong style="font-size: 15px">{{ trans_choice('site.editors', 1) }}</strong> <br />
											<div class="clearfix"></div>

											@foreach( $webinar->webinar_editors as $webinar_editor )
												<div>
													<div class="pull-right">
														<a class="btn btn-xs btn-info addPresenterBtn"
														   data-toggle="modal"
														   data-edit="1"
														   data-target="#addPresenterModal"
														   data-editor_id="{{ $webinar_editor->editor ? $webinar_editor->editor->id : '' }}"
														   data-first_name="{{ $webinar_editor->editor ? $webinar_editor->editor->first_name : $webinar_editor->name }}"
														   data-last_name="{{ $webinar_editor->editor ? $webinar_editor->editor->last_name : '' }}"
														   data-title="{{ $webinar_editor->editor 
														   ? trans('site.edit-editor').' '.$webinar_editor->editor->first_name.' '.$webinar_editor->editor->last_name
														   : trans('site.edit-editor') . ' ' . $webinar_editor->name }}"
														   data-editors = "{{ $selectEditor }}"
														   data-editors_count = "{{ $selectEditor->keys()->last() }}"
														   data-presenter_url="{{ $webinar_editor->presenter_url }}"
														   data-action="{{ route('admin.webinar.webinar-editor.update', $webinar_editor->id) }}"
														>
															<i class="fa fa-pencil"></i></a>

														<a class="btn btn-xs btn-danger deletePresenterBtn"
														   data-toggle="modal"
														   data-target="#deletePresenterModal"
														   data-first_name="{{ $webinar_editor->editor ? $webinar_editor->editor->first_name : '' }}"
														   data-last_name="{{ $webinar_editor->editor ? $webinar_editor->editor->last_name : '' }}"
														   data-action="{{ route('admin.webinar.webinar-editor.delete', $webinar_editor->id) }}">
															<i class="fa fa-trash"></i></a>
													</div>
													<div class="webinar-presenter" style="word-break: break-word;">
														@if ($webinar_editor->editor)
															<div class="presenter-thumb" style="background-image: url('{{ $webinar_editor->editor->profile_image  }}')"></div>
															{{ $webinar_editor->editor->first_name }} {{ $webinar_editor->editor->last_name }} <br />
															{{ $webinar_editor->editor->email }} <br>
														@else
															{{ $webinar_editor->name }} <br>
														@endif
														{{ $webinar_editor->presenter_url }}
													</div>
												</div>
												<br />
											@endforeach
										</div>
									</div>
								</div>
							</div>
						@endforeach
					</div>
				@endforeach

			</div>
		</div>
	</div>
	<div class="clearfix"></div>
</div>

<div id="makeReplayModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="no-margin">{{ trans('site.make-as-replay') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					{{ method_field('PUT') }}
					<div class="form-group">
						<label>{{ trans('site.make-as-replay-question') }}</label>
						<select name="set_as_replay" class="form-control" required>
							<option value="" disabled selected>Select Option</option>
							<option value="1">Yes</option>
							<option value="0">No</option>
						</select>
					</div>
					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="scheduleWebinarModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Schedule Webinar</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action=""
					  onsubmit="disableSubmit(this)">
					{{csrf_field()}}

					<div class="form-group">
						<label>Date</label>
						<input type="date" name="date" class="form-control" required>
					</div>

					<div class="form-group">
						<label>
							Run the cron after save?
						</label>
						<br>
						<input type="checkbox" data-toggle="toggle" data-on="Yes"
							   class="for-sale-toggle" data-off="No"
							   name="run_cron" data-width="84">
					</div>

					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ trans('site.submit') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="viewRegistrantModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="no-margin">Registrants</h4>
			</div>
			<div class="modal-body">
				<table class="table" id="registrantTable">
					<thead>
						<tr>
							<th width="200">Learner</th>
							<th>Join Url</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					{{-- show data here --}}
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<div id="removeParticipantModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="no-margin">Remove Participant</h4>
			</div>
			<div class="modal-body">
					<p>Are you sure you want to remove this participant?</p>

					<div class="text-right">
						<button class="btn btn-danger">{{ trans('site.delete') }}</button>
					</div>
			</div>
		</div>
	</div>
</div>

<!-- Delete Presenter Modal -->
<div id="deletePresenterModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('site.delete-presenter') }} <em></em></h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
          {{ csrf_field() }}
          {{ method_field('DELETE') }}
          <p>{{ trans('site.delete-presenter-question') }}</p>
          <div class="text-right">
          	<button type="submit" class="btn btn-danger">{{ trans('site.delete-presenter') }}</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

<!-- Edit Presenter Modal -->
<div id="editPresenterModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
      	<h4 class="no-margin">{{ trans('site.edit-presenter') }} <em></em></h4>
      </div>
      <div class="modal-body">
      	<form method="POST" enctype="multipart/form-data" action="">
      		{{ csrf_field() }}
      		{{ method_field('PUT') }}
	      	<div class="form-group">
	      		<div class="text-center">
			        <div class="user-thumb-image image-file margin-bottom">
			          <div class="image-preview" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
			          <input type="file" accept="image/*" name="image" accept="image/jpg, image/jpeg, image/png">
			        </div>
		        </div>
	      	</div>
	      	<div class="form-group">
	      		<label>{{ trans('site.first-name') }}</label>
	      		<input type="text" name="first_name" required class="form-control"> 
	      	</div>
	      	<div class="form-group">
	      		<label>{{ trans('site.last-name') }}</label>
	      		<input type="text" name="last_name" required class="form-control"> 
	      	</div>
	      	<div class="form-group">
	      		<label>{{ trans_choice('site.emails', 1) }}</label>
	      		<input type="email" name="email" required class="form-control"> 
	      	</div>
	      	<div class="text-right">
				<button type="submit" class="btn btn-primary">{{ trans('site.update-presenter') }}</button>
	      	</div>
      	</form>
      </div>
    </div>
   </div>
</div>

<!-- Add Presenter Modal -->
<div id="addPresenterModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
      	<h4 class="no-margin"><em></em></h4>
      </div>
      <div class="modal-body">
      	<form method="POST" enctype="multipart/form-data" action="" onsubmit="disableSubmit(this)">
      		{{ csrf_field() }}
	      	<div class="form-group">
				<label>{{ trans('site.assign-editor') }}</label>
				<select name="editor_id" class="form-control select2">
					<option value="" disabled="" selected>-- Select Editor --</option>
				</select>
			</div>
			<div class="form-group">
				<label>Name</label>
				<input type="text" name="name" class="form-control"></input>
			</div>
			<div class="form-group">
				<label>{{ trans('site.presenter-url') }}</label>
				<input type="URL" name="presenter_url" class="form-control" maxlength="1000"></input>
			</div>
	      	<div class="text-right">
				<button type="submit" class="btn btn-primary">{{ ucwords(trans('site.save')) }}</button>
	      	</div>
      	</form>
      </div>
    </div>
   </div>
</div>


<!-- Add Webinar Modal -->
<div id="addWebinarModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('site.add-webinar') }}</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('admin.webinar.store') }}" enctype="multipart/form-data"
		onsubmit="disableSubmit(this)">
          {{ csrf_field() }}
          <input type="hidden" name="course_id" value="{{ $course->id }}">
          <div class="form-group">
          	<label>{{ trans('site.title') }}</label>
          	<input type="text" name="title" class="form-control" required>
          </div>
          <div class="form-group">
          	<label>{{ trans('site.description') }}</label>
          	<textarea class="form-control" name="description" required rows="6"></textarea>
          </div>
		  <div class="form-group">
			<label>Host</label>
			<input type="text" name="host" class="form-control">
		  </div>
          <div class="form-group">
          	<label>{{ trans('site.start-date') }}</label>
          	<input type="datetime-local" name="start_date" class="form-control" required>
          </div>
			<div class="form-group">
				<label>{{ 'Webinar ID' }}</label>
				<input type="text" name="link" class="form-control">
			</div>

          <div class="form-group">
            {{--<label id="course-image">Image</label>
            <div class="course-form-image image-file margin-bottom">
              <div class="image-preview" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
              <input type="file" accept="image/*" name="image" accept="image/jpg, image/jpeg, image/png">
            </div>--}}
			  <label for="image">{{ trans('site.image') }}</label>
			  <input type="file" accept="image/*" name="image" id="webinarImage" accept="image/jpg, image/jpeg, image/png"
			  onchange="readURL(this)">

			  <input type="hidden" name="x" />
			  <input type="hidden" name="y" />
			  <input type="hidden" name="w" />
			  <input type="hidden" name="h" />
          </div>

			<div class="image_container">
				<img id="webinarImagePreview" src="#" alt="your image" />
			</div>

          <div class="text-right">
          	<button type="submit" class="btn btn-primary">{{ trans('site.add-webinar') }}</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>


<!-- Edit Webinar Modal -->
<div id="editWebinarModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('site.edit-webinar') }} <em></em></h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <div class="form-group">
          	<label>{{ trans('site.title') }}</label>
          	<input type="text" name="title" class="form-control" required>
          </div>
          <div class="form-group">
          	<label>{{ trans('site.description') }}</label>
          	<textarea class="form-control" name="description" required rows="6"></textarea>
          </div>
		  <div class="form-group">
			<label>Host</label>
			<input type="text" name="host" class="form-control">
		  </div>
          <div class="form-group">
          	<label>{{ trans('site.start-date') }}</label>
          	<input type="datetime-local" name="start_date" class="form-control" required>
          </div>
          <div class="form-group">
          	<label>{{ 'Webinar ID' }}</label>
          	<input type="text" name="link" class="form-control">
          </div>

          <div class="form-group">
            {{--<label id="course-image">Image</label>
            <div class="course-form-image image-file margin-bottom">
              <div class="image-preview" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
              <input type="file" accept="image/*" name="image" accept="image/jpg, image/jpeg, image/png">
            </div>--}}
			  <label for="image">{{ trans('site.image') }}</label>
			  <input type="file" accept="image/*" name="image" id="webinarImageEdit" accept="image/jpg, image/jpeg, image/png"
					 onchange="readURLEdit(this)">

			  <input type="hidden" name="x" />
			  <input type="hidden" name="y" />
			  <input type="hidden" name="w" />
			  <input type="hidden" name="h" />
          </div>

			<div class="image_container_edit">
				<img id="webinarImagePreviewEdit" src="#" alt="your image" />
			</div>

          <div class="text-right">
          	<button type="submit" class="btn btn-primary">{{ trans('site.update-webinar') }}</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>


<!-- Delete Webinar Modal -->
<div id="deleteWebinarModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('site.delete-webinar') }} <em></em></h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
          {{ csrf_field() }}
          <p>{{ trans('site.delete-webinar-question') }}</p>
          <div class="text-right">
          	<button type="submit" class="btn btn-danger">{{ trans('site.delete-webinar') }}</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

<!-- Hide Webinar Modal -->
<div id="hideWebinarModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Hide Webinar <em></em></h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" enctype="multipart/form-data">
					{{ csrf_field() }}
					<input type="hidden" name="field" value="status">
					<input type="hidden" name="value" value="0">
					<p>
						Are you sure to hide this webinar?
					</p>
					<div class="text-right">
						<button type="submit" class="btn btn-primary">
							{{ trans('site.save') }}
						</button>
					</div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="webinarEmailTempModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="no-margin">Email Template</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.settings.update.webinar_email_template') }}">
					{{ csrf_field() }}
					<textarea class="form-control tinymce" name="webinar_email_template">{{ App\Settings::webinarEmailTemplate() }}</textarea>
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="webinarEmailOutModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="no-margin">Webinar Email</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}

					<div class="form-group">
						<label>
							Subject
						</label>
						<input type="text" class="form-control" name="subject" required>
					</div>
					<div class="form-group">
						<label>Send Date</label>
						<input type="date" class="form-control" name="send_date" required>
					</div>
					<div class="form-group">
						<label>Message</label>
						<textarea class="form-control tinymce" name="message" id="modalEditor"></textarea>
					</div>

					<div class="form-group">
						<label>Send test to</label>
						<input type="email" name="test_email" class="form-control">
					</div>

					<div class="text-right margin-top">
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="registerLearnersToWebinarModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Register Learners to Webinar</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action=""
					  onsubmit="disableSubmit(this)">
					{{csrf_field()}}

					<div class="form-group">
						<label>Date</label>
						<input type="date" name="date" class="form-control" required>
					</div>

					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ trans('site.submit') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')
	<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/cropper/4.0.0/cropper.js"></script>
<script>

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#webinarImagePreview').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
            $('#webinarImagePreview').cropper("destroy");
            setTimeout(initCropper, 100);
        } else {
			$(".image_container").hide();
		}
    }

    function initCropper() {

        var container = $(".image_container");
        container.show();

        var image = $('#webinarImagePreview');

        var cropper = image.cropper({
            zoomable: false,
            background:false,
            movable:false,
            /*ready: function (e) {
                $(this).cropper('setData', {
                    height: 467,
                    rotate: 0,
                    scaleX: 1,
                    scaleY: 1,
                    width:  573,
                    x:      469,
                    y:      19
                });
                $(this).cropper('setCanvasData', {
                    width:573,
                    height: 467,
					top:0,
					left:0
                });
            },*/
            crop: function(event) {
                var modal = $("#addWebinarModal");
                modal.find('input[name=x]').val(event.detail.x);
                modal.find('input[name=y]').val(event.detail.y);
                modal.find('input[name=w]').val(event.detail.width);
                modal.find('input[name=h]').val(event.detail.height);
            }
        });
    }

    function readURLEdit(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#webinarImagePreviewEdit').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
            $('#webinarImagePreviewEdit').cropper("destroy");
            setTimeout(initCropperEdit, 100);
        } else {
            $(".image_container_edit").hide();
        }
	}

    function initCropperEdit() {

        var container = $(".image_container_edit");
        container.show();

        var image = $('#webinarImagePreviewEdit');

        var cropper = image.cropper({
            zoomable: false,
            background:false,
            movable:false,
            /*ready: function (e) {
                $(this).cropper('setData', {
                    height: 467,
                    rotate: 0,
                    scaleX: 1,
                    scaleY: 1,
                    width:  573,
                    x:      469,
                    y:      19
                });
                $(this).cropper('setCanvasData', {
                    width:573,
                    height: 467,
					top:0,
					left:0
                });
            },*/
            crop: function(event) {
                var modal = $("#editWebinarModal");
                modal.find('input[name=x]').val(event.detail.x);
                modal.find('input[name=y]').val(event.detail.y);
                modal.find('input[name=w]').val(event.detail.width);
                modal.find('input[name=h]').val(event.detail.height);
            }
        });
    }

    let deleteParticipantLink = "";

	$(document).ready(function(){

		$('.deletePresenterBtn').click(function(){
			var form = $('#deletePresenterModal form');
			var action = $(this).data('action');
			var first_name = $(this).data('first_name');
			var last_name = $(this).data('last_name');

			$('#deletePresenterModal em').text(first_name + ' ' + last_name);
			form.attr('action', action);
		});

		$('.editPresenterBtn').click(function(){
			var modal = $('#editPresenterModal');
			var image = $(this).data('image');
			var first_name = $(this).data('first_name');
			var last_name = $(this).data('last_name');
			var email = $(this).data('email');
			var action = $(this).data('action');
			modal.find('form').attr('action', action);
			modal.find('.image-preview').css('background-image', 'url('+image+')');
			modal.find('em').text(first_name + ' ' + last_name);
			modal.find('input[name=first_name]').val(first_name);
			modal.find('input[name=last_name]').val(last_name);
			modal.find('input[name=email]').val(email);
		});

		$('.addPresenterBtn').click(function(){
			var modal = $('#addPresenterModal');
			var title = $(this).data('title');
			var action = $(this).data('action');
			var editors = $(this).data('editors');
			var editors_count = $(this).data('editors_count')
			var edit = $(this).data('edit');
			modal.find('form').trigger('reset');
			modal.find('select[name=editor_id]').html('<option value="" selected>-- Select Editor --</option>');
			
			if(edit){ //add selected editor on drpdwn && set presenter_url
				var first_name = $(this).data('first_name')
				var last_name = $(this).data('last_name')
				var editor_id = $(this).data('editor_id')
				var presenter_url = $(this).data('presenter_url')
				if(editor_id) {
					modal.find('select[name=editor_id]').append('<option value="'+editor_id+'" selected>'+first_name+' '+last_name+'</option>');
				} else {
					modal.find('[name=name]').val(first_name);
				}
				
				modal.find('[name=presenter_url]').val(presenter_url);
			}
			for(var i =0; i <= editors_count; i++){
				if(editors[i]){
					modal.find('select[name=editor_id]').append('<option value="'+editors[i]['id']+'">'+editors[i]['first_name']+' '+editors[i]['last_name']+'</option>');
				}
			}
			modal.find('em').text(title);
			modal.find('form').attr('action', action);
		});

		$(".makeReplayBtn").click(function(){
            var modal = $('#makeReplayModal');
            var action = $(this).data('action');
            var replay = $(this).data('replay');
            modal.find('form').attr('action', action);
            modal.find('form').find('select').val(replay);
		});

		let registrantTable = $("#registrantTable").DataTable();

		$(".viewRegistrantsBtn").click(function(){
			//let registrants = $(this).data('registrants');
			let webinar_id = $(this).data('webinar-id');
			let self = this;
            self.disabled = true;

			// Clear the DataTable before adding new data
            registrantTable.clear().draw();

            $.ajax({
                type:'GET',
                url: '/webinar/' + webinar_id + '/registrant/list',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(data){
                    $("#viewRegistrantModal").modal('show');
                    $.each(data, function(k, v) {
                        registrantTable.row.add([
                            v.user.full_name,
                            v.join_url,
                            "<button class='btn btn-danger btn-xs removeParticipantBtn' data-toggle='modal' data-target='#removeParticipantModal'" +
                            " onclick='removeParticipant("+v.id+", this)' data-action='/webinar/registrant/" + v.id + "/delete'>Delete</button>"
                        ]).draw(false);
                    });
                    self.disabled = false;

                },
                error: function(data) {
                    console.log("error");
                    console.log(data);
                }
            });

			/*$.each(registrants, function(k, v) {
				registrantTable.row.add([
						v.user.full_name,
						v.join_url,
						"<button class='btn btn-danger btn-xs removeParticipantBtn' data-toggle='modal' data-target='#removeParticipantModal'" +
						" onclick='removeParticipant("+v.id+", this)' data-action='/webinar/registrant/" + v.id + "/delete'>Delete</button>"
				]).draw(false);
			});

			console.log(registrants);*/
		});

		$(".scheduleWebinarBtn").click(function(){
			let action = $(this).data('action');
			let date = $(this).data('date');
			let modal = $("#scheduleWebinarModal");

			modal.find('form').attr('action', action);
			modal.find("[name=date]").val(date);
		});

		$('.editWebinarBtn').click(function(){
			var form = $('#editWebinarModal form');
			var action = $(this).data('action');
			var title = $(this).data('title');
			var description = $(this).data('description');
			var start_date = $(this).data('start_date');
			var image = $(this).data('image');
			var link = $(this).data('link');

			$('#editWebinarModal em').text(title);
			form.attr('action', action);
			form.find('input[name=title]').val(title);
			form.find('textarea[name=description]').val(description);
			form.find('input[name=start_date]').val(start_date);
			form.find('input[name=link]').val(link);
			form.find('.image-preview').css('background-image', 'url('+image+')');
		});


		$('.deleteWebinarBtn').click(function(){
			var form = $('#deleteWebinarModal form');
			var action = $(this).data('action');
			var title = $(this).data('title');

			$('#deleteWebinarModal em').text(title);
			form.attr('action', action);
		});

		$(".hideWebinarBtn").click(function(){
		    let modal = $('#hideWebinarModal');
            let form = modal.find('form');
            let action = $(this).data('action');
            let title = $(this).data('title');

            modal.find('em').text(title);
            form.attr('action', action);
		});

		$(".emailOutBtn").click(function(){
		    let modal 		= $("#webinarEmailOutModal");
		    let action 		= $(this).data('action');
		    let form 		= modal.find('form');
            let subject 	= $(this).data('subject');
		    let send_date 	= $(this).data('send-date');
		    let message 	= $(this).data('message');

		    form.attr('action', action);
		    form.find('[name=send_date]').val(send_date);
            form.find('[name=subject]').val(subject);

            //tinymce.get('modalEditor').setContent(message);
			setTimeout(function(){
				setEditorContent('modalEditor', message)
			}, 500);
		});

		$(".registerLearnersToWebinarBtn").click(function(){
            let modal 	= $("#registerLearnersToWebinarModal");
            let action 	= $(this).data('action');
            let form	= modal.find('form');

            form.attr('action', action);
		});

		$("#removeParticipantModal .btn-danger").click(function() {
			$(this).attr("disabled", "disabled");
			$.ajax({
				type:'DELETE',
				url: deleteParticipantLink,
				headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
				success: function(data){
					console.log(data);
					registrantTable.row('.selected').remove().draw(false);
					$("#removeParticipantModal .close").trigger("click");
					$("#removeParticipantModal .btn-danger").removeAttr("disabled");
				},
				error: function(data) {
					alert(data.responseJSON.message);
					$("#removeParticipantModal .close").trigger("click");
					$("#removeParticipantModal .btn-danger").removeAttr("disabled");
				}
			});
		});
	});

	function removeParticipant(participant_id, self) {
		$("#registrantTable").find("tbody").find("tr").removeClass("selected");
		$(self).closest("tr").addClass("selected");
		deleteParticipantLink = '/webinar/registrant/' + participant_id + '/delete';
	}
</script>
@stop