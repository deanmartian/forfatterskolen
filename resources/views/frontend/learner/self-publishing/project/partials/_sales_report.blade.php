<div class="sp-card">
    <div class="sp-card__header">
        <h2>
            <i class="fa fa-file-text-o" style="color:var(--brand-primary);margin-right:6px"></i>
            Salgsrapport
        </h2>
    </div>
    <div class="sp-card__body">
        @php
            $reportItems = [
                'Antall solgt' => $quantitySoldCount,
                'Omsatt' => $turnedOverCount,
                'Gratis' => $freeCount,
                'Provisjon' => $commissionCount,
                'Makulert' => $shreddedCount,
                'Defekt' => $defectiveCount,
                'Korreksjoner' => $correctionsCount,
                'Tellinger' => $countsCount,
                'Returer' => $returnsCount,
            ];
        @endphp

        <div class="sp-report-grid">
            @foreach ($reportItems as $label => $count)
                <div class="sp-report-row">
                    <span class="sp-report-label">{{ $label }}</span>
                    <span class="sp-report-value">{{ $count }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    .sp-report-grid {
        display: flex;
        flex-direction: column;
        gap: 0;
    }
    .sp-report-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        border-bottom: 1px solid var(--border-color, #e5e7eb);
        transition: background .15s;
    }
    .sp-report-row:last-child {
        border-bottom: none;
    }
    .sp-report-row:hover {
        background: var(--brand-pale, #f9edef);
    }
    .sp-report-label {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
    }
    .sp-report-value {
        font-size: 16px;
        font-weight: 700;
        color: #1f2937;
        min-width: 60px;
        text-align: right;
    }
</style>
