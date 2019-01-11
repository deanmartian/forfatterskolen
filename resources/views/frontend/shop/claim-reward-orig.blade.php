@extends('frontend.layout')

@section('title')
    <title>Claim Reward &rsaquo; Forfatterskolen</title>
@stop

@section('content')

    <div class="container">
        <div class="row">
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
                                <input type="password" name="password" placeholder="Passord" class="form-control" required>
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
            <div class="col-sm-12">
                <h4>Bestillingsskjema for {{$course->title}}</h4>
            </div>
            <form class="form-theme" method="POST" action="{{route('front.course.claim-reward', ['id' => $course->id])}}"
                  id="place_order_form" onsubmit="disableSubmit(this)">
                {{csrf_field()}}
                <div class="col-sm-12 col-md-8">
                    <div class="panel panel-default">
                        <div class="panel-heading"><h4>Brukerinformasjon</h4></div>
                        <div class="panel-body">
                            <br />
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
                                @else
                                    <div class="col-md-6">
                                        <a href="{{ route('auth.login.google') }}" class="loginBtn loginBtn--google btn">
                                            Logg inn med Google
                                        </a>

                                        <a href="{{ route('auth.login.facebook') }}" class="loginBtn loginBtn--facebook btn">
                                            Logg inn med Facebook
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-sm-12 col-md-4">
                    <!-- Payment Details -->
                    <div class="panel panel-default no-margin-bottom">
                        <div class="panel-heading"><h4>Kurspakke</h4></div>
                        <div class="panel-body">
                            @foreach($course->rewardPackages as $k => $package)
                                <div class="package-option">
                                    <input type="radio" name="package_id" value="{{$package->id}}" required>
                                    <label for="{{$package->variation}}">{{$package->variation}} </label>
                                </div>
                            @endforeach
                        </div>
                    </div> <!-- end kurspakke -->

                    <div class="panel panel-default no-margin-bottom">
                        <div class="panel-heading"><h4>Rabattkupong</h4></div>
                        <div class="panel-body">
                            <input type="text" name="coupon" class="form-control margin-bottom" required>

                            <button type="submit" class="btn btn-theme btn-lg btn-block" id="submitOrder">Claim</button>
                        </div>
                    </div> <!-- end Rabattkupong -->

                </div>

                <div class="clearfix"></div>
            </form>
        </div>
    </div>

@stop

@section('scripts')
    <script>
        $(document).ready(function(){
            setTimeout(function(){
                if ($("div[class=package-option]").find('input[name=package_id]').length > 1) {
                    $("div[class=package-option]:nth-child(2)").find('input[name=package_id]').attr('checked', true).trigger('change');
                } else {
                    $("div[class=package-option]:nth-child(1)").find('input[name=package_id]').attr('checked', true).trigger('change');
                }
                $('input:radio[name=payment_plan_id]:first').attr('checked', true).trigger('change');
            }, 100);
        });
    </script>
@stop
