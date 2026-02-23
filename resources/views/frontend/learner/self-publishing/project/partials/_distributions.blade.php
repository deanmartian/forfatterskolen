<div class="sp-card">
    <div class="sp-card__header">
        <h2>
            <i class="fa fa-truck" style="color:var(--brand-primary);margin-right:6px"></i>
            Distribusjonskostnader
        </h2>
    </div>
    <div class="sp-card__body">
        <div class="table-responsive">
            <table class="sp-table">
                <thead>
                    <tr>
                        <th>Nr</th>
                        <th>Tjeneste</th>
                        <th>Antall</th>
                        <th>Beløp</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($projectUserBook->distributionCosts as $distributionCost)
                        <tr>
                            <td>{{ $distributionCost->nr }}</td>
                            <td>{{ AdminHelpers::distributionServices($distributionCost->service)['value'] }}</td>
                            <td>{{ $distributionCost->number }}</td>
                            <td>{{ AdminHelpers::currencyFormat($distributionCost->learner_amount) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                @if ($projectUserBook->distributionCosts()->count())
                    <tfoot>
                        <tr>
                            <td colspan="3"><strong>Totalt</strong></td>
                            <td>
                                <strong>{{ FrontendHelpers::currencyFormat($projectUserBook->totalDistributionCost()) }}</strong>
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
