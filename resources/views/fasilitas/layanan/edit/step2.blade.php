  
@extends('fasilitas.main')

@section('head')
  <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section('content')
  <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->

        {{-- Step Navigation --}}
        <div class="row mb-2">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body py-2">
                        <ul class="step d-flex flex-nowrap">
                            <li class="step-item completed"><a href="#">Data Layanan</a></li>
                            <li class="step-item active"><a href="#">Daftar Peralatan</a></li>
                            <li class="step-item"><a href="#">Review</a></li>
                            
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
          <div class="col-lg-12 col-6">
            
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">DAFTAR PERALATAN</h3>

                <button class="btn btn-success btn-sm float-right" 
                        data-toggle="modal" 
                        data-target="#modalPeralatan"><i class="fas fa-plus"></i>&nbsp;&nbsp;&nbsp;Tambah</button>
              </div>
              <!-- /.card-header -->

              <div class="card-body">

                <table id="example" class="table table-bordered table-striped">
                  <thead>
                    <tr class="table-condensed">
                      <th style="width: 10px"><center>NO.</center></th>
                      <th><center>KODE</center></th>
                      <th><center>NAMA</center></th>
                      <th><center>MERK</center></th>
                      <th><center>JENIS ALAT</center></th>
                      <th><center>IP ADDRESS</center></th>
                      <th><center>KONDISI</center></th>
                      <th style="width: 100px"></th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($daftar_peralatan as $satu)
                    <tr class="table-condensed">
                      <td></td>
                      <td><center>{{ strtoupper($satu->peralatan->kode) }}</center></td>
                      <td><center>{{ strtoupper($satu->peralatan->nama) }}</center></td>
                      <td><center>{{ strtoupper($satu->peralatan->merk) }}</center></td>
                      <td><center>{{ strtoupper($satu->peralatan->jenis->nama) }}</center></td>
                      <td><center>{{ strtoupper($satu->ip_address) }}</center></td>
  @if($satu->kondisi == config('constants.kondisi_peralatan_layanan.beroperasi'))
                      <td><center><span class="badge bg-success">BEROPERASI</span></center></td>
  @else
                      <td><center><span class="badge bg-danger">GANGGUAN</span></center></td>
  @endif
                      <td>
                        <center>
                          <button type="button" 
                                  class="btn btn-info btn-sm" 
                                  onclick="edit('{{ $satu->id }}', '{{ $satu->peralatan->id }}')"
                                  title="Edit Data Peralatan">
                                  <i class="fas fa-pencil-alt"></i>
                          </button>
                          <button class="btn btn-secondary btn-sm" 
                                  onclick="detail('{{ $satu->peralatan->id }}')"
                                  title="Detail Peralatan">
                                  <i class="fas fa-angle-double-right"></i>
                          </button>
                          <button type="button" 
                                  class="btn btn-danger btn-sm" 
                                  onclick="hapus('{{ $satu->peralatan->id }}', '{{ $layanan->id }}')"
                                  title="Hapus Peralatan">
                                  <i class="fas fa-trash-alt"></i>
                          </button>
                        </center>
                      </td>
                    </tr>
@endforeach                   
                  </tbody>
                </table>

              </div>
              <!-- /.card-body -->

              <div class="card-footer">
                <a class="btn btn-success btn-sm" 
                   href="{{url('/fasilitas/layanan/tambah/step1/back/'.$layanan->id)}}" 
                   role="button"><i class="fas fa-angle-left"></i>&nbsp;&nbsp;&nbsp;Kembali</a>
                <a class="btn btn-success btn-sm float-right" 
                   href="{{ route('fasilitas.layanan.edit.step3.form', ['id' => $layanan->id]) }}" 
                   role="button">Lanjut &nbsp;&nbsp;&nbsp;<i class="fas fa-angle-right"></i></a>
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

    <!-- Modal tombol detail -->
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

    <!-- isi modal tombol edit -->
    <div class="modal fade" id="modal_edit">
      <div class="modal-dialog modal-lg">
        <div class="modal-content" id="isi_modal_edit">
          <!-- isi modal dari js -->
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <!-- Modal untuk tambah peralatan -->
    <div class="modal fade" 
         id="modalPeralatan" 
         tabindex="-1" 
         role="dialog" 
         aria-labelledby="modalPeralatanLabel" 
         aria-hidden="true">

      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

          <div class="modal-header">
            <h5 class="modal-title">Daftar Peralatan Tersedia</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body">

            <form id="filter-form">
              @csrf

            <input type="hidden" name="layanan_id" value="{{ $layanan->id }}">
            
            <div class="row">

              <div class="col-lg-3">
                <label for="jenis" class="form-label">Jenis Alat</label>
                <select name="jenis" class="form-control">
                    <option value="">- ALL -</option>
                    @foreach($jenis as $satu)
                    <option value="{{ $satu->id }}">{{ $satu->kode }} - {{ $satu->nama }}</option>
                    @endforeach
                </select>
              </div>

              <div class="col-lg-3">
                <label for="kondisi" class="form-label">Kondisi</label>
                <select name="kondisi" class="form-control">
                    <option value="">- ALL -</option>
                    <option value="1">NORMAL</option>
                    <option value="0">RUSAK</option>
                </select>
              </div>

              <div class="col-lg-3">
                <label for="sewa" class="form-label">Status Kepemilikan</label>
                <select name="sewa" class="form-control">
                    <option value="">- ALL -</option>
                    <option value="1">SEWA</option>
                    <option value="0">ASET</option>
                </select>
              </div>

              <div class="col-lg-3">
                <label for="perusahaan" class="form-label">Perusahaan Pemilik</label>
                <select name="perusahaan" class="form-control">
                    <option value="">- ALL -</option>
                    @foreach($perusahaan as $satu)
                    <option value="{{ $satu->id }}">{{ $satu->kode }} - {{ $satu->nama }}</option>
                    @endforeach
                </select>
              </div>

            </div>
            <!-- row -->
            <br>
            <div class="row">
              <div class="col-lg-12">
                  <button type="submit" 
                          class="btn btn-primary btn-sm float-right">
                          <i class="fas fa-filter"></i>&nbsp;&nbsp;&nbsp;Filter Data</button>
              </div>
            </div>
            <!-- row -->
            </form>
            <!-- form -->

            <br>
            <div class="row">
              <div class="col-lg-12">
                <div id="daftar-peralatan-tabel">
                  <!-- AJAX content will be inserted here -->

                </div>
              </div>
              <!-- col-lg-12 -->
            </div>
            <!-- row -->

          </div>
        </div>
      </div>
    </div>
    <!-- Akhir dari Modal untuk tambah peralatan -->

    @endsection
    <!-- /. section content -->

    @section('tail')

    <!-- javascript untuk pop up notifikasi -->
    <script type="text/javascript">
      @if (session()->has('notif'))
        @if (session()->get('notif') == 'hapus_sukses')
          $(document).Toasts('create', {
              class: 'bg-success',
              title: 'Sukses!',
              body: 'Peralatan telah berhasil dihapus',
              autohide: true,
              delay: 3000
            })
        @elseif (session()->get('notif') == 'simpan_sukses')
          $(document).Toasts('create', {
              class: 'bg-success',
              title: 'Sukses!',
              body: 'Data peralatan telah berhasil disimpan',
              autohide: true,
              delay: 3000
            })
        @elseif(session()->get('notif') == 'hapus_gagal')
          $(document).Toasts('create', {
              class: 'bg-danger',
              title: 'Error!',
              body: 'Gagal menghapus peralatan',
              autohide: true,
              delay: 3000
            })
        @elseif(session()->get('notif') == 'simpan_gagal')
          $(document).Toasts('create', {
              class: 'bg-danger',
              title: 'Error!',
              body: 'Gagal menyimpan data peralatan',
              autohide: true,
              delay: 3000
            })
        @elseif(session()->get('notif') == 'item_null')
          $(document).Toasts('create', {
              class: 'bg-danger',
              title: 'Error!',
              body: 'Peralatan tidak ditemukan',
              autohide: true,
              delay: 3000
            })
        @endif
      @endif
    </script>

    <!-- javascript untuk pop up khusus notifikasi tambah berhasil -->
    <script>
        $(document).ready(function () {
            if (localStorage.getItem('tambah_sukses') === 'true') {
                // Tampilkan toast
                $(document).Toasts('create', {
                    class: 'bg-success',
                    title: 'Berhasil!',
                    body: 'Peralatan berhasil ditambahkan',
                    autohide: true,
                    delay: 5000
                });
            }
              // Hapus flag agar tidak muncul lagi saat reload berikutnya
              // localStorage.removeItem('tambah_peralatan_sukses');
              localStorage.clear();
        });
    </script>


    <!-- javascript untuk proses submit filter data peralatan tersedia dan tambah peralatan -->
    <script type="text/javascript">
      $(function () {
        // Filter form submit
        $('#filter-form').on('submit', function (e) {
            e.preventDefault();

            // proses POST filter data daftar peralatan tersedia
            $.post('{{ url('/fasilitas/layanan/peralatan/filter') }}', 
              $(this).serialize(), 
              function (data) {
                $('#daftar-peralatan-tabel').html(data);

                // Inisialisasi ulang DataTable setelah isi diganti
                if ($.fn.DataTable.isDataTable('#tabelPeralatanTersedia')) {
                    $('#tabelPeralatanTersedia').DataTable().destroy();
                }

                peralatanTable = $('#tabelPeralatanTersedia').DataTable({
                    paging: true,
                    pageLength: 5, // jumlah row default data yang ditampilkan
                    lengthChange: true,
                    searching: true,
                    ordering: true,
                    autoWidth: false,
                    responsive: true,
                    language: {
                      "lengthMenu": "Tampilkan _MENU_ data per halaman",
                      "zeroRecords": "Data tidak ditemukan",
                      "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                      "infoEmpty": "Tidak ada data",
                      "infoFiltered": "(difilter dari _MAX_ total data)",
                      "search": "Cari:",
                      "paginate": {
                        "next": "Berikutnya",
                        "previous": "Sebelumnya"
                      }
                    },
                    columnDefs: [{
                      targets: 0,
                      searchable: false,
                      orderable: false
                    }],
                    order: [[1, 'asc']]
                });

                // Penomoran ulang kolom pertama
                peralatanTable.on('order.dt search.dt draw.dt', function () {
                    let i = 1;
                    peralatanTable.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell) {
                        cell.innerHTML = i++;
                    });
                }).draw();
            })
            /*.done(function () {
                alert('Berhasil');
            })*/
            // jika proses POST gagal
            .fail(function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert('Error: ' + xhr.responseText);
            });
        });
        // akhir dari proses POST filter data daftar peralatan tersedia
        
        // proses tambah peralatan di modal daftar peralatan tersedia
        $('#daftar-peralatan-tabel').on('click', '.btn-tambah-peralatan', function () {
          const peralatanId = $(this).data('id');
          const layananId = $('input[name="layanan_id"]').val();

          $.post('{{ url('/fasilitas/layanan/peralatan/tambah') }}', {
              _token: '{{ csrf_token() }}',
              peralatan_id: peralatanId,
              layanan_id: layananId,
          })
          // apabila proses tambah berhasil
          .done(function () {
              // Tutup modal
              $('#modalPeralatan').modal('hide');
              // Simpan flag sukses ke localStorage agar tetap ada setelah reload
              localStorage.setItem('tambah_sukses', 'true');
              // Reload halaman
              location.reload();
          })
          // apabila proses tambah gagal, tampilkan pesan error
          .fail(function (xhr) {
              let errorMsg = 'Gagal menambahkan peralatan.';
              
              if (xhr.responseJSON) {
                  if (xhr.responseJSON.reason) {
                      errorMsg = xhr.responseJSON.reason;
                  } else if (xhr.responseJSON.message) {
                      errorMsg = xhr.responseJSON.message;
                  }
              }

              // Tutup modal
              $('#modalPeralatan').modal('hide');

              // Tampilkan toast gagal
              $(document).Toasts('create', {
                  class: 'bg-danger',
                  title: 'Error!',
                  body: errorMsg,
                  autohide: true,
                  delay: 3000
              });
          });
        });
        // akhir dari proses tambah peralatan di modal daftar peralatan tersedia
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

        // Ajax Load data from ajax
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
      function hapus(peralatan_id, layanan_id) {
        //alert(id);
        $('#isi_modal_hapus').empty();;

          var html = '<form action="{{route('fasilitas.layanan.peralatan.hapus')}}" method="post">';
              html += '@csrf';
              html += '<div class="modal-body">';
              html += '<p><center>Ingin menghapus peralatan ini?</center></p>';
              html += '<input type="text" name="peralatan_id" value="'+peralatan_id+'" hidden>';
              html += '<input type="text" name="layanan_id" value="'+layanan_id+'" hidden>';
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


    <!-- javascript untuk menampilkan modal edit data peralatan -->
    <script type="text/javascript">
      function edit(id, peralatan_id){

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Ajax Load data from ajax
        $.ajax({
            url : "{{ route('fasilitas.layanan.peralatan.detail') }}",
            type: "POST",
            data : {
              id: id,
              peralatan_id: peralatan_id
            },
            success: function(data){

              //alert(data.peralatan.nama);

              $('#isi_modal_edit').empty();

              var html = '<div class="modal-header">';
                  html += '<h4 class="modal-title">Form Ubah Data Peralatan</h4>';
                  html += '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                  html += '<span aria-hidden="true">&times;</span></button></div>';// modal header
                  html += '<form action="{{route('fasilitas.layanan.peralatan.edit')}}" method="post">';
                  html += '@csrf';
                  html += '<div class="modal-body">';
                  html += '<input type="text" name="id" value="'+ id +'" hidden>';
                  html += '<input type="text" name="peralatan_id" value="'+ peralatan_id +'" hidden>';
                  
                  html += '<div class="form-group row">';
                  html += '<label class="col-sm-3 col-form-label">Kode</label>';
                  html += '<div class="col-sm-9">';
                  html += '<input type="text" name="kode" class="form-control" value="'+ data.peralatan.kode.toUpperCase() +'" disabled>';
                  html += '</div>';
                  html += '</div>';

                  html += '<div class="form-group row">';
                  html += '<label class="col-sm-3 col-form-label">Nama</label>';
                  html += '<div class="col-sm-9">';
                  html += '<input type="text" name="nama" class="form-control" value="'+ data.peralatan.nama.toUpperCase() +'" disabled>';
                  html += '</div>';
                  html += '</div>';

                  html += '<div class="form-group row">';
                  html += '<label class="col-sm-3 col-form-label">Merk</label>';
                  html += '<div class="col-sm-9">';
                  html += '<input type="text" name="merk" class="form-control" value="'+ data.peralatan.merk.toUpperCase() +'" disabled>';
                  html += '</div>';
                  html += '</div>';

                  html += '<div class="form-group row">';
                  html += '<label class="col-sm-3 col-form-label">Tipe</label>';
                  html += '<div class="col-sm-9">';
                  html += '<input type="text" name="tipe" class="form-control" value="'+ data.peralatan.tipe.toUpperCase() +'" disabled>';
                  html += '</div>';
                  html += '</div>';

                  html += '<div class="form-group row">';
                  html += '<label class="col-sm-3 col-form-label">Model</label>';
                  html += '<div class="col-sm-9">';
                  html += '<input type="text" name="model" class="form-control" value="'+ data.peralatan.model.toUpperCase() +'" disabled>';
                  html += '</div>';
                  html += '</div>';

                  html += '<div class="form-group row">';
                  html += '<label class="col-sm-3 col-form-label">Serial Number</label>';
                  html += '<div class="col-sm-9">';
                  html += '<input type="text" name="serial_number" class="form-control" value="'+ data.peralatan.serial_number.toUpperCase() +'" disabled>';
                  html += '</div>';
                  html += '</div>';

              if(data.satuPeralatan.ip_address != null){
                  html += '<div class="form-group row">';
                  html += '<label class="col-sm-3 col-form-label">IP Address</label>';
                  html += '<div class="col-sm-9">';
                  html += '<input type="text" name="ip_address" class="form-control" value="'+ data.satuPeralatan.ip_address +'">';
                  html += '</div>';
                  html += '</div>';
              }else{
                  html += '<div class="form-group row">';
                  html += '<label class="col-sm-3 col-form-label">IP Address</label>';
                  html += '<div class="col-sm-9">';
                  html += '<input type="text" name="ip_address" class="form-control" value="">';
                  html += '</div>';
                  html += '</div>';
              }
              
              if(data.satuPeralatan.kondisi == 1){
                  html += '<div class="form-group row">';
                  html += '<label class="col-sm-3 col-form-label required">Kondisi</label>';
                  html += '<div class="col-sm-9">';
                  html += '<select name="kondisi" class="form-control" required>';
                  html += '<option value="1" selected>BEROPERASI</option>';
                  html += '<option value="0">GANGGUAN</option>';
                  html += '</select>';
                  html += '<div class="invalid-feedback">Kondisi wajib dipilih.</div>';
                  html += '</div>';
                  html += '</div>';
              }else{
                  html += '<div class="form-group row">';
                  html += '<label class="col-sm-3 col-form-label required">Kondisi</label>';
                  html += '<div class="col-sm-9">';
                  html += '<select name="kondisi" class="form-control" required>';
                  html += '<option value="1">BEROPERASI</option>';
                  html += '<option value="0" selected>GANGGUAN</option>';
                  html += '</select>';
                  html += '<div class="invalid-feedback">Kondisi wajib dipilih.</div>';
                  html += '</div>';
                  html += '</div>';
              }

                  html += '</div>';
                  html += '<div class="modal-footer justify-content-between">';
                  html += '<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Tidak</button>';
                  html += '<button type="submit" class="btn btn-primary btn-sm float-right">Simpan</button>';
                  html += '</div>';
                  html += '</form>';
            
              $("#isi_modal_edit").append(html);
              $("#modal_edit").modal('show'); // show bootstrap modal when complete loaded
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Gagal menampilkan detail peralatan');
            }
        });
    }
    </script>

@endsection
<!-- /. section tail -->