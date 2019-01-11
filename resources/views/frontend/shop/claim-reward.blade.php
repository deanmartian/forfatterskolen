@extends('frontend.layout')

@section('title')
    <title>Claim Reward &rsaquo; Forfatterskolen</title>
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
												Er du allerede registrert hos oss må du logge inn her
											</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-4">
                                        <input type="email" name="email" placeholder="Epost-adresse" class="form-control" value="{{old('email')}}" required>
                                        <p style="margin-top: 7px;"><a href="{{ route('auth.login.show') }}?t=passwordreset" tabindex="-1">Glemt Passord?</a></p>
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <input type="password" name="password" placeholder="Passord" class="form-control" required>
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <button type="submit" class="btn site-btn-global">Login</button>
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

                        <form class="form-theme" method="POST" action="{{route('front.course.claim-reward', ['id' => $course->id])}}"
                              id="place_order_form">
                            {{csrf_field()}}
                            <h2>Bestillingsskjema for {{$course->title}}</h2>
                            <div class="panel-heading">Brukerinformasjon</div>
                            <div class="panel-body px-0">
                                <div class="form-group">
                                    <label for="email" class="control-label">E-postadresse</label>
                                    <input type="email" id="email" class="form-control large-input" name="email" required
                                           @if(Auth::guest()) value="{{old('email')}}" @else value="{{Auth::user()->email}}"
                                           readonly @endif placeholder="E-postadresse">
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="first_name" class="control-label">Fornavn</label>
                                        <input type="text" id="first_name" class="form-control large-input" name="first_name" required
                                               @if(Auth::guest()) value="{{old('first_name')}}" @else
                                               value="{{Auth::user()->first_name}}" readonly @endif placeholder="Fornavn">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="last_name" class="control-label">Etternavn</label>
                                        <input type="text" id="last_name" class="form-control large-input" name="last_name" required
                                               @if(Auth::guest()) value="{{old('last_name')}}" @else
                                               value="{{Auth::user()->last_name}}" readonly @endif placeholder="Etternavn">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="street" class="control-label">Gate</label>
                                    <input type="text" id="street" class="form-control large-input" name="street" required
                                           @if(Auth::guest()) value="{{old('last_name')}}"
                                           @else value="{{Auth::user()->address['street']}}" @endif>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="zip" class="control-label">Postnummer</label>
                                        <input type="text" id="zip" class="form-control large-input" name="zip" required
                                               @if(Auth::guest()) value="{{old('zip')}}"
                                               @else value="{{Auth::user()->address['zip']}}" @endif>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="city" class="control-label">Poststed</label>
                                        <input type="text" id="city" class="form-control" name="city" required @if(Auth::guest()) value="{{old('city')}}" @else value="{{Auth::user()->address['city']}}" @endif>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="phone" class="control-label">Telefonnummer</label>
                                        <input type="text" id="phone" class="form-control large-input" name="phone" required
                                               @if(Auth::guest()) value="{{old('phone')}}"
                                               @else value="{{Auth::user()->address['phone']}}" @endif>
                                    </div>
                                    @if(Auth::guest())
                                        <div class="col-md-6">
                                            <label for="password" class="control-label">Lag et passord</label>
                                            <input type="password" id="password" class="form-control large-input"
                                                   name="password" required>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group row">
                                    @if(!Auth::guest())
                                        <div class="col-md-6 custom-checkbox">
                                            <input type="checkbox" name="update_address" id="update_address" checked>
                                            <label for="update_address" class="control-label">Update Address</label>
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
                            </div> <!-- end panel-body-->
                    </div> <!-- end panel-->
                </div> <!-- end col-lg-8 -->
                <div class="col-lg-4">
                    <div class="panel panel-default mb-0">
                        <div class="panel-heading-underlined pt-0">Kurspakke</div>
                        <div class="panel-body px-0 pb-0">
                            @foreach($course->rewardPackages as $k => $package)
                                <div class="package-option custom-radio">
                                    <input type="radio" name="package_id" value="{{$package->id}}" id="{{ $package->variation }}" required>
                                    <label for="{{$package->variation}}">{{$package->variation}} </label>
                                </div>
                            @endforeach
                        </div>

                        <div class="panel-heading-underlined">Rabattkupong</div>
                        <div class="panel-body px-0 pb-4">
                            <input type="text" name="coupon" class="form-control">
                        </div>

                        <button type="submit" class="btn site-btn-global-w-arrow" id="submitOrder">Claim</button>
                    </div>
                </div> <!-- end col-lg-4 -->
                </form>
            </div> <!-- end row -->
        </div> <!-- end container -->
    </div>

    <div id="processOrderModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body">
                    <p class="text-center">
                        Din ordre blir behandlet
                    </p>

                    <div class="loading" style="margin-left: 60px;">Vennligst vent</div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('scripts')
    <script>
        $(document).ready(function(){

            $("#place_order_form").on('submit',function(){
                $("#processOrderModal").modal('show');
                $("#submitOrder").attr('disabled',true);
            });

            setTimeout(function(){
                if ($(".package-option").find('input[name=package_id]').length > 1) {
                    $(".package-option:nth-child(2)").find('input[name=package_id]').attr('checked', true).trigger('change');
                } else {
                    $(".package-option:nth-child(1)").find('input[name=package_id]').attr('checked', true).trigger('change');
                }
                $('input:radio[name=payment_plan_id]:first').attr('checked', true).trigger('change');
            }, 100);
        });
    </script>
@stop
