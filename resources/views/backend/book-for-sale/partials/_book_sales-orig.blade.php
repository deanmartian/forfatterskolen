<div class="panel">
    <div class="panel-body">
        <div class="col-md-6">
            <button class="btn btn-primary pull-right btn-xs bookSalesBtn" data-toggle="modal"
                data-target="#bookSalesModal">
                + Book Sales
            </button>
            <h4>
                Books sales
            </h4>

            <div class="clearfix"></div>

            <div class="table-responsive" style="padding: 10px">
                <table class="table dt-table">
                    <thead>
                    <tr>
                        <th>Book</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($book->sales as $bookSale)
                            <tr>
                                <td>
                                    {{ $bookSale->book->title }}
                                </td>
                                <td>
                                    {{ $bookSale->sale_type_text }}
                                </td>
                                <td>
                                    {{ $bookSale->quantity }}
                                </td>
                                <td>
                                    {{ $bookSale->total_amount_formatted }}
                                </td>
                                <td>
                                    {{ $bookSale->date }}
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-xs bookSalesBtn" data-toggle="modal"
                                            data-record="{{ json_encode($bookSale) }}"
                                            data-target="#bookSalesModal">
                                        <i class="fa fa-edit"></i>
                                    </button>

                                    <button class="btn btn-danger btn-xs deleteRecordBtn" data-toggle="modal"
                                            data-target="#deleteModal"
                                            data-title="Delete Book Sale"
                                            data-action="{{ route('admin.learner.delete-book-sales',
                                            [$bookSale->user_id, $bookSale->id]) }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>