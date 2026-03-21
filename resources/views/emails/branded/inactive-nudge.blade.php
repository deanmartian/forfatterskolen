@extends('emails.layout')

@section('content')
{{-- Hero --}}
<tr><td style="background:#fff;padding:40px;text-align:center;border-bottom:1px solid rgba(0,0,0,0.06);">
    <div style="width:56px;height:56px;border-radius:50%;background:#fef3cd;margin:0 auto 16px;line-height:56px;font-size:28px;">&#128075;</div>
    <h1 style="font-family:Georgia,serif;font-size:24px;font-weight:700;color:#1a1a1a;margin:0 0 8px;">Vi savner deg, {{ $firstName }}!</h1>
    <p style="font-size:15px;color:#5a5550;margin:0;font-family:-apple-system,sans-serif;">Det er en stund siden sist du var innom portalen.</p>
</td></tr>

<tr><td style="background:#fff;padding:32px 40px;">
    <p style="font-size:15px;color:#5a5550;line-height:1.7;margin:0 0 20px;font-family:-apple-system,sans-serif;">
        Hei {{ $firstName }}! Vi ser at det er litt siden du logget inn. Kurset ditt venter p&aring; deg, og det er aldri for sent &aring; ta opp tr&aring;den igjen.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#faf8f5;border-radius:10px;margin-bottom:24px;">
        <tr><td style="padding:20px 24px;">
            <p style="font-size:13px;font-weight:600;color:#1a1a1a;margin:0 0 8px;font-family:-apple-system,sans-serif;">Tips for &aring; komme i gang igjen:</p>
            <p style="font-size:13px;color:#5a5550;line-height:1.6;margin:0;font-family:-apple-system,sans-serif;">
                &#10003; Sett av 15 minutter i dag til &aring; lese neste modul<br>
                &#10003; Skriv bare &eacute;n setning &mdash; resten f&oslash;lger<br>
                &#10003; Bli med p&aring; neste mentorm&oslash;tet for inspirasjon
            </p>
        </td></tr>
    </table>

    @if(!empty($quote))
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#fef9f0;border-radius:10px;margin-bottom:24px;">
        <tr><td style="padding:16px 24px;text-align:center;">
            <p style="font-family:Georgia,serif;font-size:15px;font-style:italic;color:#5a5550;line-height:1.6;margin:0 0 6px;">&laquo;{{ $quote['text'] }}&raquo;</p>
            <p style="font-size:12px;color:#8a8580;margin:0;font-family:-apple-system,sans-serif;">&mdash; {{ $quote['author'] }}</p>
        </td></tr>
    </table>
    @endif

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr><td style="text-align:center;">
            <a href="{{ $portalUrl ?? config('app.url') . '/learner/dashboard' }}" style="display:inline-block;padding:14px 32px;background:#862736;color:#fff;border-radius:8px;text-decoration:none;font-weight:700;font-size:15px;font-family:-apple-system,sans-serif;">Tilbake til portalen &rarr;</a>
        </td></tr>
    </table>
</td></tr>
@endsection
