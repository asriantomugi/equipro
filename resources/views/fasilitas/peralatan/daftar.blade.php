  
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
              <div class="card-header">
                <h3 class="card-title">DAFTAR PERALATAN</h3>

                <a class="btn btn-success btn-sm float-right" 
                   href="{{url('/fasilitas/peralatan/tambah')}}" 
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
                      <th><center>MERK</center></th>
                      <th><center>TIPE</center></th>
                      <th><center>MODEL</center></th>
                      <th><center>SN</center></th>
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
                      <td><center>{{ strtoupper($satu->merk) }}</center></td>
                      <td><center>{{ strtoupper($satu->tipe) }}</center></td>
                      <td><center>{{ strtoupper($satu->model) }}</center></td>
                      <td><center>{{ strtoupper($satu->serial_number) }}</center></td>

  @if($satu->kondisi == config('constants.kondisi_peralatan.Normal'))
                      <td><center><span class="badge bg-success">NORMAL</span></center></td>
  @else
                      <td><center><span class="badge bg-danger">RUSAK</span></center></td>
  @endif

  @if($satu->status == 1)
                      <td><center><span class="badge bg-success">AKTIF</span></center></td>
  @else
                      <td><center><span class="badge bg-danger">TIDAK AKTIF</span></center></td>
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
                          <a class="btn btn-info btn-sm" 
                             href="{{url('/fasilitas/peralatan/edit/'.$satu->id)}}" 
                             role="button"
                             title="Edit Data"><i class="fas fa-pencil-alt"></i></a>
                          <button class="btn btn-secondary btn-sm" 
                                  onclick="detail('{{ $satu->id }}')"
                                  title="Detail">
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
          body: 'Peralatan baru telah berhasil ditambahkan',
          autohide: true,
          delay: 3000
        })
    @elseif(session()->get('notif') == 'edit_sukses')
      $(document).Toasts('create', {
          class: 'bg-success',
          title: 'Sukses!',
          body: 'Data peralatan telah berhasil diubah'
        })
    @elseif(session()->get('notif') == 'tambah_gagal')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Error!',
          body: 'Gagal menambahkan peralatan baru',
          autohide: true,
          delay: 3000
        })
    @elseif(session()->get('notif') == 'edit_gagal')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Error!',
          body: 'Gagal mengubah data peralatan',
          autohide: true,
          delay: 3000
        })
    @elseif(session()->get('notif') == 'item_null')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Error!',
          body: 'Gagal menampilkan data peralatan',
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

          //alert(data.jenis.nama);

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

@endsection
<!-- /. section tail -->