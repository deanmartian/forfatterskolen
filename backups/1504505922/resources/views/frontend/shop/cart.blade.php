@extends('frontend.layout')

@section('title')
<title>Cart &rsaquo; Forfatterskolen</title>
@stop

@section('content')
</div>

<div class="container">
	<div class="row">
		<ol class="breadcrumb">
		  <li><a href="{{route('front.home')}}">Home</a></li>
		  <li class="active">Shopping Cart</li>
		</ol>
	</div>

	<div class="row">
		<div class="col-sm-12"><h2 class="cart-title">Shopping Cart</h2></div>
	</div>

	<div class="row cart">
		<div class="col-sm-12 col-md-8">
			<div class="panel panel-default">
				<div class="table-responsive">
					<table class="table">
						<tbody>
							<?php $total = 0; ?>
							@foreach($items as $item)
							<tr>
								<td class="item-thumb"><div></div></td>
								<td>
									<strong><a href="{{$item->course->url}}" class="cart_item_title">{{$item->course['title']}}</a></strong><br />
									<strong>Package:</strong> {{$item->variation}} <br />
									<form method="post" action="{{route('front.shop.remove_from_cart')}}">
										{{csrf_field()}}
										<input type="hidden" name="package_id" value="{{$item->id}}">
										<button type="submit" class="item-remove">Remove</button>
									</form>
								</td>
								<td class="item-price">
									<strong>{{FrontendHelpers::currencyFormat($item->price)}}</strong>
								</td>
							</tr>
							<?php $total += $item->price; ?>
							@endforeach
						</tbody>
					</table>
				</div>
				<div class="panel-footer">
					<a href="{{route('front.course.index')}}" class="btn btn-theme"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add More Courses</a>
				</div>
			</div>
		</div>

		<div class="col-sm-12 col-md-4">
			<div class="panel panel-default">
			  <div class="panel-body cart-summary">
			  	<h4>Order Summary</h4>
			  	<span class="pull-right">{{FrontendHelpers::currencyFormat($total)}}</span>
			  	Items ({{count($items)}}) 
			  	<hr />
			  	<div class="cart-total">
			  		<strong>
			  			<span class="pull-right">{{FrontendHelpers::currencyFormat($total)}}</span>
			  			Total
			  			<div class="clearfix"></div>
			  		</strong>
			  		<small>Price are inclusive of all taxes</small>
			  	</div>
			  	<a class="btn btn-lg btn-theme btn-block btn-checkout" href="{{route('front.shop.checkout')}}">Checkout</a>
			  </div>
			</div>
		</div>
	</div>

</div>

@stop