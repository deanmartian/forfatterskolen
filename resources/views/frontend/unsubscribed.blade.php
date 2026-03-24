@extends('frontend.layout')

@section('title')
    <title>Avmeldt nyhetsbrev — Forfatterskolen</title>
@stop

@section('content')
<div style="min-height: 70vh; display: flex; align-items: center; justify-content: center;">
    <div style="max-width: 550px; text-align: center; padding: 40px 30px;">
        <div style="font-size: 48px; margin-bottom: 20px;">📝</div>
        <h1 style="font-family: Georgia, serif; font-size: 28px; color: #1a1a1a; margin-bottom: 16px;">
            Du er nå avmeldt
        </h1>
        <p style="font-size: 17px; color: #555; line-height: 1.7; margin-bottom: 24px;">
            Vi har fjernet <strong>{{ $email }}</strong> fra nyhetsbrevet vårt.
            Du vil ikke motta flere e-poster fra oss.
        </p>
        <p style="font-size: 15px; color: #888; line-height: 1.6; margin-bottom: 30px;">
            Vi håper du har fått inspirasjon til skrivingen din — og ønsker deg lykke til videre!
            Skulle du ombestemme deg, er du alltid velkommen tilbake.
        </p>

        <div id="resubscribeSection">
            <form method="POST" action="{{ route('newsletter.resubscribe') }}" style="margin-bottom: 16px;">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <button type="submit"
                    style="display: inline-block; padding: 14px 32px; background-color: #fff; color: #862736; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 16px; border: 2px solid #862736; cursor: pointer; transition: all 0.2s;"
                    onmouseover="this.style.backgroundColor='#862736';this.style.color='#fff'"
                    onmouseout="this.style.backgroundColor='#fff';this.style.color='#862736'">
                    Meld meg på igjen →
                </button>
            </form>
        </div>

        <a href="https://forfatterskolen.no"
           style="display: inline-block; padding: 14px 32px; background-color: #862736; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 16px;">
            Til forsiden →
        </a>
    </div>
</div>
@endsection
