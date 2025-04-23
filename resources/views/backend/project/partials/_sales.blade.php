<div class="panel">
    <div class="panel-body">
        <button class="btn btn-primary pull-right btn-sm inventorySalesBtn" data-toggle="modal"
                data-target="#inventorySalesModal">
                + Sales
        </button>
            <h4>
                Sales
            </h4>

        <div class="clearfix"></div>

        <div class="table-responsive" style="padding: 10px">
            <table class="table dt-table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Date</th>
                        <th></th>
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
                            <td>
                                <button class="btn btn-primary btn-xs inventorySalesBtn" data-toggle="modal"
                                        data-record="{{ json_encode($sale) }}"
                                        data-target="#inventorySalesModal">
                                    <i class="fa fa-edit"></i>
                                </button>

                                <button class="btn btn-danger btn-xs deleteBtn" data-toggle="modal"
                                        data-target="#deleteModal"
                                        data-title="Delete Inventory Sale"
                                        onclick="showDeleteSalesModal({{ $sale->id }})">
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