@extends('emails.layout')

@section('content')
{{-- Hero: Ukebrev fra Kristine --}}
<tr><td style="background:#fff;padding:40px;text-align:center;border-bottom:1px solid rgba(0,0,0,0.06);">
    <p style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:1px;color:#862736;margin:0 0 12px;font-family:-apple-system,sans-serif;">UKE {{ $weekNumber ?? '' }} &bull; {{ $courseName ?? '' }}</p>
    <h1 style="font-family:Georgia,serif;font-size:24px;font-weight:700;color:#1a1a1a;margin:0 0 8px;">Hei {{ $firstName ?? '' }}!</h1>
    <p style="font-size:15px;color:#5a5550;margin:0;font-family:-apple-system,sans-serif;">Her er ukens oppdatering fra kurset ditt.</p>
</td></tr>

{{-- Nye moduler denne uken --}}
@if(!empty($weekModules) && count($weekModules) > 0)
<tr><td style="background:#fff;padding:24px 40px 0;">
    <p style="font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:1px;color:#862736;margin:0 0 16px;font-family:-apple-system,sans-serif;">&#128214; DENNE UKENS MODULER</p>
    @foreach($weekModules as $mod)
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#faf8f5;border-radius:10px;margin-bottom:10px;">
        <tr><td style="padding:14px 20px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="width:36px;vertical-align:top;">
                        <div style="width:28px;height:28px;border-radius:50%;background:#862736;color:#fff;text-align:center;line-height:28px;font-size:13px;font-weight:700;font-family:-apple-system,sans-serif;">{{ $mod['order'] }}</div>
                    </td>
                    <td style="vertical-align:top;">
                        <p style="font-size:14px;font-weight:600;color:#1a1a1a;margin:0 0 2px;font-family:-apple-system,sans-serif;">{{ $mod['title'] }}</p>
                        @if(!empty($mod['description']))
                        <p style="font-size:12px;color:#8a8580;margin:0;font-family:-apple-system,sans-serif;">{{ \Illuminate\Support\Str::limit($mod['description'], 100) }}</p>
                        @endif
                    </td>
                </tr>
            </table>
        </td></tr>
    </table>
    @endforeach
</td></tr>
@endif

{{-- Oppgavefrister denne uken --}}
@if(!empty($weekAssignments) && count($weekAssignments) > 0)
<tr><td style="background:#fff;padding:24px 40px 0;">
    <p style="font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:1px;color:#862736;margin:0 0 16px;font-family:-apple-system,sans-serif;">&#128221; OPPGAVER DENNE UKEN</p>
    @foreach($weekAssignments as $assignment)
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-left:3px solid #862736;margin-bottom:10px;">
        <tr><td style="padding:10px 16px;background:#faf8f5;border-radius:0 10px 10px 0;">
            <p style="font-size:14px;font-weight:600;color:#1a1a1a;margin:0 0 2px;font-family:-apple-system,sans-serif;">{{ $assignment['title'] }}</p>
            @if(!empty($assignment['deadline']))
            <p style="font-size:12px;color:#862736;font-weight:600;margin:0;font-family:-apple-system,sans-serif;">Frist: {{ $assignment['deadline'] }}</p>
            @endif
        </td></tr>
    </table>
    @endforeach
</td></tr>
@endif

{{-- Webinarer denne uken --}}
@if(!empty($weekWebinars) && count($weekWebinars) > 0)
<tr><td style="background:#fff;padding:24px 40px 0;">
    <p style="font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:1px;color:#862736;margin:0 0 16px;font-family:-apple-system,sans-serif;">&#127909; WEBINAR DENNE UKEN</p>
    @foreach($weekWebinars as $webinar)
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4ff;border-radius:10px;margin-bottom:10px;">
        <tr><td style="padding:14px 20px;">
            <p style="font-size:14px;font-weight:600;color:#1a1a1a;margin:0 0 4px;font-family:-apple-system,sans-serif;">{{ $webinar['title'] }}</p>
            @if(!empty($webinar['host']))
            <p style="font-size:12px;color:#5a5550;margin:0 0 4px;font-family:-apple-system,sans-serif;">Med {{ $webinar['host'] }}</p>
            @endif
            <p style="font-size:12px;color:#862736;font-weight:600;margin:0;font-family:-apple-system,sans-serif;">&#128197; {{ $webinar['startTime'] }}</p>
        </td></tr>
    </table>
    @endforeach
</td></tr>
@endif

{{-- Mentormøte-påminnelse --}}
@if(!empty($hasMentorInfo) && $hasMentorInfo)
<tr><td style="background:#fff;padding:24px 40px 0;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f5f0ff;border-radius:10px;">
        <tr><td style="padding:16px 20px;">
            <p style="font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:1px;color:#862736;margin:0 0 8px;font-family:-apple-system,sans-serif;">&#128101; MENTORMØTE</p>
            <p style="font-size:14px;color:#1a1a1a;margin:0 0 8px;font-family:-apple-system,sans-serif;">Husk at du kan booke en mentortime med din redaktør! Få personlig veiledning og tilbakemelding på manuset ditt.</p>
            <a href="{{ config('app.url') . '/learner/dashboard' }}" style="display:inline-block;padding:8px 20px;background:#862736;color:#fff;border-radius:6px;text-decoration:none;font-weight:600;font-size:13px;font-family:-apple-system,sans-serif;">Book mentortime &rarr;</a>
        </td></tr>
    </table>
</td></tr>
@endif

{{-- Personlig melding fra admin (customMessage) --}}
@include('emails.partials.custom-message')

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
            <a href="{{ $portalUrl ?? config('app.url') . '/learner/dashboard' }}" style="display:inline-block;padding:14px 32px;background:#862736;color:#fff;border-radius:8px;text-decoration:none;font-weight:700;font-size:15px;font-family:-apple-system,sans-serif;">Gå til portalen &rarr;</a>
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
