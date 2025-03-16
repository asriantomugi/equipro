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

        <!-- MENU HOME -->
        <li class="nav-item">
          <a href="{{url('/master-data/home')}}" class="nav-link @if($menu == 'Master Data') active @endif">
            <i class="nav-icon fas fa-home"></i>
            <p>Home</p>
          </a>
        </li>

        <!-- MENU USER -->
        <li class="nav-item">
          <a href="{{url('/master-data/user/daftar')}}" class="nav-link @if($menu == 'User') active @endif">
            <i class="nav-icon fas fa-user"></i>
            <p>User</p>
          </a>
        </li>

        <!-- MENU FASILITAS -->
        <li class="nav-item">
          <a href="{{url('/master-data/fasilitas/daftar')}}" class="nav-link @if($menu == 'Fasilitas') active @endif">
            <i class="nav-icon fas fa-plane"></i>
            <p>Fasilitas</p>
          </a>
        </li>

        <!-- MENU JENIS ALAT -->
        <li class="nav-item">
          <a href="{{url('/master-data/jenis-alat/daftar')}}" class="nav-link @if($menu == 'JenisAlat') active @endif">
            <i class="nav-icon fas fa-tools"></i>
            <p>Jenis Alat</p>
          </a>
        </li>

        <!-- MENU LOKASI -->
        <li class="nav-item @if($menu == 'Lokasi') menu-open @endif">
          <a href="#" class="nav-link @if($menu == 'Lokasi') active @endif">
            <i class="nav-icon fas fa-building"></i>
            <p>Lokasi<i class="right fas fa-angle-left"></i></p>
          </a>

          <ul class="nav nav-treeview">

            <li class="nav-item">
              <a href="{{url('/master-data/lokasi-tk-1/daftar')}}" class="nav-link @if($page == 'LokasiTk1') active @endif">
                <i class="fas fa-caret-right nav-icon"></i>
                <p>Lokasi Tk. I</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="{{url('/master-data/lokasi-tk-2/daftar')}}" class="nav-link @if($page == 'LokasiTk2') active @endif">
                <i class="fas fa-caret-right nav-icon"></i>
                <p>Lokasi Tk. II</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="{{url('/master-data/lokasi-tk-3/daftar')}}" class="nav-link @if($page == 'LokasiTk3') active @endif">
                <i class="fas fa-caret-right nav-icon"></i>
                <p>Lokasi Tk. III</p>
              </a>
            </li>

          </ul>
        </li>
        <!----- END OF MENU USER ----->

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
  