@extends('backend.layout')

@section('title')
<title>Author Royalty Details &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-money"></i> Royalty Details: {{ $author->full_name ?? $author->first_name.' '.$author->last_name }}</h3>
        <a class="btn btn-default pull-right" href="{{ route('admin.royalty.authors.index') }}?year={{ $year }}@if ($quarter)&quarter={{ $quarter }}@endif">Back to list</a>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12 margin-top">
        <form method="GET" class="form-inline" style="margin-bottom: 15px;">
            <div class="form-group">
                <label for="year">Year</label>
                <select name="year" id="year" class="form-control">
                    @foreach ($years as $yearOption)
                        <option value="{{ $yearOption }}" @if ($yearOption == $year) selected @endif>
                            {{ $yearOption }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-left: 10px;">
                <label for="quarter">Quarter</label>
                <select name="quarter" id="quarter" class="form-control">
                    <option value="">All</option>
                    @foreach ($quarters as $quarterOption)
                        <option value="{{ $quarterOption }}" @if ($quarterOption == $quarter) selected @endif>
                            Q{{ $quarterOption }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-default" style="margin-left: 10px;">Filter</button>
        </form>

        <div class="well">
            <strong>Total Sales:</strong> {{ FrontendHelpers::currencyFormat($totals['sales']) }}
            <span style="margin-left: 20px;"><strong>Total Costs:</strong> {{ FrontendHelpers::currencyFormat($totals['costs']) }}</span>
            <span style="margin-left: 20px;"><strong>Net Payout:</strong> {{ FrontendHelpers::currencyFormat($totals['net_payout']) }}</span>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>Payout statements</strong>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-border">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Reference</th>
                                <th>Statement</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payouts as $payout)
                                <tr>
                                    <td>{{ $payout->year }} Q{{ $payout->quarter }}</td>
                                    <td>{{ FrontendHelpers::currencyFormat($payout->amount_total) }}</td>
                                    <td>
                                        @if ($payout->paid_at)
                                            <span class="label label-success">Paid</span>
                                            <br>
                                            <small>{{ $payout->paid_at->format('Y-m-d') }}</small>
                                        @else
                                            <span class="label label-warning">Unpaid</span>
                                        @endif
                                    </td>
                                    <td>{{ $payout->note ?: '—' }}</td>
                                    <td>
                                        @if ($payout->statement_path)
                                            <a class="btn btn-xs btn-success" href="{{ route('admin.royalty.authors.statement.download', $payout->id) }}">
                                                Download
                                            </a>
                                        @endif
                                        <form method="POST" action="{{ route('admin.royalty.authors.statement.generate', $payout->id) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-info">Generate</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">No payouts found for this period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-border">
                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Registration ID</th>
                        <th>Sales</th>
                        <th>Costs</th>
                        <th>Net Payout</th>
                        <th>Paid</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($registrations as $registration)
                        <tr>
                            <td>
                                {{ $registration['book_name'] ?: $registration['project_name'] }}
                                <br>
                                <small>{{ $registration['project_name'] }}</small>
                            </td>
                            <td>{{ $registration['project_registration_id'] }}</td>
                            <td>{{ FrontendHelpers::currencyFormat($registration['sales']) }}</td>
                            <td>{{ FrontendHelpers::currencyFormat($registration['costs']) }}</td>
                            <td>{{ FrontendHelpers::currencyFormat($registration['net_payout']) }}</td>
                            <td>
                                @if ($registration['paid'])
                                    <span class="label label-success">Paid</span>
                                @else
                                    <span class="label label-warning">Unpaid</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No registrations found for this author.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
