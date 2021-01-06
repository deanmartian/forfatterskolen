@extends('backend.layout')

@section('title')
    <title>SOS Children &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file"></i> {{ trans('site.sos-children-page') }}</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <a href="{{ route('admin.sos-children.create') }}" class="btn btn-success margin-top">{{ trans('site.add-document') }}</a>
        <a href="{{ route('admin.sos-children.get-main-description') }}" class="btn btn-primary margin-top">{{ trans('site.edit-descriptions') }}</a>
        {{--<button class="btn btn-primary margin-top" data-toggle="modal"
        data-target="#editMainDescriptionModal"
        data-fields="{{ json_encode($hasMainDescription) }}"
        data-is-main-description="{{ count($hasMainDescription) }}"
        id="editMainDescriptionBtn">Edit Main Description</button>--}}

        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ trans('site.title') }}</th>
                        <th width="600">{{ trans('site.description') }}</th>
                        <th>{{ trans('site.video-url') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documents as $document)
                        <tr>
                            <td>{{ $document->title }}</td>
                            <td>{!! $document->description !!}</td>
                            <td>
                                <a href="{{ $document->video_url }}" target="_blank">
                                    {{ $document->video_url }}
                                </a>
                            </td>
                            <td>
                                {{--<button class="btn btn-info btn-xs editDocumentBtn"
                                data-toggle="modal" data-target="#editDocumentModal"
                                data-action="{{ route('admin.sos-children.update', $document->id) }}"
                                data-fields="{{ json_encode($document) }}"
                                data-has-primary-video="{{ count($primaryVideo) }}"><i class="fa fa-pencil"></i></button>--}}
                                <a href="{{ route('admin.sos-children.edit', $document->id) }}" class="btn btn-info btn-xs">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <button class="btn btn-danger btn-xs deleteDocumentBtn"
                                data-toggle="modal" data-target="#deleteDocumentModal"
                                data-action="{{ route('admin.sos-children.destroy', $document->id) }}"
                                ><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="editMainDescriptionModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Main Description</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.sos-children.post-main-description') }}">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" cols="30" rows="10" class="form-control"
                            required></textarea>
                        </div>
                        <input type="hidden" name="id" value="">
                        <button type="submit" class="btn btn-primary pull-right margin-top">Save</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="addDocumentModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Document</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.sos-children.store') }}">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" cols="30" rows="10" class="form-control"
                                      required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Video URL</label>
                            <input type="url" class="form-control" name="video_url" required>
                        </div>
                        {{--@if(!count($primaryVideo))--}}
                            <div class="form-group">
                                <label>Is Primary Video?</label> <br>
                                <input type="checkbox" data-toggle="toggle" data-on="Yes"
                                       class="for-sale-toggle" data-off="No"
                                      name="is_primary">
                            </div>
                        {{--@endif--}}
                        <button type="submit" class="btn btn-primary pull-right margin-top">Save</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editDocumentModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Document</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" cols="30" rows="10" class="form-control"
                                      required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Video URL</label>
                            <input type="url" class="form-control" name="video_url" required>
                        </div>
                        <div class="form-group primary-video-fg" style="display: none">
                            <label>Is Primary Video?</label> <br>
                            <input type="checkbox" data-toggle="toggle" data-on="Yes"
                                   class="for-sale-toggle" data-off="No"
                                   name="is_primary">
                        </div>
                        <button type="submit" class="btn btn-primary pull-right margin-top">Save</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('backend.sos-children.partials.delete')
@stop

@section('scripts')
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script>
        $(document).ready(function(){
           $("#editMainDescriptionBtn").click(function(){
              var  isMainDescription = $(this).data('is-main-description');
              if (isMainDescription) {
                  var modal = $("#editMainDescriptionModal");
                  var fields = $(this).data('fields');
                  modal.find('textarea[name=description]').val(fields.description);
                  modal.find('input[name=id]').val(fields.id);
              }
           });

           $(".editDocumentBtn").click(function(){
              var action        = $(this).data('action'),
                fields          = $(this).data('fields'),
                modal           = $("#editDocumentModal"),
                  form          = modal.find('form'),
              hasPrimaryVideo   = $(this).data('has-primary-video');

               form.attr('action', action);
               form.find('input[name=title]').val(fields.title);
               form.find('textarea[name=description]').val(fields.description);
               form.find('input[name=video_url]').val(fields.video_url);

               if (hasPrimaryVideo) {
                   if (fields.is_primary) {
                       form.find('input[name=is_primary]').bootstrapToggle('on');
                       $(".primary-video-fg").show();
                   } else {
                       $(".primary-video-fg").hide();
                   }
               } else {

                       $(".primary-video-fg").show();

               }
           });

           $(".deleteDocumentBtn").click(function(){
               var action        = $(this).data('action'),
                   modal           = $("#deleteDocumentModal"),
                   form          = modal.find('form');
               form.attr('action', action);
           });
        });
    </script>
@stop