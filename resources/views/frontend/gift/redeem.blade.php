@extends('frontend.layout')

@section('title')
<title>Forfatterskolen</title>
@stop

@section('content')
    <div class="login-container" data-bg="https://www.forfatterskolen.no/images-new/login/login-bg.jpg">
        <div class="container">
            <div class="row first-row">
                <div class="col-md-6 left-container" data-bg="https://www.forfatterskolen.no/images-new/login/left-bg.jpg">
                </div>
                <div class="col-md-6 right-container">
                    <div class="d-table h-100 w-100 text-center">
                        <div class="d-table-cell align-middle">

                            <form action="" method="POST">
                                {{ csrf_field() }}
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa at-icon"></i></span>
                                    </div>
                                    <input type="email" name="email" class="form-control no-border-left"
                                           placeholder="{{ trans('site.front.form.email') }}" required
                                           value="{{ Auth::user() ? Auth::user()->email : old('email') }}">
                                </div>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa lock-icon"></i></span>
                                    </div>
                                    <input type="text" name="redeem_code" placeholder="Redeem Code"
                                           class="form-control no-border-left" required>
                                </div>

                                <button type="submit" class="btn site-btn-global float-right">{{ trans('site.submit') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop