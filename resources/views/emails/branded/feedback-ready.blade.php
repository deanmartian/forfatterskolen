@extends('emails.layout')

@section('content')
{{-- Mørk hero --}}
<tr><td style="background:linear-gradient(135deg,#1c1917,#2a2520);padding:40px;text-align:center;">
    <div style="font-size:36px;margin-bottom:12px;">&#127881;</div>
    <h1 style="font-family:Georgia,serif;font-size:24px;font-weight:700;color:#fff;margin:0 0 8px;">Tilbakemeldingen er klar!</h1>
    <p style="font-size:15px;color:rgba(255,255,255,0.6);margin:0;font-family:-apple-system,sans-serif;">Din redaktør har lest ferdig manuset ditt.</p>
</td></tr>

<tr><td style="background:#fff;padding:32px 40px;">
    <p style="font-size:15px;color:#5a5550;line-height:1.7;margin:0 0 20px;font-family:-apple-system,sans-serif;">
        Hei {{ $firstName ?? '' }}! Redaktøren din har nå gått gjennom manuset og lagt inn kommentarer i margen. Logg inn i portalen for å laste ned tilbakemeldingen.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr><td style="text-align:center;padding:8px 0;">
            <a href="{{ $feedbackUrl ?? config('app.url').'/account/manuscripts' }}" style="display:inline-block;padding:14px 32px;background:#862736;color:#fff;border-radius:8px;text-decoration:none;font-weight:700;font-size:15px;font-family:-apple-system,sans-serif;">Se tilbakemeldingen &rarr;</a>
        </td></tr>
    </table>
</td></tr>
@endsection
