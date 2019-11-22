@extends('layouts.frontLayout.front_design')
@section('content')	
	
<section>
	<div class="container">
	<div class="row">
		<div class="col-sm-3">
			@include('layouts.frontLayout.front_sidebar')
		</div>
			
			<div class="col-sm-9 padding-right">
				<div class="features_items"><!--features_items-->
					<h2 class="title text-center">
						@if(!empty($search_product))
							{{ $search_product }} Item
						@else
							{{ $categoriesDetails->name }} Items
						@endif
						    ({{ count($productsAll) }})
					</h2>
					<div align="left"><?php echo $breadcrumb; ?></div>
					<div>&nbsp;</div>
					@foreach($productsAll as $pro)
					<div class="col-sm-4">
						<div class="product-image-wrapper">
							<div class="single-products">
									<div class="productinfo text-center">
										<img src="{{ asset('images/backend_images/products/small/'.$pro->image) }}" alt="" />
										<h2>Ksh {{ $pro->price }}</h2>
										<p>{{ $pro->product_name }}</p> 
										<a href="{{ url('/products/view/'.$pro->id) }}"  class="btn btn-default add-to-cart"><i class="fa fa-shopping-cart"></i>Add to cart</a>
									</div>
									<div class="product-overlay">
										<div class="overlay-content">
											<h2>Ksh {{ $pro->price }}</h2>
											<p>{{ $pro->product_name }}</p>
											<a href="{{ url('/products/view/'.$pro->id) }}"class="btn btn-default add-to-cart"><i class="fa fa-shopping-cart"></i>Add to cart</a>
										</div>
									</div>
							</div>
							<div class="choose">
								<ul class="nav nav-pills nav-justified">
									<li><a href="#"><i class="fa fa-plus-square"></i>Add to wishlist</a></li>
									<li><a href="#"><i class="fa fa-plus-square"></i>Add to compare</a></li>
								</ul>
							</div>
						</div>
					</div>
					@endforeach
					@if(empty($search_product))
					
					@endif
					
				</div><!--features_items-->
				
				
				
			</div>
		</div>
	</div>
</section>

@endsection