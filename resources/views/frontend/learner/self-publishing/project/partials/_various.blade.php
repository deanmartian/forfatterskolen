<div class="sp-card">
    <div class="sp-card__header">
        <h2>
            <i class="fa fa-cogs" style="color:var(--brand-primary);margin-right:6px"></i>
            {{ trans('site.author-portal.various') }}
        </h2>
    </div>
    <div class="sp-card__body">
        @php
            $variousFields = [
                trans('site.author-portal.publisher') => $projectUserBook->various->publisher ?? '',
                trans('site.author-portal.minimum-beh') => $projectUserBook->various->minimum_stock ?? '',
                trans('site.author-portal.weight-in-grams') => $projectUserBook->various->weight ?? '',
                trans('site.author-portal.height-mm') => $projectUserBook->various->height ?? '',
                trans('site.author-portal.width-mm') => $projectUserBook->various->width ?? '',
                trans('site.author-portal.thickness-mm') => $projectUserBook->various->thickness ?? '',
                trans('site.author-portal.self-catering') => $projectUserBook->various->cost ?? '',
                trans('site.author-portal.material-cost') => $projectUserBook->various->material_cost ?? '',
            ];
        @endphp

        <div class="sp-detail-list">
            @foreach ($variousFields as $label => $value)
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
