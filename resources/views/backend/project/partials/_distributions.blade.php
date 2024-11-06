<div class="panel">
    <div class="panel-body">
        <div class="col-md-12">
            <button class="btn btn-primary pull-right btn-xs distributionsBtn" data-toggle="modal"
                data-target="#distributionsModal"
                data-action="{{ route($saveDistributionRoute, $projectUserBook->id) }}">
                + Add Distribution Cost
            </button>

            <div class="clearfix"></div>

            <table class="table margin-top">
                <thead>
                    <tr>
                        <th>Nr</th>
                        <th>Service</th>
                        <th>Number</th>
                        <th>Amount</th>
                        <th>Learner Price</th>
                        <th>Date</th>
                        <th></th>
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
                                {{ AdminHelpers::currencyFormat($distributionCost->amount) }}
                            </td>
                            <td>
                                {{ AdminHelpers::currencyFormat($distributionCost->learner_amount) }}
                            </td>
                            <td>
                                {{ $distributionCost->date ? FrontendHelpers::formatDate($distributionCost->date) : '' }}
                            </td>
                            <td>
                                <button class="btn btn-primary btn-xs distributionsBtn" data-toggle="modal" 
                                data-record="{{ json_encode($distributionCost) }}"
                                data-action="{{ route($saveDistributionRoute, $projectUserBook->id) }}"
                                data-target="#distributionsModal">
                                    <i class="fa fa-edit"></i>
                                </button>

                                <button class="btn btn-danger btn-xs deleteBtn" data-toggle="modal"
                                data-target="#deleteModal" 
                                data-action="{{ route($deleteDistributionRoute, $distributionCost->id) }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach

                    @if ($projectUserBook->distributionCosts()->count())
                        <tr>
                            <td colspan="3" style="font-weight: bold">
                                Total
                            </td>
                            <td colspan="2">
                                {{ FrontendHelpers::currencyFormat($projectUserBook->totalDistributionCost()) }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>