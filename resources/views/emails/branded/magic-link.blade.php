@extends('emails.layout')

@section('content')
<tr><td style="background:#fff;padding:8px 40px 32px;text-align:center;">
    <div style="width:48px;height:48px;border-radius:50%;background:#f4e8ea;margin:0 auto 16px;line-height:48px;">
        <span style="font-size:22px;">&#128273;</span>
    </div>
    <h1 style="font-family:Georgia,serif;font-size:22px;font-weight:700;color:#1a1a1a;margin:0 0 8px;">Logg inn på Forfatterskolen</h1>
    <p style="font-size:15px;color:#5a5550;line-height:1.6;margin:0 0 24px;font-family:-apple-system,sans-serif;">
        Klikk knappen under for å logge inn. Lenken er gyldig i 30 minutter.
    </p>
    <a href="{{ $loginUrl ?? '#' }}" style="display:inline-block;padding:14px 40px;background:#862736;color:#fff;border-radius:8px;text-decoration:none;font-weight:700;font-size:16px;font-family:-apple-system,sans-serif;">Logg inn &rarr;</a>
    <p style="font-size:12px;color:#8a8580;margin:20px 0 0;line-height:1.6;font-family:-apple-system,sans-serif;">
        Hvis du ikke ba om denne lenken, kan du trygt ignorere denne e-posten.
    </p>
</td></tr>
@endsection
