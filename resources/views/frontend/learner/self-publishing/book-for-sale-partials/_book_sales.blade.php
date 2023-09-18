<div class="panel">
    <div class="panel-body">
        <div class="col-md-6">
            <div class="table-responsive" style="padding: 10px">
                <table class="table dt-table">
                    <thead>
                    <tr>
                        <th>Book</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Amount</th>
                        <th>Date</th>
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
                            </tr>
                        @endforeach
                        @if($book->sales->count())
                            <tr>
                                <td colspan="3">
                                    <b>Total</b>
                                </td>
                                <td colspan="2">
                                    {{ FrontendHelpers::currencyFormat($totalBookSale) }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>