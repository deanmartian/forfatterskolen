<div class="panel">
    <div class="panel-body">
        <table class="table">
            <thead>
                <tr>
                    <td>Year</td>
                    <th>Q1 Cost ($)</th>
                    <th>Q2 Cost ($)</th>
                    <th>Q3 Cost ($)</th>
                    <th>Q4 Cost ($)</th>
                    <td>Sales</td>
                    <td>Total Storage Cost</td>
                    <td>Payout</td>
                    <td></td>
                </tr>
            </thead>
            <tbody>
                @foreach ($storageCosts as $storageCost)
                    @php
                        $year = $storageCost['year'];
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
                            @php
                                $payoutLogs = AdminHelpers::storagePayoutLogs($registration_id, $year);
                                print_r($payoutLogs->count());
                            @endphp

                            <label for="">Is Payout paid?</label>
                            @if ($payoutLogs->count())
                                <a href="#" data-toggle="modal" data-target="#payoutHistoryModal"
                                data-record="{{ json_encode($payoutLogs) }}"
                                onclick="payoutHistoryView(this)">View History</a>
                            @endif
                            <br>
                            @foreach([1, 2, 3, 4] as $q)
                                @php
                                    $payoutEntry = isset($payouts[$year][$q]) ? $payouts[$year][$q]->first() : null;
                                    $paid = $payoutEntry ? $payoutEntry->is_paid : false;
                                    $payoutId = $payoutEntry ? $payoutEntry->id : null;
                                @endphp

                                <input type="hidden" name="quarter_{{ $q }}" value="{{ $paid }}" class="hidden-quarter">

                                <form method="POST" action="{{ route('admin.quarterly-payouts.store') }}" style="display:inline-block;">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $payoutId }}">
                                    <input type="hidden" name="project_registration_id" value="{{ $registration_id }}">
                                    <input type="hidden" name="year" value="{{ $year }}">
                                    <input type="hidden" name="quarter" value="{{ $q }}">
                                    <label>Q{{ $q }}:
                                        <input type="checkbox" name="is_paid" onchange="this.form.submit()" {{ $paid ? 'checked' : '' }}>
                                    </label><br>
                                </form>
                            @endforeach
                            <br>
                            {{-- <input type="checkbox" data-toggle="toggle" data-on="Yes"
                                data-off="No" data-type="copy-editing" data-size="mini" data-value="{{ $storageCost['year'] }}"
                                data-id="{{ $registration_id }}"
                                onchange="payoutToggle(this)" 
                                @if (in_array($storageCost['year'], $paidDistributionYears))
                                    {{ 'checked' }}
                                @endif> <br> --}}
                            <a href="{{ route('admin.project.storage-cost.export', 
                                [$project->id, $registration_id, $storageCost['year']]) }}" 
                                class="btn btn-primary btn-xs">
                                Download
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
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>