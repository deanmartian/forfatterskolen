@extends('emails.layout')

@section('content')
{{-- Mørk hero --}}
<tr><td style="background:linear-gradient(135deg,#1c1917,#2a2520);padding:40px;text-align:center;">
    <p style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:1px;color:#ff8a65;margin:0 0 12px;font-family:-apple-system,sans-serif;">&#9889; SISTE FRIST!</p>
    <h1 style="font-family:Georgia,serif;font-size:24px;font-weight:700;color:#fff;margin:0 0 8px;">Fristen er i dag!</h1>
    <p style="font-size:15px;color:rgba(255,255,255,0.6);margin:0;font-family:-apple-system,sans-serif;">{{ $courseName ?? '' }}</p>
</td></tr>

<tr><td style="background:#fff;padding:32px 40px;">
    <p style="font-size:15px;color:#5a5550;line-height:1.7;margin:0 0 20px;font-family:-apple-system,sans-serif;">
        Hei {{ $firstName ?? '' }}! I dag er siste frist for &aring; levere oppgaven din. Ikke g&aring; glipp av muligheten &mdash; lever n&aring;!
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
                        <p style="font-size:14px;font-weight:600;color:#ff5722;margin:2px 0 0;font-family:-apple-system,sans-serif;">{{ $submissionDate }} (I DAG)</p>
                    </td>
                </tr>
                @endif
            </table>
        </td></tr>
    </table>

    @include('emails.partials.custom-message')

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr><td style="text-align:center;">
            <a href="{{ $portalUrl ?? config('app.url') . '/account/dashboard' }}" style="display:inline-block;padding:14px 32px;background:#862736;color:#fff;border-radius:8px;text-decoration:none;font-weight:700;font-size:15px;font-family:-apple-system,sans-serif;">Lever n&aring; &rarr;</a>
        </td></tr>
    </table>
</td></tr>
@endsection
