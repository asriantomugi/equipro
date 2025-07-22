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
                        <h3 class="card-title">DAFTAR RIWAYAT LAPORAN</h3>
                        {{-- Tidak ada tombol tambah --}}
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 10px"><center>NO.</center></th>
                                        <th><center>NO LAPORAN</center></th>
                                        <th><center>LAYANAN</center></th>
                                        <th><center>FASILITAS</center></th>
                                        <th><center>LOKASI T.1</center></th>
                                        <th><center>LOKASI T.2</center></th>
                                        <th><center>LOKASI T.3</center></th>
                                        <th><center>STATUS</center></th>
                                        <th style="width: 10px"><center>ACTION</center></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($daftar->isEmpty())
                                        <tr>
                                            <td colspan="9" class="text-center">Tidak ada riwayat laporan yang tersedia.</td>
                                        </tr>
                                    @else
                                        @foreach ($daftar as $satu)
                                            <tr>
                                                <td><center>{{ $loop->iteration }}</center></td>
                                                <td>{{ strtoupper($satu->no_laporan) }}</td>
                                                <td>{{ strtoupper($satu->layanan->nama ?? '-') }}</td>
                                                <td>{{ strtoupper($satu->layanan->fasilitas->nama ?? '-') }}</td>
                                                <td>{{ strtoupper($satu->layanan->LokasiTk1->nama ?? '-') }}</td>
                                                <td>{{ strtoupper($satu->layanan->LokasiTk2->nama ?? '-') }}</td>
                                                <td>{{ strtoupper($satu->layanan->LokasiTk3->nama ?? '-') }}</td>
                                                <td><center>{!! $satu->status_label !!}</center></td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        {{-- Jika ingin hanya detail, hapus tombol edit di bawah --}}
                                                        <a class="btn btn-info btn-sm" 
                                                            href="{{ url('/logbook/laporan/edit/' . $satu->id) }}" 
                                                            role="button"
                                                            title="Lihat Detail">
                                                            <i class="fas fa-angle-double-right"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div> {{-- end table-responsive --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal untuk Detail -->
<div class="modal fade" id="modal_detail">
    <div class="modal-dialog">
        <div class="modal-content" id="detail"></div>
    </div>
</div>
@endsection

@section('tail')
<script>
$(function(){
    @if (session()->has('notif'))
        let message = '';
        let type = 'bg-success';
        let title = 'Sukses!';

        switch ("{{ session()->get('notif') }}") {
            case 'simpan_sukses':
                message = 'Data laporan telah berhasil disimpan';
                break;
            case 'simpan_gagal':
                message = 'Gagal menyimpan data laporan';
                type = 'bg-danger';
                title = 'Error!';
                break;
            case 'edit_gagal':
                message = 'Gagal mengubah data laporan';
                type = 'bg-danger';
                title = 'Error!';
                break;
            case 'edit_sukses':
                message = 'Data laporan telah berhasil diubah';
                break;
            case 'item_null':
                message = 'Gagal menampilkan data laporan';
                type = 'bg-danger';
                title = 'Error!';
                break;
        }

        if(message !== ''){
            $(document).Toasts('create', {
                class: type,
                title: title,
                body: message,
                autohide: true,
                delay: 3000
            });
        }
    @endif
});

// fungsi detail jika masih ingin popup modal detail
function detail(id) {
    $("#detail").load("/logbook/laporan/detail/" + id);
    $("#modal_detail").modal("show");
}
</script>
@endsection
