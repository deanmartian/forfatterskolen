{{-- @extends('frontend.layout') --}}
@extends('frontend.layouts.course-portal')

@section('title')
<title>Invoices &rsaquo; Forfatterskolen</title>
@stop

@section('heading') {{ trans('site.learner.my-invoice') }} @stop
@section('styles')
	<style>
		.nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus {
			color: #555;
			cursor: default;
			background-color: #fff;
			border: 1px solid #ddd;
			border-bottom-color: transparent;
		}

		#viewOrderModal table.no-border td {
			border: none;
		}
	</style>
@stop

@section('content')
	<div class="learner-container" id="app-container">
		<div class="container">
			<div class="row">
				@include('frontend.partials.learner-search-new')
			</div> <!-- end row -->

			<div class="row mt-5">
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
							[
								'name' => 'redeem',
								'label' => 'Redeem Gift'
							],
							[
								'name' => 'order-history',
								'label' => trans('site.order-history.title')
							],
							[
								'name' => 'time-register',
								'label' => 'Time Register'
							]
						]
					@endphp

					<ul class="nav nav-tabs margin-top">
						<li @if(!in_array(Request::input('tab'), array_column($tabWithLabel, 'name'))) class="active" @endif>
							<a href="?tab=fiken">
								Fiken
							</a>
						</li>

						@foreach($tabWithLabel as $tab)
							<li @if( Request::input('tab') == $tab['name'] ) class="active" @endif>
								<a href="?tab={{ $tab['name'] }}">
									{{ $tab['label'] }}
								</a>
							</li>
						@endforeach
					</ul>


					<div class="tab-content">
						<div class="tab-pane fade in active">

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
																   class="btn btn-sm btn-danger downloadCreditNote">
																	<i class="fa fa-download"></i>
																</a>
															@endif
														</td>
														<td>
															{{ $order->created_at_formatted }}
														</td>
														<td>
															@if($order->price)
																<button class="btn btn-dark btn-sm viewOrderBtn"
																		data-toggle="modal"
																		data-target="#viewOrderModal"
																		data-fields="{{ json_encode($order) }}">
																	<i class="fas fa-eye"></i>
																</button>

																<button class="btn btn-sm btn-danger downloadReceipt"
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
									{{ $sveaOrders->appends(request()->except('page')) }}
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
														<a href="{{ route('learner.course.show', $orderAttachment->course_taken_id) }}">
															{{ $orderAttachment->course_title }}
														</a>
													</td>
													<td>
														<a href="{{ $orderAttachment->file_path }}" download>
															{{ basename($orderAttachment->file_path) }}
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
															<span class="label label-success">{{$status}}</span>
														@elseif($invoice->fiken_is_paid === 2)
															<span class="label label-warning">{{$status}}</span>
														@elseif($invoice->fiken_is_paid === 3)
															<span class="label label-primary text-uppercase">Kreditert</span>
														@else
															<span class="label label-danger">{{$status}}</span>
														@endif
													</td>
													<td>{{date_format(date_create($invoice->created_at), 'M d, Y H.i')}}</td>
													<td> {{ $invoice->kid_number }} </td>
													<td> 9015 18 00393 </td>
													<td>
														@if($invoice->credit_note_url)
															<a href="{{ route('learner.download.credit-note', $invoice->id) }}">
																Credit Note
															</a>
														@endif
													</td>
													<td>
														<a href="{{route('learner.download.invoice', $invoice->id)}}">{{ trans('site.learner.download-invoice') }}</a>

														@if ($invoice->fiken_invoice_id && !$invoice->fiken_is_paid)
															<button class="btn btn-success btn-xs vippsFakturaBtn" style="margin-top: 5px"
																	data-toggle="modal"
																	data-target="#vippsFakturaModal"
																	data-action="{{ route('learner.invoice.vipps-e-faktura', $invoice->id) }}">
																	Send som Efaktura
															</button>
														@endif

														@if(!$invoice->fiken_is_paid)
															<div class="gateway--paypal">
																<form method="POST" action="{{ route('checkout.payment.paypal', encrypt($invoice->id)) }}">
																	{{ csrf_field() }}
																	{{--<input type="image" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="right" alt="PayPal - The safer, easier way to pay online">--}}
																	<button class="btn btn-primary">
																		<i class="fa fa-paypal" aria-hidden="true"></i> {{ trans('site.learner.pay-with-paypal-or-credit-card') }}
																	</button>
																</form>
															</div>

															<a href="{{ route('learner.invoice.vipps-payment', $invoice->fiken_invoice_id) }}" class="mt-3">
																<img src="{{ asset('images-new/betal-vipps.png') }}" class="w-75 mt-3">
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
									{{ $invoices->render() }}
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

	<div id="setVippsEFakturaModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-sm">
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

						<button type="submit" class="btn btn-primary pull-right">{{ trans('site.save') }}</button>
						<div class="clearfix"></div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div id="stopVippsEFakturaModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">
						{!! trans('site.stop-vipps-efaktura') !!}
					</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<form method="POST" action="{{ route('learner.set-vipps-e-faktura') }}" onsubmit="disableSubmit(this)">
						{{ csrf_field() }}
						<input type="hidden" name="mobile_number">

						<div class="form-group">
							{!! trans('site.stop-vipps-efaktura-message') !!}
						</div>

						<button type="submit" class="btn btn-danger pull-right">{{ trans('site.delete') }}</button>
						<div class="clearfix"></div>
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

@stop

@section('scripts')
	<script type="text/javascript" src="{{ asset('js/app.js?v='.time()) }}"></script>
	<script>
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
	</script>
@stop
