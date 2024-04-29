@extends('backend.layout')

@section('title')
<title>{{ $learner->first_name }} &rsaquo; Email History &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-email"></i> Email History</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default margin-top">
            <div class="table-responsive" style="padding: 10px">
                <table class="table">
                    <thead>
                    <tr>
                        <th>{{ trans('site.subject') }}</th>
                        <th>{{ trans('site.from') }}</th>
                        <th>{{ trans('site.date-sent') }}</th>
                        <th>Date Opened</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($emailHistories as $emailHistory)
                            <tr>
                                <td>
                                    {{ $emailHistory->subject }}
                                </td>
                                <td>
                                    {{ $emailHistory->from_email }}
                                </td>
                                <td>
                                    {{ $emailHistory->created_at }}
                                </td>
                                <td>
                                    {{ $emailHistory->date_open }}
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-info btn-xs" data-toggle="modal"
                                            data-target="#showEmailModal"
                                            data-message="{{ $emailHistory->message }}" onclick="showEmailMessage(this)">
                                            Show Message
                                    </button>
                                    <button class="btn btn-success btn-xs resendEmailHistoryBtn" data-toggle="modal" 
                                        data-target="#resendEmailHistoryModal" data-record="{{ json_encode($emailHistory) }}">
                                        Resend Email
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="pull-right margin-top">
                    {{ $emailHistories->links() }}
                </div>
            </div>
        </div> <!-- end panel -->
    </div>

    <div id="showEmailModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Message Body</h4>
                </div>
                <div class="modal-body">
    
                </div>
            </div>
        </div>
    </div>

    <div id="resendEmailHistoryModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Resend Email</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.send-email-to-queue') }}" onsubmit="disableSubmit(this)">
                        {{csrf_field()}}
                        <input type="hidden" name="parent">
                        <input type="hidden" name="parent_id">
                        <input type="hidden" name="recipient" value="{{ $learner->email }}">
    
                        <div class="form-group">
                            <label>{{ trans('site.subject') }}</label>
                            <input type="text" class="form-control" name="subject" required>
                        </div>
    
                        <div class="form-group">
                            <label>{{ trans('site.message') }}</label>
                            <textarea name="message" cols="30" rows="10"
                                      class="form-control tinymce" id="sendEmailHistoryEditor"></textarea>
                        </div>
    
                        <div class="form-group">
                            <label>From</label>
                            <input type="email" class="form-control" placeholder="Email"
                                   name="from_email">
                        </div>
    
                        <div class="text-right">
                            <input type="submit" class="btn btn-primary" value="{{ trans('site.send') }}" id="send_email_btn">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
<script>
    $(".resendEmailHistoryBtn").click(function(){
		let record = $(this).data('record');
		let modal = $("#resendEmailHistoryModal");

		modal.find("[name=parent]").val(record.parent);
		modal.find("[name=parent_id]").val(record.parent_id);
		modal.find("[name=subject]").val(record.subject);
		modal.find("[name=from_email]").val(record.from_email);

		tinymce.get('sendEmailHistoryEditor').execCommand('mceRefresh');
		setTimeout(function(){
			console.log("inside set timeout");
			console.log(record.message);
            tinymce.activeEditor.setContent(record.message);
		}, 200);
	});

    function showEmailMessage(t) {
        let modal = $("#showEmailModal");
        let message = $(t).data('message');
        modal.find('.modal-body').html(message);
    }
</script>
@stop