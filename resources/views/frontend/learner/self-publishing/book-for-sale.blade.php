@extends('frontend.learner.self-publishing.layout')

@section('page_title', 'Sales &rsaquo; Forfatterskolen')

@section('styles')
<style>
    .nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus {
        color: #555;
        cursor: default;
        background-color: #fff;
        border: 1px solid #ddd;
        border-bottom-color: transparent;
    }
</style>
@stop

@section('content')
    <div class="learner-container">
        <div class="container">
            <div class="row">
                <div class="col-md-12 dashboard-course no-left-padding">
                    <div class="card global-card p-3">
                        <div class="card-body">
                            <a href="{{ route('learner.book-sale') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Tilbake
                            </a>

                            <ul class="nav nav-tabs mt-3">
                                <li @if( Request::input('tab') == 'inventory' || Request::input('tab') == '') class="active" @endif>
                                    <a href="?tab=inventory">Lagerstatus</a>
                                </li>
                                <li @if( Request::input('tab') == 'sales-report') class="active" @endif>
                                    <a href="?tab=sales-report">Salgsrapport</a>
                                </li>
                                <li @if( Request::input('tab') == 'book-sales') class="active" @endif>
                                    <a href="?tab=book-sales">Boksalg</a>
                                </li>
                                <li @if( Request::input('tab') == 'distribution') class="active" @endif>
                                    <a href="?tab=distribution">Distribusjon</a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade show active">
                                    @if( Request::input('tab') == 'inventory' || Request::input('tab') == '')
                                        @include('frontend.learner.self-publishing.book-for-sale-partials._inventory')
                                     @elseif (Request::input('tab') == 'sales-report')
                                        @include('frontend.learner.self-publishing.book-for-sale-partials._sales_report')
                                    @elseif (Request::input('tab') == 'book-sales')
                                        @include('frontend.learner.self-publishing.book-for-sale-partials._book_sales')
                                    @elseif (Request::input('tab') == 'distribution')
                                        @include('frontend.learner.self-publishing.book-for-sale-partials._distributions')
                                    @endif
                                </div>
                            </div>
                        </div> <!-- end global--card -->
                    </div>
                </div> <!-- end col-md-12 dashboard-course no-left-padding -->
            </div>
        </div>
    </div>
@stop