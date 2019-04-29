@extends('frontend.layout')

@section('title')
<title>Invoices &rsaquo; Forfatterskolen</title>
@stop

@section('heading') {{ trans('site.learner.my-invoice') }} @stop

@section('content')
	<div class="learner-container">
		<div class="container">
			<div class="row">
				@include('frontend.partials.learner-search-new')
			</div> <!-- end row -->

			<div class="row mt-5">
				<div class="col-sm-12">
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
									<th></th>
								</tr>
								</thead>
								<tbody>
								@foreach($invoices as $invoice)
                                    <?php
                                    $transactions_sum = $invoice->transactions->sum('amount');
                                    $balance = $invoice->fiken_balance;
                                    $status = $invoice->fiken_is_paid ? "BETALT" : "UBETALT";
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
											@if($invoice->fiken_is_paid)
												<span class="label label-success">{{$status}}</span>
											@else
												<span class="label label-danger">{{$status}}</span>
											@endif
										</td>
										<td>{{date_format(date_create($invoice->created_at), 'M d, Y H.i')}}</td>
										<td> {{ $invoice->kid_number }} </td>
										<td> 9015 18 00393 </td>
										<td>
											<a href="{{$invoice->pdf_url}}">{{ trans('site.learner.download-invoice') }}</a>

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

												<a href="{{ route('learner.invoice.vipps-payment', $invoice->invoice_number) }}" class="mt-3">
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
				</div> <!-- end col-sm-12 -->
			</div> <!-- end row -->
		</div> <!-- end container-->
	</div>

	@if ( $errors->any() )
		<div class="alert alert-danger" role="alert" id="fixed_to_bottom_alert">
			<a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
			<ul>
				@foreach($errors->all() as $error)
					<li>{{$error}}</li>
				@endforeach
			</ul>
		</div>
	@endif
@stop
