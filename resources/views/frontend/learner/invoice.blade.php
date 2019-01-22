@extends('frontend.layout')

@section('title')
<title>Invoices &rsaquo; Forfatterskolen</title>
@stop

@section('heading') Mine Fakturaer @stop

@section('content')
	<div class="learner-container">
		<div class="container">
			<div class="row">
				@include('frontend.partials.learner-search-new')
			</div> <!-- end row -->

			<div class="row mt-5">
				<div class="col-sm-12">
					<div class="card global-card">
						<table class="table table-global">
							<thead>
								<tr>
									<th>Fakturanummer</th>
									<th>Frist</th>
									<th>Restbeløp</th>
									<th>Status</th>
									<th>Opprettet</th>
									<th>Kid Nummer</th>
									<th>Konto Nummer</th>
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
										<a href="{{$invoice->pdf_url}}">Download</a>
										{{ $invoice->gross }}
										{{--@if($Pbalance - $total > 0 )
											<form name="_xclick" id="paypal_form" action="https://www.paypal.com/cgi-bin/webscr" method="post">
												<input type="hidden" name="cmd" value="_xclick">
												<input type="hidden" name="business" value="post.forfatterskolen@gmail.com">
												<input type="hidden" name="currency_code" value="NOK">
												<input type="hidden" name="custom" value="{{$invoice->id}}">
												<input type="hidden" name="item_name" value="Course Order Invoice">
												<input type="hidden" name="amount" value="{{ $Pbalance }}">
												<input type="hidden" name="return" value="{{route('learner.invoice.show', ['id' => $invoice->id])}}">
												<input type="image" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="right" alt="PayPal - The safer, easier way to pay online">
											</form>
										@endif--}}
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div> <!-- end card -->
					<div class="float-right">
						{{ $invoices->render() }}
					</div>
				</div> <!-- end col-sm-12 -->
			</div> <!-- end row -->
		</div> <!-- end container-->
	</div>
@stop
