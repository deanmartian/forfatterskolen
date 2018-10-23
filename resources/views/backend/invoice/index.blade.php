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
			        <th>{{ trans('site.invoice-nr') }}</th>
			        <th>{{ trans_choice('site.learners', 1) }}</th>
			        <th>{{ trans('site.status') }}</th>
			        <th>{{ trans('site.pdf-url') }}</th>
			        <th>{{ trans('site.date-created') }}</th>
		      	</tr>
		    </thead>

		    <tbody>
		    	@foreach($invoices as $invoice)
		    	<tr>
		    		<td>
						<a href="{{route('admin.invoice.show', $invoice->id)}}">{{$invoice->invoice_number}}</a>
		    		</td>
					<td><a href="{{route('admin.learner.show', $invoice->user->id)}}">{{$invoice->user->fullname}}</a></td>
		    		<td>
						@if($invoice->fiken_is_paid)
							<span class="label label-success">BETALT</span>
						@else
							<span class="label label-danger">UBETALT</span>
						@endif
					</td>
		    		<td><a href="{{$invoice->pdf_url}}" target="_blank">{{ trans('site.view-pdf') }}</a></td>
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