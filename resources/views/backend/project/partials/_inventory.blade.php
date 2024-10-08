<div class="panel">
    <div class="panel-body">
        <div class="col-md-6">
            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">Total Sold Books</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div class="form-group">
                        <input type="text" class="form-control"
                        value="{{ $totalBookSold }}" disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">Total Sales</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div class="form-group">
                        <input type="text" class="form-control"
                        value="{{ $totalBookSale }}" disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">Inventory</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <table class="table">
                        <thead>
                            <tr>
                                <td>Total</td>
                                <td>Delivered</td>
                                <td>Physical Items</td>
                                <td>Returns</td>
                                <td>Balance</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">Order</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div class="form-group">
                        <input type="text" class="form-control"
                        value="" disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">Reservations</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div class="form-group">
                        <input type="text" class="form-control"
                        value="" disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <form action="{{ request()->url() }}" id="inventory-form" method="GET">
                    <input type="hidden" name="tab" value="inventory">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Year</label>
                            <select name="year" id="inventory-year-selector" class="form-control inventory-selector">
                                <option value="all">All</option>
                                @foreach ($years as $year)
                                    <option value="{{ $year }}" 
                                    {{ request('year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Month</label>
                            <select name="month" id="inventory-month-selector" class="form-control inventory-selector">
                                <option value="all">All</option>
                                @for ($month = 1; $month <= 12; $month++)
                                    <option value="{{ $month }}"
                                    {{ request('month') == $month ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::createFromFormat('!m', $month)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </form>
                
                <table class="table">
                    <tbody>
                        <tr>
                            <td></td>
                            <td>Total</td>
                        </tr>

                        @foreach ($yearlyData as $yearly)
                            <tr>
                                <td>
                                    {{ $yearly['name'] }}
                                </td>
                                <td>
                                    {{ $yearly['value'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

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
                        @foreach($projectBook->sales as $bookSale)
                            <tr>
                                <td>
                                    {{ $projectBook->book_name }}
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

                                    <button class="btn btn-danger btn-xs deleteBtn" data-toggle="modal"
                                            data-target="#deleteModal"
                                            data-title="Delete Book Sale"
                                            data-action="{{ route($deleteBookSaleRoute, $bookSale->id) }}">
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