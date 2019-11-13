@extends('layouts.frontLayout.front_design')
@section('content')
	
	<section id="form" style="margin-top: 0px;"><!--form-->
		<div class="container">
			<div class="row">
				<div class="col-sm-4 col-sm-offset-1">
					<div class="login-form"><!--login form-->
						<h2>Update account</h2>
						<form id="accountForm" name="accountForm" action="{{ url ('/account') }}" method="POST">
							@csrf
							<input value="{{ $userDetails->name }}" id="name" name="name" type="text" placeholder="Name" />
							<input value="{{ $userDetails->email }}" id="email" name="email" type="text" placeholder="Email" />
							<input value="{{ $userDetails->address }}" id="address" name="address" type="text" placeholder="Address" />
							<select id ="county" name="county">
								@foreach($counties as $county)
								<option value="{{ $county->county_name }}" @if($county->county_name == $userDetails->county) selected @endif>{{ $county->county_name }}</option>
								@endforeach
							</select>
							<select style="margin-top: 10px;" id="region" name="region">
								@foreach($sub_counties as $sub_count)
								<option value="{{ $sub_count->sub_county }}" @if($sub_count->sub_county == $userDetails->region) selected @endif>{{ $sub_count->sub_county }}</option>
								@endforeach
							</select>
							<!--<input style="margin-top: 10px;" id="region" name="region" type="text" placeholder="Region" />-->
							<input style="margin-top: 10px;" value="{{ $userDetails->phone }}" id="phone" name="phone" type="text" placeholder="Phone Number" />
							<button type="submit" class="btn btn-default">update</button>
						</form>
						
					</div><!--/login form-->
				</div>
				<div class="col-sm-1">
					<h2 class="or">OR</h2>
				</div>
				<div class="col-sm-4">
					<div class="signup-form">
						<h2>Update Password</h2>
						<form id="passwordForm" name="passwordForm" action="{{ url ('/update-user-pass') }}" method="POST">
							@csrf
							<input id="current_pass" name="current_pass" type="password" placeholder="Current Password" />
							<span id="chkPwd"></span>
							<input id="new_pass" name="new_pass" type="password" placeholder="New Password" />
							<input id="confirm_pass" name="confirm_pass" type="password" placeholder="Confirm Password" />
							<button type="submit" class="btn btn-default">Update</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section><!--/form-->

@endsection