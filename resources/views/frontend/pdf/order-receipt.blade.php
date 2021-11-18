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
    </style>
</head>
<body>

<div class="row" style="height: 130px">
    <div class="col-sm-12">
        <div class="w-50 float-left">
            <span>Retur:</span> <br>
            <span>Forfatterskolen AS</span> <br>
            <span>Postboks 9233 Kjøsterud</span> <br>
            <span>3064 DRAMMEN</span> <br>
            <span>NORWAY</span>
        </div>
        <div class="w-50 float-right">
            <img src="{{ url('/images-new/logo-tagline.png') }}" alt="Logo" class="w-100"
                 style="height: 100px;object-fit: contain;">
        </div>
    </div>
</div>

<div class="row" style="height: 80px">
    <div class="col-sm-12">
        <div class="w-50 float-left">
            <span>{{ $user->full_name }}</span> <br>
            <span>{{ $user->address->street }}</span> <br>
            <span>{{ $user->address->zip }} {{ $user->address->city }}</span>
        </div>
        <div class="w-50 float-right">
            <span class="mr-2">{{ trans('site.date') }}: </span> <span>{{ $order->created_at_formatted }}</span>
        </div>
    </div>
</div>

<h4 class="mt-4 mb-0">Ordre</h4>

<div class="col-sm-12 mt-5">
    <table class="table no-border">
        <tbody>
        <tr>
            <td>
                <b class="mr-2">Kjøp av:</b>
                @if ($order->type === 1)
                    <b><i>{{ $order->item }} - {{ $order->package->variation }}</i></b>
                @else
                    <b><i>{{ $order->item }}</i></b>
                @endif
                    <br>
                {{--(<span>{{ trans('site.front.form.payment-method') }}: {{ $order->payment_mode_id === 1 ? 'Bankoverføring'
                                    : $order->paymentMode->mode }}</span>,
                <span>{{ trans('site.front.form.payment-plan') }}: {{ $order->paymentPlan->plan }}</span>)--}}
            </td>
            <td>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<div class="col-sm-5 ml-auto">
    <table class="table">
        <tbody>
        <tr>
            <td>
                <b>{{ trans('site.front.price') }}</b>
            </td>
            <td>
                {{ $order->price_formatted }}
            </td>
        </tr>
        @if($order->discount > 0)
            <tr>
                <td>
                    <b>{{ trans('site.front.discount') }}</b>
                </td>
                <td>
                    {{ $order->discount_formatted }}
                </td>
            </tr>
        @endif

        @if($order->plan_id != 8)
            <tr>
                <td>
                    <b>{{ trans('site.front.per-month') }}</b>
                </td>
                <td>
                    {{ $order->monthly_price_formatted }}
                </td>
            </tr><!-- check if full payment-->
        @endif

        @if($order->coachingTime && $order->coachingTime->additional_price)
            <tr>
                <td>
                    <b>{{ trans('site.add-on-price') }}</b>
                </td>
                <td>
                    {{ $order->coachingTime->additional_price_formatted }}
                </td>
            </tr>
        @endif

        <tr>
            <td>
                <b>{{ trans('site.front.total') }}</b>
            </td>
            <td>
                {{ $order->total_formatted }}
            </td>
        </tr>
        </tbody>
    </table>
</div>

</body>
</html>