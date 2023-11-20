<h1>{{ trans('site.front.login.register') }}</h1>

<form method="post" method="post" action="{{route('frontend.register.store')}}" onsubmit="disableSubmit(this)">
    {{csrf_field()}}
    @if (Request::has('r'))
        <input type="hidden" name="redirect" value="{{ Request::get('r') }}">
    @endif

    <div class="form-group">
        <label>
            {{ trans('site.front.form.email') }}
        </label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa mail-icon"></i></span>
            </div>
            <input type="email" name="register_email"
                   class="form-control no-border-left" required value="{{old('register_email')}}">
        </div>
    </div>
    
    <div class="form-group">
        <label>
            {{ trans('site.front.form.first-name') }}
        </label>

        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa user-icon"></i></span>
            </div>
            <input type="text" name="register_first_name"
                   class="form-control no-border-left" required value="{{old('register_first_name')}}">
        </div>
    </div>

    <div class="form-group">
        <label>{{ trans('site.front.form.last-name') }}</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa user-icon"></i></span>
            </div>
            <input type="text" name="register_last_name"
                   class="form-control no-border-left" required value="{{old('register_last_name')}}">
        </div>
    </div>

    <div class="form-group">
        <label>{{ trans('site.front.form.password') }}</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa lock-icon"></i></span>
            </div>
            <input type="password" name="register_password"
                   class="form-control no-border-left" required>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            {!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::renderJS() !!}
            {!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::display(['data-callback' => 'captchaCB']) !!}
        </div>
        <div class="col-md-6 text-right">
            <button type="submit" class="btn site-btn-global-w-arrow">{{ trans('site.front.register') }}</button>
        </div>
    </div>    

    <div class="clearfix"></div>

    <div class="login-text">Logg inn med:</div>

    <div class="social-btn-container">
        <a href="{{ route('auth.login.google') }}" class="newLoginBtn newLoginBtn--google btn">
            Google
        </a>

        <a href="{{ route('auth.login.facebook') }}" class="newLoginBtn newLoginBtn--facebook btn">
            Faceboook
        </a>
    </div>
</form>

<div class="clearfix"></div>

@if (Session::has('passwordreset_success'))
    <div class="alert alert-success no-bottom-margin  d-flex mt-3">
        {{Session::get('passwordreset_success')}}
    </div>
@endif