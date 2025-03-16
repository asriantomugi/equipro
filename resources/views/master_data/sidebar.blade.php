<script src="{{ asset('js/notifications.js') }}"></script>
<!-- Main Sidebar Container -->
  
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    
  <!-- Brand Logo -->
  <a href="{{asset('/')}}" class="brand-link">
    <img src="{{asset('dist/img/logo.png')}}" alt="logo" class="" style="width:20px;height:20px;">
    <!-- <span class="brand-text font-weight-light">AdminLTE 3</span> -->
  </a>

    <!-- Sidebar -->
   <div class="sidebar">
    
    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
      <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->

        <li class="nav-header">NAVIGATION</li>

        <li class="nav-item">
          <a href="{{url('/module')}}" class="nav-link @if($menu == 'Module') active @endif">
            <i class="nav-icon fas fa-th"></i>
            <p>Module</p>
          </a>
        </li>

        <li class="nav-header">MENU</li>

        <li class="nav-item">
          <a href="{{url('/master-data/home')}}" class="nav-link @if($menu == 'Master Data') active @endif">
            <i class="nav-icon fas fa-home"></i>
            <p>Home</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{url('/master-data/user/daftar')}}" class="nav-link @if($menu == 'User') active @endif">
            <i class="nav-icon fas fa-user"></i>
            <p>User</p>
          </a>
        </li>

        <!-- agar menu terakhir tidak tertutup -->
        <li class="nav-item">
          <a class="nav-link"></a>
        </li>

      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>

<!-- /.sidebar -->
</aside>
  