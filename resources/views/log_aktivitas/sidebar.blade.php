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
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        <!-- Navigasi Umum -->
        <li class="nav-header">NAVIGATION</li>

        <!-- Menu: Module -->
        <li class="nav-item">
          <a href="{{ url('/module') }}" class="nav-link @if(isset($menu) && $menu == 'Module') active @endif">
            <i class="nav-icon fas fa-th"></i>
            <p>Module</p>
          </a>
        </li>

        <!-- Menu Lain -->
        <li class="nav-header">MENU</li>

        <!-- Menu: Log Aktivitas -->
        <li class="nav-item">
          <a href="{{ url('/log-aktivitas') }}" class="nav-link @if(isset($menu) && $menu == 'Log Aktivitas') active @endif">
            <i class="nav-icon fas fa-user-clock"></i>
            <p>Log Aktivitas</p>
          </a>
        </li>

        <!-- Tambahan menu lain (opsional) -->
        {{-- 
        <li class="nav-item">
          <a href="{{ url('/pengaturan') }}" class="nav-link @if(isset($menu) && $menu == 'Pengaturan') active @endif">
            <i class="nav-icon fas fa-cogs"></i>
            <p>Pengaturan</p>
          </a>
        </li>
        --}}

        <!-- Spacer bawah agar tidak tertutup -->
        <li class="nav-item">
          <a class="nav-link" style="pointer-events: none;"></a>
        </li>

      </ul>
    </nav>
  </div>
</aside>
