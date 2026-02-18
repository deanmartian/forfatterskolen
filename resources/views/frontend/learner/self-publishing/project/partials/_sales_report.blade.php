<div class="sp-card">
    <div class="sp-card__header">
        <h2>
            <i class="fa fa-file-text-o" style="color:var(--brand-primary);margin-right:6px"></i>
            Salgsrapport
        </h2>
    </div>
    <div class="sp-card__body">
        @foreach ([
            'turned-over' => $turnedOverCount,
            'free' => $freeCount,
            'commission' => $commissionCount,
            'shredded' => $shreddedCount,
            'defective' => $defectiveCount,
            'corrections' => $correctionsCount,
            'counts' => $countsCount,
            'balance' => $balanceCount,
        ] as $label => $count)
            <div class="row" style="margin-bottom:8px">
                <div class="col-xs-4 col-sm-3">
                    <label class="control-label" style="font-weight:600;color:#374151;font-size:14px">
                        {{ ucfirst(str_replace('-', ' ', $label)) }}
                    </label>
                </div>
                <div class="col-xs-8 col-sm-9">
                    <input type="text" class="form-control"
                        value="{{ $count }}" disabled
                        style="background:#f9fafb;border:1px solid var(--border-color);border-radius:8px">
                </div>
            </div>
        @endforeach
    </div>
</div>
