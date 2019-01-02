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
			<h4>Bestillingsskjema for {{ $shopManuscript->title }}</h4>
		</div>
		<form class="form-theme" method="POST" enctype="multipart/form-data" action="{{ route('front.shop-manuscript.place_order', ['id' => $shopManuscript->id]) }}"
			  onsubmit="processCheckout(this)">
			{{csrf_field()}}
			<div class="col-sm-12 col-md-8">
				<div class="panel panel-default">
				  <div class="panel-heading"><h4>Brukerinformasjon</h4></div>
				  <div class="panel-body">
				  	<div class="form-group">
						@if(Session::has('manuscript_test_error'))
							<div class="alert alert-danger">
								<ul>
									<li>{{ Session::get('manuscript_test_error') }}</li>
								</ul>
							</div>
						@endif

				  		<div id="manuscript-file">
					  		<label for="manuscript" class="control-label">Last opp manuskriptet</label>
					  		<input type="file" id="manuscript" class="form-control" name="manuscript" accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text" required>
						</div>
				  		<label for="send_to_email" style="margin-top: 7px"><input type="checkbox" name="send_to_email" id="send_to_email">&nbsp;&nbsp;Send til E-post</label>
				  	</div>
					  <div class="form-group">
						  <label for="">Sjanger</label>
						  <select class="form-control" name="genre" required>
							  <option value="" disabled="disabled" selected>Velg sjanger</option>
							  @foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
								  <option value="{{ $type['id'] }}" @if (old('genre') == $type['id']) selected @endif> {{ $type['option'] }} </option>
							  @endforeach
						  </select>
					  </div>
					  <!-- check if the manuscript is not the start -->
					  @if($shopManuscript->id != 9)
					  <div class="form-group">
						  <label for="">Synopsis (valgfritt)</label>
						  <input type="file" class="form-control" name="synopsis" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
					  </div>
					  @endif
					  <div class="form-group">
						  <label for="">Noen ord om manuset (valgfritt)</label>
						  <textarea name="description" id="" cols="30" rows="10" class="form-control">{{ old('description') }}</textarea>
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
				  <div class="panel-heading"><h4>Betalingsmetode</h4></div>
				  <div class="panel-body">
				  	<select class="form-control" name="payment_mode_id" required>
				  	@foreach(App\PaymentMode::get() as $paymentMode)
						<option value="{{$paymentMode->id}}" data-mode="{{ $paymentMode->mode }}">{{$paymentMode->mode}}</option>
				  	@endforeach
				  	</select>
				  	<em><small>Merk: Vi godtar kun full betaling på PAYPAL</small></em>
				   </div>
				</div>
				<?php $hasPaidCourse = false; ?>
				@if( !Auth::guest() )
				<?php  
		        foreach( Auth::user()->coursesTaken as $courseTaken ) :
		            if( $courseTaken->package->course->type != "Free" && $courseTaken->is_active ) :
		                $hasPaidCourse = true;
		                break;
		            endif;
		        endforeach;
				?>
				@endif
				<div class="panel panel-default no-margin-bottom">
				  <div class="panel-heading"><h4>Betalingsplan</h4></div>
				  <div class="panel-body">
				  	@foreach(App\PaymentPlan::orderBy('division', 'asc')->where('id', '<>', 4)->where('id','!=', 7)->where('id','!=', 9)->get() as $paymentPlan)
						<div class="payment-option">
							<input type="radio" @if($paymentPlan->plan == 'Hele beløpet') checked @endif name="payment_plan_id" value="{{$paymentPlan->id}}" data-plan="{{ $paymentPlan->plan }}" id="{{$paymentPlan->plan}}" required>
							<label for="{{$paymentPlan->plan}}">{{$paymentPlan->plan}} </label>
						</div>
				  	@endforeach

						<div class="col-sm-12 no-left-padding">
							<input type="checkbox" required> Jeg aksepterer
							<a href="{{ route('front.terms', 'manuscript-terms') }}"
							   target="_blank">kjøpsvilkårene</a>
						</div>

					<hr />
					<div class="text-center margin-bottom checkout-total">

						@if( $hasPaidCourse )
						<strong>Du har en elevrabatt på 5%</strong> <br /><br />
						@endif

						<h4>Totalt</h4>
						<span>{{ FrontendHelpers::currencyFormat($hasPaidCourse ? $shopManuscript->full_payment_price - ($shopManuscript->full_payment_price * 0.05) : $shopManuscript->full_payment_price) }}</span>
					</div>
				  	<button type="submit" class="btn btn-theme btn-lg btn-block" id="proceed_checkout">
						<i class="fa fa-spinner fa-pulse display-none"></i>
						Bestill</button>
				  </div>
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

	$("#proceed_checkout").click(function(e){
	    //e.preventDefault();
	    //$(this).find('.fa').removeClass('display-none');
	   // $(this).attr('disabled','disabled');
	    //$(this).closest("form")
	});



    var full_payment_price = '{{ FrontendHelpers::currencyFormat($hasPaidCourse ? $shopManuscript->full_payment_price - ($shopManuscript->full_payment_price * 0.05) : $shopManuscript->full_payment_price) }}';
    var months_3_price = '{{ FrontendHelpers::currencyFormat($hasPaidCourse ? $shopManuscript->months_3_price - ($shopManuscript->months_3_price * 0.05) : $shopManuscript->months_3_price) }}';

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

function processCheckout(t) {
    let checkout_btn = $(t).find("#proceed_checkout");
    checkout_btn.find('.fa').removeClass('display-none');
    checkout_btn.attr('disabled','disabled');
}
</script>
@stop