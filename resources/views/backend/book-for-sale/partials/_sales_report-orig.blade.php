<div class="panel">
    <div class="panel-body">
        <div class="col-md-6">
            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">Quantity Sold</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $quantitySoldCount }}" disabled>
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-xs" onclick="showDetails('quantity-sold', {{ $book->id }})">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">Turned over</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $turnedOverCount }}" disabled>
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-xs" onclick="showDetails('turned-over', {{ $book->id }})">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">Free</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $freeCount }}" disabled>
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-xs" onclick="showDetails('free', {{ $book->id }})">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">Commission</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $commissionCount }}" disabled>
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-xs" onclick="showDetails('commission', {{ $book->id }})">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">Shredded</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $shreddedCount }}" disabled>
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-xs" onclick="showDetails('shredded', {{ $book->id }})">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">Defective</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $defectiveCount }}" disabled>
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-xs" onclick="showDetails('defective', {{ $book->id }})">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">Corrections</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $correctionsCount }}" disabled>
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-xs" onclick="showDetails('corrections', {{ $book->id }})">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">Counts</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $countsCount }}" disabled>
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-xs" onclick="showDetails('counts', {{ $book->id }})">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">Returns</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $returnsCount }}" disabled>
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-xs" onclick="showDetails('returns', {{ $book->id }})">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- end col-md-6 -->

        <div class="col-md-6 hidden" id="sales-report-details">
            <h3 style="display: inline" class="report-title"></h3>
            <button class="btn btn-success btn-sm margin-bottom pull-right salesReportBtn" data-toggle="modal" 
            data-target="#salesReportModal">
                Add
            </button>
            <input type="hidden" name="hidden_type">
            <input type="hidden" name="hidden_book_id">

            <div class="clearfix"></div>

            <table class="table" id="sales-details-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Value</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>