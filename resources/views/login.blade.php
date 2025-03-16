<!DOCTYPE html>
<html lang="en">
<head>
  <style>
  
  .hold-transition {
      align-items: center;
      justify-content: center;
      
  }

  .background-container {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      z-index: -1;
      overflow: hidden;
  }

  .background-container img {
      width: 100%;
      height: 100%;
      object-fit: cover;
  }

  .login-page .brand-link {
    padding: 0px;
  }

  .login-box {
      width: 420px;
      /* height: 490px; */
      height: 580px;
      background-color: rgba(255, 255, 255, 0.8);
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  }

  .login-form {
      text-align: center;
      font-size: 20px;
  }

  .login-form a {
      color: #333;
      text-decoration: none;
  }

  .card {
      margin-bottom: 0;
  }

  .login-card-body {
      padding: 30px;
  }

  </style>
  <!-- head -->
  @include('layout.head')
</head>

<div class="hold-transition login-box">
  <a href="{{asset('/')}}" class="background-container">
    <img src="{{asset('dist/img/GSE.jpg')}}" alt="background-container">
  </a>

  <body class="login-page">
  <div class="login-form">
    <a href="{{asset('/')}}" class="brand-link">
      <img src="{{asset('dist/img/injourney-logo.png')}}" alt="Gambar Logo" class="gambar-logo" 
      style="width:290px; height:120px;">
    </a>
    <br>
    <h5>EQUIPRO<br><small>Peralatan Electronics & Technology Services</small></h5>
  </div>
  <br>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Silahkan log in untuk memulai</p>

      <!-- pesan error, jika login gagal -->
      @if(session()->has('loginError'))
      <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {{session('loginError')}}
      </div>
      @endif

      <!-- form login -->
      <form action="{{url('/login/process')}}" method="post">
        {{ csrf_field() }}
        <div class="input-group mb-3">
          <input type="email" 
                 class="form-control" 
                 placeholder="Email" 
                 name="email"
                 required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>

        <div class="input-group mb-3">
          <input type="password" 
                 class="form-control" 
                 placeholder="Password"
                 name="password"
                 required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>

		    <div class="input-group mb-3">
          <button type="submit" class="btn btn-primary btn-block">Log In</button>
        </div>
      </form>
	  
      <p class="mb-1">
        <a href="forgot-password.html">Lupa password?</a>
      </p>
      
    </div>
	
    <!-- /.login-card-body -->

</div>

</div>
<!-- /.login-box -->

<br>
<!--
<p class="mb-0">
	Belum punya akun? <a href="register.html" class="text-center">Daftar disini</a>
</p>
-->

<!-- tail -->
@include('layout.tail')

<!-- javascript untuk pop up notifikasi -->
<script type="text/javascript">
  @if (session()->has('notif'))
    @if (session()->get('notif') == 'tidak_sesuai')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Login Gagal!',
          body: 'Email/Password tidak sesuai.'
        })
    @elseif(session()->get('notif') == 'tidak_aktif')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Login Gagal!',
          body: 'User tidak aktif.'
        })
    @endif
  @endif
</script>

</body>
</html>
