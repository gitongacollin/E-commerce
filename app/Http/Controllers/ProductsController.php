<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;
use App\Category;
use App\Product;
use Illuminate\Support\Facades\Input;
use Image;

class ProductsController extends Controller
{
    public function addProduct(Request $request){

        if($request->isMethod('post')){
            $data= $request->all();
            //echo "<pre>"; print_r($data); die;
            if (empty($data['category_id'])) {
                return redirect()->back()->with('flash_message_error','Under category is missing');
            }
            $product = new Product;
            $product->category_id = $data['category_id'];
            $product->product_name = $data['product_name'];
            $product->product_color = $data['product_color'];
            $product->product_code = $data['product_code'];
            if(!empty($data['description'])){
                $product->description = $data['description'];
            }else{
                $product->description = '';
            }
            
            $product->price = $data['price'];
            // Upload Image
            if($request->hasFile('image')){
                $image_tmp = Input::file('image');
                if($image_tmp->isValid()){
                    $extension = $image_tmp->getClientOriginalExtension();
                    $filename = rand(111,99999).'.'.$extension;
                    $large_image_path = 'images/backend_images/products/large/'.$filename;
                    $medium_image_path = 'images/backend_images/products/medium/'.$filename;
                    $small_image_path = 'images/backend_images/products/small/'.$filename;
                    // Resize Images
                    Image::make($image_tmp)->save($large_image_path);
                    Image::make($image_tmp)->resize(600,600)->save($medium_image_path);
                    Image::make($image_tmp)->resize(300,300)->save($small_image_path);
                    // Store image name in products table
                    $product->image = $filename;
                }
            }
            $product->save();
            //return redirect()->back()->with('flash_message_success','Product has been added Successfully!');
            return redirect('/admin/view-product')->with('flash_message_success','Product has been added Successfully!');
        }

    	$categories = Category::where(['parent_id'=>0])->get();
        $categories_dropdown = "<option value='' selected disabled>Select</option>";
    	foreach($categories as $cat){
            $categories_dropdown .= "<option value='".$cat->id."'>".$cat->name."</option>";
            $sub_categories = Category::where(['parent_id'=>$cat->id])->get();
            foreach ($sub_categories as $sub_cat) {
                $categories_dropdown .= "<option value = '".$sub_cat->id."'>&nbsp;--&nbsp;".$sub_cat->name."</option>";
            }
        }

    	return view('admin.products.add_product')->with(compact('categories_dropdown'));
    }

    public function viewProduct()
    {
        $products = Product::get();
        $products = json_decode(json_encode($products));
        foreach ($products as $key => $val) {
            $category_name = Category::where(['id'=>$val->category_id])->first();
            $products[$key]->category_name = $category_name->name;
        }
        return view('admin.products.view_product')->with(compact('products'));
    }
}
