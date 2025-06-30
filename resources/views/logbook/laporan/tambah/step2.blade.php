@extends('logbook.main')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
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
                            <li class="step-item completed"><a href="{{ route('tambah.step1') }}">Pilih Layanan</a></li>
                            <li class="step-item active"><a href="#">Input Gangguan</a></li>
                            <li class="step-item"><a href="#">Tindaklanjut</a></li>
                            <li class="step-item"><a href="#">Review</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('tambah.step2.simpan') }}" id="step2-form">
            @csrf
            <input type="hidden" name="laporan_id" value="{{ $laporan->id ?? '' }}">
            <input type="hidden" name="layanan_id" value="{{ $laporan->layanan_id ?? $layanan->id }}">

            {{-- CARD 1: DATA LAYANAN --}}
            <div class="card">
                <div class="card-header"><h3 class="card-title">DATA LAYANAN</h3></div>
                <div class="card-body">
                    @php
                        $fasilitasNama = $layanan->fasilitas->nama ?? '-';
                        $lokasiTkt1Nama = $layanan->LokasiTk1->nama ?? '-';
                        $lokasiTkt2Nama = $layanan->LokasiTk2->nama ?? '-';
                        $lokasiTkt3Nama = $layanan->LokasiTk3->nama ?? '-';
                    @endphp

                    @foreach([
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
                        <label for="jenis_laporan" class="col-sm-3 col-form-label">
                            JENIS LAPORAN <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <select name="jenis_laporan" id="jenis_laporan" class="form-control" required>
                                <option value="">- Pilih -</option>
                                @foreach ($jenisLaporan as $key => $value)
                                    <option value="{{ $key }}" {{ old('jenis_laporan', $laporan->jenis_laporan ?? '') == $key ? 'selected' : '' }}>
                                        {{ Str::title(str_replace('_', ' ', $key)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jenis_laporan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                </div>
            </div>

            {{-- CARD 2: FORM GANGGUAN --}}
            <div class="card d-none mt-2" id="card-input-gangguan">
                <div class="card-header"><h3 class="card-title">INPUT GANGGUAN</h3></div>
                <div class="card-body" id="form-gangguan-container">
                    {{-- Diisi oleh JS --}}
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="card mt-3">
                <div class="card-footer">
                    <a href="{{ route('tambah.step1') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-angle-left"></i>&nbsp;&nbsp;Kembali
                    </a>
                    <button type="submit" class="btn btn-success btn-sm float-right">
                        Lanjut &nbsp;&nbsp;<i class="fas fa-angle-right"></i>
                    </button>
                </div>
            </div>
        </form>

    </div>
</section>
@endsection

@push('scripts')
<script>
    const kondisiPeralatan = @json(config('constants.kondisi_peralatan'));
    const peralatan = @json($layanan->daftarPeralatanLayanan);
   const jenisLaporanAwal = @json(old('jenis_laporan', $laporan->jenis_laporan ?? ''));

    function markInvalid(el, message) {
        el.classList.remove('is-valid');
        el.classList.add('is-invalid');
        const msg = document.createElement('div');
        msg.className = 'invalid-feedback dynamic';
        msg.innerHTML = message;
        if (!el.parentNode.querySelector('.invalid-feedback.dynamic')) {
            el.parentNode.appendChild(msg);
        }
    }

    function markValid(el) {
        el.classList.remove('is-invalid');
        el.classList.add('is-valid');
        const feedback = el.parentNode.querySelector('.invalid-feedback.dynamic');
        if (feedback) feedback.remove();
    }

    function buildGangguanForm(jenis) {
        const container = document.getElementById('form-gangguan-container');
        const cardGangguan = document.getElementById('card-input-gangguan');
        let html = '';

        cardGangguan.classList.remove('d-none');
        cardGangguan.style.display = 'block';

        if (jenis === 'gangguan_peralatan') {
            html += `
                <div class="form-group row mb-2">
                    <label class="col-sm-3 col-from-label">Waktu Gangguan <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <input type="datetime-local" name="waktu_gangguan" class="form-control">
                    </div>
                </div>
            `;

            peralatan.forEach((item, index) => {
                html += `
                    <hr> 
                    <div class="mb-4">
                        <strong>Peralatan ${index + 1}: <span class="badge bg-primary">${item.peralatan?.nama ?? '-'}</span></strong>
                        <input type="hidden" name="peralatan[${index}][id]" value="${item.peralatan?.id}">
                    </div>

                    <div class="form-group row mb-2">
                        <label class="col-sm-3 col-from-label">Kondisi Peralatan <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select name="peralatan[${index}][kondisi]" class="form-control">
                                <option value="">- Pilih -</option>
                                ${Object.entries(kondisiPeralatan).map(([label, value]) => {
                                    const val = value === true ? 1 : 0;
                                    const labelFormatted = label.charAt(0).toUpperCase() + label.slice(1);
                                    return `<option value="${val}">${labelFormatted}</option>`;
                                }).join('')}
                            </select>
                        </div>
                    </div>

                    <div class="form-group row mb-2">
                        <label class="col-sm-3 col-from-label">Deskripsi Gangguan</label>
                        <div class="col-sm-9">
                            <textarea name="peralatan[${index}][deskripsi]" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                `;
            });

        } else {
            html += `
                <div class="form-group row mb-2">
                    <label class="col-sm-3 col-from-label">Waktu Gangguan <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <input type="datetime-local" name="waktu_gangguan" class="form-control">
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label class="col-sm-3 col-from-label">Deskripsi Gangguan</label>
                    <div class="col-sm-9">
                        <textarea name="deskripsi_gangguan" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            `;
        }

        container.innerHTML = html;
    }

    document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('step2-form');
    const select = document.getElementById('jenis_laporan');

    if (jenisLaporanAwal) buildGangguanForm(jenisLaporanAwal);

    select.addEventListener('change', e => {
        console.log('Jenis laporan dipilih:', e.target.value);
        if (e.target.value) buildGangguanForm(e.target.value);
        else {
            document.getElementById('card-input-gangguan').classList.add('d-none');
            document.getElementById('form-gangguan-container').innerHTML = '';
        }
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        let valid = true;
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback.dynamic').forEach(el => el.remove());

        const jenis = select.value.trim();
        if (!jenis) {
            markInvalid(select, 'Jenis laporan wajib dipilih');
            valid = false;
        }

        const waktu = form.querySelector('[name="waktu_gangguan"]');
        if (!waktu || !waktu.value) {
            markInvalid(waktu, 'Waktu gangguan wajib diisi');
            valid = false;
        }

        if (jenis === 'gangguan_peralatan') {
            const kondisiFields = form.querySelectorAll('[name^="peralatan"][name$="[kondisi]"]');
            kondisiFields.forEach(el => {
                if (!el.value) {
                    markInvalid(el, 'Kondisi peralatan wajib dipilih');
                    valid = false;
                }
            });
        }

        if (valid) form.submit();
    });
});

</script>
@endpush
