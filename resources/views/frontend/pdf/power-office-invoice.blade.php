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
        .w-50{
            vertical-align: top;
        }

        .float-left{
            float: right;
        }

        .float-right{
            float: right;
        }

        .receipt-logo-container{
            background: #ffffff!important;
            text-align: right;
            margin-left: 120px;
            position: absolute;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="row" style="height: 120px">
        <div class="col-sm-12">
            <div class="w-50 float-right">
                <div class="receipt-logo-container">
                    <img src="https://www.easywrite.se/images/EasyWrite Logo.png" alt="Logo" style="width: 200px;">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="w-50 float-left">
            <span>
                {{ $customer['Name']  }}
            </span> <br>
    
            @if ($userAddress)
                <span>
                    {{ $userAddress->street }}
                </span> <br>
                <span>
                    {{ $userAddress->zip }} {{ $userAddress->city }}, Norge
                </span>
            @endif
        </div>
        <div class="w-50 float-right" style="margin-left: 400px">
            <p>
                Easywrite AS<br>
                Lihagen 21, <br>
                3029 Drammen, Norge
            </p>
            <b>
                Faktura
            </b>
            <p>
                Fakturadato: {{ $poInvoice ? FrontendHelpers::formatDate($poInvoice['CreatedDateTimeOffset']) 
                : FrontendHelpers::formatDate($order['CreatedDateTimeOffset']) }} <br>

                Kundenr: {{ $poInvoice ? $poInvoice['CustomerNo'] : $order['CustomerNo'] }}
            </p>

            @if ($poInvoice)
                <b>
                    Betallingsinformasjon
                </b>
                <p>
                    Forfallsdato: {{ FrontendHelpers::formatDate($poInvoice['DueDate']) }} <br>
                    Kontonummer: 2480 34 61208 <br>
                    KID: {{ $poInvoice['Cid'] }}
                </p>

                <b>
                    For betaling fra utlandet:
                </b>
                <p>
                    Iban nummer: NO5824803461208 <br>
                    Bic: SPTRNO22
                </p>
            @endif
        </div>

        @php
            $topSpace = $poInvoice ? '400px' : '300px';
        @endphp
        <table class="table table-striped" style="margin-top: {{ $topSpace }}">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>MVA</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @if ($poInvoice)
                    @foreach ($poInvoiceLines as $invoiceLine)
                        <tr>
                            <td>
                                {{ $invoiceLine['Description'] }}
                            </td>
                            <td>
                                {{ $invoiceLine['Quantity'] }}
                            </td>
                            <td>
                                {{ $invoiceLine['ProductUnitPrice'] }}
                            </td>
                            <td>
                                {{ $invoiceLine['VatRate'] * 100 }} %
                            </td>
                            <td class="text-right">
                                {{ number_format($invoiceLine['NetAmount'], 2) }}
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="4" class="text-right">
                            Sum eks. mva
                        </td>
                        <td class="text-right">
                            {{ number_format($poInvoice['NetAmount'], 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right">
                            {{ $poInvoiceLines[0]['VatRate'] * 100 }}% mva av {{ number_format($poInvoice['NetAmount'], 2) }}
                        </td>
                        <td class="text-right">
                            {{ number_format($poInvoice['TotalAmount'] - $poInvoice['NetAmount'], 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right">
                            Beløp å betale <b>{{ $poInvoice['CurrencyCode'] }}</b>
                        </td>
                        <td class="text-right">
                            {{ number_format($poInvoice['TotalAmount'], 2) }}
                        </td>
                    </tr>
                @else
                    @foreach ($order['SalesOrderLines'] as $orderLine)
                        <tr>
                            <td>
                                {{ $orderLine['Description'] }}
                            </td>
                            <td>
                                {{ $orderLine['Quantity'] }}
                            </td>
                            <td>
                                {{ $orderLine['ProductUnitPrice'] }}
                            </td>
                            <td>
                                {{ $orderLine['VatRate'] * 100 }} %
                            </td>
                            <td class="text-right">
                                {{ number_format($orderLine['NetAmount'], 2) }}
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="4" class="text-right">
                            Sum eks. mva
                        </td>
                        <td class="text-right">
                            {{ number_format($order['NetAmount'], 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right">
                            {{ $order['SalesOrderLines'][0]['VatRate'] * 100 }}% mva av {{ number_format($order['NetAmount'], 2) }}
                        </td>
                        <td class="text-right">
                            {{ number_format($order['TotalAmount'] - $order['NetAmount'], 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right">
                            Beløp å betale <b>{{ $order['CurrencyCode'] }}</b>
                        </td>
                        <td class="text-right">
                            {{ number_format($order['TotalAmount'], 2) }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</body>
</html>