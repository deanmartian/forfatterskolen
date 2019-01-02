@extends('frontend.layout')

@section('title')
<title>Checkout &rsaquo; Forfatterskolen</title>
@stop

@section('content')
</div>

<div class="container">
	<div class="row">
		@if(Auth::guest())
		<div class="col-sm-12">
			<div class="margin-bottom">Allerede elev? Klikk <a href="#" data-toggle="collapse" data-target="#checkoutLogin">her</a> for å logge inn.</div>
			<form id="checkoutLogin" class="collapse @if($errors->first('login_error')) fade in @endif" action="{{route('frontend.login.checkout.store')}}" method="POST">
				{{csrf_field()}}
				<div class="row">
					<div class="form-group col-sm-3">
						<input type="email" name="email" placeholder="E-postadresse" class="form-control" value="{{old('email')}}" required>
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
		<div class="col-sm-12">
			<h4>Bestillingsskjema for {{ $workshop->title }}</h4>
		</div>
		<form class="form-theme" method="POST" enctype="multipart/form-data" action="{{ route('front.workshop.place_order', ['id' => $workshop->id]) }}"
		onsubmit="disableSubmit(this)">
			{{csrf_field()}}
			<div class="col-sm-12 col-md-8">
				<div class="panel panel-default">
				  <div class="panel-heading"><h4>Brukerinformasjon</h4></div>
				  <div class="panel-body">
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
				  <div class="panel-heading"><h4>Allergier</h4></div>
				  <div class="panel-body">
				  	<select class="form-control" name="menu_id" required>
				  	@foreach($workshop->menus as $menu)
						<option value="{{$menu->id}}">{{$menu->title}}</option>
				  	@endforeach
				  	</select>
					  <?php
					  	$notes_placeholder = 'skriv her om du har noen allergier eller andre hensyn vi trenger å vite om før skriveverkstedet'
					  ?>
				  	<textarea class="form-control margin-top" name="notes" placeholder="{{ $notes_placeholder }}" rows="4"></textarea>
				</div>
				</div>

				<div class="panel panel-default no-margin-bottom">
				  <div class="panel-heading"><h4>Betalingsmetode</h4></div>
				  <div class="panel-body" style="padding-bottom: 0">
				  	<select class="form-control" name="payment_mode_id" required>
				  	@foreach(App\PaymentMode::get() as $paymentMode)
						<option value="{{$paymentMode->id}}" data-mode="{{ $paymentMode->mode }}">{{$paymentMode->mode}}</option>
				  	@endforeach
				  	</select>
				  	<em><small>Merk: Vi godtar kun full betaling på PAYPAL</small></em>

					  <div class="col-sm-12 margin-top">
						  <input type="checkbox" required> Jeg aksepterer
						  <a href="{{ route('front.terms', 'workshop-terms') }}"
							 target="_blank">kjøpsvilkårene</a>
					  </div>

					  <hr>

				   </div>
					<div class="text-center margin-bottom checkout-total">
						<?php
                        $courseWorkshops = 0;
                        if (Auth::user()) {
                            foreach( Auth::user()->coursesTaken as $courseTaken ) {
                                $courseWorkshops += $courseTaken->package->workshops;
                            }
						}
						?>
						<h4>Totalt</h4>
							@if (Auth::user())
								@if (Auth::user()->workshopsTaken->count() == 0 && $courseWorkshops > 0)
									<span>{{ FrontendHelpers::currencyFormat($workshop->price * 0) }}</span>
								@else
									<span>{{ FrontendHelpers::currencyFormat($workshop->price) }}</span>
								@endif
							@else
								<span>{{ FrontendHelpers::currencyFormat($workshop->price) }}</span>
							@endif
					</div>
				  	<button type="submit" class="btn btn-theme btn-lg btn-block">Bestill</button>
				</div>

			</div>
			
			<div class="clearfix"></div>
		</form>

	</div>

</div>

@stop


@section('scripts')
<script>
$(document).ready(function(){

	$('input[name=send_to_email]').change(function(){
		if( $(this).is(':checked') ){
			$('#manuscript-file').fadeOut();
			$('#manuscript-file #manuscript').prop('required', false);
		} else {
			$('#manuscript-file').fadeIn();
			$('#manuscript-file #manuscript').prop('required', true);
		}
	});

	var price = '{{ FrontendHelpers::currencyFormat($workshop->price) }}';
	var split_payment_price = '{{ FrontendHelpers::currencyFormat($workshop->split_payment_price) }}';




    $('select[name=payment_mode_id]').on('change', function(){
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


    $('input[name=payment_plan_id]').change(function(){
    	var checkout_total = $('.checkout-total');
        var plan = $(this).data('plan');
        if( plan == 'Hele beløpet' ) {
        	checkout_total.find('span').text(price);
        } else {
        	checkout_total.find('span').text(split_payment_price);
        }
    });
});
</script>
@stop