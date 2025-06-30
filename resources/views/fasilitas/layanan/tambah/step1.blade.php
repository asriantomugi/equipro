  
@extends('fasilitas.main')


@section('content')
  <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->

        <div class="row">
          <div class="col-lg-12">
             <div class="card">
              <div class="card-body">
                <br>
                <div class="stepper-wrapper">
                  <div class="stepper-item active">
                    <div class="step-counter">1</div>
                    <div class="step-name">Data Layanan</div>
                  </div>
                  <div class="stepper-item">
                    <div class="step-counter">2</div>
                    <div class="step-name">Daftar Peralatan</div>
                  </div>
                  <div class="stepper-item">
                    <div class="step-counter">3</div>
                    <div class="step-name">Review</div>
                  </div>
                </div>
                <!-- stepper-wrapper -->
              </div>
              <!-- card-body -->
            </div>
            <!-- card -->
          </div>
          <!-- col-lg-12 -->
        </div>  
        <!-- row -->

        <div class="row">
          <div class="col-lg-12 col-6">
            
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">FORM DATA LAYANAN</h3>
              </div>
              <!-- /.card-header -->

<!-- form start -->
<form class="form-horizontal needs-validation" 
      action="{{url('/fasilitas/layanan/tambah/step1')}}"
      method="post" 
      novalidate>
@csrf

              <div class="card-body">

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label required">Fasilitas</label>
                  <div class="col-sm-9">
                    <select name="fasilitas" 
                            class="form-control" 
                            required>
                      <option value="">- Pilih -</option>
@foreach ($fasilitas as $satu)
                      <option value="{{ $satu->id }}">{{ $satu->kode }} - {{ $satu->nama }}</option>
@endforeach
                    </select>
                    <div class="invalid-feedback">Fasilitas wajib dipilih.</div>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label required">Kode</label>
                  <div class="col-sm-9">
                    <input type="text" 
                           name="kode" 
                           class="form-control"
                           value=""
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
                           value=""
                           required>
                    <div class="invalid-feedback">Nama wajib diisi</div>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label required">Lokasi Tingkat I</label>
                  <div class="col-sm-9">
                  <select class="form-control" 
                            name="lokasi_tk_1"
                            id="formLokasiTk1"
                            required>
                      <option value="">- Pilih -</option>
    @foreach($lokasi_tk_1 as $satu)
                      <option value="{{$satu->id}}">{{strtoupper($satu->nama)}}</option>
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

                <div class="form-group row mb-3">
                  <label class="col-sm-3 col-form-label required">Lokasi Tingkat III</label>
                  <div class="col-sm-9">
                    <select id="formLokasiTk3" 
                            name="lokasi_tk_3" 
                            class="form-control" 
                            required>
                            <option value="">- Pilih -</option>
                    </select>
                    <div class="invalid-feedback">Lokasi Tingkat III wajib dipilih.</div>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label required">Kondisi</label>
                  <div class="col-sm-9">
                    <select name="kondisi" 
                            class="form-control" 
                            required>
                      <option value="">- Pilih -</option>
                      <option value="1">SERVICEABLE</option>
                      <option value="0">UNSERVICEABLE</option>
                    </select>
                    <div class="invalid-feedback">Kondisi wajib dipilih.</div>
                  </div>
                </div>


              </div>
              <!-- /.card-body -->

              <div class="card-footer">
                <a class="btn btn-default btn-sm" 
                   href="{{url('/fasilitas/layanan/daftar')}}" 
                   role="button">Batal</a>
                <button type="submit" class="btn btn-success btn-sm float-right">Lanjut &nbsp;&nbsp;&nbsp;<i class="fas fa-angle-right"></i></button>
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
<!-- /. section content -->


@section('tail')

    <!-- javascript untuk pop up notifikasi -->
    <script type="text/javascript">
      @if (session()->has('notif'))
        @if (session()->get('notif') == 'simpan_sukses')
          $(document).Toasts('create', {
              class: 'bg-success',
              title: 'Sukses!',
              body: 'Data layanan telah berhasil disimpan',
              autohide: true,
              delay: 3000
            })
        @elseif(session()->get('notif') == 'kode_terdaftar')
          $(document).Toasts('create', {
              class: 'bg-danger',
              title: 'Error!',
              body: 'Kode yang dimasukkan sudah terdaftar',
              autohide: true,
              delay: 3000
            })
        @endif
      @endif
    </script>

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

    <!-- Javascript untuk menampilkan pilihan dropdown lokasi tingkat III berdasarkan lokasi tingkat II yang dipilih -->
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const formLokasiTk2 = document.getElementById('formLokasiTk2');
        const formLokasiTk3 = document.getElementById('formLokasiTk3');

        // proses ketika terjadi perubahan value di dropdown lokasi tingkat II
        formLokasiTk2.addEventListener('change', function () {
          // ambil id dari lokasi tingkat II
          const lokasi_tk_2_id = formLokasiTk2.value;
          formLokasiTk3.innerHTML = '<option value="">- Pilih -</option>';
          // jika id lokasi tingkat II ada
          if (lokasi_tk_2_id) {
            // Mengirim request POST untuk mendapatkan kota berdasarkan lokasi tingkat II yang dipilih
            fetch('/json/lokasi-tk-3/daftar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json', // Mengirim data dalam format JSON
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', // Menyertakan token CSRF untuk keamanan
                },
                body: JSON.stringify({ lokasi_tk_2_id: lokasi_tk_2_id }) // Mengirimkan ID lokasi tingkat II
            })
            .then(response => response.json()) // Mengonversi response menjadi JSON
            .then(data => {
              // Jika data diterima, update dropdown lokasi tingkat III
              formLokasiTk3.innerHTML = '<option value="">- Pilih -</option>';
              data.forEach(item => {
                formLokasiTk3.innerHTML += `<option value="${item.id}">${item.kode.toUpperCase()} - ${item.nama.toUpperCase()}</option>`;
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
<!-- /. section tail -->