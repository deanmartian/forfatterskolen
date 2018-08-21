@extends('backend.layout')

@section('title')
<title>Shop Manuscripts &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3><i class="fa fa-file-text-o"></i> All Shop Manuscripts</h3>
</div>

<div class="col-md-12">
 	<ul class="nav nav-tabs margin-top">
	    <li @if( Request::input('tab') != 'sold' ) class="active" @endif><a href="?tab=all">Shop Manuscripts</a></li>
	    <li @if( Request::input('tab') == 'sold' ) class="active" @endif><a href="?tab=sold">Sold Shop Manuscripts</a></li>
  	</ul>
	<div class="tab-content">
	  	<div class="tab-pane fade in active">
	  		@if( Request::input('tab') != 'sold' )
	  		<div class="panel panel-default no-padding-bottom" style="border-top: 0">
		  		<div class="panel-body">
					<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addShopManuscriptModal">Add Shop Manuscript</button>
					<div class="table-users table-responsive">
						<table class="table no-margin-bottom">
							<thead>
						    	<tr>
							        <th>Title</th>
							        <th>Description</th>
							        <th>Max Words</th>
							        <th>Price</th>
							        <th>Split Payment Price</th>
							        <th>Fiken Product ID</th>
							        <th></th>
						      	</tr>
						    </thead>

						    <tbody>
						    	@foreach($shopManuscripts as $shopManuscript)
						    	<tr>
									<td>{{ $shopManuscript->title }}</td>
									<td>{{ $shopManuscript->description }}</td>
									<td>{{ $shopManuscript->max_words }}</td>
									<td>{{ FrontendHelpers::currencyFormat($shopManuscript->price) }}</td>
									<td>{{ FrontendHelpers::currencyFormat($shopManuscript->split_payment_price) }}</td>
									<td>{{ $shopManuscript->fiken_product }}</td>
									<td>
										<button type="button" class="btn btn-info btn-xs editShopManuscriptBtn" data-toggle="modal" data-target="#editShopManuscriptModal" data-title="{{ $shopManuscript->title }}" data-description="{{ $shopManuscript->description }}" data-max-words="{{ $shopManuscript->max_words }}" data-price="{{ $shopManuscript->price }}" data-split-payment-price="{{ $shopManuscript->split_payment_price }}" data-fiken_product="{{ $shopManuscript->fiken_product }}" data-action="{{ route('admin.shop-manuscript.update', $shopManuscript->id) }}"><i class="fa fa-pencil"></i></button>
										<button type="button" class="btn btn-danger btn-xs deleteShopManuscriptBtn" data-toggle="modal" data-target="#deleteShopManuscriptModal" data-title="{{ $shopManuscript->title }}" data-action="{{ route('admin.shop-manuscript.destroy', $shopManuscript->id) }}"><i class="fa fa-trash"></i></button>
									</td>
								</tr>
						      	@endforeach
						    </tbody>
						</table>
					</div>
					<div class="text-right margin-top">
						{{$shopManuscripts->render()}}
					</div>
				</div>
			</div>
			@else
	  		<div class="panel panel-default" style="border-top: 0">
		  		<div class="panel-body">
					<div class="table-users table-responsive">
						<table class="table no-margin-bottom">
							<thead>
						    	<tr>
							        <th>Manuscript</th>
							        <th>Learner</th>
							        <th>Date Sold</th>
							        <th>Status</th>
							        <th>Assigned admin</th>
							        <th></th>
						      	</tr>
						    </thead>

						    <tbody>
						    	@foreach($shopManuscripts as $shopManuscript)
						    	<tr>
									<td>
										@if($shopManuscript->is_active)
										<a href="{{ route('shop_manuscript_taken', ['id' => $shopManuscript->user->id, 'shop_manuscript_taken_id' => $shopManuscript->id]) }}">{{$shopManuscript->shop_manuscript->title}}</a>
										@else
										{{$shopManuscript->shop_manuscript->title}}
										@endif
									</td>
									<td><a href="{{ route('admin.learner.show', $shopManuscript->user->id) }}">{{ $shopManuscript->user->full_name }}</a></td>
									<td>{{ $shopManuscript->created_at }}</td>
									<td>
										@if( $shopManuscript->status == 'Finished' )
										<span class="label label-success">Finished</span>
										@elseif( $shopManuscript->status == 'Started' )
										<span class="label label-primary">Started</span>
										@elseif( $shopManuscript->status == 'Not started' )
										<span class="label label-warning">Not started</span>
										@endif
									</td>
									<td>
										@if( $shopManuscript->admin )
										{{ $shopManuscript->admin->full_name }}
										@else
										<em>Not set</em>
										@endif
									</td>
									<td class="text-right">
										@if(!$shopManuscript->is_active)
							        	<form method="POST" action="{{ route('activate_shop_manuscript_taken') }}" class="inline-block">
											{{ csrf_field() }}
											<input type="hidden" name="shop_manuscript_id" value="{{ $shopManuscript->id }}">
											<button class="btn btn-warning btn-xs" type="submit"><i class="fa fa-check"></i></button>
										</form>
										@endif
							        	<form method="POST" action="{{ route('delete_shop_manuscript_taken') }}" class="inline-block">
											{{ csrf_field() }}
											<input type="hidden" name="shop_manuscript_id" value="{{ $shopManuscript->id }}">
											<button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash"></i></button>
										</form>
									</td>
								</tr>
						      	@endforeach
						    </tbody>
						</table>
					</div>
					<div class="text-right margin-top">
						{!! $shopManuscripts->appends(Request::except('page'))->render() !!}
					</div>
				</div>
			</div>
			@endif
	  	</div>
	</div>




	<div id="addShopManuscriptModal" class="modal fade" role="dialog">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Add shop manuscript</h4>
	      </div>
	      <div class="modal-body">
	      	<form method="POST" action="{{ route('admin.shop-manuscript.store') }}">
	      		{{ csrf_field() }}
	      		<div class="form-group">
	      			<label>Title</label>
	      			<input type="text" class="form-control" name="title" required>
	      		</div>
	      		<div class="form-group">
	      			<label>Description</label>
	      			<textarea class="form-control" name="description" required></textarea>
	      		</div>
	      		<div class="form-group">
	      			<label>Max Words</label>
	      			<input type="number" class="form-control" name="max_words" required>
	      		</div>
	      		<div class="form-group">
	      			<label>Price</label>
	      			<input type="number" step="0.01" class="form-control" name="price" required>
	      		</div>
	      		<div class="form-group">
	      			<label>Split Payment Price</label>
	      			<input type="number" step="0.01" class="form-control" name="split_payment_price" required>
	      		</div>
	      		<div class="form-group">
	      			<label>Fiken Product ID</label>
	      			<input type="text" class="form-control" name="fiken_product" value="" required>
	      		</div>
	      		<button type="submit" class="btn btn-primary pull-right">Add</button>
	      		<div class="clearfix"></div>
	      	</form>
	      </div>
	    </div>

	  </div>
	</div>


	<div id="editShopManuscriptModal" class="modal fade" role="dialog">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Edit shop manuscript</h4>
	      </div>
	      <div class="modal-body">
	      	<form method="POST" action="">
	      		{{ csrf_field() }}
	      		{{ method_field('PUT') }}
	      		<div class="form-group">
	      			<label>Title</label>
	      			<input type="text" class="form-control" name="title" value="" required>
	      		</div>
	      		<div class="form-group">
	      			<label>Description</label>
	      			<textarea class="form-control" name="description" required></textarea>
	      		</div>
	      		<div class="form-group">
	      			<label>Max Words</label>
	      			<input type="number" class="form-control" name="max_words" value="" required>
	      		</div>
	      		<div class="form-group">
	      			<label>Price</label>
	      			<input type="number" step="0.01" class="form-control" name="price" value="" required>
	      		</div>
	      		<div class="form-group">
	      			<label>Split Payment Price</label>
	      			<input type="number" step="0.01" class="form-control" name="split_payment_price" value="" required>
	      		</div>
	      		<div class="form-group">
	      			<label>Fiken Product ID</label>
	      			<input type="text" class="form-control" name="fiken_product" value="" required>
	      		</div>
	      		<button type="submit" class="btn btn-primary pull-right">Update</button>
	      		<div class="clearfix"></div>
	      	</form>
	      </div>
	    </div>

	  </div>
	</div>


	<div id="deleteShopManuscriptModal" class="modal fade" role="dialog">
	  <div class="modal-dialog modal-sm">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Delete <em></em></h4>
	      </div>
	      <div class="modal-body">
	      	<form method="POST" action="">
	      		{{ csrf_field() }}
	      		{{ method_field('DELETE') }}
		      	Are you sure to delete this shop manuscript?
		      	<div class="text-right margin-top">
	      			<button class="btn btn-danger" type="submit">Delete</button>
	      		</div>
	      	</form>
	      </div>
	    </div>
	  </div>
	</div>
</div>
@stop

@section('scripts')
<script>
	$('.editShopManuscriptBtn').click(function(){
		var form = $('#editShopManuscriptModal');
		var action = $(this).data('action');
		var title = $(this).data('title');
		var description = $(this).data('description');
		var max_words = $(this).data('max-words');
		var price = $(this).data('price');
		var split_payment_price = $(this).data('split-payment-price');
		var fiken_product = $(this).data('fiken_product');

		form.find('form').attr('action', action);
		form.find('input[name=title]').val(title);
		form.find('textarea[name=description]').val(description);
		form.find('input[name=max_words]').val(max_words);
		form.find('input[name=price]').val(price);
		form.find('input[name=split_payment_price]').val(split_payment_price);
		form.find('input[name=fiken_product]').val(fiken_product);
	});	


	$('.deleteShopManuscriptBtn').click(function(){
		var form = $('#deleteShopManuscriptModal');
		var action = $(this).data('action');
		var title = $(this).data('title');

		form.find('form').attr('action', action);
		form.find('.modal-title em').text(title);
	});
</script>
@stop