@extends('frontend.learner.self-publishing.layout')

@section('page_title', trans('site.author-portal-menu.sales') . ' &rsaquo; Forfatterskolen')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" href="{{ asset('js/toastr/toastr.min.css') }}">
    <style>
        /* ── Stat cards row ─────────────────────────────── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--bg-card, #fff);
            border: 1px solid var(--border-color, #e5e7eb);
            border-radius: var(--radius, 10px);
            box-shadow: var(--shadow-sm, 0 1px 3px rgba(0,0,0,.08));
            padding: 20px 22px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            transition: box-shadow .2s ease, transform .15s ease;
        }

        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,.10);
            transform: translateY(-2px);
        }

        .stat-card__icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 18px;
        }

        .stat-card__icon--sold   { background: var(--brand-pale, #f9edef); color: var(--brand-primary, #862736); }
        .stat-card__icon--income { background: #e8f5e9; color: #2e7d32; }
        .stat-card__icon--best   { background: #fff3e0; color: #e65100; }
        .stat-card__icon--books  { background: #e3f2fd; color: #1565c0; }

        .stat-card__body { flex: 1; min-width: 0; }

        .stat-card__label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .stat-card__value {
            font-size: 22px;
            font-weight: 700;
            color: #1f2937;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stat-card__sub {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 2px;
        }

        /* ── Tabs ───────────────────────────────────────── */
        .sales-tabs .nav-tabs {
            border-bottom: 2px solid var(--border-color, #e5e7eb);
            margin-bottom: 0;
        }

        .sales-tabs .nav-tabs > li > a {
            border: none;
            border-bottom: 3px solid transparent;
            color: #6b7280;
            font-weight: 600;
            font-size: 14px;
            padding: 10px 20px;
            margin-bottom: -2px;
            transition: color .2s, border-color .2s;
        }

        .sales-tabs .nav-tabs > li > a:hover {
            color: var(--brand-primary, #862736);
            background: transparent;
            border-bottom-color: var(--brand-pale, #f9edef);
        }

        .sales-tabs .nav-tabs > li.active > a,
        .sales-tabs .nav-tabs > li.active > a:focus {
            color: var(--brand-primary, #862736);
            border-bottom-color: var(--brand-primary, #862736);
            background: transparent;
        }

        /* ── Filter bar ─────────────────────────────────── */
        .filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: flex-end;
            margin-bottom: 24px;
        }

        .filter-bar .filter-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .filter-bar .filter-group label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .3px;
            color: #6b7280;
            margin: 0;
        }

        .filter-bar .filter-group select {
            min-width: 150px;
            border: 1px solid var(--border-color, #e5e7eb);
            border-radius: 8px;
            padding: 7px 12px;
            font-size: 14px;
            color: #374151;
            background: #fff;
            transition: border-color .2s;
        }

        .filter-bar .filter-group select:focus {
            border-color: var(--brand-primary, #862736);
            outline: none;
            box-shadow: 0 0 0 3px rgba(134,39,54,.12);
        }

        .filter-bar__actions {
            margin-left: auto;
        }

        /* ── Card wrapper ───────────────────────────────── */
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

        /* ── Chart ──────────────────────────────────────── */
        .chart-wrap {
            position: relative;
            height: 360px;
            width: 100%;
        }

        /* ── Table ──────────────────────────────────────── */
        .sp-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

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

        /* ── Buttons ────────────────────────────────────── */
        .btn-brand {
            background: var(--brand-primary, #862736);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s, transform .1s;
        }

        .btn-brand:hover { background: var(--brand-dark, #5f1a25); color: #fff; }
        .btn-brand:active { transform: scale(.97); }

        .btn-outline-brand {
            background: transparent;
            color: var(--brand-primary, #862736);
            border: 1.5px solid var(--brand-primary, #862736);
            border-radius: 8px;
            padding: 7px 16px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s, color .2s;
        }

        .btn-outline-brand:hover {
            background: var(--brand-pale, #f9edef);
            color: var(--brand-dark, #5f1a25);
        }

        .btn-sm-brand {
            font-size: 12px;
            padding: 5px 12px;
            border-radius: 6px;
        }

        /* ── Payout checkbox (read-only) ────────────────── */
        .payout-checks {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .payout-checks label {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 13px;
            color: #374151;
            cursor: default;
        }

        .locked-checkbox {
            pointer-events: none;
            appearance: auto;
            accent-color: var(--brand-primary, #862736);
            opacity: 1;
        }

        /* ── Storage-cost quarter cells ─────────────────── */
        .q-cell { font-size: 13px; line-height: 1.7; }
        .q-cell b { color: #374151; }

        /* ── Print helper ───────────────────────────────── */
        @media print {
            .no-print { display: none !important; }
            .sp-card { box-shadow: none; border: 1px solid #ccc; }
        }

        /* ── DataTables overrides ───────────────────────── */
        div.dataTables_wrapper div.dataTables_length select { width: 100%; }
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid var(--border-color, #e5e7eb);
            border-radius: 8px;
            padding: 5px 10px;
        }

        /* ── Responsive ─────────────────────────────────── */
        @media (max-width: 767px) {
            .stats-row { grid-template-columns: 1fr 1fr; }
            .stat-card__value { font-size: 18px; }
            .filter-bar { flex-direction: column; align-items: stretch; }
            .filter-bar__actions { margin-left: 0; margin-top: 4px; }
        }

        @media (max-width: 480px) {
            .stats-row { grid-template-columns: 1fr; }
        }
    </style>
@stop

@section('content')
    <div class="learner-container">
        <div class="container">
            <div class="row">
                <div class="col-md-12 learner-assignment no-left-padding">

                    {{-- ══════ Tabs ══════ --}}
                    <div class="sales-tabs">
                        <ul class="nav nav-tabs mb-4">
                            <li @if( Request::input('tab') == 'sales' || Request::input('tab') == '') class="active" @endif>
                                <a href="?tab=sales&year={{ FrontendHelpers::getLearnerSaleYear() }}">
                                    {{ trans('site.author-portal-menu.sales') }}
                                </a>
                            </li>
                            <li @if( Request::input('tab') == 'distribution' ) class="active" @endif>
                                <a href="?tab=distribution">
                                    {{ trans('site.author-portal.distribution-cost') }}
                                </a>
                            </li>
                            <li @if( Request::input('tab') == 'sales-distribution-cost' ) class="active" @endif>
                                <a href="?tab=sales-distribution-cost">
                                    {{ trans('site.sales-distribution-cost') }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- ══════ Tab content ══════ --}}
                    <div class="tab-content">
                        <div class="tab-pane fade show active">

                            {{-- ────── Distribution tab ────── --}}
                            @if( Request::input('tab') == 'distribution')
                                <div class="sp-card">
                                    <div class="sp-card__header">
                                        <h2>{{ trans('site.author-portal.distribution-cost') }}</h2>
                                    </div>
                                    <div class="sp-card__body">
                                        <div class="table-responsive">
                                            <table class="sp-table">
                                                <thead>
                                                    <tr>
                                                        <th>Nr</th>
                                                        <th>Tjeneste</th>
                                                        <th>Antall</th>
                                                        <th>Beløp</th>
                                                        <th>Dato</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if ($projectUserBook)
                                                        @foreach ($projectUserBook->distributionCosts as $distributionCost)
                                                            <tr>
                                                                <td>{{ $distributionCost->nr }}</td>
                                                                <td>{{ AdminHelpers::distributionServices($distributionCost->service)['value'] }}</td>
                                                                <td>{{ $distributionCost->number }}</td>
                                                                <td>{{ AdminHelpers::currencyFormat($distributionCost->learner_amount) }}</td>
                                                                <td>{{ $distributionCost->date ? FrontendHelpers::formatDate($distributionCost->date) : '' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                                @if ($projectUserBook && $projectUserBook->distributionCosts()->count())
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="3">Totalt</td>
                                                            <td colspan="2">
                                                                {{ FrontendHelpers::currencyFormat(
                                                                    $projectUserBook->totalDistributionCost() * 1.2) }}
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            {{-- ────── Sales-distribution-cost tab ────── --}}
                            @elseif( Request::input('tab') == 'sales-distribution-cost')
                                <div class="sp-card">
                                    <div class="sp-card__header">
                                        <h2>{{ trans('site.sales-distribution-cost') }}</h2>
                                        <button class="btn-outline-brand btn-sm-brand no-print" onclick="window.print()">
                                            <i class="fa fa-print"></i> Skriv ut
                                        </button>
                                    </div>
                                    <div class="sp-card__body">
                                        <div class="table-responsive">
                                            <table class="sp-table">
                                                <thead>
                                                    <tr>
                                                        <th>År</th>
                                                        <th>Q1</th>
                                                        <th>Q2</th>
                                                        <th>Q3</th>
                                                        <th>Q4</th>
                                                        <th>Salg</th>
                                                        <th>Lagerkostnad</th>
                                                        <th>Utbetaling</th>
                                                        <th class="no-print"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if ($registration)
                                                        @foreach ($storageCosts as $storageCost)
                                                        @php $year = $storageCost['year']; @endphp
                                                            <tr>
                                                                <td><strong>{{ $storageCost['year'] }}</strong></td>
                                                                @foreach (['q1','q2','q3','q4'] as $q)
                                                                    <td class="q-cell">
                                                                        <b>Salg:</b> {{ FrontendHelpers::currencyFormat($storageCost[$q.'_sales']) }}<br>
                                                                        <b>Lager:</b> {{ FrontendHelpers::currencyFormat($storageCost[$q.'_distributions']) }}<br>
                                                                        <b>Utbet.:</b> {{ FrontendHelpers::currencyFormat(
                                                                            $storageCost[$q.'_sales'] - $storageCost[$q.'_distributions']) }}
                                                                    </td>
                                                                @endforeach
                                                                <td>{{ FrontendHelpers::currencyFormat($storageCost['total_sales']) }}</td>
                                                                <td>{{ FrontendHelpers::currencyFormat($storageCost['total_distributions']) }}</td>
                                                                <td><strong>{{ FrontendHelpers::currencyFormat($storageCost['payout']) }}</strong></td>
                                                                <td class="no-print">
                                                                    <div style="margin-bottom:8px">
                                                                        <span class="stat-card__label" style="margin-bottom:4px;display:block">Utbetalt?</span>
                                                                        <div class="payout-checks">
                                                                            @foreach([1, 2, 3, 4] as $q)
                                                                                @php
                                                                                    $payoutEntry = isset($payouts[$year][$q]) ? $payouts[$year][$q]->first() : null;
                                                                                    $paid = $payoutEntry ? $payoutEntry->is_paid : false;
                                                                                @endphp
                                                                                <label>Q{{ $q }}
                                                                                    <input type="checkbox" {{ $paid ? 'checked' : 'disabled' }}
                                                                                        class="locked-checkbox" tabindex="-1">
                                                                                </label>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                    <a href="{{ route('learner.project.storage-cost.export',
                                                                        [$registration->project_id, $registration->id, $storageCost['year']]) }}"
                                                                        class="btn-outline-brand btn-sm-brand">
                                                                        <i class="fa fa-download"></i> Last ned
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            {{-- ────── Sales (default) tab ────── --}}
                            @else
                                {{-- Stat cards --}}
                                <div class="stats-row" id="statsRow">
                                    <div class="stat-card">
                                        <div class="stat-card__icon stat-card__icon--sold">
                                            <i class="fa fa-book"></i>
                                        </div>
                                        <div class="stat-card__body">
                                            <div class="stat-card__label">Solgte eksemplarer</div>
                                            <div class="stat-card__value" id="statSold">&mdash;</div>
                                            <div class="stat-card__sub" id="statSoldSub">i valgt år</div>
                                        </div>
                                    </div>
                                    <div class="stat-card">
                                        <div class="stat-card__icon stat-card__icon--income">
                                            <i class="fa fa-money"></i>
                                        </div>
                                        <div class="stat-card__body">
                                            <div class="stat-card__label">Inntekt</div>
                                            <div class="stat-card__value" id="statIncome">&mdash;</div>
                                            <div class="stat-card__sub">total i valgt år</div>
                                        </div>
                                    </div>
                                    <div class="stat-card">
                                        <div class="stat-card__icon stat-card__icon--best">
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <div class="stat-card__body">
                                            <div class="stat-card__label">Beste måned</div>
                                            <div class="stat-card__value" id="statBest">&mdash;</div>
                                            <div class="stat-card__sub" id="statBestSub"></div>
                                        </div>
                                    </div>
                                    <div class="stat-card">
                                        <div class="stat-card__icon stat-card__icon--books">
                                            <i class="fa fa-bar-chart"></i>
                                        </div>
                                        <div class="stat-card__body">
                                            <div class="stat-card__label">Måneder med salg</div>
                                            <div class="stat-card__value" id="statMonths">&mdash;</div>
                                            <div class="stat-card__sub">av 12</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Filter bar --}}
                                <form action="" method="GET" id="salesFilterForm" class="no-print">
                                    <input type="hidden" name="tab" value="sales">
                                    <div class="filter-bar">
                                        <div class="filter-group">
                                            <label for="yearSelector">År</label>
                                            <select name="year" id="yearSelector" onchange="this.form.submit()">
                                                @foreach ($uniqueYears as $yr)
                                                    <option value="{{ $yr }}"
                                                        @if (request()->get('year') == $yr) selected @endif>
                                                        {{ $yr }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="filter-bar__actions">
                                            <button type="button" class="btn-outline-brand btn-sm-brand" onclick="window.print()">
                                                <i class="fa fa-print"></i> Eksporter / Skriv ut
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                {{-- Chart --}}
                                <div class="sp-card" style="margin-bottom: 24px;">
                                    <div class="sp-card__header">
                                        <h2>
                                            <i class="fa fa-bar-chart" style="color:var(--brand-primary);margin-right:6px"></i>
                                            {{ trans('site.author-portal-menu.sales') }}
                                            <span id="chartYearLabel" style="font-weight:400;color:#9ca3af"></span>
                                        </h2>
                                    </div>
                                    <div class="sp-card__body">
                                        <div class="chart-wrap">
                                            <canvas id="chart-line"></canvas>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- ══════ Monthly sales detail modal ══════ --}}
    <div id="monthlySalesModal" class="modal fade" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border-radius:var(--radius);overflow:hidden">
                <div class="modal-header" style="background:var(--brand-pale);border-bottom:1px solid var(--border-color)">
                    <h4 class="modal-title" style="font-weight:700;color:#1f2937">
                        {{ trans('site.author-portal.book-sales') }}
                        <small class="text-muted d-block selected-month-year" style="font-weight:400;margin-top:2px"></small>
                    </h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="{{ trans('site.close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="monthlySalesLoader" class="text-center py-3 d-none">
                        <i class="fa fa-spinner fa-spin fa-2x" aria-hidden="true" style="color:var(--brand-primary)"></i>
                    </div>
                    <div id="monthlySalesErrorState" class="alert alert-danger d-none">
                        {{ trans('site.monthly-sales-error') }}
                    </div>
                    <div id="monthlySalesEmptyState" class="alert alert-info d-none">
                        {{ trans('site.monthly-sales-empty') }}
                    </div>
                    <div class="table-responsive">
                        <table class="sp-table" id="monthlySalesTable">
                            <thead>
                                <tr>
                                    <th>Dato</th>
                                    <th>Kundenavn</th>
                                    <th>Antall</th>
                                    <th>Pris</th>
                                    <th>Rabatt</th>
                                    <th>Beløp</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════ Books for sale modal (preserved) ══════ --}}
    <div id="booksForSaleModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content" style="border-radius:var(--radius);overflow:hidden">
                <div class="modal-header">
                    <h3 class="modal-title">Books for sale</h3>
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('learner.save-for-sale-books', $learner->id) }}"
                          onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <input type="hidden" name="id">

                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" rows="10" cols="30"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Price</label>
                            <input type="number" class="form-control" name="price" required>
                        </div>

                        <button class="btn-brand float-end" type="submit">
                            {{ trans('site.save') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════ Delete modal (preserved) ══════ --}}
    <div id="deleteRecordModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content" style="border-radius:var(--radius);overflow:hidden">
                <div class="modal-header">
                    <h3 class="modal-title"></h3>
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}

                        <p>{{ trans('site.delete-item-question') }}</p>

                        <div class="text-end margin-top">
                            <button type="submit" class="btn btn-danger">{{ trans('site.delete') }}</button>
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('site.cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.bundle.min.js'></script>
    <script>
        /* ── Lock checkboxes ───────────────────────────── */
        document.querySelectorAll('.locked-checkbox').forEach(function(cb) {
            cb.addEventListener('click', function(e) { e.preventDefault(); });
        });

        /* ── DataTables ────────────────────────────────── */
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

        /* ── Currency formatter ────────────────────────── */
        const currencyFormatter = new Intl.NumberFormat('nb-NO', {
            style: 'currency',
            currency: 'NOK',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });

        /* ── Norwegian month names ─────────────────────── */
        const monthNames = [
            "Januar", "Februar", "Mars", "April", "Mai", "Juni",
            "Juli", "August", "September", "Oktober", "November", "Desember"
        ];
        const monthAbbr = ["Jan", "Feb", "Mar", "Apr", "Mai", "Jun",
            "Jul", "Aug", "Sep", "Okt", "Nov", "Des"];

        $(document).ready(function() {
            const ctx = $("#chart-line");
            const yearSelector = $("#yearSelector");
            const monthlySalesModal = $("#monthlySalesModal");
            const monthlySalesTableBody = $("#monthlySalesTable tbody");
            const monthlySalesEmptyState = $("#monthlySalesEmptyState");
            const monthlySalesErrorState = $("#monthlySalesErrorState");
            const monthlySalesLoader = $("#monthlySalesLoader");
            const monthlySalesTitle = monthlySalesModal.find('.selected-month-year');
            const monthlySalesEndpoint = '/account/book-sale/monthly-details/';
            const totalSalesLabel = "{{ addslashes(trans('site.author-portal.total-sales')) }}";
            const salesTooltipLabel = "{{ addslashes(trans('site.author-portal-menu.sales')) }}";
            const viewDetailsLabel = "{{ addSlashes(trans('site.front.our-course.view-details')) }}";

            let year = "{{ request()->get('year') }}";
            const currentYear = new Date().getFullYear();
            if (!year) year = currentYear;

            // Show selected year label
            $('#chartYearLabel').text(year);

            /* ── Chart setup ───────────────────────────── */
            if (ctx.length) {
                const chartOptions = {
                    scales: {
                        yAxes: [{
                            ticks: {
                                min: 0,
                                max: 10,
                                stepSize: 2,
                                callback: function(value) {
                                    if (Math.floor(value) === value) return currencyFormatter.format(value);
                                }
                            },
                            gridLines: {
                                color: 'rgba(0,0,0,.05)',
                                zeroLineColor: 'rgba(0,0,0,.1)'
                            }
                        }],
                        xAxes: [{
                            gridLines: { display: false }
                        }]
                    },
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: 'rgba(31,41,55,.92)',
                        titleFontSize: 13,
                        bodyFontSize: 13,
                        cornerRadius: 8,
                        xPadding: 12,
                        yPadding: 10,
                        mode: 'single',
                        callbacks: {
                            title: function(items) {
                                return monthNames[items[0].index] + ' ' + year;
                            },
                            label: function(tooltipItem) {
                                return salesTooltipLabel + ': ' + currencyFormatter.format(tooltipItem.yLabel);
                            },
                            afterBody: function() {
                                return ['', '\u202F\u202F' + viewDetailsLabel];
                            }
                        }
                    },
                    onClick: function(evt, elements) {
                        if (!elements.length) return;
                        const element = elements[0];
                        const idx = typeof element._index !== 'undefined' ? element._index : element.index;
                        const selYear = yearSelector.length ? yearSelector.val() : year;
                        showMonthlySalesModal(selYear, idx);
                    },
                    legend: { display: true, labels: { fontColor: '#6b7280', fontSize: 13 } }
                };

                const myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: monthAbbr,
                        datasets: [{
                            data: [],
                            label: totalSalesLabel + ': ',
                            borderColor: 'var(--brand-primary)',
                            backgroundColor: 'rgba(134,39,54,.75)',
                            hoverBackgroundColor: 'rgba(134,39,54,.95)',
                            borderWidth: 0,
                            borderRadius: 4,
                            fill: false
                        }]
                    },
                    options: chartOptions
                });

                // Fetch chart data
                ajax_chart(myChart, '/account/book-sale/list-by-month/' + year);
            }

            /* ── Monthly sales modal ───────────────────── */
            function showMonthlySalesModal(selectedYear, monthIndex) {
                if (monthIndex < 0 || monthIndex >= 12) return;

                const normYear = parseInt(selectedYear, 10) || year || currentYear;
                const monthNumber = monthIndex + 1;

                monthlySalesTitle.text(monthNames[monthIndex] + ' ' + normYear);
                monthlySalesTableBody.empty();
                monthlySalesEmptyState.addClass('d-none');
                monthlySalesErrorState.addClass('d-none');
                monthlySalesLoader.removeClass('d-none');
                monthlySalesModal.modal('show');

                $.getJSON(monthlySalesEndpoint + normYear + '/' + monthNumber)
                    .done(function(records) {
                        if (Array.isArray(records) && records.length) {
                            records.forEach(function(r) {
                                const row = $('<tr/>');
                                row.append($('<td/>').text(r.date || ''));
                                row.append($('<td/>').text(r.customer_name || ''));
                                row.append($('<td/>').text(r.quantity != null ? r.quantity : ''));
                                row.append($('<td/>').text(r.price || ''));
                                row.append($('<td/>').text(r.discount || ''));
                                row.append($('<td/>').text(r.amount || ''));
                                monthlySalesTableBody.append(row);
                            });
                        } else {
                            monthlySalesEmptyState.removeClass('d-none');
                        }
                    })
                    .fail(function() {
                        monthlySalesErrorState.removeClass('d-none');
                    })
                    .always(function() {
                        monthlySalesLoader.addClass('d-none');
                    });
            }

            /* ── Books-for-sale modal ──────────────────── */
            $(".booksForSaleBtn").click(function() {
                let record = $(this).data('record');
                let modal = $('#booksForSaleModal');
                modal.find('[name=id]').val('');
                if (record) {
                    modal.find('[name=id]').val(record.id);
                    modal.find('[name=title]').val(record.title);
                    modal.find('[name=description]').text(record.description);
                    modal.find('[name=price]').val(record.price);
                }
            });

            /* ── Delete modal ──────────────────────────── */
            $(".deleteRecordBtn").click(function() {
                let modal = $("#deleteRecordModal");
                modal.find('.modal-title').text($(this).data('title'));
                modal.find('form').attr('action', $(this).data('action'));
            });
        });

        /* ── Chart data loader + stat-card updater ─────── */
        function ajax_chart(chart, url) {
            $.getJSON(url).done(function(response) {
                const maxVal = Math.max(...response);
                const total = response.reduce((a, b) => a + b, 0);
                const formatted = currencyFormatter.format(total);

                // Update chart data
                chart.data.datasets[0].data = response;
                chart.data.datasets[0].label = '{{ addslashes(trans("site.author-portal.total-sales")) }}: ' + formatted;

                const hasData = response.some(v => v > 0);
                if (hasData) {
                    const dynMax = Math.ceil(maxVal * 1.15 / 10) * 10 || 10;
                    const step = Math.ceil(dynMax / 5);
                    chart.options.scales.yAxes[0].ticks.max = dynMax;
                    chart.options.scales.yAxes[0].ticks.stepSize = step;
                }
                chart.update();

                // ── Update stat cards ──
                // Total sold = sum of quantities (we approximate from amounts if separate endpoint unavailable)
                const monthsWithSales = response.filter(v => v > 0).length;
                const bestIdx = response.indexOf(maxVal);

                $('#statIncome').text(formatted);
                $('#statMonths').text(monthsWithSales);

                if (bestIdx >= 0 && maxVal > 0) {
                    $('#statBest').text(monthNames[bestIdx]);
                    $('#statBestSub').text(currencyFormatter.format(maxVal));
                } else {
                    $('#statBest').text('–');
                    $('#statBestSub').text('');
                }

                // For "sold count" we show the total if the API returns amounts
                // (the backend returns NOK amounts per month; override if a count endpoint exists)
                $('#statSold').text(hasData ? total.toLocaleString('nb-NO') : '0');
                $('#statSoldSub').text('kr i valgt år');
            });
        }
    </script>
@stop
