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
                </tr>
            </thead>
            <tbody>
                @foreach ($storageCosts as $storageCost)
                    <tr>
                        <td>
                            {{ $storageCost['year'] }}
                        </td>
                        <td>
                            {{ FrontendHelpers::currencyFormat($storageCost['q1_distributions']) }}
                        </td>
                        <td>
                            {{ FrontendHelpers::currencyFormat($storageCost['q2_distributions']) }}
                        </td>
                        <td>
                            {{ FrontendHelpers::currencyFormat($storageCost['q3_distributions']) }}
                        </td>
                        <td>
                            {{ FrontendHelpers::currencyFormat($storageCost['q4_distributions']) }}
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
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>