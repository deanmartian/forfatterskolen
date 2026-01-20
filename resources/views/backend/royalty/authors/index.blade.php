@extends('backend.layout')

@section('title')
<title>Author Royalty Summary &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-money"></i> Author Royalties</h3>
    </div>

    <div class="col-md-12 margin-top">
        @if ($flash = session('message.content'))
            @php
                $alertType = session('alert_type', 'success');
            @endphp
            <div class="alert alert-{{ $alertType }}">
                <button type="button" class="close" data-dismiss="alert" aria-label="close">&times;</button>
                {{ $flash }}
            </div>
        @endif

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

        <form method="POST" action="{{ route('admin.royalty.authors.mark-paid') }}" id="mark-paid-form">
            @csrf
            <input type="hidden" name="year" value="{{ $year }}">
            <input type="hidden" name="quarter" id="mark-paid-quarter" value="{{ $quarter }}">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="hidden" name="search" value="{{ $search }}">
            <input type="hidden" name="note" id="mark-paid-note">

            <div class="form-inline" style="margin-bottom: 10px;">
                <button type="submit" class="btn btn-success" id="mark-paid-button">
                    Mark as paid
                </button>
                <span class="text-muted" style="margin-left: 10px;">
                    Select a quarter or leave “All” to pay the full year.
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-border">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="select-all-authors">
                            </th>
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
                                    <input type="checkbox" name="author_ids[]" value="{{ $author['user_id'] }}" class="author-select">
                                </td>
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
                                <td colspan="7">No authors found for the selected period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        <div class="pull-right">
            {{ $authors->appends(request()->query())->render() }}
        </div>
        <div class="clearfix"></div>
    </div>

    <script>
        (function () {
            var selectAll = document.getElementById('select-all-authors');
            var form = document.getElementById('mark-paid-form');
            var button = document.getElementById('mark-paid-button');
            var quarterInput = document.getElementById('mark-paid-quarter');
            var noteInput = document.getElementById('mark-paid-note');

            function selectedCount() {
                return document.querySelectorAll('.author-select:checked').length;
            }

            function toggleButton() {
                if (!button) {
                    return;
                }
                button.disabled = selectedCount() === 0;
            }

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    document.querySelectorAll('.author-select').forEach(function (checkbox) {
                        checkbox.checked = selectAll.checked;
                    });
                    toggleButton();
                });
            }

            document.querySelectorAll('.author-select').forEach(function (checkbox) {
                checkbox.addEventListener('change', toggleButton);
            });

            if (form) {
                form.addEventListener('submit', function (event) {
                    if (selectedCount() === 0) {
                        event.preventDefault();
                        alert('Select at least one author to mark as paid.');
                        return;
                    }

                    var notePrompt = quarterInput.value
                        ? 'Optional note/reference for this payout (Q' + quarterInput.value + '):'
                        : 'Optional note/reference for this payout (full year):';
                    var note = prompt(notePrompt, '');
                    if (note === null) {
                        event.preventDefault();
                        return;
                    }
                    noteInput.value = note;
                });
            }

            toggleButton();
        })();
    </script>
@endsection
