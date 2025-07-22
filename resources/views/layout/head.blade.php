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

    

  /* Custom styling untuk validasi */
        .form-control.is-invalid {
            border-color: #dc3545;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
            padding-right: calc(1.5em + 0.75rem);
        }
        
        .form-control.is-valid {
            border-color: #28a745;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='m2.3 6.73.94-.94 1.4 1.4-2.34 2.34L.84 7.05l.94-.94z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
            padding-right: calc(1.5em + 0.75rem);
        }
        
        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875rem;
            color: #dc3545;
        }
        
        .valid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875rem;
            color: #28a745;
        }

        .error-icon {
            color: #dc3545;
            margin-left: 5px;
        }

        .success-icon {
            color: #28a745;
            margin-left: 5px;
        }

        .table td .btn + .btn {
          margin-left: 0.5rem;
        }

    
  </style>

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

