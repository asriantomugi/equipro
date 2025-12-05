  
@extends('master_data.main')


@section('content')
  <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->

<!-- pesan error validasi -->
@if($errors->any())
        <div class="row">
          <div class="col-lg-6">
            <div class="alert alert-danger alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                  <h5><b></b>Kesalahan !</b></h5>
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
                <h3 class="card-title">FORM TAMBAH PERUSAHAAN</h3>
              </div>
              <!-- /.card-header -->

<!-- form start -->
<form class="form-horizontal needs-validation" 
      action="{{ route('master_data.perusahaan.tambah') }}"
      method="post" 
      novalidate>
@csrf

              <div class="card-body">

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label required">Nama</label>
                  <div class="col-sm-9">
                    <input type="text" 
                           name="nama" 
                           class="form-control"
                           value="{{ old('nama') }}"
                           required>
                    <div class="invalid-feedback">Nama wajib diisi</div>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label required">Email</label>
                  <div class="col-sm-9">
                    <input type="email" 
                           name="email" 
                           class="form-control"
                           value="{{ old('email') }}"
                           required>
                    <div class="invalid-feedback">Email wajib diisi</div>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Alamat</label>
                  <div class="col-sm-9">
                    <textarea class="form-control" 
                              rows="3"
                              name="alamat" 
                              placeholder="">{{ old('alamat') }}</textarea>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Telepon</label>
                  <div class="col-sm-9">
                    <input type="text" 
                           name="telepon" 
                           class="form-control"
                           value="{{ old('telepon') }}">
                  </div>
                </div>

              </div>
              <!-- /.card-body -->

              <div class="card-footer">
                <a class="btn btn-default btn-sm" 
                   href="{{ route('master_data.perusahaan.daftar') }}" 
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