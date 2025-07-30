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
  <a href="{{ url('/dashboard/laporan') }}" class="nav-link @if(isset($menu) && $menu == 'Laporan') active @endif">
    <i class="nav-icon fas fa-chart-pie"></i>
    <p>Laporan</p>
  </a>
</li>

<li class="nav-item">
  <a href="{{ url('/dashboard/fasilitas') }}" class="nav-link @if(isset($menu) && $menu == 'Fasilitas') active @endif">
    <i class="nav-icon fas fa-tools"></i>
    <p>Fasilitas</p>
  </a>
</li>

        <!-- Agar menu terakhir tidak tertutup -->
        <li class="nav-item">
          <a class="nav-link"></a>
        </li>

      </ul>
    </nav>
  </div>
</aside>
