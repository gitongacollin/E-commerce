<?php

namespace App;
use Auth;
use Session;
use DB;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function attributes(){
    	return $this->hasMany('App\ProductsAttribute','product_id');
    }

    public static function cartCount(){
    	if(Auth::check()){
    		// User is logged in; We will use Auth
    		$user_email = Auth::user()->email;
    		$cartCount = DB::table('cart')->where('user_email',$user_email)->sum('quantity');
    	}else{
    		// User is not logged in. We will use Session
    		$session_id = Session::get('session_id');
    		$cartCount = DB::table('cart')->where('session_id',$session_id)->sum('quantity');
    	}
    	return $cartCount;
    }

    public static function productCount($cat_id){
    	$catCount = Product::where(['category_id'=>$cat_id,'status'=>1])->count();
    	return $catCount;
    }

    public static function getProductStock($product_id,$product_size){
        $getProductStock = ProductsAttribute::select('stock')->where(['product_id'=>$product_id,'size'=>$product_size])->first();
        return $getProductStock->stock;
    }

    public static function deleteCartProduct($product_id,$user_email){
        DB::table('cart')->where(['product_id'=>$product_id,'user_email'=>$user_email])->delete();
    }

    public static function getProductStatus($product_id){
        $getProductStatus = Product::select('status')->where('id',$product_id)->first();
        return $getProductStatus->status;
    }

    public static function getCategoryStatus($category_id){
        $getCategoryStatus = Category::select('status')->where('id',$category_id)->first();
        return $getCategoryStatus->status;
    }

    public static function getAttributeCount($product_id,$product_size){
        $getAttributeCount = ProductsAttribute::where(['product_id'=>$product_id,'size'=>$product_size])->count();
        return $getAttributeCount;   
    }
}
