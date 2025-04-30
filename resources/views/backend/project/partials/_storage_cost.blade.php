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
                            <label for="">Is Payout paid?</label>
                            <input type="checkbox" data-toggle="toggle" data-on="Yes"
                                data-off="No" data-type="copy-editing" data-size="mini" data-value="{{ $storageCost['year'] }}"
                                data-id="{{ $registration_id }}"
                                onchange="payoutToggle(this)" 
                                @if (in_array($storageCost['year'], $paidDistributionYears))
                                    {{ 'checked' }}
                                @endif> <br>
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
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>