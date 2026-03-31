@extends('emails.layout')

@section('content')
{{-- Hero --}}
<tr><td style="background:linear-gradient(135deg,#1c1917,#2a2520);padding:40px;text-align:center;">
    <div style="width:56px;height:56px;border-radius:50%;background:rgba(46,125,50,0.15);margin:0 auto 16px;line-height:56px;">
        <span style="font-size:28px;color:#2e7d32;">&#10003;</span>
    </div>
    <h1 style="font-family:Georgia,serif;font-size:26px;font-weight:700;color:#fff;margin:0 0 8px;">Takk for bestillingen!</h1>
    <p style="font-size:15px;color:rgba(255,255,255,0.6);margin:0;">Ordrenummer {{ $orderNumber ?? '' }}</p>
</td></tr>

{{-- Body --}}
<tr><td style="background:#fff;padding:32px 40px;">
    <p style="font-size:16px;color:#1a1a1a;line-height:1.6;margin:0 0 20px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
        Hei {{ $firstName ?? '' }}!
    </p>
    <p style="font-size:15px;color:#5a5550;line-height:1.7;margin:0 0 24px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
        Vi har mottatt bestillingen din og gleder oss til å ha deg med. Her er detaljene:
    </p>

    {{-- Ordredetaljer --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#faf8f5;border-radius:10px;margin-bottom:24px;">
        <tr><td style="padding:20px 24px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="font-size:14px;color:#5a5550;padding:6px 0;font-family:-apple-system,sans-serif;">Kurs</td>
                    <td style="font-size:14px;color:#1a1a1a;font-weight:600;text-align:right;padding:6px 0;font-family:-apple-system,sans-serif;">{{ $courseName ?? '' }}</td>
                </tr>
                @if(isset($packageName))
                <tr>
                    <td style="font-size:14px;color:#5a5550;padding:6px 0;font-family:-apple-system,sans-serif;">Pakke</td>
                    <td style="font-size:14px;color:#1a1a1a;font-weight:600;text-align:right;padding:6px 0;font-family:-apple-system,sans-serif;">{{ $packageName }}</td>
                </tr>
                @endif
                @if(isset($courseStartDate))
                <tr>
                    <td style="font-size:14px;color:#5a5550;padding:6px 0;font-family:-apple-system,sans-serif;">Kursstart</td>
                    <td style="font-size:14px;color:#1a1a1a;font-weight:600;text-align:right;padding:6px 0;font-family:-apple-system,sans-serif;">{{ $courseStartDate }}</td>
                </tr>
                @endif
                @if(isset($totalAmount))
                <tr>
                    <td colspan="2" style="border-top:2px solid #1a1a1a;padding-top:10px;"></td>
                </tr>
                <tr>
                    <td style="font-size:16px;color:#1a1a1a;font-weight:700;padding:4px 0;font-family:-apple-system,sans-serif;">Totalt</td>
                    <td style="font-size:16px;color:#1a1a1a;font-weight:700;text-align:right;padding:4px 0;font-family:-apple-system,sans-serif;">{{ $totalAmount }}</td>
                </tr>
                @endif
            </table>
        </td></tr>
    </table>

    {{-- Hva skjer nå --}}
    <h2 style="font-family:Georgia,serif;font-size:18px;font-weight:700;color:#1a1a1a;margin:0 0 16px;">Hva skjer nå?</h2>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
        <tr>
            <td width="36" valign="top" style="padding-right:12px;">
                <div style="width:28px;height:28px;border-radius:50%;background:#f4e8ea;text-align:center;line-height:28px;font-size:12px;font-weight:700;color:#862736;">1</div>
            </td>
            <td>
                <p style="font-size:14px;font-weight:600;color:#1a1a1a;margin:0 0 2px;font-family:-apple-system,sans-serif;">Bli med i gruppepraten</p>
                <p style="font-size:13px;color:#8a8580;margin:0;font-family:-apple-system,sans-serif;">Du får tilgang i portalen der du møter medstudenter.</p>
            </td>
        </tr>
    </table>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
        <tr>
            <td width="36" valign="top" style="padding-right:12px;">
                <div style="width:28px;height:28px;border-radius:50%;background:#f4e8ea;text-align:center;line-height:28px;font-size:12px;font-weight:700;color:#862736;">2</div>
            </td>
            <td>
                <p style="font-size:14px;font-weight:600;color:#1a1a1a;margin:0 0 2px;font-family:-apple-system,sans-serif;">Kurset åpner snart</p>
                <p style="font-size:13px;color:#8a8580;margin:0;font-family:-apple-system,sans-serif;">Vi sender deg innloggingsdetaljer før kursstart.</p>
            </td>
        </tr>
    </table>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
        <tr>
            <td width="36" valign="top" style="padding-right:12px;">
                <div style="width:28px;height:28px;border-radius:50%;background:#f4e8ea;text-align:center;line-height:28px;font-size:12px;font-weight:700;color:#862736;">3</div>
            </td>
            <td>
                <p style="font-size:14px;font-weight:600;color:#1a1a1a;margin:0 0 2px;font-family:-apple-system,sans-serif;">Mentormøter allerede nå</p>
                <p style="font-size:13px;color:#8a8580;margin:0;font-family:-apple-system,sans-serif;">Du har tilgang til mandagsmøter + hele arkivet.</p>
            </td>
        </tr>
    </table>

    {{-- CTA --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr><td style="text-align:center;padding:8px 0 16px;">
            <a href="{{ $portalUrl ?? config('app.url').'/account/dashboard' }}" style="display:inline-block;padding:14px 32px;background:#862736;color:#fff;border-radius:8px;text-decoration:none;font-weight:700;font-size:15px;font-family:-apple-system,sans-serif;">Gå til portalen &rarr;</a>
        </td></tr>
    </table>
</td></tr>
@endsection
