<div class="table-responsive" style="padding: 10px">
    <table class="table dt-table">
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
                    <button class="btn btn-success btn-xs resendEmailHistoryBtn loadScriptButton" data-toggle="modal"
                            data-target="#resendEmailHistoryModal" data-record="{{ json_encode($emailHistory) }}"
                            style="margin-top: 5px;">
                        Resend Email
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @if($emailHistories->isEmpty())
        <div class="text-center text-muted" style="padding: 10px;">No email history found.</div>
    @endif
</div>
