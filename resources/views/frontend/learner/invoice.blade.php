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
        @media only screen and (max-width: 768px) {
            .global-nav-tabs {
                display: flex;
                flex-wrap: wrap;
                gap: 4px;
                padding-left: 0;
            }

            .global-nav-tabs .nav-item {
                flex: 0 0 auto;
            }

            .global-nav-tabs .nav-link {
                font-size: 12px;
                padding: 6px 10px;
            }

            .learner-invoice-wrapper .table {
                font-size: 12px;
            }

            .learner-invoice-wrapper .table th,
            .learner-invoice-wrapper .table td {
                padding: 6px 8px;
                white-space: nowrap;
            }

            .invoice-actions {
                min-width: 120px;
                max-width: 150px;
            }

            .invoice-actions .btn {
                font-size: 11px;
                padding: 4px 8px;
            }

            .learner-invoice-wrapper .container {
                padding-left: 10px;
                padding-right: 10px;
            }

            /* Kort-layout for fakturaer under smal skjerm */
            .learner-invoice-wrapper .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }

        @media only screen and (max-width: 480px) {
            .global-nav-tabs .nav-link {
                font-size: 11px;
                padding: 5px 8px;
            }

            .invoice-actions {
                min-width: 100px;
            }

            .invoice-actions .btn {
                font-size: 10px;
                padding: 3px 6px;
                width: 100%;
            }

            /* Utestående-kort responsiv */
            .learner-invoice-wrapper .card .row .col-sm-6 {
                border-left: none !important;
                padding-top: 12px;
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
								'label' => trans('site.gift-purchases')
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
													<th>{{ trans('site.item') }}</th>
													<th>{{ trans_choice('site.packages', 1) }}</th>
													<th>{{ trans('site.credit-note') }}</th>
													<th>{{ trans('site.date') }}</th>
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

														<a href="{{ $orderAttachment->file_path }}?v={{ time() }}" class="pull-right blue-link"
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
												<th>{{ trans('site.item') }}</th>
												<th>{{ trans('site.redeem-code') }}</th>
												<th>{{ trans('site.redeemed') }}</th>
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
													<label>{{ trans('site.redeem-code') }}</label>
													<input type="text" name="redeem_code" class="form-control"
														   style="text-transform: uppercase" required>
												</div>

												<button class="btn btn-success w-100" type="submit">
													{{ trans('site.submit') }}
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
                                                                                                              <button class="btn btn-xs createInvoiceBtn disabled" style="background:#5F0000;color:#fff;border:none;border-radius:8px;" data-toggle="modal"
                                                                                                              data-target="#createInvoiceModal"
                                                                                                              data-action="{{ route('learner.invoice.pay-later.generate', $order->id) }}"
                                                                                                              data-plan-id="{{ optional($order->paymentPlan)->id }}"
                                                                                                              data-payment-plan-ids='@json(optional(optional($order->package)->course)->payment_plan_ids)'
                                                                                                              data-total="{{ $order->price - $order->discount }}"
                                                                                                              disabled style="pointer-events: none;">
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
													<th>{{ trans('site.author-portal.project') }}</th>
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

								{{-- Totalt utestående --}}
								@if(isset($unpaid) && $unpaid->count() > 0)
									@php
										$totalUnpaid = $unpaid->sum(function($inv) {
											$txSum = $inv->transactions->sum('amount');
											return $inv->fiken_balance - $txSum;
										});
										$nextInvoice = $unpaid->first();
									@endphp
									<div class="card mb-4" style="border-left: 4px solid #5F0000; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
										<div class="card-body" style="padding: 20px 24px;">
											<div class="row align-items-center">
												<div class="col-sm-6">
													<div style="font-size: 13px; color: #5D7285; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Totalt utestående</div>
													<div style="font-size: 28px; font-weight: 700; color: #5F0000; margin-top: 4px;">
														{{ \App\Http\FrontendHelpers::currencyFormat($totalUnpaid) }}
													</div>
													<div style="font-size: 13px; color: #5D7285; margin-top: 2px;">
														{{ $unpaid->count() }} {{ $unpaid->count() === 1 ? 'ubetalt faktura' : 'ubetalte fakturaer' }}
													</div>
												</div>
												@if($nextInvoice)
												<div class="col-sm-6" style="border-left: 1px solid #eee;">
													<div style="font-size: 13px; color: #5D7285; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Neste faktura</div>
													<div style="margin-top: 6px;">
														<span style="font-weight: 600; color: #2e3a59;">#{{ $nextInvoice->invoice_number }}</span>
														<span style="color: #5D7285; margin-left: 8px;">
															{{ \App\Http\FrontendHelpers::currencyFormat($nextInvoice->fiken_balance - $nextInvoice->transactions->sum('amount')) }}
														</span>
													</div>
													<div style="font-size: 13px; color: #5D7285; margin-top: 2px;">
														Frist: {{ \Carbon\Carbon::parse($nextInvoice->fiken_dueDate)->format('d.m.Y') }}
													</div>
													@if($nextInvoice->kid_number)
													<div style="font-size: 12px; color: #5D7285; margin-top: 2px;">
														KID: {{ $nextInvoice->kid_number }}
													</div>
													@endif
												</div>
												@endif
											</div>
										</div>
									</div>
								@endif

								<div class="card global-card">
									<div class="card-body py-0">
										<table class="table table-global">
											<thead>
											<tr>
												<th style="text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; color: #5D7285;">{{ trans('site.learner.invoice-number') }}</th>
												<th style="text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; color: #5D7285;">{{ trans('site.learner.deadline') }}</th>
												<th style="text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; color: #5D7285;">{{ trans('site.learner.remainders') }}</th>
												<th style="text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; color: #5D7285;">{{ trans('site.learner.status') }}</th>
												<th style="text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; color: #5D7285;">{{ trans('site.learner.created') }}</th>
												<th style="text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; color: #5D7285;">{{ trans('site.learner.kid-number') }}</th>
												<th style="text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; color: #5D7285;">{{ trans('site.learner.account-number') }}</th>
												<th style="text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; color: #5D7285;">{{ trans('site.credit-note') }}</th>
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
															<span style="display:inline-block;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;background:#e8f5e9;color:#2e7d32;">{{$status}}</span>
														@elseif($invoice->fiken_is_paid === 2)
															<span style="display:inline-block;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;background:#fff3e0;color:#e65100;">{{$status}}</span>
														@elseif($invoice->fiken_is_paid === 3)
															<span style="display:inline-block;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;background:#f3e5f5;color:#7b1fa2;text-transform:uppercase;">Kreditert</span>
														@else
															<span style="display:inline-block;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;background:#ffebee;color:#c62828;">{{$status}}</span>
														@endif
													</td>
													<td>
														{{ ucfirst(
																\Carbon\Carbon::parse($invoice->created_at)
																	->locale('nb')
																	->translatedFormat('M d, Y H.i')
															) }}
														{{-- {{date_format(date_create($invoice->created_at), 'M d, Y H.i')}} --}}</td>
													<td> {{ $invoice->kid_number }} </td>
													<td> 9015 18 00393 </td>
													<td>
														@if($invoice->credit_note_url)
															<a href="{{ route('learner.download.credit-note', $invoice->id) }}" 
																class="blue-outline-btn">
																{{ trans('site.credit-note') }}
															</a>
														@endif
													</td>
													<td class="invoice-actions">
														<a href="{{route('learner.download.invoice', $invoice->id)}}?v={{ time() }}" 
															class="blue-outline-btn d-inline-block">
															{{ trans('site.learner.download-invoice') }}
														</a>

														@if ($invoice->fiken_invoice_id && !$invoice->fiken_is_paid)
															<button class="btn btn-xs vippsFakturaBtn"
															style="margin-top:5px;background:#852635;color:#fff;border:none;border-radius:6px;font-size:11px;padding:4px 10px;"
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
                                                                                                                @if($invoice->fiken_is_paid == 1)
                                                                                                                        <a href="{{ route('learner.invoice.receipt.download', $invoice->id) }}"
                                                                                                                            class="btn btn-info btn-xs"
                                                                                                                            style="margin-top: 5px">
                                                                                                                            Kvittering
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
		<div class="modal-dialog modal-lg">
			<div class="modal-content" style="border-radius: 10px; overflow: hidden;">
				<div class="modal-header" style="border-bottom: 3px solid #5F0000; padding: 16px 24px;">
					<h4 class="modal-title" style="color: #5F0000; font-weight: 700; font-size: 18px;">Opprett betalingsplan</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body" style="padding: 24px;">
					<form method="POST" action="" onsubmit="disableSubmit(this)" id="createInvoiceForm">
						{{ csrf_field() }}
						<input type="hidden" name="payment_plan_id" id="ppPlanIdInput" value="">
						<input type="hidden" name="payment_plan_in_months" id="ppCustomMonthsInput" value="">
						<input type="hidden" name="split_invoice" value="1">

						{{-- Totalbeløp-banner --}}
						<div style="background: #FFEEE8; border-radius: 8px; padding: 14px 20px; margin-bottom: 20px; text-align: center;">
							<span style="font-size: 13px; color: #5D7285; text-transform: uppercase; letter-spacing: 0.5px;">Totalbeløp</span>
							<div id="ppTotalAmount" style="font-size: 26px; font-weight: 700; color: #5F0000;">0 kr</div>
						</div>

						{{-- Velg betalingsplan --}}
						<div class="form-group">
							<label style="font-weight: 600; color: #2e3a59; margin-bottom: 10px; display: block;">Velg betalingsplan</label>
							<div id="ppMonthCards" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 12px;">
								{{-- Fylles av JS --}}
							</div>
						</div>

						{{-- Egendefinert antall måneder --}}
						<div class="form-group" style="margin-bottom: 20px;">
							<label style="font-size: 13px; color: #5D7285;">Eller skriv inn antall måneder</label>
							<input type="number" id="ppCustomMonths" class="form-control" min="1" max="60" placeholder="f.eks. 4"
								style="max-width: 180px; border-radius: 6px;">
						</div>

						{{-- Månedlig beløp --}}
						<div id="ppMonthlyRow" style="display: none; background: #e8f5e9; border-radius: 6px; padding: 10px 16px; margin-bottom: 16px; text-align: center;">
							<span style="color: #2e7d32; font-size: 14px;">= <strong id="ppMonthlyAmount">0 kr</strong>/mnd</span>
						</div>

						{{-- Forhåndsvisning av fakturaer --}}
						<div id="ppPreviewSection" style="display: none; margin-bottom: 20px;">
							<label style="font-weight: 600; color: #2e3a59; margin-bottom: 8px; display: block;">Fakturaoversikt</label>
							<div style="max-height: 250px; overflow-y: auto; border: 1px solid #e4e8ed; border-radius: 6px;">
								<table class="table table-sm" style="margin-bottom: 0; font-size: 13px;">
									<thead style="background: #f8f9fa;">
										<tr>
											<th style="padding: 8px 12px; color: #5D7285; font-weight: 600;">#</th>
											<th style="padding: 8px 12px; color: #5D7285; font-weight: 600;">Beløp</th>
											<th style="padding: 8px 12px; color: #5D7285; font-weight: 600;">Forfallsdato</th>
										</tr>
									</thead>
									<tbody id="ppPreviewBody">
									</tbody>
									<tfoot style="background: #f8f9fa; font-weight: 600;">
										<tr>
											<td style="padding: 8px 12px;">Totalt</td>
											<td style="padding: 8px 12px;" id="ppPreviewTotal">0 kr</td>
											<td></td>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>

						<div class="text-right">
							<button type="button" class="btn" data-dismiss="modal" style="margin-right: 8px;">Avbryt</button>
							<button type="submit" class="btn submitInvoice" style="background: #5F0000; color: #fff; border: none; border-radius: 8px; padding: 8px 24px; font-weight: 600;">
								Opprett betaling
							</button>
						</div>
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
        let invoiceSubmissionInProgress = false;

        // Alle betalingsplaner fra DB
        @php
            $ppPlans = App\PaymentPlan::orderBy('division', 'asc')->get()->map(function($p) {
                return ['id' => $p->id, 'plan' => $p->plan, 'division' => $p->division];
            })->values()->toArray();
            $ppPlans[] = ['id' => 10, 'plan' => '24 måneder', 'division' => 24];
        @endphp
        const allPaymentPlans = @json($ppPlans);

        let ppTotal = 0;
        let ppSelectedMonths = 0;
        let ppSelectedPlanId = null;

        function formatKr(amount) {
            return Math.round(amount).toLocaleString('nb-NO') + ' kr';
        }

        function renderMonthCards(allowedPlanIds, total) {
            var container = $('#ppMonthCards');
            container.empty();

            var plans = allPaymentPlans.filter(function(p) {
                return allowedPlanIds.indexOf(p.id) !== -1;
            });

            if (!plans.length) {
                container.html('<p class="text-muted">Ingen betalingsplaner tilgjengelig.</p>');
                return;
            }

            plans.forEach(function(plan) {
                var monthly = plan.division > 1 ? Math.floor(total / plan.division) : total;
                var label = plan.division === 1 ? plan.plan : plan.division + ' mnd';
                var sublabel = plan.division > 1 ? '(' + formatKr(monthly) + '/mnd)' : '';

                var card = $('<div class="pp-month-card" data-plan-id="' + plan.id + '" data-division="' + plan.division + '"></div>');
                card.css({
                    border: '2px solid #e4e8ed',
                    borderRadius: '8px',
                    padding: '12px 16px',
                    cursor: 'pointer',
                    textAlign: 'center',
                    minWidth: '120px',
                    flex: '1 1 auto',
                    transition: 'border-color 0.2s'
                });
                card.html('<div style="font-weight:600;color:#2e3a59;">' + label + '</div>' +
                    (sublabel ? '<div style="font-size:12px;color:#5D7285;">' + sublabel + '</div>' : ''));

                card.on('click', function() {
                    selectPlan(plan.id, plan.division);
                });

                container.append(card);
            });
        }

        function selectPlan(planId, division) {
            ppSelectedPlanId = planId;
            ppSelectedMonths = division;

            // Oppdater hidden inputs
            $('#ppPlanIdInput').val(planId);
            $('#ppCustomMonthsInput').val('');
            $('#ppCustomMonths').val('');

            // Highlight valgt kort
            $('.pp-month-card').css('border-color', '#e4e8ed');
            $('.pp-month-card[data-plan-id="' + planId + '"]').css('border-color', '#5F0000');

            updatePreview();
        }

        function selectCustomMonths(months) {
            months = parseInt(months, 10);
            if (isNaN(months) || months < 1) {
                $('#ppMonthlyRow').hide();
                $('#ppPreviewSection').hide();
                return;
            }

            ppSelectedPlanId = null;
            ppSelectedMonths = months;

            // Fjern plan-ID, bruk custom
            $('#ppPlanIdInput').val('');
            $('#ppCustomMonthsInput').val(months);

            // Fjern highlight fra kort
            $('.pp-month-card').css('border-color', '#e4e8ed');

            updatePreview();
        }

        function updatePreview() {
            var months = ppSelectedMonths;
            var total = ppTotal;

            if (!months || months < 1 || !total) {
                $('#ppMonthlyRow').hide();
                $('#ppPreviewSection').hide();
                return;
            }

            var monthly = Math.floor(total / months);
            var remainder = total - (monthly * months);

            // Månedlig beløp
            if (months > 1) {
                $('#ppMonthlyAmount').text(formatKr(monthly));
                $('#ppMonthlyRow').show();
            } else {
                $('#ppMonthlyRow').hide();
            }

            // Forhåndsvisning tabell
            var tbody = $('#ppPreviewBody');
            tbody.empty();

            var baseDate = new Date();
            var runningTotal = 0;

            for (var i = 1; i <= months; i++) {
                var amount = monthly;
                if (i === months) {
                    amount = monthly + remainder; // siste faktura får resten
                }
                runningTotal += amount;

                var dueDate = new Date(baseDate);
                dueDate.setMonth(dueDate.getMonth() + i);
                var dateStr = ('0' + dueDate.getDate()).slice(-2) + '.' +
                              ('0' + (dueDate.getMonth() + 1)).slice(-2) + '.' +
                              dueDate.getFullYear();

                tbody.append(
                    '<tr>' +
                    '<td style="padding:8px 12px;">Faktura ' + i + '</td>' +
                    '<td style="padding:8px 12px;">' + formatKr(amount) + '</td>' +
                    '<td style="padding:8px 12px;">' + dateStr + '</td>' +
                    '</tr>'
                );
            }

            $('#ppPreviewTotal').text(formatKr(runningTotal));
            $('#ppPreviewSection').show();
        }

        // Lock modal during submission
        function lockInvoiceModal(modal, submitButton) {
            if (!modal || !modal.length || invoiceSubmissionInProgress) return;
            invoiceSubmissionInProgress = true;
            if (submitButton && submitButton.length) {
                submitButton.prop('disabled', true).addClass('disabled');
            }
            var closeButtons = modal.find('[data-dismiss="modal"], .close');
            closeButtons.each(function () {
                $(this).attr('data-dismiss-disabled', 'true').removeAttr('data-dismiss')
                    .prop('disabled', true).addClass('disabled').css('pointer-events', 'none');
            });
            var modalInstance = modal.data('bs.modal');
            if (modalInstance) {
                modal.data('invoice-original-backdrop', modalInstance.options.backdrop);
                modal.data('invoice-original-keyboard', modalInstance.options.keyboard);
                modalInstance.options.backdrop = 'static';
                modalInstance.options.keyboard = false;
            }
            modal.off('click.dismiss.bs.modal');
            modal.on('hide.bs.modal.invoice', function (e) {
                if (invoiceSubmissionInProgress) e.preventDefault();
            });
            if (!$('.invoice-submit-overlay').length) {
                var overlay = $('<div class="invoice-submit-overlay"></div>').css({
                    position:'fixed',top:0,left:0,width:'100%',height:'100%',
                    background:'rgba(255,255,255,0.7)',zIndex:1055,display:'flex',
                    alignItems:'center',justifyContent:'center',textAlign:'center',
                    fontSize:'18px',color:'#333',padding:'20px'
                }).text(invoiceProcessingMessage);
                $('body').append(overlay);
            }
        }

        // Eksisterende knapp-handlers
        $(".vippsFakturaBtn").click(function() {
            $("#vippsFakturaModal").find('form').attr('action', $(this).data('action'));
        });

        $(".viewOrderBtn").click(function(){
           var fields = $(this).data('fields');
           var modal = $("#viewOrderModal");
           modal.find("#displayDate").text(fields.created_at_formatted);
           if (fields.type === 1) modal.find(".package-variation").text(fields.item + " - " + fields.packageVariation);
           if (fields.type === 2) modal.find(".package-variation").text(fields.item);
           if (fields.type > 2) modal.find(".package-variation").text(fields.payment_mode_id === 1 ? fields.packageVariation : fields.item);
           modal.find(".payment-mode").text(fields.payment_mode_id === 1 ? 'Bankoverføring' : '');
           modal.find(".payment-plan").text(fields.payment_plan.plan);
           modal.find('.price-formatted').text(fields.price_formatted);
           modal.find('.discount-row').removeClass('hide');
           modal.find('.discount-formatted').text(fields.discount_formatted);
           if (!fields.discount) modal.find('.discount-row').addClass('hide');
           modal.find('.per-month-row').addClass('hide');
           if (fields.plan_id !== 8) modal.find('.per-month-row').removeClass('hide');
           modal.find('.additional-price-row').addClass('hide');
           if (fields.coaching_time && fields.coaching_time.additional_price) {
               modal.find('.additional-price-row').removeClass('hide');
               modal.find('.additional-price').text(fields.coaching_time.additional_price_formatted);
           }
           modal.find('.per-month').text(fields.monthly_price_formatted);
           modal.find('.total-formatted').text(fields.total_formatted);
		});

        $(".downloadReceipt").click(function(){
            var fields = $(this).data('fields');
            var type = fields.svea_invoice_id ? 'invoice' : 'receipt';
            var link = document.createElement('a');
            link.href = '/account/invoice/' + fields.id + '/download/' + type + '?v=' + Date.now();
            document.body.appendChild(link);
            link.click();
		});

        $(".setVippsEFakturaBtn").click(function(){
            $("#setVippsEFakturaModal").find('input[name=mobile_number]').val($(this).data('vipps-number'));
		});

        $(".stopVippsEFakturaBtn").click(function(){
            $("#stopVippsEFakturaModal").find('input[name=mobile_number]').val($(this).data('vipps-number'));
        });

        // Custom months input
        $('#ppCustomMonths').on('input', function() {
            selectCustomMonths($(this).val());
        });

        // Modal reset ved åpning/lukking
        $('#createInvoiceModal').on('shown.bs.modal', function () {
            invoiceSubmissionInProgress = false;
            $('.invoice-submit-overlay').remove();
            var modal = $(this);
            modal.off('hide.bs.modal.invoice');
            modal.find('[data-dismiss-disabled]').each(function () {
                $(this).removeAttr('data-dismiss-disabled').attr('data-dismiss', 'modal')
                    .prop('disabled', false).removeClass('disabled').css('pointer-events', '');
            });
            modal.find('.submitInvoice').prop('disabled', false).removeClass('disabled');
        });

        $('#createInvoiceForm').on('submit', function () {
            lockInvoiceModal($('#createInvoiceModal'), $('#createInvoiceModal .submitInvoice'));
        });

        $('#createInvoiceModal').on('hidden.bs.modal', function () {
            invoiceSubmissionInProgress = false;
            $('.invoice-submit-overlay').remove();
        });

        // createInvoiceBtn — åpne modal med riktige data
        var createInvoiceReady = false;
        var createInvoiceButtons = $(".createInvoiceBtn");
        createInvoiceButtons.prop('disabled', true).addClass('disabled').css('pointer-events', 'none');

        $(window).on('load', function () {
            createInvoiceReady = true;
            createInvoiceButtons.prop('disabled', false).removeClass('disabled').css('pointer-events', '');
        });

        $(".createInvoiceBtn").click(function(event) {
            if (!createInvoiceReady) {
                event.preventDefault();
                event.stopImmediatePropagation();
                return false;
            }

            var action = $(this).data('action');
            var modal = $("#createInvoiceModal");
            modal.find('form').attr('action', action);

            // Total
            ppTotal = parseFloat($(this).attr('data-total')) || 0;
            $('#ppTotalAmount').text(formatKr(ppTotal));

            // Reset
            ppSelectedPlanId = null;
            ppSelectedMonths = 0;
            $('#ppPlanIdInput').val('');
            $('#ppCustomMonthsInput').val('');
            $('#ppCustomMonths').val('');
            $('#ppMonthlyRow').hide();
            $('#ppPreviewSection').hide();

            // Parse allowed plan IDs
            var rawPlanIds = $(this).attr('data-payment-plan-ids');
            var allowedPlanIds = [];

            if (rawPlanIds) {
                try {
                    var parsed = JSON.parse(rawPlanIds);
                    if (Array.isArray(parsed)) {
                        allowedPlanIds = parsed.map(function(id) { return parseInt(id, 10); })
                            .filter(function(id) { return !isNaN(id); });
                    }
                } catch (e) {
                    allowedPlanIds = rawPlanIds.split(',').map(function(id) { return parseInt(id, 10); })
                        .filter(function(id) { return !isNaN(id); });
                }
            }

            // Render månedskort
            renderMonthCards(allowedPlanIds, ppTotal);

            // Forhåndsvelg første kort
            var firstCard = $('#ppMonthCards .pp-month-card').first();
            if (firstCard.length) {
                selectPlan(parseInt(firstCard.data('plan-id'), 10), parseInt(firstCard.data('division'), 10));
            }
        });
	</script>
@stop
