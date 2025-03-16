  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    
    <a href="{{asset('/')}}" class="brand-link">
      <img src="{{asset('dist/img/ap-logo-sekunder.png')}}" alt="logo" class="" style="width:220px;height:50px;">
      <!-- <span class="brand-text font-weight-light">AdminLTE 3</span> -->
    </a>
    

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <!--
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{asset('dist/img/user2-160x160.jpg')}}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{strtoupper((Auth::user()->name))}}</a>
        </div>
      </div>
    -->
      
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" 
            data-widget="treeview" 
            role="menu" 
            data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

          <li class="nav-header">NAVIGATION</li>

          <!-- MENU DASHBOARD -->
          <li class="nav-item @if($menu == 'Dashboard') menu-open @endif">
            <a href="#" class="nav-link @if($menu == 'Dashboard') active @endif">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard<i class="right fas fa-angle-left"></i></p>
            </a>

            <ul class="nav nav-treeview">

              <li class="nav-item">
                <a href="{{url('/')}}" class="nav-link @if($submenu == 'overview') active @endif">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Overview</p>
                </a>
              </li>

			       <li class="nav-item">
@if(session()->get('role_id') == 6)
                <a href="{{url('/profil/buau/perusahaan')}}" class="nav-link @if($submenu == 'profil') active @endif">
@else
                <a href="{{url('/profil')}}" class="nav-link @if($submenu == 'profil') active @endif">
@endif
                  <i class="far fa-circle nav-icon"></i>
                  <p>Profil Saya</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{url('/informasi/daftar')}}" class="nav-link @if($submenu == 'informasi') active @endif">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Informasi Pelayanan</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{url('/dokumen/daftar')}}" class="nav-link @if($submenu == 'dokumen') active @endif">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Dokumen Pendukung</p>
                </a>
              </li>

			        <li class="nav-item">
                <a href="{{url('/manual/daftar')}}" class="nav-link @if($submenu == 'manual') active @endif">
                  <i class="far fa-circle nav-icon"></i>
                  <p>User Manual</p>
                </a>
              </li>

            </ul>
          </li>
		      <!-- END OF MENU DASHBOARD -->


          <!-- MENU IZIN MASUK -->
		      <li class="nav-item @if($menu == 'Izin Masuk') menu-open @endif">
            <a href="#" class="nav-link @if($menu == 'Izin Masuk') active @endif">
              <i class="nav-icon fas fa-search-plus"></i>
              <p>Tanda Izin Masuk<i class="right fas fa-angle-left"></i></p>
            </a>

            <ul class="nav nav-treeview">

              <li class="nav-item ">
                <a href="{{url('/permohonan/daftar')}}" class="nav-link @if($submenu == 'permohonan') active @endif">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Permohonan</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{url('/revisi/daftar')}}" class="nav-link @if($submenu == 'revisi') active @endif">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Revisi Permohonan</p>
                </a>
              </li>

			        <li class="nav-item">
                <a href="{{url('/jadwal/daftar')}}" class="nav-link @if($submenu == 'jadwal') active @endif">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Jadwal Pengecekan</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{url('/proses/daftar')}}" class="nav-link @if($submenu == 'proses') active @endif">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Sedang Proses</p>
                </a>
              </li>

			       <li class="nav-item">
                <a href="{{url('/batal/daftar')}}" class="nav-link @if($submenu == 'batal') active @endif">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Pembatalan / Ditolak</p>
                </a>
              </li>

			        <li class="nav-item">
                <a href="{{url('/bayar/daftar')}}" class="nav-link @if($submenu == 'bayar') active @endif">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
                    Pembayaran
                    <span class="badge badge-info right">6</span>
                  </p>
                </a>
              </li>

			        <li class="nav-item">
                <a href="{{url('/ambil/daftar')}}" class="nav-link @if($submenu == 'ambil') active @endif">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Pengambilan</p>
                </a>
              </li>

			        <li class="nav-item">
                <a href="{{url('/cabut/daftar')}}" class="nav-link @if($submenu == 'cabut') active @endif">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Pencabutan</p>
                </a>
              </li>

            </ul>
          </li>
          <!-- END OF MENU UJI LAIK -->

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>