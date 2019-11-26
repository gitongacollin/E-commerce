@extends('layouts.adminLayout.admin_design')
@section('content')

<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="{{ url ('/admin/dashboard')}}" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="#">Admins</a> <a href="#" class="current">Edit Admins</a> </div>
    <h1>Edit Admins</h1>
    @if(Session::has('flash_message_error'))
      <div class="alert alert-error alert-block">
          <button type="button" class="close" data-dismiss="alert">×</button> 
              <strong>{!! session('flash_message_error') !!}</strong>
      </div>
    @endif   
    @if(Session::has('flash_message_success'))
        <div class="alert alert-success alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button> 
                <strong>{!! session('flash_message_success') !!}</strong>
        </div>
    @endif
  </div>
  <div class="container-fluid"><hr>
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"> <i class="icon-info-sign"></i> </span>
            <h5>Edit Admins</h5>
          </div>
          <div class="widget-content nopadding">
            <form class="form-horizontal" method="post" action="{{ url('/admin/edit-admin/'.$adminDetails->id)}}" name="edit_admins" id="edit_admins" novalidate="novalidate">
              @csrf
              <div class="control-group">
                <label class="control-label">Type</label>
                <div class="controls">
                  <input type="text" name="type" id="type" readonly="" value="{{ $adminDetails->type }}">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label">Username</label>
                <div class="controls">
                  <input type="text" name="username" id="username" readonly="" value="{{ $adminDetails->username }}">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label">Password</label>
                <div class="controls">
                  <input type="password" name="password" id="password" required="" >
                </div>
              </div>
              @if($adminDetails->type=="Sub Admin")
              <div class="control-group">
                <label class="control-label">Access</label>
                <div class="controls">
                  <input style="margin-top: -3px;" type="checkbox" name="categories_access" id="categories_access" value="1" @if($adminDetails->categories_access == "1") checked @endif>&nbsp; Categories &nbsp;&nbsp;&nbsp;
                  <input style="margin-top: -3px;" type="checkbox" name="products_access" id="products_access" value="1" @if($adminDetails->products_access == "1") checked @endif> Products &nbsp;&nbsp;&nbsp;
                  <input style="margin-top: -3px;" type="checkbox" name="orders_access" id="orders_access" value="1" @if($adminDetails->orders_access == "1") checked @endif>&nbsp; Orders &nbsp;&nbsp;&nbsp;
                  <input style="margin-top: -3px;" type="checkbox" name="users_access" id="users_access" value="1" @if($adminDetails->users_access == "1") checked @endif>&nbsp; Users &nbsp;&nbsp;&nbsp;
                </div>
              </div>
              @endif

               <!-- <div class="control-group">
                <label class="control-label">Admin Level</label>
                <div class="controls">
                  <select name="parent_id" style="width: 220px">
                    <option value="0">Main Level</option>
                  </select>
                </div>
              </div> -->
              <div class="control-group">
                <label class="control-label">Enable</label>
                <div class="controls">
                  <input type="checkbox" name="status" id="status" value="1" @if($adminDetails->status == "1") checked @endif>
                </div>
              </div>
              <div class="form-actions">
                <input type="submit" value="Edit Admin" class="btn btn-success">
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



@endsection