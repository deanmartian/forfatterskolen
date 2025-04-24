<div class="panel">
    <div class="panel-body">
        <div class="col-md-6">
            @foreach ([
                //'quantity-sold' => $quantitySoldCount, 
                'turned-over' => $turnedOverCount, 
                'free' => $freeCount, 
                'commission' => $commissionCount, 
                'shredded' => $shreddedCount,
                'defective' => $defectiveCount,
                'corrections' => $correctionsCount,
                'counts' => $countsCount,
                'balance' => $balanceCount
                //'returns' => $returnsCount
            ] as $label => $count)
                <div class="row">
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label class="control-label">{{ ucfirst(str_replace('-', ' ', $label)) }}</label>
                        </div>
                    </div>
                    <div class="col-xs-9">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ $count }}" disabled>
                                <span class="input-group-btn">
                                    <button class="btn btn-primary btn-xs" 
                                    onclick="showDetails('{{ $label }}', {{ $projectUserBook->id ?? $book->id }})">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            {{-- <div class="row">
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
                                <button class="btn btn-primary btn-xs" 
                                onclick="showDetails('quantity-sold', {{ $projectUserBook->id }})">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div> --}} <!-- end quantity sold -->
        </div> <!-- col-md-6 -->
        
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