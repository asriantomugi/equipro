<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    
    <!-- User Profile Dropdown Menu -->
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-user"></i>
        <span class="brand-text font-weight-light">Selamat datang, {{strtoupper((session()->get('name')))}} ({{session()->get('role_name')}}) !</span>
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        
        <!-- Profile Menu Items -->
        <a href="{{url('/profile')}}" class="dropdown-item">
          <i class="fas fa-user mr-2"></i> Ubah Profil
        </a>
        
        <a href="{{url('/profile/ubah_password')}}" class="dropdown-item">
          <i class="fas fa-key mr-2"></i> Ubah Password
        </a>
        
        <a href="{{url('/profile/activity-log')}}" class="dropdown-item">
          <i class="fas fa-history mr-2"></i> Log Aktivitas
        </a>
        
        <div class="dropdown-divider"></div>
        
        <a href="{{url('/logout')}}" class="dropdown-item">
          <i class="fa fa-power-off mr-2"></i> Logout
        </a>

      </div>
    </li>
  </ul>
</nav>
<!-- /.navbar -->
