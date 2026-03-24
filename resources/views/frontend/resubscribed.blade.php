@extends('frontend.layout')

@section('title')
    <title>Velkommen tilbake — Forfatterskolen</title>
@stop

@section('content')
<div style="min-height: 70vh; display: flex; align-items: center; justify-content: center;">
    <div style="max-width: 550px; text-align: center; padding: 40px 30px;">
        <div style="font-size: 48px; margin-bottom: 20px;">🎉</div>
        <h1 style="font-family: Georgia, serif; font-size: 28px; color: #1a1a1a; margin-bottom: 16px;">
            Velkommen tilbake!
        </h1>
        <p style="font-size: 17px; color: #555; line-height: 1.7; margin-bottom: 24px;">
            <strong>{{ $email }}</strong> er nå meldt på nyhetsbrevet igjen.
            Du vil motta skrivetips og informasjon om kurs fra oss.
        </p>
        <a href="https://forfatterskolen.no"
           style="display: inline-block; padding: 14px 32px; background-color: #862736; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 16px;">
            Til forsiden →
        </a>
    </div>
</div>
@endsection
