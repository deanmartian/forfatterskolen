<div class="sp-card">
    <div class="sp-card__header">
        <h2>
            <i class="fa fa-archive" style="color:var(--brand-primary);margin-right:6px"></i>
            Lagerstatus
        </h2>
    </div>
    <div class="sp-card__body">
        {{-- Oppsummering --}}
        <div class="sp-summary-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px">
            <div class="sp-summary-item">
                <span class="sp-summary-label">Totalt solgte bøker</span>
                <span class="sp-summary-value">{{ $totalBookSold }}</span>
            </div>
            <div class="sp-summary-item">
                <span class="sp-summary-label">Totalt salg</span>
                <span class="sp-summary-value">{{ $totalBookSale }}</span>
            </div>
            <div class="sp-summary-item">
                <span class="sp-summary-label">Bestillinger</span>
                <span class="sp-summary-value">{{ $book->inventory->order ?? '–' }}</span>
            </div>
            <div class="sp-summary-item">
                <span class="sp-summary-label">Reservasjoner</span>
                <span class="sp-summary-value">{{ $book->inventory->reservations ?? '–' }}</span>
            </div>
        </div>

        {{-- Lagertabell --}}
        <div class="table-responsive">
            <table class="sp-table">
                <thead>
                    <tr>
                        <th>Totalt</th>
                        <th>Levert</th>
                        <th>Fysiske eks.</th>
                        <th>Returer</th>
                        <th>Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $book->inventory->total ?? '–' }}</td>
                        <td>{{ $book->inventory->delivered ?? '–' }}</td>
                        <td>{{ $book->inventory->physical_items ?? '–' }}</td>
                        <td>{{ $book->inventory->returns ?? '–' }}</td>
                        <td><strong>{{ $book->inventory->balance ?? '–' }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .sp-summary-item {
        background: #f9fafb;
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 8px;
        padding: 14px 16px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .sp-summary-label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .3px;
        color: #6b7280;
    }
    .sp-summary-value {
        font-size: 20px;
        font-weight: 700;
        color: #1f2937;
    }
</style>
