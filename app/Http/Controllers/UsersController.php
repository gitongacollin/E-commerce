<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;
use Session;
use Illuminate\Support\Facades\Mail;

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

    public function userLogout(){
        //Session::flush();
        Auth::logout();
        Session::forget('frontSession');
        return redirect('/')->with('flash_message_success','Logout Successful');
    }
}