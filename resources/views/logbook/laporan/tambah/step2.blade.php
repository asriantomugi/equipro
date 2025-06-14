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
                        <div class="container">
                            <ul class="step d-flex flex-nowrap">
                                <li class="step-item completed"><a href="{{ route('tambah.step1') }}">Step 1</a></li>
                                <li class="step-item active"><a href="#">Step 2</a></li>
                                <li class="step-item"><a href="#">Step 3</a></li>
                                <li class="step-item"><a href="#">Step 4</a></li>
                                <li class="step-item"><a href="#">Step 5</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Layanan -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">DATA LAYANAN</h3></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                @php
                                    $fasilitasNama = $layanan->fasilitas->nama ?? '-';
                                    $lokasiTkt1Nama = $layanan->lokasiTkt1->nama ?? '-';
                                    $lokasiTkt2Nama = $layanan->lokasiTkt2->nama ?? '-';
                                    $lokasiTkt3Nama = $layanan->lokasiTkt3->nama ?? '-';
                                @endphp

                                @foreach ([
                                    'KODE' => $layanan->kode,
                                    'NAMA' => $layanan->nama,
                                    'FASILITAS' => $fasilitasNama,
                                    'LOKASI TINGKAT 1' => $lokasiTkt1Nama,
                                    'LOKASI TINGKAT 2' => $lokasiTkt2Nama,
                                    'LOKASI TINGKAT 3' => $lokasiTkt3Nama,
                                    'STATUS' => $layanan->status ? 'Aktif' : 'Tidak Aktif',
                                    'KONDISI' => $layanan->kondisi == config('constants.kondisi_layanan.Serviceable') ? 'Serviceable' : 'Unserviceable',
                                ] as $label => $value)
                                    <div class="form-group row align-items-center">
                                        <label class="col-sm-3 col-form-label">{{ $label }}</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" value="{{ $value }}" readonly>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Step 2 -->
        <form method="POST" action="{{ route('tambah.step2.simpan') }}">
            @csrf
            <input type="hidden" name="layanan_id" value="{{ $layanan->id }}">

            <!-- Pilih Jenis Laporan -->
            <div class="row mb-1">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Pilih Jenis Laporan untuk Layanan: <strong>{{ $layanan->nama }}</strong></h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label for="jenis_laporan" class="col-sm-2 col-form-label">Jenis Laporan</label>
                                <div class="col-sm-6">
                                    <select class="form-control" name="jenis_laporan" id="jenis_laporan" required>
                                        <option value="">- Pilih -</option>
                                        @foreach($jenisLaporan as $key => $value)
                                            <option value="{{ $key }}">{{ Str::title(str_replace('_', ' ', $key)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dynamic Form Gangguan -->
            <div class="row mb-1">
                <div class="col-lg-12">
                    <div id="form-gangguan-container" class="mt-1"></div>

                    <!-- Tombol Navigasi -->
                    <div class="form-group mt-3">
                        <a href="{{ route('tambah.step1') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary float-right">Lanjut</button>
                    </div>
                </div>
            </div>
        </form>

    </div>
</section>
@endsection

@push('scripts')
<script>
    const kondisiGangguan = @json(config('constants.kondisi_gangguan_peralatan'));
    const peralatan = @json($layanan->daftarPeralatanLayanan);

    document.addEventListener('DOMContentLoaded', function () {
        const jenisLaporanSelect = document.getElementById('jenis_laporan');
        const container = document.getElementById('form-gangguan-container');

        jenisLaporanSelect.addEventListener('change', function () {
            const selected = this.value;
            let html = '';

            if (selected === 'gangguan_peralatan') {
                html += `
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Input Gangguan Peralatan</h3></div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Waktu Gangguan</label>
                                <div class="col-sm-6">
                                    <input type="datetime-local" name="waktu_gangguan" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                if (peralatan.length > 0) {
                    peralatan.forEach((item, index) => {
                        let options = '<option value="">- Pilih -</option>';
                        for (const [label, value] of Object.entries(kondisiGangguan)) {
                            options += `<option value="${value}">${label.charAt(0).toUpperCase() + label.slice(1)}</option>`;
                        }

                        html += `
                            <div class="card mt-3">
                                <div class="card-body">
                                    <h6>Peralatan ${index + 1}: ${item.peralatan?.nama ?? '-'}</h6>
                                    <input type="hidden" name="peralatan[${index}][id]" value="${item.peralatan?.id}">
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Kondisi Peralatan</label>
                                        <div class="col-sm-6">
                                            <select name="peralatan[${index}][kondisi]" class="form-control" required>
                                                ${options}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row mt-2">
                                        <label class="col-sm-2 col-form-label">Deskripsi Gangguan</label>
                                        <div class="col-sm-6">
                                            <textarea name="peralatan[${index}][deskripsi]" class="form-control" rows="3" required></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    html += `<div class="alert alert-warning mt-3">Tidak ada peralatan pada layanan ini.</div>`;
                }

            } else if (selected === 'gangguan_non_peralatan') {
                html = `
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Input Gangguan Non Peralatan</h3></div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Waktu Gangguan</label>
                                <div class="col-sm-6">
                                    <input type="datetime-local" name="waktu_gangguan" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group row mt-2">
                                <label class="col-sm-2 col-form-label">Deskripsi Gangguan</label>
                                <div class="col-sm-6">
                                    <textarea name="deskripsi_gangguan" class="form-control" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            container.innerHTML = html;
        });
    });
</script>
@endpush
