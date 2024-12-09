<div class="panel">
    <div class="panel-body">
        <div class="col-md-12">
            <table class="table margin-top">
                <thead>
                    <tr>
                        <th>Nr</th>
                        <th>{{ trans('site.author-portal.service-text') }}</th>
                        <th>{{ trans('site.author-portal.number-text') }}</th>
                        <th>{{ trans('site.amount') }}</th>
                        <th>{{ trans('site.date') }}</th>
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