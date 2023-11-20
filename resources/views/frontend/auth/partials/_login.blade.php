<h1>{{ trans('site.front.form.login') }}</h1>

<ul class="nav global-gray-tabs justify-content-center">
    <li>
        <a href="?tab=main" @if( Request::input('tab') == 'main' || Request::input('tab') == '') class="active" @endif>
            Min Side
        </a>
    </li>
    <li>
        <a href="?tab=self-publishing" @if( Request::input('tab') == 'self-publishing' ) class="active" @endif>
            Selvpubliseringsportal
        </a>
    </li>
</ul>

@if( Request::input('tab') == 'main' || Request::input('tab') == '')
    <form method="post" action="{{route('frontend.login.store')}}" onsubmit="disableSubmit(this)">
        {{csrf_field()}}
        
        <div class="form-group">
            <label>
                {{ trans('site.front.form.email') }}
            </label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa mail-icon"></i></span>
                </div>
                <input type="email" name="email" class="form-control no-border-left" required value="{{old('email')}}">
            </div>
        </div>

        <div class="form-group">
            <label>
                {{ trans('site.front.form.password') }}
            </label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa lock-icon"></i></span>
                </div>
                <input type="password" name="password"
                    class="form-control no-border-left" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                {!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::renderJS() !!}
                {!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::display(['data-callback' => 'captchaCB']) !!}
            </div>
            <div class="col-md-6 text-right">
                <button type="submit" class="btn site-btn-global-w-arrow">{{ trans('site.front.form.login') }}</button>
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

            <a href="{{ route('auth.login.vipps') }}" class="newLoginBtn newLoginBtn--vipps btn">
                <img src="{{ asset('images-new/icon/vipps-text.png') }}" alt="">
            </a>
        </div>
    </form>
@endif

@if( Request::input('tab') == 'self-publishing' )
    <form method="post" action="{{route('frontend.login.self-publishing-store')}}" 
    onsubmit="disableSubmit(this)">
        {{csrf_field()}}

        <div class="form-group">
            <label>
                {{ trans('site.front.form.email') }}
            </label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa mail-icon"></i></span>
                </div>
                <input type="email" name="email" class="form-control no-border-left" required value="{{old('email')}}">
            </div>
        </div>

        <div class="form-group">
            <label>
                {{ trans('site.front.form.password') }}
            </label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa lock-icon"></i></span>
                </div>
                <input type="password" name="password"
                    class="form-control no-border-left" required>
            </div>
        </div>

        <button type="submit" class="btn site-btn-global-w-arrow pull-right">{{ trans('site.front.form.login') }}</button>
    </form>
@endif

<div class="clearfix"></div>

@if (Session::has('passwordreset_success'))
    <div class="alert alert-success no-bottom-margin  d-flex mt-3">
        {{Session::get('passwordreset_success')}}
    </div>
@endif