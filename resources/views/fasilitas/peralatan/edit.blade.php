  
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
      action="{{ route('fasilitas.peralatan.edit') }}"
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
                      <option value="{{ $satu->id }}" {{ old('jenis', $peralatan->jenis_id ?? '') == $satu->id ? 'selected' : '' }}>
                        {{ strtoupper($satu->kode) }} - {{ strtoupper($satu->nama) }}
                      </option>
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
                           value="{{ strtoupper(old('kode', $peralatan->kode ?? '')) }}"
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
                           value="{{ strtoupper(old('nama', $peralatan->nama ?? '')) }}"
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
                           value="{{ strtoupper(old('merk', $peralatan->merk ?? '')) }}">
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-4 col-form-label ">Tipe</label>
                  <div class="col-sm-8">
                    <input type="text" 
                           name="tipe" 
                           class="form-control"
                           value="{{ strtoupper(old('tipe', $peralatan->tipe ?? '')) }}">
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Model</label>
                  <div class="col-sm-8">
                    <input type="text" 
                           name="model" 
                           class="form-control"
                           value="{{ strtoupper(old('model', $peralatan->model ?? '')) }}">
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Serial Number</label>
                  <div class="col-sm-8">
                    <input type="text" 
                           name="serial_number" 
                           class="form-control"
                           value="{{ strtoupper(old('serial_number', $peralatan->serial_number ?? '')) }}">
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
                          $end = date('Y');
                      @endphp

                      @for ($i = $end; $i >= $start; $i--)
                        <option value="{{ $i }}" {{ old('thn_produksi', $peralatan->thn_produksi ?? '') == $i ? 'selected' : '' }}>
                          {{ $i }}
                        </option>
                      @endfor
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
                          $end = date('Y');
                      @endphp

                      @for ($i = $end; $i >= $start; $i--)
                        <option value="{{ $i }}" {{ old('thn_pengadaan', $peralatan->thn_pengadaan ?? '') == $i ? 'selected' : '' }}>
                          {{ $i }}
                        </option>
                      @endfor
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
                      <option value="1" {{ old('sewa', $peralatan->sewa ?? '') == '1' ? 'selected' : '' }}>SEWA</option>
                      <option value="0" {{ old('sewa', $peralatan->sewa ?? '') == '0' ? 'selected' : '' }}>ASET</option>
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
                      <option value="{{ $satu->id }}" {{ old('perusahaan', $peralatan->perusahaan_id ?? '') == $satu->id ? 'selected' : '' }}>
                        {{ $satu->nama }}
                      </option>
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
                      <option value="1" {{ old('kondisi', $peralatan->kondisi ?? '') == '1' ? 'selected' : '' }}>NORMAL</option>
                      <option value="0" {{ old('kondisi', $peralatan->kondisi ?? '') == '0' ? 'selected' : '' }}>RUSAK</option>
                    </select>
                    <div class="invalid-feedback">Kondisi wajib dipilih.</div>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Keterangan</label>
                  <div class="col-sm-8">
                    <textarea name="keterangan" 
                              class="form-control"
                              rows="2">{{ strtoupper(old('keterangan', $peralatan->keterangan ?? '')) }}</textarea>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-4 col-form-label required">Status</label>
                  <div class="col-sm-8">
                    <select name="status" 
                            class="form-control" 
                            required>
                      <option value="">- Pilih -</option>
                      <option value="1" {{ old('status', $peralatan->status ?? '') == '1' ? 'selected' : '' }}>AKTIF</option>
                      <option value="0" {{ old('status', $peralatan->status ?? '') == '0' ? 'selected' : '' }}>TIDAK AKTIF</option>
                    </select>
                    <div class="invalid-feedback">Status wajib dipilih.</div>
                  </div>
                </div>

              </div>
              <!-- /.card-body -->

              <div class="card-footer">
                <a class="btn btn-default btn-sm" 
                   href="{{ route('fasilitas.peralatan.daftar') }}" 
                   role="button">Batal</a>
                <button type="submit" 
                        class="btn btn-primary btn-sm float-right">
                        Simpan
                </button>
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