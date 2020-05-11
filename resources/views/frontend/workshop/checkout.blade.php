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

						<form class="form-theme" method="POST" enctype="multipart/form-data" action="{{ route('front.workshop.place_order', ['id' => $workshop->id]) }}"
							  id="place_order_form">
							{{csrf_field()}}
							<h2>
								{{ str_replace('_title_', $workshop->title, trans('site.front.form.book-form-for')) }}
							</h2>
							<div class="panel-heading">
								{{ trans('site.front.form.user-information') }}
							</div>
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
										<input type="text" id="city" class="form-control" name="city" required
											   @if(Auth::guest()) value="{{old('city')}}"
											   @else value="{{Auth::user()->address['city']}}" @endif>
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
							</div> <!-- end panel-body -->
					</div> <!-- end panel panel-default -->
				</div> <!-- end col-lg-8 -->

				<div class="col-lg-4">
					<div class="panel panel-default mb-0">
						@if (!in_array($workshop->id, [12, 13]))
							<div class="panel-heading-underlined pt-0">
								{{ trans('site.front.workshop.checkout-allergier') }}
							</div>
							<div class="panel-body px-0 pb-0">
								<select class="form-control" name="menu_id" required>
									@foreach($workshop->menus as $menu)
										<option value="{{$menu->id}}">{{$menu->title}}</option>
									@endforeach
								</select>
								<?php
									$notes_placeholder = trans('site.front.workshop.checkout-notes');
								?>
								<textarea class="form-control mt-3" name="notes" placeholder="{{ $notes_placeholder }}"
										  rows="4"></textarea>
							</div>
						@endif

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

						<div class="checkout-total mt-3">
                            <?php
                            $courseWorkshops = 0;
                            if (Auth::user()) {
                                foreach( Auth::user()->coursesTaken as $courseTaken ) {
                                    $courseWorkshops += $courseTaken->package->workshops;
                                }
                            }
								$workshopPrice = $workshop->price;

                            	if (Auth::user() && Auth::user()->coursesTakenNotOld2->count()) {
                            	    $workshopPrice = $workshop->price - 500;
								}
                            ?>

							@if (Auth::user() && Auth::user()->coursesTakenNotOld2->count())
								<h3>
									Studentrabatt:
									<span class="theme-text font-barlow-regular">
										{{ \App\Http\FrontendHelpers::currencyFormat(500) }}
									</span>
								</h3>
							@endif
							<h3>{{ trans('site.front.total') }}:
								@if (Auth::user())
									{{--@if (Auth::user()->workshopsTaken->count() == 0 && $courseWorkshops > 0)
										<span class="theme-text font-barlow-regular">{{ \App\Http\FrontendHelpers::currencyFormat($workshop->price * 0) }}</span>
									@else
										<span class="theme-text font-barlow-regular">{{ \App\Http\FrontendHelpers::currencyFormat($workshop->price) }}</span>
									@endif--}}
									<span class="theme-text font-barlow-regular">{{ \App\Http\FrontendHelpers::currencyFormat($workshopPrice) }}</span>
								@else
									<span class="theme-text font-barlow-regular">{{ \App\Http\FrontendHelpers::currencyFormat($workshopPrice) }}</span>
								@endif
							</h3>

							<button type="submit" class="btn site-btn-global-w-arrow">
								{{ trans('site.front.buy') }}
							</button>
						</div>
					</div>

				</div> <!-- end col-lg-4 -->
				</form>
			</div> <!-- end row -->
		</div> <!-- end container -->
	</div> <!-- end checkout-page -->

@stop


@section('scripts')

@stop