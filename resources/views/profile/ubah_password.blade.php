@extends('profile.main')

@section('content')
  <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->

<!-- pesan sukses -->
@if(session('success'))
        <div class="row">
          <div class="col-lg-6">
            <div class="alert alert-success alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                  <h5><b>Berhasil!</b></h5>
                  {{ session('success') }}
                </div>
          </div>
        </div>
@endif

<!-- pesan error -->
@if(session('error'))
        <div class="row">
          <div class="col-lg-6">
            <div class="alert alert-danger alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                  <h5><b>Error!</b></h5>
                  {{ session('error') }}
                </div>
          </div>
        </div>
@endif

<!-- pesan error validasi -->
@if($errors->any())
        <div class="row">
          <div class="col-lg-6">
            <div class="alert alert-danger alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                  <h5><b>Kesalahan!</b></h5>
                  <ul>
  @foreach ($errors->all() as $error)
                  <li>{{$error}}</li>
  @endforeach
                  </ul>
                </div>
          </div>
        </div>
@endif

        <div class="row">
          <div class="col-lg-6 col-6">
            
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">FORM UBAH PASSWORD USER</h3>
              </div>
              <!-- /.card-header -->

<!-- form start -->
<form class="form-horizontal needs-validation" 
      action="/profil/ubah_password"
      method="post" 
      novalidate>
@csrf

              <div class="card-body">

                <input type="text" name="id" value="{{ $user->id }}" hidden>
 
                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Nama</label>
                  <div class="col-sm-8">
                    <input type="text" 
                           name="nama" 
                           class="form-control"
                           value="{{ strtoupper($user->name) }}"
                           readonly>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Email</label>
                  <div class="col-sm-8">
                    <input type="email" 
                           name="email" 
                           class="form-control"
                           value="{{ $user->email }}"
                           readonly>
                  </div>
                </div>
				
				<div class="form-group row">
                  <label class="col-sm-4 col-form-label">Peran/Unit</label>
                  <div class="col-sm-8">
                    <input type="text" 
                           name="role" 
                           class="form-control"
                           value="{{ $user->role->nama ?? 'N/A' }}"
                           readonly>
                  </div>
                </div>
				
				<div class="form-group row">
                  <label class="col-sm-4 col-form-label required">Password Baru</label>
                  <div class="col-sm-8">
                    <input type="password" 
                           name="password" 
                           class="form-control"
                           value=""
                           required>
                    <div class="invalid-feedback">Password wajib diisi</div>
                    <small class="form-text text-muted">
                      Password minimal 8 karakter
                    </small>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-4 col-form-label required">Konfirmasi Password</label>
                  <div class="col-sm-8">
                    <input type="password" 
                           name="password_confirmation" 
                           class="form-control"
                           value=""
                           required>
                    <div class="invalid-feedback">Konfirmasi Password wajib diisi</div>
                  </div>
                </div>

              </div>
              <!-- /.card-body -->

              <div class="card-footer">
                <a class="btn btn-default btn-sm" 
                   href="/module" 
                   role="button">Batal</a>
                <button type="submit" class="btn btn-primary btn-sm float-right">Simpan</button>
              </div>

</form>
<!-- form -->
              
            </div>
            <!-- /.card -->

          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->
        <!-- Main row -->
		
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

@endsection
