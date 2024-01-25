@extends('frontend.layout')

@section('title')
<title>Login &rsaquo; Forfatterskolen</title>
@stop

@section('content')
<div class="global-checkout-page" id="app-container">
    <div class="header" data-bg="https://www.forfatterskolen.no/images-new/checkout-top.png">
    </div>
    <div class="body">
        <div class="container d-flex align-items-center justify-content-center min-vh-100">
            <div class="col-md-6 bg-white p-4 rounded">
                <div class="card">
                    <div class="card-body">
                        <h1 class="mb-2 text-center">{{ trans('site.front.form.login') }}</h1>

                        <form id="checkoutLogin" action="{{ route('frontend.login.checkout.store') }}" method="POST">
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
                    
                            <button type="submit" class="btn site-btn-global pull-right">
                                {{ trans('site.front.form.login') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div> <!-- end container-->
    </div>
</div>
@stop