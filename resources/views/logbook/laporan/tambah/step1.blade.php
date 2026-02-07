@extends('logbook.main')

@section('head')
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<section class="content">
  <div class="container-fluid">

    <!-- Stepper -->
    <div class="row mb-2">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body py-2">
            <ul class="step d-flex flex-nowrap">
              <li class="step-item active"><a href="#">Pilih Layanan</a></li>
              <li class="step-item"><a href="#">Input Gangguan</a></li>
              <li class="step-item"><a href="#">Tindaklanjut</a></li>
              <li class="step-item"><a href="#">Review</a></li>
            </ul>
          </div> <!-- card-body -->
        </div> <!-- card -->
      </div> <!-- col-lg-12 -->
    </div> <!-- row -->

    <!-- Filter Form (langsung tampil di halaman) -->
    <div class="row mb-3">
      <div class="col-lg-12">

        <div class="card">

          <form id="form-filter-layanan">
            @csrf

            <div class="card-body row">

              <div class="form-group col-md-3">
                <label>Fasilitas</label>
                <select name="fasilitas" 
                        class="form-control">
                  <option value="">- ALL -</option>
        @foreach($fasilitas as $item)
                  <option value="{{ $item->id }}" {{ old('fasilitas') == $item->id ? 'selected' : '' }}>
                    {{ strtoupper($item->kode) }} - {{ strtoupper($item->nama) }}
                  </option>
        @endforeach
                </select>
              </div>

              <div class="form-group col-md-3">
                <label>Lokasi Tingkat 1</label>
                <select class="form-control" 
                        name="lokasiTk1"
                        id="formLokasiTk1">
                  <option value="">- ALL -</option>
        @foreach($lokasiTk1 as $item)
                  <option value="{{ $item->id }}" {{ old('lokasiTk1') == $item->id ? 'selected' : '' }}>
                    {{ strtoupper($item->kode) }} - {{ strtoupper($item->nama) }}
                  </option>
        @endforeach
                </select>
              </div>

              <div class="form-group col-md-3">
                <label>Lokasi Tingkat 2</label>
                <select id="formLokasiTk2" 
                        name="lokasiTk2" 
                        class="form-control">
                        <option value="">- ALL -</option>
                </select>
              </div>

              <div class="form-group col-md-3">
                <label>Lokasi Tingkat 3</label>
                <select id="formLokasiTk3" 
                        name="lokasiTk3"  
                        class="form-control">
                        <option value="">- ALL -</option>
                </select>
              </div>

            </div> <!-- card body -->

            <div class="card-footer">
              <button type="submit"
                      class="btn btn-primary btn-sm float-right">
                      <i class="fas fa-filter"></i> Filter
              </button>
            </div> <!-- card footer -->

          </form> <!-- form -->

        </div> <!-- card -->
      </div> <!-- col-lg-12 -->
    </div> <!-- row -->

    <!-- Tabel Layanan (kosong awalnya) -->
    <div class="row">

      <div class="col-lg-12">

        <div class="card">

          <div class="card-header">
            <h3 class="card-title">DAFTAR LAYANAN</h3>
          </div> <!-- card-header -->

          <div class="card-body" id="tabel-daftar-layanan">
            <table id="layananTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th><center>NO</center></th>
                  <th><center>KODE</center></th>
                  <th><center>NAMA LAYANAN</center></th>
                  <th><center>FASILITAS</center></th>
                  <th><center>LOKASI TK 1</center></th>
                  <th><center>LOKASI TK 2</center></th>
                  <th><center>LOKASI TK 3</center></th>
                  <th><center></center></th>
                </tr>
              </thead>
              <tbody>
                {{-- Kosong dulu --}}
              </tbody>
            </table>
          </div> <!-- card-body -->

          <div class="card-footer">
            <a href="{{ route('logbook.laporan.daftar') }}" class="btn btn-default">Batal</a>
          </div> <!-- card-footer -->

        </div> <!-- card -->
        
      </div> <!-- col-lg-12 -->
    </div> <!-- row -->

  </div> <!-- container-fluid -->

</section> <!-- content -->

<!-- isi modal tombol detail -->
<div class="modal fade" id="modal_detail">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" id="detail">
      
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
@endsection

@section('tail')

<!-- javascript untuk pop up notifikasi -->
<script type="text/javascript">
  @if (session()->has('notif'))
    @if (session()->get('notif') == 'layanan_null')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Error!',
          body: 'Layanan tidak ada.',
          autohide: true,
          delay: 3000
        })
    @elseif(session()->get('notif') == 'layanan_open')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Error!',
          body: 'Terdapat laporan aktif pada layanan tersebut.',
          autohide: true,
          delay: 3000
        })
    @endif
  @endif
</script>

<script>
  $(document).ready(function () {
    let table = $('#layananTable').DataTable();

    $('#form-filter-layanan').on('submit', function (e) {
      e.preventDefault();

      $.post('{{ route('logbook.laporan.tambah.step1.filter') }}',
        $(this).serialize(),
        function (data) {
          $('#tabel-daftar-layanan').html(data);

          // Re-init datatable setelah replace
          $('#layananTable').DataTable();
        }
      ).fail(function (xhr) {
        alert('Gagal mengambil data: ' + xhr.responseText);
      });
    });
  });
</script>

<!-- javascript untuk menampilkan modal detail -->
<script type="text/javascript">
  function detail(id){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    //Ajax Load data from ajax
    
    $.ajax({
        url : "{{ route('fasilitas.layanan.detail') }}",
        type: "POST",
        data : {id: id},
        success: function(data){

         // alert(data.daftarPeralatan);

          $('#detail').empty();

          var row = '<div class="modal-header">';
              row += '<h4 class="modal-title">Detail Layanan</h4>';
              row += '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
              row += '<span aria-hidden="true">&times;</span></button></div>';// modal header

              row += '<div class="modal-body">';
              row += "<table border='0' cellpadding='5px'>";            
              row += "<tr><th>Fasilitas</th><td>:</td><td>"+ data.layanan.fasilitas.kode.toUpperCase() +" - "+ data.layanan.fasilitas.nama.toUpperCase(); +"</td></tr>";
              row += "<tr><th>Kode</th><td>:</td><td>"+ data.layanan.kode.toUpperCase(); +"</td></tr>";  
              row += "<tr><th>Nama</th><td>:</td><td>"+ data.layanan.nama.toUpperCase(); +"</td></tr>";
              row += "<tr><th>Lokasi Tingkat I</th><td>:</td><td>"+ data.layanan.lokasi_tk1.kode.toUpperCase() +" - "+ data.layanan.lokasi_tk1.nama.toUpperCase(); +"</td></tr>";
              row += "<tr><th>Lokasi Tingkat II</th><td>:</td><td>"+ data.layanan.lokasi_tk2.kode.toUpperCase() +" - "+ data.layanan.lokasi_tk2.nama.toUpperCase(); +"</td></tr>";
              row += "<tr><th>Lokasi Tingkat III</th><td>:</td><td>"+ data.layanan.lokasi_tk3.kode.toUpperCase() +" - "+ data.layanan.lokasi_tk3.nama.toUpperCase(); +"</td></tr>";
                
          if(data.layanan.kondisi == 1){
              row += "<tr><th>Kondisi</th><td>:</td><td><span class='badge bg-success'>SERVICEABLE</span></td></tr>";
          }else{
              row += "<tr><th>Kondisi</th><td>:</td><td><span class='badge bg-danger'>UNSERVICEABLE</span></td></tr>";
          }

          if(data.layanan.status == 1){
              row += "<tr><th>Status</th><td>:</td><td><span class='badge bg-success'>AKTIF</span></td></tr>";
          }else{
              row += "<tr><th>Status</th><td>:</td><td><span class='badge bg-danger'>TIDAK AKTIF</span></td></td></tr>";
          }

         if(data.layanan.created_by != null){
              row += "<tr><th>Dibuat Oleh</th><td>:</td><td>"+ data.layanan.get_created_name.name.toUpperCase(); +"</td></tr>";
              row += "<tr><th>Dibuat Pada</th><td>:</td><td>"+ data.layanan.created_at +"</td></tr>";
          }else{
              row += "<tr><th>Dibuat Oleh</th><td>:</td><td></td></tr>";
              row += "<tr><th>Dibuat Pada</th><td>:</td><td></td></tr>";
          } 

          if(data.layanan.updated_by != null){
              row += "<tr><th>Update Terakhir Oleh</th><td>:</td><td>"+ data.layanan.get_updated_name.name.toUpperCase(); +"</td></tr>";
              row += "<tr><th>Update Terakhir Pada</th><td>:</td><td>"+ data.layanan.updated_at +"</td></tr>";
          }else{
              row += "<tr><th>Update Terakhir Oleh</th><td>:</td><td></td></tr>";
              row += "<tr><th>Update Terakhir Pada</th><td>:</td><td></td></tr>";
          }      

              row += '</table>';

              row += "<hr>";
              row += "<h5>Daftar Peralatan</h5>";
              row += "<table class='table table-sm table-bordered'>";

              row += "<thead>";
              row += "<tr>";
              row += "<th><center>No</center></th>";
              row += "<th><center>Kode</center></th>";
              row += "<th><center>Nama</center></th>";
              row += "<th><center>Merk</center></th>";
              row += "<th><center>Jenis Alat</center></th>";
              row += "<th><center>IP Address</center></th>";
              row += "<th><center>Kondisi</center></th>";
              row += "</tr>";
              row += "</thead>";

              row += "<tbody>";

          if (data.daftarPeralatan.length > 0) {
            $.each(data.daftarPeralatan, function (i, satu) {

              row += "<tr>";
              row += "<td><center>"+ (i+1) +"</center></td>";
              row += "<td><center>"+ satu.peralatan.kode.toUpperCase() +"</center></td>";
              row += "<td><center>"+ satu.peralatan.nama.toUpperCase() +"</center></td>";
              row += "<td><center>"+ satu.peralatan.merk.toUpperCase() +"</center></td>";
              row += "<td><center>"+ satu.peralatan.jenis.nama.toUpperCase() +"</center></td>";
              row += "<td><center>"+ (satu.ip_address ?? '-') +"</center></td>";

            if (satu.kondisi == 1) {
              row += "<td><center><span class='badge bg-success'>BEROPERASI</span></center></td>";
            } else if (satu.kondisi == 0) {
              row += "<td><center><span class='badge bg-danger'>GANGGUAN</span></center></td>";
            } else {
              row += "<td></td>";
            }

              row += "</tr>";
            });

          } else {
              row += "<tr><td colspan='7' class='text-center'>Tidak ada peralatan</td></tr>";
          }
              row += "</tbody>";

              row += "</table>";


              row += '</div>'; // modal body
              row += '<div class="modal-footer justify-content-between">';
              row += '<button type="button" class="btn btn-default" data-dismiss="modal">Kembali</button>';
              //row += '<button type="button" class="btn btn-danger">Hapus</button>';
              row += '</div>';

            $("#detail").append(row);
            $("#modal_detail").modal('show'); // show bootstrap modal when complete loaded
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Gagal menampilkan detail layanan');
        }
    });
}
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
      formLokasiTk2.innerHTML = '<option value="">- ALL -</option>';
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
          formLokasiTk2.innerHTML = '<option value="">- ALL -</option>';
          data.forEach(item => {
            formLokasiTk2.innerHTML += `<option value="${item.id}">${item.kode.toUpperCase()} - ${item.nama.toUpperCase()}</option>`;
          });
          formLokasiTk3.innerHTML = '<option value="">- ALL -</option>';
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
      formLokasiTk3.innerHTML = '<option value="">- ALL -</option>';
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
          formLokasiTk3.innerHTML = '<option value="">- ALL -</option>';
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