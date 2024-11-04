@extends('frontend.learner.self-publishing.layout')

@section('title')
    <title>Project Storage &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" />
<style>
    .custom-select {
        width: 50px !important;
    }
</style>
@stop

@section('content')
<div class="learner-container">
    <div class="container">
        <div class="row">
            <a href="{{ route('learner.project.storage', $project->id) }}"
               class="btn btn-secondary mb-3">
                <i class="fa fa-arrow-left"></i> Back
            </a>

            <div class="col-md-12 learner-assignment no-left-padding">
                <div class="card global-card">
                    <div class="card-body p-0">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ISBN</th>
                                    <th>
                                        Book name
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        {{ $projectUserBook->value }}
                                    </td>
                                    <td>
                                        {{ $projectBook->book_name ?? '' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($projectUserBook)
                    <ul class="nav nav-tabs my-5">
                        <li @if( Request::input('tab') == 'master' || Request::input('tab') == '') class="active" @endif>
                            <a href="?tab=master">Master Data</a>
                        </li>
                        <li @if( Request::input('tab') == 'various' ) class="active" @endif>
                            <a href="?tab=various">Various</a>
                        </li>
                        <li @if( Request::input('tab') == 'inventory' ) class="active" @endif>
                            <a href="?tab=inventory">Inventory Data</a>
                        </li>
                        <li @if( Request::input('tab') == 'book-sales' ) class="active" @endif>
                            <a href="?tab=book-sales">Book Sales</a>
                        </li>
                        <li @if( Request::input('tab') == 'distribution' ) class="active" @endif>
                            <a href="?tab=distribution">Distribution Cost</a>
                        </li>
                        <li @if( Request::input('tab') == 'sales' ) class="active" @endif>
                            <a href="?tab=sales">Inventory Sales</a>
                        </li>
                        <li @if( Request::input('tab') == 'sales-report' ) class="active" @endif>
                            <a href="?tab=sales-report">Sales Report</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade in active">
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
        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        pageLength: 10,
        "aaSorting": []
    });

    $(".inventory-selector").change(function() {
        var form = document.getElementById('inventory-form');
        form.submit();
    });
</script>
@stop