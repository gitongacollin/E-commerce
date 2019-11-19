@extends('layouts.frontLayout.front_design')
@section('content')
<section id="cart_items" style="margin-top:20px;">
	<div class="container">
		<div class="breadcrumbs">
			<ol class="breadcrumb">
			  <li><a href="#">Home</a></li>
			  <li class="active">Order Review</li>
			</ol>
		</div>
			<div class="row">
				<div class="col-sm-4 col-sm-offset-1">
					<div class="login-form">
						<h2>Bill Address</h2>
							<div class="form-group">
								{{ $userDetails->name }}
							</div>
							<div class="form-group">
							 {{ $userDetails->address }}
							</div>
							<!-- <div class="form-group">	
								<input name="billing_county" id="billing_county" @if(!empty($userDetails->County)) value="{{ $userDetails->County }}" @endif type="text" placeholder="Billing County" class="form-control" />
							</div> -->
							<div class="form-group">
								{{ $userDetails->region }}
							</div>
							<div class="form-group">
									{{ $userDetails->county }}
							</div>
							<div class="form-group">
								{{ $userDetails->phone }}
							</div>
					</div>
				</div>
				<div class="col-sm-1">
					<h2></h2>
				</div>
				<div class="col-sm-4">
					<div class="signup-form">
						<h2>Shippping Address</h2>
							<div class="form-group">
								{{ $shippingDetails->name }}
							</div>
							<div class="form-group">
								{{ $shippingDetails->address }}
							</div>
							<div class="form-group">
								{{ $shippingDetails->region }}
							</div>
							<div class="form-group">
									{{ $shippingDetails->county }}
							</div>
							<div class="form-group">
								{{ $shippingDetails->phone }}
							</div>
					</div>
				</div>
			</div>
			<div class="review-payment">
				<h2>Review & Payment</h2>
			</div>

			<div class="table-responsive cart_info">

				<table class="table table-condensed">
					<thead>
						<tr class="cart_menu">
							<td class="image">Item</td>
							<td class="description"></td>
							<td class="price">Price</td>
							<td class="quantity">Quantity</td>
							<td class="total">Total</td>
							<td></td>
						</tr>
					</thead>
					<tbody>

						<?php $total_amount =0; ?>

						@foreach($userCart as $cart)
						<tr>
							<td class="cart_product">
								<a href=""><img style="width: 100px;" src="{{ asset('images/backend_images/products/small/'.$cart->image) }}" alt=""></a>
							</td>
							<td class="cart_description">
								<h4><a href="">{{ $cart->product_name }}</a></h4>
								<p>Product Code: {{ $cart->product_code }}</p>
							</td>
							<td class="cart_price">
								<p>KSH {{ $cart->price }}</p>
							</td>
							<td class="cart_quantity">
								<div class="cart_quantity_button">
									{{ $cart->quantity }}
								</div>
							</td>
							<td class="cart_total">
								<p class="cart_total_price">KSH {{ $cart->price*$cart->quantity }}</p>
							</td>
						</tr>
						<?php $total_amount = $total_amount + ($cart->price*$cart->quantity); ?>
						@endforeach
						<tr>
							<td colspan="4">&nbsp;</td>
							<td colspan="2">
								<table class="table table-condensed total-result">
									<tr>
										<td>Cart Sub Total</td>
										<td>KSH {{ $total_amount }}</td>
									</tr>
									<tr class="shipping-cost">
										<td>Shipping cost (+)</td>
										<td>KSH 0 </td>	
									</tr>
									<tr class="discount-cost">
										<td>Discount Amount (-)</td>
										<td>
											@if(!empty(Session::get('couponAmount')))
												KSH <?php echo Session::get('couponAmount'); ?>
											@else
												KSH 0
											@endif
										</td>	
									</tr>
									<tr>
										<td>Grand Total</td>
										<?php $grand_total = $total_amount - Session::get('couponAmount'); ?>
										<td><span>KSH <?php echo $grand_total; ?></span></td>
										
									</tr>
								</table>
							</td>
						</tr>


					</tbody>
				</table>
			</div>

			<form name="paymentForm" id="paymentForm" action="{{ url('/place-order') }}" method="post">
				@csrf
				<input type="hidden" name="grand_total" value="{{ $grand_total }}">
				<div class="payment-options">
					<span>
						<label><strong>Select Payment Method:</strong></label>
					</span>
					<span>
						<label><input type="radio" name="payment_method" id="COD" value="Cash on Delivery"> <strong>Cash on Delivery</strong></label>
					</span>
					<span>
						<label><input type="radio" name="payment_method" id="Paypal" value="Paypal"> <strong>Paypal</strong></label>
					</span>
					<span style="float:right;">
						<button type="submit" class="btn btn-default" onclick="return selectPaymentMethod();">Place Order</button>
					</span>
				</div>
			</form>
	</div>

</section>

@endsection