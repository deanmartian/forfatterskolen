@extends('backend.layout')

@section('title')
    <title>Email</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-envelope"></i> Email</h3>
        <div class="clearfix"></div>
    </div>
    <div class="col-md-12">
        <div class="login-container">
            <div class="text-center">
                <h3>Email Login</h3>
            </div>
            <form role="form" id="login" method="POST" action="{{ route('admin.email.login') }}">
                {{csrf_field()}}
                <div class="form-group">
                    <input type="text" id="email" class="form-control" name="email" value="" placeholder="Email" required>
                </div>
                <div class="form-group fg-line">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button type="submit" name="submit" class="btn btn-block btn-primary m-t-10 waves-effect">Sign in</button>
                @if($flash = session('message.content'))
                    <br>
                    <div class="alert alert-{{ session('message.level') }}" role="alert">
                        {{ $flash }}
                    </div>

                @endif
            </form>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $(function(){
            setTimeout(function(){
                $(".alert").fadeOut();
            },3000);
        });
    </script>
@stop