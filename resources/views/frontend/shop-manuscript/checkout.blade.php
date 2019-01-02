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

						<form class="form-theme" method="POST" enctype="multipart/form-data" action="{{ route('front.shop-manuscript.place_order', ['id' => $shopManuscript->id]) }}"
							  id="place_order_form">
							{{csrf_field()}}

							<h2>Bestillingsskjema for {{$shopManuscript->title}}</h2>
							<div class="panel-heading">Brukerinformasjon</div>
							<div class="panel-body px-0">

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
									<div class="custom-checkbox mt-2">
										<input type="checkbox" name="send_to_email" id="send_to_email">
										<label for="send_to_email" class="control-label">Send til E-post</label>
									</div>
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
									<textarea name="description" id="" cols="30" rows="7" class="form-control">{{ old('description') }}</textarea>
								</div>

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
									@endif
								</div>

							</div> <!-- end panel-body -->
					</div> <!-- end panel -->
				</div> <!-- end col-lg-8 -->

				<div class="col-lg-4">
					<div class="panel panel-default mb-0">
						<div class="panel-heading-underlined">Betalingsmetode</div>
						<div class="panel-body px-0 pb-0">
							<select class="form-control" name="payment_mode_id" required data-size="15">
								@foreach(App\PaymentMode::get() as $paymentMode)
									<option value="{{$paymentMode->id}}" data-mode="{{ $paymentMode->mode }}">{{$paymentMode->mode}}</option>
								@endforeach
							</select>
							<em><small>Merk: Vi godtar kun full betaling på PAYPAL</small></em>
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

						<div class="panel-heading-underlined">Betalingsplan</div>
						<div class="panel-body px-0 pb-0">
							<div class="row">
								<div class="col-sm-12" id="paymentPlanContainer">
									@foreach(App\PaymentPlan::orderBy('division', 'asc')->where('id', '<>', 4)->where('id','!=', 7)->where('id','!=', 9)->get() as $paymentPlan)
										<div class="payment-option custom-radio col-sm-6 px-0">
											<input type="radio" @if($paymentPlan->plan == 'Hele beløpet') checked @endif
											name="payment_plan_id" value="{{$paymentPlan->id}}" data-plan="{{ $paymentPlan->plan }}"
												   id="{{$paymentPlan->plan}}" required>
											<label for="{{$paymentPlan->plan}}">{{$paymentPlan->plan}} </label>
										</div>
									@endforeach
								</div>
							</div>

							<div class="row">
								<div class="col-sm-12 margin-top custom-checkbox">
									<input type="checkbox" name="agree_terms" id="agree_terms" required>
									<label for="agree_terms">Jeg aksepterer</label>
									<a href="{{ route('front.terms', 'course-terms') }}"
									   target="_new">kjøpsvilkårene</a>
								</div>
							</div>
						</div> <!-- end panel-body -->

						<div class="margin-bottom checkout-total mt-3">
							@if( $hasPaidCourse )
								<strong>Du har en elevrabatt på 5%</strong>
							@endif

							<h3>Totalt:
								<span class="theme-text font-barlow-regular">{{ \App\Http\FrontendHelpers::currencyFormat($hasPaidCourse ?
								$shopManuscript->full_payment_price - ($shopManuscript->full_payment_price * 0.05) :
								$shopManuscript->full_payment_price) }}</span>
							</h3>

							<button type="submit" class="btn site-btn-global-w-arrow" id="proceed_checkout">
								<i class="fa fa-spinner fa-pulse d-none"></i>
								Bestill</button>
						</div> <!-- end checkout-total -->
					</div> <!-- end panel -->
				</div> <!-- end col-lg-4 -->
				</form>
			</div> <!-- end row -->
		</div> <!-- end container -->
	</div>

@stop


@section('scripts')
	<script>
        $(document).ready(function(){

            $('input[name=send_to_email]').change(function(){
                let manuscript_file = $('#manuscript-file');
                if( $(this).is(':checked') ){
                    manuscript_file.fadeOut();
                    manuscript_file.find('#manuscript').prop('required', false);
                } else {
                    $('#manuscript-file').fadeIn();
                    manuscript_file.find('#manuscript').prop('required', true);
                }
            });

            $("#place_order_form").on('submit',function(){
                $("#proceed_checkout").attr('disabled','disabled').find('.fa').removeClass('d-none');
			});

            let full_payment_price = '{{ App\Http\FrontendHelpers::currencyFormat($hasPaidCourse ? $shopManuscript->full_payment_price - ($shopManuscript->full_payment_price * 0.05) : $shopManuscript->full_payment_price) }}';
            let months_3_price = '{{ App\Http\FrontendHelpers::currencyFormat($hasPaidCourse ? $shopManuscript->months_3_price - ($shopManuscript->months_3_price * 0.05) : $shopManuscript->months_3_price) }}';

            $('select[name=payment_mode_id]').on('change', function(){
                let checkout_total = $('.checkout-total');
                let price = full_payment_price;
                let mode = $('option:selected', this).data('mode');
                let payment_plan_id = $('input:radio[name=payment_plan_id]');
                if( mode === "Paypal" ) {
                    payment_plan_id.parent().addClass('disabled');
                    payment_plan_id.prop('disabled', true);
                    payment_plan_id.prop('checked', false);
                    payment_plan_id.filter('[id="Hele beløpet"]').prop('checked', true);
                    payment_plan_id.filter('[id="Hele beløpet"]').parent().removeClass('disabled');
                    payment_plan_id.filter('[id="Hele beløpet"]').prop('disabled', false);
                    $('.checkout-total span').text(price);
                } else {
                    payment_plan_id.parent().removeClass('disabled');
                    payment_plan_id.prop('disabled', false);
                }
            });



            $('input[name=payment_plan_id]').on('change', function(){
                let checkout_total = $('.checkout-total');
                let plan = $(this).data('plan');
                let price = 0;
                if( plan === 'Hele beløpet' ) {
                    price = full_payment_price;
                } else if( plan === '3 måneder' ) {
                    price = months_3_price;
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