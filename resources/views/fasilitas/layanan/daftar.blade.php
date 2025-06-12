  
@extends('fasilitas.main')

@section('head')
  <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection


@section('content')
  <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->

        <div class="row">
          <div class="col-lg-12 col-6">
            <div class="card">
              <div class="card-body">

<!-- form filter --> 
<form class="form-horizontal needs-validation" 
      action="{{ route('fasilitas.layanan.filter') }}"
      method="post" 
      enctype="multipart/form-data"
      novalidate>
@csrf
                <div class="row">

                  <!-- field fasilitas -->
                  <div class="col-lg-3">
                    <label>Fasilitas</label> 
                    <select class="form-control" 
                            name="fasilitas"
                            id="fasilitas">
                            <option value="" >- ALL -</option>
  @foreach($fasilitas as $satu)
                            <option value="{{$satu->id}}" @if($fasilitas_id == $satu->id) selected @endif>
                              {{strtoupper($satu->kode)}} - {{strtoupper($satu->nama)}}
                            </option>
  @endforeach
                    </select>
                  </div>

                  <!-- field lokasi tingkat I -->
                  <div class="col-lg-3"> 
                    <label>Lokasi Tingkat I</label>
                    <select class="form-control" name="lokasi_tk_1" id="formLokasiTk1">
                      <option value="">- ALL -</option>
  @foreach($lokasi_tk_1 as $satu)
                      <option value="{{$satu->id}}" @if($lokasi_tk_1_id == $satu->id) selected @endif>
                        {{strtoupper($satu->kode)}} - {{strtoupper($satu->nama)}}
                      </option>
  @endforeach
                    </select>
                  </div>

                  <!-- field lokasi tingkat II -->
                  <div class="col-lg-3"> 
                    <label>Lokasi Tingkat II</label>
                    <select class="form-control" 
                            name="lokasi_tk_2"
                            id="formLokasiTk2">
                      <option value="" >- ALL -</option>
                    </select>
                  </div>

                  <!-- field lokasi tingkat III -->
                  <div class="col-lg-3"> 
                    <label>Lokasi Tingkat III</label>
                    <select class="form-control" 
                            name="lokasi_tk_3"
                            id="formLokasiTk3">
                      <option value="" >- ALL -</option>
                    </select>
                  </div>

                </div>
                <!-- /.row -->
                <br>
                <div class="row">

                  <!-- field kondisi -->
                  <div class="col-lg-3"> 
                    <label>Kondisi</label>
                    <select class="form-control" 
                            name="kondisi"
                            id="kondisi">
                      <option value="" @if($kondisi === null || $kondisi === '') selected @endif>- ALL -</option>
                      <option value="1" @if($kondisi === 1) selected @endif>SERVICEABLE</option>
                      <option value="0" @if($kondisi === 0) selected @endif>UNSERVICEABLE</option>
                    </select>
                  </div>

                  <!-- field status -->
                  <div class="col-lg-3"> 
                    <label>Status</label>
                    <select class="form-control" 
                            name="status"
                            id="status">
                      <option value="" >- ALL -</option>
                      <option value="" @if($kondisi === null || $kondisi === '') selected @endif>- ALL -</option>
                      <option value="2" @if($kondisi === 1) selected @endif>DRAFT</option>
                      <option value="1" @if($kondisi === 0) selected @endif>AKTIF</option>
                      <option value="0" @if($kondisi === 0) selected @endif>TIDAK AKTIF</option>
                    </select>
                  </div>

                </div>
                <!-- /.row -->

              </div>
              <!-- /.card-body -->

              <div class="card-footer">
                  <button type="submit" 
                          class="btn btn-primary btn-sm float-right">
                          <i class="fas fa-filter"></i>&nbsp;&nbsp;&nbsp;Filter</button>
              </div>
</form>
<!-- form --> 
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->

        <div class="row">
          <div class="col-lg-12 col-6">
            
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">DAFTAR LAYANAN</h3>

                <a class="btn btn-success btn-sm float-right" 
                   href="{{url('/fasilitas/layanan/tambah/step1')}}" 
                   role="button"><i class="fas fa-plus"></i>&nbsp;&nbsp;&nbsp;Tambah</a>

              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example" class="table table-bordered table-striped">
                  <thead>
                    <tr class="table-condensed">
                      <th style="width: 10px"><center>NO.</center></th>
                      <th><center>KODE</center></th>
                      <th><center>NAMA</center></th>
                      <th><center>FASILITAS</center></th>
                      <th><center>LOK. TK I</center></th>
                      <th><center>LOK. TK II</center></th>
                      <th><center>LOK. TK III</center></th>
                      <th><center>KONDISI</center></th>
                      <th><center>STATUS</center></th>
                      <th style="width: 100px"></th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($daftar as $satu)
                    <tr class="table-condensed">
                      <td></td>
                      <td><center>{{ strtoupper($satu->kode) }}</center></td>
                      <td><center>{{ strtoupper($satu->nama) }}</center></td>
                      <td><center>{{ strtoupper($satu->fasilitas->kode) }}</center></td>
                      <td><center>{{ strtoupper($satu->LokasiTk1->nama) }}</center></td>
                      <td><center>{{ strtoupper($satu->LokasiTk2->nama) }}</center></td>
                      <td><center>{{ strtoupper($satu->LokasiTk3->nama) }}</center></td>

  @if($satu->kondisi == config('constants.kondisi_layanan.serviceable'))
                      <td><center><span class="badge bg-success">SERVICEABLE</span></center></td>
  @else
                      <td><center><span class="badge bg-danger">UNSERVICEABLE</span></center></td>
  @endif

  @if($satu->status == 0)
                      <td><center><span class="badge bg-danger">TIDAK AKTIF</span></center></td>
  @elseif($satu->status == 1)
                      <td><center><span class="badge bg-success">AKTIF</span></center></td>
  @elseif($satu->status == 2)
                      <td><center><span class="badge bg-warning">DRAFT</span></center></td>
  @endif
                      <td>
<!--
<form action="{{url('/fasilitas/peralatan/detail')}}"
      method="post">
@csrf

<input type="text" name="id" value="{{ $satu->id }}" hidden>
<button type="submit" class="btn btn-primary btn-sm float-right">Test</button>
</form>
-->
                        <center>
  @if($satu->status != config('constants.status_layanan.draft'))
                          <a class="btn btn-info btn-sm" 
                             href="{{ route('fasilitas.layanan.edit.step1.form', ['id' => $satu->id]) }}" 
                             role="button"
                             title="Edit Data"><i class="fas fa-pencil-alt"></i></a>
  @endif
                          <button class="btn btn-secondary btn-sm" 
                                  onclick="detail('{{ $satu->id }}')"
                                  title="Detail">
                                  <i class="fas fa-angle-double-right"></i>
                          </button>
  @if($satu->status == config('constants.status_layanan.draft'))
                          <a class="btn btn-warning btn-sm" 
                             href="{{ route('fasilitas.layanan.tambah.step1.back.form', ['id' => $satu->id]) }}" 
                             role="button"
                             title="Lanjut Tambah Data"><i class="fas fa-pencil-alt"></i></a>

                          <button class="btn btn-danger btn-sm" 
                                  onclick="hapus('{{ $satu->id }}')"
                                  title="Hapus">
                                  <i class="fas fa-trash-alt"></i>
                          </button>
  @endif
                        </center>
                      </td>
                    </tr>
@endforeach                   
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
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

    <!-- isi modal tombol hapus -->
  <div class="modal fade" id="modal_hapus">
    <div class="modal-dialog">
      <div class="modal-content" id="isi_modal_hapus">
        <!-- isi modal dari js -->
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->

@endsection
<!-- /. section content -->


@section('tail')

<!-- javascript untuk pop up notifikasi -->
<script type="text/javascript">
  @if (session()->has('notif'))
    @if (session()->get('notif') == 'tambah_sukses')
      $(document).Toasts('create', {
          class: 'bg-success',
          title: 'Sukses!',
          body: 'Layanan baru telah berhasil ditambahkan',
          autohide: true,
          delay: 3000
        })
    @elseif(session()->get('notif') == 'draft_sukses')
      $(document).Toasts('create', {
          class: 'bg-success',
          title: 'Sukses!',
          body: 'Draft layanan telah berhasil disimpan',
          autohide: true,
          delay: 3000
        })
    @elseif(session()->get('notif') == 'hapus_sukses')
      $(document).Toasts('create', {
          class: 'bg-success',
          title: 'Sukses!',
          body: 'Draft layanan telah berhasil dihapus',
          autohide: true,
          delay: 3000
        })
    @elseif(session()->get('notif') == 'simpan_sukses')
      $(document).Toasts('create', {
          class: 'bg-success',
          title: 'Sukses!',
          body: 'Data layanan telah berhasil disimpan',
          autohide: true,
          delay: 3000
        })
    @elseif(session()->get('notif') == 'tambah_gagal')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Error!',
          body: 'Gagal menambahkan layanan baru',
          autohide: true,
          delay: 3000
        })
    @elseif(session()->get('notif') == 'simpan_gagal')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Error!',
          body: 'Gagal menyimpan data layanan',
          autohide: true,
          delay: 3000
        })
    @elseif(session()->get('notif') == 'hapus_gagal')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Error!',
          body: 'Gagal menghapus draft layanan',
          autohide: true,
          delay: 3000
        })
    @elseif(session()->get('notif') == 'item_null')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Error!',
          body: 'Gagal menampilkan data layanan',
          autohide: true,
          delay: 3000
        })
    @endif
  @endif
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
        url : "{{url('/fasilitas/peralatan/detail')}}",
        type: "POST",
        data : {id: id},
        success: function(data){

          //alert(data.nama);

          $('#detail').empty();

          var row = '<div class="modal-header">';
              row += '<h4 class="modal-title">Detail Peralatan</h4>';
              row += '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
              row += '<span aria-hidden="true">&times;</span></button></div>';// modal header

              row += '<div class="modal-body">';
              row += "<table border='0' cellpadding='5px'>";            
              row += "<tr><th>Kode</th><td>:</td><td>"+ data.peralatan.kode.toUpperCase(); +"</td></tr>";  
              row += "<tr><th>Nama</th><td>:</td><td>"+ data.peralatan.nama.toUpperCase(); +"</td></tr>";

          if(data.peralatan.merk != null){
              row += "<tr><th>Merk</th><td>:</td><td>"+ data.peralatan.merk.toUpperCase(); +"</td></tr>";
          }else{
              row += "<tr><th>Merk</th><td>:</td><td></td></tr>";
          }

          if(data.peralatan.tipe != null){
              row += "<tr><th>Tipe</th><td>:</td><td>"+ data.peralatan.tipe.toUpperCase(); +"</td></tr>";
          }else{
              row += "<tr><th>Tipe</th><td>:</td><td></td></tr>";
          }

          if(data.peralatan.model != null){
              row += "<tr><th>Model</th><td>:</td><td>"+ data.peralatan.model.toUpperCase(); +"</td></tr>";
          }else{
              row += "<tr><th>Model</th><td>:</td><td></td></tr>";
          }

          if(data.peralatan.serial_number != null){
              row += "<tr><th>Serial Number</th><td>:</td><td>"+ data.peralatan.serial_number.toUpperCase(); +"</td></tr>";
          }else{
              row += "<tr><th>Serial Number</th><td>:</td><td></td></tr>";
          }

          if(data.peralatan.thn_produksi != null){
              row += "<tr><th>Tahun Produksi</th><td>:</td><td>"+ data.peralatan.thn_produksi; +"</td></tr>";
          }else{
              row += "<tr><th>Tahun Produksi</th><td>:</td><td></td></tr>";
          }

          if(data.peralatan.thn_pengadaan != null){
              row += "<tr><th>Tahun Pengadaan</th><td>:</td><td>"+ data.peralatan.thn_pengadaan; +"</td></tr>";
          }else{
              row += "<tr><th>Tahun Pengadaan</th><td>:</td><td></td></tr>";
          }
                   
              row += "<tr><th>Jenis Alat</th><td>:</td><td>"+ data.jenis.nama.toUpperCase(); +"</td></tr>";
          
          if(data.peralatan.sewa == 1){
              row += "<tr><th>Status Kepemilikan</th><td>:</td><td>SEWA</td></tr>";
          }else{
              row += "<tr><th>Status Kepemilikan</th><td>:</td><td>ASET</td></tr>";
          }

              row += "<tr><th>Perusahaan Pemilik</th><td>:</td><td>"+ data.perusahaan.nama.toUpperCase(); +"</td></tr>";

          if(data.peralatan.keterangan != null){
              row += "<tr><th>Keterangan</th><td>:</td><td>"+ data.peralatan.keterangan.toUpperCase(); +"</td></tr>";
          }else{
              row += "<tr><th>Keterangan</th><td>:</td><td></td></tr>";
          }

          if(data.peralatan.status == 1){
              row += "<tr><th>Status</th><td>:</td><td>AKTIF</td></tr>";
          }else{
              row += "<tr><th>Status</th><td>:</td><td>TIDAK AKTIF</td></tr>";
          }

          if(data.created_by != null){
              row += "<tr><th>Dibuat Oleh</th><td>:</td><td>"+ data.created_by.name.toUpperCase(); +"</td></tr>";
              row += "<tr><th>Dibuat Pada</th><td>:</td><td>"+ data.peralatan.created_at +"</td></tr>";
          }else{
              row += "<tr><th>Dibuat Oleh</th><td>:</td><td></td></tr>";
              row += "<tr><th>Dibuat Pada</th><td>:</td><td></td></tr>";
          } 

          if(data.updated_by != null){
              row += "<tr><th>Update Terakhir Oleh</th><td>:</td><td>"+ data.updated_by.name.toUpperCase(); +"</td></tr>";
              row += "<tr><th>Update Terakhir Pada</th><td>:</td><td>"+ data.peralatan.updated_at +"</td></tr>";
          }else{
              row += "<tr><th>Update Terakhir Oleh</th><td>:</td><td></td></tr>";
              row += "<tr><th>Update Terakhir Pada</th><td>:</td><td></td></tr>";
          }      
              
              row += '</table>';
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
            alert('Gagal menampilkan detail peralatan');
        }
    });
}
</script>


<!-- javascript untuk menampilkan modal untuk menghapus peralatan -->
<script type="text/javascript">
  function hapus(layanan_id) {
    //alert(id);
    $('#isi_modal_hapus').empty();;

      var html = '<form action="{{ route('fasilitas.layanan.hapus') }}" method="post">';
          html += '@csrf';
          html += '<div class="modal-body">';
          html += '<p><center>Ingin menghapus draft layanan ini?</center></p>';
          html += '<input type="text" name="id" value="'+layanan_id+'" hidden>';
          html += '</div>';
          html += '<div class="modal-footer justify-content-between">';
          html += '<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Tidak</button>';
          html += '<button type="submit" class="btn btn-danger btn-sm float-right">Hapus</button>';
          html += '</div>';
          html += '</form>';

    $("#isi_modal_hapus").append(html);
    $("#modal_hapus").modal('show');  
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


<!-- Javascript untuk memproses dropdown lokasi setelah proses filter data -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const lokasiTk1Id = "{{ $lokasi_tk_1_id }}";
    const lokasiTk2Id = "{{ $lokasi_tk_2_id }}";
    const lokasiTk3Id = "{{ $lokasi_tk_3_id }}";

    // Cek jika ada lokasi_tk_1_id yang dikirim dari controller
    if (lokasiTk1Id) {
        fetch('/json/lokasi-tk-2/daftar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ lokasi_tk_1_id: lokasiTk1Id })
        })
        .then(response => response.json())
        .then(data => {
            let options = '<option value="">- ALL -</option>';
            data.forEach(item => {
                const selected = lokasiTk2Id == item.id ? 'selected' : '';
                options += `<option value="${item.id}" ${selected}>${item.kode.toUpperCase()} - ${item.nama.toUpperCase()}</option>`;
            });
            document.getElementById('formLokasiTk2').innerHTML = options;

            // Kalau Tk2 ada, lanjut load Tk3
            if (lokasiTk2Id) {
                fetch('/json/lokasi-tk-3/daftar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ lokasi_tk_2_id: lokasiTk2Id })
                })
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">- ALL -</option>';
                    data.forEach(item => {
                        const selected = lokasiTk3Id == item.id ? 'selected' : '';
                        options += `<option value="${item.id}" ${selected}>${item.kode.toUpperCase()} - ${item.nama.toUpperCase()}</option>`;
                    });
                    document.getElementById('formLokasiTk3').innerHTML = options;
                });
            }
        });
    }
  });
</script>

@endsection
<!-- /. section tail -->