<div class="panel">
    <div class="panel-body">
        <div class="col-md-12">
            <table class="table margin-top">
                <thead>
                    <tr>
                        <th>Nr</th>
                        <th>Service</th>
                        <th>Number</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($projectUserBook->distributionCosts as $distributionCost)
                    <tr>
                        <td>
                            {{ $distributionCost->nr }}
                        </td>
                        <td>
                            {{ AdminHelpers::distributionServices($distributionCost->service)['value'] }}
                        </td>
                        <td>
                            {{ $distributionCost->number }}
                        </td>
                        <td>
                            {{ AdminHelpers::currencyFormat($distributionCost->learner_amount) }}
                        </td>
                        <td>
                            {{ $distributionCost->date ? FrontendHelpers::formatDate($distributionCost->date) : '' }}
                        </td>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>