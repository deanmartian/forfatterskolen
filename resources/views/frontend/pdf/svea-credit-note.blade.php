<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Document</title>
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/frontend/main.css') }}"/>

    <style>
        .no-border td {
            border: none !important;
        }
        .receipt-logo-container{
            background: #ffffff!important;
            text-align: left;
            position: absolute;
        }
        .receipt-logo-container img{
            height: 100px; /*67*/
            object-fit: contain;
        }
        .receipt-pink-bg span{
            color: black;
            /* font-weight: 500; */
        }
        .receipt-pink-bg{
            padding: 20px;
            background-color: #4c8485;
        }
        .customer-name-address{
            /* padding-top: 26px */
        }
        .receipt-table{
            padding: 0px;
        }
        .float-right{
            float: right;
        }
        .receipt-footer div, .receipt-footer span{
            font-size: 15px;
            color: black;
        }
        .w-50{
            vertical-align: top;
        }
        .w-48{
            width: 48%;
        }
        .receipt-table{
            border-top: 1px solid #e175a2;
            border-bottom: 1px solid #e175a2;
        }
        tr > td{
            padding: 0px !important;
        }
        .w-80{
            width: 80%;
            vertical-align: top;
        }
        .w-20{
            width: 20%;
            vertical-align: top;
        }
        .w-70{
            width: 70%;
            vertical-align: top;
        }
        .total-table{
            font-weight: 600;
        }
        /* .receipt-table span, .receipt-table div{
            font-size: 15px;
        } */
    </style>
</head>
<body>

<div class="row" style="height: 150px">
    <div class="col-sm-12">
        <div class="w-50 float-left">
            <div class="receipt-logo-container">
                <img src="{{ url('/images-new/logo-tagline.png') }}" alt="Logo" style="width: 260px;">
            </div>
        </div>
        <div class="w-50 float-right">
            <div class="receipt-invoice-pink">
                <span style="font-size: 19px; font-weight: 600;">Kreditnota</span>
                <div class="receipt-pink-bg">
                    <div>
                        <span>{{ trans('site.order-history.due-date') }}</span>
                        <span>{{ $order->created_at_formatted }}</span>
                    </div>
                    <div>
                        <span>{{ trans('site.order-history.amount-to-pay') }}</span>
                        <span>{{ $order->total_formatted }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" style="height: 160px">
    <div class="col-sm-12">
        <div class="w-50 float-left">
            <div class="receipt-papermoon-address">
                <span style="font-weight: 600;">{{ trans('site.order-history.fs-name') }}</span> <br>
                <span style="font-weight: 600;">{{ trans('site.order-history.fs-address1') }}</span> <br>
                <span>{{ trans('site.order-history.fs-address2') }}, {{ trans('site.order-history.fs-country') }}</span> <br>
                <span>{{ trans('site.order-history.fs-site') }}</span>
            </div>
        </div>
        <div class="w-50 float-right">
            @if($order->company)
            <div class="customer-name-address">
                <span>{{ $order->company->company_name }}</span> <br>
                <span>{{ $order->company->street_address }}</span> <br>
                <span>{{ $order->company->post_number }} {{ $order->company->place }}</span><br>
                <span>{{ $order->company->customer_number }}</span>
            </div>
            @else
            <div class="customer-name-address">
                <span>{{ $user->full_name }}</span> <br>
                <span>{{ $user->address->street }}</span> <br>
                <span>{{ $user->address->zip }} {{ $user->address->city }}</span>
            </div>
            @endif
        </div>
    </div>
</div>

<hr style="height: 1px; background-color: #4c8485; border: none;">
<div class="row receipt-table" style="height: 80px">
    <div class="col-sm-12">
        <div class="w-48 float-left">
            <div>
                <span class="float-left">{{ trans('site.order-history.invoice-number') }}</span>
                <span class="float-right">{{ substr(str_repeat(0, 6).$order->id, - 6) }}</span>
            </div>
            <div>
                <span>{{ trans('site.order-history.customer-number') }}</span>
                <span class="float-right">{{ substr(str_repeat(0, 6).$user->id, - 6) }}</span>
            </div>
            <div>
                <span>{{ trans('site.order-history.customer-reference') }}</span>
                <span class="float-right">Sven-Inge Henningsen</span>
            </div>
        </div>
        <div class="w-50 float-right">
            <div>
                <span>{{ trans('site.order-history.invoice-date') }}</span>
                <span class="float-right">{{ $order->created_at_formatted }}</span>
            </div>
            <div>
                <span>{{ trans('site.order-history.payment-terms') }}</span>
                <span class="float-right">{{ $order->payment_plan ? $order->payment_plan->plan : '' }}</span>
            </div>
            <div>
                <span>{{ trans('site.order-history.interest') }}</span>
                <span class="float-right">12%</span>
            </div>
        </div>
    </div>
</div>
<hr style="height: 1px; background-color: #4c8485; border: none;">

<div class="row receipt-table" style="height: 180px">
    <div class="col-sm-12">
        <table class="table no-border">
            <tbody>
            <tr>
                <td>{{ trans('site.order-history.description') }}</td>
                <td>{{ trans('site.order-history.vat') }}</td>
                <td>{{ trans('site.order-history.quantity') }}</td>
                <td>{{ trans('site.order-history.price') }}</td>
                <td>{{ trans('site.order-history.sum') }}</td>
            </tr>
            <tr>
                <td>
                    @if (in_array($order->type, [1, 6]))
                    <b><i>{{ $order->item }} - {{ $order->package->variation }}</i></b>
                    @else
                        {{ $order->item }}
                    @endif
                </td>
                <td>0%</td>
                <td>1 stk</td>
                <td>{{ $order->price_formatted }}</td>
                <td>{{ $order->total_formatted }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="row receipt-table total-table" style="height: 160px">
    <div class="col-sm-12">
        <div class="w-50 float-right">
            <div>
                <span>{{ trans('site.order-history.total-vat') }}</span>
                <span class="float-right">{{ $order->total_formatted }}</span>
            </div>
            <div>
                <span>{{ trans('site.order-history.total-to-pay') }}</span>
                <span class="float-right">{{ $order->total_formatted }}</span>
            </div>
            <div>
                <span>Kreditert beløp</span>
                <span class="float-right">-{{ $order->total_formatted }}</span>
            </div>
        </div>
    </div>
</div>


<div class="row receipt-footer" style="height: 210px">
    <div class="col-sm-12">
        <div class="w-50">
            <hr style="height: 1px; background-color: #4c8485; border: none;">
            <div>
                <span>{{ trans('site.order-history.fs-name') }}</span>
            </div>
            <div>
                <span>{{ trans('site.order-history.fs-address1') }}</span>
            </div>
            <div>{{ trans('site.order-history.fs-address2') }}</div>
            <div>{{ trans('site.order-history.fs-country') }} <span>{{ trans('site.order-history.organization') }}</span></div>
            <hr style="height: 1px; background-color: #4c8485; border: none;">
        </div>
        <div class="w-25 float-right">
            <!-- <div style="padding-left: 20px;">Scanna i bankapp</div>
            <img style="padding: 20px 0px 0px 20px;" src="{{ url('/images-new/receipt-qr.png') }}" alt="Logo" style="width: 100%;"> -->
        </div>
    </div>
</div>

<!-- <div class="row">
    <div class="col-sm-12">
        <div class="w-50"></div>
        <div class="w-50 float-right">

            $outputString = preg_replace('/[^0-9]/', ' ', $order->total_formatted);
            echo($outputString);
        ?></div>
    </div>
</div> -->

</body>
</html>