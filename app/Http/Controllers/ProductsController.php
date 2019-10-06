<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Auth;
use Session;
use App\Category;
use App\Product;
use Image;
use App\ProductsAttribute;

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
            if(!empty($data['product_color'])){
                $product->product_color = $data['product_color'];
            }else{
                $product->product_color = '';
            }
            $product->product_code = $data['product_code'];
            if(!empty($data['description'])){
                $product->description = $data['description'];
            }else{
                $product->description = '';
            }

            if(!empty($data['care'])){
                $product->care = $data['care'];
            }else{
                $product->care = '';
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

    public function editProduct(Request $request,$id=null){
        if($request->isMethod('post')){
            $data = $request->all();
            //echo "<pre>"; print_r($data); die;
            /*if(empty($data['status'])){
                $status='0';
            }else{
                $status='1';
            }*/
            // Upload Image
            if($request->hasFile('image')){
                $image_tmp = Input::file('image');
                if ($image_tmp->isValid()) {
                    // Upload Images after Resize
                    $extension = $image_tmp->getClientOriginalExtension();
                    $fileName = rand(111,99999).'.'.$extension;
                    $large_image_path = 'images/backend_images/products/large'.'/'.$fileName;
                    $medium_image_path = 'images/backend_images/products/medium'.'/'.$fileName;  
                    $small_image_path = 'images/backend_images/products/small'.'/'.$fileName;  
                    Image::make($image_tmp)->save($large_image_path);
                    Image::make($image_tmp)->resize(600, 600)->save($medium_image_path);
                    Image::make($image_tmp)->resize(300, 300)->save($small_image_path);
                }
            }else if(!empty($data['current_image'])){
                $fileName = $data['current_image'];
            }else{
                $fileName = 'No Image';
            }
            if(empty($data['description'])){
                $data['description'] = '';
            }
            if(empty($data['care'])){
                $data['care'] = '';
            }
            Product::where(['id'=>$id])->update(['category_id'=>$data['category_id'],'product_name'=>$data['product_name'],
                'product_code'=>$data['product_code'],'care'=>$data['care'],'description'=>$data['description'],'price'=>$data['price'],'image'=>$fileName]);
        
            return redirect('/admin/view-product')->with('flash_message_success', 'Product has been edited successfully');
        }
        // Get Product Details start //
        $productDetails = Product::where(['id'=>$id])->first();
        // Get Product Details End //
        // Categories drop down start //
        $categories = Category::where(['parent_id' => 0])->get();
        $categories_drop_down = "<option value='' disabled>Select</option>";
        foreach($categories as $cat){
            if($cat->id==$productDetails->category_id){
                $selected = "selected";
            }else{
                $selected = "";
            }
            $categories_drop_down .= "<option value='".$cat->id."' ".$selected.">".$cat->name."</option>";
            $sub_categories = Category::where(['parent_id' => $cat->id])->get();
            foreach($sub_categories as $sub_cat){
                if($sub_cat->id==$productDetails->category_id){
                    $selected = "selected";
                }else{
                    $selected = "";
                }
                $categories_drop_down .= "<option value='".$sub_cat->id."' ".$selected.">&nbsp;&nbsp;--&nbsp;".$sub_cat->name."</option>";  
            }   
        }
        // Categories drop down end //
        return view('admin.products.edit_product')->with(compact('productDetails','categories_drop_down'));
    }


    public function deleteProductImage($id=null){
        // Get Product Image
        $productImage = Product::where('id',$id)->first(); 
        // Get Product Image Paths
        $large_image_path = 'images/backend_images/products/large/';
        $medium_image_path = 'images/backend_images/products/medium/';
        $small_image_path = 'images/backend_images/products/small/';
        // Delete Large Image if not exists in Folder
        if(file_exists($large_image_path.$productImage->image)){
            unlink($large_image_path.$productImage->image);
        }
        // Delete Medium Image if not exists in Folder
        if(file_exists($medium_image_path.$productImage->image)){
            unlink($medium_image_path.$productImage->image);
        }
        // Delete Small Image if not exists in Folder
        if(file_exists($small_image_path.$productImage->image)){
            unlink($small_image_path.$productImage->image);
        }
        // Delete Image from Products table
        Product::where(['id'=>$id])->update(['image'=>'']);
        return redirect()->back()->with('flash_message_success', 'Product image has been deleted successfully');
    }

    public function deleteProduct($id = null){
        if(!empty($id)){
            Product::where(['id' =>$id])->delete();
            return redirect()->back()->with('flash_message_success','Product Successfully deleted!'); 
        }
    }

    public function viewProduct(){
        $products = Product::orderBy('id','DESC')->get();
        $products = json_decode(json_encode($products));
        foreach ($products as $key => $val) {
            $category_name = Category::where(['id'=>$val->category_id])->first();
            $products[$key]->category_name = $category_name->name;
        }
        return view('admin.products.view_product')->with(compact('products'));
    }

    public function addAttribute(Request $request, $id=null){
        $productDetails = Product::with('attributes')->where(['id' => $id])->first();
        $productDetails = json_decode(json_encode($productDetails));
        //echo "<pre>"; print_r($productDetails); die;
        $categoryDetails = Category::where(['id'=>$productDetails->category_id])->first();
        $category_name = $categoryDetails->name;
        if($request->isMethod('post')){
            $data = $request->all();
            //echo "<pre>"; print_r($data); die;
            foreach($data['sku'] as $key => $val){
                if(!empty($val)){
                    $attrCountSKU = ProductsAttribute::where(['sku'=>$val])->count();
                    if($attrCountSKU>0){
                        return redirect('admin/add-attribute/'.$id)->with('flash_message_error', 'SKU already exists. Please add another SKU.');    
                    }
                    $attrCountSizes = ProductsAttribute::where(['product_id'=>$id,'size'=>$data['size'][$key]])->count();
                    if($attrCountSizes>0){
                        return redirect('admin/add-attribute/'.$id)->with('flash_message_error', 'Attribute already exists. Please add another Attribute.');    
                    }
                    $attr = new ProductsAttribute;
                    $attr->product_id = $id;
                    $attr->sku = $val;
                    $attr->size = $data['size'][$key];
                    $attr->price = $data['price'][$key];
                    $attr->stock = $data['stock'][$key];
                    $attr->save();
                }
            }
            return redirect('admin/add-attribute/'.$id)->with('flash_message_success', 'Product Attributes has been added successfully');
        }
        $title = "Add Attribute";
        return view('admin.products.add_attribute')->with(compact('title','productDetails','category_name'));
    }

    public function deleteAttribute($id = null){
        ProductsAttribute::where(['id'=>$id])->delete();
        return redirect()->back()->with('flash_message_success', 'Product Attribute has been deleted successfully');
    }

    public function products($url=null){
        //show 404 page if category url does not exist

        $countCategory = Category::where(['url'=>$url, 'status'=>1])->count();
        if($countCategory==0){
            abort(404);
        }

        // Get All Categories and Sub Categories
        $categories = Category::with('categories')->where(['parent_id' => 0])->get();

        $categoriesDetails = Category::where(['url'=>$url])->first();


        if($categoriesDetails->parent_id==0){

            $subCategories = Category::where(['parent_id'=>$categoriesDetails->id])->get();

            $subCategories = json_decode(json_encode($subCategories));

            foreach($subCategories as $subcat){

                $cat_ids[] = $subcat->id;
            }
            $productsAll = Product::whereIn('category_id', $cat_ids)->get();

        }else{
            $productsAll = Product::where(['category_id'=>$categoriesDetails->id])->get(); 

        }

        return view('products.listing')->with(compact('categories','categoriesDetails', 'productsAll'));
    }

    public function product($id=null){

         $productDetails = Product::with('attributes')->where('id',$id)->first();
         $productDetails = json_decode(json_encode($productDetails));
         //echo "<pre>"; print_r($productDetails); die;

         
        // Get All Categories and Sub Categories
        $categories = Category::with('categories')->where(['parent_id' => 0])->get();


         return view('products.detail')->with(compact('productDetails','categories'));
    }

    public function getProductPrice(Request $request){
        $data = $request->all();
        //echo "<pre>"; print_r($data); die;
        $proArr = explode("-",$data['idSize']);
        //echo $proArr[0]; echo $proArr[1];die;
        $proArr = ProductsAttribute::where(['product_id'=> $proArr[0], 'size' => $proArr[1]])-> first();
        echo $proArr->price;
    }
}
