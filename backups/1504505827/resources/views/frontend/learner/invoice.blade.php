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
								<th>Status</th>
								<th>Opprettet</th>
							</tr>
						</thead>
						<tbody>
							@foreach(Auth::user()->invoices as $invoice)
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
							<tr>
								<td><a href="{{route('learner.invoice.show', $invoice->id)}}">{{$fikenInvoice->invoiceNumber}}</a></td>
								<td>
									@if($sale->paid)
									<span class="label label-success">{{$status}}</span>
									@else
									<span class="label label-danger">{{$status}}</span>
									@endif
								</td>
								<td>{{$invoice->created_at}}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
</div>

@stop
