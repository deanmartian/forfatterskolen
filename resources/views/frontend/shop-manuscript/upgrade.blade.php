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
                            <div class="col-sm-12">
                                <div class="margin-bottom">Allerede elev? Klikk <a href="#" data-toggle="collapse" data-target="#checkoutLogin">her</a> for å logge inn.</div>
                                <form id="checkoutLogin" class="collapse @if($errors->first('login_error')) fade in @endif" action="{{route('frontend.login.checkout.store')}}" method="POST">
                                    {{csrf_field()}}
                                    <div class="row">
                                        <div class="form-group col-sm-3">
                                            <input type="email" name="email" placeholder="Epost-adresse" class="form-control" value="{{old('email')}}" required>
                                            <p style="margin-top: 7px;"><a href="{{ route('auth.login.show') }}?t=passwordreset" tabindex="-1">Glemt Passord?</a></p>
                                        </div>
                                        <div class="form-group col-sm-3">
                                            <input type="password" name="password" placeholder="Password" class="form-control" required>
                                        </div>
                                        <div class="form-group col-sm-3">
                                            <button type="submit" class="btn btn-primary">Login</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif
                        @if ( $errors->any() )
                            <div class="col-sm-12 col-md-8">
                                <div class="alert alert-danger no-bottom-margin">
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>{!! $error !!}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <br />
                            </div>
                        @endif
                        <h2>Bestillingsskjema for {{ $shopManuscript->title }}</h2>
                        <form class="form-theme" method="POST" enctype="multipart/form-data"
                              action="{{ route('front.shop-manuscript.upgrade-manuscript', ['id' => $shopManuscript->id]) }}">
                        {{csrf_field()}}
                        <div class="panel-heading">
                            Brukerinformasjon
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <div id="manuscript-file">
                                    <label for="manuscript" class="control-label">Manuscript file</label>
                                    <input type="file" id="manuscript" class="form-control" name="manuscript"
                                           accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                           application/pdf, application/vnd.oasis.opendocument.text" required>
                                </div>
                                <label for="send_to_email" style="margin-top: 7px">
                                    <input type="checkbox" name="send_to_email" id="send_to_email">
                                    &nbsp;&nbsp;Send til epost
                                </label>
                            </div>
                            <div class="form-group">
                                <label for="email" class="control-label">Epost-adresse</label>
                                <input type="email" id="email" class="form-control" name="email" required
                                       @if(Auth::guest()) value="{{old('email')}}" @else value="{{Auth::user()->email}}"
                                       readonly @endif>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="first_name" class="control-label">Fornavn</label>
                                    <input type="text" id="first_name" class="form-control" name="first_name" required
                                           @if(Auth::guest()) value="{{old('first_name')}}" @else value="{{Auth::user()->first_name}}"
                                           readonly @endif>
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="control-label">Etternavn</label>
                                    <input type="text" id="last_name" class="form-control" name="last_name" required
                                           @if(Auth::guest()) value="{{old('last_name')}}" @else value="{{Auth::user()->last_name}}"
                                           readonly @endif>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="street" class="control-label">Gate</label>
                                <input type="text" id="street" class="form-control" name="street" required
                                       @if(Auth::guest()) value="{{old('last_name')}}"
                                       @else value="{{Auth::user()->address['street']}}" @endif>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="zip" class="control-label">Postnummer</label>
                                    <input type="text" id="zip" class="form-control" name="zip" required
                                           @if(Auth::guest()) value="{{old('zip')}}"
                                           @else value="{{Auth::user()->address['zip']}}" @endif>
                                </div>
                                <div class="col-md-6">
                                    <label for="city" class="control-label">Poststed</label>
                                    <input type="text" id="city" class="form-control" name="city" required
                                           @if(Auth::guest()) value="{{old('city')}}"
                                           @else value="{{Auth::user()->address['city']}}" @endif>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="phone" class="control-label">Telefonnummer</label>
                                    <input type="text" id="phone" class="form-control" name="phone" required
                                           @if(Auth::guest()) value="{{old('phone')}}"
                                           @else value="{{Auth::user()->address['phone']}}" @endif>
                                </div>
                                @if(Auth::guest())
                                    <div class="col-md-6">
                                        <label for="password" class="control-label">Lag ett passord</label>
                                        <input type="password" id="password" class="form-control" name="password" required>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group row">
                                @if(!Auth::guest())
                                    <div class="col-md-6">
                                        <label for="update_address" class="control-label">
                                            <input type="checkbox" name="update_address" id="update_address" checked>
                                            Update Address</label>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div> <!-- end panel-->
                </div> <!-- end col-lg-8-->

                <div class="col-lg-4">

                    <div class="panel panel-default no-margin-bottom">
                        <div class="panel-heading-underlined">
                            Betalings Metode
                        </div>
                        <div class="panel-body px-0 pb-0">
                            <select class="form-control" name="payment_mode_id" required>
                                @foreach(App\PaymentMode::get() as $paymentMode)
                                    <option value="{{$paymentMode->id}}" data-mode="{{ $paymentMode->mode }}">{{$paymentMode->mode}}</option>
                                @endforeach
                            </select>
                            <em><small>Merk: Vi godtar kun full betaling på PAYPAL</small></em>

                            <hr>

                            <div class="margin-bottom checkout-total mt-3">

                                <h3>
                                    Totalt:
                                <span class="theme-text font-barlow-regular">
                                    {{ \App\Http\FrontendHelpers::currencyFormat($upgradeManuscript->price) }}
                                </span>
                                </h3>
                            </div>

                            <button type="submit" class="btn site-btn-global-w-arrow">Bestill</button>
                        </div>
                    </div>
                </div>

                    <div class="clearfix"></div>
                </form>

            </div>

        </div>
    </div>

    @if(Session::has('manuscript_test_error'))
        <div id="manuscriptTestErrorModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <div style="color: red; font-size: 24px"><i class="fa fa-close"></i></div>
                        {!! Session::get('manuscript_test_error') !!}
                    </div>
                </div>
            </div>
        </div>
    @endif

@stop


@section('scripts')
    <script>
        $(document).ready(function(){

            @if(Session::has('manuscript_test_error'))
                $('#manuscriptTestErrorModal').modal('show');
            @endif

            $('input[name=send_to_email]').change(function(){
                if( $(this).is(':checked') ){
                    $('#manuscript-file').fadeOut();
                    $('#manuscript-file #manuscript').prop('required', false);
                } else {
                    $('#manuscript-file').fadeIn();
                    $('#manuscript-file #manuscript').prop('required', true);
                }
            });



            var full_payment_price = '{{ FrontendHelpers::currencyFormat($shopManuscript->full_payment_price) }}';
            var months_3_price = '{{ FrontendHelpers::currencyFormat($shopManuscript->months_3_price) }}';

            $('select[name=payment_mode_id]').on('change', function(){
                var checkout_total = $('.checkout-total');
                var price = full_payment_price;
                var mode = $('option:selected', this).data('mode');
                if( mode == "Paypal" ) {
                    $('input:radio[name=payment_plan_id]').parent().addClass('disabled');
                    $('input:radio[name=payment_plan_id]').prop('disabled', true);
                    $('input:radio[name=payment_plan_id]').prop('checked', false);
                    $('input:radio[name=payment_plan_id]').filter('[id="Hele beløpet"]').prop('checked', true);
                    $('input:radio[name=payment_plan_id]').filter('[id="Hele beløpet"]').parent().removeClass('disabled');
                    $('input:radio[name=payment_plan_id]').filter('[id="Hele beløpet"]').prop('disabled', false);
                    $('.checkout-total span').text(price);
                } else {
                    $('input:radio[name=payment_plan_id]').parent().removeClass('disabled');
                    $('input:radio[name=payment_plan_id]').prop('disabled', false);
                }
            });



            $('input[name=payment_plan_id]').on('change', function(){
                var checkout_total = $('.checkout-total');
                var plan = $(this).data('plan');
                if( plan == 'Hele beløpet' ) {
                    var price = full_payment_price;
                } else if( plan == '3 måneder' ) {
                    var price = months_3_price;
                }
                checkout_total.find('span').text(price);
            });
        });
    </script>
@stop