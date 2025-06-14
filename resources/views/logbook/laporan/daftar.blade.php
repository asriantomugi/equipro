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
                        <a href="{{ url('/logbook/laporan/tambah/step1') }}" class="btn btn-success btn-sm float-right" role="button">
                            <i class="fas fa-plus"></i>&nbsp;&nbsp;&nbsp;Tambah
                        </a>
                    </div>
                    <div class="card-body">
                        <table id="laporan" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 10px"><center>NO.</center></th>
                                    <th><center>NO TICKET</center></th>
                                    <th><center>NAMA PERALATAN</center></th>
                                    <th><center>FASILITAS</center></th>
                                    <th><center>LOKASI T.1</center></th>
                                    <th><center>LOKASI T.2</center></th>
                                    <th><center>LOKASI T.3</center></th>
                                    <th style="width: 10px"><center>ACTION</center></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($daftar->isEmpty())
                                    <tr>
                                        <td colspan="8" class="text-center">Belum ada data laporan.</td>
                                    </tr>
                                @else
                                    @foreach ($daftar as $satu)
                                        <tr>
                                            <td><center>{{ $loop->iteration }}</center></td>
                                            <td>{{ strtoupper($satu->no_laporan) }}</td>
                                            <td>{{ strtoupper($satu->peralatan->nama ?? '-') }}</td>
                                            <td>{{ strtoupper($satu->fasilitas->nama ?? '-') }}</td>
                                            <td>{{ strtoupper($satu->getLokasiTk1->nama ?? '-') }}</td>
                                            <td>{{ strtoupper($satu->getLokasiTk2->nama ?? '-') }}</td>
                                            <td>{{ strtoupper($satu->getLokasiTk3->nama ?? '-') }}</td>
                                            <td>
                                                <center>
                                                <a class="btn btn-info btn-sm" 
                                                    href="{{url('/logbook/laporan/edit/'.$satu->id)}}" 
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
                                @endif
                            </tbody>
                        </table>
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
<script type="text/javascript">
    $(document).ready(function () {
        $('#laporan').DataTable();

        @if (session()->has('notif'))
            let message = '';
            let type = '';

            switch ("{{ session('notif') }}") {
                case 'tambah_sukses':
                    message = 'Laporan baru berhasil ditambahkan.';
                    type = 'bg-success';
                    break;
                case 'edit_sukses':
                    message = 'Laporan berhasil diubah.';
                    type = 'bg-success';
                    break;
                case 'tambah_gagal':
                    message = 'Gagal menambahkan Laporan baru.';
                    type = 'bg-danger';
                    break;
                case 'edit_gagal':
                    message = 'Gagal mengubah data Laporan.';
                    type = 'bg-danger';
                    break;
            }

            if (message) {
                $(document).Toasts('create', {
                    class: type,
                    title: 'Notifikasi',
                    body: message
                });
            }
        @endif
    });
</script>
@endsection
