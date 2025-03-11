<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Password reset</title>
    @include('backend.partials.backend-css')
</head>
<body>
    <div class="login-container">
        <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
            <h3 class="text-center mb-3">Editor Password Reset</h3>
    
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
    
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
    
            <form method="POST" action="{{ route('editor.password.email') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required autofocus>
                </div>
    
                <button type="submit" class="btn btn-block btn-primary waves-effect" style="margin-top: 10px">Send Reset Link</button>
            </form>
    
            <div class="text-center" style="margin-top: 10px">
                <a href="/" class="text-decoration-none">Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>