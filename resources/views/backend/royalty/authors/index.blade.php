@extends('backend.layout')

@section('title')
<title>Author Royalty Summary &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-money"></i> Author Royalties</h3>
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

            <div class="form-group" style="margin-left: 10px;">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="">All</option>
                    <option value="payable" @if ($status === 'payable') selected @endif>Payable</option>
                    <option value="paid" @if ($status === 'paid') selected @endif>Paid</option>
                    <option value="negative" @if ($status === 'negative') selected @endif>Negative</option>
                    <option value="no-sales" @if ($status === 'no-sales') selected @endif>No Sales</option>
                </select>
            </div>

            <div class="form-group" style="margin-left: 10px;">
                <label for="search">Search</label>
                <input type="text" name="search" id="search" class="form-control" value="{{ $search }}" placeholder="Author or project">
            </div>

            <button type="submit" class="btn btn-default" style="margin-left: 10px;">Filter</button>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-border">
                <thead>
                    <tr>
                        <th>Author</th>
                        <th>Total Sales</th>
                        <th>Total Costs</th>
                        <th>Net Payout</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($authors as $author)
                        <tr>
                            <td>
                                <strong>{{ $author['name'] }}</strong><br>
                                <small>{{ $author['email'] }}</small>
                            </td>
                            <td>{{ FrontendHelpers::currencyFormat($author['total_sales']) }}</td>
                            <td>{{ FrontendHelpers::currencyFormat($author['total_costs']) }}</td>
                            <td>{{ FrontendHelpers::currencyFormat($author['net_payout']) }}</td>
                            <td>
                                @php
                                    $statusClasses = [
                                        'payable' => 'label label-warning',
                                        'paid' => 'label label-success',
                                        'negative' => 'label label-danger',
                                        'no-sales' => 'label label-default',
                                    ];
                                @endphp
                                <span class="{{ $statusClasses[$author['status']] ?? 'label label-default' }}">
                                    {{ ucwords(str_replace('-', ' ', $author['status'])) }}
                                </span>
                            </td>
                            <td class="text-right">
                                <a class="btn btn-xs btn-primary" href="{{ route('admin.royalty.authors.show', $author['user_id']) }}?year={{ $year }}@if ($quarter)&quarter={{ $quarter }}@endif">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No authors found for the selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pull-right">
            {{ $authors->appends(request()->query())->render() }}
        </div>
        <div class="clearfix"></div>
    </div>
@endsection
