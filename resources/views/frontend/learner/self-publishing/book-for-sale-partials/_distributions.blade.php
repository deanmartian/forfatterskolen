<div class="panel">
    <div class="panel-body">
        <div class="col-md-6">
            <table class="table margin-top">
                <thead>
                    <tr>
                        <th>Nr</th>
                        <th>Service</th>
                        <th>Number</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($book->distributionCosts as $distributionCost)
                        <tr>
                            <td>
                                {{ $distributionCost->nr }}
                            </td>
                            <td>
                                {{ $distributionCost->service }}
                            </td>
                            <td>
                                {{ $distributionCost->number }}
                            </td>
                            <td>
                                {{ $distributionCost->amount }}
                            </td>
                        </tr>
                    @endforeach

                    @if ($book->distributionCosts()->count())
                        <tr>
                            <td colspan="3" style="font-weight: bold">
                                Total
                            </td>
                            <td colspan="2">
                                {{ FrontendHelpers::currencyFormat($book->totalDistributionCost()) }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>