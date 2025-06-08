  
@extends('fasilitas.main')

@section('head')
  <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

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
          <div class="col-lg-12">
             <div class="card">
              <div class="card-body">
                <br>
                <div class="stepper-wrapper">
                  <div class="stepper-item completed">
                    <div class="step-counter">1</div>
                    <div class="step-name">Data Layanan</div>
                  </div>
                  <div class="stepper-item completed">
                    <div class="step-counter">2</div>
                    <div class="step-name">Daftar Peralatan</div>
                  </div>
                  <div class="stepper-item active">
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
                <h3 class="card-title">DATA LAYANAN</h3>
              </div>
              <!-- /.card-header -->

              <div class="card-body">

                <table border='0' cellpadding='5px'>          
                <tr><th>Fasilitas</th><td>:</td><td>{{ $layanan->fasilitas->kode }} - {{ $layanan->fasilitas->nama }}</td></tr>
                <tr><th>Kode</th><td>:</td><td>{{ $layanan->kode }}</td></tr>
                <tr><th>Nama</th><td>:</td><td>{{ $layanan->nama }}</td></tr>
                <tr><th>Lokasi Tingkat I</th><td>:</td><td>{{ $layanan->LokasiTk1->kode }} - {{ $layanan->LokasiTk1->nama }}</td></tr>
                <tr><th>Lokasi Tingkat II</th><td>:</td><td>{{ $layanan->LokasiTk2->kode }} - {{ $layanan->LokasiTk2->nama }}</td></tr>
                <tr><th>Lokasi Tingkat III</th><td>:</td><td>{{ $layanan->LokasiTk3->kode }} - {{ $layanan->LokasiTk3->nama }}</td></tr>
                <tr><th>Kondisi Layanan</th><td>:</td><td>{{ $layanan->nama }}</td></tr>
  @if($layanan->kondisi == config('constants.kondisi_layanan.serviceable'))
                <tr><th>Kondisi Layanan</th><td>:</td><td><span class="badge bg-success">SERVICEABLE</span></td></tr>
  @else
                <tr><th>Kondisi Layanan</th><td>:</td><td><span class="badge bg-danger">UNSERVICEABLE</span></td></tr>
  @endif
                </table>

              </div>
              <!-- /.card-body -->
              
            </div>
            <!-- /.card -->
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->

        <div class="row">
          <div class="col-lg-12 col-6">
            
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">DAFTAR PERALATAN</h3>
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
                      <th><center>TIPE</center></th>
                      <th><center>MODEL</center></th>
                      <th><center>SN</center></th>
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
                      <td><center>{{ strtoupper($satu->peralatan->tipe) }}</center></td>
                      <td><center>{{ strtoupper($satu->peralatan->model) }}</center></td>
                      <td><center>{{ strtoupper($satu->peralatan->serial_number) }}</center></td>
                      <td><center>{{ strtoupper($satu->ip_address) }}</center></td>
  @if($satu->kondisi == config('constants.kondisi_peralatan_layanan.beroperasi'))
                      <td><center><span class="badge bg-success">BEROPERASI</span></center></td>
  @else
                      <td><center><span class="badge bg-danger">GANGGUAN</span></center></td>
  @endif
                      <td>
                        <center>
                          <button class="btn btn-secondary btn-sm" 
                                  onclick="detail('{{ $satu->peralatan->id }}')"
                                  title="Detail Peralatan">
                                  <i class="fas fa-angle-double-right"></i>
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
                   href="{{ route('fasilitas.layanan.tambah.step2.form', ['id' => $layanan->id]) }}" 
                   role="button"><i class="fas fa-angle-left"></i>&nbsp;&nbsp;&nbsp;Kembali</a>

                <button type="button" 
                        class="btn btn-primary btn-sm float-right" 
                        onclick="submit('{{ $layanan->id }}')"
                        title="Submit">
                        Submit &nbsp;&nbsp;&nbsp;<i class="fas fa-check"></i>
                </button>

                <!--<a class="btn btn-primary btn-sm float-right" 
                   href="{{ route('fasilitas.layanan.tambah.step3', ['id' => $layanan->id]) }}" 
                   role="button">Submit &nbsp;&nbsp;&nbsp;<i class="fas fa-check"></i></a>
                <button type="submit" class="btn btn-success btn-sm float-right">Lanjut &nbsp;&nbsp;&nbsp;<i class="fas fa-angle-right"></i></button> -->
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

    <!-- isi modal tombol submit -->
    <div class="modal fade" id="modal_submit">
      <div class="modal-dialog">
        <div class="modal-content" id="isi_modal_submit">
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
        @if (session()->get('notif') == 'tambah_gagal')
          $(document).Toasts('create', {
              class: 'bg-danger',
              title: 'Sukses!',
              body: 'Gagal menambahkan layanan baru',
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


  <!-- javascript untuk menampilkan modal untuk submit layanan -->
    <script type="text/javascript">
      function submit(layanan_id) {
        //alert(id);
        $('#isi_modal_submit').empty();;

          var html = '<form action="{{route('fasilitas.layanan.tambah.step3')}}" method="post">';
              html += '@csrf';
              html += '<div class="modal-body">';
              html += '<p><center>Ingin menyimpan dan mengaktifkan layanan ini?</center></p>';
              html += '<input type="text" name="id" value="'+layanan_id+'" hidden>';
              html += '</div>';
              html += '<div class="modal-footer justify-content-between">';
              html += '<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Tidak</button>';
              html += '<button type="submit" class="btn btn-primary btn-sm float-right">&nbsp;&nbsp;&nbsp;Ya&nbsp;&nbsp;&nbsp;</button>';
              html += '</div>';
              html += '</form>';

        $("#isi_modal_submit").append(html);
        $("#modal_submit").modal('show');  
      }
    </script>


@endsection
<!-- /. section tail -->