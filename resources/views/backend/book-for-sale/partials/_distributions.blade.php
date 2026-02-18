<div class="sp-card">
    <div class="sp-card__header">
        <h2>
            <i class="fa fa-truck" style="color:var(--brand-primary);margin-right:6px"></i>
            {{ trans('site.author-portal.distribution-cost') }}
        </h2>
    </div>
    <div class="sp-card__body">
        <div class="table-responsive">
            <table class="sp-table">
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
                            <td>{{ $distributionCost->nr }}</td>
                            <td>{{ AdminHelpers::distributionServices($distributionCost->service)['value'] }}</td>
                            <td>{{ $distributionCost->number }}</td>
                            <td>{{ AdminHelpers::currencyFormat($distributionCost->learner_amount) }}</td>
                            <td>{{ $distributionCost->date ? FrontendHelpers::formatDate($distributionCost->date) : '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
