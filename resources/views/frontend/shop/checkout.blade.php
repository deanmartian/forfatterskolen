@extends('frontend.layout')

@section('title')
	<title>Checkout &rsaquo; Forfatterskolen</title>
@stop

@section('content')

	<div class="checkout-page" data-bg="https://www.forfatterskolen.no/images-new/checkout-bg.png">
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
										<p style="margin-top: 7px;"><a href="{{ route('auth.login.show') }}?t=passwordreset"
																	   tabindex="-1">
												{{ trans('site.front.form.reset-password') }}?</a></p>
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
							<form class="form-theme" method="POST" action="{{route('front.course.place_order', ['id' => $course->id])}}"
								  id="place_order_form">
								{{csrf_field()}}
							<h2>
								{{ str_replace('_title_', $course->title, trans('site.front.form.book-form-for')) }}
							</h2>
							<div class="panel-heading">{{ trans('site.front.form.user-information') }}</div>
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
										<label for="zip" class="control-label">{{ trans('site.front.form.zip') }}</label>
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
										{{--<div class="col-md-6 custom-checkbox">
											<input type="checkbox" name="update_address" id="update_address" checked>
											<label for="update_address" class="control-label">
												{{ trans('site.front.form.update-address') }}
											</label>
										</div>--}}
									@if(Auth::guest())
										<div class="col-md-6">
											<a href="{{ route('auth.login.google') }}" class="loginBtn loginBtn--google btn">
												{{ trans('site.front.form.login-with-google') }}
											</a>

											<a href="{{ route('auth.login.facebook') }}" class="loginBtn loginBtn--facebook btn">
												{{ trans('site.front.form.login-with-facebook') }}
											</a>
										</div>
									@endif
								</div>
							</div> <!-- end panel-body -->
						</div> <!-- end panel -->
					</div>

					<?php $hasPaidCourse = false; ?>
					@if( !Auth::guest() )
						<?php
						// check if course bought is not expired yet
						foreach( Auth::user()->coursesTakenNotOld as $courseTaken ) :
							if( $courseTaken->package->course->type != "Free" && $courseTaken->is_active ) :
                                if ($courseTaken->package->course->is_free != 1) {
                                    $hasPaidCourse = true;
                                }
								break;
							endif;
						endforeach;
						?>
					@endif
					<div class="col-lg-4">
						<!-- Payment Details -->
						<div class="panel panel-default mb-0">
							<div class="panel-heading-underlined pt-0">
								{{ trans('site.front.form.course-package') }}
							</div>
							<div class="panel-body px-0 pb-0">
								@foreach($packages as $k => $package)
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
									?>
									<div class="package-option custom-radio">
										<input type="radio" name="package_id"
											   id="{{ $package->variation }}"
											   value="{{$package->id}}"
											   data-full_payment_price="{{ FrontendHelpers::currencyFormat($full_payment_price) }}"
											   data-months_3_price="{{ FrontendHelpers::currencyFormat($months_3_price) }}"
											   data-months_6_price="{{ FrontendHelpers::currencyFormat($months_6_price) }}"
											   data-months_12_price="{{ FrontendHelpers::currencyFormat($months_12_price) }}"
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

							<div class="panel-heading-underlined">
								{{ trans('site.front.form.payment-method') }}
							</div>
							<div class="panel-body px-0 pb-0">
								<select class="form-control" name="payment_mode_id" required data-size="15">
									@foreach(\App\Http\FrontendHelpers::paymentModes(true) as $paymentMode)
										<option value="{{$paymentMode->id}}" data-mode="{{ $paymentMode->mode }}">{{$paymentMode->mode}}</option>
									@endforeach
								</select>
								{{--<em><small>Merk: Vi godtar kun full betaling på PAYPAL</small></em>--}}
							</div>

							<div class="panel-heading-underlined">
								{{ trans('site.front.form.discount-coupon') }}
							</div>
							<div class="panel-body px-0 pb-0">
								<input type="text" name="coupon" class="form-control" value="{{ $coupon }}">
							</div>

							<div class="panel-heading-underlined">
								{{ trans('site.front.form.payment-plan') }}
							</div>
							<div class="panel-body px-0 pb-0">
								<div class="row">
									<div class="col-sm-12" id="paymentPlanContainer">
										@foreach(App\PaymentPlan::orderBy('division', 'asc')->get() as $paymentPlan)
											<div class="payment-option custom-radio col-sm-6 px-0">
												<input type="radio" @if($paymentPlan->plan == 'Full Payment') checked @endif
												name="payment_plan_id" value="{{$paymentPlan->id}}" data-plan="{{trim($paymentPlan->plan)}}"
													   id="{{$paymentPlan->plan}}" required onchange="payment_plan_change(this)"
												data-plan-id="{{ $paymentPlan->id }}">
												<label for="{{$paymentPlan->plan}}">{{$paymentPlan->plan}} </label>
											</div>
									  @endforeach
									</div>
								</div>

								<div class="row">
									<div class="col-sm-12 margin-top custom-checkbox">
										<input type="checkbox" name="agree_terms" id="agree_terms" required>
										{!! str_replace(['_start_label_', '_end_label_', '_start_link_','_end_link_'],
										['<label for="agree_terms">',
										'</label>',
										'<a href="'.route('front.terms', 'course-terms').'" target="_new">','</a>'],
										trans('site.front.form.accept-terms')) !!}
									</div>
								</div>
							</div> <!-- end panel-body -->


							<div class="margin-bottom checkout-total mt-3">
								@if( $hasPaidCourse && $package->has_student_discount)
									@if($course->type == "Single")
										<strong>{{ trans('site.front.form.student-discount-500') }}</strong> <br />
									@endif

									@if($course->type == "Group")
										<strong>{{ trans('site.front.form.student-discount-1000') }}</strong> <br /> <br>
									@endif
								@endif

								<div id="price-wrapper" class="hide">
									<h3>{{ trans('site.front.price') }}:
										<span id="price-display" class="theme-text font-barlow-regular"></span>
									</h3>
								</div>

								<div id="discount-wrapper" class="hide">
									<h3>{{ trans('site.front.discount') }}:
										<span id="discount-display" class="theme-text font-barlow-regular"></span></h3>
								</div>

								<h3>{{ trans('site.front.total') }}:

									<?php $standard_price = $course->packages->where('variation', 'Standard Kurs')->first(); ?>
									@if( $standard_price )
										<span class="theme-text font-barlow-regular total-display">
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
										<span class="theme-text font-barlow-regular total-display">
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
								</h3>

									<h3 class="mt-0" id="monthly-price">
										{{ trans('site.front.per-month') }}: <span class="theme-text font-barlow-regular"></span>
									</h3>

									<button type="submit" class="btn site-btn-global-w-arrow" id="submitOrder">
										{{ trans('site.front.buy') }}
									</button>
							</div>
						</div> <!-- end panel-default -->

					</div> <!-- end col-md-4 -->
				</form>
			</div>
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

            let course_id = '<?php echo $course->id?>';
            let count_package_change = 0; // used to determine the onload
            let onload_package_id = parseInt('{{ Request::has('package') ? Request::get('package') : 0 }}');

            setTimeout(function(){

                // check if package parameter is set and radio with the value exists
				let radio_value = $(":radio[value="+onload_package_id+"]");
                if (onload_package_id > 0 && radio_value.length > 0) {
                    radio_value.attr('checked', true).trigger('change');
				} else {
                    if ($(".package-option").find('input[name=package_id]').length > 1) {
                        $(".package-option:nth-child(2)").find('input[name=package_id]').attr('checked', true).trigger('change');
                    } else {
                        $(".package-option:nth-child(1)").find('input[name=package_id]').attr('checked', true).trigger('change');
                    }
				}

                $('input:radio[name=payment_plan_id]:first').attr('checked', true).trigger('change');

            }, 100);

            $('input[name=package_id]').on('change', function(){
                //$('input:radio[name=split_invoice]').prop('disabled', true).prop('checked', false);
                generatePackagePaymentOption($(this).val());
                count_package_change++;

                let new_total = 0;
                /*new_total = $(this).attr('data-full_payment_sale_price_number')
                    ? $(this).data('full_payment_sale_price_number')
                    : $(this).data('full_payment_price_number');*/
                new_total = $(this).data('full_payment_price_number');

                let discount_value = $("input[name=discount_value]").val();
                if (discount_value) {
                    /*let price_value = $(this).attr('data-full_payment_sale_price_number')
                        ? $(this).data('full_payment_sale_price_number')
                        : $(this).data('full_payment_price_number');*/
                    let price_value = $(this).data('full_payment_price_number');
                    new_total = price_value - discount_value;
                }

                checkSalePrice($(this), 'full_payment_price_number', 'full_payment_sale_price_number', 'full_payment_price');

                $.get('/format_money/'+new_total, {}, function(){}, 'json').done(function(data){
                    let checkout_total = $('.checkout-total');
                    checkout_total.find('span.total-display').text(data);
                    $("#monthly-price").addClass('hide');
                });
            });

            $('select[name=payment_mode_id]').on('change', function(){
                let mode = $('option:selected', this).data('mode');
                let payment_plan_id = $('input:radio[name=payment_plan_id]');
                let discount_value = $("[name=discount_value]").val();
                $("#monthly-price").addClass('hide');

                if( mode === "Paypal" || mode === "Vipps") {
                    payment_plan_id.parent().addClass('disabled');
                    payment_plan_id.prop('disabled', true);
                    payment_plan_id.prop('checked', false);
                    payment_plan_id.filter('[id="Hele beløpet"]').prop('checked', true);
                    payment_plan_id.filter('[id="Hele beløpet"]').parent().removeClass('disabled');
                    payment_plan_id.filter('[id="Hele beløpet"]').prop('disabled', false);

                    let price = $('input:radio[name=package_id]:checked').data('full_payment_price_number');
                    let total = parseInt(price) - parseInt(discount_value);

                    $.get('/format_money/'+total, {}, function(){}, 'json').done(function(data){
                        let checkout_total = $('.checkout-total');
                        checkout_total.find('span.total-display').text(data);
                    });
                    //$('input:radio[name=split_invoice]').prop('disabled', true);
                } else {
                    payment_plan_id.parent().removeClass('disabled');
                    payment_plan_id.prop('disabled', false);
                }
            });

            //setup before functions
            let typingTimer;                //timer identifier
            let doneTypingInterval = 1000;  //time in ms, 5 second for example
            let coupon = $('input[name=coupon]');

			//on keyup, start the countdown
            coupon.on('keyup', function () {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(checkDiscount, doneTypingInterval);
            });

			//on keydown, clear the countdown
            coupon.on('keydown', function () {
                clearTimeout(typingTimer);
            });

            let passed_coupon = '{{ $coupon }}';
            if (passed_coupon) {
                console.log("inside if coupon");
                coupon.trigger('keyup');
            }

            //user is "finished typing," do something
            function checkDiscount () {
                let data = {coupon: coupon.val(), package_id: $('input[name=package_id]:checked').val(),
					payment_plan_id: $('input[name=payment_plan_id]:checked').val()};
                $.get('/course/'+course_id+'/check_discount', data, function(){}, 'json')
					.fail(function(){
						$("#discount-wrapper").addClass('hide');
						alert("Invalid Coupon Code.");

						let new_total = 0;
						let checked_payment_plan = $('input[name=payment_plan_id]:checked');

						if (checked_payment_plan.length > 0) {

							let plan = checked_payment_plan.data('plan');
							let price = 0;

							if( plan === 'Hele beløpet' ) {
								//var price = $('#package_select option:selected').data('full_payment_price_number');
								price = $('input[name=package_id]:checked').data('full_payment_price_number');
							} else if( plan === '3 måneder' ) {
								//var price = $('#package_select option:selected').data('months_3_price_number');
								price = $('input[name=package_id]:checked').data('months_3_price_number');
							} else if( plan === '6 måneder' ) {
								//var price = $('#package_select option:selected').data('months_6_price_number');
								price = $('input[name=package_id]:checked').data('months_6_price_number');
							} else if( plan === '12 måneder' ) {
                                //var price = $('#package_select option:selected').data('months_6_price_number');
                                price = $('input[name=package_id]:checked').data('months_12_price_number');
                            }

							new_total = price + $("input[name=discount_value]").val();
						} else {
							let price = $('#package_select').find('option:selected').data('full_payment_price_number');

							new_total = price + $("input[name=discount_value]").val();
						}

						$.get('/format_money/'+new_total, {}, function(){}, 'json').done(function(data){
							let checkout_total = $('.checkout-total');
							checkout_total.find('span.total-display').text(data);
						});

						$("input[name=discount_value]").val('');

					})
					.done(function(data){
						$("#discount-wrapper").removeClass('hide');
						$("#discount-display").text(data.discount_text);
						$("input[name=discount_value]").val(data.discount);

						let new_total = 0;
						let checked_payment_plan = $('input[name=payment_plan_id]:checked');

						if (checked_payment_plan.length > 0) {

							let plan = checked_payment_plan.data('plan');
							let price = 0;

							if( plan === 'Hele beløpet' ) {
								//var price = $('#package_select option:selected').data('full_payment_price_number');
								price = $('input[name=package_id]:checked').data('full_payment_price_number');
							} else if( plan === '3 måneder' ) {
								//var price = $('#package_select option:selected').data('months_3_price_number');
								price = $('input[name=package_id]:checked').data('months_3_price_number');
							} else if( plan === '6 måneder' ) {
								//var price = $('#package_select option:selected').data('months_6_price_number');
								price = $('input[name=package_id]:checked').data('months_6_price_number');
							} else if( plan === '12 måneder' ) {
                                //var price = $('#package_select option:selected').data('months_6_price_number');
                                price = $('input[name=package_id]:checked').data('months_12_price_number');
                            }

							new_total = price - data.discount;
						} else {
							let price = $('#package_select').find('option:selected').data('full_payment_price_number');

							new_total = price - data.discount;
						}

						$.get('/format_money/'+new_total, {}, function(){}, 'json').done(function(data){
							let checkout_total = $('.checkout-total');
							checkout_total.find('span.total-display').text(data);
						});
					});
            }

        });

        function generatePackagePaymentOption(package_id){
            let paymentPlanContainer = $("#paymentPlanContainer");
            paymentPlanContainer.find('.payment-option').hide().find('[type=radio]').attr('disabled', true);

            $.get('/payment-plan-options/'+package_id, {}, function(){}, 'json').done(function(data){
                $.each(data, function (k, v) {
                    let checked = '';
                    if (k === 0) {
                        checked = 'checked';
                    }
                    paymentPlanContainer.find('[type=radio][value='+v.id+']').attr('disabled', false)
						.closest('.payment-option').show();
                });

                paymentPlanContainer.find('.payment-option:first-of-type').find('[type=radio]').prop('checked', true);
            });
        }

        function payment_plan_change(t) {

            let checkout_total = $('.checkout-total');
            let plan = $(t).data('plan');
            let new_total = 0;
            /*let split_invoice = $('input:radio[name=split_invoice]');
            	split_invoice.prop('disabled', false);*/
			let checked_package_id = $('input[name=package_id]:checked');
			let discount_value = $("input[name=discount_value]").val();

            if( plan === 'Hele beløpet' ) {
                /*new_total = checked_package_id.attr('data-full_payment_sale_price_number')
                    ? checked_package_id.data('full_payment_sale_price_number')
                    : checked_package_id.data('full_payment_price_number');*/
                new_total = checked_package_id.data('full_payment_price_number');

                /*let price_value = checked_package_id.attr('data-full_payment_sale_price_number')
                    ? checked_package_id.data('full_payment_sale_price_number')
                    : checked_package_id.data('full_payment_price_number');*/
                let price_value = checked_package_id.data('full_payment_price_number');

                if (discount_value) {
                    new_total = price_value - discount_value;
                }

                /*split_invoice.prop('disabled', true);
                split_invoice.prop('checked', false);*/

                // check for sale price
                checkSalePrice(checked_package_id, 'full_payment_price_number', 'full_payment_sale_price_number', 'full_payment_price');
                $("#monthly-price").addClass('hide');

            } else if( plan === '3 måneder' ) {
                /*new_total = checked_package_id.attr('data-months_3_sale_price_number')
                    ? checked_package_id.data('months_3_sale_price_number')
                    : checked_package_id.data('months_3_price_number');*/
                new_total = checked_package_id.data('months_3_price_number');

                /*let price_value = checked_package_id.attr('data-months_3_sale_price_number')
                    ? checked_package_id.data('months_3_sale_price_number')
                    : checked_package_id.data('months_3_price_number');*/
                let price_value = checked_package_id.data('months_3_price_number');

                if (discount_value) {
                    new_total = price_value - discount_value;
                }

                checkSalePrice(checked_package_id, 'months_3_price_number', 'months_3_sale_price_number', 'months_3_price');
                checkMonthlyPrice(new_total, 3);
            } else if( plan === '6 måneder' ) {
                /*new_total = checked_package_id.attr('data-months_6_sale_price_number')
                    ? checked_package_id.data('months_6_sale_price_number')
                    : checked_package_id.data('months_6_price_number');*/
                new_total = checked_package_id.data('months_6_price_number');

                /*let price_value = checked_package_id.attr('data-months_6_sale_price_number')
                    ? checked_package_id.data('months_6_sale_price_number')
                    : checked_package_id.data('months_6_price_number');*/
                let price_value = checked_package_id.data('months_6_price_number');

                if (discount_value) {
                    new_total = price_value - discount_value;
                }
               checkSalePrice(checked_package_id, 'months_6_price_number', 'months_6_sale_price_number', 'months_6_price');
                checkMonthlyPrice(new_total,6);
            } else if( plan === '12 måneder' ) {
                /*new_total = checked_package_id.attr('data-months_12_sale_price_number')
                    ? checked_package_id.data('months_12_sale_price_number')
                    : checked_package_id.data('months_12_price_number');*/
                new_total = checked_package_id.data('months_12_price_number');

                /*let price_value = checked_package_id.attr('data-months_12_sale_price_number')
                    ? checked_package_id.data('months_12_sale_price_number')
                    : checked_package_id.data('months_12_price_number');*/
                let price_value = checked_package_id.data('months_12_price_number');

                if (discount_value) {
                    new_total = price_value - discount_value;
                }
               checkSalePrice(checked_package_id, 'months_12_price_number', 'months_12_sale_price_number', 'months_12_price');
                checkMonthlyPrice(new_total, 12);
            }
            $.get('/format_money/'+new_total, {}, function(){}, 'json').done(function(data){
                let checkout_total = $('.checkout-total');
                checkout_total.find('span.total-display').text(data);
            });
        }

        function checkSalePrice(checked_package_id, orig, sale, pris) {
            if (checked_package_id.attr('data-'+sale)) {
                let orig_price = checked_package_id.data(orig);
                let sale_price = checked_package_id.data(sale);
                let discount_price = orig_price - sale_price;

                $("#discount-wrapper").removeClass('hide');
                $("#price-wrapper").removeClass('hide');
                $("#price-display").text(checked_package_id.data(pris));
                $("[name=discount_value]").val(discount_price);

                $.get('/format_money/'+discount_price, {}, function(){}, 'json').done(function(data){
                    $("#discount-display").text(data);
                });
            } else {
                $("#discount-wrapper").addClass('hide');
                $("#price-wrapper").addClass('hide');
            }
		}

		function checkMonthlyPrice(total_price, divisor) {
            total_price  = parseFloat(total_price);
            let monthly_price = total_price/divisor;
            let rounded = Math.floor(monthly_price);
            $("#monthly-price").removeClass('hide');
            $.get('/format_money/'+rounded, {}, function(){}, 'json').done(function(data){
                $("#monthly-price").find('span').text(data);
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