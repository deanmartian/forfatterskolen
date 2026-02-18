<div class="panel">
    <div class="panel-body">
        <div class="table-responsive" style="padding: 10px">
            <table class="table dt-table">
                <thead>
                    <tr>
                        <th>{{ trans('site.type') }}</th>
                        <th>{{ trans('site.order-history.quantity') }}</th>
                        <th>{{ trans('site.date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inventorySales as $sale)
                        <tr>
                            <td>
                                {{ $sale->inventory_type }}
                            </td>
                            <td>
                                {{ $sale->quantity }}
                            </td>
                            <td>
                                {{ $sale->date }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>