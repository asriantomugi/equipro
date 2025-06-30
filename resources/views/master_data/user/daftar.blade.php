  
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
                <h3 class="card-title">DAFTAR USER</h3>

                <a class="btn btn-success btn-sm float-right" 
                   href="{{url('/master-data/user/tambah')}}" 
                   role="button"><i class="fas fa-plus"></i>&nbsp;&nbsp;&nbsp;Tambah</a>

              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example" class="table table-bordered table-striped">
                  <thead>
                    <tr class="table-condensed">
                      <th style="width: 10px"><center>NO.</center></th>
                      <th><center>NAMA</center></th>
                      <th><center>EMAIL</center></th>
                      <th><center>ROLE</center></th>
                      <th><center>STATUS</center></th>
                      <th style="width: 100px"></th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($daftar as $satu)
                    <tr class="table-condensed">
                      <td></td>
                      <td><center>{{ strtoupper($satu->name) }}</center></td>
                      <td><center>{{ $satu->email }}</center></td>
                      <td><center>{{ strtoupper($satu->role->nama) }}</center></td>
  @if($satu->status == 1)
                      <td><center><span class="badge bg-success">AKTIF</span></center></td>
  @else
                      <td><center><span class="badge bg-danger">TIDAK AKTIF</span></center></td>
  @endif
                      <td>
<!--
<form action="{{url('/user/detail')}}"
      method="post">
@csrf

<input type="text" name="id" value="{{ $satu->id }}" hidden>
<button type="submit" class="btn btn-primary btn-sm float-right">Test</button>
</form>
-->
                        <center>
    @if($satu->role_id != config('constants.role.super_admin'))
                          <a class="btn btn-info btn-sm" 
                             href="{{url('/master-data/user/edit/'.$satu->id)}}" 
                             role="button"
                             title="Edit Data"><i class="fas fa-pencil-alt"></i></a>
							 
						              <a class="btn btn-danger btn-sm" 
                             href="{{url('/master-data/user/password/reset/'.$satu->id)}}" 
                             role="button"
                             title="Reset Password"><i class="fas fa-lock"></i></a>
    @endif
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
          body: 'User baru telah berhasil ditambahkan'
        })
    @elseif(session()->get('notif') == 'edit_sukses')
      $(document).Toasts('create', {
          class: 'bg-success',
          title: 'Sukses!',
          body: 'Data user telah berhasil diubah'
        })
	@elseif(session()->get('notif') == 'password_sukses')
      $(document).Toasts('create', {
          class: 'bg-success',
          title: 'Sukses!',
          body: 'Password user telah berhasil di-reset'
        })
    @elseif(session()->get('notif') == 'tambah_gagal')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Error!',
          body: 'Gagal menambahkan user baru'
        })
    @elseif(session()->get('notif') == 'edit_gagal')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Error!',
          body: 'Gagal mengubah data user'
        })
	@elseif(session()->get('notif') == 'password_gagal')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Error!',
          body: 'Gagal me-reset password user'
        })
    @elseif(session()->get('notif') == 'user_null')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Error!',
          body: 'Gagal menampilkan data user'
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
        url : "{{url('/master-data/user/detail')}}",
        type: "POST",
        data : {id: id},
        success: function(data){

          //alert(data.user.detail.jabatan);

          $('#detail').empty();

          var row = '<div class="modal-header">';
              row += '<h4 class="modal-title">Detail User</h4>';
              row += '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
              row += '<span aria-hidden="true">&times;</span></button></div>';// modal header

              row += '<div class="modal-body">';
              row += "<table border='0' cellpadding='5px'>";            
              row += "<tr><th>Nama</th><td>:</td><td>"+ data.user.name.toUpperCase(); +"</td></tr>";            
              row += "<tr><th>Email</th><td>:</td><td>"+ data.user.email +"</td></tr>";
              row += "<tr><th>Role</th><td>:</td><td>"+ data.user.role.nama.toUpperCase(); +"</td></tr>";
              row += "<tr><th>Perusahaan</th><td>:</td><td>"+ data.perusahaan.nama.toUpperCase(); +"</td></tr>";

          if(data.user.detail.jabatan != null){
              row += "<tr><th>Jabatan</th><td>:</td><td>"+ data.user.detail.jabatan.toUpperCase(); +"</td></tr>";
          }else{
              row += "<tr><th>Jabatan</th><td>:</td><td></td></tr>";
          }   
              
          if(data.user.status == 1){
              row += "<tr><th>Status</th><td>:</td><td>AKTIF</td></tr>";
          }else{
              row += "<tr><th>Status</th><td>:</td><td>TIDAK AKTIF</td></tr>";
          }

          if(data.user.detail.alamat != null){
              row += "<tr><th>Alamat</th><td>:</td><td>"+ data.user.detail.alamat.toUpperCase(); +"</td></tr>";
          }else{
              row += "<tr><th>Alamat</th><td>:</td><td></td></tr>";
          }   
              
          if(data.user.detail.telepon != null){
              row += "<tr><th>Telepon</th><td>:</td><td>"+ data.user.detail.telepon +"</td></tr>";
          }else{
              row += "<tr><th>Telepon</th><td>:</td><td></td></tr>";
          }

          if(data.created_by != null){
              row += "<tr><th>Dibuat Oleh</th><td>:</td><td>"+ data.created_by.name.toUpperCase(); +"</td></tr>";
              row += "<tr><th>Dibuat Pada</th><td>:</td><td>"+ data.user.created_at +"</td></tr>";
          }else{
              row += "<tr><th>Dibuat Oleh</th><td>:</td><td></td></tr>";
              row += "<tr><th>Dibuat Pada</th><td>:</td><td></td></tr>";
          } 

          if(data.updated_by != null){
              row += "<tr><th>Update Terakhir Oleh</th><td>:</td><td>"+ data.updated_by.name.toUpperCase(); +"</td></tr>";
              row += "<tr><th>Update Terakhir Pada</th><td>:</td><td>"+ data.user.updated_at +"</td></tr>";
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
            alert('Gagal menampilkan detail user');
        }
    });
}
</script>

@endsection
<!-- /. section tail -->