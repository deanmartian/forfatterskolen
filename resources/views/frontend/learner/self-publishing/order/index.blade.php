@extends('frontend.learner.self-publishing.layout')

@section('title')
    <title>Project &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
    <style>
        .fa-shopping-cart-red {
            color: #862736 !important;
            font-size: 20px;
        }

        .fa-shopping-cart-red:before {
            content: "\f07a";
        }
    </style>
@stop

@section('content')
    <div class="learner-container order-container">
        <div class="container">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#current" data-toggle="tab">Current Orders</a></li>
                <li><a href="#tab2" data-toggle="tab">Order History</a></li>
                <li><a href="#tab3" data-toggle="tab">Saved Quotes</a></li>
            </ul>
          
            <div class="tab-content">
                <div class="tab-pane active" id="current">
                    @include('frontend.learner.self-publishing.order._current' ,
                    [
                        'orders' => $currentOrders
                    ])
                </div>
                <div class="tab-pane" id="tab2">
                    @include('frontend.learner.self-publishing.order._history' ,
                    [
                        'orders' => $orderHistory
                    ])
                </div>
                <div class="tab-pane" id="tab3">
                    @include('frontend.learner.self-publishing.order._quote' ,
                    [
                        'orders' => $savedQuotes
                    ])
                </div>
            </div>
        </div>
    </div>

    <div id="deleteOrderModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        Delete Order
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        @method('DELETE')
                        
                        <p>
                            Are you sure you want to delete this record?
                        </p>

                        <div class="text-right">
                            <button class="btn btn-danger" type="submit">{{ trans('site.delete') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="saveQuoteModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        Save Quote
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        
                        <p>
                            Are you sure you want to save this quote?
                        </p>

                        <div class="text-right">
                            <button class="btn btn-success" type="submit">{{ trans('site.submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="moveOrderModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        Move Order
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        
                        <p>
                            Are you sure you want to move this order?
                        </p>

                        <div class="text-right">
                            <button class="btn btn-success" type="submit">{{ trans('site.submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
<script>
    $(".deleteOrderBtn").click(function() {
        let action = $(this).data('action');

        $("#deleteOrderModal").find("form").attr('action', action);
    });

    $(".saveQuoteBtn").click(function(){
        let action = $(this).data('action');

        $("#saveQuoteModal").find("form").attr('action', action);
    });

    $(".moveOrderBtn").click(function(){
        let action = $(this).data('action');

        $("#moveOrderModal").find("form").attr('action', action);
    });
</script>
@stop