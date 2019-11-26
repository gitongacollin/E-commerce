<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use App\Admin;

class Adminlogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(empty(Session::has('adminSession'))){
            return redirect('/admin');
        }else{
            //Get admin/Sub Admin details
            $adminDetails = Admin::where('username',Session::get('adminSession'))->first();
            /*$adminDetails = json_decode(json_encode($adminDetails));
            echo "<pre>"; print_r($adminDetails);die;*/
        }
        return $next($request);
    }
}
