<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/


Route::match(['get', 'post'],'/admin','AdminController@login');



Route::get('/logout', 'AdminController@logout');

//Users Register/Login page
Route::get('/login-register','UsersController@loginRegister');

//users login submit form
Route::post('/user-login','UsersController@userLogin');

//users Register form submit
Route::post('/user-register','UsersController@register');

// Confirm Account
Route::get('confirm/{code}','UsersController@confirmAccount');


//users logout
Route::get('/userLogout', 'UsersController@userLogout');


//check if user already exists
Route::match(['GET','POST'],'/check-email','UsersController@checkEmail');

//category/Listing Page
Route::get('/products/{url}', 'ProductsController@products');
// Product Detail Page
Route::get('/products/view/{id}','ProductsController@product');

//Get product Attribute price
Route::get('/get-product-price','ProductsController@getProductPrice');

//Add to cart Route
Route::match(['get','post'],'/add-to-cart', 'ProductsController@addtocart');

//Cart page route
Route::match(['get', 'post'], '/cart', 'ProductsController@cart');

//Delete Product from cart page
Route::get('/cart/delete-product/{id}','ProductsController@deleteCartProduct');

//Update quantity on cart page
Route::get('/cart/update-quantity/{id}/{quantity}','ProductsController@updateCartQuantity');

//Coupon code on cart page
Route::post('/cart/apply-coupon','ProductsController@applyCoupon');

//Prevent all routes after login
Route::group(['middleware'=>['frontLogin']],function(){
//Users Account Page
Route::match(['get','post'],'/account','UsersController@account');
Route::post('/check-user-pass','UsersController@chkUserPassword');
Route::post('/update-user-pass','UsersController@updatePassword');

});

Route::group(['middleware' => ['adminlogin']],function(){
	//ACL ROUTE MODULES
	Route::resource('roles','RoleController');
    Route::resource('users','UserController');

	 Route::get('/admin/dashboard','AdminController@dashboard');
	 Route::get('/admin/settings','AdminController@settings');
	 Route::get('/admin/check-pwd', 'AdminController@chkPassword');
	 Route::match(['get', 'post'],'/admin/update-pwd', 'AdminController@updatePassword');

	 //Categories routes (Admin)
	 Route::match(['get','post'],'/admin/add-category','CategoryController@addCategory');
	 Route::match(['get','post'],'/admin/edit-category/{id}','CategoryController@editCategory');
	 Route::match(['get','post'],'/admin/delete-category/{id}','CategoryController@deleteCategory');
	 Route::get('/admin/view-category','CategoryController@viewCategory');


	 //Products routes(Admin)
	 Route::match(['get','post'],'/admin/add-product','ProductsController@addProduct');
	 Route::match(['get','post'],'/admin/edit-product/{id}','ProductsController@editProduct');
	 Route::get('/admin/view-product','ProductsController@viewProduct');
	 Route::get('/admin/delete-product/{id}','ProductsController@deleteProduct');
	 Route::get('/admin/delete-product-image/{id}','ProductsController@deleteProductImage');
	 Route::get('/admin/delete-alt-image/{id}','ProductsController@deleteAltImage');


	 //Products attributes Routes
	 Route::match(['get','post'],'/admin/add-attribute/{id}','ProductsController@addAttribute');
	 Route::match(['get','post'],'/admin/edit-attribute/{id}','ProductsController@editAttribute');
	 Route::match(['get','post'],'/admin/add-images/{id}','ProductsController@addImages');
	 Route::get('/admin/delete-attribute/{id}','ProductsController@deleteAttribute');

	 //Coupons Routes
	 Route::match(['get','post'], '/admin/add-coupon', 'CouponsController@addCoupon');
	 Route::get('/admin/view-coupon','CouponsController@viewCoupon');
	 Route::match(['get','post'],'/admin/edit-coupon/{id}', 'CouponsController@editCoupon');
	 Route::get('/admin/delete-coupon/{id}','CouponsController@deleteCoupon');

	 //Banners Routes
	 Route::match(['get','post'],'/admin/add-banner','BannersController@addBanner');
	 Route::get('/admin/view-banners','BannersController@viewBanner');
	 Route::match(['get','post'],'/admin/edit-banner/{id}','BannersController@editBanner');
	 Route::get('/admin/delete-banner/{id}','BannersController@deleteBanner');


});



Auth::routes();

Route::get('/','IndexController@index');

Route::get('/kwe', 'HomeController@index')->name('home');
