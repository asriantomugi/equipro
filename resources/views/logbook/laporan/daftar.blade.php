@extends('logbook.main')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-6">
                <div class="card">

                    <div class="card-header">
                        <h3 class="card-title">DAFTAR LAPORAN</h3>
                        <a href="{{ route('logbook.laporan.tambah.step1.form') }}"
                           class="btn btn-success btn-sm float-right" 
                           role="button">
                           <i class="fas fa-plus"></i>&nbsp;&nbsp;&nbsp;Tambah
                        </a>
                    </div> <!-- card-header -->

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th><center>NO</center></th>
                                        <th><center>NO LAPORAN</center></th>
                                        <th><center>LAYANAN</center></th>
                                        <th><center>FASILITAS</center></th>
                                        <th><center>LOKASI TK 1</center></th>
                                        <th><center>LOKASI TK 2</center></th>
                                        <th><center>LOKASI TK 3</center></th>
                                        <th><center>WAKTU OPEN</center></th>
                                        <th><center>STATUS</center></th>
                                        <th><center></center></th>
                                    </tr>
                                </thead>
                                <tbody>
                        @foreach ($daftar as $satu)
                                    <tr>
                                        <td><center>{{ $loop->iteration }}</center></td>
                                        <td>{{ strtoupper($satu->no_laporan) }}</td>
                                        <td>{{ strtoupper($satu->layanan->nama ?? '-') }}</td>
                                        <td>{{ strtoupper($satu->layanan->fasilitas->nama ?? '-') }}</td>
                                        <td>{{ strtoupper($satu->layanan->LokasiTk1->nama ?? '-') }}</td>
                                        <td>{{ strtoupper($satu->layanan->LokasiTk2->nama ?? '-') }}</td>
                                        <td>{{ strtoupper($satu->layanan->LokasiTk3->nama ?? '-') }}</td>
                                        <td><center>
                            @if($satu->waktu_open)
                                            {{ \Carbon\Carbon::parse($satu->waktu_open)->format('d-m-Y H:i') }}
                            @else
                                            -
                            @endif
                                        </center></td>
                                        
  @if($satu->status == config('constants.status_laporan.draft'))
                                        <td><center><span class="badge bg-warning">DRAFT</span></center></td>
  @elseif($satu->status == config('constants.status_laporan.open'))
                                        <td><center><span class="badge bg-danger">OPEN</span></center></td>
  @elseif($satu->status == config('constants.status_laporan.close'))
                                        <td><center><span class="badge bg-success">CLOSE</span></center></td>
  @endif
                      
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                {{-- Tombol Edit Berdasarkan Status --}}
                            @if ($satu->status == config('constants.status_laporan.draft'))
                                                {{-- Edit untuk Draft (status = 1) - dimulai dari step 2 --}}
                                                <a href="{{ route('laporan.edit.step2', ['id' => $satu->id]) }}" 
                                                   class="btn btn-info btn-sm mr-1" 
                                                   role="button" 
                                                   title="Edit Draft">
                                                   <i class="fas fa-pencil-alt"></i>
                                                </a>
                            @elseif ($satu->status == config('constants.status_laporan.open') && $satu->kondisi == 0)
                                                {{-- Edit untuk Open (status = 2) dengan kondisi Unserviceable - dimulai dari step 3 --}}
                                                <a href="{{ route('logbook.laporan.edit.step3', ['id' => $satu->id]) }}" 
                                                   class="btn btn-info btn-sm mr-1" 
                                                   role="button" 
                                                   title="Tindak Lanjuti">
                                                   <i class="fas fa-pencil-alt"></i>
                                                </a>
                            @endif
                        
                                                <button type="button" 
                                                        class="btn btn-secondary btn-sm mr-1 btn-detail"
                                                        data-id="{{ $satu->id }}"
                                                        title="Detail">
                                                        <i class="fas fa-angle-double-right"></i>
                                                </button>

                            @if ($satu->status == config('constants.status_laporan.draft'))
                                                <button class="btn btn-danger btn-sm" 
                                                        onclick="hapus('{{ $satu->id }}')"
                                                        title="Hapus">
                                                        <i class="fas fa-trash-alt"></i>
                                                </button>
                            @endif
                                            </div>
                                        </td>
                                    </tr>
                        @endforeach
                                </tbody>
                            </table>
                        </div> <!-- table-responsive -->
                    </div> <!-- card-body -->

                </div> <!-- card -->
            </div> <!-- col-lg-12 -->
        </div> <!-- row -->
    </div> <!-- container-fluid -->
</section>

<!-- Modal untuk Detail -->
<div class="modal fade" id="modal_detail">
    <div class="modal-dialog modal-xl"> 
        <div class="modal-content" id="detail"></div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="modal_hapus">
    <div class="modal-dialog">
        <div class="modal-content" id="isi_modal_hapus">
        </div>
    </div>
</div>

@endsection

@section('tail')

<!-- javascript untuk pop up notifikasi -->
<script type="text/javascript">
  @if (session()->has('notif'))
    @if (session()->get('notif') == 'tambah_sukses')
      $(document).Toasts('create', {
          class: 'bg-success',
          title: 'Sukses!',
          body: 'Laporan baru telah berhasil ditambahkan',
          autohide: true,
          delay: 3000
        })
    @elseif(session()->get('notif') == 'draft_sukses')
      $(document).Toasts('create', {
          class: 'bg-success',
          title: 'Sukses!',
          body: 'Draft laporan telah berhasil disimpan',
          autohide: true,
          delay: 3000
        })
    @elseif(session()->get('notif') == 'hapus_sukses')
      $(document).Toasts('create', {
          class: 'bg-success',
          title: 'Sukses!',
          body: 'Draft laporan telah berhasil dihapus',
          autohide: true,
          delay: 3000
        })
    @elseif(session()->get('notif') == 'simpan_sukses')
      $(document).Toasts('create', {
          class: 'bg-success',
          title: 'Sukses!',
          body: 'Data laporan telah berhasil disimpan',
          autohide: true,
          delay: 3000
        })
    @elseif(session()->get('notif') == 'tambah_gagal')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Error!',
          body: 'Gagal menambahkan laporan baru',
          autohide: true,
          delay: 3000
        })
    @elseif(session()->get('notif') == 'simpan_gagal')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Error!',
          body: 'Gagal menyimpan data laporan',
          autohide: true,
          delay: 3000
        })
    @elseif(session()->get('notif') == 'hapus_gagal')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Error!',
          body: 'Gagal menghapus draft laporan',
          autohide: true,
          delay: 3000
        })
    @elseif(session()->get('notif') == 'laporan_null')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Error!',
          body: 'Gagal menampilkan data laporan',
          autohide: true,
          delay: 3000
        })
    @elseif(session()->get('notif') == 'layanan_null')
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

<!-- javascript untuk menampilkan modal untuk menghapus laporan -->
<script type="text/javascript">
  function hapus(laporan_id) {
    //alert(id);
    $('#isi_modal_hapus').empty();;

      var html = '<form action="{{ route('logbook.laporan.hapus') }}" method="post">';
          html += '@csrf';
          html += '<div class="modal-body">';
          html += '<p><center>Ingin menghapus draft laporan ini?</center></p>';
          html += '<input type="text" name="id" value="'+laporan_id+'" hidden>';
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

@endsection

