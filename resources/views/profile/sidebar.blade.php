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

        <!-- MENU MODULE -->
        <li class="nav-item">
          <a href="{{url('/module')}}" class="nav-link @if($menu == 'Module') active @endif">
            <i class="nav-icon fas fa-th"></i>
            <p>Module</p>
          </a>
        </li>

        <li class="nav-header">MENU</li>
        <!-- MENU PROFIL -->
        <li class="nav-item @if($menu == 'Profile') menu-open @endif">
          <a href="#" class="nav-link @if($menu == 'Profile') active @endif">
            <i class="nav-icon fas fa-user-cog"></i>
            <p>
              Profil
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
            <li class="nav-item">
              <a href="{{ route('profile.edit') }}" class="nav-link @if(request()->routeIs('profile.edit')) active @endif">
                <i class="far fa-circle nav-icon"></i>
                <p>Edit Profil</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('profile.ubah_password') }}" class="nav-link @if(request()->routeIs('profile.ubah_password')) active @endif">
                <i class="far fa-circle nav-icon"></i>
                <p>Ubah Password</p>
              </a>
            </li>
          </ul>
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
