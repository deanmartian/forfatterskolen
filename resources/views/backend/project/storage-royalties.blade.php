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
            Use the payout checkboxes to mark paid quarters and save them per year.
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

            <div class="panel">
                <div class="panel-header" style="padding: 10px">
                    <em><b>Payout Overview</b></em>
                </div>
                <div class="panel-body table-users">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Year</th>
                                <th>Paid Quarters</th>
                                <th>Unpaid Quarters</th>
                                <th>Total Payout</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($storageCosts as $storageCost)
                                @php
                                    $year = $storageCost['year'];
                                    $paidQuarters = [];
                                    $unpaidQuarters = [];

                                    foreach ([1, 2, 3, 4] as $q) {
                                        $payoutEntry = isset($payouts[$year][$q]) ? $payouts[$year][$q]->first() : null;
                                        if ($payoutEntry && $payoutEntry->is_paid) {
                                            $paidQuarters[] = "Q{$q}";
                                        } else {
                                            $unpaidQuarters[] = "Q{$q}";
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $year }}</td>
                                    <td>{{ $paidQuarters ? implode(', ', $paidQuarters) : '-' }}</td>
                                    <td>{{ $unpaidQuarters ? implode(', ', $unpaidQuarters) : '-' }}</td>
                                    <td>{{ FrontendHelpers::currencyFormat($storageCost['payout']) }}</td>
                                    <td>
                                        @if (count($unpaidQuarters) === 0)
                                            <span class="label label-success">All paid</span>
                                        @else
                                            <span class="label label-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header" style="padding: 10px">
                    <em><b>Royalty Payouts</b></em>
                </div>
                <div class="panel-body table-users">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Year</th>
                                <th>Q1</th>
                                <th>Q2</th>
                                <th>Q3</th>
                                <th>Q4</th>
                                <th>Sales</th>
                                <th>Total Storage Cost</th>
                                <th>Payout</th>
                                <th>Payout Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($storageCosts as $storageCost)
                                @php
                                    $year = $storageCost['year'];
                                    $payoutLogs = AdminHelpers::storagePayoutLogs($registration_id, $year);
                                @endphp
                                <tr>
                                    <td>
                                        {{ $storageCost['year'] }}
                                    </td>
                                    <td>
                                        <b>Sales:</b> {{ FrontendHelpers::currencyFormat($storageCost['q1_sales']) }} <br>
                                        <b>Storage Cost:</b> {{ FrontendHelpers::currencyFormat($storageCost['q1_distributions']) }} <br>
                                        <b>Payout:</b> {{ FrontendHelpers::currencyFormat(
                                            ($storageCost['q1_sales'] - $storageCost['q1_distributions'])
                                            ) }}
                                    </td>
                                    <td>
                                        <b>Sales:</b> {{ FrontendHelpers::currencyFormat($storageCost['q2_sales']) }} <br>
                                        <b>Storage Cost:</b> {{ FrontendHelpers::currencyFormat($storageCost['q2_distributions']) }} <br>
                                        <b>Payout:</b> {{ FrontendHelpers::currencyFormat(
                                            ($storageCost['q2_sales'] - $storageCost['q2_distributions'])
                                            ) }}
                                    </td>
                                    <td>
                                        <b>Sales:</b> {{ FrontendHelpers::currencyFormat($storageCost['q3_sales']) }} <br>
                                        <b>Storage Cost:</b> {{ FrontendHelpers::currencyFormat($storageCost['q3_distributions']) }} <br>
                                        <b>Payout:</b> {{ FrontendHelpers::currencyFormat(
                                            ($storageCost['q3_sales'] - $storageCost['q3_distributions'])
                                            ) }}
                                    </td>
                                    <td>
                                        <b>Sales:</b> {{ FrontendHelpers::currencyFormat($storageCost['q4_sales']) }} <br>
                                        <b>Storage Cost:</b> {{ FrontendHelpers::currencyFormat($storageCost['q4_distributions']) }} <br>
                                        <b>Payout:</b> {{ FrontendHelpers::currencyFormat(
                                            ($storageCost['q4_sales'] - $storageCost['q4_distributions'])
                                            ) }}
                                    </td>
                                    <td>
                                        {{ FrontendHelpers::currencyFormat($storageCost['total_sales']) }}
                                    </td>
                                    <td>
                                        {{ FrontendHelpers::currencyFormat($storageCost['total_distributions']) }}
                                    </td>
                                    <td>
                                        {{ FrontendHelpers::currencyFormat($storageCost['payout']) }}
                                    </td>
                                    <td>
                                        <div class="payout-status" data-year="{{ $year }}">
                                            @foreach([1, 2, 3, 4] as $q)
                                                @php
                                                    $payoutEntry = isset($payouts[$year][$q]) ? $payouts[$year][$q]->first() : null;
                                                    $paid = $payoutEntry ? $payoutEntry->is_paid : false;
                                                    $payoutId = $payoutEntry ? $payoutEntry->id : null;
                                                @endphp
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox"
                                                            class="quarter-toggle"
                                                            data-quarter="{{ $q }}"
                                                            data-payout-id="{{ $payoutId }}"
                                                            {{ $paid ? 'checked' : '' }}>
                                                        Q{{ $q }}
                                                    </label>
                                                    <input type="hidden" class="hidden-quarter"
                                                        name="quarter_{{ $q }}" value="{{ $paid }}">
                                                </div>
                                            @endforeach
                                            <button type="button"
                                                class="btn btn-primary btn-xs payout-save-btn"
                                                data-year="{{ $year }}">
                                                Save payout status
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.project.storage-cost.export',
                                                [$project->id, $registration_id, $storageCost['year']]) }}"
                                                class="btn btn-primary btn-xs">
                                                Download PDF
                                            </a>
                                            <a href="{{ route('admin.project.storage-cost.export-excel',
                                                [$project->id, $registration_id, $storageCost['year']]) }}"
                                                class="btn btn-success btn-xs">
                                                Download Excel
                                            </a>
                                            <button data-action="{{ route('admin.project.storage-cost.send',
                                                [$project->id, $registration_id, $storageCost['year']]) }}"
                                                data-toggle="modal"
                                                data-target="#sendStorageCostModal"
                                                class="btn btn-info btn-xs sendStorageCostBtn">
                                                Send Email
                                            </button>
                                            @if ($payoutLogs->count())
                                                <button class="btn btn-default btn-xs"
                                                    data-toggle="modal"
                                                    data-target="#payoutHistoryModal"
                                                    data-record="{{ json_encode($payoutLogs) }}"
                                                    onclick="payoutHistoryView(this)">
                                                    View History
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
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
    const payoutStoreUrl = "{{ route('admin.quarterly-payouts.store') }}";
    const payoutCsrf = "{{ csrf_token() }}";

    $(".payout-save-btn").click(function () {
        const $container = $(this).closest(".payout-status");
        const year = $(this).data("year");

        $container.find(".quarter-toggle").each(function () {
            const $toggle = $(this);
            const quarter = $toggle.data("quarter");
            const payoutId = $toggle.data("payout-id");
            const isPaid = $toggle.is(":checked");

            if (!isPaid && !payoutId) {
                return;
            }

            const payload = {
                id: payoutId,
                project_registration_id: "{{ $registration_id }}",
                year: year,
                quarter: quarter
            };

            if (isPaid) {
                payload.is_paid = "on";
            }

            $.ajax({
                type: "POST",
                url: payoutStoreUrl,
                headers: { "X-CSRF-TOKEN": payoutCsrf },
                data: payload
            });
        });
    });

    $(document).on("change", ".quarter-toggle", function () {
        const $checkbox = $(this);
        const $wrapper = $checkbox.closest(".checkbox");
        $wrapper.find(".hidden-quarter").val($checkbox.is(":checked") ? "1" : "0");
    });

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
