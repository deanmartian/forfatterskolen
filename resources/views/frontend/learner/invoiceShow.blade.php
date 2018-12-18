@extends('frontend.layout')

<?php
$fikenURL = false;
foreach( $fikenInvoices as $fikenInvoice ) :
    if( $invoice->fiken_url == $fikenInvoice->_links->alternate->href ) :
      $fikenURL = true;
      break;
    endif;
endforeach;
$fikenError = false;
if( $fikenURL ) :
  $sale = FrontendHelpers::FikenConnect($fikenInvoice->sale);
  $status = $sale->paid ? "BETALT" : "UBETALT";
else :
  $fikenError = true;
endif;
?>

@section('title')
<title>Faktura #{{$fikenInvoice->invoiceNumber}} &rsaquo; Forfatterskolen</title>
@stop


@section('content')
<div class="account-container">
	
	@include('frontend.partials.learner-menu')
	
	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="margin-bottom">Faktura #{{$fikenInvoice->invoiceNumber}}</h2>
					Status: 
					@if($sale->paid)
					<span class="label label-success">{{$status}}</span>
					@else
					<span class="label label-danger">{{$status}}</span>
					@endif <br />
					Opprettet: {{$fikenInvoice->issueDate}} <br />
					Forfallsdato: {{$fikenInvoice->dueDate}}
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-12 col-md-7">
       						<embed src="{{$invoice->pdf_url}}" width="100%" height="500px" />
							<br />
       					</div>
						<div class="col-sm-12 col-md-5">
							<div class="margin-bottom"><strong>Transaksjoner</strong></div>
							<?php $balance = (double)$fikenInvoice->gross/100; $total = 0;?>
							<div class="table-responsive">
								<table class="table table-striped">
									<thead>
										<tr>
											<th>Metode</th>
											<th>Betalings metode</th>
											<th class="text-right">Beløp</th>
										</tr>
									</thead>
									<tbody>
										@if(count($invoice->transactions) > 0)
										@foreach($invoice->transactions as $transaction)
										<tr>
											<td>{{$transaction->mode}}</td>
											<td>{{$transaction->mode_transaction}}</td>
											<td class="text-right">{{FrontendHelpers::currencyFormat($transaction->amount)}}</td>
										</tr>
										<?php $total += $transaction->amount; ?>
										@endforeach
										<tr class="text-right">
											<td colspan="2"><strong>Totalt</strong></td>
											<td>{{FrontendHelpers::currencyFormat($total)}}</td>
										</tr>
										@else
										<tr class="text-center text-muted">
											<td colspan="3">Ingen transaksjoner</td>
										</tr>
										@endif
										<tr class="text-right">
											<td colspan="3">
												<h4>
												<strong>Balanse:&nbsp;&nbsp;
												@if( $sale->paid )
												{{FrontendHelpers::currencyFormat(0)}}
												@else
												{{FrontendHelpers::currencyFormat($balance - $total)}}
												@endif
												</strong>
												</h4>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							@if($balance - $total > 0 )
							<form name="_xclick" id="paypal_form" action="https://www.paypal.com/cgi-bin/webscr" method="post">
				                <input type="hidden" name="cmd" value="_xclick">
				                <input type="hidden" name="business" value="post.forfatterskolen@gmail.com">
				                <input type="hidden" name="currency_code" value="NOK">
				                <input type="hidden" name="custom" value="{{$invoice->id}}">
				                <input type="hidden" name="item_name" value="Course Order Invoice">
				                <input type="hidden" name="amount" value="{{ $balance }}">
				                <input type="hidden" name="return" value="{{route('learner.invoice.show', ['id' => $invoice->id])}}">
				                <input type="image" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="right" alt="PayPal - The safer, easier way to pay online">
				            </form>
				            @endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
</div>

@stop
