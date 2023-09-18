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
                                <td>
                                    {{ $book->inventory->total ?? '' }}
                                </td>
                                <td>
                                    {{ $book->inventory->delivered ?? '' }}
                                </td>
                                <td>
                                    {{ $book->inventory->physical_items ?? '' }}
                                </td>
                                <td>
                                    {{ $book->inventory->returns ?? '' }}
                                </td>
                                <td>
                                    {{ $book->inventory->balance ?? '' }}
                                </td>
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
                        value="{{ $book->inventory->order ?? '' }}" disabled>
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
                        value="{{ $book->inventory->reservations ?? '' }}" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>