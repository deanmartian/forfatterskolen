<div class="panel">
    <div class="panel-body">
        <div class="col-md-12">
            <h4>
                {{ trans('site.author-portal.book-sales') }}
            </h4>

            <div class="clearfix"></div>

            <div class="table-responsive" style="padding: 10px">
                <table class="table dt-table">
                    <thead>
                        <tr>
                            <th>{{ trans('site.date') }}</th>
                            <th>{{ trans('site.author-portal.customer-name') }}</th>
                            <th>{{ trans('site.order-history.quantity') }}</th>
                            <th>{{ trans('site.price') }}</th>
                            <th>{{ trans('site.front.discount') }}</th>
                            <th>{{ trans('site.amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projectBook->sales as $bookSale)
                            <tr>
                                <td>
                                    {{ $bookSale->date }}
                                </td>
                                <td>
                                    {{ $bookSale->customer_name }}
                                </td>
                                <td>
                                    {{ $bookSale->quantity }}
                                </td>
                                <td>
                                    {{ $bookSale->price_formatted }}
                                </td>
                                <td>
                                    {{ $bookSale->discount_formatted }}
                                </td>
                                <td>
                                    {{ $bookSale->total_amount_formatted }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>