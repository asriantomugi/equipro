  
@extends('fasilitas.main')


@section('content')
  <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->

<!-- pesan error validasi -->
@if($errors->any())
        <div class="row">
          <div class="col-lg-7">
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
          <div class="col-lg-7 col-6">
            
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">FORM EDIT DATA PERALATAN</h3>
              </div>
              <!-- /.card-header -->

<!-- form start -->
<form class="form-horizontal needs-validation" 
      action="{{url('/fasilitas/peralatan/edit')}}"
      method="post" 
      novalidate>
@csrf

              <div class="card-body">

                <input type="text" name="id" value="{{ $peralatan->id }}" hidden>

                <div class="form-group row">
                  <label class="col-sm-4 col-form-label required">Jenis Alat</label>
                  <div class="col-sm-8">
                    <select name="jenis" 
                            class="form-control" 
                            required>
                      <option value="">- Pilih -</option>
@foreach ($jenis_alat as $satu)
  @if($satu->id == $peralatan->jenis_id)
                      <option value="{{ $satu->id }}" selected>{{ $satu->kode }} - {{ $satu->nama }}</option>
  @else
                      <option value="{{ $satu->id }}">{{ $satu->kode }} - {{ $satu->nama }}</option>
  @endif
@endforeach
                    </select>
                    <div class="invalid-feedback">Jenis Alat wajib dipilih.</div>
                  </div>
                </div>
 
                <div class="form-group row">
                  <label class="col-sm-4 col-form-label required">Kode</label>
                  <div class="col-sm-8">
                    <input type="text" 
                           name="kode" 
                           class="form-control"
                           value="{{ strtoupper($peralatan->kode) }}"
                           required>
                    <div class="invalid-feedback">Kode wajib diisi</div>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-4 col-form-label required">Nama</label>
                  <div class="col-sm-8">
                    <input type="text" 
                           name="nama" 
                           class="form-control"
                           value="{{ strtoupper($peralatan->nama) }}"
                           required>
                    <div class="invalid-feedback">Nama wajib diisi</div>
                  </div>
                </div>
                
                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Merk</label>
                  <div class="col-sm-8">
                    <input type="text" 
                           name="merk" 
                           class="form-control"
                           value="{{ strtoupper($peralatan->merk) }}">
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-4 col-form-label ">Tipe</label>
                  <div class="col-sm-8">
                    <input type="text" 
                           name="tipe" 
                           class="form-control"
                           value="{{ strtoupper($peralatan->tipe) }}">
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Model</label>
                  <div class="col-sm-8">
                    <input type="text" 
                           name="model" 
                           class="form-control"
                           value="{{ strtoupper($peralatan->model) }}">
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Serial Number</label>
                  <div class="col-sm-8">
                    <input type="text" 
                           name="serial_number" 
                           class="form-control"
                           value="{{ strtoupper($peralatan->serial_number) }}">
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Tahun Produksi</label>
                  <div class="col-sm-8">
                    <select name="thn_produksi" 
                            class="form-control">
                      <option value="">- Pilih -</option>
@php
  $start = 1980;
  $end = date("Y"); // tahun sekarang
  for ($i = $end; $i >= $start; $i--) {
    if($i == $peralatan->thn_produksi){
      echo "<option value=\"$i\" selected>$i</option>";
    }else{
      echo "<option value=\"$i\">$i</option>";
    }
      
  }
@endphp
                    </select>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Tahun Pengadaan</label>
                  <div class="col-sm-8">
                    <select name="thn_pengadaan" 
                            class="form-control">
                      <option value="">- Pilih -</option>
@php
  $start = 1980;
  $end = date("Y"); // tahun sekarang
  for ($i = $end; $i >= $start; $i--) {
    if($i == $peralatan->thn_pengadaan){
      echo "<option value=\"$i\" selected>$i</option>";
    }else{
      echo "<option value=\"$i\">$i</option>";
    }
  }
@endphp
                    </select>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-4 col-form-label required">Status Kepemilikan</label>
                  <div class="col-sm-8">
                    <select name="sewa" 
                            class="form-control" 
                            required>
                      <option value="">- Pilih -</option>
@if($peralatan->sewa == 1)
                      <option value="1" selected>SEWA</option>
                      <option value="0">ASET</option>
@else
                      <option value="1">SEWA</option>
                      <option value="0" selected>ASET</option>
@endif
                    </select>
                    <div class="invalid-feedback">Status Kepemilikan wajib dipilih.</div>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-4 col-form-label required">Perusahaan Pemilik</label>
                  <div class="col-sm-8">
                    <select name="perusahaan" 
                            class="form-control" 
                            required>
                      <option value="">- Pilih -</option>
@foreach ($perusahaan as $satu)
  @if($satu->id == $peralatan->perusahaan_id)
                      <option value="{{ $satu->id }}" selected>{{ $satu->nama }}</option>
  @else
                      <option value="{{ $satu->id }}">{{ $satu->nama }}</option>
  @endif
@endforeach
                    </select>
                    <div class="invalid-feedback">Perusahaan Pemilik wajib dipilih.</div>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-4 col-form-label required">Kondisi</label>
                  <div class="col-sm-8">
                    <select name="kondisi" 
                            class="form-control" 
                            required>
                      <option value="">- Pilih -</option>
@if($peralatan->kondisi == 1)
                      <option value="1" selected>NORMAL</option>
                      <option value="0">RUSAK</option>
@else
                      <option value="1">NORMAL</option>
                      <option value="0" selected>RUSAK</option>
@endif
                    </select>
                    <div class="invalid-feedback">Kondisi wajib dipilih.</div>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Keterangan</label>
                  <div class="col-sm-8">
                    <textarea name="keterangan" 
                              class="form-control"
                              rows="2">{{ strtoupper($peralatan->keterangan) }}</textarea>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-4 col-form-label required">Status</label>
                  <div class="col-sm-8">
                    <select name="status" 
                            class="form-control" 
                            required>
                      <option value="">- Pilih -</option>
@if($peralatan->status == 1)
                      <option value="1" selected>AKTIF</option>
                      <option value="0">TIDAK AKTIF</option>
@else
                      <option value="1">AKTIF</option>
                      <option value="0" selected>TIDAK AKTIF</option>
@endif
                    </select>
                    <div class="invalid-feedback">Status wajib dipilih.</div>
                  </div>
                </div>

              </div>
              <!-- /.card-body -->

              <div class="card-footer">
                <a class="btn btn-default btn-sm" 
                   href="{{url('/fasilitas/peralatan/daftar')}}" 
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