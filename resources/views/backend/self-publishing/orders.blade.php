@extends('backend.layout')

@section('title')
    <title>Publishing &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-users"></i> Self Publishing orders</h3>

        <a href="{{ route('admin.project.index') }}" class="btn btn-default" style="margin-left: 10px">
            Back
        </a>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <ul class="nav nav-tabs margin-top">
            <li class="active"><a href="#current" data-toggle="tab">Current Orders</a></li>
            <li><a href="#history" data-toggle="tab">Order History</a></li>
            <li><a href="#quotes" data-toggle="tab">Saved Quotes</a></li>
        </ul>

        <div class="tab-content table-users">
            <div class="tab-pane active" id="current">
                @include('backend.self-publishing._order-list' ,
                    [
                        'orders' => $currentOrders
                    ])
            </div>

            <div class="tab-pane" id="history">
                @include('backend.self-publishing._order-list' ,
                    [
                        'orders' => $orderHistory
                    ])
            </div>

            <div class="tab-pane" id="quotes">
                @include('backend.self-publishing._order-list' ,
                    [
                        'orders' => $savedQuotes
                    ])
            </div>
        </div> <!-- end tab-content -->
    </div>
@stop