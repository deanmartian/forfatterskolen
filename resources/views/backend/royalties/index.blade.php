@extends('backend.layout')

@section('title')
    <title>Royalty Overview &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <a href="{{ route('admin.project.index') }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Back
        </a>
        <h3><i class="fa fa-line-chart"></i> Royalty Overview</h3>
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
                <em><b>Authors</b></em>
            </div>
            <div class="panel-body table-users">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Forfatter</th>
                            <th>Antall bøker</th>
                            <th>Omsetning</th>
                            <th>Kostnader</th>
                            <th>Payout</th>
                            <th>Status</th>
                            <th>Handlinger</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($overview as $row)
                            @php
                                $sales = $row['sales'];
                                $costs = $row['costs'];
                                $payout = $row['payout'];
                                $isPaid = $row['is_paid'];

                                if ($sales == 0 && $costs == 0) {
                                    $statusLabel = '⚪ Ingen salg';
                                    $statusClass = 'label-default';
                                } elseif ($isPaid) {
                                    $statusLabel = '🔵 Utbetalt';
                                    $statusClass = 'label-info';
                                } elseif ($payout > 0) {
                                    $statusLabel = '🟢 Klar';
                                    $statusClass = 'label-success';
                                } else {
                                    $statusLabel = '🟠 Ubetalt';
                                    $statusClass = 'label-warning';
                                }
                            @endphp
                            <tr>
                                <td>{{ $row['author']->full_name }}</td>
                                <td>{{ $row['book_count'] }}</td>
                                <td>{{ FrontendHelpers::currencyFormat($sales) }}</td>
                                <td>{{ FrontendHelpers::currencyFormat($costs) }}</td>
                                <td>
                                    @if($payout !== 0)
                                        <strong>{{ FrontendHelpers::currencyFormat($payout) }}</strong>
                                    @else
                                        –
                                    @endif
                                </td>
                                <td><span class="label {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                <td>
                                    <a href="{{ route('admin.royalties.show', [$row['author']->id, 'year' => $year, 'quarter' => $quarter]) }}" class="btn btn-primary btn-xs">
                                        Se detaljer
                                    </a>
                                    @if($payout > 0 && ! $isPaid)
                                        <form method="POST" action="{{ route('admin.royalties.payout', $row['author']->id) }}" style="display:inline-block">
                                            @csrf
                                            <input type="hidden" name="year" value="{{ $year }}">
                                            <input type="hidden" name="quarter" value="{{ $quarter }}">
                                            <button type="submit" class="btn btn-success btn-xs">Utbetal</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">No authors found for this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
