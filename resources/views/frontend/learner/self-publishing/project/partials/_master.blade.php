<div class="sp-card">
    <div class="sp-card__header">
        <h2>
            <i class="fa fa-database" style="color:var(--brand-primary);margin-right:6px"></i>
            {{ trans('site.author-portal.master-data') }}
        </h2>
    </div>
    <div class="sp-card__body">
        @php
            $masterFields = [
                trans('site.author-portal.subtitle') => $projectUserBook->detail->subtitle ?? '',
                trans('site.author-portal.original-title') => $projectUserBook->detail->original_title ?? '',
                trans('site.author-portal.author-text') => $projectUserBook->detail->author ?? '',
                trans_choice('site.editors', 1) => $projectUserBook->detail->editor ?? '',
                trans('site.author-portal.publisher') => $projectUserBook->detail->publisher ?? '',
                trans('site.author-portal.book-group') => $projectUserBook->detail->book_group ?? '',
                trans('site.author-portal.release-date') => $projectUserBook->detail->release_date ?? '',
                trans('site.author-portal.price-no-vat') => $projectUserBook->detail->price_vat ?? '',
                trans('site.author-portal.registered-cultural-council') => $projectUserBook->detail->registered_with_council ?? '',
            ];
        @endphp

        <div class="sp-detail-list">
            @foreach ($masterFields as $label => $value)
                <div class="sp-detail-row">
                    <span class="sp-detail-label">{{ $label }}</span>
                    <span class="sp-detail-value">{{ $value ?: '–' }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    .sp-detail-list {
        display: flex;
        flex-direction: column;
    }
    .sp-detail-row {
        display: flex;
        align-items: center;
        padding: 12px 14px;
        border-bottom: 1px solid var(--border-color, #e5e7eb);
        transition: background .15s;
    }
    .sp-detail-row:last-child {
        border-bottom: none;
    }
    .sp-detail-row:hover {
        background: #f9fafb;
    }
    .sp-detail-label {
        flex: 0 0 200px;
        font-size: 13px;
        font-weight: 600;
        color: #6b7280;
    }
    .sp-detail-value {
        flex: 1;
        font-size: 14px;
        color: #1f2937;
        font-weight: 500;
    }
    @media (max-width: 576px) {
        .sp-detail-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
        }
        .sp-detail-label {
            flex: none;
        }
    }
</style>
