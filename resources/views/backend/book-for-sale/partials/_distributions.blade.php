<div class="panel">
    <div class="panel-body">
        <div class="col-md-6">
            <button class="btn btn-primary pull-right btn-xs distributionsBtn" data-toggle="modal"
                data-target="#distributionsModal">
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
                        <th width="100"></th>
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
                            <td>
                                <button class="btn btn-primary btn-xs distributionsBtn" data-toggle="modal" 
                                data-record="{{ json_encode($distributionCost) }}"
                                data-target="#distributionsModal">
                                    <i class="fa fa-edit"></i>
                                </button>

                                <button class="btn btn-danger btn-xs deleteRecordBtn" data-toggle="modal"
                                data-target="#deleteModal" 
                                data-action="{{ route('admin.book-for-sale.delete-distribution-cost', $distributionCost->id) }}">
                                    <i class="fa fa-trash"></i>
                                </button>
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