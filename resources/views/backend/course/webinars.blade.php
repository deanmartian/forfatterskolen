@extends('backend.layout')

@section('title')
<title>Webinars &rsaquo; {{$course->title}} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
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

				@foreach($course->webinars->chunk(3) as $webinar_chunk)
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
											   data-action="{{ route('admin.webinar.destroy', $webinar->id) }}"
											   data-title="{{ $webinar->title }}"
											><i class="fa fa-trash"></i></a>
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
										<!-- <i class="fa fa-users"></i>&nbsp;&nbsp;Attendees (20) -->
										</p>

										<button class="btn btn-primary btn-xs makeReplayBtn"
										data-toggle="modal" data-target="#makeReplayModal"
										data-action="{{ route('admin.webinar.make-replay', ['webinar_id' => $webinar->id]) }}"
										data-replay="{{ $webinar->set_as_replay }}">
											{{ trans('site.make-as-replay') }}
										</button>

										@if (in_array($course->id, [17, 23]) || $course->is_free) {{-- check if webinar-pakke --}}
											<?php
												$webinarEmailOut = \App\Http\AdminHelpers::getWebinarEmailOut($webinar->id, $course->id);
											?>
											<button class="btn btn-success btn-xs emailOutBtn" data-toggle="modal"
													data-target="#webinarEmailOutModal" data-backdrop="static"
											data-action="{{ route('admin.webinar.email-out', [$webinar->id, $course->id]) }}"
											data-send-date="{{ $webinarEmailOut
												? strftime('%Y-%m-%d', strtotime($webinarEmailOut->send_date))
												: '' }}"
											data-message="{{ $webinarEmailOut ? $webinarEmailOut->message
											 	: App\Settings::webinarEmailTemplate()}}">
												Set Email Out
											</button>
										@endif

										<hr />
										<div >
											<button class="btn btn-xs btn-primary margin-bottom addPresenterBtn pull-right"
													data-toggle="modal"
													data-target="#addPresenterModal"
													data-title="{{ $webinar->title }}"
													data-action="{{ route('admin.webinar.webinar-presenter.store', ['webinar_id' => $webinar->id]) }}">
												{{ trans('site.add-presenter') }}</button>
											<strong style="font-size: 15px">{{ trans('site.presenters') }}</strong> <br />
											<div class="clearfix"></div>

											@foreach( $webinar->webinar_presenters as $webinar_presenter )
												<div>
													<div class="pull-right">
														<a class="btn btn-xs btn-info editPresenterBtn"
														   data-toggle="modal"
														   data-target="#editPresenterModal"
														   data-first_name="{{ $webinar_presenter->first_name }}"
														   data-last_name="{{ $webinar_presenter->last_name }}"
														   data-email="{{ $webinar_presenter->email }}"
														   data-action="{{ route('admin.webinar.webinar-presenter.update', ['webinar_id' =>$webinar->id, 'id' => $webinar_presenter->id]) }}"
														>
															<i class="fa fa-pencil"></i></a>

														<a class="btn btn-xs btn-danger deletePresenterBtn"
														   data-toggle="modal"
														   data-target="#deletePresenterModal"
														   data-first_name="{{ $webinar_presenter->first_name }}"
														   data-last_name="{{ $webinar_presenter->last_name }}"
														   data-action="{{ route('admin.webinar.webinar-presenter.destroy', ['webinar_id' =>$webinar->id, 'id' => $webinar_presenter->id]) }}">
															<i class="fa fa-trash"></i></a>
													</div>
													<div class="webinar-presenter">
														<div class="presenter-thumb" style="background-image: url('{{ $webinar_presenter->image  }}')"></div>
														{{ $webinar_presenter->first_name }} {{ $webinar_presenter->last_name }} <br />
														{{ $webinar_presenter->email }}
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
				<form method="POST" enctype="multipart/form-data" action="">
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

<!-- Delete Presenter Modal -->
<div id="deletePresenterModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('site.delete-presenter') }} <em></em></h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="" enctype="multipart/form-data">
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
      	<h4 class="no-margin">{{ trans('site.add-presenter-to') }} <em></em></h4>
      </div>
      <div class="modal-body">
      	<form method="POST" enctype="multipart/form-data" action="">
      		{{ csrf_field() }}
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
				<button type="submit" class="btn btn-primary">{{ ucwords(trans('site.add-presenter')) }}</button>
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
        <form method="POST" action="{{ route('admin.webinar.store') }}" enctype="multipart/form-data">
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
          	<label>{{ trans('site.start-date') }}</label>
          	<input type="datetime-local" name="start_date" class="form-control" required>
          </div>
          <div class="form-group">
          	<label>{{ strtoupper(trans('site.url')) }}</label>
          	<input type="url" name="link" class="form-control" required>
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
        <form method="POST" action="" enctype="multipart/form-data">
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
          	<label>{{ trans('site.start-date') }}</label>
          	<input type="datetime-local" name="start_date" class="form-control" required>
          </div>
          <div class="form-group">
          	<label>{{ strtoupper(trans('site.url')) }}</label>
          	<input type="url" name="link" class="form-control" required>
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
        <form method="POST" action="" enctype="multipart/form-data">
          {{ csrf_field() }}
          {{ method_field('DELETE') }}
          <p>{{ trans('site.delete-webinar-question') }}</p>
          <div class="text-right">
          	<button type="submit" class="btn btn-danger">{{ trans('site.delete-webinar') }}</button>
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
					<textarea class="form-control editor" name="webinar_email_template">{{ App\Settings::webinarEmailTemplate() }}</textarea>
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
				<form method="POST" action="">
					{{ csrf_field() }}

					<div class="form-group">
						<label>Send Date</label>
						<input type="date" class="form-control" name="send_date" required>
					</div>
					<div class="form-group">
						<label>Message</label>
						<textarea class="form-control editor" name="message"></textarea>
					</div>
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/cropper/4.0.0/cropper.js"></script>
	<script type="text/javascript" src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
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

		$(".emailOutBtn").click(function(){
		    let modal 		= $("#webinarEmailOutModal");
		    let action 		= $(this).data('action');
		    let form 		= modal.find('form');
		    let send_date 	= $(this).data('send-date');
		    let message 	= $(this).data('message');

		    form.attr('action', action);
		    form.find('[name=send_date]').val(send_date);

            tinymce.activeEditor.setContent(message);
		});

        // tinymce
        let editor_config = {
            path_absolute: "{{ URL::to('/') }}",
            height: '20em',
            selector: '.editor',
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
                if (type === 'image') {
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
	});
</script>
@stop