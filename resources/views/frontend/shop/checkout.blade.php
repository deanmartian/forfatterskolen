@extends('frontend.layout')

@section('title')
<title>Checkout &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
	<style>
		.loading {
			font-size: 20px;
		}

		.loading:after {
			overflow: hidden;
			display: inline-block;
			vertical-align: bottom;
			-webkit-animation: ellipsis steps(4,end) 900ms infinite;
			animation: ellipsis steps(4,end) 900ms infinite;
			content: "\2026"; /* ascii code for the ellipsis character */
			width: 0;
		}

		@keyframes ellipsis {
			to {
				width: 1.25em;
			}
		}

		@-webkit-keyframes ellipsis {
			to {
				width: 1.25em;
			}
		}
	</style>
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
		<form class="form-theme" method="POST" action="{{route('front.course.place_order', ['id' => $course->id])}}"
		id="place_order_form">
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
			

			<?php $hasPaidCourse = false; ?>
			@if( !Auth::guest() )
			<?php
			// check if course bought is not expired yet
	        foreach( Auth::user()->coursesTakenNotOld as $courseTaken ) :
	            if( $courseTaken->package->course->type != "Free" && $courseTaken->is_active ) :
	                $hasPaidCourse = true;
	                break;
	            endif;
	        endforeach;
			?>
			@endif
			<div class="col-sm-12 col-md-4">
				<!-- Payment Details -->
				<div class="panel panel-default no-margin-bottom">
				  <div class="panel-heading"><h4>Kurspakke</h4></div>
				  <div class="panel-body">
					  	@foreach($course->packages as $k => $package)
					  	<?php
                            $full_payment_price = $package->full_payment_price;
                            $months_3_price = $package->months_3_price;
                            $months_6_price = $package->months_6_price;
                          	$months_12_price = $package->months_12_price;

                            $full_payment_sale_price = $package->full_payment_sale_price;
                          	$months_3_sale_price = $package->months_3_sale_price;
                          	$months_6_sale_price = $package->months_6_sale_price;
                          	$months_12_sale_price = $package->months_12_sale_price;

                          $today 			= \Carbon\Carbon::today()->format('Y-m-d');
                          $fromFull 		= \Carbon\Carbon::parse($package->full_payment_sale_price_from)->format('Y-m-d');
                          $toFull 			= \Carbon\Carbon::parse($package->full_payment_sale_price_to)->format('Y-m-d');
                          $isBetweenFull 	= (($today >= $fromFull) && ($today <= $toFull)) ? 1 : 0;

                          $fromMonths3 			= \Carbon\Carbon::parse($package->months_3_sale_price_from)->format('Y-m-d');
                          $toMonths3 			= \Carbon\Carbon::parse($package->months_3_sale_price_to)->format('Y-m-d');
                          $isBetweenMonths3 	= (($today >= $fromMonths3) && ($today <= $toMonths3)) ? 1 : 0;

                          $fromMonths6 			= \Carbon\Carbon::parse($package->months_6_sale_price_from)->format('Y-m-d');
                          $toMonths6 			= \Carbon\Carbon::parse($package->months_6_sale_price_to)->format('Y-m-d');
                          $isBetweenMonths6 	= (($today >= $fromMonths6) && ($today <= $toMonths6)) ? 1 : 0;

                          $fromMonths12 		= \Carbon\Carbon::parse($package->months_12_sale_price_from)->format('Y-m-d');
                          $toMonths12 			= \Carbon\Carbon::parse($package->months_12_sale_price_to)->format('Y-m-d');
                          $isBetweenMonths12 	= (($today >= $fromMonths12) && ($today <= $toMonths12)) ? 1 : 0;

							if( $hasPaidCourse && $package->has_student_discount) {
                                if($course->type == "Single") {
                                    $full_payment_price = $package->full_payment_price - 500;
                                    $months_3_price = $package->months_3_price - 500;
                                    $months_6_price = $package->months_6_price - 500;
                                    $months_12_price = $package->months_12_price - 500;

                                    $full_payment_sale_price = $package->full_payment_sale_price - 500;
                                    $months_3_sale_price = $package->months_3_sale_price - 500;
                                    $months_6_sale_price = $package->months_6_sale_price - 500;
                                    $months_12_sale_price = $package->months_12_sale_price - 500;
								}

                                if($course->type == "Group") {
                                    $full_payment_price = $package->full_payment_price - 1000;
                                    $months_3_price = $package->months_3_price - 1000;
                                    $months_6_price = $package->months_6_price - 1000;
                                    $months_12_price = $package->months_12_price - 1000;

                                    $full_payment_sale_price = $package->full_payment_sale_price - 1000;
                                    $months_3_sale_price = $package->months_3_sale_price - 1000;
                                    $months_6_sale_price = $package->months_6_sale_price - 1000;
                                    $months_12_sale_price = $package->months_12_sale_price - 1000;
								}

							}

					  	/*$full_payment_price = $hasPaidCourse ? $package->full_payment_price - 1500 : $package->full_payment_price;
					  	$months_3_price = $hasPaidCourse ? $package->months_3_price - 1500 : $package->months_3_price;
					  	$months_6_price = $hasPaidCourse ? $package->months_6_price - 1500 : $package->months_6_price;*/
					  	?>
							<div class="package-option">
								<input type="radio" name="package_id"
									   value="{{$package->id}}"
									   data-full_payment_price="{{ FrontendHelpers::currencyFormat($full_payment_price) }}"
									   data-months_3_price="{{ FrontendHelpers::currencyFormat($months_3_price) }}"
									   data-months_6_price="{{ FrontendHelpers::currencyFormat($months_6_price) }}"
									   data-full_payment_price_number="{{ $full_payment_price }}"
									   data-months_3_price_number="{{ $months_3_price }}"
									   data-months_6_price_number="{{ $months_6_price }}"
									   data-months_12_price_number="{{ $months_12_price }}"

									   @if ($isBetweenFull && $package->full_payment_sale_price)
									   		data-full_payment_sale_price = "{{ FrontendHelpers::currencyFormat($full_payment_sale_price) }}"
									   		data-full_payment_sale_price_number = "{{ $full_payment_sale_price }}"
									   @endif

									   @if ($isBetweenMonths3 && $package->months_3_sale_price)
									   		data-months_3_sale_price = "{{ FrontendHelpers::currencyFormat($months_3_sale_price) }}"
									   		data-months_3_sale_price_number = "{{ $months_3_sale_price }}"
									   @endif

									   @if ($isBetweenMonths6 && $package->months_6_sale_price)
									   		data-months_6_sale_price = "{{ FrontendHelpers::currencyFormat($months_6_sale_price) }}"
									   		data-months_6_sale_price_number = "{{ $months_6_sale_price }}"
									   @endif

									   @if ($isBetweenMonths12 && $package->months_12_sale_price)
										   data-months_12_sale_price = "{{ FrontendHelpers::currencyFormat($months_12_sale_price) }}"
										   data-months_12_sale_price_number = "{{ $months_12_sale_price }}"
									   @endif

									   required>
								<label for="{{$package->variation}}">{{$package->variation}} </label>
							</div>
				  		@endforeach

				  </div>
				  </div>

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

				<div class="panel panel-default no-margin-bottom">
					<div class="panel-heading"><h4>Rabattkupong</h4></div>
					<div class="panel-body">
						<input type="text" name="coupon" class="form-control">
					</div>
				</div>
				

				<div class="panel panel-default no-margin-bottom">
				  <div class="panel-heading"><h4>Betalingsplan</h4></div>
				  <div class="panel-body">
					  <div class="row">
						  <div class="col-sm-6" id="paymentPlanContainer">
							{{--@foreach(App\PaymentPlan::orderBy('division', 'asc')->get() as $paymentPlan)
								<div class="payment-option">
									<input type="radio" @if($paymentPlan->plan == 'Full Payment') checked @endif name="payment_plan_id" value="{{$paymentPlan->id}}" data-plan="{{trim($paymentPlan->plan)}}" id="{{$paymentPlan->plan}}" required>
									<label for="{{$paymentPlan->plan}}">{{$paymentPlan->plan}} </label>
								</div>
						  @endforeach--}}
						  </div>
						  <div class="col-sm-6" style="margin-top: 8px" id="splitInvoiceContainer">
							  <b>Månedlig faktura?*</b>
							  <div class="payment-option">
								  <input type="radio" name="split_invoice" value="1" disabled required>
								  <label for="Yes">Ja</label>
							  </div>
							  <div class="payment-option">
								  <input type="radio" name="split_invoice" value="0" disabled required>
								  <label for="No">Nei</label>
							  </div>
						  </div>
					  </div>

					  <div class="row">
						  <div class="col-sm-12 text-center">
							  *Du kan velge om du vil ha faktura en gang i måneden, eller
							  èn faktura der du kan betale inn ønsket beløp innen forfallsdatoen
						  </div>

						  <div class="col-sm-12 margin-top">
							  <input type="checkbox" required> Jeg aksepterer <a href="{{ route('front.terms', 'course-terms') }}"
							  target="_new">kjøpsvilkårene</a>
						  </div>
					  </div>
					<hr />

					<div class="text-center margin-bottom checkout-total">

						@if( $hasPaidCourse && $package->has_student_discount)
							@if($course->type == "Single")
								<strong>Du har en rabatt som elev på Kr 500,00</strong> <br /><br />
							@endif

							@if($course->type == "Group")
								<strong>Du har en rabatt som elev på Kr 1000,00</strong> <br /><br />
							@endif
						@endif

						<h4>Totalt</h4>
						<?php $standard_price = $course->packages->where('variation', 'Standard Kurs')->first(); ?>
						@if( $standard_price )
							<span>
							@if( $hasPaidCourse && $package->has_student_discount)
								{{--check if course is Webinar-pakke and apply 500 only--}}
									@if($course->type == "Single")
										{{ FrontendHelpers::currencyFormat($standard_price->full_payment_price - 500) }}
									@endif

									@if($course->type == "Group")
										{{ FrontendHelpers::currencyFormat($standard_price->full_payment_price - 1000) }}
									@endif
							@else
							{{ FrontendHelpers::currencyFormat($standard_price->full_payment_price) }}
							@endif
							</span>
						@else
						<span>
						@if( $hasPaidCourse && $package->has_student_discount)
								@if($course->type == "Single")
									{{ FrontendHelpers::currencyFormat($course->packages[0]->full_payment_price - 500) }}
								@endif

								@if($course->type == "Group")
									{{ FrontendHelpers::currencyFormat($course->packages[0]->full_payment_price - 1000) }}
								@endif
						@else
						{{ FrontendHelpers::currencyFormat($course->packages[0]->full_payment_price) }}
						@endif
						</span>
						@endif

					</div>

						<div id="discount-wrapper" class="hide text-center">
							<h4>Rabatt</h4>
							<span id="discount-display" style="font-size: 22px"></span>
						</div>

				  	<button type="submit" class="btn btn-theme btn-lg btn-block" id="submitOrder">Bestill</button>
				  </div>
				</div>
			</div>
			
			<div class="clearfix"></div>
		</form>

	</div>

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

<input type="hidden" name="discount_value">
@stop


@section('scripts')
<script>
$(document).ready(function(){

    $("#place_order_form").on('submit',function(){
        $("#processOrderModal").modal('show');
        $("#submitOrder").attr('disabled',true);
	});

    $('a[target^="_new"]').click(function() {
        return openWindow(this.href);
    });


    var course_id = '<?php echo $course->id?>';
    var count_package_change = 0; // used to determine the onload

    setTimeout(function(){
        if ($("div[class=package-option]").find('input[name=package_id]').length > 1) {
            $("div[class=package-option]:nth-child(2)").find('input[name=package_id]').attr('checked', true).trigger('change');
		} else {
            $("div[class=package-option]:nth-child(1)").find('input[name=package_id]').attr('checked', true).trigger('change');
		}
        $('input:radio[name=payment_plan_id]:first').attr('checked', true).trigger('change');
	}, 100);

    $('input[name=package_id]').on('change', function(){
    	var checkout_total = $('.checkout-total');
        $('input:radio[name=split_invoice]').prop('disabled', true);
        $('input:radio[name=split_invoice]').prop('checked', false);
		generatePackagePaymentOption($(this).val());
        count_package_change++;

    	var new_total = 0;

        if ($('input[name=payment_plan_id]:checked').length > 0) {
            var plan = $('input[name=payment_plan_id]:checked').data('plan');
            if( plan == 'Hele beløpet' ) {
                var price = $(this).data('full_payment_price');
				var price_value = $(this).attr('data-full_payment_sale_price_number')
					? $(this).data('full_payment_sale_price_number')
					: $(this).data('full_payment_price_number');
                new_total = price_value;

				 if ($("input[name=discount_value]").val()) {
					 var discount_value = $("input[name=discount_value]").val();
					 new_total = price_value - discount_value;
				 }
            } else if( plan == '3 måneder' ) {
                var price = $(this).data('months_3_price');
                var price_value = $(this).attr('data-months_3_sale_price_number')
                    ? $(this).data('months_3_sale_price_number')
                    : $(this).data('months_3_price_number');
                new_total = price_value;

                if ($("input[name=discount_value]").val()) {
                    var discount_value = $("input[name=discount_value]").val();
                    new_total = price_value - discount_value;
                }
            } else if( plan == '6 måneder' ) {
                var price = $(this).data('months_6_price');
                var price_value = $(this).attr('data-months_6_sale_price_number')
                    ? $(this).data('months_6_sale_price_number')
                    : $(this).data('months_6_price_number');
                new_total = price_value;

                if ($("input[name=discount_value]").val()) {
                    var discount_value = $("input[name=discount_value]").val();
                    new_total = price_value - discount_value;
                }
            }
        } else {
            new_total = $(this).attr('data-full_payment_sale_price_number')
                ? $(this).data('full_payment_sale_price_number')
                : $(this).data('full_payment_price_number');
            if ($("input[name=discount_value]").val()) {
                var discount_value = $("input[name=discount_value]").val();
                var price_value = $(this).attr('data-full_payment_sale_price_number')
                    ? $(this).data('full_payment_sale_price_number')
                    : $(this).data('full_payment_price_number');
                new_total = price_value - discount_value;
            }
		}

        $.get('/format_money/'+new_total, {}, function(){}, 'json').done(function(data){
            var checkout_total = $('.checkout-total');
            checkout_total.find('span').text(data);
        });
    });



    $('select[name=payment_mode_id]').on('change', function(){
        var mode = $('option:selected', this).data('mode');
        if( mode == "Paypal" ) {
            $('input:radio[name=payment_plan_id]').parent().addClass('disabled');
            $('input:radio[name=payment_plan_id]').prop('disabled', true);
            $('input:radio[name=payment_plan_id]').prop('checked', false);
            $('input:radio[name=payment_plan_id]').filter('[id="Hele beløpet"]').prop('checked', true);
            $('input:radio[name=payment_plan_id]').filter('[id="Hele beløpet"]').parent().removeClass('disabled');
            $('input:radio[name=payment_plan_id]').filter('[id="Hele beløpet"]').prop('disabled', false);
	    	var package = $('#package_select option:selected');
            //$('#package_select option:selected').data('full_payment_price');
	        var price = $('input:radio[name=package_id]:checked').data('full_payment_price');
        	$('.checkout-total span').text(price);
            $('input:radio[name=split_invoice]').prop('disabled', true);
        } else {
            $('input:radio[name=payment_plan_id]').parent().removeClass('disabled');
            $('input:radio[name=payment_plan_id]').prop('disabled', false);
        }
    });


    /*$('input[name=payment_plan_id]').change(function(){
    	var checkout_total = $('.checkout-total');
        var plan = $(this).data('plan');
        var new_total = 0;

        if( plan == 'Hele beløpet' ) {
            new_total = $('input[name=package_id]:checked').attr('data-full_payment_sale_price_number')
                ? $('input[name=package_id]:checked').data('full_payment_sale_price_number')
                : $('input[name=package_id]:checked').data('full_payment_price_number');

            var price_value = $('input[name=package_id]:checked').attr('data-full_payment_sale_price_number')
                ? $('input[name=package_id]:checked').data('full_payment_sale_price_number')
                : $('input[name=package_id]:checked').data('full_payment_price_number');

            if ($("input[name=discount_value]").val()) {
                var discount_value = $("input[name=discount_value]").val();
                new_total = price_value - discount_value;
            }
        } else if( plan == '3 måneder' ) {
            new_total = $('input[name=package_id]:checked').attr('data-months_3_sale_price_number')
                ? $('input[name=package_id]:checked').data('months_3_sale_price_number')
                : $('input[name=package_id]:checked').data('months_3_price_number');

            var price_value = $('input[name=package_id]:checked').attr('data-months_3_sale_price_number')
                ? $('input[name=package_id]:checked').data('months_3_sale_price_number')
                : $('input[name=package_id]:checked').data('months_3_price_number');

            if ($("input[name=discount_value]").val()) {
                var discount_value = $("input[name=discount_value]").val();
                new_total = price_value - discount_value;
            }
        } else if( plan == '6 måneder' ) {
            new_total = $('input[name=package_id]:checked').attr('data-months_6_sale_price_number')
                ? $('input[name=package_id]:checked').data('months_6_sale_price_number')
                : $('input[name=package_id]:checked').data('months_6_price_number');

            var price_value = $('input[name=package_id]:checked').attr('data-months_6_sale_price_number')
                ? $('input[name=package_id]:checked').data('months_6_sale_price_number')
                : $('input[name=package_id]:checked').data('months_6_price_number');

            if ($("input[name=discount_value]").val()) {
                var discount_value = $("input[name=discount_value]").val();
                new_total = price_value - discount_value;
            }
        }
        $.get('/format_money/'+new_total, {}, function(){}, 'json').done(function(data){
            var checkout_total = $('.checkout-total');
            checkout_total.find('span').text(data);
        });
    });*/

    //setup before functions
    var typingTimer;                //timer identifier
    var doneTypingInterval = 1000;  //time in ms, 5 second for example
    var $coupon = $('input[name=coupon]');

//on keyup, start the countdown
    $coupon.on('keyup', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(checkDiscount, doneTypingInterval);
    });

//on keydown, clear the countdown
    $coupon.on('keydown', function () {
        clearTimeout(typingTimer);
    });

//user is "finished typing," do something
    function checkDiscount () {
        var data = {coupon: $coupon.val(), package_id: $('input[name=package_id]:checked').val()};
		$.get('/course/'+course_id+'/check_discount', data, function(){}, 'json')
			.fail(function(){
                $("#discount-wrapper").addClass('hide');
			    alert("Invalid Coupon Code.");

                var new_total = 0;

                if ($('input[name=payment_plan_id]:checked').length > 0) {

                    var plan = $('input[name=payment_plan_id]:checked').data('plan');

                    if( plan == 'Hele beløpet' ) {
                        //var price = $('#package_select option:selected').data('full_payment_price_number');
						var price = $('input[name=package_id]:checked').attr('data-full_payment_sale_price_number');
                    } else if( plan == '3 måneder' ) {
                        //var price = $('#package_select option:selected').data('months_3_price_number');
                        var price = $('input[name=package_id]:checked').attr('data-months_3_sale_price_number');
                    } else if( plan == '6 måneder' ) {
                        //var price = $('#package_select option:selected').data('months_6_price_number');
                        var price = $('input[name=package_id]:checked').attr('data-months_6_sale_price_number');
                    }

                    new_total = price + $("input[name=discount_value]").val();
                } else {
                    var price = $('#package_select option:selected').data('full_payment_price_number');

                    new_total = price + $("input[name=discount_value]").val();
                }

                $.get('/format_money/'+new_total, {}, function(){}, 'json').done(function(data){
                    var checkout_total = $('.checkout-total');
                    checkout_total.find('span').text(data);
                });

                $("input[name=discount_value]").val('');

			})
			.done(function(data){
			    $("#discount-wrapper").removeClass('hide');
			    $("#discount-display").text(data.discount_text);
			    $("input[name=discount_value]").val(data.discount);

			    var new_total = 0;

			    if ($('input[name=payment_plan_id]:checked').length > 0) {

                    var plan = $('input[name=payment_plan_id]:checked').data('plan');

                    if( plan == 'Hele beløpet' ) {
                        //var price = $('#package_select option:selected').data('full_payment_price_number');
                        var price = $('input[name=package_id]:checked').attr('data-full_payment_sale_price_number');
                    } else if( plan == '3 måneder' ) {
                        //var price = $('#package_select option:selected').data('months_3_price_number');
                        var price = $('input[name=package_id]:checked').attr('data-months_3_sale_price_number');
                    } else if( plan == '6 måneder' ) {
                        //var price = $('#package_select option:selected').data('months_6_price_number');
                        var price = $('input[name=package_id]:checked').attr('data-months_6_sale_price_number');
                    }

                    new_total = price - data.discount;
				} else {
                    var price = $('#package_select option:selected').data('full_payment_price_number');

                    new_total = price - data.discount;
				}

                $.get('/format_money/'+new_total, {}, function(){}, 'json').done(function(data){
                    var checkout_total = $('.checkout-total');
                    checkout_total.find('span').text(data);
				});
			});
    }

    function generatePackagePaymentOption(package_id){
        var paymentPlanContainer = $("#paymentPlanContainer");
        paymentPlanContainer.empty();

        $.get('/payment-plan-options/'+package_id, {}, function(){}, 'json').done(function(data){
            $.each(data, function (k, v) {
                var checked = '';
                if (k === 0/* && count_package_change === 1*/) {
                    checked = 'checked';
				}

                var paymentOptions = '<div class="payment-option">';
                paymentOptions += '<input type="radio" name="payment_plan_id" value="'+v.id+'" data-plan="'+v.plan+'" id="'+v.plan+'" '+checked+' required onchange="payment_plan_change(this)">';
                paymentOptions += '<label>'+v.plan+'</label>';
                paymentOptions += '</div>';
                paymentPlanContainer.append(paymentOptions);
            });
		});
	}
});

function payment_plan_change(t) {

    var checkout_total = $('.checkout-total');
    var plan = $(t).data('plan');
    var new_total = 0;
    $('input:radio[name=split_invoice]').prop('disabled', false);

    if( plan == 'Hele beløpet' ) {
        new_total = $('input[name=package_id]:checked').attr('data-full_payment_sale_price_number')
            ? $('input[name=package_id]:checked').data('full_payment_sale_price_number')
            : $('input[name=package_id]:checked').data('full_payment_price_number');

        var price_value = $('input[name=package_id]:checked').attr('data-full_payment_sale_price_number')
            ? $('input[name=package_id]:checked').data('full_payment_sale_price_number')
            : $('input[name=package_id]:checked').data('full_payment_price_number');

        if ($("input[name=discount_value]").val()) {
            var discount_value = $("input[name=discount_value]").val();
            new_total = price_value - discount_value;
        }

        $('input:radio[name=split_invoice]').prop('disabled', true);
        $('input:radio[name=split_invoice]').prop('checked', false);
    } else if( plan == '3 måneder' ) {
        new_total = $('input[name=package_id]:checked').attr('data-months_3_sale_price_number')
            ? $('input[name=package_id]:checked').data('months_3_sale_price_number')
            : $('input[name=package_id]:checked').data('months_3_price_number');

        var price_value = $('input[name=package_id]:checked').attr('data-months_3_sale_price_number')
            ? $('input[name=package_id]:checked').data('months_3_sale_price_number')
            : $('input[name=package_id]:checked').data('months_3_price_number');

        if ($("input[name=discount_value]").val()) {
            var discount_value = $("input[name=discount_value]").val();
            new_total = price_value - discount_value;
        }
    } else if( plan == '6 måneder' ) {
        new_total = $('input[name=package_id]:checked').attr('data-months_6_sale_price_number')
            ? $('input[name=package_id]:checked').data('months_6_sale_price_number')
            : $('input[name=package_id]:checked').data('months_6_price_number');

        var price_value = $('input[name=package_id]:checked').attr('data-months_6_sale_price_number')
            ? $('input[name=package_id]:checked').data('months_6_sale_price_number')
            : $('input[name=package_id]:checked').data('months_6_price_number');

        if ($("input[name=discount_value]").val()) {
            var discount_value = $("input[name=discount_value]").val();
            new_total = price_value - discount_value;
        }
    } else if( plan == '12 måneder' ) {
        new_total = $('input[name=package_id]:checked').attr('data-months_12_sale_price_number')
            ? $('input[name=package_id]:checked').data('months_12_sale_price_number')
            : $('input[name=package_id]:checked').data('months_12_price_number');

        var price_value = $('input[name=package_id]:checked').attr('data-months_12_sale_price_number')
            ? $('input[name=package_id]:checked').data('months_12_sale_price_number')
            : $('input[name=package_id]:checked').data('months_12_price_number');

        if ($("input[name=discount_value]").val()) {
            var discount_value = $("input[name=discount_value]").val();
            new_total = price_value - discount_value;
        }
    }
    $.get('/format_money/'+new_total, {}, function(){}, 'json').done(function(data){
        var checkout_total = $('.checkout-total');
        checkout_total.find('span').text(data);
    });
}

function openWindow(url) {

    if (window.innerWidth <= 640) {
        // if width is smaller then 640px, create a temporary a elm that will open the link in new tab
        let a = document.createElement('a');
        a.setAttribute("href", url);
        a.setAttribute("target", "_blank");

        let dispatch = document.createEvent("HTMLEvents");
        dispatch.initEvent("click", true, true);

        a.dispatchEvent(dispatch);
        window.open(url);
    }
    else {
        let width = window.innerWidth * 0.66 ;
        // define the height in
        let height = width * window.innerHeight / window.innerWidth ;
        // Ratio the hight to the width as the user screen ratio
        window.open(url , 'newwindow', 'width=' + width + ', height=' + height + ', top=' + ((window.innerHeight - height) / 2) + ', left=' + ((window.innerWidth - width) / 2));
    }
    return false;
}
</script>
@stop