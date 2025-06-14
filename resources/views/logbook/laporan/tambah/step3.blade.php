@extends('logbook.main')
@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')
<section class="content">
    <div class="container-fluid">

        <!-- Step Navigation -->
        <div class="row mb-2">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body py-2">
                        <ul class="step d-flex flex-nowrap justify-content-between mb-0">
                            <li class="step-item completed">
                                <a href="{{ route('tambah.step1') }}">Step 1</a>
                            </li>
                            <li class="step-item completed">
                                <a href="{{ route('tambah.step2') }}">Step 2</a>
                            </li>
                            <li class="step-item active">
                                <a href="#">Step 3</a>
                            </li>
                            <li class="step-item">
                                <a href="#">Step 4</a>
                            </li>
                            <li class="step-item">
                                <a href="#">Step 5</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Tindak Lanjut -->
        @if ($step == 3)
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Form Tindak Lanjut</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('laporan.simpanStep3') }}" method="POST">
                            @csrf

                            <!-- Hidden Inputs -->
                            <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">
                            <input type="hidden" name="layanan_id" value="{{ $laporan->layanan_id }}">
                            <input type="hidden" name="jenis_laporan" value="{{ $laporan->jenis_laporan }}">

                            @if ($laporan->jenis_laporan === 'peralatan')
                                <input type="hidden" name="gangguan_peralatan_id" value="{{ $laporan->gangguan_peralatan_id }}">
                                <input type="hidden" name="peralatan_id" value="{{ $laporan->peralatan_id }}">

                                <div class="form-group">
                                    <label for="jenis_tindaklanjut">Jenis Tindak Lanjut</label>
                                    <select name="jenis_tindaklanjut" id="jenis_tindaklanjut" class="form-control" required>
                                        <option value="0">Pemeliharaan</option>
                                        <option value="1">Perbaikan</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="tanggal">Tanggal</label>
                                    <input type="date" name="tanggal" id="tanggal" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="waktu">Waktu</label>
                                    <input type="time" name="waktu" id="waktu" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="deskripsi">Deskripsi Perbaikan</label>
                                    <textarea name="deskripsi" id="deskripsi" class="form-control" required></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="kondisi">Kondisi Setelah Perbaikan</label>
                                    <select name="kondisi" id="kondisi" class="form-control" required>
                                        <option value="1">Serviceable</option>
                                        <option value="0">Unserviceable</option>
                                    </select>
                                </div>
                            @else
                                <input type="hidden" name="gangguan_non_peralatan_id" value="{{ $laporan->gangguan_non_peralatan_id }}">

                                <div class="form-group">
                                    <label for="tanggal">Tanggal</label>
                                    <input type="date" name="tanggal" id="tanggal" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="waktu">Waktu</label>
                                    <input type="time" name="waktu" id="waktu" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="deskripsi">Deskripsi Tindak Lanjut</label>
                                    <textarea name="deskripsi" id="deskripsi" class="form-control" required></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="kondisi">Kondisi</label>
                                    <select name="kondisi" id="kondisi" class="form-control" required>
                                        <option value="1">Selesai</option>
                                        <option value="0">Belum Selesai</option>
                                    </select>
                                </div>
                            @endif

                            <!-- Tombol Navigasi -->
                            <div class="form-group mt-4 d-flex justify-content-between">
                                <a href="{{ route('tambah.step2') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Lanjut <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
</section>
@endsection
