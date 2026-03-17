@extends('emails.layout')

@section('content')
{{-- Hvit hero med klokke --}}
<tr><td style="background:#fff;padding:40px;text-align:center;border-bottom:1px solid rgba(0,0,0,0.06);">
    <div style="width:56px;height:56px;border-radius:50%;background:#fef3cd;margin:0 auto 16px;line-height:56px;font-size:28px;">&#9200;</div>
    <p style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:1px;color:#e65100;margin:0 0 12px;font-family:-apple-system,sans-serif;">P&Aring;MINNELSE</p>
    <h1 style="font-family:Georgia,serif;font-size:24px;font-weight:700;color:#1a1a1a;margin:0 0 8px;">Husk oppgaven din!</h1>
    <p style="font-size:15px;color:#5a5550;margin:0;font-family:-apple-system,sans-serif;">{{ $courseName ?? '' }}</p>
</td></tr>

<tr><td style="background:#fff;padding:32px 40px;">
    <p style="font-size:15px;color:#5a5550;line-height:1.7;margin:0 0 20px;font-family:-apple-system,sans-serif;">
        Hei {{ $firstName ?? '' }}! Vi vil minne deg p&aring; at du har en oppgave som snart skal leveres.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#faf8f5;border-radius:10px;margin-bottom:24px;">
        <tr><td style="padding:16px 24px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="padding:4px 0;">
                        <p style="font-size:12px;color:#8a8580;margin:0;font-family:-apple-system,sans-serif;">Oppgave</p>
                        <p style="font-size:14px;font-weight:600;color:#1a1a1a;margin:2px 0 0;font-family:-apple-system,sans-serif;">{{ $assignmentTitle ?? '' }}</p>
                    </td>
                </tr>
                @if(!empty($submissionDate))
                <tr>
                    <td style="padding:8px 0 4px;">
                        <p style="font-size:12px;color:#8a8580;margin:0;font-family:-apple-system,sans-serif;">Frist</p>
                        <p style="font-size:14px;font-weight:600;color:#862736;margin:2px 0 0;font-family:-apple-system,sans-serif;">{{ $submissionDate }}</p>
                    </td>
                </tr>
                @endif
            </table>
        </td></tr>
    </table>

    @include('emails.partials.custom-message')

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr><td style="text-align:center;">
            <a href="{{ $portalUrl ?? config('app.url') . '/learner/dashboard' }}" style="display:inline-block;padding:14px 32px;background:#862736;color:#fff;border-radius:8px;text-decoration:none;font-weight:700;font-size:15px;font-family:-apple-system,sans-serif;">Lever oppgaven &rarr;</a>
        </td></tr>
    </table>
</td></tr>
@endsection
