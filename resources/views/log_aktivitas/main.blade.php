<!DOCTYPE html>
<html lang="en">
<head>
  @include('layout.head') <!-- head umum -->
  @yield('head') <!-- head khusus halaman -->

  <!-- ChartJS (jika dibutuhkan) -->
  <script src="{{ asset('/plugins/chart.js/Chart.min.js') }}"></script>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  @include('layout.navbar')

  <!-- Sidebar -->
  @include('layout.sidebar')

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    @include('layout.breadcrumb') <!-- Breadcrumb -->
    @yield('content') <!-- Konten utama -->
  </div>

  <!-- Footer -->
  @include('layout.footer')

</div>

<!-- Script umum -->
@include('layout.tail')

<!-- Script tambahan halaman -->
@yield('tail')
@stack('scripts')

</body>
</html>
