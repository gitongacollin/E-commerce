<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Auth;
use Session;
use Image;
use DB;
use App\Category;
use App\Product;
use App\ProductsAttribute;
use App\ProductsImage;
use App\Coupon;
use App\DeliveryAddress;
use App\User;
use App\County;
use App\Order;
use App\OrdersProduct;

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

            if(empty($data['status'])){
                $status = 0;
            }else{
                $status = 1;
            }
            $product->status = $status;
            $product->save();
            //return redirect()->back()->with('flash_message_success','Product has been added Successfully!');
            return redirect('/admin/view-product')->with('flash_message_success','Product has been added Successfully!');
        }

    	$categories = Category::where(['parent_id'=>0])->get();
        $categories_dropdown = "<option value='Select' selected disabled>Select</option>";
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
            if(empty($data['status'])){
                $status='0';
            }else{
                $status='1';
            }
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
                'product_code'=>$data['product_code'],'care'=>$data['care'],'description'=>$data['description'],'price'=>$data['price'],'image'=>$fileName, 'status'=>$status]);
        
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

                    //prevent duplicate SKU 
                    $attrCountSKU = ProductsAttribute::where(['sku'=>$val])->count();
                    if($attrCountSKU>0){
                        return redirect('admin/add-attribute/'.$id)->with('flash_message_error', '"'.$data['sku'][$key]. '" SKU already exists. Please add another SKU.');    
                    }

                    //prevent duplicate size
                    $attrCountSizes = ProductsAttribute::where(['product_id'=>$id,'size'=>$data['size'][$key]])->count();
                    if($attrCountSizes>0){
                        return redirect('admin/add-attribute/'.$id)->with('flash_message_error', '"'.$data['size'][$key]. '" size already exists. Please add another size.');    
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

    public function editAttribute(Request $request,$id=null){
        if($request->isMethod('post')){
            $data = $request->all();
            //echo "<pre>"; print_r($data);die;
            foreach($data['idAttr'] as $key =>$attr){
                ProductsAttribute::where(['id'=>$data['idAttr'][$key]])->update(['price'=>$data['price'][$key],'stock'=>$data['stock'][$key]]);
            }
            return redirect()->back()->with('flash_message_success','Products Attributes has been updates successfully');
        }
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
            $productsAll = Product::whereIn('category_id', $cat_ids)->where('status',1)->get();

        }else{
            $productsAll = Product::where(['category_id'=>$categoriesDetails->id])->where('status',1)->get(); 

        }

        return view('products.listing')->with(compact('categories','categoriesDetails', 'productsAll'));
    }

    public function searchProducts(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            $categories = Category::with('categories')->where(['parent_id' => 0])->get();
            $search_product = $data['product'];
            /*$productsAll = Product::where('product_name','like','%'.$search_product.'%')->orwhere('product_code',$search_product)->where('status',1)->paginate();*/
            $productsAll = Product::where(function($query) use($search_product)
            {
                $query->where('product_name','like','%'.$search_product.'%');
                $query->orWhere('product_code','like','%'.$search_product.'%');
                $query->orWhere('description','like','%'.$search_product.'%');
            })->where('status',1)->get();

            $breadcrumb = "<a href='/'>Home</a> / ".$search_product;

            return view('products.listing')->with(compact('categories','productsAll','search_product','breadcrumb')); 
        }
    }

    public function product($id=null){

        //show 404 page if product status is 0

        $productCount = Product::where(['id'=>$id, 'status'=>1])->count();
        if($productCount==0){
            abort(404);
        }

         $productDetails = Product::with('attributes')->where('id',$id)->first();
         $productDetails = json_decode(json_encode($productDetails));
         //echo "<pre>"; print_r($productDetails); die;

         
        // Get All Categories and Sub Categories
        $categories = Category::with('categories')->where(['parent_id' => 0])->get();

        //Get Alt images
        $productAltImages = ProductsImage::where('product_id',$id)->get();
        //$productAltImages = json_decode(json_encode($productAltImages));
        //echo "<pre>"; print_r($productAltImages); die;

        $total_stock= ProductsAttribute::where('product_id',$id)->sum('stock');

        $relatedProducts = Product::where('id','!=',$id)->where(['category_id'=>$productDetails->category_id])->get();
        /*$relatedProducts = json_decode(json_encode($relatedProducts));
        echo "<pre>";print_r($relatedProducts);die;*/

        // foreach ($relatedProducts->chunk(3) as $chunk) {
        //     foreach($chunk as $item){
        //         echo $item; echo "<br>";
        //     }
        //     echo "<br><br><br>";
        // }
        // die;


         return view('products.detail')->with(compact('productDetails','categories','productAltImages','total_stock','relatedProducts'));
    }

    public function getProductPrice(Request $request){
        $data = $request->all();
        //echo "<pre>"; print_r($data); die;
        $proArr = explode("-",$data['idSize']);
        //echo $proArr[0]; echo $proArr[1];die;
        $proArr = ProductsAttribute::where(['product_id'=> $proArr[0], 'size' => $proArr[1]])-> first();
        echo $proArr->price;
        echo "#";
        echo $proArr->stock;
    }

    public function addImages(Request $request, $id=null){
        $productDetails = Product::with('attributes')->where(['id' => $id])->first();
        


        if($request->isMethod('post')){
            //add Images 
            $data = $request->all();
            //echo "<pre>"; print_r($data);die;
            if($request->hasFile('image')){
                $files = $request->file('image');
                foreach($files as $file){
                    //Upload image after file resize
                    $image = new ProductsImage;
                    $extension = $file->getClientOriginalExtension();
                    $fileName = rand(111,99999).'.'.$extension;
                    $large_image_path = 'images/backend_images/products/large/'.$fileName;
                    $medium_image_path = 'images/backend_images/products/medium/'.$fileName;
                    $small_image_path = 'images/backend_images/products/small/'.$fileName;
                    Image::make($file)->save($large_image_path);
                    Image::make($file)->resize(600,600)->save($medium_image_path);
                    Image::make($file)->resize(300,300)->save($small_image_path);
                    $image->image = $fileName;
                    $image->product_id = $data['product_id'];
                    $image->save();
                }
            }


            return redirect('admin/add-images/'.$id)->with('flash_message_success', 'Image of the product has been added successfully');
        }
        $productsImage = ProductsImage::where(['product_id'=>$id])->get();
        $title = "Add Images";
        return view('admin.products.add_images')->with(compact('title','productsImage','productDetails'));
    }

    public function deleteAltImage($id=null){
        // Get Product Image
        $productImage = ProductsImage::where('id',$id)->first();

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

        // Delete Image from Products Images table
        ProductsImage::where(['id'=>$id])->delete();

        return redirect()->back()->with('flash_message_success', 'Alternative Product image(s) has been deleted successfully');
    }

    public function cart(){
        if(Auth::check()){
            $user_email = Auth::user()->email;
            $userCart = DB::table('cart')->where(['user_email' => $user_email])->get();

        }else{
            $session_id = Session::get('session_id');
            $userCart = DB::table('cart')->where(['session_id' => $session_id])->get();
        }
        
        foreach($userCart as $key => $product){
            // echo $product->product_id;
            $productDetails = product::where('id',$product->product_id)->first();
            $userCart[$key]->image = $productDetails->image;
        }
        // echo "<pre>"; print_r($userCart); die;

        return view('products.cart')->with(compact('userCart'));
    }


    public function checkout(Request $request){
        $user_id = Auth::user()->id;
        $user_email = Auth::user()->email;
        $userDetails = User::find($user_id);
        $counties = County::get();

        //Check if Shipping Address exists
        $shippingCount = DeliveryAddress::where('user_id',$user_id)->count();
        $shippingDetails = array();
        if($shippingCount>0){
            $shippingDetails = DeliveryAddress::where('user_id',$user_id)->first();
        }

        // Update cart table with user email
        $session_id = Session::get('session_id');
        DB::table('cart')->where(['session_id'=>$session_id])->update(['user_email'=>$user_email]);
        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            // Return to Checkout page if any of the field is empty
            if(empty($data['billing_name']) || empty($data['billing_address']) || empty($data['billing_county']) || empty($data['billing_region']) || empty($data['billing_phone']) || empty($data['shipping_name']) || empty($data['shipping_address']) || empty($data['shipping_county']) || empty($data['shipping_region']) || empty($data['shipping_phone']) ){
                    return redirect()->back()->with('flash_message_error','Please fill all fields to Checkout!');
            }
            // Update User details
            User::where('id',$user_id)->update(['name'=>$data['billing_name'],'address'=>$data['billing_address'],'county'=>$data['billing_county'],'region'=>$data['billing_region'],'phone'=>$data['billing_phone']]);

            if($shippingCount>0){
                // Update Shipping Address
                DeliveryAddress::where('user_id',$user_id)->update(['name'=>$data['shipping_name'],'address'=>$data['shipping_address'],'county'=>$data['shipping_county'],'region'=>$data['shipping_region'],'phone'=>$data['shipping_phone']]);
            }else{
                // Add New Shipping Address
                $shipping = new DeliveryAddress;
                $shipping->user_id = $user_id;
                $shipping->user_email = $user_email;
                $shipping->name = $data['shipping_name'];
                $shipping->address = $data['shipping_address'];
                $shipping->county = $data['shipping_county'];
                $shipping->region = $data['shipping_region'];
                $shipping->phone = $data['shipping_phone'];
                $shipping->save();
            }

           /* $pincodeCount = DB::table('pincodes')->where('pincode',$data['shipping_pincode'])->count();
            if($pincodeCount == 0){
                return redirect()->back()->with('flash_message_error','Your location is not available for delivery. Please enter another location.');
            }*/

            /*echo "Redirect to order Review Page"; die;*/

            return redirect('/order-review');
        }

        $meta_title = "Checkout - Soko Freshy";
        return view('products.checkout')->with(compact('userDetails','counties','shippingDetails','meta_title'));
    }

    public function addtocart(Request $request){

        Session::forget('couponAmount');
        Session::forget('couponCode');

        $data = $request->all();
        // echo "<pre>"; print_r($data); die;

        if(empty(Auth::user()->email)){
            $data['user_email'] = '';
        }else{
            $data['user_email'] = Auth::user()->email;
        }

        $session_id = Session::get('session_id');
        if(!isset($session_id)){
            $session_id = str_random(40);
            Session::put('session_id',$session_id);
        }

        $sizeArr = explode("-",$data['size']);

        $countProducts = DB::table('cart')->where(['product_id'=>$data['product_id'],'size'=>$sizeArr[1],'session_id'=>$session_id])->count();
        if($countProducts>0){
            return redirect()->back()->with('flash_message_error','Product already exists in cart');
        }else{

            $getSKU = ProductsAttribute::select('sku')->where(['product_id' => $data['product_id'], 'size' => $sizeArr[1]])->first();

            DB::table('cart')->insert(['product_id'=>$data['product_id'],'product_name'=>$data['product_name'],'product_code'=>$getSKU->sku,'price'=>$data['price'],'size'=>$sizeArr[1],'quantity'=>$data['quantity'],'user_email'=>$data['user_email'],'session_id'=>$session_id]);
        }

        return redirect()->back()->with('flash_message_success','Product has been added to cart');
    }

    public function deleteCartProduct($id = null){

        Session::forget('couponAmount');
        Session::forget('couponCode');
        
        // echo $id; die;
        DB::table('cart')->where('id', $id)->delete();
        return redirect('cart')->with('flash_message_error', 'Product has been successfully removed from cart');
    }

    public function updateCartQuantity($id=null,$quantity=null){

        Session::forget('couponAmount');
        Session::forget('couponCode');

        $getCartDetails = DB::table('cart')->where('id',$id)->first();
        $getAttributeStock = ProductsAttribute::where('sku',$getCartDetails->product_code)->first();
        // echo $getAttributeStock->stock; echo "--";
        $updated_quantity =$getCartDetails->quantity+$quantity;
        if($getAttributeStock->stock >= $updated_quantity){
        DB::table('cart')->where('id',$id)->increment('quantity',$quantity);
        return redirect('cart')->with('flash_message_success', 'Product has been successfully updated');
        }else{
            return redirect('cart')->with('flash_message_error', 'Product is more than in stock ');
        }
    }

    public function applyCoupon(Request $request){

       Session::forget('couponAmount');
       Session::forget('couponCode');
        $data =$request->all();
        // echo "<pre>";print_r($data);die;
        $couponCount = Coupon::where('coupon_code',$data['coupon_code'])->count();
        if($couponCount == 0){
            return redirect()->back()->with('flash_message_error', 'Invalid coupon code');
        }else{
            //Get coupon details
            $couponDetails = Coupon::where('coupon_code',$data['coupon_code'])->first();
            //if coupon is inactive
            if($couponDetails->status==0){
                return redirect()->back()->with('flash_message_error','Coupon is not active');
            }

            //Check if coupon is expired
           $expiry_date = $couponDetails->expiry_date; 
           $current_date = date('y-m-d'); 
           if($expiry_date < $current_date){
            return redirect()->back()->with('flash_message_error', 'The coupon has expired');
           }
           //Coupon is valid
           //get cart total amount


           if(Auth::check()){
                $user_email = Auth::user()->email;
                $userCart = DB::table('cart')->where(['user_email' => $user_email])->get();

            }else{
                $session_id = Session::get('session_id');
                $userCart = DB::table('cart')->where(['session_id' => $session_id])->get();
            }

           $total_amount = 0;
           foreach ($userCart as $item ) {
               $total_amount = $total_amount + ($item->price * $item->quantity);
           }
           // Check if amount type is Fixed or Percentage
           if($couponDetails->amount_type == "Fixed"){
            $couponAmount = $couponDetails->amount;
           }else{
            $couponAmount = $total_amount * ($couponDetails->amount/100);
           }

           //Add Coupon code $ Amount in Session
           Session::put('couponAmount',$couponAmount);
           Session::put('couponCode',$data['coupon_code']);

           return redirect()->back()->with('flash_message_success', 'Coupon code successfully applied. Discount Applied!');

        }

    }

    public function orderReview(){
        $user_id = Auth::user()->id;
        $user_email = Auth::user()->email;
        $userDetails = User::where('id',$user_id)->first();
        $shippingDetails = DeliveryAddress::where('user_id',$user_id)->first();
        $shippingDetails = json_decode(json_encode($shippingDetails));
        
        $userCart = DB::table('cart')->where(['user_email' => $user_email])->get();
        $total_weight = 0;
        foreach($userCart as $key => $product){
            $productDetails = Product::where('id',$product->product_id)->first();
            $userCart[$key]->image = $productDetails->image;
            $total_weight = $total_weight + $productDetails->weight;
        }
        /*echo "<pre>";print_r($userCart); die;*/

        // Fetch Shipping Charges
        /*$shippingCharges = Product::get(ShippingCharges)($total_weight,$shippingDetails->county);
        Session::put('ShippingCharges',$shippingCharges);*/

        return view('products.order_review')->with(compact('userDetails','shippingDetails','userCart'));
    }

    public function placeOrder(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            $user_id = Auth::user()->id;
            $user_email = Auth::user()->email;


            //Get shipping address of User
            $shippingDetails = DeliveryAddress::where(['user_email' => $user_email])->first();
            /*$shippingDetails = json_decode(json_encode($shippingDetails));*/

            if(empty(Session::get('CouponCode'))){
               $coupon_code = ''; 
            }else{
               $coupon_code = Session::get('CouponCode'); 
            }

            if(empty(Session::get('CouponAmount'))){
               $coupon_amount = ''; 
            }else{
               $coupon_amount = Session::get('CouponAmount'); 
            }


            $order = new order;
            $order->user_id = $user_id;
            $order->user_email = $user_email;
            $order->name = $shippingDetails->name;
            $order->address = $shippingDetails->address;
            $order->county = $shippingDetails->county;
            $order->region = $shippingDetails->region;
            $order->phone = $shippingDetails->phone;
            $order->coupon_code = $coupon_code;
            $order->coupon_amount = $coupon_amount;
            $order->order_status = "New";
            $order->payment_method = $data['payment_method'];
            $order->grand_total = $data['grand_total'];
            $order->save();

            $order_id = DB::getPdo()->lastInsertId();

            $cartProducts = DB::table('cart')->where(['user_email'=>$user_email])->get();
            foreach($cartProducts as $pro){
                $cartpro = new OrdersProduct;
                $cartpro->order_id = $order_id;
                $cartpro->user_id = $user_id;
                $cartpro->product_id = $pro->product_id;
                $cartpro->product_code = $pro->product_code;
                $cartpro->product_name = $pro->product_name;
                $cartpro->product_size = $pro->size;
                $cartpro->product_price = $pro->price;
                $cartpro->product_qty = $pro->quantity;
                $cartpro->save();
            }

            Session::put('order_id',$order_id);
            Session::put('grand_total',$data['grand_total']);

            if($data['payment_method']=="Cash on Delivery"){

                $productDetails = Order::with('orders')->where('id',$order_id)->first();
                $productDetails = json_decode(json_encode($productDetails),true);
                /*echo "<pre>"; print_r($productDetails);*/ /*die;*/

                $userDetails = User::where('id',$user_id)->first();
                $userDetails = json_decode(json_encode($userDetails),true);
                /*echo "<pre>"; print_r($userDetails); die;*/
                /* Code for Order Email Start */
                $email = $user_email;
                $messageData = [
                    'email' => $email,
                    'name' => $shippingDetails->name,
                    'order_id' => $order_id,
                    'productDetails' => $productDetails,
                    'userDetails' => $userDetails
                ];
                Mail::send('emails.order',$messageData,function($message) use($email){
                    $message->to($email)->subject('Order Placed - SokoFreshy');    
                });
                /* Code for Order Email Ends */

                // COD - Redirect user to thanks page after saving order
                return redirect('/thanks');
            }else{
                // Paypal - Redirect user to paypal page after saving order
                return redirect('/paypal');
            }



            /*echo "<pre>";print_r($shippingDetails);die;*/
        }


    }

    public function thanks(Request $request){
        $user_email = Auth::user()->email;
        DB::table('cart')->where('user_email',$user_email)->delete();
        return view('orders.thanks');
    }

    public function paypal(Request $request){
        $user_email = Auth::user()->email;
        DB::table('cart')->where('user_email',$user_email)->delete();
        return view('orders.paypal');
    }

    public function userOrders(){
        $user_id = Auth::user()->id;
        $orders = Order::with('orders')->where('user_id',$user_id)->orderBy('id','DESC')->get();
        /*$orders = json_decode(json_encode($orders));
        echo "<pre>"; print_r($orders); die;*/
        return view('orders.user_orders')->with(compact('orders'));
    }

    public function userOrderDetails($order_id){
        $user_id = Auth::user()->id;
        $orderDetails = Order::with('orders')->where('id',$order_id)->first();

        return view('orders.user_order_details')->with(compact('orderDetails'));
    }

    public function viewOrders(){
        $orders = Order::with('orders')->orderBy('id','Desc')->get();
        $orders = json_decode(json_encode($orders));
        /*echo "<pre>"; print_r($orders); die;*/
        return view('admin.orders.view_orders')->with(compact('orders'));
    }

    public function viewOrderDetails($order_id){
        $orderDetails = Order::with('orders')->where('id',$order_id)->first();
        $orderDetails = json_decode(json_encode($orderDetails));
        /*echo "<pre>"; print_r($orderDetails); die;*/
        $user_id = $orderDetails->user_id;
        $userDetails = User::where('id',$user_id)->first();
        /*$userDetails = json_decode(json_encode($userDetails));
        echo "<pre>"; print_r($userDetails);*/
        return view('admin.orders.order_details')->with(compact('orderDetails','userDetails'));
    }

    public function viewOrderInvoice($order_id){
        $orderDetails = Order::with('orders')->where('id',$order_id)->first();
        $orderDetails = json_decode(json_encode($orderDetails));
        /*echo "<pre>"; print_r($orderDetails); die;*/
        $user_id = $orderDetails->user_id;
        $userDetails = User::where('id',$user_id)->first();
        /*$userDetails = json_decode(json_encode($userDetails));
        echo "<pre>"; print_r($userDetails);*/
        return view('admin.orders.order_invoice')->with(compact('orderDetails','userDetails'));
    }

    public function updateOrderStatus(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            Order::where('id',$data['order_id'])->update(['order_status'=>$data['order_status']]);
            return redirect()->back()->with('flash_message_success','Status of order has been updated successfully!');
        }
    }
}
