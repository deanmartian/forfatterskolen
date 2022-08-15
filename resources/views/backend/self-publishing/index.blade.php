@extends('backend.layout')

@section('title')
    <title>Publishing &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file"></i> Self Publishing</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12 margin-top">

        <button type="button" class="btn btn-success addSelfPublishingBtn" data-toggle="modal"
                data-target="#selfPublishingModal" data-action="{{ route('admin.self-publishing.store') }}">
            Add Self Publishing
        </button>

        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ trans('site.title') }}</th>
                    <th>{{ trans('site.description') }}</th>
                    <th>File</th>
                    <th>Editor</th>
                    <th>Price</th>
                    <th>Editor Share</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($publishingList as $publishing)
                    <tr>
                        <td>
                            {{ $publishing->title }}
                        </td>
                        <td>
                            {{ $publishing->description }}
                        </td>
                        <td>
                            {{ $publishing->editor ? $publishing->editor->full_name : '' }}
                        </td>
                        <td>
                            {!! $publishing->file_link !!}
                        </td>
                        <td>
                            {{ $publishing->price ? \App\Http\FrontendHelpers::currencyFormat($publishing->price) : '' }}
                        </td>
                        <td>
                            {{ $publishing->editor_share ? \App\Http\FrontendHelpers::currencyFormat($publishing->editor_share) : '' }}
                        </td>
                        <td>
                            <a href="{{ route('admin.self-publishing.learners', $publishing->id) }}" class="btn btn-success btn-xs">
                                <i class="fa fa-user"></i>
                            </a>
                            <button class="btn btn-primary btn-xs editSelfPublishingBtn" data-toggle="modal"
                                    data-target="#selfPublishingModal" data-fields="{{ json_encode($publishing) }}"
                                    data-action="{{ route('admin.self-publishing.update', $publishing->id) }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteSelfPublishingBtn" data-toggle="modal"
                                    data-target="#deleteSelfPublishingModal"
                                    data-action="{{ route('admin.self-publishing.destroy', $publishing->id) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="selfPublishingModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description"cols="30" rows="10" class="form-control"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Manuscript</label>
                            <input type="file" name="manuscript" class="form-control" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf,
					    application/vnd.oasis.opendocument.text">
                        </div>

                        <div class="form-group">
                            <label>Editor</label>
                            <select name="editor_id" class="form-control select2 template">
                                <option value="" selected="" disabled>- Select Editor -</option>
                                @foreach($editors as $editor)
                                    <option value="{{ $editor->id }}">
                                        {{$editor->full_name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" id="learner-list">
                            <label>
                                Learners
                            </label>
                            <select name="learners[]" class="form-control select2 template" multiple="multiple">
                                @foreach($learners as $learner)
                                    <option value="{{$learner->id}}">
                                        {{$learner->full_name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Price</label>
                            <input type="number" name="price" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Editor Share</label>
                            <input type="number" name="editor_share" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary pull-right">{{ trans('site.save') }}</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div id="deleteSelfPublishingModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.delete') }} <em></em></h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        Are you sure you want to delete this record?
                        <div class="text-right margin-top">
                            <button class="btn btn-danger" type="submit">{{ trans('site.delete') }}</button>
                        </div>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        let modal = $("#selfPublishingModal");
        $(".addSelfPublishingBtn").click(function() {
            let form = modal.find('form');

            modal.find('.modal-title').text('Add Self Publishing');
            form.find('[name=_method]').remove();
            $("#learner-list").show();

            var action = $(this).data('action');
            form.attr('action', action);
            form.find('input[name=title]').val('');
            form.find('textarea[name=description]').val('');
            form.find('input[name=price]').val('');
            form.find('input[name=editor_share]').val('');
            form.find('select[name=editor_id]').val('').trigger('change');
        });

        $(".editSelfPublishingBtn").click(function() {
            let form = modal.find('form');
            var fields = $(this).data('fields');

            modal.find('.modal-title').text('Edit Self Publishing');
            form.find('[name=_method]').remove();
            form.prepend("<input type='hidden' name='_method' value='PUT'>");
            $("#learner-list").hide();

            var action = $(this).data('action');
            form.attr('action', action);
            form.find('input[name=title]').val(fields.title);
            form.find('textarea[name=description]').val(fields.description);
            form.find('select[name=editor_id]').val(fields.editor_id).trigger('change');
            form.find('input[name=price]').val(fields.price);
            form.find('input[name=editor_share]').val(fields.editor_share);
        });

        $(".deleteSelfPublishingBtn").click(function() {
            var action = $(this).data('action');
            let modal = $("#deleteSelfPublishingModal");

            let form = modal.find('form');
            form.attr('action', action);
        })
    </script>
@stop