@extends('layouts.adminLayout.admin_design')
@section('content')

<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="{{ url ('/admin/dashboard') }}" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="{{ url('/admin/view-banner')}}">Banners</a> <a href="#" class="current">Edit Banner</a> </div>
    <h1>Banner</h1>
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
            <h5>Edit banner</h5>
          </div>
          <div class="widget-content nopadding">
            <form enctype="multipart/form-data" class="form-horizontal" method="post" action="{{ url('admin/edit-banner/'.$bannerDetails->id) }}" name="edit_banner" id="edit_banner" novalidate="novalidate">
              @csrf
              <div class="control-group">
                <label class="control-label">Image</label>
                <div class="controls">
                  <div id="uniform-undefined">
                    <table>
                      <tr>
                        <td>
                          <input name="image" id="image" type="file">
                          @if(!empty($bannerDetails->image))
                            <input type="hidden" name="current_image" value="{{ $bannerDetails->image }}"> 
                          @endif
                        </td>
                        <td>
                          @if(!empty($bannerDetails->image))
                            <img style="width:150px;" src="{{ asset('/images/frontend_images/banners/'.$bannerDetails->image) }}"> |<a href="{{ url('/admin/delete-banner-image/'.$bannerDetails->id) }}"> Delete</a>
                          @endif
                        </td>
                      </tr>
                    </table>
                </div>
               </div>
              </div>
              <div class="control-group">
                <label class="control-label">Title</label>
                <div class="controls">
                  <input type="text" name="title" id="title" value="{{ $bannerDetails->title }}">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label">Link</label>
                <div class="controls">
                  <input type="text" name="link" id="link" value="{{ $bannerDetails->link }}">
                </div>
              </div>

              <div class="control-group">
                <label class="control-label">Enable</label>
                <div class="controls">
                  <input type="checkbox" name="status" id="status" @if($bannerDetails->status == "1") checked @endif value="1">
                </div>
              </div>
              <div class="form-actions">
                <input type="submit" value="Edit Banner" class="btn btn-success">
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection