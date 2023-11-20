<h1>{{ trans('site.front.login.change-password') }}</h1>

<form method="post" action="{{route('frontend.password-change')}}" onsubmit="disableSubmit(this)">
    {{csrf_field()}}

    <div class="form-group">
        <label>{{ trans('site.front.form.email') }}</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa mail-icon"></i></span>
            </div>
            <input type="email" name="email"
                   class="form-control no-border-left" required value="{{old('email')}}">
        </div>
    </div>

    <div class="form-group">
        <label>{{ trans('site.front.login.enter-your-current-password') }}</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa lock-icon"></i></span>
            </div>
            <input type="password" name="current_password"
                   class="form-control no-border-left" required>
        </div>
    </div>

    <div class="form-group">
        <label>{{ trans('site.front.login.enter-your-new-password') }}</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa lock-icon"></i></span>
            </div>
            <input type="password" name="password"
                   class="form-control no-border-left" required>
        </div>
    </div>

    <div class="form-group">
        <label>{{ trans('site.confirm-password') }}</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa lock-icon"></i></span>
            </div>
            <input type="password" name="password_confirmation"
                   class="form-control no-border-left" required>
        </div>
    </div>

    <button type="submit" class="btn site-btn-global-w-arrow pull-right">
        {{ trans('site.front.login.change-password') }}
    </button>
</form>

<div class="clearfix"></div>

@if (Session::has('password_change_success'))
    <div class="alert alert-success no-bottom-margin  d-flex mt-3">
        {{Session::get('password_change_success')}}
    </div>
@endif