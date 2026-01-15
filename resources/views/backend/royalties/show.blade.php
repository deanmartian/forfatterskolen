@extends('backend.layout')

@section('title')
    <title>Royalty Details &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <a href="{{ route('admin.royalties.index', ['year' => $year, 'quarter' => $quarter]) }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Back
        </a>
        <h3><i class="fa fa-line-chart"></i> Royalty Details: {{ $author->full_name }}</h3>
    </div>

    <div class="col-sm-12 margin-top">
        <div class="panel">
            <div class="panel-header" style="padding: 10px">
                <em><b>Period</b></em>
            </div>
            <div class="panel-body">
                <form method="GET" class="form-inline">
                    <div class="form-group">
                        <label for="year">Year</label>
                        <input type="number" class="form-control" id="year" name="year" value="{{ $year }}" min="2000">
                    </div>
                    <div class="form-group" style="margin-left: 10px;">
                        <label for="quarter">Quarter</label>
                        <select name="quarter" id="quarter" class="form-control">
                            @foreach([1,2,3,4] as $q)
                                <option value="{{ $q }}" @if($q == $quarter) selected @endif>Q{{ $q }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="margin-left: 10px;">Apply</button>
                </form>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header" style="padding: 10px">
                <em><b>Author Summary</b></em>
            </div>
            <div class="panel-body table-users">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Omsetning</th>
                            <th>Kostnader</th>
                            <th>Payout</th>
                            <th>Status</th>
                            <th>Handling</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $summaryPaid = $payoutEntry && $payoutEntry->is_paid;
                        @endphp
                        <tr>
                            <td>{{ FrontendHelpers::currencyFormat($totalSales) }}</td>
                            <td>{{ FrontendHelpers::currencyFormat($totalCosts) }}</td>
                            <td><strong>{{ FrontendHelpers::currencyFormat($totalPayout) }}</strong></td>
                            <td>
                                @if ($totalSales == 0 && $totalCosts == 0)
                                    <span class="label label-default">⚪ Ingen salg</span>
                                @elseif ($summaryPaid)
                                    <span class="label label-info">🔵 Utbetalt</span>
                                @elseif ($totalPayout > 0)
                                    <span class="label label-success">🟢 Klar</span>
                                @else
                                    <span class="label label-warning">🟠 Ubetalt</span>
                                @endif
                            </td>
                            <td>
                                @if($totalPayout > 0 && ! $summaryPaid)
                                    <form method="POST" action="{{ route('admin.royalties.payout', $author->id) }}" style="display:inline-block">
                                        @csrf
                                        <input type="hidden" name="year" value="{{ $year }}">
                                        <input type="hidden" name="quarter" value="{{ $quarter }}">
                                        <button type="submit" class="btn btn-success btn-xs">Utbetal</button>
                                    </form>
                                @else
                                    –
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header" style="padding: 10px">
                <em><b>Forfatter / Bok / Format</b></em>
            </div>
            <div class="panel-body">
                <ul>
                    <li>
                        <strong>{{ $author->full_name }}</strong>
                        <ul>
                            @foreach($books as $book)
                                <li>
                                    {{ $book['title'] }}
                                    @if($book['formats']->count())
                                        <ul>
                                            @foreach($book['formats'] as $format)
                                                <li>{{ $format }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </li>
                </ul>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header" style="padding: 10px">
                <em><b>Book Breakdown (Q{{ $quarter }} {{ $year }})</b></em>
            </div>
            <div class="panel-body table-users">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Bok</th>
                            <th>Omsetning</th>
                            <th>Kostnader</th>
                            <th>Payout</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($books as $book)
                            <tr>
                                <td>{{ $book['title'] }}</td>
                                <td>{{ FrontendHelpers::currencyFormat($book['sales']) }}</td>
                                <td>{{ FrontendHelpers::currencyFormat($book['costs']) }}</td>
                                <td>{{ FrontendHelpers::currencyFormat($book['payout']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
