@extends('backend.layout')

@section('title')
    <title>Publishing &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
    <style>
        #viewOrderModal .modal-header {
            padding: 0;
            border-bottom: 1px solid #e5e5e5;
        }

        #viewOrderModal table.no-border td, #viewOrderModal table.no-border tr {
            border: none;
        }
    </style>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file"></i> Svea Orders</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12 margin-top">
        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ trans_choice('site.learners', 1) }}</th>
                    <th>{{ trans('site.details') }}</th>
                    <th>Svea Payment Type</th>
                    <th>Svea Payment Plan</th>
                    <th>{{ trans('site.date-ordered') }}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('admin.learner.show', $order->user_id) }}">
                                {{ $order->user->full_name }}
                            </a>
                        </td>
                        <td>
                            {!! \App\Http\AdminHelpers::getOrderDetails($order) !!}
                        </td>
                        <td>
                            {{ $order->svea_payment_type }}
                        </td>
                        <td>
                            {{ $order->svea_payment_type_description }}
                        </td>
                        <td>{{ \App\Http\FrontendHelpers::formatDate($order->created_at) }}</td>
                        <td>
                            <button class="btn btn-primary btn-xs viewOrderBtn" data-toggle="modal"
                                    data-target="#viewOrderModal"
                                    data-fields="{{ json_encode($order) }}">
                                Receipt
                            </button>
                            @if ($order->svea_delivery_id && !$order->is_credited_amount)
                                <br>
                                <button class="btn btn-info btn-xs addSveaCreditNoteBtn" data-toggle="modal"
                                        data-target="#addSveaCreditNoteModal"
                                        data-action="{{ route("admin.learner.svea.create-credit-note",
													$order->id) }}"
                                        data-fields="{{ json_encode($order) }}" style="margin-top: 5px">
                                    Credit Order
                                </button>
                            @endif
                            @if($order->svea_order_id && !$order->svea_delivery_id)
                                <br>
                                <button class="btn btn-success btn-xs sveaDeliverBtn" data-toggle="modal"
                                        data-target="#sveaDeliverModal"
                                        data-action="{{ route("admin.learner.svea.deliver-order", $order->id) }}"
                                        style="margin-top: 5px">
                                    <i class="fa fa-truck"></i> Deliver
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="pull-right">
                {{ $orders->render() }}
            </div>
        </div>
    </div>

    <div id="viewOrderModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" style="padding: 2rem; font-size: 3rem">&times;</button>
                </div>
                <div class="modal-body" style="padding: 22px 30px;">

                    <div class="row">
                        <div class="col-sm-6">
                            <span>Retur:</span> <br>
                            <span>Forfatterskolen AS</span> <br>
                            <span>Postboks 9233 Kjøsterud</span> <br>
                            <span>3064 DRAMMEN</span> <br>
                            <span>NORWAY</span>
                        </div>

                        <div class="col-sm-6">
                            <img src="{{ asset('/images-new/logo-tagline.png') }}" alt="Logo" class="w-100"
                                 style="height: 100px;object-fit: contain;">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-sm-6">
                            <span class="full-name"></span> <br>
                            <span class="street"></span> <br>
                            <span class="zip-city"></span>
                        </div>
                        <div class="col-sm-6">
                            <span class="mr-2">{{ trans('site.date') }}: </span> <span id="displayDate"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="mt-4 mb-0 font-weight-bold">Ordre</h3>
                        </div>
                    </div>

                    <div class="col-sm-12 mt-4">
                        <table class="table no-border">
                            <tbody>
                            <tr>
                                <td>
                                    <b class="mr-2">Kjøp av:</b>
                                    <b class="package-variation"></b>
                                    <br>
                                </td>
                                <td>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-sm-5 col-sm-offset-7">
                        <table class="table">
                            <tbody>
                            <tr>
                                <td>
                                    <b>{{ trans('site.front.price') }}</b>
                                </td>
                                <td class="price-formatted">
                                </td>
                            </tr>
                            <tr class="discount-row">
                                <td>
                                    <b>{{ trans('site.front.discount') }}</b>
                                </td>
                                <td class="discount-formatted">
                                </td>
                            </tr>
                            <tr class="per-month-row">
                                <td>
                                    <b>{{ trans('site.front.per-month') }}</b>
                                </td>
                                <td class="per-month">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>{{ trans('site.front.total') }}</b>
                                </td>
                                <td class="total-formatted">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="clearfix"></div>
                </div> <!-- end modal-body -->
            </div> <!-- end modal content -->
        </div> <!-- view order modal -->
    </div>

    <div id="addSveaCreditNoteModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        Credit Order
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}

                        <p>
                            Do you want to Credit this order?
                        </p>
                        <button class="btn btn-primary pull-right" type="submit">
                            {{ trans('site.submit') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="sveaDeliverModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        Deliver Order
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}

                        <p>
                            Do you want to Deliver this order?
                        </p>
                        <button class="btn btn-primary pull-right" type="submit">
                            {{ trans('site.submit') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $(".viewOrderBtn").click(function(){
            let fields = $(this).data('fields');
            let modal = $("#viewOrderModal");

            modal.find("#displayDate").text(fields.created_at_formatted);

            modal.find('.full-name').text(fields.user.full_name);
            modal.find('.street').text(fields.user.address.street);
            modal.find('.zip-city').text(fields.user.address.zip + " " + fields.user.address.city);

            modal.find(".package-variation").text(fields.payment_mode_id === 1 ? fields.packageVariation : fields.item);
            modal.find(".payment-mode").text(fields.payment_mode_id === 1 ? 'Bankoverføring' : '');
            modal.find(".payment-plan").text(fields.payment_plan ? fields.payment_plan.plan : '');

            modal.find('.price-formatted').text(fields.price_formatted);

            modal.find('.discount-row').removeClass('hide');
            modal.find('.discount-formatted').text(fields.discount_formatted);

            if (!fields.discount) {
                modal.find('.discount-row').addClass('hide');
            }

            modal.find('.per-month-row').addClass('hide');
            if (fields.plan_id !== 8) {
                modal.find('.per-month-row').removeClass('hide');
            }

            modal.find('.per-month').text(fields.monthly_price_formatted);
            modal.find('.total-formatted').text(fields.total_formatted);
        });

        $(".addSveaCreditNoteBtn").click(function() {
            let modal = $("#addSveaCreditNoteModal");
            let action = $(this).data('action');
            let fields = $(this).data('fields');

            let form = modal.find('form');

            form.attr('action', action);
        });

        $(".sveaDeliverBtn").click(function(){
            let modal = $("#sveaDeliverModal");
            let action = $(this).data('action');
            let form = modal.find('form');

            form.attr('action', action);
        });
    </script>
@stop