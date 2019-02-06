@extends('backend.layout')

@section('title')
    <title>Support &rsaquo; Forfatterskolen Admin</title>
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
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> {{ trans_choice('site.solutions', 2) }}</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <button class="btn btn-success margin-top" data-target="#addSolutionModal" data-toggle="modal">{{ trans('site.add-solution') }}</button>
        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ trans('site.id') }}</th>
                    <th>{{ trans_choice('site.solutions', 1) }}</th>
                    <th>{{ trans('site.description') }}</th>
                    <th>{{ trans('site.is-instruction') }}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    @foreach($solutions as $solution)
                        <tr>
                            <td>{{ $solution->id }}</td>
                            <td>{{ $solution->title }}</td>
                            <td>{{ $solution->description }}</td>
                            <td>{{ $solution->is_instruction ? 'Yes' : 'No' }}</td>
                            <td>
                                <a href="{{ route('admin.solution-article.index', $solution->id) }}" class="btn btn-xs btn-success">
                                    {{ trans_choice('site.articles', 2) }}
                                </a>
                                <button class="btn btn-xs btn-primary editSolutionBtn" data-fields="{{ json_encode($solution) }}" data-action="{{ route('admin.solution.update', $solution->id) }}"
                                        data-filename="{{ \App\Http\AdminHelpers::extractFileName($solution->image) }}" data-toggle="modal" data-target="#editSolutionModal"><i class="fa fa-pencil"></i></button>
                                <button class="btn btn-xs btn-danger deleteSolutionBtn" data-action="{{ route('admin.solution.destroy', $solution->id) }}" data-toggle="modal" data-target="#deleteSolutionModal"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pull-right">
            {{ $solutions->render() }}
        </div>

    </div>

    <div id="addSolutionModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.add-solution') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.solution.store') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>{{ trans('site.title') }}</label>
                            <input type="text" class="form-control" name="title" placeholder="{{ trans('site.title') }}" required>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('site.description') }}</label>
                            <textarea class="form-control" name="description" placeholder="{{ trans('site.description') }}" required rows="8"></textarea>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.is-instruction') }}?</label> <br>
                            <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                   name="is_instruction" data-width="84">
                        </div>

                        <div class="form-group" id="image_form_group">
                            <label for="image">{{ trans('site.image') }}</label>
                            <input type="file" accept="image/*" name="image" id="webinarImage" accept="image/jpg, image/jpeg, image/png"
                                   {{--onchange="readURL(this)"--}}>

                            <input type="hidden" name="x" />
                            <input type="hidden" name="y" />
                            <input type="hidden" name="w" />
                            <input type="hidden" name="h" />
                        </div>

                        <div class="image_container">
                            <img id="webinarImagePreview" src="#" alt="your image" />
                        </div>

                        <button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.save') }}</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editSolutionModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.edit-solution') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}
                        <div class="form-group">
                            <label>{{ trans('site.title') }}</label>
                            <input type="text" class="form-control" name="title" placeholder="{{ trans('site.title') }}" required>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('site.description') }}</label>
                            <textarea class="form-control" name="description" placeholder="{{ trans('site.description') }}" required rows="8"></textarea>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('site.is-instruction') }}?</label> <br>
                            <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                   name="is_instruction" data-width="84">
                        </div>

                        <div class="form-group" id="image_form_group_edit">
                            <label for="image">{{ trans('site.image') }}</label>
                            <input type="file" accept="image/*" name="image" id="webinarImageEdit" accept="image/jpg, image/jpeg, image/png"
                                   {{--onchange="readURLEdit(this)"--}}>
                            <span class="image-name"></span>

                            <input type="hidden" name="x" />
                            <input type="hidden" name="y" />
                            <input type="hidden" name="w" />
                            <input type="hidden" name="h" />
                        </div>

                        <div class="image_container_edit">
                            <img id="webinarImagePreviewEdit" src="#" alt="your image" />
                        </div>

                        <button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.save') }}</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteSolutionModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.delete-solution') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        {!! trans('site.delete-solution-question') !!}
                        <br />
                        <button type="submit" class="btn btn-danger pull-right margin-top">{{ trans('site.delete') }}</button>
                        <div class="clearfix"></div>
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

        var image_form_group = $("#image_form_group"),
            image_form_group_edit = $("#image_form_group_edit");
        image_form_group.hide();
        image_form_group_edit.hide();

        $('.deleteSolutionBtn').click(function(){
            var form = $('#deleteSolutionModal').find('form');
            var action = $(this).data('action');
            form.attr('action', action);
        });

        $(".editSolutionBtn").click(function(){
            var form = $('#editSolutionModal form');
            var fields = $(this).data('fields');
            var action = $(this).data('action');
            let filename = $(this).data('filename');
            form.attr('action', action);
            form.find('input[name=title]').val(fields.title);
            form.find('textarea[name=description]').val(fields.description);

            if (fields.is_instruction) {
                image_form_group_edit.show();
                $("#editSolutionModal").find('input[name=is_instruction]').bootstrapToggle('on');
            }
            $("#editSolutionModal").find('.image-name').text(filename);
        });

        $("#addSolutionModal").find("input[name=is_instruction]").change(function(){
            if ($(this).prop('checked')) {
                image_form_group.show();
            } else {
                image_form_group.hide();
            }
        });

        $("#editSolutionModal").find("input[name=is_instruction]").change(function(){
            if ($(this).prop('checked')) {
                image_form_group_edit.show();
            } else {
                image_form_group_edit.hide();
            }
        });

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
                    var modal = $("#addSolutionModal");
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
                    var modal = $("#editSolutionModal");
                    modal.find('input[name=x]').val(event.detail.x);
                    modal.find('input[name=y]').val(event.detail.y);
                    modal.find('input[name=w]').val(event.detail.width);
                    modal.find('input[name=h]').val(event.detail.height);
                }
            });
        }
    </script>
@stop