@extends('emails.layout')

@section('content')
<tr><td style="background:#fff;padding:8px 40px 32px;">
    <h1 style="font-family:Georgia,serif;font-size:24px;font-weight:700;color:#1a1a1a;margin:0 0 16px;text-align:center;">Velkommen til Forfatterskolen, {{ $firstName ?? '' }}!</h1>
    <p style="font-size:15px;color:#5a5550;line-height:1.7;margin:0 0 20px;font-family:-apple-system,sans-serif;">
        Så gøy at du er med! Du er nå en del av Norges største nettbaserte skrivefellesskap. Her er noen tips for å komme i gang:
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
        <tr>
            <td width="36" valign="top" style="padding-right:12px;">
                <div style="width:28px;height:28px;border-radius:50%;background:#e8f5e9;text-align:center;line-height:28px;font-size:14px;">&#9999;&#65039;</div>
            </td>
            <td>
                <p style="font-size:14px;font-weight:600;color:#1a1a1a;margin:0 0 2px;font-family:-apple-system,sans-serif;">Gratis tekstvurdering</p>
                <p style="font-size:13px;color:#8a8580;margin:0;font-family:-apple-system,sans-serif;">Send inn opptil 500 ord og få tilbakemelding fra redaktør. Helt gratis!</p>
            </td>
        </tr>
    </table>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
        <tr>
            <td width="36" valign="top" style="padding-right:12px;">
                <div style="width:28px;height:28px;border-radius:50%;background:#e8f5e9;text-align:center;line-height:28px;font-size:14px;">&#128218;</div>
            </td>
            <td>
                <p style="font-size:14px;font-weight:600;color:#1a1a1a;margin:0 0 2px;font-family:-apple-system,sans-serif;">Se våre kurs</p>
                <p style="font-size:13px;color:#8a8580;margin:0;font-family:-apple-system,sans-serif;">Roman, krim, barnebok og mer. Earlybird-priser tilgjengelig!</p>
            </td>
        </tr>
    </table>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
        <tr>
            <td width="36" valign="top" style="padding-right:12px;">
                <div style="width:28px;height:28px;border-radius:50%;background:#e8f5e9;text-align:center;line-height:28px;font-size:14px;">&#127908;&#65039;</div>
            </td>
            <td>
                <p style="font-size:14px;font-weight:600;color:#1a1a1a;margin:0 0 2px;font-family:-apple-system,sans-serif;">Mentormøter hver mandag</p>
                <p style="font-size:13px;color:#8a8580;margin:0;font-family:-apple-system,sans-serif;">Møt kjente forfattere live. Neste: mandag kl. 20:00.</p>
            </td>
        </tr>
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr><td style="text-align:center;">
            <a href="{{ config('app.url') }}/course" style="display:inline-block;padding:14px 32px;background:#862736;color:#fff;border-radius:8px;text-decoration:none;font-weight:700;font-size:15px;font-family:-apple-system,sans-serif;">Utforsk kurs &rarr;</a>
        </td></tr>
    </table>
</td></tr>
@endsection
