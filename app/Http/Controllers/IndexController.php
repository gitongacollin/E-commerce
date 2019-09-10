<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Category;

class IndexController extends Controller
{
    public function index(){
    	//in Ascending oreder (by default)
    	//$productsAll = Product::get();

    	//in Descending order
    	//$productsAll = Product::orderBy('id','DESC')->get();

    	//random order
    	$productsAll = Product::inRandomOrder()->get();



    	//Get all Categories and sub categories
    	$categories = Category::where(['parent_id'=>0])->get();
    	//$categories = json_decode(json_encode($categories));
    	//echo "<pre>";print_r($categories); die;
    	$catgories_menu = "";
    	foreach ($categories as $cat) {
    		$catgories_menu .= "<div class= 'panel-heading'>
									<h4 class='panel-title'>
										<a data-toggle='collapse' data-parent='#".$cat->id."' href='#".$cat->url."'>
											<span class='badge pull-right'><i class='fa fa-plus'></i></span>
											".$cat->name."
										</a>
									</h4>
								</div>
								<div id='#".$cat->id."' class='panel-collapse collapse'>
									<div class='panel-body'>
										<ul>";
							    		$sub_categories = Category::where(['parent_id'=>$cat->id])->get();
							    		foreach($sub_categories as $subcat){
											$catgories_menu .= "<li><a href='#'>".$subcat->name." </a></li>";
										}
										$catgories_menu .= "</ul>
									</div>
								</div>"; 
    		
    	}






    	return view('index')->with(compact('productsAll','catgories_menu'));
    }
}
