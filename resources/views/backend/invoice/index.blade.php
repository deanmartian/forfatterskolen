@extends('backend.layout')

@section('title')
<title>Invoices &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
	<style>
		.center-area {
			display: inline-block;
			position: absolute;
			left: 40%;
		}
	</style>
@stop

@section('content')

	<div class="page-toolbar" style="position: relative;">
		<h3><i class="fa fa-file-o"></i> {{ trans_choice('site.invoices', 2) }}</h3>

		<div class="center-area centered">
			<h3>
				{{ trans('site.balance') }}: {{ \App\Http\FrontendHelpers::currencyFormat($totalBalance - $totalPaid) }}
			</h3>
		</div>

		<div class="navbar-form navbar-right">
			<div class="form-group">
				<form role="search" method="GET">
					<div style="background: #fff; cursor: pointer; padding: 8px 10px;
					border: 1px solid #ccc; width: 100%; display: inline; margin-right: 5px">
						<i class="fa fa-file"></i>&nbsp;
						<input type="text" name="fiken_invoice_id" style="border: none; width: 180px" 
						placeholder="Search fiken invoice id" value="{{ Request::has('fiken_invoice_id') ? Request::get('fiken_invoice_id') : '' }}"/>
					</div>

					<button class="btn btn-default" type="submit" style="margin-right: 10px"><i class="fa fa-search"></i></button>
				</form>
			</div>

			<div class="form-group">
				<form role="search" method="GET">
					<div id="reportrange" style="background: #fff; cursor: pointer; padding: 8px 10px;
					border: 1px solid #ccc; width: 100%; display: inline; margin-right: 5px">
						<i class="fa fa-calendar"></i>&nbsp;
						<input type="text" name="dates" style="border: none; width: 180px"/> <i class="fa fa-caret-down"></i>
					</div>

					<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
				</form>
			</div>
		</div>
	</div>

<div class="col-md-12">
	<div class="table-users table-responsive">
		<table class="table">
			<thead>
		    	<tr>
			        <th>{{ trans('site.invoice-nr') }}</th>
			        <th>Fiken Invoice Id</th>
			        <th>{{ trans_choice('site.learners', 1) }}</th>
			        <th>{{ trans('site.status') }}</th>
			        <th>{{ trans('site.pdf-url') }}</th>
					<th>Amount</th>
					<th>{{ trans('site.due-date') }}</th>
			        <th>{{ trans('site.date-created') }}</th>
		      	</tr>
		    </thead>

		    <tbody>
		    	@foreach($invoices as $invoice)
		    	<tr>
		    		<td>
						<a href="{{route('admin.invoice.show', $invoice->id)}}">{{$invoice->invoice_number}}</a>
		    		</td>
					<td>
						{{ $invoice->fiken_invoice_id }}
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
					<td>{{ \App\Http\FrontendHelpers::currencyFormat($invoice->fiken_balance) }}</td>
					<td>{{ $invoice->fiken_dueDate }}</td>
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

@section('scripts')
	<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

	<script>
        $(function() {
            let start = moment('{{ $startDate }}');
            let end = moment('{{ $endDate }}');

            function cb(start, end) {
                $('#reportrange').find('input').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);

            cb(start, end);

        });
	</script>
@stop