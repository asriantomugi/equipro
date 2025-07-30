@extends('logbook.main')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<style>
.filter-section {
    background-color: #f8f9fa;
    border-radius: 5px;
    padding: 15px;
}
.filter-row {
    margin-bottom: 20px;
}
.filter-row:last-child {
    margin-bottom: 0;
}
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

/* CSS untuk modal footer dengan button di ujung */
.modal-footer-separated {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-top: 1px solid #dee2e6;
}
.modal-footer-separated .btn {
    margin: 0;
}
</style>
@endsection

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Card Filter -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form id="filterForm">
                            <div class="row filter-row">
                                <div class="col-lg-3">
                                    <label for="filter_fasilitas">Fasilitas</label>
                                    <select class="form-control filter-input" id="filter_fasilitas" name="fasilitas_id">
                                        <option value="">- ALL -</option>
                                        @foreach($fasilitas as $fas)
                                            <option value="{{ $fas->id }}">{{ $fas->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <label for="filter_layanan">Layanan</label>
                                    <select class="form-control filter-input" id="filter_layanan" name="layanan_id" disabled>
                                        <option value="">- ALL -</option>
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <label for="filter_status">Status</label>
                                    <select class="form-control filter-input" id="filter_status" name="status">
                                        <option value="">- ALL -</option>
                                        <option value="{{ config('constants.status_laporan.open') }}">Open</option>
                                        <option value="{{ config('constants.status_laporan.closed') }}">Closed</option>
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <label for="filter_jenis">Jenis Laporan</label>
                                    <select class="form-control filter-input" id="filter_jenis" name="jenis">
                                        <option value="">- ALL -</option>
                                        <option value="1">Gangguan Peralatan</option>
                                        <option value="0">Gangguan Non Peralatan</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row filter-row">
                                <div class="col-lg-3">
                                    <label for="tanggal_mulai">Tanggal Mulai</label>
                                    <input type="date" class="form-control filter-input" id="tanggal_mulai" name="tanggal_mulai">
                                </div>
                                <div class="col-lg-3">
                                    <label for="tanggal_selesai">Tanggal Selesai</label>
                                    <input type="date" class="form-control filter-input" id="tanggal_selesai" name="tanggal_selesai">
                                </div>
                                
                            </div>
                            <div class="card-footer">
                                <button type="submit" 
                                        class="btn btn-primary btn-sm float-right">
                                        <i class="fas fa-filter"></i>&nbsp;&nbsp;&nbsp;Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Card Tabel -->
            <div class="col-lg-12">
                <div class="card" style="position: relative;">
                    <div class="card-header">
                        <h3 class="card-title">HASIL FILTER</h3>
                        <div class="card-tools">
                            <span id="totalData" class="badge badge-info mr-2">Total: 0</span>
                            <button type="button" class="btn btn-success btn-sm" id="exportBtn" role="button">
                                <i class="fas fa-file-excel"></i> Export
                            </button>
                        </div>
                    </div>
                    
                    <!-- Loading Overlay -->
                    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                            <p class="mt-2">Memuat data...</p>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="dataTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 10px"><center>NO.</center></th>
                                        <th><center>NO LAPORAN</center></th>
                                        <th><center>LAYANAN</center></th>
                                        <th><center>FASILITAS</center></th>
                                        <th><center>LOKASI T.1</center></th>
                                        <th><center>LOKASI T.2</center></th>
                                        <th><center>LOKASI T.3</center></th>
                                        <th><center>JENIS LAPORAN</center></th>
                                        <th><center>WAKTU OPEN</center></th>
                                        <th><center>WAKTU CLOSE</center></th>
                                        <th><center>STATUS</center></th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <tr>
                                        <td colspan="12" class="text-center">Silakan pilih filter untuk menampilkan data</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk memilih kolom yang ingin diekspor -->
    <!-- Modal for Export Form -->
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form id="exportForm">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="exportModalLabel">Export Data Laporan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        
        <div class="modal-body">
          <!-- PERBAIKAN: Hidden inputs untuk menyimpan filter yang aktif -->
          <input type="hidden" id="exportFasilitas" name="fasilitas_id" value="">
          <input type="hidden" id="exportLayanan" name="layanan_id" value="">
          <input type="hidden" id="exportStatus" name="status" value="">
          <input type="hidden" id="exportJenis" name="jenis" value="">
          <input type="hidden" id="exportTanggalMulai" name="tanggal_mulai" value="">
          <input type="hidden" id="exportTanggalSelesai" name="tanggal_selesai" value="">
          
          <!-- Pilih Kolom -->
        <div class="form-group">
            <label for="exportKolom">Pilih Kolom yang Ingin Diekspor:</label>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-check">
                        <input type="checkbox" name="exportKolom[]" value="no_laporan" class="form-check-input checkbox-laporan" id="col_no_laporan">
                        <label class="form-check-label" for="col_no_laporan">No. Laporan</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="exportKolom[]" value="kode_layanan" class="form-check-input checkbox-laporan" id="col_kode_layanan">
                        <label class="form-check-label" for="col_kode_layanan">Kode Layanan</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="exportKolom[]" value="nama_layanan" class="form-check-input checkbox-laporan" id="col_nama_layanan">
                        <label class="form-check-label" for="col_nama_layanan">Nama Layanan</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="exportKolom[]" value="jenis_gangguan" class="form-check-input checkbox-laporan" id="col_jenis_gangguan">
                        <label class="form-check-label" for="col_jenis_gangguan">Jenis Gangguan</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="exportKolom[]" value="waktu_gangguan" class="form-check-input checkbox-laporan" id="col_waktu_gangguan">
                        <label class="form-check-label" for="col_waktu_gangguan">Waktu Gangguan</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="exportKolom[]" value="waktu_open" class="form-check-input checkbox-laporan" id="col_waktu_open">
                        <label class="form-check-label" for="col_waktu_open">Waktu Open</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="exportKolom[]" value="waktu_close" class="form-check-input checkbox-laporan" id="col_waktu_close">
                        <label class="form-check-label" for="col_waktu_close">Waktu Close</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="exportKolom[]" value="status" class="form-check-input checkbox-laporan" id="col_status">
                        <label class="form-check-label" for="col_status">Status</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="exportKolom[]" value="kondisi_layanan_terakhir" class="form-check-input checkbox-laporan" id="col_kondisi_layanan">
                        <label class="form-check-label" for="col_kondisi_layanan">Kondisi Layanan Terakhir</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check">
                        <input type="checkbox" name="exportKolom[]" value="kode_peralatan" class="form-check-input checkbox-laporan" id="col_kode_peralatan">
                        <label class="form-check-label" for="col_kode_peralatan">Kode Peralatan</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="exportKolom[]" value="nama_peralatan" class="form-check-input checkbox-laporan" id="col_nama_peralatan">
                        <label class="form-check-label" for="col_nama_peralatan">Nama Peralatan</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="exportKolom[]" value="deskripsi_gangguan" class="form-check-input checkbox-laporan" id="col_deskripsi_gangguan">
                        <label class="form-check-label" for="col_deskripsi_gangguan">Deskripsi Gangguan</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="exportKolom[]" value="jenis_tindaklanjut" class="form-check-input checkbox-laporan" id="col_jenis_tindaklanjut">
                        <label class="form-check-label" for="col_jenis_tindaklanjut">Jenis Tindaklanjut</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="exportKolom[]" value="waktu_tindaklanjut" class="form-check-input checkbox-laporan" id="col_waktu_tindaklanjut">
                        <label class="form-check-label" for="col_waktu_tindaklanjut">Waktu Tindaklanjut</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="exportKolom[]" value="deskripsi_tindaklanjut" class="form-check-input checkbox-laporan" id="col_deskripsi_tindaklanjut">
                        <label class="form-check-label" for="col_deskripsi_tindaklanjut">Deskripsi Tindaklanjut</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="exportKolom[]" value="kondisi_peralatan_terakhir" class="form-check-input checkbox-laporan" id="col_kondisi_peralatan">
                        <label class="form-check-label" for="col_kondisi_peralatan">Kondisi Peralatan Terakhir</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="exportKolom[]" value="kode_peralatan_pengganti" class="form-check-input checkbox-laporan" id="col_kode_pengganti">
                        <label class="form-check-label" for="col_kode_pengganti">Kode Peralatan Pengganti</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="exportKolom[]" value="nama_peralatan_pengganti" class="form-check-input checkbox-laporan" id="col_nama_pengganti">
                        <label class="form-check-label" for="col_nama_pengganti">Nama Peralatan Pengganti</label>
                    </div>
                </div>
            </div>
            
            <!-- Tombol Select All / Deselect All -->
            <div class="mt-3">
                <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllColumns">Pilih Semua</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllColumns">Hapus Semua</button>
            </div>
        </div>
        
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
          <button type="button" class="btn btn-success" id="confirmExportBtn">
            <i class="fas fa-file-excel"></i> Export ke Excel
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
</section>
@endsection

@section('tail')
<script>
$(function(){
    // Jangan load data pertama kali - biarkan tabel kosong

    // Event listener untuk filter
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadData();
    });

    // PERBAIKAN: Event listener untuk tombol export
    $('#exportBtn').on('click', function() {
        // Copy filter yang sedang aktif ke hidden inputs di modal export
        $('#exportFasilitas').val($('#filter_fasilitas').val());
        $('#exportLayanan').val($('#filter_layanan').val());
        $('#exportStatus').val($('#filter_status').val());
        $('#exportJenis').val($('#filter_jenis').val());
        $('#exportTanggalMulai').val($('#tanggal_mulai').val());
        $('#exportTanggalSelesai').val($('#tanggal_selesai').val());
        
        // Reset checkbox dan preview
        $('.checkbox-laporan').prop('checked', false);
        updateSelectedColumnsPreview();
        
        // Tampilkan modal
        $('#exportModal').modal('show');
    });

    // Select All Columns
    $('#selectAllColumns').on('click', function() {
        $('.checkbox-laporan').prop('checked', true);
        updateSelectedColumnsPreview();
    });

    // Deselect All Columns
    $('#deselectAllColumns').on('click', function() {
        $('.checkbox-laporan').prop('checked', false);
        updateSelectedColumnsPreview();
    });

    // Update preview saat checkbox berubah
    $(document).on('change', '.checkbox-laporan', function() {
        updateSelectedColumnsPreview();
    });

    // PERBAIKAN: Konfirmasi ekspor dengan parameter filter yang benar
    $('#confirmExportBtn').on('click', function() {
        exportDataFromModal();
    });

    // Auto filter layanan berdasarkan fasilitas di modal export (sudah tidak diperlukan karena menggunakan hidden input)

    // Filter hierarki - Auto filter layanan berdasarkan fasilitas di form utama
    $('#filter_fasilitas').change(function(){
        let fasilitasId = $(this).val();
        
        if(fasilitasId) {
            // Enable layanan dropdown
            $('#filter_layanan').prop('disabled', false);
            
            $.ajax({
                url: '{{ route("get.layanan.by.fasilitas") }}',
                type: 'GET',
                data: {fasilitas_id: fasilitasId},
                success: function(data) {
                    $('#filter_layanan').empty();
                    $('#filter_layanan').append('<option value="">- ALL -</option>');
                    $.each(data, function(key, value) {
                        $('#filter_layanan').append('<option value="'+ value.id +'">'+ value.nama +'</option>');
                    });
                }
            });
        } else {
            // Disable layanan dropdown
            $('#filter_layanan').prop('disabled', true);
            $('#filter_layanan').empty();
            $('#filter_layanan').append('<option value="">- ALL -</option>');
        }
    });

    @if (session()->has('notif'))
        let message = '';
        let type = 'bg-success';
        let title = 'Sukses!';

        switch ("{{ session()->get('notif') }}") {
            case 'export_sukses':
                message = 'Data berhasil diekspor';
                break;
            case 'export_gagal':
                message = 'Gagal mengekspor data';
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

// Fungsi untuk update preview kolom yang dipilih
function updateSelectedColumnsPreview() {
    let selectedColumns = [];
    $('.checkbox-laporan:checked').each(function() {
        selectedColumns.push($(this).next('label').text());
    });
    
    if (selectedColumns.length > 0) {
        $('#selectedColumnsPreview').html(selectedColumns.map(col => 
            `<span class="badge badge-primary mr-1">${col}</span>`
        ).join(''));
    } else {
        $('#selectedColumnsPreview').html('<em class="text-muted">Belum ada kolom yang dipilih</em>');
    }
}

// PERBAIKAN: Fungsi untuk export data dari modal dengan parameter filter yang lengkap
function exportDataFromModal() {
    // Validasi kolom yang dipilih
    let selectedColumns = [];
    $('.checkbox-laporan:checked').each(function() {
        selectedColumns.push($(this).val());
    });
    
    if (selectedColumns.length === 0) {
        alert('Silakan pilih minimal satu kolom untuk diekspor.');
        return;
    }
    
    // PERBAIKAN: Ambil data filter dari hidden inputs di modal (bukan dari form filter)
    let params = new URLSearchParams();
    
    // Filter data - ambil dari hidden inputs yang sudah di-set saat modal dibuka
    let fasilitasId = $('#exportFasilitas').val();
    let layananId = $('#exportLayanan').val();
    let status = $('#exportStatus').val();
    let jenis = $('#exportJenis').val();
    let tanggalMulai = $('#exportTanggalMulai').val();
    let tanggalSelesai = $('#exportTanggalSelesai').val();
    
    // Tambahkan parameter hanya jika tidak kosong
    if (fasilitasId && fasilitasId !== '') params.append('fasilitas_id', fasilitasId);
    if (layananId && layananId !== '') params.append('layanan_id', layananId);
    if (status && status !== '') params.append('status', status);
    if (jenis && jenis !== '') params.append('jenis', jenis);
    if (tanggalMulai && tanggalMulai !== '') params.append('tanggal_mulai', tanggalMulai);
    if (tanggalSelesai && tanggalSelesai !== '') params.append('tanggal_selesai', tanggalSelesai);
    
    // Kolom yang dipilih
    params.append('columns', selectedColumns.join(','));
    params.append('export', 'excel');
    
    // Debug: log parameter yang akan dikirim
    console.log('Export parameters:', {
        fasilitas_id: fasilitasId,
        layanan_id: layananId,
        status: status,
        jenis: jenis,
        tanggal_mulai: tanggalMulai,
        tanggal_selesai: tanggalSelesai,
        columns: selectedColumns.join(',')
    });
    
    // Generate URL dan download
    let exportUrl = '{{ route("laporan.export") }}?' + params.toString();
    window.open(exportUrl, '_blank');
    
    // Tutup modal
    $('#exportModal').modal('hide');
    
    // Show success message
    $(document).Toasts('create', {
        class: 'bg-success',
        title: 'Export Berhasil',
        body: 'Data sedang diproses untuk diunduh.',
        autohide: true,
        delay: 3000
    });
}

// Fungsi untuk load data via AJAX
function loadData(page = 1) {
    // Show loading
    $('#loadingOverlay').show();
    
    // Get filter data
    let formData = new FormData(document.getElementById('filterForm'));
    let params = new URLSearchParams();
    
    for (let [key, value] of formData.entries()) {
        if (value) {
            params.append(key, value);
        }
    }
    
    // Add page parameter
    params.append('page', page);
    params.append('ajax', '1');
    
    $.ajax({
        url: '{{ route("export.getData") }}',
        type: 'GET',
        data: params.toString(),
        success: function(response) {
            // Update table body
            $('#tableBody').html(response.html);
            
            // Update pagination info
            $('#paginationInfo').html(`Menampilkan ${response.from} sampai ${response.to} dari ${response.total} total data`);
            
            // Update pagination links
            $('#paginationLinks').html(response.pagination);
            
            // Update total data badge
            $('#totalData').html(`Total: ${response.total}`);
            
            // Hide loading
            $('#loadingOverlay').hide();
        },
        error: function(xhr, status, error) {
            console.error('Error loading data:', error);
            $('#tableBody').html(`
                <tr>
                    <td colspan="12" class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Terjadi kesalahan saat memuat data
                    </td>
                </tr>
            `);
            $('#loadingOverlay').hide();
        }
    });
}

// Fungsi untuk pagination
$(document).on('click', '.pagination a', function(e) {
    e.preventDefault();
    let url = $(this).attr('href');
    let page = new URL(url).searchParams.get('page');
    if (page) {
        loadData(page);
    }
});
</script>
@endsection