@extends('logbook.main')

@section('content')
<section class="content">
    <div class="container-fluid">

        <!-- Step Navigation -->
        <div class="row mb-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-center flex-wrap gap-2">
                            <a class="btn btn-info mx-1 my-1" href="{{ route('tambah.step1') }}">
                                Step - 1 &nbsp;&nbsp; Pilih Layanan
                            </a>
                            <a class="btn btn-info mx-1 my-1" href="{{ route('tambah.step2', ['layanan_id' => $laporan->layanan_id]) }}">
                                Step - 2 &nbsp;&nbsp; Input Jenis Laporan
                            </a>
                            <a class="btn btn-info mx-1 my-1" href="{{ route('tambah.gangguan.form', [
                                'laporan_id' => $laporan->id,
                                'layanan_id' => $laporan->layanan_id,
                                'peralatan_id' => $laporan->peralatan_id
                            ]) }}">
                                Step - 3 &nbsp;&nbsp; Input Gangguan
                            </a>
                            <a class="btn btn-info mx-1 my-1" href="{{ route('tambah.gangguan.tindaklanjut', ['laporan_id' => $laporan->id]) }}">
                                Step - 4 &nbsp;&nbsp; Tindaklanjut
                            </a>
                            <a class="btn btn-info mx-1 my-1" href="{{ route('tambah.gangguan.penggantian', ['laporan_id' => $laporan->id]) }}">
                                Step - 5 &nbsp;&nbsp; Jenis TindakLanjut
                            </a>
                            <a class="btn btn-outline-secondary disabled mx-1 my-1" href="#" tabindex="-1" aria-disabled="true">
                                Step - 6 &nbsp;&nbsp; Review
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Penggantian -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0">FORM PENGGANTIAN PERALATAN</h3>
            </div>

            <form action="{{ route('tambah.gangguan.penggantian.simpan') }}" method="POST">
                @csrf
                <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">
                <input type="hidden" name="gangguan_peralatan_id" value="{{ $gangguan_peralatan_id }}">

                <div class="card-body">
                    <!-- Data Peralatan Lama Header + Tombol Ganti Peralatan -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">DATA PERALATAN LAMA</h5>
                        @if ($peralatanPengganti && $peralatanPengganti->count())
                            <div class="dropdown">
                                <button class="btn btn-warning btn-sm dropdown-toggle" type="button" id="dropdownGantiPeralatan" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-sync-alt"></i> Ganti Peralatan
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownGantiPeralatan">
                                    @foreach ($peralatanPengganti as $pengganti)
                                        <li><a class="dropdown-item" href="#">{{ $pengganti->kode }} - {{ $pengganti->nama }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <button class="btn btn-warning btn-sm" disabled>
                                <i class="fas fa-sync-alt"></i> Ganti Peralatan
                            </button>
                        @endif
                    </div>

                    <!-- Data Peralatan Lama Details -->
                    @forelse ($peralatanLama as $peralatan)
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <div class="row mb-2">
                                    <label class="col-sm-3 col-form-label">Kode</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value="{{ $peralatan->kode ?? '-' }}" readonly>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <label class="col-sm-3 col-form-label">Nama</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value="{{ $peralatan->nama ?? '-' }}" readonly>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <label class="col-sm-3 col-form-label">Merk</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value="{{ $peralatan->merk ?? '-' }}" readonly>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <label class="col-sm-3 col-form-label">Tipe</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value="{{ $peralatan->tipe ?? '-' }}" readonly>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <label class="col-sm-3 col-form-label">Model</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value="{{ $peralatan->model ?? '-' }}" readonly>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <label class="col-sm-3 col-form-label">Serial Number</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value="{{ $peralatan->serial_number ?? '-' }}" readonly>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <label class="col-sm-3 col-form-label">Status</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value="{{ $peralatan->status ?? '-' }}" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-3 col-form-label">Kondisi</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value="{{ $peralatan->kondisi ? 'Serviceable' : 'Unserviceable' }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                    @empty
                        <div class="alert alert-warning">Peralatan tidak ditemukan.</div>
                    @endforelse

                    <!-- Kondisi Layanan Setelah Penggantian -->
                    <div class="form-group mt-4">
                        <label for="kondisi_layanan_temp">Kondisi Layanan Setelah Penggantian</label>
                        <select name="kondisi_layanan_temp" class="form-control" required>
                            <option value="" selected disabled>- Pilih -</option>
                            @foreach (config('constants.kondisi_layanan') as $label => $value)
                                <option value="{{ $value }}">{{ ucfirst($label) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Footer -->
                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('tambah.gangguan.tindaklanjut', [
                        'laporan_id' => $laporan->id,
                        'layanan_id' => $laporan->layanan_id,
                        'peralatan_id' => $laporan->peralatan_id
                    ]) }}" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">Lanjut</button>
                </div>
            </form>
        </div>

    </div>
</section>
@endsection
