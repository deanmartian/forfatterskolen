@extends('frontend.layout')

@section('title')
<title>Logg inn — Forfatterskolen</title>
@stop

@section('styles')
<style>
    .login-page { background: #f9edef; }
    .login-wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        max-width: 1100px;
        margin: 0 auto;
        min-height: 600px;
    }
    .login-image {
        background-image: url('{{ asset("images-new/login/left-image.png") }}');
        background-size: cover;
        background-position: center;
        min-height: 500px;
    }
    .login-card {
        background: #fff;
        padding: 48px 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .login-title {
        font-family: Georgia, serif;
        font-size: 26px;
        color: #1a1a1a;
        margin-bottom: 28px;
        font-weight: normal;
    }
    .login-input {
        width: 100%;
        padding: 13px 16px;
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        font-size: 15px;
        margin-bottom: 14px;
        box-sizing: border-box;
        transition: border-color 0.2s;
        font-family: inherit;
    }
    .login-input:focus {
        border-color: #862736;
        outline: none;
        box-shadow: 0 0 0 3px rgba(134,39,54,0.1);
    }
    .login-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #333;
        margin-bottom: 6px;
    }
    .login-btn-primary {
        width: 100%;
        background: #862736;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 13px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        margin-bottom: 12px;
        transition: background 0.2s;
    }
    .login-btn-primary:hover { background: #5f1a25; }
    .login-btn-secondary {
        width: 100%;
        background: #fff;
        color: #862736;
        border: 1.5px solid #862736;
        border-radius: 8px;
        padding: 12px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        margin-bottom: 10px;
        text-align: center;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .login-btn-secondary:hover { background: #f9edef; color: #862736; }
    .login-btn-oauth {
        width: 100%;
        background: #fff;
        color: #1a1a1a;
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px;
        font-size: 15px;
        cursor: pointer;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-decoration: none;
        font-weight: 500;
    }
    .login-btn-oauth:hover { border-color: #999; color: #1a1a1a; }
    .login-btn-vipps {
        background: #FF5B24;
        color: #fff;
        border-color: #FF5B24;
    }
    .login-btn-vipps:hover { background: #e04e1e; color: #fff; }
    .login-divider {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 18px 0;
        color: #999;
        font-size: 13px;
    }
    .login-divider::before, .login-divider::after {
        content: '';
        flex: 1;
        border-top: 1px solid #e5e7eb;
    }
    .login-link {
        color: #862736;
        font-size: 14px;
        text-decoration: none;
    }
    .login-link:hover { text-decoration: underline; }
    .login-bottom {
        text-align: center;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
        font-size: 14px;
        color: #666;
    }
    .login-section { display: none; }
    .login-section.active { display: block; }
    .password-wrapper { position: relative; }
    .password-wrapper input { padding-right: 44px; }
    .password-toggle {
        position: absolute;
        right: 12px;
        top: 14px;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 18px;
        color: #999;
    }
    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 14px;
        margin-bottom: 16px;
    }
    .alert-danger { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
    .back-link { display: inline-block; margin-bottom: 16px; font-size: 14px; }
    @media (max-width: 768px) {
        .login-wrapper { grid-template-columns: 1fr; }
        .login-image { display: none; }
        .login-card { padding: 32px 24px; }
    }
</style>
@stop

@section('content')
<div class="login-page">
    <div class="login-wrapper">
        <div class="login-image"></div>
        <div class="login-card">

            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif
            @if(session('magic_sent'))
                <div class="alert alert-success">Vi har sendt deg en innloggingslenke på e-post. Sjekk innboksen din!</div>
            @endif
            @if(session('passwordreset_success'))
                <div class="alert alert-success">{{ session('passwordreset_success') }}</div>
            @endif

            {{-- SEKSJON 1: Logg inn --}}
            <div id="section-login" class="login-section active">
                <h1 class="login-title">Logg inn på Forfatterskolen</h1>

                <form method="POST" action="{{ route('frontend.login.store') }}">
                    @csrf
                    <label class="login-label">E-postadresse</label>
                    <input type="email" name="email" class="login-input" value="{{ old('email') }}" required autofocus placeholder="din@epost.no">

                    <label class="login-label">Passord</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="loginPassword" class="login-input" required placeholder="Ditt passord">
                        <button type="button" class="password-toggle" onclick="togglePw('loginPassword')">👁</button>
                    </div>

                    <button type="submit" class="login-btn-primary">Logg inn</button>
                </form>

                <a href="#" class="login-link" onclick="showSection('forgot-password'); return false;">Glemt passordet? →</a>

                <div class="login-divider"><span>eller</span></div>

                <a href="#" class="login-btn-secondary" onclick="showSection('magic-link'); return false;">
                    ✉️ Send meg en innloggingslenke
                </a>

                <a href="{{ route('auth.login.google') }}" class="login-btn-oauth">
                    <svg width="18" height="18" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    Fortsett med Google
                </a>

                <a href="{{ route('auth.login.vipps') }}" class="login-btn-oauth login-btn-vipps">
                    Fortsett med Vipps
                </a>

                <div class="login-bottom">
                    Ny bruker? <a href="#" class="login-link" onclick="showSection('register'); return false;">Registrer deg gratis →</a>
                </div>
            </div>

            {{-- SEKSJON 2: Registrering --}}
            <div id="section-register" class="login-section">
                <a href="#" class="login-link back-link" onclick="showSection('login'); return false;">← Tilbake til innlogging</a>
                <h1 class="login-title">Opprett en konto</h1>

                <form method="POST" action="{{ route('frontend.register.store') }}">
                    @csrf
                    <label class="login-label">Fornavn</label>
                    <input type="text" name="first_name" class="login-input" required placeholder="Fornavn">

                    <label class="login-label">Etternavn</label>
                    <input type="text" name="last_name" class="login-input" required placeholder="Etternavn">

                    <label class="login-label">E-postadresse</label>
                    <input type="email" name="email" class="login-input" required placeholder="din@epost.no">

                    <label class="login-label">Passord</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="regPassword" class="login-input" required placeholder="Velg et passord">
                        <button type="button" class="password-toggle" onclick="togglePw('regPassword')">👁</button>
                    </div>

                    <button type="submit" class="login-btn-primary">Registrer deg</button>
                </form>

                <div class="login-divider"><span>eller</span></div>

                <a href="{{ route('auth.login.google') }}" class="login-btn-oauth">
                    <svg width="18" height="18" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    Registrer med Google
                </a>

                <a href="{{ route('auth.login.vipps') }}" class="login-btn-oauth login-btn-vipps">
                    Registrer med Vipps
                </a>

                <div class="login-bottom">
                    Har du allerede konto? <a href="#" class="login-link" onclick="showSection('login'); return false;">Logg inn →</a>
                </div>
            </div>

            {{-- SEKSJON 3: Magic Link --}}
            <div id="section-magic-link" class="login-section">
                <a href="#" class="login-link back-link" onclick="showSection('login'); return false;">← Tilbake til innlogging</a>
                <h1 class="login-title">Send innloggingslenke</h1>
                <p style="font-size:14px;color:#666;margin-bottom:20px;">Skriv inn e-postadressen din, så sender vi deg en lenke du kan logge inn med — uten passord.</p>

                <form method="POST" action="{{ route('magic-link.send') }}">
                    @csrf
                    <label class="login-label">E-postadresse</label>
                    <input type="email" name="email" class="login-input" required placeholder="din@epost.no" autofocus>
                    <button type="submit" class="login-btn-primary">Send innloggingslenke</button>
                </form>
            </div>

            {{-- SEKSJON 4: Glemt passord --}}
            <div id="section-forgot-password" class="login-section">
                <a href="#" class="login-link back-link" onclick="showSection('login'); return false;">← Tilbake til innlogging</a>
                <h1 class="login-title">Tilbakestill passordet</h1>
                <p style="font-size:14px;color:#666;margin-bottom:20px;">Skriv inn e-postadressen din, så sender vi deg en lenke for å sette nytt passord.</p>

                <form method="POST" action="{{ route('frontend.passwordreset.store') }}">
                    @csrf
                    <label class="login-label">E-postadresse</label>
                    <input type="email" name="email" class="login-input" required placeholder="din@epost.no">
                    <button type="submit" class="login-btn-primary">Tilbakestill passordet</button>
                </form>
            </div>

        </div>
    </div>
</div>
@stop

@section('js')
<script>
function showSection(section) {
    document.querySelectorAll('.login-section').forEach(function(el) {
        el.classList.remove('active');
    });
    document.getElementById('section-' + section).classList.add('active');
}

function togglePw(id) {
    var el = document.getElementById(id);
    el.type = el.type === 'password' ? 'text' : 'password';
}

@if(session('magic_sent'))
    // Vis magic link bekreftelse
@endif
</script>
@stop
