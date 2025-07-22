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
                        <div class="table-responsive"> {{-- Tambahan agar tabel bisa scroll horizontal --}}
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
                                            <td colspan="9" class="text-center">No data available in table</td>
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
                                
                                                        <button class="btn btn-secondary btn-sm"
                                                                onclick="detail('{{ $satu->id }}')"
                                                                title="Detail">
                                                            <i class="fas fa-angle-double-right"></i>
                                                        </button>

                                                        @if ($satu->status == config('constants.status_laporan.draft'))
                                                        <button class="btn btn-danger btn-sm"
                                                                onclick="konfirmasiHapus('{{ $satu->id }}')"
                                                                title="Hapus Draft">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                        @endif
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
<script type="text/javascript">
$(function(){
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
        @elseif(session()->get('notif') == 'item_null')
            $(document).Toasts('create', {
                class: 'bg-danger',
                title: 'Error!',
                body: 'Gagal menampilkan data laporan',
                autohide: true,
                delay: 3000
            })
        @endif
    @endif
});
</script>

<script type="text/javascript">
function konfirmasiHapus(laporan_id) {
    $('#isi_modal_hapus').empty();

    var html = '<form action="{{ route('logbook.laporan.hapus') }}" method="post">';
        html += '@csrf';
        html += '<div class="modal-body">';
        html += '<p><center>Ingin menghapus draft laporan ini?</center></p>';
        html += '<input type="text" name="id" value="'+ laporan_id +'" hidden>';
        html += '</div>';
        html += '<div class="modal-footer justify-content-between">';
        html += '<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Tidak</button>';
        html += '<button type="submit" class="btn btn-danger btn-sm">Hapus</button>';
        html += '</div>';
        html += '</form>';

    $("#isi_modal_hapus").append(html);
    $("#modal_hapus").modal('show');
}
</script>

<script type="text/javascript">
function detail(id) {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    const kondisi_peralatan_normal = {{ config('constants.kondisi_peralatan.Normal') }};
    const jenis_tindaklanjut_perbaikan = {{ config('constants.jenis_tindaklanjut.perbaikan') }};
    const kondisi_tindaklanjut_beroperasi = {{ config('constants.kondisi_tindaklanjut.beroperasi') }};

    $.ajax({
        url: "{{ url('/logbook/laporan/detail') }}",
        type: "POST",
        data: { id: id },
        success: function(data) {
            $('#detail').empty();

            console.log(data); // Debugging

            let html = '<div class="modal-header">';
            html += '<h4 class="modal-title">Detail Laporan</h4>';
            html += '<button type="button" class="close" data-dismiss="modal">&times;</button>';
            html += '</div>';

            html += '<div class="modal-body">';

            // ========== INFORMASI LAYANAN ==========
            html += '<h5>INFORMASI LAYANAN</h5>';
            const layanan = data.laporan.layanan;
            html += '<table cellpadding="6">';
            html += `<tr><th>Kode Layanan</th><td>:</td><td>${layanan.kode ?? '-'}</td></tr>`;
            html += `<tr><th>Nama Layanan</th><td>:</td><td>${layanan.nama ?? '-'}</td></tr>`;
            html += `<tr><th>Fasilitas</th><td>:</td><td>${layanan.fasilitas?.kode ?? '-'} - ${layanan.fasilitas?.nama ?? '-'}</td></tr>`;
            html += `<tr><th>Lokasi Tingkat I</th><td>:</td><td>${layanan.lokasi_tk1?.kode ?? '-'} - ${layanan.lokasi_tk1?.nama ?? '-'}</td></tr>`;
            html += `<tr><th>Lokasi Tingkat II</th><td>:</td><td>${layanan.lokasi_tk2?.kode ?? '-'} - ${layanan.lokasi_tk2?.nama ?? '-'}</td></tr>`;
            html += `<tr><th>Lokasi Tingkat III</th><td>:</td><td>${layanan.lokasi_tk3?.kode ?? '-'} - ${layanan.lokasi_tk3?.nama ?? '-'}</td></tr>`;
            html += `<tr><th>Kondisi Saat Ini</th><td>:</td><td>${data.laporan.kondisi_layanan_temp ? "<span class='badge bg-success'>SERVICEABLE</span>" : "<span class='badge bg-danger'>UNSERVICEABLE</span>"}</td></tr>`;
            html += '</table>';
            html += '<div class="mb-3"></div>';

        

            // ========== INFORMASI GANGGUAN ==========
            html += '<h5 class="mb-1">INFORMASI GANGGUAN</h5>';
            if (data.laporan.jenis == 1) {
                html += '<table class="table table-bordered table-striped table-sm">';
                html += '<thead><tr><th>No</th><th>Kode</th><th>Nama Peralatan</th><th>Waktu</th><th>Deskripsi</th><th>Kondisi</th></tr></thead><tbody>';
                if (data.detailGangguanPeralatan.length > 0) {
                    data.detailGangguanPeralatan.forEach((g, i) => {
                        html += `<tr>
                            <td>${i + 1}</td>
                            <td>${g.peralatan?.kode ?? '-'}</td>
                            <td>${g.peralatan?.nama ?? '-'}</td>
                            <td>${formatDate(g.waktu)}</td>
                            <td>${g.deskripsi ?? '-'}</td>
                            <td>${g.kondisi == kondisi_peralatan_normal ? '<span class="badge bg-success">NORMAL</span>' : '<span class="badge bg-danger">RUSAK</span>'}</td>
                        </tr>`;
                    });
                } else {
                    html += '<tr><td colspan="6" class="text-center">Tidak ada data gangguan peralatan</td></tr>';
                }
                html += '</tbody></table>';
            } else {
                html += '<table class="table table-bordered table-striped table-sm">';
                html += '<thead><tr><th>No</th><th>Waktu</th><th>Deskripsi</th></tr></thead><tbody>';
                html += `<tr>
                    <td>1</td>
                    <td>${formatDate(data.gangguanNonPeralatan?.waktu)}</td>
                    <td>${data.gangguanNonPeralatan?.deskripsi ?? '-'}</td>
                </tr>`;
                html += '</tbody></table>';
                html += '<div class="mb-4"></div>';
            }

           

            // ========== INFORMASI TINDAKLANJUT ==========
            html += '<h5 class="mb-1">INFORMASI TINDAKLANJUT</h5>';
            if (data.laporan.jenis == 1 && data.penggantian.length > 0) {
                html += '<table class="table table-bordered table-striped table-sm">';
                html += '<thead><tr><th>No</th><th>Jenis</th><th>Peralatan Lama</th><th>Peralatan Baru</th><th>Waktu</th><th>Deskripsi</th><th>Kondisi</th></tr></thead><tbody>';
                data.penggantian.forEach((p, i) => {
                    let jenis = p.tindaklanjut?.jenis_tindaklanjut == jenis_tindaklanjut_perbaikan ? 'PERBAIKAN' : 'PENGGANTIAN';
                    let kondisi = p.tindaklanjut?.kondisi == kondisi_tindaklanjut_beroperasi ? '<span class="badge bg-success">BEROPERASI</span>' : '<span class="badge bg-danger">GANGGUAN</span>';
                    html += `<tr>
                        <td>${i + 1}</td>
                        <td>${jenis}</td>
                        <td>${p.peralatanLama?.nama ?? '-'}</td>
                        <td>${p.peralatanBaru?.nama ?? '-'}</td>
                        <td>${formatDate(p.tindaklanjut?.waktu)}</td>
                        <td>${p.tindaklanjut?.deskripsi ?? '-'}</td>
                        <td>${kondisi}</td>
                    </tr>`;
                });
                html += '</tbody></table>';
            } else if (data.tindaklanjut) {
                let kondisi = data.tindaklanjut.kondisi == kondisi_tindaklanjut_beroperasi ? '<span class="badge bg-success">BEROPERASI</span>' : '<span class="badge bg-danger">GANGGUAN</span>';
                html += '<table class="table table-bordered table-striped table-sm">';
                html += '<thead><tr><th>No</th><th>Waktu</th><th>Deskripsi</th><th>Kondisi</th></tr></thead><tbody>';
                html += `<tr>
                    <td>1</td>
                    <td>${formatDate(data.tindaklanjut?.waktu)}</td>
                    <td>${data.tindaklanjut?.deskripsi ?? '-'}</td>
                    <td>${kondisi}</td>
                </tr>`;
                html += '</tbody></table>';
            } else {
                html += '<p class="text-center">Tidak ada data tindak lanjut.</p>';
            }
            html += '<div class="mb-4"></div>';

             // ========== INFORMASI PEMBUAT & UPDATE ==========
html += '<h5 class="mb-1">KETERANGAN</h5>';
html += '<table cellpadding="6">';

if (data.laporan.get_created_name) {
    html += `<tr><th>Dibuat Oleh</th><td>:</td><td>${data.laporan.get_created_name?.name?.toUpperCase() ?? '-'}</td></tr>`;
    html += `<tr><th>Dibuat Pada</th><td>:</td><td>${data.laporan.created_at ?? '-'}</td></tr>`;
} else {
    html += `<tr><th>Dibuat Oleh</th><td>:</td><td>-</td></tr>`;
    html += `<tr><th>Dibuat Pada</th><td>:</td><td>-</td></tr>`;
}

if (data.laporan.get_updated_name) {
    html += `<tr><th>Update Terakhir Oleh</th><td>:</td><td>${data.laporan.get_updated_name?.name?.toUpperCase() ?? '-'}</td></tr>`;
    html += `<tr><th>Update Terakhir Pada</th><td>:</td><td>${data.laporan.updated_at ?? '-'}</td></tr>`;
} else {
    html += `<tr><th>Update Terakhir Oleh</th><td>:</td><td>-</td></tr>`;
    html += `<tr><th>Update Terakhir Pada</th><td>:</td><td>-</td></tr>`;
}

            html += '</table>';
            html += '</div>';
            html += '<div class="modal-footer"><button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Tutup</button></div>';

            $('#detail').html(html);
            $('#modal_detail').modal('show');
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            alert('Gagal memuat detail laporan. Silakan cek console.');
        }
    });
}

function formatDate(date) {
    if (!date) return '-';
    let d = new Date(date);
    if (isNaN(d)) return '-';
    return d.getDate().toString().padStart(2, '0') + '-' +
           (d.getMonth() + 1).toString().padStart(2, '0') + '-' +
           d.getFullYear() + ' ' +
           d.getHours().toString().padStart(2, '0') + ':' +
           d.getMinutes().toString().padStart(2, '0');
}
</script>
@endsection
