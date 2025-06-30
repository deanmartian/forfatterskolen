@extends('backend.layout')

@section('title')
<title>Free Courses &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/cropper.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/toastr/toastr.min.css') }}">
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
<div class="page-toolbar">
	<h3><i class="fa fa-file-text-o"></i> {{ trans('site.free-courses') }}</h3>
	<div class="clearfix"></div>
</div>

<div class="margin-top">

    <ul class="nav nav-tabs margin-top">
        <li @if( Request::input('tab') != 'webinar' ) class="active" @endif><a href="?tab=course">{{ trans_choice('site.courses', 1) }}</a></li>
        <li @if( Request::input('tab') == 'webinar' ) class="active" @endif><a href="?tab=webinar">{{ trans('site.webinars') }}</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade in active">
            @if( Request::input('tab') != 'webinar' )
                <div class="col-md-12 margin-bottom margin-top">
                    <button class="btn btn-success" data-toggle="modal" data-target="#addFreeCourseModal">{{ trans('site.add-free-course') }}</button>
                </div>
                @foreach($freeCourses->chunk(3) as $freeCourses)
                    <div class="col-sm-12">
                        <div class="row">
                            @foreach( $freeCourses as $freeCourse )
                            <div class="col-md-4">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div class="pull-right">
                                            <button type="button" data-toggle="modal" data-target="#editFreeCourseModal" class="btn btn-info btn-xs editFreeCourseBtn" data-action="{{ route('admin.free-course.update', $freeCourse->id) }}" data-title="{{ $freeCourse->title }}" data-description="{{ $freeCourse->description }}" data-url="{{ $freeCourse->url }}" data-image="{{ $freeCourse->course_image }}"><i class="fa fa-pencil"></i></button>
                                            <button type="button" data-target="#deleteFreeCourseModal" data-toggle="modal" class="btn btn-danger btn-xs deleteFreeCourseBtn" data-action="{{ route('admin.free-course.destroy', $freeCourse->id) }}" data-title="{{ $freeCourse->title }}"><i class="fa fa-trash"></i></button>
                                        </div>
                                        <h4 class="margin-bottom">{{ $freeCourse->title }}</h4>
                                        <div class="margin-top">
                                            {!! nl2br($freeCourse->description) !!}
                                            <br />
                                            <br />
                                            {{ strtoupper(trans('site.url')) }}: <a href="{{ $freeCourse->url }}" target="_blank">{{ $freeCourse->url }}</a>
                                            @if( $freeCourse->course_image )
                                            <br />
                                            <img src="{{ $freeCourse->course_image }}" height="150px" class="margin-top">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-sm-12 margin-top">
                    <button class="btn btn-primary margin-bottom" data-toggle="modal" data-target="#addWebinarModal"
                            data-backdrop="static">{{ trans('site.add-webinar') }}</button>
                </div>

                @foreach($freeWebinars->chunk(3) as $webinar_chunk)
                    <div class="col-sm-12 webinar-list-container">
                        @foreach($webinar_chunk as $webinar)
                            <div class="col-md-4">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div class="webinar-img">
                                            <img src="{{ $webinar->image ? $webinar->image : asset('images/no_image.png') }}">
                                        </div>
                                        <div class="pull-right button-container">
                                            <?php
                                            $webinarUrl = "https://forfatterskolen.no/free-webinar/".$webinar->id;
                                            ?>
                                            <input type="text" value="{{ $webinarUrl }}"
                                                   style="position: absolute; left: -10000px;">
                                            <button class="btn btn-xs btn-success copyToClipboard">
                                                <i class="fa fa-clipboard"></i>
                                            </button>
                                            <a class="btn btn-xs btn-info editWebinarBtn"
                                               data-toggle="modal"
                                               data-target="#editWebinarModal"
                                               data-action="{{ route('admin.free-webinar.update', $webinar->id) }}"
                                               data-title="{{ $webinar->title }}"
                                               data-description="{{ $webinar->description }}"
                                               data-start_date="{{ strftime('%Y-%m-%dT%H:%M:%S', strtotime($webinar->start_date)) }}"
                                               data-image="{{ $webinar->image }}"
                                               data-gtwebinar_id="{{ $webinar->gtwebinar_id }}"
                                            >
                                                <i class="fa fa-pencil"></i></a>

                                            <a class="btn btn-xs btn-danger deleteWebinarBtn"
                                               data-toggle="modal"
                                               data-target="#deleteWebinarModal"
                                               data-action="{{ route('admin.free-webinar.destroy', $webinar->id) }}"
                                               data-title="{{ $webinar->title }}"
                                            ><i class="fa fa-trash"></i></a>
                                        </div>
                                        <strong>{{ $webinar->title }}</strong>
                                        <br />
                                        {!! nl2br($webinar->description) !!}
                                        <br />
                                        <p style="line-height: 1.8em; margin-top: 7px; word-break: break-all">
                                            <i class="fa fa-desktop"></i>&nbsp;&nbsp;{{ $webinar->gtwebinar_id }} <br />
                                            <i class="fa fa-calendar-o"></i>&nbsp;&nbsp;{{ $webinar->start_date }} <br />
                                        </p>

                                        <hr>

                                        <div >
                                            <button class="btn btn-xs btn-primary margin-bottom addPresenterBtn pull-right"
                                                    data-toggle="modal"
                                                    data-target="#addPresenterModal"
                                                    data-title="{{ $webinar->title }}"
                                                    data-action="{{ route('admin.free-webinar.presenter.store', ['id' => $webinar->id]) }}">
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
                                                           data-image="{{ $webinar_presenter->image }}"
                                                           data-action="{{ route('admin.free-webinar.presenter.update', ['webinar_id' =>$webinar->id, 'id' => $webinar_presenter->id]) }}"
                                                        >
                                                            <i class="fa fa-pencil"></i></a>

                                                        <a class="btn btn-xs btn-danger deletePresenterBtn"
                                                           data-toggle="modal"
                                                           data-target="#deletePresenterModal"
                                                           data-first_name="{{ $webinar_presenter->first_name }}"
                                                           data-last_name="{{ $webinar_presenter->last_name }}"
                                                           data-action="{{ route('admin.free-webinar.presenter.delete', ['webinar_id' =>$webinar->id, 'id' => $webinar_presenter->id]) }}">
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

            @endif
        </div>
    </div>

	<div class="clearfix"></div>
</div>


<!-- Add Free Course Modal -->
<div id="addFreeCourseModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ ucwords(trans('site.add-free-course')) }}</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('admin.free-course.store') }}" enctype="multipart/form-data"
            onsubmit="disableSubmit(this)">
          {{csrf_field()}}
          <div class="form-group">
          	<label>{{ trans('site.title') }}</label>
          	<input type="text" name="title" class="form-control" required>
          </div>
          <div class="form-group">
          	<label>{{ trans('site.description') }}</label>
          	<textarea class="form-control" name="description" required rows="6"></textarea>
          </div>
          <div class="form-group">
          	<label>{{ strtoupper(trans('site.url')) }}</label>
          	<input type="text" name="url" class="form-control" required>
          </div>

          <div class="form-group">
            <label id="course-image">{{ trans('site.image') }}</label>
            <div class="course-form-image image-file margin-bottom">
              <div class="image-preview" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
              <input type="file" accept="image/*" name="course_image" accept="image/jpg, image/jpeg, image/png">
            </div>
          </div>
          <div class="text-right">
          	<button type="submit" class="btn btn-primary">{{ trans('site.add-free-course') }}</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>



<!-- Edit Free Course Modal -->
<div id="editFreeCourseModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('site.edit-free-course') }}</h4>
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
          	<label>{{ strtoupper(trans('site.url')) }}</label>
          	<input type="text" name="url" class="form-control" required>
          </div>
          
          <div class="form-group">
            <label id="course-image">{{ trans('site.image') }}</label>
            <div class="course-form-image image-file margin-bottom">
              <div class="image-preview" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
              <input type="file" accept="image/*" name="course_image" accept="image/jpg, image/jpeg, image/png">
            </div>
          </div>
          <div class="text-right">
          	<button type="submit" class="btn btn-primary">{{ trans('site.save') }}</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>


<!-- Delete Free Course Modal -->
<div id="deleteFreeCourseModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('site.delete-course') }}</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
          {{ csrf_field() }}
          {{ method_field('DELETE') }}
          <p>{{ trans('site.delete-free-course-question') }} <strong></strong>?</p>
          <div class="text-right">
          	<button type="submit" class="btn btn-danger">{{ trans('site.delete') }}</button>
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
                <form method="POST" action="{{ route('admin.free-webinar.store') }}" enctype="multipart/form-data"
                    onsubmit="disableSubmit(this)">
                    {{ csrf_field() }}
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
                        <label>GoToWebinar ID</label>
                        <input type="text" name="gtwebinar_id" class="form-control webinar-id" required>
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
                        <label>{{ trans('site.start-date') }}</label>
                        <input type="datetime-local" name="start_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>GoToWebinar ID</label>
                        <input type="text" name="gtwebinar_id" class="form-control webinar-id" required>
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
                        <button type="submit" class="btn btn-primary">{{ trans('site.add-presenter') }}</button>
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
                        <button type="submit" class="btn btn-primary">{{ trans('site.edit-presenter') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/cropper/4.0.0/cropper.js"></script>
    <script src="{{ asset('js/toastr/toastr.min.js') }}"></script>
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
            crop: function(event) {
                var modal = $("#editWebinarModal");
                modal.find('input[name=x]').val(event.detail.x);
                modal.find('input[name=y]').val(event.detail.y);
                modal.find('input[name=w]').val(event.detail.width);
                modal.find('input[name=h]').val(event.detail.height);
            }
        });
    }

	$('.editFreeCourseBtn').click(function(){
		var form = $('#editFreeCourseModal form');
		var title = $(this).data('title');
		var description = $(this).data('description');
		var url = $(this).data('url');
		var image = $(this).data('image');
		var action = $(this).data('action');

		form.attr('action', action);
		form.find('input[name=title]').val(title);
		form.find('textarea[name=description]').val(description);
		form.find('input[name=url]').val(url);
		form.find('.image-preview').css('background-image', 'url('+image+')');
	});

	$('.deleteFreeCourseBtn').click(function(){
		var form = $('#deleteFreeCourseModal form');
		var action = $(this).data('action');
		var title = $(this).data('title');

		form.attr('action', action);
		form.find('strong').text(title);
	});

    $('.editWebinarBtn').click(function(){
        var form = $('#editWebinarModal').find('form');
        var action = $(this).data('action');
        var title = $(this).data('title');
        var description = $(this).data('description');
        var start_date = $(this).data('start_date');
        var image = $(this).data('image');
        var gtwebinar_id = $(this).data('gtwebinar_id');

        $('#editWebinarModal').find('em').text(title);
        form.attr('action', action);
        form.find('input[name=title]').val(title);
        form.find('textarea[name=description]').val(description);
        form.find('input[name=start_date]').val(start_date);
        form.find('input[name=gtwebinar_id]').val(gtwebinar_id);
        form.find('.image-preview').css('background-image', 'url('+image+')');
    });

    $('.deleteWebinarBtn').click(function(){
        var form = $('#deleteWebinarModal form');
        var action = $(this).data('action');
        var title = $(this).data('title');

        $('#deleteWebinarModal em').text(title);
        form.attr('action', action);
    });

    $('.addPresenterBtn').click(function(){
        var modal = $('#addPresenterModal');
        var title = $(this).data('title');
        var action = $(this).data('action');
        modal.find('em').text(title);
        modal.find('form').attr('action', action);
    });

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

    $(".webinar-id").keypress(function (e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which !== 8 && e.which !== 0 && (e.which < 48 || e.which > 57)) {
            //display error message
            return false;
        }
    });

    $(".copyToClipboard").click(function(){
        let copyText = $(this).closest('.button-container').find('[type=text]');
        /* Select the text field */
        copyText.select();
        /* Copy the text inside the text field */
        document.execCommand("copy");

        toastr.success('Copied to clipboard.', "Success");
        if (window.getSelection) {
            if (window.getSelection().empty) {  // Chrome
                window.getSelection().empty();
            } else if (window.getSelection().removeAllRanges) {  // Firefox
                window.getSelection().removeAllRanges();
            }
        } else if (document.selection) {  // IE?
            document.selection.empty();
        }
    });
</script>
@stop