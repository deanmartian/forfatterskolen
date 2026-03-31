@extends('emails.layout')

@section('content')
{{-- Hero: Uke-oversikt --}}
<tr><td style="background:#fff;padding:40px;text-align:center;border-bottom:1px solid rgba(0,0,0,0.06);">
    <p style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:1px;color:#862736;margin:0 0 12px;font-family:-apple-system,sans-serif;">UKE {{ $weekNumber }} &bull; {{ $weekRange }}</p>
    <h1 style="font-family:Georgia,serif;font-size:24px;font-weight:700;color:#1a1a1a;margin:0 0 8px;">God morgen, {{ $firstName }}!</h1>
    <p style="font-size:15px;color:#5a5550;margin:0;font-family:-apple-system,sans-serif;">Her er ukeoversikten din hos Forfatterskolen.</p>
</td></tr>

{{-- Mentorm&oslash;te --}}
@if(!empty($mentorMeeting))
<tr><td style="background:#fff;padding:0 40px;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#1c1917,#2a2520);border-radius:10px;margin-top:24px;">
        <tr><td style="padding:24px;">
            <p style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:1px;color:#ffd54f;margin:0 0 12px;font-family:-apple-system,sans-serif;">&#127908; MENTORM&Oslash;TE DENNE UKEN</p>
            <h2 style="font-family:Georgia,serif;font-size:20px;font-weight:700;color:#fff;margin:0 0 8px;">{{ $mentorMeeting['title'] }}</h2>
            <p style="font-size:14px;color:rgba(255,255,255,0.7);margin:0 0 12px;font-family:-apple-system,sans-serif;">{{ $mentorMeeting['date'] }}</p>
            @if(!empty($mentorMeeting['presenter']))
            <table role="presentation" cellpadding="0" cellspacing="0">
                <tr>
                    @if(!empty($mentorMeeting['presenter']['image']))
                    <td style="vertical-align:middle;padding-right:12px;">
                        <img src="{{ $mentorMeeting['presenter']['image'] }}" alt="" width="40" height="40" style="border-radius:50%;display:block;">
                    </td>
                    @endif
                    <td style="vertical-align:middle;">
                        <p style="font-size:14px;font-weight:600;color:#fff;margin:0;font-family:-apple-system,sans-serif;">{{ $mentorMeeting['presenter']['name'] }}</p>
                    </td>
                </tr>
            </table>
            @endif
            @if(!empty($mentorMeeting['description']))
            <p style="font-size:13px;color:rgba(255,255,255,0.6);line-height:1.6;margin:12px 0 0;font-family:-apple-system,sans-serif;">{{ \Illuminate\Support\Str::limit(strip_tags($mentorMeeting['description']), 150) }}</p>
            @endif
        </td></tr>
    </table>
</td></tr>
@endif

{{-- Kurswebinarer --}}
@if(!empty($webinars) && count($webinars) > 0)
<tr><td style="background:#fff;padding:24px 40px 0;">
    <p style="font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:1px;color:#862736;margin:0 0 16px;font-family:-apple-system,sans-serif;">&#128197; WEBINARER DENNE UKEN</p>
    @foreach($webinars as $webinar)
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#faf8f5;border-radius:10px;margin-bottom:10px;">
        <tr><td style="padding:14px 20px;">
            <p style="font-size:14px;font-weight:600;color:#1a1a1a;margin:0 0 2px;font-family:-apple-system,sans-serif;">{{ $webinar['title'] }}</p>
            <p style="font-size:12px;color:#8a8580;margin:0;font-family:-apple-system,sans-serif;">{{ $webinar['date'] }} &bull; {{ $webinar['courseName'] }}</p>
        </td></tr>
    </table>
    @endforeach
</td></tr>
@endif

{{-- Nye moduler --}}
@if(!empty($upcomingModules) && count($upcomingModules) > 0)
<tr><td style="background:#fff;padding:24px 40px 0;">
    <p style="font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:1px;color:#862736;margin:0 0 16px;font-family:-apple-system,sans-serif;">&#128214; NYE MODULER DENNE UKEN</p>
    @foreach($upcomingModules as $module)
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#faf8f5;border-radius:10px;margin-bottom:10px;">
        <tr><td style="padding:14px 20px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="width:36px;vertical-align:top;">
                        <div style="width:28px;height:28px;border-radius:50%;background:#862736;color:#fff;text-align:center;line-height:28px;font-size:13px;font-weight:700;font-family:-apple-system,sans-serif;">{{ $module['order'] }}</div>
                    </td>
                    <td style="vertical-align:top;">
                        <p style="font-size:14px;font-weight:600;color:#1a1a1a;margin:0 0 2px;font-family:-apple-system,sans-serif;">{{ $module['title'] }}</p>
                        <p style="font-size:12px;color:#8a8580;margin:0;font-family:-apple-system,sans-serif;">{{ $module['courseName'] }} &bull; Tilgjengelig {{ $module['availableDate'] }}</p>
                    </td>
                </tr>
            </table>
        </td></tr>
    </table>
    @endforeach
</td></tr>
@endif

{{-- Oppgavefrister --}}
@if(!empty($assignmentDeadlines) && count($assignmentDeadlines) > 0)
<tr><td style="background:#fff;padding:24px 40px 0;">
    <p style="font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:1px;color:#862736;margin:0 0 16px;font-family:-apple-system,sans-serif;">&#128221; OPPGAVEFRISTER DENNE UKEN</p>
    @foreach($assignmentDeadlines as $assignment)
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-left:3px solid #862736;margin-bottom:10px;">
        <tr><td style="padding:10px 16px;background:#faf8f5;border-radius:0 10px 10px 0;">
            <p style="font-size:14px;font-weight:600;color:#1a1a1a;margin:0 0 2px;font-family:-apple-system,sans-serif;">{{ $assignment['title'] }}</p>
            <p style="font-size:12px;color:#862736;font-weight:600;margin:0;font-family:-apple-system,sans-serif;">Frist: {{ $assignment['deadline'] }} &bull; {{ $assignment['courseName'] }}</p>
        </td></tr>
    </table>
    @endforeach
</td></tr>
@endif

{{-- Motivasjonssitat --}}
@if(!empty($quote))
<tr><td style="background:#fff;padding:24px 40px 0;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#fef9f0;border-radius:10px;">
        <tr><td style="padding:20px 24px;text-align:center;">
            <p style="font-family:Georgia,serif;font-size:16px;font-style:italic;color:#5a5550;line-height:1.6;margin:0 0 8px;">&laquo;{{ $quote['text'] }}&raquo;</p>
            <p style="font-size:12px;color:#8a8580;margin:0;font-family:-apple-system,sans-serif;">&mdash; {{ $quote['author'] }}</p>
        </td></tr>
    </table>
</td></tr>
@endif

{{-- CTA + Kristine-signatur --}}
<tr><td style="background:#fff;padding:32px 40px;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
        <tr><td style="text-align:center;">
            <a href="{{ $portalUrl ?? config('app.url') . '/account/dashboard' }}" style="display:inline-block;padding:14px 32px;background:#862736;color:#fff;border-radius:8px;text-decoration:none;font-weight:700;font-size:15px;font-family:-apple-system,sans-serif;">G&aring; til portalen &rarr;</a>
        </td></tr>
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid rgba(0,0,0,0.06);padding-top:24px;">
        <tr>
            <td style="vertical-align:top;width:56px;padding-right:16px;">
                <div style="width:48px;height:48px;border-radius:50%;background:#fef3cd;text-align:center;line-height:48px;font-size:24px;">&#128155;</div>
            </td>
            <td style="vertical-align:top;">
                <p style="font-size:15px;color:#1a1a1a;margin:0 0 4px;font-family:-apple-system,sans-serif;">God skriveuke!</p>
                <p style="font-size:13px;font-weight:600;color:#1a1a1a;margin:0;font-family:-apple-system,sans-serif;">Kristine S. Henningsen</p>
                <p style="font-size:12px;color:#8a8580;margin:2px 0 0;font-family:-apple-system,sans-serif;">Forfatterskolen</p>
            </td>
        </tr>
    </table>
</td></tr>
@endsection
