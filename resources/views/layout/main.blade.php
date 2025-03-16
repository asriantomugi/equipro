<!DOCTYPE html>
<html lang="en">
<head>
  <!-- head -->
  @include('layout.head')

  <!-- spesific head -->
  @yield('head')

</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <!--
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
  </div>
  -->

  <!-- navbar -->
  @include('layout.navbar')

  <!-- menu side bar -->
  @include('layout.sidebar')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    
    <!-- breadcrumb -->
     @include('layout.breadcrumb')

    <!-- main content -->
    <!-- menampilkan konten dari masing-masing halaman -->
    @yield('content')

  </div>
  <!-- /.content-wrapper -->

  <!-- footer -->
  @include('layout.footer')

</div>
<!-- ./wrapper -->

<!-- tail -->
@include('layout.tail')

<!-- spesific tail -->
@yield('tail')

</body>
</html>
