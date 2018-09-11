@extends('frontend.layout')

@section('title')
    <title>Checkout &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="container">
        <div class="row">

            @if(Session::has('compute_manuscript'))
                <?php
                $data = Session::get('data');
                ?>
            @endif

            @if(Auth::guest())
                <div class="col-sm-12">
                    <div class="margin-bottom">Allerede elev? Klikk <a href="#" data-toggle="collapse" data-target="#checkoutLogin">her</a> for å logge inn.</div>
                    <form id="checkoutLogin" class="collapse @if($errors->first('login_error')) fade in @endif" action="{{route('frontend.login.checkout.store')}}" method="POST">
                        {{csrf_field()}}
                        <div class="row">
                            <div class="form-group col-sm-3 no-bottom-margin">
                                <input type="email" name="email" placeholder="Epost-adresse" class="form-control" value="{{old('email')}}" required>
                                <p style="margin-top: 7px;"><a href="{{ route('auth.login.show') }}?t=passwordreset" tabindex="-1">Glemt Passord?</a></p>
                            </div>
                            <div class="form-group col-sm-3">
                                <input type="password" name="password" placeholder="Passord" class="form-control" required>
                            </div>
                            <div class="form-group col-sm-3">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </div>

                        <div>
                            <a href="{{ route('auth.login.google') }}" class="loginBtn loginBtn--google btn">
                                Logg inn med Google
                            </a>

                            <div class="clearfix"></div>

                            <a href="{{ route('auth.login.facebook') }}" class="loginBtn loginBtn--facebook btn">
                                Logg inn med Facebook
                            </a>
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

                <div class="col-sm-12">
                    <h4>Bestillingsskjema for {{ $data['title'] }}</h4>
                </div>

                <form action="" method="post" id="add-on-form" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="file" class="hidden" name="manuscript"
                           accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                </form>

            <form class="form-theme" method="POST" action="{{ route('front.coaching-timer-place-order', $data['plan_id']) }}">
                {{ csrf_field() }}
                <input type="hidden" name="file_location" value="{{ $data['file_location'] }}">
                <input type="hidden" name="price" value="{{ $data['price'] }}">
                <div class="col-sm-12 col-md-8">
                    <div class="panel panel-default">
                        <div class="panel-heading"><h4>Brukerinformasjon</h4></div>
                        <div class="panel-body">

                            <div class="form-group">
                                <div id="manuscript-file">
                                    <label for="manuscript" class="control-label">Last opp manuskriptet</label>
                                    <input type="text" readonly class="form-control" placeholder="Velg et dokument å laste opp"
                                           value="{{ $data['file_name'] }}"
                                           id="select-document">
                                </div>
                                <button type="button" class="margin-top btn btn-theme" id="submit-add-on">Tillegg</button>
                                @if(Session::has('compute_manuscript'))
                                    <a href="{{ route('front.coaching-timer-checkout', $data['plan_id']) }}" class="btn btn-default margin-top">Cancel</a>
                                @endif
                            </div>

                            <div class="form-group">
                                (ønsker du at redakøren skal lese manuset ditt før timen, kan du laste opp manus her)
                                tillegg i prisen
                            </div>

                            <div class="row mb-25">
                                <div class="col-sm-4">
                                    <label>Ønsket dato og tid</label>
                                    <input type="datetime-local" class="form-control" name="suggested_date[]" required>
                                </div>
                                <div class="col-sm-4">
                                    <label>Ønsket dato og tid</label>
                                    <input type="datetime-local" class="form-control" name="suggested_date[]" required>
                                </div>
                                <div class="col-sm-4">
                                    <label>Ønsket dato og tid</label>
                                    <input type="datetime-local" class="form-control" name="suggested_date[]" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="">Skriv litt her om hva du vil ha hjelp til</label>
                                <textarea name="help_with" id="" cols="30" rows="10" class="form-control"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="email" class="control-label">E-postadresse</label>
                                <input type="email" id="email" class="form-control" name="email" required @if(Auth::guest()) value="{{old('email')}}" @else value="{{Auth::user()->email}}" readonly @endif>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="first_name" class="control-label">Fornavn</label>
                                    <input type="text" id="first_name" class="form-control" name="first_name" required @if(Auth::guest()) value="{{old('first_name')}}" @else value="{{Auth::user()->first_name}}" readonly @endif>
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="control-label">Etternavn</label>
                                    <input type="text" id="last_name" class="form-control" name="last_name" required @if(Auth::guest()) value="{{old('last_name')}}" @else value="{{Auth::user()->last_name}}" readonly @endif>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="street" class="control-label">Gate</label>
                                <input type="text" id="street" class="form-control" name="street" required @if(Auth::guest()) value="{{old('last_name')}}" @else value="{{Auth::user()->address['street']}}" @endif>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="zip" class="control-label">Postnummer</label>
                                    <input type="text" id="zip" class="form-control" name="zip" required @if(Auth::guest()) value="{{old('zip')}}" @else value="{{Auth::user()->address['zip']}}" @endif>
                                </div>
                                <div class="col-md-6">
                                    <label for="city" class="control-label">Poststed</label>
                                    <input type="text" id="city" class="form-control" name="city" required @if(Auth::guest()) value="{{old('city')}}" @else value="{{Auth::user()->address['city']}}" @endif>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="phone" class="control-label">Telefonnummer</label>
                                    <input type="text" id="phone" class="form-control" name="phone" required @if(Auth::guest()) value="{{old('phone')}}" @else value="{{Auth::user()->address['phone']}}" @endif>
                                </div>
                                @if(Auth::guest())
                                    <div class="col-md-6">
                                        <label for="password" class="control-label">Lag et passord</label>
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
                    </div>
                </div>

                <div class="col-sm-12 col-md-4">
                    <div class="panel panel-default no-margin-bottom">
                        <div class="panel-heading"><h4>Betalings Metode</h4></div>
                        <div class="panel-body">
                            <select class="form-control" name="payment_mode_id" required>
                                @foreach(App\PaymentMode::get() as $paymentMode)
                                    <option value="{{$paymentMode->id}}" data-mode="{{ $paymentMode->mode }}">{{$paymentMode->mode}}</option>
                                @endforeach
                            </select>
                            <em><small>Merk: Vi godtar kun full betaling på PAYPAL</small></em>

                            <div class="col-sm-12 no-left-padding">
                                <input type="checkbox" required> I agree to the
                                <a href="{{ route('front.terms', 'coaching-terms') }}"
                                   target="_blank">terms and conditions</a>
                            </div>

                            <hr>

                            <div class="text-center margin-bottom checkout-total">

                                <h4>Totalt</h4>
                                <span>{{ \App\Http\FrontendHelpers::currencyFormat($data['price']) }}</span>
                            </div>

                            <button type="submit" class="btn btn-theme btn-lg btn-block" id="process-order">Bestill</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(Session::has('compute_manuscript'))
        <div id="computeManuscriptModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        {!! Session::get('compute_manuscript') !!}
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop

@section('scripts')
    <script>
        $(document).ready(function(){

            @if(Session::has('compute_manuscript'))
                $('#computeManuscriptModal').modal('show');
            @endif

            var form = $('#add-on-form');
            $("#select-document").click(function(){
                form.find('input[type=file]').click();
            });
            form.find('input[type=file]').on('change', function(){
                var file = $(this).val().split('\\').pop();
                $("#select-document").val(file);
                if (file) {
                    $("#process-order").attr('disabled', 'disabled');
                }
            });

            $("#submit-add-on").click(function(e){
                e.preventDefault();
                form.submit();
            });
        });
    </script>
@stop