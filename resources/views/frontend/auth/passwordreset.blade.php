@extends('frontend.layout')

@section('page_title', 'Tilbakestill passord &rsaquo; Forfatterskolen')
@section('meta_desc', 'Tilbakestill passordet ditt for Forfatterskolen.')

@section('styles')
<style>
    .pr-wrapper {
        min-height: 70vh;
        display: flex;
        align-items: center;
        padding: 60px 20px;
        background: linear-gradient(135deg, #faf8f5 0%, #fdf5f6 100%);
    }
    .pr-card {
        max-width: 480px;
        width: 100%;
        margin: 0 auto;
        background: #fff;
        border-radius: 16px;
        padding: 50px 44px;
        box-shadow: 0 10px 40px rgba(134, 39, 54, 0.1);
    }
    .pr-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: linear-gradient(135deg, #862736 0%, #5e1a26 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
        box-shadow: 0 8px 24px rgba(134, 39, 54, 0.2);
    }
    .pr-icon svg { width: 34px; height: 34px; color: #fff; }
    .pr-card h1 {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1.85rem;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0 0 10px;
        text-align: center;
    }
    .pr-card .pr-sub {
        font-size: 1rem;
        color: #5a5550;
        line-height: 1.6;
        text-align: center;
        margin-bottom: 32px;
    }
    .pr-field {
        margin-bottom: 18px;
    }
    .pr-field label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 8px;
    }
    .pr-field input[type="password"] {
        width: 100%;
        background: #fff;
        border: 1.5px solid #d8d2cc;
        border-radius: 8px;
        padding: 13px 16px;
        font-size: 1rem;
        color: #1a1a1a;
        transition: border-color 0.15s, box-shadow 0.15s;
        box-sizing: border-box;
        height: auto;
        line-height: 1.4;
    }
    .pr-field input[type="password"]:focus {
        outline: none;
        border-color: #862736;
        box-shadow: 0 0 0 3px rgba(134, 39, 54, 0.12);
    }
    .pr-btn {
        display: block;
        width: 100%;
        background: #862736;
        color: #fff;
        padding: 14px 24px;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
        box-shadow: 0 4px 12px rgba(134, 39, 54, 0.2);
        margin-top: 8px;
    }
    .pr-btn:hover {
        background: #9c2e40;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(134, 39, 54, 0.3);
    }
    .pr-errors {
        background: #fdecee;
        border: 1px solid #f5c6cb;
        color: #862736;
        border-radius: 8px;
        padding: 14px 18px;
        margin-bottom: 22px;
        font-size: 0.92rem;
    }
    .pr-errors ul {
        margin: 0;
        padding-left: 18px;
    }
    @media (max-width: 540px) {
        .pr-card { padding: 40px 28px; }
        .pr-card h1 { font-size: 1.55rem; }
    }
</style>
@stop

@section('content')
<div class="pr-wrapper">
    <div class="pr-card">
        <div class="pr-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0-1.657-1.343-3-3-3s-3 1.343-3 3 1.343 3 3 3m0 0v3m9-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9l3 3-3 3"/>
            </svg>
        </div>

        <h1>Velg nytt passord</h1>
        <p class="pr-sub">Skriv inn et nytt passord du vil bruke for å logge inn på Forfatterskolen.</p>

        @if($errors->any())
            <div class="pr-errors">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('frontend.passwordreset.update', $passwordReset->token) }}">
            {{ csrf_field() }}

            <div class="pr-field">
                <label for="pr-password">Nytt passord</label>
                <input id="pr-password" type="password" name="password" required autofocus minlength="6">
            </div>

            <div class="pr-field">
                <label for="pr-password-confirm">Bekreft passord</label>
                <input id="pr-password-confirm" type="password" name="password_confirmation" required minlength="6">
            </div>

            <button type="submit" class="pr-btn">Oppdater passord</button>
        </form>
    </div>
</div>
@stop
