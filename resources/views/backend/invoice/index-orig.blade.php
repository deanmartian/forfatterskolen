@extends('backend.layout')

@section('title')
<title>Invoices &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')

<div class="col-md-12">
	<div class="table-users table-responsive">
		<table class="table">
			<thead>
		    	<tr>
			        <th>Invoice #</th>
			        <th>Learner</th>
			        <th>Status</th>
			        <th>PDF URL</th>
			        <th>Date Created</th>
		      	</tr>
		    </thead>

		    <tbody>
		    	@foreach($invoices as $invoice)
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
		    		<td>
		    			@if( !$fikenError )
						<a href="{{route('admin.invoice.show', $invoice->id)}}">{{$fikenInvoice->invoiceNumber}}</a>
						@else
		    			<a style="color: red" href="{{route('admin.invoice.show', $invoice->id)}}">Error in Fiken URL</a>
						@endif
		    		</td>
					<td><a href="{{route('admin.learner.show', $invoice->user->id)}}">{{$invoice->user->fullname}}</a></td>
		    		<td>
		    			@if( !$fikenError )
						@if($sale->paid)
						<span class="label label-success">{{$status}}</span>
						@else
						<span class="label label-danger">{{$status}}</span>
						@endif
						@endif
					</td>
		    		<td><a href="{{$invoice->pdf_url}}" target="_blank">View PDF</a></td>
		    		<td>{{$invoice->created_at}}</td>
		      	</tr>
		      	@endforeach
		    </tbody>
		</table>
	</div>
	
	<div class="pull-right">{{$invoices->render()}}</div>
	<div class="clearfix"></div>
</div>

@stop