  
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
                <h3 class="card-title">FORM EDIT DATA LOKASI TINGKAT II</h3>
              </div>
              <!-- /.card-header -->

<!-- form start -->
<form class="form-horizontal needs-validation" 
      action="{{url('/master-data/lokasi-tk-1/edit')}}"
      method="post" 
      novalidate>
@csrf

              <div class="card-body">

                <input type="text" name="id" value="{{ $lokasi_tk_1->id }}" hidden>
 
                <div class="form-group row">
                  <label class="col-sm-3 col-form-label required">Kode</label>
                  <div class="col-sm-9">
                    <input type="text" 
                           name="kode" 
                           class="form-control"
                           value="{{ strtoupper($lokasi_tk_1->kode) }}"
                           required>
                    <div class="invalid-feedback">Kode wajib diisi</div>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label required">Nama</label>
                  <div class="col-sm-9">
                    <input type="text" 
                           name="nama" 
                           class="form-control"
                           value="{{ strtoupper($lokasi_tk_1->nama) }}"
                           required>
                    <div class="invalid-feedback">Nama wajib diisi</div>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label required">Status</label>
                  <div class="col-sm-9">
                    <select class="form-control" name="status" required>
                      <option value="">- Pilih -</option>
  @if($lokasi_tk_1->status == 1)
                      <option value="1" selected>AKTIF</option>
                      <option value="0">TIDAK AKTIF</option>
  @else
                      <option value="1">AKTIF</option>
                      <option value="0" selected>TIDAK AKTIF</option>
  @endif
                    </select>
                  </div>
                  <div class="invalid-feedback">Status wajib diisi</div>
                </div>

              </div>
              <!-- /.card-body -->

              <div class="card-footer">
                <a class="btn btn-default" 
                   href="{{url('/master-data/lokasi-tk-1/daftar')}}" 
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