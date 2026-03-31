@extends('emails.layout')

@section('content')
<tr><td style="background:#fff;padding:8px 40px 32px;">
    <div style="text-align:center;margin-bottom:24px;">
        <p style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:1px;color:#862736;margin:0 0 8px;font-family:-apple-system,sans-serif;">Faktura</p>
        <h1 style="font-family:Georgia,serif;font-size:24px;font-weight:700;color:#1a1a1a;margin:0 0 4px;">Rate {{ $rateNumber ?? '' }} av {{ $totalRates ?? '' }}</h1>
        <p style="font-size:14px;color:#8a8580;margin:0;font-family:-apple-system,sans-serif;">Forfall: {{ $dueDate ?? '' }}</p>
    </div>

    {{-- Beløp --}}
    <div style="text-align:center;background:#faf8f5;border-radius:10px;padding:24px;margin-bottom:24px;">
        <p style="font-size:13px;color:#8a8580;margin:0 0 4px;font-family:-apple-system,sans-serif;">Beløp</p>
        <p style="font-family:Georgia,serif;font-size:36px;font-weight:700;color:#1a1a1a;margin:0;">{{ $amount ?? '' }}</p>
        <p style="font-size:12px;color:#8a8580;margin:4px 0 0;font-family:-apple-system,sans-serif;">Rentefritt &middot; Ingen gebyrer</p>
    </div>

    <p style="font-size:15px;color:#5a5550;line-height:1.7;margin:0 0 20px;font-family:-apple-system,sans-serif;">
        Hei {{ $firstName ?? '' }}! Her er faktura for rate {{ $rateNumber ?? '' }} av {{ $courseName ?? '' }}. Du kan betale direkte i portalen med Vipps, kort eller bankoverføring.
    </p>

    {{-- Detaljer --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#faf8f5;border-radius:10px;margin-bottom:24px;">
        <tr><td style="padding:16px 24px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                @if(isset($orderNumber))
                <tr>
                    <td style="font-size:13px;color:#8a8580;padding:4px 0;font-family:-apple-system,sans-serif;">Ordrenummer</td>
                    <td style="font-size:13px;color:#1a1a1a;text-align:right;padding:4px 0;font-family:-apple-system,sans-serif;">{{ $orderNumber }}</td>
                </tr>
                @endif
                <tr>
                    <td style="font-size:13px;color:#8a8580;padding:4px 0;font-family:-apple-system,sans-serif;">Kurs</td>
                    <td style="font-size:13px;color:#1a1a1a;text-align:right;padding:4px 0;font-family:-apple-system,sans-serif;">{{ $courseName ?? '' }}</td>
                </tr>
                @if(isset($remaining))
                <tr>
                    <td style="font-size:13px;color:#8a8580;padding:4px 0;font-family:-apple-system,sans-serif;">Gjenstående</td>
                    <td style="font-size:13px;color:#1a1a1a;text-align:right;padding:4px 0;font-family:-apple-system,sans-serif;">{{ $remaining }}</td>
                </tr>
                @endif
            </table>
        </td></tr>
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr><td style="text-align:center;padding:8px 0;">
            <a href="{{ $payUrl ?? config('app.url').'/account/invoices' }}" style="display:inline-block;padding:14px 32px;background:#862736;color:#fff;border-radius:8px;text-decoration:none;font-weight:700;font-size:15px;font-family:-apple-system,sans-serif;">Betal nå &rarr;</a>
        </td></tr>
    </table>
</td></tr>
@endsection
