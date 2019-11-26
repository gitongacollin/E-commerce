<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;
use DB;
use Session;
use App\County;
use App\SubCounty;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UsersController extends Controller
{

    public function loginRegister(){
    	return view('users.login_register');
    }


    public function userLogin(Request $request){
    	if($request->isMethod('post')){
    		$data = $request->all();
    		//echo"<pre>";print_r($data);die();
    		if(Auth::attempt(['email'=>$data['email'],'password'=>$data['password']])){
                $userStatus = User::where('email',$data['email'])->first();
                if(!$userStatus->hasVerifiedEmail()){
                    return redirect()->back()->with('flash_message_error','Your account is not activated! Please confirm your email to activate.');    
                }
                Session::put('frontSession',$data['email']);

                if(!empty(Session::get('session_id'))){
                    $session_id = Session::get('session_id');
                    DB::table('cart')->where('session_id',$session_id)->update(['user_email' => $data['email']]);
                }

                return redirect('/');

            

            }else{
                return redirect()->back()->with('flash_message_error','Invalid Username or Password!');
            }
    	}
    }

    public function register(Request $request){
    	if($request->isMethod('post')){
    		$data =$request->all();
    		//echo"<pre>"; print_r($data); die();
    		//Check if user already exists
    		$usersCount = User::where('email',$data['email'])->count();
    		if($usersCount>0){
    			return redirect()->back()->with('flash_message_error','Email already exists!');
    		}else{

    			$user = new User;
                $user->name = $data['name'];
                $user->email = $data['email'];
                $user->password = bcrypt($data['password']);
                date_default_timezone_set('Africa/Nairobi');
                $user->created_at = date("Y-m-d H:i:s");
                $user->updated_at = date("Y-m-d H:i:s");
                $user->save();


                // Send Confirmation Email
                $email = $data['email'];
                $messageData = ['email'=>$data['email'],'name'=>$data['name'],'code'=>base64_encode($data['email'])];
                Mail::send('emails.confirmation',$messageData,function($message) use($email){
                    $message->to($email)->subject('Confirm your Soko Freshy Account');
                });

                return redirect()->back()->with('flash_message_success','Please confirm your email to activate your account!');

                if(Auth::attempt(['email'=>$data['email'],'password'=>$data['password']])){
                	
                    Session::put('frontSession',$data['email']);

                    if(!empty(Session::get('session_id'))){
                        $session_id = Session::get('session_id');
                        DB::table('cart')->where('session_id',$session_id)->update(['user_email' => $data['email']]);
                    }

                    return redirect('/');
                }
    		}
    	}
    }

    public function confirmAccount($email){
        $email = base64_decode($email);
        $userCount = User::where('email',$email)->count();
        if($userCount > 0){
            $userDetails = User::where('email',$email)->first();
            if($userDetails->markEmailAsVerified()){
                return redirect('')->with('flash_message_error','Your Email account is already activated. You can login.');
            }else{
                User::where('email',$email)->update(['email_verified_at'=>now()]);

                // Send Welcome Email
                $messageData = ['email'=>$email,'name'=>$userDetails->name];
                Mail::send('emails.welcome',$messageData,function($message) use($email){
                    $message->to($email)->subject('Welcome to Soko Freshy Website');
                });

                return redirect('')->with('flash_message_success','Your Email account is activated. You can login.');
            }
        }else{
            abort(404);
        }
    }

    public function checkEmail(Request $request){
    	// Check if User already exists
    	$data = $request->all();
		$usersCount = User::where('email',$data['email'])->count();
		if($usersCount>0){
			echo "false";
		}else{
			echo "true"; die;
		}		
    }


    public function account(Request $request){
       $user_id = Auth::user()->id;
       $userDetails = User::find($user_id);
       $counties = County::get();
       $sub_counties = SubCounty::get();
        

        if($request->isMethod('post')){
            $data = $request->all();

            if(empty($data['name'])){
                return redirect()->back()->with('flash_message_error','Please enter your Name to update your account details!');    
            }
            if(empty($data['email'])){
                $data['email'] = '';    
            }

            if(empty($data['address'])){
                $data['address'] = '';    
            }

            if(empty($data['county'])){
                $data['county'] = '';    
            }

            if(empty($data['region'])){
                $data['region'] = '';    
            }
            if(empty($data['phone'])){
                $data['phone'] = '';    
            }

            $user = User::find($user_id);
            $user->name = $data['name'];
            $user->name = $data['email'];
            $user->address = $data['address'];
            $user->region = $data['region'];
            $user->county = $data['county'];
            $user->phone = $data['phone'];
            $user->save();
            return redirect()->back()->with('flash_message_success','Details have been updated successfully.');
        }
        
        return view('users.account')->with(compact('counties','sub_counties','userDetails'));
    }

    public function chkUserPassword(Request $request){
        $data = $request->all();
        /*echo"<pre>";print_r($data);die;*/
        $current_password =$data['current_pass'];
        $user_id = Auth::User()->id;
        $check_password = User::where('id',$user_id)->first();
        if(Hash::check($current_password,$check_password->password)){
            echo "true";die;
        }else{
            echo "false";die;
        }
    }

    public function updatePassword(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>";print_r($data);die;*/
            $old_pass = User::where('id',Auth::User()->id)->first();
            $current_pass = $data['current_pass'];
            if(Hash::check($current_pass,$old_pass->password)){
                //Update password
                $new_pass= bcrypt($data['new_pass']);
                User::where('id',Auth::User()->id)->update(['password'=>$new_pass]);
                return redirect()->back()->with('flash_message_success',' Password updated successfully!');
            }else{
                return redirect()->back()->with('flash_message_error','Current password is incorrect');
            }
        }
    }
    

    public function userLogout(){
        //Session::flush();
        Auth::logout();
        Session::forget('frontSession');
        Session::forget('session_id');
        return redirect('/')->with('flash_message_success','Logout Successful');
    }

    public function viewUsers(){
        $users = User::get();
        return view('admin.users.view_users')->with(compact('users'));
    }

    public function viewUsersCharts(){
        $current_month_users = User::whereYear('created_at', Carbon::now()->year)
                                ->whereMonth('created_at', Carbon::now()->month)->count(); 
        $last_month_users = User::whereYear('created_at', Carbon::now()->year)
                                ->whereMonth('created_at', Carbon::now()->subMonth(1))->count();
        $last_two_month_users = User::whereYear('created_at', Carbon::now()->year)
                                ->whereMonth('created_at', Carbon::now()->subMonth(2))->count();
        return view('admin.users.view_users_charts')->with(compact('current_month_users','last_month_users','last_two_month_users'));
    }
}
