<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Password Reset</title>
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

            <form method="POST" action="{{route('editor.passwordreset.update', $passwordReset->token)}}">
                {{csrf_field()}}
                <div class="form-group">
                    <label>Nytt Passord</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Bekreft Passordet</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary pull-right">Oppdater Passordet</button>
                <div class="clearfix"></div>
            </form>
        </div>
    </div>

</body>
</html>