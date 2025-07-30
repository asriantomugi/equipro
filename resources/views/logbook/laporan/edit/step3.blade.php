@extends('logbook.main')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
<style>
    .btn-tambah-tindak {
        box-shadow: 0 2px 4px rgba(0,123,255,0.3);
        transition: all 0.3s ease;
    }
    .btn-tambah-tindak:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,123,255,0.4);
    }
    .draft-form-container {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .form-section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 15px;
        border-bottom: 2px solid #007bff;
        padding-bottom: 5px;
    }
</style>
@endsection

@section('content')
<section class="content">
    <div class="container-fluid">

        {{-- Step Navigation --}}
        <div class="row mb-2">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body py-2">
                        <ul class="step d-flex flex-nowrap">
                            <li class="step-item completed"><a href="#">Pilih Layanan</a></li>
                            <li class="step-item completed"><a href="#">Input Gangguan</a></li>
                            <li class="step-item active"><a href="#">Tindak Lanjut</a></li>
                            <li class="step-item"><a href="#">Review</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>


        {{-- Kondisi untuk Status DRAFT: Form Input Tindak Lanjut --}}
@if($laporan->status == 'draft' || $laporan->status == config('constants.status_laporan.draft'))
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">FORM TINDAK LANJUT</h3>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Form untuk Status DRAFT - DIPERBAIKI --}}
<form id="formTindakLanjutDraft" action="{{ route('logbook.laporan.edit.step3.update', $laporan->id) }}" method="POST">
    @csrf
    <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">
    <input type="hidden" name="form_type" value="draft">
    <input type="hidden" name="layanan_id" value="{{ $laporan->layanan_id }}">
    <input type="hidden" name="jenis_laporan" value="{{ $laporan->jenis == 1 ? 1 : 0 }}">
    
    @if($laporan->jenis == 1)
        {{-- =============== PERALATAN (jika gangguan_peralatan) =============== --}}
        @if(count($peralatanGangguanIds) > 0)
            @php $shown = 0; @endphp
            @foreach($peralatanGangguanIds as $index => $peralatanId)
                @php
                    $peralatan = $layanan->daftarPeralatanLayanan->where('peralatan_id', $peralatanId)->first()->peralatan ?? null;
                    $existingTL = $tindaklanjutPeralatan->get($peralatanId);
                    $shown++;
                @endphp
                
                @if ($shown > 1)<hr>@endif

                <div class="mb-3">
                    <strong>Peralatan {{ $shown }}:
                        <span class="badge bg-primary">{{ $peralatan->nama ?? 'Peralatan ID: ' . $peralatanId }}</span>
                    </strong>
                </div>

                <input type="hidden" name="tindaklanjut[{{ $peralatanId }}][peralatan_id]" value="{{ $peralatanId }}">

                {{-- Jenis --}}
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Jenis <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <select name="tindaklanjut[{{ $peralatanId }}][jenis_tindaklanjut]" 
                                class="form-control tindak-jenis"
                                data-nama="{{ $peralatan->nama ?? 'Peralatan ID: ' . $peralatanId }}"
                                required>
                            <option value="">- Pilih -</option>
                            @foreach($jenisTindakLanjut as $label => $value)
                                <option value="{{ $value ? 1 : 0 }}" {{ $existingTL && $existingTL->jenis_tindaklanjut == ($value ? 1 : 0) ? 'selected' : '' }}>
                                    {{ ucfirst($label) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback dynamic"></div>
                    </div>
                </div>

                {{-- Waktu --}}
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Waktu <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <input type="datetime-local"
                               name="tindaklanjut[{{ $peralatanId }}][waktu]"
                               class="form-control tindak-waktu"
                               data-nama="{{ $peralatan->nama ?? 'Peralatan ID: ' . $peralatanId }}"
                               value="{{ $existingTL ? \Carbon\Carbon::parse($existingTL->waktu)->format('Y-m-d\TH:i') : '' }}"
                               required>
                        <div class="invalid-feedback dynamic"></div>
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Deskripsi</label>
                    <div class="col-sm-9">
                        <textarea name="tindaklanjut[{{ $peralatanId }}][deskripsi]"
                                  class="form-control"
                                  rows="3">{{ $existingTL->deskripsi ?? '' }}</textarea>
                    </div>
                </div>

                {{-- Kondisi --}}
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Kondisi <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <select name="tindaklanjut[{{ $peralatanId }}][kondisi]"
                                class="form-control tindak-kondisi"
                                data-nama="{{ $peralatan->nama ?? 'Peralatan ID: ' . $peralatanId }}"
                                required>
                            <option value="">- Pilih -</option>
                            @foreach($kondisiTindaklanjut as $label => $value)
                                <option value="{{ $value ? 1 : 0 }}" {{ $existingTL && $existingTL->kondisi == ($value ? 1 : 0) ? 'selected' : '' }}>
                                    {{ ucfirst($label) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback dynamic"></div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                Tidak ada peralatan yang mengalami gangguan untuk ditindaklanjuti.
            </div>
        @endif
    @else
        {{-- =============== NON‑PERALATAN =============== --}}
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Waktu <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="datetime-local" 
                       name="waktu" 
                       class="form-control"
                       value="{{ $tindaklanjutNonPeralatan ? \Carbon\Carbon::parse($tindaklanjutNonPeralatan->waktu)->format('Y-m-d\TH:i') : '' }}"
                       required>
                <div class="invalid-feedback dynamic"></div>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Deskripsi</label>
            <div class="col-sm-9">
                <textarea name="deskripsi" 
                          class="form-control"
                          rows="3">{{ $tindaklanjutNonPeralatan->deskripsi ?? '' }}</textarea>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Kondisi <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <select name="kondisi" class="form-control non-peralatan-kondisi" required>
                    <option value="">- Pilih -</option>
                    @foreach($kondisiTindaklanjut as $label => $value)
                        <option value="{{ $value ? 1 : 0 }}" {{ $tindaklanjutNonPeralatan && $tindaklanjutNonPeralatan->kondisi == ($value ? 1 : 0) ? 'selected' : '' }}>
                            {{ ucfirst($label) }}
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback dynamic"></div>
            </div>
        </div>
    @endif
</form>
                </div>
            </div>
        </div>
    </div>

        @else
            
{{-- Bagian untuk Status OPEN: Tampilan Data Tindak Lanjut dengan Modal --}}
@if($laporan->status == 'open' || $laporan->status == config('constants.status_laporan.open'))
    {{-- Tabel Tindak Lanjut yang Sudah Ada --}}
    @if($existingTindakLanjut->count() > 0)
        @if($laporan->jenis == 1)
            {{-- Untuk Gangguan Peralatan - Tabel per Peralatan --}}
            @php
                // PERBAIKAN: Gunakan peralatanGangguanIds sebagai acuan urutan yang konsisten
                // bukan groupBy dari existingTindakLanjut yang bisa berubah urutan
                $tindakLanjutByPeralatan = $existingTindakLanjut->groupBy('peralatan_id');
                $shown = 0;
            @endphp
            
            {{-- Loop berdasarkan peralatanGangguanIds untuk menjaga urutan konsisten --}}
            @foreach($peralatanGangguanIds as $peralatanId)
                @php
                    // Cek apakah peralatan ini memiliki tindak lanjut
                    $tindakLanjutList = $tindakLanjutByPeralatan->get($peralatanId);
                    
                    // Skip jika tidak ada tindak lanjut untuk peralatan ini
                    if (!$tindakLanjutList || $tindakLanjutList->isEmpty()) {
                        continue;
                    }
                    
                    $peralatan = $layanan->daftarPeralatanLayanan->where('peralatan_id', $peralatanId)->first()->peralatan ?? null;
                    // Cek kondisi terbaru peralatan ini
                    $kondisiTerakhir = $kondisiTerbaru[$peralatanId] ?? 0;
                    $masihGangguan = ($kondisiTerakhir == 0);
                    $shown++;
                @endphp
                
                @if ($shown > 1)<hr>@endif
                
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                {{-- Badge Nama Peralatan dengan nomor urut yang konsisten --}}
                                <div class="mb-3">
                                    <strong>Peralatan {{ array_search($peralatanId, $peralatanGangguanIds) + 1 }}:
                                        <span class="badge bg-primary">{{ $peralatan->nama ?? 'Peralatan Tidak Diketahui' }}</span>
                                    </strong>
                                </div>
                                {{-- Button Tambah per Peralatan - HANYA TAMPIL JIKA MASIH GANGGUAN --}}
                                @if($masihGangguan)
                                    <button type="button" class="btn btn-success btn-sm ml-auto" 
                                            data-toggle="modal" 
                                            data-target="#modalTindakLanjut"
                                            data-peralatan-id="{{ $peralatanId }}"
                                            data-peralatan-nama="{{ $peralatan->nama ?? 'Peralatan Tidak Diketahui' }}"
                                            data-peralatan-nomor="{{ array_search($peralatanId, $peralatanGangguanIds) + 1 }}"
                                            title="Tambah Tindak Lanjut">
                                        <i class="fas fa-plus"></i>&nbsp;&nbsp;&nbsp;Tambah
                                    </button>
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-sm">
                                        <thead class="bg-light">
                                            <tr>
                                                <th width="5%"><center>No</center></th>
                                                <th width="20%"><center>Waktu</center></th>
                                                <th width="15%"><center>Jenis</center></th>
                                                <th width="45%"><center>Deskripsi</center></th>
                                                <th width="15%"><center>Kondisi</center></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- Urutkan tindak lanjut berdasarkan waktu --}}
                                            @foreach($tindakLanjutList->sortBy('waktu') as $index => $tindak)
                                                <tr>
                                                    <td><center>{{ $index + 1 }}</center></td>
                                                    <td><center>
                                                        <small>
                                                            {{ \Carbon\Carbon::parse($tindak->waktu)->format('d/m/Y') }}
                                                        </small><br>
                                                        <small>{{ \Carbon\Carbon::parse($tindak->waktu)->format('H:i') }}</small>
                                                    </center></td>
                                                    <td><center>
                                                        <span class="badge {{ $tindak->jenis_tindaklanjut ? 'badge-info' : 'badge-warning' }}">
                                                            {{ $tindak->jenis_tindaklanjut ? 'Perbaikan' : 'Penggantian' }}
                                                        </span>
                                                    </center></td>
                                                    <td><center>{{ $tindak->deskripsi ?? '-' }}</center></td>
                                                    <td><center>
                                                        <span class="badge {{ $tindak->kondisi ? 'badge-success' : 'badge-danger' }}">
                                                            {{ $tindak->kondisi ? 'Beroperasi' : 'Gangguan' }}
                                                        </span>
                                                    </center></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            
            {{-- TAMBAHAN: Tampilkan peralatan yang belum ada tindak lanjutnya --}}
            @foreach($peralatanGangguanIds as $peralatanId)
                @php
                    $tindakLanjutList = $tindakLanjutByPeralatan->get($peralatanId);
                    
                    // Hanya tampilkan jika belum ada tindak lanjut
                    if ($tindakLanjutList && !$tindakLanjutList->isEmpty()) {
                        continue;
                    }
                    
                    $peralatan = $layanan->daftarPeralatanLayanan->where('peralatan_id', $peralatanId)->first()->peralatan ?? null;
                    $kondisiTerakhir = $kondisiTerbaru[$peralatanId] ?? 0;
                    $masihGangguan = ($kondisiTerakhir == 0);
                    
                    // Hanya tampilkan jika masih gangguan
                    if (!$masihGangguan) {
                        continue;
                    }
                @endphp
                
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="mb-3">
                                    <strong>Peralatan {{ array_search($peralatanId, $peralatanGangguanIds) + 1 }}:
                                        <span class="badge bg-primary">{{ $peralatan->nama ?? 'Peralatan Tidak Diketahui' }}</span>
                                    </strong>
                                </div>
                                <button type="button" class="btn btn-success btn-sm ml-auto" 
                                        data-toggle="modal" 
                                        data-target="#modalTindakLanjut"
                                        data-peralatan-id="{{ $peralatanId }}"
                                        data-peralatan-nama="{{ $peralatan->nama ?? 'Peralatan Tidak Diketahui' }}"
                                        data-peralatan-nomor="{{ array_search($peralatanId, $peralatanGangguanIds) + 1 }}"
                                        title="Tambah Tindak Lanjut">
                                    <i class="fas fa-plus"></i>&nbsp;&nbsp;&nbsp;Tambah
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    Belum ada tindak lanjut untuk peralatan ini.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            
        @else
            {{-- Untuk Non-Peralatan - Tabel Tunggal (tidak berubah) --}}
            @php
                $kondisiTerakhir = $kondisiTerbaru['non_peralatan'] ?? 0;
                $masihGangguan = ($kondisiTerakhir == 0);
            @endphp
            <div class="row mb-3">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                Riwayat Tindak Lanjut
                                {{-- Badge kondisi terbaru --}}
                                <span class="badge {{ $masihGangguan ? 'badge-danger' : 'badge-success' }} ml-2">
                                    {{ $masihGangguan ? 'Gangguan' : 'Beroperasi' }}
                                </span>
                            </h5>
                            {{-- Button Tambah untuk Non-Peralatan - HANYA TAMPIL JIKA MASIH GANGGUAN --}}
                            @if($masihGangguan)
                                <button type="button" class="btn btn-primary btn-sm btn-tambah-tindak" 
                                        data-toggle="modal" 
                                        data-target="#modalTindakLanjut"
                                        title="Tambah Tindak Lanjut">
                                    Tambah
                                </button>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="5%"><center>No</center></th>
                                            <th width="25%"><center>Waktu</center></th>
                                            <th width="55%"><center>Deskripsi</center></th>
                                            <th width="15%"><center>Kondisi</center></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Urutkan berdasarkan waktu --}}
                                        @foreach($existingTindakLanjut->sortBy('waktu') as $index => $tindak)
                                            <tr>
                                                <td><center>{{ $index + 1 }}</center></td>
                                                <td><center>
                                                    <small>
                                                        {{ \Carbon\Carbon::parse($tindak->waktu)->format('d/m/Y') }}
                                                    </small><br>
                                                    <small>{{ \Carbon\Carbon::parse($tindak->waktu)->format('H:i') }}</small>
                                                </center></td>
                                                <td><center>{{ $tindak->deskripsi ?? '-' }}</center></td>
                                                <td><center>
                                                    <span class="badge {{ $tindak->kondisi ? 'badge-success' : 'badge-danger' }}">
                                                        {{ $tindak->kondisi ? 'Beroperasi' : 'Gangguan' }}
                                                    </span>
                                                </center></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @else
        {{-- Empty State dengan Button Tambah yang Prominent --}}
        <div class="row mb-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <h4 class="text-muted mb-3">Belum ada data tindak lanjut</h4>
                        <p class="text-muted mb-4">Mulai tambahkan tindak lanjut untuk laporan ini</p>
                        
                        @if($laporan->jenis == 1)
                            {{-- Untuk gangguan peralatan, tampilkan button per peralatan yang bermasalah --}}
                            @if(count($peralatanGangguanIds) > 0)
                                @foreach($peralatanGangguanIds as $index => $peralatanId)
                                    @php
                                        $peralatan = $layanan->daftarPeralatanLayanan->where('peralatan_id', $peralatanId)->first()->peralatan ?? null;
                                        // Untuk empty state, semua peralatan dianggap masih gangguan
                                        $kondisiTerakhir = $kondisiTerbaru[$peralatanId] ?? 0;
                                        $masihGangguan = ($kondisiTerakhir == 0);
                                    @endphp
                                    @if($masihGangguan)
                                        <button type="button" class="btn btn-primary btn-lg btn-tambah-tindak mb-2" 
                                                data-toggle="modal" 
                                                data-target="#modalTindakLanjut"
                                                data-peralatan-id="{{ $peralatanId }}"
                                                data-peralatan-nama="{{ $peralatan->nama ?? 'Peralatan ID: ' . $peralatanId }}"
                                                data-peralatan-nomor="{{ $index + 1 }}">
                                            Tambah Tindak Lanjut - Peralatan {{ $index + 1 }}: {{ $peralatan->nama ?? 'Peralatan ID: ' . $peralatanId }}
                                        </button><br>
                                    @endif
                                @endforeach
                            @else
                                <p class="text-warning">Tidak ada peralatan yang mengalami gangguan</p>
                            @endif
                        @else
                            {{-- Untuk gangguan non-peralatan --}}
                            @php
                                $kondisiTerakhir = $kondisiTerbaru['non_peralatan'] ?? 0;
                                $masihGangguan = ($kondisiTerakhir == 0);
                            @endphp
                            @if($masihGangguan)
                                <button type="button" class="btn btn-primary btn-lg btn-tambah-tindak" 
                                        data-toggle="modal" 
                                        data-target="#modalTindakLanjut">
                                    Tambah Tindak Lanjut Pertama
                                </button>
                            @else
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i>
                                    Layanan sudah beroperasi normal
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif

            {{-- Modal Form Tindak Lanjut untuk Status Open --}}
            <div class="modal fade" id="modalTindakLanjut" tabindex="-1" role="dialog" aria-labelledby="modalTindakLanjutLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTindakLanjutLabel">Form Tindak Lanjut</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="formTindakLanjut" action="{{ route('logbook.laporan.edit.step3.update', $laporan->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">
                            <input type="hidden" name="tindak_lanjut_id" id="tindak_lanjut_id">
                            <input type="hidden" name="form_type" value="ajax">
                            {{-- Hidden input untuk peralatan_id --}}
                            <input type="hidden" name="peralatan_id" id="peralatan_id">
                            
                            <div class="modal-body">
                                @if($laporan->jenis == 1)
                                    {{-- Tampilkan peralatan yang dipilih --}}
                                    <div class="form-group row" id="peralatan_info_row" style="display: none;">
                                        <label class="col-sm-3 col-form-label">Peralatan</label>
                                        <div class="col-sm-9">
                                            <input type="text" id="peralatan_nama" class="form-control" readonly>
                                        </div>
                                    </div>

                                    {{-- Form untuk Gangguan Peralatan --}}
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Jenis <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <select name="jenis_tindaklanjut" id="jenis_tindaklanjut" class="form-control" required>
                                                <option value="">- Pilih -</option>
                                                @foreach($jenisTindakLanjut as $label => $value)
                                                    <option value="{{ $value ? 1 : 0 }}">{{ ucfirst($label) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Waktu <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="datetime-local" name="waktu" id="waktu" class="form-control" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Deskripsi</label>
                                    <div class="col-sm-9">
                                        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Kondisi <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <select name="kondisi" id="kondisi" class="form-control" required>
                                            <option value="">- Pilih -</option>
                                            @foreach($kondisiTindaklanjut as $label => $value)
                                                <option value="{{ $value ? 1 : 0 }}">{{ ucfirst($label) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="modal-footer d-flex justify-content-between">
                                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success btn-sm float-right" id="btnSubmitTindak">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- Navigation Buttons - DIPERBAIKI --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-footer">
                        <a href="{{ route('laporan.edit.step2', $laporan->id) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-angle-left"></i>
                            Kembali
                        </a>
                        
                        @if($laporan->status == 'draft' || $laporan->status == config('constants.status_laporan.draft'))
                            {{-- Untuk status draft, button lanjut akan trigger submit form --}}
                            <button type="button" class="btn btn-success btn-sm float-right" id="btnLanjutDraft">
                                <span class="button-text">Lanjut</span>
                                <i class="fas fa-angle-right"></i>
                            </button>
                        @else
                            {{-- Untuk status selain draft, langsung link ke step 4 --}}
                            <a href="{{ route('logbook.laporan.edit.step4', $laporan->id) }}" class="btn btn-success btn-sm float-right">
                                Lanjut
                                <i class="fas fa-angle-right"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
/* hilangkan pseudo‑asterisk bawaan browser / bootstrap */
input[type="datetime-local"]::after,
input.form-control:required::after,
input[type="datetime-local"]:required::after {
    content: none !important;
    display: none !important;
    color: transparent !important;
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script>
$(document).ready(function() {
    // Set default waktu ke sekarang untuk form draft
    function setDefaultDateTimeForDraft() {
        var now = new Date();
        var year = now.getFullYear();
        var month = String(now.getMonth() + 1).padStart(2, '0');
        var day = String(now.getDate()).padStart(2, '0');
        var hours = String(now.getHours()).padStart(2, '0');
        var minutes = String(now.getMinutes()).padStart(2, '0');
        var datetime = `${year}-${month}-${day}T${hours}:${minutes}`;
        
        // Set default untuk form draft jika belum ada value
        $('input[type="datetime-local"]').each(function() {
            if (!$(this).val()) {
                $(this).val(datetime);
            }
        });
    }

    // Initialize default datetime untuk form draft
    var isDraftStatus = {{ ($laporan->status == 'draft' || $laporan->status == config('constants.status_laporan.draft')) ? 'true' : 'false' }};
    if (isDraftStatus) {
        setDefaultDateTimeForDraft();
    }

    // Handle button lanjut untuk status draft - DIPERBAIKI
    $('#btnLanjutDraft').on('click', function(e) {
        e.preventDefault();
        
        // Trigger submit form draft
        $('#formTindakLanjutDraft').submit();
    });

    // Handle form submission untuk status draft 
    $('#formTindakLanjutDraft').on('submit', function(e) {
        e.preventDefault();
        
        let formData = $(this).serialize();
        let url = $(this).attr('action');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#btnLanjutDraft').prop('disabled', true);
                $('#btnLanjutDraft .button-text').text('Menyimpan...');
                $('#btnLanjutDraft i').removeClass('fa-angle-right').addClass('fa-spinner fa-spin');
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message || 'Data berhasil disimpan');
                    
                    if (response.redirect) {
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 1500);
                    } else {
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    }
                } else {
                    showAlert('error', response.message || 'Terjadi kesalahan');
                }
            },
            error: function(xhr) {
                let message = 'Terjadi kesalahan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let errors = xhr.responseJSON.errors;
                    message = Object.values(errors).flat().join('<br>');
                }
                showAlert('error', message);
            },
            complete: function() {
                $('#btnLanjutDraft').prop('disabled', false);
                $('#btnLanjutDraft .button-text').text('Lanjut');
                $('#btnLanjutDraft i').removeClass('fa-spinner fa-spin').addClass('fa-angle-right');
            }
        });
    });

    // Reset modal when opened (untuk status open)
    $('#modalTindakLanjut').on('show.bs.modal', function(e) {
        resetModalForm();
        // TIDAK SET DEFAULT WAKTU - biarkan kosong
        $('#modalTindakLanjutLabel').text('Form Tindak Lanjut');
        $('#btnSubmitTindak').text('Simpan');
        
        // Auto select peralatan jika button diklik dari tabel peralatan tertentu
        var button = $(e.relatedTarget);
        var peralatanId = button.data('peralatan-id');
        var peralatanNama = button.data('peralatan-nama');
        
        console.log('Peralatan ID dari button:', peralatanId); // Debug log
        console.log('Peralatan Nama dari button:', peralatanNama); // Debug log
        
        if (peralatanId) {
            $('#peralatan_id').val(peralatanId);
            
            @if($laporan->jenis == 1)
                // Tampilkan info peralatan
                var displayNama = peralatanNama || 'Peralatan ID: ' + peralatanId;
                $('#peralatan_nama').val(displayNama);
                $('#peralatan_info_row').show();
            @endif
        } else {
            // Jika tidak ada peralatan_id, coba ambil yang pertama untuk gangguan peralatan
            @if($laporan->jenis == 1)
                @if(count($peralatanGangguanIds) > 0)
                    var firstPeralatanId = {{ $peralatanGangguanIds[0] }};
                    $('#peralatan_id').val(firstPeralatanId);
                    
                    // Cari nama peralatan
                    var firstPeralatanNama = 'Peralatan ID: ' + firstPeralatanId;
                    @foreach($layanan->daftarPeralatanLayanan as $dpl)
                        if ({{ $dpl->peralatan->id }} == firstPeralatanId) {
                            firstPeralatanNama = '{{ $dpl->peralatan->nama }}';
                        }
                    @endforeach
                    
                    $('#peralatan_nama').val(firstPeralatanNama);
                    $('#peralatan_info_row').show();
                @endif
            @endif
        }
    });

    $('#formTindakLanjut').on('submit', function(e) {
        e.preventDefault();
        
        // Validasi peralatan_id untuk gangguan peralatan
        @if($laporan->jenis == 1)
            var peralatanId = $('#peralatan_id').val();
            if (!peralatanId) {
                showAlert('error', 'ID Peralatan tidak ditemukan. Silakan refresh halaman dan coba lagi.');
                return;
            }
        @endif
        
        let formData = $(this).serialize();
        let url = $(this).attr('action');
        let kondisiDipilih = $('#kondisi').val(); // Ambil kondisi yang dipilih
        
        console.log('Form data:', formData);
        console.log('Kondisi dipilih:', kondisiDipilih);
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#btnSubmitTindak').prop('disabled', true).text('Menyimpan...');
            },
            success: function(response) {
                console.log('Response:', response);
                if (response.success) {
                    $('#modalTindakLanjut').modal('hide');
                    
                    // HILANGKAN NOTIFIKASI - langsung reload
                    // AUTO RELOAD HALAMAN SETELAH SUKSES SIMPAN
                    // Ini akan refresh halaman sehingga button tambah otomatis hilang jika sudah beroperasi
                    setTimeout(function() {
                        location.reload();
                    }, 500); // Kurangi delay menjadi 500ms
                } else {
                    showAlert('error', response.message || 'Terjadi kesalahan');
                }
            },
            error: function(xhr) {
                console.log('Error response:', xhr.responseJSON);
                let message = 'Terjadi kesalahan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let errors = xhr.responseJSON.errors;
                    message = Object.values(errors).flat().join('<br>');
                }
                showAlert('error', message);
            },
            complete: function() {
                $('#btnSubmitTindak').prop('disabled', false).text('Simpan');
            }
        });
    });

    // HILANGKAN NOTIFIKASI KONDISI - tidak perlu show info lagi
    // $('#kondisi').on('change', function() {
    //     var kondisiTerpilih = $(this).val();
    //     if (kondisiTerpilih == '1') {
    //         showInfo('info', 'Memilih kondisi "Beroperasi" akan memperbarui status layanan.');
    //     }
    // });

    
    // Reset modal form
    function resetModalForm() {
        $('#formTindakLanjut')[0].reset();
        $('#tindak_lanjut_id').val('');
        $('#peralatan_id').val('');
        $('#peralatan_info_row').hide();
        $('#formTindakLanjut').find('.is-invalid').removeClass('is-invalid');
        $('#formTindakLanjut').find('.invalid-feedback').remove();
        // PASTIKAN WAKTU KOSONG
        $('#waktu').val('');
    }

    // Show alert function
    function showAlert(type, message) {
        let alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        let iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
        
        let alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="fas ${iconClass}"></i> ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        // Remove existing alerts
        $('.alert').remove();
        
        // Add new alert at the top of container
        $('.container-fluid').prepend(alertHtml);
        
        // Auto hide after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }

    // Validation on form fields untuk status draft
    $('#formTindakLanjutDraft input, #formTindakLanjutDraft select, #formTindakLanjutDraft textarea').on('blur change', function() {
        validateField($(this));
    });

    // Validation on form fields untuk modal
    $('#formTindakLanjut input, #formTindakLanjut select, #formTindakLanjut textarea').on('blur change', function() {
        validateField($(this));
    });

    function validateField(field) {
        let isValid = true;
        let message = '';
        
        // Remove existing validation classes
        field.removeClass('is-invalid is-valid');
        field.siblings('.invalid-feedback').remove();
        
        // Check if required field is empty
        if (field.prop('required') && !field.val().trim()) {
            isValid = false;
            message = 'Field ini wajib diisi';
        }
        
        // Add validation classes and feedback
        if (!isValid) {
            field.addClass('is-invalid');
            field.after(`<div class="invalid-feedback">${message}</div>`);
        } else if (field.val().trim()) {
            field.addClass('is-valid');
        }
        
        return isValid;
    }

    // Form validation before submit untuk draft
    $('#formTindakLanjutDraft').on('submit', function(e) {
        let isFormValid = true;
        
        $(this).find('input[required], select[required], textarea[required]').each(function() {
            if (!validateField($(this))) {
                isFormValid = false;
            }
        });
        
        if (!isFormValid) {
            e.preventDefault();
            showAlert('error', 'Mohon lengkapi semua field yang wajib diisi');
            return false;
        }
    });

    // Form validation before submit untuk modal
    $('#formTindakLanjut').on('submit', function(e) {
        let isFormValid = true;
        
        $(this).find('input[required], select[required], textarea[required]').each(function() {
            if (!validateField($(this))) {
                isFormValid = false;
            }
        });
        
        if (!isFormValid) {
            e.preventDefault();
            showAlert('error', 'Mohon lengkapi semua field yang wajib diisi');
            return false;
        }
    });

    // Auto-save functionality untuk draft (opsional)
    var autoSaveTimeout;
    $('#formTindakLanjutDraft input, #formTindakLanjutDraft select, #formTindakLanjutDraft textarea').on('input change', function() {
        if (isDraftStatus) {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(function() {
                // Implementasi auto-save jika diperlukan
                console.log('Auto-save triggered');
            }, 3000); // Auto-save setelah 3 detik tidak ada perubahan
        }
    });

    // Konfirmasi sebelum meninggalkan halaman jika ada perubahan yang belum disimpan
    var formChanged = false;
    $('#formTindakLanjutDraft input, #formTindakLanjutDraft select, #formTindakLanjutDraft textarea').on('input change', function() {
        formChanged = true;
    });

    $('#formTindakLanjutDraft').on('submit', function() {
        formChanged = false; // Reset flag saat form di-submit
    });

    $(window).on('beforeunload', function() {
        if (formChanged && isDraftStatus) {
            return 'Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman ini?';
        }
    });

    // Helper function untuk format tanggal
    function formatDateTime(dateString) {
        if (!dateString) return '';
        var date = new Date(dateString);
        var year = date.getFullYear();
        var month = String(date.getMonth() + 1).padStart(2, '0');
        var day = String(date.getDate()).padStart(2, '0');
        var hours = String(date.getHours()).padStart(2, '0');
        var minutes = String(date.getMinutes()).padStart(2, '0');
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }

    // Keyboard shortcuts untuk efisiensi
    $(document).on('keydown', function(e) {
        // Ctrl+S untuk save form draft
        if (e.ctrlKey && e.which === 83 && isDraftStatus) {
            e.preventDefault();
            $('#formTindakLanjutDraft').submit();
        }
        
        // Escape untuk close modal
        if (e.which === 27) {
            $('#modalTindakLanjut').modal('hide');
        }
    });

    // Tooltip initialization
    $('[data-toggle="tooltip"]').tooltip();

    // Initialize any additional UI components
    if (typeof initializeAdditionalComponents === 'function') {
        initializeAdditionalComponents();
    }
});
</script>
@endpush