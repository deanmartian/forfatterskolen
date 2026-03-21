@extends('emails.layout')

@section('content')
<tr><td style="background:#fff;padding:8px 40px 32px;">
    <div style="text-align:center;margin-bottom:24px;">
        <div style="width:56px;height:56px;border-radius:50%;background:#fef3cd;margin:0 auto 16px;line-height:56px;font-size:28px;">&#128197;</div>
        <p style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:1px;color:#e65100;margin:0 0 12px;font-family:-apple-system,sans-serif;">P&Aring;MINNELSE</p>
        <h1 style="font-family:Georgia,serif;font-size:24px;font-weight:700;color:#1a1a1a;margin:0 0 4px;">Fakturaen forfaller om 14 dager</h1>
        <p style="font-size:14px;color:#8a8580;margin:0;font-family:-apple-system,sans-serif;">Forfall: {{ $dueDate ?? '' }}</p>
    </div>

    {{-- Beløp --}}
    <div style="text-align:center;background:#faf8f5;border-radius:10px;padding:24px;margin-bottom:24px;">
        <p style="font-size:13px;color:#8a8580;margin:0 0 4px;font-family:-apple-system,sans-serif;">Utst&aring;ende bel&oslash;p</p>
        <p style="font-family:Georgia,serif;font-size:36px;font-weight:700;color:#1a1a1a;margin:0;">{{ $amount ?? '' }}</p>
    </div>

    <p style="font-size:15px;color:#5a5550;line-height:1.7;margin:0 0 20px;font-family:-apple-system,sans-serif;">
        Hei {{ $firstName ?? '' }}! Dette er en vennlig p&aring;minnelse om at du har en faktura som forfaller om 14 dager. Du kan betale enkelt i portalen med Vipps, kort eller bankoverf&oslash;ring.
    </p>

    {{-- Detaljer --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#faf8f5;border-radius:10px;margin-bottom:24px;">
        <tr><td style="padding:16px 24px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                @if(!empty($kidNumber))
                <tr>
                    <td style="font-size:13px;color:#8a8580;padding:4px 0;font-family:-apple-system,sans-serif;">KID-nummer</td>
                    <td style="font-size:13px;color:#1a1a1a;text-align:right;padding:4px 0;font-family:-apple-system,sans-serif;">{{ $kidNumber }}</td>
                </tr>
                @endif
                <tr>
                    <td style="font-size:13px;color:#8a8580;padding:4px 0;font-family:-apple-system,sans-serif;">Forfallsdato</td>
                    <td style="font-size:13px;color:#1a1a1a;text-align:right;padding:4px 0;font-family:-apple-system,sans-serif;">{{ $dueDate ?? '' }}</td>
                </tr>
            </table>
        </td></tr>
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr><td style="text-align:center;padding:8px 0;">
            <a href="{{ $payUrl ?? config('app.url').'/learner/invoices' }}" style="display:inline-block;padding:14px 32px;background:#862736;color:#fff;border-radius:8px;text-decoration:none;font-weight:700;font-size:15px;font-family:-apple-system,sans-serif;">Se faktura &rarr;</a>
        </td></tr>
    </table>

    <p style="font-size:13px;color:#8a8580;line-height:1.6;margin:20px 0 0;text-align:center;font-family:-apple-system,sans-serif;">
        Har du allerede betalt? Se bort fra denne p&aring;minnelsen.
    </p>
</td></tr>
@endsection
