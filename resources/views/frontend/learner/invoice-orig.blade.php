@extends('frontend.layout')

@section('title')
<title>Invoices &rsaquo; Forfatterskolen</title>
@stop


@section('content')
<div class="account-container">
	
	@include('frontend.partials.learner-menu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2>Mine Fakturaer</h2>
				</div>
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>Fakturanummer</th>
								<th>Frist</th>
								<th>Restbeløp</th>
								<th>Status</th>
								<th>Opprettet</th>
								<th>Kid Nummer</th>
								<th>Konto Nummer</th>
							</tr>
						</thead>
						<tbody>
							@foreach($invoices as $invoice)
							<?php
							/*$fikenURL = false;
							foreach( $fikenInvoices as $fikenInvoice ) :
							    if( $invoice->fiken_url == $fikenInvoice->_links->alternate->href ) :
							      $fikenURL = true;
							      break;
							    endif;
							endforeach;*/
							/*$fikenError = false;
							if( $fikenURL ) :
							  	$sale = FrontendHelpers::FikenConnect($fikenInvoice->sale);
							  	$status = $sale->paid ? "BETALT" : "UBETALT";
							  	$balance = (double)$fikenInvoice->gross/100;
							else :
							  	$fikenError = true;
							endif;*/
                            $transactions_sum = $invoice->transactions->sum('amount');

                            // remove if the above code is uncomment
							$balance = $invoice->fiken_balance;
                            $status = $invoice->fiken_is_paid ? "BETALT" : "UBETALT";
							?>
							<tr>
								<td><a href="{{route('learner.invoice.show', $invoice->id)}}">{{$invoice->invoice_number}}</a></td>
								<td>{{ \Carbon\Carbon::parse($invoice->fiken_dueDate)->format('d.m.Y') }}</td>
								<td>
									@if(/*$sale->paid*/ $invoice->fiken_is_paid)
									{{FrontendHelpers::currencyFormat(0)}}
									@else
									{{FrontendHelpers::currencyFormat($balance - $transactions_sum)}}
									@endif
								</td>
								<td>
									@if(/*$sale->paid*/ $invoice->fiken_is_paid)
									<span class="label label-success">{{$status}}</span>
									@else
									<span class="label label-danger">{{$status}}</span>
									@endif
								</td>
								<td>{{date_format(date_create($invoice->created_at), 'M d, Y H.i')}}</td>
								<td> {{ $invoice->kid_number }} </td>
								<td> 9015 18 00393 </td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>

				<div class="pull-right">
					{{ $invoices->render() }}
				</div>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
</div>

@stop
