@extends('emails.layout')

@section('content')
<tr><td style="background:#fff;padding:8px 40px 32px;">
    <div style="text-align:center;margin-bottom:24px;">
        <div style="width:48px;height:48px;border-radius:50%;background:#e8f5e9;margin:0 auto 12px;line-height:48px;font-size:22px;">&#128196;</div>
        <h1 style="font-family:Georgia,serif;font-size:22px;font-weight:700;color:#1a1a1a;margin:0 0 4px;">Manuset ditt er mottatt!</h1>
        <p style="font-size:14px;color:#8a8580;margin:0;font-family:-apple-system,sans-serif;">Vi setter i gang med lesingen.</p>
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#faf8f5;border-radius:10px;margin-bottom:24px;">
        <tr><td style="padding:16px 24px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                @if(isset($filename))
                <tr>
                    <td style="font-size:13px;color:#8a8580;padding:4px 0;font-family:-apple-system,sans-serif;">Filnavn</td>
                    <td style="font-size:13px;color:#1a1a1a;text-align:right;padding:4px 0;font-family:-apple-system,sans-serif;">{{ $filename }}</td>
                </tr>
                @endif
                @if(isset($wordCount))
                <tr>
                    <td style="font-size:13px;color:#8a8580;padding:4px 0;font-family:-apple-system,sans-serif;">Ordtelling</td>
                    <td style="font-size:13px;color:#1a1a1a;text-align:right;padding:4px 0;font-family:-apple-system,sans-serif;">{{ $wordCount }} ord</td>
                </tr>
                @endif
                @if(isset($genre))
                <tr>
                    <td style="font-size:13px;color:#8a8580;padding:4px 0;font-family:-apple-system,sans-serif;">Sjanger</td>
                    <td style="font-size:13px;color:#1a1a1a;text-align:right;padding:4px 0;font-family:-apple-system,sans-serif;">{{ $genre }}</td>
                </tr>
                @endif
                <tr>
                    <td style="font-size:13px;color:#8a8580;padding:4px 0;font-family:-apple-system,sans-serif;">Forventet leveranse</td>
                    <td style="font-size:13px;color:#862736;font-weight:600;text-align:right;padding:4px 0;font-family:-apple-system,sans-serif;">{{ $expectedDelivery ?? 'Innen 10 virkedager' }}</td>
                </tr>
            </table>
        </td></tr>
    </table>

    <p style="font-size:14px;color:#5a5550;line-height:1.7;margin:0 0 20px;font-family:-apple-system,sans-serif;">
        En erfaren redaktør vil nå lese gjennom manuset ditt og gi deg grundig tilbakemelding med kommentarer i margen. Du får beskjed så snart det er klart.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr><td style="text-align:center;">
            <a href="{{ config('app.url') }}/learner/manuscripts" style="display:inline-block;padding:14px 32px;background:#862736;color:#fff;border-radius:8px;text-decoration:none;font-weight:700;font-size:15px;font-family:-apple-system,sans-serif;">Se status i portalen &rarr;</a>
        </td></tr>
    </table>
</td></tr>
@endsection
