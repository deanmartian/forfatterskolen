@extends('backend.layout')

@section('title')
<title>Shop Manuscripts &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3><i class="fa fa-file-text-o"></i> {{ trans('site.all-shop-manuscripts') }}</h3>
	<a href="javascript:void(0)" data-toggle="modal" data-target="#manuscriptEmailTemplate"> {{ trans('site.email-template') }}</a>
</div>

<div class="col-md-12">
 	<ul class="nav nav-tabs margin-top">
	    <li @if( Request::input('tab') != 'sold' && Request::input('tab') != 'manuscripts') class="active" @endif><a href="?tab=all">{{ trans_choice('site.shop-manuscripts', 2) }}</a></li>
	    <li @if( Request::input('tab') == 'sold' ) class="active" @endif><a href="?tab=sold">{{ trans('site.sold-shop-manuscripts') }}</a></li>
		{{--<li @if( Request::input('tab') == 'manuscripts' ) class="active" @endif><a href="?tab=manuscripts">{{ trans_choice('site.manuscripts', 2) }}</a></li>--}}
  	</ul>
	<div class="tab-content">
	  	<div class="tab-pane fade in active">
	  		@if( Request::input('tab') == 'manuscripts' )
				<div class="panel panel-default" style="border-top: 0">
					<div class="panel-body">
						<div class="table-users table-responsive">
							<table class="table no-margin-bottom">
								<thead>
								<tr>
									<th>{{ trans('site.id') }}</th>
									<th>{{ trans_choice('site.manuscripts', 1) }}</th>
									<th>{{ trans('site.words-count') }}</th>
									<th>{{ trans_choice('site.courses', 1) }}</th>
									<th>{{ trans('site.grade') }}</th>
									<th>{{ trans_choice('site.feedbacks', 2) }}</th>
									<th>{{ trans('site.uploaded-by') }}</th>
									<th>{{ trans('site.date-uploaded') }}</th>
									<th>{{ trans('site.status') }}</th>
									<th>{{ trans('site.assigned-admin') }}</th>
								</tr>
								</thead>
								<tbody>
								@foreach($shopManuscripts as $manuscript)
									<tr>
										<td>{{$manuscript->id}}</td>
										<td>
                                            <?php $extension = explode('.', basename($manuscript->filename)); ?>
											@if( end($extension) == 'pdf' )
												<i class="fa fa-file-pdf-o"></i>
											@elseif( end($extension) == 'docx' )
												<i class="fa fa-file-word-o"></i>
											@elseif( end($extension) == 'odt' )
												<i class="fa fa-file-text-o"></i>
											@endif
											<a href="{{ route('admin.manuscript.show', $manuscript->id) }}">{{ basename($manuscript->filename) }}</a>
										</td>
										<td>{{$manuscript->word_count}}</td>
										<td><a href="{{route('admin.course.show', $manuscript->courseTaken->package->course->id)}}">{{$manuscript->courseTaken->package->course->title}}</a></td>
										<td>
											@if($manuscript->grade)
												{{$manuscript->grade}}
											@else
												<em>Not set</em>
											@endif
										</td>
										<td>{{count($manuscript->feedbacks)}}</td>
										<td><a href="{{route('admin.learner.show', $manuscript->user->id)}}">{{$manuscript->user->fullname}}</a></td>
										<td>{{$manuscript->created_at}}</td>
										<td>
											@if( $manuscript->status == 'Finished' )
												<span class="label label-success">Finished</span>
											@elseif( $manuscript->status == 'Started' )
												<span class="label label-primary">Started</span>
											@elseif( $manuscript->status == 'Not started' )
												<span class="label label-warning">Not started</span>
											@endif
										</td>
										<td>
											@if( $manuscript->admin )
												{{ $manuscript->admin->full_name }}
											@else
												<em>Not set</em>
											@endif
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
			@elseif ( Request::input('tab') == 'sold' )
	  		<div class="panel panel-default" style="border-top: 0">
		  		<div class="panel-body">
					<div class="navbar-form navbar-right">
						<div class="form-group">
							<form role="search" method="get" action="">
								<input type="hidden" name="tab" value="sold">
								<div class="input-group">
									<input type="text" class="form-control" name="search" placeholder="{{ trans('site.search') }}.."
									value="{{ Request::get('search') }}">
									<span class="input-group-btn">
										<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
									</span>
								</div>
							</form>
						</div>
					</div>

					<div class="clearfix"></div>

					<div class="table-users table-responsive">
						<table class="table no-margin-bottom">
							<thead>
						    	<tr>
							        <th>{{ trans_choice('site.manuscripts', 1) }}</th>
							        <th>{{ trans_choice('site.learners', 1) }}</th>
							        <th>{{ trans('site.date-sold') }}</th>
							        <th>{{ trans('site.status') }}</th>
							        <th>{{ trans('site.assigned-admin') }}</th>
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
			@else
				<div class="panel panel-default no-padding-bottom" style="border-top: 0">
					<div class="panel-body">
						<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addShopManuscriptModal">{{ ucwords(trans('site.add-shop-manuscript')) }}</button>
						<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#excessAmountModal">
							Edit Excess Word Amount
						</button>
						<div class="table-users table-responsive">
							<table class="table no-margin-bottom">
								<thead>
								<tr>
									<th>{{ trans('site.title') }}</th>
									<th>{{ trans('site.description') }}</th>
									<th>{{ ucwords(trans('site.max-words')) }}</th>
									<th>{{ trans('site.full-payment-price') }}</th>
									<th>3 {{ trans('site.months-payment') }}</th>
									<th></th>
								</tr>
								</thead>

								<tbody>
								@foreach($shopManuscripts as $shopManuscript)
									<tr>
										<td>{{ $shopManuscript->title }}</td>
										<td>{{ $shopManuscript->description }}</td>
										<td>{{ $shopManuscript->max_words }}</td>
										<td>{{ FrontendHelpers::currencyFormat($shopManuscript->full_payment_price) }}</td>
										<td>{{ FrontendHelpers::currencyFormat($shopManuscript->months_3_price) }}</td>
										<td>
											<?php
											$upgradeManuscripts = \App\ShopManuscript::where('max_words','>', $shopManuscript->max_words)->get();
                                            $upgradeManuscriptsPrice = \App\ShopManuscriptUpgrade::where('shop_manuscript_id', $shopManuscript->id)->get();
											?>
											<button type="button" class="btn btn-info btn-xs editShopManuscriptBtn" data-toggle="modal" data-target="#editShopManuscriptModal" data-fields="{{ json_encode($shopManuscript) }}" data-manuscripts="{{ json_encode($upgradeManuscripts) }}" data-manuscripts-price="{{ json_encode($upgradeManuscriptsPrice) }}" data-action="{{ route('admin.shop-manuscript.update', $shopManuscript->id) }}"><i class="fa fa-pencil"></i></button>
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
			@endif
	  	</div>
	</div>




	<div id="addShopManuscriptModal" class="modal fade" role="dialog">
	  <div class="modal-dialog modal-lg">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">{{ trans('site.add-shop-manuscript') }}</h4>
	      </div>
	      <div class="modal-body">
	      	<form method="POST" action="{{ route('admin.shop-manuscript.store') }}" onsubmit="disableSubmit(this)">
	      		{{ csrf_field() }}
	      		<div class="row">
	      			<div class="col-sm-5">
			      		<div class="form-group">
			      			<label>{{ trans('site.title') }}</label>
			      			<input type="text" class="form-control" name="title" required>
			      		</div>
			      		<div class="form-group">
			      			<label>{{ trans('site.description') }}</label>
			      			<textarea class="form-control" name="description" rows="8" required></textarea>
			      		</div>
			      		<div class="form-group">
			      			<label>{{ ucwords(trans('site.max-words')) }}</label>
			      			<input type="number" class="form-control" name="max_words" required>
			      		</div>
			      	</div>
			      	<div class="col-sm-7">
		              <ul class="nav nav-tabs">
		                <li class="active"><a data-toggle="tab" href="#fullprice">{{ trans('site.full-payment') }}</a></li>
		                <li><a data-toggle="tab" href="#3months">3 {{ trans('site.months') }}</a></li>
						  <li><a data-toggle="tab" href="#upgradeprice">{{ trans('site.upgrade-price') }}</a></li>
		              </ul>
		              <div class="tab-content">
		                <div id="fullprice" class="tab-pane fade in active">
		                  <h4>{{ trans('site.full-payment-price') }}</h4>
		                  <div class="form-group">
		                    <label>{{ trans('site.price') }}</label>
		                    <input type="number" step="0.01" name="full_payment_price" placeholder="{{ trans('site.price') }}" min="0" required class="form-control">
		                  </div>
		                  <div class="form-group">
		                    <label>Fiken Product ID</label>
		                    <input type="text" name="full_price_product" placeholder="Fiken Product ID" required class="form-control">
		                  </div>
		                  <div class="form-group">
		                    <label>{{ trans('site.due-date-in-days') }}</label>
		                    <input type="number" name="full_price_due_date" placeholder="{{ trans('site.due-date') }}" min="0" required class="form-control">
		                  </div>
		                </div>
		                <div id="3months" class="tab-pane fade">
		                  <h4>{{ str_replace('_MONTH_NUMBER_',3,trans('site.months-payment-price')) }}</h4>
		                  <div class="form-group">
		                    <label>{{ trans('site.price') }}</label>
		                    <input type="number" step="0.01" name="months_3_price" placeholder="{{ trans('site.price') }}" min="0" required class="form-control">
		                  </div>
		                  <div class="form-group">
		                    <label>Fiken Product ID</label>
		                    <input type="text" name="months_3_product" placeholder="Fiken Product ID" required class="form-control">
		                  </div>
		                  <div class="form-group">
		                    <label>{{ trans('site.due-date-in-days') }}</label>
		                    <input type="number" name="months_3_due_date" placeholder="{{ trans('site.due-date') }}" min="0" required class="form-control">
		                  </div>
		                </div>
					  <div id="upgradeprice" class="tab-pane fade">
						  <h4>{{ trans('site.upgrade-price') }}</h4>
						  <div class="form-group">
							  <label>{{ trans('site.price') }}</label>
							  <input type="number" step="0.01" name="upgrade_price" placeholder="{{ trans('site.price') }}" min="0" required class="form-control">
						  </div>
					  </div>
		              </div>
		            </div>
	      		</div>

	      		<button type="submit" class="btn btn-primary pull-right">{{ trans('site.add') }}</button>
	      		<div class="clearfix"></div>
	      	</form>
	      </div>
	    </div>

	  </div>
	</div>

	<div id="excessAmountModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Excess amount modal</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.settings.update-record') }}" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<input type="hidden" name="setting_name" value="{{ $excessPerWordAmount->setting_name }}">

						<div class="form-group">
							<label>
								Amount per word
							</label>
							<input type="number" class="form-control" step="0.01" name="setting_value" 
							value="{{ $excessPerWordAmount->setting_value }}" required>
						</div>

					  <div class="text-right margin-top">
						<button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="manuscriptEmailTemplate" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.email-template') }}</h4>
			</div>
			<div class="modal-body">
                <?php

                if ($isUpdate) {
                    $route = route($emailTemplateRoute, ['id' => $emailTemplate->id]);
                } else {
                    $route = route($emailTemplateRoute);
                }

                ?>
				<form method="POST" action="<?php echo e($route); ?>" novalidate>
                    <?php echo e(csrf_field()); ?>


                    <?php if($isUpdate): ?>
						<?php echo e(method_field('PUT')); ?>
					<?php endif; ?>
					<div class="form-group">
						<label>{{ trans('site.body') }}</label>
						<textarea name="email_content" cols="30" rows="10" class="form-control tinymce" required
						><?php echo e($emailTemplate ? $emailTemplate->email_content : ''); ?></textarea>
					</div>

					<input type="hidden" name="page_name" value="Manuscript">

					<button type="submit" class="btn btn-primary pull-right">{{ trans('site.save') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="editShopManuscriptModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
	    <div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.edit-shop-manuscript') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.shop-manuscript.store') }}" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					{{ method_field('PUT') }}
					<div class="row">
						<div class="col-sm-5">
							<div class="form-group">
								<label>{{ trans('site.title') }}</label>
								<input type="text" class="form-control" name="title" required>
							</div>
							<div class="form-group">
								<label>{{ trans('site.description') }}</label>
								<textarea class="form-control" name="description" rows="8" required></textarea>
							</div>
							<div class="form-group">
								<label>{{ ucwords(trans('site.max-words')) }}</label>
								<input type="number" class="form-control" name="max_words" required>
							</div>
						</div>
						<div class="col-sm-7">
							<ul class="nav nav-tabs">
							<li class="active"><a data-toggle="tab" href="#fullpriceedit">{{ trans('site.full-payment') }}</a></li>
							<li><a data-toggle="tab" href="#3monthsedit">3 {{ trans('site.months') }}</a></li>
								<li><a data-toggle="tab" href="#upgradeedit">{{ trans('site.upgrade-price') }}</a></li>
							</ul>
							<div class="tab-content">
							<div id="fullpriceedit" class="tab-pane fade in active">
								<h4>{{ trans('site.full-payment-price') }}</h4>
								<div class="form-group">
								<label>{{ trans('site.price') }}</label>
								<input type="number" step="0.01" name="full_payment_price" placeholder="{{ trans('site.price') }}" min="0" required class="form-control">
								</div>
								<div class="form-group">
								<label>Fiken Product ID</label>
								<input type="text" name="full_price_product" placeholder="Fiken Product ID" required class="form-control">
								</div>
								<div class="form-group">
								<label>{{ trans('site.due-date-in-days') }}</label>
								<input type="number" name="full_price_due_date" placeholder="{{ trans('site.due-date') }}" min="0" required class="form-control">
								</div>
							</div>
							<div id="3monthsedit" class="tab-pane fade">
								<h4>{{ str_replace('_MONTH_NUMBER_', 3, trans('site.months-payment-price')) }}</h4>
								<div class="form-group">
								<label>{{ trans('site.price') }}</label>
								<input type="number" step="0.01" name="months_3_price" placeholder="{{ trans('site.price') }}" min="0" required class="form-control">
								</div>
								<div class="form-group">
								<label>Fiken Product ID</label>
								<input type="text" name="months_3_product" placeholder="Fiken Product ID" required class="form-control">
								</div>
								<div class="form-group">
								<label>{{ trans('site.due-date-in-days') }}</label>
								<input type="number" name="months_3_due_date" placeholder="{{ trans('site.due-date') }}" min="0" required class="form-control">
								</div>
							</div>
							<div id="upgradeedit" class="tab-pane fade">
								<h4>{{ trans('site.upgrade-price') }}</h4>
								<div class="form-group">
									<label>{{ trans('site.price') }}</label>
									<input type="number" step="0.01" name="upgrade_price" placeholder="{{ trans('site.price') }}" min="0" required class="form-control">
								</div>
								<div id="manuscript-list-container"></div>
							</div>
							</div>
						</div>
					</div>

					<button type="submit" class="btn btn-primary pull-right">{{ trans('site.save') }}</button>
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
				<h4 class="modal-title">{{ trans('site.delete') }} <em></em></h4>
			</div>
			<div class="modal-body">
			<form method="POST" action="" onsubmit="disableSubmit(this)">
				{{ csrf_field() }}
				{{ method_field('DELETE') }}
				{{ trans('site.delete-shop-manuscript-question') }}
				<div class="text-right margin-top">
					<button class="btn btn-danger" type="submit">{{ trans('site.delete') }}</button>
				</div>
			</form>
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
		var fields = $(this).data('fields');
		var manuscripts = $(this).data('manuscripts');
		var manuscripts_price = $(this).data('manuscripts-price');

		form.find('form').attr('action', action);
		form.find('input[name=title]').val(fields.title);
		form.find('textarea[name=description]').val(fields.description);
		form.find('input[name=max_words]').val(fields.max_words);
		form.find('input[name=full_payment_price]').val(fields.full_payment_price);
		form.find('input[name=full_price_product]').val(fields.full_price_product);
		form.find('input[name=full_price_due_date]').val(fields.full_price_due_date);

		form.find('input[name=months_3_price]').val(fields.months_3_price);
		form.find('input[name=months_3_product]').val(fields.months_3_product);
		form.find('input[name=months_3_due_date]').val(fields.months_3_due_date);

        form.find('input[name=upgrade_price]').val(fields.upgrade_price);

        var manuscriptList = '',
		manuscript_list_container = $("#manuscript-list-container");
        manuscript_list_container.empty();
        $.each(manuscripts,function(k,v){
           manuscriptList += '<div class="form-group">' +
			   '<label>Upgrade price for '+v.title+'</label>' +
			   '<input type="number" step="0.01" name="upgrade_price_'+v.id+'" placeholder="Price" min="0" required class="form-control">' +
			   '</div>';
		});

        manuscript_list_container.append(manuscriptList);

        $.each(manuscripts_price, function(k,v) {
           form.find('input[name=upgrade_price_'+v.upgrade_shop_manuscript_id+']').val(v.price);
		});
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