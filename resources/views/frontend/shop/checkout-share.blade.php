@extends('frontend.layout')

@section('title')
    <title>Checkout &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="checkout-page">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="panel panel-default">
                        @if(Auth::guest())
                            <form id="checkoutLogin" action="{{route('frontend.login.checkout.store')}}" method="POST">
                                {{csrf_field()}}
                                <div class="row">
                                    <div class="col-sm-12">
                                        <span>
                                            {{ trans('site.front.form.already-registered-text') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-4">
                                        <input type="email" name="email" placeholder="{{ trans('site.front.form.email-address') }}"
                                               class="form-control" value="{{old('email')}}" required>
                                        <p style="margin-top: 7px;">
                                            <a href="{{ route('auth.login.show') }}?t=passwordreset" tabindex="-1">
                                                {{ trans('site.front.form.reset-password') }}?
                                            </a>
                                        </p>
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <input type="password" name="password" placeholder="{{ trans('site.front.form.password')}}"
                                               class="form-control" required>
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <button type="submit" class="btn site-btn-global">
                                            {{ trans('site.front.form.login')}}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endif

                        @if ( $errors->any() )
                            <div class="col-sm-12">
                                <div class="alert alert-danger mb-0">
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>{!! $error !!}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <br />
                            </div>
                        @endif
                        <form class="form-theme" method="POST" action=""
                              id="place_order_form">
                            {{csrf_field()}}
                            <h2>
                                {{ str_replace('_title_', $course->title, trans('site.front.form.book-form-for')) }}
                            </h2>
                            <div class="panel-heading">
                                {{ trans('site.front.form.user-information') }}
                            </div>
                            <div class="panel-body px-0">
                                <div class="form-group">
                                    <label for="email" class="control-label">
                                        {{ trans('site.front.form.email-address') }}
                                    </label>
                                    <input type="email" id="email" class="form-control large-input" name="email" required
                                           @if(Auth::guest()) value="{{old('email')}}" @else value="{{Auth::user()->email}}"
                                           readonly @endif placeholder="{{ trans('site.front.form.email-address') }}">
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="first_name" class="control-label">
                                            {{ trans('site.front.form.first-name') }}
                                        </label>
                                        <input type="text" id="first_name" class="form-control large-input" name="first_name" required
                                               @if(Auth::guest()) value="{{old('first_name')}}" @else
                                               value="{{Auth::user()->first_name}}" readonly @endif
                                               placeholder="{{ trans('site.front.form.first-name') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="last_name" class="control-label">
                                            {{ trans('site.front.form.last-name') }}
                                        </label>
                                        <input type="text" id="last_name" class="form-control large-input" name="last_name" required
                                               @if(Auth::guest()) value="{{old('last_name')}}" @else
                                               value="{{Auth::user()->last_name}}" readonly @endif
                                               placeholder="{{ trans('site.front.form.last-name') }}">
                                    </div>
                                </div>

                                @if(Auth::guest())
                                    <div class="form-group">
                                        <label for="password" class="control-label">
                                            {{ trans('site.front.form.create-password') }}
                                        </label>
                                        <input type="password" id="password" class="form-control large-input"
                                               name="password" required>
                                    </div>
                                @endif
                            </div> <!-- end panel-body -->
                    </div>
                </div> <!-- end col-lg-8 -->

                <div class="col-lg-4">
                    <!-- Payment Details -->
                    <div class="panel panel-default mb-0">
                        <div class="panel-heading-underlined pt-0">
                            {{ trans('site.front.form.course-package') }}
                        </div>
                        <div class="panel-body px-0 pb-0">
                            <div class="package-option custom-radio">
                                <input type="radio" name="package_id"
                                       id="{{ $package->variation }}"
                                       value="{{$package->id}}" checked
                                       required>
                                <label for="{{$package->variation}}">{{$package->variation}} </label>
                            </div>

                            <div id="price-wrapper">
                                <h3 class="mb-0">{{ trans('site.front.price') }}:
                                    <span id="price-display" class="theme-text font-barlow-regular">
                                    {{ \App\Http\FrontendHelpers::currencyFormat($package->full_payment_price) }}</span>
                                </h3>
                            </div>

                            <div id="discount-wrapper">
                                <h3 class="mb-0 mt-2">{{ trans('site.front.your-discount') }}:
                                    <span id="discount-display" class="theme-text font-barlow-regular">
                                        {{ \App\Http\FrontendHelpers::currencyFormat($package->full_payment_price) }}
									</span>
                                </h3>
                            </div>

                            <h3 class="mt-2">{{ trans('site.front.total') }}:
                                <span class="theme-text font-barlow-regular">
                                    {{ \App\Http\FrontendHelpers::currencyFormat(0) }}
                                </span>
                            </h3>

                            <button type="submit" class="btn site-btn-global-w-arrow mt-2" id="submitOrder">
                                {{ trans('site.front.buy') }}
                            </button>
                        </div>
                    </div>
                </div> <!-- end col-lg-4 -->
                </form>

            </div> <!-- end row -->
        </div> <!-- end container -->
    </div> <!-- end checkout-page -->
@stop