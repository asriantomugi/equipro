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
                                        <th><center>WAKTU OPEN</center></th>
                                        <th><center>WAKTU CLOSE</center></th>
                                        <th><center>STATUS</center></th>
                                        <th style="width: 10px"><center>ACTION</center></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($daftar->isEmpty())
                                        <tr>
                                            <td colspan="11" class="text-center">No data available in table</td>
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
                                                <td><center>
                                                    @if($satu->waktu_open)
                                                        {{ \Carbon\Carbon::parse($satu->waktu_open)->format('d-m-Y H:i') }}
                                                    @else
                                                        -
                                                    @endif
                                                </center></td>
                                                <td><center>
                                                    @if($satu->waktu_close)
                                                        {{ \Carbon\Carbon::parse($satu->waktu_close)->format('d-m-Y H:i') }}
                                                    @else
                                                        -
                                                    @endif
                                                </center></td>
                                                <td><center>{!! $satu->status_label !!}</center></td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button type="button" 
                                                                class="btn btn-info btn-sm btn-detail"
                                                                data-id="{{ $satu->id }}"
                                                                title="Lihat Detail">
                                                            <i class="fas fa-angle-double-right"></i>
                                                        </button>
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
@endsection

@section('tail')
<script>
// Updated constants to match your config exactly
var KONDISI_GANGGUAN_PERALATAN_BEROPERASI = '1';
var KONDISI_GANGGUAN_PERALATAN_GANGGUAN = '0';
var JENIS_TINDAKLANJUT_PERBAIKAN = true;
var JENIS_TINDAKLANJUT_PENGGANTIAN = false;
var KONDISI_TINDAKLANJUT_BEROPERASI = true;
var KONDISI_TINDAKLANJUT_GANGGUAN = false;

$(document).ready(function(){
    console.log('Document ready - JavaScript loaded successfully');
    
    // Initialize DataTable if using DataTables
    if ($.fn.DataTable && $('#example').length) {
        if (!$.fn.DataTable.isDataTable('#example')) {
            $('#example').DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                }
            });
        }
    }
    
    // Set CSRF Token untuk AJAX
    $.ajaxSetup({
        headers: { 
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
        }
    });
    
    // Event listener for button detail
    $(document).on('click', '.btn-detail', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        console.log('Detail button clicked for ID:', id);
        
        if (id && id !== undefined && id !== null && id !== '') {
            detail(id);
        } else {
            console.error('ID tidak ditemukan atau tidak valid:', id);
            alert('Error: ID laporan tidak valid');
        }
    });

    // Toast notifications
    @if (session()->has('notif'))
        if (typeof $(document).Toasts === 'function') {
            let message = '';
            let type = 'bg-success';
            let title = 'Sukses!';

            switch ("{{ session()->get('notif') }}") {
                case 'tambah_sukses':
                    message = 'Data laporan telah berhasil disimpan';
                    break;
                case 'tambah_gagal':
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
        }
    @endif
});

// Enhanced detail function with better error handling
function detail(id) {
    console.log('detail function called with ID:', id);
    
    // More thorough validation
    if (!id || id === undefined || id === null || id === '' || isNaN(id)) {
        console.error('ID is invalid:', id);
        alert('Error: ID laporan tidak valid');
        return;
    }
    
    // Set loading state
    $('#detail').html(
        '<div class="modal-header">' +
        '<h4 class="modal-title">Loading...</h4>' +
        '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
        '</div>' +
        '<div class="modal-body">' +
        '<div class="text-center">' +
        '<i class="fas fa-spinner fa-spin fa-2x"></i>' +
        '<p class="mt-2">Memuat data laporan...</p>' +
        '</div>' +
        '</div>'
    );
    $('#modal_detail').modal('show');
    
    // AJAX Request with better error handling
    $.ajax({
        url: "{{ url('/logbook/laporan/detail') }}",
        type: "POST",
        data: { 
            id: parseInt(id), // Ensure ID is integer
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        timeout: 15000,
        beforeSend: function() {
            console.log('Sending AJAX request for ID:', id);
        },
        success: function(data) {
            console.log('AJAX Success - Response data:', data);
            
            if (!data || !data.laporan) {
                throw new Error('Data laporan tidak valid');
            }
            
            renderDetailModal(data);
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error Details:');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('Response Text:', xhr.responseText);
            console.error('Status Code:', xhr.status);
            
            renderErrorModal(xhr, status, error);
        }
    });
}

// Helper function untuk format tanggal - DIPERBAIKI TOTAL
function formatDate(date) {
    console.log('formatDate input:', date, 'type:', typeof date);
    
    if (!date || date === '-' || date === null || date === undefined || date === '') {
        return '-';
    }
    
    let d;
    
    try {
        // Handle different date formats
        if (typeof date === 'string') {
            // Cek apakah sudah dalam format yang diinginkan (dd-mm-yyyy hh:mm:ss)
            if (/^\d{2}-\d{2}-\d{4} \d{2}:\d{2}:\d{2}$/.test(date)) {
                return date.substring(0, 16); // Ambil hanya sampai menit (dd-mm-yyyy hh:mm)
            }
            
            // Handle ISO string atau format lainnya
            d = new Date(date);
        } else if (date instanceof Date) {
            d = date;
        } else if (typeof date === 'object') {
            // Handle Laravel Carbon object format atau object datetime
            if (date.date) {
                d = new Date(date.date);
            } else if (date.formatted) {
                return date.formatted;
            } else {
                // Coba convert object ke string dulu
                d = new Date(date.toString());
            }
        } else {
            console.warn('Unknown date format:', date);
            return '-';
        }
        
        if (isNaN(d.getTime())) {
            console.warn('Invalid date:', date);
            return '-';
        }
        
        // Format: dd-mm-yyyy hh:mm
        let formatted = d.getDate().toString().padStart(2, '0') + '-' +
               (d.getMonth() + 1).toString().padStart(2, '0') + '-' +
               d.getFullYear() + ' ' +
               d.getHours().toString().padStart(2, '0') + ':' +
               d.getMinutes().toString().padStart(2, '0');
        
        console.log('formatDate output:', formatted);
        return formatted;
        
    } catch (error) {
        console.error('Error formatting date:', error, 'Input:', date);
        return '-';
    }
}
// Updated renderDetailModal function - HANDLE UPDATED BY YANG KOSONG
function renderDetailModal(data) {
    console.log('=== FULL DEBUG DATA ===');
    console.log('Full response:', data);
    console.log('Laporan created_at:', data.laporan.created_at);
    console.log('Laporan updated_at:', data.laporan.updated_at);
    console.log('Laporan created_by:', data.laporan.created_by);
    console.log('Laporan updated_by:', data.laporan.updated_by);

    let html = '<div class="modal-header">';
    html += '<h4 class="modal-title">Detail Laporan - ' + (data.laporan.no_laporan || 'N/A') + '</h4>';
    html += '<button type="button" class="close" data-dismiss="modal">&times;</button>';
    html += '</div>';
    html += '<div class="modal-body">';

    // ========== INFORMASI LAYANAN ==========
    html += '<h5>INFORMASI LAYANAN</h5>';
    const layanan = data.laporan.layanan;
    html += '<div class="table-responsive">';
    html += '<table class="table table-sm">';
    html += '<tr><th width="200">Fasilitas</th><td>:</td><td>' + (layanan?.fasilitas?.kode || '-') + ' - ' + (layanan?.fasilitas?.nama || '-') + '</td></tr>';
    html += '<tr><th>Kode Layanan</th><td>:</td><td>' + (layanan?.kode || '-') + '</td></tr>';
    html += '<tr><th>Nama Layanan</th><td>:</td><td>' + (layanan?.nama || '-') + '</td></tr>';
    html += '<tr><th>Lokasi Tingkat I</th><td>:</td><td>' + (layanan?.lokasi_tk1?.kode || '-') + ' - ' + (layanan?.lokasi_tk1?.nama || '-') + '</td></tr>';
    html += '<tr><th>Lokasi Tingkat II</th><td>:</td><td>' + (layanan?.lokasi_tk2?.kode || '-') + ' - ' + (layanan?.lokasi_tk2?.nama || '-') + '</td></tr>';
    html += '<tr><th>Lokasi Tingkat III</th><td>:</td><td>' + (layanan?.lokasi_tk3?.kode || '-') + ' - ' + (layanan?.lokasi_tk3?.nama || '-') + '</td></tr>';
    html += '<tr><th>Kondisi Saat Ini</th><td>:</td><td>' + (data.laporan.kondisi_layanan_temp ? "<span class='badge badge-success'>SERVICEABLE</span>" : "<span class='badge badge-danger'>UNSERVICEABLE</span>") + '</td></tr>';
    html += '</table>';
    html += '</div>';
    html += '<hr>';

    // ========== INFORMASI GANGGUAN ==========
    html += '<h5>INFORMASI GANGGUAN</h5>';
    if (data.laporan.jenis == 1) {
        // Gangguan Peralatan
        html += '<div class="table-responsive">';
        html += '<table class="table table-bordered table-striped table-sm">';
        html += '<thead><tr><th><center>No</center></th><th><center>Kode</center></th><th><center>Nama Peralatan</center></th><th><center>Waktu</center></th><th><center>Deskripsi</center></th><th><center>Kondisi</center></th></tr></thead><tbody>';
        
        if (data.detailGangguanPeralatan && data.detailGangguanPeralatan.length > 0) {
            data.detailGangguanPeralatan.forEach(function(g, i) {
                html += '<tr>';
                html += '<td><center>' + (i + 1) + '</center></td>';
                html += '<td><center>' + (g.peralatan?.kode?.toUpperCase() || '-') + '</center></td>';
                html += '<td><center>' + (g.peralatan?.nama?.toUpperCase() || '-') + '</center></td>';
                
                let waktuGangguan = g.waktu || g.created_at || g.updated_at;
                html += '<td><center>' + formatDate(waktuGangguan) + '</center></td>';
                
                html += '<td><center>' + (g.deskripsi?.toUpperCase() || '-') + '</center></td>';
                html += '<td><center>' + (g.kondisi === KONDISI_GANGGUAN_PERALATAN_BEROPERASI || g.kondisi === '1' || g.kondisi === 1 ? '<span class="badge badge-success">BEROPERASI</span>' : '<span class="badge badge-danger">GANGGUAN</span>') + '</center></td>';
                html += '</tr>';
            });
        } else {
            html += '<tr><td colspan="6" class="text-center">Tidak ada data gangguan peralatan</td></tr>';
        }
        html += '</tbody></table>';
        html += '</div>';
    } else {
        // Gangguan Non-Peralatan
        html += '<div class="table-responsive">';
        html += '<table class="table table-bordered table-striped table-sm">';
        html += '<thead><tr><th><center>No</center></th><th><center>Waktu</center></th><th><center>Deskripsi</center></th></tr></thead><tbody>';
        html += '<tr>';
        html += '<td><center>1</center></td>';
        
        let gangguanNonPeralatan = data.gangguanNonPeralatan || data.laporan.gangguan_non_peralatan;
        let waktuNonPeralatan = gangguanNonPeralatan?.waktu || gangguanNonPeralatan?.created_at || gangguanNonPeralatan?.updated_at;
        
        html += '<td><center>' + formatDate(waktuNonPeralatan) + '</center></td>';
        html += '<td><center>' + (gangguanNonPeralatan?.deskripsi?.toUpperCase() || '-') + '</center></td>';
        html += '</tr>';
        html += '</tbody></table>';
        html += '</div>';
    }
    html += '<hr>';

    // ========== INFORMASI TINDAKLANJUT ==========
    html += '<h5>TINDAK LANJUT</h5>';
    
    if (data.laporan.jenis == 1) {
        // Untuk gangguan peralatan
        const adaPenggantian = data.penggantian && data.penggantian.length > 0;
        const adaPerbaikan = data.perbaikan && data.perbaikan.length > 0;
        
        if (adaPenggantian || adaPerbaikan) {
            html += '<div class="table-responsive">';
            html += '<table class="table table-bordered table-striped table-sm">';
            html += '<thead><tr><th><center>No</center></th><th><center>Jenis</center></th><th><center>Peralatan Lama</center></th><th><center>Peralatan Baru</center></th><th><center>Waktu</center></th><th><center>Deskripsi</center></th><th><center>Kondisi</center></th></tr></thead><tbody>';
            
            let no = 1;
            
            // Tampilkan data penggantian
            if (adaPenggantian) {
                data.penggantian.forEach(function(item) {
                    let kondisi = (item.tindaklanjut?.kondisi == 1 || item.tindaklanjut?.kondisi == '1' || item.tindaklanjut?.kondisi === true) ? '<span class="badge badge-success">BEROPERASI</span>' : '<span class="badge badge-danger">GANGGUAN</span>';
                    let waktuTindakLanjut = item.tindaklanjut?.waktu || item.tindaklanjut?.created_at || item.created_at;
                    
                    html += '<tr>';
                    html += '<td><center>' + (no++) + '</center></td>';
                    html += '<td><center><span class="badge badge-warning">PENGGANTIAN</span></center></td>';
                    html += '<td><center>' + (item.peralatan_lama?.nama?.toUpperCase() || '-') + '</center></td>';
                    html += '<td><center>' + (item.peralatan_baru?.nama?.toUpperCase() || '-') + '</center></td>';
                    html += '<td><center>' + formatDate(waktuTindakLanjut) + '</center></td>';
                    html += '<td><center>' + (item.tindaklanjut?.deskripsi?.toUpperCase() || '-') + '</center></td>';
                    html += '<td><center>' + kondisi + '</center></td>';
                    html += '</tr>';
                });
            }
            
            // Tampilkan data perbaikan
            if (adaPerbaikan) {
                data.perbaikan.forEach(function(item) {
                    let kondisi = (item.kondisi == 1 || item.kondisi == '1' || item.kondisi === true) ? '<span class="badge badge-success">BEROPERASI</span>' : '<span class="badge badge-danger">GANGGUAN</span>';
                    let waktuPerbaikan = item.waktu || item.created_at;
                    
                    html += '<tr>';
                    html += '<td><center>' + (no++) + '</center></td>';
                    html += '<td><center><span class="badge badge-info">PERBAIKAN</span></center></td>';
                    html += '<td><center>' + (item.peralatan?.nama?.toUpperCase() || '-') + '</center></td>';
                    html += '<td><center>-</center></td>';
                    html += '<td><center>' + formatDate(waktuPerbaikan) + '</center></td>';
                    html += '<td><center>' + (item.deskripsi?.toUpperCase() || '-') + '</center></td>';
                    html += '<td><center>' + kondisi + '</center></td>';
                    html += '</tr>';
                });
            }
            
            html += '</tbody></table>';
            html += '</div>';
        } else {
            html += '<div class="alert alert-info text-center">Tidak ada data tindak lanjut yang tersedia.</div>';
        }
    } else {
        // Untuk gangguan non-peralatan
        if (data.semuaTindakLanjutNonPeralatan && data.semuaTindakLanjutNonPeralatan.length > 0) {
            html += '<div class="table-responsive">';
            html += '<table class="table table-bordered table-striped table-sm">';
            html += '<thead><tr><th><center>No</center></th><th><center>Jenis</center></th><th><center>Waktu</center></th><th><center>Deskripsi</center></th><th><center>Kondisi</center></th></tr></thead><tbody>';
            
            data.semuaTindakLanjutNonPeralatan.forEach(function(item, i) {
                let kondisi = (item.kondisi == 1 || item.kondisi == '1' || item.kondisi === true) ? '<span class="badge badge-success">BEROPERASI</span>' : '<span class="badge badge-danger">GANGGUAN</span>';
                let waktuTindakLanjut = item.waktu || item.created_at;
                
                html += '<tr>';
                html += '<td><center>' + (i + 1) + '</center></td>';
                html += '<td><center><span class="badge badge-info">PERBAIKAN</span></center></td>';
                html += '<td><center>' + formatDate(waktuTindakLanjut) + '</center></td>';
                html += '<td><center>' + (item.deskripsi?.toUpperCase() || '-') + '</center></td>';
                html += '<td><center>' + kondisi + '</center></td>';
                html += '</tr>';
            });
            
            html += '</tbody></table>';
            html += '</div>';
        } else {
            html += '<div class="alert alert-info text-center">Tidak ada data tindak lanjut yang tersedia.</div>';
        }
    }
    html += '<hr>';

    // ========== INFORMASI PEMBUAT & UPDATE - HANDLE UPDATED BY KOSONG ==========
    html += '<h5>KETERANGAN</h5>';
    html += '<div class="table-responsive">';
    html += '<table class="table table-sm">';

    // Dibuat Oleh
    if (data.laporan.get_created_name || data.laporan.getCreatedName) {
        let createdUser = data.laporan.get_created_name || data.laporan.getCreatedName;
        html += '<tr><th width="200">Dibuat Oleh</th><td>:</td><td>' + (createdUser?.name?.toUpperCase() || '-') + '</td></tr>';
    } else {
        html += '<tr><th width="200">Dibuat Oleh</th><td>:</td><td>-</td></tr>';
    }
    
    // Dibuat Pada
    html += '<tr><th>Dibuat Pada</th><td>:</td><td>' + formatDate(data.laporan.created_at) + '</td></tr>';

    // LOGIKA KHUSUS UNTUK UPDATED BY DAN UPDATED AT
    let hasUpdatedBy = data.laporan.get_updated_name || data.laporan.getUpdatedName || data.laporan.updated_by;
    let hasBeenUpdated = data.laporan.updated_at && data.laporan.created_at && 
                        (new Date(data.laporan.updated_at).getTime() > new Date(data.laporan.created_at).getTime());

    console.log('Has updated by:', hasUpdatedBy);
    console.log('Has been updated:', hasBeenUpdated);
    console.log('Updated by value:', data.laporan.updated_by);

    if (hasBeenUpdated) {
        // Jika ada perubahan waktu update
        if (hasUpdatedBy) {
            // Ada user yang update
            let updatedUser = data.laporan.get_updated_name || data.laporan.getUpdatedName;
            html += '<tr><th>Update Terakhir Oleh</th><td>:</td><td>' + (updatedUser?.name?.toUpperCase() || '-') + '</td></tr>';
            html += '<tr><th>Update Terakhir Pada</th><td>:</td><td>' + formatDate(data.laporan.updated_at) + '</td></tr>';
        } else {
            // Tidak ada user yang update (kemungkinan update otomatis sistem)
            html += '<tr><th>Update Terakhir Oleh</th><td>:</td><td><span class="text-muted">SISTEM (Auto Update)</span></td></tr>';
            html += '<tr><th>Update Terakhir Pada</th><td>:</td><td>' + formatDate(data.laporan.updated_at) + '</td></tr>';
        }
    } else {
        // Belum pernah di-update
        html += '<tr><th>Update Terakhir Oleh</th><td>:</td><td><span class="text-muted">Belum pernah diupdate</span></td></tr>';
        html += '<tr><th>Update Terakhir Pada</th><td>:</td><td><span class="text-muted">-</span></td></tr>';
    }

    html += '</table>';
    html += '</div>';
    html += '</div>';
    html += '<div class="modal-footer justify-content-between">';
    html += '<button type="button" class="btn btn-default" data-dismiss="modal">Kembali</button>';
    html += '</div>';

    $('#detail').html(html);
}
// Log informasi saat halaman dimuat
console.log('Riwayat Laporan Detail Script Loaded');
</script>
@endsection