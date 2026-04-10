@extends('frontend.learner.self-publishing.layout')

@section('page_title', 'Lagerdetaljer &rsaquo; Forfatterskolen')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" />
<style>
    /* ── Tabs ───────────────────────────────────────── */
    .storage-tabs .nav-tabs {
        border-bottom: 2px solid var(--border-color, #e5e7eb);
        margin-bottom: 0;
        flex-wrap: wrap;
        display: flex;
        gap: 0;
    }

    .storage-tabs .nav-tabs > li > a {
        border: none;
        border-bottom: 3px solid transparent;
        color: #6b7280;
        font-weight: 600;
        font-size: 13px;
        padding: 10px 16px;
        margin-bottom: -2px;
        transition: color .2s, border-color .2s;
        white-space: nowrap;
    }

    .storage-tabs .nav-tabs > li > a:hover {
        color: var(--brand-primary, #862736);
        background: transparent;
        border-bottom-color: var(--brand-pale, #f9edef);
    }

    .storage-tabs .nav-tabs > li.active > a,
    .storage-tabs .nav-tabs > li.active > a:focus {
        color: var(--brand-primary, #862736);
        border-bottom-color: var(--brand-primary, #862736);
        background: transparent;
    }

    /* ── Back button ────────────────────────────────── */
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: transparent;
        color: var(--brand-primary, #862736);
        border: 1.5px solid var(--brand-primary, #862736);
        border-radius: 8px;
        padding: 7px 16px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: background .2s, color .2s;
        margin-bottom: 20px;
    }

    .btn-back:hover {
        background: var(--brand-pale, #f9edef);
        color: var(--brand-dark, #5f1a25);
        text-decoration: none;
    }

    /* ── ISBN header card ────────────────────────────── */
    .isbn-header {
        background: var(--bg-card, #fff);
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: var(--radius, 10px);
        box-shadow: var(--shadow-sm, 0 1px 3px rgba(0,0,0,.08));
        padding: 16px 22px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .isbn-header__item {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .isbn-header__label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .4px;
        color: #9ca3af;
    }

    .isbn-header__value {
        font-size: 15px;
        font-weight: 700;
        color: #1f2937;
    }

    /* ── Tab content spacing ─────────────────────────── */
    .tab-content {
        margin-top: 24px;
    }

    /* ── Responsive ──────────────────────────────────── */
    @media (max-width: 767px) {
        .storage-tabs .nav-tabs > li > a {
            font-size: 12px;
            padding: 8px 12px;
        }
    }

    /* ── Shared sp-card/sp-table (if not loaded from layout) ── */
    .sp-card {
        background: var(--bg-card, #fff);
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: var(--radius, 10px);
        box-shadow: var(--shadow-sm, 0 1px 3px rgba(0,0,0,.08));
        overflow: hidden;
    }
    .sp-card__header {
        padding: 18px 22px;
        border-bottom: 1px solid var(--border-color, #e5e7eb);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }
    .sp-card__header h2 {
        font-size: 16px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }
    .sp-card__body { padding: 22px; }

    .sp-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .sp-table thead th {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .4px;
        color: #6b7280;
        background: #f9fafb;
        padding: 10px 14px;
        border-bottom: 2px solid var(--border-color, #e5e7eb);
        white-space: nowrap;
    }
    .sp-table tbody tr { transition: background .15s; }
    .sp-table tbody tr:nth-child(even) { background: #fafafa; }
    .sp-table tbody tr:hover { background: var(--brand-pale, #f9edef); }
    .sp-table tbody td {
        padding: 11px 14px;
        font-size: 14px;
        color: #374151;
        border-bottom: 1px solid var(--border-color, #e5e7eb);
        vertical-align: middle;
    }
    .sp-table tfoot td {
        padding: 12px 14px;
        font-weight: 700;
        font-size: 14px;
        border-top: 2px solid var(--border-color, #e5e7eb);
    }

    /* ── Filter bar (for inventory tab) ─────────────── */
    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-end;
    }
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .filter-group label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .3px;
        color: #6b7280;
        margin: 0;
    }
    .filter-group select {
        min-width: 150px;
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 8px;
        padding: 7px 12px;
        font-size: 14px;
        color: #374151;
        background: #fff;
        transition: border-color .2s;
    }
    .filter-group select:focus {
        border-color: var(--brand-primary, #862736);
        outline: none;
        box-shadow: 0 0 0 3px rgba(134,39,54,.12);
    }
</style>
@stop

@section('content')
<div class="learner-container">
    <div class="container">
        <div class="row">
            <div class="col-md-12 learner-assignment no-left-padding">

                <a href="{{ route('learner.project.storage', $project->id) }}" class="btn-back">
                    <i class="fa fa-arrow-left"></i> {{ trans('site.back') }}
                </a>

                {{-- ISBN header --}}
                <div class="isbn-header">
                    <div class="isbn-header__item">
                        <span class="isbn-header__label">{{ trans('site.author-portal.isbn') }}</span>
                        <span class="isbn-header__value">{{ $projectUserBook->value }}</span>
                    </div>
                    <div class="isbn-header__item">
                        <span class="isbn-header__label">{{ trans('site.author-portal.book-name') }}</span>
                        <span class="isbn-header__value">{{ $projectBook->book_name ?? '–' }}</span>
                    </div>
                </div>

                @if($projectUserBook)
                    {{-- Tabs --}}
                    <div class="storage-tabs">
                        <ul class="nav nav-tabs">
                            <li @if( Request::input('tab') == 'master' || Request::input('tab') == '') class="active" @endif>
                                <a href="?tab=master">{{ trans('site.author-portal.master-data') }}</a>
                            </li>
                            <li @if( Request::input('tab') == 'various' ) class="active" @endif>
                                <a href="?tab=various">{{ trans('site.author-portal.various') }}</a>
                            </li>
                            <li @if( Request::input('tab') == 'inventory' ) class="active" @endif>
                                <a href="?tab=inventory">{{ trans('site.author-portal.inventory-data') }}</a>
                            </li>
                            <li @if( Request::input('tab') == 'book-sales' ) class="active" @endif>
                                <a href="?tab=book-sales">{{ trans('site.author-portal.book-sales') }}</a>
                            </li>
                            <li @if( Request::input('tab') == 'distribution' ) class="active" @endif>
                                <a href="?tab=distribution">{{ trans('site.author-portal.distribution-cost') }}</a>
                            </li>
                            <li @if( Request::input('tab') == 'sales' ) class="active" @endif>
                                <a href="?tab=sales">{{ trans('site.author-portal.inventory-sales') }}</a>
                            </li>
                            <li @if( Request::input('tab') == 'sales-report' ) class="active" @endif>
                                <a href="?tab=sales-report">{{ trans('site.author-portal.sales-report') }}</a>
                            </li>
                        </ul>
                    </div>

                    {{-- Tab content --}}
                    <div class="tab-content">
                        <div class="tab-pane fade show active">
                            @if( Request::input('tab') == 'various')
                                @include('frontend.learner.self-publishing.project.partials._various')
                            @elseif( Request::input('tab') == 'inventory')
                                @include('frontend.learner.self-publishing.project.partials._inventory')
                            @elseif( Request::input('tab') == 'book-sales')
                                @include('frontend.learner.self-publishing.project.partials._book-sales')
                            @elseif( Request::input('tab') == 'distribution')
                                @include('frontend.learner.self-publishing.project.partials._distributions')
                            @elseif( Request::input('tab') == 'sales')
                                @include('frontend.learner.self-publishing.project.partials._sales')
                            @elseif( Request::input('tab') == 'sales-report')
                                @include('frontend.learner.self-publishing.project.partials._sales_report')
                            @else
                                @include('frontend.learner.self-publishing.project.partials._master')
                            @endif
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(".dt-table").DataTable({
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Alle"]],
        pageLength: 10,
        aaSorting: [],
        language: {
            search: "Søk:",
            lengthMenu: "Vis _MENU_ rader",
            info: "Viser _START_ til _END_ av _TOTAL_ rader",
            infoEmpty: "Ingen rader å vise",
            paginate: { previous: "Forrige", next: "Neste" }
        }
    });

    $(".inventory-selector").change(function() {
        document.getElementById('inventory-form').submit();
    });
</script>
@stop
