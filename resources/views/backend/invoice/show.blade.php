@extends('backend.layout')

@section('title')
<title>Invoice #{{$invoice->invoice_number}} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="margin-top">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="row">
					<div class="col-sm-12 col-md-6">
						<embed src="{{$invoice->pdf_url}}" style="width: 100%; height: 600px"></embed>
					</div>
					<div class="col-sm-12 col-md-6">
            <div class="pull-right">
              <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editInvoiceModal"><i class="fa fa-pencil"></i></button>
              <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteInvoiceModal"><i class="fa fa-trash"></i></button>
            </div>

						<h3>Invoice #{{$invoice->invoice_number}}</h3><br />
            Learner: <a href="{{route('admin.learner.show', $invoice->user->id)}}">{{$invoice->user->fullname}}</a> <br />
						Status: 
						@if($invoice->fiken_is_paid)
						<span class="label label-success">BETALT</span>
						@else
						<span class="label label-danger">UBETALT</span>
						@endif <br />
						Created at: {{$invoice->fiken_issueDate}} <br />
						Due Date: {{$invoice->fiken_dueDate}}



						<div class="margin-top margin-bottom"><strong>Transactions</strong></div>

						<?php $balance = $invoice->fiken_balance; $total = 0;?>

						<div class="table-responsive">
							<table class="table table-side-bordered">
								<thead>
									<tr>
										<th>Mode</th>
										<th>Mode Transaction</th>
										<th class="text-right">Amount</th>
										<th class="text-right"></th>
									</tr>
								</thead>
								<tbody>
									@if(count($invoice->transactions) > 0)
									@foreach($invoice->transactions as $transaction)
									<tr>
										<td>{{$transaction->mode}}</td>
										<td>{{$transaction->mode_transaction}}</td>
										<td class="text-right">{{FrontendHelpers::currencyFormat($transaction->amount)}}</td>
										<td class="text-right">
											<button type="button" data-toggle="modal" data-target="#editTransactionModal" class="btn btn-info btn-xs btn-edit-transaction" data-id="{{$transaction->id}}" data-mode="{{$transaction->mode}}" data-mode-transaction="{{$transaction->mode_transaction}}" data-amount="{{$transaction->amount}}" data-action="{{route('admin.transaction.update', ['invoice_id' => $invoice->id, 'transaction_id' => $transaction->id])}}"><i class="fa fa-pencil"></i></button>

											<button type="button" data-toggle="modal" data-target="#deleteTransactionModal" class="btn btn-danger btn-xs btn-delete-transaction" data-id="{{$transaction->id}}" data-action="{{route('admin.transaction.destroy', ['invoice_id' => $invoice->id, 'transaction_id' => $transaction->id])}}"><i class="fa fa-trash"></i></button>
										</td>
									</tr>
									<?php $total += $transaction->amount; ?>
									@endforeach
									<tr class="text-right">
										<td colspan="2"><strong>Total</strong></td>
										<td>{{FrontendHelpers::currencyFormat($total)}}</td>
										<td></td>
									</tr>
									@else
									<tr class="text-center text-muted">
										<td colspan="4">No transactions</td>
									</tr>
									@endif

									<tr class="text-right">
										<td colspan="3"><h4><strong>Balance:&nbsp;&nbsp;
					@if($invoice->fiken_is_paid)
                    {{FrontendHelpers::currencyFormat(0)}}
                    @else
                    {{FrontendHelpers::currencyFormat($balance - $total)}}
                    @endif
                    </strong></h4></td>
										<td></td>
									</tr>

								</tbody>
							</table>
						</div>
						<button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#addTransactionModal">+ Add Transaction</button>
            @if ( $errors->any() )
            <br />
            <br />
            <div class="alert alert-danger no-bottom-margin">
                <ul>
                @foreach($errors->all() as $error)
                <li>{{$error}}</li>
                @endforeach
                </ul>
            </div>
            @endif
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="addTransactionModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Transaction</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{route('admin.transaction.store', $invoice->id)}}">
      		{{csrf_field()}}
      		<div class="form-group">
      			<label>Mode of Payment</label>
      			<select class="form-control" name="mode" required>
      				<option value="" disabled selected>- Select Mode -</option>
      				<option value="Paypal">Paypal</option>
      				<option value="Bank Deposit">Bank Deposit</option>
      			</select>
      		</div>
      		<div class="form-group">
      			<label>Transaction ID</label>
      			<input type="text" class="form-control" name="mode_transaction" required>
      		</div>
      		<div class="form-group">
      			<label>Amount</label>
				<input type="number" step="0.01" min="0.01" class="form-control" name="amount" required>
      		</div>
  			<button type="submit" class="btn btn-primary pull-right">Add Transaction</button>
  			<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>


<div id="editTransactionModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Transaction</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="">
      		{{csrf_field()}}
      		<input type="hidden" name="transaction_id">
      		<div class="form-group">
      			<label>Mode of Payment</label>
      			<select class="form-control" name="mode" required>
      				<option value="" disabled selected>- Select Mode -</option>
      				<option value="Paypal">Paypal</option>
      				<option value="Bank Deposit">Bank Deposit</option>
      			</select>
      		</div>
      		<div class="form-group">
      			<label>Transaction ID</label>
      			<input type="text" class="form-control" name="mode_transaction" required>
      		</div>
      		<div class="form-group">
      			<label>Amount</label>
				<input type="number" step="0.01" min="0.01" class="form-control" name="amount" required>
      		</div>
  			<button type="submit" class="btn btn-primary pull-right">Update Transaction</button>
  			<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>


<div id="deleteTransactionModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Delete Transaction</h4>
      </div>
      <div class="modal-body">
      	Are you sure to delete this transaction?
      	<form method="POST" action="">
      		{{csrf_field()}}
      		<input type="hidden" name="transaction_id">
  			<button type="submit" class="btn btn-danger pull-right margin-top">Delete Transaction</button>
  			<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>


<div id="editInvoiceModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Invoice #{{$invoice->invoice_number}}</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('admin.invoice.update', $invoice->id) }}">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <input type="hidden" name="learner_id" value="{{ $invoice->user->id }}">
          <div class="form-group">
            <label>Fiken URL</label>
            <input type="text" name="fiken_url" class="form-control" value="{{ $invoice->fiken_url }}" required>
          </div>
          <div class="form-group">
            <label>PDF URL</label>
            <input type="text" name="pdf_url" class="form-control" value="{{ $invoice->pdf_url }}" required>
          </div>
          <div class="form-group">
            <label>Balance</label>
            <input type="number" step="0.01" name="balance" class="form-control" value="{{ $invoice->balance }}">
          </div>
          <button type="submit" class="btn btn-primary pull-right">Update Invoice</button>
          <div class="clearfix"></div>
        </form>
      </div>
    </div>

  </div>
</div>



<div id="deleteInvoiceModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h4>Delete Invoice</h4>
      </div>
      <div class="modal-body">
        Are you sure to delete this invoice?
        <div class="text-right margin-top">
          <form method="POST">
            {{ csrf_field() }}
            {{ method_field('DELETE') }}
            <button type="submit" class="btn btn-danger">Delete Invoice</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@stop


@section('scripts')
<script>
  $('.select_course').on('change', function(){
    var packages = $('option:selected', this).data('packages');
    var package_select = $('#editInvoiceModal select[name=package_id]');
    package_select.empty();
    package_select.append('<option disabled selected value="">- Select package -</option>');
    for(var i=0; i < packages.length; i++){
      package_select.append('<option value="' + packages[0]['id'] + '">' + packages[0]['variation'] + ' (' + packages[0]['price'] + ')</option>');
    }
  });
</script>
@stop