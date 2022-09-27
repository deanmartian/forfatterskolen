<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forfatterskolen | Sign In</title>
    @include('backend.partials.backend-css')
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0 maximum-scale=1.0, user-scalable=no">
</head>
<body>
<div class="login-container">
    <div class="text-center">
        <img src="{{asset('images/logo.png')}}">
    </div>
    <form role="form" id="login" method="POST" action="{{ route('giutbok.login.store') }}">
        {{csrf_field()}}
        <input type="hidden" name="intended" value="{{url()->current()}}">
        <div class="form-group">
            <input type="email" id="email" class="form-control" name="email" value="" placeholder="Email" required>
        </div>
        <div class="form-group fg-line">
            <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <button type="submit" name="submit" class="btn btn-block btn-primary m-t-10 waves-effect">Sign in</button>
        @if ( $errors->any() )
            <br />
            <div class="alert alert-danger no-bottom-margin">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </form>
</div>
@include('backend.partials.scripts')
</body>
</html>
