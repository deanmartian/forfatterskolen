<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logg inn — Forfatterskolen</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f9edef;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 440px;
            padding: 40px;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 24px;
        }
        .login-logo img { height: 32px; }
        .login-title {
            text-align: center;
            font-family: Georgia, serif;
            font-size: 22px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 28px;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
        }
        .form-group input {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-group input:focus {
            border-color: #862736;
            box-shadow: 0 0 0 3px rgba(134,39,54,0.1);
        }
        .password-wrapper {
            position: relative;
        }
        .password-wrapper input {
            padding-right: 44px;
        }
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            color: #999;
        }
        .btn-primary {
            width: 100%;
            padding: 13px;
            background: #862736;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 4px;
        }
        .btn-primary:hover { background: #6e1f2d; }
        .forgot-link {
            display: block;
            text-align: right;
            font-size: 13px;
            color: #862736;
            text-decoration: none;
            margin-top: 8px;
        }
        .forgot-link:hover { text-decoration: underline; }
        .divider {
            display: flex;
            align-items: center;
            margin: 24px 0;
            color: #999;
            font-size: 13px;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-top: 1px solid #e5e7eb;
        }
        .divider span { padding: 0 12px; }
        .btn-social {
            width: 100%;
            padding: 13px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            margin-bottom: 10px;
            transition: opacity 0.2s;
        }
        .btn-social:hover { opacity: 0.9; }
        .btn-magic {
            background: #fff;
            color: #862736;
            border: 2px solid #862736;
        }
        .btn-google {
            background: #fff;
            color: #333;
            border: 1px solid #d1d5db;
        }
        .btn-vipps {
            background: #FF5B24;
            color: #fff;
            border: none;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
            color: #666;
        }
        .register-link a {
            color: #862736;
            text-decoration: none;
            font-weight: 600;
        }
        .register-link a:hover { text-decoration: underline; }
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 16px;
        }
        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .magic-link-form {
            display: none;
            margin-bottom: 10px;
        }
        .magic-link-form.show { display: block; }
        .magic-link-form input {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 15px;
            margin-bottom: 8px;
        }
        .magic-link-form button {
            width: 100%;
            padding: 13px;
            background: #862736;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <a href="/">
                <img src="{{ asset('images/logo.png') }}" alt="Forfatterskolen">
            </a>
        </div>

        <h1 class="login-title">Logg inn på Forfatterskolen</h1>

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('magic_sent'))
            <div class="alert alert-success">
                Vi har sendt deg en innloggingslenke på e-post. Sjekk innboksen din!
            </div>
        @endif

        @if(session('passwordreset_success'))
            <div class="alert alert-success">
                {{ session('passwordreset_success') }}
            </div>
        @endif

        {{-- Innloggingsskjema --}}
        <form method="POST" action="{{ route('frontend.login.store') }}">
            @csrf
            <div class="form-group">
                <label for="email">E-postadresse</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="din@epost.no">
            </div>

            <div class="form-group">
                <label for="password">Passord</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" required placeholder="Ditt passord">
                    <button type="button" class="password-toggle" onclick="togglePassword()">👁</button>
                </div>
            </div>

            <button type="submit" class="btn-primary">Logg inn</button>

            <a href="{{ url('/auth/login?t=passwordreset') }}" class="forgot-link">Glemt passordet? →</a>
        </form>

        <div class="divider"><span>eller</span></div>

        {{-- Magic Link --}}
        <a href="#" class="btn-social btn-magic" onclick="document.getElementById('magicForm').classList.toggle('show'); return false;">
            ✉️ Send meg en innloggingslenke
        </a>

        <div id="magicForm" class="magic-link-form @if(session('magic_link_sent')) show @endif">
            @if(session('magic_link_sent'))
                <div style="background:#e8f5e9;color:#2e7d32;padding:12px 16px;border-radius:8px;margin-bottom:12px;text-align:center;">
                    ✅ Sjekk innboksen din! Vi har sendt en innloggingslenke.
                </div>
            @else
                <form method="POST" action="{{ url('/auth/magic-link/send') }}">
                    @csrf
                    <input type="email" name="email" placeholder="Skriv inn e-postadressen din" required>
                    <button type="submit">Send innloggingslenke</button>
                </form>
            @endif
        </div>

        {{-- Google --}}
        <a href="{{ route('auth.login.google') }}" class="btn-social btn-google">
            <svg width="18" height="18" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
            Fortsett med Google
        </a>

        {{-- Vipps --}}
        <a href="{{ route('auth.login.vipps') }}" class="btn-social btn-vipps">
            Fortsett med Vipps
        </a>

        <div class="register-link">
            Ny bruker? <a href="{{ url('/auth/login?t=register') }}">Registrer deg gratis →</a>
        </div>
    </div>

    <script>
        function togglePassword() {
            var pw = document.getElementById('password');
            pw.type = pw.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>
