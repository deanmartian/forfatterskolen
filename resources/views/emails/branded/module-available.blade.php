@extends('emails.layout')

@section('content')
{{-- Hero med modulnummer --}}
<tr><td style="background:linear-gradient(135deg,#1c1917,#2a2520);padding:40px;text-align:center;">
    <p style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:1px;color:#ffd54f;margin:0 0 12px;font-family:-apple-system,sans-serif;">MODUL {{ $lessonOrder ?? '' }}</p>
    <h1 style="font-family:Georgia,serif;font-size:24px;font-weight:700;color:#fff;margin:0 0 8px;">{{ $lessonTitle ?? 'Ny modul er klar!' }}</h1>
    <p style="font-size:15px;color:rgba(255,255,255,0.6);margin:0;font-family:-apple-system,sans-serif;">{{ $courseName ?? '' }}</p>
</td></tr>

<tr><td style="background:#fff;padding:32px 40px;">
    <p style="font-size:15px;color:#5a5550;line-height:1.7;margin:0 0 20px;font-family:-apple-system,sans-serif;">
        Hei {{ $firstName ?? '' }}! Modul {{ $lessonOrder ?? '' }} i {{ $courseName ?? 'kurset' }} er nå tilgjengelig. Logg inn for å se nytt materiale og komme i gang.
    </p>

    @if(!empty($lessonDescription))
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#faf8f5;border-radius:10px;margin-bottom:24px;">
        <tr><td style="padding:16px 24px;">
            <p style="font-size:13px;font-weight:600;color:#1a1a1a;margin:0 0 4px;font-family:-apple-system,sans-serif;">Om denne modulen</p>
            <p style="font-size:13px;color:#5a5550;line-height:1.6;margin:0;font-family:-apple-system,sans-serif;">{{ $lessonDescription }}</p>
        </td></tr>
    </table>
    @endif

    @if(!empty($hasAssignment))
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#faf8f5;border-radius:10px;margin-bottom:24px;">
        <tr><td style="padding:16px 24px;">
            <p style="font-size:13px;color:#5a5550;margin:0;font-family:-apple-system,sans-serif;">
                &#128221; <strong style="color:#1a1a1a;">Oppgave tilgjengelig</strong> — husk å levere innen fristen!
            </p>
        </td></tr>
    </table>
    @endif

    {{-- Progresjonsbar --}}
    @if(isset($progressPercent))
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
        <tr><td>
            <p style="font-size:12px;color:#8a8580;margin:0 0 6px;font-family:-apple-system,sans-serif;">Din progresjon: {{ $progressPercent }}%</p>
            <div style="background:#f0eeeb;border-radius:4px;height:8px;width:100%;">
                <div style="background:#862736;border-radius:4px;height:8px;width:{{ $progressPercent }}%;"></div>
            </div>
        </td></tr>
    </table>
    @endif

    @include('emails.partials.custom-message')

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr><td style="text-align:center;">
            <a href="{{ $portalUrl ?? config('app.url') . '/learner/dashboard' }}" style="display:inline-block;padding:14px 32px;background:#862736;color:#fff;border-radius:8px;text-decoration:none;font-weight:700;font-size:15px;font-family:-apple-system,sans-serif;">Se modulen &rarr;</a>
        </td></tr>
    </table>
</td></tr>
@endsection
