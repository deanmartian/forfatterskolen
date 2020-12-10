@extends('backend.layout')

@section('title')
    <title>Email History &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> Email History</h3>
    </div>

    <div class="col-md-12">
        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Recipient</th>
                        <th>Subject</th>
                        <th>From</th>
                        <th>Date Sent</th>
                        <th>Date Opened</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($histories as $history)
                        <tr>
                            <td>
                                @if($history->recipient_id)
                                    <a href="{{ route('admin.learner.show', $history->recipient_id) }}">
                                        {{ $history->recipient }}
                                    </a>
                                @else
                                    {{ $history->recipient }}
                                @endif
                            </td>
                            <td>
                                {{ $history->subject }}
                            </td>
                            <td>
                                {{ $history->from_email }}
                            </td>
                            <td>
                                {{ $history->created_at }}
                            </td>
                            <td>
                                {{ $history->date_open }}
                            </td>
                            <td>
                                <button class="btn btn-primary btn-xs viewEmailBtn"
                                        data-toggle="modal"
                                        data-target="#viewEmailModal"
                                        data-record="{{ json_encode($history) }}">
                                    View Email
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pull-right">{{ $histories->render() }}</div>
    </div>

    <div id="viewEmailModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">View Email</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ trans('site.subject') }}</label> <br>
                        <span class="subject-container"></span>
                    </div>

                    <div class="form-group">
                        <label>{{ trans('site.from') }}</label> <br>
                        <span class="from-container"></span>
                    </div>

                    <div class="form-group">
                        <label>{{ trans('site.message') }}</label> <br>
                        <span class="message-container"></span>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end viewEmailModal -->
@stop

@section('scripts')
    <script>
        $(".viewEmailBtn").click(function(){
            let record = $(this).data('record');
            let modal = $('#viewEmailModal');

            modal.find('.subject-container').empty().append(record.subject);
            modal.find('.from-container').empty().append(record.from_email);
            modal.find('.message-container').empty().append(record.message);
        });
    </script>
@stop