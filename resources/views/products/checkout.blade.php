@extends('layouts.frontLayout.front_design')
@section('content')

<section id="form" style="margin-top:20px;"><!--form-->
	<div class="container">
		<div class="breadcrumbs">
			<ol class="breadcrumb">
			  <li><a href="#">Home</a></li>
			  <li class="active">CheckOut</li>
			</ol>
		</div>
		<form action="{{ url('/checkout') }}" method="post">
			@csrf
			<div class="row">
				<div class="col-sm-4 col-sm-offset-1">
					<div class="login-form"><!--login form-->
						<h2>Bill To</h2>
							<div class="form-group">
								<input name="billing_name" id="billing_name" @if(!empty($userDetails->name)) value="{{ $userDetails->name }}" @endif type="text" placeholder="Billing Name" class="form-control" />
							</div>
							<div class="form-group">
								<input name="billing_address" id="billing_address" @if(!empty($userDetails->address)) value="{{ $userDetails->address }}" @endif type="text" placeholder="Billing Address" class="form-control" />
							</div>
							<!-- <div class="form-group">	
								<input name="billing_county" id="billing_county" @if(!empty($userDetails->County)) value="{{ $userDetails->County }}" @endif type="text" placeholder="Billing County" class="form-control" />
							</div> -->
							<div class="form-group">
								<input name="billing_region" id="billing_region" @if(!empty($userDetails->region)) value="{{ $userDetails->region }}" @endif type="text" placeholder="Billing Region" class="form-control" />
							</div>
							<div class="form-group">
								<select id="billing_county" name="billing_county" class="form-control">
									<option value="">Select County</option>
									@foreach($counties as $county)
										<option value="{{ $county->county_name }}" @if(!empty($userDetails->county) && $county->county_name == $userDetails->county) selected @endif>{{ $county->county_name }}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group">
								<input name="billing_phone" id="billing_phone" @if(!empty($userDetails->phone)) value="{{ $userDetails->phone }}" @endif type="text" placeholder="Billing Phone" class="form-control" />
							</div>
							<div class="form-check">
							    <input type="checkbox" class="form-check-input" id="copyAddress">
							    <label class="form-check-label" for="copyAddress">Shipping Address same as Billing Address</label>
							</div>
					</div><!--/login form-->
				</div>
				<div class="col-sm-1">
					<h2></h2>
				</div>
				<div class="col-sm-4">
					<div class="signup-form"><!--sign up form-->
						<h2>Ship To</h2>
							<div class="form-group">
								<input name="shipping_name" id="shipping_name" @if(!empty($shippingDetails->name)) value="{{ $shippingDetails->name }}" @endif type="text" placeholder="Shipping Name" class="form-control" />
							</div>
							<div class="form-group">
								<input name="shipping_address" id="shipping_address" @if(!empty($shippingDetails->address)) value="{{ $shippingDetails->address }}" @endif type="text" placeholder="Shipping Address" class="form-control" />
							</div>
							<div class="form-group">
								<input name="shipping_region" id="shipping_region" @if(!empty($shippingDetails->region)) value="{{ $shippingDetails->region }}" @endif type="text" placeholder="Shipping Region" class="form-control" />
							</div>
							<div class="form-group">
								<select id="shipping_county" name="shipping_county" class="form-control">
									<option value="">Select County</option>
										@foreach($counties as $county)
											<option value="{{ $county->county_name }}" @if(!empty($shippingDetails->county) && $county->county_name == $shippingDetails->county) selected @endif>{{ $county->county_name }}</option>
										@endforeach
								</select>
							</div>
							<div class="form-group">
								<input name="shipping_phone" id="shipping_phone" @if(!empty($shippingDetails->phone)) value="{{ $shippingDetails->phone }}" @endif type="text" placeholder="Shipping Phone Number" class="form-control" />
							</div>
							<button type="submit" class="btn btn-default check Out">Checkout</button>
					</div><!--/sign up form-->
				</div>
			</div>
		</form>
	</div>
</section><!--/form-->

@endsection