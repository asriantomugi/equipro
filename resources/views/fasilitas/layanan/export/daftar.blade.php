@extends('fasilitas.main')

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
                                            <option value="{{ $fas->id }}">{{ strtoupper($fas->kode) }} - {{ strtoupper($fas->nama) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <label for="filter_lokasi_tk_1">Lokasi Tingkat I</label>
                                    <select class="form-control filter-input" id="filter_lokasi_tk_1" name="lokasi_tk_1_id">
                                        <option value="">- ALL -</option>
                                        @foreach($lokasi_tk_1 as $lok1)
                                            <option value="{{ $lok1->id }}">{{ strtoupper($lok1->kode) }} - {{ strtoupper($lok1->nama) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <label for="filter_lokasi_tk_2">Lokasi Tingkat II</label>
                                    <select class="form-control filter-input" id="filter_lokasi_tk_2" name="lokasi_tk_2_id" disabled>
                                        <option value="">- ALL -</option>
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <label for="filter_lokasi_tk_3">Lokasi Tingkat III</label>
                                    <select class="form-control filter-input" id="filter_lokasi_tk_3" name="lokasi_tk_3_id" disabled>
                                        <option value="">- ALL -</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row filter-row">
                                <div class="col-lg-3">
                                    <label for="filter_kondisi">Kondisi</label>
                                    <select class="form-control filter-input" id="filter_kondisi" name="kondisi">
                                        <option value="">- ALL -</option>
                                        <option value="1">SERVICEABLE</option>
                                        <option value="0">UNSERVICEABLE</option>
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <label for="filter_status">Status</label>
                                    <select class="form-control filter-input" id="filter_status" name="status">
                                        <option value="">- ALL -</option>
                                        <option value="2">DRAFT</option>
                                        <option value="1">AKTIF</option>
                                        <option value="0">TIDAK AKTIF</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary btn-sm float-right">
                                    <i class="fas fa-filter"></i>&nbsp;&nbsp;&nbsp;Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
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
                                        <th><center>KODE</center></th>
                                        <th><center>NAMA</center></th>
                                        <th><center>FASILITAS</center></th>
                                        <th><center>LOK. TK I</center></th>
                                        <th><center>LOK. TK II</center></th>
                                        <th><center>LOK. TK III</center></th>
                                        <th><center>KONDISI</center></th>
                                        <th><center>STATUS</center></th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
    @if(isset($data) && $data->isEmpty())
        <tr>
            <td colspan="9" class="text-center">Silakan pilih filter untuk menampilkan data</td>
        </tr>
    @else
        @foreach($data as $index => $layanan)
            <tr>
                <td><center>{{ $index + 1 }}</center></td>
                <td><center>{{ strtoupper($layanan->kode) }}</center></td>
                <td><center>{{ strtoupper($layanan->nama) }}</center></td>
                <td><center>{{ strtoupper($layanan->fasilitas->nama ?? 'N/A') }}</center></td>
                <td><center>{{ strtoupper($layanan->LokasiTk1->nama ?? 'N/A') }}</center></td>
                <td><center>{{ strtoupper($layanan->LokasiTk2->nama ?? 'N/A') }}</center></td>
                <td><center>{{ strtoupper($layanan->LokasiTk3->nama ?? 'N/A') }}</center></td>
                <td><center>{{ $layanan->kondisi == config('constants.kondisi_layanan.serviceable') ? 'SERVICEABLE' : 'UNSERVICEABLE' }}</center></td>
                <td><center>{{ $layanan->status == 1 ? 'AKTIF' : ($layanan->status == 0 ? 'TIDAK AKTIF' : 'DRAFT') }}</center></td>
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
    </div>

    <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="exportForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exportModalLabel">Export Data Layanan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
                    <div class="modal-body">
                        <input type="hidden" id="exportFasilitas" name="fasilitas_id" value="">
                        <input type="hidden" id="exportLokasiTk1" name="lokasi_tk_1_id" value="">
                        <input type="hidden" id="exportLokasiTk2" name="lokasi_tk_2_id" value="">
                        <input type="hidden" id="exportLokasiTk3" name="lokasi_tk_3_id" value="">
                        <input type="hidden" id="exportKondisi" name="kondisi" value="">
                        <input type="hidden" id="exportStatus" name="status" value="">
                        
                        <div class="form-group">
                            <label for="exportKolom">Pilih Kolom yang Ingin Diekspor:</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" name="exportKolom[]" value="kode" class="form-check-input checkbox-layanan" id="col_kode">
                                        <label class="form-check-label" for="col_kode">Kode Layanan</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="exportKolom[]" value="nama" class="form-check-input checkbox-layanan" id="col_nama">
                                        <label class="form-check-label" for="col_nama">Nama Layanan</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="exportKolom[]" value="fasilitas_kode" class="form-check-input checkbox-layanan" id="col_fasilitas_kode">
                                        <label class="form-check-label" for="col_fasilitas_kode">Kode Fasilitas</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="exportKolom[]" value="fasilitas_nama" class="form-check-input checkbox-layanan" id="col_fasilitas_nama">
                                        <label class="form-check-label" for="col_fasilitas_nama">Nama Fasilitas</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="exportKolom[]" value="lokasi_tk_1_kode" class="form-check-input checkbox-layanan" id="col_lokasi_tk_1_kode">
                                        <label class="form-check-label" for="col_lokasi_tk_1_kode">Kode Lokasi Tk I</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="exportKolom[]" value="lokasi_tk_1_nama" class="form-check-input checkbox-layanan" id="col_lokasi_tk_1_nama">
                                        <label class="form-check-label" for="col_lokasi_tk_1_nama">Nama Lokasi Tk I</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" name="exportKolom[]" value="lokasi_tk_2_kode" class="form-check-input checkbox-layanan" id="col_lokasi_tk_2_kode">
                                        <label class="form-check-label" for="col_lokasi_tk_2_kode">Kode Lokasi Tk II</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="exportKolom[]" value="lokasi_tk_2_nama" class="form-check-input checkbox-layanan" id="col_lokasi_tk_2_nama">
                                        <label class="form-check-label" for="col_lokasi_tk_2_nama">Nama Lokasi Tk II</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="exportKolom[]" value="lokasi_tk_3_kode" class="form-check-input checkbox-layanan" id="col_lokasi_tk_3_kode">
                                        <label class="form-check-label" for="col_lokasi_tk_3_kode">Kode Lokasi Tk III</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="exportKolom[]" value="lokasi_tk_3_nama" class="form-check-input checkbox-layanan" id="col_lokasi_tk_3_nama">
                                        <label class="form-check-label" for="col_lokasi_tk_3_nama">Nama Lokasi Tk III</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="exportKolom[]" value="kondisi" class="form-check-input checkbox-layanan" id="col_kondisi">
                                        <label class="form-check-label" for="col_kondisi">Kondisi</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="exportKolom[]" value="status" class="form-check-input checkbox-layanan" id="col_status">
                                        <label class="form-check-label" for="col_status">Status</label>
                                    </div>
                                </div>
                            </div>
                            
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
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadData();
    });

    $('#exportBtn').on('click', function() {
        $('#exportFasilitas').val($('#filter_fasilitas').val());
        $('#exportLokasiTk1').val($('#filter_lokasi_tk_1').val());
        $('#exportLokasiTk2').val($('#filter_lokasi_tk_2').val());
        $('#exportLokasiTk3').val($('#filter_lokasi_tk_3').val());
        $('#exportKondisi').val($('#filter_kondisi').val());
        $('#exportStatus').val($('#filter_status').val());
        
        $('.checkbox-layanan').prop('checked', false);
        updateSelectedColumnsPreview();
        
        $('#exportModal').modal('show');
    });

    $('#selectAllColumns').on('click', function() {
        $('.checkbox-layanan').prop('checked', true);
        updateSelectedColumnsPreview();
    });

    $('#deselectAllColumns').on('click', function() {
        $('.checkbox-layanan').prop('checked', false);
        updateSelectedColumnsPreview();
    });

    $(document).on('change', '.checkbox-layanan', function() {
        updateSelectedColumnsPreview();
    });

    $('#confirmExportBtn').on('click', function() {
        exportDataFromModal();
    });

    $('#filter_lokasi_tk_1').change(function(){
        let lokasiTk1Id = $(this).val();
        
        if(lokasiTk1Id) {
            $('#filter_lokasi_tk_2').prop('disabled', false);
            
            $.ajax({
                url: '{{ route("fasilitas.layanan.lokasi-tk2") }}', // Updated route
                type: 'GET',
                data: {lokasi_tk_1_id: lokasiTk1Id},
                success: function(data) {
                    $('#filter_lokasi_tk_2').empty();
                    $('#filter_lokasi_tk_2').append('<option value="">- ALL -</option>');
                    $.each(data, function(key, value) {
                        $('#filter_lokasi_tk_2').append('<option value="'+ value.id +'">'+ value.kode.toUpperCase() +' - '+ value.nama.toUpperCase() +'</option>');
                    });
                    
                    $('#filter_lokasi_tk_3').prop('disabled', true);
                    $('#filter_lokasi_tk_3').empty();
                    $('#filter_lokasi_tk_3').append('<option value="">- ALL -</option>');
                }
            });
        } else {
            $('#filter_lokasi_tk_2').prop('disabled', true).empty().append('<option value="">- ALL -</option>');
            $('#filter_lokasi_tk_3').prop('disabled', true).empty().append('<option value="">- ALL -</option>');
        }
    });

    $('#filter_lokasi_tk_2').change(function(){
        let lokasiTk2Id = $(this).val();
        
        if(lokasiTk2Id) {
            $('#filter_lokasi_tk_3').prop('disabled', false);
            
            $.ajax({
                url: '{{ route("fasilitas.layanan.lokasi-tk3") }}', // Updated route
                type: 'GET',
                data: {lokasi_tk_2_id: lokasiTk2Id},
                success: function(data) {
                    $('#filter_lokasi_tk_3').empty();
                    $('#filter_lokasi_tk_3').append('<option value="">- ALL -</option>');
                    $.each(data, function(key, value) {
                        $('#filter_lokasi_tk_3').append('<option value="'+ value.id +'">'+ value.kode.toUpperCase() +' - '+ value.nama.toUpperCase() +'</option>');
                    });
                }
            });
        } else {
            $('#filter_lokasi_tk_3').prop('disabled', true).empty().append('<option value="">- ALL -</option>');
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

function updateSelectedColumnsPreview() {
    let selectedColumns = [];
    $('.checkbox-layanan:checked').each(function() {
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

function exportDataFromModal() {
    let selectedColumns = [];
    $('.checkbox-layanan:checked').each(function() {
        selectedColumns.push($(this).val());
    });
    
    if (selectedColumns.length === 0) {
        alert('Silakan pilih minimal satu kolom untuk diekspor.');
        return;
    }
    
    let params = new URLSearchParams();
    
    // Filter data
    let fasilitasId = $('#exportFasilitas').val();
    let lokasiTk1Id = $('#exportLokasiTk1').val();
    let lokasiTk2Id = $('#exportLokasiTk2').val();
    let lokasiTk3Id = $('#exportLokasiTk3').val();
    let kondisi = $('#exportKondisi').val();
    let status = $('#exportStatus').val();
    
    if (fasilitasId && fasilitasId !== '') params.append('fasilitas_id', fasilitasId);
    if (lokasiTk1Id && lokasiTk1Id !== '') params.append('lokasi_tk_1_id', lokasiTk1Id);
    if (lokasiTk2Id && lokasiTk2Id !== '') params.append('lokasi_tk_2_id', lokasiTk2Id);
    if (lokasiTk3Id && lokasiTk3Id !== '') params.append('lokasi_tk_3_id', lokasiTk3Id);
    if (kondisi && kondisi !== '') params.append('kondisi', kondisi);
    if (status && status !== '') params.append('status', status);
    
    // Kolom yang dipilih
    params.append('columns', selectedColumns.join(','));
    
    // Debug: log parameter yang akan dikirim
    console.log('Export parameters:', {
        fasilitas_id: fasilitasId,
        lokasi_tk_1_id: lokasiTk1Id,
        lokasi_tk_2_id: lokasiTk2Id,
        lokasi_tk_3_id: lokasiTk3Id,
        kondisi: kondisi,
        status: status,
        columns: selectedColumns.join(',')
    });
    
    // Generate URL dan download
    let exportUrl = '{{ route("fasilitas.layanan.export") }}?' + params.toString();
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

function loadData(page = 1) {
    $('#loadingOverlay').show();
    
    let formData = new FormData(document.getElementById('filterForm'));
    let params = new URLSearchParams();
    
    for (let [key, value] of formData.entries()) {
        if (value) {
            params.append(key, value);
        }
    }
    
    params.append('page', page);
    params.append('ajax', '1');
    
    $.ajax({
        url: '{{ route("fasilitas.layanan.export.data") }}',
        type: 'GET',
        data: params.toString(),
        success: function(response) {
            $('#tableBody').html(response.html);
            $('#totalData').html(`Total: ${response.total}`);
            $('#loadingOverlay').hide();
        },
        error: function(xhr, status, error) {
            console.error('Error loading data:', error);
            $('#tableBody').html(`
                <tr>
                    <td colspan="9" class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Terjadi kesalahan saat memuat data
                    </td>
                </tr>
            `);
            $('#loadingOverlay').hide();
        }
    });
}

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
