<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">

  <!-- Brand Logo -->
  <a href="{{ url('/') }}" class="brand-link">
    <img src="{{ asset('dist/img/logo.png') }}" alt="logo" style="width:20px;height:20px;">
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        <!-- Header Navigasi -->
        <li class="nav-header">NAVIGATION</li>

        <li class="nav-item">
          <a href="{{ url('/module') }}" class="nav-link @if(isset($menu) && $menu == 'Module') active @endif">
            <i class="nav-icon fas fa-th"></i>
            <p>Module</p>
          </a>
        </li>

        <!-- Menu Utama -->
        <li class="nav-header">MENU</li>

        <li class="nav-item">
          <a href="{{ url('/logbook/home') }}" class="nav-link @if(isset($menu) && $menu == 'Home') active @endif">
            <i class="nav-icon fas fa-home"></i>
            <p>Home</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ url('/logbook/laporan/daftar') }}" class="nav-link @if(isset($menu) && $menu == 'Laporan') active @endif">
            <i class="nav-icon fas fa-file-alt"></i>
            <p>Laporan</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ url('/logbook/riwayat/daftar') }}" class="nav-link @if(isset($menu) && $menu == 'Riwayat') active @endif">
            <i class="nav-icon fas fa-history"></i>
            <p>Riwayat</p>
          </a>
        </li>

        {{-- MENU LOG AKTIVITAS - hanya Super Admin --}}
        @if(session()->get('role_id') == config('constants.role.super_admin'))
        <li class="nav-item">
          <a href="{{ url('/log-aktivitas') }}" class="nav-link @if(isset($menu) && $menu == 'Log Aktivitas') active @endif">
            <i class="nav-icon fas fa-user-clock"></i>
            <p>Log Aktivitas</p>
          </a>
        </li>
        @endif

        <!-- Agar menu terakhir tidak tertutup -->
        <li class="nav-item">
          <a class="nav-link"></a>
        </li>

      </ul>
    </nav>
  </div>
</aside>