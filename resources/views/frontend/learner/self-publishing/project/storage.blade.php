@extends('frontend.learner.self-publishing.layout')

@section('page_title', 'Prosjektlager &rsaquo; Forfatterskolen')

@section('styles')
<style>
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
    }
    .sp-card__header h2 {
        font-size: 16px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }
    .sp-card__body { padding: 0; }

    .sp-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .sp-table thead th {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .4px;
        color: #6b7280;
        background: #f9fafb;
        padding: 10px 22px;
        border-bottom: 2px solid var(--border-color, #e5e7eb);
        white-space: nowrap;
    }
    .sp-table tbody tr { transition: background .15s; }
    .sp-table tbody tr:hover { background: var(--brand-pale, #f9edef); }
    .sp-table tbody td {
        padding: 14px 22px;
        font-size: 14px;
        color: #374151;
        border-bottom: 1px solid var(--border-color, #e5e7eb);
        vertical-align: middle;
    }
    .sp-table tbody td a {
        color: var(--brand-primary, #862736);
        font-weight: 600;
        text-decoration: none;
    }
    .sp-table tbody td a:hover {
        text-decoration: underline;
    }
</style>
@stop

@section('content')
<div class="learner-container">
    <div class="container">
        <div class="row">
            <div class="col-md-12 dashboard-course no-left-padding">

                <a href="{{ route('learner.project.show', $project->id) }}" class="btn-back">
                    <i class="fa fa-arrow-left"></i> {{ trans('site.back') }}
                </a>

                <div class="sp-card">
                    <div class="sp-card__header">
                        <h2>
                            <i class="fa fa-archive" style="color:var(--brand-primary);margin-right:6px"></i>
                            Prosjektlager
                        </h2>
                    </div>
                    <div class="sp-card__body">
                        <div class="table-responsive">
                            <table class="sp-table">
                                <thead>
                                    <tr>
                                        <th>{{ trans('site.author-portal.isbn') }}</th>
                                        <th>{{ trans('site.author-portal.book-name') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($projectCentralDistributions as $projectCentralDistribution)
                                        <tr>
                                            <td>
                                                <a href="{{ route('learner.project.storage-details',
                                                    [$project->id, $projectCentralDistribution->id]) }}">
                                                    {{ $projectCentralDistribution->value }}
                                                </a>
                                            </td>
                                            <td>{{ $projectBook->book_name ?? '' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
