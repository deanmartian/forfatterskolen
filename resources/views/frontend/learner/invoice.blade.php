{{-- @extends('frontend.layout') --}}
@extends('frontend.layouts.course-portal')

@section('title')
<title>Invoices &rsaquo; Forfatterskolen</title>
@stop

@section('heading') {{ trans('site.learner.my-invoice') }} @stop
@section('styles')
	<style>
		/* .nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus {
			color: #555;
			cursor: default;
			background-color: #fff;
			border: 1px solid #ddd;
			border-bottom-color: transparent;
		} */

		#viewOrderModal table.no-border td {
			border: none;
		}

		.invoice-actions {
			white-space: normal;
			word-wrap: break-word;
			max-width: 220px;
			min-width: 180px;
			vertical-align: top;
		}

		.invoice-actions .btn,
		.invoice-actions form,
		.invoice-actions a {
			display: block;
			width: 100%;
			margin-bottom: 0.5rem;
			text-align: center;
		}

		.invoice-actions .btn {
			white-space: normal !important;
			word-break: break-word;
			text-align: center;
		}

		.invoice-actions img {
			max-width: 100%;
			height: auto;
			display: block;
		}

		/* Media Queries */
        @media only screen and (max-width: 500px) {
            .global-nav-tabs {
                display: inline-grid;
				padding-left: 10px;
            }
        }
	</style>
@stop

@section('content')
	<div class="learner-container learner-invoice-wrapper" id="app-container">
		<div class="container">
			{{-- <div class="row">
				@include('frontend.partials.learner-search-new')
			</div> <!-- end row --> --}}

			<div class="row">
				<div class="col-sm-12">

					@php
						$tabWithLabel = [
							[
									'name' => 'svea',
									'label' => 'Svea'
							],
							[
									'name' => 'regret-form',
									'label' => 'Angreskjema'
							],
							[
								'name' => 'gift',
								'label' => 'Gift Purchases'
							],
							/* [
								'name' => 'redeem',
								'label' => 'Redeem Gift'
							], */
							[
								'name' => 'order-history',
								'label' => trans('site.order-history.title')
							],
							[
									'name' => 'pay-later',
									'label' => trans('site.pay-later')
							],
							/* [
								'name' => 'time-register',
								'label' => 'Time Register'
							] */
						]
					@endphp

					<ul class="nav global-nav-tabs">
						<li class="nav-item">
							<a href="?tab=fiken" 
							class="nav-link {{ !in_array(Request::input('tab'), array_column($tabWithLabel, 'name')) ? 'active' : '' }}">
								Fiken
							</a>
						</li>

						@foreach($tabWithLabel as $tab)
							<li class="nav-item">
								<a href="?tab={{ $tab['name'] }}" 
								class="nav-link {{  ( Request::input('tab') == $tab['name'] ) ? 'active' : '' }}">
									{{ $tab['label'] }}
								</a>
							</li>
						@endforeach
					</ul>


					<div class="tab-content">
						<div class="tab-pane fade in active pt-4">

							@if( Request::input('tab') == 'svea' )

								<div class="card global-card">
									<div class="card-body py-0">
										<table class="table table-global">
											<thead>
												<tr>
													<th>Item</th>
													<th>Package</th>
													<th>Credit Note</th>
													<th>Date</th>
													<th width="150"></th>
												</tr>
											</thead>
											<tbody>
												@foreach($sveaOrders as $order)
													<tr>
														<td>
															{{ $order->item }}
														</td>
														<td>
															{{ $order->packageVariation }}
														</td>
														<td>
															@if ($order->is_credited_amount)
																<a href="{{ route('learner.order.download-credited', $order->id) }}"
																   class="btn blue-outline-btn downloadCreditNote">
																	<i class="fa fa-download"></i>
																</a>
															@endif
														</td>
														<td>
															{{ $order->created_at_formatted }}
														</td>
														<td>
															@if($order->price)
																<button class="btn blue-link viewOrderBtn"
																		data-toggle="modal"
																		data-target="#viewOrderModal"
																		data-fields="{{ json_encode($order) }}">
																	<i class="fas fa-eye"></i>
																</button>

																<button class="btn blue-link downloadReceipt"
																		style="margin-left: 5px"
																		data-fields="{{ json_encode($order) }}">
																	<i class="fa fa-download"></i>
																</button>
															@endif
														</td>
													</tr>
												@endforeach
											</tbody>
										</table>
									</div> <!-- end card-body -->
								</div> <!-- end global-card -->

								<div class="float-right">
									{{ $sveaOrders->appends(request()->except('page'))->links('pagination.short-pagination') }}
								</div>
							@elseif( Request::input('tab') == 'regret-form' )
								<div class="card global-card">
									<div class="card-body py-0">
										<table class="table table-global">
										<thead>
											<tr>
												<th>{{ trans_choice('site.courses', 1) }}</th>
												<th>{{ trans('site.learner.files-text') }}</th>
											</tr>
										</thead>
										<tbody>
											@foreach($orderAttachments as $orderAttachment)
												<tr>
													<td>
														<p class="pull-left">
															{{ $orderAttachment->course_title }}
														</p>

														<a href="{{ route('learner.course.show', $orderAttachment->course_taken_id) }}" 
															class="pull-right blue-link">
															<i class="fa fa-eye"></i>
														</a>
													</td>
													<td>
														<p class="pull-left">
															{{ basename($orderAttachment->file_path) }}
														</p>

														<a href="{{ $orderAttachment->file_path }}" class="pull-right blue-link"
															download>
															<i class="fa fa-download"></i>
														</a>
													</td>
													<td></td>
												</tr>
											@endforeach
											</tbody>
										</table>
									</div>
								</div>
							@elseif( Request::input('tab') == 'gift' )

								<div class="card global-card">
									<div class="card-body py-0">
										<table class="table table-global">
											<thead>
											<tr>
												<th>Item</th>
												<th>Redeem Code</th>
												<th>Redeemed</th>
											</tr>
											</thead>
											<tbody>
											@foreach($giftPurchases as $giftPurchase)
												<tr>
													<td>
														<a href="{{ $giftPurchase->item_link }}">
															{{ $giftPurchase->item_name }}
														</a>
													</td>
													<td>{{ $giftPurchase->redeem_code }}</td>
													<td>
														@if ($giftPurchase->is_redeemed)
															<label class="label label-success" style="font-size: 13px">
																{{ trans('site.front.yes') }}
															</label>
														@else
															<label class="label label-danger" style="font-size: 13px">
																{{ trans('site.front.no') }}
															</label>
														@endif

													</td>
												</tr>
											@endforeach
											</tbody>
										</table>
									</div>
								</div>

							@elseif( Request::input('tab') == 'redeem' )
								<div class="card global-card">
									<div class="card-body">
										<div class="col-md-4 col-md-offset-4">
											<form action="{{ route('learner.redeem-gift') }}" method="POST">
												{{ csrf_field() }}

												<div class="form-group mb-0">
													<label>Redeem Code</label>
													<input type="text" name="redeem_code" class="form-control"
														   style="text-transform: uppercase" required>
												</div>

												<button class="btn btn-success w-100" type="submit">
													Submit
												</button>
											</form>
										</div>
									</div>
								</div>

							@elseif(Request::input('tab') == 'order-history')
								<order-history :order-history="{{ json_encode($orderHistory) }}"
											   :user="{{ json_encode(Auth::user()) }}"></order-history>
							@elseif( Request::input('tab') == 'pay-later' )
								<div class="card global-card">
									<div class="card-body py-0">
										<table class="table table-global">
											<thead>
												<tr>
													<th>{{ trans('site.front.form.course-package') }}</th>
													{{-- <th>{{ trans('site.front.form.payment-plan') }}</th> --}}
													<th>{{ trans('site.front.form.payment-method') }}</th>
													<th>{{ trans('site.date') }}</th>
													<th>{{ trans('site.front.total') }}</th>
													<th></th>
												</tr>
											</thead>
											<tbody>
												@forelse($payLaterOrders as $order)
													<tr>
														<td>{{ $order->packageVariation }}</td>
														{{-- <td>{{ optional($order->paymentPlan)->plan }}</td> --}}
														<td>{{ optional($order->paymentMode)->mode }}</td>
														<td>{{ $order->created_at_formatted }}</td>
														<td>{{ $order->total_formatted }}</td>
														<td>
															@if ($order->package->course->payment_plan_ids)
																<button class="btn btn-success btn-xs createInvoiceBtn" data-toggle="modal"
																	data-target="#createInvoiceModal"
																	data-action="{{ route('learner.invoice.pay-later.generate', $order->id) }}"
																	data-plan-id="{{ optional($order->paymentPlan)->id }}"
																	data-payment-plan-ids='@json(optional(optional($order->package)->course)->payment_plan_ids)'>
																	+ {{ trans('site.create-invoice') }}
																</button>
															@endif
														</td>
													</tr>
												@empty
													<tr>
														<td colspan="6" class="text-center">
															{{ trans('site.pay-later-no-record') }}
														</td>
													</tr>
												@endforelse
											</tbody>
										</table>
									</div>
								</div>

								<div class="float-right">
										{{ $payLaterOrders->appends(request()->except('page'))->links('pagination.short-pagination') }}
								</div>
							@elseif( Request::input('tab') == 'time-register' )
								<div class="card global-card">
									<div class="card-body py-0">
										<table class="table table-global">
											<thead>
												<tr>
													<th>Project</th>
													<th>{{ trans('site.date') }}</th>
													<th></th>
												</tr>
											</thead>
											<tbody>
											@foreach($timeRegisters as $timeRegister)
												<tr>
													<td>
														{{ $timeRegister->project ? $timeRegister->project->name : '' }}
													</td>
													<td>
														{{ $timeRegister->date }}
													</td>
													<td>
														@if($timeRegister->invoice_file)
															<a href="{{route('learner.download.time-register-invoice', $timeRegister->id)}}">
																{{ trans('site.learner.download-invoice') }}
															</a>
														@endif
													</td>
												</tr>
											@endforeach
											</tbody>
										</table>
									</div>
								</div>
							@else

								<?php
									$hasVipps = Auth::user()->address && Auth::user()->address->vipps_phone_number;
								?>
								@if ($hasVipps)
									<a href="javascript:void(0)" class="btn short-red-outline-btn mb-4 stopVippsEFakturaBtn" 
									data-toggle="modal"
									data-target="#stopVippsEFakturaModal"
									data-vipps-number="{{ NULL }}">
										{!! trans('site.stop-vipps-efaktura') !!}
									</a>
								@else
									<a href="javascript:void(0)" class="btn short-red-outline-btn mb-4 setVippsEFakturaBtn" 
									data-toggle="modal"
									data-target="#setVippsEFakturaModal"
									data-vipps-number="{{ Auth::user()->address->vipps_phone_numberc }}">
										{!! trans('site.set-vipps-efaktura') !!}
									</a>
								@endif

								<div class="card global-card">
									<div class="card-body py-0">
										<table class="table table-global">
											<thead>
											<tr>
												<th>{{ trans('site.learner.invoice-number') }}</th>
												<th>{{ trans('site.learner.deadline') }}</th>
												<th>{{ trans('site.learner.remainders') }}</th>
												<th>{{ trans('site.learner.status') }}</th>
												<th>{{ trans('site.learner.created') }}</th>
												<th>{{ trans('site.learner.kid-number') }}</th>
												<th>{{ trans('site.learner.account-number') }}</th>
												<th>Credit Note</th>
												<th></th>
											</tr>
											</thead>
											<tbody>
											@foreach($invoices as $invoice)
                                                <?php
                                                $transactions_sum = $invoice->transactions->sum('amount');
                                                $balance = $invoice->fiken_balance;
                                                $status = $invoice->fiken_is_paid === 1 ? "BETALT"
                                                    : ($invoice->fiken_is_paid === 2 ? "SENDT TIL INKASSO" : "UBETALT");
                                                $Pbalance = (double)$invoice->gross/100;
                                                $total = 0;

                                                if(count($invoice->transactions) > 0) {
                                                    foreach($invoice->transactions as $transaction) {
                                                        $total += $transaction->amount;
                                                    }
                                                }
                                                ?>
												<tr>
													<td>{{$invoice->invoice_number}}</td>
													<td>{{ \Carbon\Carbon::parse($invoice->fiken_dueDate)->format('d.m.Y') }}</td>
													<td>
														@if($invoice->fiken_is_paid)
															{{\App\Http\FrontendHelpers::currencyFormat(0)}}
														@else
															{{\App\Http\FrontendHelpers::currencyFormat($balance - $transactions_sum)}}
														@endif
													</td>
													<td>
														@if($invoice->fiken_is_paid === 1)
															<span class="label label-green">{{$status}}</span>
														@elseif($invoice->fiken_is_paid === 2)
															<span class="label label-orange">{{$status}}</span>
														@elseif($invoice->fiken_is_paid === 3)
															<span class="label label-violet text-uppercase">Kreditert</span>
														@else
															<span class="label label-danger">{{$status}}</span>
														@endif
													</td>
													<td>{{date_format(date_create($invoice->created_at), 'M d, Y H.i')}}</td>
													<td> {{ $invoice->kid_number }} </td>
													<td> 9015 18 00393 </td>
													<td>
														@if($invoice->credit_note_url)
															<a href="{{ route('learner.download.credit-note', $invoice->id) }}" 
																class="blue-outline-btn">
																Credit Note
															</a>
														@endif
													</td>
													<td class="invoice-actions">
														<a href="{{route('learner.download.invoice', $invoice->id)}}" 
															class="blue-outline-btn d-inline-block">
															{{ trans('site.learner.download-invoice') }}
														</a>

														@if ($invoice->fiken_invoice_id && !$invoice->fiken_is_paid)
															<button class="btn btn-success btn-xs vippsFakturaBtn" 
															style="margin-top: 5px"
																	data-toggle="modal"
																	data-target="#vippsFakturaModal"
																	data-action="{{ route('learner.invoice.vipps-e-faktura', 
																	$invoice->id) }}">
																	Send som Efaktura
															</button>
														@endif

														@if(!$invoice->fiken_is_paid)
															<div class="gateway--paypal mt-3" style="display: block; width: 100%;">
																<form method="POST" 
																action="{{ route('checkout.payment.paypal', encrypt($invoice->id)) }}">
																	{{ csrf_field() }}
																	<button class="btn btn-primary d-block w-100">
																		<i class="fa fa-paypal" aria-hidden="true"></i> 
																		{{ trans('site.learner.pay-with-paypal-or-credit-card') }}
																	</button>
																</form>
															</div>

															<a href="{{ route('learner.invoice.vipps-payment', 
															$invoice->fiken_invoice_id) }}" class="mt-3">
																<img src="{{ asset('images-new/betal-vipps.png') }}" 
																class="mt-3">
															</a>
														@endif
													</td>
												</tr>
											@endforeach
											</tbody>
										</table>
									</div>
								</div> <!-- end card -->
								<div class="float-right">
									{{ $invoices->appends(Request::all())->links('pagination.short-pagination') }}
								</div>

							@endif

						</div> <!-- end tab-pane -->
					</div> <!-- end tab-content -->
				</div> <!-- end col-sm-12 -->
			</div> <!-- end row -->
		</div> <!-- end container-->
	</div>

	<div id="vippsFakturaModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">
						VIPPS eFaktura
					</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<form method="POST" action="" onsubmit="disableSubmit(this)">
						{{ csrf_field() }}

						<div class="form-group">
							<label>Mobile Number</label>
							<input type="text" class="form-control" name="mobile_number" required>
						</div>

						<button type="submit" class="btn btn-primary pull-right">{{ trans('site.send') }}</button>
						<div class="clearfix"></div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div id="setVippsEFakturaModal" class="modal global-modal fade" role="dialog">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">
						{!! trans('site.vipps-efaktura') !!}
					</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<form method="POST" action="{{ route('learner.set-vipps-e-faktura') }}" onsubmit="disableSubmit(this)">
						{{ csrf_field() }}

						<div class="form-group">
							<label>
								{!! trans('site.mobile-number') !!}
							</label>
							<input type="text" class="form-control" name="mobile_number" required>
						</div>

						<button type="submit" class="btn red-global-btn mt-3 pull-right">{{ trans('site.save') }}</button>
						<div class="clearfix"></div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div id="stopVippsEFakturaModal" class="modal global-modal fade" role="dialog">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">
						<i class="far fa-flag"></i>
					</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<form method="POST" action="{{ route('learner.set-vipps-e-faktura') }}" onsubmit="disableSubmit(this)">
						{{ csrf_field() }}
						<input type="hidden" name="mobile_number">

						<h3>
							{!! trans('site.stop-vipps-efaktura') !!}
						</h3>
						<div class="form-group">
							{!! trans('site.stop-vipps-efaktura-message') !!}
						</div>

						<button type="submit" class="btn red-global-btn mt-3 pull-right">{{ trans('site.delete') }}</button>
						<div class="clearfix"></div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div id="redeemModal" class="modal global-modal fade" role="dialog">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">
						<img src="{{ asset('images-new/icon/gift.png') }}">
					</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<form action="{{ route('learner.redeem-gift') }}" method="POST" onsubmit="disableSubmit(this)">
						{{ csrf_field() }}

						<h3>
							Redeem Code
						</h3>

						<div class="form-group">
							<label>Code*</label>
							<input type="text" name="redeem_code" class="form-control" placeholder="Enter code"
								   style="text-transform: uppercase" required>
						</div>

						<button class="btn red-global-btn mt-3" type="submit">
							Submit
						</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div id="viewOrderModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" style="padding: 2rem; font-size: 3rem">&times;</button>
				</div>
				<div class="modal-body" style="padding: 22px 30px;">

					<div class="row">
						<div class="col-sm-6">
							<span>Retur:</span> <br>
							<span>Forfatterskolen AS</span> <br>
							<span>Postboks 9233 Kjøsterud</span> <br>
							<span>3064 DRAMMEN</span> <br>
							<span>NORWAY</span>
						</div>

						<div class="col-sm-6">
							<img src="{{ asset('/images-new/logo-tagline.png') }}" alt="Logo" class="w-100"
								 style="height: 100px;object-fit: contain;">
						</div>
					</div>

					<div class="row mt-3">
						<div class="col-sm-6">
							<span>{{ $user->full_name }}</span> <br>
							<span>{{ $user->address->street }}</span> <br>
							<span>{{ $user->address->zip }} {{ $user->address->city }}</span>
						</div>
						<div class="col-sm-6">
							<span class="mr-2">{{ trans('site.date') }}: </span> <span id="displayDate"></span>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-12">
							<h3 class="mt-4 mb-0 font-weight-bold">Ordre</h3>
						</div>
					</div>

					<div class="col-sm-12 mt-4">
						<table class="table no-border">
							<tbody>
							<tr>
								<td>
									<b class="mr-2">Kjøp av:</b>
									<b class="package-variation"></b>
									<br>

									{{--<span>
										{{ trans('site.front.form.payment-method') }}: <i class="payment-mode"></i>
									</span>,

									<span>
										{{ trans('site.front.form.payment-plan') }}: <i class="payment-plan"></i>
									</span>--}}
								</td>
								<td>
								</td>
							</tr>
							</tbody>
						</table>
					</div>

					<div class="col-sm-5 col-sm-offset-7">
						<table class="table">
							<tbody>
								<tr>
									<td>
										<b>{{ trans('site.front.price') }}</b>
									</td>
									<td class="price-formatted">
									</td>
								</tr>
								<tr class="discount-row">
									<td>
										<b>{{ trans('site.front.discount') }}</b>
									</td>
									<td class="discount-formatted">
									</td>
								</tr>
								<tr class="per-month-row">
									<td>
										<b>{{ trans('site.front.per-month') }}</b>
									</td>
									<td class="per-month">
									</td>
								</tr>
								<tr class="additional-price-row hide">
									<td>
										<b>{{ trans('site.add-on-price') }}</b>
									</td>
									<td class="additional-price">
									</td>
								</tr>
								<tr>
									<td>
										<b>{{ trans('site.front.total') }}</b>
									</td>
									<td class="total-formatted">
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div> <!-- end modal-body -->
			</div> <!-- end modal content -->
		</div> <!-- view order modal -->
	</div>

	<div id="orderHistoryModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">
						Order History
					</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">

				</div>
			</div>
		</div>
	</div>

	<div id="createInvoiceModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">{{ trans('site.create-invoice') }}</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<form method="POST" action="" onsubmit="disableSubmit(this)">
						{{ csrf_field() }}

                                                <div class="form-group">
                                                        <label>{{ trans('site.front.form.payment-plan') }}</label> <br>
                                                        <div class="payment-plan-options">
                                                                @foreach(App\PaymentPlan::orderBy('division', 'asc')->get() as $paymentPlan)
                                                                        <div class="col-sm-6 payment-plan-option" data-payment-plan-option="true">
                                                                                <input type="radio" @if($paymentPlan->plan == 'Full Payment') checked @endif
                                                                                name="payment_plan_id" value="{{$paymentPlan->id}}" data-plan="{{trim($paymentPlan->plan)}}"
                                                                                        id="{{$paymentPlan->plan}}" onchange="payment_plan_change(this)"
                                                                                        data-plan-id="{{ $paymentPlan->id }}">
                                                                                <label>{{$paymentPlan->plan}} </label>
                                                                        </div>
                                                                @endforeach

                                                                <div class="col-sm-6 payment-plan-option" data-payment-plan-option="true">
                                                                        <input type="radio" @if($paymentPlan->plan == 'Full Payment') checked @endif
                                                                        name="payment_plan_id" value="10" data-plan="{{trim('24 måneder')}}"
                                                                                id="24 måneder" onchange="payment_plan_change(this)"
                                                                                data-plan-id="10">
                                                                        <label>24 måneder</label>
                                                                </div>
                                                                <div class="clearfix"></div>
                                                        </div>
                                                </div>

						<button type="submit" class="btn btn-primary pull-right submitInvoice">
							{{ trans('site.create-invoice') }}
						</button>
						<div class="clearfix"></div>
					</form>
				</div>
			</div>

		</div>
	</div>

@stop

@section('scripts')
	<script type="text/javascript" src="{{ asset('js/app.js?v='.time()) }}"></script>
	<script>
        const invoiceProcessingMessage = @json(trans('site.pay-later-invoice-processing'));
        const noPaymentPlanMessage = @json(__('No payment plans available for this purchase.'));
        const paymentPlanOptionSelector = '[data-payment-plan-option]';
        let invoiceSubmissionInProgress = false;

        function lockInvoiceModal(modal, submitButton) {
            if (!modal || !modal.length || invoiceSubmissionInProgress) {
                return;
            }

            invoiceSubmissionInProgress = true;

            if (submitButton && submitButton.length) {
                submitButton.prop('disabled', true).addClass('disabled');
            }

            const closeButtons = modal.find('[data-dismiss="modal"], .close');
            closeButtons.each(function () {
                const button = $(this);
                button.attr('data-dismiss-disabled', 'true');
                button.removeAttr('data-dismiss');
                button.prop('disabled', true).addClass('disabled');
                button.css('pointer-events', 'none');
            });

            const modalInstance = modal.data('bs.modal');
            if (modalInstance) {
                if (typeof modal.data('invoice-original-backdrop') === 'undefined') {
                    modal.data('invoice-original-backdrop', modalInstance.options.backdrop);
                }

                if (typeof modal.data('invoice-original-keyboard') === 'undefined') {
                    modal.data('invoice-original-keyboard', modalInstance.options.keyboard);
                }

                modalInstance.options.backdrop = 'static';
                modalInstance.options.keyboard = false;
            }

            modal.off('click.dismiss.bs.modal');
            modal.on('hide.bs.modal.invoice', function (event) {
                if (invoiceSubmissionInProgress) {
                    event.preventDefault();
                }
            });

            if (!$('.invoice-submit-overlay').length) {
                const overlay = $('<div class="invoice-submit-overlay"><div class="invoice-submit-message"></div></div>');
                overlay.css({
                    position: 'fixed',
                    top: 0,
                    left: 0,
                    width: '100%',
                    height: '100%',
                    background: 'rgba(255,255,255,0.7)',
                    'z-index': 1055,
                    display: 'flex',
                    'align-items': 'center',
                    'justify-content': 'center',
                    'text-align': 'center',
                    'font-size': '18px',
                    color: '#333',
                    padding: '20px'
                });

                overlay.find('.invoice-submit-message').text(invoiceProcessingMessage);
                $('body').append(overlay);
            }
        }

        $(".vippsFakturaBtn").click(function() {
            let action = $(this).data('action');
            $("#vippsFakturaModal").find('form').attr('action', action);
        });

        $(".viewOrderBtn").click(function(){
           let fields = $(this).data('fields');
           let modal = $("#viewOrderModal");

           modal.find("#displayDate").text(fields.created_at_formatted);

           if (fields.type === 1) {
               modal.find(".package-variation").text(fields.item + " - " + fields.packageVariation);
		   }

            if (fields.type === 2) {
                modal.find(".package-variation").text(fields.item);
            }

			if (fields.type > 2) {
				modal.find(".package-variation").text(fields.payment_mode_id === 1 ? fields.packageVariation : fields.item);
			}

           modal.find(".payment-mode").text(fields.payment_mode_id === 1 ? 'Bankoverføring' : '');
           modal.find(".payment-plan").text(fields.payment_plan.plan);

           modal.find('.price-formatted').text(fields.price_formatted);

           modal.find('.discount-row').removeClass('hide');
           modal.find('.discount-formatted').text(fields.discount_formatted);

           if (!fields.discount) {
               modal.find('.discount-row').addClass('hide');
		   }

		   modal.find('.per-month-row').addClass('hide');
		   if (fields.plan_id !== 8) {
               modal.find('.per-month-row').removeClass('hide');
		   }

            modal.find('.additional-price-row').addClass('hide');
            if (fields.coaching_time && fields.coaching_time.additional_price) {
                modal.find('.additional-price-row').removeClass('hide');
                modal.find('.additional-price').text(fields.coaching_time.additional_price_formatted);
			}

		   modal.find('.per-month').text(fields.monthly_price_formatted);
		   modal.find('.total-formatted').text(fields.total_formatted);
		});

        $(".downloadReceipt").click(function(){
            let fields = $(this).data('fields');
            let type = fields.svea_invoice_id ? 'invoice' : 'receipt';
            const link = document.createElement('a');
            link.href = '/account/invoice/' + fields.id + '/download/' + type;
            // link.setAttribute('download', 'test.doc');
            document.body.appendChild(link);
            link.click();
		});

        $(".setVippsEFakturaBtn").click(function(){
            let vipps_phone_number = $(this).data('vipps-number');
            $("#setVippsEFakturaModal").find('input[name=mobile_number]').val(vipps_phone_number);
		});

        $(".stopVippsEFakturaBtn").click(function(){
            let vipps_phone_number = $(this).data('vipps-number');
            $("#stopVippsEFakturaModal").find('input[name=mobile_number]').val(vipps_phone_number);
        });

        function collectAllPaymentPlans(modal) {
                let cachedPlans = modal.data('all-payment-plans');

                if (cachedPlans) {
                        return cachedPlans;
                }

                let plans = [];

                modal.find('.payment-plan-options').find(paymentPlanOptionSelector).each(function () {
                        let option = $(this);
                        let input = option.find('input[name="payment_plan_id"]');

                        if (!input.length) {
                                return;
                        }

                        let planId = parseInt(input.data('plan-id'), 10);

                        plans.push({
                                value: input.val(),
                                plan: input.data('plan'),
                                planId: planId,
                                id: input.attr('id'),
                                label: option.find('label').text().trim()
                        });
                });

                plans = plans.filter(function (plan) {
                        return !isNaN(plan.planId);
                });

                modal.data('all-payment-plans', plans);

                return plans;
        }

        function renderPaymentPlanOptions(modal, plans) {
                let container = modal.find('.payment-plan-options');
                container.empty();

                if (!plans || !plans.length) {
                        let message = $('<p class="text-muted no-payment-plan-message"></p>').text(noPaymentPlanMessage);
                        container.append(message);
                        return false;
                }

                plans.forEach(function (plan) {
                        let optionWrapper = $('<div class="col-sm-6 payment-plan-option" data-payment-plan-option="true"></div>');
                        let input = $('<input type="radio" name="payment_plan_id">');

                        input.val(plan.value);
                        if (plan.plan) {
                                input.attr('data-plan', plan.plan);
                        }
                        input.attr('data-plan-id', plan.planId);

                        if (plan.id) {
                                input.attr('id', plan.id);
                        }

                        input.on('change', function () {
                                payment_plan_change(this);
                        });

                        let label = $('<label></label>').text(plan.label + ' ');

                        optionWrapper.append(input);
                        optionWrapper.append(label);

                        container.append(optionWrapper);
                });

                container.append('<div class="clearfix"></div>');

                return true;
        }

        $('#createInvoiceModal').on('shown.bs.modal', function () {
            invoiceSubmissionInProgress = false;
            $('.invoice-submit-overlay').remove();

            const modal = $(this);
            modal.off('hide.bs.modal.invoice');

            modal.find('[data-dismiss-disabled]').each(function () {
                const button = $(this);
                button.removeAttr('data-dismiss-disabled');
                button.attr('data-dismiss', 'modal');
                button.prop('disabled', false).removeClass('disabled');
                button.css('pointer-events', '');
            });

            const submitButton = modal.find('.submitInvoice');
            submitButton.prop('disabled', false).removeClass('disabled');

            const modalInstance = modal.data('bs.modal');
            if (modalInstance) {
                const originalBackdrop = modal.data('invoice-original-backdrop');
                const originalKeyboard = modal.data('invoice-original-keyboard');

                modalInstance.options.backdrop = typeof originalBackdrop !== 'undefined' ? originalBackdrop : true;
                modalInstance.options.keyboard = typeof originalKeyboard !== 'undefined' ? originalKeyboard : true;
            }
        });

        $('#createInvoiceModal form').on('submit', function () {
            const modal = $('#createInvoiceModal');
            const submitButton = modal.find('.submitInvoice');
            lockInvoiceModal(modal, submitButton);
        });

        $('#createInvoiceModal').on('hidden.bs.modal', function () {
            invoiceSubmissionInProgress = false;
            $('.invoice-submit-overlay').remove();
        });

                $(".createInvoiceBtn").click(function() {
                        let action = $(this).data('action');
                        let modal = $("#createInvoiceModal");
                        let submitButton = modal.find('.submitInvoice');

                        modal.find('form').attr('action', action);

                        let rawPlanIds = $(this).attr('data-payment-plan-ids');
                        let currentPlanId = $(this).attr('data-plan-id');
                        let allowedPlanIds = [];

                        if (rawPlanIds) {
                                try {
                                        let parsedPlanIds = JSON.parse(rawPlanIds);

                                        if (Array.isArray(parsedPlanIds)) {
                                                allowedPlanIds = parsedPlanIds
                                                        .map(function (planId) {
                                                                return parseInt(planId, 10);
                                                        })
                                                        .filter(function (planId) {
                                                                return !isNaN(planId);
                                                        });
                                        }
                                } catch (error) {
                                        allowedPlanIds = rawPlanIds.split(',')
                                                .map(function (planId) {
                                                        return parseInt(planId, 10);
                                                })
                                                .filter(function (planId) {
                                                        return !isNaN(planId);
                                                });
                                }
                        }

                        let hasAllowedPlanIds = allowedPlanIds.length > 0;
                        let parsedCurrentPlanId = parseInt(currentPlanId, 10);

                        if (isNaN(parsedCurrentPlanId)) {
                                parsedCurrentPlanId = null;
                        }

                        let allPaymentPlans = collectAllPaymentPlans(modal);
                        let plansToRender = allPaymentPlans;

                        if (hasAllowedPlanIds) {
                                plansToRender = allPaymentPlans.filter(function (plan) {
                                        return allowedPlanIds.indexOf(plan.planId) !== -1;
                                });
                        }

                        let hasPlans = renderPaymentPlanOptions(modal, plansToRender);
                        let splitInvoiceOptions = modal.find('input[name="split_invoice"]');

                        if (!hasPlans) {
                                splitInvoiceOptions.prop('checked', false);
                                splitInvoiceOptions.prop('disabled', true);
                                submitButton.prop('disabled', true).addClass('disabled');
                                return;
                        }

                        submitButton.prop('disabled', false).removeClass('disabled');

                        let paymentPlanInputs = modal.find('input[name="payment_plan_id"]');
                        let selectedInput = $();

                        if (parsedCurrentPlanId !== null) {
                                selectedInput = paymentPlanInputs.filter(function () {
                                        return parseInt($(this).data('plan-id'), 10) === parsedCurrentPlanId;
                                }).first();
                        }

                        if (!selectedInput.length) {
                                selectedInput = paymentPlanInputs.first();
                        }

                        if (selectedInput.length) {
                                selectedInput.prop('checked', true);
                                payment_plan_change(selectedInput.get(0));
                        } else {
                                splitInvoiceOptions.prop('checked', false);
                                splitInvoiceOptions.prop('disabled', true);
                        }
                });

		function payment_plan_change(t) {
			let plan = $(t).data('plan');
			let split_invoice = $('input:radio[name=split_invoice]');
			split_invoice.prop('disabled', false);

			if( plan === 'Hele beløpet' ) {
				split_invoice.prop('disabled', true);
				split_invoice.prop('checked', false);
			}
		}
	</script>
@stop
