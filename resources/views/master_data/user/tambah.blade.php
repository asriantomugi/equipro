  
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
                <h3 class="card-title">FORM TAMBAH USER</h3>
              </div>
              <!-- /.card-header -->

<!-- form start -->
<form class="form-horizontal needs-validation" 
      action="{{url('/master-data/user/tambah')}}"
      method="post" 
      novalidate>
@csrf

              <div class="card-body">
 
                <div class="form-group row">
                  <label class="col-sm-4 col-form-label required">Nama</label>
                  <div class="col-sm-8">
                    <input type="text" 
                           name="nama" 
                           class="form-control"
                           value=""
                           required>
                    <div class="invalid-feedback">Nama wajib diisi</div>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-4 col-form-label required">Email</label>
                  <div class="col-sm-8">
                    <input type="email" 
                           name="email" 
                           class="form-control"
                           value=""
                           required>
                    <div class="invalid-feedback">Email wajib diisi</div>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-4 col-form-label required">Role</label>
                  <div class="col-sm-8">
                    <select class="form-control" name="role" required>
                      <option value="">- Pilih -</option>
  @foreach($roles as $role)
                      <option value="{{ $role->id }}">{{ strtoupper($role->nama) }}</option>
  @endforeach
                    </select>
                  </div>
                  <div class="invalid-feedback">Role wajib diisi.</div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-4 col-form-label required">Perusahaan</label>
                  <div class="col-sm-8">
                    <select class="form-control" name="perusahaan" required>
                      <option value="">- Pilih -</option>
  @foreach($perusahaan as $satu)
                      <option value="{{ $satu->id }}">{{ strtoupper($satu->nama) }}</option>
  @endforeach
                    </select>
                  </div>
                  <div class="invalid-feedback">Perusahaan wajib diisi</div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Jabatan</label>
                  <div class="col-sm-8">
                    <input type="text" 
                           name="jabatan" 
                           class="form-control"
                           value="">
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Alamat</label>
                  <div class="col-sm-8">
                    <textarea class="form-control" 
                              rows="3"
                              name="alamat" 
                              placeholder=""></textarea>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Telepon/HP</label>
                  <div class="col-sm-8">
                    <input type="text" 
                           name="telepon" 
                           class="form-control"
                           value="">
                  </div>
                </div>

                <!--
                <div class="form-group row">
                  <label class="col-sm-4 col-form-label required">Status</label>
                  <div class="col-sm-8">
                    <select class="form-control" name="aktif" required>
                      <option value="">- Pilih -</option>
                      <option value="1">AKTIF</option>
                      <option value="0">TIDAK AKTIF</option>
                    </select>
                  </div>
                  <div class="invalid-feedback">Status wajib diisi.</div>
                </div>
                -->

                <div class="form-group row">
                  <label class="col-sm-4 col-form-label required">Password</label>
                  <div class="col-sm-8">
                    <input type="password" 
                           name="password" 
                           class="form-control"
                           value=""
                           required>
                    <div class="invalid-feedback">Password wajib diisi</div>
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
                <a class="btn btn-default" 
                   href="{{url('/master-data/user/daftar')}}" 
                   role="button">Batal</a>
                <button type="submit" class="btn btn-primary float-right">Simpan</button>
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