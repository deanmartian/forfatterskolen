<div class="row">
    <div class="col-md-7">
        <p>
            {{ $customer['Name']  }}
        </p>

        @if ($userAddress)
            <p>
                {{ $userAddress->street }}
            </p>
            <p>
                {{ $userAddress->zip }} {{ $userAddress->city }}, Norge
            </p>
        @endif
    </div>
    <div class="col-md-5">
        <p>Easywrite AS</p>
        <p>
            Lihagen 21,
        </p>
        <p>
            3029 Drammen, Norge
        </p>
        <b>
            Faktura
        </b>
        <div class="form-group" style="margin-bottom: 0">
            <div class="row">
                <div class="col-md-6">Fakturadato:</div>
                <div class="col-md-6">
                    {{ $poInvoice ? FrontendHelpers::formatDate($poInvoice['CreatedDateTimeOffset']) 
                        : FrontendHelpers::formatDate($order['CreatedDateTimeOffset'])  }}
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-6">Kundenr:</div>
                <div class="col-md-6">
                    {{ $poInvoice ? $poInvoice['CustomerNo'] : $order['CustomerNo'] }}
                </div>
            </div>
        </div>

        @if ($poInvoice)
            <b>
                Betallingsinformasjon
            </b>
            <div class="form-group" style="margin-bottom: 0">
                <div class="row">
                    <div class="col-md-6">Forfallsdato:</div>
                    <div class="col-md-6">
                        {{ FrontendHelpers::formatDate($poInvoice['DueDate']) }}
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 0">
                <div class="row">
                    <div class="col-md-6">Kontonummer:</div>
                    <div class="col-md-6">
                        2480 34 61208
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">KID:</div>
                    <div class="col-md-6">
                        {{$poInvoice['Cid'] }}
                    </div>
                </div>
            </div>

            <b>
                For betaling fra utlandet:
            </b>

            <div class="form-group" style="margin-bottom: 0">
                <div class="row">
                    <div class="col-md-6">Iban nummer:</div>
                    <div class="col-md-6">
                        NO5824803461208
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 0">
                <div class="row">
                    <div class="col-md-6">Bic:</div>
                    <div class="col-md-6">
                        SPTRNO22
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
<table class="table table-striped" style="margin-top: 30px">
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

<button class="btn btn-primary pull-right downloadInvoice" 
    data-action="{{ route('admin.power-office.download', $invoice->id) }}">
    <i class="fa fa-spinner fa-pulse" style="display: none"></i> Download
</button>

<div class="clearfix"></div>

