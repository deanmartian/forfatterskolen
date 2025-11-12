<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('site.order-history.invoice-number') }}</title>
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/frontend/main.css') }}"/>
    <style>
        body {
            font-size: 14px;
            margin: 0;
            padding: 0;
            background: #fff;
        }

        .receipt-wrapper {
            padding: 40px;
        }

        .receipt-document {
            max-width: 820px;
            margin: 0 auto;
        }

        .receipt-table {
            width: 100%;
            border-collapse: collapse;
        }

        .receipt-header-table td,
        .receipt-customer-table td,
        .receipt-info-table td {
            vertical-align: top;
        }

        .receipt-return-label {
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .receipt-return-address div,
        .receipt-customer-address div {
            line-height: 1.5;
        }

        .receipt-branding {
            text-align: right;
        }

        .receipt-branding img {
            max-width: 260px;
            width: 100%;
        }

        .receipt-summary {
            background: #f2f5f5;
            padding: 18px 20px;
            border-radius: 6px;
        }

        .receipt-summary table {
            width: 100%;
            border-collapse: collapse;
        }

        .receipt-summary td {
            padding: 4px 0;
            font-size: 13px;
        }

        .receipt-summary .label {
            text-transform: uppercase;
            font-weight: 700;
        }

        .receipt-summary .value {
            text-align: right;
        }

        .receipt-summary .emphasis .value {
            font-size: 15px;
        }

        .receipt-info-table {
            margin-top: 28px;
        }

        .receipt-info {
            width: 100%;
            border-collapse: collapse;
        }

        .receipt-info td {
            padding: 4px 0;
            font-size: 13px;
        }

        .receipt-info .label {
            text-transform: uppercase;
            font-weight: 600;
            color: #5f6a72;
        }

        .receipt-info .value {
            text-align: right;
            font-size: 14px;
        }

        .receipt-section-title {
            font-size: 18px;
            font-weight: 700;
            margin: 36px 0 14px;
        }

        .receipt-items {
            width: 100%;
            border-collapse: collapse;
        }

        .receipt-items th {
            text-transform: uppercase;
            color: #6b7378;
            border-bottom: 2px solid #e2e6e8;
            padding: 10px 0;
            text-align: left;
            font-size: 12px;
        }

        .receipt-items td {
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
            font-size: 14px;
        }

        .receipt-items tbody tr:last-child td {
            border-bottom: none;
        }

        .receipt-totals {
            width: 100%;
            max-width: 320px;
            margin-left: auto;
            margin-top: 24px;
            border-collapse: collapse;
        }

        .receipt-totals td {
            padding: 4px 0;
            font-size: 14px;
        }

        .receipt-totals .label {
            font-weight: 600;
        }

        .receipt-totals .value {
            text-align: right;
            font-weight: 600;
        }

        .receipt-totals .emphasis .value {
            font-size: 16px;
            font-weight: 700;
        }

        .receipt-footer {
            margin-top: 44px;
            padding-top: 20px;
            border-top: 1px solid #d7dcdf;
            font-size: 13px;
        }

        .receipt-footer div {
            line-height: 1.5;
        }
    </style>
</head>
<body>
@php
    use Carbon\Carbon;
    use App\Http\FrontendHelpers;

    $invoiceNumber = $invoice->invoice_number ?? $invoice->id;
    $invoiceNumberFormatted = $invoiceNumber ? str_pad($invoiceNumber, 6, '0', STR_PAD_LEFT) : '';
    $issueDate = $invoice->fiken_issueDate ? Carbon::parse($invoice->fiken_issueDate)->format('d.m.Y') : ($invoice->created_at ? Carbon::parse($invoice->created_at)->format('d.m.Y') : '');
    $dueDate = $invoice->fiken_dueDate ? Carbon::parse($invoice->fiken_dueDate)->format('d.m.Y') : '';
    $amount = $invoice->gross ? $invoice->gross / 100 : 0;
    $amountFormatted = FrontendHelpers::currencyFormat($amount);
    $transactionsSum = $invoice->transactions->sum('amount');
    $balanceRaw = $invoice->fiken_balance ?? 0;
    $balanceDue = $invoice->fiken_is_paid ? 0 : $balanceRaw - $transactionsSum;
    $balanceFormatted = FrontendHelpers::currencyFormat(max($balanceDue, 0));
    $vatCents = $invoice->vat ?? 0;
    $vatAmount = $vatCents ? $vatCents / 100 : 0;
    $vatFormatted = $vatCents ? FrontendHelpers::currencyFormat($vatAmount) : '';
    $netAmount = max($amount - $vatAmount, 0);
    $netFormatted = FrontendHelpers::currencyFormat($netAmount);
    $totalToPay = $amountFormatted;
    $paymentDate = $invoice->fiken_sale_payment_date
        ? Carbon::parse($invoice->fiken_sale_payment_date)->format('d.m.Y')
        : Carbon::parse($invoice->updated_at ?? now())->format('d.m.Y');
    $description = '';
    if ($invoice->package) {
        $description = $invoice->package->variation ?: optional($invoice->package->course)->title;
    }
    if (! $description) {
        $description = trans('site.order-history.invoice-number').': '.($invoiceNumberFormatted ?: $invoiceNumber);
    }
    $address = optional($user->address);
    $customerCityParts = array_filter([$address->zip, $address->city]);
    $customerCity = implode(' ', $customerCityParts);
    $accountNumber = '9015 18 00393';
    $kidNumber = $invoice->kid_number ?: '';
    $invoiceCompany = method_exists($invoice, 'company') ? optional($invoice->company) : optional(null);
    $customerName = $invoiceCompany->company_name ?? $user->full_name;
    $customerStreet = $invoiceCompany->street_address ?? $address->street;
    $customerCityDisplay = $invoiceCompany->company_name ? trim(implode(' ', array_filter([$invoiceCompany->post_number, $invoiceCompany->place]))) : $customerCity;
    $customerReference = $invoiceCompany->customer_number ?? $user->full_name;
    $companyNumber = $invoiceCompany->customer_number;
@endphp
<div class="receipt-wrapper">
    <div class="receipt-document">
        <table class="receipt-table receipt-header-table">
            <tr>
                <td>
                    <div class="receipt-return-label">Retur:</div>
                    <div class="receipt-return-address">
                        <div>{{ trans('site.order-history.fs-name') }}</div>
                        <div>{{ trans('site.order-history.fs-address1') }}</div>
                        <div>{{ trans('site.order-history.fs-address2') }}</div>
                        <div>{{ trans('site.order-history.fs-country') }}</div>
                    </div>
                </td>
                <td class="receipt-branding">
                    <img src="{{ url('/images-new/logo-tagline.png') }}" alt="Logo" style="width: 260px;">
                </td>
            </tr>
        </table>

        <table class="receipt-table receipt-customer-table" style="margin-top: 36px;">
            <tr>
                <td class="receipt-customer-address">
                    <div>{{ $customerName }}</div>
                    @if($customerStreet)
                        <div>{{ $customerStreet }}</div>
                    @endif
                    @if($customerCityDisplay)
                        <div>{{ $customerCityDisplay }}</div>
                    @endif
                    @if($companyNumber)
                        <div>{{ $companyNumber }}</div>
                    @endif
                </td>
                <td style="width: 360px;">
                    <div class="receipt-summary">
                        <table>
                            <tr>
                                <td class="label">{{ trans('site.order-history.due-date') }}</td>
                                <td class="value">{{ $dueDate }}</td>
                            </tr>
                            <tr>
                                <td class="label">{{ trans('site.learner.account-number') }}</td>
                                <td class="value">{{ $accountNumber }}</td>
                            </tr>
                            <tr>
                                <td class="label">KID</td>
                                <td class="value">{{ $kidNumber }}</td>
                            </tr>
                            <tr class="emphasis">
                                <td class="label">Å betale</td>
                                <td class="value">{{ $amountFormatted }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <table class="receipt-table receipt-info-table">
            <tr>
                <td style="width: 37%;"></td>
                <td style="width: 50%;">
                    <table class="receipt-info">
                        <tr>
                            <td class="labels text-uppercase">{{ trans('site.order-history.invoice-number') }}</td>
                            <td class="value">{{ $invoiceNumberFormatted }}</td>
                        </tr>
                        <tr>
                            <td class="labels text-uppercase">{{ trans('site.order-history.invoice-date') }}</td>
                            <td class="value">{{ $issueDate }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="receipt-section-title">Kvittering</div>

        <table class="receipt-items">
            <thead>
                <tr>
                    <th>{{ __('Betalings dato') }}</th>
                    <th>{{ __('Beløp') }}</th>
                    <th>{{ __('Saldo') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $paymentDate }}</td>
                    <td>{{ $amountFormatted }}</td>
                    <td>{{ $balanceFormatted }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
