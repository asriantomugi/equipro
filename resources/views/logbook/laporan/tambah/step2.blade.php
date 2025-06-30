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
                                <li class="step-item completed"><a href="{{ route('tambah.step1') }}">Pilih Layanan</a></li>
                                <li class="step-item active"><a href="#">Input Gangguan</a></li>
                                <li class="step-item"><a href="#">Tindaklanjut</a></li>
                                <li class="step-item"><a href="#">Review</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD: DATA LAYANAN -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">DATA LAYANAN</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                @php
                                    $fasilitasNama = $layanan->fasilitas->nama ?? '-';
                                    $lokasiTkt1Nama = $layanan->LokasiTk1->nama ?? '-';
                                    $lokasiTkt2Nama = $layanan->LokasiTk2->nama ?? '-';
                                    $lokasiTkt3Nama = $layanan->LokasiTk3->nama ?? '-';
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

                                <div class="form-group row">
                                    <label for="jenis_laporan" class="col-sm-3 col-form-label">Jenis Laporan <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <select class="form-control" name="jenis_laporan" id="jenis_laporan" required>
                                            <option value="">- Pilih -</option>
                                            @foreach($jenisLaporan as $key => $value)
                                                <option value="{{ $key }}">{{ Str::title(str_replace('_', ' ', $key)) }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">Jenis laporan wajib diisi</div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('tambah.step2.simpan') }}">
            @csrf
            <input type="hidden" name="layanan_id" value="{{ $layanan->id }}">

            <!-- CARD: INPUT GANGGUAN (DISABLED DEFAULT) -->
            <div class="row mt-1">
                <div class="col-lg-12">
                    <div class="card d-none" id="card-input-gangguan">
                        <div class="card-header">
                            <h3 class="card-title">INPUT GANGGUAN</h3>
                        </div>
                        <div class="card-body" id="form-gangguan-container">
                            <!-- Diisi oleh JS -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                            <a href="{{ route('tambah.step1') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-angle-left"></i>&nbsp;&nbsp;Kembali
                            </a>
                            <button type="submit" class="btn btn-success btn-sm float-right">
                                Lanjut &nbsp;&nbsp;<i class="fas fa-angle-right"></i>
                            </button>
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
        const cardGangguan = document.getElementById('card-input-gangguan');

        jenisLaporanSelect.addEventListener('change', function () {
            const selected = this.value;
            let html = '';

            // Reset tampilan
            if (!selected) {
                cardGangguan.classList.add('d-none');
                container.innerHTML = '';
                return;
            }

            // Tampilkan card gangguan
            cardGangguan.classList.remove('d-none');

            if (selected === 'gangguan_peralatan') {
                html += `
                    <div class="form-group row mb-3">
                        <label class="col-sm-3 col-form-label">Waktu Gangguan <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="datetime-local" name="waktu_gangguan" class="form-control" required>
                        </div>
                    </div>
                `;

                if (peralatan.length > 0) {
                    peralatan.forEach((item, index) => {
                        let options = '<option value="">- Pilih -</option>';
                        for (const [label, value] of Object.entries(kondisiGangguan)) {
                            options += `<option value="${value}">${label}</option>`;
                        }

                        html += `
                            <hr>
                            <div class="mb-2">
                                <strong>Peralatan ${index + 1}: <span class="badge bg-primary">${item.peralatan?.nama ?? '-'}</span></strong>
                                <input type="hidden" name="peralatan[${index}][id]" value="${item.peralatan?.id}">
                            </div>

                            <div class="form-group row mb-2">
                                <label class="col-sm-3 col-form-label">Kondisi Peralatan <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select name="peralatan[${index}][kondisi]" class="form-control" required>
                                        ${options}
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label">Deskripsi Gangguan <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <textarea name="peralatan[${index}][deskripsi]" class="form-control" rows="3" required></textarea>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    html += `<div class="alert alert-warning mt-3">Tidak ada peralatan pada layanan ini.</div>`;
                }

            } else if (selected === 'gangguan_non_peralatan') {
                html = `
                    <div class="form-group row mb-3">
                        <label class="col-sm-3 col-form-label">Waktu Gangguan <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="datetime-local" name="waktu_gangguan" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label class="col-sm-3 col-form-label">Deskripsi Gangguan <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <textarea name="deskripsi_gangguan" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                `;
            }

            container.innerHTML = html;
        });
    });
</script>
@endpush
