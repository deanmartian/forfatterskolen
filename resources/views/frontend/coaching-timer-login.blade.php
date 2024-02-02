@extends('frontend.layout')

@section('title')
    <title>Checkout &rsaquo; Forfatterskolen</title>
@stop

@section('content')
<div class="global-checkout-page" id="app-container">
    <div class="header" data-bg="https://www.forfatterskolen.no/images-new/checkout-top.png">
    </div>
    <div class="body">
        <div class="container d-flex align-items-center justify-content-center min-vh-100">
            <div class="col-md-8 bg-white p-4 rounded">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav global-nav-tabs">
                            <li class="nav-item">
                                <a data-toggle="tab" href="#login" 
                                class="nav-link @if(!Request::input('t')) active @endif" role="tab">
                                    <span>{{ trans('site.front.form.login') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a data-toggle="tab" href="#register" 
                                class="nav-link @if(Request::input('t') == 'register') active @endif" role="tab">
                                    <span>{{ trans('site.front.login.register') }}</span>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div id="login" class="tab-pane fade @if(!Request::input('t')) in active @endif" role="tabpanel">

                                {{-- <h1 class="mt-5 text-center">{{ trans('site.front.form.login') }}</h1> --}}
                                <h3 class="w-75 text-center mx-auto my-5">
                                    {{ trans('site.front.checkout.login-or-register-note') }}
                                </h3>

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
                                            <input type="email" name="email" class="form-control no-border-left" required 
                                            value="{{old('email')}}">
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
                            </div> <!-- end login-->

                            <div id="register" class="tab-pane fade @if(Request::input('t') == 'register') in active @endif" 
                                role="tabpanel">

                                <h1 class="my-5 text-center">{{ trans('site.front.login.register') }}</h1>

                                <form method="post" method="post" action="{{route('frontend.register.store')}}" 
                                onsubmit="disableSubmit(this)">
                                    {{csrf_field()}}
                                    <input type="hidden" name="redirect" value="{{ url()->current() }}">
                                
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
                                            <button type="submit" class="btn site-btn-global-w-arrow">
                                                {{ trans('site.front.register') }}
                                            </button>
                                        </div>
                                    </div>    
                                
                                    <div class="clearfix"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- end container-->
    </div>
</div>

@stop

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/app.js?v='.time()) }}"></script>
@stop