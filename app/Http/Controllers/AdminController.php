<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;
use App\User;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use DB;
use Illuminate\Support\Facades\Hash;
use App\Admin;

class AdminController extends Controller
{
    public function login(Request $request){
    	if($request->isMethod('post')){
    		$data = $request->input();
            echo $adminCount = Admin::where(['username' => $data['username'],'password'=>md5($data['password']),'status'=>1])->count(); 
            if($adminCount > 0){
    			//echo "Success"; die;
                Session::put('adminSession',$data['username']);
                return redirect('/admin/dashboard');
    		}else{
    			//echo "Failed"; die;
                return redirect('/admin')->with('flash_message_error','Invalid Username or Password');
    		}
    	}
    	return view('admin.admin_login');
    }

    public function dashboard(){
        if(Session::has('adminSession')){
            //perform all dashboard tasks
        }else{
            return redirect('/admin')->with('flash_message_error','Please login to access');
        }
        return view('admin.dashboard');
    }

    public function settings(){
        $adminDetails = Admin::where(['username'=>Session::get('adminSession')])->first();

        return view('admin.settings')->with(compact('adminDetails'));
    }


    public function chkPassword(Request $request){
        $data = $request->all(); 
        $adminCount = Admin::where(['username' => Session::get('adminSession'),'password'=>md5($data['current_pwd'])])->count(); 
            if ($adminCount == 1) {
                //echo '{"valid":true}';die;
                echo "true"; die;
            } else {
                //echo '{"valid":false}';die;
                echo "false"; die;
            }
    }

    public function updatePassword(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            //echo "<pre>"; print_r($data); die;
            /*$check_password = Admin::where(['username' => Session::get('adminSession')])->first();*/
            $current_password = $data['current_pwd'];
            $adminCount = Admin::where(['username' => Session::get('adminSession'),'password'=>md5($data['current_pwd'])])->count();
            if ($adminCount == 1) {
                // here you know data is valid
                $password = md5($data['new_pwd']);
                Admin::where('username',Session::get('adminSession'))->update(['password'=>$password]);
                return redirect('/admin/settings')->with('flash_message_success', 'Password updated successfully.');
            }else{
                return redirect('/admin/settings')->with('flash_message_error', 'Current Password entered is incorrect.');
            }
        }
    }


    public function logout(){
        Session::flush();
        return redirect('/admin')->with('flash_message_success','Logout Successful');
    }

    public function viewAdmins(){
        $admins = Admin::get();
        /*$admins = json_decode(json_encode($admins));
        echo "<pre>"; print_r($admins); die;*/
        return view('admin.admins.view_admins')->with(compact('admins'));
    }

    public function addAdmin(Request $request){
        if(Session::get('adminDetails')['type']!=="Admin"){
         return redirect('/admin/dashboard')->with('flash_message_error','You do not have access to view this page');
        }
        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>";print_r($data);die;*/
            $adminCount = Admin::where('username',$data['username'])->count();
            if($adminCount>0){
                return redirect()->back()->with('flash_message_error','Username already exists. Choose another username');
            }else{
                if(empty($data['status'])){
                    $data['status'] = 0;
                }
                if($data['type']=="Admin"){
                    $admin = new Admin;
                    $admin->type = $data['type'];
                    $admin->username = $data['username'];
                    $admin->password = md5($data['password']);
                    $admin->status = $data['status'];
                    $admin->save();
                    return redirect()->back()->with('flash_message_success','Admin added successfully');
                }elseif ($data['type']=="Sub Admin") {
                    if(empty($data['categories_access'])){
                        $data['categories_access'] = 0;
                    }
                    if(empty($data['products_access'])){
                        $data['products_access'] = 0;
                    }
                    if(empty($data['orders_access'])){
                        $data['orders_access'] = 0;
                    }
                    if(empty($data['users_access'])){
                        $data['users_access'] = 0;
                    }
                    $admin = new Admin;
                    $admin->type = $data['type'];
                    $admin->username = $data['username'];
                    $admin->password = md5($data['password']);
                    $admin->status = $data['status'];
                    $admin->categories_access = $data['categories_access'];
                    $admin->products_access = $data['products_access'];
                    $admin->orders_access = $data['orders_access'];
                    $admin->users_access = $data['users_access'];
                    $admin->save();
                    return redirect()->back()->with('flash_message_success','Sub Admin added successfully');
                }
            }
        }
        return view('admin.admins.add_admins');
    }

    public function editAdmin($id, Request $request){
        if(Session::get('adminDetails')['type']!=="Admin"){
         return redirect('/admin/dashboard')->with('flash_message_error','You do not have access to view this page');
        }
        $adminDetails = Admin::where('id',$id)->first();
        /*$adminDetails = json_decode(json_encode($adminDetails));
        echo "<pre>";print_r($adminDetails);die;*/
        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>";print_r($data);die;*/
            if(empty($data['status'])){
                    $data['status'] = 0;
                }
                if($data['type']=="Admin"){
                    Admin::where('username',$data['username'])->update(['password'=>md5($data['password']),'status'=>$data['status']]);
                    return redirect()->back()->with('flash_message_success','Admin updated successfully');
                }elseif ($data['type']=="Sub Admin") {
                    if(empty($data['categories_access'])){
                        $data['categories_access'] = 0;
                    }
                    if(empty($data['products_access'])){
                        $data['products_access'] = 0;
                    }
                    if(empty($data['orders_access'])){
                        $data['orders_access'] = 0;
                    }
                    if(empty($data['users_access'])){
                        $data['users_access'] = 0;
                }
                    Admin::where('username',$data['username'])->update(['password'=>md5($data['password']),'status'=>$data['status'],'categories_access'=>$data['categories_access'],'products_access'=>$data['products_access'],'orders_access'=>$data['orders_access'],'users_access'=>$data['users_access']]);
                    return redirect()->back()->with('flash_message_success','Sub Admin updated successfully');
                }
        }
        return view('admin.admins.edit_admins')->with(compact('adminDetails'));

    }

}
