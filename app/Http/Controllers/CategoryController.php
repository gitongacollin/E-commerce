<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;

class CategoryController extends Controller
{
   public function addCategory(Request $request)
   {
      if(Session::get('adminDetails')['categories_access']==0){
         return redirect('/admin/dashboard')->with('flash_message_error','You do not have access to view this page');
      }
   	if($request->isMethod('post')){
   		$data = $request->all();
   		//echo "<pre>"; print_r($data); die;

         if(empty($data['status'])){
            $status = 0;
         }else{
            $status = 1;
         }

   		$category= new Category;
   		$category->name = $data['category_name'];
         $category->parent_id = $data['parent_id'];
   		$category->description = $data['description'];
   		$category->url = $data['url'];
         $category->status = $status;
   		$category->save();
   		return redirect('/admin/view-category')->with('flash_message_success','Category Successfully added!');
   	}
      $levels = Category::where(['parent_id'=>0])->get();
   	return view('admin.categories.add_category')->with(compact('levels'));
   }

   public function viewCategory()
   {
   	$categories = Category::get();
   	$categories = json_decode(json_encode($categories));
   	return view('admin.categories.view_category')->with(compact('categories'));
   }

   public function editCategory(Request $request, $id = null)
   {
      if(Session::get('adminDetails')['categories_access']==0){
         return redirect('/admin/dashboard')->with('flash_message_error','You do not have access to view this page');
      }
   	if($request->isMethod('post'))
   	{
   		$data = $request->all();

         if(empty($data['status'])){
            $status = 0;
         }else{
            $status = 1;
         }

   		Category::where(['id'=>$id])->update(['name'=>$data['category_name'],'description'=>$data['description'],'url'=>$data['url'], 'status'=>$status]);
   		return redirect('/admin/view-category')->with('flash_message_success','Category Successfully updated!');
   	}
   	$categoryDetails= Category::where(['id'=>$id])->first();
      $levels = Category::where(['parent_id'=>0])->get();
   	return view ('admin.categories.edit_category')->with(compact('categoryDetails','levels'));
   }

   public function deleteCategory(Request $request,$id = null){
   	if(!empty($id)){
   		Category::where(['id' =>$id])->delete();
   		return redirect()->back()->with('flash_message_success','Category Successfully deleted!'); 
   	}
   }
   public function viewCategories(){
      if(Session::get('adminDetails')['categories_access']==0){
         return redirect('/admin/dashboard')->with('flash_message_error','You do not have access to view this page');
      }
      $categories = Category::get();
      return view('admin.categories.view_categories')->with(compact('categories'));
   }
}
