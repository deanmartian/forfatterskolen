@extends('backend.layout')

@section('title')
    <title>Email History &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> Checkout Logs</h3>
    </div>

    <div class="col-md-12">
        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Item</th>
                        <th>Is Ordered</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($logs as $log)
                    <tr>
                        <td>{{ $log->id }}</td>
                        <td>
                            <a href="{{ route('admin.learner.show', $log->user_id) }}">
                                {{ $log->user->fullname }}
                            </a>
                        </td>
                        <td>
                            {!! $log->item_link !!}
                        </td>
                        <td>
                            {{ $log->is_ordered_text }}
                        </td>
                        <td>
                            {{ $log->order_date }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="pull-right">
            {{ $logs->render() }}
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