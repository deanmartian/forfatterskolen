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
                            {{--<div>
                                Allerede elev? Klikk <a href="#" data-toggle="collapse" data-target="#checkoutLogin"
                                class="font-barlow-regular">her</a> for å logge inn.
                            </div>
                            <form id="checkoutLogin" class="collapse @if($errors->first('login_error')) fade in @endif" action="{{route('frontend.login.checkout.store')}}" method="POST">--}}
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
                                            <a href="{{ route('auth.login.show') }}?t=passwordreset"
                                                                       tabindex="-1">
                                                {{ trans('site.front.form.reset-password') }}?
                                            </a>
                                        </p>
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <input type="password" name="password" placeholder="{{ trans('site.front.form.password') }}"
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

                        <form action="" method="post" id="add-on-form" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="file" class="hidden" name="manuscript"
                                   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                        </form>

                        <form class="form-theme" method="POST" action="{{ route('front.other-service-place_order') }}"
                        id="place_order_form">
                            {{csrf_field()}}
                            <input type="hidden" name="file_location" value="{{ $data['file_location'] }}">
                            <input type="hidden" name="price" value="{{ $data['price'] }}">

                            <h2>
                                {{ str_replace('_title_', $data['title'], trans('site.front.form.book-form-for')) }}
                            </h2>
                            <div class="panel-heading">
                                {{ trans('site.front.form.user-information') }}
                            </div>
                            <div class="panel-body px-0">
                                <div class="form-group">
                                    <div id="manuscript-file">
                                        <label for="manuscript" class="control-label">
                                            {{ trans('site.front.form.upload-manuscript') }}
                                        </label>
                                    </div>
                                    <div class="input-group mb-3">
                                        <input type="text" readonly class="form-control"
                                               placeholder="{{ trans('site.front.form.select-document-to-upload') }}"
                                               value="{{ $data['file_name'] }}"
                                               id="select-document">
                                        <div class="input-group-append">
                                            <button class="btn btn-common-red btn-common-padding" type="button"
                                                    id="upload-btn">
                                                {{ trans('site.front.upload') }}
                                            </button>
                                        </div>
                                    </div>
                                    <button type="button" class="margin-top btn btn-theme hidden" id="submit-add-on">
                                        {{ trans('site.front.submit') }}
                                    </button>
                                    @if($data['price'])
                                        <a href="{{ route('front.other-service-checkout', ['plan' => 1, 'has_data' => 0]) }}"
                                           class="btn btn-default btn-common-padding">
                                            {{ trans('site.front.correction.fjem') }}
                                        </a>
                                    @endif
                                </div>

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
                                <div class="form-group">
                                    <label for="street" class="control-label">
                                        {{ trans('site.front.form.street') }}
                                    </label>
                                    <input type="text" id="street" class="form-control large-input" name="street" required
                                           @if(Auth::guest()) value="{{old('last_name')}}"
                                           @else value="{{Auth::user()->address['street']}}" @endif>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="zip" class="control-label">
                                            {{ trans('site.front.form.zip') }}
                                        </label>
                                        <input type="text" id="zip" class="form-control large-input" name="zip" required
                                               @if(Auth::guest()) value="{{old('zip')}}"
                                               @else value="{{Auth::user()->address['zip']}}" @endif>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="city" class="control-label">
                                            {{ trans('site.front.form.city') }}
                                        </label>
                                        <input type="text" id="city" class="form-control" name="city" required @if(Auth::guest()) value="{{old('city')}}" @else value="{{Auth::user()->address['city']}}" @endif>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="phone" class="control-label">
                                            {{ trans('site.front.form.phone-number') }}
                                        </label>
                                        <input type="text" id="phone" class="form-control large-input" name="phone" required
                                               @if(Auth::guest()) value="{{old('phone')}}"
                                               @else value="{{Auth::user()->address['phone']}}" @endif>
                                    </div>
                                    @if(Auth::guest())
                                        <div class="col-md-6">
                                            <label for="password" class="control-label">
                                                {{ trans('site.front.form.create-password') }}
                                            </label>
                                            <input type="password" id="password" class="form-control large-input"
                                                   name="password" required>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group row">

                                </div>
                            </div> <!-- end panel-body -->
                    </div> <!-- end panel -->
                </div> <!-- end col-lg-8 -->

                <div class="col-lg-4">
                    <div class="panel panel-default mb-0">
                        <div class="panel-heading-underlined">
                            {{ trans('site.front.form.payment-method') }}
                        </div>
                        <div class="panel-body px-0 pb-0">
                            <select class="form-control" name="payment_mode_id" required data-size="15">
                                @foreach(\App\Http\FrontendHelpers::paymentModes() as $paymentMode)
                                    <option value="{{$paymentMode->id}}" data-mode="{{ $paymentMode->mode }}">{{$paymentMode->mode}}</option>
                                @endforeach
                            </select>
                            <em><small>{{ trans('site.front.coaching-timer.payment-note') }}</small></em>
                        </div>

                        <div class="margin-bottom checkout-total mt-3">
                            <h3>{{ trans('site.front.total') }}:
                                <span class="theme-text font-barlow-regular">
                                    {{ \App\Http\FrontendHelpers::currencyFormat($data['price']) }}
                                </span>
                            </h3>
                        </div>

                        @if($data['price'])
                            <button type="submit" class="btn site-btn-global-w-arrow" id="process-order"
                            {{ $data['price'] < 484 ? 'disabled' : '' }}>
                                <i class="fa fa-spinner fa-pulse d-none"></i>
                                {{ trans('site.front.buy') }}
                            </button>
                        @else
                            {{ trans('site.front.correction.no-price-message') }}
                        @endif
                    </div>
                </div>
                </form>
            </div> <!-- end row -->
        </div> <!-- end container -->
    </div>

@stop

@section('scripts')
    <script>
        $(document).ready(function(){

            @if(Session::has('compute_manuscript'))
            $('#computeManuscriptModal').modal('show');
            @endif

            let form = $('#add-on-form');
            $("#select-document").click(function(){
                form.find('input[type=file]').click();
            });

            $("#upload-btn").click(function(){
                form.find('input[type=file]').click();
            });

            form.find('input[type=file]').on('change', function(){
                var file = $(this).val().split('\\').pop();
                $("#select-document").val(file);
                if (file) {
                    $("#submit-add-on").trigger('click');
                    $("#process-order").attr('disabled', 'disabled');
                }
            });

            $("#submit-add-on").click(function(e){
                e.preventDefault();
                form.submit();
            });

            $("#place_order_form").on('submit',function(){
                $("#process-order").attr('disabled','disabled').find('.fa').removeClass('d-none');
            });
        });
    </script>
@stop