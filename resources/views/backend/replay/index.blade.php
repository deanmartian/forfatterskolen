@extends('backend.layout')

@section('title')
    <title>Email History &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> Replay</h3>
        <button class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#replayModal"
                data-action="{{ route('admin.replay.store') }}"
                id="addReplayBtn">
            Add Replay
        </button>
    </div>

    <div class="col-md-12">
        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Video Link</th>
                        <th>File</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($replays as $replay)
                        <tr>
                            <td>
                                {{ $replay->title }}
                            </td>
                            <td>
                                {{ $replay->video_link }}
                            </td>
                            <td>
                                @if ($replay->file)
                                    <a href="{{ url($replay->file) }}" download>
                                        {{ basename($replay->file) }}
                                    </a>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-primary btn-xs editReplayBtn" data-toggle="modal" data-target="#replayModal"
                                data-action="{{ route('admin.replay.update', $replay->id) }}"
                                        data-fields="{{ json_encode($replay) }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-xs deleteReplayBtn" data-toggle="modal"
                                        data-target="#deleteReplayModal" data-action="{{ route('admin.replay.delete', $replay->id) }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="replayModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" onsubmit="disableSubmit(this)" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>

                        <div class="form-group">
                            <label>Video Link</label>
                            <input type="text" class="form-control" name="video_link" required>
                        </div>

                        <div class="form-group">
                            <label>File</label>
                            <input type="file" class="form-control" name="file">
                        </div>

                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-success">{{ trans('site.submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> <!-- end replayModal -->

    <div id="deleteReplayModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.delete') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{ csrf_field() }}
                        {{ method_field('delete') }}
                        <p>
                            {!! trans('site.delete-item-question') !!}
                        </p>
                        <button class="btn btn-danger pull-right">{{ trans('site.delete') }}</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>

        </div>
    </div>

@stop

@section('scripts')
    <script>
        let modal = $("#replayModal");
        $("#addReplayBtn").click(function() {
            modal.find(".modal-title").text("Add Replay");
            modal.find('[name=_method]').remove();
            let action = $(this).data('action');
            modal.find('form').attr('action', action)
        });

        $(".editReplayBtn").click(function() {
            modal.find(".modal-title").text("Edit Replay");

            modal.find('[name=_method]').remove();

            let action = $(this).data('action');
            let fields = $(this).data('fields');
            let form = modal.find('form');
            form.prepend("<input type='hidden' name='_method' value='PUT'>");
            form.attr('action', action);
            form.find('[name=title]').val(fields.title);
            form.find('[name=video_link]').val(fields.video_link);
        });

        $(".deleteReplayBtn").click(function(){
            let modal = $("#deleteReplayModal");
            let action = $(this).data('action');
            modal.find("form").attr('action', action);
        });
    </script>
@stop