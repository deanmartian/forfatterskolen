@extends('frontend.layout')

@section('title')
    <title>Avmelding &ndash; Forfatterskolen</title>
@stop

@section('content')
<div class="container" style="max-width: 600px; margin: 80px auto; text-align: center;">
    @if($success)
        <h1 style="color: #862736;">Avmelding bekreftet</h1>
        <p style="font-size: 18px; margin-top: 20px;">
            E-postadressen <strong>{{ $email }}</strong> er nå avmeldt fra nyhetsbrev og markedsførings-e-poster fra Forfatterskolen.
        </p>
        <p style="color: #666; margin-top: 20px;">
            Du vil fortsatt motta viktige e-poster som kursbekreftelser og fakturaer.
        </p>
    @else
        <h1 style="color: #862736;">Noe gikk galt</h1>
        <p style="font-size: 18px; margin-top: 20px;">
            Vi kunne ikke behandle avmeldingen. Vennligst kontakt oss på
            <a href="mailto:post@forfatterskolen.no">post@forfatterskolen.no</a>.
        </p>
    @endif

    <p style="margin-top: 40px;">
        <a href="{{ url('/') }}" style="color: #862736;">← Tilbake til Forfatterskolen</a>
    </p>
</div>
@endsection
