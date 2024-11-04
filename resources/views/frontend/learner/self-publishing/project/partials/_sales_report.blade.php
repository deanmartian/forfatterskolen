<div class="panel">
    <div class="panel-body">
        <div class="col-md-12">
            @foreach ([
                'quantity-sold' => $quantitySoldCount, 
                'turned-over' => $turnedOverCount, 
                'free' => $freeCount, 
                'commission' => $commissionCount, 
                'shredded' => $shreddedCount,
                'defective' => $defectiveCount,
                'corrections' => $correctionsCount,
                'counts' => $countsCount,
                'returns' => $returnsCount
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
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>