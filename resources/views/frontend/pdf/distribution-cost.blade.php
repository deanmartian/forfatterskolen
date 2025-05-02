<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Distribution Cost Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            text-align: center;
        }

        h2 {
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f4f4f4;
        }

        .footer {
            margin-top: 20px;
            font-size: 10px;
            text-align: right;
        }
    </style>
</head>
<body>
    <h2>{{ trans('site.distribution-cost-report') }}</h2>
    <p><strong>{{ trans('site.date-generated') }}:</strong>
        {{ ucfirst(FrontendHelpers::convertMonthLanguage(\Carbon\Carbon::now()->format('m'))) }}
        {{ \Carbon\Carbon::now()->format('j, Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>{{ trans('site.year') }}</th>
                @if (!isset($selectedQuarters) || isset($selectedQuarters) && in_array(1, $selectedQuarters))
                    <th>{{ trans('site.q1-cost') }}</th>
                @endif

                @if (!isset($selectedQuarters) || isset($selectedQuarters) && in_array(2, $selectedQuarters))
                    <th>{{ trans('site.q2-cost') }}</th>
                @endif

                @if (!isset($selectedQuarters) || isset($selectedQuarters) && in_array(3, $selectedQuarters))
                    <th>{{ trans('site.q3-cost') }}</th>
                @endif

                @if (!isset($selectedQuarters) || isset($selectedQuarters) && in_array(4, $selectedQuarters))
                    <th>{{ trans('site.q4-cost') }}</th>
                @endif
                
                <th>{{ trans('site.author-portal-menu.sales') }}</th>
                <th>{{ trans('site.total-storage-cost') }}</th>
                <th>{{ trans('site.payout') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $storageCost)
            <tr>
                <td>{{ $storageCost['year'] }}</td>
                @if (!isset($selectedQuarters) || isset($selectedQuarters) && in_array(1, $selectedQuarters))
                    <td>
                        <b>Sales:</b> {{ FrontendHelpers::currencyFormat($storageCost['q1_sales']) }} <br>
                        <b>Storage Cost:</b><br> {{ FrontendHelpers::currencyFormat($storageCost['q1_distributions']) }} <br>
                        <b>Payout:</b> {{ FrontendHelpers::currencyFormat(
                            ($storageCost['q1_sales'] - $storageCost['q1_distributions'])
                            ) }}
                    </td>
                @endif

                @if (!isset($selectedQuarters) || isset($selectedQuarters) && in_array(2, $selectedQuarters))
                    <td>
                        <b>Sales:</b> {{ FrontendHelpers::currencyFormat($storageCost['q2_sales']) }} <br>
                        <b>Storage Cost:</b><br> {{ FrontendHelpers::currencyFormat($storageCost['q2_distributions']) }} <br>
                        <b>Payout:</b> {{ FrontendHelpers::currencyFormat(
                            ($storageCost['q2_sales'] - $storageCost['q2_distributions'])
                            ) }}
                    </td>
                @endif

                @if (!isset($selectedQuarters) || isset($selectedQuarters) && in_array(3, $selectedQuarters))
                    <td>
                        <b>Sales:</b> {{ FrontendHelpers::currencyFormat($storageCost['q3_sales']) }} <br>
                        <b>Storage Cost:</b><br> {{ FrontendHelpers::currencyFormat($storageCost['q3_distributions']) }} <br>
                        <b>Payout:</b> {{ FrontendHelpers::currencyFormat(
                            ($storageCost['q3_sales'] - $storageCost['q3_distributions'])
                            ) }}
                    </td>
                @endif

                @if (!isset($selectedQuarters) || isset($selectedQuarters) && in_array(4, $selectedQuarters))
                    <td>
                        <b>Sales:</b> {{ FrontendHelpers::currencyFormat($storageCost['q4_sales']) }} <br>
                        <b>Storage Cost:</b><br> {{ FrontendHelpers::currencyFormat($storageCost['q4_distributions']) }} <br>
                        <b>Payout:</b> {{ FrontendHelpers::currencyFormat(
                            ($storageCost['q4_sales'] - $storageCost['q4_distributions'])
                            ) }}
                    </td>
                @endif
                <td>
                    {{ FrontendHelpers::currencyFormat($storageCost['total_sales']) }}
                </td>
                <td>
                    {{ FrontendHelpers::currencyFormat($storageCost['total_distributions']) }}
                </td>
                <td>
                    {{ FrontendHelpers::currencyFormat($storageCost['payout']) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>{{ trans('site.generated-by') }} | {{ \Carbon\Carbon::now()->toDateTimeString() }}</p>
    </div>
</body>
</html>
