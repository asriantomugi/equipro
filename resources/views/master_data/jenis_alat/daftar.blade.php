  
@extends('master_data.main')

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
                <h3 class="card-title">DAFTAR JENIS ALAT</h3>

                <a class="btn btn-success btn-sm float-right" 
                   href="{{url('/master-data/jenis-alat/tambah')}}" 
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
                      <th><center>STATUS</center></th>
                      <th style="width: 100px"></th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($daftar as $satu)
                    <tr class="table-condensed">
                      <td></td>
                      <td>{{ strtoupper($satu->kode) }}</td>
                      <td>{{ strtoupper($satu->nama) }}</td>
  @if($satu->status == 1)
                      <td><center><span class="badge bg-success">AKTIF</span></center></td>
  @else
                      <td><center><span class="badge bg-danger">TIDAK AKTIF</span></center></td>
  @endif
                      <td>
<!--
<form action="{{url('/master-data/jenis-alat/detail')}}"
      method="post">
@csrf

<input type="text" name="id" value="{{ $satu->id }}" hidden>
<button type="submit" class="btn btn-primary btn-sm float-right">Test</button>
</form>
-->
                        <center>
                          <a class="btn btn-info btn-sm" 
                             href="{{url('/master-data/jenis-alat/edit/'.$satu->id)}}" 
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
        <div class="modal-dialog">
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
          body: 'Jenis Alat baru telah berhasil ditambahkan'
        })
    @elseif(session()->get('notif') == 'edit_sukses')
      $(document).Toasts('create', {
          class: 'bg-success',
          title: 'Sukses!',
          body: 'Data Jenis Alat telah berhasil diubah'
        })
    @elseif(session()->get('notif') == 'tambah_gagal')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Error!',
          body: 'Gagal menambahkan jenis alat baru'
        })
    @elseif(session()->get('notif') == 'edit_gagal')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Error!',
          body: 'Gagal mengubah data jenis alat'
        })
    @elseif(session()->get('notif') == 'item_null')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Error!',
          body: 'Gagal menampilkan data jenis alat'
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
        url : "{{url('/master-data/jenis-alat/detail')}}",
        type: "POST",
        data : {id: id},
        success: function(data){

          //alert(data.jenis_alat.detail);

          $('#detail').empty();

          var row = '<div class="modal-header">';
              row += '<h4 class="modal-title">Detail Jenis Alat</h4>';
              row += '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
              row += '<span aria-hidden="true">&times;</span></button></div>';// modal header

              row += '<div class="modal-body">';
              row += "<table border='0' cellpadding='5px'>";            
              row += "<tr><th>Kode</th><td>:</td><td>"+ data.jenis_alat.kode.toUpperCase(); +"</td></tr>";  
              row += "<tr><th>Nama</th><td>:</td><td>"+ data.jenis_alat.nama.toUpperCase(); +"</td></tr>";            
              
          if(data.jenis_alat.status == 1){
              row += "<tr><th>Status</th><td>:</td><td>AKTIF</td></tr>";
          }else{
              row += "<tr><th>Status</th><td>:</td><td>TIDAK AKTIF</td></tr>";
          }

          if(data.created_by != null){
              row += "<tr><th>Dibuat Oleh</th><td>:</td><td>"+ data.created_by.name.toUpperCase(); +"</td></tr>";
              row += "<tr><th>Dibuat Pada</th><td>:</td><td>"+ data.jenis_alat.created_at +"</td></tr>";
          }else{
              row += "<tr><th>Dibuat Oleh</th><td>:</td><td></td></tr>";
              row += "<tr><th>Dibuat Pada</th><td>:</td><td></td></tr>";
          } 

          if(data.updated_by != null){
              row += "<tr><th>Update Terakhir Oleh</th><td>:</td><td>"+ data.updated_by.name.toUpperCase(); +"</td></tr>";
              row += "<tr><th>Update Terakhir Pada</th><td>:</td><td>"+ data.jenis_alat.updated_at +"</td></tr>";
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
            alert('Gagal menampilkan detail jenis alat');
        }
    });
}
</script>

@endsection
<!-- /. section tail -->