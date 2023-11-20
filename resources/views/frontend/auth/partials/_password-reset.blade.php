<h1>{{ trans('site.front.login.password-reset-title') }}</h1>

<form method="post" action="{{route('frontend.passwordreset.store')}}" onsubmit="disableSubmit(this)">
    {{csrf_field()}}

    <div class="form-group">
        <label>{{ trans('site.front.form.email') }}</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa at-icon"></i></span>
            </div>
            <input type="email" name="reset_email"
            class="form-control no-border-left" required value="{{old('reset_email')}}">
        </div>
    </div>
    
    <button type="submit" class="btn site-btn-global-w-arrow float-right">
        {{ trans('site.front.login.password-reset') }}
    </button>
</form>

<div class="clearfix"></div>

@if (Session::has('passwordreset_success'))
    <div class="alert alert-success no-bottom-margin  d-flex mt-3">
        {{Session::get('passwordreset_success')}}
    </div>
@endif