@extends($layout)

@section('title')
    <title>Project &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <a href="{{ $backRoute }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Back
        </a>
        <h3><i class="fa fa-line-chart"></i> Book Sales per Author (Royalty Payouts)</h3>
    </div>

    <div class="col-sm-12 margin-top">
        <div class="alert alert-info">
            Review quarterly book sales, storage costs, and royalty payouts for this author in one place.
        </div>

        @if($projectUserBook)
            <div class="row">
                <div class="col-md-6">
                    <div class="panel">
                        <div class="panel-header" style="padding: 10px">
                            <em>
                                <b>
                                    Book
                                </b>
                            </em>
                        </div>
                        <div class="panel-body table-users">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ISBN</th>
                                        <th>Book name</th>
                                        <th>Author</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            {{ $projectUserBook->value }}
                                        </td>
                                        <td>
                                            {{ $projectBook->book_name ?? '' }}
                                        </td>
                                        <td>
                                            {{ $project->user->full_name ?? '' }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            @include('backend.project.partials._storage_cost')
        @else
            <div class="alert alert-warning">
                No storage book record is available for this project registration yet.
            </div>
        @endif
    </div>

    <div id="sendStorageCostModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        Send Storage Cost
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{csrf_field()}}
                        @php
                            $storageCostEmailTemplate = AdminHelpers::emailTemplate('Storage Cost Payout');
                        @endphp
                        <div class="form-group">
                            <label>Quarter</label> <br>
                            <div style="display: inline-block">
                                @foreach([1, 2, 3, 4] as $q)
                                    <label>Q{{ $q }}:
                                        <input type="checkbox" name="quarters[{{ $q }}]" class="quarter-checkbox"
                                        data-quarter="{{ $q }}" style="margin-right: 5px">
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Subject</label>
                            <input type="text" class="form-control" name="subject"
                            value="{{ $storageCostEmailTemplate->subject }}" required>
                        </div>

                        <div class="form-group">
                            <label>From Email</label>
                            <input type="text" class="form-control" name="from_email"
                            value="{{ $storageCostEmailTemplate->from_email }}" required>
                        </div>

                        <div class="form-group">
                            <label>Message</label>
                            <textarea name="message" cols="30" rows="10"
                            class="form-control tinymce" required>{!! $storageCostEmailTemplate->email_content !!}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary pull-right">{{ trans('site.submit') }}</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="payoutHistoryModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        Payout History
                    </h4>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                          <tr>
                            <th>Year</th>
                            <th>Quarter</th>
                            <th>Amount</th>
                            <th>Date</th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
<script>
    $(".sendStorageCostBtn").click(function () {
        let modal = $("#sendStorageCostModal");
        let action = $(this).data('action');

        modal.find("form").attr('action', action);

        // Scope only to the current <td>
        let td = $(this).closest('td');

        td.find('.hidden-quarter').each(function () {
            const quarter = $(this).attr('name').split('_')[1];
            const value = $(this).val();

            const checkbox = modal.find(`.quarter-checkbox[data-quarter="${quarter}"]`);
            if (checkbox.length) {
                checkbox.prop('checked', value === "1");
            }
        });
    });

    function payoutHistoryView(self) {
        let logs = $(self).data('record');
        const tbody = $("#payoutHistoryModal").find("tbody");
        tbody.empty();
        logs.forEach(log => {
            const row = `
                <tr>
                    <td>${log.year}</td>
                    <td>${log.quarter}</td>
                    <td>${log.amount}</td>
                    <td>${log.date ?? ''}</td>
                </tr>
            `;
            tbody.append(row);
        });
    }
</script>
@stop
