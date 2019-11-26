<?php $url = url()->current(); ?>
<!--sidebar-menu-->
<div id="sidebar"><a href="#" class="visible-phone"><i class="icon icon-home"></i> Dashboard</a>
  <ul>
    <li <?php if (preg_match("/dashboard/i", $url)) { ?>class="active" <?php } ?>><a href="{{ url ('/admin/dashboard')}}"><i class="icon icon-home"></i> <span>Dashboard</span></a> </li>
    <li class="submenu"> <a href="#"><i class="icon icon-th-list"></i> <span>Admins</span> <span class="label label-important">0</span></a>
      <ul <?php if (preg_match("/admins/i", $url)) { ?>style="display: block;" <?php } ?>>
        <li <?php if (preg_match("/add-admins/i", $url)) { ?>class="active" <?php } ?>><a href="{{ url('/admin/add-admins')}}">Add admins</a></li>
        <li <?php if (preg_match("/view-admins/i", $url)) { ?>class="active" <?php } ?>><a href="{{ url('/admin/view-admins')}}">View Admins  </a></li>
      </ul>
    </li>
    <?php
    $base_user_url = trim(basename($url));
    ?>
    <li class="submenu"> <a href="#"><i class="icon icon-th-list"></i> <span>Users</span> <span class="label label-important">0</span></a>
      <ul <?php if (preg_match("/user/i", $url)) { ?>style="display: block;" <?php } ?> >
        <li <?php if ($base_user_url=="view-users") { ?>class="active" <?php } ?> ><a href="{{ url('/admin/view-users')}}">View Users</a></li>
        <li <?php if ($base_user_url=="view-users-charts") { ?>class="active" <?php } ?> ><a href="{{ url('/admin/view-users-charts')}}">View Charts</a></li>
      </ul>
    </li>
    <li class="submenu"> <a href="#"><i class="icon icon-th-list"></i> <span>Categories</span> <span class="label label-important">0</span></a>
      <ul <?php if (preg_match("/category/i", $url)) { ?>style="display: block;" <?php } ?>>
        <li <?php if (preg_match("/add-category/i", $url)) { ?>class="active" <?php } ?>><a href="{{ url('/admin/add-category')}}">Add Category</a></li>
        <li <?php if (preg_match("/view-category/i", $url)) { ?>class="active" <?php } ?>><a href="{{ url('/admin/view-category')}}">View Categories  </a></li>
      </ul>
    </li>
    <li class="submenu"> <a href="#"><i class="icon icon-th-list"></i> <span>Products</span> <span class="label label-important">0</span></a>
      <ul <?php if (preg_match("/product/i", $url)) { ?>style="display: block;" <?php } ?>>
        <li <?php if (preg_match("/add-product/i", $url)) { ?>class="active" <?php } ?>><a href="{{ url('/admin/add-product')}}">Add Product</a></li>
        <li <?php if (preg_match("/view-product/i", $url)) { ?>class="active" <?php } ?> ><a href="{{ url('/admin/view-product')}}">View Product  </a></li>
      </ul>
    </li>
    <li class="submenu"> <a href="#"><i class="icon icon-th-list"></i> <span>Coupon</span> <span class="label label-important">0</span></a>
      <ul <?php if (preg_match("/coupon/i", $url)) { ?>style="display: block;" <?php } ?> >
        <li <?php if (preg_match("/add-coupon/i", $url)) { ?>class="active" <?php } ?> ><a href="{{ url('/admin/add-coupon')}}">Add Coupon</a></li>
        <li <?php if (preg_match("/view-coupon/i", $url)) { ?>class="active" <?php } ?> ><a href="{{ url('/admin/view-coupon')}}">View Coupon</a></li>
      </ul>
    </li>

    <li class="submenu"> <a href="#"><i class="icon icon-th-list"></i> <span>Banner</span> <span class="label label-important">0</span></a>
      <ul <?php if (preg_match("/banner/i", $url)) { ?>style="display: block;" <?php } ?> >
        <li <?php if (preg_match("/add-banner/i", $url)) { ?>class="active" <?php } ?> ><a href="{{ url('/admin/add-banner')}}">Add Banner</a></li>
        <li <?php if (preg_match("/view-banners/i", $url)) { ?>class="active" <?php } ?> ><a href="{{ url('/admin/view-banners')}}">View Banner</a></li>
      </ul>
    </li>
    <li class="submenu"> <a href="#"><i class="icon icon-th-list"></i> <span>Orders</span> <span class="label label-important">0</span></a>
      <ul <?php if (preg_match("/order/i", $url)) { ?>style="display: block;" <?php } ?> >
        <li <?php if (preg_match("/view-orders/i", $url)) { ?>class="active" <?php } ?> ><a href="{{ url('/admin/view-orders')}}">View Orders</a></li>
        <li <?php if (preg_match("/view-order-charts/i", $url)) { ?>class="active" <?php } ?> ><a href="{{ url('/admin/view-order-charts')}}">View Orders Charts</a></li>
      </ul>
    </li>
    <li class="submenu"> <a href="#"><i class="icon icon-th-list"></i> <span>Enquiries</span> <span class="label label-important">1</span></a>
      <ul <?php if (preg_match("/enquiries/i", $url)){ ?> style="display: block;" <?php } ?>>
        <li <?php if (preg_match("/view-enquiries/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/view-enquiries')}}">View Enquiries</a></li>
      </ul>
    </li>
    <li class="submenu"> <a href="#"><i class="icon icon-th-list"></i> <span>CMS Pages</span> <span class="label label-important">2</span></a>
      <ul <?php if (preg_match("/cms-page/i", $url)){ ?> style="display: block;" <?php } ?>>
        <li <?php if (preg_match("/add-cms-page/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/add-cms-page')}}">Add CMS Page</a></li>
        <li <?php if (preg_match("/view-cms-pages/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/view-cms-pages')}}">View CMS Pages</a></li>
      </ul>
    </li>
   <!--  <li><a class="submenu" href="{{ url('/admin/users') }}"> <i class="icon icon-th-list"></i> <span>Manage Users</span> <span class="label label-important">0</span></a></li>
    <li><a class="submenu" href="{{ url('/admin/role') }}"> <i class="icon icon-th-list"></i> <span>Manage Roles</span> <span class="label label-important">0</span></a>
    <li> <a href="#"><i class="icon icon-inbox"></i> <span>Invoice</span></a> </li> -->
    
</div>
<!--sidebar-menu-->