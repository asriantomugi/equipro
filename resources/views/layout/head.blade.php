  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>EQUIPRO</title>

  <!--Pengaturan Font Pada Seluruh Tabel-->
  <style>
    .table-condensed{
      font-size: 14px;
    }
  </style>

  <!--Pengaturan Untuk Step Navigation-->
  <style>
    .step {
      list-style: none;
      margin: .2rem 0;
      width: 100%;
    }

    .step .step-item {
      -ms-flex: 1 1 0;
      flex: 1 1 0;
      margin-top: 0;
      min-height: 1rem;
      position: relative; 
      text-align: center;
    }

    .step .step-item:not(:first-child)::before {
      background: #0069d9;
      content: "";
      height: 2px;
      left: -50%;
      position: absolute;
      top: 9px;
      width: 100%;
    }

    .step .step-item a {
      color: #acb3c2;
      display: inline-block;
      padding: 20px 10px 0;
      text-decoration: none;
    }

    .step .step-item a::before {
      background: #0069d9;
      border: .1rem solid #fff;
      border-radius: 50%;
      content: "";
      display: block;
      height: .9rem;
      left: 50%;
      position: absolute;
      top: .2rem;
      transform: translateX(-50%);
      width: .9rem;
      z-index: 1;
    }

    .step .step-item.active a::before {
      background: #fff;
      border: .1rem solid #0069d9;
    }

    .step .step-item.active ~ .step-item::before {
      background: #e7e9ed;
    }

    .step .step-item.active ~ .step-item a::before {
      background: #e7e9ed;
    }

    .step-item a.active {
        color: #0d6efd; 
    }
  </style>

  <!-- DataTables JS -->
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('/plugins/fontawesome-free/css/all.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="{{asset('/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
  <!-- Select2 -->
  <link rel="stylesheet" href="{{asset('/plugins/select2/css/select2.min.css')}}">
  <link rel="stylesheet" href="{{asset('/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{asset('/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- JQVMap -->
  <link rel="stylesheet" href="{{asset('/plugins/jqvmap/jqvmap.min.css')}}">
    <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('/dist/css/adminlte.min.css')}}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{asset('/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{asset('/plugins/daterangepicker/daterangepicker.css')}}">
  <!-- summernote -->
  <link rel="stylesheet" href="{{asset('/plugins/summernote/summernote-bs4.min.css')}}">
  <!-- Chart -->
  <link rel="stylesheet" href="{{asset('/plugins/chart.js/Chart.min.css')}}">
  <!-- DataTables -->
  <link rel="stylesheet" href="{{asset('/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{asset('/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{asset('/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
  <!-- Asterik Sign -->
  <link rel="stylesheet" href="{{asset('/dist/css/asterik.css')}}">
  <!-- Step Bar -->
  <link rel="stylesheet" href="{{asset('/dist/css/stepbar.css')}}">

