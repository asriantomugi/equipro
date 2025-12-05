  
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
                <h3 class="card-title">FORM TAMBAH LOKASI TINGKAT III</h3>
              </div>
              <!-- /.card-header -->

<!-- form start -->
<form class="form-horizontal needs-validation" 
      action="{{ route('master_data.lokasi_tk_3.tambah') }}"
      method="post" 
      novalidate>
@csrf

              <div class="card-body">
 
              <div class="form-group row">
                <label class="col-sm-3 col-form-label required">Lokasi Tingkat I</label>
                <div class="col-sm-9">
                <select class="form-control" 
                          name="lokasi_tk_1"
                          id="formLokasiTk1"
                          required>
                    <option value="">- Pilih -</option>
  @foreach($lokasi_tk_1 as $satu)
                    <option value="{{ $satu->id }}">{{ $satu->kode }} - {{ $satu->nama }}</option>
  @endforeach
                  </select>
                  <div class="invalid-feedback">Lokasi Tingkat I wajib dipilih.</div>
                </div>
              </div>

              <div class="form-group row mb-3">
                <label class="col-sm-3 col-form-label required">Lokasi Tingkat II</label>
                <div class="col-sm-9">
                  <select id="formLokasiTk2" 
                          name="lokasi_tk_2" 
                          class="form-control" 
                          required>
                          <option value="">- Pilih -</option>
                  </select>
                  <div class="invalid-feedback">Lokasi Tingkat II wajib dipilih.</div>
                </div>
              </div>

              <div class="form-group row">
                  <label class="col-sm-3 col-form-label required">Kode</label>
                  <div class="col-sm-9">
                    <input type="text" 
                           name="kode" 
                           class="form-control"
                           value="{{ old('kode') }}"
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
                           value="{{ old('nama') }}"
                           required>
                    <div class="invalid-feedback">Nama wajib diisi</div>
                  </div>
                </div>

              </div>
              <!-- /.card-body -->

              <div class="card-footer">
                <a class="btn btn-default btn-sm" 
                   href="{{ route('master_data.lokasi_tk_3.daftar') }}" 
                   role="button">Batal</a>
                <button type="submit" class="btn btn-primary btn-sm float-right">Simpan</button>
              </div>
              
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

    <!-- Javascript untuk menampilkan pilihan dropdown lokasi tingkat II berdasarkan lokasi tingkat I yang dipilih -->
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const formLokasiTk1 = document.getElementById('formLokasiTk1');
        const formLokasiTk2 = document.getElementById('formLokasiTk2');

        // proses ketika terjadi perubahan value di dropdown lokasi tingkat I
        formLokasiTk1.addEventListener('change', function () {
          // ambil id dari lokasi tingkat I
          const lokasi_tk_1_id = formLokasiTk1.value;
          formLokasiTk2.innerHTML = '<option value="">- Pilih -</option>';
          // jika id lokasi tingkat I ada
          if (lokasi_tk_1_id) {
            // Mengirim request POST untuk mendapatkan kota berdasarkan lokasi tingkat I yang dipilih
            fetch('/json/lokasi-tk-2/daftar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json', // Mengirim data dalam format JSON
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', // Menyertakan token CSRF untuk keamanan
                },
                body: JSON.stringify({ lokasi_tk_1_id: lokasi_tk_1_id }) // Mengirimkan ID lokasi tingkat I
            })
            .then(response => response.json()) // Mengonversi response menjadi JSON
            .then(data => {
              // Jika data diterima, update dropdown lokasi tingkat II
              formLokasiTk2.innerHTML = '<option value="">- Pilih -</option>';
              data.forEach(item => {
                formLokasiTk2.innerHTML += `<option value="${item.id}">${item.kode.toUpperCase()} - ${item.nama.toUpperCase()}</option>`;
              });
            })
            .catch(error => {
                console.error('Gagal memuat data:', error);
            });
          }
        });
      });
    </script>

@endsection