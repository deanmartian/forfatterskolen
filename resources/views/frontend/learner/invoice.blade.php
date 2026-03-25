{{-- @extends('frontend.layout') --}}
@extends('frontend.layouts.course-portal')

@section('title')
<title>Fakturaer &rsaquo; Forfatterskolen</title>
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

		/* Fjern grå understreking på inaktive faner */
		#invoiceTabs .nav-link {
			border-bottom: none !important;
			cursor: pointer;
		}
		#invoiceTabs .nav-link.active {
			border-bottom: 3px solid #E83A47 !important;
			font-weight: bold;
			color: #000;
		}

		/* Fiken-tabell: scroll horisontalt inne i sin boks, ikke hele siden */
		.fiken-table-wrap {
			overflow-x: auto;
			-webkit-overflow-scrolling: touch;
		}

		/* Forhindre at hele siden får horisontal overflow */
		.learner-invoice-wrapper {
			overflow-x: hidden;
		}

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

                /* Mobil-kort — generisk for alle faner */
        .inv-mobile-cards { display: none; }
        .fiken-mobile-cards { display: none; }

        .inv-card {
            background: #fff;
            border: 1px solid #e4e8ed;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 10px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }

        .inv-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .inv-card-title {
            font-weight: 700;
            color: #2e3a59;
            font-size: 14px;
            flex: 1;
            margin-right: 8px;
        }

        .inv-card-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 12px;
            margin-bottom: 12px;
        }

        .inv-card-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #5D7285;
            font-weight: 600;
        }

        .inv-card-value {
            font-size: 13px;
            color: #2e3a59;
        }

        .inv-card-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .inv-card-actions .btn {
            font-size: 12px;
            padding: 6px 14px;
            border-radius: 8px;
        }

        .fiken-card {
            background: #fff;
            border: 1px solid #e4e8ed;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 10px;
        }

        .fiken-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .fiken-card-nr {
            font-weight: 700;
            color: #2e3a59;
            font-size: 15px;
        }

        .fiken-card-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 12px;
            margin-bottom: 12px;
        }

        .fiken-card-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #5D7285;
            font-weight: 600;
        }

        .fiken-card-value {
            font-size: 13px;
            color: #2e3a59;
        }

        .fiken-card-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .fiken-card-actions .btn,
        .fiken-card-actions a {
            font-size: 11px;
            padding: 5px 10px;
            border-radius: 6px;
            text-align: center;
        }

        .fiken-card-actions .gateway--paypal {
            width: 100%;
        }

        .fiken-card-actions .gateway--paypal .btn {
            width: 100%;
            font-size: 12px;
        }

        /* Media Queries */
        @media only screen and (max-width: 768px) {
            /* Tabs som pills, wrapping */
            .global-nav-tabs {
                display: flex;
                flex-wrap: wrap;
                gap: 4px;
                padding-left: 0;
                border-bottom: none;
                list-style: none;
            }

            .global-nav-tabs .nav-item {
                flex: 0 0 auto;
            }

            .global-nav-tabs .nav-link {
                font-size: 11px;
                padding: 6px 10px;
                border: 1px solid #dee2e6;
                border-radius: 20px;
                white-space: nowrap;
                color: #2e3a59;
                background: #fff;
            }

            .global-nav-tabs .nav-link.active {
                background: #5F0000;
                color: #fff;
                border-color: #5F0000;
            }

            /* Skjul desktop tabell, vis mobilkort */
            .fiken-table-wrap { display: none; }
            .fiken-mobile-cards { display: block; }
            .inv-desktop-table { display: none; }
            .inv-mobile-cards { display: block; }

            .learner-invoice-wrapper .container {
                padding-left: 8px;
                padding-right: 8px;
            }

            /* Utestående-kort responsiv */
            .learner-invoice-wrapper .card .row .col-sm-6 {
                border-left: none !important;
                padding-top: 12px;
            }
        }

        @media only screen and (max-width: 480px) {
            .global-nav-tabs .nav-link {
                font-size: 10px;
                padding: 4px 8px;
            }

            .fiken-card { padding: 12px; }
            .fiken-card-value { font-size: 12px; }
            .inv-card { padding: 12px; }
            .inv-card-value { font-size: 12px; }
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
								'name' => 'gift',
								'label' => 'Gavekjøp'
							],
							[
								'name' => 'order-history',
								'label' => 'Kjøpshistorikk'
							],
							[
									'name' => 'pay-later',
									'label' => 'Opprett delbetaling'
							],
						]
					@endphp

					@php
						$activeTab = Request::input('tab', 'fiken');
						if (!in_array($activeTab, array_column($tabWithLabel, 'name'))) $activeTab = 'fiken';
					@endphp

					<ul class="nav global-nav-tabs" id="invoiceTabs">
						<li class="nav-item">
							<a href="javascript:void(0)" data-tab="fiken"
							class="nav-link {{ $activeTab === 'fiken' ? 'active' : '' }}">
								Fiken
							</a>
						</li>

						@foreach($tabWithLabel as $tab)
							<li class="nav-item">
								<a href="javascript:void(0)" data-tab="{{ $tab['name'] }}"
								class="nav-link {{ $activeTab === $tab['name'] ? 'active' : '' }}">
									{{ $tab['label'] }}
								</a>
							</li>
						@endforeach
					</ul>

					<div class="tab-content pt-4">

						{{-- ═══ SVEA ═══ --}}
						<div class="inv-tab-panel" data-panel="svea" @if($activeTab !== 'svea') style="display:none;" @endif>

								{{-- Desktop tabell --}}
								<div class="card global-card inv-desktop-table">
									<div class="card-body py-0">
										<table class="table table-global">
											<thead>
												<tr>
													<th>Produkt</th>
													<th>Pakke</th>
													<th>Kreditnota</th>
													<th>Dato</th>
													<th width="150"></th>
												</tr>
											</thead>
											<tbody>
												@foreach($sveaOrders as $order)
													<tr>
														<td>{{ $order->item }}</td>
														<td>{{ $order->packageVariation }}</td>
														<td>
															@if ($order->is_credited_amount)
																<a href="{{ route('learner.order.download-credited', $order->id) }}"
																   class="btn blue-outline-btn downloadCreditNote">
																	<i class="fa fa-download"></i>
																</a>
															@endif
														</td>
														<td>{{ $order->created_at_formatted }}</td>
														<td>
															@if($order->price)
																<button class="btn blue-link viewOrderBtn"
																		data-bs-toggle="modal"
																		data-bs-target="#viewOrderModal"
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
									</div>
								</div>

								{{-- Mobilkort --}}
								<div class="inv-mobile-cards">
									@foreach($sveaOrders as $order)
										<div class="inv-card">
											<div class="inv-card-header">
												<span class="inv-card-title">{{ $order->item }}</span>
											</div>
											<div class="inv-card-grid">
												<div>
													<div class="inv-card-label">Pakke</div>
													<div class="inv-card-value">{{ $order->packageVariation }}</div>
												</div>
												<div>
													<div class="inv-card-label">Dato</div>
													<div class="inv-card-value">{{ $order->created_at_formatted }}</div>
												</div>
											</div>
											<div class="inv-card-actions">
												@if ($order->is_credited_amount)
													<a href="{{ route('learner.order.download-credited', $order->id) }}"
														class="btn" style="background:#5D7285;color:#fff;flex:1;">
														<i class="fa fa-download"></i> Kreditnota
													</a>
												@endif
												@if($order->price)
													<button class="btn viewOrderBtn" style="background:#5F0000;color:#fff;flex:1;"
															data-bs-toggle="modal"
															data-bs-target="#viewOrderModal"
															data-fields="{{ json_encode($order) }}">
														<i class="fas fa-eye"></i> Vis
													</button>
													<button class="btn downloadReceipt" style="background:#2e3a59;color:#fff;flex:1;"
															data-fields="{{ json_encode($order) }}">
														<i class="fa fa-download"></i> Kvittering
													</button>
												@endif
											</div>
										</div>
									@endforeach
								</div>

								<div class="float-end">
									{{ $sveaOrders->appends(request()->except('page'))->links('pagination.short-pagination') }}
								</div>
						</div>
						{{-- ═══ ANGRESKJEMA (skjult, innhold flyttet til Kjøpshistorikk) ═══ --}}
						<div class="inv-tab-panel" data-panel="regret-form" style="display:none;">
								{{-- Desktop tabell --}}
								<div class="card global-card inv-desktop-table">
									<div class="card-body py-0">
										<table class="table table-global">
										<thead>
											<tr>
												<th>Kurs</th>
												<th>Fil</th>
											</tr>
										</thead>
										<tbody>
											@foreach($orderAttachments as $orderAttachment)
												<tr>
													<td>
														<p class="float-start">{{ $orderAttachment->course_title }}</p>
														<a href="{{ route('learner.course.show', $orderAttachment->course_taken_id) }}"
															class="float-end blue-link">
															<i class="fa fa-eye"></i>
														</a>
													</td>
													<td>
														<p class="float-start">{{ basename($orderAttachment->file_path) }}</p>
														<a href="{{ $orderAttachment->file_path }}?v={{ time() }}" class="float-end blue-link"
															download>
															<i class="fa fa-download"></i>
														</a>
													</td>
												</tr>
											@endforeach
											</tbody>
										</table>
									</div>
								</div>

								{{-- Mobilkort --}}
								<div class="inv-mobile-cards">
									@foreach($orderAttachments as $orderAttachment)
										<div class="inv-card">
											<div class="inv-card-header">
												<span class="inv-card-title">{{ $orderAttachment->course_title }}</span>
											</div>
											<div class="inv-card-actions">
												<a href="{{ route('learner.course.show', $orderAttachment->course_taken_id) }}"
													class="btn" style="background:#5F0000;color:#fff;flex:1;">
													<i class="fa fa-eye"></i> Vis kurs
												</a>
												<a href="{{ $orderAttachment->file_path }}?v={{ time() }}"
													class="btn" style="background:#2e3a59;color:#fff;flex:1;" download>
													<i class="fa fa-download"></i> {{ basename($orderAttachment->file_path) }}
												</a>
											</div>
										</div>
									@endforeach
								</div>

						</div>

						{{-- ═══ GAVEKJØP ═══ --}}
						<div class="inv-tab-panel" data-panel="gift" @if($activeTab !== 'gift') style="display:none;" @endif>

								{{-- Desktop tabell --}}
								<div class="card global-card inv-desktop-table">
									<div class="card-body py-0">
										<table class="table table-global">
											<thead>
											<tr>
												<th>Produkt</th>
												<th>Innløsningskode</th>
												<th>Innløst</th>
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
															<label class="badge bg-success" style="font-size: 13px">Ja</label>
														@else
															<label class="badge bg-danger" style="font-size: 13px">Nei</label>
														@endif
													</td>
												</tr>
											@endforeach
											</tbody>
										</table>
									</div>
								</div>

								{{-- Mobilkort --}}
								<div class="inv-mobile-cards">
									@foreach($giftPurchases as $giftPurchase)
										<div class="inv-card">
											<div class="inv-card-header">
												<span class="inv-card-title">
													<a href="{{ $giftPurchase->item_link }}" style="color:#2e3a59;text-decoration:none;">
														{{ $giftPurchase->item_name }}
													</a>
												</span>
												@if ($giftPurchase->is_redeemed)
													<span class="badge bg-success" style="font-size:11px;">Ja</span>
												@else
													<span class="badge bg-danger" style="font-size:11px;">Nei</span>
												@endif
											</div>
											<div class="inv-card-grid">
												<div>
													<div class="inv-card-label">Innløsningskode</div>
													<div class="inv-card-value" style="font-family:monospace;letter-spacing:1px;">{{ $giftPurchase->redeem_code }}</div>
												</div>
											</div>
										</div>
									@endforeach
								</div>

						</div>

						{{-- ═══ INNLØS KODE (skjult panel) ═══ --}}
						<div class="inv-tab-panel" data-panel="redeem" style="display:none;">
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

						</div>

						{{-- ═══ KJØPSHISTORIKK ═══ --}}
						<div class="inv-tab-panel" data-panel="order-history" @if($activeTab !== 'order-history') style="display:none;" @endif>

								{{-- Desktop tabell --}}
								<div class="card global-card inv-desktop-table">
									<div class="card-body py-0">
										<table class="table table-global">
											<thead>
												<tr>
													<th>Produkt</th>
													<th>Pakke</th>
													<th>Dato</th>
													<th>Totalt</th>
													<th></th>
												</tr>
											</thead>
											<tbody>
												@forelse($orderHistory as $order)
													@php
														$orderAttFiles = $orderAttachments->filter(function($a) use ($order) {
															return $a->course_id == $order->item_id || $a->package_id == $order->package_id;
														});
													@endphp
													<tr>
														<td>
															<div style="font-weight:600;">{{ $order->item }}</div>
															@if($orderAttFiles->count() > 0)
																<div style="margin-top:6px;">
																	@foreach($orderAttFiles as $att)
																		<a href="{{ $att->file_path }}?v={{ time() }}"
																			class="btn btn-sm" download
																			style="background:#f0f0f0;color:#2e3a59;border:1px solid #dee2e6;border-radius:6px;font-size:11px;margin-right:4px;margin-bottom:4px;">
																			<i class="fa fa-file-alt" style="color:#5F0000;margin-right:3px;"></i>
																			Angreskjema
																		</a>
																	@endforeach
																</div>
															@endif
														</td>
														<td>{{ $order->packageVariation }}</td>
														<td>{{ $order->created_at_formatted }}</td>
														<td style="font-weight:600;">{{ $order->total_formatted }}</td>
														<td>
															@if($order->price)
																<div style="display:flex;gap:6px;align-items:center;">
																	<button class="btn btn-sm ohViewBtn"
																		style="background:#5F0000;color:#fff;border:none;border-radius:6px;font-size:11px;padding:4px 10px;"
																		data-bs-toggle="modal"
																		data-bs-target="#ohViewModal"
																		data-order='@json($order)'>
																		<i class="fas fa-eye"></i> Vis
																	</button>
																	<a href="/account/order/{{ $order->id }}/download/"
																		class="btn btn-sm"
																		style="background:#2e3a59;color:#fff;border:none;border-radius:6px;font-size:11px;padding:4px 10px;">
																		<i class="fas fa-download"></i>
																	</a>
																	<button class="btn btn-sm ohCompanyBtn"
																		style="background:#fff;color:#2e3a59;border:1px solid #dee2e6;border-radius:6px;font-size:11px;padding:4px 10px;"
																		data-bs-toggle="modal"
																		data-bs-target="#ohCompanyModal"
																		data-order-id="{{ $order->id }}"
																		data-company='@json($order->company)'>
																		Rediger Bedrift
																	</button>
																</div>
															@endif
														</td>
													</tr>
												@empty
													<tr><td colspan="5" class="text-center" style="color:#5D7285;">Ingen kjøp registrert.</td></tr>
												@endforelse
											</tbody>
										</table>
									</div>
								</div>

								{{-- Mobilkort --}}
								<div class="inv-mobile-cards">
									@forelse($orderHistory as $order)
										@php
											$orderAttFiles = $orderAttachments->filter(function($a) use ($order) {
												return $a->course_id == $order->item_id || $a->package_id == $order->package_id;
											});
										@endphp
										<div class="inv-card">
											<div class="inv-card-header">
												<span class="inv-card-title">{{ $order->item }}</span>
											</div>
											<div class="inv-card-grid">
												<div>
													<div class="inv-card-label">Pakke</div>
													<div class="inv-card-value">{{ $order->packageVariation }}</div>
												</div>
												<div>
													<div class="inv-card-label">Dato</div>
													<div class="inv-card-value">{{ $order->created_at_formatted }}</div>
												</div>
												<div>
													<div class="inv-card-label">Totalt</div>
													<div class="inv-card-value" style="font-weight:700;color:#5F0000;">{{ $order->total_formatted }}</div>
												</div>
												@if(optional($order->paymentPlan)->plan)
												<div>
													<div class="inv-card-label">Betalingsplan</div>
													<div class="inv-card-value">{{ $order->paymentPlan->plan }}</div>
												</div>
												@endif
											</div>

											{{-- Angreskjema-filer --}}
											@if($orderAttFiles->count() > 0)
												<div style="margin-bottom:10px;">
													@foreach($orderAttFiles as $att)
														<a href="{{ $att->file_path }}?v={{ time() }}"
															class="btn btn-sm d-inline-block" download
															style="background:#f8f4f0;color:#5F0000;border:1px solid #e0d6cc;border-radius:6px;font-size:11px;margin-bottom:4px;">
															<i class="fa fa-file-alt" style="margin-right:3px;"></i>
															Angreskjema
														</a>
													@endforeach
												</div>
											@endif

											<div class="inv-card-actions">
												@if($order->price)
													<button class="btn ohViewBtn" style="background:#5F0000;color:#fff;flex:1;"
														data-bs-toggle="modal"
														data-bs-target="#ohViewModal"
														data-order='@json($order)'>
														<i class="fas fa-eye"></i> Vis
													</button>
													<a href="/account/order/{{ $order->id }}/download/"
														class="btn" style="background:#2e3a59;color:#fff;flex:1;">
														<i class="fas fa-download"></i> Last ned
													</a>
													<button class="btn ohCompanyBtn" style="background:#fff;color:#2e3a59;border:1px solid #dee2e6;flex:1;"
														data-bs-toggle="modal"
														data-bs-target="#ohCompanyModal"
														data-order-id="{{ $order->id }}"
														data-company='@json($order->company)'>
														Bedrift
													</button>
												@endif
											</div>
										</div>
									@empty
										<div class="inv-card text-center" style="color:#5D7285;">
											Ingen kjøp registrert.
										</div>
									@endforelse
								</div>
						</div>

						{{-- ═══ BETAL SENERE ═══ --}}
						<div class="inv-tab-panel" data-panel="pay-later" @if($activeTab !== 'pay-later') style="display:none;" @endif>
								{{-- Desktop tabell --}}
								<div class="card global-card inv-desktop-table">
									<div class="card-body py-0">
										<table class="table table-global">
											<thead>
												<tr>
													<th>{{ trans('site.front.form.course-package') }}</th>
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
														<td>{{ optional($order->paymentMode)->mode }}</td>
														<td>{{ $order->created_at_formatted }}</td>
														<td>{{ $order->total_formatted }}</td>
														<td>
															@if (optional(optional($order->package)->course)->payment_plan_ids)
																<button class="btn btn-sm createInvoiceBtn"
																	style="background:#5F0000;color:#fff;border:none;border-radius:8px;cursor:pointer;"
																	data-bs-toggle="modal"
																	data-bs-target="#createInvoiceModal"
																	data-action="{{ route('learner.invoice.pay-later.generate', $order->id) }}"
																	data-plan-id="{{ optional($order->paymentPlan)->id }}"
																	data-payment-plan-ids='@json(optional(optional($order->package)->course)->payment_plan_ids)'
																	data-total="{{ $order->price - $order->discount }}">
																	+ Opprett betalingsløsning
																</button>
															@endif
														</td>
													</tr>
												@empty
													<tr>
														<td colspan="5" class="text-center">
															Ingen bestillinger med betal senere.
														</td>
													</tr>
												@endforelse
											</tbody>
										</table>
									</div>
								</div>

								{{-- Mobilkort --}}
								<div class="inv-mobile-cards">
									@forelse($payLaterOrders as $order)
										<div class="inv-card">
											<div class="inv-card-header">
												<span class="inv-card-title">{{ $order->packageVariation }}</span>
											</div>
											<div class="inv-card-grid">
												<div>
													<div class="inv-card-label">Betalingsmetode</div>
													<div class="inv-card-value">{{ optional($order->paymentMode)->mode }}</div>
												</div>
												<div>
													<div class="inv-card-label">Dato</div>
													<div class="inv-card-value">{{ $order->created_at_formatted }}</div>
												</div>
												<div>
													<div class="inv-card-label">Totalt</div>
													<div class="inv-card-value" style="font-weight:700;color:#5F0000;">{{ $order->total_formatted }}</div>
												</div>
											</div>
											<div class="inv-card-actions">
												@if (optional(optional($order->package)->course)->payment_plan_ids)
													<button class="btn createInvoiceBtn"
														style="background:#5F0000;color:#fff;border:none;flex:1;"
														data-bs-toggle="modal"
														data-bs-target="#createInvoiceModal"
														data-action="{{ route('learner.invoice.pay-later.generate', $order->id) }}"
														data-plan-id="{{ optional($order->paymentPlan)->id }}"
														data-payment-plan-ids='@json(optional(optional($order->package)->course)->payment_plan_ids)'
														data-total="{{ $order->price - $order->discount }}">
														+ Opprett betalingsløsning
													</button>
												@endif
											</div>
										</div>
									@empty
										<div class="inv-card text-center" style="color:#5D7285;">
											Ingen bestillinger med betal senere.
										</div>
									@endforelse
								</div>

								<div class="float-end">
										{{ $payLaterOrders->appends(request()->except('page'))->links('pagination.short-pagination') }}
								</div>
						</div>

						{{-- ═══ TIMEREGISTRERING (skjult panel) ═══ --}}
						<div class="inv-tab-panel" data-panel="time-register" style="display:none;">
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
						</div>

						{{-- ═══ FIKEN ═══ --}}
						<div class="inv-tab-panel" data-panel="fiken" @if($activeTab !== 'fiken') style="display:none;" @endif>

								<?php
									$hasVipps = Auth::user()->address && Auth::user()->address->vipps_phone_number;
								?>
								@if ($hasVipps)
									<a href="javascript:void(0)" class="btn short-red-outline-btn mb-4 stopVippsEFakturaBtn" 
									data-bs-toggle="modal"
									data-bs-target="#stopVippsEFakturaModal"
									data-vipps-number="{{ optional(Auth::user()->address)->vipps_phone_number }}">
										{!! trans('site.stop-vipps-efaktura') !!}
									</a>
								@else
									<a href="javascript:void(0)" class="btn short-red-outline-btn mb-4 setVippsEFakturaBtn" 
									data-bs-toggle="modal"
									data-bs-target="#setVippsEFakturaModal"
									data-vipps-number="{{ optional(Auth::user()->address)->vipps_phone_number }}">
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

								@if(isset($unpaid) && $unpaid->count() > 0)
								<div style="background: #f8f5f0; border-left: 3px solid #b8860b; border-radius: 6px; padding: 14px 18px; margin-bottom: 20px; font-size: 13px; color: #5a4a3a; line-height: 1.6;">
									<strong style="color: #5a4a3a;">ℹ️ Om utestående beløp</strong><br>
									Totalt utestående betyr ikke at enkeltstående fakturaer har gått til forfall. Dersom du ønsker å gjøre en restinnbetaling, kan du gjøre dette på din neste faktura.
								</div>
								@endif

								<div class="card global-card">
									<div class="card-body py-0 fiken-table-wrap">
										<table class="table table-global fiken-table">
											<thead>
											<tr>
												<th style="text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; color: #5D7285;">{{ trans('site.learner.invoice-number') }}</th>
												<th style="text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; color: #5D7285;">{{ trans('site.learner.deadline') }}</th>
												<th style="text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; color: #5D7285;">{{ trans('site.learner.remainders') }}</th>
												<th style="text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; color: #5D7285;">{{ trans('site.learner.status') }}</th>
												<th class="col-hide-mobile" style="text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; color: #5D7285;">Innbetalt dato</th>
												<th style="text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; color: #5D7285;">{{ trans('site.learner.kid-number') }}</th>
												<th class="col-hide-mobile" style="text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; color: #5D7285;">{{ trans('site.learner.account-number') }}</th>
												<th class="col-hide-mobile" style="text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; color: #5D7285;">{{ trans('site.credit-note') }}</th>
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
													<td class="col-hide-mobile">
														@if($invoice->fiken_sale_payment_date)
														{{ \Carbon\Carbon::parse($invoice->fiken_sale_payment_date)->format('d.m.Y') }}
													@else
														—
													@endif
												</td>
													<td> {{ $invoice->kid_number }} </td>
													<td class="col-hide-mobile"> 9015 18 00393 </td>
													<td class="col-hide-mobile">
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
															<button class="btn btn-sm vippsFakturaBtn"
															style="margin-top:5px;background:#852635;color:#fff;border:none;border-radius:6px;font-size:11px;padding:4px 10px;"
																	data-bs-toggle="modal"
																	data-bs-target="#vippsFakturaModal"
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
                                                                                                                            class="btn btn-info btn-sm"
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

								{{-- Mobilkort for Fiken-fakturaer --}}
								<div class="fiken-mobile-cards">
									@foreach($invoices as $invoice)
										@php
											$m_txSum = $invoice->transactions->sum('amount');
											$m_balance = $invoice->fiken_balance;
											$m_status = $invoice->fiken_is_paid === 1 ? "BETALT"
												: ($invoice->fiken_is_paid === 2 ? "SENDT TIL INKASSO" : "UBETALT");
										@endphp
										<div class="fiken-card">
											<div class="fiken-card-header">
												<span class="fiken-card-nr">#{{ $invoice->invoice_number }}</span>
												@if($invoice->fiken_is_paid === 1)
													<span style="padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;background:#e8f5e9;color:#2e7d32;">{{ $m_status }}</span>
												@elseif($invoice->fiken_is_paid === 2)
													<span style="padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;background:#fff3e0;color:#e65100;">{{ $m_status }}</span>
												@elseif($invoice->fiken_is_paid === 3)
													<span style="padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;background:#f3e5f5;color:#7b1fa2;">Kreditert</span>
												@else
													<span style="padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;background:#ffebee;color:#c62828;">{{ $m_status }}</span>
												@endif
											</div>
											<div class="fiken-card-grid">
												<div>
													<div class="fiken-card-label">Frist</div>
													<div class="fiken-card-value">{{ \Carbon\Carbon::parse($invoice->fiken_dueDate)->format('d.m.Y') }}</div>
												</div>
												<div>
													<div class="fiken-card-label">Restbeløp</div>
													<div class="fiken-card-value" style="font-weight:600;color:#5F0000;">
														@if($invoice->fiken_is_paid)
															{{ \App\Http\FrontendHelpers::currencyFormat(0) }}
														@else
															{{ \App\Http\FrontendHelpers::currencyFormat($m_balance - $m_txSum) }}
														@endif
													</div>
												</div>
												<div>
													<div class="fiken-card-label">KID</div>
													<div class="fiken-card-value">{{ $invoice->kid_number }}</div>
												</div>
												<div>
													<div class="fiken-card-label">Konto</div>
													<div class="fiken-card-value">9015 18 00393</div>
												</div>
												@if($invoice->fiken_sale_payment_date)
												<div>
													<div class="fiken-card-label">Innbetalt</div>
													<div class="fiken-card-value">{{ \Carbon\Carbon::parse($invoice->fiken_sale_payment_date)->format('d.m.Y') }}</div>
												</div>
												@endif
											</div>
											<div class="fiken-card-actions">
												<a href="{{ route('learner.download.invoice', $invoice->id) }}?v={{ time() }}"
													class="btn" style="background:#5F0000;color:#fff;flex:1;">
													Last ned
												</a>
												@if($invoice->fiken_invoice_id && !$invoice->fiken_is_paid)
													<button class="btn vippsFakturaBtn" style="background:#852635;color:#fff;flex:1;"
														data-bs-toggle="modal" data-bs-target="#vippsFakturaModal"
														data-action="{{ route('learner.invoice.vipps-e-faktura', $invoice->id) }}">
														eFaktura
													</button>
												@endif
												@if($invoice->fiken_is_paid == 1)
													<a href="{{ route('learner.invoice.receipt.download', $invoice->id) }}"
														class="btn" style="background:#2e7d32;color:#fff;flex:1;">
														Kvittering
													</a>
												@endif
												@if($invoice->credit_note_url)
													<a href="{{ route('learner.download.credit-note', $invoice->id) }}"
														class="btn" style="background:#5D7285;color:#fff;flex:1;">
														Kreditnota
													</a>
												@endif
												@if(!$invoice->fiken_is_paid)
													<div class="gateway--paypal" style="width:100%;margin-top:4px;">
														<form method="POST" action="{{ route('checkout.payment.paypal', encrypt($invoice->id)) }}">
															{{ csrf_field() }}
															<button class="btn btn-primary d-block w-100">
																<i class="fa fa-paypal"></i> Betal med kort
															</button>
														</form>
													</div>
													<a href="{{ route('learner.invoice.vipps-payment', $invoice->fiken_invoice_id) }}" style="width:100%;display:block;margin-top:4px;">
														<img src="{{ asset('images-new/betal-vipps.png') }}" style="width:100%;max-width:200px;">
													</a>
												@endif
											</div>
										</div>
									@endforeach
								</div>
								<div class="float-end">
									{{ $invoices->appends(Request::all())->links('pagination.short-pagination') }}
								</div>

						</div>
						{{-- /FIKEN --}}
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
					<button type="button" class="close" data-bs-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<form method="POST" action="" onsubmit="disableSubmit(this)">
						{{ csrf_field() }}

						<div class="form-group">
							<label>Mobile Number</label>
							<input type="text" class="form-control" name="mobile_number" required>
						</div>

						<button type="submit" class="btn btn-primary float-end">{{ trans('site.send') }}</button>
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
					<button type="button" class="close" data-bs-dismiss="modal">&times;</button>
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

						<button type="submit" class="btn red-global-btn mt-3 float-end">{{ trans('site.save') }}</button>
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
					<button type="button" class="close" data-bs-dismiss="modal">&times;</button>
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

						<button type="submit" class="btn red-global-btn mt-3 float-end">{{ trans('site.delete') }}</button>
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
					<button type="button" class="close" data-bs-dismiss="modal">&times;</button>
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
					<button type="button" class="close" data-bs-dismiss="modal" style="padding: 2rem; font-size: 3rem">&times;</button>
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
							<span class="me-2">{{ trans('site.date') }}: </span> <span id="displayDate"></span>
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
									<b class="me-2">Kjøp av:</b>
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
					<button type="button" class="close" data-bs-dismiss="modal">&times;</button>
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
					<button type="button" class="close" data-bs-dismiss="modal">&times;</button>
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

						{{-- Førstebetaling --}}
						<div class="form-group" style="margin-bottom: 16px;">
							<label style="font-weight: 600; color: #2e3a59; margin-bottom: 6px; display: block;">Førstebetaling</label>
							<div style="display: flex; align-items: center; gap: 12px;">
								<div style="position: relative; max-width: 180px;">
									<input type="number" id="ppFirstPayment" name="first_payment" class="form-control" min="0" value="0"
										style="border-radius: 6px; padding-right: 32px;">
									<span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #5D7285; font-size: 13px;">kr</span>
								</div>
								<span style="font-size: 14px; color: #5D7285;">Restbeløp: <strong id="ppRemainingAmount" style="color: #5F0000;">0 kr</strong></span>
							</div>
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

						{{-- Forfallsdag --}}
						<div style="margin-bottom: 12px;">
							<label style="font-size: 13px; color: #5D7285;">Forfallsdag i måneden</label>
							<select id="ppDueDay" class="form-control" style="max-width: 180px; border-radius: 6px;">
								@for($d = 1; $d <= 28; $d++)
									<option value="{{ $d }}" {{ $d == 1 ? 'selected' : '' }}>{{ $d }}.</option>
								@endfor
							</select>
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

						<div class="text-end">
							<button type="button" class="btn" data-bs-dismiss="modal" style="margin-right: 8px;">Avbryt</button>
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

	{{-- Vis ordre modal (Kjøpshistorikk) --}}
	<div id="ohViewModal" class="modal fade" tabindex="-1">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content" style="border-radius:12px;overflow:hidden;">
				<div class="modal-header" style="background:#5F0000;color:#fff;border:none;padding:16px 24px;">
					<h5 class="modal-title" style="font-weight:600;">Ordredetaljer</h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body" style="padding:24px;">
					<div class="row mb-3">
						<div class="col-6">
							<img src="/images-new/logo-tagline.png" alt="Logo" style="height:60px;object-fit:contain;">
						</div>
						<div class="col-6 text-end" id="ohViewCustomer"></div>
					</div>
					<hr>
					<div class="row mb-3">
						<div class="col-sm-6">
							<div style="font-size:12px;color:#5D7285;">Ordrenummer</div>
							<div style="font-weight:600;" id="ohViewOrderNr"></div>
						</div>
						<div class="col-sm-3">
							<div style="font-size:12px;color:#5D7285;">Dato</div>
							<div id="ohViewDate"></div>
						</div>
						<div class="col-sm-3">
							<div style="font-size:12px;color:#5D7285;">Betalingsplan</div>
							<div id="ohViewPlan"></div>
						</div>
					</div>
					<hr>
					<table class="table" style="margin-bottom:0;">
						<thead><tr>
							<th style="font-size:12px;color:#5D7285;">Beskrivelse</th>
							<th style="font-size:12px;color:#5D7285;">MVA</th>
							<th style="font-size:12px;color:#5D7285;">Antall</th>
							<th style="font-size:12px;color:#5D7285;text-align:right;">Pris</th>
							<th style="font-size:12px;color:#5D7285;text-align:right;">Sum</th>
						</tr></thead>
						<tbody>
							<tr>
								<td id="ohViewProduct" style="font-weight:600;"></td>
								<td id="ohViewVat"></td>
								<td>1 stk</td>
								<td style="text-align:right;" id="ohViewPrice"></td>
								<td style="text-align:right;font-weight:600;" id="ohViewTotal"></td>
							</tr>
						</tbody>
					</table>
					<hr>
					<div class="text-end">
						<span style="font-size:12px;color:#5D7285;">Totalt å betale:</span>
						<span style="font-size:20px;font-weight:700;color:#5F0000;margin-left:8px;" id="ohViewGrandTotal"></span>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- Rediger bedrift modal (Kjøpshistorikk) --}}
	<div id="ohCompanyModal" class="modal fade" tabindex="-1">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content" style="border-radius:12px;overflow:hidden;">
				<div class="modal-header" style="background:#5F0000;color:#fff;border:none;padding:16px 24px;">
					<h5 class="modal-title" style="font-weight:600;">Rediger Bedrift</h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body" style="padding:24px;">
					<input type="hidden" id="ohCompanyOrderId">
					<input type="hidden" id="ohCompanyId">
					<div class="mb-3">
						<label class="form-label" style="font-size:13px;font-weight:600;">Organisasjonsnummer</label>
						<input type="text" class="form-control" id="ohCompanyCustNr" style="border-radius:8px;">
					</div>
					<div class="mb-3">
						<label class="form-label" style="font-size:13px;font-weight:600;">Bedriftsnavn</label>
						<input type="text" class="form-control" id="ohCompanyName" style="border-radius:8px;">
					</div>
					<div class="mb-3">
						<label class="form-label" style="font-size:13px;font-weight:600;">Adresse</label>
						<input type="text" class="form-control" id="ohCompanyStreet" style="border-radius:8px;">
					</div>
					<div class="row">
						<div class="col-4 mb-3">
							<label class="form-label" style="font-size:13px;font-weight:600;">Postnr</label>
							<input type="text" class="form-control" id="ohCompanyPostNr" style="border-radius:8px;">
						</div>
						<div class="col-8 mb-3">
							<label class="form-label" style="font-size:13px;font-weight:600;">Sted</label>
							<input type="text" class="form-control" id="ohCompanyPlace" style="border-radius:8px;">
						</div>
					</div>
					<button class="btn w-100" id="ohCompanySaveBtn"
						style="background:#5F0000;color:#fff;border:none;border-radius:8px;padding:10px;font-weight:600;">
						Lagre
					</button>
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
            // 24 mnd fjernet - bruker kun planer fra DB
        @endphp
        const allPaymentPlans = @json($ppPlans);

        let ppTotal = 0;
        let ppFirstPayment = 0;
        let ppSelectedMonths = 0;
        let ppSelectedPlanId = null;

        function formatKr(amount) {
            return Math.round(amount).toLocaleString('nb-NO') + ' kr';
        }

        function renderMonthCards(allowedPlanIds) {
            var container = $('#ppMonthCards');
            container.empty();
            var remaining = ppTotal - ppFirstPayment;
            if (remaining < 0) remaining = 0;

            var plans = allPaymentPlans.filter(function(p) {
                return allowedPlanIds.indexOf(p.id) !== -1;
            });

            if (!plans.length) {
                container.html('<p class="text-muted">Ingen betalingsplaner tilgjengelig.</p>');
                return;
            }

            plans.forEach(function(plan) {
                var monthly = plan.division > 1 ? Math.floor(remaining / plan.division) : remaining;
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
            var remaining = ppTotal - ppFirstPayment;
            if (remaining < 0) remaining = 0;

            if (!months || months < 1 || !ppTotal) {
                $('#ppMonthlyRow').hide();
                $('#ppPreviewSection').hide();
                return;
            }

            var monthly = Math.floor(remaining / months);
            var rest = remaining - (monthly * months);

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
            var rowNum = 0;

            // Førstebetaling-rad
            if (ppFirstPayment > 0) {
                rowNum++;
                runningTotal += ppFirstPayment;
                var todayStr = ('0' + baseDate.getDate()).slice(-2) + '.' +
                               ('0' + (baseDate.getMonth() + 1)).slice(-2) + '.' +
                               baseDate.getFullYear();
                tbody.append(
                    '<tr style="background:#FFEEE8;">' +
                    '<td style="padding:8px 12px;font-weight:600;">Førstebetaling</td>' +
                    '<td style="padding:8px 12px;font-weight:600;">' + formatKr(ppFirstPayment) + '</td>' +
                    '<td style="padding:8px 12px;">' + todayStr + '</td>' +
                    '</tr>'
                );
            }

            for (var i = 1; i <= months; i++) {
                rowNum++;
                var amount = monthly;
                if (i === months) {
                    amount = monthly + rest; // siste faktura får resten
                }
                runningTotal += amount;

                var dueDay = parseInt($('#ppDueDay').val()) || new Date().getDate();
                var dueDate = new Date(baseDate);
                dueDate.setMonth(dueDate.getMonth() + i);
                dueDate.setDate(Math.min(dueDay, 28));
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
            var closeButtons = modal.find('[data-bs-dismiss="modal"], .close');
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

        // Forfallsdag endring → oppdater forhåndsvisning
        $('#ppDueDay').on('change', function() {
            if (ppSelectedMonths > 0) updatePreview(ppSelectedMonths);
        });

        // Førstebetaling input
        $('#ppFirstPayment').on('input', function() {
            var val = parseFloat($(this).val()) || 0;
            if (val < 0) val = 0;
            if (val >= ppTotal) val = ppTotal - 1;
            ppFirstPayment = val;
            $('#ppRemainingAmount').text(formatKr(ppTotal - ppFirstPayment));

            // Oppdater månedskort med nye beløp
            var allowedPlanIds = [];
            try {
                allowedPlanIds = JSON.parse(window._ppLastAllowedPlanIds || '[]');
            } catch(e) {}
            renderMonthCards(allowedPlanIds);

            // Re-select current plan
            if (ppSelectedPlanId) {
                $('.pp-month-card[data-plan-id="' + ppSelectedPlanId + '"]').css('border-color', '#5F0000');
            }
            updatePreview();
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
        $(".createInvoiceBtn").click(function(event) {
            var action = $(this).data('action');
            var modal = $("#createInvoiceModal");
            modal.find('form').attr('action', action);

            // Total
            ppTotal = parseFloat($(this).attr('data-total')) || 0;
            $('#ppTotalAmount').text(formatKr(ppTotal));

            // Reset
            ppSelectedPlanId = null;
            ppSelectedMonths = 0;
            ppFirstPayment = 0;
            $('#ppFirstPayment').val(0);
            $('#ppRemainingAmount').text(formatKr(ppTotal));
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

            // Lagre allowedPlanIds for bruk i førstebetaling-handler
            window._ppLastAllowedPlanIds = JSON.stringify(allowedPlanIds);

            // Render månedskort
            renderMonthCards(allowedPlanIds);

            // Forhåndsvelg første kort
            var firstCard = $('#ppMonthCards .pp-month-card').first();
            if (firstCard.length) {
                selectPlan(parseInt(firstCard.data('plan-id'), 10), parseInt(firstCard.data('division'), 10));
            }
        });
        // ═══ Kjøpshistorikk — Vis ordre modal ═══
        $(document).on('click', '.ohViewBtn', function() {
            var order = $(this).data('order');
            if (!order) return;
            $('#ohViewOrderNr').text(String(order.id).padStart(6, '0'));
            $('#ohViewDate').text(order.created_at_formatted || '');
            $('#ohViewPlan').text(order.payment_plan ? order.payment_plan.plan : '-');
            $('#ohViewProduct').text(order.packageVariation || order.item || '');
            $('#ohViewVat').text([2,7,9,10].includes(order.type) ? '25%' : '0%');
            $('#ohViewPrice').text(order.price_formatted || '');
            $('#ohViewTotal').text(order.total_formatted || '');
            $('#ohViewGrandTotal').text(order.total_formatted || '');

            var custHtml = '';
            if (order.company && order.company.company_name) {
                custHtml = '<div style="font-weight:600;">' + order.company.company_name + '</div>'
                    + '<div>' + (order.company.street_address || '') + '</div>'
                    + '<div>' + (order.company.post_number || '') + ' ' + (order.company.place || '') + '</div>';
            }
            $('#ohViewCustomer').html(custHtml);
        });

        // ═══ Kjøpshistorikk — Rediger bedrift modal ═══
        $(document).on('click', '.ohCompanyBtn', function() {
            var orderId = $(this).data('order-id');
            var company = $(this).data('company') || {};
            $('#ohCompanyOrderId').val(orderId);
            $('#ohCompanyId').val(company.id || '');
            $('#ohCompanyCustNr').val(company.customer_number || '');
            $('#ohCompanyName').val(company.company_name || '');
            $('#ohCompanyStreet').val(company.street_address || '');
            $('#ohCompanyPostNr').val(company.post_number || '');
            $('#ohCompanyPlace').val(company.place || '');
        });

        $('#ohCompanySaveBtn').on('click', function() {
            var orderId = $('#ohCompanyOrderId').val();
            var data = {
                id: $('#ohCompanyId').val(),
                order_id: orderId,
                customer_number: $('#ohCompanyCustNr').val(),
                company_name: $('#ohCompanyName').val(),
                street_address: $('#ohCompanyStreet').val(),
                post_number: $('#ohCompanyPostNr').val(),
                place: $('#ohCompanyPlace').val()
            };
            $.ajax({
                url: '/account/order/' + orderId + '/save-company',
                method: 'POST',
                data: data,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function() {
                    bootstrap.Modal.getInstance(document.getElementById('ohCompanyModal')).hide();
                    window.swal ? window.swal.fire({ icon:'success', title:'Lagret!', timer:1500, showConfirmButton:false })
                                : alert('Lagret!');
                    location.reload();
                },
                error: function() {
                    alert('Kunne ikke lagre. Prøv igjen.');
                }
            });
        });

        // ═══ Tab-bytte uten side-reload ═══
        $('#invoiceTabs').on('click', 'a[data-tab]', function(e) {
            e.preventDefault();
            e.stopPropagation(); // Hindrer at document-click-handler kollapser sidebaren
            var tab = $(this).data('tab');

            // Oppdater aktiv fane i navigasjonen
            $('#invoiceTabs .nav-link').removeClass('active');
            $(this).addClass('active');

            // Vis/skjul paneler
            $('.inv-tab-panel').hide();
            $('.inv-tab-panel[data-panel="' + tab + '"]').show();

            // Oppdater URL uten reload
            var url = new URL(window.location);
            if (tab === 'fiken') {
                url.searchParams.delete('tab');
            } else {
                url.searchParams.set('tab', tab);
            }
            history.pushState({tab: tab}, '', url);
        });

        // Håndter tilbake/frem i nettleseren
        window.addEventListener('popstate', function(e) {
            var tab = (e.state && e.state.tab) ? e.state.tab : 'fiken';
            var url = new URL(window.location);
            if (!tab || tab === 'fiken') {
                tab = url.searchParams.get('tab') || 'fiken';
            }

            $('#invoiceTabs .nav-link').removeClass('active');
            $('#invoiceTabs a[data-tab="' + tab + '"]').addClass('active');
            $('.inv-tab-panel').hide();
            $('.inv-tab-panel[data-panel="' + tab + '"]').show();
        });
	</script>
@stop
