@extends('emails.layout')

@section('content')
<tr><td style="background:#fff;padding:8px 40px 32px;text-align:center;">
    <p style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:1px;color:#862736;margin:0 0 12px;font-family:-apple-system,sans-serif;">Gratiswebinar</p>
    <h1 style="font-family:Georgia,serif;font-size:22px;font-weight:700;color:#1a1a1a;margin:0 0 8px;">Du er påmeldt!</h1>
    <p style="font-size:15px;color:#5a5550;margin:0 0 24px;font-family:-apple-system,sans-serif;">{{ $webinarTitle ?? '' }}</p>

    {{-- Dato/tid-kort --}}
    <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto 24px;">
        <tr>
            <td style="background:#faf8f5;border-radius:8px;padding:12px 20px;text-align:center;">
                <div style="font-size:24px;font-weight:700;color:#1a1a1a;font-family:-apple-system,sans-serif;">{{ $webinarDay ?? '' }}</div>
                <div style="font-size:11px;color:#8a8580;text-transform:uppercase;font-family:-apple-system,sans-serif;">{{ $webinarMonth ?? '' }}</div>
            </td>
            <td width="12"></td>
            <td style="background:#faf8f5;border-radius:8px;padding:12px 20px;text-align:center;">
                <div style="font-size:24px;font-weight:700;color:#1a1a1a;font-family:-apple-system,sans-serif;">{{ $webinarTime ?? '' }}</div>
                <div style="font-size:11px;color:#8a8580;text-transform:uppercase;font-family:-apple-system,sans-serif;">{{ $webinarDayName ?? '' }}</div>
            </td>
        </tr>
    </table>

    @if(isset($webinarDescription))
    <p style="font-size:14px;color:#5a5550;line-height:1.6;margin:0 0 24px;text-align:left;font-family:-apple-system,sans-serif;">
        {{ $webinarDescription }}
    </p>
    @endif

    <a href="{{ $calendarUrl ?? '#' }}" style="display:inline-block;padding:14px 32px;background:#862736;color:#fff;border-radius:8px;text-decoration:none;font-weight:700;font-size:15px;font-family:-apple-system,sans-serif;">Legg til i kalender &rarr;</a>
</td></tr>
@endsection
