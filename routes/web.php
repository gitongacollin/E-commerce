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

Route::group(['middleware' => ['auth']],function(){
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

	 //Products attributes Routes
	 Route::match(['get','post'],'/admin/add-attribute/{id}','ProductsController@addAttribute');
	 Route::match(['get','post'],'/admin/add-images/{id}','ProductsController@addImages');
	 Route::get('/admin/delete-attribute/{id}','ProductsController@deleteAttribute');


});



Auth::routes();

Route::get('/','IndexController@index');

Route::get('/home', 'HomeController@index')->name('home');
