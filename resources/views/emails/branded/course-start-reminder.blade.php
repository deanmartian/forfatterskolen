@extends('emails.layout')

@section('content')
{{-- Mørk hero --}}
<tr><td style="background:linear-gradient(135deg,#1c1917,#2a2520);padding:40px;text-align:center;">
    <p style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:1px;color:#ffd54f;margin:0 0 12px;font-family:-apple-system,sans-serif;">&#9889; I morgen!</p>
    <h1 style="font-family:Georgia,serif;font-size:24px;font-weight:700;color:#fff;margin:0 0 8px;">{{ $courseName ?? '' }} starter!</h1>
    <p style="font-size:15px;color:rgba(255,255,255,0.6);margin:0;font-family:-apple-system,sans-serif;">{{ $courseStartDate ?? '' }}</p>
</td></tr>

<tr><td style="background:#fff;padding:32px 40px;">
    <p style="font-size:15px;color:#5a5550;line-height:1.7;margin:0 0 20px;font-family:-apple-system,sans-serif;">
        Hei {{ $firstName ?? '' }}! I morgen åpner {{ $courseName ?? 'kurset' }}. Alt kursmaterialet er klart for deg, og du kan begynne med en gang. Her er hva du trenger å vite:
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
        <tr>
            <td width="36" valign="top" style="padding-right:12px;">
                <div style="width:28px;height:28px;border-radius:50%;background:#f4e8ea;text-align:center;line-height:28px;font-size:12px;font-weight:700;color:#862736;">1</div>
            </td>
            <td>
                <p style="font-size:14px;font-weight:600;color:#1a1a1a;margin:0 0 2px;font-family:-apple-system,sans-serif;">Logg inn i portalen</p>
                <p style="font-size:13px;color:#8a8580;margin:0;font-family:-apple-system,sans-serif;">Modul 1 og første oppgave er klar.</p>
            </td>
        </tr>
    </table>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
        <tr>
            <td width="36" valign="top" style="padding-right:12px;">
                <div style="width:28px;height:28px;border-radius:50%;background:#f4e8ea;text-align:center;line-height:28px;font-size:12px;font-weight:700;color:#862736;">2</div>
            </td>
            <td>
                <p style="font-size:14px;font-weight:600;color:#1a1a1a;margin:0 0 2px;font-family:-apple-system,sans-serif;">Mentormøte mandag kl. 20:00</p>
                <p style="font-size:13px;color:#8a8580;margin:0;font-family:-apple-system,sans-serif;">Bli med på ukens mentormøte — perfekt for å komme i gang!</p>
            </td>
        </tr>
    </table>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
        <tr>
            <td width="36" valign="top" style="padding-right:12px;">
                <div style="width:28px;height:28px;border-radius:50%;background:#f4e8ea;text-align:center;line-height:28px;font-size:12px;font-weight:700;color:#862736;">3</div>
            </td>
            <td>
                <p style="font-size:14px;font-weight:600;color:#1a1a1a;margin:0 0 2px;font-family:-apple-system,sans-serif;">Presenter deg i gruppepraten</p>
                <p style="font-size:13px;color:#8a8580;margin:0;font-family:-apple-system,sans-serif;">Si hei til de andre i gruppen og fortell hva du skriver på!</p>
            </td>
        </tr>
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr><td style="text-align:center;">
            <a href="{{ config('app.url') }}/learner/dashboard" style="display:inline-block;padding:14px 32px;background:#862736;color:#fff;border-radius:8px;text-decoration:none;font-weight:700;font-size:15px;font-family:-apple-system,sans-serif;">Start kurset &rarr;</a>
        </td></tr>
    </table>
</td></tr>
@endsection
