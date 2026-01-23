<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Royalty Statement</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .meta { margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f5f5f5; }
        .totals td { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Royalty Statement</h1>
    <div class="meta">
        <div><strong>Author:</strong> {{ $author->full_name ?? $author->first_name.' '.$author->last_name }}</div>
        <div><strong>Email:</strong> {{ $author->email }}</div>
        <div><strong>Period:</strong> {{ $year }} Q{{ $quarter }}</div>
        <div><strong>Status:</strong> {{ $payout->paid_at ? 'Paid' : 'Unpaid' }}</div>
        @if ($payout->paid_at)
            <div><strong>Paid at:</strong> {{ $payout->paid_at->format('Y-m-d') }}</div>
        @endif
        @if ($payout->note)
            <div><strong>Reference:</strong> {{ $payout->note }}</div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Project</th>
                <th>Registration ID</th>
                <th>Sales</th>
                <th>Costs (Base)</th>
                <th>Costs (Multiplied)</th>
                <th>Net</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($registrations as $registration)
                <tr>
                    <td>{{ $registration['book_name'] ?: $registration['project_name'] }}</td>
                    <td>{{ $registration['project_registration_id'] }}</td>
                    <td>{{ FrontendHelpers::currencyFormat($registration['sales']) }}</td>
                    <td>{{ FrontendHelpers::currencyFormat($registration['cost_base']) }}</td>
                    <td>{{ FrontendHelpers::currencyFormat($registration['cost_multiplied']) }}</td>
                    <td>{{ FrontendHelpers::currencyFormat($registration['net']) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No activity for this period.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="totals">
                <td colspan="2">Totals</td>
                <td>{{ FrontendHelpers::currencyFormat($totals['sales']) }}</td>
                <td>{{ FrontendHelpers::currencyFormat($totals['cost_base']) }}</td>
                <td>{{ FrontendHelpers::currencyFormat($totals['cost_multiplied']) }}</td>
                <td>{{ FrontendHelpers::currencyFormat($totals['net']) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
